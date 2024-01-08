<?php

class Device
{
	public string $id;
	public string $sn;
}
class Test {

    public ?PDO $database = null;
    public $database2 = null;

    /*
    public function dbConnect(){
        if ($this->database === null) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $this->database = mysqli_connect('localhost', 'root', '', 'winback_dev', 3306);
            //$this->database = mysqli_connect($_ENV['HOSTNAME'], $_ENV['ADMIN'], $_ENV['PWD'], $_ENV['DB'], $_ENV['DB_PORT']);
            //var_dump($this->database);
            if ($this->database -> connect_errno) {
                echo "Failed to connect to MySQL: " . $this->database -> connect_error;
                return "error";
            }
        }
        return $this->database;
    }
    */
    function dbConnect()
    {
        if ($this->database == null) {
            $this->database = new PDO('mysql:host=localhost;dbname=winback_dev;charset=utf8', 'root', '');
        }
        return $this->database;
    }

    function getDevice($identifier) {
        $database = $this->dbConnect();
        $request = "SELECT id, sn FROM device WHERE id = ?";
        $statement = $database->prepare($request);
        $statement->execute([$identifier]);
        $row = $statement->fetch();
        var_dump($row);
        return $row;
    }

    function setForced($identifier, $forced) {
        $database = $this->dbConnect();
        $request = "UPDATE device SET forced = $forced WHERE id = ?";
        $statement = $database->prepare($request);
        $statement->execute([$identifier]);
        $row = $statement->fetch();
        var_dump($row);
    }

    function addDevice($identifier) {
        $database = $this->dbConnect();
        $request = "INSERT INTO device (device_family_id, sn, version_upload, ip_addr, created_at) VALUES ('3', $identifier, '0', '0', '".date("Y-m-d | H:i:s")."')";
        $statement = $database->prepare($request);
        $statement->execute();
        $row = $statement->fetch();
        var_dump($row);
    }

    function updateOrInsert($identifier) {
        if ($this->getDevice($identifier)) {
            $this->setForced($identifier, 1);
        }
        else {
            $this->addDevice($identifier);
        }

    }

    /* TEST */
    public function dbConnect2(){
        if ($this->database2 === null) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $this->database2 = mysqli_connect($_ENV['HOSTNAME'], $_ENV['ADMIN'], $_ENV['PWD'], $_ENV['DB'], $_ENV['DB_PORT']);
            //var_dump($this->database);
            if ($this->database2 -> connect_errno) {
                echo "Failed to connect to MySQL: " . $this->database2 -> connect_error;
                return "error";
            }
            /*
            else {
                return $this->database;
            }
            */
        }
        return $this->database2;
    }

    function sendRq($request){
        $this->database = $this->dbConnect();
        $result = mysqli_query($this->database2, $request);
        //mysqli_close($this->database);
        return $result;
    }

    function initDeviceInDB(string $sn){
        if ($sn!="") {
            $req = "INSERT INTO ".DEVICE_TABLE." (".DEVICE_TYPE.", ".SN.", ".DEVICE_VERSION.", ".VERSION_UPLOAD.",".IS_CONNECT.",".IP_ADDR.",".LOG_POINTEUR.",".SELECTED.",".CONNECTED.",".CREATED_AT.") VALUES ('3', '".$sn."', '0', '0', '1','0','0','0','0','".date("Y-m-d | H:i:s")."')";
            if ($res = $this->sendRq($req)) {
                return $res;
            }
        }
        echo "\r\nSN: {$sn}\r\n";
        echo "\r\nSN empty or vers empty or devType empty !\r\n";
        return false;
    }

    function select($attr, $from, $where = ""){
        $req = "SELECT $attr FROM $from";
        if(!empty($where)){
            $req .= " WHERE $where";
        }
        return $req;
    }
    
    function update($column, $value, $table, $where=''){
        $req = "UPDATE ".$table." SET ".$column." = '".$value."'";
        if(!empty($where)){
            $req .= " WHERE $where";
        }
        return $req;
    }

    function setDeviceInfo(string $sn, string $vers, string $ipAddr, string $logFile)
    {
        //$utils = new Utils();
        $whereCond = SN." = '".$sn."'";
        /*
        $geography = $this->getLocationInfoByIp($ipAddr); //get location by ip
        $geography["country"] = $utils->clean($geography["country"]);
        $geography["city"] = $utils->clean($geography["city"]);
        */
        // treat punctuation in name
        $req = $this->select('*', DEVICE_TABLE, $whereCond);
        if($res = $this->sendRq($req)){
            if($row = mysqli_fetch_assoc($res)){
                $req = "UPDATE ".DEVICE_TABLE." SET ".DEVICE_VERSION." = '".$vers."',".IS_CONNECT." = 1,".LOG_FILE." = '".$logFile."',".DOWNLOAD." = 0,".UPDATED_AT." = '".date('Y-m-d | H:i:s')."',".IP_ADDR." = '".$ipAddr."'";
                if(!empty($whereCond)){
                    $req .= " WHERE ".$whereCond;
                }
                $res = $this->sendRq($req);
                return $row;
            }else{
                $res = $this->initDeviceInDB($sn);
                $res2 = $this->sendRq($req);
                if($row = mysqli_fetch_assoc($res2)){
                    return $row;
                }
            }
        }
        else {
            return false;
        }
    }
}

$start_time = microtime(true);
$test = new Test();
$test->updateOrInsert('1233');
$test->updateOrInsert('4001');
$end_time = microtime(true);
$execution_time = ($end_time - $start_time);
echo " Execution time of script = ".$execution_time." sec";

/*
$data = "{1,1";
$data2 = "3215";
if (intval($data)) {
    $boardType = hexdec($data);
    echo "yes";
}
*/

/*
function compareVersion($version, $version_test) {
    $version_split = explode(".", $version);
    $prefix = intval($version_split[0]);
    $suffix = intval($version_split[1]);

    $version_split_test = explode(".", $version_test);
    $prefix_test = intval($version_split_test[0]);
    $suffix_test = intval($version_split_test[1]);

    if ($prefix_test > $prefix or ($prefix_test <= $prefix and $suffix_test >= $suffix)) {
        echo true;
        return true;
    }
}
*/

/*
$version = '3.7';
$version_test = '3.11';
$second_version_test = '3.13';
compareVersion($version, $version_test);
*/

//$space = 15 - strlen(gethostbyname("www.winback-assist.com"));
//echo ($space);
/*
foreach ($modulo_list as $key => $value) {
    getPointeur($value);
}
*/