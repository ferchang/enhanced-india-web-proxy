<?php
if(ini_get('register_globals')) exit('Error: register_globals is on!');
define('CAN_INCLUDE', true);

require './include/config-common.php';

require ROOT.'include/config-admin.php';

require ROOT.'include/admin_auth.php';

//$big_log_rows_threshold=300;

if(!empty($_POST)) require ROOT.'include/prevent_xsrf.php';

require ROOT.'include/func_friendly_size.php';
require ROOT.'include/func_duration2friendly_str.php';

if(isset($_GET['user'])) $user2=$_GET['user'];

if(!empty($_POST)) foreach($_POST as $k=>$v) {
		if(strpos($k, $post_field_sep)!==false) {
			list($user, $action)=explode($post_field_sep, $k);
			break;
		}
}
else $action='';

if($action==='del') {
	unlink("writable/$user.log");
	header('Location: user_logs.php');
	exit;
}

$logs=glob('writable/*.log');

?>
<html>
<head>
<style>
body {
	background: #378;
}
#logs {
	border: 3px solid #000;
	padding: 10px;
	background: rgb(214,196,238);
	
}
#logs td {
	border: thin solid #000;
	padding: 3px;
	text-align: center;
}

#user_log {
	border: 3px solid #000;
	padding: 10px;
	background: rgb(214,196,238);
	
}
#user_log td {
	border: thin solid #000;
	padding: 3px;
	text-align: center;
}
</style>
</head>
<body>
<?php
require ROOT.'include/page_center_start.php';

if(isset($user2)) {
	$contents=file_get_contents("writable/$user2.log");
	$lines=explode("\n", $contents);
	echo '<table id=user_log>';
	echo '<form action="" method=post>';
	echo '<input type="hidden" name="antixsrf_token" value="';
	echo ANTIXSRF_TOKEN4POST;
	echo '">';
	echo '<tr><th>no</th><th>user ip</th><th>target host</th><th>time</th></tr>';
	foreach($lines as $c=>$line) {
		if($line==='') continue;
		echo '<tr><td>', $c+1, '</td>';
		list($ip, $host, $time)=explode('#', $line);
		if($ip==='') $ip='n/a';
		if($host==='') $host='n/a';
		if($time==='') $time='n/a';
		else $time=duration2friendly_str(time()-$time, 2, true).'  ago';
		echo "<td>$ip</td><td>$host</td><td>$time</td>";
	}
	echo '</tr></table>';
	echo "<br><center><input type='submit' value='delete $user2 logs' name='$user2{$GLOBALS['post_field_sep']}del' onclick='return confirm(\"delete $user2 logs?\")'></center>";
}
else {

	if(empty($logs)) echo '<h2>no user logs found</h2>';
	else {
		echo '<table id=logs>';

		echo '<form action="" method=post>';

		echo '<input type="hidden" name="antixsrf_token" value="';
		echo ANTIXSRF_TOKEN4POST;
		echo '">';

		echo '<tr><th>user</th><th>size</th>';

		$c=1;
		foreach($logs as $log) {
			$user=explode('.', explode('/', $log)[1])[0];
			$size=filesize($log);
			echo '<tr>';
			echo "<td><a href='user_logs.php?user=$user'>$user</a></td>";
			echo '<td>', friendly_size($size, true),'</td>';
			echo "<td style='border: none'><input type='submit' value=del name='$user{$GLOBALS['post_field_sep']}del' onclick='return confirm(\"delete $user logs?\")'></td>";
			echo '<tr style="height: 10px"></tr>';
		}
	}

}

echo "</tr></table></form>";

require ROOT.'include/home_link.php';

?>