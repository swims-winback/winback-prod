<?php
namespace App\Server;

use App\Entity\DeviceFamily;
use App\Repository\DeviceFamilyRepository;
use Exception;
//use Monolog\Logger;

ini_set('memory_limit','128M');
//require_once dirname(__DIR__).'/config.php';
//require_once(__DIR__.'/config.php');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//require_once dirname(__FILE__, 3).'/configServer/config.php';

class Utils {
    
    //private $deviceType;
    private $deviceFamily;
    private $deviceFamilyRepository;
    private $dbRequest;
    function __construct() {
    }
    /*
    function __construct(DbRequest $dbRequest) {
        $this->dbRequest;
    }
    */
    /**
     * List files in directory, ascending order
     *
     * array of files in directory, in ascending order
     */
    function listFiles(string $deviceType, $path)
    {
        if (file_exists($path.deviceTypeArray[$deviceType])) {
            return array_diff(scandir($path.deviceTypeArray[$deviceType]), array('..', '.'));
        }
        else {
            echo "\r\nUhuh, something went wrong ! Path doesn't exist, please check that ".$path.deviceTypeArray[$deviceType]." exists.\r\n";
            echo "\r\n #################### \r\n";
            return false;
        }
    }

    /**
     * Get device version from a file list
     * 
     * @param array $fileList
     * @param string $boardType
     * @return string $version
     */
    function getVersion(array $fileList, string $boardType = '2') : string
    {
        //$fileList = $this->listUpFile($deviceType);
        if ($fileList) {
            foreach ($fileList as $fileName) {
                $versionValue = basename($fileName, extFILENAME);
                $aValue = explode($boardType.'_v', $versionValue);
                /*
                echo "\r\n".'Filename : '.$fileName."\r\n";
                */
                if(isset($aValue[1])){
                    $version = $aValue[1];
                    /*
                    echo "\r\n".'Version : '.$aValue[1]."\r\n";
                    echo "\r\ngetVersion function is working correctly !\r\n";
                    echo "\r\n #################### \r\n";
                    */
                    return $version;
                }
            }
            echo "\r\nUhuh, something went wrong ! Filelist is empty, please check your package folder.\r\n";
            echo "\r\n #################### \r\n";
        }

    }

    /**
     * Get last file of a list of software files and extract the version number
     */
    function getVersion2(array $fileList) : string
    {
        if ($fileList) {
            $versionValue = basename(end($fileList), extFILENAME);
            $version = substr($versionValue, -7);
            return $version;
        }
        echo "\r\nUhuh, something went wrong ! Filelist is empty, please check your package folder.\r\n";
        echo "\r\n #################### \r\n";
        return false;

    }

    /**
     * Compare contents of package file and archive file
     *
     * @param string $fileArch
     * @param string $fileUp
     * @return boolean
     */
    function compareFile(string $fileArch, string $fileUp) : bool
    {
        if (!file_exists($fileArch) || !file_exists($fileUp)) {
            echo "Archive File {$fileArch} or Package File {$fileUp} not present on the server.";
            //$logger->error("Archive File {$fileArch} or Package File {$fileUp} not present on the server.");
            return false;
        }
        if(filesize($fileArch) !== filesize($fileUp)) {
            echo "Archive File {$fileArch} and Package File {$fileUp} are not the same size.";
            //$logger->error("Archive File {$fileArch} and Package File {$fileUp} are not the same size.");
            return false;
        }
        // Check if content is different
        $ahandle = fopen($fileArch, 'rb');
        $bhandle = fopen($fileUp, 'rb');
        

		if($ahandle && $bhandle){
            
			  if(fread($ahandle, 8192) != fread($bhandle, 8192))
			  {
                echo "\r\nUhuh, something went wrong ! Contents of package file and archive file are different, please check your files.\r\n";
                echo "\r\n #################### \r\n";
                return false;
			  }
			fclose($ahandle);
			fclose($bhandle);

            //return true;
		}
        return true;
    }

    function compareFileTest(string $fileArch, string $fileUp) : bool
    {
        if (!file_exists($fileArch) || !file_exists($fileUp)) {
            echo "version not present on the server";
            //$logger->error("Archive File {$fileArch} or Package File {$fileUp} not present on the server.");
            return false;
        }
        if(filesize($fileArch) !== filesize($fileUp)) {
            //$logger->error("Archive File {$fileArch} and Package File {$fileUp} are not the same size.");
            return false;
        }
        // Check if content is different
        $ahandle = fopen($fileArch, 'rb');
        $bhandle = fopen($fileUp, 'rb');
        

		if($ahandle && $bhandle){
            
			  if(fread($ahandle, 8192) != fread($bhandle, 8192))
			  {
                echo "\r\nUhuh, something went wrong ! Contents of package file and archive file are different, please check your files.\r\n";
                echo "\r\n #################### \r\n";
                return false;
			  }
			fclose($ahandle);
			fclose($bhandle);

            //return true;
		}
        return true;
    }

    /**
     * Summary of checkLastVersion
     * @param string $deviceType
     * @param string $boardType
     * @return array|bool
     */
    function checkLastVersion(string $deviceType, string $boardType = '2')
    {
        $dbRequest = new DbRequest;
        //$scanPackFile = $this->listFiles($deviceType, PACK_PATH); // Package list
        //print_r($scanPackFile);
        //if ($scanPackFile!=false) {
            $lastVersUp = $dbRequest->getDeviceTypeActualVers($deviceType);
            //print_r($lastVersUp);
            //$filename = stFILENAME."_".$deviceType."_".$boardType."_v".$lastVersUp.extFILENAME;
            //$filename = $lastVersUp["name"];
            //$version = $lastVersUp["version"];
            //$lastUpVerFile = PACK_PATH.deviceTypeArray[$deviceType].$filename;
            return $lastVersUp;
        //}
        //echo ("\r\nerror :\r\n
        //\r\nUhuh, something went wrong ! Package list is not found.\r\n
        //\r\n####################\r\n");
        //return false;
    }
    /**
     * List files in archive & package folder; 
     * if size of lists are identical, 
     * get version of package & archive; 
     * if versions are identicals, 
     * build software filename & returns it
     *
     * @param string $deviceType
     * @param string $boardType
     * @return string|boolean $filename
     */

    /*
    function checkFile(string $deviceType, string $boardType = '2') : string|bool
    {
        $scanPackFile = $this->listFiles($deviceType, PACK_PATH); // Package list
        $scanArchFile = $this->listFiles($deviceType, PACK_ARCH_PATH); // Archive list
        if ($scanPackFile!=false && $scanArchFile!=false) {
            if(count($scanPackFile) === count($scanArchFile))
            {
                $lastVersUp = $this->getVersion2($scanPackFile);
                $lastVersArch = $this->getVersion2($scanArchFile);
                if($lastVersArch === $lastVersUp)
                {
                    $filename = stFILENAME."_".$deviceType."_".$boardType."_v".$lastVersUp.extFILENAME;
                    $lastUpVerFile = PACK_PATH.deviceTypeArray[$deviceType].$filename;
                    $lastArchVerFile = PACK_ARCH_PATH.deviceTypeArray[$deviceType].$filename;
                    if($this->compareFile($lastArchVerFile, $lastUpVerFile))
                    {
                        return $filename;
                    }
                    else
                    {
                        echo ("\r\nerror 1 :\r\n
                        \r\nUhuh, something went wrong ! CompareFile doesn't work !\r\n
                        \r\n####################\r\n");
                        return false;
                    }
                }
                else{
                    echo ("\r\nerror 2 :\r\n
                    \r\nUhuh, something went wrong ! Versions of package file and archive file are different, please check your files.\r\n
                    \r\n####################\r\n");
                    return false;
                }
            }
            echo ("\r\nerror 3 :\r\n
                \r\nUhuh, something went wrong ! Package list and Archive list don't contain the same number of elements, please check your files lists.\r\n
                \r\n####################\r\n");
            return false;
        }
        echo ("\r\nerror :\r\n
        \r\nUhuh, something went wrong ! Package list and Archive list are not found.\r\n
        \r\n####################\r\n");
        return false;
    }
    */
    
    function checkFile(string $deviceType, string $boardType = '2') : string|bool
    {
        $scanPackFile = $this->listFiles($deviceType, $_ENV['PACK_PATH']); // Package list
        #$scanArchFile = $this->listFiles($deviceType, PACK_ARCH_PATH); // Archive list
        #if ($scanPackFile!=false && $scanArchFile!=false) {
        if ($scanPackFile!=false) {
            $lastVersUp = $this->getVersion2($scanPackFile);

            $filename = stFILENAME."_".$deviceType."_".$boardType."_v".$lastVersUp.extFILENAME;
            $lastUpVerFile = $_ENV['PACK_PATH'].deviceTypeArray[$deviceType].$filename;
            //$lastArchVerFile = PACK_ARCH_PATH.deviceTypeArray[$deviceType].$filename;
            return $filename;
        }
        echo ("\r\nerror :\r\n
        \r\nUhuh, something went wrong ! Package list is not found.\r\n
        \r\n####################\r\n");
        return false;
    }
    
    function checkFileTest(string $deviceType, string $boardType = '2') : string|bool
    {
        $scanPackFile = $this->listFiles($deviceType, $_ENV['PACK_PATH']); // Package list
        #$scanArchFile = $this->listFiles($deviceType, PACK_ARCH_PATH); // Archive list
        #if ($scanPackFile!=false && $scanArchFile!=false) {
        if ($scanPackFile!=false) {
            $lastVersUp = $this->getVersion2($scanPackFile);

            $filename = stFILENAME."_".$deviceType."_".$boardType."_v".$lastVersUp.extFILENAME;
            $lastUpVerFile = $_ENV['PACK_PATH'].deviceTypeArray[$deviceType].$filename;
            //$lastArchVerFile = PACK_ARCH_PATH.deviceTypeArray[$deviceType].$filename;
            return $filename;
        }
        echo ("\r\nerror :\r\n
        \r\nUhuh, something went wrong ! Package list is not found.\r\n
        \r\n####################\r\n");
        return false;
    }
    /*
    function checkFileTest(string $deviceType, string $boardType = '2') : string|bool
    {
        $scanPackFile = $this->listFiles($deviceType, PACK_PATH); // Package list
        $scanArchFile = $this->listFiles($deviceType, PACK_ARCH_PATH); // Archive list
        if ($scanPackFile!=false && $scanArchFile!=false) {
            if(count($scanPackFile) === count($scanArchFile))
            {
                $lastVersUp = $this->getVersion2($scanPackFile);
                $lastVersArch = $this->getVersion2($scanArchFile);
                if($lastVersArch === $lastVersUp)
                {
                    $filename = stFILENAME."_".$deviceType."_".$boardType."_v".$lastVersUp.extFILENAME;
                    $lastUpVerFile = PACK_PATH.deviceTypeArray[$deviceType].$filename;
                    $lastArchVerFile = PACK_ARCH_PATH.deviceTypeArray[$deviceType].$filename;
                    if($this->compareFileTest($lastArchVerFile, $lastUpVerFile))
                    {
                        return $filename;
                    }
                    else
                    {
                        echo ("\r\nerror 1 :\r\n
                        \r\nUhuh, something went wrong ! CompareFile doesn't work !\r\n
                        \r\n####################\r\n");
                        return false;
                    }
                }
                else{
                    echo ("\r\nerror 2 :\r\n
                    \r\nUhuh, something went wrong ! Versions of package file and archive file are different, please check your files.\r\n
                    \r\n####################\r\n");
                    return false;
                }
            }
            echo ("\r\nerror 3 :\r\n
                \r\nUhuh, something went wrong ! Package list and Archive list don't contain the same number of elements, please check your files lists.\r\n
                \r\n####################\r\n");
            return false;
        }
        echo ("\r\nerror :\r\n
        \r\nUhuh, something went wrong ! Package list and Archive list are not found.\r\n
        \r\n####################\r\n");
        return false;
    }
    */

    /**
     * set software filename, verify file exists in package folder, returns filecontent to string
     * equivalence of fileContent in original code
     * if package file exists, return package file content
     * else return last package file ?
     * @param string $deviceType
     * @param string $fileName
     * @return string|bool
     */
    function getFileContent(string $deviceType, string $fileName) : string|bool
    {
        //print_r(PACK_PATH.deviceTypeArray[$deviceType].$fileName);
		if(file_exists($_ENV['PACK_PATH'].deviceTypeArray[$deviceType].$fileName)){
            //echo "\r\n ".$fileName . " file exists !\r\n";
            $content = file_get_contents($_ENV['PACK_PATH'].deviceTypeArray[$deviceType].$fileName);
            if ($content) {
                return $content;
            }
            else {
                echo "\r\nContent cannot be get.\r\n";
                return false;
            }
		}
        else
        {
			$aValue = explode('_', $fileName);
            $boardType = $aValue[2]; //TODO to be used in the future in file_get_contents
            $lastVersUp = $this->checkLastVersion($deviceType, $boardType);
			$actualFile = $lastVersUp["name"];
            if(file_exists($_ENV['PACK_PATH'].deviceTypeArray[$deviceType].$actualFile)){
                $content = file_get_contents($_ENV['PACK_PATH'].deviceTypeArray[$deviceType].$actualFile);
                if (!$content) {;
                    //throw new Exception('Content cannot be get.');
                    echo "\r\nContent cannot be get.\r\n";
                    return false;
                }
                return $content;
            }
            echo "\r\nFile doesn't exist, please check again.\r\n";
            return false;
		}
    }
    function getFileContentTest(string $deviceType, string $fileName) : bool
    {
		if(file_exists($_ENV['PACK_PATH'].deviceTypeArray[$deviceType].$fileName)){
            //echo("\r\n ".$fileName . " file exists !\r\n");
            $content = file_get_contents($_ENV['PACK_PATH'].deviceTypeArray[$deviceType].$fileName);
            if (!$content) {
                //throw new Exception('Content cannot be get.');
                echo "\r\nContent cannot be get.\r\n";
                return false;
            }
            return true;
		}
        else
        {
            //echo();
			$aValue = explode('_', $fileName);
            $boardType = $aValue[2]; //TODO to be used in the future in file_get_contents
            $lastVersUp = $this->checkLastVersion($deviceType, $boardType);
			$actualFile = $lastVersUp["name"];
            if(file_exists($_ENV['PACK_PATH'].deviceTypeArray[$deviceType].$actualFile)){
                $content = file_get_contents($_ENV['PACK_PATH'].deviceTypeArray[$deviceType].$actualFile);
                if (!$content) {
                    echo "\r\nContent cannot be get.\r\n";
                    return false;
                }
                return true;
            }
            echo "\r\nFile doesn't exist, please check again.\r\n";
            return false;
		}
    }

    /**
     * Extract specific data from content file, based on an index and a certain length
     * Return string corresponding to data or false if nothing is found
     *
     * @param string $deviceType
     * @param integer $index
     * @param [type] $length
     * @return string|boolean
     */
    function getContentFromIndex($fileContent, int $index=0, int $length=FW_OCTETS) : string|bool
    {
        //$fileContent = $this->getFileContent($deviceType);
        //$result = substr($fileContent, $index, $length);
        return substr($fileContent, $index, $length);
    }

    function clean($string) {
        $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
     
        return preg_replace('/[^A-Za-z0-9\-]/', '_', $string); // Removes special chars.
    }
}