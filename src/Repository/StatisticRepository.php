<?php

namespace App\Repository;

use App\Entity\Statistic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Statistic>
 *
 * @method Statistic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Statistic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Statistic[]    findAll()
 * @method Statistic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatisticRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Statistic::class);
    }

    public function save(Statistic $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Statistic $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllDevice($sn, $type_patho, $zone, $patho, $accessoires)
    {
        $deviceArray = array();
        $query = $this->createQueryBuilder('s');
        $query2 = $this->createQueryBuilder('s');

        if ($sn!=null) {
            $query->andWhere('s.SN = :SN')
            ->setParameter('SN', $sn);
            $query2->andWhere('s.SN = :SN')
            ->setParameter('SN', $sn);
        }
        if ($type_patho!=null) {
            $query->andWhere('s.type_patho = :type_patho')
            ->setParameter('type_patho', $type_patho);
            $query2->andWhere('s.type_patho = :type_patho')
            ->setParameter('type_patho', $type_patho);
        }
        if ($zone!=null) {
            $query->andWhere('s.zone = :zone')
            ->setParameter('zone', $zone);
            $query2->andWhere('s.zone = :zone')
            ->setParameter('zone', $zone);
        }
        if ($patho!=null) {
            $query->andWhere('s.patho = :patho')
            ->setParameter('patho', $patho);
            $query2->andWhere('s.patho = :patho')
            ->setParameter('patho', $patho);
        } 
        if ($accessoires!=null) {
            $query->andWhere('s.accessoires = :accessoires')
            ->setParameter('accessoires', $accessoires);
            $query2->andWhere('s.accessoires = :accessoires')
            ->setParameter('accessoires', $accessoires);
        }
        $result = $query
        ->select('s.SN')
        ->distinct()
        ->getQuery()
        ->getResult();

        // on veut compter le nb de rang pour chaque patho
        foreach ($result as $key => $value) {
            $result2 = $query2
            ->select('s.SN')
            ->andWhere('s.SN = :SN')
            ->setParameter('SN', $value)
            ->getQuery()
            ->getResult();
            $countDeviceStat = count($result2); //each count
            $deviceArray[$value['SN']] = $countDeviceStat;
        }
        return $deviceArray;
    }

    public function findAllPathotype($sn, $type_patho, $zone, $patho, $accessoires)
    {
        $pathoTypeArray = array();
        $query = $this->createQueryBuilder('s');
        $query2 = $this->createQueryBuilder('s');

        if ($sn!=null) {
            $query->andWhere('s.SN = :SN')
            ->setParameter('SN', $sn);
            $query2->andWhere('s.SN = :SN')
            ->setParameter('SN', $sn);
        }
        if ($type_patho!=null) {
            $query->andWhere('s.type_patho = :type_patho')
            ->setParameter('type_patho', $type_patho);
            $query2->andWhere('s.type_patho = :type_patho')
            ->setParameter('type_patho', $type_patho);
        }
        if ($zone!=null) {
            $query->andWhere('s.zone = :zone')
            ->setParameter('zone', $zone);
            $query2->andWhere('s.zone = :zone')
            ->setParameter('zone', $zone);
        }
        if ($patho!=null) {
            $query->andWhere('s.patho = :patho')
            ->setParameter('patho', $patho);
            $query2->andWhere('s.patho = :patho')
            ->setParameter('patho', $patho);
        } 
        if ($accessoires!=null) {
            $query->andWhere('s.accessoires = :accessoires')
            ->setParameter('accessoires', $accessoires);
            $query2->andWhere('s.accessoires = :accessoires')
            ->setParameter('accessoires', $accessoires);
        }
        $result = $query
        ->select('s.type_patho')
        ->distinct()
        ->getQuery()
        ->getResult();

        // on veut compter le nb de rang pour chaque patho
        foreach ($result as $key => $value) {
            $result2 = $query2
            ->select('s.type_patho')
            ->andWhere('s.type_patho = :type_patho')
            ->setParameter('type_patho', $value)
            ->getQuery()
            ->getResult();
            $countPathoTypeStat = count($result2); //each count
            $pathoTypeArray[$value['type_patho']] = $countPathoTypeStat;
        }
        return $pathoTypeArray;
    }
    /*
    public function findAllZone()
    {
        return $this->createQueryBuilder('s')
        ->select('s.zone')
        ->distinct()
        ->getQuery()
        ->getResult();
    }
    */
    public function findAllZone($sn, $type_patho, $zone, $patho, $accessoires)
    {
        $zoneArray = array();
        $query = $this->createQueryBuilder('s');
        $query2 = $this->createQueryBuilder('s');

        if ($sn!=null) {
            $query->andWhere('s.SN = :SN')
            ->setParameter('SN', $sn);
            $query2->andWhere('s.SN = :SN')
            ->setParameter('SN', $sn);
        }
        if ($type_patho!=null) {
            $query->andWhere('s.type_patho = :type_patho')
            ->setParameter('type_patho', $type_patho);
            $query2->andWhere('s.type_patho = :type_patho')
            ->setParameter('type_patho', $type_patho);
        }
        if ($zone!=null) {
            $query->andWhere('s.zone = :zone')
            ->setParameter('zone', $zone);
            $query2->andWhere('s.zone = :zone')
            ->setParameter('zone', $zone);
        }
        if ($patho!=null) {
            $query->andWhere('s.patho = :patho')
            ->setParameter('patho', $patho);
            $query2->andWhere('s.patho = :patho')
            ->setParameter('patho', $patho);
        } 
        if ($accessoires!=null) {
            $query->andWhere('s.accessoires = :accessoires')
            ->setParameter('accessoires', $accessoires);
            $query2->andWhere('s.accessoires = :accessoires')
            ->setParameter('accessoires', $accessoires);
        }
        $result = $query
        ->select('s.zone')
        ->distinct()
        ->getQuery()
        ->getResult();

        // on veut compter le nb de rang pour chaque patho
        foreach ($result as $key => $value) {
            $result2 = $query2
            ->select('s.zone')
            ->andWhere('s.zone = :zone')
            ->setParameter('zone', $value)
            ->getQuery()
            ->getResult();
            $countZoneStat = count($result2); //each count
            $zoneArray[$value['zone']] = $countZoneStat;
        }
        return $zoneArray;
    }
    public function findAllPatho($sn, $type_patho, $zone, $patho, $accessoires)
    {
        $pathoArray = array();
        $query = $this->createQueryBuilder('s');
        $query2 = $this->createQueryBuilder('s');

        if ($sn!=null) {
            $query->andWhere('s.SN = :SN')
            ->setParameter('SN', $sn);
            $query2->andWhere('s.SN = :SN')
            ->setParameter('SN', $sn);
        }
        if ($type_patho!=null) {
            $query->andWhere('s.type_patho = :type_patho')
            ->setParameter('type_patho', $type_patho);
            $query2->andWhere('s.type_patho = :type_patho')
            ->setParameter('type_patho', $type_patho);
        }
        if ($zone!=null) {
            $query->andWhere('s.zone = :zone')
            ->setParameter('zone', $zone);
            $query2->andWhere('s.zone = :zone')
            ->setParameter('zone', $zone);
        }
        if ($patho!=null) {
            $query->andWhere('s.patho = :patho')
            ->setParameter('patho', $patho);
            $query2->andWhere('s.patho = :patho')
            ->setParameter('patho', $patho);
        } 
        if ($accessoires!=null) {
            $query->andWhere('s.accessoires = :accessoires')
            ->setParameter('accessoires', $accessoires);
            $query2->andWhere('s.accessoires = :accessoires')
            ->setParameter('accessoires', $accessoires);
        }
        $result = $query
        ->select('s.patho')
        ->distinct()
        ->getQuery()
        ->getResult();

        // on veut compter le nb de rang pour chaque patho
        foreach ($result as $key => $value) {
            $result2 = $query2
            ->select('s.patho')
            ->andWhere('s.patho = :patho')
            ->setParameter('patho', $value)
            ->getQuery()
            ->getResult();
            $countPathoStat = count($result2); //each count
            $pathoArray[$value['patho']] = $countPathoStat;
        }
        return $pathoArray;
    }

    /*
    public function findAllTool()
    {
        return $this->createQueryBuilder('s')
        ->select('s.accessoires')
        ->distinct()
        ->getQuery()
        ->getResult();
    }
    */
    public function findAllTool($sn, $type_patho, $zone, $patho, $accessoires)
    {
        $toolArray = array();
        $query = $this->createQueryBuilder('s');
        $query2 = $this->createQueryBuilder('s');

        if ($sn!=null) {
            $query->andWhere('s.SN = :SN')
            ->setParameter('SN', $sn);
            $query2->andWhere('s.SN = :SN')
            ->setParameter('SN', $sn);
        }
        if ($type_patho!=null) {
            $query->andWhere('s.type_patho = :type_patho')
            ->setParameter('type_patho', $type_patho);
            $query2->andWhere('s.type_patho = :type_patho')
            ->setParameter('type_patho', $type_patho);
        }
        if ($zone!=null) {
            $query->andWhere('s.zone = :zone')
            ->setParameter('zone', $zone);
            $query2->andWhere('s.zone = :zone')
            ->setParameter('zone', $zone);
        }
        if ($patho!=null) {
            $query->andWhere('s.patho = :patho')
            ->setParameter('patho', $patho);
            $query2->andWhere('s.patho = :patho')
            ->setParameter('patho', $patho);
        } 
        if ($accessoires!=null) {
            $query->andWhere('s.accessoires = :accessoires')
            ->setParameter('accessoires', $accessoires);
            $query2->andWhere('s.accessoires = :accessoires')
            ->setParameter('accessoires', $accessoires);
        }
        $result = $query
        ->select('s.accessoires')
        ->distinct()
        ->getQuery()
        ->getResult();

        // on veut compter le nb de rang pour chaque patho
        foreach ($result as $key => $value) {
            $result2 = $query2
            ->select('s.accessoires')
            ->andWhere('s.accessoires = :accessoires')
            ->setParameter('accessoires', $value)
            ->getQuery()
            ->getResult();
            $countToolStat = count($result2); //each count
            $toolArray[$value['accessoires']] = $countToolStat;
        }
        return $toolArray;
    }
//    /**
//     * @return Statistic[] Returns an array of Statistic objects
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

//    public function findOneBySomeField($value): ?Statistic
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
