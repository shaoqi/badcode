<?
/******************************
 * $File: borrow.excel.php
 * $Description: 借款导出
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

require_once("borrow.model.php");
require_once("borrow.change.php");

class borrowexcel {
	
	//导出用户的资金记录
	function AccountList($data){
		$title = array("Id","用户名称","总金额","可用金额","冻结金额","待收金额");
		if ($data['page']>0){
			$_result = accountClass::GetList($data);
			$result  = $_result['list'];
		}else{
			$data['limit'] = "all";
			$result = accountClass::GetList($data);
		}
		foreach ($result as $key => $value){
			$_data[$key] = array($key+1,$value['username'],$value['total'],$value['balance'],$value['frost'],$value['await']);
		}
		exportData("账号信息管理",$title,$_data);
		exit;
	}
	
	
	//导出用户的资金记录
	function LogList($data){
		$title = array("Id","用户名","交易号","类型","操作金额","备注","添加时间");
		if ($data['page']>0){
			$_result = accountClass::GetLogList($data);
			$result  = $_result['list'];
		}else{
			$data['limit'] = "all";
			$result = accountClass::GetLogList($data);
		}
		foreach ($result as $key => $value){
			$_data[$key] = array($key+1,$value['username'],$value['nid'],$value['type'],$value['money'],$value['remark'],date("Y-m-d H:i:s",$value['addtime']));
		}
		exportData("账号信息管理",$title,$_data);
		exit;
	}
	
	
	
	//
	function BorrowRepayList($data){
		$title = array("Id","用户名","交易号","类型","操作金额","备注","添加时间");
		if ($data['page']>0){
			$_result = accountClass::GetLogList($data);
			$result  = $_result['list'];
		}else{
			$data['limit'] = "all";
			$result = accountClass::GetLogList($data);
		}
		foreach ($result as $key => $value){
			$_data[$key] = array($key+1,$value['username'],$value['nid'],$value['type'],$value['money'],$value['remark'],date("Y-m-d H:i:s",$value['addtime']));
		}
		exportData("账号信息管理",$title,$_data);
		exit;
	}
	
	
	
	//导出用户的资金记录
	function BadBorrowRepayList($data){
		$title = array("Id","用户名","交易号","类型","操作金额","备注","添加时间");
		if ($data['page']>0){
			$_result = accountClass::GetLogList($data);
			$result  = $_result['list'];
		}else{
			$data['limit'] = "all";
			$result = accountClass::GetLogList($data);
		}
		foreach ($result as $key => $value){
			$_data[$key] = array($key+1,$value['username'],$value['nid'],$value['type'],$value['money'],$value['remark'],date("Y-m-d H:i:s",$value['addtime']));
		}
		exportData("账号信息管理",$title,$_data);
		exit;
	}
	
	
	
	//导出用户的资金记录
	function ChangeList($data){
		$title = array("Id","用户名","交易号","类型","操作金额","备注","添加时间");
		if ($data['page']>0){
			$_result = accountClass::GetLogList($data);
			$result  = $_result['list'];
		}else{
			$data['limit'] = "all";
			$result = accountClass::GetLogList($data);
		}
		foreach ($result as $key => $value){
			$_data[$key] = array($key+1,$value['username'],$value['nid'],$value['type'],$value['money'],$value['remark'],date("Y-m-d H:i:s",$value['addtime']));
		}
		exportData("账号信息管理",$title,$_data);
		exit;
	}
	function BorrowChangeList($data){
		
		if($data['status']=='' || $data['status']==2){
			$title = array("Id","转让者","投标标题","利率","待收期数","总期数","待收本金","待收利息","转让价格","发布时间");
		}elseif($data['status']==1){
			$title = array("Id","转让者","投标标题","利率","待收期数","总期数","待收本金","待收利息","转让价格","发布时间","购买者","购买时间");
		}elseif($data['status']==5){
			$title = array("Id","转让者","投标标题","利率","待收期数","总期数","待收本金","待收利息","转让价格","发布时间","撤销时间");
		}		
		if ($data['page']>0){
			$_result = borrowChangeClass::GetChangeList($data);
			$result  = $_result['list'];
		}else{
			$data['limit'] = "all";
			$result = borrowChangeClass::GetChangeList($data);
		}
		foreach ($result as $key => $value){
			/* if($data['status']=='' || $data['status']==2){
				$_data[$key] = array($key+1,$value['username'],$value['borrow_name'],$value['borrow_apr'],$value['wait_times']."/".$value['borrow_period'],$value['recover_account_capital_wait'],date("Y-m-d H:i:s",$value['addtime']));
			}elseif($data['status']==1){
				$_data[$key] = array($key+1,$value['username'],$value['borrow_name'],$value['borrow_apr'],$value['wait_times']."/".$value['borrow_period'],$value['recover_account_capital_wait'],date("Y-m-d H:i:s",$value['addtime']));
			}elseif($data['status']==5){
				$_data[$key] = array($key+1,$value['username'],$value['borrow_name'],$value['borrow_apr'],$value['wait_times']."/".$value['borrow_period'],$value['recover_account_capital_wait'],date("Y-m-d H:i:s",$value['addtime']));
			}  */
			 if( $data['status']=='' || $data['status']==2 ){
				$_data[$key] = array($key+1,$value['username'],$value['borrow_name'],$value['borrow_apr'],$value['wait_times'],$value['borrow_period'],$value['recover_account_capital_wait'],$value['recover_account_interest_wait'],$value['account'],date("Y-m-d H:i:s",$value['addtime']));
			}elseif($data['status']==1){
				$_data[$key] = array($key+1,$value['username'],$value['borrow_name'],$value['borrow_apr'],$value['wait_times'],$value['borrow_period'],$value['recover_account_capital_wait'],$value['recover_account_interest_wait'],$value['account'],date("Y-m-d H:i:s",$value['addtime']),$value['buy_username'],date("Y-m-d H:i:s",$value['buy_time']));
			}elseif($data['status']==5){
				$_data[$key] = array($key+1,$value['username'],$value['borrow_name'],$value['borrow_apr'],$value['wait_times'],$value['borrow_period'],$value['recover_account_capital_wait'],$value['recover_account_interest_wait'],$value['account'],date("Y-m-d H:i:s",$value['cancel_time']),date("Y-m-d H:i:s",$value['addtime']));
			} 
		}
		exportData("账号信息管理",$title,$_data);
		exit;
	}
	
}
?>