<?
/******************************
 * $File: borrow.model.php
 * $Description: 借款语言文件
 * $Author: ahui 
 * $Time:2010-08-09
 * $Update:None 
 * $UpdateDate:None 
 * Copyright(c) 2012 by dycms.net. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问
//#borrow_url# 表示借款标的地址
//#borrow_name#  表示借款的名称

$MsgInfo["error"] = "您的操作有误";
$MsgInfo["right"] = "操作成功";

$MsgInfo["user_no_login"] = "你还没登录或登录已超时";
$MsgInfo["borrow_account_no"] = "您的余额不足,无法发布秒标";
$MsgInfo["valicode_error"] = "验证码不正确";
$MsgInfo["borrow_name_empty"] = "借款标题没有填写";
$MsgInfo["borrow_account_empty"] = "借款金额不能为空";
$MsgInfo["borrow_account_over_credituse"] = "借款金额不能大于可用信用借款额度";
$MsgInfo["borrow_account_over_diya"] = "借款金额不能大于可用抵押借款额度";
$MsgInfo["borrow_account_over_min"] = "借款金额不能小于网站的设定最低金额";
$MsgInfo["borrow_account_over_max"] = "借款金额不能大于网站的设定最高金额";
$MsgInfo["borrow_apr_empty"] = "借款利率不能为空";
$MsgInfo["borrow_apr_over_max"] = "借款金额不能大于网站的设定最高金额";
$MsgInfo["borrow_success_msg"] = "你的借款操作已成功，请等待管理员的审核";
$MsgInfo["borrow_is_exist"] = "您已经有一个借款，请先撤销原来的借款再进行操作";
$MsgInfo["borrow_period_season_error"] = "您选择的是按季还款，借款期限请填写3的倍数";
$MsgInfo["borrow_cancel_success"] = "借款标撤回成功";
$MsgInfo["borrow_cancel_yestender"] = "已经有人投标，不能撤回。";
$MsgInfo["borrow_nid_empty"] = "借款id不能为空";
$MsgInfo["borrow_cancel_false"] = "撤销借款标失败，请跟客服联系";
$MsgInfo["borrow_cancel_success"] = "撤销借款标成功，你可以重新发布借款标";
$MsgInfo["borrow_cancel_has"] = "此标已经撤销，不能重复撤销";
$MsgInfo["account_tender_user_cancel"] = "招标[#borrow_url#]失败返回的投标额";
$MsgInfo["borrow_not_exiest"] = "借款标不存在，请不要乱操作";
$MsgInfo["borrow_nid_empty"] = "借款id不能为空";
$MsgInfo["borrow_user_lock"] = "您账号已经被锁定，不能进行投标，请跟管理员联系";
$MsgInfo["borrow_fullcheck_error"] = "此标已经审核通过，请不要重复审核";
$MsgInfo["borrow_verify_error"] = "此标已经审核通过，不能继续审核";
$MsgInfo["borrow_user_id_empty"] = "用户不存在";
$MsgInfo["borrow_verify_success"] = "初审成功";
$MsgInfo["borrow_verify_user_msg"] = "发布了[#borrow_url#]借款标。";
$MsgInfo["borrow_not_reverify"] = "此标还没通过初审，不能复审。";
$MsgInfo["borrow_not_full"] = "标还未满，不能复审。";
$MsgInfo["borrow_tender_not_exiest"] = "投资信息不存在。";
$MsgInfo["borrow_tender_verify_yes"] = "此投资已经通过审核，不能撤回。";
$MsgInfo["borrow_tender_cancel_success"] = "投资撤销成功。";
$MsgInfo["borrow_tender_user_id_re"] = "不能投资自己的标。";
$MsgInfo["borrow_vouch_not_self"] = "不能为自己进行担保。";

$MsgInfo["borrow_reverify_success"] = "满标复审成功";

$MsgInfo["borrow_repay_id_empty"] = "还款的ID不能为空";
$MsgInfo["borrow_repay_yes"] = "已还款，请不要重复还款";
$MsgInfo["borrow_repay_id_empty"] = "还款的ID不能为空";
$MsgInfo["borrow_repay_error"] = "此还款有误，请不要乱操作";
$MsgInfo["borrow_repay_up_notrepay"] = "上一期还未还款，请先还上一期";
$MsgInfo["borrow_repay_account_use_none"] = "你的余额不足，请先充值";
$MsgInfo["borrow_account_over_vouchuse"] = "借款的额度超过担保的额度";
$MsgInfo["borrow_apr_over_max"] = "借款的利率高于最大值";
$MsgInfo["borrow_tiyan_not_public"] = "已经有发布其他的借款标，不能再进行发布体验标";
$MsgInfo["borrow_fullcheck_yes"] = "满标已经审核通过，不能再进行审核";



$MsgInfo["borrow_tiyan_is_exist"] = "体验标已经存在";

$MsgInfo["vip_apply_success"] = "vip会员申请成功，请等待管理员的审核";
$MsgInfo["vip_status_yes"] = "您已经是vip会员，请不要重复添加";

$MsgInfo["equal_account_empty"] = "金额不能为空";
$MsgInfo["equal_period_empty"] = "借款期限不能为空";
$MsgInfo["equal_apr_empty"] = "利率不能为空";
$re_time = (strtotime("2011-05-20")-strtotime(date("Y-m-d",time())))/(60*60*24);

$MsgInfo["remind_tender_user_cancel_title"] = "投资的标#borrow_name#失败";
$MsgInfo["remind_tender_user_cancel_contents"] = "你所投资的标#borrow_url#用户已经撤销借款";
$MsgInfo["amount_tender_user_cancel"] = "所担保的标【#borrow_url#】用户已撤销借款，担保投资额度返回";


$MsgInfo["remind_vouch_user_cancel_title"] = "担保【#borrow_name#】标失败";
$MsgInfo["remind_vouch_user_cancel_contents"] = "你所担保的标【#borrow_url#】用户已经撤销借款";
$MsgInfo["vouch_late_days_30no"] = "此担保还未逾期超过30天，不然进行垫付。";
$MsgInfo["vouch_late_repay"] = "担保垫付成功。";
$MsgInfo["web_late_repay"] = "网站垫付成功。";

$MsgInfo["tender_full_yes"] = "此标已满，请勿再投。";
$MsgInfo["tender_verify_no"] = "此标尚未通过审核。";
$MsgInfo["tender_late_yes"] = "此标已过期。";
$MsgInfo["tender_money_error"] = "请输入正确的金额。";
$MsgInfo["tender_money_min_error"] = "最小的投资金额不能小于。";
$MsgInfo["tender_money_no"] = "余额不足，请先充值。";
$MsgInfo["tender_vouch_full_no"] = "此标是担保标，并且担保的额度还未满，请勿投标。";
$MsgInfo["tender_50_no"] = "投标金额请设置为50的倍数。";


$MsgInfo["tender_friends_error"] = "此标是友情借款，你不是友情借款里的借款人。不能投标";
$MsgInfo["cancel_remark_empty"] = "撤销申请备注不能为空";
$MsgInfo["cancel_remark_success"] = "撤销申请成功，请等待管理员审核。";
$MsgInfo["borrow_cancel_verify_false"] = "撤销审核成功。";

$MsgInfo["borrow_cancel_error"] = "此标不是正在招标的借款，不能撤回。";

$MsgInfo["amount_type_name_empty"] = "额度类型名称不能为空";
$MsgInfo["amount_type_nid_empty"] = "额度类型标识名不能为空";
$MsgInfo["amount_type_nid_exiest"] = "额度类型标识名已经存在";
$MsgInfo["amount_type_add_success"] = "添加额度类型成功";
$MsgInfo["amount_type_update_success"] = "修改额度类型成功";
$MsgInfo["amount_type_del_success"] = "删除额度类型成功";
$MsgInfo["amount_type_empty"] = "额度类型不存在";
$MsgInfo["amount_type_id_empty"] = "额度类型ID不存在";


$MsgInfo["amount_apply_id_empty"] = "额度申请ID不存在";
$MsgInfo["amount_apply_check_yes"] = "此申请已经通过审核";


$MsgInfo["amount_apply_update_success"] = "更新成功";

$MsgInfo["borrow_scale70_not_cancel"] = "借款进度还未达到70%，不能撤标";
$MsgInfo["borrow_scale100_not_cancel"] = "此标已经满标，不能撤标，请等待复审。";
$MsgInfo["borrow_no_more"] = "您的投资金额大于您未还金额的一半";
$MsgInfo["borrow_no_repay"] = "您有借款未还，不能进行投标。";


$MsgInfo["borrow_repay_vouch_error"] = "此标已经担保垫付。";
$MsgInfo["borrow_repay_web_error"] = "此标已经网站垫付。";
$MsgInfo["borrow_apr"] = "你输入的利率不在范围内";

?>
