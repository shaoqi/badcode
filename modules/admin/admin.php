<?php
/******************************
 * $File: index.php
 * $Description: 后台处理文件
 * $Author: ahui 
 * $Time:2011-11-09
 * $Update:None 
 * $UpdateDate:None 
 * Copyright(c) 2012 by dycms.net. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

//管理后台的共同配置变量
$_A = array();
//插件
$_A['list_purview']["system"]["name"] = "系统管理";
$_A['list_purview']["system"]["result"]["system_clearcache"] = array("name"=>"清空缓存","url"=>"system/clearcache");
$_A['list_purview']["system"]["result"]["system_info"] = array("name"=>"系统参数","url"=>"system/info");
$_A['list_purview']["system"]["result"]["system_watermark"] = array("name"=>"图片水印","url"=>"system/watermark");
//$_A['list_purview']["system"]["result"]["system_fujian"] = array("name"=>"附近设置","url"=>"system/fujian");
$_A['list_purview']["system"]["result"]["system_email"] = array("name"=>"邮箱设置","url"=>"system/email");
$_A['list_purview']["system"]["result"]["system_id5"] = array("name"=>"ID5设置","url"=>"system/id5");
$_A['list_purview']["system"]["result"]["system_module"] = array("name"=>"模块管理","url"=>"system/module");
$_A['list_purview']["system"]["result"]["system_upfiles"] = array("name"=>"图片管理","url"=>"system/upfiles");
$_A['list_purview']["system"]["result"]["system_users_admin"] = array("name"=>"管理员管理","url"=>"code/users/admin");
$_A['list_purview']["system"]["result"]["system_users_admin_type"] = array("name"=>"管理员类型","url"=>"code/users/admin_type");
$_A['list_purview']["system"]["result"]["system_users_admin_log"] = array("name"=>"管理员记录","url"=>"code/users/admin_log");
//$_A['list_purview']["system"]["result"]["system_dbbackup_back"] = array("name"=>"数据库备份","url"=>"system/dbbackup/back");


$_A['list_purview']["site"]["name"] = "站点管理";
$_A['list_purview']["site"]["result"]["site_list"] = array("name"=>"站点列表","url"=>"system/site/list");
$_A['list_purview']["site"]["result"]["site_new"] = array("name"=>"添加站点","url"=>"system/site/new");
$_A['list_purview']["site"]["result"]["site_menu"] = array("name"=>"菜单管理","url"=>"system/site/menu");

?>