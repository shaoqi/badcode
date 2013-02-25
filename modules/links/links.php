<?php
/******************************
 * $File: links.php
 * $Description:友情链接处理文件
 * $Author: ahui 
 * $Time:2011-11-09
 * $Update:None 
 * $UpdateDate:None 
 * Copyright(c) 2012 by dycms.net. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

require_once("links.class.php");

$_A['list_purview']["links"]["name"] = "友情链接";
$_A['list_purview']["links"]["result"]["links_list"] = array("name"=>"友情链接","url"=>"code/links/list");
$_A['list_purview']["links"]["result"]["links_type"] = array("name"=>"类型管理","url"=>"code/links/type");



/**
 * 如果类型为空的话则显示所有的文件列表
**/
if ($_A['query_type'] == "list"){
	check_rank("links_list");//检查权限
	
}
	
	
/**
 * 链接类型
**/
elseif ($_A['query_type'] == "type"){
	if (isset($_REQUEST['del_id'])){
		if ($_REQUEST['del_id'] !=1){
			$mysql->db_delete("links_type","id=".$_REQUEST['del_id']);
			$msg = array("删除成功","",$_A['query_url']."/type");
		}else{
			$msg = array("类型ID1为系统类型，不能删除","",$_A['query_url']."/type");
		}
	}elseif (!isset($_POST['submit'])){
		$_A['links_type_list'] = linksClass::GetTypeList();
	}else{
		foreach ($_POST['id'] as $key => $val){
			$mysql->db_query("update {links_type} set typename='".$_POST['typename'][$key]."' where id=".$val);
		}
		if ($_POST['typename1']!=""){
			$index['typename'] = $_POST['typename1'];
			$mysql->db_add("links_type",$index,"notime");
		}
		$msg = array("类型操作成功","",$_A['query_url']."/type");
	}
}

/**
 * 添加
**/
elseif ($_A['query_type'] == "new" || $_A['query_type'] == "edit" ){
	if (isset($_POST['type_id']) && $_POST['type_id']!=""){
		$var = array("type_id","status","order","url","logo","webname","summary","linkman","email");
		$data = post_var($var);
		
		$_G['upimg']['file'] = "logoimg";
		$_G['upimg']['mask_status'] = 0;
		
		$_G['upimg']['file'] = "logoimg";
		$_G['upimg']['code'] = "links";
		$_G['upimg']['type'] = "link";
		$_G['upimg']['user_id'] = $_G["user_id"];
		$_G['upimg']['article_id'] = $_G["user_id"];
		$pic_result = $upload->upfile($_G['upimg']);
		
		if ($pic_result!=""){
			$data['logoimg'] = $pic_result[0]['upfiles_id'];
		}
		
		
		if ($_A['query_type'] != "new"){
			$data['id'] = $_POST['id'];
			$result = linksClass::Update($data);
		}else{
			$result = linksClass::Add($data);
		}
		if ($result == false){
			$msg = array("输入有误，请跟管理员联系");
		}else{
			$msg = array("操作成功","返回上一页",$_A['query_url']);
		}
	
	
	}else{
		$_A['links_type_list'] = linksClass::GetTypeList();
		if ($_A['query_type'] == "edit"){
			$_A['links_result'] = linksClass::GetOne(array("id"=>$_REQUEST['id']));
		}
	}
}
	
	
/**
 * 删除
**/
elseif ($_A['query_type'] == "del"){
	$data['id'] = $_REQUEST['id'];
	$result = linksClass::Delete($data);
	if ($result !== true){
		$msg = array($result);
	}else{
		$msg = array("删除成功","返回上一页",$_A['query_url']);
	}
}


?>