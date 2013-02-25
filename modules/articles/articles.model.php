<?
/******************************
 * $File: articles.model.php
 * $Description: 文章语言提示包
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$MsgInfo["articles_type_nid_exiest"] = "类型别名已经存在";
$MsgInfo["articls_type_name_empty"] = "类型名称不能为空";
$MsgInfo["articls_type_nid_empty"] = "类型别名不能为空";
$MsgInfo["articles_type_add_success"] = "类型添加成功";
$MsgInfo["articles_type_update_success"] = "类型修改成功";
$MsgInfo["articles_type_id_empty"] = "文章类型id不能为空";
$MsgInfo["articles_type_del_success"] = "类型删除成功";
$MsgInfo["articles_type_not_exiest"] = "文章类型不存在，请不要乱操作";
$MsgInfo["articles_type_del_pid_exiest"] = "有子类栏目，不能删除";
$MsgInfo["articles_type_del_article_exiest"] = "栏目下有文章，不能删除栏目";


$MsgInfo["articles_error"] = "您的操作有误";
$MsgInfo["articles_verify_yes"] = "已经审核通过";
$MsgInfo["articles_action_success"] = "文章操作成功";
$MsgInfo["articles_yes_not"] = "此文章已经通过不能修改和删除";
$MsgInfo["articles_name_empty"] = "文章标题不能为空";
$MsgInfo["articles_add_success"] = "文章添加成功";
$MsgInfo["articles_contents_empty"] = "文章内容不能为空";
$MsgInfo["articles_not_exiest"] = "文章不存在";
$MsgInfo["articles_type_id_empty"] = "文章的栏目不能为空";
$MsgInfo["articles_id_empty"] = "文章id为空";
$MsgInfo["articles_password_empty"] = "你设定了加密，密码不能为空。";
$MsgInfo["articles_add_type_empty"] = "请先添加文章类型。";

$MsgInfo["articles_del_success"] = "文章删除成功";

$MsgInfo["articles_page_name_empty"] = "页面标题不能为空";
$MsgInfo["articles_page_nid_empty"] = "页面标识名不能为空";
$MsgInfo["articles_page_nid_exiest"] = "页面标识名已经存在";
$MsgInfo["articles_page_not_exiest"] = "页面不存在";
$MsgInfo["articles_page_id_empty"] = "页面id为空";
$MsgInfo["articles_page_password_empty"] = "你设定了加密，密码不能为空。";
$MsgInfo["articles_page_del_pid_exiest"] = "有子类栏目，不能删除";
?>