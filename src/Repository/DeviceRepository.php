<?php

namespace App\Repository;

use App\Entity\Device;
use App\Server\DbRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Device::class);
    }


    function searchDevice(DbRequest $dbRequest, $sn = '', $device='', $version=''){
        $whereExist = false;
        $where = '';
        if(!empty($sn)){
            $where .= SN." LIKE '%$sn%' ";
            $whereExist = true;
        }
        if(!empty($device)){
            if($whereExist)
                $where .= ' AND ';
            $where .= DEVICE_TYPE." LIKE '%$device%' ";
            $whereExist = true;
        }
        if(!empty($version)){
            if($whereExist)
                $where .= ' AND ';
            $where .= DEVICE_VERSION." LIKE '%$version%' ";
        }
        if(!empty($uploadVersion)){
            if($whereExist)
                $where .= ' AND ';
            $where .= VERSION_UPLOAD." LIKE '%$uploadVersion%' ";
        }
        $req = $dbRequest->select("*", DEVICE_TABLE, $where);
        echo $req;
        $res = $dbRequest->sendRq($req);
        if($res != FALSE){
            while($row = mysqli_fetch_assoc($res)){
                $result[] = $row;
            }
            return $result;
        }
        return false;
    }

    /**
     * Recherche les devices en fonction du formulaire
     *
     * @return void
     */
    public function search($value = null, $limit = null, $category = null, $version = null, $versionUpload = null, $forced = null) {
        
        $query = $this->createQueryBuilder('d');
        //$dbrequest->searchDevice($value);
        /*
        if ($value != null) {
            $dbrequest->searchDevice($value);
        }
        */
        
        
        if($value != null){
            //$query->where('d.sn = :val OR d.type = :val OR d.version = :val OR d.ipAddr = :val OR d.logPointeur = :val')
            //->setParameter('value', $value);
            //$query->where('d.sn = :value OR d.version = :value')
            //$query->where('d.sn = :value OR d.version = :value')
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
        //}
        //var_dump($category);
        return $query->getQuery()
        //->getOneOrNullResult()
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

    public function findDeviceByForced($value = null)
    {
        $query = $this->createQueryBuilder('m');
        if ($value != null) {
            $query->where('m.forced = :val')
            ->setParameter('val', $value);
        }
        return $query->getQuery()
        ->getResult()
        //->getOneOrNullResult()
    ;
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
