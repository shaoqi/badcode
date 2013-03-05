<?php
/******************************
 * $File:site.php
 * $Description: 站点处理文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

/**
 *  1，所有的站点
**/
if ($_A['query_type'] == "list"){
	check_rank("site_list");//检查权限
	
	if ($_POST['id']!=""){
		$data['id'] = $_POST['id'];
		$data['order'] = $_POST['order'];
		$result = adminClass::ActionSite($data);
		$msg = array("操作成功");
	}
}


/**
 *  2，站点所属的菜单，可以做出多个站点
**/
elseif ($_A['query_type'] == "menu" ){
	check_rank("site_menu");//检查权限
	if (isset($_POST['name'])){
		$msg = check_valicode();
		if ($msg==""){
			$var = array("name","nid","order","checked","contents");
			$data = post_var($var);
			if ($_POST['id']!=""){
				$data['id'] = $_POST['id'];
				$result = adminClass::UpdateSiteMenu($data);
				if ($result>0){
					$msg = array($MsgInfo["admin_site_menu_update_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "update";
			}else{
				$result = adminClass::AddSiteMenu($data);
				if ($result>0){
					$msg = array($MsgInfo["admin_site_menu_add_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "add";
			}
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "admin";
			$admin_log["type"] = "site_menu";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = adminClass::GetSiteMenuOne($data);
		if (is_array($result)){
			$_A["site_menu_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
	}elseif ($_REQUEST['checked']!=""){
		$data['id'] = $_REQUEST['checked'];
		$result = adminClass::UpdateSiteMenuChecked($data);
	
	}elseif($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$result = adminClass::DelSiteMenu($data);
		if ($result>0){
			$msg = array($MsgInfo["admin_site_menu_del_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "admin";
		$admin_log["type"] = "site_menu";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}
}

/**
 * 3,添加站点
**/
elseif ($_A['query_type'] == "new"){
	check_rank("site_new");//检查权限
	if (isset($_POST['name']) && $_POST['name']!=""){
	
		$var = array("name","status","nid","pid","remark","value","type","menu_id","order","index_tpl","list_tpl","content_tpl","keywords","description","seotitle");
		$data = post_var($var);
		if ($_POST['id']!=""){
			$data['id'] = $_POST['id'];
			$result = adminClass::UpdateSite($data);
			if ($result>0){
				$msg = array($MsgInfo["admin_site_update_success"],"",$_A['query_url_all']."&action=".$_REQUEST['action']."&menu_id={$data['menu_id']}");
			}else{
				$msg = array($MsgInfo[$result]);
			}
			$admin_log["operating"] = "update";
		}else{
			$result = adminClass::AddSite($data);
			if ($result>0){
				$msg = array($MsgInfo["admin_site_add_success"],"",$_A['query_url_all']."&action=".$_REQUEST['action']."&menu_id={$data['menu_id']}");
			}else{
				$msg = array($MsgInfo[$result]);
			}
			$admin_log["operating"] = "add";
		}
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "admin";
		$admin_log["type"] = "site";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}elseif(isset($_REQUEST['edit']) && $_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = adminClass::GetSiteOne($data);
		if (!is_array($result)){
			$msg = array($MsgInfo[$result]);
		}else{
			$_A['site_result'] = $result;
		}
	}elseif(isset($_REQUEST['del']) && $_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$result = adminClass::DelSite($data);
		if ($result>0){
			$msg = array($MsgInfo["admin_site_del_success"],"",$_A['query_url']."/list&menu_id=".$_REQUEST['menu_id']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "admin";
		$admin_log["type"] = "site";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}
}


//防止乱操作
else{
	$msg = array("输入有误，请不要乱操作");
}

//站点后台管理模板
$template = "admin_site.html";
?>