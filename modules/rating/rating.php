<?php
/******************************
 * $File: rating.php
 * $Description: 
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$_A['list_purview']["rating"]["name"] = "用户资料";
$_A['list_purview']["rating"]["result"]["rating_list"] = array("name"=>"用户管理","url"=>"code/rating/list");
$_A['list_purview']["rating"]["result"]["rating_educations"] = array("name"=>"教育","url"=>"code/rating/educations");
$_A['list_purview']["rating"]["result"]["rating_job"] = array("name"=>"工作","url"=>"code/rating/job");
$_A['list_purview']["rating"]["result"]["rating_house"] = array("name"=>"房产","url"=>"code/rating/house");
$_A['list_purview']["rating"]["result"]["rating_company"] = array("name"=>"公司管理","url"=>"code/rating/company");
$_A['list_purview']["rating"]["result"]["rating_contact"] = array("name"=>"联系方式","url"=>"code/rating/contact");
$_A['list_purview']["rating"]["result"]["rating_list"] = array("name"=>"用户管理","url"=>"code/rating/list");

require_once("rating.class.php");

/**
 * 学历
**/

if ($_A['query_type'] == "educations" ){
	if (isset($_POST['name'])){
		$msg = check_valicode();
		if ($msg==""){
			$var = array("username","status","name","degree","in_year","professional");
			$data = post_var($var);
			if ($_POST['id']!=""){
				$data['id'] = $_POST['id'];
				$result = ratingClass::UpdateEducations($data);
				if ($result>0){
					$msg = array($MsgInfo["rating_educations_update_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "update";
			}else{
				$result = ratingClass::AddEducations($data);
				if ($result>0){
					$msg = array($MsgInfo["rating_educations_add_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "add";
			}
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "rating";
			$admin_log["type"] = "educations";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = ratingClass::GetEducationsOne($data);
		if (is_array($result)){
			$_A["rating_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	
	}elseif($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$result = ratingClass::DelEducations($data);
		if ($result>0){
			$msg = array($MsgInfo["rating_educations_del_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "rating";
		$admin_log["type"] = "educations";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}elseif($_REQUEST['examine']!=""){
		if ($_POST['status']!=""){
			$var = array("verify_remark","status");
			$data = post_var($var);
			$data['id'] = $_REQUEST['examine'];
			$data['verify_userid'] = $_G['user_id'];
			$result = ratingClass::CheckEducations($data);
			
			if ($result>0){
				$msg = array("操作成功","",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "rating";
			$admin_log["type"] = "educations";
			$admin_log["operating"] = "check";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}
}



/**
 * 学历
**/

elseif ($_A['query_type'] == "job" ){
	if (isset($_POST['name'])){
		$msg = check_valicode();
		if ($msg==""){
			$var = array("username","status","name","in_year","department","office","prover","prover_tel");
			$data = post_var($var);
			if ($_POST['id']!=""){
				$data['id'] = $_POST['id'];
				$result = ratingClass::UpdateJob($data);
				if ($result>0){
					$msg = array($MsgInfo["rating_job_update_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "update";
			}else{
				$result = ratingClass::AddJob($data);
				if ($result>0){
					$msg = array($MsgInfo["rating_job_add_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "add";
			}
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "rating";
			$admin_log["type"] = "job";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = ratingClass::GetJobOne($data);
		if (is_array($result)){
			$_A["rating_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	
	}elseif($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$result = ratingClass::DelJob($data);
		if ($result>0){
			$msg = array($MsgInfo["rating_job_del_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "rating";
		$admin_log["type"] = "job";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}elseif($_REQUEST['examine']!=""){
		if ($_POST['status']!=""){
			$var = array("verify_remark","status");
			$data = post_var($var);
			$data['id'] = $_REQUEST['examine'];
			$data['verify_userid'] = $_G['user_id'];
			$result = ratingClass::CheckJobOne($data);
			
			if ($result>0){
				$msg = array("操作成功","",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "rating";
			$admin_log["type"] = "job";
			$admin_log["operating"] = "check";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}
}




/**
 * 房产资料
**/

elseif ($_A['query_type'] == "house" ){
	if (isset($_POST['name'])){
		$msg = check_valicode();
		if ($msg==""){
			$var = array("username","status","in_year","address","areas","repay","holder1","right1","holder2","right2","load_year","repay_month","balance","bank");
			$data = post_var($var);
			if ($_POST['id']!=""){
				$data['id'] = $_POST['id'];
				$result = ratingClass::UpdateHouse($data);
				if ($result>0){
					$msg = array($MsgInfo["rating_house_update_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "update";
			}else{
				$result = ratingClass::AddHouse($data);
				if ($result>0){
					$msg = array($MsgInfo["rating_house_add_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "add";
			}
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "rating";
			$admin_log["type"] = "house";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = ratingClass::GetHouseOne($data);
		if (is_array($result)){
			$_A["rating_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	
	}elseif($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$result = ratingClass::DelHouse($data);
		if ($result>0){
			$msg = array($MsgInfo["rating_house_del_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "rating";
		$admin_log["type"] = "house";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}elseif($_REQUEST['examine']!=""){
		if ($_POST['status']!=""){
			$var = array("verify_remark","status");
			$data = post_var($var);
			$data['id'] = $_REQUEST['examine'];
			$data['verify_userid'] = $_G['user_id'];
			$result = ratingClass::CheckHouseOne($data);
			
			if ($result>0){
				$msg = array("操作成功","",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "rating";
			$admin_log["type"] = "house";
			$admin_log["operating"] = "check";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}
}


/**
 * 单位资料
**/
elseif ($_A['query_type'] == "company" ){
	if (isset($_POST['name'])){
		$msg = check_valicode();
		if ($msg==""){
			$var = array("username","status","type","industry","office","rank","worktime1","worktime2","workyear","tel","address","weburl","name");
			$data = post_var($var);
			if ($_POST['id']!=""){
				$data['id'] = $_POST['id'];
				$result = ratingClass::UpdateCompany($data);
				if ($result>0){
					$msg = array($MsgInfo["rating_company_update_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "update";
			}else{
				$result = ratingClass::AddCompany($data);
				if ($result>0){
					$msg = array($MsgInfo["rating_company_add_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "add";
			}
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "rating";
			$admin_log["type"] = "company";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = ratingClass::GetCompanyOne($data);
		if (is_array($result)){
			$_A["rating_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	
	}elseif($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$result = ratingClass::DelCompany($data);
		if ($result>0){
			$msg = array($MsgInfo["rating_company_del_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "rating";
		$admin_log["type"] = "company";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}elseif($_REQUEST['examine']!=""){
		if ($_POST['status']!=""){
			$var = array("verify_remark","status");
			$data = post_var($var);
			$data['id'] = $_REQUEST['examine'];
			$data['verify_userid'] = $_G['user_id'];
			$result = ratingClass::CheckCompanyOne($data);
			
			if ($result>0){
				$msg = array("操作成功","",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "rating";
			$admin_log["type"] = "house";
			$admin_log["operating"] = "check";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}
}

/**
 * 联系方式
**/
elseif ($_A['query_type'] == "contact" ){
	if (isset($_POST['linkman2'])){
		$msg = check_valicode();
		if ($msg==""){
			$var = array("username","status","linkman2","relation2","phone2","linkman3","relation3","phone3","qq","wangwang","msn","other");
			$data = post_var($var);
			if ($_POST['id']!=""){
				$data['id'] = $_POST['id'];
				$result = ratingClass::UpdateContact($data);
				if ($result>0){
					$msg = array($MsgInfo["rating_contact_update_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "update";
			}else{
				$result = ratingClass::AddContact($data);
				if ($result>0){
					$msg = array($MsgInfo["rating_contact_add_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "add";
			}
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "rating";
			$admin_log["type"] = "company";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = ratingClass::GetContactOne($data);
		if (is_array($result)){
			$_A["rating_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	
	}elseif($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$result = ratingClass::DelContact($data);
		if ($result>0){
			$msg = array($MsgInfo["rating_contact_del_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "rating";
		$admin_log["type"] = "contact";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}elseif($_REQUEST['examine']!=""){
		if ($_POST['status']!=""){
			$var = array("verify_remark","status");
			$data = post_var($var);
			$data['id'] = $_REQUEST['examine'];
			$data['verify_userid'] = $_G['user_id'];
			$result = ratingClass::CheckContactOne($data);
			
			if ($result>0){
				$msg = array("操作成功","",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "rating";
			$admin_log["type"] = "contact";
			$admin_log["operating"] = "check";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}
}

/**
 * 个人资料
**/
elseif ($_A['query_type'] == "info" ){
	if (isset($_POST['address'])){
		$msg = check_valicode();
		if ($msg==""){
			$var = array("username","status","sex","marry","children","income","dignity","is_car","phone","address");
			$data = post_var($var);
			if ($_POST['id']!=""){
				$data['id'] = $_POST['id'];
				$result = ratingClass::UpdateInfo($data);
				if ($result>0){
					$msg = array($MsgInfo["rating_info_update_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "update";
			}else{
				$result = ratingClass::AddInfo($data);
				if ($result>0){
					$msg = array($MsgInfo["rating_info_add_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "add";
			}
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "rating";
			$admin_log["type"] = "info";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = ratingClass::GetInfoOne($data);
		if (is_array($result)){
			$_A["rating_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	
	}elseif($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$result = ratingClass::DelInfo($data);
		if ($result>0){
			$msg = array($MsgInfo["rating_info_del_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "rating";
		$admin_log["type"] = "info";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}elseif($_REQUEST['examine']!=""){
		if ($_POST['status']!=""){
			$var = array("verify_remark","status");
			$data = post_var($var);
			$data['id'] = $_REQUEST['examine'];
			$data['verify_userid'] = $_G['user_id'];
			$result = ratingClass::CheckInfoOne($data);
			
			if ($result>0){
				$msg = array("操作成功","",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "rating";
			$admin_log["type"] = "info";
			$admin_log["operating"] = "check";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}
}

/**
 * 资产状况
**/
elseif ($_A['query_type'] == "assets" ){
	if (isset($_POST['name'])){
		$msg = check_valicode();
		if ($msg==""){
			$var = array("username","status","name","assetstype","account","other");
			$data = post_var($var);
			if ($_POST['id']!=""){
				$data['id'] = $_POST['id'];
				$result = ratingClass::UpdateAssets($data);
				if ($result>0){
					$msg = array($MsgInfo["rating_assets_update_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "update";
			}else{
				$result = ratingClass::AddAssets($data);
				if ($result>0){
					$msg = array($MsgInfo["rating_assets_add_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "add";
			}
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "rating";
			$admin_log["type"] = "assets";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = ratingClass::GetAssetsOne($data);
		if (is_array($result)){
			$_A["rating_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	
	}elseif($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$result = ratingClass::DelAssets($data);
		if ($result>0){
			$msg = array($MsgInfo["rating_assets_del_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "rating";
		$admin_log["type"] = "assets";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}elseif($_REQUEST['examine']!=""){
		if ($_POST['status']!=""){
			$var = array("verify_remark","status");
			$data = post_var($var);
			$data['id'] = $_REQUEST['examine'];
			$data['verify_userid'] = $_G['user_id'];
			$result = ratingClass::CheckAssetsOne($data);
			
			if ($result>0){
				$msg = array("操作成功","",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "rating";
			$admin_log["type"] = "assets";
			$admin_log["operating"] = "check";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}
}

/**
 * 财务状况
**/
elseif ($_A['query_type'] == "finance" ){
	if (isset($_POST['name'])){
		$msg = check_valicode();
		if ($msg==""){
			$var = array("username","status","name","use_type","account","other","type");
			$data = post_var($var);
			if ($_POST['id']!=""){
				$data['id'] = $_POST['id'];
				$result = ratingClass::UpdateFinance($data);
				if ($result>0){
					$msg = array($MsgInfo["rating_finance_update_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "update";
			}else{
				$result = ratingClass::AddFinance($data);
				if ($result>0){
					$msg = array($MsgInfo["rating_finance_add_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "add";
			}
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "rating";
			$admin_log["type"] = "finance";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = ratingClass::GetFinanceOne($data);
		if (is_array($result)){
			$_A["rating_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	
	}elseif($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$result = ratingClass::DelFinance($data);
		if ($result>0){
			$msg = array($MsgInfo["rating_finance_del_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "rating";
		$admin_log["type"] = "finance";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}elseif($_REQUEST['examine']!=""){
		if ($_POST['status']!=""){
			$var = array("verify_remark","status");
			$data = post_var($var);
			$data['id'] = $_REQUEST['examine'];
			$data['verify_userid'] = $_G['user_id'];
			$result = ratingClass::CheckFinanceOne($data);
			
			if ($result>0){
				$msg = array("操作成功","",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "rating";
			$admin_log["type"] = "finance";
			$admin_log["operating"] = "check";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}
}
?>