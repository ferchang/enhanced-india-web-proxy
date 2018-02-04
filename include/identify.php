<?php
if(ini_get('register_globals')) exit('Error: register_globals is on!');
if(!defined('CAN_INCLUDE')) exit('Error: Direct access denied!');

$user=$_GET['user'];
$key=$_GET['key'];

$auth_err_msg=false;
if(!in_array($user, array_keys($users), true) or $key!==$users[$user]['key']) $auth_err_msg='auth error!';
else if(!$users[$user]['active']) $auth_err_msg='user account not active!';

if($auth_err_msg) {
	$line=file_get_contents("php://input");
	$encryptEnable=substr($line,0,1);
	my_exit($auth_err_msg);
}

$up_quota=$users[$user]['quota']['up'];
$down_quota=$users[$user]['quota']['down'];
$total_quota=$users[$user]['quota']['total'];
$duration=$users[$user]['quota']['duration'];
$user_file="writable/$user.quota";

?>