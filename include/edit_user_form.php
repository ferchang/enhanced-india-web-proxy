<?php
if(ini_get('register_globals')) exit('Error: register_globals is on!');
if(!defined('CAN_INCLUDE')) exit('Error: Direct access denied!');

$action=(isset($_GET['action']))? $_GET['action']:false;
$user=(isset($_GET['user']))? $_GET['user']:false;

function fill($name, $default='') {
	if(isset($_POST[$name]) and !isset($_POST['reset'])) echo $_POST[$name];
	else echo $default;
}

function fill_chbox($name, $default=false) {
	if(!empty($_POST) and !isset($_POST['reset'])) {
		if(isset($_POST[$name])) echo 'checked';
	}
	else if($default) echo 'checked';
}

function fill_select($name, $val, $df) {
	echo " value='$val' ";
	if(!empty($_POST) and !isset($_POST['reset'])) {
		if($_POST[$name]===$val) echo 'selected';
	}
	else if($val===$df) echo 'selected';
}

function duration2fields($t) {
	
	$y=$mth=$d=$h=$m=$s=0;
	
	$y=(int)floor($t/(365*24*60*60));
	if($y) $t=$t%(365*24*60*60);
	$mth=(int)floor($t/(30*24*60*60));
	if($mth) $t=$t%(30*24*60*60);
	$d=(int)floor($t/(24*60*60));
	if($d) $t=$t%(24*60*60);
	$h=(int)floor($t/(60*60));
	if($h) $t=$t%(60*60);
	$m=(int)floor($t/60);
	$s=$t%60;
	
	return array('y'=>$y, 'mth'=>$mth, 'd'=>$d, 'h'=>$h, 'm'=>$m, 's'=>$s);
	
}

if($user) $duration_fields=duration2fields($users[$user]['quota']['duration']);

?>

<html>
<head>
<style>
body {
	background: #378;
}

form {
	background: rgb(214,196,238);
	padding: 10px;
	
}
#warn_span {
	color: yellow;
	background: #555;
	border: thin solid #000;
	padding: 1px;
	margin: 2px;
}
.key {
	display: none;
}
select, option {
	text-align: center;
}
</style>
<script src='jquery.js'></script>
<script>
function toggle_key() {
 $("input[name=key]").toggle();
}
function validate() {
	if($("input[name=user]").val()=='') {
		alert('error: username is empty!')
		return false;
	}
	return true;
}
</script>
<script>
function no_quota_duration() {
	$("input[name=quota_years]").val(0);
	$("input[name=quota_months]").val(0);
	$("input[name=quota_days]").val(0);
	$("input[name=quota_hours]").val(0);
	$("input[name=quota_mins]").val(0);
	$("input[name=quota_secs]").val(0);
}

function no_quota_limit() {
	$("input[name=up]").val(0);
	$("input[name=down]").val(0);
	$("input[name=total]").val(0);
}

function onload() {
	$("input[type=text]").focus(function(e) { e.target.select(); });
}
</script>
</head>
<body onload='onload();'>
<?php
require ROOT.'include/page_center_start.php';
if($user) echo "&nbsp;<big>Editing account: <span style='color: yellow'>$user</span></big>";
else echo "&nbsp;<big>Add user</big>";
?>
<table><tr><td valign=center>
<form action='' method=post>
<?php
echo '<input type="hidden" name="antixsrf_token" value="';
echo ANTIXSRF_TOKEN4POST;
echo '">';
?>
<?php
if(isset($err_msg)) echo "<div style='color: red; margin-bottom: 10px'>$err_msg</div>";
?>
username: <input type=text name=user size=10 value='<?php fill('user', $user); ?>'>
&nbsp;&nbsp;active:<input type=checkbox name=active <?php fill_chbox('active', ($user)? $users[$user]['active']:1); ?>>
&nbsp;&nbsp;auth key: <input class=key type=text value='<?php fill('key', ($user)? $users[$user]['key']:random_string(22)); ?>' size=27 name=key>
<input type=button value="show/hide key" onclick="toggle_key()">
&nbsp;&nbsp;logging:
<?php
$df=null;
if($user) {
	if(in_array('ip', $users[$user]['logging'], true)) $df='ip';
	if(in_array('host', $users[$user]['logging'], true)) $df.=(($df)? '+':'').'host';
	if(in_array('time', $users[$user]['logging'], true)) $df.=(($df)? '+':'').'time';
}
?>
<select name=logging>
<option <?php fill_select('logging', 'none', $df); ?>>none
<option <?php fill_select('logging', 'ip', $df); ?>>ip
<option <?php fill_select('logging', 'host', $df); ?>>host
<option <?php fill_select('logging', 'time', $df); ?>>time
<option <?php fill_select('logging', 'ip+host', $df); ?>>ip + host
<option <?php fill_select('logging', 'ip+time', $df); ?>>ip + time
<option <?php fill_select('logging', 'ip+host+time', $df); ?>>ip + host + time
<option <?php fill_select('logging', 'host+time', $df); ?>>host + time
</select>
<br>
<fieldset>
<legend>quota</legend>
upload (bytes): <input type=text name=up size=11 title='0 means unlimited' value='<?php fill('up', ($user)? $users[$user]['quota']['up']:0); ?>'>
download (bytes): <input type=text name=down size=11 title='0 means unlimited' value='<?php fill('down', ($user)? $users[$user]['quota']['down']:0); ?>'>
total (bytes): <input type=text name=total size=11 title='0 means unlimited' value='<?php fill('total', ($user)? $users[$user]['quota']['total']:0); ?>'>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=button value='no limit' onclick='no_quota_limit();' title='all fields set to 0 means no limitation'><br>
<fieldset>
<legend>duration</legend>
years: <input type=text name=quota_years size=3 value='<?php fill('quota_years', ($user)? $duration_fields["y"]:0); ?>'>
months: <input type=text name=quota_months size=3 value='<?php fill('quota_months', ($user)? $duration_fields["mth"]:0); ?>'>
days: <input type=text name=quota_days size=3 value='<?php fill('quota_days', ($user)? $duration_fields["d"]:0); ?>'>
hours: <input type=text name=quota_hours size=3 value='<?php fill('quota_hours', ($user)? $duration_fields["h"]:0); ?>'>
minutes: <input type=text name=quota_mins size=3 value='<?php fill('quota_mins', ($user)? $duration_fields["m"]:0); ?>'>
seconds: <input type=text name=quota_secs size=3 value='<?php echo fill('quota_secs', ($user)? $duration_fields["s"]:0); ?>'>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=button value='no duration' onclick='no_quota_duration();' title='all fields set to 0 means no duration is set'>
</fieldset>
</fieldset>
<div align=center style="padding-top: 10px;"><input type=submit value='reset' name=reset>&nbsp;&nbsp;<input type=submit value='save' name=save onclick='return validate()'></div>
</form>
<center><span style='text-align: center; background: #fff; padding: 3px'><a href=show_users.php>show users</a></span></center>
<?php require ROOT.'include/page_center_end.php'; ?>
</body>
</html>
