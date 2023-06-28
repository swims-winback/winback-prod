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
        /* COMPARE FILE */
        //$this->assertSame(??, $utils->compareFile());
        /* GET FILE CONTENT */
        $rshock_soft_list = array_diff(scandir($_ENV['PACK_PATH'].deviceTypeArray[10]), array('..', '.'));
        $cryo_soft_list = array_diff(scandir($_ENV['PACK_PATH'].deviceTypeArray[11]), array('..', '.'));
        $back4_soft_list = array_diff(scandir($_ENV['PACK_PATH'].deviceTypeArray[12]), array('..', '.'));
        $bio_soft_list = array_diff(scandir($_ENV['PACK_PATH'].deviceTypeArray[13]), array('..', '.'));
        $back3_soft_list = array_diff(scandir($_ENV['PACK_PATH'].deviceTypeArray[10]), array('..', '.'));

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
    }
}