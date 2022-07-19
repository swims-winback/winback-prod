<?php
namespace App\Tests\Server;
use App\Server\TCPServer;
use PHPUnit\Framework\TestCase;

class TCPServerTest extends TestCase
{
    public function testDefault()
    {
        /*
        $product = new Product('Pomme', 'food', 1);
                                 $this->assertSame(0.055, $product->computeTVA());
        */
        $tcpserver = new TCPServer();
                                //$this->assertSame(???, $tcpserver->createServer());
    }
}