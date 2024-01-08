<?php

namespace App\Command;
// ...

use App\Entity\Main\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
class SendMailCommand extends Command
{
    protected static $defaultName = "app:sendmail";
    private $mailer;

    public function __construct(MailerInterface $mailer) {                
        $this->mailer = $mailer; 
        parent::__construct();               
    }
    protected function configure()
    {
        $this
        ->setDescription('Send Mail')
        ->setHelp('This command allows you to send mails.');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    { 
        $user = new User();
        $emailToAdmin = (new TemplatedEmail())
        ->from(new Address('noreply@winback-assist.com', 'Winback Team'))
        ->to('ldieudonat@winback.com')
        //->cc('bwollensack@winback.com')
        //->cc('croux@winback.com')
        ->subject('Ceci n\'est pas un Spam')
        ->htmlTemplate('mail/test.html.twig')
        ->context([
            'expiration_date' => new \DateTime('+7 days'),
            'username' => $user->getUsername(),
        ]);
        $this->mailer->send($emailToAdmin);

    }
}