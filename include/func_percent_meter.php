<?php
if(ini_get('register_globals')) exit("<center><h3>Error: Turn that damned register globals off!</h3></center>");
if(!defined('CAN_INCLUDE')) exit("<center><h3>Error: Direct access denied!</h3></center>");

function meter_percent_out($percent, $is_duration=false) {
	if($is_duration and $percent>=100) {
		echo '<b style="color: green; ">reset</b>';
		return;
	}
	echo $percent, '%';
	if($percent<=25) $class='meter_low';
	else if ($percent<=50) $class='meter_medium';
	else if ($percent<=75) $class='meter_considerable';
	else $class='meter_high';
	echo "&nbsp;<span class=$class>&nbsp;&nbsp;</span>";
}

function percent_meter($user, $user_file_info, $item) {
	$ceil=$GLOBALS['users'][$user]['quota'][$item];
	
	if(!$user_file_info) {
		echo 'n/a';
		return;
	}
	
	list($start, $up, $down, $t)=$user_file_info;
	$total=$up+$down;
	
	switch($item) {
		case 'up':
			if($ceil===0) echo friendly_size($up, true);
			else meter_percent_out((int)(number_format($up/$ceil, 2)*100));
		break;
		case 'down':
			if($ceil===0) echo friendly_size($down, true);
			else echo meter_percent_out((int)(number_format($down/$ceil, 2)*100));
		break;
		case 'total':
			if($ceil===0) echo friendly_size($total, true);
			else echo meter_percent_out((int)(number_format($total/$ceil, 2)*100));
		break;
		case 'duration':
			$elapsed=time()-$start;
			if($ceil===0) echo duration2friendly_str($elapsed, 2, true, true);
			else echo meter_percent_out((int)(number_format($elapsed/$ceil, 2)*100), true);
		break;
	}
	
}

?>
