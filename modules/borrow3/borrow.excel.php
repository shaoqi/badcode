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
	
}
?>