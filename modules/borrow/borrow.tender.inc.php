<?php
/******************************
 * $File: borrow.tender.inc.php
 * $Description: 用户投资用户中心处理文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

require_once("borrow.tender.php");//类名
require_once("borrow.loan.php");//类名
require_once("borrow.class.php");//类名
require_once("borrow.type.php");//类名

//投资
if ($_REQUEST['p']=="invest"){
    $borrow_result = borrowTenderClass::CheckTender(array("borrow_nid"=>$_REQUEST['borrow_nid']));
    if (is_array($borrow_result)){
        $_U['loan_tender'] = $borrow_result;
    	$account_result = accountClass::GetAccountUsers(array("user_id"=>$_G["user_id"]));
        $_U['loan_tender']['balance'] = $account_result["balance"];
        $borrow_type = borrowTypeClass::GetTypeOne(array("nid"=>$borrow_result["borrow_type"]));
         $_U['loan_tender']['password_status'] = $borrow_type["password_status"];
    }else{
        $msg = array($MsgInfo[$borrow_result],"","/");
    }
    $_G["site_nid"] = "borrow";
    $template = "users_tender_invest.html";

}
elseif ($_REQUEST['p']=="add"){
    
    //将借款标添加进去
	$_tender['borrow_nid'] = $_POST['borrow_nid'];
	$_tender['user_id'] = $_G['user_id'];
	$_tender['account'] = $_POST['money'];
	$_tender['contents'] =  iconv("UTF-8", "GB2312", $_POST['contents']);
	$_tender['paypassword'] = $_POST['paypassword'];
	$_tender['borrow_password'] = $_POST['borrow_password'];
	$_tender['valicode'] = $_POST['valicode'];
	$_tender['status'] = 0;
	$_tender['nid'] = "tender_".$_G["user_id"]."_".time();
	$result = borrowTenderClass::AddTender($_tender);

	if ($result>0){
		$msg = array(1);
	}elseif (IsExiest($MsgInfo[$result])!=""){
		$msg = array($MsgInfo[$result],"","/index.php?user&q=code/borrow/tender&p=now");
	}else{
		$msg = array($result);
	}	
    echo $msg[0];
    exit;
    
}

//正在投资的借款
elseif ($_REQUEST['p']=="now"){
    
    
}
//成功投资
elseif ($_REQUEST['p']=="success"){
    
}
//成功投资
elseif ($_REQUEST['p']=="wait" || $_REQUEST['p']=="agreement" ){
	$result = borrowCountClass::GetUsersRecoverCount(array("user_id"=>$_G['user_id']));
    $_U['borrow_wait'] = $result;
	
}


	
?>