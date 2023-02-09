<?php
namespace App\Tests\Entity;
use App\Server\Utils;
use Exception;
//use Monolog\Test\TestCase;

//use Monolog\Test\TestCase as TestTestCase;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    public function testDefault()
    {
        /*
        $product = new Product('Pomme', 'food', 1);
                                 $this->assertSame(0.055, $product->computeTVA());
        */
        $utils = new Utils();
        $deviceType = '12';
        /*
        $soft = "WLE256_12_2_v003.012.bin";
        */
        /*
        $test_soft_array = ["test1.bin", "test2.bin", "test3.bin", "test4.bin", "test5.bin"];
        $this->assertSame($test_soft_array, $utils->listFiles('TEST', PACK_PATH));
        $this->assertSame($test_soft_array, $utils->listFiles('TEST2', PACK_PATH));
        */
        /* GET VERSION */
        $back3_soft_list = array_diff(scandir(PACK_PATH . deviceTypeArray['14']), array('..', '.'));
        $this->assertSame('003.001', $utils->getVersion2($back3_soft_list));
        $back4_soft_list = array_diff(scandir(PACK_PATH . deviceTypeArray['12']), array('..', '.'));
        $this->assertSame('003.014', $utils->getVersion2($back4_soft_list));
        $bio_soft_list = array_diff(scandir(PACK_PATH . deviceTypeArray['13']), array('..', '.'));
        $this->assertSame('003.001', $utils->getVersion2($bio_soft_list));
        $cryo_soft_list = array_diff(scandir(PACK_PATH . deviceTypeArray['11']), array('..', '.'));
        $this->assertSame('003.007', $utils->getVersion2($cryo_soft_list));
        $rshock_soft_list = array_diff(scandir(PACK_PATH . deviceTypeArray['10']), array('..', '.'));
        $this->assertSame('004.002', $utils->getVersion2($rshock_soft_list));
        /* COMPARE FILE */
        //$this->assertSame(??, $utils->compareFile());
        /* CHECK FILE */
        $this->assertSame("WLE256_14_2_v003.001.bin", $utils->checkFileTest('14', $boardType = '2'));
        $this->assertSame("WLE256_12_2_v003.014.bin", $utils->checkFileTest('12', $boardType = '2'));
        $this->assertSame("WLE256_13_2_v003.001.bin", $utils->checkFileTest('13', $boardType = '2'));
        $this->assertSame("WLE256_11_2_v003.007.bin", $utils->checkFileTest('11', $boardType = '2'));
        $this->assertSame("WLE256_10_2_v004.002.bin", $utils->checkFileTest('10', $boardType = '2'));
        /* GET FILE CONTENT */
        foreach ($back3_soft_list as $soft) {
            $this->assertSame(true, $utils->getFileContentTest('14', $soft));
        }
        foreach ($back4_soft_list as $soft) {
            $this->assertSame(true, $utils->getFileContentTest('12', $soft));
        }
        foreach ($bio_soft_list as $soft) {
            $this->assertSame(true, $utils->getFileContentTest('13', $soft));
        }
        foreach ($cryo_soft_list as $soft) {
            $this->assertSame(true, $utils->getFileContentTest('11', $soft));
        }
        foreach ($rshock_soft_list as $soft) {
            $this->assertSame(true, $utils->getFileContentTest('10', $soft));
        }
        
        /*
        foreach (deviceTypeArray as $deviceType) {
            $soft_list = array_diff(scandir(PACK_PATH.deviceTypeArray[$deviceType]), array('..', '.'));
            foreach ($soft_list as $soft) {
                $this->assertSame(true, $utils->getFileContentCheck($deviceType, $soft));
            }
        }
        */


        //$this->assertSame(???, $utils->listFiles($deviceType, $path));
    }
}