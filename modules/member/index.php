<?
/******************************
 * $File: index.php
 * $Description: 管理中心
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问


$_U = array();//用户的共同配置变量

//用户中心模板引擎的配置
$magic->left_tag = "{";
$magic->right_tag = "}";
//$magic->force_compile = true;

$temlate_dir = "themes/{$_G['system']['con_template']}_member";
if (!file_exists($temlate_dir)){
$temlate_dir = "themes/{$_G['system']['con_template']}";
}
$magic->template_dir = $temlate_dir;
if ($_G["system"]["con_template_skin"]!=""){
    $magic->assign("tpldir",$_G['tpldir']."/".$_G["system"]["con_template_skin"]);
    $magic->assign("tempdir",$_G['tpldir']."/".$_G["system"]["con_template_skin"]);//图片地址
}else{
    $magic->assign("tpldir",$_G['tpldir']);
    $magic->assign("tempdir",$_G['tpldir']);//图片地址
}

//用户中心的管理地址
$member_url = "/?".$_G['query_site'];
$_U['member_url'] = $member_url;

//模块，分页，每页显示条数


//对地址栏进行归类
$q = empty($_REQUEST['q'])?"":urldecode($_REQUEST['q']);//获取内容
$_q = explode("/",$q);
$_U['query'] = $q;
$_U['query_sort'] = empty($_q[0])?"main":$_q[0];
$_U['query_class'] = empty($_q[1])?"list":$_q[1];
$_U['query_type'] = empty($_q[2])?"list":$_q[2];
$_U['query_url'] = $_U['member_url']."&q={$_U['query_sort']}/{$_U['query_class']}";
// 登录页面
if ($_U['query_sort'] == 'login'){
	require_once("login.php");
}
	
// 退出页面
elseif ($_U['query_sort'] == 'logout'){
	include_once("logout.php");
}
	
// 取回密码
elseif ($_U['query_sort'] == 'getpwd'){
	include_once("getpwd.php");
}
	
// 注册
elseif ($_U['query_sort'] == 'reg'){
	include_once("reg.php");
}

// 激活
elseif ($_U['query_sort'] == 'active'){
	include_once("active.php");
}

// 激活
elseif ($_U['query_sort'] == 'user'){
	include_once("user.php");
}


#要请好友注册	
elseif ($_U['query_sort'] == "reginvite"){
    // 推荐信息
$_REQUEST['u'] = isset($_REQUEST['u'])?$_REQUEST['u']:'';
if(!empty($_REQUEST['u'])){
    $_user_id = Url2Key($_REQUEST['u'],"reg_invite");
    setcookie('reginvite_user_id',authcode($_user_id[1],'ENCODE'),time()+31536000,"/",$_SERVER["HTTP_HOST"],false,true);
    $_SESSION['reginvite_user_id'] = $_user_id[1];
}else{
    if(!empty($_COOKIE["reginvite_user_id"])){
        $reginvite_user_id = authcode($_COOKIE["reginvite_user_id"],'DECODE');
        $_SESSION['reginvite_user_id'] = $reginvite_user_id;
    }
}
	header('location:/?user&q=reg');
}



// 检查用户名是否被注册
elseif ($_U['query_sort'] == 'check_username'){
	$username = iconv("UTF-8","GBK",urldecode($_REQUEST['username']));
	$result = usersClass::CheckUsername(array("username"=>$username));
	if ($result == true){
		echo "0";exit;
	}else{
		echo "1";exit;
	}
}

# 检查邮箱是否被注册
elseif ($_U['query_sort'] == 'check_email'){
	$result = usersClass::CheckEmail(["email"=>urldecode($_REQUEST['email'])]);
	if ($result == true){
		echo "0";exit;
	}else{
		echo "1";exit;
	}
}
// 检查 手机号码是否已经被注册
elseif($_U['query_sort'] == 'check_phone'){
	$result = usersClass::CheckPhone(array("phone"=>urldecode($_REQUEST['phone'])));

	if(true == $result){
		echo 1;
		exit;
	}else{
		echo 0;
		exit;
	}
}
// 发送手机验证码 shaoqisq123@gmail.com
elseif($_U['query_sort'] == 'send_code'){
	$result = usersClass::SendCode(array("phone"=>urldecode($_REQUEST['phone'])));
	if(true == $result){
		echo 1;
		exit;
	}else{
		echo 0;
		exit;
	}
}
elseif ($_U['query_sort'] == "plugins" ){
	$magic->assign("_U",$_U);
	$_ac = !isset($_REQUEST['ac'])?"html":$_REQUEST['ac'];
	if ($_ac=="html"){
		$_p = !isset($_REQUEST['p'])?"":$_REQUEST['p'];
		$file = ROOT_PATH."plugins/html/{$_p}.inc.php";
	}else{
		$file = ROOT_PATH."plugins/{$_ac}/{$_ac}.php";
	}
	if (file_exists($file)){
		include_once ($file);exit;
	}
}


// 判断是否登录
elseif ($_G['user_id'] == ""){
	if ($_REQUEST['user_id']!=""){
		$template = "user_main.html";
	}else{
		header('location:/?user&q=logout');
	}
}


// 用户中心处理
elseif ($_U['query_sort'] == 'code'){
	// 进入用户管理处理中心之前需要进行邮箱认证
	if(empty($_G['user_info']['email_status']) && $_U['query_class']!='approve'){
		$msg = array("请先进行邮箱认证，方可进行其他操作，谢谢合作","","/renzheng/index.html?type=email");
	}else{
		$file = ROOT_PATH."/modules/{$_U['query_class']}/{$_U['query_class']}.inc.php";
		if (is_file($file)){
			include($file);
		}else{
			$msg = array("您操作有误，请勿乱操作");
		}
	}
}

//用户中心
else{

	$template = "users_main.html";
}




//系统信息处理文件
if (isset($msg) && $msg!="") {
	$_msg = $msg[0];
	$content = empty($msg[1])?"返回上一页":$msg[1];
	$url = empty($msg[2])?"-1":$msg[2];
	$http_referer = empty($_SERVER['HTTP_REFERER'])?"":$_SERVER['HTTP_REFERER'];
	if ($http_referer == "" && $url == ""){ $url = "/";}
	if ($url == "-1") $url = "";
	elseif ($url == "" ) $url = $http_referer;
	$_U['showmsg'] = array('msg'=>$_msg,"url"=>$url,"content"=>$content);
	$template = "user_msg.html";
}


$magic->assign("_U",$_U);
$magic->assign("_G",$_G);
$magic->display($template);
exit;	
?>