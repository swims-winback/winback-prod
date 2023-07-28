<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\Server\IoServer;
use App\Server\TCPServer;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class TCPCommand extends Command
{
    protected static $defaultName = 'app:tcpserver';
    
    private $logger;

    public function __construct(LoggerInterface $logger) {                
        $this->logger = $logger; 
        parent::__construct();               
    }
    
    protected function configure(): void
    {
        $this
            ->setDescription('Start TCP server')
            ->setHelp('This command allows you to run a web socket to connect with winback devices.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $server = new TCPServer();
        $server->runServer($this->logger);
    }
}