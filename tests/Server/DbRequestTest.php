<?php
namespace App\Tests\Entity;

use App\Entity\DeviceFamily;
use App\Repository\DeviceFamilyRepository;
use App\Server\DbRequest;
use App\Server\Utils;
use Exception;
//use Monolog\Test\TestCase;

//use Monolog\Test\TestCase as TestTestCase;
use PHPUnit\Framework\TestCase;

class DbRequestTest extends TestCase
//class DbRequestTest
{

    public function testDefault()
    {
        $dbRequest = new DbRequest;

        //$dbRequest->getDeviceTypeActualVers(12);
        $this->testGetDeviceTypeActualVers('3.3', 'WLE256_14_2_v003.003.bin', 14); //BACK3
        $this->testGetDeviceTypeActualVers('3.15', 'WLE256_12_2_v003.015.bin', 12); //BACK4
        $this->testGetDeviceTypeActualVers('3.1', 'WLE256_13_2_v003.001.bin', 13); //BIOBACK
        $this->testGetDeviceTypeActualVers('3.7', 'WLE256_11_2_v003.007.bin', 11); //CRYO
        $this->testGetDeviceTypeActualVers('4.2', 'WLE256_10_2_v004.002.bin', 10); //RSHOCK

        $dbRequest->getLocationInfoByIp('82.64.154.34');
        
        //$dbRequest->setDeviceInfo('WIN0C01B42108-0001', '3.12', 12, '82.64.154.34', 'WIN0C01B42108-0001.txt');
    }
    
    public function testGetDeviceTypeActualVers($version, $filename, $deviceType)
    {
        $dbRequest = new DbRequest;
        $result = $dbRequest->getDeviceTypeActualVers($deviceType);
        $this->assertSame($filename, $result['name']);
        $this->assertSame($version, $result['version']);
    }

    public function testInitDeviceInSn()
    {
        $dbRequest = new DbRequest;
        $this->assertTrue($dbRequest->initDeviceInSN("testB3TX", "BACK3"));
        $this->assertTrue($dbRequest->initDeviceInSN("test2B3TX", "BACK3"));
        $this->assertTrue($dbRequest->setDeviceToServer("test2B3TX"));
    }
}