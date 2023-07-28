<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\Server\IoServer;
use App\Server\TCPServer;
use App\Server\TCPServer443;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class TCP443Command extends Command
{
    protected static $defaultName = 'app:tcpserver443';
    
    private $logger;

    public function __construct(LoggerInterface $logger) {                
        $this->logger = $logger; 
        parent::__construct();               
    }
    
    protected function configure(): void
    {
        $this
            ->setDescription('Start TCP server on port 443')
            ->setHelp('This command allows you to run a web socket to connect with winback devices.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /*
        $server = IoServer::factory(
            new TCPServer()
        );
        */
        $dispatcher = new EventDispatcher();
        $server = new TCPServer443();
        //$server->setDispatcher($dispatcher);
        $dispatcher->addListener(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $event) {
            // gets the input instance
            $input = $event->getInput();
        
            // gets the output instance
            $output = $event->getOutput();
        
            // gets the command to be executed
            $command = $event->getCommand();
        
            // writes something about the command
            $output->writeln(sprintf('Before running command <info>%s</info>', $command->getName()));
        
            // gets the application
            $application = $command->getApplication();
        });
        $server->runServer($this->logger);
    }
}