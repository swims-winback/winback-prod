<?php
namespace App\Server;

use App\Entity\Device;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Console\Output\ConsoleOutput;
class CommandDetect extends AbstractController {

    /***  Var to set from request  ***/
    
    private $command = ""; // str of 2 letters (ex: DE) in 20 & 21 position to get from data received
    //private $indexToGet; // index of 8 digits to get from data received
    private $reqId; // int of 2 digits (ex: 81) in 22 & 23 position to get from data received
	private $boardType; // software type (ex: sport, fitness, comfort), 2 by default for the moment
    private $path;
	
	private $logTxt = "";
    private $ptLogSave = 0;
	
	private $nbDataToSend;
	private $dataTemp;
	private $responseArray = array();

	private $getserverCesarMatrixTxArray=array(
			0x9E, 0xAC, 0xCF, 0x90, 0x36, 0x3A, 0x1F, 0xDC, 0xBB, 0x4B, 0x4A, 0x71, 0x61, 0x09, 0x10, 0x07,
			0x6A, 0xF1, 0x2A, 0x87, 0xF3, 0x1A, 0xBC, 0xAB, 0xE4, 0xDD, 0xD8, 0x48, 0x7B, 0x0A, 0xA4, 0xCB,
			0x29, 0xD3, 0x18, 0x80, 0x35, 0xCE, 0xD9, 0xA2, 0xC4, 0xAC, 0x0E, 0xA6, 0x97, 0x75, 0x58, 0xA0,
			0x8A, 0x86, 0x76, 0xDD, 0x07, 0x39, 0x02, 0xE6, 0x18, 0x43, 0x56, 0x6B, 0x21, 0x22, 0x90, 0x5E,
			0x28, 0xC0, 0x6C, 0xD9, 0x09, 0xD0, 0xA6, 0x0C, 0x3B, 0xB4, 0x35, 0x64, 0x79, 0xDF, 0x10, 0xBE,
			0x09, 0xE4, 0xC1, 0x5A, 0x08, 0x1D, 0x42, 0x84, 0x1B, 0x5B, 0xA1, 0x93, 0x56, 0x00, 0xF2, 0xA3,
			0xC4, 0xF8, 0x4E, 0x6A, 0x58, 0xC4, 0x67, 0x11, 0xAD, 0xC1, 0xAA, 0x13, 0x98, 0xBA, 0xD9, 0x74,
			0xF7, 0x73, 0x05, 0xB9, 0xB5, 0x6C, 0x9D, 0x49, 0x70, 0x8F, 0x09, 0x3C, 0xF0, 0xE6, 0x2F, 0x68,
			0xE1, 0x42, 0x46, 0x91, 0x41, 0x09, 0x98, 0xFB, 0x95, 0x43, 0xFD, 0x74, 0x67, 0x36, 0xA3, 0xF9,
			0x87, 0x19, 0x4D, 0x78, 0xAD, 0xF8, 0x14, 0xCC, 0xFF, 0xDF, 0x99, 0xB2, 0xDF, 0x2C, 0xCF, 0x60,
			0x13, 0x04, 0x19, 0x66, 0xAE, 0x77, 0x04, 0x27, 0x08, 0x63, 0x02, 0x6F, 0x38, 0x1F, 0xBA, 0xDB,
			0x01, 0xD1, 0xF6, 0x38, 0x7A, 0xE7, 0xC3, 0x66, 0xBC, 0x39, 0x7F, 0x4F, 0x57, 0x4E, 0x55, 0x61,
			0x28, 0xF7, 0xD0, 0x83, 0xE6, 0x8D, 0x4B, 0x0F, 0x3A, 0x58, 0x17, 0xDE, 0xEB, 0x5F, 0x91, 0x33,
			0x59, 0xE0, 0xE7, 0x57, 0x6D, 0xFD 
	);
	
	private $getserverCesarMatrixRxArray=array(
    0x03, 0x2B, 0x27, 0x14, 0x56, 0x0C, 0x32, 0x3F, 0x38, 0x3B, 0x56, 0x37, 0x5D, 0x52, 0x2C, 0x19,
    0x54, 0x32, 0x02, 0x3C, 0x10, 0x58, 0x42, 0x28, 0x5A, 0x30, 0x17, 0x3F, 0x0A, 0x1A, 0x32, 0x36,
    0x5C, 0x30, 0x54, 0x56, 0x2A, 0x38, 0x35, 0x4F, 0x53, 0x51, 0x13, 0x46, 0x5B, 0x1C, 0x04, 0x58,
    0x05, 0x52, 0x0B, 0x15, 0x1C, 0x25, 0x1B, 0x38, 0x16, 0x16, 0x03, 0x44, 0x15, 0x1B, 0x47, 0x0C,
    0x47, 0x35, 0x5B, 0x32, 0x20, 0x10, 0x02, 0x3A, 0x1C, 0x34, 0x46, 0x5D, 0x50, 0x2D, 0x42, 0x01,
    0x58, 0x54, 0x34, 0x07, 0x4B, 0x59, 0x42, 0x25, 0x3A, 0x05, 0x05, 0x4F, 0x41, 0x30, 0x01, 0x06,
    0x07, 0x38, 0x08, 0x0F, 0x0A, 0x41, 0x01, 0x1E, 0x3D, 0x2A, 0x58, 0x2A, 0x33, 0x1D, 0x15, 0x4D,
    0x16, 0x3B, 0x53, 0x33, 0x17, 0x23, 0x22, 0x49, 0x2B, 0x4F, 0x2A, 0x29, 0x03, 0x1D, 0x5A, 0x47,
    0x28, 0x16, 0x12, 0x1A, 0x30, 0x15, 0x32, 0x31, 0x36, 0x0B, 0x55, 0x05, 0x21, 0x37, 0x57, 0x50,
    0x2B, 0x45, 0x0D, 0x18, 0x08, 0x12, 0x13, 0x1D, 0x02, 0x5D, 0x55, 0x2E, 0x32, 0x02, 0x38, 0x3F,
    0x20, 0x2B, 0x2F, 0x22, 0x17, 0x40, 0x20, 0x04, 0x45, 0x0A, 0x37, 0x12, 0x0E, 0x32, 0x08, 0x13,
    0x58, 0x19, 0x48, 0x0D, 0x24, 0x15, 0x1B, 0x4D, 0x4C, 0x04, 0x47, 0x0A, 0x52, 0x23, 0x04, 0x5B,
    0x5E, 0x16, 0x02, 0x05, 0x4A, 0x4C, 0x50, 0x46, 0x4F, 0x17, 0x38, 0x25, 0x45, 0x36, 0x3E, 0x03,
    0x16, 0x0F, 0x53, 0x40, 0x48, 0x4C 
	);


	function __construct()
	{
		
	}

	public function writeLog(string $sn, string $deviceType){
        if (!file_exists(LOG_PATH.deviceTypeArray[$deviceType])) {
            mkdir(LOG_PATH.deviceTypeArray[$deviceType], 0777, true);
        }
        $logFile = trim($sn).".txt";
		$fd = fopen(LOG_PATH.deviceTypeArray[$deviceType].$logFile, "a+");
		if($fd){
			fwrite($fd, $this->logTxt);
			fclose($fd);
            return $logFile;
		}else{
			echo "fd error";
		}
	}

	/**
	 * Get command received from data
	 *
	 * @param [type] $data
	 * @return bool
	 */
	public function dataToTreat($data)
	{
		$cmdSoft = array('FE', 'FD', 'FC', 'FA', 'F9', 'F8', 'F7', 'F6', 'F5', 'F3', 'F2','DE','DD','DC','DB','DA','D9','D8','CF','CE','CD');
		if(isset($data[20]) && !empty($data[20])){
			$cmdRec = $data[20].$data[21];
			//echo "\r\n"."Command received : ".$cmdRec."\r\n";
			if(in_array($cmdRec, $cmdSoft)){
				return true;
			}
		}
		return false;
	}

	public function getDeviceVariables(string $data, DataResponse $dataResponse)
	{
		$deviceObj = [
			"Command" => "",
			"Serial Number" => "",
			"Device Type" => "",
			"Device Version" => "",
			"Filename" => "",
			"BoardType" => "",
			"Index" => "",
			"Request Id" => "",
			"Forced Update" => "",
		];
		$output = new ConsoleOutput();

		if(!empty($data)){
            if(isset($data[0])){
                $command = (isset($data[20]) && isset($data[21]) !== "aa") ? $data[20].$data[21] : '';
				// Get command in data received
				$deviceObj["Command"] = $command;
								
				// Get device type in data received
				//var_dump($data[3].$data[4]); //0C
				$deviceType = hexdec($data[3].$data[4]);
				//var_dump($deviceType); //12
				$deviceObj["Device Type"] = $deviceType;
				//echo "\r\nDevice Type : ".$deviceType."\r\n";
				//$output->writeln("\r\nDevice Type : ".$deviceType."\r\n");

				if($command === "DE" || $command === "DD" || $command === "DC" || $command === "DB" || $command === "D8" || $command === "CF" || $command === "CE"){
					for($i=28; $i<(28+206); $i++){
						//$dataTemp = hexdec(bin2hex($data[$i]));
						$dataTemp = $data[$i];
						if($dataTemp === 127)
						{
							$dataTemp = 92 - 35 - $this->getserverCesarMatrixRxArray[($i-28)];
						}
						else 
						{
							$dataTemp = ((hexdec(bin2hex($data[$i]))-35) - $this->getserverCesarMatrixRxArray[($i-28)]);
							
							if($dataTemp < 0)
							{
								$data[$i] = chr($dataTemp+127);
							}
							else 
							{
								$data[$i] = chr($dataTemp+35);
							}
						}
						/*
						if($dataTemp < 0)
						{
							$data[$i] = chr($dataTemp+127);
						}
						else 
						{
							$data[$i] = chr($dataTemp+35);
						}
						*/
					}
				}

				if($command === 'DE' || $command === 'DD' || $command === 'DC')
				{
					$boardType = hexdec(substr($data, 32, 4));
					$deviceObj["boardType"] = $boardType;
				}
				else
				{
					$boardType = 2;
					$deviceObj["boardType"] = $boardType;
				}

				//$fileName = $dataResponse->checkFile($deviceType, $boardType);
				//echo "\r\nfilename : ".$fileName."\r\n";
				//$deviceObj["Filename"] = $fileName;
				
				/*
				$forcedUpdate = 0;
				$deviceObj["Forced Update"] = $forcedUpdate;
				*/
				
				if($command ==='D8' || $command === 'CE')
				{
					$indexToGet = hexdec(substr($data, 24, 8));
					$deviceObj["Index"] = $indexToGet;
				}
				

				if(isset($data[1]))
				{
					// Récupérer les 20 premiers characters qui correspondent au sn
					if(!empty($data[0]))
					{
						$sn = substr($data, 0, 20);
						$deviceObj["Serial Number"] = $sn;
					}

					$this->reqId = isset($data[22]) ? hexdec($data[22].$data[23]) : 0;
					$deviceObj["Request Id"] = $this->reqId;
					
					if($command === "F3")
					{
						$length = 0;
						for($parse = 24; $parse < 238; $parse++){
							if($data[$parse] === "$")
							{
								break;
							}
							$this->logTxt .= $data[$parse];
							$length++;
						}
						$this->ptLogSave = $length;
					}
					else if($command === "DB")
					{
						$length = 0;
						for($parse = 32; $parse < 238; $parse+=2)
						{
							$dataTemp = chr(hexdec($data[$parse].$data[$parse+1]));
							if($dataTemp === "$") //separator
								break;
							$this->logTxt .= $dataTemp;
							$length++;
						}
						$this->ptLogSave = $length;
					}
					else if($command === "F9" || $command === "FE" || $command === "FA")
					{
						if(!empty($data[28]) || !empty($data[29]) || !empty($data[30]) || !empty($data[31])){			
							$version = hexdec($data[28].$data[29]).'.'.hexdec($data[30].$data[31]);
							$deviceObj["Device Version"] = $version;
						}
					}
					
					else if($command === "F5" || $command === "F6" || $command === "F7" || $command === "F8" || $command === "FC" || $command === "FD")
					{
						$indexToGet = hexdec(substr($data, 24, 8));
						$deviceObj["Index"] = $indexToGet;
					}
					
					else if($command === "DE" || $command === "DD" || $command === "DC")
					{
						$version = hexdec($data[28].$data[29]).'.'.hexdec($data[30].$data[31]);
						$dataResponse->writeCommandLog($sn, $deviceType, "\r\nVersion : ".$version."\r\n");
						$boardType = hexdec(substr($data, 32, 4));
						$indexToGet = hexdec(substr($data, 36, 8));
						$deviceObj["Device Version"] = $version;
						$deviceObj["Index"] = $indexToGet;
					}
					else if($command === "CF" || $command === "CE")
					{
						if(!empty($data[0])){
							for($parse = 32; $parse < strlen($data) && $data[$parse]!='$'; $parse++){
								$this->path .= $data[$parse];
							}
						}
					}
					return $deviceObj;
				}
            }
            
        }
	}

	
	/**
	 * Create Database connection, get device information
	 */
    public function start($data, $ipAddr)
	{
		
		$dataResponse = new DataResponse();
		$request = new DbRequest();
		$output = new ConsoleOutput();
		$device = new Device();

		$deviceObj = $this->getDeviceVariables($data, $dataResponse);
		$sn = $deviceObj["Serial Number"];
		$version = $deviceObj["Device Version"];
		$deviceType = $deviceObj["Device Type"];
		$command = $deviceObj["Command"];
		$reqId = $deviceObj["Request Id"];
		$indexToGet = $deviceObj["Index"];
		//$forcedUpdate = $deviceObj["Forced Update"];
		$boardType = $deviceObj["boardType"];

		// WRITE CMD LOG + CONNECT DB //
		$time_start4 = microtime(true);
		//$dataResponse->writeCommandLog($sn, $deviceType, "\r\nSN: ".$sn." | Msg received with IP: {$ipAddr} | \r\n".date("Y-m-d H:i:s")." | "."Command : {$data[20]}{$data[21]} |\r\nRX : ".$data."\r\n");
		$dbHandle = $request->dbConnect();
		if($dbHandle){
			$deviceTypeId = deviceTypeId[$deviceType];
            //$deviceInfo = $request->setDeviceInfo($sn, $version, $deviceTypeId);
			$deviceInfo = $request->setDeviceInfo($sn, $version, $deviceTypeId, $ipAddr);
			//$ipAddr = $ipAddr;
            //$request->setIpAddr($ipAddr, $sn);
			$request->setUpdatedAt($sn, date("Y-m-d | H:i:s"));
        }
		$time_end4 = microtime(true);
		$execution_time4 = ($time_end4 - $time_start4);
		echo "\r\nTotal Execution Time DB: ".($execution_time4*1000)." Milliseconds\r\n";

		// SET FORCED //
		$time_start4 = microtime(true);
		
		if(isset($deviceInfo[FORCED_UPDATE]) && (($deviceInfo[FORCED_UPDATE] === '1') || ($deviceInfo[FORCED_UPDATE] === 1)))
		{
			$forcedUpdate = 1;
			//$request->setForced($sn, $forcedUpdate);
		}
		else
		{
			$forcedUpdate = 0;
			//$request->setForced($sn, $forcedUpdate);
		}
		
		$time_end4 = microtime(true);
		$execution_time4 = ($time_end4 - $time_start4);
		echo "\r\nTotal Execution Time Forced: ".($execution_time4*1000)." Milliseconds\r\n";

		// SET VERSION UPLOAD //
		$time_start4 = microtime(true);
       	if(isset($deviceInfo[VERSION_UPLOAD]) && !empty($deviceInfo[VERSION_UPLOAD]) && ($boardType<32768))
		{	
				
			$versionData = explode(".",$deviceInfo[VERSION_UPLOAD],8);
			$versionString = str_pad($versionData[0], 3 , "0" , STR_PAD_LEFT);
			$revisionString = str_pad($versionData[1], 3 , "0" , STR_PAD_LEFT);
			$fileName = stFILENAME."_".$deviceType."_".$boardType."_v".$versionString.'.'.$revisionString.extFILENAME;
			$fileArch = PACK_ARCH_PATH.deviceTypeArray[$deviceType].$fileName;
			$fileUp = PACK_PATH.deviceTypeArray[$deviceType].$fileName;
            $cmpFile = $dataResponse->compareFile($fileArch, $fileUp);
            if(!$cmpFile){
                return FALSE;
            }
        }
		$time_end4 = microtime(true);
		$execution_time4 = ($time_end4 - $time_start4);
		echo "\r\nTotal Execution Time Version: ".($execution_time4*1000)." Milliseconds\r\n";

		// SET FILENAME //
		$time_start4 = microtime(true);
		if(!isset($fileName)){
			$fileName = $dataResponse->checkFile($deviceType, $boardType = '2');
		}
		$deviceObj["Filename"] = $fileName;
		
		$dataResponse->getFileContent($deviceType, $fileName);
		$time_end4 = microtime(true);
		$execution_time4 = ($time_end4 - $time_start4);
		echo "\r\nTotal Execution Time Filename: ".($execution_time4*1000)." Milliseconds\r\n";

		$time_start4 = microtime(true);
        switch ($command) {
            case "FC": //prog
            case "F8": //prog
				//$indexToGet = hexdec(substr($data, 24, 8));
            case "F7": //prog
				//$indexToGet = hexdec(substr($data, 24, 8));
            case "F6": //prog
				//$indexToGet = hexdec(substr($data, 24, 8));
            case "F5": //UART_CMD_UPDATE_SUBPROG4
				//$indexToGet = hexdec(substr($data, 24, 8));
				// if filename has not been defined by upload version
				if (!$fileName) {
					$fileName = $dataResponse->checkFile($deviceType, $boardType = '2');
				}
                $startOffset = $dataResponse->getIndexForProg($command, $dataResponse->getFileContent($deviceType, $fileName));
				//echo "\r\nstartOffset : {$startOffset}, indexToGet : {$this->indexToGet}\r\n";
				$fileContent = $dataResponse->setFileContent($dataResponse->getFileContent($deviceType, $fileName), $indexToGet, $startOffset);
				$dataResponse->setHeader(cmdByte[$command], $this->reqId);
				$response = $dataResponse->getResponseData($fileContent);
                break;
            case "FD": //UART_CMD_UPDATE_PICTURES //update version
				//$indexToGet = hexdec(substr($data, 24, 8));
				if (!$fileName) {
					$fileName = $dataResponse->checkFile($deviceType, $boardType = '2');
				}
                $startOffset = $dataResponse->getIndexForImg($dataResponse->getFileContent($deviceType, $fileName));
				//echo "\r\nstartOffset : {$startOffset}, indexToGet : {$this->indexToGet}\r\n";
				$fileContent = $dataResponse->setFileContent($dataResponse->getFileContent($deviceType, $fileName), $indexToGet, $startOffset);
				$dataResponse->setHeader(cmdByte[$command], $this->reqId);
                $response = $dataResponse->getResponseData($fileContent);
                break;
			// create device in db if not exist
            case "FE": //UART_CMD_AUTODETECT
				$request->setVersion($version, $sn);
                $dataResponse->setHeader(cmdByte[$command], $this->reqId, 39);
				if (!$fileName) {
					$fileName = $dataResponse->checkFile($deviceType, $boardType = '2');
				}
				//echo "\r\nfilename: ".$fileName."\r\n";
				//$dataResponse->writeCommandLog($sn, $deviceType, "\r\nfilename: ".$fileName."\r\n");
				$startOffset = $dataResponse->getIndexForImg($dataResponse->getFileContent($deviceType, $fileName));
				$sizeContent = $dataResponse->getCRCAutoDetect($deviceType, $startOffset, $fileName);
				$tempResponse = $dataResponse->autoDetectBody($sizeContent, $dataResponse->getFileContent($deviceType, $fileName), $forcedUpdate);
				/*
				$dataResponse->writeCommandLog($sn, $deviceType, "\r\nfilename: ".$fileName."\r\n");
				$fileContent = $dataResponse->getFileContent($deviceType, $fileName);
                $startOffset = $dataResponse->getIndexForImg($fileContent);
				$sizeContent = $dataResponse->getCRCAutoDetect($startOffset, $fileContent);
				$tempResponse = $dataResponse->autoDetectBody($sizeContent, $fileContent, $forcedUpdate);
				*/
				//echo "\r\nstartOffset : {$startOffset}, indexToGet : {$this->indexToGet}\r\n";
                $response = $dataResponse->getAutoDetectResponse($tempResponse);
				$dataResponse->writeCommandLog($sn, $deviceType, "\r\nFE - TX : ".bin2hex($response)."\r\n");
                break;
		    case "DE": //autoDetect BOARD, ASK_GMU_VERSION
				//$indexToGet = hexdec(substr($data, 36, 8));
				$time_start4 = microtime(true);
				$logFile = trim($sn).".txt";
				$request->setDeviceData($sn, $version, $logFile);
				$time_end4 = microtime(true);
				$execution_time4 = ($time_end4 - $time_start4);
				echo "\r\nTotal Execution Time DE-a: ".($execution_time4*1000)." Milliseconds\r\n";

				$time_start4 = microtime(true);
				if (!isset($fileName)) {
					$fileName = $dataResponse->checkFile($deviceType, $boardType = '2');
				}
				$fileContent = $dataResponse->setFileContent($dataResponse->getFileContent($deviceType, $fileName));
				$time_end4 = microtime(true);
				$execution_time4 = ($time_end4 - $time_start4);
				echo "\r\nTotal Execution Time DE-b: ".($execution_time4*1000)." Milliseconds\r\n";

				$time_start4 = microtime(true);
				$dataResponse->setHeader(cmdByte[$command], $this->reqId);
				$tempResponse = $dataResponse->getResponseData($fileContent);
				$time_end4 = microtime(true);
				$execution_time4 = ($time_end4 - $time_start4);
				echo "\r\nTotal Execution Time DE-c: ".($execution_time4*1000)." Milliseconds\r\n";

				$time_start4 = microtime(true);
				$tempResponse = $dataResponse->pointeurToResponse($sn, $deviceType, $tempResponse);
				$time_end4 = microtime(true);
				$execution_time4 = ($time_end4 - $time_start4);
				echo "\r\nTotal Execution Time DE-d: ".($execution_time4*1000)." Milliseconds\r\n";

				$time_start4 = microtime(true);
				$tempResponse[37] = $forcedUpdate;
				//echo "\r\nforcedUpdate: ".$forcedUpdate."\r\n";
				$response = $dataResponse->getDate($tempResponse);
				$pinCode = intval($request->getDevice($sn, PIN_CODE));
				//echo "\r\nPin code : ".$pinCode;
				$response[62] = chr(intval($request->getDevice($sn, PUB_ACCEPTED)));
				$response[63] = chr($pinCode/256);
				$response[64] = chr($pinCode%256);
				$commentsString="Update version \nMaj touch and go";
				$time_end4 = microtime(true);
				$execution_time4 = ($time_end4 - $time_start4);
				echo "\r\nTotal Execution Time DE-e: ".($execution_time4*1000)." Milliseconds\r\n";

				$time_start4 = microtime(true);
				for($i = 0; $i < strlen($commentsString) ; $i++)
				{				
					$response[70 + $i] = $commentsString[$i];
				}
				$time_end4 = microtime(true);
				$execution_time4 = ($time_end4 - $time_start4);
				echo "\r\nTotal Execution Time DE-f: ".($execution_time4*1000)." Milliseconds\r\n";

				$time_start4 = microtime(true);
				for($i=6; $i<strlen($response); $i++)
				{
					$response[$i] = chr(hexdec(bin2hex($response[$i])) + $this->getserverCesarMatrixTxArray[($i-6)%214]);
				}
				$time_end4 = microtime(true);
				$execution_time4 = ($time_end4 - $time_start4);
				echo "\r\nTotal Execution Time DE-g: ".($execution_time4*1000)." Milliseconds\r\n";	
				//echo("\r\nDE - TX data : ".bin2hex($response)."\r\n");
				//$dataResponse->writeCommandLog($sn, $deviceType, "\r\nDE - TX data : ".bin2hex($response)."\r\n");
				break;	

			case "DC": //Download BOARD //Download Version
				$this->responseArray[0] = $indexToGet;
				if (!$fileName) {
					$fileName = $dataResponse->checkFile($deviceType, $boardType = '2');
				} 
				$totalFileContent = $dataResponse->getFileContent($deviceType, $fileName);
				$fileSize = strlen($totalFileContent);
				$dataResponse->writeCommandLog($sn, $deviceType, "\r\n".$indexToGet."/".$fileSize."\r\n");
				$percentage = intval(($indexToGet/$fileSize)*100);
				$dataResponse->writeCommandLog($sn, $deviceType, "\r\n".$percentage." %\r\n");
				$time_start5 = microtime(true);
				$fileContentArray = $dataResponse->setFileContent4096Bytes($dataResponse->getFileContent($deviceType, $fileName), $indexToGet);
				$time_end5 = microtime(true);
				$execution_time5 = ($time_end5 - $time_start5);
				echo "\r\nTotal Execution Time step 1: ".($execution_time5*1000)." Milliseconds\r\n";

				$time_start5 = microtime(true);
				$fileContent = $fileContentArray[0];
				$nbDataToSend = $fileContentArray[1];
				//$dataResponse->writeCommandLog($sn, $deviceType, "\r\nFile Size : ".$nbDataToSend."\r\n"); //4096
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, $nbDataToSend);
				$time_end5 = microtime(true);
				$execution_time5 = ($time_end5 - $time_start5);
				echo "\r\nTotal Execution Time step 2: ".($execution_time5*1000)." Milliseconds\r\n";

				$time_start5 = microtime(true);
				$response = $dataResponse->getCesarMatrix(
					$tempResponse = $dataResponse->getResponseData($fileContent)
				);
				$time_end5 = microtime(true);
				$execution_time5 = ($time_end5 - $time_start5);
				echo "\r\nTotal Execution Time step 3: ".($execution_time5*1000)." Milliseconds\r\n";
				//TODO $dataResponse->writeCommandLog($sn, $deviceType, "\r\nIndex : ".$indexToGet."\r\n");
				//echo "\r\n"."TX data : ".bin2hex($response)."\r\n";
				//$output->writeln("\r\nDC - TX data : ".bin2hex($response)."\r\n");
				//$output->writeln("\r\n"."TX data size: ".strlen($response)."\r\n");
				break;
			case "DB": //Load & copy Logs
				$logFile = $this->writeLog($sn, $deviceType);
				$time_end4 = microtime(true);
				$execution_time4 = ($time_end4 - $time_start4);
				echo "\r\nTotal Execution Time command-a: ".($execution_time4*1000)." Milliseconds\r\n";
			case "D9":	//resend logs pointer
				
				//$time_start5 = microtime(true);
				$newPointeur = $dataResponse->getPointeur($sn, $deviceType);
				/*
				$time_end5 = microtime(true);
				$execution_time5 = ($time_end5 - $time_start5);
				echo "\r\nTotal Execution Time 5: ".($execution_time5*1000)." Milliseconds\r\n";
				*/
				//$time_start6 = microtime(true);
				//$request->setLog($sn, $newPointeur);
				$dataResponse->setHeader(cmdByte["DB"], $this->reqId, 11);
				/*
				$time_end6 = microtime(true);
				$execution_time6 = ($time_end6 - $time_start6);
				echo "\r\nTotal Execution Time 6: ".($execution_time6*1000)." Milliseconds\r\n";
				*/
				//$time_start7 = microtime(true);
				$response = $dataResponse->getCesarMatrix(
					$tempResponse = $dataResponse->getLogByPointer($newPointeur)
				);
				/*
				$time_end7 = microtime(true);
				$execution_time7 = ($time_end7 - $time_start7);
				echo "\r\nTotal Execution Time 7: ".($execution_time7*1000)." Milliseconds\r\n";
				*/
				//$output->writeln("\r\nDB - TX data : ".bin2hex($response)."\r\n");
				//$dataResponse->writeCommandLog($sn, $deviceType, "\r\nDB - TX data : ".bin2hex($response)."\r\n");
				//TODO $output->writeln("\r\n"."TX data size: ".strlen(bin2hex($response))."\r\n");
				break;

			case "DA": //Pubs ask date
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, 9);
				//$response = $dataResponse->getCesarMatrix($temporaryResponse);
				$response = $dataResponse->getCesarMatrix(
					$tempResponse = $dataResponse->getPubsData(deviceTypeArray[$deviceType])
				);
				//TODO $output->writeln("\r\n"."TX data size: ".strlen(bin2hex($response))."\r\n");
				break;

		    case "D8": //Download PUBS
				//$indexToGet = hexdec(substr($data, 24, 8));
				$size = (filesize(PUB_PATH.deviceTypeArray[$deviceType]."PUBS.bin")-$indexToGet);
				if($size>4096)$size=4096;
				//echo '....'.$this->indexToGet.'.....'.$size.'.....';
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, $size);
				//echo "\r\n"."TX data: ".bin2hex($temporaryResponse)."\r\n"; 
				$response = $dataResponse->getCesarMatrix(
					$tempResponse = $dataResponse->getPubsFile(deviceType[$deviceType], $indexToGet, $size)
				);
				//$output->writeln("\r\n"."TX data: ".bin2hex($response)."\r\n");
				//TODO $output->writeln("\r\n"."TX data size: ".strlen(bin2hex($response))."\r\n");
				break;
			case "CF": //Synchro directory		
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, 0);
				$response = $dataResponse->getSynchroDirectoryData($this->path, deviceType[$deviceType]);				
				//TODO $output->writeln("\r\nCF - TX : ".bin2hex($response)."\r\n");
				//TODO $dataResponse->writeCommandLog($sn, $deviceType, "\r\nCF - TX : ".bin2hex($response)."\r\n");
				for($i=6;$i<strlen($response);$i++)$response[$i]=chr(hexdec(bin2hex($response[$i]))+$this->getserverCesarMatrixTxArray[($i-6)%214]);//$this->Response[$i]+=$getserverCesarMatrixArray[$i%214];				
				break;
			case "CE": //Download file
				//$indexToGet = hexdec(substr($data, 24, 8));		
				//echo "\n"." CE data : ".$this->indexToGet." ".$this->path;
				$size=(filesize(LIB_PATH.deviceType[$deviceType].$this->path)-$indexToGet);
				if($size>4096)$size=4096;
				//echo '....'.$this->indexToGet.'.....'.$size.'.....';
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, $size);
				$tempResponse = $dataResponse->getFile4096Bytes($this->path, deviceType[$deviceType], $indexToGet, $size);
				$response = $dataResponse->getCesarMatrix($tempResponse);
				break;

            case "F9": //Ready To Receive
				//TODO $dataResponse->writeCommandLog($sn, $deviceType, "\r\nfilename: ".$fileName."\r\n");
				$request->setConnect('1', $sn);
				$request->setVersion($version, $sn);
				$newPointeur = strval($request->getDevice($sn, LOG_POINTEUR));
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, 11);
                //$dataResponse->initAutoDetectResponse(11);
				$response = $dataResponse->getLogByPointer($newPointeur);
				//TODO $dataResponse->writeCommandLog($sn, $deviceType, "\r\nF9 - TX : ".bin2hex($response)."\r\n");
				break;

            case "F3": //Receive log file				
				$logFile = $this->writeLog($sn, $deviceType);
				$request->setLogFile($sn, $logFile); // insert logfilename in db
				$pointer = intval($request->getDevice($sn, LOG_POINTEUR)) + $this->ptLogSave;
				$newPointeur = strval($pointer);
				$request->setLog($sn, $newPointeur);
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, 11);
                //$dataResponse->initAutoDetectResponse(11);
				$response = $dataResponse->getLogByPointer($newPointeur);
				//TODO $dataResponse->writeCommandLog($sn, $deviceType, "\r\nF3 - TX : ".bin2hex($response)."\r\n");
				break;

            case "F2": //Close log 
				//$request->setConnect('1', $sn);
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, 0);
				$response = $dataResponse->getResponseData();
				//TODO $dataResponse->writeCommandLog($sn, $deviceType, "\r\nF2 - TX : ".bin2hex($response)."\r\n");
				break;

            case "FA":
                //$deviceInfo = $request->initDeviceInDB($sn, $version, $deviceType);
				//$request->initDeviceInDB($sn, $version, $deviceType, $ipAddr);
                //$request->setIpAddr($ipAddr, $sn);
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, 0);
				$response = $dataResponse->getResponseData();
				//TODO $dataResponse->writeCommandLog($sn, $deviceType, "\r\nFA - TX : ".bin2hex($response)."\r\n");
                break;

            default:
				$output->writeln("Command {$command} not found !");
                break;

		
		}
		$sFooter = $dataResponse->setFooter($response);
		$this->responseArray[1] = $response.$sFooter;
		//return $response.$sFooter;
		return $this->responseArray;
    }
}

