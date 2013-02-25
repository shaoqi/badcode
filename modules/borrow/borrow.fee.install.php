<?php
/******************************
 * $File: borrow.fee.install.php
 * $Description: 费用的安装文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问
$sql = "CREATE TABLE IF NOT EXISTS `{borrow_style}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` varchar(50) NOT NULL COMMENT '标示名',
  `status` int(11) NOT NULL COMMENT '是否启用',
  `name` varchar(50) NOT NULL COMMENT '名称',
  `title` varchar(50) NOT NULL COMMENT '名称，可改',
  `contents` longtext NOT NULL COMMENT '算法公式',
  `remark` longtext NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  ;";
$mysql->db_querys($sql);

?>