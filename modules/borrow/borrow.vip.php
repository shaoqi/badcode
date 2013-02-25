<?php

class borrowVipClass
{
	/**
	 * 担保列表
	 *
	 * @return Array
	 */
	public static function GetVipList($data = array()){
		global $mysql;
		$user_id = empty($data['user_id'])?"":$data['user_id'];
		$username = empty($data['username'])?"":$data['username'];
	
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
		
		$_sql = "where 1=1";		 
		if (IsExiest($user_id)!=""){
			$_sql .= " and p1.user_id = $user_id";
		}
		if (IsExiest($username)!=""){
			$_sql .= " and p2.username like '%$username%'";
		}
		if (IsExiest($data["kefu_username"])!=""){
			$_sql .= " and p3.username like '%".urldecode($data['kefu_username'])."%'";
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
	
		$_select = "p1.*,p2.username,p2.realname,p3.username as kefu_username";
		$sql = "select SELECT from `{borrow_vip}` as p1
				left join `{user}` as p2 on p2.user_id = p1.user_id
				left join `{user}` as p3 on p1.kefu_userid = p3.user_id
		 {$_sql}  order by p1.addtime desc LIMIT";
				
		//是否显示全部的信息
		if (isset($data['limit']) ){
			$_limit = "";
			if ($data['limit'] != "all"){
				$_limit = "  limit ".$data['limit'];
			}
			$result= $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,  'order by p1.id desc', $_limit), $sql));
			return $result;
		}			 
		
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num', '', ''), $sql));
		
		$total = $row['num'];
		$total_page = ceil($total / $epage);
		$index = $epage * ($page - 1);
		$limit = " limit {$index}, {$epage}";
		
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, 'order by p1.id desc', $limit), $sql));		
		$list = $list?$list:array();
		return array(
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'epage' => $epage,
            'total_page' => $total_page
        );
		
	}
	
	
	public static function GetUserVip($data = array()){
		global $mysql;
		if (IsExiest($data['user_id'])=="") return false;
		$sql = "select p1.*,p2.username as kefu_username,p2.realname as kefu_realname,p3.username from `{borrow_vip}` as p1 left join `{user}` as p2 on p1.kefu_userid=p2.user_id left join `{user}` as p3 on p1.user_id=p3.user_id where p1.user_id={$data['user_id']}";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false){
			$sql = "insert into `{borrow_vip}` set user_id={$data['user_id']}";
			$mysql->db_query($sql);
			self::GetUserVip($data);
		}else{
			if ($result["status"]==1){
				if ($result["end_date"]!="" && $result["end_date"] < time()){
					$result["status"] = 3;
				}
			}
			return $result;
		}
	}
	
	public static function GetVipStatus($data = array()){
		global $mysql;
		if (IsExiest($data['user_id'])=="") return false;
		$result = self::GetUserVip($data);
		$status = $result["status"];
		if ($result["status"]==1){
			if ($result["end_date"]!="" && $result["end_date"] > time()){
				$status = 3;
			}
		}
		
		return $status;
	}
	
	
	public static function ApplyUserVip($data = array()){
		global $mysql;
		if (IsExiest($data['user_id'])=="") return false;
		$result = self::GetUserVip($data);
		if ($result["status"]==1){
			return "vip_status_yes";
		}else{
			$sql = "update `{borrow_vip}` set years=1,`addtime` = '".time()."',`addip` = '".ip_address()."',status=2,kefu_userid='{$data['kefu_userid']}',remark='{$data['remark']}' where user_id='{$data['user_id']}'";
			return $mysql->db_query($sql);
		}
	}
	
	public static function UpdateKefu($data = array()){
		global $mysql;
		if (IsExiest($data['user_id'])=="") return false;
		
		$sql = "update `{borrow_vip}` set kefu_userid={$data['kefu_userid']} where user_id='{$data['user_id']}'";
		return $mysql->db_query($sql);
		
	}
	
	public static function CheckVip($data = array()){
		global $mysql;
		if (IsExiest($data['user_id'])=="") return false;
		$result = self::GetUserVip($data);
		if ($result["status"]==1){
			return "vip_status_yes";
		}else{
			$sql = "update `{borrow_vip}` set status={$data['vip_status']},kefu_userid={$data['kefu_userid']},years={$data['years']},verify_userid={$data['verify_userid']},first_date='".time()."',end_date='".(time()+60*60*24*365)."',verify_time='{$data['verify_time']}',verify_remark='{$data['verify_remark']}' where user_id='{$data['user_id']}'";
			return $mysql->db_query($sql);
		}
	}
}
?>
