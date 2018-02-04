<?php
if(ini_get('register_globals')) exit('Error: register_globals is on!');
if(!defined('CAN_INCLUDE')) exit('Error: Direct access denied!');

require ROOT.'include/func_write_file_with_lock.php';

function my_exit($msg) {
	my_echo($msg);
	exit();
}

function my_echo($msg) {
	global $encryptEnable;
	if($encryptEnable==="Y") echo encrypt_string($msg);
	else echo $msg;
}

function remaining($v1, $v2) {
	$v1=$v1-$v2;
	if($v1<0) $v1=0;
	return $v1;
}

function next_quota_msg($duration) {
	$out="next quota available in: ";
	if(!$duration) $out.='never';
	else {
		if(!function_exists('duration2friendly_str')) require ROOT.'include/func_duration2friendly_str.php';
		$duration_remain=remaining($duration, time()-$GLOBALS['start']);
		$out.=duration2friendly_str($duration_remain);
	}
	return $out;
}

//===================================================================================

// Sample encrypt.Keeping the ouput size same.
function encrypt_string($input) {
	global $encKey;
	$line="";
	for($i=0;$i<strlen($input);$i++) $line .= chr(ord($input[$i])+$encKey);
    return $line;   
}
  
// Sample decrypt.Keeping the ouput size same.
function deccrypt_string($input) {   
	global $encKey; 
	$line="";
	for($i=0;$i<strlen($input);$i++) $line .= chr(ord($input[$i])-$encKey);
    return $line;
}

?>