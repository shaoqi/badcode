<?php
/******************************
 * $File: account.install.php
 * $Description: 资金安装
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/


if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$sql = "

CREATE TABLE IF NOT EXISTS `{account}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0' COMMENT '用户名称',
  `total` decimal(11,2) DEFAULT '0.00' COMMENT '资金总额',
  `income` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '收入',
  `expend` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '支出',
  `balance` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '可用金额',
  `balance_cash` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '可提现',
  `balance_frost` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '不可提现',
  `frost` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '冻结金额',
  `await` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '待收金额',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_2` (`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  ;


CREATE TABLE IF NOT EXISTS `{account_balance}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nid` varchar(32) NOT NULL COMMENT '交易号',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `type` varchar(50) NOT NULL COMMENT '类型',
  `money` decimal(11,2) NOT NULL,
  `total` decimal(11,2) NOT NULL COMMENT '网站总金额',
  `balance` decimal(11,2) NOT NULL COMMENT '净赚余额',
  `income` decimal(11,2) NOT NULL COMMENT '收入',
  `expend` decimal(11,2) NOT NULL COMMENT '支出',
  `remark` text NOT NULL COMMENT '备注',
  `addtime` varchar(32) NOT NULL,
  `addip` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `nid` (`nid`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`),
  KEY `userid_type` (`user_id`,`type`),
  KEY `userid_nid_type` (`user_id`,`nid`,`type`)
) ENGINE=MyISAM  ;


CREATE TABLE IF NOT EXISTS `{account_bank}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `status` int(2) NOT NULL,
  `name` varchar(100) DEFAULT NULL COMMENT '银行名称',
  `nid` varchar(50) NOT NULL,
  `litpic` varchar(255) DEFAULT NULL COMMENT '缩略图地址',
  `cash_money` varchar(100) DEFAULT NULL COMMENT '最高体现金额',
  `reach_day` int(11) NOT NULL COMMENT '到账日期',
  `addtime` varchar(50) NOT NULL,
  `addip` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `nid` (`nid`),
  KEY `name` (`name`)
) ENGINE=MyISAM ;



CREATE TABLE IF NOT EXISTS `{account_cash}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0' COMMENT '用户ID',
  `nid` varchar(100) NOT NULL,
  `status` int(2) DEFAULT '0' COMMENT '状态',
  `account` varchar(100) DEFAULT '0' COMMENT '账号',
  `bank` varchar(302) DEFAULT NULL COMMENT '所属银行',
  `bank_id` int(30) NOT NULL,
  `branch` varchar(100) DEFAULT NULL COMMENT '支行',
  `province` int(11) NOT NULL,
  `city` int(11) NOT NULL,
  `total` decimal(11,2) DEFAULT '0.00' COMMENT '总额',
  `credited` decimal(11,2) DEFAULT '0.00' COMMENT '到账总额',
  `fee` varchar(20) DEFAULT '0' COMMENT '手续费',
  `verify_userid` decimal(11,2) DEFAULT NULL,
  `verify_time` int(11) DEFAULT NULL,
  `verify_remark` varchar(250) DEFAULT NULL,
  `addtime` varchar(11) DEFAULT NULL,
  `addip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `nid` (`nid`)
) ENGINE=MyISAM  ;

CREATE TABLE IF NOT EXISTS `{account_log}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nid` varchar(50) NOT NULL COMMENT '交易号',
  `user_id` int(11) DEFAULT '0' COMMENT '用户ID',
  `type` varchar(100) DEFAULT NULL COMMENT '类型',
  `total` decimal(11,2) DEFAULT '0.00' COMMENT '总金额',
  `total_old` decimal(11,2) NOT NULL COMMENT '上次总金额',
  `money` decimal(11,2) DEFAULT NULL COMMENT '操作金额',
  `income` decimal(11,2) DEFAULT '0.00' COMMENT '收入',
  `income_old` decimal(11,2) NOT NULL,
  `income_new` decimal(11,2) NOT NULL,
  `expend` decimal(11,2) DEFAULT '0.00' COMMENT '支出',
  `expend_old` decimal(11,2) NOT NULL,
  `expend_new` decimal(11,2) NOT NULL,
  `balance` decimal(11,2) DEFAULT '0.00' COMMENT '可用余额',
  `balance_old` decimal(11,2) NOT NULL COMMENT '旧的可用余额',
  `balance_new` decimal(11,2) NOT NULL COMMENT '最新的金额',
  `balance_cash` decimal(11,2) NOT NULL COMMENT '可提现金额',
  `balance_cash_old` decimal(11,2) NOT NULL,
  `balance_cash_new` decimal(11,2) NOT NULL,
  `balance_frost` decimal(11,2) NOT NULL COMMENT '不可提现冻结金额',
  `balance_frost_old` decimal(11,2) NOT NULL,
  `balance_frost_new` decimal(11,2) NOT NULL,
  `frost` decimal(11,2) NOT NULL COMMENT '冻结金额',
  `frost_old` decimal(11,2) NOT NULL COMMENT '冻结旧金额',
  `frost_new` decimal(11,2) NOT NULL COMMENT '新的冻结金额',
  `await` decimal(11,2) NOT NULL COMMENT '待收金额',
  `await_old` decimal(11,2) NOT NULL,
  `await_new` decimal(11,2) NOT NULL COMMENT '新的待收余额',
  `to_userid` int(11) DEFAULT NULL COMMENT '交易对方',
  `remark` varchar(250) DEFAULT '0' COMMENT '备注',
  `addtime` varchar(11) DEFAULT NULL,
  `addip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nid_2` (`nid`),
  KEY `nid` (`nid`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`),
  KEY `userid_type` (`user_id`,`type`),
  KEY `userid_nid_type` (`user_id`,`nid`,`type`)
) ENGINE=MyISAM   ;


CREATE TABLE IF NOT EXISTS `{account_recharge}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nid` varchar(32) DEFAULT NULL COMMENT '订单号',
  `user_id` int(11) DEFAULT '0' COMMENT '用户ID',
  `status` int(2) DEFAULT '0' COMMENT '状态',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '金额',
  `fee` decimal(11,2) NOT NULL COMMENT '费用',
  `balance` decimal(11,2) NOT NULL COMMENT '实际到账余额',
  `payment` varchar(100) DEFAULT NULL COMMENT '所属银行',
  `url` longtext NOT NULL COMMENT '地址',
  `sign` longtext NOT NULL COMMENT '加密',
  `return` text COMMENT '返回的数值',
  `type` varchar(10) DEFAULT '0' COMMENT '类型',
  `remark` varchar(250) DEFAULT '0' COMMENT '备注',
  `verify_userid` int(11) DEFAULT '0' COMMENT '审核人',
  `verify_time` varchar(11) DEFAULT NULL COMMENT '审核时间',
  `verify_remark` varchar(250) DEFAULT '' COMMENT '审核备注',
  `addtime` varchar(11) DEFAULT NULL,
  `addip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nid_2` (`nid`),
  KEY `user_id` (`user_id`),
  KEY `verify_userid` (`verify_userid`),
  KEY `nid` (`nid`)
) ENGINE=MyISAM ;


CREATE TABLE IF NOT EXISTS `{account_users}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nid` varchar(32) NOT NULL COMMENT '交易号',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `type` varchar(50) NOT NULL,
  `money` decimal(11,2) NOT NULL,
  `total` decimal(11,2) NOT NULL COMMENT '用户总金额',
  `balance` decimal(11,2) NOT NULL COMMENT '余额',
  `income` decimal(11,2) NOT NULL COMMENT '收入',
  `expend` decimal(11,2) NOT NULL COMMENT '支出',
  `remark` text NOT NULL COMMENT '备注',
  `addtime` varchar(32) NOT NULL,
  `addip` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `nid` (`nid`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`),
  KEY `userid_type` (`user_id`,`type`),
  KEY `userid_nid_type` (`user_id`,`nid`,`type`)
) ENGINE=MyISAM   ;


CREATE TABLE IF NOT EXISTS `{account_users_bank}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0' COMMENT '用户ID',
  `status` int(2) NOT NULL,
  `account` varchar(100) DEFAULT NULL COMMENT '账号',
  `bank` varchar(50) DEFAULT NULL COMMENT '所属银行',
  `branch` varchar(100) DEFAULT NULL COMMENT '支行',
  `province` int(5) DEFAULT '0' COMMENT '省份',
  `city` int(5) DEFAULT '0' COMMENT '城市',
  `area` int(5) DEFAULT '0' COMMENT '区',
  `addtime` varchar(11) DEFAULT NULL,
  `addip` varchar(15) DEFAULT NULL,
  `update_time` varchar(50) NOT NULL,
  `update_ip` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM ;



CREATE TABLE IF NOT EXISTS `{account_web}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `money` varchar(10) NOT NULL,
  `type` varchar(30) NOT NULL,
  `nid` varchar(100) NOT NULL,
  `remark` text NOT NULL,
  `addtime` varchar(30) NOT NULL,
  `addip` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `nid` (`nid`),
  KEY `user_id` (`nid`)
) ENGINE=MyISAM  ;


CREATE TABLE IF NOT EXISTS `{account_payment}` (
  `id` mediumint(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL COMMENT '名称',
  `nid` varchar(100) DEFAULT NULL COMMENT '标识名',
  `status` smallint(3) unsigned DEFAULT '0' COMMENT '状态',
  `litpic` varchar(100) NOT NULL COMMENT '缩略图',
  `style` int(2) DEFAULT NULL COMMENT '类型',
  `config` longtext COMMENT '相关信息',
  `description` longtext COMMENT '描述',
  `order` smallint(3) unsigned DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ;
";

$mysql->db_querys($sql);
