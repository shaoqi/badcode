<?php
/******************************
 * $File: remind.php
 * $Description: 提醒模块后台处理文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

require_once("remind.class.php");

$_A['list_purview']["remind"]["name"] = "提醒设置";
$_A['list_purview']["remind"]["result"]["remind_list"] = array("name"=>"提醒管理","url"=>"code/remind/list");


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
		$var = array("name","nid","type_id","order","message","phone","email");
		$data = post_var($var);
		$result = remindClass::Add($data);
		if ($result !== true){
			$msg = array($result);
		}else{
			$msg = array("操作成功");
		}
	}else{
		$data['limit'] = "all";
		$data['id'] = $_REQUEST['id'];
		$_A['remind_type_result'] =remindClass::GetTypeOne($data);
		if (is_array($_A['remind_type_result'])){
			$data['type_id'] = $_REQUEST['id'];
			$_A['remind_list'] = remindClass::GetList($data);
		}else{
			$msg = array($result);
		}
		$pname = empty($pname)?"跟类型下":$pname;
		$magic->assign("pname",$pname);
	}
}


/**
 * 排序
**/
elseif ($_A['query_type'] == "actions"){
	if (isset($_POST['id'])){
		$data['id'] = $_POST['id'];
		$data['name'] = $_POST['name'];
		$data['nid'] = $_POST['nid'];
		$data['order'] = $_POST['order'];$data['order'] = $_POST['order'];
		$data['message'] = $_POST['message'];
		$data['phone'] = $_POST['phone'];
		$data['email'] = $_POST['email'];
		$result = remindClass::Action($data);
		if ($result !== true){
			$msg = array($result);
		}else{
			$msg = array("操作成功");
		}
	}else{
		//添加
		if (isset($_POST['name'])){
			$data['type'] = "add";
			$data['name'] = $_POST['name'];
			$data['nid'] = $_POST['nid'];
			$data['type_id'] = $_POST['type_id'];
			$data['order'] = $_POST['order'];
			$data['message'] = $_POST['message'];
			$data['phone'] = $_POST['phone'];
			$data['email'] = $_POST['email'];
			$result = remindClass::Action($data);
			if ($result !== true){
				$msg = array($result);
			}else{
				$msg = array("操作成功");
			}
		}else{
			$msg = array("操作有误");
		}
	}
}
/**
 * 删除
**/
elseif ($_A['query_type'] == "del"){
	$id = $_REQUEST['id'];
	$result = remindClass::Delete(array("id"=>$id));
	if ($result !== true){
		$msg = array($result);
	}else{
		$msg = array("删除成功");
	}
}

/**
 * 链接类型
**/
elseif ($_A['query_type'] == "type_new" || $_A['query_type'] == "type_edit"){
	if (isset($_POST['name'])){
		$var = array("name","nid","order");
		$data = post_var($var);
		if ($_A['query_type'] == "type_new"){
			$result = remindClass::AddType($data);
			if ($result !== true){
				$msg = array($result);
			}else{
				$msg = array("添加成功");
			}
		}else{
			$data['id'] = $_POST['id'];
			$result = remindClass::UpdateType($data);
			if ($result !== true){
				$msg = array($result);
			}else{
				$msg = array("添加成功");
			}
		}
		$user->add_log($_log,$result);//记录操作
	}elseif( $_A['query_type'] == "type_edit"){
		$data['id'] = $_REQUEST['id'];
		$_A['remind_type_result'] = remindClass::GetTypeOne($data);
	}
}

/**
 * 删除
**/
elseif ($_A['query_type'] == "type_del"){
	$result = LremindClass::DeleteType($_REQUEST['id']);
	if ($result !== true){
		$msg = array($result);
	}else{
		$msg = array("删除成功");
	}
	$user->add_log($_log,$result);//记录操作
}
/**
 * 类型排序
**/
elseif ($_A['query_type'] == "type_action"){
	if (isset($_POST['id'])){
		$data['id'] = $_POST['id'];
		$data['name'] = $_POST['name'];
		$data['nid'] = $_POST['nid'];
		$data['order'] = $_POST['order'];
		$result = remindClass::ActionType($data);
		if ($result !== true){
			$msg = array($result);
		}else{
			$msg = array("操作成功");
		}
	}else{
		if (isset($_POST['name'])){
			$data['type'] = $_POST['type'];
			$data['name'] = $_POST['name'];
			$data['nid'] = $_POST['nid'];
			$data['order'] = $_POST['order'];
			$result = remindClass::ActionType($data);
			if ($result !== true){
				$msg = array($result);
			}else{
				$msg = array("操作成功");
			}
		}else{
			$msg = array("操作有误");
		}
	}
}

//防止乱操作
else{
	$msg = array("输入有误，请不要乱操作","",$url);
}



?>