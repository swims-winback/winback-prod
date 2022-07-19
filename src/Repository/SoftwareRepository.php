<?php

namespace App\Repository;

use App\Entity\Software;
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

    /**
     * Recherche les softwares en fonction du formulaire
     *
     * @return void
     */
    public function search($value = null, $category = null) {
        //return $this->createQueryBuilder('d')
        $query = $this->createQueryBuilder('d');
        if($value != null){
            $query->where('d.name LIKE :value OR d.softwareFile LIKE :value')
            ->setParameter(':value', '%'.$value.'%');
            /*
            $query->where('d.name = :value OR d.softwareFile = :value')
            ->setParameter('value', $value);
            */
        }
        if($category != null){
            $query->leftJoin('d.deviceFamily', 'c');
            $query->andWhere('c.id = :id')
            ->setParameter('id', $category);
        }
        return $query->getQuery()
        //->getOneOrNullResult()
        ->getResult()
    ;
    }

    /*
    public function findSoftwareByCriteria($version = null, $family = null, $value = null)
    {
        $query = $this->createQueryBuilder('m');
        if ($version != null) {
            $query->where('m.version = :val')
            ->setParameter('val', $version);
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
    */
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
            ->andWhere('s.version = :val')
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
