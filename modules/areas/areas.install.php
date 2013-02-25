<?
/******************************
 * $File: areas.install.php
 * $Description: 地区的安装文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$sql = "

CREATE TABLE IF NOT EXISTS `{areas}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `nid` varchar(50) DEFAULT NULL,
  `pid` int(11) DEFAULT NULL,
  `province` int(11) NOT NULL DEFAULT '0',
  `city` int(11) NOT NULL DEFAULT '0',
  `domain` varchar(100) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nid` (`nid`),
  KEY `pid` (`pid`),
  KEY `province` (`province`),
  KEY `city` (`city`),
  KEY `nid_pid` (`nid`,`pid`)
) 

";
$mysql->db_querys($sql);
?>
