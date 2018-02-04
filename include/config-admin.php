<?php
if(ini_get('register_globals')) exit('Error: register_globals is on!');
if(!defined('CAN_INCLUDE')) exit('Error: Direct access denied!');

ini_set('display_errors', '1');

set_time_limit(30);

session_start();

require ROOT.'include/func_random.php';

$post_field_sep=':::';

//-----------------------------

if(!isset($_SESSION['antixsrf_token4post'])) $_SESSION['antixsrf_token4post']=random_string(22);

if(!isset($_SESSION['antixsrf_token4get'])) $_SESSION['antixsrf_token4get']=random_string(22);

define('ANTIXSRF_TOKEN4POST', $_SESSION['antixsrf_token4post']);

define('ANTIXSRF_TOKEN4GET', $_SESSION['antixsrf_token4get']);

//-----------------------------

?>