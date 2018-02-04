<?php
if(ini_get('register_globals')) exit('Error: register_globals is on!');
define('CAN_INCLUDE', true);

require './include/config-common.php';

require ROOT.'include/config-admin.php';

if(!empty($_POST)) require ROOT.'include/prevent_xsrf.php';

if(isset($_POST['destroy'])) {
	session_destroy();
	header("Location: {$_SERVER['PHP_SELF']}");
	exit;
}

require ROOT.'include/page_center_start.php';
echo '<pre style="border: thin solid #000; padding: 5px">';
print_r($_SESSION);
echo '</pre><br><center><form style="margin-bottom: 0px" method="post" action="">';

echo '<input type="hidden" name="antixsrf_token" value="';
echo ANTIXSRF_TOKEN4POST;
echo '">';

echo '<input type="submit" name="destroy" value="Destroy session"></form></center>';

require ROOT.'include/home_link.php';

?>