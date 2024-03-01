<?php

namespace App\Repository;

use App\Entity\Main\Protocol;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Protocol>
 *
 * @method Protocol|null find($id, $lockMode = null, $lockVersion = null)
 * @method Protocol|null findOneBy(array $criteria, array $orderBy = null)
 * @method Protocol[]    findAll()
 * @method Protocol[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProtocolRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Protocol::class);
    }

    public function findAllDate($sn)
    {
        return $this->createQueryBuilder('s')
        ->select('s.date')
        ->andWhere('s.sn = :sn')
        ->setParameter('sn', $sn)
        ->distinct()
        ->getQuery()
        ->getResult();
    }

    public function findAllProtocol($date)
    {
        return $this->createQueryBuilder('s')
        ->select('s.protocol_id')
        ->andWhere('s.date = :date')
        ->setParameter('date', $date)
        ->distinct()
        ->getQuery()
        ->getResult();
    }

    public function findByDate($value, $date): array
    {
        return $this->createQueryBuilder('s')
            ->select('s.protocol_id')
            ->andWhere('s.sn = :val')
            ->setParameter('val', $value)
            ->andWhere('s.date = :sub')
            ->setParameter('sub', $date)
            //->orderBy('s.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Find Mode by sn and date
     * @param string $value
     * @param mixed $date
     * @return array
     */
    public function findMode($value, $date): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.sn = :val')
            ->setParameter('val', $value)
            ->andWhere('s.date = :sub')
            ->setParameter('sub', $date)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findSN(): array
    {
        return $this->createQueryBuilder('s')
            ->select('s.sn')
            ->distinct()
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Protocol[] Returns an array of Protocol objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Protocol
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
