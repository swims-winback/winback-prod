<?php

namespace App\Repository\Statistics;

use App\Entity\Main\Statistics\StatisticPathoType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatisticPathoType>
 *
 * @method StatisticPathoType|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatisticPathoType|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatisticPathoType[]    findAll()
 * @method StatisticPathoType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatisticPathoTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatisticPathoType::class);
    }

    public function save(StatisticPathoType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(StatisticPathoType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return StatisticPathoType[] Returns an array of StatisticPathoType objects
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

//    public function findOneBySomeField($value): ?StatisticPathoType
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
