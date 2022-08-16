<?php
namespace App\Server;

//require_once ('config.php');
require_once dirname(__FILE__, 3).'/configServer/config.php';
//require_once CLASS_PATH.'Utils.php';
require_once("Utils.php");
require_once("DbRequest.php");
//include_once CLASS_PATH.'Utils.php';



class DataResponse extends Utils
{
    private $aResponse = array();
    private $aIndex = array();
    //private $aSizeProg = array();
    private $sHeader;
    private $fileContent;
    private $fileContentCRC;
    private $fileName;
	//private $contents;
    private $startOffset = 0;
    private $sizeContent = 0;
    //private $sizeProg = 0;

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

    function __construct() {
        
        //$this->utils = new Utils();
    }
    /*
    function __construct(Utils $utils) {
        $this->utils = $utils;
        //$this->utils = new Utils();
    }
	*/

    /*
	function getIndexFile($command){
        $this->startOffset = $this->aIndex[0]+$this->aIndex[1]+$this->aIndex[2]+$this->aIndex[3];
    }
    */

    /**
     * Returns specific substracted data from file content, the beginning of data corresponds to startOffset & the data length corresponds to fromIndex
     *
     * @param string $fileContent //$fileContent extracted with getFileContent
     * @param [type] $startOffset
     * @param integer $fromIndex
     * @return string
     */
    function setFileContent(string $fileContent, $fromIndex = 0, $startOffset = 0){
        $startOffset += $fromIndex;
        $fileContentFromIndex = $this->getContentFromIndex($fileContent, $startOffset); // TODO check startOffset et fromIndex
        return $fileContentFromIndex;
    }

    /*
    function setFileContent(string $deviceType, int $startOffset = 0) : string|bool
    {
        //$startOffset += $fromIndex;
        //$this->getContentFromIndex = $this->getContentFromIndex($deviceType, $fileName, $this->startOffset);
        echo $this->getContentFromIndex($deviceType, $startOffset);
        echo "\r\nsetFileContent function is working correctly !\r\n";
        echo "\r\n #################### \r\n";
        return $this->getContentFromIndex($deviceType, $startOffset);
    }
	*/

    function getIndexForImg($fileContent){
        //$fileContent = $this->getFileContent($deviceType);
        //$this->aIndex[0] = hexdec(bin2hex($this->fileContent[18]))*256;
        //$this->aIndex[1] = hexdec(bin2hex($this->fileContent[19]));
        $aIndex[0] = hexdec(bin2hex($fileContent[18]))*256;
        $aIndex[1] = hexdec(bin2hex($fileContent[19]));
        $startOffset = $aIndex[0] + $aIndex[1];
        /*
        echo "\r\naIndex[0] : ".$aIndex[0]."\r\n";
        echo "\r\naIndex[1] : ".$aIndex[1]."\r\n";
        echo "\r\nstartOffset : ".$startOffset."\r\n";
        echo "\r\ngetIndexForImg function is working correctly !\r\n";
        echo "\r\n #################### \r\n";
        */
        echo "\r\nstartOffset : ".$startOffset."\r\n";
        return $startOffset;
    }
    
    function getIndexForProg(string $command, string $fileContent){
        //$fileContent = $this->getFileContent($deviceType);
        $listProgIndex = 
            array(
                "FC" => array(8,9,10,11),
                "F8" => array(28,29,30,31),
                "F7" => array(36,37,38,39),
                "F6" => array(44,45,46,47),
                "F5" => array(52,53,54,55)
            );

        $aIndex[0] = hexdec(bin2hex($fileContent[$listProgIndex[$command][0]]))*256*256*256;
        $aIndex[1] = hexdec(bin2hex($fileContent[$listProgIndex[$command][1]]))*256*256;
        $aIndex[2] = hexdec(bin2hex($fileContent[$listProgIndex[$command][2]]))*256;
        $aIndex[3] = hexdec(bin2hex($fileContent[$listProgIndex[$command][3]]));
        $startOffset = $aIndex[0] + $aIndex[1] + $aIndex[2] + $aIndex[3];
        /*
        echo "\r\nstartOffset : ".$startOffset."\r\n";
        echo "\r\ngetIndexForProg function is working correctly !\r\n";
        echo "\r\n #################### \r\n";
        */
        return $startOffset;
    }

    // Function used in commandDetect, in DD case
    /*
	function setFileContent2048Bytes($fromIndex = 0){
        $this->startOffset += $fromIndex;
        $this->getContentFromIndex = $this->utils->getContentFromIndex($this->fileContent, $this->startOffset,2048);
		return strlen($this->getContentFromIndex);
    }
	*/
    /**
     * Extract content from file, between startOffset and 4096
     */
	function setFileContent4096Bytes($fileContent, $fromIndex = 0, $startOffset = 0){
        //$startOffset = 0;
        $startOffset += $fromIndex;
        //$fileContent = $this->getFileContent($deviceType);
        $fileContentFromIndex = $this->getContentFromIndex($fileContent, $startOffset, 4096);
        //echo "\r\nfile content length : ".strlen($fileContentFromIndex);
        $resultArray = array($fileContentFromIndex, strlen($fileContentFromIndex));
		return $resultArray;
    }
	
    function setFileName($fileName){
        //return $this->fileName = $fileName;
        $this->fileName = $fileName;
        return $this;
    }
    
    function getFileName(){
        return $this->fileName;
    }

    function setHeader(int $cmd, int $reqId, $dataSize=FW_OCTETS){
        $this->header = chr(AA).chr(0).chr($reqId).chr($cmd).chr(intval($dataSize/256)).chr($dataSize%256);
        $binHeader = bin2hex($this->header);
        //echo "\r\nheader : {$binHeader}\r\n";
        return $this->header;
    }
    
    function setFooter($oTram){
        $tramSize = 0;
        for($parse = 0; $parse < strlen($oTram); $parse++){
			$tramSize = $tramSize + (hexdec(bin2hex($oTram[$parse])) * (($parse%255)+1));
        }
        return chr($tramSize);

    }
	
    function getSynchroDirectoryData($path, $device){
        if (file_exists(LIB_PATH.$device.$path)) {
            $listFiles = scandir(LIB_PATH.$device.$path);
            //echo "\r\npath : {$path}\r\n";
            $directoryListString='';
            for($i=0;$i<count($listFiles);$i++)if($listFiles[$i][0]!='.'){
                if(strpos($listFiles[$i],'.')){
                    $handle = fopen(LIB_PATH.$device.$path.$listFiles[$i], 'rb');
                    if($handle){
                        $contents=fread($handle, 9);
                        fclose($handle);
                        $version=hexdec(bin2hex($contents[7]));
                        $revision=hexdec(bin2hex($contents[8]));
                        $directoryListString.=$listFiles[$i].','.((intval($version/100))%10).((intval($version/10))%10).($version%10).((intval($revision/100))%10).((intval($revision/10))%10).($revision%10).'|';
                    }
                }else $directoryListString.=$listFiles[$i].'|';
            }
            //echo "\r\ndirectory : {$directoryListString}\r\n";
            
            //$directoryListString="DOCS/|";
            $dataSize=strlen($directoryListString);
            $this->header[4]=chr(intval($dataSize/256));
            $this->header[5]=chr($dataSize%256);
            $Response = $this->header.$directoryListString;
            return $Response;
        }
        else {            
            //$dataSize=strlen($directoryListString);
            $directoryListString = false;
            $this->header[4]=chr(0);
            $this->header[5]=chr(0);
            $Response = $this->header.$directoryListString;
            return $Response;
        }

	}



	/**
	 * Write logTxt variable to a log file with sn as filename
	 *
	 */
    /*
	function writeLog(string $sn, string $deviceType, string $logTxt){
        if (!file_exists(LOG_PATH.deviceTypeArray[$deviceType])) {
            mkdir(LOG_PATH.deviceTypeArray[$deviceType], 0777, true);
        }
        $logFile = trim($sn).".txt";
		$fd = fopen(LOG_PATH.deviceTypeArray[$deviceType].$logFile, "a+");
		if($fd){
			fwrite($fd, $logTxt);
			fclose($fd);
            return $logFile;
		}else{
			echo "fd error";
		}
	}
    */

    /**
	 * Write logTxt variable to a log file with sn as filename
	 *
	 */
	function writeCommandLog(string $sn, string $deviceType, string $logTxt){
        $path = LOG_PATH."command/".deviceType[$deviceType];
		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}
		$logFile = trim($sn).".txt";
        if (file_exists($path.$logFile) && filesize($path.$logFile) < 20000) {
            $fd = fopen(LOG_PATH."command/".deviceType[$deviceType].$logFile, "a+");
            if($fd){
                fwrite($fd, $logTxt);
                fclose($fd);
                return $logFile;
            }else{
                echo "fd error";
            }
        }
        else {
            $fd = fopen(LOG_PATH."command/".deviceType[$deviceType].$logFile, "w");
            if($fd){
                fwrite($fd, $logTxt);
                fclose($fd);
                return $logFile;
            }else{
                echo "fd error";
            }
        }
	}

    function getPubsData(string $deviceType){

        $handle = fopen(PUB_PATH.$deviceType."PUBS".extFILENAME, 'rb');

		if($handle){
			$contents = fread($handle, 5); // Get 5 first characters of contents
			fclose($handle);
			
            $pubSize = filesize(PUB_PATH.$deviceType."PUBS".extFILENAME);

            // Concatenate header, contents & pubsize to form response
            $response = $this->header.$contents.chr(intval($pubSize/256/256/256)).chr(intval($pubSize/256/256)).chr(intval($pubSize/256)).chr(intval($pubSize%256));
            //echo bin2hex($response);
            /*
            echo "\r\ngetPubsData function is working correctly !\r\n";
            echo "\r\ngetPubsData Response size: ".strlen($response)."\r\n";
            echo "\r\ngetPubsData Response: ".bin2hex($response)."\r\n";
            echo "\r\n #################### \r\n";
            */
            return $response;
		}
		else
        {
            
            echo "\r\nUhuh, something went wrong ! Check that pubs folder exists for {$deviceType}.\r\n";
            echo "\r\n #################### \r\n";
        }

		
	}
	
    /**
     * Get content of pub file and build response with header & content
     *
     * @param string $deviceType
     * @param integer $fromIndex
     * @param integer $size
     * @return string $response
     */
    function getPubsFile(string $deviceType, $fromIndex = 0, $size = 0){
		
		$contents = file_get_contents(PUB_PATH.$deviceType."PUBS.bin");	
		if ($contents) {
            $response = $this->header.substr($contents, $fromIndex, $size);
            for($aInit = strlen($response); $aInit < ($size+6); $aInit++){
                $response[$aInit] = chr(255);
            }
            //echo bin2hex($response);
            return $response;
        }
        else
        {
            echo "\r\nUhuh, something went wrong ! Check that pubs folder exists for {$deviceType}.\r\n";
            echo "\r\n #################### \r\n";
        }

		
	} 

    function getFile4096Bytes($path, $device, $fromIndex = 0, $size = 0){
		
		$contents=file_get_contents(LIB_PATH.$device.$path);	
		
		$Response = $this->header.substr($contents, $fromIndex, $size);
		for($aInit = strlen($Response); $aInit < ($size+6); $aInit++){
            $Response[$aInit] = chr(255);
        }
        return $Response;
		
	}

    /* ############################# */
    
    function getCRCAutoDetect(string $deviceType, $startOffset, $fileName)
    //function getCRCAutoDetect($startOffset, $crcFileContent)
    {
        //echo "\r\nGetCRCAutoDetect...\r\n";
        //$fileName = $this->checkFile($deviceType, $boardType = '2');
        $crcFileContent = $this->getFileContent($deviceType, $fileName);
        $fileContentCRC = substr($crcFileContent, $startOffset, strlen($crcFileContent) - $startOffset);
        $sizeContent = 0;
        for($parse = 0; $parse < strlen($fileContentCRC); $parse++){
            $sizeContent = $sizeContent + (hexdec(bin2hex($fileContentCRC[$parse])));
        }
        //$sizeContent = chr($sizeContent);
        echo "sizeContent : ".chr($sizeContent);
        //echo "size content type: ".gettype($sizeContent);
        return chr($sizeContent);
    }

    function initAutoDetectResponse($nbData = 39){
        for($aInit = 0; $aInit < $nbData; $aInit++){
            $aResponse[$aInit] = chr(0);
        }
        return $aResponse;
    }

    /**
     * Fill tempResponse with elements in fileContent
     */
    function autoDetectBody(string $sizeContent, $fileContent, $forced = 0){
        $aResponse = $this->initAutoDetectResponse(); // Init aResponse content
        //$fileContent = $this->getContentFromIndex($deviceType, $fromIndex);
        //$fileContent = $this->getFileContent($deviceType);

        $aResponse[0] = $fileContent[4];

        $aResponse[7] = $fileContent[2];
        $aResponse[8] = $fileContent[3];

        $aResponse[9] = $fileContent[12];
        $aResponse[10] = $fileContent[13];
        $aResponse[11] = $fileContent[14];
        $aResponse[12] = $fileContent[15];

        $aResponse[13] = $fileContent[20];
        $aResponse[14] = $fileContent[21];
        $aResponse[15] = $fileContent[22];
        $aResponse[16] = $fileContent[23];
        $aResponse[17] = $fileContent[24];
        $aResponse[18] = $fileContent[25];
        $aResponse[19] = $fileContent[26];
        $aResponse[20] = $fileContent[27];
		
        $aResponse[21] = $fileContent[32];
        $aResponse[22] = $fileContent[33];
        $aResponse[23] = $fileContent[34];
        $aResponse[24] = $fileContent[35];
		
        $aResponse[25] = $fileContent[40];
        $aResponse[26] = $fileContent[41];
        $aResponse[27] = $fileContent[42];
        $aResponse[28] = $fileContent[43];
		
        $aResponse[29] = $fileContent[48];
        $aResponse[30] = $fileContent[49];
        $aResponse[31] = $fileContent[50];
        $aResponse[32] = $fileContent[51];
		
        $aResponse[33] = $fileContent[56];
        $aResponse[34] = $fileContent[57];
        $aResponse[35] = $fileContent[58];
        $aResponse[36] = $fileContent[59];

        $aResponse[37] = chr($forced);
		
        $aResponse[38] = $sizeContent;

        return $aResponse;
	}

    /**
     * create final response with header & tempResponse
     */
    function getAutoDetectResponse($aResponse){
        $response =  $this->header;
        $response .= implode('', $aResponse);
        return $response;

    }

    //function getResponseData(int $cmd, int $reqId, string $deviceType, $index) : string
    function getResponseData($fileContent='') : string
    {
        //echo "header: ".$this->header;
        //echo "fileContent: ".$fileContent;
        $response = $this->header.$fileContent;

        return $response;
    }
	
    /**
     * For a pointer of 5 digits:
     * Initiate a response array of size 11
     * response 6,7,8,9,10 = pointer 1,2,3,4,5
     *
     * @param integer $pointer
     * @return array
     */
	function setResponseLog($pointer = 0){
        $aResponse = array_fill(0, 11, chr(0)); //Init response array filled with n zeros
        $aResponse2 = $aResponse;
        $pointer = str_split($pointer);
		$ptLength = count($pointer);
        $size = count($pointer);
        
		$parse = 0;
		for($i = $ptLength - 1; $i >= 0 ; $i--){
			$aResponse[10 - $i] = chr($pointer[$parse]);
            //echo "\r\npointer: ".$pointer[$parse]."\r\n";
			$parse++;
		}
        
        if ($aResponse === $aResponse2) {
            echo true;
        }
        //echo "\r\nsetResponseLog Response size : ".sizeof($aResponse)."\r\n";
        //echo "\r\nsetResponseLog Response : ";
        //print_r($aResponse);
        //echo "\r\n";
        return $aResponse;
	}
	
    // call setResponseLog + getAutoDetectResponse
	function getLogByPointer($pointer = 0){
		$aResponse = $this->setResponseLog($pointer);
		$response = $this->getAutoDetectResponse($aResponse);
        /*
        echo "\r\ngetLogByPointer Response size : ".strlen($response)."\r\n";
        echo "\r\ngetLogByPointer Response : ".$response."\r\n";
        */
		return $response;
	}

    function getCesarMatrix($tempResponse)
    {
        for($i=6; $i<strlen($tempResponse);$i++)
        {
            $tempResponse[$i] = chr(hexdec(bin2hex($tempResponse[$i])) + $this->getserverCesarMatrixTxArray[($i-6)%214]);
        }
        /*
        echo "\r\ngetCesarMatrix Response size : ".strlen($tempResponse)."\r\n";
        echo "\r\ngetCesarMatrix Response : ".bin2hex($tempResponse)."\r\n";
        */
        return $tempResponse;
    }

    function getDate($response)
    {
        $dateString = date("Y-m-d H:i:s");
        $punkt = [":", "-", " "]; // special characters to be deleted from datestring
        $dateString = str_replace($punkt, "", $dateString);
        $dateString = substr($dateString, 2, strlen($dateString)-2);
        for($i = 0; $i < strlen($dateString) ; $i++) // Copy datestring in response and omitting two first characters of year
        {				
            $response[50 + $i] = $dateString[$i];
        }
        return $response;
    }

    /**
     * Filesize of logfile if it exist to be used as a pointeur to copy data from device
     *
     * @param string $sn
     * @param string $deviceType
     * @return int $newPointeur
     */
    function getPointeur($sn, $deviceType)
    {
        $path = LOG_PATH.deviceTypeArray[$deviceType].trim($sn).".txt";
        if(file_exists($path)){
            $newPointeur = filesize($path);
        }
        else {
            $newPointeur = 0;
        }
        $newPointeur = strval($newPointeur);
        //echo "\r\nNew Pointeur = {$newPointeur}\r\n";
        return $newPointeur;
    }

    function pointeurToResponse($sn, $deviceType, $temporaryResponse)
    {
        $newPointeur = $this->getPointeur($sn, $deviceType);
        $ptLenght = strlen($newPointeur);
        $parse = 0;
        for($i = $ptLenght - 1; $i >= 0 ; $i--){				
            $temporaryResponse[49 - $i] = chr($newPointeur[$parse]);
            $parse++;
        }
        //echo "\r\nResponse = {$temporaryResponse} length = ".strlen($temporaryResponse)."\r\n";
        return $temporaryResponse;
    }
}

/*
$deviceType = "12";
$fileName = "WLE256_12_2_v003.005.bin";
$fromIndex = 10;
*/

/* Command variables for getIndexForImg test */
//$command = "FC";
//$command = "F8";
//$command = "F7";
//$command = "F6";
//$command = "F5";

/* variables for setHeader test */

/*
$cmd = 222;
$reqId = 2;

$utils = new Utils($deviceType);
$dataResponse = new dataResponse($utils);
//$dataResponse->setFileContent4096Bytes($deviceType, $fromIndex);
//$dataResponse->getIndexForImg($deviceType);
//$dataResponse->getIndexForProg($command, $deviceType);
$dataResponse->setHeader($cmd, $reqId);
*/

/* DA Command */
/*
$sn = "WIN0C_TEST_LEA2   ";
$deviceType = "12";
$command = "DA";
$reqId = 99;
$dataResponse = new DataResponse();
$dataResponse->setHeader(cmdByte[$command], $reqId, 9);
//$temporaryResponse = $dataResponse->getPubsData(deviceTypeArray[$deviceType]);
//$output->writeln("\r\n"."TX data : ".bin2hex($temporaryResponse)."\r\n");
//$response = $dataResponse->getCesarMatrix($temporaryResponse);
$dataResponse->getCesarMatrix(
    $response = $dataResponse->getPubsData(deviceTypeArray[$deviceType])
);
*/
/* DC Command */
/*
$dataResponse = new DataResponse();
$deviceType = "12";
$reqId = 99;
$command = "DC";
$indexToGet = 000000;
$fileName = "WLE256_12_2_v003.005";
$fileContentArray = $dataResponse->setFileContent4096Bytes($dataResponse->getFileContent($deviceType, $fileName), $indexToGet);
$fileContent = $fileContentArray[0];
$nbDataToSend = $fileContentArray[1];
echo ($nbDataToSend);
*/
/*
$dataResponse->setHeader(cmdByte[$command], $reqId, $nbDataToSend);
$response = $dataResponse->getCesarMatrix(
    $temporaryResponse = $dataResponse->getResponseData($fileContent)
);
*/
//echo "\r\n"."TX data : ".bin2hex($response)."\r\n";
//$output->writeln("\r\n"."TX data : ".bin2hex($response)."\r\n");
//$output->writeln("\r\n"."TX data size: ".strlen(bin2hex($response))."\r\n");
/* D9 Command */
/*
$dataResponse = new DataResponse();
$request = new DbRequest();
$sn = "WIN0C_TEST_LEA2   ";
$deviceType = "12";
$reqId = 99;
$newPointeur = $dataResponse->getPointeur($sn, $deviceType);
$request->setLog($sn, $newPointeur);
$dataResponse->setHeader(cmdByte["DB"], $reqId, 11);
$temporaryResponse = $dataResponse->getLogByPointer($newPointeur);
//$output->writeln("\r\n"."TX data : ".bin2hex($temporaryResponse)."\r\n");
$response = $dataResponse->getCesarMatrix($temporaryResponse);
*/
//$dataResponse->setHeader(cmdByte[$command], $this->reqId, 0);
//$response = $dataResponse->getSynchroDirectoryData($this->path, deviceType[$deviceType]);