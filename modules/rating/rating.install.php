<?
/******************************
 * $File: ratting.install.php
 * $Description: 用户信息资料的安装
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$sql = "

CREATE TABLE IF NOT EXISTS `{rating_assets}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `status` int(2) NOT NULL,
  `assetstype` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `account` varchar(30) NOT NULL,
  `other` text NOT NULL,
  `verify_remark` varchar(200) NOT NULL,
  `verify_userid` int(11) NOT NULL,
  `verify_time` varchar(50) NOT NULL,
  `addtime` varchar(50) NOT NULL,
  `addip` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM ;


CREATE TABLE IF NOT EXISTS `{rating_company}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '所属用户',
  `status` int(2) NOT NULL COMMENT '认证，0表示审核中，1表示通过，2表示未通过',
  `name` varchar(100) NOT NULL COMMENT '公司名称',
  `tel` varchar(100) NOT NULL COMMENT '电话',
  `address` varchar(200) NOT NULL COMMENT '公司地址',
  `license_num` varchar(30) NOT NULL COMMENT '执照号',
  `tax_num_guo` varchar(30) NOT NULL COMMENT '税务号(国税)',
  `tax_num_di` varchar(30) NOT NULL COMMENT '税务号(地税)',
  `rent_time` varchar(30) NOT NULL COMMENT '租期',
  `rent_money` varchar(30) NOT NULL COMMENT '租金',
  `hangye` varchar(30) NOT NULL,
  `people` varchar(30) NOT NULL,
  `time` varchar(30) NOT NULL,
  `verify_userid` int(11) NOT NULL,
  `verify_remark` varchar(200) NOT NULL,
  `verify_time` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `{rating_contact}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `status` int(2) NOT NULL,
  `linkman2` varchar(30) NOT NULL COMMENT '配偶',
  `phone2` varchar(30) NOT NULL COMMENT '配偶手机',
  `linkman3` varchar(30) NOT NULL COMMENT '直系亲属',
  `phone3` varchar(30) NOT NULL COMMENT '直系亲属手机',
  `linkman4` varchar(30) NOT NULL COMMENT '同事',
  `phone4` varchar(30) NOT NULL COMMENT '同事电话',
  `linkman5` varchar(30) NOT NULL COMMENT '紧急联系人',
  `phone5` varchar(30) NOT NULL COMMENT '紧急联系人电话',
  `verify_userid` int(11) NOT NULL,
  `verify_remark` varchar(200) NOT NULL,
  `verify_time` varchar(50) NOT NULL,
  `addtime` varchar(30) NOT NULL,
  `addip` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  ;


CREATE TABLE IF NOT EXISTS `{rating_educations}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '所属用户',
  `status` int(2) NOT NULL COMMENT '认证，0表示审核中，1表示通过，2表示未通过',
  `name` varchar(100) NOT NULL COMMENT '学校名称',
  `degree` varchar(100) NOT NULL COMMENT '学历',
  `in_year` varchar(100) NOT NULL COMMENT '入学时间',
  `professional` varchar(100) NOT NULL COMMENT '专业',
  `verify_userid` int(11) NOT NULL COMMENT '审核用户id',
  `verify_remark` varchar(200) NOT NULL COMMENT '审核备注',
  `verify_time` varchar(50) NOT NULL COMMENT '审核时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM ;


CREATE TABLE IF NOT EXISTS `{rating_finance}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `status` int(2) NOT NULL,
  `type` int(11) NOT NULL,
  `use_type` int(2) NOT NULL COMMENT '1为收入，2为支出',
  `account` varchar(30) NOT NULL,
  `name` varchar(30) NOT NULL,
  `other` varchar(200) NOT NULL,
  `verify_userid` int(11) NOT NULL,
  `verify_time` varchar(50) NOT NULL,
  `verify_remark` varchar(200) NOT NULL,
  `addtime` varchar(50) NOT NULL,
  `addip` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  ;

CREATE TABLE IF NOT EXISTS `{rating_houses}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '所属用户',
  `status` int(2) NOT NULL COMMENT '认证，0表示审核中，1表示通过，2表示未通过',
  `name` varchar(100) NOT NULL COMMENT '公司名称',
  `address` varchar(250) NOT NULL COMMENT '所在地',
  `areas` varchar(200) NOT NULL COMMENT '房产面积',
  `in_year` varchar(100) NOT NULL COMMENT '建筑年份',
  `repay` varchar(100) NOT NULL COMMENT '供款状况',
  `holder1` varchar(100) NOT NULL COMMENT '所有权1',
  `right1` varchar(100) NOT NULL COMMENT '份额1',
  `holder2` varchar(100) NOT NULL COMMENT '所有权2',
  `right2` varchar(100) NOT NULL COMMENT '份额2',
  `load_year` varchar(100) NOT NULL COMMENT '贷款年限',
  `repay_month` varchar(100) NOT NULL COMMENT '每月还款',
  `balance` varchar(100) NOT NULL COMMENT '还款金额',
  `bank` varchar(100) NOT NULL COMMENT '银行',
  `verify_userid` int(11) NOT NULL,
  `verify_remark` varchar(200) NOT NULL,
  `verify_time` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  ;


CREATE TABLE IF NOT EXISTS `{rating_info}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `status` int(2) NOT NULL,
  `realname` varchar(30) NOT NULL,
  `code` int(11) NOT NULL,
  `card_id` varchar(30) NOT NULL,
  `type` varchar(30) NOT NULL,
  `phone_num` varchar(30) NOT NULL COMMENT '手机号',
  `sex` int(2) NOT NULL,
  `marry` int(2) NOT NULL,
  `children` int(2) NOT NULL,
  `income` int(2) NOT NULL,
  `dignity` int(2) NOT NULL COMMENT '身份',
  `birthday` varchar(30) NOT NULL,
  `school_year` varchar(30) NOT NULL,
  `school` varchar(100) NOT NULL,
  `edu` int(11) NOT NULL,
  `house` int(11) NOT NULL,
  `province` int(11) NOT NULL,
  `city` int(11) NOT NULL,
  `area` int(11) NOT NULL,
  `is_car` int(2) NOT NULL,
  `address` varchar(200) NOT NULL COMMENT '现居住地址',
  `phone` varchar(30) NOT NULL,
  `verify_userid` int(11) NOT NULL,
  `verify_time` varchar(50) NOT NULL,
  `verify_remark` varchar(200) NOT NULL,
  `addtime` varchar(50) NOT NULL,
  `addip` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM   ;


CREATE TABLE IF NOT EXISTS `{rating_job}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '所属用户',
  `type` int(2) NOT NULL,
  `status` int(2) NOT NULL COMMENT '认证，0表示审核中，1表示通过，2表示未通过',
  `name` varchar(100) NOT NULL COMMENT '公司名称',
  `industry` varchar(30) NOT NULL,
  `department` varchar(50) NOT NULL COMMENT '部门',
  `office` varchar(200) NOT NULL COMMENT '职位',
  `address` varchar(100) NOT NULL,
  `peoples` varchar(30) NOT NULL COMMENT '人数',
  `worktime1` varchar(100) NOT NULL COMMENT '入职时间',
  `tel` varchar(30) NOT NULL,
  `verify_userid` int(11) NOT NULL,
  `verify_remark` varchar(200) NOT NULL,
  `verify_time` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM ;
";

$mysql->db_querys($sql);
