<?php
/******************************
 * $File: borrow.repay.php
 * $Description: 还款文件
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
require_once(ROOT_PATH."/modules/approve/approve.class.php");

class borrowRepayClass
{
    //普通还款款
    function RepayInfo($data){
        global $mysql,$_G;
        if ($data['user_id']==""){
            return "borrow_repay_userid_error";
        }
        if ($data['repay_id']==""){
            return "borrow_repay_id_error";
        }
        //读取还款相关数据
        $sql = "select * from `{borrow_repay}` where user_id='{$data['user_id']}' and id='{$data['repay_id']}'";
        $repay_result = $mysql->db_fetch_array($sql);
        if ($repay_result==false){
            return "borrow_repay_error";
        }
        //读取相关的借款说明
       	$sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$repay_result['borrow_nid']}' and p1.user_id='{$data['user_id']}'";
        $borrow_result = $mysql->db_fetch_array($sql);
        if ($borrow_result==false){
            return "borrow_repay_borrow_error";
        }
       $repay_result['borrow_url'] = "<a href={$_G['web_domain']}/invest/a{$borrow_result['borrow_nid']}.html target=_blank >{$borrow_result['name']}</a>";//借款标地址
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
		}else{
		    $repay_account = $repay_result["repay_account"];//还款总额
		    $repay_period = $repay_result["repay_period"];
            

    		//判断上一期是否已还款+
    		if ($repay_period!=1){
                $sql = "select repay_period from `{borrow_repay}` where borrow_nid={$borrow_result['borrow_nid']} order by repay_time";
                $result = $mysql->db_fetch_arrays($sql);
                $_repay_period = [];
                foreach($result as $value){
                    $_repay_period[]=$value['repay_period'];
                }
                $key = array_keys($_repay_period, $repay_period);
    			$_repay_period = $_repay_period[$key[0]-1];
    			$sql = "select repay_status from `{borrow_repay}` where `repay_period`=$_repay_period and borrow_nid={$borrow_result['borrow_nid']}";
    			$result = $mysql->db_fetch_array($sql);
    			if ($result!=false && $result['repay_status']!=1){
    				return "borrow_repay_up_notrepay";
    			}
    		}
              //判断是提前还款，还是正常还款。+
            if ($repay_result["repay_days"]==""){
                $_repay_time = get_mktime(date("Y-m-d",$repay_result["repay_time"]));
                $_now_time = get_mktime(date("Y-m-d",time()));                
                $late_days = ($_now_time - $_repay_time)/(60*60*24);
                $repay_result["repay_days"] = $late_days;
            }
            
            //是否逾期还款
            if ($late_days<0){
                //return "borrow_repay_advance_error";
            }
            
            //如果逾期则不能在此文件操作
             if ($late_days>0){
               return "borrow_repay_late_day_error";
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
                $_fee["type"] = "borrow_repay";//类型
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
                 
        		//判断可用余额是否够还款,并且此标还未进行还款
                if ($repay_result['repay_step']==0){
            		$account_result =  accountClass::GetAccountUsers(array("user_id"=>$repay_result['user_id']));
            		if ($account_result['balance']<$repay_result["repay_account_all"]){
            			return "borrow_repay_account_use_none";
            		}
                }
                
                 //将还款信息写进还款列表中去。+
                if ($repay_result["repay_action_time"]==""){
                    $sql = "update `{borrow_repay}` set repay_step=1,repay_days='{$repay_result["repay_days"]}',repay_action_time='".time()."'  where id='{$repay_result['id']}'";
                    $mysql->db_query($sql);
                }else{
                    $sql = "update `{borrow_repay}` set repay_step=1 where id='{$repay_result['id']}'";
                    $mysql->db_query($sql);
                }
        		//更新满标时的操作人
                return array("result"=>1,"step"=>1,"key"=>"0","name"=>"正在还款中，请不要关闭浏览器");	
            }else{
                $fun = "RepayStep".$data['step'];
                $repay_result["key"] = $data['key'];
    		    $result = self::$fun($repay_result);
                return $result;
            }
		}
        //基本的资料
    }
    
    
    //操作投资人的信息
	function RepayStep1($repay_result){
	    global $mysql,$_G;
        $repay_nid = $repay_result["borrow_nid"]."_".$repay_result['user_id']."_".$repay_result['id']."_".$repay_result['repay_period'];
		$borrow_url = $repay_result["borrow_url"];
        if ($repay_result['repay_step']!=1){
            return "borrow_repay_step1_error";
         }
	   	//扣除借款人的还款金额
		$log_info["user_id"] = $repay_result["user_id"];//操作用户id
		$log_info["nid"] = "borrow_repay_".$repay_nid;//订单号
        $log_info["account_web_status"] = 0;//
        $log_info["account_user_status"] = 1;//
        $log_info["borrow_nid"] = $repay_result["borrow_nid"];//收入
		$log_info["code"] = "borrow";//
		$log_info["code_type"] = "borrow_repay";//
		$log_info["code_nid"] = $repay_result["id"];//
		$log_info["money"] = $repay_result["repay_account"];//操作金额
		$log_info["income"] = 0;//收入
		$log_info["expend"] = $log_info["money"];//支出
		$log_info["balance_cash"] = 0;//可提现金额
		$log_info["balance_frost"] = -$log_info["money"];//不可提现金额
		$log_info["frost"] = 0;//冻结金额
		$log_info["await"] = 0;//待收金额
		$log_info["repay"] = 0;//待还金额
		$log_info["type"] = "borrow_repay";//类型
		$log_info["to_userid"] = 0;//付给谁
	    $log_info["remark"] = "对[{$borrow_url}]借款标第".$repay_result["repay_period"]."期还款";
		accountClass::AddLog($log_info);
        
        if ($repay_result["borrow_type"]=="day" || $repay_result["repay_period"] == $repay_result["borrow_period"]){
			if ($repay_result["borrow_frost_account"]>0){
				//最后一起解除冻结的金额
				$log_info["user_id"] = $repay_result["user_id"];//操作用户id
				$log_info["nid"] = "borrow_repay_frost_".$repay_result["borrow_nid"]."_".$repay_result["user_id"];//订单号
                $log_info["borrow_nid"] = $repay_result["borrow_nid"];//收入
                $log_info["account_web_status"] = 0;//
                $log_info["account_user_status"] = 0;//
        		$log_info["code"] = "borrow";//
        		$log_info["code_type"] = "borrow_repay_frost";//
        		$log_info["code_nid"] = $repay_result["borrow_nid"];//
				$log_info["money"] = $repay_result["borrow_frost_account"];//操作金额
				$log_info["income"] =0;//收入
				$log_info["expend"] = 0;//支出
				$log_info["balance_cash"] = $log_info["money"];//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
				$log_info["frost"] = -$log_info["money"];//冻结金额
				$log_info["await"] = 0;//待收金额
				$log_info["repay"] = 0;//待还金额
				$log_info["type"] = "borrow_repay_frost";//类型
				$log_info["to_userid"] = 0;//付给谁
				$log_info["remark"] = "对[{$borrow_url}]借款的解冻";
				accountClass::AddLog($log_info);
			}
		}
        
        //扣除费用
        //判断是否是vip
        $vip_status =0;
        $vip_result=usersClass::GetUsersVip(array("user_id"=>$repay_result["user_id"]));
        if($vip_result==true){
             $vip_status = $vip_result['status'];
        }
        $credit_result = borrowClass::GetBorrowCredit(array("user_id"=>$repay_result["user_id"]));
        $_fee["vip_status"] = $vip_status;//判断是不是vip
        $_fee["credit_fee"] =$credit_result['credit']['fee'];//判断是不是vip
        $_fee["borrow_type"] = $repay_result["borrow_type"];//借款类型
        $_fee["borrow_style"] = $repay_result["borrow_style"];//还款方式
        $_fee["type"] = "borrow_repay";//属于借款者还是投资人
        $_fee["user_type"] = "borrow";//属于借款者还是投资人
        $_fee["capital"] = $repay_result["repay_capital"];//属于借款者还是投资人
        $_fee["interest"] = $repay_result["repay_interest"];//属于借款者还是投资人
        $result = borrowFeeClass::GetFeeValue($_fee);
        if ($result != false){
            foreach ($result as $key => $value){
                $log_info["user_id"] = $repay_result["user_id"];//操作用户id
				$log_info["nid"] = "borrow_repay_fee_".$value["nid"]."_".$repay_nid;//订单号
                $log_info["account_web_status"] = 1;//
                $log_info["account_user_status"] = 1;//
				$log_info["borrow_nid"] = $repay_result['borrow_nid'];//收入
    			$log_info["code"] = "borrow";//
    			$log_info["code_type"] = "borrow_repay_fee_".$value["nid"];//
    			$log_info["code_nid"] = $repay_result["id"];//
    			$log_info["money"] = $value['account'];//操作金额
				$log_info["income"] = 0;//收入
				$log_info["expend"] =  $log_info["money"];//支出
				$log_info["balance_cash"] = -$log_info["money"];//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
				$log_info["frost"] = 0;//冻结金额
				$log_info["await"] = 0;//待收金额
				$log_info["repay"] = 0;//待还金额
				$log_info["type"] = "borrow_repay_fee_".$value["nid"];//类型
				$log_info["to_userid"] = 0;//付给谁
				$log_info["remark"] =  "对[{$borrow_url}]借款标第".$repay_result["repay_period"]."期正常还款，扣除[{$repay_result["borrow_url"]}]{$log_info["money"]}元{$value['name']}";
				accountClass::AddLog($log_info);
            }
        }
        
       	//借款者的信用积分增加
		$credit_log['user_id'] = $repay_result["user_id"];
		$credit_log['nid'] = "borrow_success";
		$credit_log['code'] = "borrow";		
		$credit_log['addtime'] = time();
		$credit_log['article_id'] = $repay_result["id"];
		$credit_log['value'] = round($repay_result["repay_capital"]/100);	
		$credit_log['type'] = "正常还款{$repay_result["repay_capital"]}所得的积分";		
		$result = creditClass::ActionCreditLog($credit_log);
        
        //用户记录
        $user_log["user_id"] = $repay_result["user_id"];
		$user_log["code"] = "borrow";
		$user_log["type"] = "repay_success";
		$user_log["operating"] = "repay";
		$user_log["article_id"] = $repay_result["user_id"];
		$user_log["result"] = 1;
		$user_log["content"] = "对借款标[{$borrow_url}]正常还款";
		usersClass::AddUsersLog($user_log);	
        
        $sql = "update `{borrow_repay}` set repay_step=2,repay_account_all='{$repay_result['repay_account_all']}',repay_yestime='".time()."',repay_account_yes=repay_account,repay_interest_yes=repay_interest,repay_capital_yes=repay_capital where id='{$repay_result['id']}'";
		$mysql->db_query($sql);
        
        return array("result"=>1,"step"=>2,"key"=>0,"name"=>"正在对借款人进行操作，请不要关闭浏览器");
    }
	
    //操作投资人的信息
	function RepayStep2($repay_result){
		global $mysql,$_G;
        
        //判断还款状态是否正确
        if ($repay_result['repay_step']!=2){
            return "borrow_repay_step2_error";
        }
        if($repay_result=="") return "";
        //成功借款
        $sql = "select p1.*,p2.username,p3.change_status,p3.change_userid,p4.username as change_username from `{borrow_recover}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id left join `{borrow_tender}` as p3 on p1.tender_id=p3.id left join `{users}` as p4 on p3.change_userid=p4.user_id where p1.recover_period='{$repay_result["repay_period"]}' and  p1.borrow_nid='{$repay_result['borrow_nid']}' limit {$repay_result['key']},1";
		$recover_result = $mysql->db_fetch_array($sql);
		if ($recover_result==false){
            $sql = "update `{borrow_repay}` set repay_step=3 where id='{$repay_result['id']}'";
            $mysql->db_query($sql);
			return array("result"=>1,"step"=>3,"key"=>0,"name"=>"正在进行最后还款操作，请不要关闭浏览器");
		}
       
		$recove_id = $recover_result['id'];
		if ($recover_result['change_status']==1){
			$recove_userid = $recover_result['change_userid'];
			$recove_username = $recover_result['change_username'];
		}else{
			$recove_userid = $recover_result['user_id'];
			$recove_username = $recover_result['username'];
		}
		$recover_account = $recover_result['recover_account'];
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
		$log_info["nid"] = "tender_recover_yes_".$_recover_nid;//订单号
        $log_info["account_web_status"] = 0;//
        $log_info["account_user_status"] = 1;//
        $log_info["borrow_nid"] = $borrow_nid;//收入
		$log_info["code"] = "borrow";//
		$log_info["code_type"] = "tender_recover_yes";//
		$log_info["code_nid"] = $recove_id;//
		$log_info["money"] = $recover_account;//操作金额
		$log_info["income"] = $log_info["money"];//收入
		$log_info["expend"] = 0;//支出
		$log_info["balance_cash"] = $log_info["money"];//可提现金额
		$log_info["balance_frost"] = 0;//不可提现金额
		$log_info["frost"] = 0;//冻结金额
		$log_info["await"] = -$log_info["money"];//待收金额
		$log_info["repay"] = 0;//待还金额
		$log_info["type"] = "tender_recover_yes";//类型
		$log_info["to_userid"] = $borrow_userid;//付给谁
	    $log_info["remark"] = "客户（{$borrow_username}）对[{$borrow_url}]借款标的第".($recover_period)."期还款";
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
        $vip_status =0;
        $vip_result=usersClass::GetUsersVip(array("user_id"=>$recove_userid));
        if($vip_result==true){
             $vip_status = $vip_result['status'];
        }
        $credit_result = borrowClass::GetBorrowCredit(array("user_id"=>$recove_userid));
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
                $recover_fee += $value["account"];
                $log_info["user_id"] = $recove_userid;//操作用户id
				$log_info["nid"] = "tender_recover_fee_".$value["nid"]."_".$_recover_nid;//订单号
				$log_info["borrow_nid"] = $recover_result['borrow_nid'];//收入
                $log_info["account_web_status"] = 1;//
                $log_info["account_user_status"] = 1;//
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
				$log_info["remark"] =  "用户成功正常还款，扣除[{$repay_result["borrow_url"]}]{$log_info["money"]}元{$value['name']}";
				accountClass::AddLog($log_info);
            }
        }
			
        
        
		//还款人收到还款站内信 
		$remind['nid'] = "repay_success";
		$remind['receive_userid'] = $repay_result["user_id"];
        $remind['remind_nid'] =  "repay_success_".$recover_result["borrow_nid"]."_".$repay_result["user_id"]."_".$recover_result["id"];
		$remind['code'] = "borrow";
		$remind['article_id'] = $repay_result["user_id"];
		$remind['title'] = "您已对客户".$recove_username."成功还款。";
		$remind['content'] = "您已在".date("Y-m-d",time())."对客户".$recove_username."还款成功，还款金额￥".$recover_result['recover_account'];
		remindClass::sendRemind($remind);
		
		//投资人收到还款站内信 
		$remind['nid'] = "recover_success";				
		$remind['receive_userid'] = $recove_userid;
        $remind['remind_nid'] =  "recover_success_".$recover_result["borrow_nid"]."_".$recove_userid."_".$recover_result["id"];
		$remind['code'] = "invest";
		$remind['article_id'] = $recove_userid;
		$remind['title'] = "用户【".$borrow_username."】对您所投资的借款标[{$borrow_name}]已经成功还款。";
		$remind['content'] = "用户【".$borrow_username."】在".date("Y-m-d",time())."对您所投资的借款标[{$borrow_url}]已经成功还款,还款金额￥".$recover_result['recover_account'];
        remindClass::sendRemind($remind);


        // 给投资人发送手机短信
        $sql = 'select `phone` from `{users_info}` where `user_id`='.$recove_userid.' and `phone_status`=1 and `phone`!=\'\'';
        $phone = $mysql->db_fetch_array($sql);
        if(!empty($phone)){
            $phone_data = [];
            $phone_data['status'] = 1;
            $phone_data['user_id'] = $recove_userid;
            $phone_data['type'] = "recover_success";
            $phone_data['phone'] = $phone['phone'];
            $phone_data['contents'] = "尊敬的融易融用户，您投资的借款标[{$borrow_name}]￥".$recover_result['recover_account']."成功回款。为了提高资金的使用率，您可提现或续投平台的新标。感谢您对融易融的支持。";
            $phone_data['contents'] = iconv("GBK","UTF-8",$phone_data['contents']);
            $result = approveClass::SendSMS($phone_data);
        }
                    // 投标奖励
                    if($repay_result['award_status']>0 && $repay_result['award_false']==0){
                        $sql = 'select (`recover_account_all`-`recover_account_interest`) as acount from {borrow_tender} where id='.$recover_result['tender_id'].' and `award`=0';
                        $recover_capital = $mysql->db_fetch_array($sql);
                        if(!empty($recover_capital['acount'])){
                            if($roam_result['award_status']==2){
                                $award = round($recover_capital['acount']*$repay_result['award_scale']/100,2);
                            }
                            if($repay_result['award_status']==1){
                                $award = $repay_result['award_account'];
                            }
                            if($award>0){
                                $log_info["user_id"] = $recove_userid;//操作用户id
                                $log_info["nid"] = "brrow_award_add_".$recover_result["borrow_nid"]."_".$recove_userid."_".$recover_result['tender_id'];//订单号
                                $log_info["account_web_status"] = 1;//
                                $log_info["account_user_status"] = 1;//
                                $log_info["borrow_nid"] = $recover_result["borrow_nid"];//收入
                                $log_info["code"] = "tender";//
                                $log_info["code_type"] = "brrow_tender_award";//
                                $log_info["code_nid"] = $recover_result['tender_id'];//
                                $log_info["money"] = $award;//操作金额
                                $log_info["income"] = $award;//收入
                                $log_info["expend"] = 0;//支出
                                $log_info["balance_cash"] = $award;//可提现金额
                                $log_info["balance_frost"] = 0;//不可提现金额
                                $log_info["frost"] = 0;//冻结金额
                                $log_info["await"] = 0;//待收金额
                                $log_info["type"] = "brrow_tender_award";//类型
                                $log_info["to_userid"] = $recove_userid;//付给谁
                                $log_info["remark"] =  "投资借款[{$borrow_url}]获得的投资奖励";
                                accountClass::AddLog($log_info);
                                $remind['nid'] = "brrow_tender_award";
                                $remind['remind_nid'] = $_nid;
                                $remind['receive_userid'] = $recove_userid;
                                $remind['article_id'] = $recover_result['tender_id'];
                                $remind['code'] = "borrow";
                                $remind['title'] = '投标奖励';
                                $remind['content'] = '你所投资的【'.$repay_result["username"].'】标['.$borrow_url.']在'.date('Y-m-d').'获得'.$award.'元奖励';
                                remindClass::sendRemind($remind);
                            }
                        }
                    }
        
        $sql = "update `{borrow_recover}` set recover_type='yes',recover_fee='{$recover_fee}',recover_yestime='".time()."',recover_account_yes = recover_account ,recover_capital_yes = recover_capital ,recover_interest_yes = recover_interest,status=1,recover_status=1 where id = '{$recover_result['id']}'";
		$mysql->db_query($sql);
        
            	
		 //更新投资的信息
		$sql = "select count(1) as recover_times,sum(recover_account_yes) as recover_account_yes_num,sum(recover_interest_yes) as recover_interest_yes_num,sum(recover_capital_yes) as recover_capital_yes_num  from `{borrow_recover}` where tender_id='{$recover_result['tender_id']}' and recover_status=1";
		$result = $mysql->db_fetch_array($sql);
		$recover_times = $result['recover_times'];
        
        
       	$sql = "update  `{borrow_tender}` set recover_times={$recover_times},recover_account_yes= {$result['recover_account_yes_num']},recover_account_capital_yes =  {$result['recover_capital_yes_num']} ,recover_account_interest_yes = {$result['recover_interest_yes_num']},recover_account_wait= recover_account_all - recover_account_yes,recover_account_capital_wait = account - recover_account_capital_yes  ,recover_account_interest_wait = recover_account_interest -  recover_account_interest_yes  where id = '{$recover_result['tender_id']}'";
		$mysql->db_query($sql);
        	
		borrowCountClass::UpdateBorrowCount(array("user_id"=>$recove_userid,"borrow_nid"=>"{$repay_result['borrow_nid']}","nid"=>$recover_nid,"tender_recover_times_yes"=>1,"tender_recover_times_wait"=>-1,"tender_recover_yes"=>$recover_result['recover_account'],"tender_recover_wait"=>-$recover_result['recover_account'],"tender_capital_yes"=>$recover_result['recover_capital'],"tender_capital_wait"=>-$recover_result['recover_capital'],"tender_interest_yes"=>$recover_result['recover_interest'],"tender_interest_wait"=>-$recover_result['recover_interest']));
			
        
        return array("result"=>1,"step"=>2,"key"=>$repay_result['key']+1,"name"=>"正在为投资人[{$recove_username}]操作相关的金额，请不要关闭浏览器");
   }
	
     //操作投资人的信息
    function RepayStep3($repay_result){
       global $mysql;
       	//判断还款状态是否正确
        if ($repay_result['repay_step']!=3){
            return "borrow_repay_step3_error";
        }
		if ($repay_result["repay_status"]!=1){
		  
		
			
			//更新统计信息
			borrowCountClass::UpdateBorrowCount(array("user_id"=>$repay_result["user_id"],"borrow_nid"=>"{$repay_result['borrow_nid']}","nid"=>"borrow_repay_".$repay_result['borrow_nid']."_".$repay_result['id']."_".$repay_result['repay_period'],"borrow_repay_yes_times"=>1,"borrow_repay_wait_times"=>-1,"borrow_repay_yes"=>$repay_result["repay_account"],"borrow_repay_wait"=>-$repay_result["repay_account"],"borrow_repay_interest_yes"=>$repay_result["repay_interest"],"borrow_repay_interest_wait"=>-$repay_result["repay_interest"],"borrow_repay_capital_yes"=>$repay_result["repay_capital"],"borrow_repay_capital_wait"=>-$repay_result["repay_capital"]));	
			if ($repay_result["repay_yestime"]==""){
                $repay_result["repay_yestime"] = time();
			}
			
            if ($repay_result['borrow_type']!="second"){
				$_amount["user_id"] = $repay_result['user_id'];//用户id
				$_amount["amount_type"] = $repay_result["amount_type"];//额度类型
				$_amount["amount_style"] = "forever";
				$_amount["type"] = "borrow_repay";
				$_amount["oprate"] = "return";
				$_amount["account"] = $repay_result['repay_capital'];
				$_amount["nid"] = $_amount["type"]."_".$repay_result['user_id']."_".$repay_result['borrow_nid']."_".$repay_result['id'];
				$_amount["remark"] = "还款成功[{$repay_result["borrow_url"]}]，返回{$repay_result['repay_capital']}元额度";
				borrowAmountClass::AddAmountLog($_amount);
			}
            
            
			$sql = "update `{borrow_repay}` set repay_fee='{$repay_result['repay_account_fee']}',repay_status=1,repay_type='yes' where id='{$repay_result["id"]}'";
			$mysql->db_query($sql);
            
            //还款次数
            $sql = "select count(1) as num,sum(repay_account) as yes_repay_account,sum(repay_capital) as yes_repay_capital,sum(repay_interest) as yes_repay_interest  from `{borrow_repay}` where borrow_nid='{$repay_result["borrow_nid"]}' and repay_status=1";
            $num_result = $mysql->db_fetch_array($sql);
            if ($num_result==""){
                $repay_times = 0;
            }else{
                $repay_times = $num_result["num"];
            }
            
            //正常还款费用
            $repay_fee_normal = 0;
            $sql = "select sum(repay_fee) as num  from `{borrow_repay}` where borrow_nid='{$repay_result["borrow_nid"]}' and repay_status=1 and repay_type='yes'";
            $fee_result = $mysql->db_fetch_array($sql);
            if ($fee_result!=false){
                $repay_fee_normal = $fee_result["num"];
            }
            $sql = "select * from `{borrow_repay}` where borrow_nid='{$repay_result["borrow_nid"]}' and repay_status=0 order by repay_period asc";
            $_result = $mysql->db_fetch_array($sql);
            if ($_result!=false){
                $repay_next_account = $_result["repay_account"];
                $repay_next_time = $_result["repay_time"];
                $repay_full_status=0;
            }else{
                $repay_next_account ="";
                $repay_next_time = "";
                $repay_full_status=1;
            }
            
            //更新投资的人的状态是否已经还完
            $sql = "update `{borrow_tender}` set recover_full_status='{$repay_full_status}' where borrow_nid='{$repay_result["borrow_nid"]}'";
            $mysql->db_query($sql);  
            
            
            $sql = "update `{borrow}` set repay_fee_normal='{$repay_fee_normal}',repay_full_status='{$repay_full_status}',repay_account_yes='{$num_result["yes_repay_account"]}',repay_account_wait=repay_account_all-repay_account_yes,repay_account_capital_yes='{$num_result["yes_repay_capital"]}',repay_account_capital_wait=repay_account_capital-repay_account_capital_yes,repay_account_interest_yes='{$num_result["yes_repay_interest"]}',repay_account_interest_wait=repay_account_interest-repay_account_interest_yes,repay_times='{$repay_times}',repay_next_account='{$repay_next_account}',repay_next_time='{$repay_next_time}' where borrow_nid='{$repay_result["borrow_nid"]}'";
            $mysql->db_query($sql);	//添加最后的还款金额
            
            //判断有加入流转标不
            if (file_exists(DEAYOU_PATH."modules/borrow/borrow.roam.php")){
                if ($repay_result["borrow_type"]=="roam"){
                    $sql = "select sum(repay_capital) as num from `{borrow_repay}` where borrow_nid = '{$repay_result['borrow_nid']}' and repay_status=1";
                    $_recover_result = $mysql->db_fetch_array($sql);
                    if ($_recover_result==false){
                        $_recover_yes = 0;
                    }else{
                        $sql = "select account_min from `{borrow_roam}`  where borrow_nid = '{$repay_result['borrow_nid']}'";
                        $roam_result = $mysql->db_fetch_array($sql);
                        $_recover_yes = $_recover_result['num']/$roam_result['account_min'];
                    }
                    $sql = "update `{borrow_roam}` set recover_yes={$_recover_yes},recover_wait=portion_total-recover_yes where borrow_nid = '{$repay_result['borrow_nid']}'";
                    $mysql->db_query($sql);
                }
            }
            
		}
        $sql = "update `{borrow_repay}` set repay_step=4 where id='{$repay_result['id']}'";
        $mysql->db_query($sql);
        
		$sql="update {borrow_change} set status=0 where tender_id in (select id from {borrow_tender} where borrow_nid={$repay_result['borrow_nid']}) and status=2";
		$mysql->db_query($sql);
       
        return array("result"=>0,"step"=>0,"key"=>0,"name"=>"还款成功");
    }
    
}    
    
    
?>
