<?
/******************************
 * $File: borrow.repay_advance.php
 * $Description: 提前还款文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

require_once("borrow.loan.php");
require_once("borrow.fee.php");
require_once("borrow.calculates.php");
require_once("borrow.model.php");

class borrowRepayAdvanceClass
{
    
    //提前还款
    function RepayAdvanceInfo($data){
        global $mysql,$_G;
        if ($data['user_id']==""){
            return "borrow_repay_userid_error";
        }
        if ($data['borrow_nid']==""){
            return "borrow_repay_borrow_nid_error";
        }
        //读取未还款相关数据+
        $sql = "select * from `{borrow_repay}` where user_id='{$data['user_id']}' and repay_status=0 and borrow_nid='{$data['borrow_nid']}'";
        $repay_result = $mysql->db_fetch_arrays($sql);
        if ($repay_result==false){
            return "borrow_repay_advance_not_error";
        }
        
         $sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$data['borrow_nid']}' and p1.user_id='{$data['user_id']}'";
        $borrow_result = $mysql->db_fetch_array($sql);
        if ($borrow_result==false){
            return "borrow_repay_borrow_error";
        }
        if ($borrow_result["borrow_type"]=="roam"){
            return "borrow_repay_advance_roam_error";
        }
        $is_repay = 1;
        $repay_capital = 0;
        $repay_interest = 0;
        foreach ($repay_result as  $key => $value){
         //判断是提前还款，还是正常还款。+
            if ($value["repay_days"]==""){
                $_repay_time = get_mktime(date("Y-m-d",$value["repay_time"]));
                if ($borrow_result["repay_advance_time"]!=""){
                    $_now_time = get_mktime(date("Y-m-d",$borrow_result["repay_advance_time"]));  
                }else{
                    $_now_time = get_mktime(date("Y-m-d",time()));  
                }
                             
                $late_days = ($_now_time - $_repay_time)/(60*60*24);
                $value["repay_days"] = $late_days;
            }
            if ($value["repay_days"]>=0){
                $is_repay = 0;
            }
            $repay_capital +=$value["repay_capital"];
            $repay_interest +=$value["repay_interest"];
         }
         if ($is_repay!=1){
            return "borrow_repay_advance_repay_error";
         }
        
         //扣除费用
        //判断是否是vip
        $vip_status =0;
        $vip_result=usersClass::GetUsersVip(array("user_id"=>$data["user_id"]));
        if($vip_result==true){
             $vip_status = $vip_result['status'];
        }
        $credit_result = borrowClass::GetBorrowCredit(array("user_id"=>$data["user_id"]));
        $_fee["vip_status"] = $vip_status;//判断是不是vip
        $_fee["credit_fee"] =$credit_result['credit']['fee'];//判断是不是vip
        $_fee["borrow_type"] = $borrow_result["borrow_type"];//借款类型
        $_fee["borrow_style"] = $borrow_result["borrow_style"];//还款方式
        $_fee["type"] = "borrow_repay_advance";//类型
        $_fee["user_type"] = "borrow";//属于借款者还是投资人
        $_fee["capital"] = $repay_capital;//属于借款者还是投资人
        $_fee["interest"] = $repay_interest;//属于借款者还是投资人
        $fee_result = borrowFeeClass::GetFeeValue($_fee);
        $_fee_account = 0;
        if ($fee_result!=false){
            foreach ($fee_result as $key => $value){
                $_fee_account += $value["account"];
            }
        }
        //应该还款的总额。
        $repay_all  = $repay_capital + $_fee_account;
        $data["repay_account_fee"] = $_fee_account;
      //第一步做相关的判断
        if ($data['step']==0){
            //判断是否可以重新审核
             
    		//判断可用余额是否够还款,并且此标还未进行还款
            if ($borrow_result['repay_adavance_step']==0){
        		$account_result =  accountClass::GetAccountUsers(array("user_id"=>$data['user_id']));
        		if ($account_result['balance']<$repay_all){
        			return "borrow_repay_account_use_none";
        		}
            }
            
             //将还款信息写进还款列表中去。+
            if ($borrow_result["repay_advance_time"]==""){
                $sql = "update `{borrow}` set repay_advance_step=1,repay_advance_time='".time()."'  where borrow_nid='{$borrow_result['borrow_nid']}'";
                $mysql->db_query($sql);
            }else{
                $sql = "update `{borrow}` set repay_advance_step=1 where borrow_nid='{$data['borrow_nid']}'";
                $mysql->db_query($sql);
            }
            
    		//更新满标时的操作人
            return array("result"=>1,"period"=>1,"step"=>1,"key"=>"0","name"=>"正在还款中，请不要关闭浏览器");	
        }else{
            $data['borrow_url'] = "<a href={$_G['web_domain']}/invest/a{$borrow_result['borrow_nid']}.html target=_blank style=color:blue>{$borrow_result['name']}</a>";//借款标地址
            $data['borrow_name'] = $borrow_result["name"];
            $data['borrow_username'] = $borrow_result["username"];
            $data['borrow_type'] = $borrow_result["borrow_type"];
            $data['borrow_style'] = $borrow_result["borrow_style"];
            $data['borrow_period'] = $borrow_result["borrow_period"];
            $data['borrow_frost_account'] = $borrow_result["borrow_frost_account"];
            $data['amount_type'] = $borrow_result["amount_type"];
            $data['repay_advance_step'] = $borrow_result["repay_advance_step"];
            $data['repay_advance_time'] = $borrow_result["repay_advance_time"];
            $data['repay_all'] = $repay_all;
            $data['repay_capital'] = $repay_capital;
            $data['repay_interest'] = $repay_interest;
            $data['vip_status'] = $vip_status;
            $fun = "RepayAdvanceStep".$data['step'];
		    $result = self::$fun($data);
            return $result;
        }
    }
    
    //操作投资人的信息
	function RepayAdvanceStep1($data){
	    global $mysql,$_G;
        $repay_nid = $data["borrow_nid"]."_".$data['user_id'];
		$borrow_url = $data["borrow_url"];
        if ($data['repay_advance_step']!=1){
            return "borrow_repayfull_step1_error";
         }
         
	   	//扣除借款人的还款金额
		$log_info["user_id"] = $data["user_id"];//操作用户id
		$log_info["nid"] = "borrow_repay_advance_".$repay_nid;//订单号
        $log_info["account_web_status"] = 0;//
        $log_info["account_user_status"] = 1;//
        $log_info["borrow_nid"] = $data["borrow_nid"];//收入
		$log_info["code"] = "borrow";//
		$log_info["code_type"] = "borrow_repay_advance";//
		$log_info["code_nid"] = $data["borrow_nid"];//
		$log_info["money"] = $data["repay_capital"];//操作金额
		$log_info["income"] = 0;//收入
		$log_info["expend"] = $log_info["money"];//支出
		$log_info["balance_cash"] = 0;//可提现金额
		$log_info["balance_frost"] = -$log_info["money"];//不可提现金额
		$log_info["frost"] = 0;//冻结金额
		$log_info["await"] = 0;//待收金额
		$log_info["repay"] = 0;//待还金额
		$log_info["type"] = "borrow_repay_advance";//类型
		$log_info["to_userid"] = 0;//付给谁
	    $log_info["remark"] = "对[{$borrow_url}]借款标提前还款，还款的剩余本金";
		accountClass::AddLog($log_info);
        
        
		if ($data["borrow_frost_account"]>0){
			//最后一起解除冻结的金额
			$log_info["user_id"] = $data["user_id"];//操作用户id
			$log_info["nid"] = "borrow_repay_frost_".$data["borrow_nid"]."_".$data["user_id"];//订单号
            $log_info["borrow_nid"] = $data["borrow_nid"];//收入
            $log_info["account_web_status"] = 0;//
            $log_info["account_user_status"] = 0;//
    		$log_info["code"] = "borrow";//
    		$log_info["code_type"] = "borrow_repay_advance_frost";//
    		$log_info["code_nid"] = $data["borrow_nid"];//
			$log_info["money"] = $data["borrow_frost_account"];//操作金额
			$log_info["income"] =0;//收入
			$log_info["expend"] = 0;//支出
			$log_info["balance_cash"] = $log_info["money"];//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = -$log_info["money"];//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["repay"] = 0;//待还金额
			$log_info["type"] = "borrow_repay_frost";//类型
			$log_info["to_userid"] = 0;//付给谁
			$log_info["remark"] = "对[{$borrow_url}]借款提前还款的解冻";
			accountClass::AddLog($log_info);
		}
		
        
        //扣除费用
        //判断是否是vip
        $vip_status =0;
        $vip_result=usersClass::GetUsersVip(array("user_id"=>$data["user_id"]));
        if($vip_result==true){
             $vip_status = $vip_result['status'];
        }
        $credit_result = borrowClass::GetBorrowCredit(array("user_id"=>$data["user_id"]));
        $_fee["vip_status"] = $vip_status;//判断是不是vip
        $_fee["credit_fee"] =$credit_result['credit']['fee'];//判断是不是vip
        $_fee["borrow_type"] = $data["borrow_type"];//借款类型
        $_fee["borrow_style"] = $data["borrow_style"];//还款方式
        $_fee["type"] = "borrow_repay_advance";//属于借款者还是投资人
        $_fee["user_type"] = "borrow";//属于借款者还是投资人
        $_fee["capital"] = $data["repay_capital"];//属于借款者还是投资人
        $_fee["interest"] = $data["repay_interest"];//属于借款者还是投资人
        $result = borrowFeeClass::GetFeeValue($_fee);
        if ($result != false){
            foreach ($result as $key => $value){
                $log_info["user_id"] = $data["user_id"];//操作用户id
				$log_info["nid"] = "borrow_repay_advance_fee_".$value["nid"]."_".$data["borrow_nid"];//订单号
				$log_info["borrow_nid"] = $data['borrow_nid'];//收入
                $log_info["account_web_status"] = 0;//
                $log_info["account_user_status"] = 1;//
    			$log_info["code"] = "borrow";//
    			$log_info["code_type"] = "borrow_repay_advance_fee_".$value["nid"];//
    			$log_info["code_nid"] = $data["borrow_nid"];//
    			$log_info["money"] = $value['account'];//操作金额
				$log_info["income"] = 0;//收入
				$log_info["expend"] =  $log_info["money"];//支出
				$log_info["balance_cash"] = -$log_info["money"];//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
				$log_info["frost"] = 0;//冻结金额
				$log_info["await"] = 0;//待收金额
				$log_info["repay"] = 0;//待还金额
				$log_info["type"] = "borrow_repay_advance_fee_".$value["nid"];//类型
				$log_info["to_userid"] = 0;//付给谁
				$log_info["remark"] =  "对[{$borrow_url}]借款标提前还款，扣除[{$data["borrow_url"]}]{$log_info["money"]}元{$value['name']}";
				accountClass::AddLog($log_info);
            }
        }
        
       	//借款者的信用积分增加
		$credit_log['user_id'] =  $data["user_id"];
		$credit_log['nid'] = "borrow_success";
		$credit_log['code'] = "borrow";
		$credit_log['type'] = "repay_advance";
		$credit_log['addtime'] = time();
		$credit_log['article_id'] = $data["borrow_nid"];
		$credit_log['value'] = $data['repay_capital'];	
		$credit_log['type'] = "正常还款{$data['repay_capital']}所得的积分";		
		$result = creditClass::ActionCreditLog($credit_log);
        
        //用户记录
        $user_log["user_id"] = $data["user_id"];
		$user_log["code"] = "borrow";
		$user_log["type"] = "repay_advance_success";
		$user_log["operating"] = "repay_advance";
		$user_log["article_id"] = $data["user_id"];
		$user_log["result"] = 1;
		$user_log["content"] = "对借款标[{$borrow_url}]提前还款";
		usersClass::AddUsersLog($user_log);	
        
        $sql = "update `{borrow_repay}` set repay_type='advance',advance_status=1,repay_account_all=repay_capital,repay_yestime='{$data['repay_advance_time']}',repay_account_yes=repay_capital,repay_interest_yes=0,repay_interest_wait=0,repay_capital_yes=repay_capital,repay_capital_wait=0 where borrow_nid='{$data['borrow_nid']}' and repay_status=0 and user_id='{$data['user_id']}'";
		$mysql->db_query($sql);
        
        $sql = "update `{borrow}` set repay_advance_step=2 where borrow_nid='{$data["borrow_nid"]}'";
        $mysql->db_query($sql);
       
        return array("result"=>1,"step"=>2,"key"=>0,"period"=>0,"name"=>"正在对借款人进行操作，请不要关闭浏览器");
    }
    
    
    //操作投资人的信息
	function RepayAdvanceStep2($data = array()){
		global $mysql,$_G;
        $borrow_url = $data["borrow_url"];
        //判断还款状态是否正确
        if ($data['repay_advance_step']!=2){
            return "borrow_repayfull_step2_error";
        }
        if($data=="") return "borrow_repay_advance_error";
        //还款信息
        $sql = "select p1.*,p2.username from `{borrow_tender}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id  where  p1.borrow_nid='{$data['borrow_nid']}'  limit {$data['key']},1";
        $tender_result = $mysql->db_fetch_array($sql);
    
        $sql = "select p1.* from `{borrow_recover}` as p1 where  p1.borrow_nid='{$data['borrow_nid']}' and p1.recover_status=0 and p1.tender_id='{$tender_result['id']}' ";
		$recover_result = $mysql->db_fetch_arrays($sql);
        if ($recover_result==false) { 	
             $sql = "update {borrow} set repay_advance_step=3 where borrow_nid='{$data['borrow_nid']}'";
            $mysql->db_query($sql);
            return array("result"=>1,"step"=>3,"key"=>0,"name"=>"正在进行最后还款操作，请不要关闭浏览器");
        }
        $recover_capital = 0;
        foreach ($recover_result as $key => $value){
            $recover_capital += $value["recover_capital"];
            $recover_interest += $value["recover_interest"];
        }
       
		//如果成功，则将还款信息输进表里面去
        $_recover_nid = $data["borrow_nid"]."_".$tender_result['user_id']."_".$tender_result["id"];
		$recover_nid = "tender_recover_advance_yes_".$_recover_nid;//订单号
		//投资人的资金返回
		$log_info["user_id"] = $tender_result["user_id"];//操作用户id
		$log_info["nid"] = $recover_nid;//订单号
        $log_info["borrow_nid"] = $data["borrow_nid"];//收入
        $log_info["account_web_status"] = 0;//
        $log_info["account_user_status"] = 1;//
		$log_info["code"] = "borrow";//
		$log_info["code_type"] = "tender_recover_advance_yes";//
		$log_info["code_nid"] = $tender_result["id"];//
		$log_info["money"] = $recover_capital;//操作金额
		$log_info["income"] = $log_info["money"];//收入
		$log_info["expend"] = 0;//支出
		$log_info["balance_cash"] = $log_info["money"];//可提现金额
		$log_info["balance_frost"] = 0;//不可提现金额
		$log_info["frost"] = 0;//冻结金额
		$log_info["await"] = -$log_info["money"];//待收金额
		$log_info["repay"] = 0;//待还金额
		$log_info["type"] = "tender_recover_advance_yes";//类型
		$log_info["to_userid"] = $data["borrow_userid"];//付给谁
	    $log_info["remark"] = "客户（{$data["borrow_username"]}）对[{$borrow_url}]借款标的提前还款";
		accountClass::AddLog($log_info);
				
		//投资人的资金返回
		$log_info["user_id"] = $tender_result["user_id"];//操作用户id
		$log_info["nid"] = "tender_recover_advance_frost_yes_".$_recover_nid ;//订单号
        $log_info["borrow_nid"] = $data["borrow_nid"];//收入
        $log_info["account_web_status"] = 0;//
        $log_info["account_user_status"] = 0;//
		$log_info["code"] = "borrow";//
		$log_info["code_type"] = "tender_recover_advance_frost_yes";//
		$log_info["code_nid"] = $tender_result["id"];//
		$log_info["money"] = $recover_interest;//操作金额
		$log_info["income"] = 0;//收入
		$log_info["expend"] = $log_info["money"];//支出
		$log_info["balance_cash"] = 0;//可提现金额
		$log_info["balance_frost"] = 0;//不可提现金额
		$log_info["frost"] = 0;//冻结金额
		$log_info["await"] = -$log_info["money"];//待收金额
		$log_info["repay"] = 0;//待还金额
		$log_info["type"] = "tender_recover_advance_frost_yes";//类型
		$log_info["to_userid"] = $data["borrow_userid"];//付给谁
	    $log_info["remark"] = "客户（{$data["borrow_username"]}）对[{$borrow_url}]借款标的提前还款损失的利息";
		accountClass::AddLog($log_info);
        
		$user_log["user_id"] = $tender_result["user_id"];
		$user_log["code"] = "tender";
		$user_log["type"] = "recover_advance_success";
		$user_log["operating"] = "recover";
		$user_log["article_id"] = $tender_result["id"];
		$user_log["result"] = 1;
		$user_log["content"] = "收到借款标[{$borrow_url}]提前还款";
		usersClass::AddUsersLog($user_log);	
	
        
        $credit_result = borrowClass::GetBorrowCredit(array("user_id"=>$tender_result["user_id"]));
        $_fee["vip_status"] = $data['vip_status'];//判断是不是vip
        $_fee["credit_fee"] =$credit_result['credit']['fee'];//判断是不是vip
        $_fee["borrow_type"] = $data["borrow_type"];//借款类型
        $_fee["borrow_style"] = $data["borrow_style"];//还款方式
        $_fee["type"] = "borrow_repay_advance";//属于借款者还是投资人
        $_fee["user_type"] = "borrow";//属于借款者还是投资人
        $_fee["capital"] = $recover_capital;//属于借款者还是投资人
        $_fee["interest"] = $recover_interest;//属于借款者还是投资人
        $result = borrowFeeClass::GetFeeValue($_fee);
        $recover_fee = 0;
        if ($result != false){
            foreach ($result as $key => $value){
                $recover_fee += $value["account"];
                $log_info["user_id"] = $tender_result["user_id"];//操作用户id
				$log_info["nid"] = "tender_recover_advance_fee_".$value["nid"]."_".$_recover_nid;//订单号
                $log_info["account_web_status"] = 0;//
                $log_info["account_user_status"] = 1;//
				$log_info["borrow_nid"] = $data['borrow_nid'];//收入
    			$log_info["code"] = "borrow";//
    			$log_info["code_type"] = "tender_recover_advance";//
    			$log_info["code_nid"] = $recover_result["id"];//
    			$log_info["money"] = $value['account'];//操作金额
				$log_info["income"] = $log_info["money"];//收入
				$log_info["expend"] = 0;//支出
				$log_info["balance_cash"] = $log_info["money"];//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
				$log_info["frost"] = 0;//冻结金额
				$log_info["await"] = 0;//待收金额
				$log_info["repay"] = 0;//待还金额
				$log_info["type"] = "tender_recover_advance_fee_".$value["nid"];//类型
				$log_info["to_userid"] = 0;//付给谁
				$log_info["remark"] =  "用户成功提前还款，增加[{$data["borrow_url"]}]{$log_info["money"]}元{$value['name']}";
				accountClass::AddLog($log_info);
            }
        }
            	
			
		borrowCountClass::UpdateBorrowCount(array("user_id"=>$tender_result['user_id'],"borrow_nid"=>"{$data['borrow_nid']}","nid"=>$recover_nid,"tender_recover_advance_times"=>1,"tender_recover_advance_capital"=>$recover_capital,"tender_recover_advance_interest"=>$recover_interest,"tender_recover_advance_fee"=>$recover_fee,"tender_recover_advance_account"=>$recover_fee+$recover_capital));
		
        
        $sql = "update  `{borrow_recover}` set recover_type='advance',recover_yestime='{$data["repay_advance_time"]}',recover_account_yes = recover_capital ,recover_capital_yes = recover_capital ,recover_interest_yes = 0,recover_interest_wait = 0,recover_account_wait = 0,status=1,recover_status=1  where  borrow_nid='{$data['borrow_nid']}' and recover_status=0 and tender_id='{$tender_result['id']}'  ";
		$mysql->db_query($sql);
        
        
         //更新投资的信息
		$sql = "select count(1) as recover_times,sum(recover_account_yes) as recover_account_yes_num,sum(recover_interest_yes) as recover_interest_yes_num,sum(recover_capital_yes) as recover_capital_yes_num  from `{borrow_recover}` where tender_id='{$tender_result['id']}' ";
		$result = $mysql->db_fetch_array($sql);
		$recover_times = $result['recover_times'];
        
        
       	$sql = "update  `{borrow_tender}` set recover_advance_fee='{$recover_fee}',recover_full_status=1,recover_type='advance',recover_fee='{$recover_fee}',recover_times={$recover_times},recover_account_yes=recover_account_interest_yes+account, recover_account_capital_yes = account,recover_account_wait= 0,recover_account_capital_wait = 0 ,recover_account_interest_wait = 0 where id = '{$tender_result['id']}'";
		$mysql->db_query($sql);
        
		
		//还款人收到还款站内信 
		$remind['nid'] = "repay_advance_success";
		$remind['receive_userid'] = $data["user_id"];
        $remind['remind_nid'] =  "repay_advance_success_".$data["borrow_nid"]."_".$data["user_id"]."_".$tender_result["id"];
		$remind['code'] = "borrow";
		$remind['article_id'] = $tender_result["id"];
		$remind['title'] = "您已对客户".$tender_result["username"]."成功还款。";
		$remind['content'] = "您已在".date("Y-m-d",time())."对客户".$tender_result["username"]."还款成功，还款金额￥".$recover_result['recover_account'];
		remindClass::sendRemind($remind);
		
		//投资人收到还款站内信 
		$remind['nid'] = "recover_advance_success";				
		$remind['receive_userid'] = $tender_result['user_id'];
        $remind['remind_nid'] =  "recover_advance_success_".$data["borrow_nid"]."_".$tender_result["user_id"]."_".$tender_result["id"];
		$remind['code'] = "invest";
		$remind['article_id'] = $tender_result['user_id'];
		$remind['title'] = "用户【".$data["borrow_username"]."】对您所投资的借款标[{$borrow_url}]已经成功还款。";
		$remind['content'] = "用户【".$data["borrow_username"]."】在".date("Y-m-d",time())."对您所投资的借款标[{$borrow_url}]已经成功还款,还款金额￥".$recover_capital;
        remindClass::sendRemind($remind);
        return array("result"=>1,"step"=>2,"key"=>$data['key']+1,"name"=>"正在为投资人[{$tender_result['username']}]操作相关的金额，请不要关闭浏览器");
   } 
   
    //操作投资人的信息
	function RepayAdvanceStep3($data){
		global $mysql,$_G;
        $borrow_url = $data["borrow_url"];
        //判断还款状态是否正确
        if ($data['repay_advance_step']!=3){
            return "borrow_repayfull_step3_error";
        }
        if($data=="") return "borrow_repay_advance_error";
        
		
  	     borrowCountClass::UpdateBorrowCount(array("user_id"=>$tender_result['user_id'],"borrow_nid"=>"{$data['borrow_nid']}","nid"=>"borrow_repay_advance_".$data["borrow_nid"],"borrow_repay_advance_times"=>1,"borrow_repay_advance_capital"=>$data["repay_capital"],"borrow_repay_advance_interest"=>$data["repay_interest"],"borrow_repay_advance_fee"=>$data["repay_all"]-$data["repay_capital"],"borrow_repay_advance_account"=>$data["repay_all"],"borrow_repay_yes"=>$data["repay_capital"],"borrow_repay_wait"=>-$data["repay_capital"],"borrow_repay_capital_yes"=>$data["repay_capital"],"borrow_repay_capital_wait"=>-$data["repay_capital"]));
                
        //提前还款的期数
		$sql = "select count(1) as num from `{borrow_repay}` where borrow_nid='{$data["borrow_nid"]}' and repay_type='advance'";
        $advance_result = $mysql->db_fetch_array($sql);
        $repay_fee_advance = round($data["repay_account_fee"]/$advance_result["num"],2);
        
       	$sql = "update `{borrow_repay}` set repay_fee='{$repay_fee_advance}' where borrow_nid='{$data["borrow_nid"]}' and repay_type='advance'";
		$mysql->db_query($sql);
        
        //提前还款返回的额度
    	$_amount["user_id"] = $data['user_id'];//用户id
    	$_amount["amount_type"] = $data["amount_type"];//额度类型
    	$_amount["amount_style"] = "forever";
    	$_amount["type"] = "borrow_repay_advance";
    	$_amount["oprate"] = "return";
        $_amount["account"] = $data['repay_capital'];
    	$_amount["nid"] = $_amount["type"]."_".$data['user_id']."_".$data['borrow_nid'];
    	$_amount["remark"] = "提前还款成功[{$data["borrow_url"]}]，返回{$data['repay_capital']}元额度";
        borrowAmountClass::AddAmountLog($_amount);	
        
        
        //更新还款的所有信息
		$sql = "update `{borrow_repay}` set repay_status=1,repay_action_time='{$data["repay_advance_time"]}' where borrow_nid='{$data["borrow_nid"]}' and repay_status=0";
		$mysql->db_query($sql);
        
        //还款次数
        $sql = "select count(1) as num,sum(repay_account_yes) as yes_repay_account,sum(repay_capital) as yes_repay_capital,sum(repay_interest_yes) as yes_repay_interest  from `{borrow_repay}` where borrow_nid='{$data["borrow_nid"]}' and repay_status=1";
        $num_result = $mysql->db_fetch_array($sql);
        if ($num_result==""){
            $repay_times = 0;
        }else{
            $repay_times = $num_result["num"];
        }
        
        $repay_next_account ="";
        $repay_next_time = "";
        $repay_full_status=1;
        //更新投资的人的状态是否已经还完
        $sql = "update `{borrow_tender}` set recover_full_status='{$repay_full_status}' where borrow_nid='{$data["borrow_nid"]}'";
        $mysql->db_query($sql); 
            
        $sql = "update `{borrow}` set repay_advance_status=1,repay_fee_advance='{$data["repay_account_fee"]}',repay_full_status='{$repay_full_status}',repay_account_yes='{$num_result["yes_repay_account"]}',repay_account_wait=0,repay_account_capital_yes='{$num_result["yes_repay_capital"]}',repay_account_capital_wait=0,repay_account_interest_yes='{$num_result["yes_repay_interest"]}',repay_account_interest_wait=0,repay_times='{$repay_times}',repay_next_account='{$repay_next_account}',repay_next_time='{$repay_next_time}' where borrow_nid='{$data["borrow_nid"]}'";
        $mysql->db_query($sql);	//添加最后的还款金额
         
            
        $sql = "update `{borrow}` set repay_full_status=1,repay_advance_step=4 where borrow_nid='{$data['borrow_nid']}'";
        $mysql->db_query($sql);
        
        return array("result"=>0,"step"=>0,"key"=>0,"name"=>"还款成功");
    }
}
?>
