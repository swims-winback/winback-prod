<?php

namespace App\Command;


use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;
class UpdateCode extends Command
{
    //#[AsCommand(name: "app:updateCode")]
     protected static $defaultName = 'app:updateCode';
    protected function configure()
    {
        //...
    ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    { 
        $pathToPython = "./src/Process/UpdateCode.py";
        $pathToFile = "./src/Process/input/products_registered.xlsx";

        $helper = $this->getHelper('question');

        $process = new Process(['python', $pathToPython, $pathToFile]);
        $process->start();
        foreach ($process as $type => $data) {
            if ($process::OUT === $type) {
                echo "\nDebug :".$data;
            } else { // $process::ERR === $type
                echo "\nErreur : ".$data;
            }
        }
        echo $process->getOutput();
        return 0;
    }
}