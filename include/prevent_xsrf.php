<?php
if(ini_get('register_globals')) exit('Error: register_globals is on!');
if(!defined('CAN_INCLUDE')) exit('Error: Direct access denied!');

if(!empty($_POST)) {
	if(!isset($_POST['antixsrf_token']) or ANTIXSRF_TOKEN4POST!==$_POST['antixsrf_token']) exit('XSRF prevention mechanism triggered!');
}
else if(!isset($_GET['antixsrf_token']) or ANTIXSRF_TOKEN4GET!==$_GET['antixsrf_token']) exit('XSRF prevention mechanism triggered!');

?>