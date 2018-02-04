<?php
if(ini_get('register_globals')) exit('Error: register_globals is on!');
if(!defined('CAN_INCLUDE')) exit('Error: Direct access denied!');

if(!isset($_SESSION['admin'])) {
	header('Location: login.php');
	exit;
}

?>