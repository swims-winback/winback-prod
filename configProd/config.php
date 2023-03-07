<?php
//ini_set('memory_limit','16M');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Europe/Paris');
/*
defined('LOCAL_PATH_BOOTSTRAP') || define("LOCAL_PATH_BOOTSTRAP", __DIR__);

// -----------------------------------------------------------------------
// DEFINE SEPERATOR ALIASES
// -----------------------------------------------------------------------
define("URL_SEPARATOR", '/');
define("DS", DIRECTORY_SEPARATOR);
define("PS", PATH_SEPARATOR);
define("US", URL_SEPARATOR);

// -----------------------------------------------------------------------
// DEFINE ROOT PATHS
// -----------------------------------------------------------------------
define("RELATIVE_PATH_ROOT", '');
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
define("HTTP_PATH_ROOT",
        isset($_SERVER["HTTP_HOST"]) ?
        $_SERVER["HTTP_HOST"] : (
        isset($_SERVER["SERVER_NAME"]) ?
        $_SERVER["SERVER_NAME"] : '_UNKNOWN_'));

// -----------------------------------------------------------------------
// DEFINE RELATIVE PATHS
// -----------------------------------------------------------------------
define("RELATIVE_PATH_BASE",
        str_replace(LOCAL_PATH_ROOT, RELATIVE_PATH_ROOT, getcwd()));
define("RELATIVE_PATH_APP", dirname(RELATIVE_PATH_BASE));
define("RELATIVE_PATH_LIBRARY", RELATIVE_PATH_APP . DS . 'vendor');
define("RELATIVE_PATH_HELPERS", RELATIVE_PATH_BASE);
define("RELATIVE_PATH_TEMPLATE", RELATIVE_PATH_BASE . DS . 'templates');
define("RELATIVE_PATH_CONFIG", RELATIVE_PATH_BASE . DS . 'config');
define("RELATIVE_PATH_PAGES", RELATIVE_PATH_BASE . DS . 'pages');
define("RELATIVE_PATH_ASSET", RELATIVE_PATH_BASE . DS . 'assets');
define("RELATIVE_PATH_ASSET_IMG", RELATIVE_PATH_ASSET . DS . 'img');
define("RELATIVE_PATH_ASSET_CSS", RELATIVE_PATH_ASSET . DS . 'css');
define("RELATIVE_PATH_ASSET_JS", RELATIVE_PATH_ASSET . DS . 'js');

// -----------------------------------------------------------------------
// DEFINE LOCAL PATHS
// -----------------------------------------------------------------------
define("LOCAL_PATH_BASE", LOCAL_PATH_ROOT . RELATIVE_PATH_BASE);
define("LOCAL_PATH_APP", LOCAL_PATH_ROOT . RELATIVE_PATH_APP);
define("LOCAL_PATH_LIBRARY", LOCAL_PATH_ROOT . RELATIVE_PATH_LIBRARY);
define("LOCAL_PATH_HELPERS", LOCAL_PATH_ROOT . RELATIVE_PATH_HELPERS);
define("LOCAL_PATH_TEMPLATE", LOCAL_PATH_ROOT . RELATIVE_PATH_TEMPLATE);
define("LOCAL_PATH_CONFIG", LOCAL_PATH_ROOT . RELATIVE_PATH_CONFIG);
define("LOCAL_PATH_PAGES", LOCAL_PATH_ROOT . RELATIVE_PATH_PAGES);
define("LOCAL_PATH_ASSET", LOCAL_PATH_ROOT . RELATIVE_PATH_ASSET);
define("LOCAL_PATH_ASSET_IMG", LOCAL_PATH_ROOT . RELATIVE_PATH_ASSET_IMG);
define("LOCAL_PATH_ASSET_CSS", LOCAL_PATH_ROOT . RELATIVE_PATH_ASSET_CSS);
define("LOCAL_PATH_ASSET_JS", LOCAL_PATH_ROOT . RELATIVE_PATH_ASSET_JS);

// -----------------------------------------------------------------------
// DEFINE URL PATHS
// -----------------------------------------------------------------------
if (US === DS) { // needed for compatibility with windows
    define("HTTP_PATH_BASE", HTTP_PATH_ROOT . RELATIVE_PATH_BASE);
    define("HTTP_PATH_APP", HTTP_PATH_ROOT . RELATIVE_PATH_APP);
    define("HTTP_PATH_LIBRARY", false);
    define("HTTP_PATH_HELPERS", false);
    define("HTTP_PATH_TEMPLATE", false);
    define("HTTP_PATH_CONFIG", false);
    define("HTTP_PATH_PAGES", false);
    define("HTTP_PATH_ASSET", HTTP_PATH_ROOT . RELATIVE_PATH_ASSET);
    define("HTTP_PATH_ASSET_IMG", HTTP_PATH_ROOT . RELATIVE_PATH_ASSET_IMG);
    define("HTTP_PATH_ASSET_CSS", HTTP_PATH_ROOT . RELATIVE_PATH_ASSET_CSS);
    define("HTTP_PATH_ASSET_JS", HTTP_PATH_ROOT . RELATIVE_PATH_ASSET_JS);
} else {
    define("HTTP_PATH_BASE", HTTP_PATH_ROOT .
            str_replace(DS, US, RELATIVE_PATH_BASE));
    define("HTTP_PATH_APP", HTTP_PATH_ROOT .
            str_replace(DS, US, RELATIVE_PATH_APP));
    define("HTTP_PATH_LIBRARY", false);
    define("HTTP_PATH_HELPERS", false);
    define("HTTP_PATH_TEMPLATE", false);
    define("HTTP_PATH_CONFIG", false);
    define("HTTP_PATH_PAGES", false);
    define("HTTP_PATH_ASSET", HTTP_PATH_ROOT .
            str_replace(DS, US, RELATIVE_PATH_ASSET));
    define("HTTP_PATH_ASSET_IMG", HTTP_PATH_ROOT .
            str_replace(DS, US, RELATIVE_PATH_ASSET_IMG));
    define("HTTP_PATH_ASSET_CSS", HTTP_PATH_ROOT .
            str_replace(DS, US, RELATIVE_PATH_ASSET_CSS));
    define("HTTP_PATH_ASSET_JS", HTTP_PATH_ROOT .
            str_replace(DS, US, RELATIVE_PATH_ASSET_JS));
}

// -----------------------------------------------------------------------
// DEFINE REQUEST PARAMETERS
// -----------------------------------------------------------------------
define("REQUEST_QUERY",
        isset($_SERVER["QUERY_STRING"]) && $_SERVER["QUERY_STRING"] != '' ?
        $_SERVER["QUERY_STRING"] : false);
define("REQUEST_METHOD",
        isset($_SERVER["REQUEST_METHOD"]) ?
        strtoupper($_SERVER["REQUEST_METHOD"]) : false);
define("REQUEST_STATUS",
        isset($_SERVER["REDIRECT_STATUS"]) ?
        $_SERVER["REDIRECT_STATUS"] : false);
define("REQUEST_PROTOCOL",
        isset($_SERVER["HTTP_ORIGIN"]) ?
        substr($_SERVER["HTTP_ORIGIN"], 0,
        strpos($_SERVER["HTTP_ORIGIN"], '://') + 3) : 'http://');
define("REQUEST_PATH",
        isset($_SERVER["REQUEST_URI"]) ?
        str_replace(RELATIVE_PATH_BASE, '',
        $_SERVER["REQUEST_URI"]) : '_UNKNOWN_');
define("REQUEST_PATH_STRIP_QUERY",
        REQUEST_QUERY ?
        str_replace('?' . REQUEST_QUERY, '', REQUEST_PATH) : REQUEST_PATH);

// -----------------------------------------------------------------------
// DEFINE SITE PARAMETERS
// -----------------------------------------------------------------------
define("PRODUCTION", false);
define("PAGE_PATH_DEFAULT", US . 'index');
define("PAGE_PATH",
        (REQUEST_PATH_STRIP_QUERY === US) ?
        PAGE_PATH_DEFAULT : REQUEST_PATH_STRIP_QUERY);

*/


/*
 *      DEFINE Header Byte Value  
*/

if (!defined('AA')) define('AA', 170);

/*
 *      DEFINE Command Byte Value 
 */

if (!defined('EF')) define('EF', 239); // TODO is it used?
if (!defined('EE')) define('EE', 238); // TODO is it used?
if (!defined('ED')) define('ED', 237); // TODO is it used?
if (!defined('FF')) define('FF', 255); // TODO is it used?
/*
define('FE', 254);
define('FD', 253);
define('F9', 249);
define('FA', 250);
define('FC', 252);
define('DE', 222);
*/


if (!defined('cmdByte')) define('cmdByte', array('FE' => 254, 'FD' => 253,'FC' => 252,'FA' => 250,'F9' => 249,'F8' => 248,'F7' => 247,'F6' => 246,'F5' => 245,'F4' => 244,'F3' => 243,'F2' => 242,'DE' => 222,'DD' => 221,'DC' => 220,'DB' => 219,'DA' => 218,'D9' => 217,'D8' => 216,'CF' => 207,'CE' => 206, 'CD' => 205, 'CC' => 204, 'CB' => 203));
if (!defined('cmdSoft')) define('cmdSoft', array('FE', 'FD', 'FC', 'FA', 'F9', 'F8', 'F7', 'F6', 'F5', 'F3', 'F2','DE','DD','DC','DB','DA','D9','D8','CF','CE', 'CD', 'CC', 'CB'));
if (!defined('cmdBack')) define('cmdBack', array('DE','DD','DC','DB','DA','D9','D8','CF','CE', 'CD', 'CC', 'CB'));

/*
PROD
if (!defined('cmdByte')) define('cmdByte', array('FE' => 254, 'FD' => 253,'FC' => 252,'FA' => 250,'F9' => 249,'F8' => 248,'F7' => 247,'F6' => 246,'F5' => 245,'F4' => 244,'F3' => 243,'F2' => 242,'DE' => 222,'DD' => 221,'DC' => 220,'DB' => 219,'DA' => 218,'D9' => 217,'D8' => 216,'CF' => 207,'CE' => 206, 'CD' => 205, 'CC' => 204, 'CB' => 203));
if (!defined('cmdSoft')) define('cmdSoft', array('FE', 'FD', 'FC', 'FA', 'F9', 'F8', 'F7', 'F6', 'F5', 'F3', 'F2','DE','DD','DC','DB','DA','D9','D8','CF','CE', 'CD'));
if (!defined('cmdBack')) define('cmdBack', array('DE','DD','DC','DB','DA','D9','D8','CF','CE', 'CD', 'CC', 'CB'));
*/
/* Define PATH */

//define( 'ROOT_DIR', dirname(__FILE__, 2) );
if (!defined('ROOT_DIR')) define( 'ROOT_DIR', dirname(__FILE__, 2) );
//if(!defined('ABS_PATH')) define('ABS_PATH', $_SERVER['DOCUMENT_ROOT'].'MyProyect/');
//define("CONFIG_FILE", ROOT_DIR."/config.php");

if (!defined('CLASS_PATH')) define("CLASS_PATH", ROOT_DIR."/src/Class/");
//if (!defined('RESSOURCE_PATH')) define("RESSOURCE_PATH", ROOT_DIR."/public/Ressource/");
if (!defined('RESSOURCE_PATH')) define('RESSOURCE_PATH', $_ENV['RESSOURCE_PATH']);
//if(!defined('ABS_RESSOURCE_PATH')) define ('ABS_RESSOURCE_PATH', $_ENV['ABS_RESSOURCE_PATH']);
if (!defined('CONFIG_PATH')) define("CONFIG_PATH", ROOT_DIR."/Config");

if (!defined('LOGS_FILE')) define("LOGS_FILE", 'wintra.log');
if (!defined('GATELOG_FILE')) define("GATELOG_FILE", 'serverGate.log');
if (!defined('PACK_PATH')) define("PACK_PATH", $_ENV['PACK_PATH']);
//if (!defined('ABS_PACK_PATH')) define("ABS_PACK_PATH", $_ENV['ABS_PACK_PATH']);
if (!defined('REL_PACK_PATH')) define("REL_PACK_PATH", $_ENV['REL_PACK_PATH']);
if (!defined('ARCH_PATH')) define("ARCH_PATH", $_ENV['ARCH_PATH']);
//if (!defined('ABS_ARCH_PATH')) define("ABS_ARCH_PATH", $_ENV['ABS_ARCH_PATH']);
if (!defined('REL_ARCH_PATH')) define("REL_ARCH_PATH", $_ENV['REL_ARCH_PATH']);
if (!defined('PACK_ARCH_PATH')) define("PACK_ARCH_PATH", $_ENV['PACK_ARCH_PATH']);
if (!defined('REL_PACK_ARCH_PATH')) define("REL_PACK_ARCH_PATH", $_ENV['REL_PACK_ARCH_PATH']);
//if (!defined('PACK_FACTORYPATH')) define("PACK_FACTORYPATH", RESSOURCE_PATH.'factory/');

#if (!defined('LIBRARY_PATH')) define('LIBRARY_PATH', $_ENV['LIBRARY_PATH']);
if (!defined('PUB_PATH')) define("PUB_PATH", $_ENV['PUB_PATH']);
if (!defined('LIB_PATH')) define("LIB_PATH", $_ENV['LIB_PATH']);
if (!defined('PROTO_PATH')) define("PROTO_PATH", $_ENV['PROTO_PATH']);
if (!defined('LOG_PATH')) define("LOG_PATH", $_ENV['LOG_PATH']);

//if (!defined('PACK_SAVE_FACTORYPATH')) define("PACK_SAVE_FACTORYPATH", ARCH_PATH.'factory/');
if (!defined('deviceType')) define('deviceType', array(10 => 'RSHOCK/', 11 => 'CRYOBACK4/', 12 => 'BACK4/', 13 => 'BIOBACK/', 14 => 'BACK3/'));
if (!defined('DEVICE_TYPE_ARRAY')) define('DEVICE_TYPE_ARRAY', array(10 => 'RSHOCK/', 11 => 'CRYOBACK4/', 12 => 'BACK4/', 13 => 'BIOBACK/', 14 => 'BACK3/'));
if (!defined('deviceTypeName')) define('deviceTypeName', array(10 => 'RSHOCK', 11 => 'CRYOBACK4', 12 => 'BACK4', 13 => 'BIOBACK', 14 => 'BACK3'));
if (!defined('deviceTypeArray')) define('deviceTypeArray', array(10 => 'RSHOCK/', 11 => 'CRYOBACK4/', 12 => 'BACK4/', 13 => 'BIOBACK/', 14 => 'BACK3/'));
if (!defined('deviceTypeId')) define('deviceTypeId', array(10 => 1, 12 => 3, 11 => 4, 14 => 5, 13 => 6)); //Ids corresponding to Ids automatically created in SQLdb
if (!defined('deviceIdType')) define('deviceIdType', array(1 => 10, 3 => 12, 4 => 11, 5 => 14, 6 => 13));
if (!defined('deviceId')) define('deviceId', array('RSHOCK/' => 10, 'CRYOBACK4/' => 11, 'BACK4/' => 12 , 'BIOBACK/' => 13, 'BACK3/' => 14));

/*
 *      DEFINE File Info
*/

if (!defined('FW_OCTETS')) define("FW_OCTETS", 256);
if (!defined('stFILENAME')) define("stFILENAME", "WLE256");
if (!defined('extFILENAME')) define("extFILENAME", ".bin");
#define('PACK_ARCH_PATH', ARCH_PATH."package/");



/* ================================ */
/* ================ DB Info ================ */
/* ================================ */

/* Define DB Info */
if (!defined('DB')) define('DB', $_ENV['DB']);
if (!defined('ADMIN')) define('ADMIN', $_ENV['ADMIN']);
if (!defined('PWD')) define("PWD", $_ENV['PWD']);
if (!defined('HOSTNAME')) define('HOSTNAME', $_ENV['HOSTNAME']);
if (!defined('DB_PORT')) define('DB_PORT', $_ENV['DB_PORT']);
/*
if (!defined('DB')) define('DB', 'winback_test');
if (!defined('ADMIN')) define('ADMIN', "root");
if (!defined('PWD')) define("PWD", "");
if (!defined('HOSTNAME')) define('HOSTNAME', 'localhost');
*/
/*
define('DB', 'winback');
define('ADMIN', "root");
define("PWD", "root");
define('HOSTNAME', 'localhost:8889');
*/

//if($isFile = file_exists('../Ressource/Config/ptfConfig.php')){

/*
  if(file_exists('../Ressource/Config/ptfConfig.php')){    
    include_once '../Ressource/Config/ptfConfig.php';
        define('SERVER_HOSTNAME', PTF_ADDR);
        define('SERVER_PORT', PTF_PORT);
}
*/
/*
else {
//if(!$isFile){
    define('SERVER_HOSTNAME', $_SERVER['SERVER_ADDR']);
    define('SERVER_PORT', $_SERVER['SERVER_PORT']);    
}
*/


/* ================================ */
/* ================ PTF Info ================ */
/* ================================ */

//define("PTF_ADDR", '92.154.81.215');
//define("PTF_ADDR", '192.168.1.168');
//define("PTF_PORT", '8080');
if (!defined('PTF_ADDR')) define("PTF_ADDR", '51.91.18.215');
//define("PTF_PORT", '5910');
if (!defined('SERVER_HOSTNAME')) define('SERVER_HOSTNAME', PTF_ADDR);
//define('SERVER_PORT', PTF_PORT);

//define('ADDRESS', "192.168.1.193");
if (!defined('ADDRESS')) define('ADDRESS', "51.91.18.215");
//define('ADDRESS', "192.168.1.127"); // wb wifi IP Address used in socket config //win0C
//define('ADDRESS', "10.0.0.78"); // winback wifi //win0A
if (!defined('PORT')) define('PORT', $_ENV['PORT']);