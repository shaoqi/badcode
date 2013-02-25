<?php
/******************************
 * $File: borrow.style.int.php
 * $Description: 借款还款方式后台处理
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问
$sql = "CREATE TABLE IF NOT EXISTS `{borrow_type}` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `nid` varchar(50) NOT NULL COMMENT '类型标识名',
  `status` int(11) NOT NULL COMMENT '状态',
  `name` varchar(50) NOT NULL COMMENT '名称',
  `title` varchar(50) NOT NULL COMMENT '标题',
  `apr_first` decimal(11,2) NOT NULL COMMENT '开始年利率',
  `apr_end` decimal(11,2) NOT NULL COMMENT '结束年利率',
  `period_first` int(11) NOT NULL COMMENT '开始有效期',
  `period_end` int(11) NOT NULL COMMENT '开始结束期',
  `validate_first` int(11) NOT NULL COMMENT '开始有效期',
  `validate_end` int(11) NOT NULL COMMENT '结束有效期',
  `styles` varchar(200) NOT NULL COMMENT '还款方式',
  `frost_scale` decimal(11,2) NOT NULL COMMENT '冻结保证金比例',
  `late_days` int(11) NOT NULL COMMENT '多久开始进行垫付',
  `vip_late_scale` decimal(11,2) NOT NULL COMMENT 'vip逾期垫付本息比例',
  `all_late_scale` decimal(11,2) NOT NULL COMMENT '普通会员垫付本金比例',
  `system_borrow_full_status` int(11) NOT NULL COMMENT '系统满标审核',
  `system_borrow_repay_status` int(11) NOT NULL COMMENT '系统用户还款',
  `system_web_repay_status` int(11) NOT NULL COMMENT '系统逾期自动垫付',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  ;";
$mysql->db_querys($sql);

?>