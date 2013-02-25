<?php
/******************************
 * $File: borrow.style.int.php
 * $Description: 借款还款方式后台处理
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

$_A['list_purview']["borrow"]["result"]["borrow_style"] = array("name"=>"还款方式","url"=>"code/borrow/style");

require_once("borrow.style.php");//类名

check_rank("borrow_style");//设置权限


//修改还款方式
if ($_REQUEST["p"] == "edit"){
   if ($_POST['id']!=""){
        $var = array("id","title","status","contents");
		$data = post_var($var);
		
		$result = borrowStyleClass::UpdateStyle($data);
		if ($result>0){
			$msg = array("还款方式修改成功","",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "borrow";
		$admin_log["type"] = "style";
		$admin_log["operating"] = "edit";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
    } 
}

?>