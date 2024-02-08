<?php

namespace App\Command;

use App\Entity\Customer\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Process\Process;
class ErrorCommand extends Command
{
     protected static $defaultName = 'app:errorCommand';
     private $mailer;
    
     public function __construct(MailerInterface $mailer) {                
        $this->mailer = $mailer; 
        parent::__construct();               
    }
     protected function configure()
    {
        $this->addArgument('deviceType', InputArgument::REQUIRED)
    ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    { 
        $pathToPython = "./src/Process/ErrorStats.py";
        $deviceType = $input->getArgument('deviceType');
        $process = new Process(['python', $pathToPython, $deviceType]);
        $process->setTimeout(3600);
        $process->start();
        foreach ($process as $type => $data) {
            if ($process::OUT === $type) {
                echo "\nDebug :".$data;
            } else { // $process::ERR === $type
                echo "\nErreur : ".$data;
            }
        }
        echo $process->getOutput();
        /* PROCESS 2 */
        /*
        $pathToPython2 = "./src/Process/test.py";
        $process2 = new Process(['python', $pathToPython2]);
        $process2->start();
        foreach ($process2 as $type => $data) {
            if ($process2::OUT === $type) {
                echo "\nDebug :".$data;
            } else { // $process::ERR === $type
                echo "\nErreur : ".$data;
            }
        }
        echo $process2->getOutput();
        */
        /* Email */
        //$user = new User();
        /*
        $emailToAdmin = (new TemplatedEmail())
            ->from(new Address('noreply@winback-assist.com', 'Winback Team'))
            ->to('ldieudonat@winback.com')
            ->subject('Winback Assist - Error report')
            ->htmlTemplate('mail/error.html.twig');
        $this->mailer->send($emailToAdmin);
        */
        return 0;
    }
}