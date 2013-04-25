<?php
/******************************
 * $File: approve.inc.php
 * $Description: 认证用户中心管理
 * $Author: ahui 
 * $Time:2011-11-09
 * $Update:None 
 * $UpdateDate:None 
 * Copyright(c) 2012 by dycms.net. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

require_once("approve.class.php");

if ($_U['query_type']=="realname"){
	if (isset($_POST['realname'])){
		// 如果是通过loan 过来的需要先进行邮箱认证才可以
		if($_POST['loan']==1 && empty($_G['user_info']['email_status'])){
			$msg = array("实名认证之前，请先进行邮箱认证","","/renzheng/index.html?type=email");
		}else{
			require_once(ROOT_PATH."/modules/account/account.class.php");
			$var = array("realname","sex","card_id");
			$data = post_var($var);
			
			$data['user_id'] = $_G['user_id'];
			$data['status'] = 0;
			$account=accountClass::GetOne($data);
			$type = array("image/jpeg","image/gif","image/pjpeg");
			$type1 = $_FILES["card_pic1"]["type"];
			$type2 = $_FILES["card_pic2"]["type"];
			$fee=isset($_G['system']['con_realname_fee'])?$_G['system']['con_realname_fee']:5;
			if ($account['balance']<$fee && $fee!=0){
				if($fee!=0){
					$msg = array("余额不足5元，请先充值","",$url);
				}			
			}elseif((!in_array($type1, $type) && $type1!='') || (!in_array($type2, $type) && $type2!='')){			
				$msg = array("上传图片的格式应为jpg.gif","",$url);	
			}elseif($_FILES["card_pic1"]["size"]>524288 || $_FILES["card_pic2"]["size"]>524288){			
				$msg = array("图片大小不能超过512KB","",$url);
			}else{
				//$data['realname'] = iconv('UTF-8', 'GB2312',$data['realname']);
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
				$borrow_type = $_POST['borrow_type'];
				if ($result>0){
					
					$log_info["user_id"] = $data["user_id"];//操作用户id
					$log_info["nid"] = "realname_approve_".$data['user_id'];//订单号
					$log_info["account_web_status"] = 1;//
					$log_info["account_user_status"] = 1;//
					$log_info["code"] = "user";//
					$log_info["code_type"] = "user_realname_approve";//
					$log_info["code_nid"] = $data['user_id'];//
					$log_info["money"] = $fee;//操作金额
					$log_info["income"] = 0;//收入
					$log_info["expend"] =$log_info["money"];//支出
					$log_info["balance_cash"] = -$$log_info["money"];//可提现金额
					$log_info["balance_frost"] =0;//不可提现金额
					$log_info["frost"] = 0;//冻结金额
					$log_info["await"] = 0;//待收金额
					$log_info["type"] = "realname_approve";//类型
					$log_info["to_userid"] = $data['user_id'];//付给谁
					$log_info["remark"] = "姓名认证添加成功，扣除{$log_info["money"]}元";
					if($fee!=0){
						$result = accountClass::AddLog($log_info);
					}
					if($_POST['loan']==1){
						$msg = array("姓名认证添加成功，请等待管理员审核","","/renzheng/index.html?type=success");
					}else{
						$msg = array("姓名认证添加成功，请等待管理员审核","",$url);
					}
				}else{
					if($_POST['loan']==1){
						$msg = array($MsgInfo[$result],"","/renzheng/index.html?type=realname");
					}else{
						$msg = array($MsgInfo[$result],"",$_U['query_url']."/realname");
					}
				}
				if ($_POST['type']=="ajax"){
					echo $msg[0];
					exit;
				}
			}
		}
	}else{
		$_U['realname_result'] = approveClass::GetRealnameOne(array("user_id"=>$_G['user_id']));
		
	}
}

elseif ($_U['query_type']=="education"){
	if (isset($_POST['graduate']) && $_POST['graduate']!=""){	
		$var = array("user_id","graduate","speciality","degree","enrol_date","graduate_date");
		$data = post_var($var);
		$data["user_id"] = $_G['user_id'];
		$account=accountClass::GetOne($data);
        $fee=($_G['system']['con_edu_fee']>=0)?$_G['system']['con_edu_fee']:2;
		/* if ($account['balance']<2){
			$msg = array("余额不足2元，请先充值","",$url);	
		}else{ */
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
				$log_info["user_id"] = $data["user_id"];//操作用户id
				$log_info["nid"] = "edu_approve_".$data['user_id']."_".time();//订单号
                $log_info["account_web_status"] = 1;//
                $log_info["account_user_status"] = 1;//
                $log_info["code"] = "user";//
    			$log_info["code_type"] = "user_edu_approve";//
    			$log_info["code_nid"] = $data['user_id'];//
				$log_info["money"] = $fee;//操作金额
				$log_info["edu_pic"] = $data["edu_pic"];
				$log_info["income"] = 0;//收入
				$log_info["expend"] = $fee;//支出
				$log_info["balance_cash"] = -$fee;//可提现金额
				$log_info["balance_frost"] =0;//不可提现金额
				$log_info["frost"] = 0;//冻结金额
				$log_info["await"] = 0;//待收金额
				$log_info["type"] = "edu_approve";//类型
				$log_info["to_userid"] = $data['user_id'];//付给谁
				$log_info["remark"] = "学历认证添加成功，扣除{$fee}元";
				$result = accountClass::AddLog($log_info);
				$msg = array("学历认证添加成功，请等待管理员审核","",$url);	
			}else{
				$msg = array($MsgInfo[$result],"",$_U['query_url']."/realname");
			}
		//}
	}else{
		$_U['edu_result'] = approveClass::GetEduOne(array("user_id"=>$_G['user_id']));
		
	}
}
elseif ($_U['query_type']=="video"){
	if(isset($_POST['submit'])){
		$data['status'] = 0;
		$data['user_id'] = $_G['user_id'];
		$result = approveClass::UpdateVideo($data);
		if ($result==true){
			$msg = array("提交申请成功","","index.php?user&q=code/approve/video");
		}else{
			$msg = array($reuslt);
		}
	}else{
		$data['user_id'] = $_G['user_id'];
		$_U['video_result'] = approveClass::GetVideoOne($data);
	}
}
elseif ($_U['query_type']=="phone"){

	if(isset($_POST['sms_code'])){
		$data['code'] = $_POST['sms_code'];
		$data['phone'] = $_POST['phone_new'];
		$data['type'] = "smscode";
		$data['user_id'] = $_G['user_id'];
		$result = approveClass::CheckSmsCode($data);
		if ($result>0){
			if ($_REQUEST['_type']=="borrow"){
				echo 1; exit;
			}else{
				$msg = array("认证成功","","index.php?user&q=code/approve/phone_status");
			}
			
		}elseif ($MsgInfo[$result]!=""){
			if ($_REQUEST['_type']=="borrow"){
					echo 0; exit;
				}else{
					$msg = array($MsgInfo[$result]);
				}
			
		}else{
			if ($_REQUEST['_type']=="borrow"){
					echo 0; exit;
				}else{
					$msg = array("验证码错误");
				}
		}
	}elseif(isset($_POST['phone'])){
		if ($_SESSION['smscode_time']+60>time() && $_SESSION['smscode_phone']==$_POST['phone'])
		{
			$msg = array("请过1分钟后再申请");
		}else{
			$data['phone'] = $_POST['phone'];
			$data['user_id'] = $_G['user_id'];
			$result = approveClass::AddSms($data);
			if ($result>0){
				$data['status'] = 1;
				$data['user_id'] = $_G['user_id'];
				$data['type'] = "smscode";
				$data['code'] = rand(100000,999999);
				$data['contents'] = "尊敬的融易融用户您好：您本次手机验证码为".$data['code']."，该验证码3分钟后失效，祝您投资愉快。如有疑问请拨打电话4000852600进行咨询。";
				$data['contents'] = iconv("GBK","UTF-8",$data['contents']);
				$result = approveClass::SendSMS($data);
				$_SESSION['smscode_time'] = time();
				$_SESSION['smscode_othertime'] = $_SESSION['smscode_time']-time();
				$_SESSION['smscode_phone'] = $data['phone'];
				$msg = array(1);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			
		}
		if ($_REQUEST['style']=="ajax"){
			echo $msg[0];
			exit;
		}
	}elseif($_REQUEST['style']=="cancel"){
		if ($_SESSION['smscancel_time']+60*2>time())
		{
		
		}else{
			
			$data['status'] = 1;
			$data['user_id'] = $_G['user_id'];
			$data['type'] = "smscancel";
			$data['code'] = rand(100000,999999);
			$data['contents'] = "您的手机验证码为:".$data['code'];
			$result = approveClass::SendSMS($data);
			$_SESSION['smscancel_time'] = time();
		}
	}else{
		$data['user_id'] = $_G['user_id'];
		$_U['phone_result'] = approveClass::GetSmsOne($data);
	}
}

//邮箱认证
elseif ($_U['query_type'] == "email"){
	$_U['site_name'] = "邮箱认证";

	if (isset($_POST['email']) && $_POST['email']!="" ){
		$data['user_id'] = $_G['user_id'];
		$data['email'] = $_POST['email'];
		$result = usersClass::CheckEmail($data);

		if ($result==true){
			$result = usersClass::UpdateEmail($data);
			if ($result == false){
				$msg = array($result);	
			}else{
				$data['username'] = $_G['user_result']['username'];
				$data['webname'] = $_G['system']['con_webname'];
				$data['title'] = "注册邮件确认";
				if($_REQUEST['type']=='loan'){
					$data['loan'] = 1;
				}
				$data['msg'] = RegEmailMsg($data);
				$data['type'] = "reg";
				
				if (isset($_SESSION['sendemail_time']) && $_SESSION['sendemail_time']+60*2>time()){
					$msg = array("请2分钟后再次请求。","",$url);
				}else{
					$result = usersClass::SendEmail($data);
					if ($result==true) {
						$_SESSION['sendemail_time'] = time();
						if($_POST['loan']==1){
							$emailurl = "http://mail.".str_replace("@","",strstr( $data['email'],"@"));	
							echo "<script>alert('激活信息已经发送到您的邮箱，请注意查收');
							location.href='".$emailurl."'</script>";
						}else{
							$msg = array("激活信息已经发送到您的邮箱，请注意查收。","",$url);
						}
					}else{
						$msg = array("发送失败，请跟管理员联系。","",$url);
					}
				}
			}
		}else{
			if($_POST['loan']==1){
			$msg = array("你重新填写的邮箱已经存在","","/renzheng/index.html?type=email");	
			}else{
			$msg = array("你重新填写的邮箱已经存在","",$url);	
			}
		}
	}
}elseif ($_U['query_type'] == "checkemail"){
	if (isset($_POST['email']) && $_POST['email']!="" ){
		$data['email'] = trim($_POST['email']);
		if($_G['user_info']['email']==$data['email']){
			echo 3;
			exit;
		}else{
			$result = usersClass::CheckEmail($data);
			if($result==false){
				echo 1;exit;
			}else{
				$pattern= "/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/";
				$is_email = preg_match($pattern,$data['email']);
				if($is_email==1){
					echo 3;exit;
				}else{
					echo 2;exit;
				}			
			}
		}
	}		
}
$template = "user_approve.html";
?>
