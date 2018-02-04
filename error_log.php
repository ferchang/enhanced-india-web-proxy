<?php
if(ini_get('register_globals')) exit('Error: register_globals is on!');
define('CAN_INCLUDE', true);

require './include/config-common.php';

require ROOT.'include/config-admin.php';

require ROOT.'include/admin_auth.php';

if(!empty($_POST)) require ROOT.'include/prevent_xsrf.php';

$error_log_file=ROOT.'writable/error_log.txt';

if(isset($_POST['clear'])) file_put_contents($error_log_file, '', LOCK_EX);

$logs=file_get_contents($error_log_file);

$tmp="$error_log_file.h";

$current_hash='';

if($logs!=='') {
	$current_hash=substr(hash('sha256', $logs), 0, 32);
	if(file_exists($tmp)) $last_hash=file_get_contents($tmp);
	else $last_hash='';
	if($current_hash!==$last_hash) $new=true;
	file_put_contents($tmp, $current_hash);
}

file_put_contents($tmp, $current_hash);

?>
<html>
<head>
<style>
body {
	color: #fff;
	background: #555;
}
textarea {
	display: block;
	width: 100%;
	height: 88%;
	margin-bottom: 10px;
	background: #aaa;
	<?php
	if(isset($new)) echo "border: medium solid #f00;\n";
	else echo "border: thin solid #000;\n";
	echo "color: #000;\n";
	if($logs==='') echo "text-align: center;\n"; 
	?>
	padding: 5px;
	
}
a {
	background: #aaa;
	padding: 3px
}
#error {
	color: red;
	background: #000;
	padding: 3px;
	display: inline;
}
</style>
<script>
function reload() {
	target=location.pathname+'?';
	target+=(new Date().getTime());
	location.href=target;
}
</script>
</head>
<body>
<textarea readonly>
<?php
if($logs==='') echo "\n\nError log file is empty.";
else echo $logs;
?>
</textarea>
<form action="" method="post">
<?php
echo '<input type="hidden" name="antixsrf_token" value="';
echo ANTIXSRF_TOKEN4POST;
echo '">';
?>
<center>
<a href="index.php" style='background: #fff; padding: 5px; border: thin solid #000'>Home</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=submit value='Reload' onclick='reload(); return false;'>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=submit name=clear value='Clear error log' >
</center>
</form>
</body>
</html>
<?php
