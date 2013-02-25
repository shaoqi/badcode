<?php
/******************************
 * $File: active.php
 * $Description: 激活
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$id = urldecode($_REQUEST['id']);
$_id = explode(",",authcode(trim($id),"DECODE"));
$data['user_id'] = $_id[0];
$valid_time = $_id[1];
//判断激活的时间是否过期
if ($valid_time+60*60<time()){
	if ($_REQUEST['type']=="success"){
		$msg = array($MsgInfo['users_active_pass'],"","/renzheng/index.html?type=email");
	}else{
		$msg = array($MsgInfo['users_active_pass'],"","/?user");
	}
}else{
	$result = usersClass::ActiveUsersEmail(array("user_id"=>$data['user_id']));
	if ($_REQUEST['type']=="success"){
		$msg = array($MsgInfo[$result],"","/renzheng/index.html?type=realname");
	}else{
		$msg = array($MsgInfo[$result],"","/?user");
	}
}