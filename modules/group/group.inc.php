<?php
/******************************
 * $File:group.inc.php
 * $Description: 圈子前台管理文件
 * $Author: ahui 
 * $Time:2011-11-09
 * $Update:None 
 * $UpdateDate:None 
 * Copyright(c) 2012 by dycms.net. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

require_once("group.class.php");

if ($_U['query_type']=="new"){
	$msg = check_valicode();
	if ($msg==""){
		$var = array("name","type_id","remark","public","litpic","order");
		$data = post_var($var);
		$data['user_id'] = $_G['user_id'];
		$data['member_count'] = 1;
		$result = groupClass::AddGroup($data);
		if ($result>0){
			$msg = array($MsgInfo["group_add_success"],"","/group/index.html");
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
	}

}

elseif ($_U['query_type']=="edit"){
	if (isset($_POST['name'])){
		$var = array("name","type_id","remark","public","litpic");
		$data = post_var($var);
		if ($_POST['id']!=""){
			$member_status = groupClass::CheckGroupRank(array("user_id"=>$_G['user_id'],"group_id"=>$_POST['id']));
			if ($member_status<2) {
				$msg = array($MsgInfo["group_member_not_admin"],"","/group/a{$_POST['id']}.html");
			}else{	
				$data['id'] = $_POST['id'];
				$data['user_id'] = $_POST['user_id'];
				$result = groupClass::UpdateGroup($data);
				if ($result>0){
					$msg = array($MsgInfo["group_update_success"],"","/group/a{$_POST['id']}.html");
				}else{
					$msg = array($MsgInfo[$result]);
				}
			}
		}
		
	}

}

elseif ($_U['query_type']=="join"){
	if (isset($_POST['remark']) && $_POST['remark']!=""){
		$data['group_id'] = $_REQUEST['group_id'];
		if (!isset($_REQUEST['type']) || $_REQUEST['type']==""){
			$data['user_id'] = $_G['user_id'];
			$data['remark'] = $_POST['remark'];
			$result = groupClass::AddGroupMember($data);
			if ($result>0){
				$msg = array($MsgInfo["group_member_add_success"]);
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}else{
			if ($_REQUEST['type']=="exit"){
				$data['user_id'] = $_G['user_id'];
				$result = groupClass::ExitGroupMember($data);
				if ($result>0){
					$msg = array($MsgInfo["group_member_exit_success"],"","/group/a{$_REQUEST['group_id']}.html");
				}else{
					$msg = array($MsgInfo[$result]);
				}
			}else{
				$data['type'] = $_REQUEST['type'];
				$data['user_id'] = $_REQUEST['user_id'];
				$data['verify_userid'] = $_G['user_id'];
				$data['verify_remark'] =  $_POST['remark'];
				$result = groupClass::VerifyGroupMember($data);
				if ($result>0){
					$msg = array($MsgInfo["group_member_verify_success"]);
				}else{
					$msg = array($MsgInfo[$result]);
				}
			}
		}
	}else{
		$temlate_dir = "themes/{$_G['system']['con_template']}";
		$magic->template_dir = $temlate_dir;
		$template = "group_join.html";
	}
}

elseif ($_U['query_type']=="articles"){
	if (isset($_POST['name']) && $_POST['name']!=""){
		$data['group_id'] = $_REQUEST['group_id'];
		$data['user_id'] = $_G['user_id'];
		$data['name'] = $_POST['name'];
		$data['contents'] = $_POST['contents'];
		//判断是否有添加圈子的权限
		$member_status = groupClass::CheckGroupRank(array("user_id"=>$_G['user_id'],"group_id"=>$data['group_id']));
		if ($member_status==0) {
			$msg = array($MsgInfo["group_articles_not_group"],"","/group/a{$_REQUEST['group_id']}.html");
		}else{	
			$data['status'] = 1;
			$result = groupClass::AddGroupArticles($data);
			if ($result>0){
				$msg = array($MsgInfo["group_articles_add_success"],"","/group/a{$_REQUEST['group_id']}.html");
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}
	}elseif (isset($_REQUEST['del']) && $_REQUEST['del']!=""){
		$data['id']=$_REQUEST['del'];
		$result = groupClass::DeleteGroupArticles($data);
		if ($result>0){
			$msg = array($MsgInfo["group_articles_del_success"],"","/group/a{$_REQUEST['group_id']}.html");
		}else{
			$msg = array($MsgInfo[$result]);
		}
	}elseif (isset($_REQUEST['articles_id']) && $_REQUEST['articles_id']!=""){
		$data['id']=$_REQUEST['articles_id'];
		$data['contents']=$_POST['contents'];
		$result = groupClass::UpdateGroupArticles($data);
		if ($result>0){
			$msg = array($MsgInfo["group_articles_update_success"],"","/group/a{$result}.html");
		}else{
			$msg = array($MsgInfo[$result]);
		}
	}
}


elseif ($_U['query_type']=="comment"){
	if (isset($_POST['contents']) && $_POST['contents']!=""){
		if ($_REQUEST['type']=="edit"){
			$data['group_id'] = $_REQUEST['group_id'];
			$data['user_id'] = $_G['user_id'];
			$data['articles_id'] = $_REQUEST['id'];
			$data['contents'] = $_POST['contents'];
			$data['id'] = $_REQUEST['cid'];
			$member_status = groupClass::CheckGroupRank(array("user_id"=>$_G['user_id'],"group_id"=>$data['group_id']));
			if ($member_status==0) {
				$msg = array($MsgInfo["group_articles_not_group"],"","/group/a{$_REQUEST['group_id']}.html");
			}else{
				$result = groupClass::UpdateGroupComments($data);
				if ($result>0){
					$msg = array($MsgInfo["group_comments_update_success"],"","/group/a{$_REQUEST['group_id']}.html?id={$_REQUEST['id']}");
				}else{
					$msg = array($MsgInfo[$result]);
				}
			}
		}else{
			$data['group_id'] = $_REQUEST['group_id'];
			$data['user_id'] = $_G['user_id'];
			$data['articles_id'] = $_REQUEST['id'];
			$data['contents'] = $_POST['contents'];
			$data['pid'] = $_REQUEST['pid'];
			$data['tid'] = $_REQUEST['tid'];
			$member_status = groupClass::CheckGroupRank(array("user_id"=>$_G['user_id'],"group_id"=>$data['group_id']));
			if ($member_status==0) {
				$msg = array($MsgInfo["group_articles_not_group"],"","/group/a{$_REQUEST['group_id']}.html");
			}else{
				$data['status'] = 1;
				$result = groupClass::AddGroupComments($data);
				if ($result>0){
					$msg = array($MsgInfo["group_comments_add_success"],"","/group/a{$_REQUEST['group_id']}.html?id={$_REQUEST['id']}");
				}else{
					$msg = array($MsgInfo[$result]);
				}
			}
		}
	}elseif (isset($_REQUEST['del']) && $_REQUEST['del']!=""){
		$data['id']=$_REQUEST['del'];
		$data['group_id']=$_REQUEST['group_id'];
		$data['articles_id']=$_REQUEST['articles_id'];
		$result = groupClass::DeleteGroupComments($data);
		if ($result>0){
			$msg = array($MsgInfo["group_comments_delete_success"],"","/group/a{$_REQUEST['group_id']}.html?id={$data['articles_id']}");
		}else{
			$msg = array($MsgInfo[$result]);
		}
	}else{
		$temlate_dir = "themes/{$_G['system']['con_template']}";
		$magic->template_dir = $temlate_dir;
		$template = "group_comment.html";
	}
}




if ($template==""){
	$template = "user_group.html";
}
?>
