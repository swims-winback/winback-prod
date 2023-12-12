<?php

namespace App\Repository;

use App\Class\SearchSn;
use App\Entity\Main\Sn;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Sn>
 *
 * @method Sn|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sn|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sn[]    findAll()
 * @method Sn[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SnRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Sn::class);
        $this->paginator = $paginator;
    }

    public function save(Sn $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sn $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

          /**
       * Get devices after filter search
       * @return PaginationInterface
       */
      public function findSearch(SearchSn $search): PaginationInterface
      {

        $query = $this->getSearchQuery($search)->getQuery();
        
        return $this->paginator->paginate(
            $query,
            $search->page,
            20
        );
      }

    /**
     * Récupère le prix minimum et maximum correspondant à une recherche
     * @return integer[]
     */
    public function findMinMax(SearchSn $search): array
    {
        $results = $this->getSearchQuery($search, true)
            ->select('MIN(d.date) as min', 'MAX(d.date) as max')
            ->getQuery()
            ->getScalarResult();
        return [(int)$results[0]['min'], (int)$results[0]['max']];
    }

    private function getSearchQuery(SearchSn $search)
      {
        $query = $this
        ->createQueryBuilder('d');
        //->orderBy('d.sn', 'ASC')
        //->select('c', 'd')
        //->join('d.deviceFamily', 'c');
        //->setMaxResults(10);

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('d.sn LIKE :q or d.device LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }
        
        if (!empty($search->min)) {
            $query = $query
                ->andWhere('d.date >= :min')
                ->setParameter('min', $search->min);
        }

        if (!empty($search->max)) {
            $query = $query
                ->andWhere('d.date <= :max')
                ->setParameter('max', $search->max);
        }

        return $query;
      }

      public function findAllSubType()
      {
          return $this->createQueryBuilder('s')
          ->select('s.subtype')
          ->distinct()
          ->getQuery()
          ->getResult();
      }

      public function findAllDeviceType($subtype)
      {
          return $this->createQueryBuilder('s')
          ->select('s.device')
          ->andWhere('s.subtype = :sub')
          ->setParameter('sub', $subtype)
          ->distinct()
          ->getQuery()
          ->getResult();
      }

      public function findBySubType($value): array
      {
          return $this->createQueryBuilder('s')
              ->andWhere('s.subtype = :val')
              ->setParameter('val', $value)
              //->orderBy('s.id', 'ASC')
              //->setMaxResults(10)
              ->getQuery()
              ->getResult()
          ;
      }
        public function findByDeviceType($value, $subtype): array
        {
            return $this->createQueryBuilder('s')
                ->andWhere('s.device = :val')
                ->setParameter('val', $value)
                ->andWhere('s.subtype = :sub')
                ->setParameter('sub', $subtype)
                //->orderBy('s.id', 'ASC')
                //->setMaxResults(10)
                ->getQuery()
                ->getResult()
            ;
        }
      
//    /**
//     * @return Sn[] Returns an array of Sn objects
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

//    public function findOneBySomeField($value): ?Sn
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
