<?php
/******************************
 * $File: borrow.COUNT.php
 * $Description: 借款统计文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

class borrowCountClass
{
	
	
	/**
	 * 用户投资按月统计,只统计借款成功标，也就是满标审核成功的投资的资金，满标未审核成功，以及待审核的投资都不统计。
	 *
	 * @return Array
	 */
 
 
	//data = array("user_id"=>"");
	public static function UpdateBorrowCount($data = array()){
		global $mysql;
		if ($data['user_id']=="") return "";
		$user_id =$data['user_id'];
		$result = $mysql->db_fetch_array("select 1 from `{borrow_count}` where user_id='{$data['user_id']}'");
		if ($result==false){
			$sql= "insert into `{borrow_count}` set user_id='{$data['user_id']}'";
			$mysql->db_query($sql);
			
		}
        $sql = "select 1 from `{borrow_count_log}` where nid='{$data['nid']}'";
        $result = $mysql->db_fetch_array($sql);
        if ($result!=false){
            return "";
        }
        
        $remark = serialize($data);
        $sql = "insert into `{borrow_count_log}` set user_id={$data['user_id']},borrow_nid='{$data['borrow_nid']}',nid='{$data['nid']}',remark='{$remark}',addtime='".time()."'";
        $mysql->db_query($sql);
        unset($data['nid']);
        unset($data['borrow_nid']);
        
        
		$sql = "update `{borrow_count}` set user_id='{$data['user_id']}'";
		unset ($data['user_id']);
		foreach ($data as $key => $value){
			$sql .= ",`{$key}`=`{$key}`+{$value}";
		}
		$sql .= " where user_id='{$user_id}'";
		$mysql->db_query($sql);
		return "";		
	}
	
	 
	public static function GetUserTenderMonth($data = array()){
		global $mysql;
		
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?1000:$data['epage'];
		$_sql = " where 1=1 ";
		$now_time = strtotime(date("Y-m-1",time()));
		$up_month = strtotime("-1 month",$now_time);
		
		//$now_time = strtotime("2011-08-11");
		//$up_month =  strtotime("2011-08-01")
		$_sql .= " and p1.addtime >= {$up_month} and p1.addtime < {$now_time}";
		$_select = "sum(p1.account) as account_num ,count(1) as count_num,p1.user_id,p2.username";
		$sql = "select SELECT from `{borrow_tender}` as p1,`{user}` as p2,`{borrow}` as p3
			 SQL and p1.user_id=p2.user_id and p1.borrow_nid =p3.borrow_nid and p3.status=3 group by p1.user_id LIMIT";
		$row = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT', 'SQL'), array('count(1) as num', '', '', $_sql), $sql));
		$total = $row['num'];
		$total_page = ceil($total / $epage);
		$index = $epage * ($page - 1);
		//$limit = " limit {$index}, {$epage}";
		$first_time = time();
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT', 'SQL'), array($_select, '', $limit, $_sql), $sql));	
		
			
		$_result = $list?$list:array();
		return array(
            'list' => $_result,
            'total' => $total,
            'page' => $page,
            'epage' => $epage,
            'total_page' => $total_page
        );
	
	}
	public static function GetUserTenderMonth1($data = array()){
		global $mysql;
		$user_id = empty($data['user_id'])?"":$data['user_id'];
		$username = empty($data['username'])?"":$data['username'];
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?30:$data['epage'];
		
		$_sql = "where 1=1 and p1.borrow_nid in (select borrow_nid from `{borrow}` where status=3) ";		 
		if (IsExiest($user_id)!=""){
			$_sql .= " and p1.user_id = $user_id";
		}
		if (IsExiest($username)!=""){
			$_sql .= " and p2.username like '%$username%'";
		}
		
		if (IsExiest($data['dotime2'])!=""){
			$dotime2 = ($data['dotime2']=="request")?$_REQUEST['dotime2']:$data['dotime2'];
			if ($dotime2!=""){
				$_sql .= " and p1.addtime < ".get_mktime($dotime2);
			}
		}
		if (IsExiest($data['dotime1'])!=""){
			$dotime1 = ($data['dotime1']=="request")?$_REQUEST['dotime1']:$data['dotime1'];
			if ($dotime2!=""){
				$_sql .= " and p1.addtime > ".get_mktime($dotime1);
			}
		}
		if (IsExiest($data['status'])!=""){
			$_sql .= " and p1.status in ({$data['status']})";
		}
		//第一步，搜索最新的投资记录
		$sql = "select * from `{borrow_tender}` where borrow_nid in (select borrow_nid from `{borrow}` where status=3) order by addtime  ";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return;
		$_first_time = $result['addtime'];
		$first_date = date("Y-m-1",$_first_time);
		$last_date = date("Y-m-1",time());
		$first_time = strtotime($first_date);//最开始的时间	
		$last_time = strtotime($last_date);//最近的时间
		$month_num = dateMonthDiff($first_date,$last_date);
		
		$_select = "sum(account) as account_num ,count(1) as count_num,p2.username";
		$sql = "select SELECT from `{borrow_tender}` as p1
				left join `{user}` as p2 on p2.user_id = p1.user_id
			 SQL  group by p1.user_id LIMIT";
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT', 'SQL'), array('count(1) as num', '', '', $_sql), $sql));
		$j =1;
		if ($month_num>12) $j=12;
		$total = $row['num'];
		$total_page = ceil($total / $epage);
		$index = $epage * ($page - 1);
		$limit = " limit {$index}, {$epage}";
		for($i=$j;$i<$month_num; $i++){
			$up_month = strtotime("$i month",$first_time);
			$now_month = strtotime("-1 month",$up_month);
			$nowlast_day = strtotime("0 day",$up_month);
			$_sql .= " and p1.addtime >= {$now_month} and p1.addtime < {$nowlast_day}";
			$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT', 'SQL'), array($_select, '', $limit, $_sql), $sql));	
			if (count($result)>0){
				$_result[date("Y-n",$now_month)] = $list;
			}
		}	
		$_result = $_result?$_result:array();
		
		return array(
            'list' => $_result,
            'total' => $total,
            'page' => $page,
            'epage' => $epage,
            'total_page' => $total_page
        );
		
	}
	
	public static function GetBorrowRepayCount(){
		global $mysql;
		$sql = "select repay_status,sum(repay_account) as repay_account_all,sum(repay_capital) as repay_capital_all,sum(repay_interest) as repay_interest_all,sum(late_interest) as late_interest_all,sum(late_reminder) as late_reminder_all  from `{borrow_repay}`  where status=1 group by repay_status";
		$result = $mysql->db_fetch_arrays($sql);
		if ($result!=false){
			
			foreach ($result as $key => $value){
				if ($value['repay_status']==1){
					$_result['repay_account_all_yes']= $value['repay_account_all'];//已还款的本息
					$_result['repay_capital_all_yes']= $value['repay_capital_all'];//已还款的本金
					$_result['repay_interest_all_yes']= $value['repay_interest_all'];//已还款的利息
					$_result['late_interest_all_yes']= $value['late_interest_all'];//已还款的逾期利息
					$_result['late_reminder_all_yes']= $value['late_reminder_all'];//已还款的逾期滞纳金
				}else{
					$_result['repay_account_all_no']= $value['repay_account_all'];//未还款的本息
					$_result['repay_capital_all_no']= $value['repay_capital_all'];//未还款的本金
					$_result['repay_interest_all_no']= $value['repay_interest_all'];//未还款的利息
					$_result['late_interest_all_no']= $value['late_interest_all'];//未还款的逾期利息
					$_result['late_reminder_all_no']= $value['late_reminder_all'];//未还款的逾期滞纳金
				}
			}
			$_result['repay_account_all']= $_result['repay_account_all_yes']+$_result['repay_account_all_no'];//总还款的本息
			$_result['repay_capital_all']= $_result['repay_capital_all_yes']+$_result['repay_capital_all_no'];//总还款的本金
			$_result['repay_interest_all']= $_result['repay_interest_all_yes']+$_result['repay_interest_all_no'];//总还款的利息
			$_result['late_interest_all']= $_result['late_interest_all_yes']+$_result['late_interest_all_no'];//总还款的逾期利息
			$_result['late_reminder_all']= $_result['late_reminder_all_yes']+$_result['late_reminder_all_no'];//总还款的逾期滞纳金
		}
		
		//已经还款
		$sql = "select *  from `{borrow_repay}` where status=1 and repay_status=1";
		$result = $mysql->db_fetch_arrays($sql);
		foreach ($result as $key => $value){
			//逾期的总金额
			$repay_time = date("Ymd",$value['repay_time']);
			$repay_yestime = date("Ymd",$value['repay_yestime']);
			if ($repay_yestime>$repay_time){
				$_result['repay_account_all_yes_late_yes'] += $value['repay_account'];//已还款逾期的本息
				$_result['repay_capital_all_yes_late_yes'] += $value['repay_capital'];//已还款逾期的本尽
				$_result['repay_interest_all_yes_late_yes'] += $value['repay_interest'];//已还款逾期的利息
				$_result['late_interest_all_yes_late_yes'] += $value['late_interest'];//已还款逾期的罚金
				$_result['late_reminder_all_yes_late_yes'] += $value['late_reminder'];//已还款逾期的滞纳金
				if ($value['repay_web']==1){
					$_result['repay_account_all_yes_web_yes'] += $value['repay_account'];//已还款网站垫付的金额
				}else{
					$_result['repay_account_all_yes_web_no'] += $value['repay_account'];//已还款网站未垫付的金额
				}
				if ($value['repay_vouch']==1){
					$_result['repay_account_all_yes_vouch'] += $value['repay_account'];//已还款担保垫付的金额
				}
			}else{
				$_result['repay_account_all_yes_late_no'] += $value['repay_account'];//已还款未逾期的本息
				$_result['repay_capital_all_yes_late_no'] += $value['repay_capital'];//已还款未逾期的本金
				$_result['repay_interest_all_yes_late_no'] += $value['repay_interest'];//已还款未逾期的利息
				$_result['late_interest_all_yes_late_no'] += $value['late_interest'];//已还款未逾期的罚金
				$_result['late_reminder_all_yes_late_no'] += $value['late_reminder'];//已还款未逾期的滞纳金
			
			}
			
		}
		
		//未还款逾期的金额
		$sql = "select *  from `{borrow_repay}`  where status=1 and repay_status=0 ";
		$result = $mysql->db_fetch_arrays($sql);
		foreach ($result as $key => $value){
			//逾期的总金额
			$repay_time = date("Ymd",$value['repay_time']);
			$repay_yestime = date("Ymd",time());
			if ($repay_yestime>$repay_time){
				$_result['repay_account_all_no_late_yes'] += $value['repay_account'];//未还款逾期的本息
				$_result['repay_capital_all_no_late_yes'] += $value['repay_capital'];//未还款逾期的本尽
				$_result['repay_interest_all_no_late_yes'] += $value['repay_interest'];//未还款逾期的利息
				if ($value['repay_web']==1){
					$_result['repay_account_all_no_web_yes'] += $value['repay_account'];//未还款网站垫付的金额
				}else{
					$_result['repay_account_all_no_web_no'] += $value['repay_account'];//未还款网站垫付的金额
				}
			}else{
				$_result['repay_account_all_no_late_no'] += $value['repay_account'];//未还款未逾期的本息
				$_result['repay_capital_all_no_late_no'] += $value['repay_capital'];//未还款未逾期的本金
				$_result['repay_interest_all_no_late_no'] += $value['repay_interest'];//未还款未逾期的利息
			}
			if ($value['repay_vouch']==1){
				$_result['repay_account_all_no_vouch'] += $value['repay_account'];//未还款担保垫付的金额
			}
		}
		$_result['repay_account_all_web_yes'] = $_result['repay_account_all_yes_web_yes'] + $_result['repay_account_all_no_web_yes'];//网站垫付的总金额
		$_result['repay_account_all_web_no'] = $_result['repay_account_all_yes_web_no'] + $_result['repay_account_all_no_web_no'];//网站未垫付的总金额
		
		return $_result;
	}
	
	
	public static function GetKefuBorrowCount(){
		global $mysql;
		
		//借款统计
		$sql = "select p1.reverify_time from `{borrow}` as p1 where p1.status=3 order by p1.reverify_time asc";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false){
			$first_time = strtotime(date("Y-m-1",$result["reverify_time"]));
			$last_time = time();
			$month_num = GetMonthNum($first_time,$last_time);
			//成功借款
			for ($i=0;$i<=$month_num;$i++){
				$_first_time =  strtotime("$i month",$first_time);
				$_last_time =  strtotime("1 month",$_first_time);
				$sql = "select sum(p1.account) as borrow_num,p2.kefu_userid,p4.username from `{borrow}` as p1 left join `{borrow_vip}` as p2 on p1.user_id =p2.user_id left join `{user}` as p4 on p2.kefu_userid=p4.user_id where p1.status=3 and p1.reverify_time>=$_first_time and p1.reverify_time<$_last_time and p2.kefu_userid>0 group by p2.kefu_userid";
				$result = $mysql->db_fetch_arrays($sql);
				if (count($result)>0){
					$_result[date("Y-n",$_first_time)] = $result;
				}
				
			}
		}
		return $_result;
	}
	
	
	public static function GetKefuTenderCount(){
		global $mysql;
		
		//借款统计
		$sql = "select p1.reverify_time from `{borrow}` as p1 where p1.status=3 order by p1.reverify_time asc";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false){
			$first_time = strtotime(date("Y-m-1",$result["reverify_time"]));
			$last_time = time();
			$month_num = GetMonthNum($first_time,$last_time);
			//成功借款
			for ($i=0;$i<=$month_num;$i++){
				$_first_time =  strtotime("$i month",$first_time);
				$_last_time =  strtotime("1 month",$_first_time);
				
				$sql = "select sum(p1.account) as tender_num,p2.kefu_userid,p4.username from `{borrow_tender}` as p1 left join `{borrow_vip}` as p2 on p1.user_id =p2.user_id left join `{borrow}` as p3 on p1.borrow_nid=p3.borrow_nid left join `{user}` as p4 on p2.kefu_userid=p4.user_id  where p1.status=1 and p2.kefu_userid>0 and p3.reverify_time>=$_first_time and p3.reverify_time<$_last_time and p3.status=3 group by p2.kefu_userid";
				$result = $mysql->db_fetch_arrays($sql);
				if (count($result)>0){
					$_result[date("Y-n",$_first_time)] = $result;
				}
				
			}
		}
		return $_result;
	}
	
	
	public static function BorrowRemindCount(){
		global $mysql;
		//满标审核
		$sql = "select count(1) as num from `{borrow}` where status=1 and borrow_account_yes = account";
		$result = $mysql->db_fetch_array($sql);
		$_result['borrow_full_check'] = $result['num'];
		
		//发标审核
		$sql = "select count(1) as num from `{borrow}` where status=0";
		$result = $mysql->db_fetch_array($sql);
		$_result['borrow_publish_wait'] = $result['num'];
		
		//额度
		$sql = "select count(1) as num from `{borrow_amount_apply}` where status=0";
		$result = $mysql->db_fetch_array($sql);
		$_result['borrow_amount_apply'] = $result['num'];
		
			
		//未还款
		$sql = "select count(1) as num from `{borrow_repay}` where repay_status=0";
		$result = $mysql->db_fetch_array($sql);
		$_result['borrow_repay_not'] = $result['num'];
		
		//未还款
		$sql = "select count(1) as num from `{borrow_repay}` where repay_status=0 and repay_time<".time();
		$result = $mysql->db_fetch_array($sql);
		$_result['borrow_repay_late_not'] = $result['num'];
		
		//身份认证
		$sql = "select count(1) as num  from `{approve_realname}` where status=0";
		$result = $mysql->db_fetch_array($sql);
		$_result['real_status'] = $result['num'];
		
		//学历认证
		
		$sql = "select count(1) as num  from `{approve_edu}` where status=0";
		$result = $mysql->db_fetch_array($sql);
		$_result['edu_status'] = $result['num'];
		
		//视频认证
		
		$sql = "select count(1) as num  from `{approve_video}` where status=0";
		$result = $mysql->db_fetch_array($sql);
		$_result['video_status'] = $result['num'];
		
		//vip申请
		
		$sql = "select count(1) as num  from `{users_vip}` where status=0";
		$result = $mysql->db_fetch_array($sql);
		$_result['vip_status'] = $result['num'];
		
		//额度申请
		$sql = "select count(1) as num  from `{borrow_amount_apply}` where status=0";
		$result = $mysql->db_fetch_array($sql);
		$_result['amount_status'] = $result['num'];
		
		
		
		//资料审核
	
		$sql = "select count(1) as num  from `{attestations}` where status=0";
		$result = $mysql->db_fetch_array($sql);
		$_result['attestation_status'] = $result['num'];
		
		//充值
	
		$sql = "select count(1) as num  from `{account_cash}` where status=0";
		$result = $mysql->db_fetch_array($sql);
		$_result['cash'] = $result['num'];
		
		//提现
	
		$sql = "select count(1) as num  from `{account_recharge}` where status=0";
		$result = $mysql->db_fetch_array($sql);
		$_result['recharge'] = $result['num'];
		
		//实名
	
		$sql = "select count(1) as num  from `{approve_realname}` where status=0";
		$result = $mysql->db_fetch_array($sql);
		$_result['approve_realname'] = $result['num'];
		$sql = "select count(1) as num  from `{approve_video}` where status=0";
		$result = $mysql->db_fetch_array($sql);
		$_result['approve_video'] = $result['num'];
		$sql = "select count(1) as num  from `{approve_scene}` where status=0";
		//$result = $mysql->db_fetch_array($sql);
		$_result['approve_scene'] = $result['num'];
		$sql = "select count(1) as num  from `{approve_sms}` where status=0";
		$result = $mysql->db_fetch_array($sql);
		$_result['approve_sms'] = $result['num'];
		$sql = "select count(1) as num  from `{approve_edu}` where status=0";
		$result = $mysql->db_fetch_array($sql);
		$_result['approve_edu'] = $result['num'];
		
		
		$sql = "select count(1) as num  from `{attestations}` where status=0";
		$result = $mysql->db_fetch_array($sql);
		$_result['attestations'] = $result['num'];
		
		return $_result;
	
	}
    
    	//data = array("user_id"=>"");
	public static function GetUsersCount($data = array()){
		global $mysql;
		if ($data['user_id']=="") return "";
        $sql = "select * from  `{borrow_count}` where user_id='{$data['user_id']}'";
        $result = $mysql->db_fetch_array($sql);
        return $result;
        
  }
  
	 public static function GetUsersTodayCount($data = array()){
		global $mysql;
		if ($data['user_id']=="") return "";
		$year = date("Y",time());
		$month = date("m",time());
		$day = date("d",time());
		//mktime(hour,minute,second,month,day,year,is_dst)
		$today = mktime(23,59,59,$month,$day,$year);
		
		$sql = "select count(*) as repay_num from  `{borrow_repay}` where user_id='{$data['user_id']}' and repay_status=0 and repay_time <= $today";
		$repay = $mysql->db_fetch_array($sql);
		
		$sql = "select count(*) as recover_num from  `{borrow_recover}` where user_id='{$data['user_id']}' and recover_status=0 and recover_time <= $today";
		$recover = $mysql->db_fetch_array($sql);
		
		$_result['repay_num'] = $repay['repay_num']; 
		$_result['recover_num'] = $recover['recover_num']; 
		return $_result;		
	  }
  
   //投资人借出统计
	public static function GetUsersRecoverCount($data = array()){
	   	global $mysql;
		if ($data['user_id']=="") return "";
        $result = array();
        $sql = "select recover_status,sum(recover_account) as anum,sum(recover_capital) as cnum,sum(recover_interest) as inum,sum(recover_interest_yes) as iynum,count(1) as num,count(distinct borrow_nid) as times  from  `{borrow_recover}` where user_id='{$data['user_id']}' and status=1 group by recover_status";
        $result = $mysql->db_fetch_arrays($sql);
        
        //借出的统计
        $_result = array();
        $_result["tender_interest_account"] = 0;
        foreach ($result as $key => $value){
            if ($value["recover_status"]==1){
                $_result["recover_yes_account"] = $value["anum"];//已收总额
                $_result["recover_yes_capital"] = $value["cnum"];//已收本金总额
                $_result["recover_yes_interest"] = $value["iynum"];//已收利息
                $_result["recover_yes_num"] = $value["num"];//已收期数
                $_result["recover_yes_times"] = $value["times"];//已收笔数
            }elseif ($value["status"]==0){
                $_result["recover_wait_account"] = $value["anum"];//未收总额
                $_result["recover_wait_capital"] = $value["cnum"];//未收本金总额
                $_result["recover_wait_interest"] = $value["inum"];//未收利息
                $_result["recover_wait_num"] = $value["num"];//未收期数
                $_result["recover_wait_times"] = $value["times"];//未收笔数
            }
            $_result["tender_interest_account"] += $value["inum"];//总利息
            $_result["tender_account"] += $value["anum"];//借出总额
        }
        
        //最近待收
        $result = array();
        $sql = "select * from `{borrow_recover}` where user_id='{$data['user_id']}' and recover_status=0 order by recover_time asc";
        $result = $mysql->db_fetch_array($sql);
        if ($result!=false){
             $_result["recover_wait_now_account"] = $result["recover_account"];//最近待收总额
             $_result["recover_wait_now_interest"] = $result["recover_interest"];//最近待收利息
             $_result["recover_wait_now_time"] = $result["recover_time"];//最近待收时间
        }
        
        //总借出统计
        $result = array();
        $sql = "select status,sum(account) as anum,count(1) as num  from `{borrow_tender}`  where user_id='{$data['user_id']}' group by status";
        $result = $mysql->db_fetch_arrays($sql);
        if ($result!=false){
             foreach ($result as  $key => $value){
                if ($value["status"]==1){
                    $_result["tender_success_account"] = $value["anum"];//投资成功总额
                    $_result["tender_success_num"] = $value["num"];//投资成功笔数
                }elseif ($value["status"]==0){
                    $_result["tender_now_account"] = $value["anum"];//投资中的总额
                    $_result["tender_now_num"] = $value["num"];//投资成功笔数
                }
             }
        }
        
        
        //逾期
        $result = array();
        // 逾期已还
        $sql = "select recover_status,sum(recover_account) as anum,count(1) as num  from `{borrow_recover}`  where user_id='{$data['user_id']}' and  recover_status=1 and (recover_yestime-recover_time)>".(time()+60*60*24);
        $recover_late = $mysql->db_fetch_arrays($sql);
        $_result["recover_late_yes_account"] = isset($recover_late["anum"])?$recover_late["anum"]:0;//逾期已还总额
        $_result["recover_late_yes_num"] = isset($recover_late["num"])?$recover_late["num"]:0;//逾期已还笔数
        $sql = "select recover_status,sum(recover_account) as anum,count(1) as num  from `{borrow_recover}`  where user_id='{$data['user_id']}' and recover_time<=".(time()+60*60*24)." and recover_status=0";
        $recover_late = $mysql->db_fetch_arrays($sql);
        $_result["recover_late_no_account"] = isset($recover_late["anum"])?$recover_late["anum"]:0;
        $_result["recover_late_no_num"] = isset($recover_late["num"])?$recover_late["num"]:0;
        $_result["tender_late_account"] = $_result["recover_late_yes_account"]+$_result["recover_late_no_account"];//逾期未还总额
        $_result["tender_late_num"] = $_result["tender_late_yes_num"]+$_result["tender_late_no_num"];//逾期未还总额
       
        //网站垫付
        $result = array();
        $sql = "select sum(recover_account_yes) as anum,count(1) as num  from `{borrow_recover}`  where user_id='{$data['user_id']}' and recover_web=1 ";
        $result = $mysql->db_fetch_array($sql);
      
        $_result["recover_web_account"] = $result["anum"];//网站垫付总额
        $_result["recover_web_num"] = $result["num"];//网站垫付笔数
        
        //投资奖励
        $result = array();
        $sql = 'select sum(money) as tnum,count(distinct `borrow_nid`) as num from {account_log} where `user_id`='.$data['user_id'].' and `code`=\'tender\' and `code_type` in (\'continued_investment_award\',\'brrow_tender_award\',\'invite_tender_award\')';
        $result = $mysql->db_fetch_array($sql);
        $_result["tender_award_account"] = $result["tnum"];//奖励总额
        $_result["tender_award_num"] = $result["num"];//奖励笔数
        
        
		//提前还款罚金收入
        $result = array();
        $sql = "select sum(recover_advance_fee) as anum,count(1) as num  from `{borrow_tender}`  where user_id='{$data['user_id']}' and recover_advance_fee>0  ";
        $result = $mysql->db_fetch_array($sql);
        $_result["tender_advance_account"] = $result["anum"];//提前还款费用
        $_result["tender_advance_num"] = $result["num"];//提前还款笔数
        
		//损失利息总额
        $result = array();
		$sql = "select sum(recover_interest-recover_interest_yes) as anum,count(1) as num  from `{borrow_recover}`  where user_id='{$data['user_id']}' and recover_status=1 ";
        $result = $mysql->db_fetch_array($sql);
        $_result["recover_loss_account"] = $result["anum"];//损失利息
        $_result["recover_loss_num"] = $result["num"];//损失利息笔数
		
        //已赚罚息=逾期罚金=网站未垫付前借款人还款得到的逾期罚金费用总计
        $result = array();
        $sql = "select sum(recover_late_fee) as anum,count(1) as num  from `{borrow_recover}`  where user_id='{$data['user_id']}' and recover_status=1 and recover_type='late' ";
        $result = $mysql->db_fetch_array($sql);
        $_result["recover_fee_account"] = $result["anum"];//罚息总额
        $_result["recover_fee_num"] = $result["num"];//罚息笔数
        $_result["tender_recover_scale"] = 0;
        $_result["tender_false_scale"] = 0;
        if ($_result["tender_success_account"]>0 ){
        //平均收益率
        $_result["tender_recover_scale"] = round(($_result["tender_interest_account"]+$_result["tender_award_account"])/$_result["tender_success_account"],2);
        
        //坏账率
        $_result["tender_false_scale"] = round($_result["tender_late_account"]/$_result["tender_success_account"],2);
        }
        return $_result;     
    }
    
    
   //借款人借入统计
	public static function GetUsersRepayCount($data = array()){
	   	global $mysql;
		if ($data['user_id']=="") return "";
        $sql = "select repay_status,sum(repay_account) as anum,sum(repay_interest) as inum,count(1) as num from  `{borrow_repay}` where user_id='{$data['user_id']}' and status=1 group by repay_status";
        $result = $mysql->db_fetch_arrays($sql);
        
        $_result = array();
        foreach ($result as $key => $value){
            if ($value["repay_status"]==1){
                $_result["repay_yes_account"] = $value["anum"];//已还总额
                $_result["repay_yes_interest"] = $value["inum"];//已还利息
                $_result["repay_yes_num"] = $value["num"];//已还期数
            }elseif ($value["status"]==0){
                $_result["repay_wait_account"] = $value["anum"];//未还总额
                $_result["repay_wait_interest"] = $value["inum"];//未还利息
                $_result["repay_wait_num"] = $value["num"];//未还期数
            }
            $_result["repay_interest"] += $value["inum"];//利息总额
        }
        
        //最近待收
        $sql = "select * from `{borrow_repay}` where user_id='{$data['user_id']}' and repay_status=0 order by repay_time asc";
        $result = $mysql->db_fetch_array($sql);
        if ($result!=false){
             $_result["repay_wait_now_account"] = $result["repay_account"];//最近待还总额
             $_result["repay_wait_now_interest"] = $result["repay_interest"];//最近待还利息
             $_result["repay_wait_now_time"] = $result["repay_time"];//最近待还时间
        }
        
        //还款笔数 和 待还笔数
         /* $sql = "select repay_status,count(distinct borrow_nid) as num from  `{borrow_repay}` where user_id='{$data['user_id']}' group by repay_status";
        $result = $mysql->db_fetch_arrays($sql); 
        foreach ($result as $key => $value){
            if ($value["repay_status"]==1){               
                $_result["repay_yes_times"] = $value["num"];//已还笔数
            }elseif ($value["status"]==0){                
                $_result["repay_wait_times"] = $value["num"];//未还笔数
            }
            
        } */
        $sql = "select repay_account_wait from  `{borrow}` where user_id='{$data['user_id']}' and status=3";
        $result = $mysql->db_fetch_arrays($sql); 
        foreach ($result as $key => $value){
            if ($value["repay_account_wait"]==0){               
                $_result["repay_yes_times"] += 1;//已还笔数
            }else{                
                $_result["repay_wait_times"]+= 1; //未还笔数
            }            
        }

        
        
        //总借入统计
        $sql = "select status,sum(account) as anum,count(1) as num from `{borrow}`  where user_id='{$data['user_id']}' group by status";
        $result = $mysql->db_fetch_arrays($sql);
        if ($result!=false){
             foreach ($result as  $key => $value){
                if ($value["status"]==3){
                    $_result["borrow_success_account"] = $value["anum"];//借款成功总额
                    $_result["borrow_success_num"] = $value["num"];//借款成功次数
                }
                if($value["status"]==1){
                    $_result["borrow_now_account"] = $value["anum"];//正在招标中
                    $_result["borrow_now_num"] = $value["num"];//正在招标中德次数
                }
                if($value["status"]==5){
                    $_result["borrow_cancel_account"] = $value["anum"];//撤标总额
                    $_result["borrow_cancel_num"] = $value["num"];//撤标次数
                }
                if($value["status"]==6){
                    $_result["borrow_over_account"] = $value["anum"];//流标总额
                    $_result["borrow_over_num"] = $value["num"];//流标次数
                }
                 $_result["borrow_loan_num"] += $value["num"];//发标次数
             }
        }
                
       //逾期
        $sql = "select repay_status,sum(repay_account) as anum,sum(late_interest) as lnum,count(1) as num  from `{borrow_repay}`  where user_id='{$data['user_id']}' and repay_time<=".(time()+60*60*24)." group by repay_status";
        $result = $mysql->db_fetch_arrays($sql);
        $_result["borrow_late_account"] = 0;
        if ($result!=false){
             foreach ($result as  $key => $value){
                if ($value["repay_status"]==1){
                    $_result["repay_late_yes_account"] = $value["anum"];//逾期已还总额
                    $_result["repay_late_yes_num"] = $value["num"];//逾期已还笔数
                }elseif ($value["repay_status"]==0){
                    $_result["repay_late_no_account"] = $value["anum"];//逾期未还总额
                    $_result["repay_late_no_num"] = $value["num"];//逾期未还笔数
                }
                $_result["repay_late_account"] += $value["anum"];//逾期未还总额
                $_result["repay_late_num"]+= $value["num"];//逾期次数
                $_result["repay_late_interest"]+= $value["lnum"];//逾期罚息
             }
        }
        
        //提前还款
        $sql = "select sum(repay_account) as anum,count(1) as num  from `{borrow_repay}`  where user_id='{$data['user_id']}' and repay_type = 'advance'";
        $result = $mysql->db_fetch_array($sql);
        $_result['repay_advance_account']=$result['anum'];//提前还款金额
        $_result['repay_advance_num']=$result['num'];//提前还款期数
        
        //借入利息成本  = 累计利息成本 / 累计借入金额
        if($_result['repay_interest']>0){
              $_result['borrow_interest_scale'] = round($_result['repay_interest'] / $_result['borrow_success_account'],2) ;      
        }
        
        return $_result;
       
    }
    //统计用户借出与还款积分
	public static function GetUsersCreditCount($data = array()){
       	global $mysql;
        $_result=array();
    	if ($data['user_id']=="") return "";
        $sql = "select sum(credit) as tender_credit from  `{credit_log}` where user_id='{$data['user_id']}' and nid='tender_success'";
        $result = $mysql->db_fetch_array($sql);        
        $_result['tender_credit']=$result['tender_credit'];
        
        $sql = "select sum(credit) as borrow_credit from  `{credit_log}` where user_id='{$data['user_id']}' and nid='borrow_success'";
        $result = $mysql->db_fetch_array($sql);
        $_result['borrow_credit']=$result['borrow_credit'];
        
        return $_result;
        
    }
    
}
?>
