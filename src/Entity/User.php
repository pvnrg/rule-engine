<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Triggere::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $triggeres;

    public function __construct()
    {
        $this->triggeres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Triggere>
     */
    public function getTriggeres(): Collection
    {
        return $this->triggeres;
    }

    public function addTriggere(Triggere $triggere): self
    {
        $existing = $this->triggeres->filter(function(Triggere $t) use ($triggere) {
            return $triggere->getUser()->getId() === $this->getId()
                && $triggere->getRule()->getId() === $t->getRule()->getId()
                && $triggere->getNotification()->getId() === $t->getNotification()->getId();
        })->first();

        if (false === $existing) {
            $this->triggeres[] = $triggere;
            $triggere->setUser($this);
        }

        return $this;
    }

    public function removeTriggere(Triggere $triggere): self
    {
        if ($this->triggeres->removeElement($triggere)) {
            // set the owning side to null (unless already changed)
            if ($triggere->getUser() === $this) {
                $triggere->setUser(null);
            }
        }

        return $this;
    }

    public function setTriggeres(Collection $triggers): self
    {
        $this->triggeres = $triggers;

        return $this;
    }

    /**
     * @param string $reason
     * @return bool
     */
    public function isTriggerOn(string $reason): bool
    {
        if ($this->triggeres->isEmpty()) {
            return false;
        }

        $found = $this->triggeres->filter(function(Triggere $triggere) use ($reason) {
            return $triggere->getRule()->getName() === $reason;
        })->first();

        return false !== $found;
    }

    /**
     * @param string $reason
     * @return Collection
     */
    public function getRuleTriggers(string $reason): Collection
    {
        return $this->triggeres->filter(function(Triggere $triggere) use ($reason) {
            return $triggere->getRule()->getName() === $reason;
        });
    }
}
