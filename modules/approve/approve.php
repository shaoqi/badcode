<?php
/******************************
 * $File: approve.php
 * $Description: 认证后台处理文件
 * $Author: ahui 
 * $Time:2011-11-09
 * $Update:None 
 * $UpdateDate:None 
 * Copyright(c) 2012 by dycms.net. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

require_once("approve.class.php");


$_A['list_purview']["approve"]["name"] = "认证管理";
$_A['list_purview']["approve"]["result"]["approv_realname"] = array("name"=>"实名认证","url"=>"code/approve/realname");
$_A['list_purview']["approve"]["result"]["approv_edu"] = array("name"=>"学历认证","url"=>"code/approve/edu");
$_A['list_purview']["approve"]["result"]["approv_sms"] = array("name"=>"手机认证","url"=>"code/approve/sms");
$_A['list_purview']["approve"]["result"]["approv_video"] = array("name"=>"视频认证","url"=>"code/approve/video");
$_A['list_purview']["approve"]["result"]["approv_all"] = array("name"=>"总体认证","url"=>"code/approve/all");


/**
 * 实名认证
**/

if ($_A['query_type'] == "realname" ){
    check_rank("approv_realname");//检查权限
	if ($_POST['type']=="user_id"){
		$var = array("username","user_id","email");
		$data = post_var($var);
		$data["limit"] = "all";
		$result = approveClass::GetUserid($data);
		if ($result>0){
			echo "<script>location.href='{$_A['query_url_all']}&user_id={$result}'</script>";
		}else{
			$msg = array($MsgInfo[$result],"",$_A['query_url_all']);
		}
	}
	
	
	elseif (isset($_POST['realname'])){
		$msg = check_valicode();
		if ($msg==""){
			$var = array("realname","card_id","user_id","sex");
			$data = post_var($var);

			$_G['upimg']['code'] = "approve";
			$_G['upimg']['type'] = "realname";
			$_G['upimg']['user_id'] = $data["user_id"];
			$_G['upimg']['article_id'] = $data["user_id"];
			
			$_G['upimg']['file'] = "card_pic1";
			$pic_result = $upload->upfile($_G['upimg']);

			if ($pic_result!=false){
				$data["card_pic1"] = $pic_result[0]["upfiles_id"];
			}
			
			$_G['upimg']['file'] = "card_pic2";
			$pic_result = $upload->upfile($_G['upimg']);
			if ($pic_result!=false){
				$data["card_pic2"] = $pic_result[0]["upfiles_id"];
			}
		
			$result = approveClass::UpdateRealname($data);
			if ($result>0){
				$msg = array($MsgInfo["approve_realname_update_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			$admin_log["operating"] = "update";
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "approve";
			$admin_log["type"] = "realname";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}
	
	
	elseif ($_REQUEST['user_id']!=""){
		$data["user_id"] = $_REQUEST['user_id'];
		$result = approveClass::GetRealnameOne($data);
		if (is_array($result)){
			$_A["approve_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result],"",$_A['query_url_all']);
		}
	}
	
	elseif($_REQUEST['examine']!=""){
		if ($_POST['status']!=""){
			$msg = check_valicode();
			if ($msg==""){
				$var = array("verify_remark","status");
				$data = post_var($var);
				$data['user_id'] = $_REQUEST['examine'];
				$data['verify_userid'] = $_G['user_id'];
				
				$result = approveClass::CheckRealname($data);
				
				if ($result>0){
					usersvipClass::regvip($data['user_id']);
					$msg = array("操作成功","",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				//加入管理员操作记录
				$admin_log["user_id"] = $_G['user_id'];
				$admin_log["code"] = "approve";
				$admin_log["type"] = "realname";
				$admin_log["operating"] = "check";
				$admin_log["article_id"] = $result>0?$result:0;
				$admin_log["result"] = $result>0?1:0;
				$admin_log["content"] =  $msg[0];
				$admin_log["data"] =  $data;
				usersClass::AddAdminLog($admin_log);
			}
		}else{
			$data["user_id"] = $_REQUEST['examine'];
			$result = approveClass::GetRealnameOne($data);
			if (is_array($result)){
				$_A["approve_result"] = $result;
			}else{
				$msg = array($MsgInfo[$result],"",$_A['query_url_all']);
			}
		}
	}
	
	elseif($_REQUEST['id5']!=""){
		if ($_POST['verify_remark']!=""){
			$msg = check_valicode();
			if ($msg==""){
			
				$data['verify_id5_remark'] =$_POST['verify_remark'];
				$data['user_id'] = $_REQUEST['id5'];
				$data['verify_id5_userid'] = $_G['user_id'];
				if ($_G['system']['con_id5_status']==0){
					$result = "approve_realname_id5_close";
				}else{
					$result = approveClass::CheckRealnameId5($data);
				}
				if ($result>0){
					$msg = array("操作成功","",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				//加入管理员操作记录
				$admin_log["user_id"] = $_G['user_id'];
				$admin_log["code"] = "approve";
				$admin_log["type"] = "realname";
				$admin_log["operating"] = "checkid5";
				$admin_log["article_id"] = $result>0?$result:0;
				$admin_log["result"] = $result>0?1:0;
				$admin_log["content"] =  $msg[0];
				$admin_log["data"] =  $data;
				usersClass::AddAdminLog($admin_log);
			}
		}		
	}elseif($_GET['type']=='excel'){
		$_sql = " where 1=1 ";
		
		//搜索用户id
        if (IsExiest($_GET['user_id'])!=false) {
            $_sql .= " and p1.user_id ='{$_GET['user_id']}'";
        }
		
		
		if (IsExiest($_GET['realname'])!=false) {
            $_sql .= " and p1.realname like '%".urldecode($_GET['realname'])."%'";
        }
		
		if (IsExiest($_GET['card_id'])!=false) {
            $_sql .= " and p1.card_id like '%{$_GET['card_id']}%'";
        }
		
		//搜索用户名
		if (IsExiest($_GET['username'])!=false) {
            $_sql .= " and p2.username = '".urldecode($_GET['username'])."'";
        }
		
		//搜索用户名
		if (IsExiest($_GET['status'])!=false) {
            $_sql .= " and p1.status = '{$_GET['status']}'";
        }
				//判断添加时间开始
		if (IsExiest($_GET['dotime1']) != false){
			if (!empty($_GET['dotime1'])){
				$_sql .= " and p1.addtime > ".get_mktime($_GET['dotime1']);
			}
		}
		//判断添加时间结束
		if (IsExiest($_GET['dotime2'])!=false){
			if (!empty($_GET['dotime2'])){
				$_sql .= " and p1.addtime < ".get_mktime($_GET['dotime2']);
			}
		}
			$_select = " p1.user_id,p1.realname,p2.username,p1.card_id,p1.sex,p1.status,p1.addtime,p1.verify_time";
			$sql = "select SELECT from `{approve_realname}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id SQL ORDER LIMIT";
			$row = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array($_select, $_sql,$_order, ''), $sql));
			$title = ['会员id','真实姓名','用户名称','身份证号码','性别','审核状态','添加时间','审核时间'];
			$_data=[];
			$status = ['未审核','审核通过','审核不通过'];
			$sex = [1=>'男',2=>'女'];
			foreach($row as $key=>$value){
				$_data[$key]=[$value['user_id'],$value['realname'],$value['username'],$value['card_id'],in_array($value['sex'],$sex)?$sex[$value['sex']]:$value['sex'],$status[$value['status']],date('Y-m-d H:i:s',$value['addtime']),date('Y-m-d H:i:s',$value['verify_time'])];
			}
			exportData("实名认证列表",$title,$_data);
			exit;
	}
}


/**
 * id5设置
**/



elseif ($_A['query_type'] == "realname_id5set"){
	
	
}

elseif ($_A['query_type'] == "all"){
	
	
}





/**
 * 手机短信
**/

elseif ($_A['query_type'] == "sms"  ){
    check_rank("approv_sms");//检查权限
	if (isset($_POST['phone'])){
		$msg = check_valicode();
		if ($msg==""){
			$var = array("username","phone");
			$data = post_var($var);
			if ($_POST['id']!=""){
				$data['id'] = $_POST['id'];
				$result = approveClass::UpdateSms($data);
				if ($result>0){
					$msg = array($MsgInfo["approve_sms_update_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result],"",$_A['query_url_all']);
				}
				$admin_log["operating"] = "update";
			}else{
				$result = approveClass::AddSms($data);
				if ($result>0){
					$msg = array($MsgInfo["approve_sms_add_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result],"",$_A['query_url_all']);
				}
				$admin_log["operating"] = "add";
			}
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "approve";
			$admin_log["type"] = "sms";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = approveClass::GetSmsOne($data);
		if (is_array($result)){
			$_A["approve_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	
	}elseif($_REQUEST['examine']!=""){
		if ($_POST['status']!=""){
			$var = array("verify_remark","status","credit");
			$data = post_var($var);
			$data['id'] = $_REQUEST['examine'];
			$data['verify_userid'] = $_G['user_id'];
			$result = approveClass::CheckSms($data);
			
			if ($result>0){
				$msg = array($MsgInfo["approve_sms_check_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "approve";
			$admin_log["type"] = "sms";
			$admin_log["operating"] = "check";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}else{
			$data['id'] = $_REQUEST['examine'];
			$result = approveClass::GetSmsOne($data);
			if (is_array($result)){
				$_A["approve_result"] = $result;
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}
	}
}

//短信发送记录
elseif ($_A['query_type'] == "sms_log"){
    check_rank("approv_sms");//检查权限
	if ($_POST['contents']!=""){
		$msg = check_valicode();
		if ($msg==""){
			$var = array("contents","status","username","phone","user_id1","user_id2");
			$data = post_var($var);
			$data['type'] = "system";
			$result = approveClass::AddSmslogGroup($data);
			
			if ($result>0){
				$msg = array($MsgInfo["approve_sms_send_group_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "approve";
			$admin_log["type"] = "sms";
			$admin_log["operating"] = "send_group";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}elseif ($_REQUEST['view']!=""){
		$data['id'] = $_REQUEST['view'];
		$result = approveClass::GetSmslogOne($data);
		if (is_array($result)){
			$_A["approve_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	}

}

elseif ($_A['query_type'] == "sms_set"){
    check_rank("approv_sms");//检查权限
	//系统参数
	if (isset($_POST['con_sms_status'])){
		$var = array("con_sms_status","con_sms_url","con_sms_text","con_sms_utf_status");
		$data = post_var($var);
		$result = adminClass::UpdateSystem($data);
		$msg = array($MsgInfo["admin_info_success"]);
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "system";
		$admin_log["type"] = "sms";
		$admin_log["operating"] = "set";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}
	
}


/**
 * id5认证列表
**/

elseif ($_A['query_type'] == "edu" ){
    check_rank("approv_edu");//检查权限
if ($_POST['type']=="user_id"){
		$var = array("username","user_id","email");
		$data = post_var($var);
		$data["limit"] = "all";
		$result = approveClass::GetUserid($data);
		if ($result>0){
			echo "<script>location.href='{$_A['query_url_all']}&user_id={$result}'</script>";
		}else{
			$msg = array($MsgInfo[$result],"",$_A['query_url_all']);
		}
	}elseif (isset($_POST['graduate'])){
		$msg = check_valicode();
		if ($msg==""){
			$var = array("user_id","graduate","speciality","degree","enrol_date","graduate_date");
			$data = post_var($var);
			$_G['upimg']['code'] = "approve";
			$_G['upimg']['type'] = "edu";
			$_G['upimg']['user_id'] = $data["user_id"];
			$_G['upimg']['article_id'] = $data["user_id"];
			
			$_G['upimg']['file'] = "edu_pic";
			$pic_result = $upload->upfile($_G['upimg']);
			if ($pic_result!=false){
				$data["edu_pic"] = $pic_result[0]["upfiles_id"];
			}
			
			$result = approveClass::UpdateEdu($data);
			if ($result>0){
				$msg = array($MsgInfo["approve_edu_update_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			$admin_log["operating"] = "update";
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "approve";
			$admin_log["type"] = "edu";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}
	
	
	elseif ($_REQUEST['user_id']!=""){
		$data["user_id"] = $_REQUEST['user_id'];
		$result = approveClass::GetEduOne($data);
		if (is_array($result)){
			$_A["approve_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result],"",$_A['query_url_all']);
		}
	}
	
	elseif($_REQUEST['examine']!=""){
		if ($_POST['status']!=""){
			$msg = check_valicode();
			if ($msg==""){
				$var = array("verify_remark","status");
				$data = post_var($var);
				$data['user_id'] = $_REQUEST['examine'];
				$data['verify_userid'] = $_G['user_id'];
				$result = approveClass::CheckEdu($data);
				
				if ($result>0){
					$msg = array("操作成功","",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				//加入管理员操作记录
				$admin_log["user_id"] = $_G['user_id'];
				$admin_log["code"] = "approve";
				$admin_log["type"] = "edu";
				$admin_log["operating"] = "check";
				$admin_log["article_id"] = $result>0?$result:0;
				$admin_log["result"] = $result>0?1:0;
				$admin_log["content"] =  $msg[0];
				$admin_log["data"] =  $data;
				usersClass::AddAdminLog($admin_log);
			}
		}else{
			$data["user_id"] = $_REQUEST['examine'];
			$result = approveClass::GetEduOne($data);
			if (is_array($result)){
				$_A["approve_result"] = $result;
			}else{
				$msg = array($MsgInfo[$result],"",$_A['query_url_all']);
			}
		}
	}
	
	elseif($_REQUEST['id5']!=""){
		if ($_POST['verify_remark']!=""){
			$msg = check_valicode();
			if ($msg==""){
			
				$data['verify_id5_remark'] =$_POST['verify_remark'];
				$data['user_id'] = $_REQUEST['id5'];
				$data['verify_id5_userid'] = $_G['user_id'];
				if ($_G['system']['con_id5_edu_status']==0){
					$result = "approve_edu_id5_close";
				}else{
					$result = approveClass::CheckEduId5($data);
				}
				if ($result>0){
					$msg = array("操作成功","",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				//加入管理员操作记录
				$admin_log["user_id"] = $_G['user_id'];
				$admin_log["code"] = "approve";
				$admin_log["type"] = "edu";
				$admin_log["operating"] = "checkid5";
				$admin_log["article_id"] = $result>0?$result:0;
				$admin_log["result"] = $result>0?1:0;
				$admin_log["content"] =  $msg[0];
				$admin_log["data"] =  $data;
				usersClass::AddAdminLog($admin_log);
			}
		}		
	}

}
elseif ($_A['query_type'] == "edu_id5" ){
check_rank("approv_edu");//检查权限

}
elseif ($_A['query_type'] == "edu_set" ){
//系统参数
check_rank("approv_edu");//检查权限
	if (isset($_POST['con_id5_edu_status'])){
		$var = array("con_id5_edu_status","con_id5_edu_fee");
		$data = post_var($var);
		$data['code'] = "id5";
		$result = adminClass::UpdateSystem($data);
		$msg = array($MsgInfo["admin_info_success"]);
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "system";
		$admin_log["type"] = "approve";
		$admin_log["operating"] = "edu";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}

}

/**
 * id5认证列表
**/

elseif ($_A['query_type'] == "video" ){
    check_rank("approv_video");//检查权限
	if ($_POST['type']=="user_id"){
		$var = array("username","user_id","email");
		$data = post_var($var);
		$data["limit"] = "all";
		$result = approveClass::GetUserid($data);
		if ($result>0){
			echo "<script>location.href='{$_A['query_url_all']}&user_id={$result}'</script>";
		}else{
			$msg = array($MsgInfo[$result],"",$_A['query_url_all']);
		}
	}
	
	
	elseif (isset($_POST['remark'])){
		$msg = check_valicode();
		if ($msg==""){
			$var = array("user_id","remark");
			$data = post_var($var);
			$result = approveClass::UpdateVideo($data);
			if ($result>0){
				$msg = array($MsgInfo["approve_video_update_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			$admin_log["operating"] = "update";
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "approve";
			$admin_log["type"] = "video";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}
	
	
	elseif ($_REQUEST['user_id']!=""){
		$data["user_id"] = $_REQUEST['user_id'];
		$result = approveClass::GetVideoOne($data);
		if (is_array($result)){
			$_A["approve_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result],"",$_A['query_url_all']);
		}
	}
	
	elseif($_REQUEST['examine']!=""){
		if ($_POST['status']!=""){
			$msg = check_valicode();
			if ($msg==""){
				$var = array("verify_remark","status","verify_credit");
				$data = post_var($var);
				$data['user_id'] = $_REQUEST['examine'];
				$data['verify_userid'] = $_G['user_id'];
				$result = approveClass::CheckVideo($data);
				
				if ($result>0){
					$msg = array("操作成功","",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				//加入管理员操作记录
				$admin_log["user_id"] = $_G['user_id'];
				$admin_log["code"] = "approve";
				$admin_log["type"] = "video";
				$admin_log["operating"] = "check";
				$admin_log["article_id"] = $result>0?$result:0;
				$admin_log["result"] = $result>0?1:0;
				$admin_log["content"] =  $msg[0];
				$admin_log["data"] =  $data;
				usersClass::AddAdminLog($admin_log);
			}
		}else{
			$data["user_id"] = $_REQUEST['examine'];
			$result = approveClass::GetVideoOne($data);
			if (is_array($result)){
				$_A["approve_result"] = $result;
			}else{
				$msg = array($MsgInfo[$result],"",$_A['query_url_all']);
			}
		}
	}

}

?>