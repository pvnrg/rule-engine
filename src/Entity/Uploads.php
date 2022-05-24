<?php

namespace App\Entity;

use App\Repository\UploadsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UploadsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Uploads
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $filename;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'uploads')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'boolean')]
    private bool $isScanned;

    #[ORM\Column(type: 'boolean')]
    private bool $scanPassed;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $scanResult;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $ciUploadId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isIsScanned(): ?bool
    {
        return $this->isScanned;
    }

    public function setIsScanned(bool $isScanned): self
    {
        $this->isScanned = $isScanned;

        return $this;
    }

    public function isScanPassed(): ?bool
    {
        return $this->scanPassed;
    }

    public function setScanPassed(bool $scanPassed): self
    {
        $this->scanPassed = $scanPassed;

        return $this;
    }

    public function getScanResult(): ?string
    {
        return $this->scanResult;
    }

    public function setScanResult(?string $scanResult): self
    {
        $this->scanResult = $scanResult;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getCiUploadId(): ?string
    {
        return $this->ciUploadId;
    }

    public function setCiUploadId(?string $ciUploadId): self
    {
        $this->ciUploadId = $ciUploadId;

        return $this;
    }

    public function isUploaded(): bool
    {
        return null !== $this->getCiUploadId();
    }
}
