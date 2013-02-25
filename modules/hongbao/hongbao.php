<?php
/******************************
 * $File: hongbao.inc.php
 * $Description: 红包文件
 * $Author: ada
 * $Time:2012-12-11
 * $Update:
 * $UpdateDate:
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

$_A['list_purview']["hongbao"]["name"] = "红包管理";
$_A['list_purview']["hongbao"]["result"]["hongbao_list"] = array("name"=>"红包列表","url"=>"code/hongbao/list");

require_once("hongbao.class.php");//类名

//check_rank("hongbao_list");//检查权限
//修改
if ($_REQUEST["p"] == "edit" || $_REQUEST["p"] == "new"){
	
   if ($_POST['name']!=""){
     $var = array("name","nid","status","order","type_id","money","percent","available_time","explode_time","mode");

       	$data = post_var($var);
        if ($_POST["id"]==""){
            $result = hongbaoClass::Add($data);
    	     if ($result>0){
    			$msg = array("红包添加成功","",$_A['query_url_all']);
    		}
        }else{
            $data["id"] = $_POST["id"];
    		$result = hongbaoClass::Update($data);
    		if ($result>0){
    			$msg = array("红包修改成功","",$_A['query_url_all']);
    		}
		}
        if ($msg==""){
    		$msg = array($MsgInfo[$result]);
        }
        
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "hongbao";
		$admin_log["type"] = "fee";
		$admin_log["operating"] = $_REQUEST["p"];
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
    } elseif ($_REQUEST["p"] == "edit"){
        $data = array("id"=>$_REQUEST["id"]);
        $_A['hongbao_result'] = hongbaoClass::GetOne($data);
    }
}elseif ($_REQUEST["p"] == "del"){
    
        $data["id"] = $_REQUEST["id"];
		$result = hongbaoClass::Delete($data);
    	$msg = array("红包删除成功","",$_A['query_url_all']);
    	
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "hongbao";
		$admin_log["type"] = "fee";
		$admin_log["operating"] = $_REQUEST["p"];
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
		
}elseif ($_REQUEST["p"] == "type_edit" || $_REQUEST["p"] == "type_new"){
	if ($_POST['name']!=""){
     $var = array("name","nid","status","desc");
       	$data = post_var($var); 
        if ($_POST["id"]==""){
            $result = hongbaoClass::AddType($data);
    	     if ($result>0){
    			$msg = array("红包类型添加成功","",$_A['query_url_all']."&p=type");
    		}
        }else{
            $data["id"] = $_POST["id"];
    		$result = hongbaoClass::UpdateType($data);
    		if ($result>0){
    			$msg = array("红包类型修改成功","",$_A['query_url_all']."&p=type");
    		}
		}
        if ($msg==""){
    		$msg = array($MsgInfo[$result]);
        }
        
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "hongbao";
		$admin_log["type"] = "fee_type";
		$admin_log["operating"] = $_REQUEST["p"];
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
    } elseif ($_REQUEST["p"] == "type_edit"){
        $data = array("id"=>$_REQUEST["id"]);
        $_A['hongbao_type_result'] = hongbaoClass::GetTypeOne($data);
    }
    
}elseif ($_REQUEST["p"] == "type_del"){
    
        $data["id"] = $_REQUEST["id"];
		$result = hongbaoClass::DeleteType($data);
    	$msg = array("红包类型删除成功","",$_A['query_url_all']."&p=type");
    	
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "hongbao";
		$admin_log["type"] = "fee_type";
		$admin_log["operating"] = $_REQUEST["p"];
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
}


$_A["sub_dir"] = "hongbao.tpl";
?>