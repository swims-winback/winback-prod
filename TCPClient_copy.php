<?php
//namespace App\Class;

//include_once '../Ressource/Config/dataConfig.php';
//include_once '../Ressource/Config/PTFConfig.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/Config/config.php');
require_once CONFIG_PATH.'/dataConfig.php';

/* Prod Address */
$address  = "51.91.18.215";
/* Dev Address */
//$address = "10.0.0.78";
$service_port = '5006';
$socket;

function connectToServer(){
    
    $GLOBALS['socket'] = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if ($GLOBALS['socket'] === false) {
        echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
    }
    //$result = socket_connect($GLOBALS['socket'], $GLOBALS['address'], $GLOBALS['service_port']);
    $result = socket_connect($GLOBALS['socket'], $GLOBALS['address'], $GLOBALS['service_port']);
    if ($result === false) {
        echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($GLOBALS['socket'])) . "\n";
    }
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
}
function closeCnx(){
    socket_close($GLOBALS['socket']);
}

function sendMsgToServer($msg){
    socket_write($GLOBALS['socket'], $msg, strlen($msg));
}

function getMsgConnectFromServer(){
    $buf = 'This is my buffer.';
    
    if (false !== ($bytes = socket_recv($GLOBALS['socket'], $buf, 1, MSG_WAITALL))) {
        closeCnx();
    } else {
        echo "socket_recv() failed; reason: " . socket_strerror(socket_last_error($GLOBALS['socket'])) . "\n";
        closeCnx();
    }
    return $buf;    
}

function getMsgFromServer(){
    $buf = 'This is my buffer.';
    
    if (false !== ($bytes = socket_recv($GLOBALS['socket'], $buf, 240, MSG_WAITALL))) {
        closeCnx();
    } else {
        echo "socket_recv() failed; reason: " . socket_strerror(socket_last_error($GLOBALS['socket'])) . "\n";
        closeCnx();
    }
    return $buf;    
}

function setFooter($oTram){
	$tramSize = 0;
	for($parse = 0; $parse < strlen($oTram); $parse++){
		$tramSize = $tramSize + (hexdec(bin2hex($oTram[$parse])) * (($parse%255)+1));
    }
    return chr($tramSize);

}

function decodeData($data){
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


function hex_to_ascii($str)
{
	$convertHex = array(0 => '0', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9',
						10 => 'A', 11 => 'B', 12 => 'C', 13 => 'D', 14 => 'E', 15 => 'F');
						
	$returnValue = '';
	
    for ($i = 0, $j = strlen($str); $i < $j; $i++) {
        $returnValue .= $convertHex[ord($str[$i])/16];
        $returnValue .= $convertHex[ord($str[$i])%16];
    }
    return $returnValue;
}

function setMsg($cmd,$datasize, $trackZone0, $trackZone1, $tagTouch0, $tagTouch1, $sn, $page, $cmdString = ""){
    $msg = $sn;
	if(isset($cmdString) && !empty($cmdString)){
		$datasize = $datasize + strlen($cmdString) + 1;
		$data = chr(170).chr(255).chr(0).chr($cmd).chr($datasize/256).chr($datasize%256).chr($trackZone0).chr($trackZone1).chr($tagTouch0).chr($tagTouch1).chr($page).$cmdString.chr(0);
	}else{
		$data = chr(170).chr(255).chr(0).chr($cmd).chr($datasize/256).chr($datasize%256).chr($trackZone0).chr($trackZone1).chr($tagTouch0).chr($tagTouch1).chr($page);
	}
    $msg .= $data.setFooter($data);
        
    return $msg;
}

if(isset($_POST)){

    switch ($_POST['action']) {
        case 'connect':
            connectToServer();
            sendMsgToServer($_POST['sn']);
			$isConnect = getMsgConnectFromServer();
			
			echo $isConnect;
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
                $msgToSend = setMsg(COMMAND['touch'], 5, $trackZone, 0, 0, 12, $_POST['sn']);
            }else if(($_POST['cmd'] == 'timerSlide')){
                $trackZone = ((($_POST['tagTouch1'] + 2.5)/5) - 1) * 255/12;
                echo $_POST['tagTouch1']." trackzone = ".$trackZone;
                $msgToSend = setMsg(COMMAND['touch'], 5, $trackZone, 0, 0, 13, $_POST['sn']);
            }else{
                $msgToSend = setMsg(COMMAND['touch'], 5, $trackZone, 0, 0, DATA_DEVICE[$_POST['cmd']][$_POST['tagTouch1']], $_POST['sn'], $_POST['page']);
            }
            connectToServer();
            sendMsgToServer($msgToSend);
            $response = getMsgFromServer();
            $diag = decodeData($response);
            echo json_encode($diag);
            break;
        case 'periodictyConnect':
            connectToServer();
            $response = getMsgFromServer();
            $diag = decodeData($response);
            echo json_encode($diag);
            break;
        case 'test':
            $msgToSend = setMsg(0, 5, 0, 0, 0, 0, $_POST['sn'], $_POST['page']);
            connectToServer();
            sendMsgToServer($msgToSend);
            $response = getMsgFromServer();
			$diag[] = substr($response, 20);
            echo json_encode($diag);
            break;
		case 'touchTest':
			$tagTouch0 = $_POST['tagTouch']/256;
			$tagTouch1 = $_POST['tagTouch']%256;
			$trackTouch0 = $_POST['trackerTouch']/256;
			$trackTouch1 = $_POST['trackerTouch']%256;
            $msgToSend = setMsg(1, 5, $trackTouch0, $trackTouch1, $tagTouch0, $tagTouch1, $_POST['sn'], $_POST['page']);
            connectToServer();
            sendMsgToServer($msgToSend);
            $response = getMsgFromServer();
			$diag[] = substr($response, 20);
            echo json_encode($diag);
			break;
		case 'cmdTest':
            $msgToSend = setMsg(2, 5, 0, 0, 0, 0, $_POST['sn'], $_POST['page'], $_POST['tagTouch1']);
            connectToServer();
            sendMsgToServer($msgToSend);
            $response = getMsgFromServer();
			$diag[] = substr($response, 20);
            echo json_encode($diag);
			break;
		case 'pageTest':
			$GLOBALS['page'] = $_POST['tagTouch1'];
			break;
        default:
            break;
    }
}