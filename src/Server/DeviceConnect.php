<?php
namespace App\Server;

//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Server\DbRequest;
require_once dirname(__FILE__, 3).'/configServer/config.php';

//class DeviceConnect extends AbstractController {
class DeviceConnect {
    //private $deviceSn;
    private $request;
    //private $port='5005';
    public $fp;
    
    function __construct() {
        if(!empty($_POST)){
            $this->request = new DbRequest();
            $this->request->dbConnect();
            $this->deviceSn = $_POST['sn'];
            if(isset($_SESSION['fp']))
                $fp = $_SESSION['fp'];
        }
    }
    
    function setFooter($oTram){
        $tramSize = 0;
        for($parse = 0; $parse < strlen($oTram); $parse++){
            //$tramSize = $tramSize + (hexdec(bin2hex($oTram[$parse])))%256;
            $tramSize = $tramSize + (hexdec(bin2hex($oTram[$parse])) * (($parse%255)+1));
        }
        return chr($tramSize);

    }

    function setMsg($cmd, $datasize, $trackZone0, $trackZone1, $tagTouch0, $tagTouch2){
        $msg = chr(AA).chr(0).chr(0).chr($cmd).chr($datasize/256).chr($datasize%256).chr($trackZone0).chr($trackZone1).chr($tagTouch0).chr($tagTouch2);
        $msg .= $this->setFooter($msg);
        
        return $msg;
    }
    
    function sendCmd($fp, $msg){
        fwrite($fp, $msg);
        //echo fread($this->fp, 512);
    }

    //TODO not used ???
    function connectToServer(){
        /*
        ///*  
        $ip = "51.91.18.215";
        $host = "tcp://".$ip.":$this->port";
        $this->fp = stream_socket_client($host, $errno, $errstr, 30, STREAM_CLIENT_PERSISTENT);
        //
        if (!$fp) {
            $_SESSION['isConnect'] = false;
            //echo "$errstr ($errno)<br />\n";
        } else {
            $_SESSION['isConnect'] = true;
        }
        */
    }
    
    function connectToDevice(){
        //$fp = stream_socket_client(ADDRESS.":".PORT, $errno, $errstr, 30,  STREAM_CLIENT_CONNECT | STREAM_CLIENT_ASYNC_CONNECT |  STREAM_CLIENT_PERSISTENT);
        $fp = stream_socket_client(ADDRESS.":".PORT, $errno, $errstr, 30,  STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT);
        //$msg = $this->setMsg(1,4,0,0,0,2);
        if (!$fp) {
            $_SESSION['isConnect'] = false;
            //echo "$errstr ($errno)<br />\n";
        } else {
            $_SESSION['isConnect'] = true;
            //fwrite($fp, 'DE'."\r\n");
            //$data = @socket_read($fp, 4096, PHP_BINARY_READ) or die("Could not read input\n");
            //fwrite($fp, "\n");

            echo fread($fp, 4096)."\n";

            //fclose($fp);
            $cmdToSend = $this->setMsg(
                $cmd = cmdByte["DE"], 
                $datasize = 1, 
                $trackZone0 = 0, 
                $trackZone1 = 0, 
                $tagTouch0 = 0, 
                $tagTouch2 = 0
            );
            $this->sendCmd($fp, $cmdToSend);
            echo "Connected !";
            //$feedback = array();
            /*
            while ($devcon = fread($fp, 4096)) {
                $arr = unpack("H*", $devcon);
                $rawhex = trim(implode("", $arr));
                echo $rawhex;
                //$feedback =
            }
            */
            
        }
        return $fp;
    }
    
    function closeConnection($fp){
        fclose($fp);
    }
}

/*
$deviceConnect = new DeviceConnect();
//$deviceConnect->connectToDevice();
$fp = $deviceConnect->connectToDevice();
*/

/*
$cmdToSend = $deviceConnect->setMsg(
    $cmd = cmdByte["DE"], 
    $datasize = 1, 
    $trackZone0 = 0, 
    $trackZone1 = 0, 
    $tagTouch0 = 0, 
    $tagTouch2 = 0
);
$deviceConnect->sendCmd($fp, $cmdToSend);
*/