<?php
/******************************
 * $File: module.php
 * $Description: 模块配置信息
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

check_rank("system_module");//检查权限

if($_A['query_type']=="update"){
	$data['nid'] = $_REQUEST['nid'];
	$result = adminClass::UpdateModuleSystem($data);
	$msg = array("系统模块更新成功","",$_A['query_url']);

}

elseif($_A['query_type']=="install" || $_A['query_type']=="edit"){
	if (IsExiest($_POST['nid'])!=false){
		$var = array("name","nickname","nid","date","status","order","default_field","description","index_tpl","list_tpl","content_tpl","version","author","type");
		$data = post_var($var);
		
		if ($_A['query_type'] == "edit"){
			$result = moduleClass::UpdateModule($data);;
			if ($result>0){
				$msg = array("模块修改成功","",$_A['query_url']);
			}else{
				$msg = array($MsgInfo[$result],"",$_A['query_url']);
			}
			$admin_log["operating"] = "update";
		}else{
			$data['version_new'] = $data['version'];
			$result = moduleClass::AddModule($data);
			if ($result>0){
				$msg = array("模块安装成功","",$_A['query_url']);
			}else{
				$msg = array($MsgInfo[$result],"",$_A['query_url']);
			}
			$admin_log["operating"] = "add";
		}
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "system";
		$admin_log["type"] = "module";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}else{
		$data['nid'] = $_REQUEST['nid'];
		$result = adminClass::GetModule($data);
		if (is_array($result)){
			$_A['module_result'] = $result;
		}else{
			$msg = array($MsgInfo[$result]);;
		}
	}

}

elseif($_A['query_type']=="delete"){
	$data['nid'] = $_REQUEST['nid'];
	$result = adminClass::DeleteModule($data);
	if ($result >0 ){
		$msg = array("系统模块已卸载","",$_A['query_url']);
	}else{
		$msg = array($MsgInfo[$result]);;
	}
}

elseif($_A['query_type']=="action"){
	$data['id'] = $_POST['id'];
	$data['order'] = $_POST['order'];
	$data['status'] = $_POST['status'];
	$result = adminClass::ActionModule($data);
	if ($result >0 ){
		$msg = array("修改成功","",$_A['query_url']);
	}else{
		$msg = array($MsgInfo[$result]);;
	}
}
$template = "admin_module.html";
?>