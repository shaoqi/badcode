<?
/******************************
 * $File: borrow.repay.php
 * $Description: 还款文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

require_once(ROOT_PATH."modules/users/users.class.php");
require_once(ROOT_PATH."modules/account/account.class.php");
require_once(ROOT_PATH."modules/remind/remind.class.php");
class borrowrepayClass
{

	
	//第一步，更新审核人的信息
	function RepayStep0($data){
		global $mysql,$_G;
		
		//判断是否可以还款
		if (IsExiest($data['repay_id'])==""){
			return "borrow_repay_id_empty";
		}
		if (IsExiest($data['user_id'])==""){
			return "borrow_user_id_empty";
		}
		if (IsExiest($data['borrow_nid'])==""){
			return "borrow_nid_empty";
		}
		
		
		//第一步，获取还款的信息
		$sql = "select p1.*,p2.username from `{borrow_repay}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.id={$data['repay_id']} and p1.user_id='{$data['user_id']}' and p1.borrow_nid='{$data['borrow_nid']}'";
		$repay_result= $mysql->db_fetch_array($sql);
		if ($repay_result==""){
			return "borrow_repay_id_empty";
		}
		if ($repay_result["user_id"]!=$data["user_id"]){
			return "borrow_user_id_empty";
		}
		if ($repay_result["status"]!=1){
			return "borrow_repay_error";
		}
		if ($repay_result["repay_status"]==1){
			return "borrow_repay_error";
		}
		$repay_id = $repay_result["id"];
		$repay_account = $repay_result["repay_account"];//还款总额
		$repay_period = $repay_result["repay_period"];
		$repay_web = $repay_result["repay_web"];
		$repay_vouch = $repay_result["repay_vouch"];
		$repay_period = $repay_result["repay_period"];
		$repay_account = $repay_result["repay_account"];//还款总额
		$repay_capital = $repay_result["repay_capital"];//还款本金
		$repay_interest = $repay_result["repay_interest"];//还款利息
		$repay_time = $repay_result["repay_time"];//还款时间
		
		//读取借款标的相关信息
		$borrow_nid = $data['borrow_nid'];
		$sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$data['borrow_nid']}'";
		$borrow_result = $mysql->db_fetch_array($sql);
		$status = $borrow_result['status'];
		$borrow_period = $borrow_result['borrow_period'];
		$borrow_frost_account = $borrow_result["borrow_frost_account"];
		$borrow_userid = $borrow_result['user_id'];
		$borrow_account = $borrow_result['account'];
		$borrow_name = $borrow_result['name'];
		$borrow_url = html_entity_decode("<a href=/invest/a{$borrow_nid}.html target=_blank style=color:blue>{$borrow_result['name']}</a>");//借款标地址
		
		//判断上一期是否已还款
		if ($repay_period!=0){
			$_repay_period = $repay_period-1;
			$sql = "select repay_status from `{borrow_repay}` where `repay_period`=$_repay_period and borrow_nid={$borrow_nid}";
			$result = $mysql->db_fetch_array($sql);
			if ($result!=false && $result['repay_status']!=1){
				return "borrow_repay_up_notrepay";
			}
		}
		
		//检查是否逾期,并且计算逾期的费用
		$late = borrowClass::LateInterest(array("time"=>$repay_time,"account"=>$repay_account,"capital"=>$repay_capital));
		$late_days = $late['late_days'];
		$late_interest = round($repay_account/100*0.4*$late_days,2);
		$late_reminder = round($repay_account/100*0.8*$late_days,2);
		$late_account = $late_interest;
		
		
		//判断可用余额是否够还款
		$account_result =  accountClass::GetAccountUsers(array("user_id"=>$borrow_userid));//获取当前用户的余额;
		if ($account_result['balance']<$repay_account+$late_account){
			return "borrow_repay_account_use_none";
		}
		
		$log_info["user_id"] = $borrow_userid;//操作用户id
		$log_info["nid"] = "borrow_repay_".$borrow_userid."_".$borrow_nid."_".$repay_id;//订单号
		$log_info["money"] = $repay_account;//操作金额
		$log_info["income"] = 0;//收入
		$log_info["expend"] = $repay_account;//支出
		$log_info["balance_cash"] = 0;//可提现金额
		$log_info["balance_frost"] = -$repay_account;//不可提现金额
		$log_info["frost"] = 0;//冻结金额
		$log_info["await"] = 0;//待收金额
		$log_info["type"] = "borrow_repay";//类型
		$log_info["to_userid"] = 0;//付给谁
		$log_info["remark"] = "对[{$borrow_url}]借款标第".($repay_period+1)."期还款";
		accountClass::AddLog($log_info);
		
		$user_log["user_id"] = $borrow_userid;
		$user_log["code"] = "borrow";
		$user_log["type"] = "repay_success";
		$user_log["operating"] = "repay";
		$user_log["article_id"] = $borrow_userid;
		$user_log["result"] = 1;
		$user_log["content"] = "对借款标[{$borrow_url}]进行还款";
		usersClass::AddUsersLog($user_log);	
		
		if ($repay_period+1 == $borrow_period){
			if ($borrow_frost_account>0){
				//第八步，最后一起解除冻结的金额
				$log_info["user_id"] = $borrow_userid;//操作用户id
				$log_info["nid"] = "borrow_frost_repay_".$borrow_userid."_".$borrow_nid.$borrow_period;//订单号
				$log_info["money"] = $borrow_frost_account;//操作金额
				$log_info["income"] =0;//收入
				$log_info["expend"] = 0;//支出
				$log_info["balance_cash"] = $borrow_frost_account;//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
				$log_info["frost"] = -$borrow_frost_account;//冻结金额
				$log_info["await"] = 0;//待收金额
				$log_info["type"] = "borrow_frost_repay";//类型
				$log_info["to_userid"] = 0;//付给谁
				$log_info["remark"] = "对[{$borrow_url}]借款的解冻";
				accountClass::AddLog($log_info);
			}
			//借款保证金		 wdf 20121115
			$sql = "select account from `{borrow}` as p1  where  p1.user_id='{$data['user_id']}' and p1.borrow_nid='{$data['borrow_nid']}'";
			$_result = $mysql->db_fetch_array($sql);	
			
			
			$borrow_margin=isset($_G['system']['con_borrow_margin_fee'])?$_G['system']['con_borrow_margin_fee']:0.003;
			$borrow_margin_fee = $_result['account']*$borrow_margin/100;
			
			$log_info["user_id"] = $borrow_userid;//操作用户id
			$log_info["nid"] = "borrow_margin_fee_return_".$borrow_userid."_".$borrow_nid;//订单号
			$log_info["money"] = $borrow_margin_fee;//操作金额
			$log_info["income"] = $borrow_margin_fee;//收入
			$log_info["expend"] = 0;//支出
			$log_info["balance_cash"] = $borrow_margin_fee;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = -$borrow_margin_fee;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["type"] = "borrow_margin_return_fee";//类型
			$log_info["to_userid"] = 0;//付给谁
			$log_info["remark"] = "用户对[$borrow_url]还款成功,解冻{$borrow_margin_fee}元保证金";
			accountClass::AddLog($log_info);
	
		}
		
		if ($late_interest>0){
			$log_info["user_id"] = $borrow_userid;//操作用户id
			$log_info["nid"] = "borrow_repay_late_".$borrow_userid."_".$borrow_nid."_".$repay_id;//订单号
			$log_info["money"] = $late_interest;//操作金额
			$log_info["income"] = 0;//收入
			$log_info["expend"] = $late_interest;//支出
			$log_info["balance_cash"] = -$late_interest;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["type"] = "borrow_repay_late";//类型
			$log_info["to_userid"] = 0;//付给谁
			$log_info["remark"] = "对[{$borrow_url}]借款第".($repay_period+1)."期的逾期金额的扣除";
			accountClass::AddLog($log_info);
			
			//滞纳金扣除
			$log_info["user_id"] = $borrow_userid;//操作用户id
			$log_info["nid"] = "borrow_repay_reminder_0_".$borrow_nid.$repay_id;//订单号
			$log_info["money"] = $late_reminder;//操作金额
			$log_info["income"] = 0;//收入
			$log_info["expend"] = $late_reminder;//支出
			$log_info["balance_cash"] = -$late_reminder;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["type"] = "borrow_repay_reminder";//类型
			$log_info["to_userid"] = 0;//付给谁
			$log_info["remark"] = "对[{$borrow_url}]借款第".($repay_period+1)."期的逾期滞纳金的扣除";;
			//accountClass::AddLog($log_info);
			
			
		}
		
		// * 更新逾期的信息
		$sql = "update`{borrow_repay}` set late_days = '{$late_days}',late_interest = '{$late_interest}',late_reminder = '{$late_reminder}' where id = {$data['repay_id']}";
		$mysql->db_query($sql);
		
		return 1;		
	}
	
	//第二步，正常的还款
	//borrow_nid,repay_id
	function RepayStep1($data){
		global $mysql,$_G;
		//判断是否可以还款
		if (IsExiest($data['repay_id'])==""){
			return "borrow_repay_id_empty";
		}
		if (IsExiest($data['user_id'])==""){
			return "borrow_user_id_empty";
		}
		if (IsExiest($data['borrow_nid'])==""){
			return "borrow_nid_empty";
		}
		if ($repay_result["repay_status"]==1){
			return "borrow_repay_error";
		}
		
		//第一步，获取还款的信息
		$sql = "select p1.*,p2.username from `{borrow_repay}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.id={$data['repay_id']} and p1.user_id='{$data['user_id']}' and p1.borrow_nid='{$data['borrow_nid']}'";
		$repay_result= $mysql->db_fetch_array($sql);
		if ($repay_result==""){
			return "borrow_repay_id_empty";
		}
		if ($repay_result["user_id"]!=$data["user_id"]){
			return "borrow_user_id_empty";
		}
		if ($repay_result["status"]!=1){
			return "borrow_repay_error";
		}
		if ($repay_result["repay_status"]==1){
			return "borrow_repay_error";
		}
		
		$repay_id = $repay_result["id"];
		$repay_account = $repay_result["repay_account"];//还款总额
		$repay_period = $repay_result["repay_period"];
		$repay_web = $repay_result["repay_web"];
		$repay_vouch = $repay_result["repay_vouch"];
		$repay_account = $repay_result["repay_account"];//还款总额
		$repay_capital = $repay_result["repay_capital"];//还款本金
		$repay_interest = $repay_result["repay_interest"];//还款利息
		$repay_time = $repay_result["repay_time"];//还款时间
		
		//读取借款标的相关信息
		$borrow_nid = $data['borrow_nid'];
		$sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$data['borrow_nid']}'";
		$borrow_result = $mysql->db_fetch_array($sql);
		$status = $borrow_result['status'];
		$borrow_userid = $borrow_result['user_id'];
		$borrow_account = $borrow_result['account'];
		$borrow_name = $borrow_result['name'];
		$borrow_username = $borrow_result['username'];
		$borrow_url = html_entity_decode("<a href=/invest/a{$borrow_nid}.html target=_blank style=color:blue>{$borrow_result['name']}</a>");//借款标地址
		
		
		//第十三步，用户正常还款
		if ($repay_web!=1 && $repay_vouch!=1){
			$sql = "select p1.* from `{borrow_recover}` as p1  where p1.`recover_period` = '{$repay_period}' and p1.borrow_nid='{$borrow_nid}' limit {$data['key']},1";
			$recover_result = $mysql->db_fetch_array($sql);
			if ($recover_result==false) return -1;
			$re_time = (strtotime(date("Y-m-d",$repay_time))-strtotime(date("Y-m-d",time())))/(60*60*24);
					
			
			//添加借款者积分
			if ($re_time!=0){
				if ($re_time<0){
					if ($re_time>=-3 && $re_time<=-1){
						$borrow_credit_nid = "borrow_repay_slow";
					}
					elseif ($re_time>=-30 && $re_time<-3){
						$borrow_credit_nid = "borrow_repay_late_common";
					}
					elseif ($re_time>=-90 && $re_time<-30){
						$borrow_credit_nid = "borrow_repay_late_serious";
					}
					elseif ( $re_time<-90){
						$borrow_credit_nid = "borrow_repay_late_spite";
					}
				}else{
					if ($re_time<=30 && $re_time>=3){
						$borrow_credit_nid = "borrow_repay_advance";
						$tender_credit_nid = "tender_repay_advance";
					}elseif ($re_time>=1 && $re_time<=3){
						$borrow_credit_nid = "borrow_repay_ontime";
						$tender_credit_nid = "tender_repay_ontime";
					}
				}
			}	
				
			if($borrow_credit_nid!=""){
				//添加借款者积分
				$credit_blog['user_id'] = $borrow_userid;
				$credit_blog['nid'] = $borrow_credit_nid;
				$credit_blog['code'] = "borrow";
				$credit_blog['type'] = "repay";
				$credit_blog['addtime'] = time();
				$credit_blog['article_id'] =$repay_id;
				$credit_blog['remark'] = "还款[{$borrow_url}]第".($repay_period+1)."期积分";
				creditClass::ActionCreditLog($credit_blog);
			}
			//借款者的信用积分增加
			$credit_log['user_id'] = $borrow_userid;
			$credit_log['nid'] = "borrow_success";
			$credit_log['code'] = "borrow";
			$credit_log['type'] = "borrow";
			$credit_log['addtime'] = time();
			$credit_log['article_id'] =$repay[0]['id'];
			$credit_log['value'] = round($repay_capital*0.01);			
			$result = creditClass::ActionCreditLog($credit_log);
			
			
			
			
			
			//更新投资人的逾期信息
			$late = borrowClass::LateInterest(array("time"=>$recover_result['recover_time'],"capital"=>$recover_result['recover_capital']));			
			
			//利息超过7天，则逾期利息为0
			$late_days = $late['late_days'];	
			$late_interest = round($recover_result['recover_account']*0.004*$late_days/2,2);//罚金
			$late_reminder = round($recover_result['recover_account']*0.008*$late_days/2,2);//滞纳金			
			$sql = "update  `{borrow_recover}` set recover_yestime='".time()."',recover_account_yes = recover_account ,recover_capital_yes = recover_capital ,recover_interest_yes = recover_interest ,status=1,recover_status=1,late_days={$late_days},late_interest={$late_interest} where id = '{$recover_result['id']}'";
			$mysql->db_query($sql);
			
			//添加投资者积分
			if($tender_credit_nid!=""){
				$credit_blog['user_id'] = $recover_result['user_id'];
				$credit_blog['nid'] = $tender_credit_nid;
				$credit_blog['code'] = "tender";
				$credit_blog['type'] = "tender_repay";
				$credit_blog['addtime'] = time();
				$credit_blog['article_id'] =$repay_id;
				$credit_blog['remark'] = "用户还款[{$borrow_url}]第".($repay_period+1)."期投资积分";
				creditClass::ActionCreditLog($credit_blog);
			}
			
				
			//逾期收入
			if($late_interest>0){
				$log_info["user_id"] = $recover_result['user_id'];//操作用户id
				$log_info["nid"] = "tender_repay_late_".$recover_result['user_id']."_".$borrow_nid."_".$repay_id."_".$recover_result['id'];//订单号
				$log_info["money"] = $late_interest;//操作金额
				$log_info["income"] = $late_interest;//收入
				$log_info["expend"] =0;//支出
				$log_info["balance_cash"] = $late_interest;//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
				$log_info["frost"] = 0;//冻结金额
				$log_info["await"] = 0;//待收金额
				$log_info["type"] = "late_repay_web";//类型
				$log_info["to_userid"] = $borrow_userid;//付给谁
				$log_info["remark"] = "用户{$borrow_username}对[{$borrow_url}]还款，逾期".$late_days."天罚息收入";
				//accountClass::AddLog($log_info);
			}
			
			
			//更新投资的信息
			$sql = "select count(1) as recover_times,sum(recover_account_yes) as recover_account_yes_num,sum(recover_interest_yes) as recover_interest_yes_num,sum(recover_capital_yes) as recover_capital_yes_num  from `{borrow_recover}` where tender_id='{$recover_result['tender_id']}' and recover_status=1";
			$result = $mysql->db_fetch_array($sql);
			$recover_times = $result['recover_times'];
			
			$sql = "update  `{borrow_tender}` set recover_times={$recover_times},recover_account_yes= {$result['recover_account_yes_num']},recover_account_capital_yes =  {$result['recover_capital_yes_num']} ,recover_account_interest_yes = {$result['recover_interest_yes_num']},recover_account_wait= recover_account_all - recover_account_yes,recover_account_capital_wait = account - recover_account_capital_yes  ,recover_account_interest_wait = recover_account_interest -  recover_account_interest_yes  where id = '{$recover_result['tender_id']}'";
			$mysql->db_query($sql);
				
				
			//用户对借款标的还款
			$log_info["user_id"] = $recover_result['user_id'];//操作用户id
			$log_info["nid"] = "tender_repay_yes_".$recover_result['user_id']."_".$borrow_nid."_".$recover_result['id'];//订单号
			$log_info["money"] = $recover_result['recover_account'];//操作金额
			$log_info["income"] = $recover_result['recover_account'];//收入
			$log_info["expend"] = 0;//支出
			$log_info["balance_cash"] = $recover_result['recover_account'];//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = -$recover_result['recover_account'];//待收金额
			$log_info["type"] = "tender_repay_yes";//类型
			$log_info["to_userid"] = $borrow_userid;//付给谁
			$log_info["remark"] = "客户（{$borrow_username}）对[{$borrow_url}]借款标的第".($repay_period+1)."期还款";
			accountClass::AddLog($log_info);
			
			$user_log["user_id"] = $recover_result['user_id'];
			$user_log["code"] = "tender";
			$user_log["type"] = "recover_success";
			$user_log["operating"] = "recover";
			$user_log["article_id"] = $recover_result['user_id'];
			$user_log["result"] = 1;
			$user_log["content"] = "收到借款标[{$borrow_url}]的还款";
			usersClass::AddUsersLog($user_log);	
			
			//添加投资人罚息 wdf
			$res_time = borrowClass::LateInterest(array("time"=>$repay_time,"account"=>$repay_account));
			$late_fee=round($repay_account*$res_time['late_days']*0.004,2);
			
			borrowClass::UpdateBorrowCount(array("user_id"=>$recover_result['user_id'],"tender_recover_times_yes"=>1,"tender_recover_times_wait"=>-1,"tender_recover_yes"=>$recover_result['recover_account'],"tender_recover_wait"=>-$recover_result['recover_account'],"tender_capital_yes"=>$recover_result['recover_capital'],"tender_capital_wait"=>-$recover_result['recover_capital'],"tender_interest_yes"=>$recover_result['recover_interest'],"tender_interest_wait"=>-$recover_result['recover_interest'],"late_add_account"=>$late_fee));
			
			
			$vip=usersClass::GetUsersVip(array("user_id"=>$recover_result['user_id']));
			if ($vip['status']==1){
				$service_fee=isset($_G['system']['con_repay_interest_service_vip'])?$_G['system']['con_repay_interest_service_vip']:8;
			}else{
				$service_fee=isset($_G['system']['con_repay_interest_service'])?$_G['system']['con_repay_interest_service']:10;
			}
			$service_fee=round($recover_result['recover_interest']*$service_fee*0.01,2);			
			$log_info["user_id"] = $recover_result['user_id'];//操作用户id
			$log_info["nid"] = "repay_interest_service_".$recover_result['user_id']."_".$borrow_nid.$recover_result['id'];//订单号
			$log_info["money"] = $service_fee;//操作金额
			$log_info["income"] = 0;//收入
			$log_info["expend"] = $service_fee;//支出
			$log_info["balance_cash"] = -$service_fee;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["type"] = "repay_interest_service";//类型
			$log_info["to_userid"] = 0;//付给谁
			$log_info["remark"] = "[{$borrow_url}]还款，扣除投资利息服务费";
			accountClass::AddLog($log_info);
			
			if ($late_days>0 ){
				$log_info["user_id"] = $recover_result['user_id'];//操作用户id
				$log_info["nid"] = "tender_late_repay_yes_".$recover_result['user_id']."_".$borrow_nid."_".$recover_result['id'];//订单号
				$log_info["money"] = $late_interest;//操作金额
				$log_info["income"] = $late_interest;//收入
				$log_info["expend"] = 0;//支出
				$log_info["balance_cash"] = $late_interest;//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
				$log_info["frost"] = 0;//冻结金额
				$log_info["await"] = 0;//待收金额
				$log_info["type"] = "tender_late_repay_yes";//类型
				$log_info["to_userid"] = $value['user_id'];//付给谁
				$log_info["remark"] = "客户（{$borrow_username}）对[{$borrow_url}]借款逾期".$late_days."天还款的罚金";
				accountClass::AddLog($log_info);			
			}
			
				$sql_b = "select username from `{users}` where user_id='{$recover_result['user_id']}'";
				$result_b = $mysql->db_fetch_array($sql_b);
				$tende_username = $result_b['username'];
				
				//还款人收到还款站内信 add 20120920 wlz
				$remind['nid'] = "repay_yes";
				
				$remind['receive_userid'] = $borrow_userid;
				$remind['code'] = "borrow";
				$remind['article_id'] = $borrow_userid;
				$remind['title'] = "您已对客户".$tende_username."成功还款。";
				$remind['content'] = "您已在".date("Y-m-d",time())."对客户".$tende_username."还款成功，还款金额￥".$recover_result['recover_account'];
				
				remindClass::sendRemind($remind);
				
			
				$sql_a = "select username from `{users}` where user_id=$borrow_userid";
				$result_a = $mysql->db_fetch_array($sql_a);
				$borrow_username = $result_a['username'];
				
				//投资人收到还款站内信 add 20120920 wlz
				$remind['nid'] = "loan_pay";				
				$remind['receive_userid'] = $recover_result['user_id'];
				$remind['code'] = "invest";
				$remind['article_id'] = $recover_result['user_id'];
				$remind['title'] = "用户【".$borrow_username."】对您所投资的借款标[{$borrow_url}]已经成功还款。";
				$remind['content'] = "用户【".$borrow_username."】在".date("Y-m-d",time())."对您所投资的借款标[{$borrow_url}]已经成功还款,还款金额￥".$recover_result['recover_account'];				
				remindClass::sendRemind($remind); 
				
				
		}else{
			
			//逾期网站垫付 借款者还款积分扣除  wdf 2012 09 24
			if($repay_web==1){								
				$credit_blog['user_id'] = $borrow_userid;
				$credit_blog['nid'] = "borrow_repay_late_serious";
				$credit_blog['code'] = "borrow";
				$credit_blog['type'] = "repay";
				$credit_blog['addtime'] = time();
				$credit_blog['article_id'] =$repay_id;
				$credit_blog['remark'] = "还款[{$borrow_url}]第".($repay_period+1)."期积分";
				creditClass::ActionCreditLog($credit_blog);
				
				//借款者的信用积分增加
				$credit_log['user_id'] = $borrow_userid;
				$credit_log['nid'] = "borrow_success";
				$credit_log['code'] = "borrow";
				$credit_log['type'] = "borrow";
				$credit_log['addtime'] = time();
				$credit_log['article_id'] =$repay['id'];
				$credit_log['value'] = round($repay_capital*0.01);				
				$result = creditClass::ActionCreditLog($credit_log);
			}
			return -1;
		}
		
		
		return 1;
		
	}
	
	
	//第三步，网站还款
	//borrow_nid,repay_id
	function RepayStep2($data){
		global $mysql,$_G;
		//判断是否可以还款
		if (IsExiest($data['repay_id'])==""){
			return "borrow_repay_id_empty";
		}
		if (IsExiest($data['user_id'])==""){
			return "borrow_user_id_empty";
		}
		if (IsExiest($data['borrow_nid'])==""){
			return "borrow_nid_empty";
		}
		
		//第一步，获取还款的信息
		$sql = "select p1.*,p2.username from `{borrow_repay}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.id={$data['repay_id']} and p1.user_id='{$data['user_id']}' and p1.borrow_nid='{$data['borrow_nid']}'";
		$repay_result= $mysql->db_fetch_array($sql);
		if ($repay_result==""){
			return "borrow_repay_id_empty";
		}
		if ($repay_result["user_id"]!=$data["user_id"]){
			return "borrow_user_id_empty";
		}
		if ($repay_result["status"]!=1){
			return "borrow_repay_error";
		}
		if ($repay_result["repay_status"]==1){
			return "borrow_repay_error";
		}
		
		$repay_id = $repay_result["id"];
		$repay_account = $repay_result["repay_account"];//还款总额
		$repay_period = $repay_result["repay_period"];
		$repay_web = $repay_result["repay_web"];
		$repay_vouch = $repay_result["repay_vouch"];
		$repay_period = $repay_result["repay_period"];
		$repay_account = $repay_result["repay_account"];//还款总额
		$repay_capital = $repay_result["repay_capital"];//还款本金
		$repay_interest = $repay_result["repay_interest"];//还款利息
		$repay_time = $repay_result["repay_time"];//还款时间
		
		//读取借款标的相关信息
		$borrow_nid = $data['borrow_nid'];
		$sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$data['borrow_nid']}'";
		$borrow_result = $mysql->db_fetch_array($sql);
		$status = $borrow_result['status'];
		$borrow_userid = $borrow_result['user_id'];
		$borrow_account = $borrow_result['account'];
		$borrow_name = $borrow_result['name'];
		$borrow_username = $borrow_result['username'];
		$borrow_url = html_entity_decode("<a href=/invest/a{$borrow_nid}.html target=_blank style=color:blue>{$borrow_result['name']}</a>");//借款标地址
		
		return 1;
	}
	
	
	//第四步，担保还款
	//borrow_nid,repay_id
	function RepayStep3($data){
		global $mysql,$_G;
		//判断是否可以还款
		if (IsExiest($data['repay_id'])==""){
			return "borrow_repay_id_empty";
		}
		if (IsExiest($data['user_id'])==""){
			return "borrow_user_id_empty";
		}
		if (IsExiest($data['borrow_nid'])==""){
			return "borrow_nid_empty";
		}
		
		//第一步，获取还款的信息
		$sql = "select p1.*,p2.username from `{borrow_repay}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.id={$data['repay_id']} and p1.user_id='{$data['user_id']}' and p1.borrow_nid='{$data['borrow_nid']}'";
		$repay_result= $mysql->db_fetch_array($sql);
		if ($repay_result==""){
			return "borrow_repay_id_empty";
		}
		if ($repay_result["user_id"]!=$data["user_id"]){
			return "borrow_user_id_empty";
		}
		if ($repay_result["status"]!=1){
			return "borrow_repay_error";
		}
		if ($repay_result["repay_status"]==1){
			return "borrow_repay_error";
		}
		
		$repay_id = $repay_result["id"];
		$repay_account = $repay_result["repay_account"];//还款总额
		$repay_period = $repay_result["repay_period"];
		$repay_web = $repay_result["repay_web"];
		$repay_vouch = $repay_result["repay_vouch"];
		$repay_period = $repay_result["repay_period"];
		$repay_account = $repay_result["repay_account"];//还款总额
		$repay_capital = $repay_result["repay_capital"];//还款本金
		$repay_interest = $repay_result["repay_interest"];//还款利息
		$repay_time = $repay_result["repay_time"];//还款时间
		
		//读取借款标的相关信息
		$borrow_nid = $data['borrow_nid'];
		$sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$data['borrow_nid']}'";
		$borrow_result = $mysql->db_fetch_array($sql);
		$status = $borrow_result['status'];
		$borrow_userid = $borrow_result['user_id'];
		$borrow_account = $borrow_result['account'];
		$borrow_name = $borrow_result['name'];
		$borrow_username = $borrow_result['username'];
		$borrow_url = html_entity_decode("<a href=/invest/a{$borrow_nid}.html target=_blank style=color:blue>{$borrow_result['name']}</a>");//借款标地址
		
		if ($repay_vouch==1){
			$sql = "select p1.* from `{borrow_vouch_recover}` as p1  where p1.`order` = '{$repay_period}' and p1.borrow_nid='{$borrow_nid}' limit {$data['key']},1";
			$vouch_recover_result = $mysql->db_fetch_array($sql);
			if ($vouch_recover_result ==false) return -1;
			$late_rate = isset($_G['system']['con_late_rate'])?$_G['system']['con_late_rate']:0.008;
			 $money = $vouch_recover_result['repay_account'];
		
			$log_info["user_id"] = $vouch_recover_result['user_id'];//操作用户id
			$log_info["nid"] = "vouch_tender_repay_yes_".$borrow_nid."_".$repay_id."_".$vouch_recover_result['id'];//订单号
			$log_info["money"] = $money;//操作金额
			$log_info["income"] = $money;//收入
			$log_info["expend"] = 0;//支出
			$log_info["balance_cash"] = $money;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = -$money;//待收金额
			$log_info["type"] = "vouch_tender_repay_yes";//类型
			$log_info["to_userid"] = 0;//付给谁
			$log_info["remark"] = "用户成功对担保借款标[$borrow_url]第".($repay_period+1)."期还款";
			accountClass::AddLog($log_info);
		}
		return -1;
	}
	//第三步，担保标还款e额度
	//borrow_nid,repay_id
	function RepayStep4($data){
		global $mysql,$_G;
		//判断是否可以还款
		if (IsExiest($data['repay_id'])==""){
			return "borrow_repay_id_empty";
		}
		if (IsExiest($data['user_id'])==""){
			return "borrow_user_id_empty";
		}
		if (IsExiest($data['borrow_nid'])==""){
			return "borrow_nid_empty";
		}
		
		//第一步，获取还款的信息
		$sql = "select p1.*,p2.username from `{borrow_repay}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.id={$data['repay_id']} and p1.user_id='{$data['user_id']}' and p1.borrow_nid='{$data['borrow_nid']}'";
		$repay_result= $mysql->db_fetch_array($sql);
		if ($repay_result==""){
			return "borrow_repay_id_empty";
		}
		if ($repay_result["user_id"]!=$data["user_id"]){
			return "borrow_user_id_empty";
		}
		if ($repay_result["status"]!=1){
			return "borrow_repay_error";
		}
		if ($repay_result["repay_status"]==1){
			return "borrow_repay_error";
		}
		
		$repay_id = $repay_result["id"];
		$repay_account = $repay_result["repay_account"];//还款总额
		$repay_period = $repay_result["repay_period"];
		$repay_web = $repay_result["repay_web"];
		$repay_vouch = $repay_result["repay_vouch"];
		$repay_period = $repay_result["repay_period"];
		$repay_account = $repay_result["repay_account"];//还款总额
		$repay_capital = $repay_result["repay_capital"];//还款本金
		$repay_interest = $repay_result["repay_interest"];//还款利息
		$repay_time = $repay_result["repay_time"];//还款时间
		
		//读取借款标的相关信息
		$borrow_nid = $data['borrow_nid'];
		$sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$data['borrow_nid']}'";
		$borrow_result = $mysql->db_fetch_array($sql);
		$status = $borrow_result['status'];
		$borrow_userid = $borrow_result['user_id'];
		$vouch_status = $borrow_result['vouch_status'];
		$borrow_account = $borrow_result['account'];
		$borrow_name = $borrow_result['name'];
		$borrow_username = $borrow_result['username'];
		$borrow_type = $borrow_result['borrow_type'];
		$borrow_url = html_entity_decode("<a href=/invest/a{$borrow_nid}.html target=_blank style=color:blue>{$borrow_result['name']}</a>");//借款标地址
		
		if($borrow_type!=4){
			//扣除借款管理费 wdf
			$borrow_manage_fee = $borrow_account*0.003;
			$log_info["user_id"] = $borrow_userid;//操作用户id
			$log_info["nid"] = "borrow_manage_fee_".$borrow_userid."_".$repay_id;
			$log_info["money"] = $borrow_manage_fee;//操作金额
			$log_info["income"] = 0;//收入
			$log_info["expend"] = $borrow_manage_fee;//支出
			$log_info["balance_cash"] = -$borrow_manage_fee;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["type"] = "borrow_manage_fee";//类型
			$log_info["to_userid"] = 0;//付给谁
			$log_info["remark"] = "借款扣除借款管理费{$borrow_manage_fee}元";
			//$result = accountClass::AddLog($log_info);		
		}
		

		
		/* if ($borrow_type==5){
			$sql = "select * from `{borrow_vouch_recover}` where borrow_nid='{$borrow_nid}' and `order`={$repay_period}  limit {$data['key']},1";
			$vouch_recover_result = $mysql->db_fetch_array($sql);
			if ($vouch_recover_result==false){
				//第十一步，借款人担保额度的增加
				$_data["user_id"] = $borrow_userid;
				$_data["amount_type"] = "vouch_borrow";
				$_data["type"] = "borrrow_vouch_repay";
				$_data["oprate"] = "return";
				$_data["nid"] = "borrrow_vouch_repay_".$borrow_userid."_".$borrow_nid."_".$repay_period;
				$_data["account"] = $repay_capital;
				$_data["remark"] =   "担保[{$borrow_url}]第".($repay_period+1)."期还款完成，借款担保额度返回";
				borrowClass::AddAmountLog($_data);
				
				$sql = "update `{borrow_vouch_repay}` set repay_yestime = ".time().",repay_yesaccount = repay_account,status=1 where borrow_nid='{$borrow_nid}' and `order`={$repay_period}";
				$mysql->db_query($sql);
				return -1;
			}else{
				$_data["user_id"] = $vouch_recover_result['user_id'];
				$_data["amount_type"] = "vouch_tender";
				$_data["type"] = "borrrow_vouch_recover";
				$_data["oprate"] = "return";
				$_data["nid"] = "borrrow_vouch_recover_".$vouch_recover_result['user_id']."_".$borrow_nid."_".$vouch_recover_result['id'];
				$_data["account"] = $vouch_recover_result['repay_capital'];
				$_data["remark"] =  "担保标[{$borrow_url}]第".($repay_period+1)."期还款成功，投资担保额度返还";
				borrowClass::AddAmountLog($_data);
				$sql = "update `{borrow_vouch_recover}` set repay_yestime = ".time().",repay_yesaccount = {$vouch_recover_result['repay_account']},status=1 where id = {$vouch_recover_result['id']}";
				$mysql->db_query($sql);
				return -1;
			}
		
		
		
		}elseif ($borrow_type==4 || $borrow_type==1){
			//信用投资额度的增加
			$_data["user_id"] = $borrow_userid;
			$_data["amount_type"] = "borrow";
			$_data["type"] = "borrrow_repay";
			$_data["oprate"] = "return";
			$_data["nid"] = "borrrow_repay_".$borrow_userid."_".$borrow_nid."_".$repay_id;
			$_data["account"] = $repay_capital;
			$_data["remark"] = "借款标[{$borrow_url}]成功还款，额度增加";
			borrowClass::AddAmountLog($_data);
			return -1;
		} */
	
		return -1;
	}
	
	//第五步，最后更新还款信息
	//borrow_nid,repay_id
	function RepayStep5($data){
		global $mysql,$_G;
		//判断是否可以还款
		if (IsExiest($data['repay_id'])==""){
			return "borrow_repay_id_empty";
		}
		if (IsExiest($data['user_id'])==""){
			return "borrow_user_id_empty";
		}
		if (IsExiest($data['borrow_nid'])==""){
			return "borrow_nid_empty";
		}
		
		//第一步，获取还款的信息
		$sql = "select p1.*,p2.username from `{borrow_repay}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.id={$data['repay_id']} and p1.user_id='{$data['user_id']}' and p1.borrow_nid='{$data['borrow_nid']}'";
		$repay_result= $mysql->db_fetch_array($sql);
		if ($repay_result==""){
			return "borrow_repay_id_empty";
		}
		if ($repay_result["user_id"]!=$data["user_id"]){
			return "borrow_user_id_empty";
		}
		if ($repay_result["status"]!=1){
			return "borrow_repay_error";
		}
		
		if ($repay_result["repay_status"]==1){
			return "borrow_repay_error";
		}
		$repay_id = $repay_result["id"];
		$repay_account = $repay_result["repay_account"];//还款总额
		$repay_status = $repay_result["repay_status"];//还款总额
		$repay_period = $repay_result["repay_period"];
		$repay_web = $repay_result["repay_web"];
		$repay_vouch = $repay_result["repay_vouch"];
		$repay_period = $repay_result["repay_period"];
		$repay_account = $repay_result["repay_account"];//还款总额
		$repay_capital = $repay_result["repay_capital"];//还款本金
		$repay_interest = $repay_result["repay_interest"];//还款利息
		$repay_time = $repay_result["repay_time"];//还款时间
		
		//读取借款标的相关信息
		$borrow_nid = $data['borrow_nid'];
		$sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$data['borrow_nid']}'";
		$borrow_result = $mysql->db_fetch_array($sql);
		$status = $borrow_result['status'];
		$borrow_userid = $borrow_result['user_id'];
		$vouch_status = $borrow_result['vouch_status'];
		$borrow_account = $borrow_result['account'];
		$borrow_name = $borrow_result['name'];
		$borrow_username = $borrow_result['username'];
		$borrow_url = html_entity_decode("<a href=/invest/a{$borrow_nid}.html target=_blank style=color:blue>{$borrow_result['name']}</a>");//借款标地址
		
		
		
		
		if ($repay_status!=1){
			//添加最后的还款金额
			$sql = "update `{borrow}` set repay_account_yes= repay_account_yes + {$repay_account},repay_account_capital_yes= repay_account_capital_yes + {$repay_capital},repay_account_interest_yes= repay_account_interest_yes + {$repay_interest},repay_account_wait= repay_account_wait - {$repay_account},repay_account_capital_wait= repay_account_capital_wait - {$repay_capital},repay_account_interest_wait= repay_account_interest_wait - {$repay_interest} where borrow_nid='{$borrow_nid}'";
			$result = $mysql -> db_query($sql);
			
			
			//更新统计信息
			borrowClass::UpdateBorrowCount(array("user_id"=>$borrow_userid,"borrow_repay_yes_times"=>1,"borrow_repay_wait_times"=>-1,"borrow_repay_yes"=>$repay_account,"borrow_repay_wait"=>-$repay_account,"borrow_repay_interest_yes"=>$repay_interest,"borrow_repay_interest_wait"=>-$repay_interest,"borrow_repay_capital_yes"=>$repay_capital,"borrow_repay_capital_wait"=>-$repay_capital));	
			
			$sql = "update `{borrow_repay}` set repay_status=1,repay_yestime='".time()."',repay_account_yes=repay_account,repay_interest_yes=repay_interest,repay_capital_yes=repay_capital where id='{$repay_id}'";
			$mysql->db_query($sql);
		}
		return 1;
	}
}
?>
