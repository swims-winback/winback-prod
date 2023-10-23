<?php

namespace App\Repository;

use App\Entity\Main\DeviceServer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DeviceServer>
 *
 * @method DeviceServer|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviceServer|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviceServer[]    findAll()
 * @method DeviceServer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviceServerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviceServer::class);
    }

    public function save(DeviceServer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DeviceServer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    
    public function findByDate($date)
    {
        return $this->createQueryBuilder('d')
        ->andWhere('d.date LIKE :val')
        ->setParameter('val', '%'.$date.'%')
        ->getQuery()
        ->getResult();
    }
    /*
    public function findByDate($date)
    {
        return $this->createQueryBuilder('d')
        ->andWhere('d.date = :val')
        ->setParameter('val', $date)
        ->getQuery()
        ->getResult();
    }
    */
//    /**
//     * @return DeviceServer[] Returns an array of DeviceServer objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DeviceServer
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
