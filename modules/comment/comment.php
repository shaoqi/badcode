<?
/******************************
 * $File: comment.php
 * $Description: 评论管理
 * $Author: ahui 
 * $Time:2011-08-09
 * $Update:None 
 * $UpdateDate:None 
 * Copyright(c) 2012 by dycms.net. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

check_rank("comment_list");//检查权限

require_once ('comment.class.php');


$_A['list_purview']["comment"]["name"] = "评论管理";
$_A['list_purview']["comment"]["result"]["comment_list"] = array("name"=>"评论管理","url"=>"code/comment/list");


if ($_A['query_type'] == "list"){
	//修改状态
	if (isset($_REQUEST['id']) && isset($_REQUEST['status'])){
		$sql = "update {comment} set status='".$_REQUEST['status']."' where id = ".$_REQUEST['id'];
		$mysql->db_query($sql);
	}

	$data['page'] = $_A['page'];
	$data['epage'] = $_A['epage'];
	$result = commentClass::GetList($data);
	
	if (is_array($result)){
		$pages->set_data($result);
		$_A['comment_list'] = $result['list'];
		$_A['showpage'] = $pages->show(3);
	
	}else{
		$msg = array($result);
	}
}


/**
 * 添加
**/
elseif ($_A['query_type'] == "new"  || $_A['query_type'] == "edit" || $_A['query_type'] == "view" ){
	
	$_A['list_title'] = "评论管理";
	if (isset($_POST['site_id'])){
		$var = array('user_id','module_code', 'article_id','comment');
		$data = post_var($var);
		
		if ($_A['query_type'] == "edit"){
			$data['id'] = $_REQUEST['id'];
			$result = commentClass::Update($data);
		}
		
		if ($result !== true){
			$msg = array($result);
		}else{
			$msg = array("操作成功");
		}
		$user->add_log($_log,$result);//记录操作
	}
	
	elseif ($_A['query_type'] == "edit" || $_A['query_type'] == "view" ){
		$data['id'] = $_REQUEST['id'];
		$data['code'] = $_REQUEST['module_code'];
		$result = commentClass::GetOne($data);
		if (is_array($result)){
			$_A['comment_result'] = $result;
			
		}else{
			$msg = array($result);
		}
		
	}
	
}			

	
/**
 * 删除
**/
elseif ($_A['query_type'] == "del"){
	$data['id'] = $_REQUEST['id'];
	$result = commentClass::Delete($data);
	if ($result !== true){
		$msg = array($result);
	}else{
		$msg = array("删除成功");
	}
	$user->add_log($_log,$result);//记录操作
}


?>