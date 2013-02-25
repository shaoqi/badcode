<?php
/******************************
 * $File:admin.model.php
 * $Description: 管理后语言包
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问


$MsgInfo["admin_system_name"] = "系统设置";
$MsgInfo["admin_system_con_webopen"] = "网站是否开启";
$MsgInfo["admin_system_con_closemsg"] = "网站关闭内容";
$MsgInfo["admin_system_con_webname"] = "网站名称";
$MsgInfo["admin_system_con_weburl"] = "网站网址";
$MsgInfo["admin_system_con_webpath"] = "网站路径";
$MsgInfo["admin_system_con_logo"] = "网站logo地址";
$MsgInfo["admin_system_con_keywords"] = "网站关键词";
$MsgInfo["admin_system_con_description"] = "网站描述";
$MsgInfo["admin_system_con_beian"] = "网站备案";
$MsgInfo["admin_system_con_template"] = "网站模板";
$MsgInfo["admin_system_con_tongji"] = "网站统计";


$MsgInfo["admin_system_con_watermark_status"] = "上传的图片是否使用图片水印功能";
$MsgInfo["admin_system_con_watermark_type"] = "选择水印的文件类型";
$MsgInfo["admin_system_con_watermark_word"] = "水印文字";
$MsgInfo["admin_system_con_watermark_file"] = "水印图片文件名（如果不存在，则使用文字水印）";
$MsgInfo["admin_system_con_watermark_font"] = "水印图片文字字体大小";
$MsgInfo["admin_system_con_watermark_color"] = "水印图片文字颜色（默认#FF0000为红色）";
$MsgInfo["admin_system_con_watermark_imgpct"] = "添加图片水印后质量参数,值越大，合并程序越低";
$MsgInfo["admin_system_con_watermark_txtpct"] = "添加文字水印后质量参数,值越小，合并程序越低";
$MsgInfo["admin_system_con_watermark_position"] = "水印位置";


$MsgInfo["admin_system_not_con"] = "参数要以con_为开头";
$MsgInfo["admin_system_del_error"] = "系统参数删除有误";
$MsgInfo["admin_system_nid_exiest"] = "参数标识名已经存在";
$MsgInfo["admin_system_not_del"] = "此参数不能删除";



$MsgInfo["admin_name_submit"] = "提交";
$MsgInfo["admin_name_reset"] = "重置";
$MsgInfo["admin_name_success"] = "成功";
$MsgInfo["admin_name_false"] = "失败";
$MsgInfo["admin_name_wait"] = "等待";
$MsgInfo["admin_name_del"] = "删除";
$MsgInfo["admin_name_edit_not_empty"] = "不修改请为空";

$MsgInfo["admin_clearcache_title"] = "清空缓存";
$MsgInfo["admin_clearcache_content"] = "清空网站的缓存，也就是清空data/compiles下的所有文件";
$MsgInfo["admin_clearcache_success"] = "清空缓存成功";

$MsgInfo["admin_info_success"] = "修改成功";

$MsgInfo["admin_email_name"] = "邮箱设置";
$MsgInfo["admin_watermark_name"] = "水印设置";


$MsgInfo["admin_email_log_name"] = "邮箱发送记录";
$MsgInfo["admin_email_active_name"] = "邮箱激活记录";
$MsgInfo["admin_email_success"] = "邮箱设置成功";
$MsgInfo["admin_email_success_check"] = "邮箱设置成功,邮箱发送正常";
$MsgInfo["admin_email_false"] = "邮箱设置成功,但邮箱发送功能异常，请检查你的邮箱设置";
$MsgInfo["admin_email_con_auth"] = "SMTP服务器是否需要验证";
$MsgInfo["admin_email_con_host"] = "SMTP服务器";
$MsgInfo["admin_email_con_url"] = "邮箱地址";
$MsgInfo["admin_email_con_password"] = "邮箱密码";
$MsgInfo["admin_email_con_from"] = "发件人Email";
$MsgInfo["admin_email_con_from_name"] = "发件人昵称或姓名";
$MsgInfo["admin_email_con_check"] = "是否进行邮箱检测";


$MsgInfo["admin_email_name_id"] = "ID";
$MsgInfo["admin_email_name_username"] = "用户名";
$MsgInfo["admin_email_name_email"] = "接收邮箱";
$MsgInfo["admin_email_name_sendemail"] = "发送邮箱";
$MsgInfo["admin_email_name_activeemail"] = "激活邮箱";
$MsgInfo["admin_email_name_title"] = "标题";
$MsgInfo["admin_email_name_status"] = "状态";
$MsgInfo["admin_email_name_msg"] = "信息";
$MsgInfo["admin_email_name_addtime"] = "添加时间";
$MsgInfo["admin_email_name_addip"] = "添加ip";



$MsgInfo["admin_site_class_list"] = "分类栏目";
$MsgInfo["admin_site_class_new"] = "添加分类";
$MsgInfo["admin_site_menu_list"] = "菜单列表";
$MsgInfo["admin_site_menu_new"] = "添加菜单";
$MsgInfo["admin_site_page_list"] = "页面列表";
$MsgInfo["admin_site_page_new"] = "添加页面";
$MsgInfo["admin_site_name_class_name"] = "名称";
$MsgInfo["admin_site_name_class_nid"] = "别名";
$MsgInfo["admin_site_name_class_pid"] = "父级";
$MsgInfo["admin_site_name_class_contents"] = "描述";
$MsgInfo["admin_site_class_name_empty"] = "名称不能为空";
$MsgInfo["admin_site_class_nid_empty"] = "别名不能为空";
$MsgInfo["admin_site_name_manage"] = "管理";
$MsgInfo["admin_site_name_new"] = "添加";
$MsgInfo["admin_site_name_edit"] = "修改";
$MsgInfo["admin_site_name_del"] = "删除";
$MsgInfo["admin_site_name_more"] = "批量操作";


$MsgInfo["admin_site_menu_empty"] = "菜单不存在";
$MsgInfo["admin_site_menu_name_empty"] = "菜单名称不能为空";
$MsgInfo["admin_site_menu_nid_empty"] = "菜单标识名不能为空";
$MsgInfo["admin_site_menu_nid_exiest"] = "菜单标识名已经存在";
$MsgInfo["admin_site_menu_add_success"] = "菜单添加成功";
$MsgInfo["admin_site_menu_update_success"] = "菜单修改成功";
$MsgInfo["admin_site_menu_del_success"] = "菜单删除成功";
$MsgInfo["admin_site_menu_id_empty"] = "站点菜单为空";
$MsgInfo["admin_site_menu_site_exiest"] = "菜单还有站点存在，不能删除";
$MsgInfo["admin_site_menu_only_one"] = "菜单不能全部删掉";


$MsgInfo["admin_site_empty"] = "站点不能为空";
$MsgInfo["admin_site_id_empty"] = "站点ID不能为空";
$MsgInfo["admin_site_name_empty"] = "站点名称不能为空";
$MsgInfo["admin_site_nid_empty"] = "站点标识名不能为空";
$MsgInfo["admin_site_nid_exiest"] = "站点标识名已经存在";
$MsgInfo["admin_site_pid_exiest"] = "站点还有子分类，不能删除";
$MsgInfo["admin_site_add_success"] = "站点添加成功";
$MsgInfo["admin_site_update_success"] = "站点修改成功";
$MsgInfo["admin_site_del_success"] = "站点删除成功";


$MsgInfo["admin_module_nid_empty"] = "模块标识名不能为空";
$MsgInfo["admin_module_empty"] = "模块不存在";
$MsgInfo["admin_module_exiest"] = "模块已经存在";


$MsgInfo["admin_system_type_id_empty"] = "系统参数类型id不能为空";
$MsgInfo["admin_system_type_empty"] = "系统参数类型不存在";
$MsgInfo["admin_system_type_name_empty"] = "系统参数类型名称不能为空";
$MsgInfo["admin_system_type_nid_empty"] = "系统参数类型标识名不能为空";
$MsgInfo["admin_system_type_nid_exiest"] = "系统参数类型标识名已经存在";
$MsgInfo["admin_system_type_code_exiest"] = "此类型下面有参数不能修改";




?>
