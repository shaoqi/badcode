<?
/******************************
 * $File: group.install.php
 * $Description: 群组
 * $Author: ahui 
 * $Time:2012-03-09
 * $Vesion:1.0
 * $Update:None 
 * $UpdateDate:None 
 * $Weburl:www.dycms.net 
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$sql = "

CREATE TABLE IF NOT EXISTS `deayou_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` int(2) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `public` int(2) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `litpic` int(11) NOT NULL,
  `order` int(10) NOT NULL,
  `manager` longtext NOT NULL,
  `users` longtext NOT NULL,
  `member_count` int(11) NOT NULL,
  `articles_count` int(11) NOT NULL,
  `comment_count` int(11) NOT NULL,
  `addtime` varchar(50) NOT NULL,
  `addip` varchar(50) NOT NULL,
  `verify_userid` int(11) NOT NULL,
  `verify_time` varchar(50) NOT NULL,
  `verify_remark` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  ;


CREATE TABLE IF NOT EXISTS `deayou_group_articles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `status` int(2) NOT NULL,
  `name` varchar(50) NOT NULL,
  `contents` longtext NOT NULL,
  `comment_count` int(11) NOT NULL COMMENT '回复次数',
  `comment_time` varchar(50) NOT NULL COMMENT '最后回复时间',
  `addtime` varchar(50) NOT NULL,
  `addip` varchar(50) NOT NULL,
  `verify_userid` int(11) NOT NULL,
  `verify_remark` varchar(200) NOT NULL,
  `verify_time` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  ;


CREATE TABLE IF NOT EXISTS `deayou_group_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `articles_id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `tid` int(11) NOT NULL COMMENT '直接下级栏目',
  `status` int(2) NOT NULL,
  `name` varchar(50) NOT NULL,
  `contents` longtext NOT NULL,
  `to_userid` int(11) NOT NULL,
  `addtime` varchar(50) NOT NULL,
  `addip` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=24 ;


CREATE TABLE IF NOT EXISTS `deayou_group_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `result` varchar(100) NOT NULL,
  `to_userid` int(11) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `verify_remark` varchar(200) NOT NULL,
  `addtime` varchar(50) NOT NULL,
  `addip` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM   ;


CREATE TABLE IF NOT EXISTS `deayou_group_member` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `status` int(2) NOT NULL,
  `admin_status` int(2) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `times` int(11) NOT NULL COMMENT '申请次数',
  `addtime` varchar(50) NOT NULL,
  `addip` varchar(50) NOT NULL,
  `update_time` varchar(50) NOT NULL,
  `update_ip` varchar(50) NOT NULL,
  `verify_time` varchar(50) NOT NULL,
  `verify_remark` varchar(200) NOT NULL,
  `verify_userid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  ;


CREATE TABLE IF NOT EXISTS `deayou_group_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `nid` varchar(100) NOT NULL,
  `status` int(2) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `order` int(11) NOT NULL,
  `addtime` varchar(50) NOT NULL,
  `addip` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM   ;

";

$mysql->db_querys($sql);
