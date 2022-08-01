<?php
namespace App\Server;
ini_set('memory_limit','128M');
//require_once dirname(__DIR__).'/config.php';
//require_once(__DIR__.'/config.php');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__, 3).'/configServer/config.php';

class Utils {
    
    //private $deviceType;
    function __construct() {
    }

    /**
     * List files in directory, ascending order
     *
     * @return array array of files in directory, in ascending order
     */
    function listFiles(string $deviceType, $path)
    {
        if (file_exists($path.deviceTypeArray[$deviceType])) {
            return array_diff(scandir($path.deviceTypeArray[$deviceType]), array('..', '.'));
        }
        else {
            echo "\r\nUhuh, something went wrong ! Path doesn't exist, please check that ".$path.deviceTypeArray[$deviceType]." exists.\r\n";
            echo "\r\n #################### \r\n";
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
            return false;
        }
        if(filesize($fileArch) !== filesize($fileUp)) {
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
		}
        return true;
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
    function checkFile(string $deviceType, string $boardType = '2') : string|bool
    {
        $scanPackFile = $this->listFiles($deviceType, PACK_PATH); // Package list
        $scanArchFile = $this->listFiles($deviceType, PACK_ARCH_PATH); // Archive list

        if(count($scanPackFile) === count($scanArchFile))
        {
            $lastVersUp = $this->getVersion($scanPackFile);
            $lastVersArch = $this->getVersion($scanArchFile);

            if($lastVersArch === $lastVersUp)
            {
                $filename = stFILENAME."_".$deviceType."_".$boardType."_v".$lastVersUp.extFILENAME;
                $lastUpVerFile = PACK_PATH.deviceTypeArray[$deviceType].$filename;
                $lastArchVerFile = PACK_ARCH_PATH.deviceTypeArray[$deviceType].$filename;
                if($this->compareFile($lastArchVerFile,$lastUpVerFile))
                {
                    return $filename;
                }
                else
                {
					echo "error 1 ";
                    echo "\r\nUhuh, something went wrong ! CompareFile doesn't work !\r\n";
                    echo "\r\n #################### \r\n";
                    return false;
                }
            }
            else{
				echo "error 2 ";
                echo "\r\nUhuh, something went wrong ! Versions of package file and archive file are different, please check your files.\r\n";
                echo "\r\n #################### \r\n";
                return false;
            }
        }
        else
        {
			echo "error 3 ";
            echo "\r\nUhuh, something went wrong ! Package list and Archive list don't contain the same number of elements, please check your files lists.\r\n";
            echo "\r\n #################### \r\n";
            return false;
        }
		echo "error 4 ";
        echo "\r\nUhuh, something went wrong !\r\n";
        echo "\r\n #################### \r\n";
        return false;
    }

    /**
     * Get list of software files, 
     * Set software filename with version
     *
     * @param string $deviceType
     * @param string $boardType
     * @return string|boolean
     */
    /*
    function setVersionFilename(string $deviceType, $boardType = '2') : string|bool
    {
        if($this->checkFile($deviceType, $boardType)){
            //$arrayFiles = $this->checkFile($deviceType, $boardType);
            $lastVerFile = $this->checkFile($deviceType, $boardType);
            //echo "lastverfile: ".$lastVerFile;
            return $lastVerFile;
        }
        else {
            return false;
        }
    }
    */
    /**
     * set software filename, verify if file exist in package folder, returns file content into a string
     * equivalence of fileContent in original code
     * @param string $deviceType
     * @return string|boolean
     */
    function getFileContent(string $deviceType, $fileName) : string|bool
    {
        //$fileName = $this->setVersionFilename($deviceType, $boardType = '2');
		if(file_exists(PACK_PATH.deviceTypeArray[$deviceType].$fileName)){
			return file_get_contents(PACK_PATH.deviceTypeArray[$deviceType].$fileName);
		}
        else
        {
			$aValue = explode('_', $fileName);
            echo "\r\naValue: ".$aValue[2];
			return file_get_contents($this->checkFile($deviceType, $aValue[2]));
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
}

/*
$deviceType = "12";
$utils = new Utils($deviceType);
$utils->getContentFromIndex($deviceType, $index=10, $length=FW_OCTETS);
*/
//$utils->getVersion($deviceType, $boardType = '2');
// verify if check file & set version filename return the same thing
/*
$deviceType = "12";
$utils = new Utils($deviceType);
echo "\r\n".$utils->checkFile($deviceType)."\r\n";
echo "\r\n".$utils->setVersionFilename($deviceType)."\r\n";
*/
/*
$deviceType = "12";
$utils = new Utils($deviceType);
$path=PACK_PATH;
echo ($utils->getVersion(array_diff(scandir($path.deviceTypeArray[$deviceType]), array('..', '.'))));
*/