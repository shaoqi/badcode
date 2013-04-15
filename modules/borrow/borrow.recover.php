<?php
class borrowRecoverClass{
	//收款明细
	function GetRecoverList($data){
		global $mysql,$_G;
		
		$_sql = " where 1=1 ";
		if (IsExiest($data['user_id'])!=false){
			$_sql .= " and p1.user_id={$data['user_id']}";
		}
		if (IsExiest($data['status'])!=false){
			$_sql .= " and p1.status={$data['status']}";
		}
		if (IsExiest($data['recover_status'])!=false){
			if($data['recover_status']==2){
				$_sql .= " and p1.recover_status=0";
			}else{
				$_sql .= " and p1.recover_status={$data['recover_status']}";
			}
		}
		if (IsExiest($data['borrow_status'])!=false){
			$_sql .= " and p2.status={$data['borrow_status']}";
		}
		if (IsExiest($data['late'])!=false){
			if($data['late']==1){
				$_sql .= " and (p1.late_days>0) or (p1.recover_time<".time()." and p1.recover_status=0)";
			}else{
				$_sql .= " and p1.late_days=0 and (p1.recover_time>".time().")";
			}
		}
		if (IsExiest($data['borrow_nid'])!=false){
			$_sql .= " and p2.borrow_nid='{$data['borrow_nid']}'";
		}
		if (IsExiest($data['borrow_type'])!=false){
			$_sql .= " and p2.borrow_type='{$data['borrow_type']}'";
		}
		if (IsExiest($data['username'])!=false){
			$_sql .= " and p3.username like '%".urldecode($data['username'])."%' ";
		}
		if (IsExiest($data['web'])!=false){
			$_sql .= " and p6.web_status=2";
		}
		
		if (IsExiest($data['dotime1'])!=false){
			$dotime1 = ($data['dotime1']=="request")?$_REQUEST['dotime1']:$data['dotime1'];
			if ($dotime1!=""){
				$_sql .= " and p1.recover_time > ".get_mktime($dotime1);
			}
		}
		
		if (IsExiest($data['dotime2'])!=false){
			$dotime2 = ($data['dotime2']=="request")?$_REQUEST['dotime2']:$data['dotime2'];
			if ($dotime2!=""){
				$_sql .= " and p1.recover_time < ".get_mktime($dotime2);
			}
		}
		if (IsExiest($data['type'])!=false){
			if ($data['type']=="yes"){
				$_sql .= " and p1.recover_status =1 or p1.recover_web=1";
			}elseif ($data['type']=="wait"){
				$_sql .= " and p1.recover_status !=1 and p1.recover_web!=1";
			}elseif ($data['type']=="web"){
				$_sql .= " and p1.recover_web=1";
			}
		}
		
		if (IsExiest($data['change'])!=false){
			$_sql .= " and p1.recover_status =1 and p6.change_status=1";
		}
		if (IsExiest($data['keywords'])!=""){
			$_sql .= " and (p2.name like '%".urldecode($data['keywords'])."%') ";
		}
		$_order = " order by p1.id ";
		if (IsExiest($data['order'])!="" ){
			if ($data['order'] == "repay_time"){
				$_order = " order by p2.id desc,p1.recover_time desc";
			}elseif ($data['order'] == "order"){
				$_order = " order by p1.`order` desc,p1.id desc ";
			}elseif ($data['order'] == "recover_status"){
				$_order = " order by p1.`recover_status` asc,p1.id desc ";
			}
		}		
		$_select = 'p1.*,p1.recover_account_yes as recover_recover_account_yes,p2.name as borrow_name,p2.borrow_period,p2.borrow_type,p3.username,p4.username as borrow_username,p4.user_id as borrow_userid,p6.buy_time';
		$sql = "select SELECT from `{borrow_recover}` as p1 
				left join `{borrow}` as p2 on  p2.borrow_nid = p1.borrow_nid
				left join `{users}` as p3 on  p3.user_id = p1.user_id
				left join `{users}` as p4 on  p4.user_id = p2.user_id
				left join `{borrow_change}` as p6 on  p1.tender_id = p6.tender_id
			   {$_sql} ORDER LIMIT";
		
		/*$_select = 'p1.*,p1.recover_account_yes as recover_recover_account_yes,p2.name as borrow_name,p2.borrow_period,p2.borrow_type,p3.username,p4.username as borrow_username,p4.user_id as borrow_userid,p5.*,p5.recover_account_yes as tender_recover_account_yes,p6.buy_time';
		$sql = "select SELECT from `{borrow_recover}` as p1 
				left join `{borrow}` as p2 on  p2.borrow_nid = p1.borrow_nid
				left join `{users}` as p3 on  p3.user_id = p1.user_id
				left join `{users}` as p4 on  p4.user_id = p2.user_id
				left join `{borrow_tender}` as p5 on  p1.tender_id = p5.id
				left join `{borrow_change}` as p6 on  p1.tender_id = p6.tender_id
			   {$_sql} ORDER LIMIT";*/
		//是否显示全部的信息
		if (isset($data['limit']) ){
			$_limit = "";
			if ($data['limit'] != "all"){
				$_limit = "  limit ".$data['limit'];
			}
			$list  = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,  $_order, $_limit), $sql));
			return $list;
		}	
		
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array(" count(*) as num ","",""),$sql));
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
		$index = $epage * ($page - 1);
		$limit = " limit {$index}, {$epage}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, $_order , $limit), $sql));
		foreach ($list as $key => $value){
			$all_capital+=$value['recover_capital'];
		}
		if ($data['style']=="change"){
			$total = count($change);
			$total_page = ceil($total / $epage);
		}elseif ($data['style']=="web"){
			$total = count($web);
			$total_page = ceil($total / $epage);
		}else{
			$total = $row['num'];
			$total_page = ceil($total / $epage);
		}
		return array(
            'list' => $list,
            'change' => $change,
            'all_capital' => $all_capital,
            'all_recover' => $all_recover,
            'web' => $web,
            'total' => $total,
            'page' => $page,
            'epage' => $epage,
            'total_page' => $total_page
        );
	}
}

?>