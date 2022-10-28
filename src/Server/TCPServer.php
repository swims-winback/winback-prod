<?php
namespace App\Server;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Server\CommandDetect;
use App\Server\DataResponse;
use App\Server\DbRequest;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

require_once dirname(__FILE__, 3).'/configServer/config.php';
require_once dirname(__FILE__, 3).'/configServer/dbConfig.php';

require_once(dirname(__FILE__, 2).'/Server/CommandDetect.php');
require_once(dirname(__FILE__, 2).'/Server/DataResponse.php');
require_once(dirname(__FILE__, 2).'/Server/DbRequest.php');

class TCPServer extends AbstractController
{
	private $timeOut;
	/**
	 * @linkConnection : 	 array of sn connected as key and array of sockets linked to this sn as values (ex: [WIN0D_TEST_61706    ] => Array), the subarray is a key-index paired with each socket (ex: [1] => Socket Object())
	 * @clients : 
	 * 
	 */
	private $linkConnection = [];
	private $clients;

	function __construct()
	{
		
	}

	// Create Socket and connect to server
	function createServer()
	{
		$request = new DbRequest;

		set_time_limit(0);
		ob_implicit_flush();

		$msg = str_repeat("\r\n".str_repeat("#", 30)."\r\n", 3)."\r\n==========   SERVER STARTED   ==========\r\n".str_repeat("\r\n".str_repeat("#", 30)."\r\n", 3);
		
		echo($msg);
		$request->setConnectAll(0);

		$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		// set the option to reuse the port
		socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
		// bind the socket to the address defined in config on port
		if (socket_bind($sock, ADDRESS, PORT) === false) {
			echo "socket_bind() a échoué : raison : " . socket_strerror(socket_last_error($sock)) . "\n";
			return false;
		}
		// start listen for connections
		if (socket_listen($sock, 5) === false) {
			echo "socket_listen() a échoué : raison : " . socket_strerror(socket_last_error($sock)) . "\n";
			return false;
		}
		
		$this->clients = array($sock);
		$resultArray = array($this->clients, $sock);
		return $resultArray;
	}

	// Verify if command in data exists in command array
	function dataToTreat($data){
		
		if(isset($data[20]) && !empty($data[20])){
			$cmdRec = $data[20].$data[21];
			if (in_array($cmdRec, cmdSoft)) {
				return true;
			}
			return false;
		}
		
	}

	/*
	function disconnectServer($clients, $clientsInfo, $output)
	{
		for($i=1; $i<count($clients); $i++)
		{
			next($clients);
			next($clientsInfo);
			if(isset(current($clientsInfo)[2])){
				// If process takes too much time, close socket ?
				if(current($clientsInfo)[2] < hrtime(true)){
					$output->writeln("\r\n".date("Y-m-d H:i:s | ")."client ".current($clientsInfo)[0]." ip ".current($clientsInfo)[1]." with key ".key($clients)." disconnected.\n");
					$this->writeServerLog("\n".date("Y-m-d H:i:s | ")."client ".current($clientsInfo)[0]." ip ".current($clientsInfo)[1]." with key ".key($clients)." disconnected.\n");
					$key = key($clients);
					
					socket_close($clients[$key]);
					$output->writeln("\r\nSocket closed !\r\n");
					unset($clients[$key]);	
					unset($clientsInfo[$key]);				
				}
			}	
		}
		//reset($clients);
		return false;
	}
	*/

	/**
	 * Write server logs to a log file with date as filename
	 *
	 */
	function writeServerLog(string $logTxt){
		if (!file_exists(LOG_PATH."server/")) {
			mkdir(LOG_PATH."server/", 0777, true);

		}
		$logFile = date("Y-m-d").".txt";
		if (file_exists(LOG_PATH."server/".$logFile) && filesize(LOG_PATH."server/".$logFile) < 200000) {
			$fd = fopen(LOG_PATH."server/".$logFile, "a+");
			if($fd){
				fwrite($fd, $logTxt);
				fclose($fd);
				return $logFile;
			}else{
				echo "fd error";
			}

		}
		else {
			$fd = fopen(LOG_PATH."server/".$logFile, "w");
			if($fd){
				fwrite($fd, $logTxt);
				fclose($fd);
				return $logFile;
			}else{
				echo "fd error";
			}
		}
	}

	function runServer()
	{
		/**
		 * @clientsInfo : array of sn connected as key and array of info linked to this sn as values (ex: [WIN0D_TEST_61706    ] => Array), the subarray is a key-index paired with each info (ex: [1] => 'ip adress : port')
		 */
		//$output = new ConsoleOutput();
		$request = new DbRequest();
		
		$resultArray = $this->createServer();
		$clients = $resultArray[0];
		$sock = $resultArray[1];
		$clientsInfo = array(array("sn unknown","ip unknown",hrtime(true)));
		$this->timeOut = 300000000000;
		
		while (true)
		{
			
			

			// get a list of all the clients that have data to be read from
			// if there are no clients with data, go to next iteration
			reset($clients);
			reset($clientsInfo);
			//TODO Close socket automatically after too much time without receiving data
			//foreach ($clients as $i=>$client)
			foreach ($clientsInfo as $i=>$client) 
			//for($i=1; $i<count($clients); $i++)
			{
				if ($i > 0) {
					if(isset($clientsInfo[$i][2])){
						// If process takes too much time, close socket
							if($clientsInfo[$i][2] < hrtime(true)){
								
								//$key = key($clients);
								$request->setConnect(0, $clientsInfo[$i][0]);
								//socket_close($clients[$key]);
								socket_close($clients[$i]);
								//TODO $output->writeln("\r\nSocket closed !\r\n");
								//$this->writeServerLog("\n".date("Y-m-d H:i:s | ")."client ".current($clientsInfo)[0]." ip ".current($clientsInfo)[1]." with key ".$i." disconnected.\n");
								//echo "\n".date("Y-m-d H:i:s | ")."client ".current($clientsInfo)[0]." ip ".current($clientsInfo)[1]." with key ".$i." disconnected.\n";
								echo "\n".date("Y-m-d H:i:s | ")."client ".$clientsInfo[$i][0]." ip ".$clientsInfo[$i][1]." with key ".$i." disconnected.\n";
								//$this->writeServerLog("\n".date("Y-m-d H:i:s | ")."client ".$clientsInfo[$i][0]." ip ".$clientsInfo[$i][1]." with key ".[$i]." disconnected.\n");
								unset($clients[$i]);	
								unset($clientsInfo[$i]);
								//array_splice($clients, $i, 1);
								//array_splice($clientsInfo, $i, 1);
							}
					}
				}
			}
			
			reset($clients);

			// create a copy, so $clients doesn't get modified by socket_select()
			$read = $clients;
			$write = null;
			$except = null;

			if (socket_select($read, $write, $except, 0) < 1) {
				continue;
			}

			// check if there is a client trying to connect

			if (in_array($sock, $read))
			{	
				$newsock = socket_accept($sock);
				$clients[] = $newsock;
				socket_getpeername($newsock, $ip, $port);
				echo "\r\n".date("Y-m-d H:i:s | ")."New client connected: {$ip} : {$port}\r\n";
				// remove the listening socket from the clients-with-data array
				$key = array_search($sock, $read); 
				unset($read[$key]);
				unset($key);
				$key = array_search($newsock, $clients);
				$clientsInfo[$key][0] = "sn unknown";
				$clientsInfo[$key][1] = "{$ip} : {$port}";
				$clientsInfo[$key][2] = hrtime(true)+$this->timeOut;
				$clientsInfo[$key][3] = "{$ip}";
				$clientsInfo[$key][4] = "{$port}";
				$clientsInfo[$key][5] = ""; //command history
				$clientsInfo[$key][6] = ""; //index
				$clientsInfo[$key][7] = ""; //device info
				$clientsInfo[$key][8] = ""; //percentage
			}

			// loop through all the clients that have data to read from
			
			//foreach ($read as $i=>$read_sock)
			foreach ($read as $read_sock)
			{
				// read until newline or 1024 bytes
				$data = @socket_read($read_sock, 4096, PHP_BINARY_READ);// or die("Could not read input\n");

					//=> If data exists
					if (!empty($data))
					{
						
						reset($clientsInfo);

						echo "\r\n********************* Connected list *****************************\r\n";
						//$this->writeServerLog("\r\n********************* Connected list *****************************\r\n");
						/*
						if ($i > 0) {
							echo "\r\n".$i." | SN : ".$clientsInfo[$i][0]." | IP : ".$clientsInfo[$i][1]." | \r\nTime : ".date("H:i:s")." | Date : ".date("Y-m-d")." | Time : ".end($clientsInfo[$i][7])." | Cmd : ".end($clientsInfo[$i][5])."\r\n";
						}
						*/
						//=> Initiate client info (id, sn, ip, time) and update it at each iteration
						// if sn or ip from db not in connected list
						for($i=1; $i<count($clients); $i++){
						//foreach ($clients as $i=>$client){
							next($clientsInfo);
							if (isset(current($clientsInfo)[1]) && isset(current($clientsInfo)[0])) {
								echo "\r\n".$i." | SN : ".current($clientsInfo)[0]." | IP : ".current($clientsInfo)[1]." | \r\nDate : ".date("Y-m-d")." | Cmd : ".current($clientsInfo)[5]." | Percentage : ".current($clientsInfo)[8]."\r\n";
								//$this->writeServerLog("\r\n".$i." | SN : ".current($clientsInfo)[0]." | IP : ".current($clientsInfo)[1]." | \r\nTime : ".date("H:i:s")." | Date : ".date("Y-m-d")."\r\n");
								
							}
						}
						
						
						// if commands are returned from device data

						if($this->dataToTreat($data)) // => msg from device
						{ 
							$deviceKey = array_search($read_sock, $clients);			
							$clientsInfo[$deviceKey][2] = hrtime(true)+$this->timeOut;
							if(substr($data, 0, 1) == 'W' && array_key_exists(hexdec($data[3].$data[4]), deviceType)){ // Verify that data comes from a device (all devices start with W)
								$time_start_socket = microtime(true);
								$task = new CommandDetect();
								$sn = substr($data, 0, 20);
								//$deviceType = hexdec($data[3].$data[4]);
								$clientsInfo[$deviceKey][0] = $sn; // Show serial number in terminal
								$deviceCommand = $data[20].$data[21];
								
								$responseArray = $task->start($data, $clientsInfo[$deviceKey][3], $clientsInfo[$deviceKey][7]);

								if ($responseArray != False) {
									// récupérer deviceInfo
									if (array_key_exists(2, $responseArray)) {
										$clientsInfo[$deviceKey][7] = $responseArray[2];
									}
									
									if (array_key_exists(0, $responseArray)) {
										$indexToGet = $responseArray[0];
										//register dc index in array
										if ($indexToGet != $clientsInfo[$deviceKey][6]) {
											socket_write($clients[$deviceKey], $responseArray[1]);
											$clientsInfo[$deviceKey][6] = $indexToGet;
											//echo "Index: ".$indexToGet;
										}
									}
									if (array_key_exists(3, $responseArray)) {
										$percentage = $responseArray[3];
										//echo "\r\n".$percentage."\r\n";
										//register dc index in array
										if ($percentage != $clientsInfo[$deviceKey][8]) {
											$time_start = microtime(true);
											echo "\r\n".$percentage." and ".$clientsInfo[$deviceKey][8]."\r\n";
											$clientsInfo[$deviceKey][8] = $percentage;
											$request->setDownload($sn, $percentage);
											$time_end = microtime(true);
											$execution_time = ($time_end - $time_start)*1000;
											echo "\r\n".$execution_time."\r\n";

											//socket_write($clients[$deviceKey], $responseArray[1]);
											
										}
									}
									else {
										socket_write($clients[$deviceKey], $responseArray[1]);
									}
									
									//socket_write($clients[$key], $responseArray[1]);
								}
								else {
									$this->writeServerLog("\r\nResponse is empty! Please check that your device can connect to the server!\r\n");
								}
								
								/*
								$process = new Process([$task->start($data, $clientsInfo[$key][3], $clients[$key])]);
								$process->start();
								*/

								/*
								foreach ($process as $type => $outputProcess) {
									if ($process::OUT === $type) {
										echo "\nRead from stdout: ".$outputProcess;
									} else { // $process::ERR === $type
										echo "\nRead from stderr: ".$outputProcess;
									}
								}
								*/
								
								//$response = $task->start($data, $clientsInfo[$key][3]);
								//$responseArray = $task->start($data, $clientsInfo[$key][3]);
								//$response = $responseArray[1];
								
								$clientsInfo[$deviceKey][5] = $deviceCommand;
								
								//if (($deviceCommand === 'F9') || ($deviceCommand === 'FA') || ($deviceCommand === 'FE') || ($deviceCommand === 'DE')){
								
								if(isset($this->linkConnection[$clientsInfo[$deviceKey][0]]) && !empty($this->linkConnection[$clientsInfo[$deviceKey][0]][0])){
									
									foreach ($clientsInfo as $i=>$client) { 
									//for ($i=1; $i < count($clientsInfo); $i++) {

										if( isset($clientsInfo[$i][0]) && isset($clientsInfo[$deviceKey][0]) && $clientsInfo[$i][0] == $clientsInfo[$deviceKey][0])
										{
											//var_dump($i);
											//var_dump($deviceKey);
											if($i!=$deviceKey)
											//if($this->linkConnection[$clientsInfo[$key][0]][0]!=$clients[$i] && $i!=$key)
											{

												echo("socket is closed :".$clientsInfo[$i][0]."with key: ".$i);
												$this->writeServerLog("socket is closed :".$clientsInfo[$i][0]."with key: ".$i);
												$key2del = array_search($this->linkConnection[$clientsInfo[$deviceKey][0]][0], $clients);

												//$request->setConnect(0, $clientsInfo[$i][0]);
												socket_close($clients[$i]);
												unset($clients[$i]);
												unset($clientsInfo[$i]);
												//array_splice($clients, $i, 1);
												//array_splice($clientsInfo, $i, 1);

											}
										}
									}
									
									$this->linkConnection[$sn][0] = $read_sock;
								}else{
									$this->linkConnection[$sn][0] = $read_sock;
								}
								//}

								$time_end_socket = microtime(true);
								$execution_time_socket = ($time_end_socket - $time_start_socket);
								echo "\r\nTotal Execution Time Socket: ".($execution_time_socket*1000)." Milliseconds\r\n";
								unset($data);
								
							}
							else
							{
								$key = array_search($read_sock, $clients);
								if($key){
									socket_close($clients[$key]);
									echo "\r\nSocket closed ! Data doesn't come from a device !\r\n";
									unset($clients[$key]);
									unset($clientsInfo[$key]);
								}				
							}
							
						}
						//=> if no commands are returned from data device
						else //=> msg to Device
						{	
							$key = array_search($read_sock, $clients);
							//$clientsInfo[$key][0] = "Computer ".$i;
							//echo "\r\n{$clientsInfo[$key][1]} send {$data} to {} with SN ".$clientsInfo[$key][0]."\r\n";
							//echo "\r\n{$ip}:{$port} send {$data} to IP ".$clientsInfo[$key][1]."\r\n";
							//TODO Device send data to computer
							echo "\r\n{$clientsInfo[$key][0]} send {$data} to computer.\r\n";

							//echo "\r\n{$ip} send {$data} from {$read_sock} to ?? with SN ".$clientsInfo[$key][0]."\r\n";
							//echo "\r\nData length : ".strlen($data)."\r\n"; // check data length
							if (strlen($data)<20) {
								//TODO $output->writeln("\r\nData : ".bin2hex($data)."\r\n");
							}

							// check data is a device & contains serial number

							//$this->linkConnection[$data][1] = $read_sock;
							if(($data[0] == 'W') && (strlen($data) == 20)) 
							{
								//$output->writeln(strlen($data1));
								//$key = 0;
								$sn = substr($data, 0, 20);
								//TODO find if $this->linkConnection exists & what to do if $this->linkConnection is not defined?
								//TODO change key 0 --> 1 if needed
								//$this->linkConnection[$data][0] = $read_sock;
								if(isset($this->linkConnection)){
									//TODO old version
									//echo $this->linkConnection;
									$keyLink = array_key_exists($data, $this->linkConnection);
									echo 'Add link connection >>>>>>>>>>>>>>>>>>>>> '.$keyLink." !!!!!!!\n";
									if($keyLink){
										//print_r($this->linkConnection);
										
										if(isset($this->linkConnection[$data][1]) && !empty($this->linkConnection[$data][1])){
											//Check if sn exists in clients
											$key1 = array_search($this->linkConnection[$data][1], $clients);
											
											if($key1){
												//print_r($clientsInfo[$key1]);
												echo "Socket close !!!!!!!\n";
												//$this->writeServerLog("\r\nSocket close :".$data."\r\n");
												$request->setConnect(0, $data);
												//$this->writeServerLog("\nsetConnect 0 ".$data."\n");
												socket_close($clients[$key1]);
												//TODO $this->writeServerLog("\n".date("Y-m-d H:i:s | ")."client ".$clientsInfo[$key1][0]." ip ".$clientsInfo[$key1][3]." with key ".key($clients)." disconnected.\n");
												unset($clients[$key1]);
												unset($clientsInfo[$key1]);	

											}
										}
										//var_dump($read_sock);
										//var_dump($clientsInfo[$key][1]);
										$this->linkConnection[$data][1] = $read_sock;
										
									}
									
								}
								//echo "SEND MSG TO >>>>>>>>>>>>>>>>>>>>> $key\n";
								echo "SEND ".$keyLink." TO ".$data." >>>>>>>>>>>>>>>>>>>>>\n";

								socket_write($read_sock, $keyLink);
							}
							else
							{
								//if((strlen($data) > 20))
								$sn = substr($data, 0, 20);
								$canal = ord($data[21]);
								echo "\r\nCanal : {$canal}\r\n";
								if($canal === 255){
									echo "\r\nReplace sock 1\r\n";
									echo "\r\nsize of linkConnection : ".sizeof($this->linkConnection)."\r\n";
									$keyCanal = array_search($this->linkConnection[$sn][1], $clients);
									if($keyCanal){
										//print_r($clients);
										socket_close($clients[$keyCanal]);
										//print_r($clients[$keyCanal]);
										//$request->setConnect(0, $sn);
										echo "\r\nSocket closed with Sn ".$sn." and keyCanal: ".$keyCanal." !\r\n";
										//array_splice($clients, $keyCanal, 1);
										//array_splice($clientsInfo, $keyCanal, 1);
										//print_r($clientsInfo);
										//print_r($clients);
										unset($clients[$keyCanal]);
										unset($clientsInfo[$keyCanal]);	
									}

									$this->linkConnection[$sn][1] = $read_sock;
									echo 'SEND MSG TO RSHOCK >>>>>>>>>>>>>>>>>>>>> '.$data."\n";


										//if(FALSE === socket_write($this->linkConnection[$sn][0], $data))
										if(socket_write($this->linkConnection[$sn][0], $data) === false)
										{
											$key = array_search($this->linkConnection[$sn][0], $clients);
											if($key){
												socket_close($clients[$key]);
												$request->setConnect(0, $sn);
												//array_splice($clients, $key, 1);
												//var_dump($clientsInfo[$key]);
												unset($clients[$key]);
												echo "\r\nSocket closed with Sn ".$sn." and keyFalse: ".$key." !\r\n";
											}
										}
									
								} 
								else 
								{
									echo 'SEND MSG TO OTHER >>>>>>>>>>>>>>>>>>>>> '.$data."\n";
									if (isset($this->linkConnection[$sn][1])) {
										socket_write($this->linkConnection[$sn][1], $data);
									}
									
								}
							}
							unset($data);
						}
					}

			}
			
			// end of reading foreach
		}
		// close the listening socket
		socket_close($sock);
		//$output->writeln("\r\nSocket closed ! Something is false.\r\n");
		echo "\r\nSocket closed ! Something is false.\r\n";
	}
}