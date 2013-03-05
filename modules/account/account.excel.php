<?
/******************************
 * $File: account.excel.php
 * $Description: 资金类文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

require_once("account.model.php");

class accountexcel {
	
	//导出用户的资金记录
	function AccountList($data){
		$title = array("Id","用户名称","总金额","可用金额","冻结金额","待收金额","待还金额");
		if ($data['page']>0){			
			$_result = accountClass::GetList($data);
			$result  = $_result['list'];
		}else{
			$data['limit'] = "all";
			$result = accountClass::GetList($data);
		}
		foreach ($result as $key => $value){
			$_data[$key] = array($key+1,$value['username'],$value['total'],$value['balance'],$value['frost'],$value['await'],$value['repay']);
		}
		exportData("资金账号管理",$title,$_data);
		exit;
	}
	
	
	//导出用户的资金记录
	function AccountLogList($data){
		global $mysql;
		$title = array("Id","用户名","类型","操作金额","收入","支出","账户总额","备注","操作时间");
		if ($data['page']>0){
			$_result = accountClass::GetLogList($data);
			$result  = $_result['list'];
		}else{
			$data['limit'] = "all";
			$result = accountClass::GetLogList($data);
		}
		//联动
		$sql = "select value,name from `{linkages}` where type_id=3";
		$type = $mysql->db_fetch_arrays($sql);
		$arr = array();
		foreach($type as $key => $values){
			$arr[$values['value']] = $values['name'];
		}
		
		foreach ($result as $key => $value){
			$_data[$key] = array($key+1,$value['username'],$arr[$value['type']],$value['money'],$value['income_new'],$value['expend_new'],$value['total'],$value['remark'],date("Y-m-d H:i:s",$value['addtime']));
		}
		exportData("资金记录列表",$title,$_data);
		exit;
	}
	
	
	//充值资金记录ID
	function RechargeLog($data){
		global $Linkages;
		$title = array("Id","用户名","交易号","类型","充值银行","充值金额","充值手续费","实际到账金额","状态","操作时间","操作ip");		
		if ($data['page']>0){
			$_result = accountClass::GetRechargeList($data);
			$result  = $_result['list'];
		}else{
			$data['limit'] = "all";
			$result = accountClass::GetRechargeList($data);
		}
		foreach ($result as $key => $value){
			$value['type'] = $Linkages['account_recharge_type'][$value['type']];
			$value['status'] = $Linkages['account_recharge_status'][$value['status']];
			if ($value['payment_name']==""){
				$value['payment_name']="手动充值";
			}
			$_data[$key] = array($key+1,$value['username'],$value['nid'],$value['type'],$value['payment_name'],$value['money'],$value['fee'],$value['balance'],$value['status'],date("Y-m-d H:i:s",$value['addtime']),$value['addip']);
		}
		exportData("充值记录",$title,$_data);
		exit;
	}
	
	
	//提现资金记录ID用户名称				
	function CashLog($data){
		global $Linkages;
		$title = array("Id","用户名","真实姓名","提现银行","支行","所在地","提现账号","提现总额","到账金额","手续费","提现时间","提现ip","状态");
		if ($data['page']>0){
			$_result = accountClass::GetCashList($data);
			$result  = $_result['list'];
		}else{
			$data['limit'] = "all";
			$result = accountClass::GetCashList($data);
		}
		foreach ($result as $key => $value){
			$value['status'] = $Linkages['account_cash_status'][$value['status']];
			//$value['city'] = modifier("areas",$value['city'],"p,c");
			$value['areas']= accountClass::GetCity($value['city']);
			$value['city'] = $value['areas']['name'];		
			$bank_name = accountClass::GetBankName($value['bank']);
			$value['bank_name'] = $bank_name['bank_name'];			
			$_data[$key] = array($key+1,$value['username'],$value['realname'],$value['bank_name'],$value['branch'],$value['city'],$value['account_all'],$value['total'],$value['credited'],$value['fee'],date("Y-m-d H:i:s",$value['addtime']),$value['addip'],$value['status']);
		}
		exportData("提现记录",$title,$_data);
		exit;
	}
		
	
	//网站费用			
	function WebLog($data){
		global $mysql;
		$title = array("Id","类型","操作人","操作金额","收入","支出","备注","操作时间","操作ip");
		if ($data['page']>0){
			$_result = accountClass::GetWebList($data);
			$result  = $_result['list'];
		}else{
			$data['limit'] = "all";
			$result = accountClass::GetWebList($data);
		}
		//联动
		$sql = "select value,name from `{linkages}` where type_id=3";
		$type = $mysql->db_fetch_arrays($sql);
		$arr = array();
		foreach($type as $key => $values){
			$arr[$values['value']] = $values['name'];
		}
		foreach ($result as $key => $value){
			$type = $Linkages['account_web_fee'][$value['type']];
			if ($value['type']=="recharge"){
				$income = $value['income'];
				$expend = $value['expend'];
			}else{
				$income = $value['expend'];
				$expend = $value['income'];
			}
			$_data[$key] = array($key+1,$arr[$value['type']] ,$value['username'],$value['money'],$value['expend'],$value['income'],strip_tags($value['remark']),date("Y-m-d H:i:s",$value['addtime']),$value['addip']);
		}
		exportData("网站费用",$title,$_data);
		exit;
	}
	
	//网站费用			
	function WebListLog($data){
		$title = array("Id","用户名","类型","网站垫付金额","备注","操作时间","操作ip");
		if ($data['page']>0){
			$_result = accountClass::GetWebList($data);
			$result  = $_result['list'];
		}else{
			$data['limit'] = "all";
			$result = accountClass::GetWebList($data);
		}
		foreach ($result as $key => $value){
			$type = $Linkages['account_web_type'][$value['type']];
			$_data[$key] = array($key+1,$value['username'],$type,round($value['money'],2),$remark,date("Y-m-d H:i:s",$value['addtime']),$value['addip']);
		}
		exportData("网站垫付费用",$title,$_data);
		exit;
	}
	
	
	//网站费用			
	function RecoverListLog($data){
		require_once(ROOT_PATH."/modules/borrow/borrow.class.php");
		$title = array("Id","借款标题","应收日期","借款者","第几期","总期数","垫付金额","应收本金","应收利息","逾期罚息","逾期天数","状态");
		if ($data['page']>0){
			$_result = borrowClass::GetRecoverList($data);
			$result  = $_result['list'];
		}else{
			$data['limit'] = "all";
			$result = borrowClass::GetRecoverList($data);
		}
		foreach ($result as $key => $value){
			if ($value['recover_status']==1){
				$status="已还";
			}else{
				$status = "未还";	
			}
			
			$_data[$key] = array($key+1,$value['borrow_name'],date("Y-m-d",$value['recover_time']),$value['borrow_username'],$value['recover_period']+1,$value['borrow_period'],$value['recover_recover_account_yes'],$value['recover_capital'],$value['late_interest'],$value['late_days'],$status);
		}
		exportData("网站应收明细账",$title,$_data);
		exit;
	}
	
	
	
	
	//		
	function UsersLog($data){
		global $mysql;
		$title = array("Id","用户名称","类型","操作金额","余额","收入","支出","备注","操作时间","操作ip");
		if ($data['page']>0){
			$_result = accountClass::GetUsersList($data);
			$result  = $_result['list'];
		}else{
			$data['limit'] = "all";
			$result = accountClass::GetUsersList($data);
		}
		//联动
		$sql = "select value,name from `{linkages}` where type_id=3";
		$type = $mysql->db_fetch_arrays($sql);
		$arr = array();
		foreach($type as $key => $values){
			$arr[$values['value']] = $values['name'];
		}
		foreach ($result as $key => $value){
			$type = $Linkages['account_type'][$value['type']];
			$_data[$key] = array($key+1,$value['username'],$arr[$value['type']],$value['money'],$value['balance'],$value['income'],$value['expend'],strip_tags($value['remark']),date("Y-m-d H:i:s",$value['addtime']),$value['addip']);
		}
		exportData("用户费用",$title,$_data);
		exit;
	}
}
?>