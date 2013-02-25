<?php
/******************************
 * $File: index.php
 * $Description: 后台处理文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

//管理后台的共同配置变量
$_A = array();
//插件

//用户模块信息
require_once(ROOT_PATH.'modules/users/users.class.php');//基本信息设置
$users = new usersClass();

//管理后台的地址,系统参数
$db_config['con_admin_tpl'] = isset($db_config['con_admin_tpl'])?$db_config['con_admin_tpl']:"";
if ($db_config['con_admin_tpl']!=""){
	$_A['tpldir'] = "themes/".$db_config['admintpl'];
}else{
	$_A['tpldir'] = "themes/admin_new";
}
$magic->template_dir = $_A['tpldir'];

//后台的管理地址
$admin_url = "?".$_G['query_site'];
$_A['admin_url'] = $admin_url;

//对地址栏进行归类
$q = empty($_REQUEST['q'])?"":$_REQUEST['q'];//获取内容
$_q = explode("/",$q);
$_A['query'] = $q;
$_A['query_sort'] = empty($_q[0])?"main":$_q[0];
$_A['query_class'] = empty($_q[1])?"list":$_q[1];
$_A['query_type'] = empty($_q[2])?"list":$_q[2];
$_A['query_url'] = $_A['admin_url']."&q={$_A['query_sort']}/{$_A['query_class']}";
$_A['query_url_all'] = $_A['admin_url']."&q={$q}";

//模块，分页，每页显示条数
$_A['page'] = empty($_REQUEST['page'])?"1":$_REQUEST['page'];//分页
$_A['epage'] = empty($_REQUEST['epage'])?"20":$_REQUEST['epage'];//分页的每一页

//后台用户登录id
$_G["user_id"] = GetCookies(array('cookie_id' => "dwcms_admin_userid","cookie_status"=>1));
if ($_G["user_id"]!=""){
    SetCookies(array('cookie_id' => "dwcms_admin_userid","cookie_status"=>1,"user_id"=>$_G["user_id"]));
	$_A["user_result"] = usersClass::GetUsers(array("user_id"=>$_G["user_id"]));
	$_A["admin_result"] = usersClass::GetUsersAdminOne(array("user_id"=>$_G["user_id"]));
	
	if ($_A["admin_result"]["type_id"]==1){
		$_A["admin_module"] = array("site","articles","attestations","credit","users","borrow","account","approve","ratting","system");
	}else{
		$_A["admin_module"] = explode(",",$_A["admin_result"]['module']);
	}
	$_A["admin_module_purview"] = adminClass::GetModuleAdmin(array("user_id"=>$_G["user_id"]));
	$display = "";
	foreach ($_A["admin_module_purview"]['all'] as $key => $value){
		$display .= ",'{$key}' : {'{$key}' : {";
		$_display = array();
		if ($value['result']!=false){
			foreach ($value['result'] as $_key => $_value){
				if ($_A["admin_module_purview"]["purview"]==""){
					$_display[] =	"'{$_key}' : ['{$_value['name']}','".$_A['admin_url']."&q={$_value['url']}']";
				}else{
					if(in_array($_key,$_A["admin_module_purview"]["purview"])){
						$_display[] =	"'{$_key}' : ['{$_value['name']}','".$_A['admin_url']."&q={$_value['url']}']";
					}
				}
			}
			$display .=	join(",",$_display);
		}
		$display .=	"}}\n\n";
	}
	$_A["admin_module_left"] = $display;
	$display = array();
	if ($_A["admin_module_purview"]['other']!=""){
		foreach ($_A["admin_module_purview"]['other'] as $key => $value){
			$display[]  .=	"'{$key}' : ['{$value['name']}','".$_A['admin_url']."&q=code/{$key}']";
		}
	}
	$_A["admin_module_other"] = join(",",$display);
	
	$_A["admin_module_top"] =$_A["admin_module_purview"]["top"];
	$_A["admin_module_all"] =$_A["admin_module_purview"]["all"];
}	

//用户登录
if ($_A['query_sort']=='login' ){
	require_once('login.php');//
}

/* 用户退出 */
else if ($_A['query_sort']=='logout'){
	DelCookies(array('cookie_id' => "dwcms_admin_userid","cookie_status"=>1));
	header("location:".$_A['admin_url']);
}

elseif ($_A['query_sort'] == "plugins" ){
	$magic->assign("_A",$_A);
	$magic->assign("_G",$_G);
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
//判断用户是否登录
elseif ($_G['user_id']!=""){
	
	$magic->assign("_G",$_G);

	/* 模块管理 */
	 if ($_A['query_sort']=='module'){
		require_once('module.php');//
	}
	
	/* 站点管理 */
	else if ($_A['query_sort']=='site'){
		require_once('site.php');//
	}
	
	/* 系统管理信息 */
	else if ($_A['query_sort']=='system'){
		require_once('system.php');//
	}
	
	
	/* 各个模块的管理 */
	else if ($_A['query_sort']=='code'){
		require_once('code.php');//
	}
	/* 默认为后台首页 */
	else{
		$template = "admin_main.html";
	}
}


else{
	$template = "admin_login.html";
}

//错误处理文件
if (isset($msg) && $msg!="") {
	$_msg = $msg[0];
	$content = empty($msg[1])?"返回上一页":$msg[1];
	$url = empty($msg[2])?"-1":$msg[2];
	$http_referer = empty($_SERVER['HTTP_REFERER'])?"":$_SERVER['HTTP_REFERER'];
	if ($http_referer == "" && $url == ""){ $url = "/";}
	if ($url == "-1") $url = "";
	elseif ($url == "" ) $url = $http_referer;
	
	$_A['showmsg'] = array('msg'=>$_msg,"url"=>$url,"content"=>$content);
	$template = "admin_msg.html";
	
}


//后台模板特殊的参数，所有的基本参数全部都是应用到这个参数里面去
$magic->assign("_A",$_A);
$magic->assign("_G",$_G);
$magic->display($template);
exit;	
?>