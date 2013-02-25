<?php
/******************************
 * $File:login.php
 * $Description: 管理后台登陆操作
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

if (isset($_POST['username'])){
	if (!IsExiest($_POST['username'])){
		$login_msg = $MsgInfo["users_username_empty"];
	}else{
		if (!isset($_POST['valicode']) || ($_POST['valicode']=="" || $_POST['valicode']!=$_SESSION['valicode'])){
			$login_msg = $MsgInfo["users_valicode_error"];
		}else{
			
			//用户登录
			$data['username'] = $_POST['username'];
			$data['password'] = $_POST['password'];
			$result = $users->AdminLogin($data);
			if (!is_array($result)){
				$login_msg = $MsgInfo[$result];
			}else{
				$data['user_id'] = $result['user_id'];
				$data['cookie_id'] = "dwcms_admin_userid";
				$data['cookie_status'] = 1;
                $data["time"] = 60*60;
				SetCookies($data);
				
				if (isset($_SESSION['referer_url']) && $_SESSION['referer_url']!=""){
					$referer_url = $_SESSION['referer_url'];
					$_SESSION['referer_url'] = "";
					header("location:".$referer_url);
				}else{
					header("location:".$_A['admin_url']);
				}
			}
		}
	}
}
$magic->assign("login_msg",$login_msg);
$template = "admin_login.html";
?>