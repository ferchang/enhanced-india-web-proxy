<?php
if(ini_get('register_globals')) exit('Error: register_globals is on!');
define('CAN_INCLUDE', true);

require './include/config-common.php';

require ROOT.'include/config-admin.php';

require ROOT.'include/admin_auth.php';

if(!empty($_POST)) require ROOT.'include/prevent_xsrf.php';

if(isset($_POST['password'])) {
	require ROOT.'include/class_bcrypt.php';	
	$bcrypt=new Bcrypt($admin_password_hash_rounds);
	require ROOT.'writable/password.php';
	if($bcrypt->verify($_POST['password'], $hash)) {
		$hash=$bcrypt->hash($_POST['new_pass']);
		$output="<?php\nif(ini_get('register_globals')) exit('Error: register_globals is on!');\nif(!defined('CAN_INCLUDE')) exit('Error: Direct access denied!');\n\n\$hash='$hash';\n\n?>";
		file_put_contents(ROOT.'writable/password.php', $output);
		$succ_msg='password change successful';
	}
	else $err_msg='password incorrect!';
}

function fill($name, $default='') {
	if(isset($_POST[$name]) and !isset($_POST['reset'])) echo $_POST[$name];
	else echo $default;
}

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
<script src='jquery.js'></script>
<script>
function validate() {
	if($("input[name=new_pass]").val()!==$("input[name=new_pass2]").val()) {
		alert('new password fields doesn\'t match!');
		return false;
	}
	return true;
}
</script>
</head>
<body>
<?php require ROOT.'include/page_center_start.php'; ?>

<h4>change admin password</h4>

<?php
if(isset($err_msg)) echo "<div style='color: red' align=center>$err_msg</div><br>";
?>
<?php
if(isset($succ_msg)) echo "<div style='color: green' align=center>$succ_msg</div><br>";
?>

<form action='' method=post>
<div align=right>
current password: <input type=password name=password size=15 ><br><br>
<?php
echo '<input type="hidden" name="antixsrf_token" value="';
echo ANTIXSRF_TOKEN4POST;
echo '">';
?>
new password: <input type=password name=new_pass size=15 value='<?php ?>'><br>
new password: <input type=password name=new_pass2 size=15 value='<?php ?>'><br><br>
</div>
<input type=submit value=submit onclick='return validate();'></form>
</tr></table>
<?php require ROOT.'include/home_link.php'; ?>

