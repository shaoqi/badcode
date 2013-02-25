<?php
/******************************
 * $File: borrow.style.install.php
 * $Description:还款方式安装文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

$sql = "CREATE TABLE IF NOT EXISTS `{borrow_fee}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` varchar(50) NOT NULL COMMENT '标识名',
  `name` varchar(50) NOT NULL COMMENT '费用',
  `title` varchar(50) NOT NULL COMMENT '名称',
  `status` int(11) NOT NULL COMMENT '状态',
  `borrow_types` varchar(200) NOT NULL COMMENT '借款方式',
  `type` varchar(50) NOT NULL COMMENT '类型',
  `fee_type` int(2) NOT NULL COMMENT '费用类型',
  `vip_borrow_scale` decimal(11,2) NOT NULL COMMENT 'vip借款本金比例',
  `all_borrow_scale` decimal(11,2) NOT NULL COMMENT '会员借款本金比例',
  `vip_borrow_scales` decimal(11,2) NOT NULL COMMENT 'vip借款比例方式',
  `vip_borrow_scales_month` int(11) NOT NULL COMMENT 'vip会员比例的月数',
  `vip_borrow_scales_scale` decimal(11,2) NOT NULL COMMENT 'vip会员借款本金月数的比例',
  `vip_borrow_scales_max` decimal(11,2) NOT NULL COMMENT 'vip借款的比例最大值',
  `all_borrow_scales` decimal(11,2) NOT NULL COMMENT '普通会员借款比例方式',
  `all_borrow_scales_month` int(11) NOT NULL COMMENT '普通会员借款比例方式的月数',
  `all_borrow_scales_scale` decimal(11,2) NOT NULL COMMENT '普通会员借款方式月数的比例',
  `all_borrow_scales_max` decimal(11,2) NOT NULL COMMENT '普通会员最高的利率',
  `vip_advance_scale` decimal(11,2) NOT NULL COMMENT 'vip提前还款的比例',
  `vip_advance_days` int(11) NOT NULL COMMENT 'vip提前还款的天数',
  `all_advance_scale` decimal(11,2) NOT NULL COMMENT '普通会员提前还款的比例',
  `all_advance_days` int(11) NOT NULL COMMENT '普通会员提前还款的比例',
  `vip_repay_scale` decimal(11,2) NOT NULL COMMENT 'vip正常还款比例',
  `all_repay_scale` decimal(11,2) NOT NULL COMMENT '普通会员正常还款比例',
  `vip_borrow_late_scale` decimal(11,2) NOT NULL COMMENT 'vip借款者逾期的比例',
  `vip_borrow_late_days` int(11) NOT NULL COMMENT 'vip借款者逾期的天数',
  `all_borrow_late_scale` decimal(11,2) NOT NULL COMMENT '普通借款者逾期比例',
  `all_borrow_late_days` int(11) NOT NULL COMMENT '普通借款者逾期的天数',
  `vip_tender_late_scale` decimal(11,2) NOT NULL COMMENT 'vip投资者逾期收款的比例',
  `vip_tender_late_days` int(11) NOT NULL COMMENT 'vip投资者逾期的天数',
  `all_tender_late_scale` decimal(11,2) NOT NULL COMMENT '普通投资者逾期的比例',
  `all_tender_late_days` int(11) NOT NULL COMMENT '普通投资者逾期的天数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  ;";
$mysql->db_querys($sql);

?>