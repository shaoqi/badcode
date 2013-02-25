<?
/******************************
 * $File: borrow.loan.php
 * $Description: 用户中心用户借款类
 * $Author: ahui 
 * $Time:2012-09-20
 * $Update:Ahui
 * $UpdateDate:2012-09-20  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

class borrowLoanClass
{
	
	//第一步，更新审核人的信息
	function CheckLoan($data){		
		global $mysql,$_G;
		//获取额度
		$amount=amountClass::GetAmountUsers(array("user_id"=>$data['user_id']));
		//借款第一步，先进行填写基本信息
		if($data['borrow_type']==1){
			$sql = "select * from `{rating_info}` where user_id='{$data['user_id']}'";
			$result = $mysql->db_fetch_array($sql);
			if ($result['realname']==""){
				return "info";
			}			
			//借款第二步，填写工作信息
			$sql = "select * from `{rating_job}` where user_id='{$data['user_id']}'";
			$result = $mysql->db_fetch_array($sql);
			if ($result['name']==""){
				return "work";
			}
			//再次申请额度
			if($amount['borrow']!=0){
				$sql = "select 1 from `{borrow_amount_apply}` where user_id='{$data['user_id']}' and status='0' and amount_type='borrow'";
				$result = $mysql->db_fetch_array($sql);
				if($result!=''){
					return "approve";
				}else{
					return "amount";
				}
			}else{
				$sql = "select 1 from `{borrow_amount_apply}` where user_id='{$data['user_id']}' and status='0' and amount_type='borrow'";
				$result = $mysql->db_fetch_array($sql);
				if($result==''){
					return "amount";
				}else{
					return "approve";
				}
			}
			//借款第三步，填写工作信息
			$sql = "select 1 from `{borrow_amount}` where user_id='{$data['user_id']}'";
			$result = $mysql->db_fetch_array($sql);
			if ($result==""){
				return "amount";
			}
			
			
		}elseif($data['borrow_type']==2){
			//抵押第一步，先进行填写抵押物信息
			$sql = "select * from `{rating_houses}` where user_id='{$data['user_id']}'";
			$result = $mysql->db_fetch_array($sql);
			if ($result['name']==""){
				return "diya";
			}
			
			//再次申请额度
			if($amount['vouch_borrow']!=0){			
			$sql = "select 1 from `{borrow_amount_apply}` where user_id='{$data['user_id']}' and status='0' and amount_type='diya_borrow'";
			$result = $mysql->db_fetch_array($sql);
				if($result!=''){
					return "approve";
				}else{
					return "amount";
				}
			}else{ 
				$sql = "select 1 from `{borrow_amount_apply}` where user_id='{$data['user_id']}' and status='0' and amount_type='diya_borrow'";
				$result = $mysql->db_fetch_array($sql);
				if($result==''){
					return "amount";
				}else{
					return "approve";
				}
			}		

			//借款第三步，填写工作信息
			$sql = "select 1 from `{borrow_amount}` where user_id='{$data['user_id']}'";
			$result = $mysql->db_fetch_array($sql);
			if ($result==""){
				return "amount";
			}
		}
				
		
		
		//借款第三步，上传资料,判断实名认证和手机认证
		require_once(ROOT_PATH."/modules/attestations/attestations.class.php");
		$result = attestationsClass::GetAttestationsUserCredit(array("user_id"=>$data['user_id']));
		if ( $result['work']['status']!=1 || $result['income']['status']!=1 || $result['bank_report']['status']!=1 || $_G['user_info']['realname_status']!=1 ){
			return "approve";
		}
	}
	
	function CheckRealname($data){	
		global $mysql,$_G;
		$sql = "select realname from `{approve_realname}` where user_id='{$data['user_id']}' ";
		$result = $mysql->db_fetch_array($sql);
		if($result['realname']!=''){
			return 1;
		}
		return 0;
	}
	
}
?>
