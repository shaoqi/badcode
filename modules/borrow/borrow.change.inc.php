<?
/******************************
 * $File: borrow.change_inc.php
 * $Description: 借款用户中心处理文件
 * $Author: ahui 
 * $Time:2010-08-09
 * $Update:None 
 * $UpdateDate:None 
 * Copyright(c) 2012 by dycms.net. All rights reserved
******************************/
require_once(ROOT_PATH."modules/borrow/borrow.change.php");
if ($_POST['id']!=""){
	if($_POST['change_account']>$_POST['max_account']){
		$msg = array("转让金额不能大于待回收本金");
	}else{
		$data['user_id'] = $_G['user_id'];
		$data['id'] = $_POST['id'];
		$data['account'] = $_POST['change_account'];
		$data['valid_time'] = 7;
		$data['remark'] = $_POST['remark'];
		$data['paypassword'] = $_POST['paypassword'];
		$result = borrowChangeClass::ActionChange($data);
		if ($result>0){
			$msg = array($MsgInfo["borrow_change_action_success"],"","?user&q=code/borrow/change&p=now");
		}elseif (IsExiest($MsgInfo[$result])!=""){			
			$msg = array($MsgInfo[$result]);
		}else{
			$msg = array("操作失败，请跟管理员联系");
		}
	}
}
elseif ($_REQUEST['cancel_id']!=""){
	$data['user_id'] = $_G['user_id'];
	$data['id'] = $_REQUEST['cancel_id'];
	$data['cancel_remark'] = $_POST['cancel_remark'];
	$data['paypassword'] = $_POST['paypassword'];
	$result = borrowChangeClass::CancelChange($data);
	if ($result>0){
		$msg = array($MsgInfo["borrow_change_cancel_success"],"","?user&q=code/borrow/change&p=now");
	}elseif (IsExiest($MsgInfo[$result])!=""){
		$msg = array($MsgInfo[$result]);
	}else{
		$msg = array("操作失败，请跟管理员联系");
	}	
}

elseif ($_REQUEST['web_id']!=""){
	if ($_POST['paypassword']!=""){
		$data['user_id'] = $_G['user_id'];
		$data['id'] = $_REQUEST['web_id'];
		$data['paypassword'] = $_POST['paypassword'];
		$result = borrowChangeClass::WebChange($data);
		if ($result>0){
			$msg = array($MsgInfo["borrow_change_web_success"],"","?user&q=code/borrow/change&p=now");
		}elseif (IsExiest($MsgInfo[$result])!=""){
			$msg = array($MsgInfo[$result]);
		}else{
			$msg = array("操作失败，请跟管理员联系");
		}	
	}
}
elseif ($_REQUEST['buy_id']!=""){
	$data['user_id'] = $_G['user_id'];
	$data['id'] = $_REQUEST['buy_id'];
	$data['paypassword'] = $_POST['paypassword'];
	$result = borrowChangeClass::BuyChange($data);
	if ($result>0){
		$msg = array($MsgInfo["borrow_change_buy_success"],"","?user&q=code/borrow/change&p=buy");
	}elseif (IsExiest($MsgInfo[$result])!=""){
		$msg = array($MsgInfo[$result]);
	}else{
		$msg = array("操作失败，请跟管理员联系");
	}	
}
elseif ($_REQUEST['_type']!=""){
	$msg = array("操作失败，请跟管理员联系");
}


?>
