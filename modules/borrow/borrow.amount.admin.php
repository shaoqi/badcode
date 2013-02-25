<?php
/******************************
 * $File: borrow.amount.admin.php
 * $Description: 借款额度后台管理
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

$_A['list_purview']["borrow"]["result"]["borrow_amount"] = array("name"=>"借款额度","url"=>"code/borrow/amount");

require_once("borrow.amount.php");//类名

check_rank("borrow_amount");//设置权限

//添加额度
 if ($_REQUEST["p"] == "new"){
   if ($_POST['type']=="user_id"){
		$var = array("username","user_id","email");
		$data = post_var($var);
		$data["limit"] = "all";
		$result = usersClass::GetUserid($data);
		if ($result>0){
			echo "<script>location.href='{$_A['query_url_all']}&p=new&user_id={$result}'</script>";
		}else{
			$msg = array($MsgInfo[$result]);
		}
	}elseif($_REQUEST['user_id']!=""){
        if ($_POST['amount_account']!=""){
       	    $var = array("amount_type","amount_style","oprate","amount_account","content");
			$data = post_var($var);
			$data['status'] = 0;
	       	$data["user_id"] = $_REQUEST['user_id'];
            $data["type"] = "webapply";
			$result = borrowAmountClass::AddAmountApply($data);
			if ($result>0){
				$msg = array($MsgInfo["amount_apply_add_success"],"",$_A['query_url_all']."&p=apply");
			}else{
				$msg = array($MsgInfo[$result]);
			}
			$admin_log["operating"] = "add";
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "borrow";
			$admin_log["type"] = "amount_apply";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
        }else{
	       	$data["user_id"] = $_REQUEST['user_id'];
    		$result = usersClass::GetUsers($data);
    		if (is_array($result)){
    			$_A["users_result"] = $result;
    		}else{
    			$msg = array("用户不存在");
            }
       }
	}
}

elseif ($_REQUEST["p"] == "check"){
    if ($_POST['status']!=""){
		$var = array("verify_remark","verify_contents","status","account","user_id","id","nid");
		$data = post_var($var);
		$data['verify_userid'] = $_G['user_id'];
		$result = borrowAmountClass::CheckAmountApply($data);
		if ($result>0){
			$msg = array("操作成功","",$_A['query_url']."/amount&p=apply");
		}else{
			$msg = array($MsgInfo[$result]);
		}
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "borrow";
		$admin_log["type"] = "amount_apply";
		$admin_log["operating"] = "check";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
		
	}else{
		
		$data["id"] = $_REQUEST['id'];
		$result = borrowAmountClass::GetAmountApplyOne($data);
		if (is_array($result)){
			$_A["amount_apply_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result],"",$_A['query_url_all']."&p=apply");
		}
	}
}



elseif ($_REQUEST["p"] == "type_edit"){
   if ($_POST['id']!=""){
        $var = array("id","title","status","remark","description","once_status");
		$data = post_var($var);
		
		$result = borrowAmountClass::UpdateAmountType($data);
		if ($result>0){
			$msg = array("借款额度类型修改成功","",$_A['query_url_all']."&p=type");
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "borrow";
		$admin_log["type"] = "amount";
		$admin_log["operating"] = "type_edit";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
    } 
}
?>