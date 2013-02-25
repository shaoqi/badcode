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

class borrowreverifyClass
{

	//第一步，更新审核人的信息
	function ReverifyStep0($data){
		global $mysql;
		
		if (IsExiest($data["borrow_nid"])=="") return "borrow_nid_empty";
		$borrow_nid = $data["borrow_nid"];
		//读取借款标的相关信息
		$sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$data['borrow_nid']}'";
		$borrow_result = $mysql->db_fetch_array($sql);
		
		$status = $data['status'];
		$borrow_status = $borrow_result["status"];
		$borrow_style = $borrow_result["borrow_style"];
		
		
		//判断是否已经审核
		if ($borrow_result['borrow_full_status']==1){
			return "borrow_fullcheck_yes";
		}
		
		//判断是否满标
		if ($borrow_result['borrow_part_status']!=1 && $borrow_result['borrow_account_yes']!=$borrow_result['account']){
			return "borrow_not_full";
		}
		
		//更新满标时的操作人
		$sql = " update `{borrow}` set reverify_userid='{$data['reverify_userid']}',reverify_remark='{$data['reverify_remark']}',reverify_time='".time()."',status='{$data['status']}' where borrow_nid='{$borrow_nid}'";
		
		 $mysql ->db_query($sql);
		 return 1;		
	}
	
	//第二步，更新审核人的信息
	function ReverifyStep1($data){
		global $mysql,$_G;
		
		if (IsExiest($data["borrow_nid"])=="") return "borrow_nid_empty";
		$borrow_nid = $data["borrow_nid"];
		
		//读取借款标的相关信息
		$sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$data['borrow_nid']}'";
		$borrow_result = $mysql->db_fetch_array($sql);
		$status = $borrow_result['status'];
		$borrow_userid = $borrow_result['user_id'];
		$borrow_period= $borrow_result['borrow_period'];
		$borrow_account = $borrow_result['account'];
		$borrow_type = $borrow_result['borrow_type'];
		//基本的资料
		$borrow_url = "<a href=/invest/a{$borrow_nid}.html target=_blank style=color:blue>{$borrow_result['name']}</a>";//借款标地址
		
		
		if ($status == 3){
			//如果成功，则将还款信息输进表里面去
			$_equal["account"] = $borrow_result["account"];
			$_equal["period"] = $borrow_result["borrow_period"];
			$_equal["apr"] = $borrow_result["borrow_apr"];
			$_equal["style"] = $borrow_result["borrow_style"];
			$equal_result = EqualInterest($_equal);
			foreach ($equal_result as $key => $value){
				//防止重复添加还款信息
				$sql = "select 1 from `{borrow_repay}` where user_id='{$borrow_userid}' and repay_period='{$key}' and borrow_nid='{$borrow_nid}'";
				$result = $mysql->db_fetch_array($sql);
				if ($result==false){
					$sql = "insert into `{borrow_repay}` set `addtime` = '".time()."',";
					$sql .= "`addip` = '".ip_address()."',user_id='{$borrow_userid}',status=1,`borrow_nid`='{$borrow_nid}',`repay_period`='{$key}',";
					$sql .= "`repay_time`='{$value['repay_time']}',`repay_account`='{$value['account_all']}',";
					$sql .= "`repay_interest`='{$value['account_interest']}',`repay_capital`='{$value['account_capital']}'";
					$mysql ->db_query($sql);
				}else{
					$sql = "update `{borrow_repay}` set `addtime` = '".time()."',";
					$sql .= "`addip` = '".ip_address()."',user_id='{$borrow_userid}',status=1,`borrow_nid`='{$borrow_nid}',`repay_period`='{$key}',";
					$sql .= "`repay_time`='{$value['repay_time']}',`repay_account`='{$value['account_all']}',";
					$sql .= "`repay_interest`='{$value['account_interest']}',`repay_capital`='{$value['account_capital']}'";
					$sql .= " where user_id='$borrow_userid' and repay_period='{$key}' and borrow_nid='{$borrow_nid}'";
					$mysql ->db_query($sql);
				}
			}
			$repay_times = count($equal_result);
			$_equal["type"] = "all";
			$equal_result = EqualInterest($_equal);
			$repay_all = $equal_result['account_total'];
			
			
			//借款者总金额增加。
			$log_info["user_id"] = $borrow_userid;//操作用户id
			$log_info["nid"] = "borrow_success_".$borrow_nid;//订单号
			$log_info["money"] = $borrow_result["account"];//操作金额
			$log_info["income"] =$log_info["money"];//收入
			$log_info["expend"] = 0;//支出
			if ($borrow_result["borrow_style"]==5){
				$log_info["balance_cash"] = 0;//可提现金额
				$log_info["balance_frost"] = $borrow_account;//不可提现金额
			}else{
				$log_info["balance_cash"] = $borrow_account;//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
			}
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["repay"] = $repay_all;//待收金额
			$log_info["type"] = "borrow_success";//类型
			$log_info["to_userid"] = 0;//付给谁
			$log_info["remark"] =  "通过[{$borrow_url}]借到的款";
			accountClass::AddLog($log_info);
			
		if ($borrow_type== 4) {
			$money = $borrow_result['account']*$borrow_result['borrow_apr']*0.01/12;
            $log_info["user_id"] = $borrow_userid; //操作用户id
            $log_info["nid"] = "borrow_miao_repay_" . $data['borrow_nid']; //订单号
            $log_info["money"] = $money; //操作金额
            $log_info["income"] = 0; //收入
            $log_info["expend"] = 0; //支出
            $log_info["balance_cash"] = $money; //可提现金额
            $log_info["balance_frost"] = 0; //不可提现金额
            $log_info["frost"] = -$money; //冻结金额
            $log_info["await"] = 0; //待收金额
            $log_info["repay"] = 0; //待收金额
            $log_info["type"] = "borrow_miao_repay"; //类型
            $log_info["to_userid"] = 0; //付给谁
            $log_info["remark"] = "秒标复审通过解冻{$money}元";
            accountClass::AddLog($log_info);
        }
			
				
			
			//第十三步，冻结借款标的保证金10%。
			if ($borrow_type==1 || $borrow_type==2){
				$borrow_frost_status = isset($_G['system']['con_borrow_frost_status'])?$_G['system']['con_borrow_frost_status']:0;
				if ($borrow_frost_status ==1){
					$log_info["user_id"] = $borrow_userid;//操作用户id
					$log_info["nid"] = "borrow_success_frost_".$borrow_nid.$borrow_userid;//订单号
					$forst_account =$borrow_account*0.1;
					$log_info["money"] = $forst_account;//操作金额
					$log_info["income"] = 0;//收入
					$log_info["expend"] = 0;//支出
					$log_info["balance_cash"] = -$forst_account;//可提现金额
					$log_info["balance_frost"] = 0;//不可提现金额
					$log_info["frost"] = $forst_account;//冻结金额
					$log_info["await"] = 0;//待收金额
					$log_info["type"] = "borrow_success_frost";//类型
					$log_info["to_userid"] = 0;//付给谁
					$log_info["remark"] =  "借款标[{$borrow_url}]借款保证金冻结";
					accountClass::AddLog($log_info);
					$sql = "update `{borrow}` set borrow_frost_account='{$forst_account}' where borrow_nid='{$borrow_nid}'";
					$mysql->db_query($sql);
				}
			}
			
			if ($borrow_type!=4){
			
				//借款管理费： 借款本金的1.5%（一次 借款人）
				$start=isset($_G['system']['con_manage_fee_start'])?$_G['system']['con_manage_fee_start']:2;
				$up=isset($_G['system']['con_manage_fee_up'])?$_G['system']['con_manage_fee_up']:0.5;
				if ($borrow_period==1){
					$borrow_manage_fee=$start*0.01;
				}elseif($borrow_period>1){
					$borrow_manage_fee=(($borrow_period-1)*$up*0.01)+($start*0.01);
				}
				$manage_fee = $borrow_account*$borrow_manage_fee;
				$log_info["user_id"] = $borrow_userid;//操作用户id
				$log_info["nid"] = "borrow_manage_fee_".$borrow_userid."_".$borrow_nid;//订单号
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
				
				$fxj=isset($_G['system']['con_borrow_fxj'])?$_G['system']['con_borrow_fxj']:5;
				$fxj_fee = $borrow_account*$fxj*0.01;
				$log_info["user_id"] = $borrow_userid;//操作用户id
				$log_info["nid"] = "borrow_fxj_".$borrow_userid."_".$borrow_nid;//订单号
				$log_info["money"] = $fxj_fee;//操作金额
				$log_info["income"] = 0;//收入
				$log_info["expend"] = 0;//支出
				$log_info["balance_cash"] = -$fxj_fee;//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
				$log_info["frost"] = $fxj_fee;//冻结金额
				$log_info["await"] = 0;//待收金额
				$log_info["type"] = "borrow_fxj";//类型
				$log_info["to_userid"] = 0;//付给谁
				$log_info["remark"] = "借款扣除借款风险金{$fxj_fee}元";
				//accountClass::AddLog($log_info);
				$sql = "update `{borrow}` set borrow_frost_account='{$fxj_fee}' where borrow_nid='{$borrow_nid}'";
				//$mysql->db_query($sql);
			}
			//更新统计信息
			borrowClass::UpdateBorrowCount(array("user_id"=>$borrow_userid,"borrow_success_times"=>1,"borrow_repay_times"=>$repay_times,"borrow_repay_wait_times"=>$repay_times,"borrow_account"=>$borrow_result["account"],"borrow_repay_account"=>$repay_all,"borrow_repay_wait"=>$repay_all,"borrow_repay_interest"=>$equal_result['interest_total'],"borrow_repay_interest_wait"=>$equal_result['interest_total'],"borrow_repay_capital"=>$equal_result['capital_total'],"borrow_repay_capital_wait"=>$equal_result['capital_total']));
			
		    //判断vip会员费是否扣除
		   // borrowClass::AccountVip(array("user_id"=>$borrow_userid));
			
			$remind['nid'] = "borrow_review_yes";
			$remind['code'] = "borrow";
			$remind['article_id'] = $borrow_nid;
			$remind['receive_userid'] = $borrow_userid;
			$remind['title'] = "您的借款标({$borrow_result['name']})复审成功";
			$remind['content'] = "您的借款标[{$borrow_url}]在".date("Y-m-d",time())."复审成功";
			remindClass::sendRemind($remind);
			
		}else{
		
		
			$remind['nid'] = "borrow_review_no";
			$remind['code'] = "borrow";
			$remind['article_id'] = $borrow_nid;
			$remind['receive_userid'] = $borrow_userid;
			$remind['title'] = "您的借款标({$borrow_result['name']})复审失败";
			$remind['content'] = "您的借款标[{$borrow_url}]在".date("Y-m-d",time())."复审失败";
			remindClass::sendRemind($remind);
			
			
			
		}
		
		return 1;
	}
	
	//第二步，更新审核人的信息
	function ReverifyStep2($data){
		global $mysql,$_G;
		
		if (IsExiest($data["borrow_nid"])=="") return "borrow_nid_empty";
		$borrow_nid = $data["borrow_nid"];
		
		//读取借款标的相关信息
		$sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$data['borrow_nid']}'";
		$borrow_result = $mysql->db_fetch_array($sql);
		$status = $borrow_result['status'];
		$borrow_userid = $borrow_result['user_id'];
		$borrow_account = $borrow_result['account'];
		$borrow_name = $borrow_result['name'];
		$borrow_username = $borrow_result['username'];
		$borrow_period=$borrow_result["borrow_period"];
		//基本的资料
		$borrow_url = html_entity_decode("<a href=/invest/a{$borrow_nid}.html target=_blank style=color:blue>{$borrow_result['name']}</a>");//借款标地址
		
		$sql = "select * from `{borrow_tender}` as p1 where p1.borrow_nid='{$data['borrow_nid']}'";
		$tender_result = $mysql->db_fetch_arrays($sql);
		
		$sql = "select * from `{borrow_repay}` as p1 where p1.borrow_nid='{$data['borrow_nid']}'";
		$repay = $mysql->db_fetch_arrays($sql);
		
		if ($tender_result==false){
			return -1;
		}
		if ($status == 3){			
			
		
			//更新投资人的状态
			foreach($tender_result as $key =>$val){
			$tender_id = $val['id'];

			$sql = "update `{borrow_tender}` set status=1 where id='{$tender_id}'";
			$mysql->db_query($sql);
				
			//添加投资的收款纪录
			$_equal["account"] = $val['account'];
			$_equal["period"] = $borrow_result["borrow_period"];
			$_equal["apr"] = $borrow_result["borrow_apr"];
			$_equal["style"] = $borrow_result["borrow_style"];
			$_equal["type"] = "";
			$equal_result = EqualInterest($_equal);
			$tender_userid = $val['user_id'];
			$tender_account = $val['account'];
			
			foreach ($equal_result as $period_key => $value){
				$repay_month_account = $value['account_all'];
				//防止重复添加还款信息
				$sql = "select 1 from `{borrow_recover}` where user_id='{$tender_userid}' and borrow_nid='{$borrow_nid}' and recover_period='{$period_key}' and tender_id='{$tender_id}'";
				$result = $mysql->db_fetch_array($sql);
				
				if ($result==false){
					$sql = "insert into `{borrow_recover}` set `addtime` = '".time()."',";
					$sql .= "`addip` = '".ip_address()."',user_id='{$tender_userid}',status=1,`borrow_nid`='{$borrow_nid}',`borrow_userid`='{$borrow_userid}',`tender_id`='{$tender_id}',`recover_period`='{$period_key}',";
					$sql .= "`recover_time`='{$value['repay_time']}',`recover_account`='{$value['account_all']}',";
					$sql .= "`recover_interest`='{$value['account_interest']}',`recover_account_yes`=0,`recover_capital_yes`=0,`recover_capital`='{$value['account_capital']}'";
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
					
			//第六步,扣除投资人的资金
			$log_info["user_id"] = $tender_userid;//操作用户id
			$log_info["nid"] = "tender_success_".$borrow_nid."_".$tender_userid."_".$tender_id;//订单号
			$log_info["money"] = $tender_account;//操作金额
			$log_info["income"] = 0;//收入
			$log_info["expend"] = $tender_account;//支出
			$log_info["balance_cash"] = 0;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = -$tender_account;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["type"] = "tender_success";//类型
			$log_info["to_userid"] = $borrow_userid;//付给谁
			$log_info["remark"] = "投标[{$borrow_url}]成功投资金额扣除";
			accountClass::AddLog($log_info);
				
				
			//第五步,更新投资标的信息
			$_equal["type"] = "all";
			$equal_result = EqualInterest($_equal);
			$recover_all = $equal_result['account_total'];
			$recover_interest_all = $equal_result['interest_total'];
			$recover_capital_all = $equal_result['capital_total'];
			//第七步,添加待收的金额
			$log_info["user_id"] = $tender_userid;//操作用户id
			$log_info["nid"] = "tender_success_frost_".$borrow_nid."_".$tender_userid."_".$tender_id;//订单号
			$log_info["money"] = $recover_all;//操作金额
			$log_info["income"] = 0;//收入
			$log_info["expend"] = 0;//支出
			$log_info["balance_cash"] = 0;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = $recover_all;//待收金额
			$log_info["type"] = "tender_success_frost";//类型
			$log_info["to_userid"] = $borrow_userid;//付给谁
			$log_info["remark"] =  "投标[{$borrow_url}]成功待收金额增加";
			accountClass::AddLog($log_info);

			//提醒设置
			$remind['nid'] = "tender_success";
			$remind['receive_userid'] = $tender_userid;
			$remind['article_id'] = $borrow_nid;
			$remind['code'] = "borrow";
			$remind['title'] = "投资({$borrow_username})的标[<font color=red>{$borrow_name}</font>]满标审核成功";
			$remind['content'] = "你所投资的标[{$borrow_url}]在".date("Y-m-d",time())."已经审核通过";
			remindClass::sendRemind($remind);
			

			$remind['nid'] = "loan_yes_account";
			$remind['code'] = "borrow";
			$remind['article_id'] = $borrow_nid;
			$remind['receive_userid'] = $tender_userid;
			$remind['title'] = "投资的标[<font color=red>{$borrow_name}</font>]成功，扣除冻结款";
			$remind['content'] = "你所投资的标[{$borrow_url}]在".date("Y-m-d",time())."审核通过,扣除冻结款{$tender_account}";
			remindClass::sendRemind($remind);
				
				
			//投资信用积分
			$credit_log['user_id'] = $tender_userid;
			$credit_log['nid'] = "tender_success";
			$credit_log['code'] = "borrow";
			$credit_log['type'] = "tender";
			$credit_log['addtime'] = time();
			$credit_log['article_id'] =$tender_id;
			$credit_log['value'] = round($tender_account*0.01);
			//$result = creditClass::ActionCreditLog($credit_log);
			
			
			//加入用户操作记录
			$user_log["user_id"] = $tender_userid;
			$user_log["code"] = "tender";
			$user_log["type"] = "tender_success";
			$user_log["operating"] = "tender";
			$user_log["article_id"] = $tender_userid;
			$user_log["result"] = 1;
			$user_log["content"] = "借款标[{$borrow_url}]通过了复审,[<a href=/protocol/a{$data['borrow_nid']}.html target=_blank>点击此处</a>]查看协议书";
			usersClass::AddUsersLog($user_log);	
				
				
			$recover_times = $borrow_period;
			//第五步,更新投资标的信息
			$_equal["type"] = "all";
			$equal_result = EqualInterest($_equal);
			$recover_all = $equal_result['account_total'];
			$recover_interest_all = $equal_result['interest_total'];
			$recover_capital_all = $equal_result['capital_total'];
			$sql = "update `{borrow_tender}` set recover_account_all='{$equal_result['account_total']}',recover_account_interest='{$equal_result['interest_total']}',recover_account_wait='{$equal_result['account_total']}',recover_account_interest_wait='{$equal_result['interest_total']}',recover_account_capital_wait='{$equal_result['capital_total']}'  where id='{$tender_id}'";
			$mysql->db_query($sql);
			
			//更新统计信息
			borrowClass::UpdateBorrowCount(array("user_id"=>$tender_userid,"tender_success_times"=>1,"tender_success_account"=>$tender_account,"tender_frost_account"=>-$tender_account,"tender_recover_account"=>$recover_all,"tender_recover_wait"=>$recover_all,"tender_capital_account"=>$recover_capital_all,"tender_capital_wait"=>$recover_capital_all,"tender_interest_account"=>$recover_interest_all,"tender_interest_wait"=>$recover_interest_all,"tender_recover_times"=>$recover_times,"tender_recover_times_wait"=>$recover_times));
			}
			return 1;
		}else{
			foreach($tender_result as $key =>$val){
				$tender_id = $val['id'];
				$tender_userid = intval($val['user_id']);
				$tender_account = $val['account'];
				//更新投资的状态

				$sql = "update `{borrow_tender}` set status=2 where id='{$tender_id}'";
				$mysql->db_query($sql);
				
				//返回投资资金
				$log_info["user_id"] = $tender_userid;//操作用户id
				$log_info["nid"] = "tender_false_".$borrow_nid."_".$tender_userid."_".$tender_id."_".$period_key;//订单号
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
				$remind['code'] = "borrow";
				$remind['article_id'] = $borrow_nid;
				$remind['receive_userid'] = $tender_userid;
				$remind['title'] = "投资的标[<font color=red>{$borrow_name}</font>]满标审核失败";
				$remind['content'] = "你所投资的标[{$borrow_url}]在".date("Y-m-d",time())."审核失败,失败原因：{$borrow_result['reverify_remark']}";
				remindClass::sendRemind($remind);
				
				
				$remind['nid'] = "loan_no_account";
				$remind['code'] = "borrow";
				$remind['article_id'] = $borrow_nid;
				$remind['receive_userid'] = $tender_userid;
				$remind['title'] = "投资的标[<font color=red>{$borrow_name}</font>]失败，解冻冻结款";
				$remind['content'] = "你所投资的标[{$borrow_url}]在".date("Y-m-d",time())."审核失败,解冻冻结款{$tender_account}";
				remindClass::sendRemind($remind);
				
				//复审失败 动态 add 20120905 wlz
				$user_log["user_id"] = $borrow_userid;
				$user_log["code"] = "borrow";
				$user_log["type"] = "borrow_reverify_success";
				$user_log["operating"] = "success";
				$user_log["article_id"] = $borrow_userid;
				$user_log["result"] = 1;
				$user_log["content"] = "借款标[{$borrow_url}]满标复审失败。";
				usersClass::AddUsersLog($user_log);	
			}
		
			//审核失败 退还借款标的冻结资金 add 20120828 wlz
			//读取借款标的相关信息
			$sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$data['borrow_nid']}'";
			$borrow_result = $mysql->db_fetch_array($sql);
			$frost_account = $borrow_result['account']*0.1;
			
			$sql = "update `{borrow}` set borrow_frost_account = borrow_frost_account+$frost_account where borrow_nid='{$data['borrow_nid']}'";
			$mysql->db_query($sql);
		}
		
		return 1;
		
		
	}
	
	//第三步，担保和额度
	function ReverifyStep3($data){
		global $mysql,$_G;
		
		if (IsExiest($data["borrow_nid"])=="") return "borrow_nid_empty";
		$borrow_nid = $data["borrow_nid"];
		
		//读取借款标的相关信息
		$sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$data['borrow_nid']}'";
		$borrow_result = $mysql->db_fetch_array($sql);
		$status = $borrow_result['status'];
		$borrow_userid = $borrow_result['user_id'];
		$borrow_account = $borrow_result['account'];
		$borrow_name = $borrow_result['name'];
		$vouch_status = $borrow_result['vouch_status'];
		//基本的资料
		$borrow_url = html_entity_decode("<a href=/invest/a{$borrow_nid}.html target=_blank style=color:blue>{$borrow_result['name']}</a>");//借款标地址
		
		if ($vouch_status==1){
			$sql = "select * from `{borrow_vouch}` as p1 where p1.borrow_nid='{$data['borrow_nid']}' limit {$data['key']},1";
			$vouch_result = $mysql->db_fetch_array($sql);
			if ($vouch_result==false){
				return -1;
			}
			$vouch_id = $vouch_result['id'];
			$vouch_userid = $vouch_result['user_id'];
			$vouch_account = $vouch_result['account'];
			if ($status==3){
				$sql = "update `{borrow_vouch}` set status=1 where id = {$vouch_id}";
				$mysql -> db_query($sql);
				
				//将回款数据添加到vouch_collection标里面去
				$_equal["account"] = $vouch_account;
				$_equal["period"] = $borrow_result["borrow_period"];
				$_equal["apr"] = $borrow_result["borrow_apr"];
				$_equal["type"] = "";
				$_equal["style"] = $borrow_result["borrow_style"];
				$equal_result = EqualInterest($_equal);
				foreach ($equal_result as $period_key => $value){
					//防止重复添加还款信息
					$sql = "select id from `{borrow_vouch_recover}` where user_id='{$vouch_userid}' and borrow_nid='{$borrow_nid}' and recover_period='{$period_key}' and vouch_id='{$vouch_id}'";
					$result = $mysql->db_fetch_array($sql);
					if ($result==false){
						$sql = "insert into `{borrow_vouch_recover}` set `addtime` = '".time()."',";
						$sql .= "`addip` = '".ip_address()."',user_id='{$vouch_userid}',status=0,vouch_id={$vouch_id},`borrow_nid`='{$borrow_nid}',`borrow_userid`='{$borrow_userid}',`order`='{$period_key}',";
						$sql .= "`repay_time`='{$value['repay_time']}',`repay_account`='{$value['account_all']}',";
						$sql .= "`repay_interest`='{$value['account_interest']}',`repay_capital`='{$value['account_capital']}'";
						$mysql->db_query($sql);
					}else{
						$sql = "update `{borrow_vouch_recover}` set `addtime` = '".time()."',";
						$sql .= "`addip` = '".ip_address()."',user_id='{$vouch_userid}',status=0,vouch_id={$vouch_id},`borrow_nid`='{$borrow_nid}',`borrow_userid`='{$borrow_userid}',`order`='{$period_key}',";
						$sql .= "`repay_time`='{$value['repay_time']}',`repay_account`='{$value['account_all']}',";
						$sql .= "`repay_interest`='{$value['account_interest']}',`repay_capital`='{$value['account_capital']}' where id='{$result['id']}'";
						$mysql->db_query($sql);
					}
				}
				
				//将还款数据添加到vouch_repay标里面去
				$_equal["account"] = $vouch_account;
				$_equal["period"] = $borrow_result["borrow_period"];
				$_equal["apr"] = $borrow_result["borrow_apr"];
				$_equal["type"] = "";
				$_equal["style"] = $borrow_result["borrow_style"];
				$equal_result = EqualInterest($_equal);
				foreach ($equal_result as $period_key => $value){
					//防止重复添加还款信息
					$sql = "select id from `{borrow_vouch_repay}` where user_id='{$vouch_userid}' and borrow_nid='{$borrow_nid}' and recover_period='{$period_key}' and vouch_id='{$vouch_id}'";
					$result = $mysql->db_fetch_array($sql);
					if ($result==false){
						$sql = "insert into `{borrow_vouch_repay}` set `addtime` = '".time()."',";
						$sql .= "`addip` = '".ip_address()."',user_id='{$borrow_userid}',status=0,`borrow_nid`='{$borrow_nid}',`order`='{$period_key}',";
						$sql .= "`repay_time`='{$value['repay_time']}',`repay_account`='{$value['account_all']}'";
						$mysql->db_query($sql);
					}else{
						$sql = "update `{borrow_vouch_repay}` set `addtime` = '".time()."',";
						$sql .= "`addip` = '".ip_address()."',user_id='{$borrow_userid}',status=0,vouch_id={$vouch_id},`borrow_nid`='{$borrow_nid}',`order`='{$period_key}',";
						$sql .= "`repay_time`='{$value['repay_time']}',`repay_account`='{$value['account_all']}',";
						$sql .= " where id='{$result['id']}'";
						$mysql->db_query($sql);
					}
				}
				
				
				//2,判断是否进行担保奖励担保奖励借款成功的奖励。
				if ($borrow_result["borrow_type"]==5){
					$vouch_award_money = $vouch_account*$borrow_result["vouch_award_scale"]*0.01;
					$log_info["user_id"] = $vouch_userid;//操作用户id
					$log_info["nid"] = "vouch_success_award_".$vouch_userid."_".$vouch_id."_".$borrow_nid;//订单号
					$log_info["money"] = $vouch_award_money;//操作金额
					$log_info["income"] = $vouch_award_money;//收入
					$log_info["expend"] = 0;//支出
					$log_info["balance_cash"] = $vouch_award_money;//可提现金额
					$log_info["balance_frost"] = 0;//不可提现金额
					$log_info["frost"] = 0;//冻结金额
					$log_info["await"] = 0;//待收金额
					$log_info["type"] = "vouch_success_award";//类型
					$log_info["to_userid"] = $borrow_userid;//付给谁
					$log_info["remark"] =  "担保借款标[{$borrow_url}]借款成功的担保奖励";
					accountClass::AddLog($log_info);
				
					//借款成功的奖励支出。
					$log_info["user_id"] = $borrow_userid;//操作用户id
					$log_info["nid"] = "vouch_success_awardpay_".$borrow_userid."_".$vouch_id."_".$borrow_nid;//订单号
					$log_info["money"] = $vouch_award_money;//操作金额
					$log_info["income"] = 0;//收入
					$log_info["expend"] = $vouch_award_money;//支出
					$log_info["balance_cash"] = -$vouch_award_money;//可提现金额
					$log_info["balance_frost"] = 0;//不可提现金额
					$log_info["frost"] = 0;//冻结金额
					$log_info["await"] = 0;//待收金额
					$log_info["type"] = "vouch_success_awardpay";//类型
					$log_info["to_userid"] = $vouch_userid;//付给谁
					$log_info["remark"] =  "担保借款标的[{$borrow_url}]借款成功的担保奖励支出";
					accountClass::AddLog($log_info);
				
					//扣除借款担保额度
					$_data["user_id"] = $borrow_userid;
					$_data["amount_type"] = "vouch_borrow";
					$_data["type"] = "borrow_success";
					$_data["oprate"] = "reduce";
					$_data["nid"] = "borrow_success_vouch_".$borrow_userid."_".$borrow_nid."_".$vouch_id;
					$_data["account"] = $borrow_account;
					$_data["remark"] = "担保借款[{$borrow_url}]审核通过扣去借款担保额度";//type 操作的类型 
					//borrowClass::AddAmountLog($_data);
				
				
				}elseif ($borrow_result["borrow_type"]==2 || $borrow_result["borrow_type"]==3){
					$sql = "update `{borrow_vouch}` set status=2 where id = {$vouch_id}";
					$mysql -> db_query($sql);
				
					//2,投资担保人的担保额度返回
					//添加额度记录
					//扣除借款担保额度
					$_data["user_id"] = $vouch_userid;
					$_data["amount_type"] = "diya_borrow";
					$_data["type"] = "borrow_false";
					$_data["oprate"] = "add";
					$_data["nid"] = "borrow_false_vouch_".$vouch_userid."_".$borrow_nid."_".$vouch_id;
					$_data["account"] = $vouch_account;
					$_data["remark"] = "担保借款[{$borrow_url}]审核失败借款担保额度返回";//type 操作的类型 
					borrowClass::AddAmountLog($_data);
				}else{
				
					//添加额度记录
					$_data["user_id"] = $borrow_userid;
					$_data["amount_type"] = "borrow";
					$_data["type"] = "borrow_success";
					$_data["oprate"] = "frost";
					$_data["nid"] = "borrow_success_credit_".$borrow_userid."_".$borrow_nid;
					$_data["account"] = $borrow_account;
					$_data["remark"] = "借款标[{$borrow_url}]满标审核通过，冻结{$borrow_account}元借款信用额度";;//type 操作的类型 
					//borrowClass::AddAmountLog($_data);
				}
			}
		}else{
			if ($status==3){
				if ($borrow_result["borrow_type"]==5){
					//扣除借款担保额度
					$_data["user_id"] = $borrow_userid;
					$_data["amount_type"] = "vouch_borrow";
					$_data["type"] = "borrow_success";
					$_data["oprate"] = "frost";
					$_data["nid"] = "borrow_success_vouch_".$borrow_userid."_".$borrow_nid."_".$vouch_id;
					$_data["account"] = $borrow_account;
					$_data["remark"] = "担保借款[{$borrow_url}]审核通过扣去借款担保额度";//type 操作的类型 
					//borrowClass::AddAmountLog($_data);
				}elseif ($borrow_result["borrow_type"]==2 || $borrow_result["borrow_type"]==3){
					$amount=borrowClass::GetAmountUsers(array("user_id"=>$borrow_userid));
					//2,投资担保人的担保额度返回
					//添加额度记录
					//扣除借款担保额度
					$_data["user_id"] = $borrow_userid;
					$_data["amount_type"] = "once_amount";
					$_data["type"] = "borrow_success";
					$_data["oprate"] = "reduce";
					$_data["nid"] = "once_amount_".$borrow_result["id"]."_".$borrow_nid."_".$borrow_userid;
					$_data["account"] = $amount['once_amount'];
					$_data["remark"] = "[{$borrow_url}]抵押标审核通过，一次性额度归零";//type 操作的类型 
					//borrowClass::AddAmountLog($_data);
				}else{
					//添加额度记录
					$_data["user_id"] = $borrow_userid;
					$_data["amount_type"] = "borrow";
					$_data["type"] = "borrow_success";
					$_data["oprate"] = "frost";
					$_data["nid"] = "borrow_success_credit_".$borrow_userid."_".$borrow_nid;
					$_data["account"] = $borrow_account;
					$_data["remark"] = "借款标[{$borrow_url}]满标审核通过，冻结{$borrow_account}元借款信用额度";;//type 操作的类型 
					//borrowClass::AddAmountLog($_data);
				}
			}
		}
		
		return 1;
	}
	
	
	//第四步，奖励
	function ReverifyStep4($data){
		global $mysql,$_G;
		
		if (IsExiest($data["borrow_nid"])=="") return "borrow_nid_empty";
		$borrow_nid = $data["borrow_nid"];
		
		//读取借款标的相关信息
		$sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$data['borrow_nid']}'";
		$borrow_result = $mysql->db_fetch_array($sql);
		$status = $borrow_result['status'];
		$borrow_userid = $borrow_result['user_id'];
		$borrow_account = $borrow_result['account'];
		$borrow_name = $borrow_result['name'];
		$vouch_status = $borrow_result['vouch_status'];
		//基本的资料
		$borrow_url = html_entity_decode("<a href=/invest/a{$borrow_nid}.html target=_blank style=color:blue>{$borrow_result['name']}</a>");//借款标地址
		if ( ($status == 3 && $borrow_result['award_status']>0) ||  $borrow_result['award_false']==1 ){
			$sql = "select * from `{borrow_tender}` as p1 where p1.borrow_nid='{$data['borrow_nid']}' limit {$data['key']},1";
			$tender_result = $mysql->db_fetch_array($sql);
			if ($tender_result==false){
				return -1;
			}
			
			//投标奖励扣除和增加。
			if ($borrow_result['award_status']==1){
				$money = round(($tender_result['account']/$borrow_account)*$borrow_result['award_account'],2);
			}elseif ($borrow_result['award_status']==2){
				$money = round((($borrow_result['award_scale']/100)*$tender_result['account']),2);
			}
			
			$tender_id = $tender_result['id'];
			$tender_userid = $tender_result['user_id'];
			$log_info["user_id"] = $tender_userid;//操作用户id
			$log_info["nid"] = "tender_award_add_".$tender_userid."_".$tender_id."_".$borrow_nid;//订单号
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
			$log_info["nid"] = "borrow_award_lower_".$borrow_userid."_".$tender_id."_".$borrow_nid;//订单号
			$log_info["money"] = $money;//操作金额
			$log_info["income"] = 0;//收入
			$log_info["expend"] = $money;//支出
			$log_info["balance_cash"] = -$money;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["type"] = "borrow_award_lower";//类型
			$log_info["to_userid"] = $tender_userid;//付给谁
			$log_info["remark"] =  "扣除借款[{$borrow_url}]的奖励";
			accountClass::AddLog($log_info);
			return 1;
		}
		return -1;
	}
	
	//第四步，奖励
	function ReverifyStep5($data){
		global $mysql,$_G;
		
		if (IsExiest($data["borrow_nid"])=="") return "borrow_nid_empty";
		$borrow_nid = $data["borrow_nid"];
		
		//读取借款标的相关信息
		$sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$data['borrow_nid']}'";
		$borrow_result = $mysql->db_fetch_array($sql);
		$status = $borrow_result['status'];
		$borrow_userid = $borrow_result['user_id'];
		$borrow_account = $borrow_result['account'];
		$borrow_name = $borrow_result['name'];
		$vouch_status = $borrow_result['vouch_status'];
		//基本的资料
		$borrow_url = html_entity_decode("<a href=/invest/a{$borrow_nid}.html target=_blank style=color:blue>{$borrow_result['name']}</a>");//借款标地址
		
	//第十一步，更新借款标的信息$nowtime = time();
		$nowtime= time();
		$endtime = get_times(array("num"=>$borrow_result["borrow_period"],"time"=>$nowtime));
		
		if ($borrow_result["borrow_style"]==1){
			$_each_time = "每三个月后".date("d",$nowtime)."日";
			$nexttime = get_times(array("num"=>3,"time"=>$nowtime));
		}else{
			$_each_time = "每月".date("d",$nowtime)."日";
			$nexttime = get_times(array("num"=>1,"time"=>$nowtime));
		}
		$_equal["account"] = $borrow_result['account'];
		$_equal["period"] = $borrow_result["borrow_period"];
		$_equal["apr"] = $borrow_result["borrow_apr"];
		$_equal["style"] = $borrow_result["borrow_style"];
		$_equal["type"] = "all";
		$equal_result = EqualInterest($_equal);
		$sql = "update `{borrow}` set borrow_full_status=1,repay_account_all='{$equal_result['account_total']}',repay_account_interest='{$equal_result['interest_total']}',repay_account_capital='{$equal_result['capital_total']}',repay_account_wait='{$equal_result['account_total']}',repay_account_interest_wait='{$equal_result['interest_total']}',repay_account_capital_wait='{$equal_result['capital_total']}',repay_last_time='{$endtime}',repay_next_time='{$nexttime}',borrow_success_time='{$nowtime}',repay_each_time='{$_each_time}',repay_times='{$repay_times}'  where borrow_nid='{$borrow_nid}'";
		$mysql->db_query($sql);
		
		
		if ($borrow_result['borrow_type']==4){
			$sql="select * from `{borrow_repay}` where borrow_nid={$borrow_nid}";
			$result=$mysql->db_fetch_array($sql);
			$borrow_repay['id']=$result['id'];
			$borrow_repay['user_id']=$borrow_userid;
			$borrow_repay['borrow_nid']=$borrow_nid;
			$repay_result=borrowClass::BorrowRepay($borrow_repay);
			
		}
			
			
		//加入用户操作记录
		/*$user_log["user_id"] = $borrow_userid;
		$user_log["code"] = "borrow";
		$user_log["type"] = "borrow_reverify_success";
		$user_log["operating"] = "success";
		$user_log["article_id"] = $borrow_userid;
		$user_log["result"] = 1;
		$user_log["content"] = "借款标[{$borrow_url}]通过了复审,[<a href=/protocol/a{$data['borrow_nid']}.html target=_blank>点击此处</a>]查看协议书";
		usersClass::AddUsersLog($user_log);	 del 20120905 wlz*/ 
			
			
		/*//提醒设置
		$remind['nid'] = "borrow_review_yes";
		$remind['receive_userid'] = $borrow_userid;
		$remind['code'] = "borrow";
		$remind['article_id'] = $borrow_nid;
		$remind['title'] = "招标[{$borrow_name}]满标审核成功";
		$remind['content'] = "你的借款标[{$borrow_url}]在".date("Y-m-d",time())."已经审核通过";
		remindClass::sendRemind($remind); del 20120905 wlz*/
			
	}
}
?>
