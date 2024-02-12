<?php
namespace App\Scheduler\Handler;

use App\Scheduler\Message\ErrorReports;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ErrorReportsHandler
{
    public function __invoke(ErrorReports $message): void 
    {
        $message->main();
        sleep(5);
    }
}