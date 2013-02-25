<?php

/******************************
 * $File: borrow.reverify.php
 * $Description: 借款类文件
 * $Author: XiaoWu 
 * $Time:2012-08-16
 * $Update:XiaoWu
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

class borrowreverifydayClass
{
	public static function ReverifyDay($data = array()){
		global $mysql,$_G;
		if (IsExiest($data["borrow_nid"])=="") return "borrow_nid_empty";
		$borrow_nid = $data["borrow_nid"];
		$sql = " update `{borrow}` set reverify_userid='{$data['reverify_userid']}',reverify_remark='{$data['reverify_remark']}',reverify_time='".time()."',status='{$data['status']}' where borrow_nid='{$borrow_nid}'";
		 $mysql ->db_query($sql);
		 $status = $data['status'];
		$sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$data['borrow_nid']}'";
		$borrow_result = $mysql->db_fetch_array($sql);
		$borrow_userid = $borrow_result["user_id"];//借款用户id
		$borrow_username = $borrow_result["username"];//借款用户id
		$borrow_account = $borrow_result["account"];//借款金额
		$borrow_day = $borrow_result["borrow_day"];//借款期限
		$borrow_apr = $borrow_result["borrow_apr"];//借款期限
		$borrow_name = $borrow_result["name"];//借款 标题
		$borrow_type = $borrow_result["type"];//借款 标题
		$borrow_cash_status = $borrow_result["cash_status"];//是否提现标
		$borrow_url = "<a href=\'/invest/a{$data['borrow_nid']}.html\' target=_blank>{$borrow_result['name']}</a>";
		$repaytime=time()+$borrow_day*60*60*24;
		$repayaccount=round($borrow_account+$borrow_account*$borrow_day*$borrow_apr/365/100,2);
		$repaylixi=round($borrow_account*$borrow_day*$borrow_apr/100/365,2);
		 if ($status == 3){
			$sql = " update `{borrow}` set borrow_full_status='1' where borrow_nid='{$borrow_nid}'";
			$mysql ->db_query($sql);
			
			//发布借款标 复审成功 扣除一次性额度为0  add 20120830 wlz
			if($borrow_result['borrow_type'] ==2 || $borrow_result['borrow_type'] ==3){
				$sql_a = "update `{borrow_amount}` set once_amount=0 where user_id='{$borrow_userid}'";
				$mysql->db_query($sql_a);
			}
			
			//借款者的信用积分增加
			$credit_log['user_id'] = $borrow_userid;
			$credit_log['nid'] = "borrow_success";
			$credit_log['code'] = "borrow";
			$credit_log['type'] = "borrow";
			$credit_log['addtime'] = time();
			$credit_log['article_id'] =$repay_id;
			$credit_log['value'] = round($borrow_account*0.01);
			$credit_log['remark'] = "借款标[{$borrow_url}]成功积分增加";
			$result = creditClass::ActionCreditLog($credit_log);
			
			
			$sql = "select 1 from `{borrow_repay}` where user_id='{$borrow_userid}' and repay_period='0' and borrow_nid='{$borrow_nid}'";
			$result = $mysql->db_fetch_array($sql);
			if ($result==false){
				$sql = "insert into `{borrow_repay}` set `addtime` = '".time()."',";
				$sql .= "`addip` = '".ip_address()."',user_id='{$borrow_userid}',status=1,`borrow_nid`='{$borrow_nid}',`repay_period`='0',";
				$sql .= "`repay_time`='{$repaytime}',`repay_account`='{$repayaccount}',";
				$sql .= "`repay_interest`='{$repaylixi}',`repay_capital`='{$borrow_account}'";
				$mysql ->db_query($sql);
			}
			
			//借款者总金额增加。
			$log_info["user_id"] = $borrow_userid;//操作用户id
			$log_info["nid"] = "borrow_success_".$borrow_nid;//订单号
			$log_info["money"] = $borrow_account;//操作金额
			$log_info["income"] = $borrow_account;//收入
			$log_info["expend"] = 0;//支出
			$log_info["balance_cash"] = $borrow_account;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["repay"] = $borrow_account;//待收金额
			$log_info["type"] = "borrow_success";//类型
			$log_info["to_userid"] = 0;//付给谁
			$log_info["remark"] =  "通过[{$borrow_url}]借到的款";
			accountClass::AddLog($log_info);
			
			if ($borrow_type==3 || $borrow_type==5){
				if ($borrow_day==1){
					$borrow_manage_fee=0.004;
				}else{
					$borrow_manage_fee=($borrow_day-1)*0.0004+0.004;
				}
			}elseif($borrow_type==7){
				if ($borrow_day==1){
					$borrow_manage_fee=0.002;
				}else{
					$borrow_manage_fee=($borrow_day-1)*0.0002+0.002;
				}
			}
			$manage_fee = $borrow_account*$borrow_manage_fee;
			$log_info["user_id"] = $borrow_userid;//操作用户id
			$log_info["nid"] = "borrow_manage_fee_".$borrow_userid."_".$borrow_nid.$repay_id;//订单号
			$log_info["money"] = $manage_fee;//操作金额
			$log_info["income"] = 0;//收入
			$log_info["expend"] = $manage_fee;//支出
			$log_info["balance_cash"] = -$manage_fee;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["type"] = "borrow_manage_fee";//类型
			$log_info["to_userid"] = 0;//付给谁
			$log_info["remark"] = "用户借款成功[$borrow_url]扣除借款管理费";
			accountClass::AddLog($log_info);
			
			//更新统计信息
			borrowClass::UpdateBorrowCount(array("user_id"=>$borrow_userid,"borrow_success_times"=>1,"borrow_repay_times"=>1,"borrow_repay_wait_times"=>1,"borrow_account"=>$borrow_account,"borrow_repay_account"=>$repayaccount,"borrow_repay_wait"=>$repayaccount,"borrow_repay_interest"=>$repaylixi,"borrow_repay_interest_wait"=>$repaylixi,"borrow_repay_capital"=>$borrow_account,"borrow_repay_capital_wait"=>$borrow_account));
			
			
			
			
			$tender_result = borrowClass::GetTenderList(array("borrow_nid"=>$borrow_nid,"limit"=>"all"));
			foreach ($tender_result as $_key => $_value){
				
				$tender_id = $_value['id'];
				
				//更新投资人的状态
				$sql = "update `{borrow_tender}` set status=1 where id={$tender_id}";
				$mysql->db_query($sql);
				
				//添加投资的收款纪录
				$tender_account = $_value['account'];
				$recover_account=round($tender_account+$tender_account*$borrow_day*$borrow_apr/100/365,2);
				$recover_lixi=round($tender_account*$borrow_day*$borrow_apr/100/365,2);
				
				$tender_userid = $_value['user_id'];
				$sql = "insert into `{borrow_recover}` set `addtime` = '".time()."',";
				$sql .= "`addip` = '".ip_address()."',user_id='{$tender_userid}',status=1,`borrow_nid`='{$borrow_nid}',`borrow_userid`='{$borrow_userid}',`tender_id`='{$tender_id}',`recover_period`='0',";
				$sql .= "`recover_time`='{$repaytime}',`recover_account`='{$recover_account}',";
				$sql .= "`recover_interest`='{$recover_lixi}',`recover_capital`='{$tender_account}'";
				$mysql ->db_query($sql);
				
				$sql = "update `{borrow_tender}` set recover_account_all='{$recover_account}',recover_account_interest='{$recover_lixi}',recover_account_wait='{$recover_account}',recover_account_interest_wait='{$recover_lixi}',recover_account_capital_wait='{$tender_account}'  where id='{$tender_id}'";
				$mysql->db_query($sql);
				
				
				if ($_value['status']!=1){
					//第六步,扣除投资人的资金
					$log_info["user_id"] = $tender_userid;//操作用户id
					$log_info["nid"] = "tender_succes_".$borrow_nid.$tender_userid.$tender_id;//订单号
					$log_info["money"] = $tender_account;//操作金额
					$log_info["income"] = 0;//收入
					$log_info["expend"] = -$tender_account;//支出
					$log_info["balance_cash"] = 0;//可提现金额
					$log_info["balance_frost"] = 0;//不可提现金额
					$log_info["frost"] = -$tender_account;//冻结金额
					$log_info["await"] = 0;//待收金额
					$log_info["type"] = "tender_success";//类型
					$log_info["to_userid"] = $borrow_userid;//付给谁
					$log_info["remark"] = "投标[{$borrow_url}]成功投资金额扣除";
					accountClass::AddLog($log_info);
					
					//第七步,添加待收的金额
					$log_info["user_id"] = $tender_userid;//操作用户id
					$log_info["nid"] = "tender_success_frost_".$borrow_nid.$tender_userid.$tender_id;//订单号
					$log_info["money"] = $recover_account;//操作金额
					$log_info["income"] = $recover_account;//收入
					$log_info["expend"] = 0;//支出
					$log_info["balance_cash"] = 0;//可提现金额
					$log_info["balance_frost"] = 0;//不可提现金额
					$log_info["frost"] = 0;//冻结金额
					$log_info["await"] = $recover_account;//待收金额
					$log_info["type"] = "tender_success_frost";//类型
					$log_info["to_userid"] = $borrow_userid;//付给谁
					$log_info["remark"] =  "投标[{$borrow_url}]成功待收金额增加";
					accountClass::AddLog($log_info);
					
					
					
					
					//第九步,提醒设置
					$remind['nid'] = "tender_success";
					$remind['sent_user'] = "0";
					$remind['receive_user'] = $tender_userid;
					$remind['article_id'] = $borrow_nid;
					$remind['code'] = "borrow";
					$remind['title'] = "投资({$borrow_username})的标[<font color=red>{$borrow_name}</font>]满标审核成功";
					$remind['content'] = "你所投资的标[{$borrow_url}]在".date("Y-m-d",time())."已经审核通过";
					$remind['type'] = "system";
					remindClass::sendRemind($remind);
					
					
					//第十步,投资者的信用积分增加
					$credit_log['user_id'] = $tender_userid;
					$credit_log['nid'] = "tender_success";
					$credit_log['code'] = "borrow";
					$credit_log['type'] = "tender";
					$credit_log['addtime'] = time();
					$credit_log['article_id'] =$tender_id;
					$credit_log['value'] = round($tender_account*0.01);
					$credit_log['remark'] = "投标[{$borrow_url}]成功积分增加";
					$result = creditClass::ActionCreditLog($credit_log);
										
				}
				
				//更新统计信息
				borrowClass::UpdateBorrowCount(array("user_id"=>$tender_userid,"tender_success_times"=>1,"tender_success_account"=>$tender_account,"tender_frost_account"=>-$tender_account,"tender_recover_account"=>$recover_account,"tender_recover_wait"=>$recover_account,"tender_capital_account"=>$tender_account,"tender_capital_wait"=>$tender_account,"tender_interest_account"=>$recover_lixi,"tender_interest_wait"=>$recover_lixi,"tender_recover_times"=>1,"tender_recover_times_wait"=>1));
						
			}
			$nowtime=time();
			$sql = "update `{borrow}` set repay_account_all='{$recover_account}',repay_account_interest='{$recover_lixi}',repay_account_capital='{$tender_account}',repay_account_wait='{$recover_account}',repay_account_interest_wait='{$recover_lixi}',repay_account_capital_wait='{$tender_account}',repay_last_time='{$repaytime}',repay_next_time='{$repaytime}',borrow_success_time='{$nowtime}',repay_each_time='{$repaytime}',repay_times='{$repaytime}'  where borrow_nid='{$borrow_nid}'";
			$mysql->db_query($sql);
			
			if($borrow_type==5){
				$_data["user_id"] = $borrow_userid;
				$_data["amount_type"] = "borrow";
				$_data["type"] = "borrow_success";
				$_data["oprate"] = "reduce";
				$_data["nid"] = "borrow_success_credit_".$borrow_userid."_".$borrow_nid.$value["id"];
				$_data["account"] = $borrow_account;
				$_data["remark"] = "借款标[{$borrow_url}]满标审核通过，借款信用额度减少";
				borrowClass::AddAmountLog($_data);
			}elseif ($borrow_type==3){
				$_data["user_id"] = $borrow_userid;
				$_data["amount_type"] = "once_amount";
				$_data["type"] = "borrow_success";
				$_data["oprate"] = "reduce";
				$_data["nid"] = "borrow_success_credit_".$borrow_userid."_".$borrow_nid.$value["id"];
				$_data["account"] = $borrow_account;
				$_data["remark"] = "借款标[{$borrow_url}]满标审核通过，借款一次性额度减少";
				borrowClass::AddAmountLog($_data);
			}
			
			//提醒设置
			$remind['nid'] = "borrow_review_yes";
			$remind['sent_user'] = "0";
			$remind['receive_user'] = $borrow_userid;
			$remind['code'] = "borrow";
			$remind['article_id'] = $borrow_nid;
			$remind['title'] = "招标[{$borrow_name}]满标审核成功";
			$remind['content'] = "你的借款标[{$borrow_url}]在".date("Y-m-d",time())."已经审核通过";
			$remind['type'] = "system";
			//remindClass::sendRemind($remind);
			
		 }elseif ($status == 4){
		 
			//返回所有投资者的金钱。
			$tender_result = borrowClass::GetTenderList(array("borrow_nid"=>$borrow_nid,"limit"=>"all"));
			foreach ($tender_result as $key => $value){
				$tender_userid = $value['user_id'];
				$tender_account= $value['account'];
				$tender_id= $value['id'];
				$log_info["user_id"] = $tender_userid;//操作用户id
				$log_info["nid"] = "tender_false_".$tender_userid."_".$tender_id.$borrow_nid;//订单号
				$log_info["money"] = $tender_account;//操作金额
				$log_info["income"] = 0;//收入
				$log_info["expend"] = 0;//支出
				$log_info["balance_cash"] = $tender_account;//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
				$log_info["frost"] = -$tender_account;//冻结金额
				$log_info["await"] = 0;//待收金额
				$log_info["type"] = "tender_false";//类型
				$log_info["to_userid"] = $borrow_userid;//付给谁
				$log_info["remark"] =  "招标[{$borrow_url}]失败返回的投标额";
				accountClass::AddLog($log_info);
				
				
				//提醒设置
				$remind['nid'] = "tender_false";
				$remind['sent_user'] = "0";
				$remind['code'] = "borrow";
				$remind['article_id'] = $borrow_nid;
				$remind['receive_user'] = $value['user_id'];
				$remind['title'] = "投资的标[<font color=red>{$borrow_name}</font>]满标审核失败";
				$remind['content'] = "你所投资的标[{$borrow_url}]在".date("Y-m-d",time())."审核失败,失败原因：{$data['reverify_remark']}";
				$remind['type'] = "system";
				remindClass::sendRemind($remind);
			
				
				//第十步,更新投资人的状态
				$sql = "update `{borrow_tender}` set status=2 where id={$tender_id}";
				$mysql->db_query($sql);
				
				//更新统计信息
				borrowClass::UpdateBorrowCount(array("user_id"=>$tender_userid,"tender_frost_account"=>-$tender_account,"tender_account"=>-$tender_account));
			}
			
			//提醒设置
			$remind['nid'] = "borrow_review_no";
			$remind['sent_user'] = "0";
			$remind['code'] = "borrow";
			$remind['article_id'] = $borrow_nid;
			$remind['receive_user'] = $borrow_userid;
			$remind['title'] = "你所申请的标[<font color=red>{$borrow_name}</font>]满标审核失败";
			$remind['content'] = "你所申请的标[{$borrow_url}]在".date("Y-m-d",time())."审核失败,失败原因：{$data['repayment_remark']}";
			$remind['type'] = "system";
			//remindClass::sendRemind($remind);
		}
		if ($borrow_result['award_status']!=0){
			if ($status == 3 || $borrow_result['award_false']==1){
				$tender_result = borrowClass::GetTenderList(array("borrow_nid"=>$borrow_nid,"limit"=>"all"));
				foreach ($tender_result as $key => $value){
					//投标奖励扣除和增加。
					if ($borrow_result['award_status']==1){
						$money = round(($value['account']/$borrow_account)*$borrow_result['award_account'],2);
					}elseif ($borrow_result['award_status']==2){
						$money = round((($borrow_result['award_scale']/100)*$value['account']),2);
					}
					$tender_id = $value['id'];
					$tender_userid = $value['user_id'];
					$log_info["user_id"] = $tender_userid;//操作用户id
					$log_info["nid"] = "tender_award_add_".$tender_userid."_".$tender_id.$borrow_nid;//订单号
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
					$log_info["nid"] = "borrow_award_lower_".$borrow_userid."_".$tender_id.$borrow_nid;//订单号
					$log_info["money"] = $money;//操作金额
					$log_info["income"] = 0;//收入
					$log_info["expend"] = -$money;//支出
					$log_info["balance_cash"] = -$money;//可提现金额
					$log_info["balance_frost"] = 0;//不可提现金额
					$log_info["frost"] = 0;//冻结金额
					$log_info["await"] = 0;//待收金额
					$log_info["type"] = "borrow_award_lower";//类型
					$log_info["to_userid"] = $tender_userid;//付给谁
					$log_info["remark"] =  "扣除借款[{$borrow_url}]的奖励";
					accountClass::AddLog($log_info);
				}
			}
		}
		return $borrow_nid;
	}
}
?>