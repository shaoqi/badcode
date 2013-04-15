<?php
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

    
    /**
     * 导出还款信息
     */
    public function GetRepayList($data)
    {
        if ($data['page']>0) {
            $result = borrowLoanClass::GetRepayList($data);
            $result = $result['list'];
        } else {
            $data['limit'] = "all";
            $result = borrowLoanClass::GetRepayList($data);
        }
        if(!empty($result)){
            $title=['贷款号','借款人','借款标题','借款期数','借款类型','应还时间','应还本息','实还时间','实还本金','实还利息','还款费用','实还总额','状态'];
            $_data = [];
            foreach($result as $value){
                $_data[]=[$value['borrow_nid'],$value['borrow_username'],$value['borrow_name'],$value['repay_period'],$value['type_title'],date('Y-m-d',$value['repay_time']),$value['repay_account'],(empty($value['repay_yestime'])?'':date('Y-m-d',$value['repay_yestime'])),$value['repay_capital_yes'],$value['repay_interest_yes'],$value['repay_fee'],($value['repay_capital_yes']+$value['repay_interest_yes']+$value['repay_fee']),$value['repay_type_name']];
            }
            exportData("借款管理之还款信息",$title,$_data);
            exit;
        }
    }

    /**
     * 导出还款信息
     */
    public function GetRecoverList($data)
    {
        if ($data['page']>0) {
            $result = borrowRecoverClass::GetRecoverList($data);
            $result = $result['list'];
        } else {
            $data['limit'] = "all";
            $result = borrowRecoverClass::GetRecoverList($data);
        }
        if(!empty($result)){
            $title=['收款人','贷款号','借款标题','借款类型','还款本息','逾期天数','应收时间','实收时间','实收总额','状态'];
            $_data = [];
            $bool_status = ['待收款','已收款'];
            $borrow_type = ['credit'=>'信用标','vouch'=>'担保标','pawn'=>'抵押标','second'=>'秒标','worth'=>'净值标','day'=>'天标','roam'=>'流转标'];
            foreach($result as $value){
                $_data[]=[$value['username'],$value['borrow_nid'],$value['borrow_name'].'(第'.$value['repay_period'].'期)',$borrow_type[$value['borrow_type']],$value['recover_account'],$value['late_days'].'天',(empty($value['recover_time'])?'':date('Y-m-d',$value['recover_time'])),(empty($value['recover_yestime'])?'':date('Y-m-d',$value['recover_yestime'])),$value['recover_account_yes'],(isset($bool_status[$value['recover_status']])?$bool_status[$value['recover_status']]:'否')];
            }
            exportData("借款管理之还款信息",$title,$_data);
            exit;
        }
    }

    /**
     * 导出投资信息
     */
    public function GetTenderList($data)
    {
        if ($data['page']>0) {
            $result = borrowTenderClass::GetTenderList($data);
            $result = $result['list'];
        } else {
            $data['limit'] = "all";
            $result = borrowTenderClass::GetTenderList($data);
        }
        if(!empty($result)){
            $title=['投资ID','投资人','投资金额','投资时间','投资状态','是否转让','投资理由','借款标','借款标识名','借款总额','自动投标'];
            $status_arr = ['待审核','成功','失败'];
            $bool_status = ['否','是'];
            $_data = [];
            foreach($result as $value){
                $_data[]=[$value['id'],$value['username'],$value['account'],date('Y-m-d H:i:s',$value['addtime']),(isset ($status_arr[$value['status']])?$status_arr[$value['status']]:'待审核'),(isset ($bool_status[$value['change_status']])?$bool_status[$value['change_status']]:'否'),(empty($value['contents'])?'':$value['contents']),$value['borrow_name'],$value['borrow_nid'],$value['borrow_account'],(isset ($bool_status[$value['auto_status']])?$bool_status[$value['auto_status']]:'否')];
            }
            exportData("借款管理之投资信息",$title,$_data);
            exit;
        }
    }
	
}