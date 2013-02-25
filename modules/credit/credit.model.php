<?php
/******************************
 * $File: credit.model.php
 * $Description: 积分操作错误提示信息
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$MsgInfo["credit_class_id_empty"] = "积分分类id不能为空";
$MsgInfo["credit_class_del_type_exiest"] = "积分类型有存在，不能删除此分类";
$MsgInfo["credit_class_update_type_exiest"] = "积分类型有存在，不能修改此分类";
$MsgInfo["credit_class_name_empty"] = "积分分类名称不能为空";
$MsgInfo["credit_class_nid_empty"] = "积分分类标识名不能为空";
$MsgInfo["credit_class_nid_exiest"] = "积分分类标识名已经存在";
$MsgInfo["credit_class_add_success"] = "积分分类添加成功";
$MsgInfo["credit_class_update_success"] = "积分分类修改成功";
$MsgInfo["credit_class_del_success"] = "积分分类删除成功";
$MsgInfo["credit_class_not_exiest"] = "积分分类不存在";
$MsgInfo["credit_class_del_rank_exiest"] = "积分分类有等级存在，不能删除";


$MsgInfo["credit_type_name_empty"] = "积分类型名称不能为空";
$MsgInfo["credit_type_nid_empty"] = "积分类型标识名不能为空";
$MsgInfo["credit_type_value_empty"] = "积分类型积分值不能为空";
$MsgInfo["credit_type_nid_exiest"] = "积分类型标识名已经存在";
$MsgInfo["credit_type_interval_empty"] = "积分类型时间不能为空";
$MsgInfo["credit_type_award_times_empty"] = "积分类型次数不能为空";
$MsgInfo["credit_type_class_id_empty"] = "积分类型分类不能为空";
$MsgInfo["credit_type_add_success"] = "积分类型添加成功";
$MsgInfo["credit_type_update_success"] = "积分类型修改成功";
$MsgInfo["credit_type_del_credit_exiest"] = "积分类型有积分存在，不能删除";
$MsgInfo["credit_type_update_credit_exiest"] = "积分类型有积分存在，不能修改";
$MsgInfo["credit_type_del_success"] = "积分类型删除成功";


$MsgInfo["credit_rank_id_empty"] = "积分等级id不能为空";
$MsgInfo["credit_rank_add_success"] = "积分等级添加成功";
$MsgInfo["credit_rank_update_success"] = "积分等级修改成功";
$MsgInfo["credit_rank_del_success"] = "积分等级删除成功";
$MsgInfo["credit_rank_name_empty"] = "积分等级名称不能为空";


$MsgInfo["credit_log_id_empty"] = "积分记录id不能为空";
$MsgInfo["credit_log_not_exiest"] = "积分记录不存在";
?>