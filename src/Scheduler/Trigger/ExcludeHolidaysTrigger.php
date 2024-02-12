<?php
namespace App\Scheduler\Trigger;

use Symfony\Component\Scheduler\Trigger\TriggerInterface;

class ExcludeHolidaysTrigger implements TriggerInterface
{
    public function __construct(private TriggerInterface $inner)
    {
    }

    // use this method to give a nice displayable name to
    // identify your trigger (it eases debugging)
    public function __toString(): string
    {
        return $this->inner.' (except holidays)';
    }

    public function getNextRunDate(\DateTimeImmutable $run): ?\DateTimeImmutable
    {
        if (!$nextRun = $this->inner->getNextRunDate($run)) {
            return null;
        }

        // loop until you get the next run date that is not a holiday
        while (!$this->isHoliday($nextRun)) {
            $nextRun = $this->inner->getNextRunDate($nextRun);
        }

        return $nextRun;
    }

    private function isHoliday(\DateTimeImmutable $timestamp): bool
    {
        // add some logic to determine if the given $timestamp is a holiday
        // return true if holiday, false otherwise
        return false;
    }
}