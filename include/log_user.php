<?php
if(ini_get('register_globals')) exit('Error: register_globals is on!');
if(!defined('CAN_INCLUDE')) exit('Error: Direct access denied!');

if(empty($users[$user]['logging'])) return;

$considered_new_time=10;

if(empty($users[$user]['logging'])) return;

$user_log_file="writable/$user.log";

if(file_exists($user_log_file)) {
	$fp=fopen($user_log_file, 'r+');
	flock($fp, LOCK_EX);
	fseek($fp, -200, SEEK_END);
	$contents=fread($fp, 999);
	$p=strrpos($contents, "\n", -2);
	list($rip, $rhost, $rtime)=explode('#', trim(substr($contents, $p)));
}
else {
	$fp=fopen($user_log_file, 'w');
	flock($fp, LOCK_EX);
	ftruncate($fp, 0);
	//fseek($fp, 0);
	$rip=$rhost=$rtime=false;
}

$rip2=$_SERVER['REMOTE_ADDR'];
$rhost2=trim($host).':'.trim($port);
$rtime2=$req_time;

$conf_ip=in_array('ip', $users[$user]['logging'], true);
$conf_host=in_array('host', $users[$user]['logging'], true);
$conf_time=in_array('time', $users[$user]['logging'], true);
$considered_new=($rtime2-$rtime>=$considered_new_time);

$log_ip=$log_host=$log_time=false;
if($conf_ip and ($rip2!==$rip or $considered_new)) $log_ip=true;
if($conf_host and ($rhost2!==$rhost or $considered_new)) $log_host=true;
if($conf_time and ($considered_new)) $log_time=true;

if($log_ip or $log_host or $log_time) {
	$out='';
	if($conf_ip) $out.=$rip2;
	$out.='#';
	if($conf_host) $out.=$rhost2;
	$out.='#';
	if($conf_time) $out.=$rtime2;
	$out.="\n";
	//fseek($fp, 0, SEEK_END);
	fwrite($fp, $out);
}

fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

?>