<?
if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问


if($_REQUEST['type']=="pwd"){

	if(isset($_REQUEST['id'])){
		$id = urldecode($_REQUEST['id']);
		$data = explode(",",authcode(trim($id),"DECODE"));
		$user_id = $data[0];
		$start_time = $data[1];
		if ($user_id==""){
			$updatepwd_msg = "您的操作有误，请勿乱操作";
		}elseif (time()>$start_time+60*60){
			$updatepwd_msg = "此链接已经过期，请重新申请";
		}else{
			$result = usersClass::GetUsers(array("user_id"=>$user_id));
			if ($result == false){
				$updatepwd_msg = "您的操作有误，请勿乱操作";
			}else{
				$_U['user_result'] =  $result;
				if ($_POST['password']!=""){
					if ($_POST['password']!=$_POST['password1']){
						$pwd_msg = "两次密码不一样";
					}else{
						$_data['user_id'] = $user_id;
						$_data['password'] = $_POST['password'];
						$result = usersClass::UpdatePassword($_data);
						if ($result>0){
							$updatepwd_msg = "密码修改成功";
						}else{
							$pwd_msg = $MsgInfo[$result];
						}
					}
					$_U['pwd_msg'] =  $pwd_msg;
				}
			}
		}
	}else{
		$updatepwd_msg = "您的操作有误，请勿乱操作";
	}
	if ($updatepwd_msg !=""){
		echo "<script>alert('{$updatepwd_msg}');location.href='/'</script>";
	}
}


if($_REQUEST['type']=="paypwd"){
	if(isset($_REQUEST['id'])){
		$id = urldecode($_REQUEST['id']);
		$data = explode(",",authcode(trim($id),"DECODE"));
		$user_id = $data[0];
		$start_time = $data[1];
		if ($user_id==""){
			$updatepwd_msg = "您的操作有误，请勿乱操作";
		}elseif (time()>$start_time+60*60){
			$updatepwd_msg = "此链接已经过期，请重新申请";
		}else{
			$result = usersClass::GetUsers(array("user_id"=>$user_id));
			if ($result == false){
				$updatepwd_msg = "您的操作有误，请勿乱操作";
			}else{
				$_U['user_result'] =  $result;
				if ($_POST['password']!=""){
					if ($_POST['password']!=$_POST['password1']){
						$pwd_msg = "两次密码不一样";
					}else{
						$_data['user_id'] = $user_id;
						$_data['password'] = $_POST['password'];
						$result = usersClass::UpdatePayPassword($_data);
						if ($result>0){
							$updatepwd_msg = "密码修改成功";
						}else{
							$pwd_msg = $MsgInfo[$result];
						}
					}
					$_U['pwd_msg'] =  $pwd_msg;
				}
			}
		}
	}else{
		$updatepwd_msg = "您的操作有误，请勿乱操作";
	}
	if ($updatepwd_msg !=""){
		echo "<script>alert('{$updatepwd_msg}');location.href='/'</script>";
	}
}

elseif(isset($_POST['email'])){
	$getpwd_msg = "";
	$var = array("email","valicode");
	$data = post_var($var);
	$msg = check_valicode();
	
	if ($msg!=""){
		$getpwd_msg = "验证码不正确";
		$msg = "";

	}elseif ($data['email']==""){

		$getpwd_msg = "邮箱地址不能为空";
	}elseif ($data['valicode']==""){
		$getpwd_msg = "验证码不能为空";
	}else{
		$result = usersClass::GetUsers($data);
		if ($result==false){
			$getpwd_msg = "邮箱不存在";
		}else{
			$data['user_id'] = $result['user_id'];
			$data['email'] = $result['email'];
			$data['webname'] = $_G['system']['con_webname'];
			if ($_POST['pwdtype']==1){
				$data['title'] = "用户取回登录密码";
				$data['msg'] = GetpwdMsg($data);
			}else{
				$data['title'] = "用户取回支付密码";
				$data['msg'] = GetPaypwdMsg($data);
			}
			$data['type'] = "reg";
			if (isset($_SESSION['sendpwd_time']) && $_SESSION['sendpwd_time']+60*2>time()){
				$getpwd_msg =  "请2分钟后再次请求。";
			}else{
				$result = usersClass::SendEmail($data);
				if ($result) {
					$_SESSION['sendpwd_time'] = time();
					$getpwd_msg =  "信息已发送到{$data['email']}，请注意查收您邮箱的邮件";
					echo "<script>alert('{$getpwd_msg}');location.href='/'</script>";
				}
				else{
					$getpwd_msg =  "发送失败，请跟管理员联系";
				}
			}
		}
	}
	$_U['getpwd_msg'] = $getpwd_msg;
}

elseif($_REQUEST['type']=="phone"){
  if(isset($_POST['phone'])){
		$getpwd_msg = "";
		$var = array("phone","pwdtype","valicode");
		$data = post_var($var);
		$msg = check_valicode();
		if ($msg!=""){
			$getpwd_msg = "验证码不正确";
			$msg = "";
		}
		elseif ($data['phone']==""){
			$getpwd_msg = "手机号码不能为空";
		}elseif ($data['valicode']==""){
			$getpwd_msg = "验证码不能为空";
		}else{
			$_data['phone'] = $data['phone'];
			$_data['phone_status'] = 1;
			$result = usersClass::GetUsersInfo($_data);
			if ($result==false){
				$getpwd_msg = "手机不存在或者此手机还没认证";
			}else{
				if (isset($_SESSION['sendpwd_time']) && $_SESSION['sendpwd_time']+60*2>time()){
					$getpwd_msg =  "请2分钟后再次请求。";
				}else{
					//发送短信
					require_once("modules/approve/approve.class.php");
					$code =rand(1000,9999);
					if ($data['pwdtype']==1){
						$send_sms['contents'] ="您取回登录密码验证码为：{$code}"."[{$_G['system']['webname']}]";
						$type = 'getpwd';
					}else{
						$send_sms['contents'] ="您取回支付密码验证码为：{$code}"."[{$_G['system']['webname']}]";
						$type = 'getpaypwd';
					}
					$send_sms['user_id'] = $result['user_id'];
					$send_sms['code'] = $code;
					$send_sms['type'] = $type;
					$result = approveClass::SendSMS($send_sms);
					echo "<script>location.href='/?user&q=getpwd&type=phone_code&phone={$data['phone']}&pwdtype={$data['pwdtype']}'</script>";
				}
			}
		}
		$_U['getpwd_msg'] = $getpwd_msg;
	}
}



elseif($_REQUEST['type']=="phone_code"){

	if(isset($_POST['code'])){
		$getpwd_msg = "";
		$var = array("code");
		$data = post_var($var);
		if ($_REQUEST['phone']==""){
			$getpwd_msg = "手机号码不能为空";
		}elseif ($data['code']==""){
			$getpwd_msg = "验证码不能为空";
		}else{
			$_data['phone'] = $data['phone'];
			$_data['phone_status'] = 1;
			$result = usersClass::GetUsersInfo($_data);
			if ($result==false){
				$getpwd_msg = "手机不存在或者此手机还没认证";
			}else{
				$_data['user_id'] = $result['user_id'];
				$_data['phone'] = $_REQUEST['phone'];
				$_data['code'] = $data['code'];
				if ($_REQUEST['pwdtype']==1){
					$_data['type'] = "getpwd";
				}else{
					$_data['type'] = "getpaypwd";
				}
				require_once("modules/approve/approve.class.php");
				$result = approveClass::CheckSmsCode($_data);
				
				if ($result>0){
					$active_id = urlencode(authcode($result['user_id'].",".time(),"ENCODE"));
					if ($_REQUEST['pwdtype']==1){
						$_url = "/?user&q=getpwd&type=pwd&id={$active_id}";
					}else{
						$_url = "/?user&q=getpwd&type=paypwd&id={$active_id}";
					}
					echo "<script>location.href='{$_url}'</script>";
				}else{
					$getpwd_msg = "验证码不正确";
				}
			}
		}
		$_U['getpwd_msg'] = $getpwd_msg;
	}
}




elseif($_REQUEST['type']=="question"){

	if(isset($_POST['username'])){
		$getpwd_msg = "";
		$var = array("username","valicode");
		$data = post_var($var);
		$msg = check_valicode();
		if ($msg!=""){
			$getpwd_msg = "验证码不正确";
			$msg = "";
		}
		elseif ($_REQUEST['username']==""){
			$getpwd_msg = "用户名不能为空";
		}elseif ($data['valicode']==""){
			$getpwd_msg = "验证码不能为空";
		}else{
			$_data['username'] = $data['username'];
			$result = usersClass::GetUsers($_data);
			if ($result==false){
				$getpwd_msg = "用户不存在";
			}else{
				$__data['user_id'] = $result['user_id'];
				$result = usersClass::GetUsersInfo($__data);
				if ($result['question']!="" && $result['answer']!=""){
					$_url = "/?user&q=getpwd&type=answer&username={$data['username']}&pwdtype={$_POST['pwdtype']}&question=".$result['question']."";
					echo "<script>location.href='{$_url}'</script>";
				}else{
					$getpwd_msg = "你还没设定安全问题";
				}
			}
		}
		$_U['getpwd_msg'] = $getpwd_msg;
	}
}




elseif($_REQUEST['type']=="answer"){

	if(isset($_POST['answer'])){
		$getpwd_msg = "";
		$var = array("answer");
		$data = post_var($var);
		if ($_REQUEST['username']==""){
			$getpwd_msg = "用户名不能为空";
		}else{
			$_data['username'] = $_REQUEST['username'];
			$result = usersClass::GetUsers($_data);
			if ($result==false){
				$getpwd_msg = "用户不存在";
			}else{
				$__data['user_id'] = $result['user_id'];
				$result = usersClass::GetUsersInfo($__data);
				if ($result['answer']==$data['answer']){
					$active_id = urlencode(authcode($result['user_id'].",".time(),"ENCODE"));
					if ($_REQUEST['pwdtype']==1){
						$_url = "/?user&q=getpwd&type=pwd&id={$active_id}";
					}else{
						$_url = "/?user&q=getpwd&type=paypwd&id={$active_id}";
					}
					echo "<script>location.href='{$_url}'</script>";
				}else{
					$getpwd_msg = "答案不正确";
				}
			}
		}
		$_U['getpwd_msg'] = $getpwd_msg;
	}
}

$title = '取回密码';
$template = 'user_getpwd.html';

?>