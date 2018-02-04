<?php
if(ini_get('register_globals')) exit('Error: register_globals is on!');
if(!defined('CAN_INCLUDE')) exit('Error: Direct access denied!');

error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'writable/error_log.txt');

define('ROOT', str_replace('/include', '', str_replace('\\', '/', __DIR__)).'/');

$req_time=time();

$quota_file_sep=' , ';

?>