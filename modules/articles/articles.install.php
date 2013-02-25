<?
/******************************
 * $File: articles.install.php
 * $Description: 文章安装文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问
$sql = "

CREATE TABLE IF NOT EXISTS `{articles}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '添加用户',
  `type_id` varchar(100) DEFAULT '0' COMMENT '文章栏目',
  `nid` varchar(100) NOT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT '标题',
  `title` varchar(200) DEFAULT NULL COMMENT '简要标题',
  `status` int(2) DEFAULT '0' COMMENT '文章状态',
  `litpic` varchar(255) DEFAULT NULL COMMENT '文章缩略图',
  `flag` varchar(250) DEFAULT NULL COMMENT '文章类型',
  `source` varchar(50) DEFAULT NULL COMMENT '文章来源',
  `public` int(2) NOT NULL,
  `password` varchar(50) NOT NULL,
  `publish` varchar(50) DEFAULT NULL COMMENT '是否发布',
  `author` varchar(50) DEFAULT NULL COMMENT '作者',
  `summary` varchar(255) DEFAULT NULL COMMENT '简要介绍',
  `contents` text COMMENT '内容',
  `credits` longtext NOT NULL COMMENT '积分',
  `tags` varchar(200) NOT NULL COMMENT '标签',
  `order` int(11) DEFAULT '0' COMMENT '排序',
  `hits` int(11) DEFAULT '0' COMMENT '点击数',
  `comment_status` int(11) DEFAULT '0' COMMENT '是否评论',
  `comment_times` int(11) NOT NULL COMMENT '评论次数',
  `comment_usertype` varchar(50) NOT NULL COMMENT '评论用户',
  `addtime` varchar(50) DEFAULT NULL COMMENT '添加时间',
  `addip` varchar(50) DEFAULT NULL COMMENT '添加ip',
  `update_time` varchar(50) NOT NULL,
  `update_ip` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `type_id` (`type_id`),
  KEY `name` (`name`),
  KEY `typeid_name` (`type_id`,`name`),
  KEY `typeid_name_status` (`type_id`,`name`,`status`),
  FULLTEXT KEY `type_id1` (`type_id`)
) ENGINE=MyISAM  ;



CREATE TABLE IF NOT EXISTS `{articles_pages}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '添加用户',
  `nid` varchar(100) NOT NULL COMMENT '标识名',
  `pid` int(11) DEFAULT '0' COMMENT '文章栏目',
  `name` varchar(255) DEFAULT NULL COMMENT '标题',
  `title` varchar(200) DEFAULT NULL COMMENT '简要标题',
  `status` int(2) DEFAULT '0' COMMENT '文章状态',
  `litpic` varchar(255) DEFAULT NULL COMMENT '文章缩略图',
  `flag` varchar(250) DEFAULT NULL COMMENT '文章类型',
  `source` varchar(50) DEFAULT NULL COMMENT '文章来源',
  `public` int(2) NOT NULL,
  `password` varchar(50) NOT NULL,
  `publish` varchar(50) DEFAULT NULL COMMENT '是否发布',
  `author` varchar(50) DEFAULT NULL COMMENT '作者',
  `summary` varchar(255) DEFAULT NULL COMMENT '简要介绍',
  `contents` text COMMENT '内容',
  `tags` varchar(200) NOT NULL COMMENT '标签',
  `order` int(11) DEFAULT '0' COMMENT '排序',
  `hits` int(11) DEFAULT '0' COMMENT '点击数',
  `comment_status` int(11) DEFAULT '0' COMMENT '是否评论',
  `comment_times` int(11) NOT NULL COMMENT '评论次数',
  `comment_usertype` varchar(50) NOT NULL COMMENT '评论用户',
  `addtime` varchar(50) DEFAULT NULL COMMENT '添加时间',
  `addip` varchar(50) DEFAULT NULL COMMENT '添加ip',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `pid` (`pid`),
  KEY `nid` (`nid`),
  KEY `name` (`name`),
  KEY `status` (`status`),
  KEY `pid_name` (`pid`,`name`)
) ENGINE=MyISAM   ;


CREATE TABLE IF NOT EXISTS `{articles_type}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `nid` varchar(100) NOT NULL,
  `pid` int(11) NOT NULL,
  `contents` text NOT NULL,
  `order` int(11) NOT NULL DEFAULT '10' COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `user_id` (`name`),
  KEY `page_id` (`nid`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM  ;

INSERT INTO `{articles_type}` (`id`, `name`, `nid`, `pid`, `contents`, `order`) VALUES
(1, '默认文章', 'default', 0, '', 10);

";
$mysql->db_querys($sql);
?>
