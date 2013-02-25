<?php
/******************************
 * $File: function.inc.php
 * $Description: 基本函数文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问


function GetPaypwdMsg($data = array()){
	global $mysql;
	$user_id = $data['user_id'];
	$username = $data['username'];
	$webname = $data['webname'];
	$email = $data['email'];
	$active_id = urlencode(authcode($user_id.",".time(),"ENCODE"));
	$_url = "http://{$_SERVER['HTTP_HOST']}/?user&q=code/users/getpaypwd&id={$active_id}";
	$user_url = "http://{$_SERVER['HTTP_HOST']}/index.php?user";
	$send_email_msg = '
	<div style="background: url(http://'.$_SERVER['HTTP_HOST'].'/data/images/base/email_bg.png) no-repeat left bottom; font-size:14px; width: 588px; ">
	<div style="padding: 10px 0px; background: url(http://'.$_SERVER['HTTP_HOST'].'/data/images/base/email_button.png)  no-repeat ">
		<h1 style="padding: 0px 15px; margin: 0px; overflow: hidden; height: 48px;">
			<a title="'.$webname.'用户中心" href="http://'.$_SERVER['HTTP_HOST'].'/index.php?user" target="_blank" swaped="true">
			<img style="border-width: 0px; padding: 0px; margin: 0px;" alt="'.$webname.'用户中心" src="http://'.$_SERVER['HTTP_HOST'].'/data/images/base/email_logo.png" height="48" width="208">		</a>
		</h1>
		<div style="padding: 0px 20px; overflow: hidden; line-height: 40px; height: 50px; text-align: right;"> </div>
		<div style="padding: 2px 20px 30px;">
			<p>亲爱的 <span style="color: rgb(196, 0, 0);">'.$username.'</span> , 您好！</p>
			<p>请点击下面的链接重新修改支付密码。</p>
			<p style="overflow: hidden; width: 100%; word-wrap: break-word;"><a title="点击完成注册" href="'.$_url.'" target="_blank" swaped="true">'.$_url.'</a>
			<br><span style="color: rgb(153, 153, 153);">(如果链接无法点击，请将它拷贝到浏览器的地址栏中)</span></p>
			
			<p style="text-align: right;"><br>'.$webname.'用户中心 敬启</p>
			<p><br>此为自动发送邮件，请勿直接回复！如您有任何疑问，请点击<a title="点击联系我们" style="color: rgb(15, 136, 221);" href="http://'.$_SERVER['HTTP_HOST'].'/lxwm/index.html" target="_blank" >联系我们&gt;&gt;</a></p>
		</div>
	</div>
</div>
		';
	return $send_email_msg;

}

function RegEmailMsg($data = array()){
	global $mysql;
	$user_id = $data['user_id'];
	$username = $data['username'];
	$webname = $data['webname'];
	$email = $data['email'];
	$query_url = isset($data['query_url'])?$data['query_url']:"action/active";
	$active_id = urlencode(authcode($user_id.",".time(),"ENCODE"));
	if($data['loan']==1){
	$_url = "http://{$_SERVER['HTTP_HOST']}/index.php?user&q=active&id={$active_id}&type=success";
	}else{
	$_url = "http://{$_SERVER['HTTP_HOST']}/index.php?user&q=active&id={$active_id}";
	}
	$user_url = "http://{$_SERVER['HTTP_HOST']}/index.php?user";
	$send_email_msg = '
	<div style="background: url(http://'.$_SERVER['HTTP_HOST'].'/data/images/base/email_bg.png) no-repeat left bottom; font-size:14px; width: 588px; ">
	<div style="padding: 10px 0px; background: url(http://'.$_SERVER['HTTP_HOST'].'/data/images/base/email_button.png)  no-repeat ">
		<h1 style="padding: 0px 15px; margin: 0px; overflow: hidden; height: 48px;">
			<a title="'.$webname.'用户中心" href="http://'.$_SERVER['HTTP_HOST'].'/index.php?user" target="_blank" swaped="true">
			<img style="border-width: 0px; padding: 0px; margin: 0px;" alt="'.$webname.'用户中心" src="http://'.$_SERVER['HTTP_HOST'].'/data/images/base/email_logo.png" height="48" width="208">		</a>
		</h1>
		<div style="padding: 0px 20px; overflow: hidden; line-height: 40px; height: 50px; text-align: right;"> </div>
		<div style="padding: 2px 20px 30px;">
			<p>亲爱的 <span style="color: rgb(196, 0, 0);">'.$username.'</span> , 您好！</p>
			<p>感谢您注册'.$webname.'，您登录的邮箱帐号为 <strong style="font-size: 16px;">'.$email.'</strong></p>
			<p>请点击下面的链接即可完成激活。</p>
			<p style="overflow: hidden; width: 100%; word-wrap: break-word;"><a title="点击完成注册" href="'.$_url.'" target="_blank" swaped="true">'.$_url.'</a>
			<br><span style="color: rgb(153, 153, 153);">(如果链接无法点击，请将它拷贝到浏览器的地址栏中)</span></p>

			<p>感谢您光临'.$webname.'用户中心！ <br>现在就登录吧!
			<a title="点击登录'.$webname.'用户中心" style="color: rgb(15, 136, 221);" href="http://'.$_SERVER['HTTP_HOST'].'/index.php?user" target="_blank" swaped="true">http://'.$_SERVER['HTTP_HOST'].'/index.php?user</a> 
			</p>
			<p style="text-align: right;"><br>'.$webname.'用户中心 敬启</p>
			<p><br>此为自动发送邮件，请勿直接回复！如您有任何疑问，请点击<a title="点击联系我们" style="color: rgb(15, 136, 221);" href="http://'.$_SERVER['HTTP_HOST'].'/lxwm/index.html" target="_blank" >联系我们&gt;&gt;</a></p>
		</div>
	</div>
</div>
		';
	return $send_email_msg;

}

function GetpwdMsg($data = array()){
	global $mysql;
	$user_id = $data['user_id'];
	$username = $data['username'];
	$webname = $data['webname'];
	$email = $data['email'];
	$active_id = urlencode(authcode($user_id.",".time(),"ENCODE"));
	$_url = "http://{$_SERVER['HTTP_HOST']}/index.php?user&q=getpwd&type=pwd&id={$active_id}";
	$user_url = "http://{$_SERVER['HTTP_HOST']}/index.php?user";
	$send_email_msg = '
	<div style="background: url(http://'.$_SERVER['HTTP_HOST'].'/data/images/base/email_bg.png) no-repeat left bottom; font-size:14px; width: 588px; ">
	<div style="padding: 10px 0px; background: url(http://'.$_SERVER['HTTP_HOST'].'/data/images/base/email_button.png)  no-repeat ">
		<h1 style="padding: 0px 15px; margin: 0px; overflow: hidden; height: 48px;">
			<a title="'.$webname.'用户中心" href="http://'.$_SERVER['HTTP_HOST'].'/index.php?user" target="_blank" swaped="true">
			<img style="border-width: 0px; padding: 0px; margin: 0px;" alt="'.$webname.'用户中心" src="http://'.$_SERVER['HTTP_HOST'].'/data/images/base/email_logo.png" height="48" width="208">		</a>
		</h1>
		<div style="padding: 0px 20px; overflow: hidden; line-height: 40px; height: 50px; text-align: right;"> </div>
		<div style="padding: 2px 20px 30px;">
			<p>亲爱的 <span style="color: rgb(196, 0, 0);">'.$username.'</span> , 您好！</p>
			<p>请点击下面的链接重新修改密码。</p>
			<p style="overflow: hidden; width: 100%; word-wrap: break-word;"><a title="点击完成注册" href="'.$_url.'" target="_blank" swaped="true">'.$_url.'</a>
			<br><span style="color: rgb(153, 153, 153);">(如果链接无法点击，请将它拷贝到浏览器的地址栏中)</span></p>
			
			<p style="text-align: right;"><br>'.$webname.'用户中心 敬启</p>
			<p><br>此为自动发送邮件，请勿直接回复！如您有任何疑问，请点击<a title="点击联系我们" style="color: rgb(15, 136, 221);" href="http://'.$_SERVER['HTTP_HOST'].'/lxwm/index.html" target="_blank" >联系我们&gt;&gt;</a></p>
		</div>
	</div>
</div>
		';
	return $send_email_msg;

}


//user_id,contents
function GetEmailMsg($data = array()){
	global $mysql,$_G;
	$user_id = $data['user_id'];
	$username = $data['username'];
    if ($username==""){
        $sql = "select p1.username,p2.realname,p2.realname_status from `{users}` as p1 left join `{users_info}` as p2 on p1.user_id=p2.user_id where p1.user_id='{$user_id}'";
        $result = $mysql->db_fetch_array($sql);
        if ($result['realname_status']==1){
            $username = $result['realname'];
        }else{
            $username = $result['username'];
        }
    }
	$webname = $_G['con_webname'];
	$user_url = "http://{$_SERVER['HTTP_HOST']}/index.php?user";
	$send_email_msg = '
	<div style="background: url(http://'.$_SERVER['HTTP_HOST'].'/data/images/base/email_bg.png) no-repeat left bottom; font-size:14px; width: 588px; ">
	<div style="padding: 10px 0px; background: url(http://'.$_SERVER['HTTP_HOST'].'/data/images/base/email_button.png)  no-repeat ">
		<h1 style="padding: 0px 15px; margin: 0px; overflow: hidden; height: 48px;">
			<a title="'.$webname.'用户中心" href="http://'.$_SERVER['HTTP_HOST'].'/index.php?user" target="_blank" swaped="true">
			<img style="border-width: 0px; padding: 0px; margin: 0px;" alt="'.$webname.'用户中心" src="http://'.$_SERVER['HTTP_HOST'].'/data/images/base/email_logo.png" height="48" width="208">		</a>
		</h1>
		<div style="padding: 0px 20px; overflow: hidden; line-height: 40px; height: 50px; text-align: right;"> </div>
		<div style="padding: 2px 20px 30px;">
			<p>尊敬的 <span style="color: rgb(196, 0, 0);">'.$username.'</span> , 您好！</p>
			<p style="overflow: hidden; width: 100%; word-wrap: break-word;">'.$data['contents'].'</p>
			<p style="text-align: right;"><br>'.$webname.'用户中心 敬启</p>
			<p><br>此为自动发送邮件，请勿直接回复！如您有任何疑问，请点击<a title="点击联系我们" style="color: rgb(15, 136, 221);" href="http://'.$_SERVER['HTTP_HOST'].'/lxwm/index.html" target="_blank" >联系我们&gt;&gt;</a></p>
		</div>
	</div>
</div>
		';
	return $send_email_msg;

}

?>