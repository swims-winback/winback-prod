<?php
namespace App\Server;

use App\Entity\Device;
//use Doctrine\Migrations\Configuration\EntityManager\ManagerRegistryEntityManager;
//use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Console\Output\ConsoleOutput;
class CommandDetect extends AbstractController {

    /***  Var to set from request  ***/
    
    private $command = ""; // str of 2 letters (ex: DE) in 20 & 21 position to get from data received
    private $indexToGet; // index of 8 digits to get from data received
    //private $fileName = "WLE256_10_2_v1.3.bin";
    private $reqId; // int of 2 digits (ex: 81) in 22 & 23 position to get from data received
	private $boardType; // software type (ex: sport, fitness, comfort), 2 by default for the moment
    private $path;
	/*
    private $sn;
	private $deviceType;
    private $version;
	*/
    private $forcedUpdate;
	
	private $logTxt = "";
    private $ptLogSave = 0;
	
	private $nbDataToSend;
	private $dataTemp;

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
		$cmdSoft = array('FE', 'FD', 'FC', 'FA', 'F9', 'F8', 'F7', 'F6', 'F5', 'F3', 'F2','DE','DD','DC','DB','DA','D9','D8','CF','CE');
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
			
			//$this->dataReceive = $data;
            if(isset($data[0])){
                $command = (isset($data[20]) && isset($data[21]) !== "aa") ? $data[20].$data[21] : '';
				// Get command in data received
				$deviceObj["Command"] = $command;
								
				// Get device type in data received
				$deviceType = hexdec($data[3].$data[4]);
				$deviceObj["Device Type"] = $deviceType;
				//echo "\r\nDevice Type : ".$deviceType."\r\n";
				//$output->writeln("\r\nDevice Type : ".$deviceType."\r\n");

				if($command === "DE" || $command === "DD" || $command === "DC" || $command === "DB" || $command === "D8" || $command === "CF" || $command === "CE"){
					// If device is Back4, hexa = 0C, dec = 12
					//if($deviceType === 12) 
					//{
					//echo "\r\ndata length: ".strlen($data);
					//$output->writeln("\r\ndata length: ".strlen($data));
					for($i=28; $i<(28+206); $i++){
						$dataTemp = hexdec(bin2hex($data[$i]));
						//echo "\r\ndataTemp 0: ".$this->dataTemp."\r\n";
						if($dataTemp === 127)
						{
							$dataTemp = 92 - 35 - $this->getserverCesarMatrixRxArray[($i-28)];
							//echo "\r\ndataTemp 1: ".$this->dataTemp."\r\n";
						}
						else 
						{
							$dataTemp = ((hexdec(bin2hex($data[$i]))-35) - $this->getserverCesarMatrixRxArray[($i-28)]);
							//echo "\r\ndataTemp 2: ".$this->dataTemp."\r\n";
						}

						if($dataTemp < 0)
						{
							$data[$i] = chr($dataTemp+127);
							//echo "\r\ndataTemp 3: ".$this->dataTemp."\r\n";
						}
						else 
						{
							$data[$i] = chr($dataTemp+35);
							//echo "\r\ndataTemp 4: ".$this->dataTemp."\r\n";
						}
					}
				}

				//$utils = new Utils($deviceType);
				if($command === 'DE' || $command === 'DD' || $command === 'DC')
				{
					$this->boardType = hexdec(substr($data, 32, 4));
				}
				else
				{
					$this->boardType = 2;
				}

				$fileName = $dataResponse->checkFile($deviceType, $this->boardType);
				//echo "\r\nfilename : ".$fileName."\r\n";
				$deviceObj["Filename"] = $fileName;
				$forcedUpdate = 0;
				$deviceObj["Forced Update"] = $forcedUpdate;

				if($command ==='D8' || $command === 'CE')
				{
					$this->indexToGet = hexdec(substr($data, 24, 8));
					$deviceObj["Index"] = $this->indexToGet;
				}

				if(isset($data[1]))
				{
					//echo "\r\n"."RX data : ".$data."\r\n";
					$output->writeln("\r\n"."RX data : ".$data."\r\n");
					// Récupérer les 20 premiers characters qui correspondent au sn
					if(!empty($data[0]))
					{
						$sn = substr($data, 0, 20);
						$deviceObj["Serial Number"] = $sn;
					}

					$this->reqId = isset($data[22]) ? hexdec($data[22].$data[23]) : 0;
					//echo "reqId : ".$this->reqId."\r\n";
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
							//$dataTemp = chr(hexdec(bin2hex($data[$parse].$data[$parse+1])));
							$dataTemp = chr(hexdec($data[$parse].$data[$parse+1]));
							if($dataTemp === "$") //separator
								break;
							$this->logTxt .= $dataTemp;
							$length++;
						}
						$this->ptLogSave = $length;
						//echo "\r\nlogTxt = ".$this->logTxt;
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
						$this->indexToGet = hexdec(substr($data, 24, 8));
						$deviceObj["Index"] = $this->indexToGet;
					}
					else if($command === "DE" || $command === "DD" || $command === "DC")
					{
						$version = hexdec($data[28].$data[29]).'.'.hexdec($data[30].$data[31]);
						echo "\r\nVersion : ".$version."\r\n";
						$this->boardType = hexdec(substr($data, 32, 4));
						$this->indexToGet = hexdec(substr($data, 36, 8));
						$deviceObj["Device Version"] = $version;
						$deviceObj["Index"] = $this->indexToGet;
						//echo "\r\nCOMMAND : ".$command."\r\n";
					}
					else if($command === "CF" || $command === "CE")
					{
						if(!empty($data[0])){
							for($parse = 32; $parse < strlen($data) && $data[$parse]!='$'; $parse++){
								$this->path .= $data[$parse];
							}
						}
					}
					
					//$dataResponse = new dataResponse($this->deviceType);
					/*
					if(!empty($this->sn)){
						$this->dbReq = new DbRequest();
					}	
					*/	
					//return $dataResponse;
					//echo "\r\nDevice Array : ".$deviceObj["Device Type"]."\r\n";
					return $deviceObj;
				}
            }
            
        }
	}

	
	/**
	 * Create Database connection, get device information
	 */
    public function start($data, $ipAddr)
	//function start(DbRequest $request, Utils $utils, $data)
	{

		$dataResponse = new DataResponse();
		$request = new DbRequest();
		$output = new ConsoleOutput();
		$device = new Device();
		//$doctrine = new ManagerRegistryEntityManager;

		$deviceObj = $this->getDeviceVariables($data, $dataResponse);
		$sn = $deviceObj["Serial Number"];
		$version = $deviceObj["Device Version"];
		//echo "Version ".$version;
		$deviceType = $deviceObj["Device Type"];
		$command = $deviceObj["Command"];
		$reqId = $deviceObj["Request Id"];
		$this->indexToGet = $deviceObj["Index"];
		$forcedUpdate = $deviceObj["Forced Update"];

		$dbHandle = $request->dbConnect();
		//echo "COMMAND ".$command;
        //if($dbHandle && $request->getElemBy(SN, DEVICE_TABLE, $sn)==false){
		if($dbHandle){
			//$deviceTypeName = deviceTypeArray[$deviceType];
			//$deviceTypeName = str_replace("/", "", $deviceTypeName); // delete slash at the end of deviceType name
			//$deviceTypeName = 2; //TODO replace default value
			//echo "\r\nDevice type name: {$deviceTypeName}\r\n";
			$deviceTypeId = $request->getDeviceType($deviceType, ID); // get deviceType id from deviceType table to put in device table
            $deviceInfo = $request->setDeviceInfo($sn, $version, $deviceTypeId);
			//var_dump($deviceInfo);
			$ipAddr = $ipAddr;
            $request->setIpAddr($ipAddr, $sn);
			//$request->setForced($sn, $forcedUpdate);
			//$request->setCreatedAt($sn, date("Y-m-d | H:i:s"));
			$request->setUpdatedAt($sn, date("Y-m-d | H:i:s"));
        }
		/*
		$device->setIpAddr($ipAddr);
		$device->setForced($forcedUpdate);
		$device->getCreatedAt(date("Y-m-d | H:i:s"));
		*/
        //$doctrine = $this->getDoctrine();
		//$em = $doctrine->getManager();
		/*
		$em = $this->getEntityManager();
        $em->persist($device);
        $em->flush();
		*/
		//TODO Uncomment to test - define forcedUpdate property
		//var_dump($deviceInfo);
		if(isset($deviceInfo[FORCED_UPDATE]) && (($deviceInfo[FORCED_UPDATE] === '1') || ($deviceInfo[FORCED_UPDATE] === 1)))
		{
			$forcedUpdate = 1;
			$request->setForced($sn, $forcedUpdate);
		}
		else
		{
			$forcedUpdate = 0;
			$request->setForced($sn, $forcedUpdate);
		}
		//var_dump($deviceInfo);
       	if(isset($deviceInfo[VERSION_UPLOAD]) && !empty($deviceInfo[VERSION_UPLOAD]) && ($this->boardType<32768))
		{	
				
			$versionData = explode(".",$deviceInfo[VERSION_UPLOAD],8);
			$versionString = str_pad($versionData[0], 3 , "0" , STR_PAD_LEFT);
			$revisionString = str_pad($versionData[1], 3 , "0" , STR_PAD_LEFT);
			//var_dump($versionData);
			/*
			echo "versionString: ".$versionString."\n";
			echo "revisionString: ".$revisionString."\n";
			*/
			//$output->writeln("\r\nVersion data: ".$versionData."\r\n");
			$fileName = stFILENAME."_".$deviceType."_".$this->boardType."_v".$versionString.'.'.$revisionString.extFILENAME;
			//echo $fileName."\n";
			$fileArch = PACK_ARCH_PATH.deviceTypeArray[$deviceType].$fileName;
			$fileUp = PACK_PATH.deviceTypeArray[$deviceType].$fileName;
            $cmpFile = $dataResponse->compareFile($fileArch, $fileUp);
            if(!$cmpFile){
				//var_dump(FALSE);
                return FALSE;
            }
        }
        

		//$fileName = $dataResponse->setFileName($fileName);
        //$dataResponse->getAllFileContent($deviceType, $fileName);
		/*
		$dataResponse->fileContent(
			$deviceType, 
			$dataResponse->setFileName($fileName)
		);
		*/
		//var_dump($fileName);

		if(!isset($fileName)){
			$fileName = $dataResponse->checkFile($deviceType, $boardType = '2');
			echo "filename after checkfile: ".$fileName;
			$dataResponse->writeCommandLog($sn, $deviceType, "filename after checkfile: ".$fileName);
		}
		$dataResponse->getFileContent($deviceType, $fileName);
		echo "filename after dataresponse: ".$fileName;
		$dataResponse->writeCommandLog($sn, $deviceType, "filename after dataresponse: ".$fileName);
		
		// TODO Uncomment to test
		
        switch ($command) {
			///*
            case "FC": //prog
            case "F8": //prog
            case "F7": //prog
            case "F6": //prog
            case "F5": //UART_CMD_UPDATE_SUBPROG4
				// if filename has not been defined by upload version
				if (!$fileName) {
					$fileName = $dataResponse->checkFile($deviceType, $boardType = '2');
				}
                $startOffset = $dataResponse->getIndexForProg($command, $dataResponse->getFileContent($deviceType, $fileName));
				//echo "\r\nstartOffset : {$startOffset}, indexToGet : {$this->indexToGet}\r\n";
				$fileContent = $dataResponse->setFileContent($dataResponse->getFileContent($deviceType, $fileName), $this->indexToGet, $startOffset);
				$dataResponse->setHeader(cmdByte[$command], $this->reqId);
				$response = $dataResponse->getResponseData($fileContent);
                break;
            case "FD": //UART_CMD_UPDATE_PICTURES
				
				if (!$fileName) {
					$fileName = $dataResponse->checkFile($deviceType, $boardType = '2');
				}
				
                $startOffset = $dataResponse->getIndexForImg($dataResponse->getFileContent($deviceType, $fileName));
				//echo "\r\nstartOffset : {$startOffset}, indexToGet : {$this->indexToGet}\r\n";
				$fileContent = $dataResponse->setFileContent($dataResponse->getFileContent($deviceType, $fileName), $this->indexToGet, $startOffset);
				$dataResponse->setHeader(cmdByte[$command], $this->reqId);
                $response = $dataResponse->getResponseData($fileContent);
                break;
            case "FE": //UART_CMD_AUTODETECT
				$request->setVersion($version, $sn);
                $dataResponse->setHeader(cmdByte[$command], $this->reqId, 39);
				
				if (!$fileName) {
					$fileName = $dataResponse->checkFile($deviceType, $boardType = '2');
				}
				
				echo "\r\nfilename: ".$fileName."\r\n";
                $startOffset = $dataResponse->getIndexForImg($dataResponse->getFileContent($deviceType, $fileName));
				$sizeContent = $dataResponse->getCRCAutoDetect($deviceType, $startOffset);
				$tempResponse = $dataResponse->autoDetectBody($sizeContent, $dataResponse->getFileContent($deviceType, $fileName), $forcedUpdate);
				//echo "\r\nstartOffset : {$startOffset}, indexToGet : {$this->indexToGet}\r\n";
                $response = $dataResponse->getAutoDetectResponse($tempResponse);
                break;
			/*
            case "DE": //autoDetect GMU
				$request->updateDeviceData($this->version, $this->sn);
                $dataResponse->setFileContent();
                $dataResponse->setHeader(cmdByte[$this->command], $this->reqId);
                $this->Response = $dataResponse->getResponseData();
				$this->Response[37]=$forcedUpdate;
                break;
		    case "DD": //Download GMU
                $this->$nbDataToSend = $dataResponse->setFileContent2048Bytes($this->indexToGet);
                $dataResponse->setHeader(cmdByte[$this->command], $this->reqId,$this->$nbDataToSend);
                $this->Response = $dataResponse->getResponseData();
                break;
			*/

			//TODO Uncomment to test
		    case "DE": //autoDetect BOARD, ASK_GMU_VERSION
				$request->setConnect('1', $sn);
				//echo("\r\nVersion: ".$version."\r\n");
				$request->setVersion($version, $sn);
				
				// TODO set version upload
				if (!isset($fileName)) {
					$fileName = $dataResponse->checkFile($deviceType, $boardType = '2');
				}
				
				echo "\r\nfilename: ".$fileName."\r\n";
				$fileContent = $dataResponse->setFileContent($dataResponse->getFileContent($deviceType, $fileName));
				$dataResponse->setHeader(cmdByte[$command], $this->reqId);
				$tempResponse = $dataResponse->getResponseData($fileContent);
				$tempResponse = $dataResponse->pointeurToResponse($sn, $deviceType, $tempResponse);

				$tempResponse[37] = $forcedUpdate;
				//echo "\r\nforcedUpdate: ".$forcedUpdate."\r\n";
				$response = $dataResponse->getDate($tempResponse);
				$pinCode = intval($request->getDevice($sn, PIN_CODE));
				//echo "\r\nPin code : ".$pinCode;
				$response[62] = chr(intval($request->getDevice($sn, PUB_ACCEPTED)));
				//$response[62] = chr(1);
				$response[63] = chr($pinCode/256);
				$response[64] = chr($pinCode%256);

				//TODO comment
				$commentsString="Update version \nMaj touch and go"; 
				//echo $commentsString;
				for($i = 0; $i < strlen($commentsString) ; $i++)
				{				
					$response[70 + $i] = $commentsString[$i];
				}
				//echo "DE Response: ".bin2hex($response);
				for($i=6; $i<strlen($response); $i++)
				{
					$response[$i] = chr(hexdec(bin2hex($response[$i])) + $this->getserverCesarMatrixTxArray[($i-6)%214]);
				}
				//$this->$fileSize = bin2hex($this->Response[4].$this->Response[5].$this->Response[6].$this->Response[7]); 
				//echo "\r\n"."TX data : ".$response."\r\n";	
				echo("\r\nDE - TX data : ".bin2hex($response)."\r\n");
				//echo "\r\n"."TX data : ".$response."\r\n";
				//echo "\r\n"."TX data : ".bin2hex($response)."\r\n";
				//echo '....'.$this->indexToGet.'.....';
				break;	

			///*
			case "DC": //Download BOARD //Download Version
				
				if (!$fileName) {
					$fileName = $dataResponse->checkFile($deviceType, $boardType = '2');
				}
				
				$fileContentArray = $dataResponse->setFileContent4096Bytes($dataResponse->getFileContent($deviceType, $fileName), $this->indexToGet);
				$fileContent = $fileContentArray[0];
				$nbDataToSend = $fileContentArray[1];
				//$dataResponse->writeCommandLog($sn, $deviceType, $nbDataToSend);
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, $nbDataToSend);
				$response = $dataResponse->getCesarMatrix(
					$tempResponse = $dataResponse->getResponseData($fileContent)
				);
				//echo "\r\n"."TX data : ".bin2hex($response)."\r\n";
				$output->writeln("\r\nDC - TX data : ".bin2hex($response)."\r\n");
				$output->writeln("\r\n"."TX data size: ".strlen($response)."\r\n");
				break;
			//*/
			case "DB": //Load & copy Logs
				$logFile = $this->writeLog($sn, $deviceType);
				$request->setLogFile($sn, $logFile);
				$request->setConnect('1', $sn);
			///*
			case "D9":	//resend logs pointer
				$newPointeur = $dataResponse->getPointeur($sn, $deviceType);
				$request->setLog($sn, $newPointeur);
				$dataResponse->setHeader(cmdByte["DB"], $this->reqId, 11);
				$response = $dataResponse->getCesarMatrix(
					$tempResponse = $dataResponse->getLogByPointer($newPointeur)
				);
				$output->writeln("\r\nDB - "."TX data : ".bin2hex($response)."\r\n");
				$output->writeln("\r\n"."TX data size: ".strlen(bin2hex($response))."\r\n");
				break;
			//*/

			//TODO work on getPubsData in dataResponse !
			///*
			case "DA": //Pubs ask date
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, 9);
				//$response = $dataResponse->getCesarMatrix($temporaryResponse);
				$response = $dataResponse->getCesarMatrix(
					$tempResponse = $dataResponse->getPubsData(deviceTypeArray[$deviceType])
				);
				$output->writeln("\r\n"."TX data size: ".strlen(bin2hex($response))."\r\n");
				break;
			//*/

			//TODO work on getPubsFile in dataResponse !
			///*
		    case "D8": //Download PUBS
				$size = (filesize(PUB_PATH.deviceTypeArray[$deviceType]."PUBS.bin")-$this->indexToGet);
				if($size>4096)$size=4096;
				//echo '....'.$this->indexToGet.'.....'.$size.'.....';
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, $size);
				//echo "\r\n"."TX data: ".bin2hex($temporaryResponse)."\r\n"; 
				$response = $dataResponse->getCesarMatrix(
					$tempResponse = $dataResponse->getPubsFile(deviceType[$deviceType], $this->indexToGet, $size)
				);
				//$output->writeln("\r\n"."TX data: ".bin2hex($response)."\r\n");
				$output->writeln("\r\n"."TX data size: ".strlen(bin2hex($response))."\r\n");
				break;
			case "CF": //Synchro directory		
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, 0);
				$response = $dataResponse->getSynchroDirectoryData($this->path, deviceType[$deviceType]);				
				$output->writeln("\r\nCF - TX : ".bin2hex($response)."\r\n");
				for($i=6;$i<strlen($response);$i++)$response[$i]=chr(hexdec(bin2hex($response[$i]))+$this->getserverCesarMatrixTxArray[($i-6)%214]);//$this->Response[$i]+=$getserverCesarMatrixArray[$i%214];				
				break;
			case "CE": //Download file		
				//echo "\n"." CE data : ".$this->indexToGet." ".$this->path;
				$size=(filesize(LIB_PATH.deviceType[$deviceType].$this->path)-$this->indexToGet);
				if($size>4096)$size=4096;
				//echo '....'.$this->indexToGet.'.....'.$size.'.....';
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, $size);
				$tempResponse = $dataResponse->getFile4096Bytes($this->path, deviceType[$deviceType], $this->indexToGet, $size);
				$response = $dataResponse->getCesarMatrix($tempResponse);
				//echo "\n"."TX data: ".bin2hex($response);
				break;
            case "F9": //Ready To Receive
				$request->setConnect('1', $sn);
				$request->setVersion($version, $sn);
				$newPointeur = strval($request->getDevice($sn, LOG_POINTEUR));
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, 11);
                //$dataResponse->initAutoDetectResponse(11);
				$response = $dataResponse->getLogByPointer($newPointeur);
				break;
			//*/

			///*
            case "F3": //Receive log file				
				$logFile = $this->writeLog($sn, $deviceType);
				$request->setLogFile($sn, $logFile); // insert logfilename in db
				$pointer = intval($request->getDevice($sn, LOG_POINTEUR)) + $this->ptLogSave;
				$newPointeur = strval($pointer);
				$request->setLog($sn, $newPointeur);
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, 11);
                //$dataResponse->initAutoDetectResponse(11);
				$response = $dataResponse->getLogByPointer($newPointeur);
				break;
			//*/

			///*
            case "F2": //Close log 
				//$request->setConnect('1', $sn);
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, 0);
				$response = $dataResponse->getResponseData();
				break;

            case "FA": //TODO what is this command used for?
                //$deviceInfo = $request->initDeviceInDB($sn, $version, $deviceType);
				$request->initDeviceInDB($sn, $version, $deviceType);
                $request->setIpAddr($ipAddr, $sn);
				$dataResponse->setHeader(cmdByte[$command], $this->reqId, 0);
				$response = $dataResponse->getResponseData();
                break;
			//*/
            default:
				//echo "Command {$command} not found !";
				$output->writeln("Command {$command} not found !");
                break;

		
			//TODO Uncomment switch end
		}
		$sFooter = $dataResponse->setFooter($response);
		return $response.$sFooter;
    }
}

