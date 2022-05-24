<?php

namespace App\Repository;

use App\Entity\Triggere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Triggere>
 *
 * @method Triggere|null find($id, $lockMode = null, $lockVersion = null)
 * @method Triggere|null findOneBy(array $criteria, array $orderBy = null)
 * @method Triggere[]    findAll()
 * @method Triggere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TriggereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Triggere::class);
    }

    public function add(Triggere $entity, bool $flush = false): void
    {
        if (!$this->isExist($entity)) {
            $this->getEntityManager()->persist($entity);
        }
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Triggere $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function isExist(Triggere $triggere): bool
    {
        $existing = $this->findOneBy([
            'user' => $triggere->getUser(),
            'rule' => $triggere->getRule(),
            'notification' => $triggere->getNotification()
        ]);

        return $existing instanceof Triggere;
    }

}
