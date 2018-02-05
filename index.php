<?php
if(ini_get('register_globals')) exit('Error: register_globals is on!');
define('CAN_INCLUDE', true);

require './include/config-common.php';

require ROOT.'include/config-admin.php';

require ROOT.'include/admin_auth.php';

$error_log_file=ROOT.'writable/error_log.txt';

$logs=file_get_contents($error_log_file);

if($logs!=='') {
	$current_hash=substr(hash('sha256', $logs), 0, 32);
	$tmp="$error_log_file.h";
	if(file_exists($tmp)) $last_hash=file_get_contents($tmp);
	else $last_hash='';
	if($current_hash!==$last_hash) $new=true;
}

require ROOT.'writable/users.php';
require ROOT.'include/func_friendly_size.php';

$error_log_size=filesize('writable/error_log.txt');
$users_count=count($users);
$user_logs_count=count(glob('writable/*.log'));

?>
<html>
<body>
<head>
<style>
body {
	background: #378;
}
#inner {
	background: rgb(214,196,238);
	padding: 5px;
	text-align: center;
	border: 3px solid #000;
	
}
</style>
</head>
<body>
<?php require ROOT.'include/page_center_start.php'; ?>
<a href=error_log.php>Error log</a>
<?php
if(isset($new)) echo '<span style="color: red;">*</span>';
?>
 (<?php friendly_size($error_log_size, true); ?>)
<br><br>
<a href=show_users.php>Users</a> (<?php echo $users_count; ?>)<br><br>
<a href=user_logs.php>User logs</a> (<?php echo $user_logs_count; ?>)<br><br>
<a href=change_password.php>Change password</a><br><br>
<a href=session.php>View session</a>
<?php require ROOT.'include/page_center_end.php'; ?>
</body>
</html>