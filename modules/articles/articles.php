<?php
/******************************
 * $File: articles.inc.php
 * $Description: 文章后台管理中心
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$_A['list_purview']["articles"]["name"] = "文章管理";
$_A['list_purview']["articles"]["result"]["articles_list"] = array("name"=>"文章管理","url"=>"code/articles/list");
$_A['list_purview']["articles"]["result"]["articles_new"] = array("name"=>"添加文章","url"=>"code/articles/new");
$_A['list_purview']["articles"]["result"]["articles_type"] = array("name"=>"分类栏目","url"=>"code/articles/type");
$_A['list_purview']["articles"]["result"]["articles_page_list"] = array("name"=>"页面列表","url"=>"code/articles/page_list");
$_A['list_purview']["articles"]["result"]["articles_page_new"] = array("name"=>"添加页面","url"=>"code/articles/page_new");

require_once("articles.class.php");

/**
 * 1,如果类型为空的话则显示所有的文件列表
**/
if ($_A['query_type'] == "list"){
	check_rank("articles_list");//检查权限
	$_A['articles_flag'] = $articles_flag;
	if ($_REQUEST['view']!=""){
		$data['id'] = $_REQUEST['view'];
		$result = articlesClass::GetOne($data);
		if (is_array($result)){
			$_A['articles_result'] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	}
	/**
	 * 批量修改
	**/
	elseif ($_POST['type'] !=""){ 
		$data['id'] = $_POST['id'];
		$data['aid'] = $_POST['aid'];
		$data['order'] = $_POST['order'];
		$data['type'] = $_POST['type'];
		$result = articlesClass::Action($data);
		if ($result>0){
			$msg = array("操作成功","",$_A["query_url_all"]."order=id_desc");		
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "articles";
		$admin_log["type"] = "article";
		$admin_log["operating"] = "action";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	
	}
	/* 删除
	**/
	elseif ($_REQUEST['del'] !=""){
		$data = array("id"=>$_REQUEST['del']);
		$result = articlesClass::Delete($data);
		if ($result>0){
			$msg = array("删除成功","",$_A["query_url_all"]."order=id_desc");
			
			
			
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "articles";
		$admin_log["type"] = "article";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}
	
	//审核
	elseif ($_REQUEST['check'] !=""){
		if($_POST['status']!=""){
			
			$data = array("id"=>$_REQUEST['check']);
			$data['valicode'] = $_POST['valicode'];
			$data['status'] = $_POST['status'];
			$data['verify_userid'] = $_G['user_id'];
			$data['verify_remark'] = $_POST['verify_remark'];
			$result = articlesClass::Verify($data);
			if ($result>0){
				$msg = array("审核成功","",$_A["query_url_all"]."&order=id_desc");
			}else{
				$msg = array($MsgInfo[$result]);
			}
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "articles";
			$admin_log["type"] = "article";
			$admin_log["operating"] = "check";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}else{
		$result = articlesClass::GetTypeList(array("limit"=>"all"));
		if ($result != false){
			foreach ($result as  $key => $value){
				$_A["articles_type_result"][$value['id']] = $value['name'];
			}
		}
	}

}

/**
 * 1,如果类型为空的话则显示所有的文件列表
**/
elseif ($_A['query_type'] == "type"){
	check_rank("articles_type");//检查权限
	if ($_POST['name']!=""){
		
		$var = array("name","nid","pid","contents","order");
		$data = post_var($var);
		$data['contents'] = empty($data['contents'])?$_POST['contents']:$data['contents'];
		if ($_POST['id']!=""){
			$data['id'] = $_POST['id'];
			$result = articlesClass::UpdateType($data);
			if ($result>0){
				$msg = array($MsgInfo["articles_type_update_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			$admin_log["operating"] = "update";
		}else{
			$result = articlesClass::AddType($data);
			if ($result>0){
				$msg = array($MsgInfo["articles_type_add_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			$admin_log["operating"] = "add";
		}
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "borrow";
		$admin_log["type"] = "type";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
		
	}elseif($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = articlesClass::GetTypeOne($data);
		if (!is_array($result)){
			$msg = array($MsgInfo[$result]);
		}else{
			$_A['article_type_result'] = $result;
		}
	}elseif($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$result = articlesClass::DelType($data);
		if ($result>0){
			$msg = array($MsgInfo["articles_type_del_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "borrow";
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
 * 添加
**/
elseif ($_A['query_type'] == "new" || $_A['query_type'] == "edit" ){
	check_rank("articles_new");//检查权限
	if (isset($_POST['name'])){
		$var = array("name","contents","user_id","status","tags","order","public","publish","credits","title");
		$data = post_var($var);
		$data['contents'] = empty($data['contents'])?$_POST['contents']:$data['contents'];
		$data["nid"] = $data['user_id'].time();
		if ($_POST['type_id']!=""){
			$data['type_id'] = join(",",$_POST['type_id']);
			$_SESSION['articles_type_id'] = $data['type_id'] ;
		}
		if ($_POST['public']==3){
			$data['password'] = $_POST['password'];
		}else{
			$data['password'] = "";
		}
		if ($_POST['flag']!=""){
			$data['flag'] = join(",",$_POST['flag']);
		}
		if ($_POST['clearlitpic']==1){
			$data['litpic'] = "";
		}
		$_G['upimg']['file'] = "litpic";
		$_G['upimg']['code'] = "articles";
		$_G['upimg']['filesize'] = "2048";
		$_G['upimg']['type'] = "article";
		$_G['upimg']['user_id'] = $data['user_id'];
		$_G['upimg']['article_id'] = $_POST['id'];
		$uploadfiles = $upload->UpfileSwfupload($_G['upimg']);
		
		if (is_array($uploadfiles)){
			$data['litpic'] = $uploadfiles['upfiles_id'];
		}
		if ($_A['query_type'] == "new"){
			$data['user_id'] = $_G['user_id'];
			$result = articlesClass::Add($data);
		}else{
			$data['id'] = $_POST['id'];
			if ($data['litpic']!="" || $_POST['clearlitpic']==1){
				$_data['user_id'] = $data["user_id"];
				$_data['id'] = $_POST["oldlitpic"];
				$upload->Delete($_data);
				
			}
			$result = articlesClass::Update($data);
		}
		if ($result >0){
			$msg = array("操作成功","",$_A['query_url']."&order=id_desc");
			
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "articles";
		$admin_log["type"] = "article";
		$admin_log["operating"] = $_A['query_type'];
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}else{
		$_A['articles_type_id'] = $_SESSION['articles_type_id'];
		if ($_A['query_type'] == "edit"){
			$data['id'] = $_REQUEST['id'];
			$result = articlesClass::GetOne($data);
			if (is_array($result)){
				$_A['articles_result'] = $result;
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}else{
			$_A['article_type_result'] = articlesClass::GetTypeMenu();
			if (count($_A['article_type_result'])==0){
				$msg = array($MsgInfo["articles_add_type_empty"],"",$_A['query_url']."/type");
			}
		}
		
		
	}
}

/**
 * 页面列表
**/
elseif ($_A['query_type'] == "page_list"){
	check_rank("articles_page_list");//检查权限
	if ($_REQUEST['view']!=""){
		$data['id'] = $_REQUEST['view'];
		$result = articlesClass::GetPageOne($data);
		if (is_array($result)){
			$_A['page_result'] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	}
	/**
	 * 删除
	**/
	elseif ($_REQUEST['del'] !=""){
		$data = array("id"=>$_REQUEST['del']);
		$result = articlesClass::DeletePage($data);
		if ($result>0){
			$msg = array("删除成功","",$_A["query_url_all"]."/page_list");
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "articles";
		$admin_log["type"] = "pages";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}

}


/**
 * 添加页面
**/
elseif ($_A['query_type'] == "page_new" || $_A['query_type'] == "page_edit" ){
	check_rank("articles_page_new");//检查权限
	if (isset($_POST['name'])){
		$var = array("name","contents","status","pid","nid","tags","order","public","publish");
		$data = post_var($var);
		$data['contents'] = empty($data['contents'])?$_POST['contents']:$data['contents'];
		if ($_POST['public']==3){
			$data['password'] = $_POST['password'];
		}else{
			$data['password'] = "";
		}
		if ($_POST['flag']!=""){
		$data['flag'] = join(",",$_POST['flag']);
		}
		if ($_A['query_type'] == "page_new"){
			$data['user_id'] = $_G['user_id'];
			$result = articlesClass::AddPage($data);
			$admin_log["operating"] = "new";
		}else{
			$data['id'] = $_POST['id'];
			$result = articlesClass::UpdatePage($data);
			$admin_log["operating"] = "edit";
		}
		if ($result >0){
			$msg = array("操作成功","",$_A['query_url']."/page_list");
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "articles";
		$admin_log["type"] = "pages";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}else{
		
		if ($_A['query_type'] == "page_edit"){
			$data['id'] = $_REQUEST['id'];
			$result = articlesClass::GetPageOne($data);
			if (is_array($result)){
				$_A['page_result'] = $result;
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}
		
		
	}
}

?>