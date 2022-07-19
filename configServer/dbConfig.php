<?php
/* Define DB table */

define('USER_TABLE', 'user');
define('DEVICE_TABLE', 'device');
define('DEVICE_FAMILY_TABLE', 'device_family');
define('SOFTWARE_TABLE', 'software');
define('TREATMENT_TABLE', 'treatment');

/* Define Device Table properties */

define('DEVICE_TYPE', 'device_family_id');
define('SN', 'sn');
define('DEVICE_VERSION', 'version');
define('VERSION_UPLOAD', 'version_upload');
define('FORCED_UPDATE', 'forced');
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
define('STATUS', 'status');
define('SERVER_DATE', 'server_date');
define('IS_CONNECT', 'is_active');
define('CONNECTED', 'connected');
/* Define User Table properties */

define('LOGIN', 'username');

/* Define Software Table properties */
define('NAME', 'name');
define('FAMILY_TYPE', 'device_family_id');
define("SOFT_VERSION", "version");
define("SOFT_CREATED_AT", "created_at");

/* Define Device Type properties */
define('ID', 'id');
define('NUMBER_ID', 'number_id');