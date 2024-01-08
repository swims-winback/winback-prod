<?php

namespace App\Command;


use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;
class UpdateSn extends Command
{
    //#[AsCommand(name: "app:updateSn")]
     protected static $defaultName = 'app:updateSn';
    protected function configure()
    {
        //...
    ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    { 
        $pathToPython = "./src/Process/UpdateSn.py";
        $pathToJson = "./src/Process/academy.json";

        $helper = $this->getHelper('question');
        #fileQuestion
        $fileQuestion = new Question('Please enter filename: ', 'PRODUCT_SELLING_2024');
        $filename = $helper->ask($input, $output, $fileQuestion);
        $output->writeln('You have just selected: '.$filename);
        
        #monthQuestion
        $monthQuestion = new ChoiceQuestion(
            'Please select month ',
            ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'],
            0
        );
        $monthQuestion->setErrorMessage('Month %s is invalid.');
        $month = $helper->ask($input, $output, $monthQuestion);
        $output->writeln('You have just selected: '.$month);

        $process = new Process(['python', $pathToPython, $pathToJson, $filename, $month]);
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