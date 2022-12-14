<?php
$a = [0, 1, 2, 3];
$b = array_slice($a, 0, 3);

print_r($a);
/*
function setDeviceInfo(string $sn, string $vers, int $devType, string $ipAddr, string $logFile) {

}
function initAutoDetectResponse($nbData = 39){
    for($aInit = 0; $aInit < $nbData; $aInit++){
        $aResponse[$aInit] = chr(0);
    }
    return $aResponse;
}
$aResponse = initAutoDetectResponse();
print_r ($aResponse);
*/
/*
$prout_array = array(54=>"ga", 25=>"ga", 34=>"ga", 38=>"bou", 45=>"bou", "bou", "bu", "bu", "bu", "ble", "bli", "bli", "blo", "bla", "gla", "glou");
$time_start = microtime(true);
$occurence = 1;
$time_array = array();
*/
/*
foreach ($prout_array as $i => $value) {
    echo $i;
}
*/
/*
for ($i=0; $i < sizeof($prout_array); $i++) { 
    # code...
    
    if ($i != 0) {
        
        if ($prout_array[$i] == $prout_array[$i-1]) {
            $occurence ++;
        }
        //if ($value != $prout_array[$i-1]) {
        else {
            $time_array[$i][0] = $occurence;
            echo "\r\n".$occurence."\r\n";
            $time_end = microtime(true);
            $execution_time = ($time_end - $time_start)*1000;
            echo "\r\n".$execution_time."\r\n";
            echo "========";
            $time_array[$i][1] = $prout_array[$i];
            echo "\r\n".$prout_array[$i]."\r\n";
            $time_start = microtime(true);
            $occurence = 1;
        }
        
    }

}
print_r($time_array);
*/

/*
print_r($prout_array);
function deleteElem($array, $index) {
    //unset($array[$index]);
    array_splice($array, $index, 1);
    return $array;
}
$prout_array= deleteElem($prout_array, 1);
print_r($prout_array);
foreach ($prout_array as $i=>$prout){
//for ($i=0; $i < sizeof($prout_array); $i++) {
    //next($prout_array);
    print_r($i);
    print_r(key($prout_array));
}
*/
/*
for($i=1; $i<10; $i++){
    next($clientsInfo);
    //echo "\r\n".$i." | SN : ".current($clientsInfo)[0]." | IP : ".current($clientsInfo)[1]." | \r\nTime : ".date("H:i:s")." | Date : ".date("Y-m-d")."\r\n";
    //$output->writeln("\r\n".$i." | SN : ".current($clientsInfo)[0]." | IP : ".current($clientsInfo)[1]." | \r\nTime : ".date("H:i:s")." | Date : ".date("Y-m-d")."\r\n");
    $output->writeln("\r\n".$i." | SN : ".current($clientsInfo)[0]." | IP : ".current($clientsInfo)[1]." | \r\nTime : ".date("H:i:s")." | Date : ".date("Y-m-d")."\r\n");
}
*/
//$array = array("blublu", "blabla", "blublu");
//print_r(array_keys($array, "blublu"));
//$device = "BACK4";
//print_r(scandir("./public/Ressource/library/".$device));