<?php

for($i=1; $i<10; $i++){
    next($clientsInfo);
    //echo "\r\n".$i." | SN : ".current($clientsInfo)[0]." | IP : ".current($clientsInfo)[1]." | \r\nTime : ".date("H:i:s")." | Date : ".date("Y-m-d")."\r\n";
    //$output->writeln("\r\n".$i." | SN : ".current($clientsInfo)[0]." | IP : ".current($clientsInfo)[1]." | \r\nTime : ".date("H:i:s")." | Date : ".date("Y-m-d")."\r\n");
    $output->writeln("\r\n".$i." | SN : ".current($clientsInfo)[0]." | IP : ".current($clientsInfo)[1]." | \r\nTime : ".date("H:i:s")." | Date : ".date("Y-m-d")."\r\n");
}