<?php

namespace App\Repository;

use App\Entity\Main\Software;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Software|null find($id, $lockMode = null, $lockVersion = null)
 * @method Software|null findOneBy(array $criteria, array $orderBy = null)
 * @method Software[]    findAll()
 * @method Software[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SoftwareRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Software::class);
    }

    public function distinctVersions(){
        return $this->createQueryBuilder('cc')
        ->groupBy('cc.deviceFamily')
        ->getQuery()
        ->getResult()
        ;
    }

    /**
     * Recherche les softwares en fonction du formulaire
     *
     */
    public function search($value = null, $category = null) {
        $query = $this->createQueryBuilder('d');
        if($value != null){
            
            $query->where('d.name LIKE :value')
            ->setParameter(':value', '%'.$value.'%');
        }
        if($category != null){
            $query->leftJoin('d.deviceFamily', 'c');
            $query->andWhere('c.name = :name')
            ->setParameter('name', $category);
        }
        //echo ($query);
        return $query
            ->orderBy('d.name', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findSoftwareByName($value): ?Software
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.name = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findSoftwareByVersion($value, $category): ?Software
    {
        return $this->createQueryBuilder('s')
            //->andWhere('s.version = :val')
            ->andWhere('s.version LIKE :val')
            ->setParameter('val', $value)
            
            ->leftJoin('s.deviceFamily', 'c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $category)
            
            ->getQuery()
            ->getOneOrNullResult();
    }

     /**
      * @return Software[] Returns an array of Software objects
      */
    
    public function findAll()
    {
        return $this->createQueryBuilder('s')
            //->andWhere('s.createdAt = :val')
            //->setParameter('val', $value)
            //->orderBy('s.created_at', 'DESC')
            ->orderBy('s.name', 'DESC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

     /**
      * @return Software[] Returns an array of Software objects
      */
    public function findByDeviceFamily($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.deviceFamily = :val')
            ->setParameter('val', $value)
            //->orderBy('s.created_at', 'DESC')
            ->orderBy('s.version', 'DESC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Software $entity, bool $flush = true): void
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
    public function remove(Software $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Software[] Returns an array of Software objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Software
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
