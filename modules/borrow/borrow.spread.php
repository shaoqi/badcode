<?php

/*
$spread_log['user_id'] = 8;
$spread_log['borrow_nid'] = "223323232442";
$spread_log['repay_period'] = 1;
$spread_log['tender_fee'] =50;
//borrowSpreadClass::AddSpreadLog($spread_log);
*/
class borrowSpreadClass
{


	function UpdateUsers(){
		global $mysql;
		$sql = "select user_id,invite_userid from `{user}` order by user_id asc";
		$result = $mysql->db_fetch_arrays($sql);
		foreach ($result as $key => $value){
			self::AddSpread(array("user_id"=>$value['user_id'],"invite_userid"=>$value['invite_userid']));
		
		}	
	}
	
	function AddSpread($data){
		global $mysql;
		$sql = "select 1 from `{borrow_spread}` where user_id='{$data['user_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if($result==false){
			$sql = "insert into `{borrow_spread}` set user_id='{$data['user_id']}',spread_userid_a='{$data['invite_userid']}'";
			$mysql->db_query($sql);
			
			$sql = "insert into `{borrow_spread_count}` set user_id='{$data['user_id']}'";
			$mysql->db_query($sql);
			
			self::AddSpreadUser(array("user_id"=>$data['user_id']));
		}else{
			$sql = "update `{borrow_spread}` set spread_userid_a='{$data['invite_userid']}' where user_id='{$data['user_id']}'";
			$mysql->db_query($sql);
			
		}
			
		//判断是否是当月，是则将统计的人数归0
		self::CheckSpreadMonth(array("user_id"=>$data['invite_userid']));
		//好友数的增加
		self::AddSpreadFriends(array("user_id"=>$data['invite_userid'],"type"=>"a"));
		
				
		if ($data['invite_userid']>0){
			$sql = "select invite_userid from `{user}` where user_id='{$data['invite_userid']}'";
			$result_b = $mysql->db_fetch_array($sql);
			if ($result_b['invite_userid']>0){
				$sql = "update `{borrow_spread}` set spread_userid_b='{$result_b['invite_userid']}' where user_id='{$data['user_id']}'";
				$mysql->db_query($sql);
				//判断是否是当月，是则将统计的人数归0
				self::CheckSpreadMonth(array("user_id"=>$result_b['invite_userid']));
				//好友数的增加
				self::AddSpreadFriends(array("user_id"=>$result_b['invite_userid'],"type"=>"b"));
				
				$sql = "select invite_userid from `{user}` where user_id='{$result_b['invite_userid']}'";
				$result_c = $mysql->db_fetch_array($sql);
				if ($result_c['invite_userid']>0){
					$sql = "update `{borrow_spread}` set spread_userid_c='{$result_c['invite_userid']}' where user_id='{$data['user_id']}'";
					$mysql->db_query($sql);
					//判断是否是当月，是则将统计的人数归0
					self::CheckSpreadMonth(array("user_id"=>$result_c['invite_userid']));
					//好友数的增加
					self::AddSpreadFriends(array("user_id"=>$result_c['invite_userid'],"type"=>"c"));
				
					$sql = "select invite_userid from `{user}` where user_id='{$result_c['invite_userid']}'";
					$result_d = $mysql->db_fetch_array($sql);
					if ($result_d['invite_userid']>0){				
						$sql = "update `{borrow_spread}` set spread_userid_d='{$result_d['invite_userid']}' where user_id='{$data['user_id']}'";
						$mysql->db_query($sql);
						
						//判断是否是当月，是则将统计的人数归0
						self::CheckSpreadMonth(array("user_id"=>$result_d['invite_userid']));
						//好友数的增加
						self::AddSpreadFriends(array("user_id"=>$result_d['invite_userid'],"type"=>"d"));
					}
				}
			}
		}
		
		
		
	}
	
	//判断是否是当月，是则将统计的人数归0
	//data = array("user_id"=>"")
	function CheckSpreadMonth($data){
		global $mysql;
		$sql = "select update_month  from `{borrow_spread_count}` where user_id='{$data['user_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result['update_month']!=date("Ym",time())){
			$sql = "update `{borrow_spread_count}` set month_account=0,month_times=0,month_friends=0,update_month='".date("Ym",time())."' where user_id='{$data['user_id']}' ";
			$mysql->db_query($sql);
			
			$sql = "update `{borrow_spread_user}` set tender_fee_month=0,month=0,month_times=0,friends_month=0,update_month='".date("Ym",time())."' where user_id='{$data['user_id']}' ";
			$mysql->db_query($sql);
		}
	}
	
	//好友数的增加
	//data = array("user_id"=>""，type="a")
	function AddSpreadFriends($data){
		global $mysql;
		$sql = "update `{borrow_spread_user}` set friends=friends+1,friends_month=friends_month+1 where user_id='{$data['user_id']}' and type='{$data['type']}'";
		$mysql->db_query($sql);
		
		$_sql = "";
		if ($data['type']=="a"){
			$_sql .= ",friends_a=friends_a+1";
		}else{
			$_sql .= ",friends_b=friends_b+1";
		}
		$sql = "update `{borrow_spread_count}` set friends=friends+1,month_friends=month_friends+1{$_sql} where user_id='{$data['user_id']}' ";
		$mysql->db_query($sql);
	}
	
	function GetSpreadUser(){
	
	}
	
	function AddSpreadUser($data){
		global $mysql;
		$sql = "select sum(1) as num from `{borrow_spread}` where spread_userid_a='{$data['user_id']}' ";
		$result = $mysql->db_fetch_array($sql);
		$sql = "select 1 from `{borrow_spread_user}` where user_id='{$data['user_id']}' and type='a'";
		$_result = $mysql->db_fetch_array($sql);
		if ($_result==false){
			$sql = "insert into `{borrow_spread_user}` set  friends='{$result['num']}',user_id='{$data['user_id']}',type='a'";
			$mysql->db_query($sql);
		}else{
			$sql = "update `{borrow_spread_user}` set friends='{$result['num']}' where user_id='{$data['user_id']}' and type='a'";
			$mysql->db_query($sql);
		}
		
		
		$sql = "select sum(1) as num from `{borrow_spread}` where spread_userid_b='{$data['user_id']}' ";
		$result = $mysql->db_fetch_array($sql);
		$sql = "select 1 from `{borrow_spread_user}` where user_id='{$data['user_id']}' and type='b'";
		$_result = $mysql->db_fetch_array($sql);
		if ($_result==false){
			$sql = "insert into `{borrow_spread_user}` set  friends='{$result['num']}',user_id='{$data['user_id']}',type='b'";
			$mysql->db_query($sql);
		}else{
			$sql = "update `{borrow_spread_user}` set friends='{$result['num']}' where user_id='{$data['user_id']}' and type='b'";
			$mysql->db_query($sql);
		}
		
		
		$sql = "select sum(1) as num from `{borrow_spread}` where spread_userid_c='{$data['user_id']}' ";
		$result = $mysql->db_fetch_array($sql);
		$sql = "select 1 from `{borrow_spread_user}` where user_id='{$data['user_id']}' and type='c'";
		$_result = $mysql->db_fetch_array($sql);
		if ($_result==false){
			$sql = "insert into `{borrow_spread_user}` set  friends='{$result['num']}',user_id='{$data['user_id']}',type='c'";
			$mysql->db_query($sql);
		}else{
			$sql = "update `{borrow_spread_user}` set friends='{$result['num']}' where user_id='{$data['user_id']}' and type='c'";
			$mysql->db_query($sql);
		}
		
		
		$sql = "select sum(1) as num from `{borrow_spread}` where spread_userid_d='{$data['user_id']}' ";
		$result = $mysql->db_fetch_array($sql);
		$sql = "select 1 from `{borrow_spread_user}` where user_id='{$data['user_id']}' and type='d'";
		$_result = $mysql->db_fetch_array($sql);
		if ($_result==false){
			$sql = "insert into `{borrow_spread_user}` set  friends='{$result['num']}',user_id='{$data['user_id']}',type='d'";
			$mysql->db_query($sql);
		}else{
			$sql = "update `{borrow_spread_user}` set friends='{$result['num']}' where user_id='{$data['user_id']}' and type='d'";
			$mysql->db_query($sql);
		}
			
	}
	
	
	
	//data = array("user_id"=>"","type"=>"","type_fee"=>"","up_userid"=>"",account="",tender_fee="")
	function AddSpreadLog($data){
		global $mysql;
		
		$sql = "select * from `{borrow_spread}` where user_id='{$data['user_id']}'";
		$borrow_spread = $mysql->db_fetch_array($sql);
		
		if ($borrow_spread['spread_userid_a']>0){
			$_data["type"] = "a";
			$_data["user_id"] = $data['user_id'];
			$_data["borrow_nid"] = $data['borrow_nid'];
			$_data["repay_period"] = $data['repay_period'];
			$_data["tender_fee"] = $data['tender_fee'];
			$_data["invite_userid"] = $borrow_spread['spread_userid_a'];
			self::ActionSpreadLog($_data);
		}
		
		
		if ($borrow_spread['spread_userid_b']>0){
			$_data["type"] = "b";
			$_data["user_id"] = $data['user_id'];
			$_data["borrow_nid"] = $data['borrow_nid'];
			$_data["repay_period"] = $data['repay_period'];
			$_data["tender_fee"] = $data['tender_fee'];
			$_data["invite_userid"] = $borrow_spread['spread_userid_b'];
			self::ActionSpreadLog($_data);
		}
		
		
		if ($borrow_spread['spread_userid_c']>0){
			$_data["type"] = "c";
			$_data["user_id"] = $data['user_id'];
			$_data["borrow_nid"] = $data['borrow_nid'];
			$_data["repay_period"] = $data['repay_period'];
			$_data["tender_fee"] = $data['tender_fee'];
			$_data["invite_userid"] = $borrow_spread['spread_userid_c'];
			self::ActionSpreadLog($_data);
		}
		
		
		if ($borrow_spread['spread_userid_d']>0){
			$_data["type"] = "d";
			$_data["user_id"] = $data['user_id'];
			$_data["borrow_nid"] = $data['borrow_nid'];
			$_data["repay_period"] = $data['repay_period'];
			$_data["tender_fee"] = $data['tender_fee'];
			$_data["invite_userid"] = $borrow_spread['spread_userid_d'];
			self::ActionSpreadLog($_data);
		}
	
	}
	
	function ActionSpreadLog($data){
		global $mysql;
		//判断是否是当月，是则将统计的人数归0
		self::CheckSpreadMonth(array("user_id"=>$result_d['invite_userid']));
		
		$type_fee = self::GetTypeFee(array("type"=>$data['type']));
		$account = round($data['tender_fee']*$type_fee*0.01,2);
		
		//插入数据记录
		$sql = "insert into `{borrow_spread_log}` set  type_fee='{$type_fee}', addtime='".time()."', addip='".ip_address()."',tender_fee='{$data['tender_fee']}',account='{$account}',repay_period='{$data['repay_period']}',borrow_nid='{$data['borrow_nid']}',type='{$data['type']}',invite_userid='{$data['invite_userid']}', user_id='{$data['user_id']}'";
		$mysql->db_query($sql);
		
		//更新用户的费用
		$sql = "update `{borrow_spread_user}` set  update_time='".time()."',update_month='".date("Ym",time())."',month=month+'{$account}',account=account+'{$account}',tender_fee=tender_fee+'{$data['tender_fee']}',tender_fee_month=tender_fee_month+'{$data['tender_fee']}',month_times=month_times+1,times=times+1 where user_id='{$data['invite_userid']}' and type='{$data['type']}'";
		$mysql->db_query($sql);
		
		//更新总的费用
		$_sql = "";
		if ($data['type']=="a"){
			$_sql .= ",account_a=account_a+{$account}";
		}else{
			$_sql .= ",account_b=account_b+{$account}";
		}
		$sql = "update `{borrow_spread_count}` set  update_time='".time()."',update_month='".date("Ym",time())."',month_account	=month_account	+'{$account}',month_times=month_times+1,account=account+'{$account}',times=times+1{$_sql} where user_id='{$data['invite_userid']}'";
		$mysql->db_query($sql);
	}
	
	//data = array("type"=>"a")
	function GetTypeFee($data){
		global $_G;
		if ($data['type']=="a"){
			return (isset($_G['system']['con_borrow_spread_a_fee']))?$_G['system']['con_borrow_spread_a_fee']:40;
		}elseif ($data['type']=="b"){
			return (isset($_G['system']['con_borrow_spread_b_fee']))?$_G['system']['con_borrow_spread_b_fee']:30;
		}elseif ($data['type']=="c"){
			return (isset($_G['system']['con_borrow_spread_c_fee']))?$_G['system']['con_borrow_spread_c_fee']:20;
		}elseif ($data['type']=="d"){
			return (isset($_G['system']['con_borrow_spread_d_fee']))?$_G['system']['con_borrow_spread_d_fee']:10;
		}
	}
	function GetSpreadOne($data){
		global $mysql;
	}
	
	function GetSpreadLogList($data){
		global $mysql;
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
		$_select = " p1.*";
		$sql = "select SELECT from `{borrow_spread_log}` as p1 LIMIT ";
		if (isset($data['limit']) ){
			$_limit = "";
			if ($data['limit'] != "all"){
				$_limit = "  limit ".$data['limit'];
			}
			$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,  $_order, $_limit), $sql));
			return $list;
		}			 
		
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num', '', ''), $sql));

		$total = $row['num'];
		$total_page = ceil($total / $epage);
		$index = $epage * ($page - 1);
		$limit = " limit {$index}, {$epage}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,$_order, $limit), $sql));		
		$list = $list?$list:array();
	
		return array(
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'epage' => $epage,
            'total_page' => $total_page
        );
	}
	
	
	function GetSpreadUserList($data){
		global $mysql;
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
		$_sql = "where 1=1 ";
		if ($data['user_id']!=""){
			$_sql .= " and p1.user_id= '{$data['user_id']}'";
		}
		$_select = " p1.*";
		$sql = "select SELECT from `{borrow_spread_user}` as p1 {$_sql} LIMIT ";
	
		
		if (isset($data['limit']) ){
			$_limit = "";
			if ($data['limit'] != "all"){
				$_limit = "  limit ".$data['limit'];
			}
			$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,  $_order, $_limit), $sql));
			return $list;
		}			 
		
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num', '', ''), $sql));

		$total = $row['num'];
		$total_page = ceil($total / $epage);
		$index = $epage * ($page - 1);
		$limit = " limit {$index}, {$epage}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,$_order, $limit), $sql));		
		$list = $list?$list:array();
		foreach ($list as $key => $value){
			$list[$key]['fee'] = self::GetTypeFee(array("type"=>$value['type']));
		}
		return array(
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'epage' => $epage,
            'total_page' => $total_page
        );
	}
	
	function GetSpreadViewList($data){
		global $mysql;
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
		$_sql = "where 1=1 ";
		if ($data['user_id']!=""){
			$_sql .= " and p1.user_id= '{$data['user_id']}'";
		}
		$_select = " p1.*,p2.username";
		if ($data['type']=="a"){
			$sql = "select SELECT from `{borrow_spread_count}` as p1 left join `{user}` as p2 on p1.user_id=p2.user_id where p1.user_id in (select user_id from `{borrow_spread}` where spread_userid_a='{$data['user_id']}') LIMIT ";
		}elseif ($data['type']=="b"){
			$sql = "select SELECT from `{borrow_spread_count}` as p1 left join `{user}` as p2 on p1.user_id=p2.user_id  where p1.user_id in (select user_id from `{borrow_spread}` where spread_userid_b='{$data['user_id']}') LIMIT ";
		}elseif ($data['type']=="c"){
			$sql = "select SELECT from `{borrow_spread_count}` as p1 left join `{user}` as p2 on p1.user_id=p2.user_id  where p1.user_id in (select user_id from `{borrow_spread}` where spread_userid_c='{$data['user_id']}') LIMIT ";
		}elseif ($data['type']=="d"){
			$sql = "select SELECT from `{borrow_spread_count}` as p1 left join `{user}` as p2 on p1.user_id=p2.user_id  where p1.user_id in (select user_id from `{borrow_spread}` where spread_userid_d='{$data['user_id']}') LIMIT ";
		}
		
		if (isset($data['limit']) ){
			$_limit = "";
			if ($data['limit'] != "all"){
				$_limit = "  limit ".$data['limit'];
			}
			$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,  $_order, $_limit), $sql));
			return $list;
		}			 
		
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num', '', ''), $sql));

		$total = $row['num'];
		$total_page = ceil($total / $epage);
		$index = $epage * ($page - 1);
		$limit = " limit {$index}, {$epage}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,$_order, $limit), $sql));		
		$list = $list?$list:array();
		foreach ($list as $key => $value){
			$list[$key]['fee'] = self::GetTypeFee(array("type"=>$value['type']));
		}
		return array(
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'epage' => $epage,
            'total_page' => $total_page
        );
	}
	
	
	
	function GetSpreadCountList($data){
		global $mysql;
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
		
		$_select = " p1.*,p2.username";
		$_order = " order by p1.account desc,p1.user_id asc";
		$sql = "select SELECT from `{borrow_spread_count}` as p1 left join `{user}` as p2 on p1.user_id=p2.user_id ORDER LIMIT ";
		
		if ($data['type']=="self"){
			if ($data['user_id']>0){
				$sql = str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, '', ''), $sql);
				$sql .= " where p1.user_id='{$data['user_id']}'";
				$result = $mysql->db_fetch_arrays($sql);
				
				//统计金额靠前的有几位
				$account = empty($result[0]['account'])?0:$result[0]['account'];
				$sql = "select sum(1) as num from `{borrow_spread_count}` where account>$account";
				$_result = $mysql->db_fetch_array($sql);
				
				//统计金额相同，但是用户id比较大的
				$sql = "select sum(1) as num from `{borrow_spread_count}` where user_id>'{$data['user_id']}' and account=$account";
				$__result = $mysql->db_fetch_array($sql);
				
				$result[0]['rank'] = $_result['num'] + $__result['num']+1;
				return array(
					'list' => $result,
					'total' => 1,
					'page' => $page,
					'epage' => $epage,
					'total_page' => $total_page
				);
			}
		}
		if (isset($data['limit']) ){
			$_limit = "";
			if ($data['limit'] != "all"){
				$_limit = "  limit ".$data['limit'];
			}
			$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,  $_order, $_limit), $sql));
			return $list;
		}			 
		
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num', '', ''), $sql));

		$total = $row['num'];
		$total_page = ceil($total / $epage);
		$index = $epage * ($page - 1);
		$limit = " limit {$index}, {$epage}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,$_order, $limit), $sql));		
		$list = $list?$list:array();
		foreach ($list as $key => $value){
			$list[$key]['rank'] = $index+$key+1;
		}
		return array(
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'epage' => $epage,
            'total_page' => $total_page
        );
	}
	
	function GetSpreadCountOne($data){
		global $mysql;
		
		$_select = " p1.*,p2.username";
		$sql = "select SELECT from `{borrow_spread_count}` as p1 left join `{user}` as p2 on p1.user_id=p2.user_id where p1.user_id='{$data['user_id']}' ";

		$result = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,$_order, ""), $sql));		
		return $result;
	}
	
	//推广审核部门统计
	function GetSpreadVerifyCount($data){
		global $mysql,$_G;
		
		$sql="select reverify_time,account,status from `{borrow}`";
		
		$result=$mysql->db_fetch_arrays($sql);
		$month=date("m",time());
		foreach($result as $key =>$value){
			if (IsExiest($data['month'])!= false){
				$m=$data['month'];
				if ($m==date("m",$value['reverify_time'])){
					if ($value['status']==3){
						$Verify[$m]['yes']+=$value['account'];//审核通过
					}else{
						$Verify[$m]['no']+=$value['account'];//审核未通过
					}
					if ($value['status']!=0){
						$Verify[$m]['verifyall']+=$value['account'];
					}
				}
				if ($Verify[$m]['yes']>0 && $Verify[$m]['no']>0 && $Verify[$m]['verifyall']>0){
					//年份
					$VerifyCount[$m]['Year']=date("Y",$value['reverify_time']);
					//当月提出申请的总额度
					$VerifyCount[$m]['ApplyTotal']=$Verify[$m]['yes']+$Verify[$m]['no'];
					//当月提出申请的总额度
					$VerifyCount[$m]['Apply']=$Verify[$m]['verifyall'];
					//当月审核比例
					$VerifyCount[$m]['VerifyScale']=round($VerifyCount[$m]['Apply']/$VerifyCount[$m]['ApplyTotal'],2)*100;
					//当月通过审核总额度
					$VerifyCount[$m]['VerifyYes']=$Verify[$m]['yes'];
					//当月通过比例
					$VerifyCount[$m]['VerifyYesScale']=round($VerifyCount[$m]['VerifyYes']/$VerifyCount[$m]['Apply'],2)*100;
					//当月任务额度
					$VerifyCount[$m]['VerifyTask']=$_G['system']['con_spread_verify_amonut'];
					//当月达成率
					$VerifyCount[$m]['VerifyTaskScale']=round($VerifyCount[$m]['VerifyYes']/$VerifyCount[$m]['VerifyTask'],2)*100;
					//当月提成收入
					if ($VerifyCount[$m]['VerifyYes']-$VerifyCount[$m]['VerifyTask']>0){
						$VerifyCount[$m]['VerifyIncome']=($VerifyCount[$m]['VerifyYes']-$VerifyCount[$m]['VerifyTask'])/100*$_G['system']['con_spread_verify_fee'];
					}else{
						$VerifyCount[$m]['VerifyIncome']=0;
					}
				}
			}else{
				for ($m=1;$m<=$month;$m++){
					if (IsExiest($data['month'])!= false){
						$m=$data['month'];
					}
					if ($m==date("m",$value['reverify_time'])){
						if ($value['status']==3){
							$Verify[$m]['yes']+=$value['account'];//审核通过
						}else{
							$Verify[$m]['no']+=$value['account'];//审核未通过
						}
						if ($value['status']!=0){
							$Verify[$m]['verifyall']+=$value['account'];
						}
					}
					if ($Verify[$m]['yes']>0 && $Verify[$m]['no']>0 && $Verify[$m]['verifyall']>0){
						//年份
						$VerifyCount[$m]['Year']=date("Y",$value['reverify_time']);
						//当月提出申请的总额度
						$VerifyCount[$m]['ApplyTotal']=$Verify[$m]['yes']+$Verify[$m]['no'];
						//当月提出申请的总额度
						$VerifyCount[$m]['Apply']=$Verify[$m]['verifyall'];
						//当月审核比例
						$VerifyCount[$m]['VerifyScale']=round($VerifyCount[$m]['Apply']/$VerifyCount[$m]['ApplyTotal'],2)*100;
						//当月通过审核总额度
						$VerifyCount[$m]['VerifyYes']=$Verify[$m]['yes'];
						//当月通过比例
						$VerifyCount[$m]['VerifyYesScale']=round($VerifyCount[$m]['VerifyYes']/$VerifyCount[$m]['Apply'],2)*100;
						//当月任务额度
						$VerifyCount[$m]['VerifyTask']=$_G['system']['con_spread_verify_amonut'];
						//当月达成率
						$VerifyCount[$m]['VerifyTaskScale']=round($VerifyCount[$m]['VerifyYes']/$VerifyCount[$m]['VerifyTask'],2)*100;
						//当月提成收入
						if ($VerifyCount[$m]['VerifyYes']-$VerifyCount[$m]['VerifyTask']>0){
							$VerifyCount[$m]['VerifyIncome']=($VerifyCount[$m]['VerifyYes']-$VerifyCount[$m]['VerifyTask'])/100*$_G['system']['con_spread_verify_fee'];
						}else{
							$VerifyCount[$m]['VerifyIncome']=0;
						}
					}
				}
			}
		}
		return array(
            'list' => $VerifyCount
        );
	}
}
?>
