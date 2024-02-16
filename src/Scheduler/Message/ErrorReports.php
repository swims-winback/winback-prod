<?php

namespace App\Scheduler\Message;

use App\Command\ErrorCommand;
use App\Controller\ErrorController;
use App\Repository\ErrorRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class ErrorReports
{
    private $mailer;
    private $errorRepository;
    public function __construct(MailerInterface $mailer, ErrorRepository $errorRepository) {                
        $this->mailer = $mailer; 
        $this->errorRepository = $errorRepository;            
    }
    public function main()
    {
        /* BACK4 */
        $errorCommand = new ErrorCommand($this->mailer);
        $input = new ArrayInput(['deviceType' => 'BACK4']);
        $output = new BufferedOutput();
        $errorCommand->run($input, $output);
        $content = $output->fetch();
        /* BACK3TX */
        $errorCommand = new ErrorCommand($this->mailer);
        $input = new ArrayInput(['deviceType' => 'BACK3TX']);
        $output = new BufferedOutput();
        $errorCommand->run($input, $output);
        $content = $output->fetch();
        /* BACK3TE */
        $errorCommand = new ErrorCommand($this->mailer);
        $input = new ArrayInput(['deviceType' => 'BACK3TE']);
        $output = new BufferedOutput();
        $errorCommand->run($input, $output);
        $content = $output->fetch();
        /* mail */
        /*
        $emailToAdmin = (new TemplatedEmail())
        ->from(new Address('noreply@winback-assist.com', 'Winback Team'))
        ->to('ldieudonat@winback.com')
        ->subject('Winback Assist - Error report')
        ->htmlTemplate('mail/error.html.twig');
        $this->mailer->send($emailToAdmin);
        */
        $errorController = new ErrorController($this->mailer, $this->errorRepository);
        $errorController->reportError($this->errorRepository);
        return new Response($content);
    }
}