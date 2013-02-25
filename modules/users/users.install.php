<?
/******************************
 * $File: users.install.php
 * $Description: 用户安装文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$sql = "

CREATE TABLE IF NOT EXISTS `{users}` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(15) NOT NULL COMMENT '用户名',
  `email` char(32) NOT NULL COMMENT '邮箱',
  `password` char(32) NOT NULL COMMENT '密码',
  `paypassword` varchar(100) NOT NULL COMMENT '支付密码',
  `logintime` int(11) NOT NULL COMMENT '登录次数',
  `reg_ip` char(15) NOT NULL COMMENT '注册ip',
  `reg_time` int(10) NOT NULL COMMENT '注册时间',
  `up_ip` char(15) NOT NULL COMMENT '上一次登录ip',
  `up_time` int(10) NOT NULL COMMENT '上一次登录时间',
  `last_ip` char(15) NOT NULL COMMENT '最后登录ip',
  `last_time` int(10) NOT NULL COMMENT '最后登录时间',
  PRIMARY KEY (`user_id`),
  KEY `id` (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `user_id_2` (`user_id`),
  KEY `username_2` (`username`),
  KEY `email_2` (`email`),
  KEY `username_userid` (`user_id`,`username`),
  KEY `userid_email` (`user_id`,`email`),
  KEY `username_email` (`username`,`email`),
  KEY `username_email_userid` (`user_id`,`username`,`email`)
) ENGINE=MyISAM  COMMENT='用户信息表'  ;

INSERT INTO `{users}` (`user_id`, `username`, `email`, `password`, `paypassword`, `logintime`, `reg_ip`, `reg_time`, `up_ip`, `up_time`, `last_ip`, `last_time`) VALUES
(1, 'deayou', '5867950@qq.com', '169a865ce7f5330056588f1989c27371', '', 0, '','','','', '', '');


CREATE TABLE IF NOT EXISTS `{users_log}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `operating` varchar(50) NOT NULL,
  `article_id` varchar(50) NOT NULL,
  `result` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `addtime` varchar(50) NOT NULL,
  `addip` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
   KEY `user_id_2` (`user_id`)
) ENGINE=MyISAM COMMENT='用户记录表'   ;

CREATE TABLE IF NOT EXISTS `{users_type}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL  COMMENT '名称',
  `nid` varchar(100) NOT NULL COMMENT '标识名',
  `remark` varchar(200) NOT NULL  COMMENT '备注',
  `litpic` varchar(100) NOT NULL  COMMENT '类型头像',
  `checked` int(2) NOT NULL COMMENT '是否默认类型',
  `order` int(11) NOT NULL,
  `addtime` varchar(50) NOT NULL,
  `addip` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `name` (`name`),
  KEY `nid` (`nid`)
) ENGINE=MyISAM COMMENT='用户类型表'  ;


INSERT INTO `{users_type}` (`id`, `name`, `nid`, `remark`, `litpic`, `checked`, `order`, `addtime`, `addip`) VALUES
(1, '普通用户', 'user', '', '', 1, 0, '', '');


CREATE TABLE IF NOT EXISTS `{users_admin}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `adminname` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `password` varchar(100) NOT NULL,
  `type_id` int(11) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `purview` longtext NOT NULL,
  `province` int(11) NOT NULL,
  `city` int(11) NOT NULL,
  `qq` varchar(50) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `addtime` varchar(50) NOT NULL,
  `addip` varchar(50) NOT NULL,
  `update_time` varchar(50) NOT NULL,
  `update_ip` varchar(50) NOT NULL,
  `logintimes` int(50) NOT NULL DEFAULT '0',
  `login_time` varchar(50) NOT NULL,
  `login_ip` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `name` (`user_id`),
  KEY `nid` (`type_id`),
  KEY `utype_id` (`user_id`,`type_id`)
) ENGINE=MyISAM COMMENT='管理员表' ;


INSERT INTO `{users_admin}` (`id`, `adminname`, `user_id`, `password`, `type_id`, `remark`, `purview`, `province`, `city`, `addtime`, `addip`, `update_time`, `update_ip`, `qq`, `logintimes`, `login_time`, `login_ip`) VALUES
(1, '帝友', 1, '169a865ce7f5330056588f1989c27371', 1, '总管理员账号', '', 0, 0, '', '', '', '', '', 0, '', '');


CREATE TABLE IF NOT EXISTS `{users_adminlog}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `code` varchar(50) NOT NULL COMMENT '用户模块',
  `type` varchar(50) NOT NULL COMMENT '类型',
  `operating` varchar(50) NOT NULL COMMENT '操作类别',
  `article_id` varchar(50) NOT NULL COMMENT '操作id',
  `result` varchar(50) NOT NULL COMMENT '返回结果',
  `content` text NOT NULL COMMENT '操作内容',
  `data` text NOT NULL COMMENT '数据',
  `addtime` varchar(50) NOT NULL,
  `addip` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `name` (`user_id`)
) ENGINE=MyISAM  COMMENT='管理员记录表' ;


CREATE TABLE IF NOT EXISTS `{users_admin_type}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `nid` varchar(100) NOT NULL,
  `rank` text NOT NULL,
  `purview` text NOT NULL,
  `module` longtext NOT NULL,
  `remark` varchar(200) NOT NULL,
  `litpic` varchar(100) NOT NULL,
  `order` int(11) NOT NULL,
  `addtime` varchar(50) NOT NULL,
  `addip` varchar(50) NOT NULL,
  `update_time` varchar(50) NOT NULL,
  `update_ip` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `nid` (`nid`)
) ENGINE=MyISAM COMMENT='管理员类型表' ;

INSERT INTO `{users_admin_type}` (`id`, `name`, `nid`, `rank`, `purview`, `module`, `remark`, `litpic`, `order`, `addtime`, `addip`, `update_time`, `update_ip`) VALUES
(1, '超级管理员', 'all', '', '', '', '', '', 10, '', '', '', '');


CREATE TABLE IF NOT EXISTS `{users_info}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `niname` varchar(50) NOT NULL COMMENT '昵称',
  `type_id` int(11) NOT NULL COMMENT '用户类型',
  `status` int(2) NOT NULL COMMENT '用户状态',
  `birthday` varchar(100) NOT NULL COMMENT '生日',
  `sex` varchar(10) NOT NULL COMMENT '性别',
  `invite_userid` int(11) NOT NULL COMMENT '邀请人',
  `invite_money` decimal(11,2) NOT NULL COMMENT '邀请人提成金额',
  `realname` varchar(200) NOT NULL COMMENT '真实姓名',
  `realname_status` int(2) NOT NULL COMMENT '真实姓名是否认证',
  `education` varchar(200) NOT NULL COMMENT '学历' ,
  `education_status` int(2) NOT NULL  COMMENT '学历是否认证',
  `phone` varchar(200) NOT NULL  COMMENT '手机',
  `phone_status` int(2) NOT NULL  COMMENT '手机是否认证',
  `video` varchar(200) NOT NULL DEFAULT ''  COMMENT '视频',
  `video_status` int(2) NOT NULL DEFAULT '0'  COMMENT '视频是否认证',
  `qq` varchar(50) NOT NULL  COMMENT 'qq',
  `question` varchar(100) NOT NULL COMMENT '问题',
  `answer` varchar(100) NOT NULL COMMENT '答案',
  `province` int(11) NOT NULL COMMENT '所在地省',
  `city` int(11) NOT NULL COMMENT '所在地城市',
  `area` int(11) NOT NULL COMMENT '所在地',
  `intro` varchar(200) NOT NULL COMMENT '简介',
  `interest` varchar(200) NOT NULL COMMENT '兴趣爱好',
  `impression` longtext NOT NULL COMMENT '印象',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `user_id` (`user_id`),
  KEY `type_id` (`type_id`),
  KEY `userid_typeid` (`user_id`,`type_id`)
) ENGINE=MyISAM  COMMENT='用户信息表' ;

INSERT INTO `{users_info}` (`id`, `user_id`, `niname`, `type_id`, `status`, `birthday`, `sex`) VALUES
(1, 1, '帝友', 1, 1, '', '男');


CREATE TABLE IF NOT EXISTS `{users_email}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `status` int(2) NOT NULL,
  `addtime` varchar(32) NOT NULL,
  `addip` varchar(32) NOT NULL,
  PRIMARY KEY (`id`), 
  KEY `id` (`id`), 
  KEY `user_id` (`user_id`),
  KEY `email` (`email`),
  KEY `userid_email` (`user_id`,`email`)
) ENGINE=MyISAM  COMMENT='用户邮箱表' ;




CREATE TABLE IF NOT EXISTS `{users_email_log}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `send_email` varchar(50) NOT NULL,
  `status` int(2) DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `msg` text,
  `addtime` varchar(50) DEFAULT NULL,
  `addip` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM COMMENT='邮箱发送记录'  ;




CREATE TABLE IF NOT EXISTS `{users_upfiles}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL COMMENT '名称',
  `code` varchar(50) DEFAULT NULL COMMENT '模块',
  `type` varchar(100) NOT NULL COMMENT '类型',
  `article_id` varchar(50) DEFAULT NULL COMMENT '所属模块id',
  `user_id` int(11) NOT NULL,
  `contents` varchar(200) NOT NULL COMMENT '简介',
  `filetype` varchar(50) DEFAULT NULL COMMENT '文件类型',
  `filename` varchar(100) DEFAULT NULL COMMENT '文件名称',
  `filesize` int(10) DEFAULT '0' COMMENT '文件大小',
  `fileurl` varchar(200) DEFAULT NULL COMMENT '文件地址',
  `addtime` varchar(30) DEFAULT NULL COMMENT '添加时间',
  `addip` varchar(30) DEFAULT NULL COMMENT '添加ip',
  `updatetime` varchar(30) DEFAULT NULL COMMENT '修改时间',
  `updateip` varchar(30) DEFAULT NULL COMMENT '修改ip',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM COMMENT='上传记录表' ;



CREATE TABLE IF NOT EXISTS `{users_visit}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `visit_userid` int(11) DEFAULT NULL,
  `addip` varchar(30) DEFAULT NULL,
  `addtime` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `user_id` (`user_id`),
  KEY `visit_userid` (`visit_userid`)
) ENGINE=MyISAM COMMENT='用户访问表'   ;



CREATE TABLE IF NOT EXISTS `{users_vip}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `status` int(2) NOT NULL COMMENT 'vip状态',
  `years` int(2) NOT NULL COMMENT 'vip年限',
  `money` int(11) NOT NULL COMMENT 'vip金钱',
  `kefu_userid` int(11) NOT NULL COMMENT '客服id',
  `remark` varchar(250) NOT NULL COMMENT '备注',
  `first_date` varchar(50) NOT NULL COMMENT '开始时间',
  `end_date` varchar(50) NOT NULL COMMENT '结束时间',
  `verify_userid` int(11) NOT NULL COMMENT '审核id',
  `verify_time` varchar(50) NOT NULL COMMENT '审核时间',
  `verify_remark` varchar(250) NOT NULL COMMENT '审核备注',
  `addtime` varchar(50) NOT NULL COMMENT '申请时间',
  `addip` varchar(50) NOT NULL COMMENT '申请ip',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
   KEY `status` (`status`)
) ENGINE=MyISAM ;


CREATE TABLE IF NOT EXISTS `{users_examines}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '所属用户',
  `code` varchar(100) NOT NULL COMMENT '模块',
  `type` varchar(200) NOT NULL COMMENT '类型',
  `article_id` int(11) NOT NULL COMMENT '内容id',
  `result` varchar(100) NOT NULL COMMENT '结果',
  `verify_userid` int(11) NOT NULL COMMENT '审核人',
  `remark` varchar(200) NOT NULL COMMENT '备注',
  `addtime` varchar(50) NOT NULL,
  `addip` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
   KEY `status` (`user_id`)
) ENGINE=MyISAM ;



CREATE TABLE IF NOT EXISTS `{users_friends}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0' COMMENT '用户',
  `friends_userid` int(11) DEFAULT '0' COMMENT '朋友',
  `type_id` int(11) DEFAULT NULL,
  `status` int(2) DEFAULT '0' COMMENT '状态',
  `type` int(2) DEFAULT '0' COMMENT '类型',
  `content` varchar(255) DEFAULT NULL COMMENT '内容',
  `addtime` varchar(50) DEFAULT NULL,
  `addip` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
   KEY `status` (`user_id`),
   KEY `friends_userid` (`friends_userid`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `{users_friends_invite}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0' COMMENT '用户',
  `friends_userid` int(11) DEFAULT '0' COMMENT '朋友',
  `status` int(2) DEFAULT '0' COMMENT '状态',
  `type` int(2) DEFAULT '0',
  `content` varchar(250) DEFAULT NULL,
  `addtime` varchar(50) DEFAULT NULL,
  `addip` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
   KEY `status` (`user_id`),
   KEY `friends_userid` (`friends_userid`)
) ENGINE=MyISAM  ;

CREATE TABLE IF NOT EXISTS `{users_friends_type}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` int(2) NOT NULL,
  `nid` varchar(100) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `order` int(11) NOT NULL DEFAULT '10' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ;

INSERT INTO `{users_friends_type}` (`id`, `name`, `status`, `nid`, `remark`, `order`) VALUES
(1, '帝友', 1, '', '', 0),
(2, '朋友', 1, '', '', 0);



CREATE TABLE IF NOT EXISTS `{users_viplog}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `money` varchar(10) NOT NULL,
  `first_time` varchar(50) NOT NULL,
  `end_time` varchar(50) NOT NULL,
  `addtime` varchar(50) NOT NULL,
  `addip` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `{users_care}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `article_id` varchar(50) CHARACTER SET gbk NOT NULL,
  `code` varchar(30) CHARACTER SET gbk NOT NULL,
  `addtime` varchar(30) CHARACTER SET gbk NOT NULL,
  `addip` varchar(30) CHARACTER SET gbk NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM
";
$mysql->db_querys($sql);
?>
