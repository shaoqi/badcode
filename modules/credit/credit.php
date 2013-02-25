<?
/******************************
 * $File: credit.php
 * $Description: 积分后台管理文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$_A['list_purview']["credit"]["name"] = "积分管理";
$_A['list_purview']["credit"]["result"]["credit_list"] = array("name"=>"用户积分","url"=>"code/credit/list");
$_A['list_purview']["credit"]["result"]["credit_log"] = array("name"=>"积分记录","url"=>"code/credit/log");
$_A['list_purview']["credit"]["result"]["credit_rank"] = array("name"=>"积分等级","url"=>"code/credit/rank");
$_A['list_purview']["credit"]["result"]["credit_type"] = array("name"=>"积分类型","url"=>"code/credit/type");
$_A['list_purview']["credit"]["result"]["credit_class"] = array("name"=>"积分分类","url"=>"code/credit/class");
	
require_once("credit.class.php");

if ($_A['query_type'] == "list"){
check_rank("credit_list");//检查权限

}
elseif ($_A['query_type'] == "log"){
	check_rank("credit_log");//检查权限
	if($_REQUEST['examine']!=""){
		if ($_POST['credit']!=""){
			$var = array("credit","user_id");
			$data = post_var($var);
			$data['id'] = $_REQUEST['examine'];
			$result = creditClass::UpdateCredit($data);
			
			if ($result>0){
				$msg = array("操作成功","",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "credit";
			$admin_log["type"] = "credit";
			$admin_log["operating"] = "update";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}else{
			$data['id'] = $_REQUEST['examine'];
			$result = creditClass::GetLogOne($data);
			if (is_array($result)){
				$_A["credit_result"] = $result;
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}
	}
}



/**
 * 积分分类
**/

elseif ($_A['query_type'] == "class" ){
	check_rank("credit_class");//检查权限
	if (isset($_POST['name'])){
	
		$var = array("name","nid");
		$data = post_var($var);
		if ($_POST['id']!=""){
			$data['id'] = $_POST['id'];
			$result = creditClass::UpdateClass($data);
			if ($result>0){
				$msg = array($MsgInfo["credit_class_update_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			$admin_log["operating"] = "update";
		}else{
			$result = creditClass::AddClass($data);
			if ($result>0){
				$msg = array($MsgInfo["credit_class_add_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			$admin_log["operating"] = "add";
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "credit";
		$admin_log["type"] = "class";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
		
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = creditClass::GetClassOne($data);
		if (is_array($result)){
			$_A["credit_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	
	}elseif($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$result = creditClass::DeleteClass($data);
		if ($result>0){
			$msg = array($MsgInfo["credit_class_del_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "credit";
		$admin_log["type"] = "class";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}
}



/**
 * 积分类型
**/

elseif ($_A['query_type'] == "type" ){
	check_rank("credit_type");//检查权限
	if (isset($_POST['name'])){
	
		//$var = array("nid","name","credit_type","value","value_scale","cycle","code","class_id","award_times","status","interval","remark");
		$var = array("nid","name","value","cycle","code","class_id","award_times","status","interval","remark");
		$data = post_var($var);
		if ($_POST['id']!=""){
			$data['id'] = $_POST['id'];
			$result = creditClass::UpdateType($data);
			if ($result>0){
				$msg = array($MsgInfo["credit_type_update_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			$admin_log["operating"] = "update";
		}else{
			$result = creditClass::AddType($data);
			if ($result>0){
				$msg = array($MsgInfo["credit_type_add_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			$admin_log["operating"] = "add";
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "credit";
		$admin_log["type"] = "type";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = creditClass::GetTypeOne($data);
		if (is_array($result)){
			$_A["credit_type_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	
	}elseif($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$result = creditClass::DeleteType($data);
		if ($result>0){
			$msg = array($MsgInfo["credit_type_del_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "credit";
		$admin_log["type"] = "type";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}
}


/**
 * 积分类型
**/

elseif ($_A['query_type'] == "rank" ){
	check_rank("credit_rank");//检查权限
	if (isset($_POST['name'])){
			$var = array("nid","name","rank","point1","point2","pic","remark","class_id","fee");
			$data = post_var($var);
			if ($_POST['id']!=""){
				$data['id'] = $_POST['id'];
				$result = creditClass::UpdateRank($data);
				if ($result>0){
					$msg = array($MsgInfo["credit_rank_update_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "update";
			}else{
				$result = creditClass::AddRank($data);
				if ($result>0){
					$msg = array($MsgInfo["credit_rank_add_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "add";
			}
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "credit";
			$admin_log["type"] = "rank";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = creditClass::GetRankOne($data);
		if (is_array($result)){
			$_A["credit_rank_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	
	}elseif($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$result = creditClass::DeleteRank($data);
		if ($result>0){
			$msg = array($MsgInfo["credit_rank_del_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "credit";
		$admin_log["type"] = "rank";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}
}

?>