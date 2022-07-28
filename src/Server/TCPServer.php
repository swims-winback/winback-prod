<?php
namespace App\Server;
/*
use App\Class\CommandDetect;
use App\Class\Utils;
*/
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Server\CommandDetect;
use App\Server\DataResponse;
use App\Server\DbRequest;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\StreamOutput;

//use App\Class\DbRequest;

require_once dirname(__FILE__, 3).'/configServer/config.php';
require_once dirname(__FILE__, 3).'/configServer/dbConfig.php';

require_once(dirname(__FILE__, 2).'/Server/CommandDetect.php');
require_once(dirname(__FILE__, 2).'/Server/DataResponse.php');
require_once(dirname(__FILE__, 2).'/Server/DbRequest.php');
//require_once(CLASS_PATH.'Utils.php');
//require_once(CLASS_PATH.'SocketObj.php');

class TCPServer extends AbstractController
{
	//private $sock;
	private $clients;
	private $clientsInfo;
	private $timeOut;
	private $linkConnection = [];
	private $key1;
	private $read;

	function __construct()
	{
		
	}

	// Create Socket and connect to server
	function createServer()
	{
		set_time_limit(0);
		ob_implicit_flush();

		$msg = str_repeat("\r\n".str_repeat("#", 30)."\r\n", 3)."\r\n==========   SERVER STARTED   ==========\r\n".str_repeat("\r\n".str_repeat("#", 30)."\r\n", 3);
		//$output = new StreamOutput(fopen('php://stdout', 'w'));
		$output = new ConsoleOutput();
		$output->writeln($msg);
		$request = new DbRequest;
		$request->setConnectAll(0);


		/*
		// create a streaming socket, of type TCP/IP
		$this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		// set the option to reuse the port
		socket_set_option($this->sock, SOL_SOCKET, SO_REUSEADDR, 1);
		// bind the socket to the address defined in config on port
		socket_bind($this->sock, ADDRESS, PORT);
		// start listen for connections
		socket_listen($this->sock);
		*/
		// create a streaming socket, of type TCP/IP
		/*
		if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
			echo "socket_create() a échoué : raison : " . socket_strerror(socket_last_error()) . "\n";
			return false;
		}
		*/
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
		//$this->clientsInfo = array(array("sn unknown","ip unknown",hrtime(true)));
		//$this->timeOut = 300000000000;

		//return $clients, $clientsInfo, $timeOut;
		//$resultArray = array($this->clients, $this->clientsInfo, $this->timeOut, $sock);
		$resultArray = array($this->clients, $sock);
		return $resultArray;
		//return $this->clients;
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

	function disconnectServer($clients, $clientsInfo, $output)
	{
		for($i=1; $i<count($clients); $i++)
		{
			next($clients);
			next($clientsInfo);
			if(isset(current($clientsInfo)[2])){
				// If process takes too much time, close socket ?
				if(current($clientsInfo)[2] < hrtime(true)){
					//echo "\n".date("Y-m-d H:i:s | ")."client ".current($clientsInfo)[0]." ip ".current($clientsInfo)[1]." with key ".key($this->clients)." disconnected.\n";
					$output->writeln("\n".date("Y-m-d H:i:s | ")."client ".current($clientsInfo)[0]." ip ".current($clientsInfo)[1]." with key ".key($clients)." disconnected.\n");
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

	/**
	 * Write server logs to a log file with date as filename
	 *
	 */
	function writeServerLog(string $logTxt){
		if (!file_exists(LOG_PATH."server/")) {
			mkdir(LOG_PATH."server/", 0777, true);

		}
		$logFile = date("Y-m-d").".txt";
		if (file_exists(LOG_PATH."server/".$logFile) && filesize(LOG_PATH."server/".$logFile) < 40000) {
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

	function closeSocket($key, $clients, $clientsInfo){
		//$request->setConnect(0, current($clientsInfo)[0]);
		//$this->writeServerLog("\nsetConnect 0 ".current($clientsInfo)[0]."\n");
		echo($clientsInfo[$key][0]);
		socket_close($clients[$key]);
		echo("\r\nSocket closed !\r\n");
		unset($clients[$key]);	
		unset($clientsInfo[$key]);
	}

	function runServer()
	{
		$output = new ConsoleOutput();
		$request = new DbRequest();
		$resultArray = $this->createServer();
		$clients = $resultArray[0];
		
		//$clients = $this->createServer();
		/*
		$clientsInfo = $resultArray[1];
		$this->timeOut = $resultArray[2];
		*/
		$clientsInfo = array(array("sn unknown","ip unknown",hrtime(true)));
		$this->timeOut = 300000000000;
		$sock = $resultArray[1];

		while (true)
		{
			// get a list of all the clients that have data to be read from
			// if there are no clients with data, go to next iteration
			//echo current($clientsInfo[0]);
			reset($clients);
			reset($clientsInfo);
			//echo current($clientsInfo[0]);
			//TODO Close socket automatically after too much time without receiving data
			//$request->setConnectAll(0);
			// 
			for($i=1; $i<count($clients); $i++)
			{
				//echo current($clientsInfo[0]);
				next($clients);
				next($clientsInfo);
				//echo current($clientsInfo[0]);
				if(isset(current($clientsInfo)[2])){
					// If process takes too much time, close socket ?
					if(current($clientsInfo)[2] < hrtime(true)){
						//echo "\n".date("Y-m-d H:i:s | ")."client ".current($clientsInfo)[0]." ip ".current($clientsInfo)[1]." with key ".key($this->clients)." disconnected.\n";
						$output->writeln("\n".date("Y-m-d H:i:s | ")."client ".current($clientsInfo)[0]." ip ".current($clientsInfo)[1]." with key ".key($clients)." disconnected.\n");
						//$this->writeServerLog("\n".date("Y-m-d H:i:s | ")."client ".current($clientsInfo)[0]." ip ".current($clientsInfo)[1]." with key ".key($clients)." disconnected.\n");
						$key = key($clients);
						//$this->closeSocket($key, $clients, $clientsInfo);
						
						$request->setConnect(0, current($clientsInfo)[0]);
						//$this->writeServerLog("\nsetConnect 0 ".current($clientsInfo)[0]."\n");
						socket_close($clients[$key]);
						$output->writeln("\r\nSocket closed !\r\n");
						unset($clients[$key]);	
						unset($clientsInfo[$key]);
										
					}
				}	
			}
			
			/*
			if ($this->disconnectServer($clients, $clientsInfo, $output)) {
				$request = new DbRequest;
				$request->setConnect(0, current($clientsInfo)[0]);
			}
			*/
			reset($clients);
			//$this->disconnectServer($clients, $clientsInfo, $output);

			// create a copy, so $clients doesn't get modified by socket_select()
			$read = $clients;
			$write = null;
			$except = null;
			/*
			if(socket_select($read, $write, $except, 0) == false)
			{
				echo "\r\nNo device found on the server. Check that device is switch on.\r\n";
				break;
			}
			*/
			//var_dump($read);
			if (socket_select($read, $write, $except, 0) < 1) {
				continue;
			}

			// check if there is a client trying to connect
			if (in_array($sock, $read))
			{	
				/*
				// accept the client, and add him to the $clients array
				if ((socket_accept($this->sock)) === false) {
					echo "socket_accept() a échoué : raison : " . socket_strerror(socket_last_error($this->sock)) . "\n";
					break;
				}
				*/
				$clients[] = $newsock = socket_accept($sock);
				socket_getpeername($newsock, $ip, $port);
				//echo "\r\n".date("Y-m-d H:i:s | ")."New client connected: {$ip} : {$port}\r\n";
				$output->writeln("\r\n".date("Y-m-d H:i:s | ")."New client connected: {$ip} : {$port}\r\n");
				//$this->writeServerLog("\r\n".date("Y-m-d H:i:s | ")."New client connected: {$ip} : {$port}\r\n");
				$request = new DbRequest();
				//$request->setConnect(1, $sn="", $ip);
				//$this->writeServerLog("\nsetConnect 1".$ip."\n");
				//$clients[] = $newsock; // put accepted socket in a client array
				// remove the listening socket from the clients-with-data array
				$key = array_search($sock, $read); 
				//$output->writeln("\r\nRead size : ".sizeof($read));
				unset($read[$key]);
				//array_splice($read, $key, 1);
				$key = array_search($newsock, $clients);
				//$output->writeln("\r\nRead size : ".sizeof($read));
				//$output->writeln("\r\nClients size : ".sizeof($clients));
				//$this->writeServerLog("\r\nClients size : ".sizeof($clients)."\r\n");
				$clientsInfo[$key][0] = "sn unknown";
				$clientsInfo[$key][1] = "{$ip} : {$port}";
				$clientsInfo[$key][2] = hrtime(true)+$this->timeOut;
				$clientsInfo[$key][3] = "{$ip}";
				$clientsInfo[$key][4] = "{$port}";
				
				//echo "\n"."There are ".count($clients)." client(s) connected to the server\n";
				//$sockData = socket_read($newsock, 4096, PHP_BINARY_READ) or die("Could not read input\n");
				//echo "\r\nData : ".$sockData;
			}
			
			$time_start = microtime(true);
			$memory_start = memory_get_usage(true);

			// loop through all the clients that have data to read from
			//echo " readsock length : ".sizeof($read);

			//TODO if read > 1 -->
			//$output->writeln("\r\nRead type : ".gettype($read));
			//$output->writeln("\r\nRead size : ".sizeof($read));

			foreach ($read as $read_sock)
			{
					// read until newline or 1024 bytes
					$data = @socket_read($read_sock, 4096, PHP_BINARY_READ);// or die("Could not read input\n");
					/*
					if (!$data) {
						echo "socket_read() a échoué : raison : " . socket_strerror(socket_last_error($sock)) . "\n";
						return false;
					}
					*/
					//$data = socket_read($read_sock, 4096) or die("Could not read input\n");
					/*
					if (($data = @socket_read($read_sock, 4096, PHP_BINARY_READ)) === false) {
						echo "socket_read() a échoué : raison : " . socket_strerror(socket_last_error($sock)) . "\n";
						return false;
					}
					*/
					$request = new DbRequest();

					//=> If data exists
					if (!empty($data))
					{
						reset($clientsInfo);
						$output->writeln("\r\n********************* Connected list *****************************\r\n");
						$this->writeServerLog("\r\n********************* Connected list *****************************\r\n");
						//=> Initiate client info (id, sn, ip, time) and update it at each iteration
						//$request->setConnectAll(0);
						// if sn or ip from db not in connected list
						for($i=1; $i<count($clients); $i++){
							//foreach ($clients as $sock){
							next($clientsInfo);
							//echo "\r\n".$i." | SN : ".current($clientsInfo)[0]." | IP : ".current($clientsInfo)[1]." | \r\nTime : ".date("H:i:s")." | Date : ".date("Y-m-d")."\r\n";
							//$output->writeln("\r\n".$i." | SN : ".current($clientsInfo)[0]." | IP : ".current($clientsInfo)[1]." | \r\nTime : ".date("H:i:s")." | Date : ".date("Y-m-d")."\r\n");
							if (isset(current($clientsInfo)[1])) {
								$output->writeln("\r\n".$i." | SN : ".current($clientsInfo)[0]." | IP : ".current($clientsInfo)[1]." | \r\nTime : ".date("H:i:s")." | Date : ".date("Y-m-d")."\r\n");
								$this->writeServerLog("\r\n".$i." | SN : ".current($clientsInfo)[0]." | IP : ".current($clientsInfo)[1]." | \r\nTime : ".date("H:i:s")." | Date : ".date("Y-m-d")."\r\n");
							}
							else {
								$output->writeln("\r\n".$i." | SN : ".current($clientsInfo)[0]." | IP : ".$ip." | \r\nTime : ".date("H:i:s")." | Date : ".date("Y-m-d")."\r\n");
								$this->writeServerLog("\r\n".$i." | SN : ".current($clientsInfo)[0]." | IP : ".$ip." | \r\nTime : ".date("H:i:s")." | Date : ".date("Y-m-d")."\r\n");
							}
							
						}
						//$this->writeServerLog("\r\nThere are ".count($clients)."clients connected.\r\n");
						//=> if no commands are returned from data device
						
						// TODO uncomment
						if(!$this->dataToTreat($data)) //=> msg to Device
						{	
							//$clientsInfo[$key][0] = "Computer ".$i;
							//echo "\r\n{$clientsInfo[$key][1]} send {$data} to {} with SN ".$clientsInfo[$key][0]."\r\n";
							//echo "\r\n{$ip}:{$port} send {$data} to IP ".$clientsInfo[$key][1]."\r\n";
							//TODO Device send data to computer
							//echo "\r\n{$ip}:{$port} send {$data}\r\n";
							//echo "\r\n{$clientsInfo[$key][3]} send {$data} to computer.\r\n";
							echo "\r\n{$clientsInfo[$key][0]} send {$data} to computer.\r\n";
							
							//$clientsInfo[$key][3]

							//echo "\r\n{$ip} send {$data} from {$read_sock} to ?? with SN ".$clientsInfo[$key][0]."\r\n";
							//echo "\r\nData length : ".strlen($data)."\r\n"; // check data length
							if (strlen($data)<20) {
								$output->writeln("\r\nData : ".bin2hex($data)."\r\n");
							}

							// check data is a device & contains serial number

							// TODO delete $data1 from if, replace with $data
							//$this->linkConnection[$data][1] = $read_sock;
							if(($data[0] == 'W') && (strlen($data) == 20)) 
							{
								//$output->writeln(strlen($data1));
								$key = 0;
								$sn = substr($data, 0, 20);
								//TODO find if $this->linkConnection exists & what to do if $this->linkConnection is not defined?
								//TODO change key 0 --> 1 if needed
								//$this->linkConnection[$data][0] = $read_sock;
								if(isset($this->linkConnection)){
									//TODO old version
									//echo $this->linkConnection;
									$key = array_key_exists($data, $this->linkConnection);
									echo 'Add link connection >>>>>>>>>>>>>>>>>>>>> '.$key." !!!!!!!\n";
									if($key){
										var_dump($this->linkConnection[$data][1]);
										if(isset($this->linkConnection[$data][1]) && !empty($this->linkConnection[$data][1])){
											
											$key1 = array_search($this->linkConnection[$data][1], $clients);
											
											if($key1){
												echo "Socket close !!!!!!!\n";
												//$this->writeServerLog("\r\nSocket close :".$data."\r\n");
												$request->setConnect(0, $data);
												//$this->writeServerLog("\nsetConnect 0 ".$data."\n");
												socket_close($clients[$key1]);
												unset($clients[$key1]);
												unset($clientsInfo[$key1]);	
											}
										}
										//var_dump($read_sock);
										//var_dump($clientsInfo[$key][1]);
										$this->linkConnection[$data][1] = $read_sock;
										
									}
									
									//TODO
									//$key = array_key_exists($data, $this->linkConnection);
									//echo "\r\n Add link connection >>>>>>>>>>>>>>>>>>>>> ".$key." !!!!!!!\n";
									//$output->writeln("\r\n Add link connection >>>>>>>>>>>>>>>>>>>>> ".$key." !!!!!!!\n");
									//if($key)
									//{
										/*
										if(isset($this->linkConnection[$data][0]) && !empty($this->linkConnection[$data][0]))
										{
											$this->key1 = array_search($this->linkConnection[$data][0], $clients);
											if($this->key1)
											{
												socket_close($clients[$this->key1]);
												$request->setConnect(0, current($clientsInfo)[0]);
												$output->writeln("\r\nSocket closed !\r\n");
												unset($clients[$this->key1]);
												unset($clientsInfo[$this->key1]);	
											}
										}
										*/
										/*
										if(isset($this->linkConnection[$data][1]) && !empty($this->linkConnection[$data][1]))
										{
											$this->key1 = array_search($this->linkConnection[$data][1], $clients);
											if($this->key1)
											{
												socket_close($clients[$this->key1]);
												$request->setConnect(0, current($clientsInfo)[0]);
												$output->writeln("\r\nSocket closed !\r\n");
												unset($clients[$this->key1]);
												unset($clientsInfo[$this->key1]);	
											}
										}
										$this->linkConnection[$data][1] = $read_sock;
										*/
										//var_dump($this->linkConnection);
									//}
								}
								//$dataResponse->writeLog("SEND MSG TO $sn >>>>>>>>>>>>>>>>>>>>> $key\n");
								//$task->writeLog($key);

								echo "SEND MSG TO >>>>>>>>>>>>>>>>>>>>> $key\n";
								//$key=0;

								socket_write($read_sock, $key);
								//socket_write($read_sock, $data);
							}
							else
							{
								$sn = substr($data, 0, 20);
								//$deviceType = hexdec($data[3].$data[4]);
								//$dataToSend = substr($data, 20);
								//echo "\nsock == {$sock} - readSock = {$read_sock}\n";
								//TODO initial canal value:
								$canal = ord($data[21]); // TODO what is canal?
								$output->writeln("\r\nCanal : {$canal}\r\n");
								//$this->linkConnection[$sn][0] = $read_sock;
								if($canal === 255){
									$output->writeln("Replace sock 1\n");
									$output->writeln("size of linkConnection : ".sizeof($this->linkConnection));
									//$key = array_search($this->linkConnection[$sn][0], $clients);
									$key = array_search($this->linkConnection[$sn][1], $clients);
									if($key){
										socket_close($clients[$key]);
										var_dump($clients[$key]);
										//var_dump(current($clientsInfo)[0]);
										//$request->setConnect(0, current($clientsInfo)[0]);
										$request->setConnect(0, $sn);
										//$this->writeServerLog("\r\nSocket close :".$sn."\r\n");
										$output->writeln("\r\nSocket closed !\r\n");
										//array_splice($clients, $key, 1);
										//array_splice($clientsInfo, $key, 1);
										unset($clients[$key]);
										unset($clientsInfo[$key]);	
									}

									$this->linkConnection[$sn][1] = $read_sock;
									//var_dump($this->linkConnection);
									//$dataResponse->writeLog('SEND MSG TO RSHOCK >>>>>>>>>>>>>>>>>>>>> '.$data."\n");
									//$task->writeLog($data);
									//echo 'SEND MSG TO RSHOCK >>>>>>>>>>>>>>>>>>>>> '.$data."\n";
									$output->writeln('SEND MSG TO RSHOCK >>>>>>>>>>>>>>>>>>>>> '.$data."\n");

									
									if(FALSE === socket_write($this->linkConnection[$sn][0], $data))
									{
										$key = array_search($this->linkConnection[$sn][0], $clients);
										if($key){
											socket_close($clients[$key]);
											//$request->setConnect(0, current($clientsInfo)[0]);
											$request->setConnect(0, $sn);
											//array_splice($clients, $key, 1);
											var_dump($clientsInfo[$key]);
											unset($clients[$key]);
											//$this->writeServerLog("\r\nSocket close :".$sn."\r\n");
											$output->writeln("\r\nSocket closed !\r\n");
										}
									}
									
								} 
								else 
								{
									//$dataResponse->writeLog('SEND MSG TO OTHER >>>>>>>>>>>>>>>>>>>>> '.$data."\n");
									//$dataResponse->writeLog($data);
									//echo 'SEND MSG TO OTHER >>>>>>>>>>>>>>>>>>>>> '.$data."\n";
									$output->writeln('SEND MSG TO OTHER >>>>>>>>>>>>>>>>>>>>> '.$data."\n");
									//var_dump($this->linkConnection);
									//socket_write($this->linkConnection[$sn][0], $data);
									if (isset($this->linkConnection[$sn][1])) {
										socket_write($this->linkConnection[$sn][1], $data);
									}
									
								}
								//var_dump($this->linkConnection);
							}
							unset($data);
						}
						// if commands are returned from device data
						else
						{ // => msg from device
							$key = array_search($read_sock, $clients);			
							$clientsInfo[$key][2] = hrtime(true)+$this->timeOut;
							//echo "\r\n".date("Y-m-d H:i:s | ")."Msg received with IP: {$ip} | SN: ".$clientsInfo[$key][0]." | \r\n ".$key." | Command : {$data[20]}{$data[21]} | RX : ".$data."\r\n"; //{$data}
							$output->writeln("\r\nSN: ".$clientsInfo[$key][0]." | Msg received with IP: {$clientsInfo[$key][3]}:{$clientsInfo[$key][4]} | \r\n".date("Y-m-d H:i:s")." | "."Command : {$data[20]}{$data[21]} |\r\nRX : ".$data."\r\n");
							//$this->writeServerLog("\r\nSN: ".$clientsInfo[$key][0]." | Msg received with IP: {$clientsInfo[$key][3]}:{$port} | \r\n".date("Y-m-d H:i:s")." | "."Command : {$data[20]}{$data[21]} |\r\nRX : ".$data."\r\n");
							//$this->writeServerLog("\r\nThere are ".count($clients)."clients connected.\r\n");

							if(substr($data, 0, 1) == 'W'){ // Verify that data comes from a device (all devices start with W)
								
								$task = new CommandDetect();
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
								//echo("IP before asking command: ".$ip);
								//echo("IP before asking command: ".$clientsInfo[$key][3]);
								
								//$response = $task->start($data, $ip);
								$response = $task->start($data, $clientsInfo[$key][3]);
								
								//$cmdRec = $data[20].$data[21];
								
								//echo $deviceCommand." ".$cmdRec;
								
								$affResponse = bin2hex($response);
								$dataResponse->writeCommandLog($sn, $deviceType, "\r\n".date("Y-m-d H:i:s | ")."Msg send : {$affResponse} \n| SN : ".$clientsInfo[$key][0]."\n| Command : ".$deviceCommand." from server\r\n");
								//echo "\r\n".date("Y-m-d H:i:s | ")."Msg send : ".strlen($affResponse)." \n| SN : ".$clientsInfo[$key][0]."\n| Command : ".$deviceCommand." from server\r\n";
								$output->writeln("\r\nSN : ".$clientsInfo[$key][0]."| Msg send : ".strlen($affResponse)."\r\n".date("Y-m-d H:i:s | ")."Command : ".$deviceCommand." from server");
								//$this->writeServerLog("\r\nThere are ".count($clients)."clients connected.\r\n");
								//$dataResponse->writeLog($sn, $deviceType, "\r\n".date("Y-m-d H:i:s | ")."Msg send : \n| SN : ".$clientsInfo[$key][0]."\n| Command : ".$deviceCommand." from server\r\n");
								//echo "\r\n".date("Y-m-d H:i:s | ")."Msg send : \n| SN : ".$clientsInfo[$key][0]."\n| Command : ".$deviceCommand." from server\r\n";

								if (($deviceCommand === 'F9') || ($deviceCommand === 'FA') || ($deviceCommand === 'FE') || ($deviceCommand === 'DE')){
									/*
									var_dump($clients);
									var_dump($this->linkConnection);
									*/
									//var_dump($clientsInfo);
									
									//var_dump($this->linkConnection[$sn]);
									//if(isset($this->linkConnection[$sn]) && !empty($this->linkConnection[$sn][0])){
									if(isset($this->linkConnection[$clientsInfo[$key][0]]) && !empty($this->linkConnection[$clientsInfo[$key][0]][0])){
										//$key = array_search($this->linkConnection[$sn][0], $clients);
										//$key = array_search($sn, $this->linkConnection);
										//$key = array_keys($this->linkConnection, $sn);
										//echo "key1: ".$key1;
										//echo "key: ".$key;
										//var_dump($key);
										for ($i=1; $i < count($clientsInfo); $i++) {
										//foreach(range(0, count($clientsInfo)) as $i) {
											if( isset($clientsInfo[$i][0]) && $clientsInfo[$i][0] == $clientsInfo[$key][0])
											{
												/*
												var_dump($i);
												var_dump($clientsInfo[$i][0]);
												var_dump($this->linkConnection[$sn][0]);
												var_dump($clients[$i]);
												*/
												if($this->linkConnection[$clientsInfo[$key][0]][0]!=$clients[$i] && $i!=$key)
												{
	
													//$output->writeln($clientsInfo[$key][0]);
													$output->writeln(" key i:".$i);
													$output->writeln(" key :".$key);
													$output->writeln("socket is closed :");
													$key2del = array_search($this->linkConnection[$clientsInfo[$key][0]][0], $clients);
													var_dump($clients[$i]);
													var_dump($clientsInfo[$i][1]);
													/*
													var_dump($clients[$key2del]);
													var_dump($clients[$i]);
													*/
													/*
													socket_close($clients[$i]);
													$request->setConnect(0, current($clientsInfo)[0]);
													unset($clients[$i]);
													unset($clientsInfo[$i]);
													*/
													$request->setConnect(0, $clientsInfo[$key][0]);
													//$this->writeServerLog("\nsetConnect 0 ".$clientsInfo[$key][0]."\n");
													
													//array_splice($clients, $key2del, 1);
													//array_splice($clientsInfo, $key2del, 1);
													/*
													socket_close($clients[$key2del]);
													unset($clients[$key2del]);
													unset($clientsInfo[$key2del]);
													*/
													socket_close($clients[$i]);
													unset($clients[$i]);
													unset($clientsInfo[$i]);
	
													
													
												}
											}
											

											
										}
										//var_dump($read_sock);
										$this->linkConnection[$sn][0] = $read_sock;
									}else{
										$this->linkConnection[$sn][0] = $read_sock;
									}
								}
								//var_dump($this->linkConnection);
								$dataResponse->writeCommandLog($sn, $deviceType, "\r\n".date("Y-m-d H:i:s | ").$response."\r\n");
								//echo "Response: ".bin2hex($response);
								//var_dump($this->linkConnection[$sn][0]);
								socket_write($this->linkConnection[$sn][0], $response);
								unset($data);
								
							}
							else
							{
								$key = array_search($read_sock, $clients);
								if($key){
									//socket_close($clients[$this->key1]);
									socket_close($clients[$key]);
									//$request->setConnect(0, current($clientsInfo)[0]);
									//$this->writeServerLog("\nsetConnect 0".current($clientsInfo)[0]."\n");
									//$this->writeServerLog("\r\nSocket closed ! Data doesn't come from a device !\r\n");
									$output->writeln("\r\nSocket closed ! Data doesn't come from a device !\r\n");
									unset($clients[$this->key1]);
									unset($clientsInfo[$this->key1]);
									//array_splice($clientsInfo, $key, 1);	
									//array_splice($clients, $key, 1);
								}				
							}
							
						}
						
					}
					/*
					else
					{
						//echo "Data doesn't exist !";
						$output->writeln("Data doesn't exist !");
					}
					*/



			} 

			$memory_end = memory_get_usage(true);
			$time_end = microtime(true);
			$execution_time = ($time_end - $time_start);
			//echo "\r\nTotal Execution Time: ".($execution_time*1000)." Milliseconds\r\n";
			//$output->writeln("\r\nTotal Execution Time: ".($execution_time*1000)." Milliseconds\r\n");
			$execution_memory = ($memory_end - $memory_start);
			//echo "\r\nTotal Execution Memory: ".($execution_memory)." \r\n";
			//$output->writeln("\r\nTotal Execution Memory: ".($execution_memory)." \r\n");
			// end of reading foreach
		}
		// close the listening socket
		socket_close($sock);
		//var_dump(current($clientsInfo)[0]);
		//$request->setConnect(0, current($clientsInfo)[0]);
		//$this->writeServerLog("\nsetConnect 0 ".$sn."\n");
		//$this->writeServerLog("\r\nSocket closed ! Something is false.\r\n");
		$output->writeln("\r\nSocket closed ! Something is false.\r\n");
	}
}

/*
$tcpserver = new TCPServer();
$tcpserver->runServer();
*/