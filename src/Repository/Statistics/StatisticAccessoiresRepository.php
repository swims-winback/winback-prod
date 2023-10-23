<?php

namespace App\Repository\Statistics;

use App\Entity\Main\Statistics\StatisticAccessoires;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatisticAccessoires>
 *
 * @method StatisticAccessoires|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatisticAccessoires|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatisticAccessoires[]    findAll()
 * @method StatisticAccessoires[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatisticAccessoiresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatisticAccessoires::class);
    }

    public function save(StatisticAccessoires $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(StatisticAccessoires $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return StatisticAccessoires[] Returns an array of StatisticAccessoires objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?StatisticAccessoires
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
