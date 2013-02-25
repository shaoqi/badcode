<?php
//借款的统计
class borrowCountClass
{
	
	
	/**
	 * 用户投资按月统计,只统计借款成功标，也就是满标审核成功的投资的资金，满标未审核成功，以及待审核的投资都不统计。
	 *
	 * @return Array
	 */
	 
	 
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
	
	function GetBorrowRepayCount(){
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
	
	
	function GetKefuBorrowCount(){
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
	
	
	function GetKefuTenderCount(){
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
	
	
	function BorrowRemindCount(){
		global $mysql;
		//满标审核
		$sql = "select count(1) as num from `{borrow}` where status=1 and borrow_account_yes = account";
		$result = $mysql->db_fetch_array($sql);
		$_result['borrow_full_check'] = $result['num'];
		
		//发标审核
		$sql = "select count(1) as num from `{borrow}` where status=0";
		$result = $mysql->db_fetch_array($sql);
		$_result['borrow_publish_wait'] = $result['num'];
		
		//身份认证
		$sql = "select count(1) as num  from `{user}` where real_status=2";
		$result = $mysql->db_fetch_array($sql);
		$_result['real_status'] = $result['num'];
		
		//学历认证
		
		$sql = "select count(1) as num  from `{user}` where edu_status=2";
		$result = $mysql->db_fetch_array($sql);
		$_result['edu_status'] = $result['num'];
		
		//视频认证
		
		$sql = "select count(1) as num  from `{user}` where video_status=2";
		$result = $mysql->db_fetch_array($sql);
		$_result['video_status'] = $result['num'];
		
		//vip申请
		
		$sql = "select count(1) as num  from `{borrow_vip}` where status=2";
		$result = $mysql->db_fetch_array($sql);
		$_result['vip_status'] = $result['num'];
		
		//额度申请
		
		$sql = "select count(1) as num  from `{user_amountapply}` where status=0";
		$result = $mysql->db_fetch_array($sql);
		$_result['amount_status'] = $result['num'];
		
		//现场认证
		
		$sql = "select count(1) as num  from `{user}` where scene_status=2";
		$result = $mysql->db_fetch_array($sql);
		$_result['scene_status'] = $result['num'];
		
		//资料审核
	
		$sql = "select count(1) as num  from `{attestation}` where status=0";
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
		
		//提现
	
		$sql = "select count(1) as num  from `{liuyan}` where status=0";
		$result = $mysql->db_fetch_array($sql);
		$_result['liuyan_status'] = $result['num'];
		
		return $_result;
	
	}
}
?>
