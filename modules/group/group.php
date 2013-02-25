<?
/******************************
 * $File: group.php
 * $Description: 联动管理
 * $Author: ahui 
 * $Time:2010-08-09
 * $Update:None 
 * $UpdateDate:None 
 * Copyright(c) 2012 by dycms.net. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$_A['list_purview']["group"]["name"] = "圈子管理";
$_A['list_purview']["group"]["result"]["group_list"] = array("name"=>"圈子管理","url"=>"code/group/list");
$_A['list_purview']["group"]["result"]["group_type"] = array("name"=>"圈子类型","url"=>"code/group/type");
$_A['list_purview']["group"]["result"]["group_member"] = array("name"=>"圈子成员","url"=>"code/group/member");
$_A['list_purview']["group"]["result"]["group_articles"] = array("name"=>"圈子帖子","url"=>"code/group/articles");
$_A['list_purview']["group"]["result"]["group_comments"] = array("name"=>"圈子评论","url"=>"code/group/comments");

require_once("group.class.php");


/**
 * 圈子列表
**/
if ($_A['query_type'] == "list"){
	if (isset($_POST['name'])){
		$var = array("name","type_id","remark","public","litpic","order");
		$data = post_var($var);
		if ($_POST['id']!=""){
			$data['id'] = $_POST['id'];
			$data['user_id'] = $_POST['user_id'];
			$result = groupClass::UpdateGroup($data);
			if ($result>0){
				$msg = array($MsgInfo["group_update_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			$admin_log["operating"] = "update";
		}else{
			$data['username'] = $_POST['username'];
			$result = groupClass::AddGroup($data);
			if ($result>0){
				$msg = array($MsgInfo["group_add_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			$admin_log["operating"] = "add";
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "group";
		$admin_log["type"] = "group";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
		
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$data['user_id'] = $_REQUEST['user_id'];
		$result = groupClass::GetGroupOne($data);
		if (is_array($result)){
			$_A["group_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
	}elseif ($_REQUEST['view']!=""){
	
		if (isset($_POST['verify_remark']) && $_POST['verify_remark']!=""){
			$var = array("id","status","verify_remark");
			$data = post_var($var);
			$data['verify_userid'] = $_G['user_id'];
			$result = groupClass::VerifyGroup($data);
			if ($result>0){
				$msg = array($MsgInfo["group_verify_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "group";
			$admin_log["type"] = "group";
			$admin_log["operating"] = "verify";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}else{
				$data['id'] = $_REQUEST['view'];
			$data['user_id'] = $_REQUEST['user_id'];
			$result = groupClass::GetGroupOne($data);
			if (is_array($result)){
				$_A["group_result"] = $result;
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}
	
	}elseif($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$data['user_id'] = $_REQUEST['user_id'];
		$data['admin'] = 1;
		$result = groupClass::DeleteGroup($data);
		if ($result>0){
			$msg = array($MsgInfo["group_del_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "group";
		$admin_log["type"] = "group";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}
}


/**
 * 圈子类型
**/

elseif ($_A['query_type'] == "type" ){
	if (isset($_POST['name'])){
		$var = array("name","nid","remark","status","order");
		$data = post_var($var);
		if ($_POST['id']!=""){
			$data['id'] = $_POST['id'];
			$result = groupClass::UpdateGroupType($data);
			if ($result>0){
				$msg = array($MsgInfo["group_type_update_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			$admin_log["operating"] = "update";
		}else{
			$result = groupClass::AddGroupType($data);
			if ($result>0){
				$msg = array($MsgInfo["group_type_add_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			$admin_log["operating"] = "add";
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "group";
		$admin_log["type"] = "type";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
		
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = groupClass::GetGroupTypeOne($data);
		if (is_array($result)){
			$_A["group_type_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	
	}elseif($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$result = groupClass::DelGroupType($data);
		if ($result>0){
			$msg = array($MsgInfo["group_type_del_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "group";
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
 * 圈子列表
**/
elseif ($_A['query_type'] == "articles"){
	if (isset($_POST['name'])){
		$var = array("name","contents","status","group_id","user_id");
		$data = post_var($var);
		if ($_POST['id']!=""){
			$data['id'] = $_POST['id'];;
			$result = groupClass::UpdateGroupArticles($data);
			if ($result>0){
				$msg = array($MsgInfo["group_articles_update_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			$admin_log["operating"] = "update";
		}else{
			$data['username'] = $_POST['username'];
			$result = groupClass::AddGroupArticles($data);
			if ($result>0){
				$msg = array($MsgInfo["group_articles_add_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			$admin_log["operating"] = "add";
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "group";
		$admin_log["type"] = "articles";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
		
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = groupClass::GetGroupArticlesOne($data);
		if (is_array($result)){
			$_A["group_articles_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
	}
}



/**
 * 圈子成员
**/

elseif ($_A['query_type'] == "member" ){
	if (isset($_POST['status'])){
		$var = array("status","admin_status");
		$data = post_var($var);
		$data['user_id'] = $_POST['user_id'];
		$data['group_id'] = $_POST['group_id'];
		$result = groupClass::UpdateGroupMember($data);
		if ($result>0){
			$msg = array($MsgInfo["group_member_update_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		$admin_log["operating"] = "update";
		
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "group";
		$admin_log["type"] = "member";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
		
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = groupClass::GetGroupMemberOne($data);
		if (is_array($result)){
			$_A["group_member_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	
	}elseif($_REQUEST['close']!=""){
		$data['user_id'] = $_REQUEST['close'];
		$data['group_id'] = $_REQUEST['group_id'];
		$result = groupClass::CloseGroupMember($data);
		if ($result>0){
			$msg = array($MsgInfo["group_member_close_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "group";
		$admin_log["type"] = "member";
		$admin_log["operating"] = "close";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}
	
}




/**
 * 圈子评论
**/

elseif ($_A['query_type'] == "comments" ){
	if (isset($_POST['status'])){
		$var = array("status","contents","group_id","articles_id");
		$data = post_var($var);
		$data['id'] = $_POST['id'];
		if ($_POST['id']!=""){
			$result = groupClass::UpdateGroupComments($data);
			if ($result>0){
				$msg = array($MsgInfo["group_comments_update_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			
			$admin_log["operating"] = "update";
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "group";
			$admin_log["type"] = "comments";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
		
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = groupClass::GetGroupCommentsOnes($data);
		if (is_array($result)){
			$_A["group_comments_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	
	}
	
}
?>