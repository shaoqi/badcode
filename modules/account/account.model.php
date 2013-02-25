<?
/******************************
 * $File: account.model.php
 * $Description: 资金提示文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$MsgInfo["recharge_check_yes"] = "此充值已经审核，请不要重复审核。";
$MsgInfo["account_recharge_log"] = "充值成功。";
$MsgInfo["account_recharge_fee_log"] = "充值的费用扣除。";
$MsgInfo["account_recharge_not_exiest"] = "充值记录不存在。";
$MsgInfo["account_recharge_yes_verify"] = "此记录已经审核通过，请不要重复操作。";
$MsgInfo["account_recharge_nid_error"] = "此操作记录重复，请跟网站技术员联系。";
$MsgInfo["account_reacharge_verify_success"] = "充值审核成功";
$MsgInfo["account_log_nid_exiest"] = "资金记录订单号已经存在";
$MsgInfo["account_user_id_empty"] = "用户id不能为空。";
$MsgInfo["account_nid_empty"] = "订单号不能为空";

$MsgInfo["account_cash_max_errot"] = "提现金额大于可用金额";
$MsgInfo["account_cash_id_empty"] = "提现ID不能为空";
$MsgInfo["account_cash_not_exiest"] = "提现不存在，请勿乱操作";
$MsgInfo["account_cash_yes_verify"] = "提现已经审核，不能重复审核";
$MsgInfo["account_cash_verify_success"] = "提现审核成功";
$MsgInfo["account_cash_user_id_empty"] = "用户id不能为空";

$MsgInfo["account_bank_add_success"] = "银行账户添加成功";
$MsgInfo["account_bank_update_success"] = "银行账户修改成功";
$MsgInfo["account_bank_del_success"] = "银行账户删除成功";
$MsgInfo["account_bank_name_empty"] = "银行名称不能为空";
$MsgInfo["account_bank_nid_empty"] = "银行标识名不能为空";
$MsgInfo["account_bank_userid_empty"] = "用户id不能为空";
$MsgInfo["account_bank_users_update_success"] = "用户账户修改成功";
$MsgInfo["account_users_bank_not5"] = "最多只能添加5个账号";

$MsgInfo["account_recharge_userlog_success"] = "用户“#keywords#”在“".date("Y-m-d H:i:s")."”";;

$MsgInfo["account_menu_bank_users"] = "用户银行账户";
$MsgInfo["account_menu_bank"] = "银行账户管理";
$MsgInfo["account_menu_bank_new"] = "添加银行账户";

$MsgInfo["account_name_id"] = "ID";
$MsgInfo["account_name_username"] = "用户名称";
$MsgInfo["account_name_list"] = "用户账户总额列表";
$MsgInfo["account_name_balance"] = "网站收支列表";
$MsgInfo["account_name_users"] = "用户收支列表";
$MsgInfo["account_name_money"] = "操作金额";
$MsgInfo["account_name_total"] = "总金额";
$MsgInfo["account_name_balance"] = "余额";
$MsgInfo["account_name_log"] = "资金记录列表";
$MsgInfo["account_name_balances"] = "网站费用";
$MsgInfo["account_name_balance_frost"] = "不可提现金额";
$MsgInfo["account_name_balance_cash"] = "可提现金额";
$MsgInfo["account_name_frost"] = "冻结金额";
$MsgInfo["account_name_await"] = "待收金额";
$MsgInfo["account_name_income"] = "收入";
$MsgInfo["account_name_expend"] = "支出";
$MsgInfo["account_name_type"] = "类型";
$MsgInfo["account_name_bank"] = "充值银行";
$MsgInfo["account_name_nid"] = "订单号";
$MsgInfo["account_name_payment"] = "支付方式";
$MsgInfo["account_name_recharge"] = "全部充值";
$MsgInfo["account_name_recharge_success"] = "充值成功";
$MsgInfo["account_name_recharge_false"] = "充值失败";
$MsgInfo["account_name_recharge_verify"] = "审核中";
$MsgInfo["account_name_recharge_view"] = "充值查看";
$MsgInfo["account_name_recharge_type"] = "充值类型";
$MsgInfo["account_name_recharge_status"] = "充值状态";
$MsgInfo["account_name_recharge_money"] = "充值金额";
$MsgInfo["account_name_recharge_fee"] = "充值手续费";
$MsgInfo["account_name_recharge_balance"] = "实际到账金额";
$MsgInfo["account_name_recharge_remark"] = "备注";
$MsgInfo["account_name_nid"] = "交易号";
$MsgInfo["account_name_status"] = "状态";
$MsgInfo["account_name_addtime"] = "操作时间";
$MsgInfo["account_name_addip"] = "操作IP";
$MsgInfo["account_name_manage"] = "操作";
$MsgInfo["account_name_edit"] = "修改";
$MsgInfo["account_name_del"] = "删除";
$MsgInfo["account_name_bank_del_msg"] = "是否删除银行的信息";
$MsgInfo["account_name_submit"] = "确认提交";


$MsgInfo["account_name_bank_new"] = "添加银行账户";
$MsgInfo["account_name_bank_list"] = "银行账户列表";
$MsgInfo["account_name_bank_name"] = "银行名称";
$MsgInfo["account_name_bank_status"] = "状态";
$MsgInfo["account_name_bank_nid"] = "银行标识名";
$MsgInfo["account_name_bank_litpic"] = "缩略图";
$MsgInfo["account_name_bank_cash_money"] = "可提现金额";
$MsgInfo["account_name_bank_reach_day"] = "到帐天数";
$MsgInfo["account_name_bank_manage"] = "操作";


$MsgInfo["payment_add_success"] = "操作成功";
$MsgInfo["payment_del_success"] = "删除成功";
$MsgInfo["payment_action_success"] = "操作成功";


$MsgInfo["payment_name_list"] = "已使用列表";
$MsgInfo["payment_name_all"] = "支付管理";
$MsgInfo["payment_name_logo"] = "支付logo";
$MsgInfo["payment_name_name"] = "支付名称";
$MsgInfo["payment_name_manage"] = "管理";
$MsgInfo["payment_name_description"] = "支付简介";
$MsgInfo["payment_name_edit"] = "编辑";
$MsgInfo["payment_name_new"] = "添加";
$MsgInfo["payment_name_del_msg"] = "确定要删除吗?删除后将不可恢复";
$MsgInfo["payment_name_del"] = "删除";
$MsgInfo["payment_name_close"] = "关闭";
$MsgInfo["payment_name_open"] = "开启";
$MsgInfo["payment_name_order"] = "排序";
$MsgInfo["payment_name_litpic"] = "缩略图";
$MsgInfo["payment_name_submit"] = "确认提交";
$MsgInfo["payment_name_reset"] = "重置";



$Linkages['account_type'] = array("");
$Linkages['account_recharge_type'] = array("1"=>"线上充值","2"=>"手动充值","0"=>"线下充值");//资金充值类型
$Linkages['account_recharge_status'] = array("1"=>"审核成功","2"=>"审核失败","0"=>"待审核");//资金充值状态
$Linkages['account_cash_status'] = array("1"=>"成功","2"=>"审核失败","0"=>"申请","3"=>"撤销");//资金提现状态
?>
