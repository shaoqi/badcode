<?
/******************************
 * $File: users.return.php
 * $Description: 处理用户数据
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问
require_once("users.class.php");

if ($_REQUEST['p']=="getone"){
	if ($_POST['user_id']=="") return "";
	$result = usersClass::GetUsersInfo(array("user_id"=>$_POST['user_id']));
	if ($result==false){
		echo "";
	}else{
		require_once(ROOT_PATH."/plugins/magic/modifier.avatar.php");
		if ($result['sex']=="女"){
			$result['_sex'] = "girl";
		}else{
			$result['_sex'] = "boy";
		}
		$result['user_avatar'] = magic_modifier_avatar($result['user_id'],"");
		$result['username'] = urlencode(iconv("GB2312","UTF-8",$result['username']));
		$result['sex'] =urlencode(iconv("GB2312","UTF-8",$result['sex']));
		$result['intro'] =  urlencode(iconv("GB2312","UTF-8",$result['intro']));
		$result['share_num'] = 0;
		$sql = "select count(1) as num from `{share}` where user_id='{$_G['user_id']}'";
		$_result = $mysql->db_fetch_array($sql);
		if ($_result!=false){
			$result['share_num'] = $_result['num'];
		}
		echo json_encode($result);
	
	}
	exit;
}



?>