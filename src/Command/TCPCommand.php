<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\Server\IoServer;
use App\Server\TCPServer;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

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
        $pathToPython = "./src/Server/TCPServer.php";
        //$server = new TCPServer(['php', $pathToPython]);
        $dispatcher = new EventDispatcher();
        $server->setDispatcher($dispatcher);
        $server->runServer($this->logger);

        $dispatcher->addListener(ConsoleEvents::ERROR, function (ConsoleErrorEvent $event): void {
            $output = $event->getOutput();
        
            $command = $event->getCommand();
        
            $output->writeln(sprintf('Oops, exception thrown while running command <info>%s</info>', $command->getName()));
        
            // gets the current exit code (the exception code)
            $exitCode = $event->getExitCode();
        
            // changes the exception to another one
            $event->setError(new \LogicException('Caught exception', $exitCode, $event->getError()));
        });
    }
}