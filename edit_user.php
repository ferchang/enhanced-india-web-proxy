<?php
if(ini_get('register_globals')) exit('Error: register_globals is on!');
define('CAN_INCLUDE', true);

require './include/config-common.php';

require ROOT.'include/config-admin.php';

require ROOT.'include/admin_auth.php';

if(!empty($_POST)) require ROOT.'include/prevent_xsrf.php';

require ROOT.'include/func_write_file_with_lock.php';

function gen_users_array_code($arr) {
	$out="<?php\nif(ini_get('register_globals')) exit('Error: register_globals is on!');\nif(!defined('CAN_INCLUDE')) exit('Error: Direct access denied!');\n\n";
	$out.='$users=array('."\n\n";
	foreach($arr as $key=>$val) {
		$out.="\t'$key'=>array(\n\n";
		$out.="\t\t'active'=>{$val['active']}, 'key'=>'{$val['key']}',\n\n";
		if(!empty($val['logging'])) $tmp="'".implode("', '", $val['logging'])."'";
		else $tmp='';
		$out.="\t\t'quota'=>array('up'=>{$val['quota']['up']}, 'down'=>{$val['quota']['down']}, 'total'=>{$val['quota']['total']}, 'duration'=>{$val['quota']['duration']}),\n\n\t\t'logging'=>array($tmp)\n\n\t),\n\n";
	}
	$out.=");\n\n?>";
	return $out;
}

if(isset($_GET['action'])) $action='adding';
if(isset($_GET['user'])) $action='editing';
if(!isset($action)) foreach($_POST as $k=>$v) {
		if(strpos($k, $post_field_sep)!==false) {
			list($user, $action)=explode($post_field_sep, $k);
			break;
		}
}

require ROOT.'writable/users.php';

$users_file="writable/users.php";

if($action==='del') {
	
	unset($users[$user]);
	
	$out=gen_users_array_code($users);
	
	write_file_with_lock($users_file, $out);
	if(file_exists("writable/$user.quota")) unlink("writable/$user.quota");
	if(file_exists("writable/$user.log")) unlink("writable/$user.log");
	
	header('Location: show_users.php');
	exit;
}

if($action==='active') {
	
	if($users[$user]['active']) $users[$user]['active']=0;
	else $users[$user]['active']=1;
	
	$out=gen_users_array_code($users);
	
	write_file_with_lock($users_file, $out);
	
	header('Location: show_users.php');
	exit;
	
}

if($action==='reset') {
	
	$user_file="writable/$user.quota";
	if(file_exists($user_file)) {
		$fp=fopen($user_file, 'r+');
		flock($fp, LOCK_EX);
		list($start, $up, $down, $t)=explode(' , ', fread($fp, 999999));;
		ftruncate($fp, 0);
		fseek($fp, 0);
		fwrite($fp, "$start , 0 , 0 , $t");
		flock($fp, LOCK_UN);
		fclose($fp);
	}
	
	//write_file_with_lock();
	
	header('Location: show_users.php');
	exit;
	
}

if($action==='edit') {
	header("Location: edit_user.php?user=$user");
	exit;
}

if($action==='add') {
	header("Location: edit_user.php?action=add");
	exit;
}

//-------------------------------------------

if($action==='editing' or $action==='adding') {
	
	if(isset($_POST['save'])) {
		
		if($action==='adding') {
			$arr=array();
			$arr['active']=(isset($_POST['active']))? 1:0;
			$arr['key']=$_POST['key'];
			$arr['quota']['up']=$_POST['up'];
			$arr['quota']['down']=$_POST['down'];
			$arr['quota']['total']=$_POST['total'];
			$duration=$_POST['quota_secs'];
			$duration+=$_POST['quota_mins']*60;
			$duration+=$_POST['quota_hours']*60*60;
			$duration+=$_POST['quota_days']*60*60*24;
			$duration+=$_POST['quota_months']*60*60*24*30;
			$duration+=$_POST['quota_years']*60*60*24*365;
			$arr['quota']['duration']=$duration;
			
			$arr['logging']=array();
			if($_POST['logging']!=='none') foreach(explode('+', $_POST['logging']) as $v) $arr['logging'][]=$v;
			
			$user=$_POST['user'];
			
			if(isset($users[$user])) {
				$err_msg="<span style='color: blue'>$user</span> already exists!";
				require ROOT.'include/edit_user_form.php';
				exit;
			}
			
			$users[$user]=$arr;
			
			$out=gen_users_array_code($users);
		
			write_file_with_lock($users_file, $out);
			
			if(file_exists("writable/$user.quota")) unlink("writable/$user.quota");
			if(file_exists("writable/$user.log")) unlink("writable/$user.log");
		
			header('Location: show_users.php');
			exit;
		
		}
		
		$arr=$users[$_GET['user']];
		$arr['active']=(isset($_POST['active']))? 1:0;
		$arr['key']=$_POST['key'];
		$arr['quota']['up']=$_POST['up'];
		$arr['quota']['down']=$_POST['down'];
		$arr['quota']['total']=$_POST['total'];
		
		$arr['logging']=array();
		if($_POST['logging']!=='none') foreach(explode('+', $_POST['logging']) as $v) $arr['logging'][]=$v;
		
		$duration=$_POST['quota_secs'];
		$duration+=$_POST['quota_mins']*60;
		$duration+=$_POST['quota_hours']*60*60;
		$duration+=$_POST['quota_days']*60*60*24;
		$duration+=$_POST['quota_months']*60*60*24*30;
		$duration+=$_POST['quota_years']*60*60*24*365;
		$arr['quota']['duration']=$duration;
		
		$pre_user=$_GET['user'];
		$new_user=$_POST['user'];
		
		if($new_user!==$pre_user) {
			if(isset($users[$new_user])) {
				$err_msg="<span style='color: blue'>$new_user</span> already exists!";
				require ROOT.'include/edit_user_form.php';
				exit;
			}
			if(file_exists("writable/$pre_user.quota")) rename("writable/$pre_user.quota", "writable/$new_user.quota");
			if(file_exists("writable/$pre_user.log")) rename("writable/$pre_user.log", "writable/$new_user.log");
		}
		
		unset($users[$_GET['user']]);
		
		$users[$_POST['user']]=$arr;
		
		$out=gen_users_array_code($users);
		
		write_file_with_lock($users_file, $out);
		
		header('Location: show_users.php');
		exit;

	}
	
	require ROOT.'include/edit_user_form.php';

}

?>