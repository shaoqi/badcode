<?
/******************************
 * $File: message.php
 * $Description: 短消息模块后台管理文件
 * $Author: ahui 
 * $Time:2010-08-09
 * $Update:None 
 * $UpdateDate:None 
 * Copyright(c) 2012 by dycms.net. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$_A['list_purview']["message"]["name"] = "短消息管理";
$_A['list_purview']["message"]["result"]["message_list"] = array("name"=>"短消息管理","url"=>"code/message/list");

require_once("message.class.php");

check_rank("message_list");//检查权限
$_A['message_type'] = $message_type;
/**
 * 
**/
if ($_A['query_type'] == "list"){

	if($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$data['type'] = $_REQUEST['type'];
		$result = messageClass::DeleteMessage($data);
		
		if ($result>0){
			$msg = array($MsgInfo["message_delete_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "message";
		$admin_log["type"] = $data['type'];
		$admin_log["operating"] = "delete";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  (join(",",$data));
		usersClass::AddAdminLog($admin_log);
	
	}
	elseif (isset($_POST['type'])){
		if ($_POST['type']=="delete"){
			$data['id'] = $_POST['id'];
			$result = messageClass::DeleteMessage($data);
			if ($result>0){
				$msg = array($MsgInfo["message_delete_success"],"",$_A['query_url']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
		
		}
	}
	elseif($_REQUEST['send']!=""){
		$data['send_id'] = $_REQUEST['send'];
		$data['send_status'] = 1;
		$result = messageClass::SendMessage($data);
		
		if ($result>0){
			$msg = array($MsgInfo["message_delete_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "message";
		$admin_log["type"] = $data['type'];
		$admin_log["operating"] = "delete";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  (join(",",$data));
		usersClass::AddAdminLog($admin_log);
	
	}
}

//短信发送记录
elseif ($_A['query_type'] == "new"){

	if ($_POST['status']!=""){
		$var = array("receive_admin_type","receive_user_type","receive_users","type","status","name","contents");
		$data = post_var($var);		
		$data['user_id'] = 0;
		$result = messageClass::AddMessage($data);
		if ($result>0){
			$msg = array($MsgInfo["message_send_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "message";
		$admin_log["type"] = $data['type'];
		$admin_log["operating"] = "add";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  (join(",",$data));
		usersClass::AddAdminLog($admin_log);
	}
}

elseif ($_A['query_type'] == "receive"){
	if($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$data['type'] = $_REQUEST['type'];
		$result = messageClass::DeleteMessageReceive($data);
		
		if ($result>0){
			$msg = array($MsgInfo["message_receive_delete_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "message";
		$admin_log["type"] = "receive";
		$admin_log["operating"] = "delete";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  (join(",",$data));
		usersClass::AddAdminLog($admin_log);
	
	}elseif (isset($_POST['type'])){
		if ($_POST['type']=="deled"){
			$data['id'] = $_POST['id'];
			$result = messageClass::DeleteMessageReceive($data);
			if ($result>0){
				$msg = array($MsgInfo["message_delete_success"],"",$_A['query_url']."/receive");
			}else{
				$msg = array($MsgInfo[$result]);
			}
		
		}
	}
}
?>