<?php
/*
use App\Class\CommandDetect;
use App\Class\Utils;
*/

use App\Server\CommandDetectCopy;
use App\Server\DataResponse;
use App\Server\DbRequest;
use Symfony\Component\Console\Output\ConsoleOutput;

//use Psr\Log\LoggerInterface;

//use App\Class\DbRequest;

date_default_timezone_set('Europe/Paris');
//require_once(__DIR__.'/config.php');
require_once __DIR__.'/configServer/config.php';
require_once __DIR__.'/configServer/dbConfig.php';

/*
require_once(CLASS_PATH.'CommandDetect.php');
require_once(CLASS_PATH.'DataResponse.php');
require_once(CLASS_PATH.'DbRequest.php');
*/
require_once(__DIR__.'/src/Server/CommandDetectCopy.php');
require_once(__DIR__.'/src/Server/DataResponse.php');
require_once(__DIR__.'/src/Server/DbRequest.php');
//require_once(CLASS_PATH.'Utils.php');
//require_once(CLASS_PATH.'SocketObj.php');

/* ######################### SERVER ######################### */
//$logger = new LoggerInterface();
echo "\r\n##################################################\r\n";
echo "\r\n##################################################\r\n";
echo "\r\n##################################################\r\n";
echo "\r\n==========   SERVER STARTED   ==========\r\n";
echo "\r\n##################################################\r\n";
echo "\r\n##################################################\r\n";
echo "\r\n##################################################\r\n";
$msg = "\r\n==========   SERVER STARTED   ==========\r\n";

//$output = new ConsoleOutput();
//$output->writeln($msg);
//$logger->info("=====   Server Started   =====\n");
error_reporting(E_ALL);
/* Autorise l'exécution inifnie du script en attente de connexion */
set_time_limit(0);
//set_time_limit(60);
/* Turn on implicit output flushing so we see what we're getting as it comes in. */
ob_implicit_flush();
#$address = '192.168.1.193';

/*
$sockObj = new SocketObj;
$sockArray = $sockObj->createSocket();
*/
/*
if($sockObj->createSocket()) // Create socket
{
	// create a list of all the clients that will be connected to us..
	// add the listening socket to this list
	$clients = array($sock);
	$clientsInfo = array(array("sn unknown", "ip unknown", hrtime(true)));
	$timeOut = 300000000000;
	echo "Clients: ".count($clients);
}
*/
/*
$clients = $sockArray[0];
$clientsInfo = $sockArray[1];
$sock = $sockArray[2];
*/
// create a streaming socket, of type TCP/IP
$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($sock, ADDRESS, PORT);
socket_listen($sock);
// create a list of all the clients that will be connected to us..
// add the listening socket to this list
$clients = array($sock);
$clientsInfo=array(array("sn unknown","ip unknown",hrtime(true)));
$timeOut=300000000000;
/*
// create a list of all the clients that will be connected to us..
// add the listening socket to this list
$clients = array($sock);
$clientsInfo = array(array("sn unknown", "ip unknown", hrtime(true)));
$timeOut = 300000000000;
*/
/*
function writeLog($log){
    //$fd_log = fopen(GATELOG_PATH.GATELOG_FILE, 'a');
    //fwrite($fd_log, $log);
    //fclose($fd_log);
}
*/
/*
function dataToTreat($data)
{
	$cmdSoft = array('FE', 'FD', 'FC', 'FA', 'F9', 'F8', 'F7', 'F6', 'F5', 'F3', 'F2','DE','DD','DC','DB','DA','D9','D8','CF');
	if(isset($data[20]) && !empty($data[20])){
		$cmdRec = $data[20].$data[21];
		echo "\r\n"."Command received : ".$cmdRec."\r\n";
		if(in_array($cmdRec, $cmdSoft)){
			$sn = substr($data, 0, 20);
			return true;
		}
	}
	return false;
}
*/
function dataToTreat($data){
	if(isset($data[0]) && !empty($data[0])){
		$cmdRec = $data[20].$data[21];
        if (cmdByte[$cmdRec]) {
            return true;
        }
        return false;
	}
}

while(true)
{
	
    // get a list of all the clients that have data to be read from
    // if there are no clients with data, go to next iteration
	reset($clients);
	reset($clientsInfo);
	
	for($i=1; $i<count($clients); $i++)
	{
		next($clients);
		next($clientsInfo);
		if(isset(current($clientsInfo)[2])){
			// If process takes too much time, close socket ?
			if(current($clientsInfo)[2] < hrtime(true)){
				echo "\n".date("Y-m-d H:i:s | ")."client ".current($clientsInfo)[0]." ip ".current($clientsInfo)[1]." with key ".key($clients)." disconnected.\n";

				$key = key($clients);
				socket_close($clients[$key]);
				unset($clients[$key]);	
				unset($clientsInfo[$key]);				
			}
		}	
	}
	reset($clients);

    // create a copy, so $clients doesn't get modified by socket_select()
    $read = $clients;
    $write = null;
    $except = null;
	
    if (socket_select($read, $write, $except, 0) < 1)
	{
        continue;
	}

    // check if there is a client trying to connect
    if (in_array($sock, $read))
    {	
        $newsock = socket_accept($sock);
        socket_getpeername($newsock, $ip, $port);
        echo "\r\n".date("Y-m-d H:i:s | ")."New client connected: {$ip} : {$port}\r\n";	
        $clients[] = $newsock; // put accepted socket in a client array
        $key = array_search($sock, $read); 
        unset($read[$key]);
		$key = array_search($newsock, $clients);
		$clientsInfo[$key][0] = "sn unknown";
		$clientsInfo[$key][1] = "{$ip} : {$port}";
		$clientsInfo[$key][2] = hrtime(true)+$timeOut;
		/*	
		$index = array_search($newsock,$clients);
		$clientsSN[$index]='unknown';
		$clientsTimeOut[$index] = hrtime(true)+30000000000;
		*/									   
		
        //echo "\n"."There are ".count($clients)." client(s) connected to the server\n";
		//$sockData = socket_read($newsock, 4096, PHP_BINARY_READ) or die("Could not read input\n");
		//echo "\r\nData : ".$sockData;
    }
    
	$time_start = microtime(true);
	$memory_start = memory_get_usage(true);

	// loop through all the clients that have data to read from
	//echo " readsock length : ".sizeof($read);

	//TODO if read > 1 -->
	
    foreach ($read as $read_sock)
    {
		// read until newline or 1024 bytes
		// socket_read while show errors when the client is disconnected, so silence the error messages
		$data = @socket_read($read_sock, 4096, PHP_BINARY_READ) or die("Could not read input\n");
		//$data = socket_read($read_sock, 1024, PHP_BINARY_READ);
		//echo $data;
		//$task = new commandDetect();
		$request = new DbRequest();
		//$dataResponse = new dataResponse($deviceType);


		// check if the client is disconnected
		// TODO if this working?
		/*   
		if (!$data)
		{
			// remove client for $clients array
			$key = array_search($read_sock, $clients);
			if($key){				
				echo "\n".date("Y-m-d H:i:s | ")."client ".$clientsSN[$key]." ip ".$ip."disconnected.\n";
				socket_close($clients[$key]);
				unset($clients[$key]);
				$clientsSN[$key]='';
				//var_dump($clients);
				continue;
			}
		}
		*/
		
		
		//=> If data exists
        if (!empty($data))
        {
			reset($clientsInfo);
			echo "\r\n##################################################\r\n";
			echo $data[20]!=0&&$data[21]!=0? "\r\n********************* CONNECTED LIST : ".$data[20].$data[21]." *****************************\r\n": "\r\n********************* Connected list".""."*****************************\r\n";
			echo "\r\n##################################################\r\n";
			//=> Initiate client info (id, sn, ip, time) and update it at each iteration
			for($i=1; $i<count($clients); $i++){
				next($clientsInfo);
				echo "\r\n".$i." | SN : ".current($clientsInfo)[0]." | IP : ".current($clientsInfo)[1]." | \r\nTime : ".date("H:i:s")." | Date : ".date("Y-m-d")."\r\n";
				//echo "\r\n".$i." | SN : ".current($clientsInfo)[0]." | IP : {$ip} | \r\nTime : ".date("H:i:s")." | Date : ".date("Y-m-d")."\r\n";
			}
			//$task = new commandDetect();
			//=> if no commands are returned from data device
			
            if(!dataToTreat($data)) //=> msg to Device
			{	
                //echo "\r\n{$ip} send {$data} from {$read_sock} to ?? with SN ".$clientsInfo[$key][0]."\r\n";
				//echo "\r\nData length : ".strlen($data)."\r\n"; // check data length
				if(($data[0] == 'W') && (strlen($data) == 20)) 
				{
					$key = 0;
					//TODO find if $linkConnection exists???
					if(isset($linkConnection)){
						$key = array_key_exists($data, $linkConnection);
						echo "\r\n Add link connection >>>>>>>>>>>>>>>>>>>>> ".$key." !!!!!!!\n";
						if($key)
						{
							if(isset($linkConnection[$data][1]) && !empty($linkConnection[$data][1]))
							{
								$key1 = array_search($linkConnection[$data][1], $clients);
								if($key1)
								{
									socket_close($clients[$key1]);
									unset($clients[$key1]);
									unset($clientsInfo[$key1]);	
								}
							}
							$linkConnection[$data][1] = $read_sock;
							//var_dump($linkConnection);
						}
					}
					$dataResponse->writeLog("SEND MSG TO $sn >>>>>>>>>>>>>>>>>>>>> $key\n");
					//$task->writeLog($key);

					echo "SEND MSG TO $sn >>>>>>>>>>>>>>>>>>>>> $key\n";
					socket_write($sn, $key);
					
				}
				else
				{
					$sn = substr($data, 0, 20);
					$deviceType = hexdec($data[3].$data[4]);
					$dataToSend = substr($data, 20);
					//echo "\nsock == {$sock} - readSock = {$read_sock}\n";
					$canal = ord($data[21]);
					echo '\r\nCanal : '.$canal.'\r\n';
					if($canal === 255){
						echo "Replace sock 1\n";
						$key = array_search($linkConnection[$sn][1], $clients);

						if($key){
							socket_close($clients[$key]);
							unset($clients[$key]);
							unset($clientsInfo[$key]);	
						}

						$linkConnection[$sn][1] = $read_sock;
						//var_dump($linkConnection);
						$dataResponse->writeLog('SEND MSG TO RSHOCK >>>>>>>>>>>>>>>>>>>>> '.$data."\n");
						//$task->writeLog($data);
						echo 'SEND MSG TO RSHOCK >>>>>>>>>>>>>>>>>>>>> '.$data."\n";

						if(FALSE === socket_write($linkConnection[$sn][0], $data))
						{
							$key = array_search($linkConnection[$sn][0], $clients);
							if($key){
								socket_close($clients[$key]);
								unset($clients[$key]);
							}
						}
					} 
					else 
					{
						//$dataResponse->writeLog('SEND MSG TO OTHER >>>>>>>>>>>>>>>>>>>>> '.$data."\n");
						//$dataResponse->writeLog($data);
						echo 'SEND MSG TO OTHER >>>>>>>>>>>>>>>>>>>>> '.$data."\n";
						//var_dump($linkConnection);
						socket_write($linkConnection[$sn][1], $data);
					}
					//var_dump($linkConnection);
				}
                unset($data);
            }
			// if commands are returned from device data
			else
			{ // => msg from device
				$key = array_search($read_sock, $clients);			
				$clientsInfo[$key][2] = hrtime(true)+$timeOut;
				echo "\r\n".date("Y-m-d H:i:s | ")."Msg received with IP: {$ip} | SN: ".$clientsInfo[$key][0]." | \r\n ".$key." | Command : {$data[20]}{$data[21]} | RX : ".$data."\r\n"; //{$data}
				
				if(substr($data, 0, 1) == 'W'){ // Verify that data comes from a device (all devices start with W)
					$task = new CommandDetectCopy();
					$sn = substr($data, 0, 20);
					$deviceType = hexdec($data[3].$data[4]);
					//echo "Device Type ".$deviceType;
					// TODO Uncomment to test function
					
					
					$clientsInfo[$key][0] = $sn; // Show serial number in terminal

					$dataResponse = new DataResponse();
					//$utils = new Utils($deviceType);
					//$device = $task->getDeviceVariables($data, $dataResponse);
					//$deviceCommand = $device["Command"];
					$deviceCommand = $data[20].$data[21];

					$response = $task->start($data, $ip);
					//$cmdRec = $data[20].$data[21];
					
					//echo $deviceCommand." ".$cmdRec;
					
					$affResponse = bin2hex($response);
					$dataResponse->writeCommandLog($sn, $deviceType, "\r\n".date("Y-m-d H:i:s | ")."Msg send : {$affResponse} \n| SN : ".$clientsInfo[$key][0]."\n| Command : ".$deviceCommand." from server\r\n");
					echo "\r\n".date("Y-m-d H:i:s | ")."Msg send : ".strlen($affResponse)." \n| SN : ".$clientsInfo[$key][0]."\n| Command : ".$deviceCommand." from server\r\n";
					//$dataResponse->writeLog($sn, $deviceType, "\r\n".date("Y-m-d H:i:s | ")."Msg send : \n| SN : ".$clientsInfo[$key][0]."\n| Command : ".$deviceCommand." from server\r\n");
					//echo "\r\n".date("Y-m-d H:i:s | ")."Msg send : \n| SN : ".$clientsInfo[$key][0]."\n| Command : ".$deviceCommand." from server\r\n";

					if (($deviceCommand === 'F9') || ($deviceCommand === 'FA') || ($deviceCommand === 'FE') || ($deviceCommand === 'DE')){
						if(isset($linkConnection[$sn]) && !empty($linkConnection[$sn][0])){
							//$key = array_search($linkConnection[$sn][0], $clients);
							$key = array_search($linkConnection[$sn][0], $clients);
							//echo "key1: ".$key1;
							//echo "key: ".$key;
							if($key)
							{
								/*
								socket_close($clients[$key1]);
								unset($clients[$key1]);
								unset($clientsInfo[$key1]);	
								*/
								socket_close($clients[$key1]);
								unset($clients[$key1]);
								unset($clientsInfo[$key1]);	
								echo "socket is closed !";
							}
							$linkConnection[$sn][0] = $read_sock;
						}else{
							$linkConnection[$sn][0] = $read_sock;
						}
					}
					//var_dump($linkConnection);
					$dataResponse->writeCommandLog($sn, $deviceType, "\r\n".date("Y-m-d H:i:s | ").$response."\r\n");
					socket_write($linkConnection[$sn][0], $response);
					unset($data);
				}
				else
				{
					$key = array_search($read_sock, $clients);
					if($key){
						socket_close($clients[$key1]);
						unset($clients[$key1]);
						unset($clientsInfo[$key1]);	
						//var_dump($clients);
					}				
				}
				
            }
        }
		else
		{
			echo "Data doesn't exist !";
		}

    } 

	$memory_end = memory_get_usage(true);
	$time_end = microtime(true);
	$execution_time = ($time_end - $time_start);
	echo "\r\nTotal Execution Time: ".($execution_time*1000)." Milliseconds\r\n";
	$execution_memory = ($memory_end - $memory_start);
	echo "\r\nTotal Execution Memory: ".($execution_memory)." \r\n";
	// end of reading foreach
} //while(true);

// close the listening socket
socket_close($sock);