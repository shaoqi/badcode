<?php
/******************************
 * $File: borrow.amount.inc.php
 * $Description: 用户借款额度用户中心处理文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

require_once("borrow.amount.php");//类名

//新的借款
if ($_REQUEST['p']==""){
    
    if (isset($_POST['name']) && $_POST["name"]!=""){
        $var = array("borrow_use","account","amount_style","amount_type","amount_account","remark","content");
		$data = post_var($var);
        $data['oprate'] = "add";
        $data['type'] = "apply";
		$data['user_id'] = $_G['user_id'];
	
		$result = borrowAmountClass::AddAmountApply($data);
		if ($result>0){	
				if($_POST['borrowtype']!=''){
					$msg = array("您的额度申请已经提交，请等待审核。","","/?user&q=code/borrow/jrsh&type=amount");
				}else{
					$msg = array("您的额度申请已经提交，请等待审核。","","/index.php?user&q=code/borrow/amount");
				}
		}else{
			$msg = array($MsgInfo[$result]);
		}
    }else{
        
    }
  
}else if ($_REQUEST['p']=="log"){
    
    
}else{
    $template = "error.html";//开始发布借款
}	
?>