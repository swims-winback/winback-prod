<?php
namespace App\Server;

use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
	/**
	 * Summary of responseArray
	 * [0] = $indexToGet;
	 * [1] = $response.$footer;
	 * [2] = $deviceInfo;
	 * [3] = $percentage;
	 */
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
	/**
	 * Summary of writeLog
	 * @param string $sn
	 * @param string $deviceType
	 * @return string $logFile
	 */
	public function writeLog(string $sn, string $deviceType){
        if (!file_exists($_ENV['LOG_PATH'].deviceTypeArray[$deviceType])) {
            mkdir($_ENV['LOG_PATH'].deviceTypeArray[$deviceType], 0777, true);
        }
        $logFile = trim($sn).".txt";
		$fd = fopen($_ENV['LOG_PATH'].deviceTypeArray[$deviceType].$logFile, "a+");
		if($fd){
			fwrite($fd, $this->logTxt);
			fclose($fd);
            return $logFile;
		}else{
			echo "fd error";
		}
	}

	/**
	 * Get forced status from database
	 * @param array $deviceInfo
	 * @return int
	 */
	function getForced($deviceInfo) {
		if (isset($deviceInfo[FORCED_UPDATE]) && (($deviceInfo[FORCED_UPDATE] === '1') || isset($deviceInfo[FORCED_UPDATE]) && ($deviceInfo[FORCED_UPDATE] === 1))) {
			$forcedUpdate = 1;
		}
		else {
			$forcedUpdate = 0;
		}
		return $forcedUpdate;
	}

	function getFilenameFromVersion($version, $deviceType, $boardType){
		$versionData = explode(".",$version,8);
		$versionString = str_pad($versionData[0], 3 , "0" , STR_PAD_LEFT);
		if (!array_key_exists(1, $versionData)) {
			$revisionString = str_pad("0", 3 , "0" , STR_PAD_LEFT);
		}
		else {
			$revisionString = str_pad($versionData[1], 3 , "0" , STR_PAD_LEFT);
		}
		$fileName = stFILENAME."_".$deviceType."_".$boardType."_v".$versionString.'.'.$revisionString.extFILENAME;
		return $fileName;
	}

	/**
	 * Get VersionUpload from database
	 * @param array $deviceInfo
	 * @param int $boardType
	 * @param string $deviceType
	 * @param object $dataResponse
	 * @return string $fileName
	 */
	function getVersionUpload($deviceInfo, $boardType, $deviceType, $dbRequest) {
		if (isset($deviceInfo[VERSION_UPLOAD]) && !empty($deviceInfo[VERSION_UPLOAD]) && ($boardType<32768)) {
			$fileName = $this->getFilenameFromVersion($deviceInfo[VERSION_UPLOAD], $deviceType, $boardType);
		} else {
			$lastVersUp = $dbRequest->getDeviceTypeActualVers($deviceType);
			/*
			if (!$lastVersUp) { # default version not defined in database, keep the same version
				//$logger->error("<error>Default Version not defined for {$deviceType}. Keeping old version.</error>");
				echo "\r\nDefault Version not defined for {$deviceType}. Keeping the same version\r\n";
				$fileName = $this->getFilenameFromVersion($deviceInfo[DEVICE_VERSION], $deviceType, $boardType);
			}
			else {
			*/
				$fileName = $lastVersUp["name"];
			//}
		}
		$fileUp = $_ENV['PACK_PATH'].deviceTypeArray[$deviceType].$fileName;
		if (!file_exists($_ENV['PACK_PATH'] . deviceTypeArray[$deviceType])) {
			echo "\r\nPath ".$_ENV['PACK_PATH'] . deviceTypeArray[$deviceType]." not present on the server.\r\n";
		}
		else
		{
			if (!file_exists($fileUp)) {
				//$logger->error("<error>Package File {$fileUp} not present on the server. Keeping old version.</error>");
				echo "\r\nPackage File {$fileUp} not present on the server.\r\n";
				//$filename = $this->getFilenameFromVersion($deviceInfo[DEVICE_VERSION], $deviceType, $boardType);
				//return $filename;
				return false;
			} else {
				return $fileName;
			}
		}
	}

	public function getDeviceVariables(string $data, DataResponse $dataResponse)
	{
		/** @var array $deviceObj
		 * ["Command"] = $command
		 * ["Serial Number"]
		 * ["Device Type"] = $deviceType
		 * ["Device Version"]
		 * ["Filename"]
		 * ["boardType"] = $boardType
		 * ["Index"]
		 * ["Request Id"]
		 * ["Forced Update"]
		 */

		/** @var mixed $data
		 * $deviceType = [3:4]
		 * $sn = [0:19]
		 * $command = [20:21]
		 * $boardType = [32:35]
		 * 
		 */
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

		if(!empty($data)){
            if(isset($data[0]) && !empty($data[0]) && isset($data[1])){
                $command = (isset($data[20]) && isset($data[21]) !== "aa") ? $data[20].$data[21] : '';
				// Get command in data received
				$deviceObj["Command"] = $command;
				// Get device type in data received
				$deviceType = hexdec($data[3].$data[4]);
				$deviceObj["Device Type"] = $deviceType;

				if (in_array($command, cmdBack)) {
					for($i=28; $i<(28+206); $i++){
						$dataTemp = hexdec(bin2hex($data[$i]));
						if($dataTemp === 127)
						{
							$dataTemp = 92 - 35 - $this->getserverCesarMatrixRxArray[($i-28)];
						}
						else 
						{
							$dataTemp = ((hexdec(bin2hex($data[$i]))-35) - $this->getserverCesarMatrixRxArray[($i-28)]);
						}
						if($dataTemp < 0)
						{
							$data[$i] = chr($dataTemp+127);
						}
						else 
						{
							$data[$i] = chr($dataTemp+35);
						}
					}
				}
				// Define BoardType
				if($command === 'DE' || $command === 'DD' || $command === 'DC' || $command === 'CD')
				{
					$boardType = hexdec(substr($data, 32, 4));
					$deviceObj["boardType"] = $boardType;
				}
				else
				{
					$boardType = 2;
					$deviceObj["boardType"] = $boardType;
				}
				// Define IndexToGet
				if($command ==='D8' || $command === 'CE' || $command === 'CB')
				{
					$indexToGet = hexdec(substr($data, 24, 8));
					$deviceObj["Index"] = $indexToGet;
				}
				
					$sn = substr($data, 0, 20);
					$deviceObj["Serial Number"] = $sn;

					$this->reqId = isset($data[22]) ? hexdec($data[22].$data[23]) : 0;
					$deviceObj["Request Id"] = $this->reqId;
					
					switch ($command) {
						case 'F3':
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
							break;
						case 'DB':
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
							break;
						case 'F9':
						case 'FE':
						case 'FA':
							if(!empty($data[28]) || !empty($data[29]) || !empty($data[30]) || !empty($data[31])){			
								$version = hexdec($data[28].$data[29]).'.'.hexdec($data[30].$data[31]);
								$deviceObj["Device Version"] = $version;
							}
							break;
						case 'F5':
						case 'F6':
						case 'F7':
						case 'F8':
						case 'FC':
						case 'FD':
							$indexToGet = hexdec(substr($data, 24, 8));
							$deviceObj["Index"] = $indexToGet;
							break;
						case 'DE':
						case 'DD':
						case 'DC':
						case 'CD':
							$version = hexdec($data[28].$data[29]).'.'.hexdec($data[30].$data[31]);
							$boardType = hexdec(substr($data, 32, 4));
							// TODO put boardType in database
							$indexToGet = hexdec(substr($data, 36, 8));
							$deviceObj["Device Version"] = $version;
							$deviceObj["Index"] = $indexToGet;
							break;
						case 'CF':
						case 'CE':
						case 'CC':
						case 'CB':
							if(!empty($data[0])){
								for($parse = 32; $parse < strlen($data) && $data[$parse]!='$'; $parse++){
									$this->path .= $data[$parse];
								}
							}
							break;
						default:
							# code...
							break;
					}
					return $deviceObj;
            }
        }

	}

	
	/**
	 * Create Database connection, get device information
	 * @param string $data
	 * @param string $ipAddr
	 * @param array $deviceInfo
	 * $this->responseArray[0] = $indexToGet;
	 * $this->responseArray[1] = $response.$footer;
	 * $this->responseArray[2] = $deviceInfo;
	 * $this->responseArray[3] = $percentage;
	 */
    public function start(string $data, string $ipAddr, array $deviceInfo) : false|array
	{
		$time_start_command = microtime(true);

		$dataResponse = new DataResponse();
		$request = new DbRequest();
		// DEFINE DEVICE VARIABLES
		$deviceObj = $this->getDeviceVariables($data, $dataResponse);
		$sn = $deviceObj["Serial Number"];
		$version = $deviceObj["Device Version"];
		$deviceType = $deviceObj["Device Type"];
		$command = $deviceObj["Command"];
		$reqId = $deviceObj["Request Id"];
		$indexToGet = $deviceObj["Index"];
		$boardType = $deviceObj["boardType"];

		if ($command == 'DE' || $command == 'FE' || $command == 'F9') {
			$deviceTypeId = deviceTypeId[$deviceType];
			$deviceTypeName = deviceTypeName[$deviceType];
			$logFile = trim($sn).".txt";
			/** @var array $deviceInfo 
			 * Info available in database
			 * [SN] : sn
			 * [FORCED_UPDATE] : forced
			*/
			$deviceInfo = $request->setDeviceInfo($sn, $version, $deviceTypeId, $ipAddr, $logFile);
			$request->setDeviceToSN($sn, $deviceTypeName);
			$request->setDeviceToServer($sn);
			$this->responseArray[2] = $deviceInfo;
		}

		// SET FORCED //
		$forcedUpdate = $this->getForced($deviceInfo);

		$fileName = $this->getVersionUpload($deviceInfo, $boardType, $deviceType, $request);
		$deviceObj["Filename"] = $fileName;

        switch ($command) {
            case "FC": //prog
            case "F8": //prog
            case "F7": //prog
            case "F6": //prog
            case "F5": //UART_CMD_UPDATE_SUBPROG4
				$totalFileContent = $dataResponse->getFileContent($deviceType, $fileName);
				$filesize = strlen($totalFileContent);
				$percentage = intval(($indexToGet/$filesize)*100);
				$dataResponse->writeCommandLog($sn, $deviceType, "\r\n".date("Y-m-d H:i:s | ").$indexToGet."/".$filesize . ' bytes - '.$percentage." %\r\n");
                $startOffset = $dataResponse->getIndexForProg($command, $totalFileContent);
				$fileContent = $dataResponse->setFileContent($totalFileContent, $indexToGet, $startOffset);
				$dataResponse->setHeader(cmdByte[$command], $this->reqId);
				$response = $dataResponse->setResponseData($fileContent);
                break;
            case "FD": //UART_CMD_UPDATE_PICTURES //update version
				if ($deviceInfo[FORCED_UPDATE] == 1) {
					$request->setForced($sn, 0);
					$deviceInfo[FORCED_UPDATE] = 0;
					$forcedUpdate = 0;
				}
				$totalFileContent = $dataResponse->getFileContent($deviceType, $fileName);
				$filesize = strlen($totalFileContent);
				$percentage = intval(($indexToGet/$filesize)*100);
				$dataResponse->writeCommandLog($sn, $deviceType, "\r\n".date("Y-m-d H:i:s | ").$indexToGet."/".$filesize . ' bytes - '.$percentage." %\r\n");

                $startOffset = $dataResponse->getIndexForImg($totalFileContent);
				$fileContent = $dataResponse->setFileContent($totalFileContent, $indexToGet, $startOffset);
				$dataResponse->setHeader(cmdByte[$command], $this->reqId);
                $response = $dataResponse->setResponseData($fileContent);
                break;
            case "FE": //UART_CMD_AUTODETECT //Ready To Receive
                $dataResponse->setHeader(cmdByte[$command], $this->reqId, 39);
				$fileContent = $dataResponse->getFileContent($deviceType, $fileName);
				$startOffset = $dataResponse->getIndexForImg($fileContent);
				$sizeContent = $dataResponse->getCRCAutoDetect($deviceType, $startOffset, $fileName);
				$tempResponse = $dataResponse->autoDetectBody($sizeContent, $fileContent, $forcedUpdate);
				$response = $dataResponse->setResponseData($tempResponse);
                break;
		    case "DE": //autoDetect BOARD, ASK_GMU_VERSION
				$fileContent = $dataResponse->setFileContent($dataResponse->getFileContent($deviceType, $fileName));
				$dataResponse->setHeader(cmdByte[$command], $this->reqId);
				$tempResponse = $dataResponse->setResponseData($fileContent);

				$tempResponse = $dataResponse->pointeurToResponse($sn, $deviceType, $tempResponse);

				$tempResponse[37] = $forcedUpdate;
				$finalResponse = $dataResponse->getDate($tempResponse);
				$pinCode = intval($deviceInfo[PIN_CODE]);
				$finalResponse[62] = chr(intval($deviceInfo[PUB_ACCEPTED]));
				$finalResponse[63] = chr($pinCode/256);
				$finalResponse[64] = chr($pinCode%256);

				//TODO $comment in database
				// length comment = 100
				if ($deviceType==14 and $fileName=="WLE256_14_2_v003.011.bin") {
					$comment = "Power regulation\nContact optimization\nHi-EMS: add Drain function\n  Bracelets: add Hi-TENS and TIC   ";
				}
				else {
					$comment = str_repeat(" ", 100);
				}
				/*
				if ($commentsString = $request->getUpdateComment($request->getDeviceTypeId($deviceType), $uploadVersion)) {
					echo ("\r\nComment String: " . $commentsString."\r\n");
				}
				else {
					$commentsString="Update version \nMaj touch and go";
					echo ("\r\nComment String: " . $commentsString."\r\n");
				}
				*/
				for($i = 0; $i < strlen($comment) ; $i++)
				{				
					$finalResponse[70 + $i] = $comment[$i];
				}
				$response = $dataResponse->getCesarMatrix($finalResponse);
				break;
			case "DD": //change l'IP Addresse à laquelle la machine se connecte
				$server_id = $deviceInfo["server_id"];				
				if ($deviceInfo["server_id"] = 1) {				
					$address = $deviceInfo["server_ip"];
					$port = $deviceInfo["server_port"];
					$input2 = $address.",".$port."\0";
					$contentSize = strlen($server_id)+strlen($input2);
					$header = $dataResponse->setHeader(cmdByte[$command], $this->reqId, $contentSize);
					$content = $dataResponse->setResponseToByte($server_id, 0);
					$tempResponse = $header.$content.$input2;
					$response = $dataResponse->getCesarMatrix($tempResponse);
					// change to 0 in db
					$request->setServerId($sn, 0);
					$deviceInfo["server_id"] = 0;
				}
				else {
					$header = $dataResponse->setHeader(cmdByte[$command], $this->reqId, strlen($server_id));
					$content = $dataResponse->setResponseToByte($server_id, 0);
					$tempResponse = $header.$content;
					$response = $dataResponse->getCesarMatrix($tempResponse);
				}
				
				break;
			case "CC": //synchro directory prtocol
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, 0);
				$response = $dataResponse->getProtocolDirectoryData($this->path, deviceType[$deviceType], $boardType);
				for($i=6;$i<strlen($response);$i++)$response[$i]=chr(hexdec(bin2hex($response[$i]))+$this->getserverCesarMatrixTxArray[($i-6)%214]);
				break;
			case "CB": //download protocol
				$directoryPath = $_ENV['PROTO_PATH'] . deviceType[$deviceType] .$this->path;
				$size=(filesize($directoryPath)-$indexToGet);
				if($size>4096)$size=4096;
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, $size);
				$tempResponse = $dataResponse->getFile4096Bytes($directoryPath, $indexToGet, $size);
				$response = $dataResponse->getCesarMatrix($tempResponse);
				break;
			case "DC": //Download BOARD //Download Version
			case "CD": //Download BOARD //Download Version
				// * Empêche la machine de rester forcée après un 1er téléchargement *//
				if ($deviceInfo[FORCED_UPDATE] == 1) {
					$request->setForced($sn, 0);
					$deviceInfo[FORCED_UPDATE] = 0;
					$forcedUpdate = 0;
				}
				//* return index in tcpserver to not send response if index is repeated*//
				$this->responseArray[0] = $indexToGet;
				$filesize =  filesize($_ENV['PACK_PATH'].deviceTypeArray[$deviceType].$fileName);
				$percentage = intval(($indexToGet/$filesize)*100);
				if ($percentage == 0 && $indexToGet == 4096 || $percentage == 99 && ($filesize-$indexToGet) < 4096) {
					$dataResponse->writeCommandLog($sn, $deviceType, "\r\n".date("Y-m-d H:i:s | ").$indexToGet."/".filesize($_ENV['PACK_PATH'].deviceTypeArray[$deviceType].$fileName) . ' bytes - '.$percentage." %\r\n");
					if ($percentage == 99 && ($filesize-$indexToGet) < 4096) {
						$request->setDownload($sn, 100);
						$percentage = 100;
					}
				}
				$this->responseArray[3] = $percentage;

				$fileContentArray = $dataResponse->setFileContent4096Bytes($_ENV['PACK_PATH'].deviceTypeArray[$deviceType].$fileName, $indexToGet);
				$fileContent = $fileContentArray[0];
				$nbDataToSend = $fileContentArray[1];
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, $nbDataToSend);

				$response = $dataResponse->getCesarMatrix(
					$tempResponse = $dataResponse->setResponseData($fileContent)
				);
				break;
			case "DB": //Load & copy Logs
				$this->writeLog($sn, $deviceType);
			case "D9":	//resend logs pointer
				$newPointeur = $dataResponse->getPointeur($sn, $deviceType);
				//$request->setLog($sn, $newPointeur);
				$dataResponse->setHeader(cmdByte["DB"], $this->reqId, 11);
				$response = $dataResponse->getCesarMatrix(
					$tempResponse = $dataResponse->getLogByPointer($newPointeur)
				);
				break;

			case "DA": //Pubs ask date
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, 9);
				$response = $dataResponse->getCesarMatrix(
					$tempResponse = $dataResponse->getPubsData(deviceTypeArray[$deviceType])
				);
				break;

		    case "D8": //Download PUBS
				$size = (filesize($_ENV['PUB_PATH'].deviceTypeArray[$deviceType]."PUBS.bin")-$indexToGet);
				if($size>4096)$size=4096;
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, $size);
				$response = $dataResponse->getCesarMatrix(
					$tempResponse = $dataResponse->getPubsFile(deviceType[$deviceType], $indexToGet, $size)
				);
				break;
			case "CF": //Synchro directory
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, 0);
				$response = $dataResponse->getSynchroDirectoryData($this->path, deviceType[$deviceType], $boardType);
				for($i=6;$i<strlen($response);$i++)$response[$i]=chr(hexdec(bin2hex($response[$i]))+$this->getserverCesarMatrixTxArray[($i-6)%214]);
				break;
			case "CE": //Download file
				if ($_ENV['APP_ENV'] == 'dev') {
					$directoryPath = $_ENV['LIB_PATH'] . deviceType[$deviceType] . $boardType.'/'.$this->path;
				}
				else {
					$directoryPath = $_ENV['LIB_PATH'] . deviceType[$deviceType] . $this->path;
				}
				$size=(filesize($directoryPath)-$indexToGet);
				if($size>4096)$size=4096;
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, $size);
				$tempResponse = $dataResponse->getFile4096Bytes($directoryPath, $indexToGet, $size);
				$response = $dataResponse->getCesarMatrix($tempResponse);
				break;

            case "F9": //Ready To Receive
				$request->setConnect('1', $sn);
				$request->setVersion($version, $sn);
				$newPointeur = $dataResponse->getPointeur2($sn, $deviceType);
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, 11);
				$content = $dataResponse->setResponseToByte($newPointeur, 11);
				$response = $dataResponse->setResponseData($content);
				break;

            case "F3": //Receive log file
				$this->writeLog($sn, $deviceType);
				$newPointeur = $dataResponse->getPointeur2($sn, $deviceType);
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, 11);
				$content = $dataResponse->setResponseToByte($newPointeur, 11);
				$response = $dataResponse->setResponseData($content);
				break;

            case "F2": //Close log 
				//$request->setConnect('1', $sn);
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, 0);
				$response = $dataResponse->setResponseData(); // Send header with empty response to indicate ends of logs to device
				break;

            case "FA":
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, 0);
				$response = $dataResponse->setResponseData();
                break;

            default:
                break;

		
		}
		
		$sFooter = $dataResponse->setFooter($response);
		$this->responseArray[1] = $response.$sFooter;
		
		$time_end_command = microtime(true);
		$execution_time_command = ($time_end_command - $time_start_command)*1000;
		if ($command != "DC" and $command != "CD" and $command != "FD" and $command != "FC") {
			$dataResponse->writeCommandLog($sn, $deviceType, "\r\n".date("Y-m-d H:i:s | ")." \n| SN : ".$sn."\n| Command : ".$command."\r\n");
		}
		if ($execution_time_command > 100) {
			echo "\r\nTime Alert: Command takes more than 100 ms:".$execution_time_command."\r\n";
			$dataResponse->writeCommandLog($sn, $deviceType, "\r\nTime Alert: Command takes more than 100 ms:".$execution_time_command."\r\n");
		}
		return $this->responseArray;
    }
}
