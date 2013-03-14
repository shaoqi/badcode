<?
/******************************
 * $File: reg.php
 * $Description: 注册
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问
require_once(ROOT_PATH."modules/approve/approve.class.php");
require_once(ROOT_PATH."modules/credit/credit.class.php");
//判断邮箱参数存在
if (isset($_POST['username'])){
	$var = array("email","username","password","phone");
	$data = post_var($var);
	$data['username'] = iconv("UTF-8","gb2312",$data['username']);
	//判断推荐人是否已经存在
	$data_info['invite_userid'] = $_POST['invite_user_id'];
	if (IsExiest($_POST['tuijian_userid'])!=false){
		$username = iconv("UTF-8","gb2312",$_POST['tuijian_userid']);
		$result = usersClass::GetUsers(array("username"=>$username));
		
		if ($result!=false){
			$data_info['invite_userid'] = $result['user_id'];
		}else{
			echo "推荐人不存在";exit;
			$msg = array("users_reg_invite_username_not_exiest","","/?user&q=reg");
		}
	}
	if(empty($_POST['phone_code'])){
		echo '请输入手机邀请码';
		exit;
	}
	if(usersClass::CheckEmail(['email'=>$data['email']])){
		echo '请输入正确的电子邮箱';
		exit;
	}
	if(!usersClass::CheckPhone(['phone'=>$data['phone']])){
		echo '请输入正确的手机号码';
		exit;
	}
	$result = approveClass::CheckSmsCode(['user_id'=>0,'type'=>'smscode','phone'=>$data['phone'],'code'=>$_POST['phone_code']]);

	if($result!=0 || 'approve_sms_check_yes'!=$result){
		echo $MsgInfo[$result];
		exit;
	}
		
	if ($msg == ""){
		$result = usersClass::AddUsers($data);
		if ($result>0 && is_numeric($result)){
			$_result = usersClass::GetUsersTypeCheck();
			$data_info['phone'] = $data['phone'];
			$data_info['phone_status'] = 1;
			$data_info['status'] = 1;
			$data_info['user_id'] = $result;
			usersClass::UpdateUsersInfo($data_info);
			approveClass::AddSms($data_info);
			
			if ($data_info['invite_userid']!=""){
				//$credit_type=creditClass::GetTypeOne(array("id"=>22));
				$sql="insert into `{users_friends}` set `user_id`={$result},`friends_userid`={$data_info['invite_userid']},`addtime` = '".time()."',`addip` = '".ip_address()."',status=1";
				$mysql->db_query($sql);
				$_sql="insert into `{users_friends}` set `user_id`={$data_info['invite_userid']},`friends_userid`={$result},`addtime` = '".time()."',`addip` = '".ip_address()."',status=1";
				$mysql->db_query($_sql);
				$_sql="insert into `{users_friends_invite}` set `user_id`={$data_info['invite_userid']},`friends_userid`=$result,`addtime` = '".time()."',`addip` = '".ip_address()."',status=1,type=1";
				$mysql->db_query($_sql);
				$credit_log['user_id'] = $data_info['invite_userid'];
				$credit_log['nid'] = "invite";
				$credit_log['code'] = "borrow";
				$credit_log['type'] = "approve";
				$credit_log['addtime'] = time();
				$credit_log['article_id'] =$data_info['invite_userid'];
				//creditClass::ActionCreditLog($credit_log);
			}
				$mail_data['username'] = $data['username'];
				$mail_data['webname'] = $_G['system']['con_webname'];
				$mail_data['title'] = "注册邮件账户激活";
				$mail_data['user_id']=$result;
				$mail_data['email'] = $data['email'];
				$mail_data['type'] = "reg";
				$mail_data['msg'] = RegEmailMsg($mail_data);
				usersClass::SendEmail($mail_data);
			/*//如果注册成功，则发送邮箱进行确认
			if ($_G["system"]["con_reg_email"]!=1){
				
				if ($_G['module']['ucenter_status']==1 ){
					$_data['email'] = $data['email'];
					$_data['username'] = $data['username'];
					$_data['password'] = $data['password'];
					$_data['user_id'] = $result;
					$ucenter_login = ucenterClass::UcenterLogin($_data);
					
					if ($ucenter_login==""){
						$msg = array("论坛同步失败，请跟管理员联系");
					}else{
						echo $ucenter_login;
					}
					$mysql = new Mysql($db_config);
					
				}
			
				$active_id = urlencode(authcode($result.",".time(),"ENCODE"));
				$reg_active_url = "?user&q=active&id={$active_id}";
				$email_info['user_id'] = $result;
				$email_info['email'] = $data['email'];
				$email_info['title'] = $MsgInfo["users_add_reg_email_title"];
                $_reg_content["user_id"] = $result;
                $_reg_content["username"] = $data['username'];
                $_reg_content["email"] = $data['email'];
                $_reg_content["webname"] = $_G['system']['con_webname'];
				$email_info["msg"] = RegEmailMsg($_reg_content);
				$email_info['type'] = "reg";
				usersClass::SendEmail($email_info);
				$url="/?user&q=reg&type=email";
			}else{
				$url="/?user";
			}*/
			
			
			//加入cookie
			
			$_cookie['user_id'] = $result;
			$_cookie['cookie_status'] = $_G['system']['con_cache_type'];
            $_cookie["cookie_id"] = $_G["system"]["con_cookie_id"];
			$_cookie['time'] = 7*60*60*24;
			SetCookies($_cookie);
            
			if ($_POST['type']=="ajax"){
            $msg = array(1);
            }else{
			$msg = array($MsgInfo["users_add_success"].'且激活信息已经发送到您的邮箱，请注意查收。',"","/?user&q=reg&type=email");
            }
		}else{
			$msg = array($MsgInfo[$result]);
		}
	}
	if ($_POST['type']=="ajax"){
	   echo $msg[0];
        exit;
	}
}else{
	$_U['sendemail'] = $_G['user_result']['email'];
	$emailurl = "http://mail.".str_replace("@","",strstr($_G['user_result']['email'],"@"));
	$_U['emailurl'] = $emailurl;
	$template = 'users_reg.html';
}


/**
 * 注册就送vip
 *
 * 2013-1-23-17:00 到 2013-2-24:00
 */
function regvip($user_id){
	global $mysql;
	$first_time = time();
	$end_time = strtotime("+12 month",$first_time);
    $sql = "update `{users_vip}` set status=1,kefu_userid=0,years=1,first_date='".$first_time."',end_date='".$end_time."' where user_id='$user_id'";
    $result = $mysql->db_query($sql);
    $remind['nid'] = "vip_success";
    $remind['receive_userid'] = $user_id;
    $remind['remind_nid'] =  "vip_success_".$user_id."_".$first_time;
    $remind['article_id'] = $user_id;
    $remind['code'] = "users";
    $remind['title'] = "申请VIP成功";
    $remind['content'] = "尊敬的用户恭喜您申请VIP成功。";
    remindClass::sendRemind($remind);
}