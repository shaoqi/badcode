<?php
/******************************
 * $File: borrow.flag.inc.php
 * $Description: 借款类型文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问


require_once("borrow.flag.php");//类名

check_rank("borrow_flag");//设置权限
//修改
if ($_REQUEST["p"] == "edit" || $_REQUEST["p"] == "new"){
   if ($_POST['name']!=""){
    
        $var = array("name","title","nid","status","style","fileurl","upfiles_id","remark","order");
       	$data = post_var($var);
    	$_G['upimg']['file'] = "pic";
		$_G['upimg']['mask_status'] = 0;
		$_G['upimg']['code'] = "borrow";
		$_G['upimg']['type'] = "flag";
		$_G['upimg']['user_id'] = $_G['user_id'];
		$_G['upimg']['article_id'] = "0";
		$pic_result = $upload->upfile($_G['upimg']);
		if (is_array($pic_result)){
	      	$data["upfiles_id"] = $pic_result[0]['upfiles_id'];
		}
        if ($_POST["id"]==""){
            $result = borrowFlagClass::AddFlag($data);
    	     if ($result>0){
    			$msg = array("属性添加成功","",$_A['query_url_all']);
    		}
        }else{
            $data["id"] = $_POST["id"];
    		$result = borrowFlagClass::UpdateFlag($data);
    		if ($result>0){
    			$msg = array("属性修改成功","",$_A['query_url_all']);
    		}
		}
        if ($msg==""){
    		$msg = array($MsgInfo[$result]);
        }
        
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "borrow";
		$admin_log["type"] = "flag";
		$admin_log["operating"] = $_REQUEST["p"];
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
    } elseif ($_REQUEST["p"] == "edit"){
        $data = array("id"=>$_REQUEST["id"]);
        $_A['borrow_flag_result'] = borrowFlagClass::GetFlagOne($data);
    }
}elseif ($_REQUEST["p"] == "del"){
    
        $data["id"] = $_REQUEST["id"];
		$result = borrowFlagClass::DeleteFlag($data);
    	$msg = array("属性删除成功","",$_A['query_url_all']);
    	
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "borrow";
		$admin_log["type"] = "flag";
		$admin_log["operating"] = $_REQUEST["p"];
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
}

$_A["sub_dir"] = "borrow.flag.tpl";
?>