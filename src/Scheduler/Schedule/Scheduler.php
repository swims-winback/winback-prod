<?php
namespace App\Scheduler\Schedule;

use App\Repository\ErrorRepository;
use App\Scheduler\Message\ErrorReports;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('main')]
class Scheduler implements ScheduleProviderInterface
{
    private $mailer;
    private $errorRepository;
    public function __construct(MailerInterface $mailer, ErrorRepository $errorRepository) {                
        $this->mailer = $mailer;
        $this->errorRepository = $errorRepository;           
    }
    public function getSchedule(): Schedule
    {
        return (new Schedule())
        ->add(
            RecurringMessage::every('1 day', new ErrorReports($this->mailer, $this->errorRepository), 
            from: new \DateTimeImmutable('07:00', new \DateTimeZone('Europe/Paris'))
            )
        );
    }
}