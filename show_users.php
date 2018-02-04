<?php
if(ini_get('register_globals')) exit('Error: register_globals is on!');
define('CAN_INCLUDE', true);

require './include/config-common.php';

require ROOT.'include/config-admin.php';

require ROOT.'include/admin_auth.php';

require ROOT.'writable/users.php';

require ROOT.'include/func_friendly_size.php';

require ROOT.'include/func_duration2friendly_str.php';

require ROOT.'include/func_percent_meter.php';

function output_edit_user_btns($user) {
	echo '<td style="border: none">';
	echo "<input type=submit value=del name='$user{$GLOBALS['post_field_sep']}del' onclick='return confirm(\"delete $user?\")'>";
	echo "&nbsp;<input type=submit value=edit name='$user{$GLOBALS['post_field_sep']}edit'>";
	echo '</td>';
}

?>

<html>
<head>
<style>
body {
	background: #378;
}
#users {
	border: 3px solid #000;
	padding: 10px;
	background: rgb(214,196,238);
	
}
#users td {
	border: thin solid #000;
	padding: 3px;
	text-align: center;
}
.key {
	text-align: center;
	border: none;
	background: rgb(214,196,238);
}
.proxy_addr {
	border: thin solid #000;
	background: rgb(230,220,260);
}
#users .spacer_row td {
	border: none;
}
#users .consumption_row td {
	border: none;
}
#users .consumption_row .meter {
	border: thin solid #000;
}
.meter_low {
	background: #0f0;
	border: thin solid #000;
}
.meter_medium {
	background: #00f;
	border: thin solid #000;
}
.meter_considerable {
	background: yellow;
	border: thin solid #000;
}
.meter_high {
	background: red;
	border: thin solid #000;
}
</style>
<script>
function toggle_key(t, v) {
	if(t.value!=v) {
		t.value=v;
		//t.select();
	}
	else t.value='click to show/hide';
}
</script>
</head>
<body>
<?php
require ROOT.'include/page_center_start.php';

if(empty($users)) echo '<h2>no users found</h2>';
else echo '<table id=users>';

echo '<form action=edit_user.php method=post>';

echo '<input type="hidden" name="antixsrf_token" value="';
echo ANTIXSRF_TOKEN4POST;
echo '">';

$c=1;
foreach($users as $key=>$userInfo) {
	
	echo '<tr><th>no</th><th>name</th><th>active</th><th>last activity</th><th>key</th><th>logging</th><th>upload</th><th>download</th><th>total</th><th>duration</th></tr>';
	
	$user_file="writable/$key.quota";
	if(!file_exists($user_file)) $user_file_info=false;
	else {
		$fp=fopen($user_file, 'r');
		flock($fp, LOCK_SH);
		$user_file_info=explode(' , ', fread($fp, 999999));
		flock($fp, LOCK_UN);
		fclose($fp);
	}
	
	echo '<tr>';
	echo "<td>$c</td>";
	$c++;
	if(file_exists("writable/$key.log")) echo "<td><a href='user_logs.php?user=$key' title='user logs'>$key</a></td>";
	else echo "<td>$key</td>";
	echo "<td>";
	if($userInfo['active']) echo '<span style="color: green">yes</span>';
	else echo '<span style="color: red">no</span>';
	echo "</td>";
	echo '<td>';
	if($user_file_info) echo duration2friendly_str(time()-$user_file_info[3], 2, true, true), ' ago';
	else echo 'n/a';
	echo '</td>';
	echo "<td><input class=key type=text onclick='toggle_key(this, \"{$userInfo['key']}\");' value='click to show/hide' size=27></span></td>";
	
	echo '<td>';
	$out='';
	if(in_array('ip', $users[$key]['logging'])) $out='ip';
	if(in_array('host', $users[$key]['logging'])) $out.=(($out)? '+':'').'host';
	if(in_array('time', $users[$key]['logging'])) $out.=(($out)? '+':'').'time';
	echo ($out)? $out:'none';
	echo '</td>';
	
	echo '<td>';
	echo friendly_size($userInfo['quota']['up']);
	echo '</td>';
	echo '<td>';
	echo friendly_size($userInfo['quota']['down']);
	echo '</td>';
	echo '<td>';
	echo friendly_size($userInfo['quota']['total']);
	echo '</td>';
	echo '<td>';
	if($userInfo['quota']['duration']===0) echo '&infin;';
	else echo duration2friendly_str($userInfo['quota']['duration'], 2, true, true);
	echo '</td>';
	output_edit_user_btns($key);
	echo '</tr>';
	
	//-----------
	
	echo '<tr class=consumption_row >';
	echo "<td></td>";
	echo "<td></td>";
	echo "<td><input value=y/n name='$key{$post_field_sep}active' type=submit></td>";
	echo '<td></td>';
	
	$proto='http'.((isset($_SERVER['HTTPS']) and strtolower($_SERVER['HTTPS'])!=='off') ? 's' : ''); $host=$_SERVER['HTTP_HOST']; $port=(($_SERVER['SERVER_PORT'] === '80' || $_SERVER['SERVER_PORT'] === '443') ? "" : ":" . $_SERVER['SERVER_PORT']); $self=$_SERVER['PHP_SELF'];
	$self=preg_replace('/[a-z_]+\.php/i', 'prx.php', $self);
	$proxy_addr="$proto://$host$port$self?user=$key&key={$userInfo['key']}";
	
	echo "<td><input readonly type=text value='$proxy_addr' onclick='this.select()' class=proxy_addr></td>";
	echo "<td style='text-align: right'>consumed:</td>";
	echo '<td class=meter>';
	percent_meter($key, $user_file_info, 'up');
	echo '</td>';
	echo '<td  class=meter>';
	percent_meter($key, $user_file_info, 'down');
	echo '</td>';
	echo '<td class=meter>';
	percent_meter($key, $user_file_info, 'total');
	echo '</td>';
	echo '<td class=meter>';
	percent_meter($key, $user_file_info, 'duration');
	echo '</td>';
	echo "<td align=center><input value='reset quotas' name='$key{$post_field_sep}reset' type=submit></td>";
	echo '</tr>';
	
	echo '<tr class=spacer_row><td colspan=11>&nbsp;</td></tr>';
	
}

echo "</tr></table><br><div align=center><input type=submit value='add user' name='{$post_field_sep}add'></div></form>";

require ROOT.'include/home_link.php';

?>


