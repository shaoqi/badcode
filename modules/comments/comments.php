<?php
/******************************
 * $File: comments.php
 * $Description: 评论管理
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$_A['list_purview']["comments"]["name"] = "评论管理";
$_A['list_purview']["comments"]["result"]["comments_list"] = array("name"=>"评论管理","url"=>"code/comments/list");

require_once("comments.class.php");


/**
 * 如果类型为空的话则显示所有的文件列表
**/
/**
 * 学历
**/

if ($_A['query_type'] == "list" ){
	if (isset($_POST['type']) && $_POST['type']!=""){
		$var = array("type","id");
		$_data = post_var($var);
		
		$_result = commentsClass::ActionComments($_data);
		if ($_result>0){
			$msg = array($MsgInfo['comments_action_success'],"",$_A['query_url']);
		}else{
			$msg = array($MsgInfo[$_result]);
		}	
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "comments";
		$admin_log["type"] = "comment";
		$admin_log["operating"] = $_POST['type'];
		$admin_log["article_id"] = $_result>0?$_result:0;
		$admin_log["result"] = $_result>0?1:0;
		$admin_log["content"] =  $_msg[0];
		$admin_log["data"] =  $_data;
		usersClass::AddAdminLog($admin_log);
	}elseif (isset($_POST['contents'])){
		$msg = check_valicode();
		if ($msg==""){
			$var = array("contents","user_id","status");
			$data = post_var($var);
			if ($_POST['id']!=""){
				$data['id'] = $_POST['id'];
				$result = commentsClass::UpdateComments($data);
				if ($result>0){
					$msg = array($MsgInfo["comments_update_success"],"",$_A['query_url_all']);
					if (IsExiest($_POST['comments'])!=false){
						$_result = commentsClass::GetCommentsOne($data);
						$_data['user_id'] = $_G['user_id'];
						$_data['code'] = $_result['code'];
						$_data['type'] = $_result['type'];
						$_data['article_id'] = $_result['article_id'];
						$_data['status'] = 1;
						if ($_result['pid']>0){
							$_data['pid'] = $_result['pid'];
						}else{
							$_data['pid'] = 1;
						}
						$_data['reply_id'] = $_result['id'];
						$_data['contents'] = $_POST['comments'];
						$_result = commentsClass::AddComments($_data);
						if ($_result>0){
							$_msg = $result['comments_add_success'];
						}else{
							$_msg = array($MsgInfo[$_result]);
						}
						
						//加入管理员操作记录
						$admin_log["user_id"] = $_G['user_id'];
						$admin_log["code"] = "comments";
						$admin_log["type"] = "comment";
						$admin_log["operating"] = "add";
						$admin_log["article_id"] = $_result>0?$_result:0;
						$admin_log["result"] = $_result>0?1:0;
						$admin_log["content"] =  $_msg[0];
						$admin_log["data"] =  $_data;
						usersClass::AddAdminLog($admin_log);
					}
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "update";
			}
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "comments";
			$admin_log["type"] = "comment";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = commentsClass::GetCommentsOne($data);
		if (is_array($result)){
			$_A["comments_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	
	}elseif($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$result = commentsClass::DelComments($data);
		if ($result>0){
			$msg = array($MsgInfo["comments_del_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "comments";
		$admin_log["type"] = "comment";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}
	
}


elseif ($_A['query_type'] == "set" ){
	//系统参数
	if (isset($_POST['con_comments_status'])){
		$var = array("con_comments_status","con_comments_check_status","con_comments_time","con_comments_keywords","con_comments_users");
		$data = post_var($var);
		$data['code'] = "comments";
		$result = adminClass::UpdateSystem($data);
		$msg = array($MsgInfo["admin_info_success"]);
	
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "system";
		$admin_log["type"] = "comments";
		$admin_log["operating"] = "comment";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
		
	}
}

?>