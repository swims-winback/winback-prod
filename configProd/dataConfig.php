<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* ROLES */

define("TEAM", array(7 => "User",
                     4 => "R&D",
                     5 => "ADV",
                     6 => "SAV"));

/* User Config */

if(isset($_SESSION['isMobile']) && $_SESSION['isMobile'] === false){
    define("RD_MENU", array('Welcome', 'Software', 'Device', 'Acces', 'Activities','SN bdd'));
    define("ADV_MENU", array('Welcome', 'Acces', 'Activities'));
    define("SAV_MENU", array('Welcome', 'Device', 'Acces', 'Activities'));
    define("USER_MENU", array('Welcome'));
}else{
    define("RD_MENU", array('Welcome', 'Device', 'Acces'));
    define("ADV_MENU", array('Welcome', 'Acces'));
    define("SAV_MENU", array('Welcome', 'Device', 'Acces'));
    define("USER_MENU", array('Welcome'));
}
define("MENU_LIST", array(7 => USER_MENU,
                          4 => RD_MENU,
                          5 => ADV_MENU,
                          6 => SAV_MENU));

/* Page Configuration */

define("INTRA_PAGE",array('Login'           => 'index.php',
                          'Welcome'         => 'welcome.php',
                          'Software'        => 'listSoft.php',
                          'Device'          => 'deviceInfo.php',
                          'Acces'           => 'acces.php',
                          'Activities'      => 'activities.php',
                          'SN bdd'      => 'SN_bdd.php'));

/* Define ERROR Message */

define("MSG_ERR", array('1'=>'Connection FAILED!',
                        '2' => 'test 2'));

/* Define Interaction Command with the device */
define("COMMAND_ARRAY", array('refresh',
                            'snDevice',
                        'snLargePad',
                        'snMousePad',
                        'snGunPad',
                        'date1Pad',
                        'date2Pad',
                        'date3Pad',
                        'lock_Pad',
                        'errorPad',
                        'autotestNTC',
                        'autotestTEC',
                        'reset',
                        'connectionTime',
                        'autoTest',
                        'devicePad4',
                        'restartDevice'));

define("COMMAND", array('touch' => 1,
                        'diag' => 2));

define("DATA_DEVICE",array('stimStatus' => array('on' => 29, 'off' => 29),
                            'stimHz' => array('low' => 3, 'normal' => 4, 'high' => 5),
                            'stimPulse' => array('short' => 6, 'normal' => 7, 'long' => 8),
                            'tecaStatus' => array('on' => 9, 'off' => 9),
                            'timerStatus' => array('Start' => 11, 'Stop' => 11, 'Pause' => 10)
                            ));

define("DATA_BX_LABEL", array(4 => "Menu",
                                6 => "Signal Hz",
                                8 => "Signal Pulse",
                                10 => "Intensity",
                                12 => "Time Min",
                                14 => "Time Sec",
                                16 => "State",
                                18 => "Valim",
                                20 => "Valim Dec",
                                22 => "V12v",
                                24 => "V12v Dec",
                                26 => "V3v3",
                                28 => "V3v3 Dec",
                                30 => "V3v3 Ref",
                                32 => "V3v3 Ref Dec",
                                34 => "VSys",
                                36 => "VSys Dec",
                                38 => "Current Sys",
                                40 => "Current Sys Dec",
                                42 => "Current Signal",
                                44 => "Current Signal Dec",
                                46 => "Temp Bridge",
                                48 => "Temp Bridge Dec",
                                50 => "Temp Transfo",
                                52 => "Temp Transfo Dec",
                                54 => "Boot Version",
                                56 => "Boot Revision",
                                58 => "Main Version",
                                60 => "Main Revision",
                                62 => "Battery Remaining",
                                64 => "Battery Discharging",
								66 => "",
                                68 => "",
                                70 => "",
                                72 => "",
                                74 => "",
								76 => "",
                                78 => "",
                                80 => "",
                                82 => "",
                                84 => "",
								86 => "",
                                88 => "",
                                90 => "",
                                92 => "",
                                94 => "",
								96 => "",
                                98 => "",
                                100 => "",
                                102 => "",
                                104 => "",
								106 => "",
                                108 => "",
                                110 => "",
                                112 => "",
                                114 => "",
								116 => "",
                                118 => "",
                                120 => "",
                                122 => "",
                                124 => "",
								126 => "",
                                128 => "",
                                130 => "",
                                132 => "",
                                134 => "",
								136 => "",
                                138 => "",
                                140 => "",
                                142 => "",
                                144 => "",
								146 => "",
                                148 => "",
                                150 => "",
                                152 => "",
                                154 => "",
								156 => "",
                                158 => "",
                                160 => "",
                                162 => "",
                                164 => "",
								166 => "",
                                168 => "",
                                170 => "",
                                172 => "",
                                174 => "",
								176 => "",
                                178 => "",
                                180 => "",
                                182 => "",
                                184 => "",
								186 => "",
                                188 => "",
                                190 => "",
                                192 => "",
                                194 => "",
								196 => "",
                                198 => ""
                              ));

define("DATA_BX_VALUE", array("Menu" => array(0=>'Principal', 1=>"Debug"),
                              "Signal Hz" => array(0=>'Off', 1=>"Low", 2 => "Normal", 4 => "High"),
                              "Signal Pulse" => array(0=>'Off', 1=>"Short", 2 => "Normal", 3 => "Long"),
                              "Intensity" => array('0', "1",'2', "3",'4', "5",'6', "7",'8', "9", "10"),
                              "Time Min" => NULL,
                              "Time Sec" => NULL,
                              "State" => array(0=>'Stop', 1=>"Ready", 2 =>"Pause"),
                              "Valim" => NULL,
                              "Valim Dec" => NULL,
                              "V12v" => NULL,
                              "V12v Dec" => NULL,
                              "V3v3" => NULL,
                              "V3v3 Dec" => NULL,
                              "V3v3 Ref" => NULL,
                              "V3v3 Ref Dec" => NULL,
                              "VSys" => NULL,
                              "VSys Dec" => NULL,
                              "Current Sys" => NULL,
                              "Current Sys Dec" => NULL,
                              "Current Signal" => NULL,
                              "Current Signal Dec" => NULL,
                              "Temp Bridge" => NULL,
                              "Temp Bridge Dec" => NULL,
                              "Temp Transfo" => NULL,
                              "Temp Transfo Dec" => NULL,
                              "Boot Version" => NULL,
                              "Boot Revision" => NULL,
                              "Main Version" => NULL,
                              "Main Revision" => NULL,
                              "Battery Remaining" => NULL,
                              "Battery Discharging" => array(0 => 'No', 1 => 'Yes')
                              ));

define("DATA_MESURE_PRE", array(29=>'v',
                                31=>'v'
                                ));
define("DATA_MESURE_SUF", array(18=>'V',
                                20=>'V',
                                22=>'V',
                                24=>'V',
                                26=>'V',
                                28=>'V',
                                30=>'V',
                                38=>'A',
                                42=>'A',
                                46=>'°C',
                                50=>'°C',
                                62=>'%'
                                ));
