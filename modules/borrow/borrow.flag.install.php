<?php
/******************************
 * $File: borrow.flag.install.php
 * $Description:标签安装文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

$sql = "CREATE TABLE IF NOT EXISTS `{borrow_flag}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '名称',
  `title` varchar(200) NOT NULL COMMENT '名称',
  `status` int(2) NOT NULL COMMENT '状态',
  `nid` varchar(100) NOT NULL COMMENT '标识名',
  `style` int(2) NOT NULL COMMENT '图片的样式',
  `fileurl` varchar(200) NOT NULL COMMENT '本地图片模式',
  `upfiles_id` int(11) NOT NULL COMMENT '上传的文件id',
  `remark` varchar(200) NOT NULL COMMENT '备注',
  `order` int(10) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  ;";
$mysql->db_querys($sql);

?>