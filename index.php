<?php

/******************************
 * $File:index.php
 * $Description: 帝友 系统核心文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
require_once ("core/config.inc.php");

error_reporting(0);


if(file_exists(DEAYOU_PATH."ipconfig.php")){
	require_once DEAYOU_PATH."ipconfig.php";
	if(ip_control_all($allow_ip_all)){
		echo '您的IP不能访问,请与管理员联系.'; exit;
	}
}
//将mysql加进去
$_G['mysql'] = $mysql;
$_G['nowtime'] = time();
//获取地址的基本信息
$query_string = explode("&",$_SERVER['QUERY_STRING']);

$_G['query_string'] = $query_string;
if (isset($_REQUEST['query_site']) && $_REQUEST['query_site']!=""){
	$_G['query_site'] = $_REQUEST['query_site'];
}elseif (isset($query_string[0])){
	$_G['query_site'] = $query_string[0];
}
$_G['web_domain'] = "http://".$_SERVER["HTTP_HOST"];

//网站基本配置文件
$system = array();
$system_name = array();
$_system = $mysql->db_selects("system");

$system['con_cookie_status'] =0;
foreach ($_system as $key => $value){
	$system[$value['nid']] = $value['value'];
	$system_name[$value['nid']] = $value['name'];
}
$system['con_tongji'] = htmlspecialchars_decode($system['con_tongji']); //网站统计JS
$_G['system'] = $system;
$_G['_system'] = $_system;

$allow_visit_ip = $system['con_visit_ip']; 
$allow_system_ip = $system['con_system_ip'];

//ip control_visit
if(ip_control($allow_visit_ip)){
	echo '您的IP不能访问,请与管理员联系.'; exit;
}

//模块
$_G['module'] = adminClass::GetModuleList(array("limit"=>"all","type"=>"all"));
foreach ($_G['module'] as $key => $value){
	$_G['_module'][$value['nid']] = $value['name'];
}
//$_G['system']['con_cookie_status'] = 1;

//获取用户id
$_sess["cookie_id"] = $_G["system"]["con_cookie_id"];
$_sess["cookie_status"] = $_G["system"]["con_cache_type"];
$_G["user_id"] = GetCookies($_sess);

if ($_G["user_id"]!=""){
    $_sess["user_id"] = $_G["user_id"];
    SetCookies($_sess);
	$_G["user_result"] = usersClass::GetUsers(array("user_id"=>$_G["user_id"]));
	$_G["user_info"] = usersClass::GetUsersInfo(array("user_id"=>$_G["user_id"]));
	
    //用户vip
   $vip_result = usersvipClass::GetUsersVip(array("user_id"=>$_G['user_id']));
    $_G['user_result']['vip_status'] = $vip_result['status'];
    $_G['user_vip_status'] = $vip_result['status'];
	// 短消息
	if( isset($_G['_module']['message'])){
		require_once (ROOT_PATH."modules/message/message.class.php");
		$_G['message_result'] =  messageClass::GetMessageReceiveList(array("user_id"=>$_G["user_id"],"limit"=>"all"));
	}
	
	// 积分
	if( isset($_G['_module']['credit'])){
		require_once (ROOT_PATH."modules/borrow/borrow.class.php");
		$_G['user_credit'] =  borrowClass::GetBorrowCredit(array("user_id"=>$_G["user_id"]));
	}
}

//联动模块
if (file_exists("modules/linkages/linkages.class.php") && isset($_G['_module']['linkages'])){
	require_once ("modules/linkages/linkages.class.php");
	$result = linkagesClass::GetList(array("limit"=>"all"));
	foreach ($result as $key => $value){
		$_G['linkages'][$value['type_nid']][$value['value']] = $value['name'];
		$_G['linkages'][$value['id']] = $value['name'];
		if ($value['type_nid']!=""){
			$_G['_linkages'][$value['type_nid']][$value['id']] = array("name"=>$value['name'],"id"=>$value['id'],"value"=>$value['value']);
		}
	}
	
}
//地区模块
if (file_exists("modules/areas/areas.class.php")  && isset($_G['_module']['areas'])){
	include_once ("modules/areas/areas.class.php");
	$_G['areas'] = areasClass::GetAreas(array("limit"=>"all"));
	$_G['areas_city'] = areasClass::GetCityAll(array("areas"=>$_G['areas']));
	//如果网站是采用二级地区分区的，则进行相关的配置
	//if (isset($_G['system']['con_area_part']) && $_G['system']['con_area_part']==1){
	if (!isset($_G['system']['con_area_part'])){
		if ($_COOKIE['set_city_nid']!=""){
			$_G['city_result'] = areasClass::GetOne(array("nid"=>$_COOKIE['set_city_nid']));
		}	
	}
}
$quer = explode("/",$query_string[0]);	
if (isset($_REQUEST['query_site']) && $_REQUEST['query_site']!=""){
	$site_nid =$_REQUEST['query_site'];
}else{
	$site_nid = isset($quer[0])?$quer[0]:"";
}
$_G["article_id"] = isset($_REQUEST['article_id'])?$_REQUEST['article_id']:"";
$_G["content_page"] = isset($quer[2])?$quer[2]:"";//内容的分页
$_G['site'] = adminClass::GetSiteList(array("limit"=>"all"));
$_G['site_list'] = adminClass::GetSites();
$_G['site_result'] = adminClass::GetSiteOnes(array("nid"=>$site_nid));
if ($_G['site_result']==false){
	$_G['site_result']['id'] = 1;
}
$_G['site_result']['pid'] = isset( $_G['site_result']['pid'])? $_G['site_result']['pid']:"";
//获得子站点的信息
foreach ($_G['site'] as $key => $value){
	if ($value['pid'] == $_G['site_result']['id']){
		if ($value['status']==1){
		$_G['site_sub_list'][] = $value;//子站点列表
		}
	}
	if ($value['id'] == $_G['site_result']['pid']){
		$_G['site_presult'][] = $value;//父站点
	}
	if ($value['pid'] == $_G['site_result']['pid']){
		if ($value['status']==1){
			$_G['site_brother_list'][] = $value;//同级站点列表
		}
	}
}
// 把系统设置的 站点参数设置调整
$site = $_G['site'];
$new_site = [];
foreach ($site as $key=>$value){
	$new_site[$value['nid']] = $value;
}
$_G['site'] = $new_site;

$_G['title'] = "";
//网站首页的标题，关键字，描述
if ($_G['site_result']['name']!=""){
    
}
function convert($size){ 
$unit=array('b','kb','mb','gb','tb','pb'); 
return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i]; 
} 


//积分模块
if (file_exists("modules/credit/credit.class.php")  && isset($_G['_module']['credit'])){
	require_once (ROOT_PATH."modules/credit/credit.class.php");
	$_G['credit']['class'] = creditClass::GetClassList(array("limit"=>"all"));
	foreach ($_G['credit']['class'] as $key => $value){
		$_G['credit']['_class'][$value['id']] = $value['name'];
	}
	$_G['credit']['rank'] = creditClass::GetRankList(array("limit"=>"all"));
}
//上传图片的配置
$_G['upimg']['cut_status'] = 0;
$_G['upimg']['user_id'] = empty($_G['user_id'])?0:$_G['user_id'];
$_G['upimg']['cut_type'] = 2;
$_G['upimg']['cut_width'] = isset($_G['system']['con_fujian_imgwidth'])?$_G['system']['con_fujian_imgwidth']:"";
$_G['upimg']['cut_height'] = isset($_G['system']['con_fujian_imgheight'])?$_G['system']['con_fujian_imgheight']:"";
//$_G['upimg']['file_dir'] = "data/aa/";
$_G['upimg']['file_size'] = 1000;
$_G['upimg']['mask_status'] = isset($_G['system']['con_watermark_status'])?$_G['system']['con_watermark_status']:"";
$_G['upimg']['mask_position'] = isset($_G['system']['con_watermark_position'])?$_G['system']['con_watermark_position']:"";
if (isset($_G['system']['con_watermark_type']) && $_G['system']['con_watermark_type']==1){
	$_G['upimg']['mask_word'] =isset($_G['system']['con_watermark_word'])?$_G['system']['con_watermark_word']:"";
	$_G['upimg']['mask_font'] = "3";
	//$_G['upimg']['mask_size'] = $_G['system']['con_watermark_font'];
	$_G['upimg']['mask_color'] = isset($_G['system']['con_watermark_color'])?$_G['system']['con_watermark_color']:"";
}else{
	$_G['upimg']['mask_img'] = isset($_G['system']['con_watermark_file'])?$_G['system']['con_watermark_file']:"";
	if ($_G['upimg']['mask_img']!=""){
		$result = $upload->GetOne(array("id"=>$_G['system']['con_watermark_file']));
		if ($result!=false){
		$_G['upimg']['mask_img'] = $result['fileurl'];
		}
	}
}

//帝友借贷配置文件
if(file_exists("core/dyp2p.inc.php")){
	include_once("core/dyp2p.inc.php");
}



//模块，分页，每页显示条数
$_G['page'] = intval(isset($_REQUEST['page'])?$_REQUEST['page']:1);//分页
$_G['epage'] = isset($_REQUEST['epage'])?$_REQUEST['epage']:10;//分页的每一页

$_G['nowurl'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$_G['url_now'] = $_SERVER['REQUEST_URI'];



//模板选择
$con_template = "themes/";
$con_template .= empty($system['con_template'])?"default":$system['con_template'];
$template_error = false;
if (!file_exists($con_template)){
	$template_error = true;
	$con_template = "themes/default";
	$magic->template_error = $template_error;
}
$magic->template_dir = $con_template;
$magic->template_themes = empty($system['con_template'])?"default":$system['con_template'];
//$magic->force_compile = true;
$_G['tpldir'] = "/".$con_template;
$magic->assign("tpldir",$_G['tpldir']);
$magic->assign("tempdir",$_G['tpldir']);//图片地址

$_G['left_tag'] = $magic->left_tag ;
$_G['right_tag'] = $magic->right_tag ;
$_G['nowtime'] = time();
$magic->assign("_G",$_G);

//管理地址
if (isset($_G['system']['con_houtai']) && $_G['system']['con_houtai']!=""){
	$admin_name = $_G['system']['con_houtai'];
}else{
	$admin_name = "deayou";
}
if ($_G['query_site'] == $admin_name ){
	//$_REQUEST(urldecode(rewrite_uri()));
	//ip control_system
	if(ip_control($allow_system_ip)){
		echo '您的IP不能访问,请与管理员联系.'; exit;
	}
	include_once ("modules/admin/index.php");exit;
}

//清空缓存
elseif ($_G['query_site'] == "clear" ){
	DelFile("data/compile");
	echo "<script>location.href='/'</script>";

}

//修改数据库同步
elseif ($_G['query_site'] == "altertable" ){
	require_once ("altertable/index.php");exit;
}

//用户中心
elseif ($_G['query_site'] == "code" ){
	if ($_REQUEST['q']!=""){
		require_once ("modules/".$_REQUEST['q']."/".$_REQUEST['q'].".return.php");
	}
	exit;
}


//用户中心
elseif ($_G['query_site'] == "user" ){
	require_once ("modules/member/index.php");exit;
}


//用户中心
elseif ($_G['query_site'] == "plugins" ){
	require_once ("plugins/index.php");exit;
}

//用户中心
elseif ($_G['query_site'] == "home" ){
	$user_id = $_REQUEST['user_id'];
	if ($user_id==""){
		$user_id = $_G['user_id'];
	}
	$_G['article_id'] = $user_id;
	$magic->assign("_G",$_G);
	usersClass::AddVisit(array("user_id"=>$user_id,"visit_userid"=>$_G['user_id']));
	if ($home_dir!=""){
		$magic->template_dir =$home_dir;
		$magic->assign("tpldir","/".$home_dir);
		$magic->display($home_template);
	}else{
		$magic->display("home.html");
	}
	exit;
}

else{	

		/**
		* 关闭网站
		**/
		if ($_G['system']['con_webopen']==0){
			die($_G['system']['con_closemsg']);
		}
		
		//首页
		elseif ($_G['query_site']==""){
			$index = $_G['site']['index'];
			$_G['site_result']['name'] = $_G['site_result']['seoname'] = empty($index['seotitle'])?$_G['system']['con_site_name']:$index['seotitle'];
			$_G['keywords'] = $index['keywords'];
			$_G['description'] = $index['description'];
			$template = "index.html";
		}
		
		//其他
		else{
			//城市切换
			if ($_G['query_site'] == "city" ){
				$nid = $_REQUEST['nid'];
				setcookie("set_city_nid",$nid,time()+3600*24*30);
				$_G['city_result'] = areasClass::GetOne(array("nid"=>$nid));
				$template = "index.html";
			}
			//错误页面
			elseif (!IsExiest($_G['site_result']['nid'])){
				$_G['msg'] = array("页面不存在","","");
				$template = "error.html";
			}
			
			//跳转地址
			elseif ($_G['site_result']["type"]=="url"){
				echo "<script>location.href='{$_G['site_result']['value']}'</script>";
				exit;
			}
			
			//其他
			else{
				$index = isset($_G['site'][$_G['site_result']['nid']])?$_G['site'][$_G['site_result']['nid']]:[];
				$_G['keywords'] = empty($index['keywords'])?$_G['system']['con_keywords']:$index['keywords'];
				$_G['description'] = empty($index['description'])?$_G['system']['con_description']:$index['description'];
				$_G['site_result']['seoname'] = empty($index['seotitle'])?$_G['site_result']['name']:$index['seotitle'];
				if ($_G['site_result']['type']=="page"){
					require_once(ROOT_PATH."modules/articles/articles.class.php");
					$_G['page_result'] = articlesClass::GetPageOne(array("id"=>$_G['site_result']['value']));
					$_G['articles'] =$_G['page_result'];
					$_G['site_result']['name'] = $_G['site_result']['name'];
					$_G['keywords'] = empty($_G['articles']['tag'])?$_G['keywords']:$_G['articles']['tag'];
					$_G['description'] = empty($_G['articles']['summary'])?$_G['description']:$_G['articles']['summary'];
				}
				//获得站点和文章的信息
				$_REQUEST['page'] = isset($_REQUEST['page'])?$_REQUEST['page']:"";
				
				if (IsExiest($_G['article_id'])!=false){
					if ($_G['site_result']['type']=="article"){
						require_once(ROOT_PATH."modules/articles/articles.class.php");
						$_G['articles'] = $_G['articles_result'] = articlesClass::GetOne(array("id"=>$_G['article_id'],"hits_status"=>1));
						$_G['site_result']['name'] = $_G['articles']['name'].' - '.$_G['site_result']['name'];
						$_G['site_result']['seoname'] = $_G['articles']['name'].' - '.$_G['site_result']['seoname'];
						$_G['keywords'] = empty($_G['articles']['tag'])?$_G['keywords']:$_G['articles']['tag'];
						$_G['description'] = empty($_G['articles']['summary'])?$_G['description']:$_G['articles']['summary'];
					}
					$template = $_G['site_result']["content_tpl"];
				}elseif (IsExiest($_REQUEST['page'])!=false || $_REQUEST['nid']!=""){
					$template = str_replace("[code]",$_G['site_result']["code"],$_G['site_result']["list_tpl"]);
				}else{
					$template = str_replace("[code]",$_G['site_result']["code"],$_G['site_result']["index_tpl"]);
				}
			}
		}
		$magic->assign("_G",$_G);
		$magic->display($template);
		
		exit;
		
		
		
		
}