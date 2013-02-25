<?
/******************************
 * $File: linkages.php
 * $Description: 联动管理
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$_A['list_purview']["linkages"]["name"] = "联动管理";
$_A['list_purview']["linkages"]["result"]["linkages_list"] = array("name"=>"联动管理","url"=>"code/linkages/list");

require_once("linkages.class.php");

check_rank("linkages_list");//检查权限
/**
 * 如果类型为空的话则显示所有的文件列表
**/
if ($_A['query_type'] == "list"){
	
}

/**
 * 添加
**/
elseif ($_A['query_type'] == "new"){

	if (isset($_POST['name'])){
		$var = array("name","value","type_id","order");
		$data = post_var($var);
		if($data['value']==""){
			$data['value'] = $data['name'];
		}
		$result = linkagesClass::Add($data);
		if ($result>0){
			$msg = array($MsgInfo["linkages_add_success"],"","{$_A['query_url']}/new&code={$_REQUEST['code']}&id={$_REQUEST['id']}");
		}else{
			$msg = array($MsgInfo[$result]);
		}
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "linkages";
		$admin_log["type"] = "action";
		$admin_log["operating"] = "add";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] = $data;
		usersClass::AddAdminLog($admin_log);
			
	}else{
		$data['limit'] = "all";
		$data['id'] = $_REQUEST['id'];
		$_A['linkage_type_result'] =linkagesClass::GetType($data);
		if (is_array($_A['linkage_type_result'])){
			$data['type_id'] = $_REQUEST['id'];
			$_A['linkage_list'] = linkagesClass::GetList($data);
		}else{
			$msg = array($result);
		}
	}
}



/**
 * 排序
**/
elseif ($_A['query_type'] == "actions"){
	if (isset($_POST['id'])){
		$data['id'] = $_POST['id'];
		$data['name'] = $_POST['name'];
		$data['value'] = $_POST['value'];
		$data['order'] = $_POST['order'];
		$result = linkagesClass::Action($data);
		if ($result !== true){
			$msg = array($MsgInfo[$result]);
		}else{
			$msg = array($MsgInfo["linkages_action_success"],"","{$_A['query_url']}/new&code={$_REQUEST['code']}&id={$_REQUEST['type_id']}");
		}
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "linkages";
		$admin_log["type"] = "action";
		$admin_log["operating"] = "updates";
		$admin_log["article_id"] = 0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}else{
		//添加
		if (isset($_POST['name'])){
			$data['type'] = "add";
			$data['name'] = $_POST['name'];
			$data['type_id'] = $_POST['type_id'];
			$data['value'] = $_POST['value'];
			$data['order'] = $_POST['order'];
			$result = linkagesClass::Action($data);
			if ($result !== true){
				$msg = array($MsgInfo[$result]);
			}else{
				$msg = array($MsgInfo["linkages_action_success"],"","{$_A['query_url']}/new&code={$_REQUEST['code']}&id={$_REQUEST['type_id']}");
			}
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "linkages";
			$admin_log["type"] = "action";
			$admin_log["operating"] = "adds";
			$admin_log["article_id"] = 0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] = $data;
			usersClass::AddAdminLog($admin_log);
		}
	}
}

/**
 * 删除
**/
elseif ($_A['query_type'] == "del"){
	$id = $_REQUEST['id'];
	$type_id = $_REQUEST['type_id'];
	$data = array("id"=>$id);
	$result = linkagesClass::Delete($data);
	
	if ($result >0){
		$msg = array($MsgInfo["linkages_del_success"],"","{$_A['query_url']}/new&id={$type_id}");
	}else{
		$msg = array($MsgInfo[$result]);
	}
	//加入管理员操作记录
	$admin_log["user_id"] = $_G['user_id'];
	$admin_log["code"] = "linkages";
	$admin_log["type"] = "action";
	$admin_log["operating"] = "del";
	$admin_log["article_id"] = $result>0?$result:0;
	$admin_log["result"] = $result>0?1:0;
	$admin_log["content"] =  $msg[0];
	$admin_log["data"] = $data;
	usersClass::AddAdminLog($admin_log);
}

/**
 * 链接类型
**/
elseif ($_A['query_type'] == "type_new" || $_A['query_type'] == "type_edit"){
	if (isset($_POST['name'])){
		$var = array("name","nid","order","class_id");
		$data = post_var($var);
		if ($_A['query_type'] == "type_new"){
			$result = linkagesClass::AddType($data);
			if ($result>0){
				$msg = array($MsgInfo['linkages_type_add_success'],"","{$_A['query_url']}/list&code={$_REQUEST['code']}");
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}else{
			$data['id'] = $_POST['id'];
			$result = linkagesClass::UpdateType($data);
			if ($result >0){
				$msg = array($MsgInfo['linkages_type_update_success'],"","{$_A['query_url']}/list&code={$_REQUEST['code']}");
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "linkages";
		$admin_log["type"] = "action";
		$admin_log["operating"] = $_A['query_type'];
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}elseif( $_A['query_type'] == "type_edit"){
		$data['id'] = $_REQUEST['id'];
		$_A['linkage_type_result'] = linkagesClass::GetType($data);
	}
}

/**
 * 删除
**/
elseif ($_A['query_type'] == "type_del"){
	$data["id"] =  $_REQUEST['id'];
	$result = linkagesClass::DelType(array("id"=>$data["id"]));
	if ($result > 0){
		$msg = array($MsgInfo['linkages_type_del_success'],"","{$_A['query_url']}/list&code={$_REQUEST['code']}");
	}else{
		$msg = array($MsgInfo[$result],"","{$_A['query_url']}/list&code={$_REQUEST['code']}");
	}
	//加入管理员操作记录
	$admin_log["user_id"] = $_G['user_id'];
	$admin_log["code"] = "linkages";
	$admin_log["type"] = "action";
	$admin_log["operating"] = $_A['query_type'];
	$admin_log["article_id"] = $_REQUEST['id'];
	$admin_log["result"] = $result>0?1:0;
	$admin_log["content"] =  $msg[0];
	$admin_log["data"] =  $data;
	usersClass::AddAdminLog($admin_log);
}


/**
 * 类型排序
**/
elseif ($_A['query_type'] == "type_action"){
	if (isset($_POST['id'])){
		$data['id'] = $_POST['id'];
		$data['name'] = $_POST['name'];
		$data['nid'] = isset($_POST['nid'])?$_POST['nid']:"";
		$data['order'] = $_POST['order'];
		$result = linkagesClass::ActionType($data);
		if ($result !== true){
			$msg = array($MsgInfo[$result]);
		}else{
			$msg = array($MsgInfo["linkages_type_update_success"],"","{$_A['query_url']}/list&code={$_REQUEST['code']}");
		}
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "linkages";
		$admin_log["type"] = "action";
		$admin_log["operating"] = "type_action";
		$admin_log["article_id"] = 0;
		$admin_log["result"] = 1;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}
	
}


elseif ($_A['query_type'] == "class" ){
	if (isset($_POST['name'])){
			$var = array("name","nid","remark","order");
			$data = post_var($var);
			if ($_POST['id']!=""){
				$data['id'] = $_POST['id'];
				$result = linkagesClass::UpdateLinkagesClass($data);
				if ($result>0){
					$msg = array($MsgInfo["linkages_class_update_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "update";
			}else{
				$result = linkagesClass::AddLinkagesClass($data);
				if ($result>0){
					$msg = array($MsgInfo["linkages_class_add_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "add";
			}
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "linkages";
			$admin_log["type"] = "class";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = linkagesClass::GetLinkagesClassOne($data);
		if (is_array($result)){
			$_A["linkages_class_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	
	}elseif($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$result = linkagesClass::DelLinkagesClass($data);
		if ($result>0){
			$msg = array($MsgInfo["linkages_class_del_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "linkages";
		$admin_log["type"] = "class";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}
	
}
?>