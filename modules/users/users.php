<?php
/******************************
 * $File: users.php
 * $Description: 用户后台处理文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$_A['list_purview']["users"]["name"] = "用户管理";
$_A['list_purview']["users"]["result"]["users_list"] = array("name"=>"用户列表","url"=>"code/users/list");
$_A['list_purview']["users"]["result"]["users_new"] = array("name"=>"添加用户","url"=>"code/users/new");
$_A['list_purview']["users"]["result"]["users_info"] = array("name"=>"用户信息","url"=>"code/users/info");
$_A['list_purview']["users"]["result"]["users_type"] = array("name"=>"用户类型","url"=>"code/users/type");
$_A['list_purview']["users"]["result"]["users_vip"] = array("name"=>"VIP管理","url"=>"code/users/vip");
$_A['list_purview']["users"]["result"]["users_manage"] = array("name"=>"理财师","url"=>"code/users/manage");

/**
 * 添加用户
**/
if ($_A['query_type'] == "new"){
	if ($_POST['username']!=""){		
		$result = usersClass::AddUsers($_POST);
		if ($result>0){
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "users";
			$admin_log["type"] = "action";
			$admin_log["operating"] = "add";
			$admin_log["article_id"] = $result;
			$admin_log["result"] = 1;
			$admin_log["content"] = str_replace(array( '#username#'), array($_POST['username']), $MsgInfo["users_add_success_msg"]);
			usersClass::AddAdminLog($admin_log);
			
			$msg = array($MsgInfo["users_add_success"]);
			
		}else{
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "users";
			$admin_log["type"] = "action";
			$admin_log["operating"] = "add";
			$admin_log["article_id"] = 0;
			$admin_log["result"] = 0;
			$admin_log["content"] =  str_replace(array( '#username#'), array($_POST['username']), $MsgInfo["users_add_error_msg"])."(".$MsgInfo[$result].")";
			usersClass::AddAdminLog($admin_log);
			
			$msg = array($MsgInfo[$result]);
		}
	}	
}

elseif ($_A['query_type'] == "info_view" || $_A['query_type'] == "info_edit"){
	if ($_POST['user_id']!=""){
		$var = array("user_id","niname","sex","birthday","status","province","city","area","question","answer");
		$data = post_var($var);
		$result = usersClass::UpdateUsersInfo($data);
		
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "users";
		$admin_log["type"] = "info";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  "用户修改成功";
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
			
		$msg = array("修改成功");
	}else{
	
$_A['_user_result'] = usersClass::GetUsersInfo(array("user_id"=>$_REQUEST['user_id']));
}


}
elseif ($_A['query_type'] == "edit"){
	
	if ($_POST['password']!="" || $_POST['paypassword']!=""){
		$data['user_id'] = $_POST['user_id'];
		$msg = "";
		if ($_POST['password']!=""){
			if($_POST['password'] != $_POST['password1']){
				$msg = array($MsgInfo['users_password_error']);
			}else{
				$data['password'] = $_POST['password'];
				$result = usersClass::UpdatePassword($data);
				if ($result>0){
				
				}else{
					$msg = array($MsgInfo[$result]);
				}
			}
		}
		
		if ($msg=="" && $_POST['paypassword']!=""){
			if($_POST['paypassword'] != $_POST['paypassword1']){
				$msg = array($MsgInfo['users_password_error']);
			}else{
				$data['paypassword'] = $_POST['paypassword'];
				$result = usersClass::UpdatePayPassword($data);
				if ($result>0){
				
				}else{
					$msg = array($MsgInfo[$result]);
				}
			}
		}
		/* if ($msg=="" ){
			$data['email'] = $_POST['email'];
			$result = usersClass::UpdateEmail($data);
			if ($result>0){
				$msg = array("操作成功");
			}else{
				$msg = array($MsgInfo[$result]);
			}
		} */
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "users";
		$admin_log["type"] = "edit";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}else{
		$_A['users_result'] = usersClass::GetUsers(array("user_id"=>$_REQUEST['user_id']));
	}
}

elseif ($_A['query_type'] == "vip" ){
	check_rank("users_vip");//检查权限
	if ($_REQUEST['action']=="view"){
		if (isset($_POST['status'])){
			$var = array("status","verify_remark","user_id","kefu_userid","years");
			$data = post_var($var);
			$data['verify_time'] = time();
			$data['user_id'] = $_REQUEST['user_id'];
			$data['verify_userid'] = $_G['user_id'];
			$user_id = $_REQUEST['user_id'];
			$result = usersvipClass::GetUsersVip(array("user_id"=>$data["user_id"]));
			if($result['status']==1){
				$result = usersvipClass::UpdateUsersVipKefu(array("user_id"=>$data["user_id"],"kefu_userid"=>$data['kefu_userid']));
				$msg = array("客服修改成功");
				$admin_log["operating"] = "update";
			}else{
				$result = usersvipClass::CheckUsersVip($data);//更新
				$admin_log["operating"] = "check";
				$msg = array("VIP用户审核成功","","{$_A['query_url']}/vip");
			}
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "users";
			$admin_log["type"] = "vip";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}else{
			$result = UsersvipClass::GetUsersVip(array("user_id"=>$_REQUEST['user_id']));//0表示用户组的类别，1表示管理组的类型
			$_A['vip_result'] = $result;
		}
	}elseif($_REQUEST['action']=="add"){
		$username = isset($_POST['uname'])?$_POST['uname']:'';
		$username = iconv('utf-8','gbk',$username);
		if(empty($username)){
			echo '用户名不能为空';
			exit;
		}
		$result = usersClass::GetUserid(['username'=>$username]);
		if($result=='users_empty'){
			echo '用户不存在';
			exit;
		}
		usersvipClass::regvip($result);
		echo '用户升级为vip';
		$admin_log = [];
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "users";
		$admin_log["type"] = "vip";
		$admin_log["article_id"] = $result;
		$admin_log["result"] = 1;
		$admin_log["content"] =  '升级VIP用户';
		$admin_log["data"] = '';
		usersClass::AddAdminLog($admin_log);
		exit;
	}

}



elseif ($_A['query_type'] == "type" ){
	if (isset($_POST['name'])){
			$var = array("name","nid","remark","order","checked");
			$data = post_var($var);
			if ($_POST['id']!=""){
				$data['id'] = $_POST['id'];
				$result = usersClass::UpdateUsersType($data);
				if ($result>0){
					$msg = array($MsgInfo["users_type_update_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "update";
			}else{
				$result = usersClass::AddUsersType($data);
				if ($result>0){
					$msg = array($MsgInfo["users_type_add_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "add";
			}
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "users";
			$admin_log["type"] = "type";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
	}elseif ($_REQUEST['checked']!=""){
		$data['id'] = $_REQUEST['checked'];
		$result = usersClass::UpdateUsersTypeChecked($data);
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "users";
		$admin_log["type"] = "type";
		$admin_log["operating"] = "checked";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = usersClass::GetUsersTypeOne($data);
		
		if (is_array($result)){
			$_A["users_type_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	
	}elseif($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$result = usersClass::DelUsersType($data);
		if ($result>0){
			$msg = array($MsgInfo["users_type_del_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "users";
		$admin_log["type"] = "type";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}
	
}
//管理员管理
elseif ($_A['query_type'] == "admin" ){
	check_rank("system_users_admin");//检查权限
	if ($_POST['type']=="user_id"){
		$var = array("username","user_id","email");
		$data = post_var($var);
		$result = usersClass::GetUserid($data);
		if ($result>0){
			echo "<script>location.href='{$_A['query_url_all']}&user_id={$result}'</script>";
		}else{
			$msg = array($MsgInfo[$result],"",$_A['query_url_all']);
		}
	}
	elseif ($_POST['action']=="update" ){
		$var = array("user_id","adminname","password","type_id","qq","phone","province","city","remark");
		$data = post_var($var);
		$result = usersClass::UpdateUsersAdmin($data);
		if ($result>0){
			$msg = array($MsgInfo["users_admin_update_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result],"",$_A['query_url_all']);
		}
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "users";
		$admin_log["type"] = "admin";
		$admin_log["operating"] = "update";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}
		
	elseif ($_REQUEST['action']=="del"){
		$data['user_id'] = $_REQUEST['user_id'];
		$result = usersClass::DeleteUsersAdmin($data);
		if ($result >0){
			$msg = array($MsgInfo["users_admin_del_success"],"","{$_A['query_url_all']}");
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "users";
		$admin_log["type"] = " admin";
		$admin_log["operating"] = 'del';
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}
	
	elseif ($_REQUEST['user_id']!=""){
		$data['user_id'] = $_REQUEST['user_id'];
		$result = usersClass::GetUsers($data);
		if (is_array($result)){
			$_A['users_result'] = $result;
			$_A['users_admin_result'] = usersClass::GetUsersAdminOne($data);
		}else{
			$msg = array($MsgInfo[$result],"",$_A['query_url_all']);
		}
	}
	
		
}

//管理员记录
elseif ($_A['query_type'] == "admin_log" ){
	check_rank("system_users_admin_log");//检查权限

}

//修改用户详细信息
elseif ($_A['query_type'] == "viewinfo" ){	
    require_once(ROOT_PATH."modules/rating/rating.class.php");
	if($_REQUEST['edit']=='basic'){
		$var = array("realname","card_id","phone","sex","rating_birthday_year","rating_birthday_mouth","rating_birthday_day","marry","edu","income","remark");
		$data = post_var($var);		
		$data['user_id'] = $_REQUEST['user_id'];
		$data['status'] = "";
		$result = ratingClass::GetInfoOne($data);
		if (is_array($result)){
			$_result = ratingClass::UpdateInfo($data);
		}else{
			$_result = ratingClass::AddInfo($data);
		}
		if($_POST['invite_username']!=''){
			$result = usersClass::GetUsers(array("username"=>$_POST['invite_username']));
			if ($result!=false){
				$data_info['invite_userid'] = $result['user_id'];
			}else{
				$msg = array($MsgInfo["邀请人不存在"],"",$_A['query_url_all']."&user_id=".$data['user_id']);
			}
			if ($data_info['invite_userid']!=""){
				$credit_type=creditClass::GetTypeOne(array("id"=>29));
				$sql="insert into `{users_friends}` set `user_id`={$data['user_id']},`friends_userid`={$data_info['invite_userid']},`addtime` = '".time()."',`addip` = '".ip_address()."',status=1";
				$mysql->db_query($sql);
				$_sql="insert into `{users_friends}` set `user_id`={$data_info['invite_userid']},`friends_userid`={$data['user_id']},`addtime` = '".time()."',`addip` = '".ip_address()."',status=1";
				$mysql->db_query($_sql);
				$_sql="insert into `{users_friends_invite}` set `user_id`={$data_info['invite_userid']},`friends_userid`={$data['user_id']},`credit`={$credit_type['value']},`addtime` = '".time()."',`addip` = '".ip_address()."',status=1,type=1";
				$mysql->db_query($_sql);
				$credit_log['user_id'] = $data_info['invite_userid'];
				$credit_log['nid'] = "invite";
				$credit_log['code'] = "borrow";
				$credit_log['type'] = "approve";
				$credit_log['addtime'] = time();
				$credit_log['article_id'] =$data_info['invite_userid'];
				//creditClass::ActionCreditLog($credit_log);
			}
		}
		
		if($_result==true){
			$msg = array("修改成功","",$_A['query_url_all']."&user_id=".$data['user_id']);
		}else{
			$msg = array($MsgInfo[$_result],"",$_A['query_url_all']."&user_id=".$data['user_id']);
		}
	}elseif($_REQUEST['edit']=='job'){
		$var = array("name","work_year","work_province","work_city","tel","reterence","reterence_tel");
		$data = post_var($var);
	    $data['user_id'] = $_G['user_id'];
		$data['status'] = 1;
		$result = ratingClass::GetJobOne($data);
			if (is_array($result)){
				$_result = ratingClass::UpdateJob($data);
			}else{
				$_result = ratingClass::AddJob($data);
			}
		if($_result==true){
			$msg = array("修改成功","",$_A['query_url_all']."&type=1&user_id=".$data['user_id']);
		}else{
			$msg = array($MsgInfo[$_result],"",$_A['query_url_all']."&type=1&user_id=".$data['user_id']);
		}
	}elseif($_REQUEST['edit']=='lianbao'){
		$var = array("name1","relationship5","tel1","name2","relationship6","tel2");
		$data = post_var($var);
		$data['user_id'] = $_G['user_id'];		
		$result = ratingClass::GetLianbaoOne($data);
			if (is_array($result)){
				$_result = ratingClass::UpdateLianbao($data);
			}else{
				$_result = ratingClass::AddLianbao($data);
			}
		if($_result==true){
			$msg = array("修改成功","",$_A['query_url_all']."&type=2&user_id=".$data['user_id']);
		}else{
			$msg = array($MsgInfo[$_result],"",$_A['query_url_all']."&type=2&user_id=".$data['user_id']);
		}
	}elseif($_REQUEST['edit']=='contact'){
		$var = array("live_address","live_tel","linkman2","relationship2","phone2","linkman3","relationship3","phone3");
		$data = post_var($var);
		$data['user_id'] = $_G['user_id'];
		$data['status'] = 1;
		$result = ratingClass::GetContactOne($data);
			if (is_array($result)){
				$_result = ratingClass::UpdateContact($data);
			}else{
				$_result = ratingClass::AddContact($data);
			}
		if($_result==true){
			$msg = array("修改成功","",$_A['query_url_all']."&type=3&user_id=".$data['user_id']);
		}else{
			$msg = array($MsgInfo[$_result],"",$_A['query_url_all']."&type=3&user_id=".$data['user_id']);
		}
	}elseif($_REQUEST['edit']=='finance'){
		$var = array("month_income","month_income_describe","month_pay","month_pay_describe","house","house_value","is_car","car_value","cangu","cangu_account","describe");
		$data = post_var($var);
		$data['user_id'] = $_G['user_id'];
		$data['status'] = 1;
		$result = ratingClass::GetFinanceOne($data);
			if (is_array($result)){
				$_result = ratingClass::UpdateFinance($data);
			}else{
				$_result = ratingClass::AddFinance($data);
			}
		if($_result==true){
			$msg = array("修改成功","",$_A['query_url_all']."&type=4&user_id=".$data['user_id']);
		}else{
			$msg = array($MsgInfo[$_result],"",$_A['query_url_all']."&type=4&user_id=".$data['user_id']);
		}
	}
	

}

//管理员类型
elseif ($_A['query_type'] == "admin_type" ){
	check_rank("system_users_admin_type");//检查权限
	if (isset($_POST['name'])){
		$var = array("name","nid","remark","order","module","purview");
		$data = post_var($var);
		if ($_POST['id']!=""){
			$data['id'] = $_POST['id'];
			$result = usersClass::UpdateAdminType($data);
			if ($result>0){
				$msg = array($MsgInfo["users_admin_type_update_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			$admin_log["operating"] = "update";
		}else{
			$result = usersClass::AddAdminType($data);
			if ($result>0){
				$msg = array($MsgInfo["users_admin_type_add_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			$admin_log["operating"] = "add";
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "users";
		$admin_log["type"] = "admin_type";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
		
	}elseif ($_REQUEST['action']=="edit"){
		$data['id'] = $_REQUEST['id'];
		$result = usersClass::GetAdminTypeOne($data);
		if (is_array($result)){
			$_A["admin_type_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	
	}elseif($_REQUEST['action']=="del"){
		$data['id'] = $_REQUEST['id'];
		$result = usersClass::DelAdminType($data);
		if ($result>0){
			$msg = array($MsgInfo["users_admin_type_del_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "users";
		$admin_log["type"] = "admin_type";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}
	
}elseif ($_A['query_type'] == "manage" ){	
	$result = usersClass::GetUserManageOne(array("user_id"=>$_REQUEST['check']));
	$_A['user_manage']  = $result;
	if(isset($_POST['submit']) && $_POST['submit']!=''){
		$var = array("status","verify_remark");		
        $data = post_var($var);
		$data['user_id'] = $_POST['user_id'];
		$result = usersClass::CheckUserManage($data);
		if($result>0){
			$msg = array("审核成功","",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
	}
}





?>