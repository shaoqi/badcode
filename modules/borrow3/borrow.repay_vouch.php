<?
/******************************
 * $File: borrow.repay_vouch.php
 * $Description: 担保垫付文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

class borrowrepayvouchClass
{

	
	//第一步，更新审核人的信息
	function RepayStep0($data){
		global $mysql;
		
		//判断是否可以还款
		if (IsExiest($data['repay_id'])==""){
			return "borrow_repay_id_empty";
		}
		if (IsExiest($data['user_id'])==""){
			return "borrow_user_id_empty";
		}
		if (IsExiest($data['borrow_nid'])==""){
			return "borrow_nid_empty";
		}
		
		
		//第一步，获取还款的信息
		$sql = "select p1.*,p2.username from `{borrow_repay}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.id={$data['repay_id']} and p1.user_id='{$data['user_id']}' and p1.borrow_nid='{$data['borrow_nid']}'";
		$repay_result= $mysql->db_fetch_array($sql);
		if ($repay_result==""){
			return "borrow_repay_id_empty";
		}
		if ($repay_result["user_id"]!=$data["user_id"]){
			return "borrow_user_id_empty";
		}
		if ($repay_result["status"]!=1){
			return "borrow_repay_error";
		}
		if ($repay_result["repay_status"]==1){
			return "borrow_repay_error";
		}
		if ($repay_result["repay_vouch"]==1){
			return "borrow_repay_vouch_error";
		}
		$repay_id = $repay_result["id"];
		$repay_account = $repay_result["repay_account"];//还款总额
		$repay_period = $repay_result["repay_period"];
		$repay_web = $repay_result["repay_web"];
		$repay_vouch = $repay_result["repay_vouch"];
		$repay_period = $repay_result["repay_period"];
		$repay_account = $repay_result["repay_account"];//还款总额
		$repay_capital = $repay_result["repay_capital"];//还款本金
		$repay_interest = $repay_result["repay_interest"];//还款利息
		$repay_time = $repay_result["repay_time"];//还款时间
		
		//读取借款标的相关信息
		$borrow_nid = $data['borrow_nid'];
		$sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$data['borrow_nid']}'";
		$borrow_result = $mysql->db_fetch_array($sql);
		$status = $borrow_result['status'];
		$borrow_period = $borrow_result['borrow_period'];
		$borrow_frost_account = $borrow_result["borrow_frost_account"];
		$borrow_userid = $borrow_result['user_id'];
		$borrow_account = $borrow_result['account'];
		$borrow_name = $borrow_result['name'];
		$borrow_url = html_entity_decode("<a href=/invest/a{$borrow_nid}.html target=_blank style=color:blue>{$borrow_result['name']}</a>");//借款标地址
		
		//判断上一期是否已还款
		if ($repay_period!=0){
			$_repay_period = $repay_period-1;
			$sql = "select repay_status from `{borrow_repay}` where `repay_period`=$_repay_period and borrow_nid={$borrow_nid}";
			$result = $mysql->db_fetch_array($sql);
			if ($result!=false && $result['repay_status']!=1){
				return "borrow_repay_up_notrepay";
			}
		}
		
		return 1;		
	}
	
	
	//第二步，扣除担保人的担保金额
	//borrow_nid,repay_id
	function RepayStep1($data){
		global $mysql,$_G;
		//判断是否可以还款
		if (IsExiest($data['repay_id'])==""){
			return "borrow_repay_id_empty";
		}
		if (IsExiest($data['user_id'])==""){
			return "borrow_user_id_empty";
		}
		if (IsExiest($data['borrow_nid'])==""){
			return "borrow_nid_empty";
		}
		
		//第一步，获取还款的信息
		$sql = "select p1.*,p2.username from `{borrow_repay}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.id={$data['repay_id']} and p1.user_id='{$data['user_id']}' and p1.borrow_nid='{$data['borrow_nid']}'";
		$repay_result= $mysql->db_fetch_array($sql);
		if ($repay_result==""){
			return "borrow_repay_id_empty";
		}
		if ($repay_result["user_id"]!=$data["user_id"]){
			return "borrow_user_id_empty";
		}
		if ($repay_result["status"]!=1){
			return "borrow_repay_error";
		}
		if ($repay_result["repay_status"]==1){
			return "borrow_repay_error";
		}
		if ($repay_result["repay_vouch_status"]==1){
			return "borrow_repay_vouch_error";
		}
		
		$repay_id = $repay_result["id"];
		$repay_account = $repay_result["repay_account"];//还款总额
		$repay_period = $repay_result["repay_period"];
		$repay_web = $repay_result["repay_web"];
		$repay_vouch = $repay_result["repay_vouch"];
		$repay_period = $repay_result["repay_period"];
		$repay_account = $repay_result["repay_account"];//还款总额
		$repay_capital = $repay_result["repay_capital"];//还款本金
		$repay_interest = $repay_result["repay_interest"];//还款利息
		$repay_time = $repay_result["repay_time"];//还款时间
		
		//读取借款标的相关信息
		$borrow_nid = $data['borrow_nid'];
		$sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$data['borrow_nid']}'";
		$borrow_result = $mysql->db_fetch_array($sql);
		$status = $borrow_result['status'];
		$borrow_userid = $borrow_result['user_id'];
		$borrow_account = $borrow_result['account'];
		$borrow_name = $borrow_result['name'];
		$borrow_username = $borrow_result['username'];
		$borrow_url = html_entity_decode("<a href=/invest/a{$borrow_nid}.html target=_blank style=color:blue>{$borrow_result['name']}</a>");//借款标地址
		
		//读取担保还款信息
		$sql = "select p1.* from `{borrow_vouch_repay}` as p1  where p1.`order` = '{$repay_period}' and p1.borrow_nid='{$borrow_nid}' limit {$data['key']},1";
		$vouch_recover_result = $mysql->db_fetch_array($sql);
		if ($vouch_recover_result ==false) {return -1;}
		
		$money = $vouch_recover_result['repay_account'];
		$log_info["user_id"] = $vouch_recover_result['user_id'];//操作用户id
		$log_info["nid"] = "vouch_late_repay_".$borrow_nid."_".$repay_id."_".$vouch_recover_result['id'];//订单号
		$log_info["money"] = $money;//操作金额
		$log_info["income"] = 0;//收入
		$log_info["expend"] = $money;//支出
		$log_info["balance_cash"] = -$money;//可提现金额
		$log_info["balance_frost"] = 0;//不可提现金额
		$log_info["frost"] = 0;//冻结金额
		$log_info["await"] =0;//待收金额
		$log_info["type"] = "vouch_late_repay";//类型
		$log_info["to_userid"] = 0;//付给谁
		$log_info["remark"] = "对担保借款标[$borrow_url]第".($repay_period+1)."期担保垫付";
		accountClass::AddLog($log_info);
		
		
		$log_info["user_id"] = $vouch_recover_result['user_id'];//操作用户id
		$log_info["nid"] = "vouch_late_repay_await_".$borrow_nid."_".$repay_id."_".$vouch_recover_result['id'];//订单号
		$log_info["money"] = $money;//操作金额
		$log_info["income"] = $money;//收入
		$log_info["expend"] =0;//支出
		$log_info["balance_cash"] = 0;//可提现金额
		$log_info["balance_frost"] = 0;//不可提现金额
		$log_info["frost"] = 0;//冻结金额
		$log_info["await"] = $money;//待收金额
		$log_info["type"] = "vouch_late_repay_await";//类型
		$log_info["to_userid"] = 0;//付给谁
		$log_info["remark"] = "担保借款标[$borrow_url]第".($repay_period+1)."期担保垫付待收金额";
		accountClass::AddLog($log_info);
		
		return 1;
	}
	
	
	
	//第三步，正常还款
	//borrow_nid,repay_id
	function RepayStep2($data){
		global $mysql,$_G;
		//判断是否可以还款
		if (IsExiest($data['repay_id'])==""){
			return "borrow_repay_id_empty";
		}
		if (IsExiest($data['user_id'])==""){
			return "borrow_user_id_empty";
		}
		if (IsExiest($data['borrow_nid'])==""){
			return "borrow_nid_empty";
		}
		
		//第一步，获取还款的信息
		$sql = "select p1.*,p2.username from `{borrow_repay}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.id={$data['repay_id']} and p1.user_id='{$data['user_id']}' and p1.borrow_nid='{$data['borrow_nid']}'";
		$repay_result= $mysql->db_fetch_array($sql);
		if ($repay_result==""){
			return "borrow_repay_id_empty";
		}
		if ($repay_result["user_id"]!=$data["user_id"]){
			return "borrow_user_id_empty";
		}
		if ($repay_result["status"]!=1){
			return "borrow_repay_error";
		}
		if ($repay_result["repay_status"]==1){
			return "borrow_repay_error";
		}
		if ($repay_result["repay_vouch_status"]==1){
			return "borrow_repay_vouch_error";
		}
		
		$repay_id = $repay_result["id"];
		$repay_account = $repay_result["repay_account"];//还款总额
		$repay_period = $repay_result["repay_period"];
		$repay_web = $repay_result["repay_web"];
		$repay_vouch = $repay_result["repay_vouch"];
		$repay_period = $repay_result["repay_period"];
		$repay_account = $repay_result["repay_account"];//还款总额
		$repay_capital = $repay_result["repay_capital"];//还款本金
		$repay_interest = $repay_result["repay_interest"];//还款利息
		$repay_time = $repay_result["repay_time"];//还款时间
		
		//读取借款标的相关信息
		$borrow_nid = $data['borrow_nid'];
		$sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$data['borrow_nid']}'";
		$borrow_result = $mysql->db_fetch_array($sql);
		$status = $borrow_result['status'];
		$borrow_userid = $borrow_result['user_id'];
		$borrow_account = $borrow_result['account'];
		$borrow_name = $borrow_result['name'];
		$borrow_username = $borrow_result['username'];
		$borrow_url = html_entity_decode("<a href=/invest/a{$borrow_nid}.html target=_blank style=color:blue>{$borrow_result['name']}</a>");//借款标地址
		
		//读取投资还款信息
		$sql = "select p1.* from `{borrow_recover}` as p1  where p1.`recover_period` = '{$repay_period}' and p1.borrow_nid='{$borrow_nid}'  limit {$data['key']},1";
		$recover_result = $mysql->db_fetch_array($sql);
		if ($recover_result==false) return -1;
		
		$money = $recover_result['repay_account'];
		$re_time = (strtotime(date("Y-m-d",$repay_time))-strtotime(date("Y-m-d",time())))/(60*60*24);
			
		
		$sql = "update  `{borrow_recover}` set recover_vouch =1,recover_yestime='".time()."',recover_account_yes = recover_account ,recover_capital_yes = recover_capital ,recover_interest_yes = recover_interest ,status=1,recover_status=1,late_days=0,late_interest=0 where id = '{$recover_result['id']}'";
		$mysql->db_query($sql);
				
			
				
		//更新投资的信息
		$sql = "select count(1) as recover_times,sum(recover_account_yes) as recover_account_yes_num,sum(recover_interest_yes) as recover_interest_yes_num,sum(recover_capital_yes) as recover_capital_yes_num  from `{borrow_recover}` where tender_id='{$recover_result['tender_id']}' and recover_status=1";
		$result = $mysql->db_fetch_array($sql);
		$recover_times = $result['recover_times'];
		
		$sql = "update  `{borrow_tender}` set recover_times={$recover_times},recover_account_yes= {$result['recover_account_yes_num']},recover_account_capital_yes =  {$result['recover_capital_yes_num']} ,recover_account_interest_yes = {$result['recover_interest_yes_num']},recover_account_wait= recover_account_all - recover_account_yes,recover_account_capital_wait = account - recover_account_capital_yes  ,recover_account_interest_wait = recover_account_interest -  recover_account_interest_yes  where id = '{$recover_result['tender_id']}'";
		$mysql->db_query($sql);
				
				
		//用户对借款标的还款
		$log_info["user_id"] = $recover_result['user_id'];//操作用户id
		$log_info["nid"] = "tender_repay_yes_".$recover_result['user_id']."_".$borrow_nid."_".$recover_result['id'];//订单号
		$log_info["money"] = $recover_result['recover_account'];//操作金额
		$log_info["income"] = $recover_result['recover_account'];//收入
		$log_info["expend"] = 0;//支出
		$log_info["balance_cash"] = $recover_result['recover_account'];//可提现金额
		$log_info["balance_frost"] = 0;//不可提现金额
		$log_info["frost"] = 0;//冻结金额
		$log_info["await"] = -$recover_result['recover_account'];//待收金额
		$log_info["type"] = "tender_repay_yes";//类型
		$log_info["to_userid"] = 0;//付给谁
		$log_info["remark"] = "[{$borrow_url}]担保标第".($repay_period+1)."期逾期担保垫付还款";
		accountClass::AddLog($log_info);
				
				
		//扣除投资的管理费
		$tender_fee = $recover_result['recover_interest']*0.05;
		//用户对借款标的还款
		$log_info["user_id"] = $recover_result['user_id'];//操作用户id
		$log_info["nid"] = "tender_repay_fee_".$recover_result['user_id']."_".$borrow_nid."_".$recover_result['id'];//订单号
		$log_info["money"] = $tender_fee;//操作金额
		$log_info["income"] = 0;//收入
		$log_info["expend"] = $tender_fee;//支出
		$log_info["balance_cash"] = -$tender_fee;//可提现金额
		$log_info["balance_frost"] = 0;//不可提现金额
		$log_info["frost"] = 0;//冻结金额
		$log_info["await"] = 0;//待收金额
		$log_info["type"] = "tender_repay_fee";//类型
		$log_info["to_userid"] = 0;//付给谁
		$log_info["remark"] = "[$borrow_url]担保标第".($repay_period+1)."期进行担保垫付还款利息管理费扣除";
		accountClass::AddLog($log_info);
		//更新统计信息
				
		return 1;
	}
	
	
	//第五步，最后更新还款信息
	//borrow_nid,repay_id
	function RepayStep3($data){
		global $mysql,$_G;
		//判断是否可以还款
		if (IsExiest($data['repay_id'])==""){
			return "borrow_repay_id_empty";
		}
		if (IsExiest($data['user_id'])==""){
			return "borrow_user_id_empty";
		}
		if (IsExiest($data['borrow_nid'])==""){
			return "borrow_nid_empty";
		}
		
		//第一步，获取还款的信息
		$sql = "select p1.*,p2.username from `{borrow_repay}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.id={$data['repay_id']} and p1.user_id='{$data['user_id']}' and p1.borrow_nid='{$data['borrow_nid']}'";
		$repay_result= $mysql->db_fetch_array($sql);
		if ($repay_result==""){
			return "borrow_repay_id_empty";
		}
		if ($repay_result["user_id"]!=$data["user_id"]){
			return "borrow_user_id_empty";
		}
		if ($repay_result["status"]!=1){
			return "borrow_repay_error";
		}
		
		if ($repay_result["repay_status"]==1){
			return "borrow_repay_error";
		}
		
		if ($repay_result["repay_vouch_status"]==1){
			return "borrow_repay_vouch_error";
		}
		$repay_id = $repay_result["id"];
		
			
		$sql = "update `{borrow_repay}` set repay_status=1,repay_vouch=1,repay_yestime='".time()."',repay_account_yes=repay_account,repay_interest_yes=repay_interest,repay_capital_yes=repay_capital where id='{$repay_id}'";
		$mysql->db_query($sql);
	
		return 1;
	}
}
?>
