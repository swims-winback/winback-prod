<?php
namespace App\Server;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\Server\IoServer;
use App\Server\TCPServer;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ServerCommand extends Command
{
    protected static $defaultName = 'app:server';
    
    private $logger;

    public function __construct(LoggerInterface $logger) {                
        $this->logger = $logger; 
        parent::__construct();               
    }
    
    protected function configure(): void
    {
        $this
            ->setDescription('Start 5007 server')
            ->setHelp('This command allows you to run a web socket to connect with 5006 server.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $server = new Server();
        $server->runServer($this->logger);
    }
}