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

CREATE TABLE IF NOT EXISTS `{message}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '发送用户',
  `type` varchar(100) NOT NULL COMMENT '发送类型',
  `status` int(11) NOT NULL COMMENT '状态',
  `receive_value` longtext NOT NULL COMMENT '接收id',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `contents` text NOT NULL COMMENT '内容',
  `addtime` varchar(50) NOT NULL DEFAULT '',
  `addip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY user_id(`user_id`),
  KEY `type`(`type`)
) ENGINE=MyISAM ;


CREATE TABLE IF NOT EXISTS `{message_receive}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '接收人',
  `status` int(11) NOT NULL COMMENT '状态',
  `send_id` int(11) NOT NULL,
  `send_userid` int(11) NOT NULL DEFAULT '0' COMMENT '发送用户',
  `type` varchar(50) NOT NULL COMMENT '类型',
  `receive_id` longtext NOT NULL COMMENT '接收人用户id',
  `receive_yes` longtext NOT NULL COMMENT '已接收id',
  `receive_value` longtext NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `contents` text NOT NULL COMMENT '内容',
  `addtime` varchar(50) NOT NULL DEFAULT '',
  `addip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `user_id`(`user_id`),
  KEY `send_id`(`send_id`)
) ENGINE=MyISAM  ;

";

$mysql->db_querys($sql);
