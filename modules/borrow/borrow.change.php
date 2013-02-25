<?
/******************************
 * $File: borrow.change.php
 * $Description: 用户中心用户借款类
 * $Author: ahui 
 * $Time:2012-09-20
 * $Update:Ahui
 * $UpdateDate:2012-09-20  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问
require_once(ROOT_PATH."modules/account/account.class.php");
require_once(ROOT_PATH."modules/remind/remind.class.php");
require_once(ROOT_PATH."modules/borrow/borrow.class.php");
require_once(ROOT_PATH."modules/borrow/borrow.count.php");

$MsgInfo["borrow_change_action_error"] = "您的操作有误，请不要乱操作";
$MsgInfo["borrow_change_account_not_numeric"] = "转让金额必须是数字";
$MsgInfo["borrow_change_account_most"] = "转让金额不能小于0";
$MsgInfo["borrow_change_action_success"] = "转让信息发布成功";
$MsgInfo["borrow_change_status_yes"] = "此信息已经转让，请等待管理员审核";
$MsgInfo["borrow_change_paypassword_error"] = "支付密码不正确";
$MsgInfo["borrow_change_wait_account_error"] = "转让金额不能大于待收金额";
$MsgInfo["borrow_change_cancel_success"] = "债权转让撤销成功";
$MsgInfo["borrow_change_web_success"] = "债权转让成功，请等待管理员审核";
$MsgInfo["borrow_change_cancel_error"] = "债权转让撤销失败，请不要乱操作";
$MsgInfo["borrow_change_not_self"] = "不能购买自己的债权";
$MsgInfo["borrow_change_account_error"] = "你的可用金额不足";
$MsgInfo["borrow_change_buy_error"] = "债权购买失败";
$MsgInfo["borrow_change_buy_success"] = "债权购买成功";
$MsgInfo["borrow_change_verify_error"] = "债权审核成功";
$MsgInfo["borrow_change_verify_success"] = "网站审核成功";

class borrowChangeClass{
	
}

if ($_REQUEST['change_check']!=""){
	if (isset($_POST['remark']) && $_POST['remark']!=""){
		$msg = check_valicode();
		if ($msg==""){
			$var = array("status","remark");
			$data = post_var($var);
			$data['id'] = $_REQUEST['change_check'];
			$result = borrowChangeClass::WebVerifyChange($data);
			if ($result>0){
				$msg = array($MsgInfo["borrow_change_verify_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "borrow";
			$admin_log["type"] = "change";
			$admin_log["operating"] = "verify";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			borrowChangeClass::AddAdminLog($admin_log);
		}
	}
}
?>