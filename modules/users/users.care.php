<?
/******************************
 * $File: users.care.php
 * $Description: 用户关注用户
 * $Author: XiaoWu 
 * $Time:2012-10-15
 * $Update:XiaoWu
 * $UpdateDate:2012-10-15  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

class userscareClass{

	//添加关注用户
	function AddUserCare($data){
		global $mysql;
		if($data['user_id']=="" || $data['care_user_id']==""){
			return false;
		}
		$sql = "select 1 from `{users_care_user}` where user_id='{$data['user_id']}' and care_user_id='{$data['care_user_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false){
			$sql = "insert into `{users_care_user}` set  addtime='".time()."',addip='".ip_address()."'";
			foreach($data as $key => $value){
				$sql .= ",`$key` = '$value'";
			}
			$mysql->db_query($sql);
			return 1;
		}else{
			return 0;
		}
	}
	
	//获取关注用户列表
	function GetUserCareList($data){
		global $mysql;
		$_sql = " where 1=1  ";

		//判断用户id
		if (IsExiest($data['user_id']) != false){
			$_sql .= " and p1.`user_id`  = '{$data['user_id']}'";
		}
		
		//判断用户名
		if (IsExiest($data['username']) != false){
			$_sql .= " and p2.`username`  = '".urldecode($data['username'])."'";
		}
		
		//判断关注用户id
		if (IsExiest($data['care_user_id']) != false){
			$_sql .= " and p1.`care_user_id`  = '{$data['care_user_id']}'";
		}
		
		//判断关注用户名
		if (IsExiest($data['care_username']) != false){
			$_sql .= " and p3.`username`  = '".urldecode($data['care_username'])."'";
		}

		$_select = "p1.*,p2.username,p3.username as care_username,p3.user_id as care_userid";
		$_order = " order by p1.id asc";
		$sql = "select SELECT from `{users_care_user}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id left join `{users}` as p3 on p1.care_user_id=p3.user_id SQL ORDER LIMIT";
		
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
		
		//返回最终的结果
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		
		return $result;
	}
	
	function DeleteUserCare($data){
		global $mysql;
		if ($data['id']=="") return false;
		$sql = "delete from `{users_care_user}` where id = {$data['id']}";
		$mysql->db_query($sql);
		return 1;
	}
	
	function DelCare($data){
		global $mysql;
		if ($data['nid']=="") return false;
		$sql = "delete from `{users_care}` where article_id = {$data['nid']}";
		$mysql->db_query($sql);
		
		return 1;
	}
	
}
?>