<?php
if(ini_get('register_globals')) exit('Error: register_globals is on!');
define('CAN_INCLUDE', true);

require './include/config-common.php';
require ROOT.'include/config-proxy.php';
require ROOT.'writable/users.php';
require ROOT.'include/helper_funcs.php';
require ROOT.'include/identify.php';
require ROOT.'include/parse_input.php';
require ROOT.'include/log_user.php';

if(file_exists($user_file)) {
	$fp=fopen($user_file, 'r+');
	flock($fp, LOCK_EX);
	//fseek($fp, 0);
	list($start, $up, $down, $t)=explode($quota_file_sep, fread($fp, 999999));
	if($duration and $req_time-$start>=$duration) {
		$start=$req_time;
		$down=0;
	} else $up_size=$up_size+(int)$up;
	ftruncate($fp, 0);
	fseek($fp, 0);
	fwrite($fp, "$start{$quota_file_sep}$up_size{$quota_file_sep}$down{$quota_file_sep}$req_time");
	fflush($fp);
	flock($fp, LOCK_UN);
	fclose($fp);
}
else {
	$start=$req_time;
	$up=$up_size;
	$down=0;
	write_file_with_lock($user_file, "$start{$quota_file_sep}$up_size{$quota_file_sep}$down{$quota_file_sep}$req_time");
}

$msg='';
if($total_quota and $down+$up_size>=$total_quota) $msg.="total quota ($total_quota bytes) exceeded!\n";
if($down_quota and $down>=$down_quota) $msg.="download quota ($down_quota bytes) exceeded!\n";
if($up_quota and $up_size>$up_quota) $msg.="upload quota ($up_quota bytes) exceeded!\n";
if($msg) my_exit($msg.next_quota_msg($duration));

$out_size=0;

$fsok=fsockopen(trim($host), intval(trim($port))); 
if($fsok!==false) {
	fwrite($fsok, $bodyData ); 
	$port ='';$host ='';$hostport= '';$bodyData='';

	$total_remain=remaining($total_quota, $down+$up_size);
	$down_remain=remaining($down_quota, $down);

	if($total_quota and $down_quota) $quota_remain=min($down_remain, $total_remain);
	else if($total_quota) $quota_remain=$total_remain;
	else if($down_quota) $quota_remain=$down_remain;

	while($line=fread($fsok, 25000)) {
		if(($total_quota or $down_quota) and $out_size+strlen($line)>=$quota_remain) {
			$out_remain=remaining($quota_remain, $out_size);
			$line=substr($line, 0, $out_remain);
		}
		if(!strlen($line)) break;
		$out_size+=strlen($line);
		my_echo($line);
	}
	fclose($fsok);
}
else my_echo('Target Host not Found/Down');

@ob_flush();
flush();
	
$down_size=$down+$out_size;

write_file_with_lock($user_file, "$start{$quota_file_sep}$up_size{$quota_file_sep}$down_size{$quota_file_sep}$req_time");

?>