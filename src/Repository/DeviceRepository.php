<?php

namespace App\Repository;

use App\Class\SearchData;
use App\Entity\Device;
use App\Server\DbRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;

require_once dirname(__FILE__, 3).'/configServer/config.php';
require_once dirname(__FILE__, 3).'/configServer/dbConfig.php';
/**
 * @method Device|null find($id, $lockMode = null, $lockVersion = null)
 * @method Device|null findOneBy(array $criteria, array $orderBy = null)
 * @method Device[]    findAll()
 * @method Device[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Device::class);
    }

    public function distinctVersions(){
        return $this->createQueryBuilder('cc')
        ->groupBy('cc.deviceFamily')
        ->getQuery()
        ->getResult()
        ;
    }
    /**
     * Recherche les devices en fonction du formulaire
     *
     * @return void
     */
    public function search($value = null, $limit = null, $category = null, $version = null, $versionUpload = null, $forced = null) {
        
        $query = $this->createQueryBuilder('d');
        
        if($value != null){
            $query->where('d.sn LIKE :value')
            ->setParameter(':value', '%'.$value.'%');
        }
        
        if($limit != null){
            $query->setMaxResults($limit);
        }
        
        
        if($category != null){
            $query->leftJoin('d.deviceFamily', 'c');
            $query->andWhere('c.id = :id')
            ->setParameter('id', $category);
        }
        

        if ($version!=null) {
            $query->andWhere('d.version = :version')
            ->setParameter('version', $version);
        }    

        if ($versionUpload!=null) {
            $query->andWhere('d.versionUpload = :versionUpload')
            ->setParameter('versionUpload', $versionUpload);
        }
        
        if ($forced!=null) {
            $query->andWhere('d.forced = :forced')
            ->setParameter('forced', $forced);
        }
        /*
        if ($connected!=null) {
            $query->andWhere('d.isActive = :connected')
            ->setParameter('connected', $connected);
        }
        */
        //}
        return $query
        ->orderBy('d.sn', 'ASC')
        ->getQuery()
        ->getResult()
    ;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Device $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Device $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Find device by id, to be used in device controller
     */
    public function findDeviceById(int $value): ?Device
    {

        return $this->createQueryBuilder('m')
        ->andWhere('m.id = :val')
        ->setParameter('val', $value)
        ->getQuery()
        ->getOneOrNullResult()
    ;
    }
    /**
     * Find device by version, to be used in device controller
     */
    public function findDeviceByVersion($value = null)
    {
        $query = $this->createQueryBuilder('m');
        if ($value != null) {
            /*
            return $this->createQueryBuilder('m')
            ->andWhere('m.version = :val')
            ->setParameter('val', $value)
            ->getQuery()
            //->getOneOrNullResult()
            */
            $query->where('m.version = :val')
            ->setParameter('val', $value);
        }
        return $query->getQuery()
        ->getResult()
        //->getOneOrNullResult()
    ;
    }
    public function findDeviceByVersionUpload($value = null)
    {
        $query = $this->createQueryBuilder('m');
        if ($value != null) {
            $query->where('m.versionUpload = :val')
            ->setParameter('val', $value);
        }
        return $query->getQuery()
        ->getResult()
        //->getOneOrNullResult()
    ;
    }

    public function findDeviceByCriteria($version = null, $uploadVersion = null, $family = null, $value = null)
    {
        $query = $this->createQueryBuilder('m');
        if ($version != null) {
            $query->where('m.version = :val')
            ->setParameter('val', $version);
            //->orderBy('m.version', 'DESC');
        }
        if ($uploadVersion != null) {
            $query->andWhere('m.versionUpload = :val2')
            ->setParameter('val2', $uploadVersion);
        }
        if($family != null){
            $query->leftJoin('m.deviceFamily', 'c');
            $query->andWhere('c.id = :id')
            ->setParameter('id', $family);
        }
        if($value != null){
            //:value->containsAny($value);
            $query->andWhere('m.sn = :value OR m.version = :value')
            ->setParameter('value', $value);
        }
        return $query->getQuery()
        ->getResult()
        //->getOneOrNullResult()
    ;
    }

    /**
      * @return Device[] Returns an array of Device objects
      */
    
      public function findAll()
      {
          return $this->createQueryBuilder('d')
              //->andWhere('s.createdAt = :val')
              //->setParameter('val', $value)
              //->orderBy('s.created_at', 'DESC')
              ->orderBy('d.sn', 'ASC')
              //->setMaxResults(10)
              ->getQuery()
              ->getResult()
          ;
      }

      /**
       * Get devices after filter search
       * @return PaginationInterface
       */
      public function findSearch(SearchData $search, PaginatorInterface $paginator): PaginationInterface
      {

        $query = $this->getSearchQuery($search)->getQuery();
        
        return $paginator->paginate(
            $query,
            $search->page,
            10
        );
      }

    private function getSearchQuery(SearchData $search): ORMQueryBuilder
    {
        $query = $this
        ->createQueryBuilder('d')
        ->orderBy('d.sn', 'ASC')
        ->select('c', 'd')
        ->join('d.deviceFamily', 'c');
        //->setMaxResults(10);

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('d.sn LIKE :q OR d.comment LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }
        
        if (!empty($search->version)) {
            $query = $query
                ->andWhere('d.version = :version')
                ->setParameter('version', $search->version);
        }

        if (!empty($search->version_upload)) {
            $query = $query
                ->andWhere('d.versionUpload = :versionUpload')
                ->setParameter('versionUpload', $search->version_upload);
        }

        
        if (!empty($search->forced)) {
            $query = $query
                ->andWhere('d.forced = 1');
        }

        if (!empty($search->connected)) {
            $query = $query
                ->andWhere('d.isActive = 1');
        }
        
        
        if (!empty($search->categories)) {
            $query = $query
                ->andWhere('c.name IN (:deviceFamily)')
                ->setParameter('deviceFamily', $search->categories);
        }
        
        return $query;
    }

      public function findByDate($date)
      {
          return $this->createQueryBuilder('d')
          ->andWhere('d.created_at LIKE :val')
          ->setParameter('val', '%'.$date.'%')
          ->getQuery()
          ->getResult();
      }
    // /**
    //  * @return Device[] Returns an array of Device objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Device
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
