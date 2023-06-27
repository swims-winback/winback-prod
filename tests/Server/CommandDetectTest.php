<?php
namespace App\Tests\Entity;

use App\Entity\DeviceFamily;
use App\Repository\DeviceFamilyRepository;
use App\Server\CommandDetect;
use App\Server\DataResponse;
use App\Server\DbRequest;
use App\Server\Utils;
use Exception;
//use Monolog\Test\TestCase;

//use Monolog\Test\TestCase as TestTestCase;
use PHPUnit\Framework\TestCase;

class CommandDetectTest extends TestCase
{

    public function testDefault()
    {

        $dbRequest = new DbRequest;
        $utils = new Utils();
        $task = new CommandDetect;
        $dataResponse = new DataResponse;
        //$deviceType = '11';

        $deviceInfoBack3 = array
        (
            0 => array (
                //DEVICE_TYPE => 3,
                //SN => 'WIN0C01B42108-0001',
                //DEVICE_VERSION => 3.15,
                VERSION_UPLOAD => false,
                FORCED_UPDATE => 0,
            ),
            1 => array (
                //DEVICE_TYPE => 3,
                //SN => 'WIN0C01B42108-0001',
                //DEVICE_VERSION => 3.15,
                VERSION_UPLOAD => false,
                FORCED_UPDATE => 0,
            ),
            2 => array (
                //DEVICE_TYPE => 3,
                //SN => 'WIN0C01B42108-0001',
                //DEVICE_VERSION => 3.15,
                VERSION_UPLOAD => false,
                FORCED_UPDATE => 0,
            ),
        );
        $deviceInfoBack4 = array
        (
            0 => array (
                //DEVICE_TYPE => 3,
                //SN => 'WIN0C01B42108-0001',
                //DEVICE_VERSION => 3.15,
                VERSION_UPLOAD => '3.12',
                FORCED_UPDATE => 0,
            )
        );

        foreach ($deviceInfoBack3 as $key => $value) {
            if ($deviceInfoBack3[$key][VERSION_UPLOAD] == false) {
                $this->assertSame('WLE256_11_2_v003.007.bin', $task->getVersionUpload($deviceInfoBack3[$key], 2, 11, $dbRequest));
            }
            /*
            else {
                $this->assertSame('WLE256_11_2_v003.007.bin', $task->getVersionUpload($deviceInfoBack3[$key], 2, 11, $dataResponse));
            }
            */
        }
        foreach ($deviceInfoBack4 as $key => $value) {
            //if ($key) {
                $this->assertSame('WLE256_12_2_v003.012.bin', $task->getVersionUpload($deviceInfoBack4[$key], 2, 12, $dbRequest));
            //}
        }

        /*
        if ($deviceInfoBack3) {
            $this->assertSame('WLE256_11_2_v003.007.bin', $task->getVersionUpload($deviceInfo, 2, 11, $dataResponse));
            $this->assertSame('WLE256_12_2_v003.007.bin', $task->getVersionUpload($deviceInfo, 2, 11, $dataResponse));
            
        }
        */
    }
}