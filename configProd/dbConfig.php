<?php
/* Define DB table */

if (!defined('USER_TABLE')) define('USER_TABLE', 'user');
if (!defined('DEVICE_TABLE')) define('DEVICE_TABLE', 'device');
if (!defined('DEVICE_FAMILY_TABLE')) define('DEVICE_FAMILY_TABLE', 'device_family');
if (!defined('SOFTWARE_TABLE')) define('SOFTWARE_TABLE', 'software');
if (!defined('TREATMENT_TABLE')) define('TREATMENT_TABLE', 'treatment');
if (!defined('SN_TABLE')) define('SN_TABLE', 'sn');

/* Define Device Table properties */

if (!defined('DEVICE_TYPE')) define('DEVICE_TYPE', 'device_family_id');
//if (!defined('DEVICE_TYPE')) define('DEVICE_TYPE', 'device_family');
if (!defined('SN')) define('SN', 'sn');
if (!defined('DEVICE_VERSION')) define('DEVICE_VERSION', 'version');
if (!defined('VERSION_UPLOAD')) define('VERSION_UPLOAD', 'version_upload');
if (!defined('FORCED_UPDATE')) define('FORCED_UPDATE', 'forced');
define('IP_ADDR', 'ip_addr');
define('LOG_POINTEUR', 'log_pointeur');
//define('RQ_SERVER', 'rqServer');
define('PUB_ACCEPTED', 'pub');
define('PIN_CODE', 'code_pin');
define('SELECTED', 'selected');
define('CREATED_AT', 'created_at');
define('UPDATED_AT', 'updated_at');
//define('IS_ACTIVE', 'is_active');
define('LOG_FILE', 'log_file');
define('SERVER_DATE', 'server_date');
define('IS_CONNECT', 'is_active');
define('CONNECTED', 'connected');
define('DOWNLOAD', 'download');
define('UPDATE_COMMENT', 'update_comment');
/* Define User Table properties */

define('LOGIN', 'username');

/* Define Software Table properties */
define('NAME', 'name');
define('FAMILY_TYPE', 'device_family_id');
//define('FAMILY_TYPE', 'device_family');
define("SOFT_VERSION", "version");
define("SOFT_CREATED_AT", "created_at");

/* Define Device Type properties */
define('ID', 'id');
define('NUMBER_ID', 'number_id');

/* Define SN Table properties */
if (!defined('SN_ID')) define('SN_ID', 'SN');
if (!defined('SN_DEVICE')) define('SN_DEVICE', 'Device');
if (!defined('SN_DATE')) define('SN_DATE', 'Date');