<?
/******************************
 * $File: borrow.repay_web.php
 * $Description: 网站还款垫付
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

require_once("borrow.loan.php");
require_once("borrow.fee.php");
require_once("borrow.model.php");
require_once("borrow.calculates.php");

class borrowRepayWebClass
{
    //普通还款款
    function RepayWebInfo($data){
        global $mysql,$_G;
       
        if ($data['repay_id']==""){
            return "borrow_repay_id_error";
        }
        //读取还款相关数据+
        $sql = "select * from `{borrow_repay}` where  id='{$data['repay_id']}'";
        $repay_result = $mysql->db_fetch_array($sql);
        if ($repay_result==false){
            return "borrow_repay_error";
        }
        $data["user_id"] = $repay_result["user_id"];
        //读取相关的借款说明+
       	$sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$repay_result['borrow_nid']}' and p1.user_id='{$data['user_id']}'";
        $borrow_result = $mysql->db_fetch_array($sql);
        if ($borrow_result==false){
            return "borrow_repay_borrow_error";
        }
        $repay_result['borrow_url'] = "<a href={$_G['web_domain']}/invest/a{$borrow_result['borrow_nid']}.html target=_blank style=color:blue>{$borrow_result['name']}</a>";//借款标地址
        $repay_result['borrow_name'] = $borrow_result["name"];
        $repay_result['borrow_username'] = $borrow_result["username"];
        $repay_result['borrow_type'] = $borrow_result["borrow_type"];
        $repay_result['borrow_style'] = $borrow_result["borrow_style"];
        $repay_result['borrow_period'] = $borrow_result["borrow_period"];
        $repay_result['borrow_frost_account'] = $borrow_result["borrow_frost_account"];
        $repay_result['amount_type'] = $borrow_result["amount_type"];
		if ($repay_result['repay_status']==1){
		  return "borrow_repay_yes";
		}elseif ($repay_result['status']!=1){
		   return "borrow_repay_status_error";
        }elseif ($repay_result['repay_web']==1){
		   return "borrow_repay_webstatus_error";
		}else{
		    $repay_account = $repay_result["repay_account"];//还款总额
		    $repay_period = $repay_result["repay_period"];
            
            /*
    		//判断上一期是否已还款+
    		if ($repay_period!=1){
    			$_repay_period = $repay_period-1;
    			$sql = "select repay_status from `{borrow_repay}` where `repay_period`=$_repay_period and borrow_nid={$borrow_result['borrow_nid']}";
    			$result = $mysql->db_fetch_array($sql);
    			if ($result!=false && $result['repay_status']!=1){
    				return "borrow_repay_up_notrepay";
    			}
    		}
            */
              //判断是提前还款，还是正常还款。+
            if ($repay_result["repay_days"]==""){
                $repay_result["repay_days"] =  borrowClass::GetDays(array("repay_time"=>$repay_result["repay_time"]));
            }
            
            //是否逾期还款
            if ($repay_result["repay_days"]<=0){
                return "borrow_repay_web_error";
            }
            
            if ( $repay_result["repay_account_all"]<=0){
                 //扣除费用
                //判断是否是vip
                $vip_status =0;
                $vip_result=usersClass::GetUsersVip(array("user_id"=>$repay_result["user_id"]));
                if($vip_result==true){
                     $vip_status = $vip_result['status'];
                }
                $repay_result['vip_status'] = $vip_status;
                $credit_result = borrowClass::GetBorrowCredit(array("user_id"=>$repay_result["user_id"]));
                $_fee["vip_status"] = $vip_status;//判断是不是vip
                $_fee["credit_fee"] =$credit_result['credit']['fee'];//判断是不是vip
                $_fee["borrow_type"] = $repay_result["borrow_type"];//借款类型
                $_fee["borrow_style"] = $repay_result["borrow_style"];//还款方式
                if ($late_days>0){
                    $_fee["type"] = "borrow_repay_late";//类型
                }elseif ($late_days==0){
                    $_fee["type"] = "borrow_repay";//类型
                }
                $_fee["user_type"] = "borrow";//属于借款者还是投资人
                $_fee["capital"] = $repay_result["repay_capital"];//属于借款者还是投资人
                $_fee["interest"] = $repay_result["repay_interest"];//属于借款者还是投资人
                $result = borrowFeeClass::GetFeeValue($_fee);
                $_fee_account = 0;
                if ($result!=false){
                    foreach ($result as $key => $value){
                        $_fee_account += $value["account"];
                    }
                }
                //应该还款的总额。
                $repay_result["repay_account_fee"]  = $_fee_account;
                $repay_result["repay_account_all"]  = $repay_result["repay_account"] + $_fee_account;
            }else{
                $repay_result["repay_account_fee"] = $repay_result["repay_account_all"]- $repay_result["repay_account"];
            }
            
            //第一步做相关的判断
            if ($data['step']==0){
                //判断是否可以重新审核
                 
                
                 //将还款信息写进还款列表中去。+
                if ($repay_result["repay_web_time"]==""){
                    $sql = "update `{borrow_repay}` set repay_web_time='".time()."' where id='{$repay_result['id']}'";
                    $mysql->db_query($sql);
                }
                
                $sql = "update `{borrow_repay}` set repay_web_step=1 where id='{$repay_result['id']}'";
                $mysql->db_query($sql);
                
        		//更新满标时的操作人
                return array("result"=>1,"step"=>1,"key"=>"0","name"=>"正在还款中，请不要关闭浏览器");	
            }else{
                $fun = "RepayWebStep".$data['step'];
                $repay_result["key"] = $data['key'];
    		    $result = self::$fun($repay_result);
                return $result;
            }
		}
        //基本的资料
    }
	
    //操作投资人的信息
	function RepayWebStep1($repay_result){
		global $mysql,$_G;
        
        //判断还款状态是否正确
        if ($repay_result['repay_web_step']!=1){
            return "borrow_repay_step1_error";
        }
        if($repay_result=="") return "";
        //成功借款
        $sql = "select p1.*,p2.username,p3.borrow_type,p3.user_id as borrow_userid,p4.vip_late_scale,p4.all_late_scale,p6.change_status,p6.change_userid from `{borrow_recover}` as p1 
				left join `{users}` as p2 on p1.user_id=p2.user_id 
				left join `{borrow}` as p3 on p1.borrow_nid=p3.borrow_nid 
				left join `{borrow_type}` as p4 on p3.borrow_type=p4.nid 
				left join `{borrow_tender}` as p6 on p1.tender_id=p6.id 
				where p1.recover_period='{$repay_result["repay_period"]}' and  p1.borrow_nid='{$repay_result['borrow_nid']}' limit {$repay_result['key']},1";
		$recover_result = $mysql->db_fetch_array($sql);
		if ($recover_result==false){
            $sql = "update `{borrow_repay}` set repay_web_step=2 where id='{$repay_result['id']}'";
            $mysql->db_query($sql);
			return array("result"=>1,"step"=>2,"key"=>0,"name"=>"正在进行最后还款操作，请不要关闭浏览器");
		}
        
		$recove_id = $recover_result['id'];
		if ($recover_result['change_status']==1){
			$recove_userid = $recover_result['change_userid'];
		}else{
			$recove_userid = $recover_result['user_id'];
		}
        $vip_status =0;
        $vip_result=usersClass::GetUsersVip(array("user_id"=>$recove_userid));
        if($vip_result==true){
             $vip_status = $vip_result['status'];
        }
		if ($vip_status==1){
			$recover_account = $recover_result['recover_account']*$recover_result['vip_late_scale']*0.01;
            $recover_capital =  $recover_result['recover_capital']*$recover_result['vip_late_scale']*0.01;
            $recover_interest =  $recover_result['recover_interest']*$recover_result['vip_late_scale']*0.01;
		}else{
			$recover_account = $recover_result['recover_capital']*$recover_result['all_late_scale']*0.01;
            $recover_capital =  $recover_result['recover_capital']*$recover_result['all_late_scale']*0.01;
		}
		$recover_period = $recover_result['recover_period'];
		$borrow_nid = $repay_result['borrow_nid'];
		$borrow_url = $repay_result['borrow_url'];
		$borrow_username = $repay_result['borrow_username'];
		$borrow_name = $repay_result['borrow_name'];
		//如果成功，则将还款信息输进表里面去
        $_recover_nid = $borrow_nid."_".$recove_userid."_".$recover_result['id']."_".$recover_period;
		$recover_nid = "tender_recover_yes_".$_recover_nid;//订单号
		//投资人的资金返回
		$log_info["user_id"] = $recove_userid;//操作用户id
		$log_info["nid"] = "tender_recover_web_".$_recover_nid;//订单号
        $log_info["borrow_nid"] = $borrow_nid;//收入
        $log_info["account_web_status"] = 1;//
        $log_info["account_user_status"] = 1;
		$log_info["code"] = "borrow";//
		$log_info["code_type"] = "tender_recover_web";//
		$log_info["code_nid"] = $recove_id;//
		$log_info["money"] = $recover_account;//操作金额
		$log_info["income"] = $log_info["money"];//收入
		$log_info["expend"] = 0;//支出
		$log_info["balance_cash"] = $log_info["money"];//可提现金额
		$log_info["balance_frost"] = 0;//不可提现金额
		$log_info["frost"] = 0;//冻结金额
		$log_info["await"] = -$log_info["money"];//待收金额
		$log_info["repay"] = 0;//待还金额
		$log_info["type"] = "tender_recover_web";//类型
		$log_info["to_userid"] = $recover_result["borrow_userid"];//付给谁
	    $log_info["remark"] = "网站对[{$borrow_url}]借款标的第".($recover_period)."期垫付还款";
		accountClass::AddLog($log_info);
				
		
		$user_log["user_id"] = $recove_userid;
		$user_log["code"] = "tender";
		$user_log["type"] = "recover_success";
		$user_log["operating"] = "recover";
		$user_log["article_id"] = $recove_userid;
		$user_log["result"] = 1;
		$user_log["content"] = "收到借款标[{$borrow_url}]的还款";
		usersClass::AddUsersLog($user_log);	
	
    	//扣除费用
        //判断是否是vip
        $credit_result = borrowClass::GetBorrowCredit(array("user_id"=>$recover_userid));
        $_fee["vip_status"] = $vip_status;//判断是不是vip
        $_fee["credit_fee"] =$credit_result['credit']['fee'];//判断是不是vip
        $_fee["borrow_type"] = $repay_result["borrow_type"];//借款类型
        $_fee["borrow_style"] = $repay_result["borrow_style"];//还款方式
        $_fee["type"] = "borrow_repay";//属于借款者还是投资人
        $_fee["user_type"] = "tender";//属于借款者还是投资人
        $_fee["capital"] = $recover_result["recover_capital"];//属于借款者还是投资人
        $_fee["interest"] = $recover_result["recover_interest"];//属于借款者还是投资人
        $result = borrowFeeClass::GetFeeValue($_fee);
        $recover_fee = 0;
        if ($result != false){
            foreach ($result as $key => $value){
				
				//不是vip会员不扣除利息服务费  wdf 20130115
				if($vip_status==0)continue;
				
                $recover_fee += $value["account"];
                $log_info["user_id"] = $recover_result["user_id"];//操作用户id
				$log_info["nid"] = "tender_recover_fee_".$value["nid"]."_".$_recover_nid;//订单号
				$log_info["borrow_nid"] = $recover_result['borrow_nid'];//收入
                $log_info["account_web_status"] = 1;//
                $log_info["account_user_status"] = 1;
    			$log_info["code"] = "borrow";//
    			$log_info["code_type"] = "tender_recover_fee_".$value["nid"];//
    			$log_info["code_nid"] = $recover_result["id"];//
    			$log_info["money"] = $value['account'];//操作金额
				$log_info["income"] = 0;//收入
				$log_info["expend"] = $log_info["money"];//支出
				$log_info["balance_cash"] = -$log_info["money"];//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
				$log_info["frost"] = 0;//冻结金额
				$log_info["await"] = 0;//待收金额
				$log_info["repay"] = 0;//待还金额
				$log_info["type"] = "tender_recover_fee_".$value["nid"];//类型
				$log_info["to_userid"] = 0;//付给谁
				$log_info["remark"] =  "网站对【[{$repay_result["borrow_url"]}]】垫付还款，扣除{$log_info["money"]}元{$value['name']}";
				accountClass::AddLog($log_info);
            }
        }
		
		//投资人收到还款站内信 
		$remind['nid'] = "recover_web";				
		$remind['receive_userid'] = $recove_userid;
        $remind['remind_nid'] =  "recover_web_".$recover_result["borrow_nid"]."_".$recove_userid."_".$recover_result["id"];
		$remind['code'] = "invest";
		$remind['article_id'] = $recove_userid;
		$remind['title'] = "网站对您所投资的借款标[{$borrow_url}]已经垫付还款。";
		$remind['content'] = "网站在".date("Y-m-d",time())."对您所投资的借款标[{$borrow_url}]已经成功垫付,还款金额￥".$recover_result['recover_account'];
        remindClass::sendRemind($remind);
      
        
            	
		 //更新回收的信息
         if ($vip_status==1){
            $sql = "update  `{borrow_recover}` set recover_type='web',recover_fee='{$recover_fee}',recover_yestime='".time()."',recover_account_yes = {$recover_account} ,recover_capital_yes = {$recover_capital} ,recover_interest_yes = {$recover_interest},status=1,recover_status=1,recover_web=1 where id = '{$recover_result['id']}'";
         }else{
            $sql = "update  `{borrow_recover}` set recover_type='web',recover_fee='{$recover_fee}',recover_yestime='".time()."',recover_account_yes = {$recover_account} ,recover_capital_yes = {$recover_capital} ,recover_interest_yes = 0,status=1,recover_status=1,recover_web=1  where id = '{$recover_result['id']}'";
         }
		$mysql->db_query($sql);
        
        
		$sql = "select count(1) as recover_times,sum(recover_account_yes) as recover_account_yes_num,sum(recover_interest_yes) as recover_interest_yes_num,sum(recover_capital_yes) as recover_capital_yes_num  from `{borrow_recover}` where tender_id='{$recover_result['tender_id']}' and recover_status=1";
		$result = $mysql->db_fetch_array($sql);
		$recover_times = $result['recover_times'];
       	$sql = "update  `{borrow_tender}` set recover_times={$recover_times},recover_account_yes= {$result['recover_account_yes_num']},recover_account_capital_yes =  {$result['recover_capital_yes_num']} ,recover_account_interest_yes = {$result['recover_interest_yes_num']},recover_account_wait= recover_account_all - recover_account_yes,recover_account_capital_wait = account - recover_account_capital_yes  ,recover_account_interest_wait = recover_account_interest -  recover_account_interest_yes  where id = '{$recover_result['tender_id']}'";
		$mysql->db_query($sql);
        	
		borrowCountClass::UpdateBorrowCount(array("user_id"=>$recove_userid,"borrow_nid"=>"{$repay_result['borrow_nid']}","nid"=>$recover_nid,"tender_recover_times_yes"=>1,"tender_recover_times_wait"=>-1,"tender_recover_yes"=>$recover_result['recover_account'],"tender_recover_wait"=>-$recover_result['recover_account'],"tender_capital_yes"=>$recover_result['recover_capital'],"tender_capital_wait"=>-$recover_result['recover_capital'],"tender_interest_yes"=>$recover_result['recover_interest'],"tender_interest_wait"=>-$recover_result['recover_interest']));
			
        
        return array("result"=>1,"step"=>1,"key"=>$repay_result['key']+1,"name"=>"正在为投资人[{$recover_result["username"]}]操作相关的金额，请不要关闭浏览器");
   }
	
     //操作投资人的信息
    function RepayWebStep2($repay_result){
       global $mysql;
       	//判断还款状态是否正确
        if ($repay_result['repay_web_step']!=2){
            return "borrow_repay_step2_error";
        }
		  
         $sql = "select * from `{borrow_recover}` where borrow_nid='{$repay_result["borrow_nid"]}' and recover_status=0 order by recover_period asc";
        $_result = $mysql->db_fetch_array($sql);
        if ($_result!=false){
            $recover_full_status=0;
        }else{
            $recover_full_status=1;
        }
        
        
        //更新投资的人的状态是否已经还完
        $sql = "update `{borrow_tender}` set recover_full_status='{$recover_full_status}' where borrow_nid='{$repay_result["borrow_nid"]}'";
        $mysql->db_query($sql);  
        
        $sql = "select sum(recover_account_yes) as num from `{borrow_recover}` where borrow_nid='{$repay_result["borrow_nid"]}' and recover_period='{$repay_result['repay_period']}'";
        $result = $mysql->db_fetch_array($sql);
        $repay_web_account = $result["num"];
        
        $sql = "update `{borrow_repay}` set repay_web=1,repay_step=3,repay_web_account='{$repay_web_account}' where id='{$repay_result['id']}'";
        $mysql->db_query($sql); 
		
       
        return array("result"=>0,"step"=>0,"key"=>0,"name"=>"垫付成功");
    }
    
}    
    
    
?>
