<?php
namespace App\Class;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

//include_once '../Ressource/Config/dataConfig.php';
//include_once '../Ressource/Config/PTFConfig.php';
//require_once($_SERVER['DOCUMENT_ROOT'] . '/Config/config.php');

require_once dirname(__FILE__, 3).'/configServer/config.php';
//require_once CONFIG_PATH.'/dataConfig.php';
global $address;
$address  = "51.91.18.215";
//$address = "10.0.0.78";
global $service_port;
$service_port = '5006';
global $socket;

class TCPClient extends AbstractController
{
    /* Prod Address */
    //private $address  = "51.91.18.215";
    /* Dev Address */
    //$address = "10.0.0.78";
    //private $service_port = '5006';
    //private $socket;


    function connectToServer(){
        $GLOBALS['address'];
        $GLOBALS['service_port'];


        $GLOBALS['socket'] = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($GLOBALS['socket'] === false) {
            echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
        }
        //$result = socket_connect($GLOBALS['socket'], $GLOBALS['address'], $GLOBALS['service_port']);
        var_dump($GLOBALS['service_port']);
        $result = socket_connect($GLOBALS['socket'], $GLOBALS['address'], $GLOBALS['service_port']);
        if ($result === false) {
            echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($GLOBALS['socket'])) . "\n";
        }
        return $GLOBALS['socket'];
    }


    public function connectToServerCopy(){
        
        $address  = "51.91.18.215";
        $service_port = '5006';
        
        //$GLOBALS['address'] = ADDRESS;
        //$GLOBALS['service_port'] = PORT;
        /*
        $GLOBALS['socket'] = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($GLOBALS['socket'] === false) {
            echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
        }
        */
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
        }
        /*
        if ($GLOBALS['socket'] === false) {
            echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
        }
        */
        //$result = socket_connect($GLOBALS['socket'], $GLOBALS['address'], $GLOBALS['service_port']);
        /*
        $result = socket_connect($GLOBALS['socket'], $GLOBALS['address'], $GLOBALS['service_port']);
        if ($result === false) {
            echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($GLOBALS['socket'])) . "\n";
        }
        */

        /*
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
        }
        //$result = socket_connect($GLOBALS['socket'], $GLOBALS['address'], $GLOBALS['service_port']);
        $result = socket_connect($socket, PTF_ADDR, PTF_PORT);
        if ($result === false) {
            echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
        }
        */
        $result = socket_connect($socket, ADDRESS, PORT);
        if ($result === false) {
            echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
        }
        return $socket;
    }
    public function closeCnx($socket){
        socket_close($socket);
    }
    
    public function sendMsgToServer($msg, $socket){
        socket_write($socket, $msg, strlen($msg));
    }
    
    public function getMsgConnectFromServer($socket){
        $buf = 'This is my buffer.';
        
        if (false !== ($bytes = socket_recv($socket, $buf, 1, MSG_WAITALL))) {
            $this->closeCnx($socket);
        } else {
            echo "socket_recv() failed; reason: " . socket_strerror(socket_last_error($socket)) . "\n";
            $this->closeCnx($socket);
        }
        return $buf;    
    }
    
    public function getMsgFromServer($socket){
        $buf = 'This is my buffer.';
        
        if (false !== ($bytes = socket_recv($socket, $buf, 240, MSG_WAITALL))) {
            $this->closeCnx($socket);
        } else {
            echo "socket_recv() failed; reason: " . socket_strerror(socket_last_error($socket)) . "\n";
            $this->closeCnx($socket);
        }
        return $buf;    
    }
    
    public function setFooter($oTram){
        $tramSize = 0;
        for($parse = 0; $parse < strlen($oTram); $parse++){
            $tramSize = $tramSize + (hexdec(bin2hex($oTram[$parse])) * (($parse%255)+1));
        }
        return chr($tramSize);
    
    }
    
    public function decodeData($data){
        for($parse = 4; $parse < 18; $parse +=2){
                $diag[DATA_BX_LABEL[$parse]] = '';
                if(isset(DATA_MESURE_PRE[$parse])){
                    $diag[DATA_BX_LABEL[$parse]] = DATA_MESURE_PRE[$parse];
                }
                if(DATA_BX_VALUE[DATA_BX_LABEL[$parse]][hexdec($data[$parse])+hexdec($data[$parse + 1])] !== NULL){
                        $diag[DATA_BX_LABEL[$parse]] .= DATA_BX_VALUE[DATA_BX_LABEL[$parse]][hexdec($data[$parse])+hexdec($data[$parse + 1])];
                }else{
                        $diag[DATA_BX_LABEL[$parse]] .= hexdec($data[$parse])*16+hexdec($data[$parse + 1]);
                }
                if(isset(DATA_MESURE_SUF[$parse])){
                    $diag[DATA_BX_LABEL[$parse]] .= DATA_MESURE_SUF[$parse];
                }
        }
        
        for($parse = 18; $parse < 62; $parse += 4){
            $diag[DATA_BX_LABEL[$parse]] = '';
            if(isset(DATA_MESURE_PRE[$parse])){
                $diag[DATA_BX_LABEL[$parse]] = DATA_MESURE_PRE[$parse];
            }
            if(isset(DATA_BX_LABEL[$parse])){
                if ((isset(DATA_BX_VALUE[DATA_BX_LABEL[$parse]][hexdec($data[$parse])+hexdec($data[$parse + 1])]))
                    && (DATA_BX_VALUE[DATA_BX_LABEL[$parse]][hexdec($data[$parse])+hexdec($data[$parse + 1])] !== NULL)){
                        $diag[DATA_BX_LABEL[$parse]] .= DATA_BX_VALUE[DATA_BX_LABEL[$parse]][hexdec($data[$parse])+hexdec($data[$parse + 1])];
                }else{
                        $diag[DATA_BX_LABEL[$parse]] .= hexdec($data[$parse])*16+hexdec($data[$parse + 1]);
                        $diag[DATA_BX_LABEL[$parse]] .= '.';
                        $diag[DATA_BX_LABEL[$parse]] .= hexdec($data[$parse+2])*16+hexdec($data[$parse + 3]);
                }
            }
            if(isset(DATA_MESURE_SUF[$parse])){
                $diag[DATA_BX_LABEL[$parse]] .= DATA_MESURE_SUF[$parse];
            }
        }
    
        for($parse = 62; $parse < 66; $parse += 2){
            $diag[DATA_BX_LABEL[$parse]] = '';
            if(isset(DATA_MESURE_PRE[$parse])){
                $diag[DATA_BX_LABEL[$parse]] = DATA_MESURE_PRE[$parse];
            }
            if(DATA_BX_VALUE[DATA_BX_LABEL[$parse]][hexdec($data[$parse])+hexdec($data[$parse + 1])] !== NULL){
                    $diag[DATA_BX_LABEL[$parse]] .= DATA_BX_VALUE[DATA_BX_LABEL[$parse]][hexdec($data[$parse])+hexdec($data[$parse + 1])];
            }else{
                    $diag[DATA_BX_LABEL[$parse]] .= hexdec($data[$parse])*16+hexdec($data[$parse + 1]);
            }
            if(isset(DATA_MESURE_SUF[$parse])){
                $diag[DATA_BX_LABEL[$parse]] .= DATA_MESURE_SUF[$parse];
            }
        }
        
        return $diag;
    }
    

    public function setMsg($cmd,$datasize, $trackZone0, $trackZone1, $tagTouch0, $tagTouch1, $sn, $page, $cmdString = ""){
        $msg = $sn;
        if(isset($cmdString) && !empty($cmdString)){
            $datasize = $datasize + strlen($cmdString) + 1;
            $data = chr(170).chr(255).chr(0).chr($cmd).chr($datasize/256).chr($datasize%256).chr($trackZone0).chr($trackZone1).chr($tagTouch0).chr($tagTouch1).chr($page).$cmdString.chr(0);
        }else{
            $data = chr(170).chr(255).chr(0).chr($cmd).chr($datasize/256).chr($datasize%256).chr($trackZone0).chr($trackZone1).chr($tagTouch0).chr($tagTouch1).chr($page);
        }
        $msg .= $data.$this->setFooter($data);
            
        return $msg;
    }

    /**
    * @Route("admin/tcpclient/", name="tcpclient")
    */
    public function runTCPClient() 
    {
        //echo json_encode("function runtcpclient");
        if(isset($_POST)){
            //var_dump($_POST);
            //echo $_POST['action'];
            //echo json_encode("function runtcpclient2");
            $socket = $this->connectToServer();
            switch ($_POST['action']) {
                case 'connect':
                    //$socket = $this->connectToServer();
                    $this->sendMsgToServer($_POST['sn'], $socket);
                    $isConnect = $this->getMsgConnectFromServer($socket);
                    
                    echo $isConnect;
                    return $this->redirectToRoute('device');
                    //closeCnx();
                    break;
                case 'disconnect':
                    //closeCnx();
                    break;
                case 'buttonTouch':
                    $trackZone = 0;
                    if(($_POST['cmd'] == 'tecaIntensity')){
                        if($_POST['tagTouch1'] == 10)
                            $add = 0;
                        else {
                            $add = 0.5;
                        }
                        $trackZone = ($_POST['tagTouch1'] + $add) * 255/10;
                        $msgToSend = $this->setMsg(COMMAND['touch'], 5, $trackZone, 0, 0, 12, $_POST['sn']);
                    }else if(($_POST['cmd'] == 'timerSlide')){
                        $trackZone = ((($_POST['tagTouch1'] + 2.5)/5) - 1) * 255/12;
                        echo $_POST['tagTouch1']." trackzone = ".$trackZone;
                        $msgToSend = $this->setMsg(COMMAND['touch'], 5, $trackZone, 0, 0, 13, $_POST['sn']);
                    }else{
                        $msgToSend = $this->setMsg(COMMAND['touch'], 5, $trackZone, 0, 0, DATA_DEVICE[$_POST['cmd']][$_POST['tagTouch1']], $_POST['sn'], $_POST['page']);
                    }
                    //$socket = $this->connectToServer();
                    $this->sendMsgToServer($msgToSend, $socket);
                    $response = $this->getMsgFromServer($socket);
                    $diag = $this->decodeData($response);
                    echo json_encode($diag);
                    break;
                case 'periodictyConnect':
                    //$socket = $this->connectToServer();
                    $response = $this->getMsgFromServer($socket);
                    $diag = $this->decodeData($response);
                    echo json_encode($diag);
                    break;
                case 'test':
                    $msgToSend = $this->setMsg(0, 5, 0, 0, 0, 0, $_POST['sn'], $_POST['page']);
                    //$socket = $this->connectToServer();
                    $this->sendMsgToServer($msgToSend, $socket);
                    $response = $this->getMsgFromServer($socket);
                    $diag[] = substr($response, 20);
                    echo json_encode($diag);
                    break;
                case 'touchTest':
                    $tagTouch0 = $_POST['tagTouch']/256;
                    $tagTouch1 = $_POST['tagTouch']%256;
                    $trackTouch0 = $_POST['trackerTouch']/256;
                    $trackTouch1 = $_POST['trackerTouch']%256;
                    $msgToSend = $this->setMsg(1, 5, $trackTouch0, $trackTouch1, $tagTouch0, $tagTouch1, $_POST['sn'], $_POST['page']);
                    //$socket = $this->connectToServer();
                    $this->sendMsgToServer($msgToSend, $socket);
                    $response = $this->getMsgFromServer($socket);
                    $diag[] = substr($response, 20);
                    echo json_encode($diag);
                    break;
                case 'cmdTest':
                    $msgToSend = $this->setMsg(2, 5, 0, 0, 0, 0, $_POST['sn'], $_POST['page'], $_POST['tagTouch1']);
                    //$socket = $this->connectToServer();
                    $this->sendMsgToServer($msgToSend, $socket);
                    $response = $this->getMsgFromServer($socket);
                    $diag[] = substr($response, 20);
                    echo json_encode($diag);
                    break;
                case 'pageTest':
                    $GLOBALS['page'] = $_POST['tagTouch1'];
                    break;
                default:
                    break;
            }
            //return true;
        }
        else {
            //return false;
        }
    }    

}