<?php
if(ini_get('register_globals')) exit('Error: register_globals is on!');
define('CAN_INCLUDE', true);

require './include/config-common.php';

require ROOT.'include/config-admin.php';

if(!empty($_POST)) require ROOT.'include/prevent_xsrf.php';

if(isset($_POST['password'])) {
	require ROOT.'include/class_bcrypt.php';	
	$bcrypt=new Bcrypt(12);
	$password_file=ROOT.'writable/password.php';
	$lock_file=ROOT.'writable/lock';
	if(file_exists($password_file)) {
		if(file_exists($lock_file)) exit('delete the lock file! (it is in the writable directory)');
		require ROOT.'writable/password.php';
		if($bcrypt->verify($_POST['password'], $hash)) {
			$_SESSION['admin']=true;
			header('Location: index.php');
			exit;
		}
		else $err_msg='password incorrect!';
	}
	else {
		touch($lock_file);
		$hash=$bcrypt->hash($_POST['password']);
		$output="<?php\nif(ini_get('register_globals')) exit('Error: register_globals is on!');\nif(!defined('CAN_INCLUDE')) exit('Error: Direct access denied!');\n\n\$hash='$hash';\n\n?>";
		file_put_contents($password_file, $output);
		exit('first time...<br><br>password file created<br><br>now delete the lock file ( it is in the writable directory) before <a href="">login</a>');
	}
	
}

?>
<html>
<head>
<style>
body {
	background: #378;
}
#inner {
	background: rgb(214,196,238);
	padding: 5px;
	border: 3px solid #000;
}
form {
	margin: 0px;
}
</style>
<script src='jquery.js'></script>
</head>
<body onload='$("input[name=password]").focus();'>
<?php require ROOT.'include/page_center_start.php'; ?>
<?php
if(isset($err_msg)) echo "<div style='color: red' align=center>$err_msg</div>";
?>
<form action='' method=post>admin password: <input type=password name=password size=15 >
<?php
echo '<input type="hidden" name="antixsrf_token" value="';
echo ANTIXSRF_TOKEN4POST;
echo '">';
?>
<input type=submit value=login></form>
<?php require ROOT.'include/page_center_end.php'; ?>
</body>
</html>