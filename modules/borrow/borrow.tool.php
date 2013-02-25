<?
/******************************
 * $File: borrow.tool.php
 * $Description: 检测工具
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

class borrowtoolClass
{
	function TypeCount(){
		global $mysql;
		$sql = "select count(1) as num,sum(money) as count,type from `{account_log}` group by type";
		$result = $mysql->db_fetch_arrays($sql);
		return $result;
	}
	
	function Check($data){
		global $mysql;
		$key = empty($data['key'])?0:$data['key'];
		$sql = "select count(1) as num,sum(money) as count,type from `{account_log}` group by type  limit $key,1";
		$result = $mysql->db_fetch_array($sql);
		//借款奖励金额减少
		if ($result['type'] == "borrow_award_lower"){
			$_result = self::borrow_award_lower($result);
		}
		//借款还款
		elseif ($result['type'] == "borrow_repay"){
			$_result = self::borrow_repay($result);
		}
		//借款成功
		elseif ($result['type'] == "borrow_success"){
			$_result = self::borrow_success($result);
		}
		//充值
		elseif ($result['type'] == "recharge"){
			$_result = self::recharge($result);
		}
		
		//充值
		elseif ($result['type'] == "online_recharge"){
			$_result = self::online_recharge($result);
		}
		
		//充值费用
		elseif ($result['type'] == "recharge_fee"){
			$_result = self::recharge_fee($result);
		}
		//借款管理费用
		elseif ($result['type'] == "borrow_manage_fee"){
			$_result = self::borrow_manage_fee($result);
		}
		//提现
		elseif ($result['type'] == "cash"){
			$_result = self::cash($result);
		}
		//提现成功
		elseif ($result['type'] == "cash_success"){
			$_result = self::cash_success($result);
		}
		//提现失败
		elseif ($result['type'] == "cash_false"){
			$_result = self::cash_false($result);
		}
		//提现失败
		elseif ($result['type'] == "cash_cancel"){
			$_result = self::cash_false($result);
		}
		//投资
		elseif ($result['type'] == "tender"){
			$_result = self::tender($result);
		}
		//投资成功
		elseif ($result['type'] == "tender_success"){
			$_result = self::tender_success($result);
		}
		
		//投资冻结
		elseif ($result['type'] == "tender_success_frost"){
			$_result = self::tender_success_frost($result);
		}
		//账号管理费
		elseif ($result['type'] == "borrow_success_account"){
			$_result = self::borrow_success_account($result);
		}
		//成交费
		elseif ($result['type'] == "borrow_success_manage"){
			$_result = self::borrow_success_manage($result);
		}
		//风险池
		elseif ($result['type'] == "fengxianchi"){
			$_result = self::fengxianchi($result);
		}
		//风险池
		elseif ($result['type'] == "fengxianchi_borrow"){
			$_result = self::fengxianchi_borrow($result);
		}
		//实名认证
		elseif ($result['type'] == "realname_fee"){
			$_result = self::realname_fee($result);
		}
		//投资还款
		elseif ($result['type'] == "tender_repay_yes"){
			$_result = self::tender_repay_yes($result);
		}
		//投资用户撤回
		elseif ($result['type'] == "tender_user_cancel"){
			$_result = self::tender_user_cancel($result);
		}
		//VIP用户
		elseif ($result['type'] == "vip_success"){
			$_result = self::vip_success($result);
		}
		else{
			$_result = array("type"=>$result['type'],"status"=>-1);
		}
		
		return $_result;
	}
	
	
	function vip_success($data){
		global $mysql;
		$sql = "select count(1) as num,sum(money) as count from `{users_vip}` where status=1";
		$result = $mysql->db_fetch_array($sql);
		
		$tool['type'] = "vip_success";
		$tool['status'] = 0;
		$tool['num'] = $result['num'];
		$tool['count'] = $result['count'];
		if ($data['num']==$tool['num'] ){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	function tender_user_cancel($data){
		global $mysql;
		$sql = "select count(1) as num,sum(account) as count from `{borrow_tender}` where status=3";
		$result = $mysql->db_fetch_array($sql);
		
		$tool['type'] = "tender_user_cancel";
		$tool['status'] = 0;
		$tool['num'] = $result['num'];
		$tool['count'] = $result['count'];
		if ($data['num']==$tool['num'] ){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	function tender_repay_yes($data){
		global $mysql;
		$sql = "select count(1) as num,sum(recover_account) as count from `{borrow_recover}` where recover_status=1";
		$result = $mysql->db_fetch_array($sql);
		
		$tool['type'] = "tender_repay_yes";
		$tool['status'] = 0;
		$tool['num'] = $result['num'];
		$tool['count'] = "-";
		if ($data['num']==$tool['num'] ){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	function realname_fee($data){
		global $mysql;
		$sql = "select count(1) as num from `{users_info}` where realname_status=1";
		$result = $mysql->db_fetch_array($sql);
		
		$tool['type'] = "realname_fee";
		$tool['status'] = 0;
		$tool['num'] = $result['num'];
		$tool['count'] = "-";
		if ($data['num']==$tool['num'] ){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	
	function fengxianchi_borrow($data){
		global $mysql;
		$sql = "select count(1) as num,sum(recover_interest) as count from `{borrow_recover}` where recover_status=1";
		$result = $mysql->db_fetch_array($sql);
		
		$tool['type'] = "fengxianchi_borrow";
		$tool['status'] = 0;
		$tool['num'] = $result['num'];
		$tool['count'] = "-";
		if ($data['num']==$tool['num']  && $data['count']==$tool['count']){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	
	function fengxianchi($data){
		global $mysql;
		$sql = "select count(1) as num,sum(recover_interest) as count from `{borrow_recover}` where recover_status=1";
		$result = $mysql->db_fetch_array($sql);
		
		$tool['type'] = "fengxianchi";
		$tool['status'] = 0;
		$tool['num'] = $result['num'];
		$tool['count'] = $result['count']*0.05;
		if ($data['num']==$tool['num'] && $data['count']==$tool['count']){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	
	function borrow_success_manage($data){
		global $mysql;
		$sql = "select count(1) as num,sum(account) as count from `{borrow}` where status=3";
		$result = $mysql->db_fetch_array($sql);
		
		$tool['type'] = "borrow_success_manage";
		$tool['status'] = 0;
		$tool['num'] = $result['num'];
		$tool['count'] = "-";
		if ($data['num']==$tool['num']){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	
	
	function borrow_success_account($data){
		global $mysql;
		$sql = "select count(1) as num,sum(account) as count from `{borrow}` where status=3";
		$result = $mysql->db_fetch_array($sql);
		
		$tool['type'] = "borrow_success_account";
		$tool['status'] = 0;
		$tool['num'] = $result['num'];
		$tool['count'] = "-";
		if ($data['num']==$tool['num']){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	
	function tender_success_frost($data){
		global $mysql;
		$sql = "select count(1) as num,sum(recover_account_all) as count from `{borrow_tender}` where status=1";
		$result = $mysql->db_fetch_array($sql);
		
		$tool['type'] = "tender_success_frost";
		$tool['status'] = 0;
		$tool['num'] = $result['num'];
		$tool['count'] = $result['count'];
		if ($data['num']==$tool['num'] && $data['count']==$tool['count'] ){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	
	function tender_success($data){
		global $mysql;
		$sql = "select count(1) as num,sum(account) as count from `{borrow_tender}` where status=1";
		$result = $mysql->db_fetch_array($sql);
		
		$tool['type'] = "tender_success";
		$tool['status'] = 0;
		$tool['num'] = $result['num'];
		$tool['count'] = $result['count'];
		if ($data['num']==$tool['num'] && $data['count']==$tool['count'] ){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	function tender($data){
		global $mysql;
		$sql = "select count(1) as num,sum(account) as count from `{borrow_tender}` ";
		$result = $mysql->db_fetch_array($sql);
		
		$tool['type'] = "tender";
		$tool['status'] = 0;
		$tool['num'] = $result['num'];
		$tool['count'] = $result['count'];
		if ($data['num']==$tool['num'] && $data['count']==$tool['count'] ){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	function cash_cancel($data){
		global $mysql;
		$sql = "select count(1) as num,sum(total) as count from `{account_cash}` where status=3 ";
		$result = $mysql->db_fetch_array($sql);
		
		$tool['type'] = "cash_cancel";
		$tool['status'] = 0;
		$tool['num'] = $result['num'];
		$tool['count'] = $result['count'];
		if ($data['num']==$tool['num'] && $data['count']==$tool['count'] ){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	function cash_false($data){
		global $mysql;
		$sql = "select count(1) as num,sum(total) as count from `{account_cash}` where status=2 ";
		$result = $mysql->db_fetch_array($sql);
		
		$tool['type'] = "cash_false";
		$tool['status'] = 0;
		$tool['num'] = $result['num'];
		$tool['count'] = $result['count'];
		if ($data['num']==$tool['num'] && $data['count']==$tool['count'] ){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	function cash_success($data){
		global $mysql;
		$sql = "select count(1) as num,sum(total) as count from `{account_cash}` where status=1 ";
		$result = $mysql->db_fetch_array($sql);
		
		$tool['type'] = "cash_success";
		$tool['status'] = 0;
		$tool['num'] = $result['num'];
		$tool['count'] = $result['count'];
		if ($data['num']==$tool['num'] && $data['count']==$tool['count'] ){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	function cash($data){
		global $mysql;
		$sql = "select count(1) as num,sum(total) as count from `{account_cash}` ";
		$result = $mysql->db_fetch_array($sql);
		
		$tool['type'] = "cash";
		$tool['status'] = 0;
		$tool['num'] = $result['num'];
		$tool['count'] = $result['count'];
		if ($data['num']==$tool['num'] && $data['count']==$tool['count'] ){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	
	function borrow_manage_fee($data){
		global $mysql;
		$sql = "select count(1) as num,sum(account) as count from `{borrow}` where status=3 ";
		$result = $mysql->db_fetch_array($sql);
		
		$tool['type'] = "borrow_manage_fee";
		$tool['status'] = 0;
		$tool['num'] = $result['num'];
		$tool['count'] = "-";
		if ($data['num']==$tool['num'] ){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	function borrow_award_lower($data){
		global $mysql;
		$sql = "select account,award_status,award_account,award_scale from `{borrow}` where (award_status>0 and status=3) or (award_false=1 and status=4 )";
		$result = $mysql->db_fetch_arrays($sql);
		
		$_result = 0;
		foreach ($result as $key => $value){
			if ($value['award_status']==1){
				$_result += $value['award_account'];
			}elseif ($value['award_status']==2){
				$_result += round($value['award_scale']*$value['account'],2);
			}
		}
		$tool['type'] = "borrow_award_lower";
		$tool['status'] = 0;
		$tool['num'] = count($result);
		$tool['count'] = $_result;
		if ($data['num']==count($result) && $data['count']==$_result ){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	
	function borrow_repay($data){
		global $mysql;
		$sql = "select count(1) as num,sum(repay_account) as count from `{borrow_repay}` where repay_status=1 ";
		$result = $mysql->db_fetch_array($sql);
		
		$tool['type'] = "borrow_repay";
		$tool['status'] = 0;
		$tool['num'] = $result['num'];
		$tool['count'] = $result['count'];
		if ($data['num']==$tool['num'] && $data['count']==$tool['count'] ){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	function borrow_success($data){
		global $mysql;
		$sql = "select count(1) as num,sum(account) as count from `{borrow}` where status=3 ";
		$result = $mysql->db_fetch_array($sql);
		
		$tool['type'] = "borrow_success";
		$tool['status'] = 0;
		$tool['num'] = $result['num'];
		$tool['count'] = $result['count'];
		if ($data['num']==$tool['num'] && $data['count']==$tool['count'] ){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	
	function recharge($data){
		global $mysql;
		$sql = "select count(1) as num,sum(money) as count from `{account_recharge}` where status=1 ";
		$result = $mysql->db_fetch_array($sql);
		
		$tool['type'] = "recharge";
		$tool['status'] = 0;
		$tool['num'] = $result['num'];
		$tool['count'] = $result['count'];
		if ($data['num']==$tool['num'] && $data['count']==$tool['count'] ){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	
	function online_recharge($data){
		global $mysql;
		$sql = "select count(1) as num,sum(money) as count from `{account_recharge}` where status=1 ";
		$result = $mysql->db_fetch_array($sql);
		
		$tool['type'] = "online_recharge";
		$tool['status'] = 0;
		$tool['num'] = $result['num'];
		$tool['count'] = $result['count'];
		if ($data['num']==$tool['num'] && $data['count']==$tool['count'] ){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	function recharge_fee($data){
		global $mysql;
		$sql = "select count(1) as num,sum(money) as count from `{account_recharge}` where status=1 ";
		$result = $mysql->db_fetch_array($sql);
		
		$tool['type'] = "recharge_fee";
		$tool['status'] = 0;
		$tool['num'] = $result['num'];
		$tool['count'] = "-";
		if ($data['num']==$tool['num']  ){
			$tool['status'] = 1;
		}
		return $tool;
	}
	
	
	
}
?>
