<?php
/******************************
 * $File: hongbao.install.php
 * $Description: 安装红包数据库
 * $Author: ada 
 * $Time:2012-12-11
 * $Update:
 * $UpdateDate:  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

$sql = " 

CREATE TABLE IF NOT EXISTS `deayou_hongbao_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` varchar(50) NOT NULL COMMENT '标识名',
  `name` varchar(50) NOT NULL COMMENT '红包名称',
  `status` int(11) NOT NULL COMMENT '状态',
  `desc` varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

CREATE TABLE IF NOT EXISTS `deayou_hongbao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` varchar(100) NOT NULL COMMENT '标识名',
  `name` varchar(255) NOT NULL COMMENT '红包名称',
  `status` int(2) NOT NULL COMMENT '红包状态',
  `order` int(11) NOT NULL COMMENT '排序',
  `type_id` int(11) NOT NULL COMMENT '类型id',
  `money` int(11) NOT NULL COMMENT '金额',
  `percent` int(11) NOT NULL COMMENT '中红包几率',
  `available_time` int(11) NOT NULL COMMENT '有效时间',
  `explode_time` int(11) NOT NULL COMMENT '间隔时间',
  `mode` int(2) NOT NULL COMMENT '启动模式,0为手动,1为自动,默认为0',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

CREATE TABLE IF NOT EXISTS `deayou_hongbao_mingxi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `name` varchar(255) NOT NULL COMMENT '红包名称',
  `money` int(11) NOT NULL COMMENT '金额',
  `addtime` int(11) NOT NULL COMMENT '发放时间',
  `status` int(2) NOT NULL COMMENT '状态，0为失败，1为成功，默认为0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

";

$mysql->db_querys($sql);
?>