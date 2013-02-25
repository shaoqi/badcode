<?php
/******************************
 * $File: admin.install.php
 * $Description: 管理安装文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问
$sql = "

CREATE TABLE IF NOT EXISTS `{system}` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(30) DEFAULT NULL COMMENT '参数名称',
  `type_id`int(11) DEFAULT 0 COMMENT '参数类型',
  `nid` char(32) DEFAULT NULL COMMENT '参数标识名',
  `value` text COMMENT '参数值',
  `code` varchar(32) DEFAULT NULL COMMENT '参数所属的模块',
  `type` varchar(50) NOT NULL COMMENT '类型',
  `style` int(2) DEFAULT NULL COMMENT '参数值的样式',
  `status` int(2) DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `user_id` (`nid`)
) ENGINE=MyISAM   ;


INSERT INTO `{system}` (`name`, `nid`, `value`, `code`, `type`, `style`, `status`) VALUES
('关闭站点（仅后台使用）', 'con_webopen', '1', 'system', '', 1, 1),
('网站关闭信息', 'con_closemsg', '', 'system', '', 1, 1),
('网站名称', 'con_webname', '帝友系统', 'system', '', 1, 1),
('网站网址', 'con_weburl', 'www.deayou.com', 'system', '', 1, 1),
('网站路径', 'con_webpath', '', 'system', '', 1, 1),
( '网站logo', 'con_logo', '', 'system', '', 1, 1),
('网站关键词', 'con_keywords', '帝友系统', 'system', '', 1, 1),
('网站描述', 'con_description', '', 'system', '', 1, 1),
('网站模板', 'con_template', '', 'system', '', 1, 1),
('网站备案号', 'con_beian', '', 'system', '', 1, 1),
('网站统计', 'con_tongji', '', 'system', '', 1, 1),
('网站后台地址', 'con_houtai', '', 'system', '', 1, 1),
('邮件服务器', 'con_email_host', '', 'email', '', 1, 1),
('邮件端口', 'con_email_port', '', 'email', '', 1, 1),
('邮件马上发送', 'con_email_now', '0', 'email', '', 1, 1),
('邮箱是否认证', 'con_email_auth', '', 'email', '', 1, 1),
('邮箱地址', 'con_email_url', '', 'email', '', 1, 1),
('发送邮件', 'con_email_from', '', 'email', '', 1, 1),
('发送邮件名称', 'con_email_from_name', '', 'email', '', 1, 1),
('邮箱密码', 'con_email_password', '', 'email', '', 1, 1),
('是否使用图片水印功能', 'con_watermark_status', '0', 'watermark', '', 1, 1),
('水印的文件类型', 'con_watermark_type', '0', 'watermark', '', 1, 1),
('水印文字', 'con_watermark_word', 'deayou.com', 'watermark', '', 1, 1),
('水印图片文件名', 'con_watermark_file', '', 'watermark', '', 1, 1),
('水印图片文字字体大小', 'con_watermark_font', '12', 'watermark', '', 1, 1),
('水印图片文字颜色', 'con_watermark_color', '#FF00003', 'watermark', '', 1, 1),
('添加图片水印后质量参数', 'con_watermark_imgpct', '100', 'watermark', '', 1, 1),
('添加文字水印后质量参数', 'con_watermark_txtpct', '100', 'watermark', '', 1, 1),
('水印位置', 'con_watermark_position', '3', 'watermark', '', 1, 1),
('短信地址', 'con_sms_url', '', 'sms', '1', 1, 0),
('短信状态', 'con_sms_status', '0', 'sms', '0', 1, 1),
('id5状态', 'con_id5_status', '0', 'id5', '', 1, 1),
('id5用户名', 'con_id5_username', '', 'id5', '', 1, 1),
('id5密码','con_id5_password', '', 'id5', '', 1, 1),
('id5费用','con_id5_fee', '', '', '', 1, 1),
('id5实名状态','con_id5_realname_status', '0', 'id5', '', 1, 1),
('id5实名费用','con_id5_realname_fee', '', 'id5', '', 1, 1),
('id5实名次数收费','con_id5_realname_times', '', 'id5', '', 1, 1),
('id5学历认证','con_id5_edu_status', '0', 'id5', '', 1, 1),
('id5学历费用','con_id5_edu_fee', '', 'id5', '', 1, 1),
('id5学历次数','con_id5_edu_times', '', 'id5', '', 1, 1);


CREATE TABLE IF NOT EXISTS `{system_type}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nid` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `code` varchar(50) NOT NULL,
  `status` int(2) NOT NULL,
  `addtime` varchar(50) NOT NULL,
  `addip` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `nid` (`nid`),
  KEY `code` (`code`),
  KEY `code_nid` (`code`,`nid`)
) ENGINE=MyISAM ;


CREATE TABLE IF NOT EXISTS `{site}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `menu_id` int(11) DEFAULT NULL COMMENT '菜单id',
  `status` int(2) NOT NULL COMMENT '状态',
  `name` varchar(255) DEFAULT NULL COMMENT '站点名称',
  `nid` varchar(50) DEFAULT NULL COMMENT '站点别名',
  `pid` int(2) DEFAULT '0' COMMENT '父级',
  `type` varchar(100) NOT NULL COMMENT '站点类型',
  `order` int(11) NOT NULL,
  `value` varchar(255) DEFAULT NULL COMMENT '值',
  `remark` varchar(250) NOT NULL COMMENT '备注',
  `litpic` varchar(50) DEFAULT NULL,
  `index_tpl` varchar(250) DEFAULT NULL,
  `list_tpl` varchar(250) DEFAULT NULL,
  `content_tpl` varchar(250) DEFAULT NULL,
  `keywords` varchar(250) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `menu_id` (`menu_id`),
  KEY `nid` (`nid`),
  KEY `pid` (`pid`),
  KEY `menuid_nid` (`nid`,`menu_id`)
) ENGINE=MyISAM ;


CREATE TABLE IF NOT EXISTS `{site_menu}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '菜单名称',
  `nid` varchar(100) NOT NULL COMMENT '标识名',
  `contents` varchar(250) NOT NULL COMMENT '简介',
  `order` int(11) NOT NULL COMMENT '排序',
  `checked` int(2) NOT NULL COMMENT '是否默认',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `nid` (`nid`)
) ENGINE=MyISAM ;


INSERT INTO `{site_menu}` ( `name`, `nid`, `contents`, `order`, `checked`) VALUES
('帝友菜单', 'deayou', '帝友系统默认菜单', 10, 0);



CREATE TABLE IF NOT EXISTS `{modules}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` varchar(50) DEFAULT NULL COMMENT '标识名',
  `name` varchar(50) DEFAULT NULL,
  `nickname` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `default_field` varchar(200) DEFAULT NULL,
  `content` text,
  `version` varchar(10) DEFAULT NULL,
  `version_new` varchar(100) NOT NULL,
  `author` varchar(50) DEFAULT NULL,
  `date` varchar(20) DEFAULT NULL,
  `update` longtext NOT NULL,
  `status` int(1) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `fields` int(2) DEFAULT NULL,
  `purview` text,
  `remark` text,
  `issent` int(2) DEFAULT NULL,
  `title_name` varchar(100) DEFAULT NULL,
  `onlyone` int(2) DEFAULT NULL,
  `index_tpl` varchar(50) DEFAULT NULL,
  `list_tpl` varchar(50) DEFAULT NULL,
  `content_tpl` varchar(50) DEFAULT NULL,
  `search_tpl` varchar(100) DEFAULT NULL,
  `article_status` int(2) DEFAULT NULL,
  `visit_type` int(2) DEFAULT NULL,
  `addtime` varchar(50) DEFAULT NULL,
  `addip` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `nid` (`nid`)
) ENGINE=MyISAM;

";
$mysql->db_querys($sql);
?>
