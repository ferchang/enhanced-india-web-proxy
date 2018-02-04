<?php
if(ini_get('register_globals')) exit('Error: register_globals is on!');
if(!defined('CAN_INCLUDE')) exit('Error: Direct access denied!');

ini_set('display_errors', '0');

set_time_limit(60*5);

// Should be same as defined in java constant file.
// should be between 1-50
$encKey =20;

?>