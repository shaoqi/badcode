<?
/******************************
 * $File: borrow.roam.php
 * $Description: 流转标文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

class borrowRoamClass
{
	function GetList($data = array()){
		global $mysql;
		
		$_sql = "where 1=1 and p1.borrow_type='roam' ";	
			 
		//搜索用户id
		if (IsExiest($data['user_id'])!=false) {
			$_sql .= " and p1.user_id = '{$data['user_id']}'";
		}
		//搜索借款标题
		if (IsExiest($data['name'])!=false) {
			$_sql .= " and p1.`name` like '%{$data['name']}%'";
		}
        
        //搜索用户名
		if (IsExiest($data['username'])!=false) {
			$_sql .= " and p3.`username` like '%{$data['username']}%'";
		}
		//搜索借款号
		if (IsExiest($data['borrow_nid'])!=false) {
			$_sql .= " and p1.borrow_nid = '{$data['borrow_nid']}'";
		}
		//搜索借款号
		if (IsExiest($data['status'])!=false) {
			$_sql .= " and p1.status = '{$data['status']}'";
		}
        //搜索借款号
		if (IsExiest($data['status_nid'])!=false) {
            if ($data['status_nid']=="first"){
			 $_sql .= " and p1.status = 0 ";
            } elseif ($data['status_nid']=="now"){
			 $_sql .= " and (p1.status = 3 or p1.status=1)  and p2.portion_wait >0   ";
            }elseif ($data['status_nid']=="recover"){
			 $_sql .= " and p1.status = 3   and p2.portion_wait = 0 and p2.recover_wait >0 ";
            }elseif ($data['status_nid']=="over"){
			 $_sql .= " and p1.status = 3  and p2.portion_wait = 0 and p2.recover_wait = 0 ";
            }elseif ($data['status_nid']=="false"){
			 $_sql .= " and p1.status = 2 ";
            }
		}
        	//判断添加时间开始
		if (IsExiest($data['dotime1']) != false){
			$dotime1 = ($data['dotime1']=="request")?$_REQUEST['dotime1']:$data['dotime1'];
			if ($dotime1!=""){
				$_sql .= " and p1.addtime > ".get_mktime($dotime1);
			}
		}

		//判断添加时间结束
		if (IsExiest($data['dotime2'])!=false){
			$dotime2 = ($data['dotime2']=="request")?$_REQUEST['dotime2']:$data['dotime2'];
			if ($dotime2!=""){
				$_sql .= " and p1.addtime < ".get_mktime($dotime2);
			}
		}
		//搜索用户名
		if (IsExiest($data['username'])!=false) {
			$_sql .= " and p3.username = '".urldecode($data['username'])."'";
		}
		//搜索流转期限
		if (IsExiest($data['borrow_period'])!=false) {
			$_sql .= " and p1.borrow_period = '{$data['borrow_period']}'";
		}
		//搜索年利率
		if (IsExiest($data['borrow_apr'])!=false) {
			$_sql .= " and p1.borrow_apr = '{$data['borrow_apr']}'";
		}
		
		$_select = "p1.*,p2.*,p3.username";
		$_order = " order by p1.id desc";
		$sql = "select SELECT from {borrow} as p1 
				left join {borrow_roam} as p2 on p1.borrow_nid=p2.borrow_nid
				left join {users} as p3 on p1.user_id=p3.user_id
				SQL ORDER LIMIT";
		
		//是否显示全部的信息
		if (IsExiest($data['limit'])!=false){
			if ($data['limit'] != "all"){ $_limit = "  limit ".$data['limit']; }
			return $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array($_select, $_sql, $_order, $_limit), $sql));
		}			 
		
		//判断总的条数
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array('count(1) as num', $_sql,'', ''), $sql));
		$total = intval($row['num']);
		
		//分页返回结果
		$data['page'] = !IsExiest($data['page'])?1:$data['page'];
		$data['epage'] = !IsExiest($data['epage'])?10:$data['epage'];
		$total_page = ceil($total / $data['epage']);
		$_limit = " limit ".($data['epage'] * ($data['page'] - 1)).", {$data['epage']}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL','ORDER', 'LIMIT'), array($_select,$_sql,$_order, $_limit), $sql));
		foreach ($list as $key => $value){
            $status_name = "";
            if ($value['status']==0){
                $status_name = "初审中";
            }elseif ($value['status']==2){
                $status_name = "初审失败";
            }elseif ($value['portion_wait']>0){
                $status_name = "流转中";
            }else{
                if ($value['recover_wait']>0){
                    $status_name = "待回购";
                }else{
                     $status_name = "已回购";
                }
            }
            $list[$key]["status_name"] = $status_name;
		}
		//返回最终的结果
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		
		return $result;
	}
    
    
    /**
	 *   
	 *
	 * @param Array $data
	 * @return Array
	 */
    function GetRoamOne($data = array()){
		global $mysql;
		$_sql = "where 1=1 ";
        
		if (IsExiest($data['user_id'])!=""){
			$_sql .= " and  p1.user_id = '{$data['user_id']}' ";
		}
		if (IsExiest($data['id'])!=""){
			$_sql .= " and  p1.id = '{$data['id']}' ";
		}
		if (IsExiest($data['borrow_nid'])!=""){
			$_sql .= " and  p1.borrow_nid = '{$data['borrow_nid']}' ";
		}
		$sql = "select  p1.*,p2.name as borrow_name,p2.borrow_period,p2.borrow_type,p2.borrow_apr,p2.borrow_style,p2.account,p2.borrow_contents,p2.award_status,p2.award_false,p2.award_scale,p2.award_account,p2.continued_status,p2.continued,p2.continued_min,p3.username,p4.name as style_name,p4.title as style_title  from `{borrow_roam}` as p1 
				 left join {borrow} as p2 on p1.borrow_nid=p2.borrow_nid
				 left join {users} as p3 on p1.user_id=p3.user_id
                 left join {borrow_style} as p4 on p2.borrow_style=p4.nid
				  $_sql
				";
		$result = $mysql->db_fetch_array($sql);
        if ($result['upfiles_id']!=""){
            $sql ="select * from `{users_upfiles}` where id in ({$result['upfiles_id']})";
            $_result = $mysql->db_fetch_arrays($sql);
            $result["upfiles_pic"] = $_result;
        }
        return $result;
        
	}
    
    function AddRoam($data){
        global $mysql,$_G;
		require_once("borrow.calculates.php");
        $roam_result = self::GetRoamOne(array("borrow_nid"=>$data["borrow_nid"]));
        $borrow_url = "<a href={$_G['web_domain']}/invest/a{$data['borrow_nid']}.html target=_blank >{$roam_result['borrow_name']}</a>";
        if ($roam_result==""){
            return "borrow_loan_roam_empty";
        }
        if ($roam_result["portion_wait"]<$data["portion"] || $data["portion"]<=0){
            return "borrow_loan_roam_portion_error";
        }
        $account = $data["portion"]*$roam_result["account_min"];
        $account_result =  accountClass::GetAccountUsers(array("user_id"=>$data['user_id']));
		if ($account_result['balance']<$account){
			return "borrow_roam_account_use_none";
		}
    	$_equal["account"] =  $account;
		$_equal["period"] = $roam_result["borrow_period"];
		$_equal["apr"] = $roam_result["borrow_apr"];
		$_equal["style"] = $roam_result["borrow_style"];
		$_equal["type"] = "all";
		$equal_result = borrowCalculateClass::GetType($_equal);
	    $recover_all = $equal_result['account_total'];
		$recover_interest_all = $equal_result['interest_total'];
		$recover_capital_all = $equal_result['capital_total'];
          
		//添加投资的借款信息
        $_nid = 'tender_'.$data['user_id']."_".time();
		$sql = "insert into `{borrow_tender}` set `addtime` = '".time()."',`addip` = '".ip_address()."'";
        $sql .= ",status=1,borrow_nid='{$data['borrow_nid']}',contents='{$data['contents']}',nid='{$_nid}',account='{$account}',account_tender='{$account}'";
        $sql .= ",user_id={$data["user_id"]}";
		$mysql->db_query($sql);
		$tender_id = $mysql->db_insert_id();
        
 	    $_equal["type"] = "";
		$equal_result = borrowCalculateClass::GetType($_equal);
         $sql = "select count(1) as num from `{borrow_repay}` where borrow_nid='{$data["borrow_nid"]}' ";
        $_result = $mysql->db_fetch_array($sql);
        $repay_period = empty($_result)?0:$_result['num'];
        
        //添加投资人的收款信息
        foreach ($equal_result as $period_key => $value){
            $repay_period = $repay_period+1;
			$repay_month_account = $value['account_all'];
			//防止重复添加还款信息
			$sql = "select 1 from `{borrow_recover}` where user_id='{$data['user_id']}' and borrow_nid='{$data["borrow_nid"]}' and recover_period='{$repay_period}' and tender_id='{$tender_id}'";
			$result = $mysql->db_fetch_array($sql);
			if ($result==false){
				$sql = "insert into `{borrow_recover}` set `addtime` = '".time()."',";
				$sql .= "`addip` = '".ip_address()."',user_id='{$data['user_id']}}',status=1,`borrow_nid`='{$data["borrow_nid"]}',`borrow_userid`='{$roam_result["user_id"]}',`tender_id`='{$tender_id}',`recover_period`='{$repay_period}',";
				$sql .= "`recover_time`='{$value['repay_time']}',`recover_account`='{$value['account_all']}',";
				$sql .= "`recover_interest`='{$value['account_interest']}',`recover_capital`='{$value['account_capital']}'";
				$mysql ->db_query($sql);
			}else{
				$sql = "update `{borrow_recover}` set `addtime` = '".time()."',";
				$sql .= "`addip` = '".ip_address()."',user_id='{$data['user_id']}}',status=1,`borrow_nid`='{$data["borrow_nid"]}',`borrow_userid`='{$roam_result["user_id"]}',`tender_id`='{$tender_id}',`recover_period`='{$repay_period}',";
				$sql .= "`recover_time`='{$value['repay_time']}',`recover_account`='{$value['account_all']}',";
				$sql .= "`recover_interest`='{$value['account_interest']}',`recover_capital`='{$value['account_capital']}'";
				$sql .= " where user_id='{$data['user_id']}}' and recover_period='{$repay_period}' and borrow_nid='{$data["borrow_nid"]}' and tender_id='{$tender_id}'";
				$mysql ->db_query($sql);
			}
            $sql = "select 1 from `{borrow_repay}` where user_id='{$roam_result["user_id"]}' and repay_period='{$repay_period}' and borrow_nid='{$data["borrow_nid"]}'";
			$result = $mysql->db_fetch_array($sql);
			if ($result==false){
				$sql = "insert into `{borrow_repay}` set `addtime` = '".time()."',";
				$sql .= "`addip` = '".ip_address()."',repay_type='wait',user_id='{$roam_result["user_id"]}',status=1,`borrow_nid`='{$data["borrow_nid"]}',`repay_period`='{$repay_period}',";
				$sql .= "`repay_time`='{$value['repay_time']}',`repay_account`='{$value['account_all']}',";
				$sql .= "`repay_interest`='{$value['account_interest']}',`repay_capital`='{$value['account_capital']}'";
				$mysql ->db_query($sql);
			}else{
				$sql = "update `{borrow_repay}` set `addtime` = '".time()."',";
				$sql .= "`addip` = '".ip_address()."',user_id='{$roam_result["user_id"]}',status=1,`borrow_nid`='{$data["borrow_nid"]}',`repay_period`='{$repay_period}',";
				$sql .= "`repay_time`='{$value['repay_time']}',`repay_account`='{$value['account_all']}',";
				$sql .= "`repay_interest`='{$value['account_interest']}',`repay_capital`='{$value['account_capital']}'";
				$sql .= " where user_id='{$roam_result["user_id"]}' and repay_period='{$repay_period}' and borrow_nid='{$data["borrow_nid"]}'";
				$mysql ->db_query($sql);
			}
		}
        //扣除投资人的还款金额
		$log_info["user_id"] = $data["user_id"];//操作用户id
		$log_info["nid"] = "tender_roam_".$data["borrow_nid"]."_".$data["user_id"]."_".$tender_id;//订单号
        $log_info["account_web_status"] = 0;//
        $log_info["account_user_status"] = 1;//
        $log_info["borrow_nid"] = $data["borrow_nid"];//收入
		$log_info["code"] = "borrow";//
		$log_info["code_type"] = "tender_roam";//
		$log_info["code_nid"] = $tender_id;//
		$log_info["money"] = $account;//操作金额
		$log_info["income"] = 0;//收入
		$log_info["expend"] = $log_info["money"];//支出
		$log_info["balance_cash"] = 0;//可提现金额
		$log_info["balance_frost"] = -$log_info["money"];//不可提现金额
		$log_info["frost"] = 0;//冻结金额
		$log_info["await"] = 0;//待收金额
		$log_info["repay"] = 0;//待还金额
		$log_info["type"] = "tender_roam";//类型
		$log_info["to_userid"] = 0;//付给谁
	    $log_info["remark"] = "购买【{$roam_result["username"]}】流转标[{$borrow_url}]{$data["portion"]}份成功金额扣除。";
		accountClass::AddLog($log_info);
        
        //扣除投资人的还款金额
		$log_info["user_id"] = $data["user_id"];//操作用户id
		$log_info["nid"] = "tender_roam_frost_".$data["borrow_nid"]."_".$data["user_id"]."_".$tender_id;//订单号
        $log_info["account_web_status"] = 0;//
        $log_info["account_user_status"] = 0;//
        $log_info["borrow_nid"] = $data["borrow_nid"];//收入
		$log_info["code"] = "borrow";//
		$log_info["code_type"] = "tender_roam_frost";//
		$log_info["code_nid"] = $tender_id;//
		$log_info["money"] = $recover_all;//操作金额
		$log_info["income"] = 0;//收入
		$log_info["expend"] = 0;//支出
		$log_info["balance_cash"] = 0;//可提现金额
		$log_info["balance_frost"] = 0;//不可提现金额
		$log_info["frost"] = 0;//冻结金额
		$log_info["await"] = $log_info["money"];//待收金额
		$log_info["repay"] = 0;//待还金额
		$log_info["type"] = "tender_roam_frost";//类型
		$log_info["to_userid"] = 0;//付给谁
	    $log_info["remark"] = "购买【{$roam_result["username"]}】流转标[{$borrow_url}]{$data["portion"]}份成功待收金额。";
		accountClass::AddLog($log_info);
        
		//推广奖励 		
		$_result=usersFriendsClass::GetUsersInviteOne(array("user_id"=>$data["user_id"]));
		$invite = isset($_G["system"]["con_invite_tender_award"])?$_G["system"]["con_invite_tender_award"]:2;
		$award_account = round($invite*$recover_interest_all/100,2);
		if ($_result['user_id']>0 ){
			$log_info["user_id"] = $_result['user_id'];//操作用户id
			$log_info["nid"] = "invite_award_add_".$data["borrow_nid"]."_".$data['user_id']."_".$tender_id;//订单号
			$log_info["account_web_status"] = 1;//
			$log_info["account_user_status"] = 1;//
			$log_info["borrow_nid"] = $data["borrow_nid"];//收入
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
			$log_info["remark"] =  "投资借款[{$borrow_url}]获得的推广奖励";
			accountClass::AddLog($log_info);
			
			$_invite['user_id']=$_result['user_id'];
			$_invite['tender_userid']=$data["user_id"];
			$_invite['tender_account']=$account;
			$_invite['tender_period']=$roam_result["borrow_period"];
			$_invite['tender_apr']=$roam_result["borrow_apr"];
			$_invite['award']=$award_account;
			usersClass::AddManageAccount($_invite);
		}

        // 投标奖励
        if(!empty($tender_id)){
            if($roam_result['award_status']>0 && $roam_result['award_false']==1){
                if($roam_result['award_status']==2){
                    $award = round($account*$roam_result['award_scale']/100,2);
                }
                if($roam_result['award_status']==1){
                    $award = $roam_result['award_account'];
                }
                if($award>0){
                    $log_info["user_id"] = $data['user_id'];//操作用户id
			        $log_info["nid"] = "brrow_award_add_".$data["borrow_nid"]."_".$data['user_id']."_".$tender_id;//订单号
			        $log_info["account_web_status"] = 1;//
			        $log_info["account_user_status"] = 1;//
			        $log_info["borrow_nid"] = $data["borrow_nid"];//收入
			        $log_info["code"] = "tender";//
			        $log_info["code_type"] = "brrow_tender_award";//
			        $log_info["code_nid"] = $tender_id;//
			        $log_info["money"] = $award;//操作金额
			        $log_info["income"] = $award;//收入
			        $log_info["expend"] = 0;//支出
			        $log_info["balance_cash"] = $award;//可提现金额
			        $log_info["balance_frost"] = 0;//不可提现金额
			        $log_info["frost"] = 0;//冻结金额
			        $log_info["await"] = 0;//待收金额
			        $log_info["type"] = "brrow_tender_award";//类型
			        $log_info["to_userid"] = $data['user_id'];//付给谁
			        $log_info["remark"] =  "投资借款[{$borrow_url}]获得的投资奖励";
			        accountClass::AddLog($log_info);
                    $remind['nid'] = "brrow_tender_award";
		            $remind['remind_nid'] = $remind['nid'].$_nid;
		            $remind['receive_userid'] = $data["user_id"];
		            $remind['article_id'] = $tender_id;
		            $remind['code'] = "borrow";
		            $remind['title'] = '投标奖励';
		            $remind['content'] = '你所投资的【'.$roam_result["username"].'】标['.$borrow_url.']在'.date('Y-m-d').'获得'.$award.'元奖励';
		            remindClass::sendRemind($remind);
                }
            }
        }
        // 续投奖励
	    if(!empty($tender_id)){
            $now = time();
            $start = empty($_G["system"]['continued_investment_min'])?0:strtotime($_G["system"]['continued_investment_min']);
            $end = empty($_G["system"]['continued_investment_max'])?0:strtotime($_G["system"]['continued_investment_max']);
            if($now>=$start && $now<$end){
                if($roam_result['continued_status']>0){
                    $sql = 'select sum(recover_account_yes-recover_fee) as recover_account from {borrow_recover} where user_id='.$data["user_id"].' and `recover_status`=1 and recover_yestime between '.(empty($account_result['continued_at'])?$start:$account_result['continued_at']).' and '.$now;
                    $replay_account = $mysql->db_fetch_array($sql);
                    $replay_account = empty($replay_account['recover_account'])?0:$replay_account['recover_account'];
                    $replay_account = $replay_account+$account_result['continued'];
                    $new_start=$mysql->db_fetch_array('select min(recover_yestime) as recover_yestime from {borrow_recover} where user_id='.$data["user_id"].' and `recover_status`=1 and recover_yestime between '.(empty($account_result['continued_at'])?$start:$account_result['continued_at']).' and '.$end);
                    $recover_start=empty($new_start['recover_yestime'])?(empty($account_result['continued_at'])?$start:$account_result['continued_at']):$new_start['recover_yestime'];
                    $sql = 'select sum(total) as total from {account_cash} where `user_id`='.$data["user_id"].' and (`status`=1 or `status`=0) and `addtime` between '.$recover_start.' and '.$now;
                    $cash = $mysql->db_fetch_array($sql);
                    $cash = empty($cash['total'])?0:$cash['total'];
                    $continued = $replay_account-$cash;
                    $continued_investment = min($continued,$account);
                    $sql = 'update {account} set continued='.max(($continued-$continued_investment),0).',continued_at='.$now.' where user_id='.$data['user_id'];
                    $mysql->db_query($sql);
                    if($roam_result['continued_min'] <= $continued and $continued>0){
                        if($roam_result['continued_status']==2){
                            $award = round($continued_investment*$roam_result['continued']/100,2);
                        }
                        if($roam_result['continued_status']==1){
                            $award = $roam_result['continued'];
                        }
                        if($award>0){
                            $log_info["user_id"] = $data['user_id'];//操作用户id
			                $log_info["nid"] = "continued_investment_award_add_".$data["borrow_nid"]."_".$data['user_id']."_".$tender_id;//订单号
			                $log_info["account_web_status"] = 1;//
			                $log_info["account_user_status"] = 1;//
			                $log_info["borrow_nid"] = $data["borrow_nid"];//收入
			                $log_info["code"] = "tender";//
			                $log_info["code_type"] = "continued_investment_award";//
			                $log_info["code_nid"] = $tender_id;//
			                $log_info["money"] = $award;//操作金额
			                $log_info["income"] = $award;//收入
			                $log_info["expend"] = 0;//支出
			                $log_info["balance_cash"] = $award;//可提现金额
			                $log_info["balance_frost"] = 0;//不可提现金额
			                $log_info["frost"] = 0;//冻结金额
			                $log_info["await"] = 0;//待收金额
			                $log_info["type"] = "continued_investment_award";//类型
			                $log_info["to_userid"] = $data['user_id'];//付给谁
			                $log_info["remark"] =  "投资借款[{$borrow_url}]获得的续投奖励";
			                accountClass::AddLog($log_info);
                            $remind['nid'] = "continued_investment_award";
		                    $remind['remind_nid'] = $remind['nid'].$_nid;
		                    $remind['receive_userid'] = $data["user_id"];
		                    $remind['article_id'] = $tender_id;
		                    $remind['code'] = "borrow";
		                    $remind['title'] = '续投奖励';
		                    $remind['content'] = '你所投资的【'.$roam_result["username"].'】标['.$borrow_url.']在'.date('Y-m-d').'获得'.$award.'元续投奖励';
		                    remindClass::sendRemind($remind);
                        }
                    }
                }
            }
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
        $_fee["borrow_type"] = $roam_result["borrow_type"];//借款类型
        $_fee["borrow_style"] = $roam_result["borrow_style"];//还款方式
        $_fee["type"] = "borrow_success";//属于借款者还是投资人
        $_fee["user_type"] = "tender";//属于借款者还是投资人
        $_fee["period"] = $roam_result["borrow_period"];//
        $_fee["capital"] = $account;//属于借款者还是投资人
        $_fee["interest"] = $recover_interest_all;//属于借款者还是投资人
        $result = borrowFeeClass::GetFeeValue($_fee);
        if ($result != false){
            foreach ($result as $key => $value){
                $log_info["user_id"] = $data["user_id"];//操作用户id
				$log_info["nid"] = "tender_roam_fee_".$data["borrow_nid"]."_".$data["user_id"]."_".$tender_id;//订单号
                $log_info["account_web_status"] = 1;//
                $log_info["account_user_status"] = 1;//
				$log_info["borrow_nid"] = $data['borrow_nid'];//收入
    			$log_info["code"] = "borrow";//
    			$log_info["code_type"] = "tender_roam_fee_".$data["borrow_nid"];//
    			$log_info["code_nid"] = $data["borrow_nid"];//
    			$log_info["money"] = $value['account'];//操作金额
				$log_info["income"] = 0;//收入
				$log_info["expend"] =  $log_info["money"];//支出
				$log_info["balance_cash"] = -$log_info["money"];//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
				$log_info["frost"] = 0;//冻结金额
				$log_info["await"] = 0;//待收金额
				$log_info["repay"] = 0;//待还金额
				$log_info["type"] = "tender_roam_fee_".$value["nid"];//类型
				$log_info["to_userid"] = 0;//付给谁
				$log_info["remark"] =  "购买【{$roam_result["username"]}】流转标[{$borrow_url}]，扣除{$log_info["money"]}元{$value['name']}";
				accountClass::AddLog($log_info);
            }
        }
        
        $sql = "select username from `{users}` where user_id='{$data['user_id']}'";
        $tender_user = $mysql->db_fetch_array($sql);
        //借款人街道标
		$log_info["user_id"] = $roam_result["user_id"];//操作用户id
		$log_info["nid"] = "borrow_roam_".$data["borrow_nid"]."_".$tender_id;//订单号
        $log_info["account_web_status"] = 0;//
        $log_info["account_user_status"] = 1;//
        $log_info["borrow_nid"] = $data["borrow_nid"];//收入
		$log_info["code"] = "borrow";//
		$log_info["code_type"] = "tender_roam";//
		$log_info["code_nid"] = $data["borrow_nid"];//
		$log_info["money"] = $account;//操作金额
		$log_info["income"] = $log_info["money"];//收入
		$log_info["expend"] = 0;//支出
		$log_info["balance_cash"] = $log_info["money"];//可提现金额
		$log_info["balance_frost"] = 0;//不可提现金额
		$log_info["frost"] = 0;//冻结金额
		$log_info["await"] = 0;//待收金额
		$log_info["repay"] = 0;//待还金额
		$log_info["type"] = "borrow_roam";//类型
		$log_info["to_userid"] = 0;//付给谁
	    $log_info["remark"] = "用户【{$tender_user["username"]}】购买流转标[{$borrow_url}]{$data["portion"]}份成功金额增加。";
		accountClass::AddLog($log_info);
        //扣除费用
        //判断是否是vip
        $vip_status =0;
        $vip_result=usersClass::GetUsersVip(array("user_id"=>$roam_result["user_id"]));
        if($vip_result==true){
             $vip_status = $vip_result['status'];
        }
        $credit_result = borrowClass::GetBorrowCredit(array("user_id"=>$roam_result["user_id"]));
        $_fee["vip_status"] = $vip_status;//判断是不是vip
        $_fee["credit_fee"] =$credit_result['credit']['fee'];//判断是不是vip
        $_fee["borrow_type"] = $roam_result["borrow_type"];//借款类型
        $_fee["borrow_style"] = $roam_result["borrow_style"];//还款方式
        $_fee["type"] = "borrow_success";//属于借款者还是投资人
        $_fee["user_type"] = "borrow";//属于借款者还是投资人
        $_fee["capital"] = $account;//属于借款者还是投资人
        $_fee["period"] = $roam_result["borrow_period"];//
        $_fee["interest"] = $recover_interest_all;//属于借款者还是投资人
        $result = borrowFeeClass::GetFeeValue($_fee);
        if ($result != false){
            foreach ($result as $key => $value){
                $log_info["user_id"] = $roam_result["user_id"];//操作用户id
				$log_info["nid"] = "borrow_roam_fee_".$data["borrow_nid"]."_".$data["user_id"]."_".$tender_id;//订单号
                $log_info["account_web_status"] = 1;//
                $log_info["account_user_status"] = 1;//
				$log_info["borrow_nid"] = $data['borrow_nid'];//收入
    			$log_info["code"] = "borrow";//
    			$log_info["code_type"] = "borrow_roam_fee_".$value["nid"];//
    			$log_info["code_nid"] = $tender_id;//
    			$log_info["money"] = $value['account'];//操作金额
				$log_info["income"] = 0;//收入
				$log_info["expend"] =  $log_info["money"];//支出
				$log_info["balance_cash"] = -$log_info["money"];//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
				$log_info["frost"] = 0;//冻结金额
				$log_info["await"] = 0;//待收金额
				$log_info["repay"] = 0;//待还金额
				$log_info["type"] = "borrow_roam_fee_".$value["nid"];//类型
				$log_info["to_userid"] = 0;//付给谁
				$log_info["remark"] =  "用户[{$tender_user["username"]}]购买流转标[{$borrow_url}]成功，扣除{$log_info["money"]}元{$value['name']}";
				accountClass::AddLog($log_info);
            }
        }
        
        
        //投资信用积分
       
		$credit_log['user_id'] = $data['user_id'];
		$credit_log['nid'] = "tender_success";
		$credit_log['code'] = "tender";
		$credit_log['type'] = "成功投资{$account}所得的积分";
		$credit_log['addtime'] = time();
		$credit_log['article_id'] =$tender_id;
		$credit_log['value'] = round($account/100);
		$result = creditClass::ActionCreditLog($credit_log);
			
		//加入用户操作记录
		$user_log["user_id"] = $data['user_id'];
		$user_log["code"] = "tender";
		$user_log["type"] = "tender_success";
		$user_log["operating"] = "tender";
		$user_log["article_id"] =$tender_id;
		$user_log["result"] = 1;
		$user_log["content"] = "流转标[{$borrow_url}]投标成功。";
		usersClass::AddUsersLog($user_log);	
			
		$sql = "update `{borrow_tender}` set status=1,recover_account_all='{$recover_all}',recover_account_interest='{$recover_interest_all}',recover_account_wait=recover_account_all-recover_account_yes,recover_account_interest_wait=recover_account_interest-recover_account_interest_yes,recover_account_capital_wait='{$account}'  where id='{$tender_id}'";
		$mysql->db_query($sql);
		
        $sql = "update `{borrow_roam}` set recover_wait=recover_wait+{$data['portion']},portion_yes=portion_yes+{$data['portion']},portion_wait=portion_wait-{$data['portion']} where borrow_nid='{$data['borrow_nid']}'";
        $mysql->db_query($sql);
        
        $sql = "update  `{borrow}`  set borrow_account_yes=borrow_account_yes+{$account},borrow_account_wait=borrow_account_wait-{$account},borrow_account_scale=(borrow_account_yes/account)*100,tender_times=tender_times+1  where borrow_nid='{$data['borrow_nid']}'";
		$mysql->db_query($sql);
                
		//更新统计信息
        $tender_account = $account;
		borrowCountClass::UpdateBorrowCount(array("user_id"=>$data["user_id"],"borrow_nid"=>"{$data['borrow_nid']}","nid"=>$_nid,"tender_success_times"=>1,"tender_success_account"=>$tender_account,"tender_frost_account"=>-$tender_account,"tender_recover_account"=>$recover_all,"tender_recover_wait"=>$recover_all,"tender_capital_account"=>$recover_capital_all,"tender_capital_wait"=>$recover_capital_all,"tender_interest_account"=>$recover_interest_all,"tender_interest_wait"=>$recover_interest_all,"tender_recover_times"=>0,"tender_recover_times_wait"=>1));
		
        $_equal["type"] = "all";
		$equal_result = borrowCalculateClass::GetType($_equal);
		
		$nowtime= time();
		$endtime = get_times(array("num"=>$borrow_result["borrow_period"],"time"=>$nowtime));
		$_each_time = "每月".date("d",$nowtime)."日";
    	$nexttime = get_times(array("num"=>1,"time"=>$nowtime));
		
        $sql = "update `{borrow}` set borrow_full_status=1,status=3,borrow_period_roam=borrow_period_roam+1,repay_account_all=repay_account_all+'{$equal_result['account_total']}',repay_account_interest=repay_account_interest+'{$equal_result['interest_total']}',repay_account_capital=repay_account_capital+'{$equal_result['capital_total']}',repay_account_wait=repay_account_wait+'{$equal_result['account_total']}',repay_account_interest_wait=repay_account_interest_wait+'{$equal_result['interest_total']}',repay_account_capital_wait=repay_account_capital_wait+'{$equal_result['capital_total']}',repay_last_time='{$endtime}',repay_next_time='{$nexttime}',borrow_success_time='{$nowtime}',repay_each_time='{$_each_time}' where borrow_nid='{$data["borrow_nid"]}'";
          $mysql->db_query($sql);
		//投资者接收
		$remind['nid'] = "tender_roam_success";
		$remind['remind_nid'] = $_nid;
		$remind['receive_userid'] = $data["user_id"];
		$remind['article_id'] = $tender_id;
		$remind['code'] = "borrow";
		$remind['title'] = "投资{$roam_result["borrow_username"]}[{$roam_result["borrow_name"]}]审核成功";
		$remind['content'] = "你所投资的标[{$borrow_url}]在".date("Y-m-d",time())."已经审核通过";
		remindClass::sendRemind($remind);
				
        
		//借款标发布者接收
		$remind['nid'] = "borrow_roam_success";
        $remind['remind_nid'] =  "borrow_roam_success_".$data["borrow_nid"]."_".$roam_result["user_id"]."_".$tender_id;
		$remind['receive_userid'] = $roam_result['user_id'];
		$remind['article_id'] = $data['borrow_nid'];
		$remind['code'] = "borrow";
		$remind['title'] = "流转标[{$roam_result["borrow_name"]}]借款成功";
		$remind['content'] = "流转标[{$borrow_url}]在".date("Y-m-d",time())."借款成功";
		remindClass::sendRemind($remind);
        
        
        return array(1);
    }
    
    function GetUserRoamCount($data = array()){
        global $mysql;
        if ($data["user_id"]==""){
            return "";
        }
        $_result = array();
        
        $sql = "select sum(repay_account) as anum,sum(repay_interest) as inum,count(1) as num from `{borrow_repay}` as p1 left join `{borrow}` as p2 on p1.borrow_nid=p2.borrow_nid where p2.borrow_type='roam'  and p1.user_id='{$data['user_id']}' ";
        //
        //最近一个月成功流转
        $month_time = strtotime("-1 month",time());
        $half_time = strtotime("-6 month",time());
        $year_time = strtotime("-12 month",time());
        $month_sql = " and p1.addtime>{$month_time}";//月
        $half_sql = " and p1.addtime>{$half_time}";//半年
        $year_sql = " and p1.addtime>{$year_time}";//一年
        $recover_sql = " and p1.repay_status=1"; //成功回购
        $late_sql = " and p1.repay_status=0 and p1.repay_time<".time();//逾期
        
        //全部
        $result = $mysql->db_fetch_array($sql);
        $_result["all"]["success_account"] = 0;
        $_result["all"]["success_interest"] = 0;
        $_result["all"]["success_num"] = 0;
        if ($result["num"]>0){
            $_result["all"]["success_account"] = $result["anum"];
            $_result["all"]["success_interest"] = $result["inum"];
            $_result["all"]["success_num"] = $result["num"];
        }
        
       
        $result = $mysql->db_fetch_array($sql.$recover_sql);
        $_result["all"]["recover_account"] = 0;
        $_result["all"]["recover_num"] = 0;
        if ($result["num"]>0){
            $_result["all"]["recover_account"] = $result["anum"];
            $_result["all"]["recover_num"] = $result["num"];
        }
        
        //逾期违约        
        $result = $mysql->db_fetch_array($sql.$late_sql);
        $_result["all"]["late_account"] = 0;
        $_result["all"]["late_num"] = 0;
       
        if ($result["num"]>0){
            $_result["all"]["late_account"] = $result["anum"];
            $_result["all"]["late_num"] = $result["num"];
        }
       
        $_result["all"]["late_apr"] = 0;
        if ($_result["all"]["success_account"]>0){
            $_result["all"]["late_apr"] = round(($_result["all"]["late_account"]/$_result["all"]["success_account"])*100,2);
        }
        
        
        //月
        $result = $mysql->db_fetch_array($sql.$month_sql);
        $_result["month"]["success_account"] = 0;
        $_result["month"]["success_interest"] = 0;
        $_result["month"]["success_num"] = 0;
        if ($result["num"]>0){
            $_result["month"]["success_account"] = $result["anum"];
            $_result["month"]["success_interest"] = $result["inum"];
            $_result["month"]["success_num"] = $result["num"];
        }
        
       
        $result = $mysql->db_fetch_array($sql.$month_sql.$recover_sql);
        $_result["month"]["recover_account"] = 0;
        $_result["month"]["recover_num"] = 0;
        if ($result["num"]>0){
            $_result["month"]["recover_account"] = $result["anum"];
            $_result["month"]["recover_num"] = $result["num"];
        }
        
        //逾期违约        
        $result = $mysql->db_fetch_array($sql.$month_sql.$late_sql);
        $_result["month"]["late_account"] = 0;
        $_result["month"]["late_num"] = 0;
       
        if ($result["num"]>0){
            $_result["month"]["late_account"] = $result["anum"];
            $_result["month"]["late_num"] = $result["num"];
        }
       
        $_result["month"]["late_apr"] = 0;
        if ($_result["month"]["success_account"]>0){
            $_result["month"]["late_apr"] = round(($_result["month"]["late_account"]/$_result["month"]["success_account"])*100,2);
        }
        
        //半年
        $result = $mysql->db_fetch_array($sql.$half_sql);
        $_result["half"]["success_account"] = 0;
        $_result["half"]["success_interest"] = 0;
        $_result["half"]["success_num"] = 0;
        if ($result["num"]>0){
            $_result["half"]["success_account"] = $result["anum"];
            $_result["half"]["success_interest"] = $result["inum"];
            $_result["half"]["success_num"] = $result["num"];
        }
        
       
        $result = $mysql->db_fetch_array($sql.$half_sql.$recover_sql);
        $_result["half"]["recover_account"] = 0;
        $_result["half"]["recover_num"] = 0;
        if ($result["num"]>0){
            $_result["half"]["recover_account"] = $result["anum"];
            $_result["half"]["recover_num"] = $result["num"];
        }
        
        //逾期违约        
        $result = $mysql->db_fetch_array($sql.$half_sql.$late_sql);
        $_result["half"]["late_account"] = 0;
        $_result["half"]["late_num"] = 0;
       
        if ($result["num"]>0){
            $_result["half"]["late_account"] = $result["anum"];
            $_result["half"]["late_num"] = $result["num"];
        }
       
        $_result["half"]["late_apr"] = 0;
        if ($_result["half"]["success_account"]>0){
            $_result["half"]["late_apr"] = round(($_result["half"]["late_account"]/$_result["half"]["success_account"])*100,2);
        }
        
        
        //年
        $result = $mysql->db_fetch_array($sql.$half_sql);
        $_result["year"]["success_account"] = 0;
        $_result["year"]["success_interest"] = 0;
        $_result["year"]["success_num"] = 0;
        if ($result["num"]>0){
            $_result["year"]["success_account"] = $result["anum"];
            $_result["year"]["success_interest"] = $result["inum"];
            $_result["year"]["success_num"] = $result["num"];
        }
        
       
        $result = $mysql->db_fetch_array($sql.$year_sql.$recover_sql);
        $_result["year"]["recover_account"] = 0;
        $_result["year"]["recover_num"] = 0;
        if ($result["num"]>0){
            $_result["year"]["recover_account"] = $result["anum"];
            $_result["year"]["recover_num"] = $result["num"];
        }
        
        //逾期违约        
        $result = $mysql->db_fetch_array($sql.$year_sql.$late_sql);
        $_result["year"]["late_account"] = 0;
        $_result["year"]["late_num"] = 0;
       
        if ($result["num"]>0){
            $_result["year"]["late_account"] = $result["anum"];
            $_result["year"]["late_num"] = $result["num"];
        }
       
        $_result["year"]["late_apr"] = 0;
        if ($_result["year"]["success_account"]>0){
            $_result["year"]["late_apr"] = round(($_result["year"]["late_account"]/$_result["year"]["success_account"])*100,2);
        }
        return $_result;
        
    }
    
       /**
	 * 修改借款标
	 *
	 * @param Array $data
	 * @return Boolen
	 */
    function  UpdateRoam($data = array()){
        global $mysql,$_G;
        
        if ($data['borrow_nid']==""){
            return "borrow_loan_nid_error";
        }
       	$sql = "update `{borrow_roam}` set ";
		foreach($data as $key => $value){
			$_sql[] .= "`$key` = '$value'";
		}
        $__sql = $sql.(join(",",$_sql))." where borrow_nid='{$data['borrow_nid']}'";
       $result = $mysql->db_query($__sql);
		return $data['borrow_nid'];	
    }
}