<?php

namespace App\Repository;

use App\Class\SearchError;
use App\Entity\Main\Error;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Error>
 *
 * @method Error|null find($id, $lockMode = null, $lockVersion = null)
 * @method Error|null findOneBy(array $criteria, array $orderBy = null)
 * @method Error[]    findAll()
 * @method Error[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ErrorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Error::class);
    }

    public function save(Error $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Error $entity, bool $flush = false): void
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
      public function findSearch(SearchError $search, PaginatorInterface $paginator): PaginationInterface
      {

        $query = $this->getSearchQuery($search)->getQuery();
        return $paginator->paginate(
            $query,
            $search->page,
            25
        );
      }

      private function getSearchQuery(SearchError $search): ORMQueryBuilder
      {
          $query = $this
          ->createQueryBuilder('d')
          ->orderBy('d.sn', 'ASC')
          ->orderBy('d.error', 'ASC')
          ->select('c', 'd')
          ->join('d.sn', 'c')
          ->select('e', 'd')
          ->join('d.error', 'e');
          //->setMaxResults(10);
        
          
          if (!empty($search->q)) {
              $query = $query
                  ->andWhere('d.sn LIKE :q')
                  ->setParameter('q', "%{$search->q}%");
          }
          
          if (!empty($search->version)) {
              $query = $query
                  ->andWhere('d.version = :version')
                  ->setParameter('version', $search->version);
          }
          
          if (!empty($search->sn_category)) {
              $query = $query
                  ->andWhere('c.sn IN (:sn)')
                  ->setParameter('sn', $search->sn_category);
          }
          if (!empty($search->error_category)) {
            $query = $query
                ->andWhere('e.error_id IN (:error)')
                ->setParameter('error', $search->error_category);
        }
          
          return $query;
      }

      public function distinctCategories(){
        $query = $this->createQueryBuilder('cc')
        ->groupBy('cc.sn')
        ->getQuery()
        ->getResult()
        ;
        return $query;
    }
    public function distinctErrors(){
        return $this->createQueryBuilder('cc')
        ->groupBy('cc.error')
        ->getQuery()
        ->getResult()
        ;
    }
    public function distinctVersions(){
        return $this->createQueryBuilder('cc')
        ->groupBy('cc.version')
        ->getQuery()
        ->getResult()
        ;
    }
      /*
      public function findAllSn(DeviceRepository $deviceRepository) {
        $back4 = deviceTypeId[12];
        $devices = $deviceRepository->findBy(array('deviceFamily' => $back4));
        foreach ($devices as $device) {
            $this->findBy(array('sn' =>$device), array(array('distinct' => TRUE)));
        }
        return True;
     }
     */
//    /**
//     * @return Error[] Returns an array of Error objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Error
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
