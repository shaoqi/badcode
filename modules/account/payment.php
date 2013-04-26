<?php
/******************************
 * $File: payment.class.php
 * $Description: 支付管理
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

require_once("account.payment.php");

if ($_REQUEST['type'] == "list" || $_REQUEST['type'] == ""){
	
	//修改状态
	if (isset($_REQUEST['id']) && isset($_REQUEST['status'])){
		$sql = "update {account_payment} set status='".$_REQUEST['status']."' where id = ".$_REQUEST['id'];
		$mysql->db_query($sql);	
	}
	
	$result = accountpaymentClass::GetList();
	
	if (is_array($result)){
		$_A['payment_list'] = $result;
	}
}


/**
 * 如果类型为空的话则显示所有的文件列表
**/
elseif ($_REQUEST['type'] == "all"){
	//修改状态
	
	$result = accountpaymentClass::GetListAll();
	
	if (is_array($result)){
		$_A['payment_list'] = $result;
	}else{
		$msg = array($result);
	}
}
/**
 * 添加
**/
elseif ($_REQUEST['type'] == "new"  || $_REQUEST['type'] == "edit" || $_REQUEST['type'] == "start" ){
	
	if (isset($_POST['name'])){
		$var = array("name","nid","order","status","description");
		$data = post_var($var);
		
		
		if ($_POST['clearlitpic']==1){
			$data['litpic'] = "";
		}else{
			$_G['upimg']['file'] = "litpic";
			$_G['upimg']['mask_status'] = 0;
			$pic_result = $upload->upfile($_G['upimg']);
			if (!empty($pic_result)){
				$data['litpic'] = $pic_result[0]['filename'];
			}
		}
		
		$config = isset($_POST['config'])?$_POST['config']:"";
		$data['config'] = serialize($config);
		$data['type'] = $_REQUEST['type'];
		if ($_REQUEST['type'] == "edit"){
			$data['id'] = isset($_POST['id'])?$_POST['id']:"";
		}
		$result = accountpaymentClass::Action($data);
		
		
		if ($result >0){
			$msg = array($MsgInfo['payment_action_success'],"",$_A['query_url']."/payment");
		}else{
			$msg = array($result);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "payment";
		$admin_log["type"] = "action";
		$admin_log["operating"] = $_REQUEST['type'];
		$admin_log["article_id"] = $data['id'];
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  join(",",$data);
		usersClass::AddAdminLog($admin_log);
	}
	
	elseif ($_REQUEST['type'] == "edit" || $_REQUEST['type'] == "new" || $_REQUEST['type'] == "start" ){
		$data['nid'] = $_REQUEST['nid'];
		$data['id'] = isset($_REQUEST['id'])?$_REQUEST['id']:"";
		$result = accountpaymentClass::GetOne($data);
	
		if (is_array($result)){
			$result['nid'] = $data['nid'];
			$_A['payment_result'] = $result;
		}else{
			$msg = array($result);
		}
		
	}
	
}			

	
/**
 * 删除
**/
elseif ($_REQUEST['type'] == "del"){
	$data['id'] = $_REQUEST['id'];
	$result = accountpaymentClass::Delete($data);
	if ($result >0){
		$msg = array($MsgInfo['payment_del_success'],"",$_A['query_url']."/payment");
	}else{
		$msg = array($MsgInfo[$result]);
	}
	//加入管理员操作记录
	$admin_log["user_id"] = $_G['user_id'];
	$admin_log["code"] = "payment";
	$admin_log["type"] = "action";
	$admin_log["operating"] = "del";
	$admin_log["article_id"] = $data['id'];
	$admin_log["result"] = $result>0?1:0;
	$admin_log["content"] =  $msg[0];
	usersClass::AddAdminLog($admin_log);
}
?>