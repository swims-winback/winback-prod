<?php
namespace App\Server;

use App\Server\TCPServer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Process\Process;

class TCPProcess extends AbstractController
{
    //#[Route('/admin/dashboard/', name: 'dashboard')]
	public function index()
	{
		$response = "hello";
        //$server = new TCPServer;
        //$process = new Process([$server]);
        /*
        $process->start();

        foreach ($process as $type => $data) {
            if ($process::OUT === $type) {
                //echo "\nRead from stdout: ".$data;
                echo "\nRead from stdout: ";
            } else { // $process::ERR === $type
                //echo "\nRead from stderr: ".$data;
                echo "\nRead from stderr: ";
            }
        }
        */
        /*
		$server->runServer(function ($type, $buffer) {
			if (Process::ERR === $type) {
				echo 'ERR > '.$buffer;
			} else {
				echo 'OUT > '.$buffer;
			}
		});
		*/

        /*
        $process->start();
        do {
            $process->checkTimeout();
            //$process->getOutput();
        } while ($process->isRunning() && (sleep(1) !== false));
        if (!$process->isSuccessful()) {
        throw new \Exception($process->getErrorOutput());
        }
        */

        /*
		return $this->render('dashboard.html.twig', [
			'response'=> $response,
            //'result'=> $result
        ]);
        */
	}
}

