<?
/******************************
 * $File: borrow.reverify.php
 * $Description: 借款类文件
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
require_once(ROOT_PATH."modules/users/users.class.php");
require_once(ROOT_PATH."modules/users/users.friends.php");

$MsgInfo["borrow_status_error"] = "审核状态不正确，如果原来是通过，则必须还是通过";

class borrowReverifyClass
{
    
    
    function ReverifyInfo($data){
        global $mysql,$_G;
        if (IsExiest($data["borrow_nid"])=="") return "borrow_nid_empty";
		//读取借款标的相关信息
	    //$sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$data['borrow_nid']}'";
	//	$borrow_result = $mysql->db_fetch_array($sql);
        $borrow_result = borrowClass::GetView(array("borrow_nid"=>$data["borrow_nid"]));
		$borrow_result['borrow_url'] = "<a href={$_G['web_domain']}/invest/a{$borrow_result['borrow_nid']}.html target=_blank >{$borrow_result['name']}</a>";//借款标地址
        if ($borrow_result['borrow_full_status']==1){
		  return "borrow_fullcheck_yes";
		}elseif ($borrow_result['borrow_part_status']!=1 && $borrow_result['borrow_account_yes']!=$borrow_result['account']  && $borrow_result["type_part_status"]!=1){
			return "borrow_not_full";
        }elseif (($borrow_result['borrow_part_status']==1 || $borrow_result["type_part_status"]==1) && $borrow_result['borrow_account_yes']==0){
			return "borrow_part_not_account";
            
		}else{
		      //部分借款
		    if ($borrow_result["type_part_status"]==1){
		       $borrow_result["account"] = $borrow_result["borrow_account_yes"];
		    }
            //判断是否是vip
            $borrow_result['vip_status'] =0;
		    $vip_result = usersvipClass::GetUsersVip(array("user_id"=>$borrow_result['user_id']));
            $borrow_result['vip_status'] = $vip_result["status"];
            
            //第一步做相关的判断
            if ($data['step']==0){
                //判断是否可以重新审核
                if ($borrow_result['reverify_status']>0 && $borrow_result['reverify_status']!=$data['status']){
                    return "borrow_status_error";
                }
                echo "正在复审中，请不要关闭浏览器";
        		//更新满标时的操作人
        		$sql = " update `{borrow}` set reverify_userid='{$data['reverify_userid']}',reverify_remark='{$data['remark']}',reverify_contents='{$data['contents']}',reverify_time='".time()."',reverify_status='{$data['status']}' where borrow_nid='{$data['borrow_nid']}'";
                $mysql ->db_query($sql);
                
        		//加入审核记录
                $_verify['user_id'] = $_G['user_id'];
                $_verify['status'] = $data['status'];
                $_verify['borrow_nid'] = $data['borrow_nid'];
                $_verify['type'] = "reverify";
                $_verify['remark'] = $data['remark'];
                $_verify['contents'] = $data['contents'];
                borrowLoanClass::AddVerify($_verify);
                return array("result"=>1,"step"=>1,"key"=>"");	
            }else{
                $fun = "ReverifyStep".$data['step'];
                $borrow_result["key"] = $data['key'];
    		    $result = self::$fun($borrow_result);
                return $result;
            }
		}
        //基本的资料
    }
	
	
	//第一步，更新审核人的信息
	function ReverifyStep1($borrow_result){
		global $mysql,$_G;
        if($borrow_result=="") return "";
        $status = $borrow_result['reverify_status'];
        //成功借款
		if ($status == 3){
			//如果成功，则将还款信息输进表里面去
            echo "正在为借款人操作相关的金额，请不要关闭浏览器";
			$_equal["account"] = $borrow_result["account"];
			$_equal["period"] = $borrow_result["borrow_period"];
			$_equal["apr"] = $borrow_result["borrow_apr"];
			$_equal["style"] = $borrow_result["borrow_style"];
			$_equal["borrow_type"] = $borrow_result["borrow_type"];
			$equal_result = borrowCalculateClass::GetType($_equal);
			foreach ($equal_result as $key => $value){
			     $key = $key+1;
				//防止重复添加还款信息
				$sql = "select 1 from `{borrow_repay}` where user_id='{$borrow_result["user_id"]}' and repay_period='{$key}' and borrow_nid='{$borrow_result["borrow_nid"]}'";
				$result = $mysql->db_fetch_array($sql);
				if ($result==false){
					$sql = "insert into `{borrow_repay}` set `addtime` = '".time()."',";
					$sql .= "`addip` = '".ip_address()."',repay_type='wait',user_id='{$borrow_result["user_id"]}',status=1,`borrow_nid`='{$borrow_result["borrow_nid"]}',`repay_period`='{$key}',";
					$sql .= "`repay_time`='{$value['repay_time']}',`repay_account`='{$value['account_all']}',";
					$sql .= "`repay_interest`='{$value['account_interest']}',`repay_capital`='{$value['account_capital']}'";
					$mysql ->db_query($sql);
				}else{
					$sql = "update `{borrow_repay}` set `addtime` = '".time()."',";
					$sql .= "`addip` = '".ip_address()."',user_id='{$borrow_result["user_id"]}',status=1,`borrow_nid`='{$borrow_result["borrow_nid"]}',`repay_period`='{$key}',";
					$sql .= "`repay_time`='{$value['repay_time']}',`repay_account`='{$value['account_all']}',";
					$sql .= "`repay_interest`='{$value['account_interest']}',`repay_capital`='{$value['account_capital']}'";
					$sql .= " where user_id='{$borrow_result["user_id"]}' and repay_period='{$key}' and borrow_nid='{$borrow_result["borrow_nid"]}'";
					$mysql ->db_query($sql);
				}
			}
            //本金和利息
            $repay_times = count($equal_result);
			$_equal["type"] = "all";
			$equal_result = borrowCalculateClass::GetType($_equal);
			$repay_all = $equal_result['account_total'];
            
            //借款者总金额增加。
			$log_info["user_id"] = $borrow_result["user_id"];//操作用户id
			$log_info["nid"] = "borrow_success_".$borrow_result["borrow_nid"]."_".$borrow_result["user_id"];//订单号
            $log_info["borrow_nid"] = $borrow_result['borrow_nid'];//收入
            $log_info["account_web_status"] = 0;//
            $log_info["account_user_status"] = 1;//
			$log_info["code"] = "borrow";//
			$log_info["code_type"] = "success";//
			$log_info["code_nid"] = $borrow_result['borrow_nid'];//
			$log_info["money"] = $borrow_result["account"];//操作金额
			$log_info["income"] = $log_info["money"];//收入
			$log_info["expend"] = 0;//支出
			$log_info["balance_cash"] = $log_info["money"];//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = 0;//待收金额
            $log_info["repay"] = $repay_all;//待还金额
			$log_info["type"] = "borrow_success";//类型
			$log_info["to_userid"] = 0;//
			$log_info["remark"] =  "通过[{$borrow_result["borrow_url"]}]借到的款";
			accountClass::AddLog($log_info);
            
            //冻结金额。
			$sql = "select frost_scale,frost_scale_vip from `{borrow_type}` where nid='{$borrow_result["borrow_type"]}'";
            $borrow_type_result = $mysql->db_fetch_array($sql);
            $frost_account = 0;
            if ($borrow_result["vip_status"]==1){
             $frost_account = round(($borrow_result["account"]*$borrow_type_result["frost_scale_vip"])/100,2);
            }else{
                $frost_account = round(($borrow_result["account"]*$borrow_type_result["frost_scale"])/100,2);
            }
			if ($frost_account>0){
				$log_info["user_id"] = $borrow_result["user_id"];//操作用户id
				$log_info["nid"] = "borrow_success_frost_".$borrow_result["borrow_nid"]."_".$borrow_result["user_id"];//订单号
				$log_info["borrow_nid"] = $borrow_result['borrow_nid'];//收入
                $log_info["account_web_status"] = 0;//
                $log_info["account_user_status"] = 0;//
    			$log_info["code"] = "borrow";//
    			$log_info["code_type"] = "success_frost";//
    			$log_info["code_nid"] = $borrow_result['borrow_nid'];//
    			$log_info["money"] = $frost_account;//操作金额
				$log_info["income"] = 0;//收入
				$log_info["expend"] = 0;//支出
				$log_info["balance_cash"] = -$log_info["money"];//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
				$log_info["frost"] =$log_info["money"];//冻结金额
				$log_info["await"] = 0;//待收金额
				$log_info["repay"] = 0;//待还金额
				$log_info["type"] = "borrow_success_frost";//类型
				$log_info["to_userid"] = 0;//付给谁
				$log_info["remark"] =  "冻结借款标[{$borrow_result["borrow_url"]}]{$frost_account}元的保证金";
				accountClass::AddLog($log_info);
				$sql = "update `{borrow}` set borrow_frost_account='{$frost_account}' where borrow_nid='{$borrow_result["borrow_nid"]}'";
				$mysql->db_query($sql);
			}
			
			if ($borrow_result['borrow_type']=="second"){
				$_equal["account"] = $borrow_result["account"];
				$_equal["period"] = $borrow_result["borrow_period"];
				$_equal["apr"] = $borrow_result["borrow_apr"];
				$_equal["style"] = $borrow_result["borrow_style"];
				$_equal["borrow_type"] = $borrow_result["borrow_type"];
				$equal_result = borrowCalculateClass::GetType($_equal);
				$money = $equal_result['interest_total'];
				
				$log_info["user_id"] = $borrow_result['user_id']; //操作用户id
				$log_info["nid"] = "borrow_miao_repay_" . $borrow_result['borrow_nid']; //订单号
                $log_info["account_web_status"] = 0;//
                $log_info["account_user_status"] = 0;//
				$log_info["money"] = $money; //操作金额
				$log_info["income"] = $money; //收入
				$log_info["expend"] = 0; //支出
				$log_info["balance_cash"] = $money; //可提现金额
				$log_info["balance_frost"] = 0; //不可提现金额
				$log_info["frost"] = -$money; //冻结金额
				$log_info["await"] = 0; //待收金额
				$log_info["repay"] = 0; //待收金额
				$log_info["type"] = "borrow_miao_repay"; //类型
				$log_info["to_userid"] = 0; //付给谁
				$log_info["remark"] = "秒标解冻{$money}元";
				accountClass::AddLog($log_info);
            }
			
            //扣除费用
            $credit_result = borrowClass::GetBorrowCredit(array("user_id"=>$borrow_result["user_id"]));
            $_fee["vip_status"] = $borrow_result["vip_status"];//判断是不是vip
            $_fee["credit_fee"] =$credit_result['credit']['fee'];//判断是不是vip
            $_fee["borrow_type"] = $borrow_result["borrow_type"];//借款类型
            $_fee["borrow_style"] = $borrow_result["borrow_style"];//还款方式
            $_fee["period"] = $borrow_result["borrow_period"];//还款方式
            $_fee["type"] = "borrow_success";//属于借款者还是投资人
            $_fee["user_type"] = "borrow";//属于借款者还是投资人
            $_fee["capital"] = $borrow_result["account"];//属于借款者还是投资人
            $_fee["interest"] = $repay_all-$_fee["capital"];//属于借款者还是投资人
            $result = borrowFeeClass::GetFeeValue($_fee);
            if ($result != false){
                foreach ($result as $key => $value){
                    $log_info["user_id"] = $borrow_result["user_id"];//操作用户id
    				$log_info["nid"] = "borrow_success_fee_".$value["nid"]."_".$borrow_result["borrow_nid"]."_".$borrow_result["user_id"];//订单号
    				$log_info["borrow_nid"] = $borrow_result['borrow_nid'];//收入
                    $log_info["account_web_status"] = 1;//
                    $log_info["account_user_status"] = 1;//
        			$log_info["code"] = "borrow";//
        			$log_info["code_type"] = "borrow_fee_".$value["nid"];//
        			$log_info["code_nid"] = $borrow_result['borrow_nid'];//
        			$log_info["money"] = $value['account'];//操作金额
    				$log_info["income"] = 0;//收入
    				$log_info["expend"] = $log_info["money"];//支出
    				$log_info["balance_cash"] = -$log_info["money"];//可提现金额
    				$log_info["balance_frost"] = 0;//不可提现金额
    				$log_info["frost"] = 0;//冻结金额
    				$log_info["await"] = 0;//待收金额
    				$log_info["repay"] = 0;//待还金额
    				$log_info["type"] = "borrow_fee_".$value["nid"];//类型
    				$log_info["to_userid"] = 0;//付给谁
    				$log_info["remark"] =  "借款成功，扣除[{$borrow_result["borrow_url"]}]{$log_info["money"]}元{$value['name']}";
    				accountClass::AddLog($log_info);
                }
            }
            
            //更新统计信息
			borrowCountClass::UpdateBorrowCount(array("user_id"=>$borrow_result['user_id'],"borrow_nid"=>"{$borrow_result['borrow_nid']}","nid"=>"borrow_success_".$borrow_result['borrow_nid'],"borrow_success_times"=>1,"borrow_repay_times"=>$repay_times,"borrow_repay_wait_times"=>$repay_times,"borrow_account"=>$borrow_result["account"],"borrow_repay_account"=>$repay_all,"borrow_repay_wait"=>$repay_all,"borrow_repay_interest"=>$equal_result['interest_total'],"borrow_repay_interest_wait"=>$equal_result['interest_total'],"borrow_repay_capital"=>$equal_result['capital_total'],"borrow_repay_capital_wait"=>$equal_result['capital_total']));
            
			//借款标发布者接收
			$remind['nid'] = "borrow_full_success";
            $remind['remind_nid'] =  "borrow_full_success_".$borrow_result["borrow_nid"]."_".$borrow_result["user_id"];
			$remind['receive_userid'] = $borrow_result['user_id'];
			$remind['article_id'] = $borrow_result['borrow_nid'];
			$remind['code'] = "borrow";
			$remind['title'] = "借款标[{$borrow_result["name"]}]满标审核成功";
			$remind['content'] = "你发布的借款标[{$borrow_result["borrow_url"]}]在".date("Y-m-d",time())."满标审核通过";
			remindClass::sendRemind($remind);
        }else{
            if ($borrow_result["borrow_frost_second"]>0){
                $log_info["user_id"] = $borrow_result['user_id']; //操作用户id
                $log_info["nid"] = "borrow_miao_false_" . $borrow_result['borrow_nid']; //订单号
                $log_info["account_web_status"] = 0;//
                $log_info["account_user_status"] = 1;//
                $log_info["money"] = $borrow_result["borrow_frost_second"]; //操作金额
                $log_info["income"] = 0; //收入
                $log_info["expend"] = 0; //支出
                $log_info["balance_cash"] = $log_info["money"]; //可提现金额
                $log_info["balance_frost"] = 0; //不可提现金额
                $log_info["frost"] = -$log_info["money"]; //冻结金额
                $log_info["await"] = 0; //待收金额
                $log_info["repay"] = 0; //待收金额
                $log_info["type"] = "borrow_miao_false"; //类型
                $log_info["to_userid"] = 0; //付给谁
                $log_info["remark"] = "秒标审核失败，解冻{$log_info["money"]}元";
                accountClass::AddLog($log_info);
            }
            
            //额度返回
               //额度冻结
        	$_amount["user_id"] = $borrow_result['user_id'];//用户id
        	$_amount["amount_type"] = $borrow_result["amount_type"];//额度类型
        	$_amount["amount_style"] = "forever";
        	$_amount["type"] = "borrow_cancel";
        	$_amount["oprate"] = "return";
            $_amount["account"] = $borrow_result['amount_account'];
        	$_amount["nid"] = $_amount["type"]."_".$borrow_result['user_id']."_".$borrow_result['borrow_nid'];
        	$_amount["remark"] = "借款标[{$borrow_result["borrow_url"]}]复审失败，返回{$borrow_result['account']}元额度";
            borrowAmountClass::AddAmountLog($_amount);	
           
            //借款标发布者接收
			$remind['nid'] = "borrow_full_false";
            $remind['remind_nid'] =  "borrow_full_false_".$borrow_result["borrow_nid"]."_".$borrow_result["user_id"];
			$remind['receive_userid'] = $borrow_result['user_id'];
			$remind['article_id'] = $borrow_result['borrow_nid'];
			$remind['code'] = "borrow";
			$remind['title'] = "借款标[{$borrow_result["name"]}]满标审核失败";
			$remind['content'] = "你发布的借款标[{$borrow_result["borrow_url"]}]在".date("Y-m-d",time())."满标审核失败，失败原因：【{$borrow_result["reverify_remark"]}】";
			remindClass::sendRemind($remind);
        }
        return array("result"=>1,"step"=>2,"key"=>0);
	}
	
    
    //操作投资人的信息
	function ReverifyStep2($borrow_result){
		global $mysql,$_G;
        if($borrow_result=="") return "";
        $status = $borrow_result['reverify_status'];
        //成功借款
        $sql = "select * from `{borrow_tender}` as p1 where  p1.borrow_nid='{$borrow_result['borrow_nid']}' limit {$borrow_result['key']},1";
		$tender_result = $mysql->db_fetch_array($sql);
		if ($tender_result==false){
			return array("result"=>1,"step"=>3,"key"=>0);
		}
        
		$tender_userid = $tender_result['user_id'];
		$tender_account = $tender_result['account'];
		$tender_id = $tender_result['id'];
		$borrow_nid = $borrow_result["borrow_nid"];
		$borrow_userid = $borrow_result["user_id"];
		$borrow_url = $borrow_result["borrow_url"];
		$borrow_name = $borrow_result["name"];
		if ($status == 3){
			//如果成功，则将还款信息输进表里面去
            echo "正在为投资人操作相关的金额，请不要关闭浏览器";
           //添加投资的收款纪录
			$_equal["account"] = $tender_result['account'];
			$_equal["period"] = $borrow_result["borrow_period"];
			$_equal["apr"] = $borrow_result["borrow_apr"];
			$_equal["style"] = $borrow_result["borrow_style"];
			$_equal["type"] = "";
			$_equal["borrow_type"] = $borrow_result["borrow_type"];
			$equal_result = borrowCalculateClass::GetType($_equal);
			foreach ($equal_result as $period_key => $value){
                $period_key= $period_key+1;
				$repay_month_account = $value['account_all'];
				//防止重复添加还款信息
				$sql = "select 1 from `{borrow_recover}` where user_id='{$tender_userid}' and borrow_nid='{$borrow_nid}' and recover_period='{$period_key}' and tender_id='{$tender_id}'";
				$result = $mysql->db_fetch_array($sql);
				if ($result==false){
					$sql = "insert into `{borrow_recover}` set recover_type='wait',`addtime` = '".time()."',";
					$sql .= "`addip` = '".ip_address()."',user_id='{$tender_userid}',status=1,`borrow_nid`='{$borrow_nid}',`borrow_userid`='{$borrow_userid}',`tender_id`='{$tender_id}',`recover_period`='{$period_key}',";
					$sql .= "`recover_time`='{$value['repay_time']}',`recover_account`='{$value['account_all']}',";
					$sql .= "`recover_interest`='{$value['account_interest']}',`recover_capital`='{$value['account_capital']}'";
					$mysql ->db_query($sql);
				}else{
					$sql = "update `{borrow_recover}` set `addtime` = '".time()."',";
					$sql .= "`addip` = '".ip_address()."',user_id='{$tender_userid}',status=1,`borrow_nid`='{$borrow_nid}',`borrow_userid`='{$borrow_userid}',`tender_id`='{$tender_id}',`recover_period`='{$period_key}',";
					$sql .= "`recover_time`='{$value['repay_time']}',`recover_account`='{$value['account_all']}',";
					$sql .= "`recover_interest`='{$value['account_interest']}',`recover_capital`='{$value['account_capital']}'";
					$sql .= " where user_id='{$tender_userid}' and recover_period='{$period_key}' and borrow_nid='{$borrow_nid}' and tender_id='{$tender_id}'";
					$mysql ->db_query($sql);
				}
			}
			$tender_nid = "tender_success_".$borrow_nid."_".$tender_userid."_".$tender_id;		
			//第六步,扣除投资人的资金
			$log_info["user_id"] = $tender_userid;//操作用户id
			$log_info["nid"] = $tender_nid;//订单号
            $log_info["account_web_status"] = 0;//
            $log_info["account_user_status"] = 1;//
            $log_info["borrow_nid"] = $borrow_nid;//收入
			$log_info["code"] = "borrow";//
			$log_info["code_type"] = "tender_success";//
			$log_info["code_nid"] = $tender_id;//
			$log_info["money"] = $tender_account;//操作金额
			$log_info["income"] = 0;//收入
			$log_info["expend"] = $tender_account;//支出
			$log_info["balance_cash"] = 0;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = -$tender_account;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["repay"] = 0;//待还金额
			$log_info["type"] = "tender_success";//类型
			$log_info["to_userid"] = $borrow_userid;//付给谁
			$log_info["remark"] = "投标[{$borrow_url}]成功投资金额扣除";
			accountClass::AddLog($log_info);
				
				
			//第五步,更新投资标的信息
			$_equal["type"] = "all";
			$_equal["borrow_type"] = $borrow_result["borrow_type"];
			$equal_result = borrowCalculateClass::GetType($_equal);
			$recover_all = $equal_result['account_total'];
			$recover_interest_all = $equal_result['interest_total'];
			$recover_capital_all = $equal_result['capital_total'];
			$recover_times = count($equal_result);
			//添加待收的金额
			$log_info["user_id"] = $tender_userid;//操作用户id
			$log_info["nid"] = "tender_success_frost_".$borrow_nid."_".$tender_userid."_".$tender_id;//订单号
            $log_info["borrow_nid"] = $borrow_nid;//收入
            $log_info["account_web_status"] = 0;//
            $log_info["account_user_status"] = 0;//
			$log_info["code"] = "borrow";//
			$log_info["code_type"] = "tender_success_frost";//
			$log_info["code_nid"] = $tender_id;//
			$log_info["money"] = $recover_all;//操作金额
			$log_info["income"] = 0;//收入
			$log_info["expend"] = 0;//支出
			$log_info["balance_cash"] = 0;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = $recover_all;//待收金额
			$log_info["repay"] = 0;//待还金额
			$log_info["type"] = "tender_success_frost";//类型
			$log_info["to_userid"] = $borrow_userid;//付给谁
			$log_info["remark"] =  "投标[{$borrow_url}]成功待收金额增加";
			accountClass::AddLog($log_info);
			
            
            //奖励信息
            $money = 0;
            //投标奖励扣除和增加。
			if ($borrow_result['award_status']==1 && $borrow_result['award_account']>0){
				$money = round(($tender_account/$borrow_result["account"])*$borrow_result['award_account'],2);
			}elseif ($borrow_result['award_status']==2){
				$money = round((($borrow_result['award_scale']/100)*$tender_account),2);
			}
			if ($money>0){
    			$log_info["user_id"] = $tender_userid;//操作用户id
    			$log_info["nid"] = "tender_award_add_".$borrow_nid."_".$tender_userid."_".$tender_id;//订单号
                $log_info["account_web_status"] = 0;//
                $log_info["account_user_status"] = 1;//
                $log_info["borrow_nid"] = $borrow_nid;//收入
    			$log_info["code"] = "borrow";//
    			$log_info["code_type"] = "tender_award_add";//
    			$log_info["code_nid"] = $tender_id;//
    			$log_info["money"] = $money;//操作金额
    			$log_info["income"] = $money;//收入
    			$log_info["expend"] = 0;//支出
    			$log_info["balance_cash"] = $money;//可提现金额
    			$log_info["balance_frost"] = 0;//不可提现金额
    			$log_info["frost"] = 0;//冻结金额
    			$log_info["await"] = 0;//待收金额
    			$log_info["type"] = "tender_award_add";//类型
    			$log_info["to_userid"] = $borrow_userid;//付给谁
    			$log_info["remark"] =  "借款[{$borrow_url}]的借款奖励";
    			accountClass::AddLog($log_info);
    		
    			$log_info["user_id"] = $borrow_userid;//操作用户id
    			$log_info["nid"] = "borrow_award_lower_".$borrow_nid."_".$borrow_userid."_".$tender_id;//订单号
                $log_info["borrow_nid"] = $borrow_nid;//收入
                $log_info["account_web_status"] = 0;//
                $log_info["account_user_status"] = 1;//
    			$log_info["code"] = "borrow";//
    			$log_info["code_type"] = "borrow_award_lower";//
    			$log_info["code_nid"] = $tender_id;//
    			$log_info["money"] = $money;//操作金额
    			$log_info["income"] = 0;//收入
    			$log_info["expend"] = $money;//支出
    			$log_info["balance_cash"] = -$money;//可提现金额
    			$log_info["balance_frost"] = 0;//不可提现金额
    			$log_info["frost"] = 0;//冻结金额
    			$log_info["await"] = 0;//待收金额
    			$log_info["repay"] = 0;//待还金额
    			$log_info["type"] = "borrow_award_lower";//类型
    			$log_info["to_userid"] = $tender_userid;//付给谁
    			$log_info["remark"] =  "扣除借款[{$borrow_url}]的奖励";
    			accountClass::AddLog($log_info);    
                                
                $sql = "update `{borrow_tender}` set tender_award_fee='{$money}' where id='{$tender_id}'";
                $mysql->db_query($sql);
            }        
                
			$borrow_username = $borrow_result['username'];
			
			//推广奖励 $tender_account $borrow_result["borrow_period"] $borrow_result["borrow_apr"]   $recover_interest_all $tender_userid
			
			$_result=usersFriendsClass::GetUsersInviteOne(array("user_id"=>$tender_userid));			
			$invite = isset($_G["system"]["con_invite_tender_award"])?$_G["system"]["con_invite_tender_award"]:2;
			$award_account = round($invite*$recover_interest_all/100,2);
			if ($_result['user_id']>0 ){
				$log_info["user_id"] = $_result['user_id'];//操作用户id
    			$log_info["nid"] = "invite_award_add_".$borrow_nid."_".$tender_userid."_".$tender_id;//订单号
                $log_info["account_web_status"] = 1;//
                $log_info["account_user_status"] = 1;//
                $log_info["borrow_nid"] = $borrow_nid;//收入
    			$log_info["code"] = "tender";//
    			$log_info["code_type"] = "invite_tender_award";//
    			$log_info["code_nid"] = $tender_id;//
    			$log_info["money"] = $award_account;//操作金额
    			$log_info["income"] = $award_account;//收入
    			$log_info["expend"] = 0;//支出
    			$log_info["balance_cash"] = $award_account;//可提现金额
    			$log_info["balance_frost"] = 0;//不可提现金额
    			$log_info["frost"] = 0;//冻结金额
    			$log_info["await"] = 0;//待收金额
    			$log_info["type"] = "invite_tender_award";//类型
    			$log_info["to_userid"] = $_result['user_id'];//付给谁
    			$log_info["remark"] =  "用户投资借款[{$borrow_url}]获得的推广奖励";
    			accountClass::AddLog($log_info);
				
				$_invite['user_id']=$_result['user_id'];
				$_invite['tender_userid']=$tender_userid;
				$_invite['tender_account']=$tender_account;
				$_invite['tender_period']=$borrow_result["borrow_period"];
				$_invite['tender_apr']=$borrow_result["borrow_apr"];
				$_invite['award']=$award_account;
				usersClass::AddManageAccount($_invite);	
			}
			
			
			//投资信用积分
			$credit_log['user_id'] = $tender_userid;
			$credit_log['nid'] = "tender_success";
			$credit_log['code'] = "borrow";
			$credit_log['type'] = "成功投资{$tender_account}所得的积分";
			$credit_log['addtime'] = time();
			$credit_log['article_id'] =$tender_id;
			$credit_log['value'] = round($tender_account/100);
			$result = creditClass::ActionCreditLog($credit_log);
				
			//加入用户操作记录
			$user_log["user_id"] = $tender_userid;
			$user_log["code"] = "tender";
			$user_log["type"] = "tender_success";
			$user_log["operating"] = "tender";
			$user_log["article_id"] = $tender_userid;
			$user_log["result"] = 1;
			$user_log["content"] = "借款标[{$borrow_url}]通过了复审,[<a href=/protocol/a{$data['borrow_nid']}.html target=_blank>点击此处</a>]查看协议书";
			usersClass::AddUsersLog($user_log);	
				
				
			$sql = "update `{borrow_tender}` set status=1,tender_status=1,recover_account_all='{$equal_result['account_total']}',recover_account_interest='{$equal_result['interest_total']}',recover_account_wait=recover_account_all-recover_account_yes,recover_account_interest_wait=recover_account_interest-recover_account_interest_yes,recover_account_capital_wait='{$equal_result['capital_total']}'  where id='{$tender_id}'";
			$mysql->db_query($sql);
			
			//更新统计信息
			borrowCountClass::UpdateBorrowCount(array("user_id"=>$tender_userid,"borrow_nid"=>"{$borrow_result['borrow_nid']}","nid"=>$tender_nid,"tender_success_times"=>1,"tender_success_account"=>$tender_account,"tender_frost_account"=>-$tender_account,"tender_recover_account"=>$recover_all,"tender_recover_wait"=>$recover_all,"tender_capital_account"=>$recover_capital_all,"tender_capital_wait"=>$recover_capital_all,"tender_interest_account"=>$recover_interest_all,"tender_interest_wait"=>$recover_interest_all,"tender_recover_times"=>$recover_times,"tender_recover_times_wait"=>$recover_times));
				
            
			//投资者接收
			$remind['nid'] = "tender_success";
			$remind['remind_nid'] = $tender_nid;
			$remind['receive_userid'] = $tender_userid;
			$remind['article_id'] = $borrow_nid;
			$remind['code'] = "borrow";
			$remind['title'] = "投资({$borrow_username})[{$borrow_name}]满标审核成功";
			$remind['content'] = "您所投资的标[{$borrow_url}]在".date("Y-m-d",time())."已经审核通过";
			remindClass::sendRemind($remind);
				
        }elseif ($status == 4){
            	//更新投资的状态
			$sql = "update `{borrow_tender}` set status=2 where id={$tender_id}";
			$mysql->db_query($sql);
			
			//返回投资资金
			$log_info["user_id"] = $tender_userid;//操作用户id
			$log_info["nid"] = "tender_false_".$borrow_nid."_".$tender_userid."_".$tender_id;//订单号
            $log_info["borrow_nid"] = $borrow_nid;//收入
            $log_info["account_web_status"] = 0;//
            $log_info["account_user_status"] = 0;//
			$log_info["code"] = "borrow";//
			$log_info["code_type"] = "tender_false";//
			$log_info["code_nid"] = $tender_id;//
			$log_info["money"] = $tender_account;//操作金额
			$log_info["income"] = $tender_account;//收入
			$log_info["expend"] = 0;//支出
			$log_info["balance_cash"] = $tender_account;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = -$tender_account;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["repay"] = 0;//待还金额
			$log_info["type"] = "tender_false";//类型
			$log_info["to_userid"] = $borrow_userid;//付给谁
			$log_info["remark"] =  "投标[{$borrow_url}]失败返回的投标额";
			accountClass::AddLog($log_info);
			
        
            //奖励信息
            if($borrow_result["award_false"]==1){
                $money = 0;
                //投标奖励扣除和增加。
    			if ($borrow_result['award_status']==1 && $borrow_result['award_account']>0){
    				$money = round(($tender_account/$borrow_result["account"])*$borrow_result['award_account'],2);
    			}elseif ($borrow_result['award_status']==2){
    				$money = round((($borrow_result['award_scale']/100)*$tender_account),2);
    			}
    			if ($money>0){
        			$log_info["user_id"] = $tender_userid;//操作用户id
        			$log_info["nid"] = "tender_award_add_".$borrow_nid."_".$tender_userid."_".$tender_id;//订单号
                    $log_info["account_web_status"] = 0;//
                    $log_info["account_user_status"] = 1;//
                    $log_info["borrow_nid"] = $borrow_nid;//收入
        			$log_info["code"] = "borrow";//
        			$log_info["code_type"] = "tender_award_add";//
        			$log_info["code_nid"] = $tender_id;//
        			$log_info["money"] = $money;//操作金额
        			$log_info["income"] = $money;//收入
        			$log_info["expend"] = 0;//支出
        			$log_info["balance_cash"] = $money;//可提现金额
        			$log_info["balance_frost"] = 0;//不可提现金额
        			$log_info["frost"] = 0;//冻结金额
        			$log_info["await"] = 0;//待收金额
        			$log_info["type"] = "tender_award_add";//类型
        			$log_info["to_userid"] = $borrow_userid;//付给谁
        			$log_info["remark"] =  "借款[{$borrow_url}]的借款奖励";
        			accountClass::AddLog($log_info);
        		
        			$log_info["user_id"] = $borrow_userid;//操作用户id
        			$log_info["nid"] = "borrow_award_lower_".$borrow_nid."_".$borrow_userid."_".$tender_id;//订单号
                    $log_info["borrow_nid"] = $borrow_nid;//收入
                    $log_info["account_web_status"] = 0;//
                    $log_info["account_user_status"] = 1;//
        			$log_info["code"] = "borrow";//
        			$log_info["code_type"] = "borrow_award_lower";//
        			$log_info["code_nid"] = $tender_id;//
        			$log_info["money"] = $money;//操作金额
        			$log_info["income"] = 0;//收入
        			$log_info["expend"] = $money;//支出
        			$log_info["balance_cash"] = -$money;//可提现金额
        			$log_info["balance_frost"] = 0;//不可提现金额
        			$log_info["frost"] = 0;//冻结金额
        			$log_info["await"] = 0;//待收金额
        			$log_info["repay"] = 0;//待还金额
        			$log_info["type"] = "borrow_award_lower";//类型
        			$log_info["to_userid"] = $tender_userid;//付给谁
        			$log_info["remark"] =  "扣除借款[{$borrow_url}]的奖励";
        			accountClass::AddLog($log_info);    
                                    
                    $sql = "update `{borrow_tender}` set tender_award_fee='{$money}' where id='{$tender_id}'";
                    $mysql->db_query($sql);
                }     
            }
            
			//投资者接收 
			$remind['nid'] = "tender_false";
			$remind['remind_nid'] = "tender_false_".$borrow_nid."_".$tender_userid."_".$tender_id;	
			$remind['code'] = "borrow";
			$remind['article_id'] = $borrow_nid;
			$remind['receive_userid'] = $tender_userid;
			$remind['title'] = "投资的标[{$borrow_result["name"]}]满标审核失败";
			$remind['content'] = "您所投资的标[{$borrow_url}]在".date("Y-m-d",time())."审核失败,失败原因：{$borrow_result['reverify_remark']}";
			remindClass::sendRemind($remind);
        }
        return array("result"=>1,"step"=>2,"key"=>$borrow_result['key']+1);
   }
   
    //奖励
    function ReverifyStep3($borrow_result){
		global $mysql,$_G;
        if($borrow_result=="") return "";
        $reverify_status = $borrow_result['reverify_status'];
		$borrow_nid = $borrow_result["borrow_nid"];
		$status = $borrow_result['status'];
		$borrow_userid = $borrow_result['user_id'];
		$borrow_account = $borrow_result['account'];
		$borrow_name = $borrow_result['name'];
		$vouch_status = $borrow_result['vouch_status'];
        if ($reverify_status==3){
    		//基本的资料
    	   //第十一步，更新借款标的信息$nowtime = time();
    		$nowtime= time();
            if ($borrow_result["borrow_type"]=="day"){
                $endtime = get_times(array("num"=>$borrow_result["borrow_period"],"time"=>$nowtime,"type"=>"day"));
            }else{
    	       	$endtime = get_times(array("num"=>$borrow_result["borrow_period"],"time"=>$nowtime));
    		}
    		if ($borrow_result["borrow_style"]=="season"){
    			$_each_time = "每三个月后".date("d",$nowtime)."日";
    			$nexttime = get_times(array("num"=>3,"time"=>$nowtime));
    		}else{
    			$_each_time = "每月".date("d",$nowtime)."日";
    			$nexttime = get_times(array("num"=>1,"time"=>$nowtime));
    		}
    		$_equal["account"] = $borrow_result['account'];
    		$_equal["period"] = $borrow_result["borrow_period"];
    		$_equal["style"] = $borrow_result["borrow_style"];
    		$_equal["apr"] = $borrow_result["borrow_apr"];
			$_equal["borrow_type"] = $borrow_result["borrow_type"];
    		$_equal["type"] = "all";
    		$equal_result = borrowCalculateClass::GetType($_equal);;
    		$sql = "update `{borrow}` set borrow_full_status=1,status='{$borrow_result['reverify_status']}',repay_account_all='{$equal_result['account_total']}',repay_account_interest='{$equal_result['interest_total']}',repay_account_capital='{$equal_result['capital_total']}',repay_account_wait='{$equal_result['account_total']}',repay_account_interest_wait='{$equal_result['interest_total']}',repay_account_capital_wait='{$equal_result['capital_total']}',repay_last_time='{$endtime}',repay_next_time='{$nexttime}',borrow_success_time='{$nowtime}',repay_each_time='{$_each_time}',repay_times='{$repay_times}'  where borrow_nid='{$borrow_nid}'";
          
    		$mysql->db_query($sql);
        }else{
            $sql = "update `{borrow}` set borrow_full_status=1,status='{$borrow_result['reverify_status']}'  where borrow_nid='{$borrow_nid}'";
    		$mysql->db_query($sql);
        }
        //秒标自动还款
        if ($borrow_result["borrow_type"]=="second" && $reverify_status==3){
            $sql = "select id,user_id from `{borrow_repay}` where borrow_nid='{$borrow_result['borrow_nid']}'";
            $result = $mysql->db_fetch_array($sql);
            return array("result"=>2,"repay_id"=>$result['id'],"user_id"=>$result['user_id'],"step"=>"0","key"=>"0");
        }
		return array("result"=>0,"step"=>"","key"=>"");
	}
}
?>
