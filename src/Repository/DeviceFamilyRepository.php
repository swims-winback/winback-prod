<?php

namespace App\Repository;

use App\Entity\Main\DeviceFamily;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviceFamily|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviceFamily|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviceFamily[]    findAll()
 * @method DeviceFamily[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviceFamilyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviceFamily::class);
    }

    /**
     * Recherche les devices family en fonction du formulaire
     *
     * @return void
     */
    public function search($category = null) {
        //return $this->createQueryBuilder('d')
        $query = $this->createQueryBuilder('d');
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


    /**
     * Find device family by name, to be used in device controller
     */
    public function findFamilyByName(string $value): ?DeviceFamily
    {

        return $this->createQueryBuilder('m')
        ->andWhere('m.name = :val')
        ->setParameter('val', $value)
        ->getQuery()
        ->getOneOrNullResult()
    ;
    }

     /**
      * @return DeviceFamily[] Returns an array of DeviceFamily objects
      */
    
    public function findFamilyByNameAll($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.name = :val')
            ->setParameter('val', $value)
            //->orderBy('m.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function findFamilyBySoftware($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.software = :val')
            ->setParameter('val', $value)
            //->orderBy('m.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Find device family by numberId, to be used in device controller
     */
    public function findFamilyByNumberId(int $value): ?DeviceFamily
    {

        return $this->createQueryBuilder('m')
        ->andWhere('m.numberId = :val')
        ->setParameter('val', $value)
        ->getQuery()
        ->getOneOrNullResult()
    ;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(DeviceFamily $entity, bool $flush = true): void
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
    public function remove(DeviceFamily $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return DeviceFamily[] Returns an array of DeviceFamily objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DeviceFamily
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
