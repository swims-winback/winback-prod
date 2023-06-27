<?php
namespace App\Tests\Entity;

use App\Server\TCPServer;
use Exception;
//use Monolog\Test\TestCase;

//use Monolog\Test\TestCase as TestTestCase;
use PHPUnit\Framework\TestCase;

class TCPServerTest extends TestCase
{

    public function testDefault()
    {
        $tcpServer = new TCPServer;
        $resultArray = $tcpServer->createServer();
        
    }
}