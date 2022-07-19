<?php
namespace App\Server;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\Server\IoServer;
use App\Server\TCPServer;

class TCPCommand extends Command
{
    protected static $defaultName = 'app:tcpserver';

    protected function configure(): void
    {
        $this
            ->setDescription('Start TCP server')
            ->setHelp('This command allows you to run a web socket to connect with winback devices.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /*
        $server = IoServer::factory(
            new TCPServer()
        );
        */
        $server = new TCPServer();
        $server->runServer();
    }
}