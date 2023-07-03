<?php
//ini_set('memory_limit','16M');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Paris');

/*
 *  Header Byte Value  
*/
if (!defined('AA')) define('AA', 170);
/**
 * cmdByte values
 */
if (!defined('cmdByte')) define('cmdByte', array('FE' => 254, 'FD' => 253,'FC' => 252,'FA' => 250,'F9' => 249,'F8' => 248,'F7' => 247,'F6' => 246,'F5' => 245,'F4' => 244,'F3' => 243,'F2' => 242,'DE' => 222,'DD' => 221,'DC' => 220,'DB' => 219,'DA' => 218,'D9' => 217,'D8' => 216,'CF' => 207,'CE' => 206, 'CD' => 205, 'CC' => 204, 'CB' => 203));
if (!defined('cmdSoft')) define('cmdSoft', array('FE', 'FD', 'FC', 'FA', 'F9', 'F8', 'F7', 'F6', 'F5', 'F3', 'F2','DE','DD','DC','DB','DA','D9','D8','CF','CE', 'CD', 'CC', 'CB'));
if (!defined('cmdBack')) define('cmdBack', array('DE','DD','DC','DB','DA','D9','D8','CF','CE', 'CD', 'CC', 'CB'));

/* Define PATH */

if (!defined('LOGS_FILE')) define("LOGS_FILE", 'wintra.log');
if (!defined('GATELOG_FILE')) define("GATELOG_FILE", 'serverGate.log');

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

if (!defined('ADDRESS_ARRAY')) define('ADDRESS_ARRAY', array($_ENV['ADDRESS']));
if (!defined('PORT_ARRAY')) define('PORT_ARRAY', array($_ENV['PORT'], $_ENV['SERVER_SECURE_PORT']));