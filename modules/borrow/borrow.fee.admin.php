<?php
/******************************
 * $File: borrow.fee.inc.php
 * $Description: 借款类型文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问


require_once("borrow.fee.php");//类名
$_A["account_type"] = array("account"=>"本息","interest"=>"利息","capital"=>"本金");

check_rank("borrow_fee");//设置权限
//修改
if ($_REQUEST["p"] == "edit" || $_REQUEST["p"] == "new"){
   if ($_POST['name']!=""){
     $var = array("name","title","nid","status","pay_tender","borrow_types","all_rank","account_scale_vip","account_scale_all","account_scales_vip","account_scales_all","user_type","type","order","fee_type","vip_borrow_scale","all_borrow_scale","all_borrow_scale","vip_borrow_scales","vip_borrow_scales_month","vip_borrow_scales_scale","vip_borrow_scales_max","all_borrow_scales","all_borrow_scales_month","all_borrow_scales_scale","all_borrow_scales_max","vip_rank","vip_period","all_period");

       	$data = post_var($var);
        if ($_POST["id"]==""){
            $result = borrowFeeClass::AddFee($data);
    	     if ($result>0){
    			$msg = array("借款费用添加成功","",$_A['query_url_all']);
    		}
        }else{
            $data["id"] = $_POST["id"];
    		$result = borrowFeeClass::UpdateFee($data);
    		if ($result>0){
    			$msg = array("借款费用修改成功","",$_A['query_url_all']);
    		}
		}
        if ($msg==""){
    		$msg = array($MsgInfo[$result]);
        }
        
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "borrow";
		$admin_log["type"] = "fee";
		$admin_log["operating"] = $_REQUEST["p"];
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
    } elseif ($_REQUEST["p"] == "edit"){
        $data = array("id"=>$_REQUEST["id"]);
        $_A['borrow_fee_result'] = borrowFeeClass::GetFeeOne($data);
    }
}elseif ($_REQUEST["p"] == "del"){
    
        $data["id"] = $_REQUEST["id"];
		$result = borrowFeeClass::DeleteFee($data);
    	$msg = array("借款费用删除成功","",$_A['query_url_all']);
    	
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "borrow";
		$admin_log["type"] = "fee";
		$admin_log["operating"] = $_REQUEST["p"];
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
}elseif ($_REQUEST["p"] == "type_edit" || $_REQUEST["p"] == "type_new"){
   if ($_POST['name']!=""){
     $var = array("name","title","nid","status","remark");

       	$data = post_var($var);
        if ($_POST["id"]==""){
            $result = borrowFeeClass::AddFeeType($data);
    	     if ($result>0){
    			$msg = array("借款费用类型添加成功","",$_A['query_url_all']."&p=type");
    		}
        }else{
            $data["id"] = $_POST["id"];
    		$result = borrowFeeClass::UpdateFeeType($data);
    		if ($result>0){
    			$msg = array("借款费用类型修改成功","",$_A['query_url_all']."&p=type");
    		}
		}
        if ($msg==""){
    		$msg = array($MsgInfo[$result]);
        }
        
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "borrow";
		$admin_log["type"] = "fee_type";
		$admin_log["operating"] = $_REQUEST["p"];
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
    } elseif ($_REQUEST["p"] == "type_edit"){
        $data = array("id"=>$_REQUEST["id"]);
        $_A['borrow_fee_result'] = borrowFeeClass::GetFeeTypeOne($data);
    }
}elseif ($_REQUEST["p"] == "type_del"){
    
        $data["id"] = $_REQUEST["id"];
		$result = borrowFeeClass::DeleteFeeType($data);
    	$msg = array("借款费用类型删除成功","",$_A['query_url_all']."&p=type");
    	
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "borrow";
		$admin_log["type"] = "fee_type";
		$admin_log["operating"] = $_REQUEST["p"];
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
}


$_A["sub_dir"] = "borrow.fee.tpl";
?>