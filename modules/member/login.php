<?
/******************************
 * $File: login.php
 * $Description: 登录
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$_U['username_cookie'] = $_COOKIE['username_cookie'];

if (isset($_POST['password'])){
	$login_msg = "";
	$_url = '/?user';
	$_url_login = '/?user&q=login';
	$_u_time = 60*60*$_POST['cooktime'];	
	setcookie("username_cookie",$_POST['keywords'],time()+$_u_time);
	
	//判断登录的基本信息
	if (isset($_POST['valicode']) && $_POST['valicode'] =="" ){
		$msg = array($MsgInfo["users_valicode_empty"],"",$_url_login);
	}elseif (isset($_POST['valicode']) && $_POST['valicode']!=$_SESSION['valicode']){
		$msg = array($MsgInfo["users_valicode_error"],"",$_url_login);
	}elseif ($_POST['keywords']==""){
		$msg = array($MsgInfo["users_keywords_empty"],"",$_url_login);
	}elseif ($_POST['password']==""){
		$msg = array($MsgInfo["users_password_empty"],"",$_url_login);
	}else{
		
		//用户登录
		if(!isset($data['user_id']) || $data['user_id']==""){
			$data['user_id'] = $_POST['keywords'];
		}
		$data['email'] =$_POST['keywords'];
		$data['username'] = $_POST['keywords'];
		$data['password'] = $_POST['password'];
		$result = usersClass::Login($data);
		
		if ($result>0){
			//加入cookie
			$data['user_id'] = $result;
			
			if ($_POST['cookie_status']==1){
				$data['cookie_status'] = 1;
				$data['dy_cookie_status'] = 1;
				$data['time'] = 7*60*60*24;
			}else{
				$data['cookie_status'] = $_G['system']['con_cookie_status'];
				$data['dy_cookie_status'] = 0;
			}
			
			//ucenter登录
			$ucenter_login = "";
			if ($_G['module']['ucenter_status']==1){
				$user_result = usersClass::GetUsers(array("user_id"=>$data['user_id']));
				$_data['username'] = $data['username'];
				$_data['password'] = $data['password'];
				$_data['user_id'] = $data['user_id'];
				$_data['email'] = $user_result['email'];
				$ucenter_login = ucenterClass::UcenterLogin($_data);
				if ($ucenter_login==""){
					$msg = array("论坛同步失败，请跟管理员联系");
				}else{
					echo $ucenter_login;
					SetCookies($data);
					$_SESSION['dw_username'] = $_data['username'];
					$msg = array($MsgInfo["users_login_success"],"",$_url);
				}
			}else{
				SetCookies($data);
				if ($_POST['ajax']==1){
					echo 'ok';
					exit;
				}
				header("Location: /?user"); 
				//echo "<script>location.href='/?user';</script>";
				//$msg = array($MsgInfo["users_login_success"],"",$_url);
			}
			
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		
	}
	
	if ($_POST['ajax']==1){
		echo $msg[0];
		exit;
	}
}
if ($_REQUEST['type']=="ajax"){
	$template = 'user_login_ajax.html';
}else{
	$template = 'user_login.html';
}
?>