<?
/******************************
 * $File: attestations.install.php
 * $Description: 证明材料安装信息
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$sql = "

CREATE TABLE IF NOT EXISTS `{attestations}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `upfiles_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `status` int(2) NOT NULL COMMENT '状态',
  `credit` int(11) NOT NULL COMMENT '积分',
  `addtime` varchar(30) NOT NULL,
  `addip` varchar(30) NOT NULL,
  `verify_time` varchar(30) NOT NULL,
  `verify_userid` int(11) NOT NULL,
  `verify_remark` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  key `user_id` (`user_id`)
) ENGINE=MyISAM   ;


CREATE TABLE IF NOT EXISTS `{attestations_type}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '名称',
  `nid` varchar(100) NOT NULL COMMENT '标识名',
  `order` int(11) NOT NULL COMMENT '排序',
  `credit` int(11) NOT NULL COMMENT '最大积分',
  `validity` int(11) NOT NULL COMMENT '有效期',
  `remark` varchar(200) NOT NULL COMMENT '简介',
  `addtime` varchar(30) NOT NULL,
  `addip` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  key `{nid}`(`nid`)
) ENGINE=MyISAM ;
";

$mysql->db_querys($sql);
