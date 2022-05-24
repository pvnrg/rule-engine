<?php

namespace App\Repository;

use App\DTO\Uploads as UploadsCollection;
use App\DTO\UploadStatus;
use App\Entity\Uploads;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Uploads>
 *
 * @method Uploads|null find($id, $lockMode = null, $lockVersion = null)
 * @method Uploads|null findOneBy(array $criteria, array $orderBy = null)
 * @method Uploads[]    findAll()
 * @method Uploads[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UploadsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Uploads::class);
    }

    public function add(Uploads $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Uploads $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function addUserUploads(UploadsCollection $uploads, User $user): void
    {
        /** @var UploadStatus $upload */
        foreach($uploads as $upload) {
            $uploadObj = (new Uploads())
                ->setUser($user)
                ->setFilename($upload->fileUploadName)
                ->setIsScanned(false)
                ->setScanPassed(false);

            $this->add($uploadObj);
        }
    }

    public function fetchToUpload(User $user): array
    {
        return $this->findBy([
            'ciUploadId' => null,
            'user' => $user
        ]);
    }

    public function fetchToScan(User $user): array
    {
        return $this->createQueryBuilder('uploads')
            ->where('uploads.ciUploadId IS NOT NULL')
            ->andWhere('uploads.isScanned = 0')
            ->andWhere('uploads.user = :user')
            ->getQuery()
            ->setParameter('user', $user)
            ->getResult();
    }
}
