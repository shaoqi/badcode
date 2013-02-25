<?
/******************************
 * $File: borrow.flag.php
 * $Description: 标签类
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

class borrowflagClass {

	
	/**
	 * 1,获得标签列表
	 *
	 * @return Array
	 */
	 /**
	 * $data = array("user_id"=>"用户id","username"=>"用户名");
	 * @return Array
	 */
	function GetList($data = array()){
		global $mysql,$_G;
		
		$_sql = "where 1=1 ";	
		
		
		
		//搜到标题
		if (IsExiest($data['name']) != false){
			$_sql .= " and p1.`name` like '%".urldecode($data['name'])."%'";
		}
		
		//判断状态
		if (IsExiest($data['status']) != false){
			$_sql .= " and p1.status = {$data['status']}";
		}
		
		
		$_order = " order by p1.order desc,p1.id desc ";
		
		
		$_select = " p1.*,p2.fileurl";
		$sql = "select SELECT from `{borrow_flag}` as p1 left join `{users_upfiles}` as p2 on p1.upfiles_id = p2.id
				 SQL ORDER LIMIT
				";
		
		//是否显示全部的信息
		if (IsExiest($data['limit'])!=false){
			if ($data['limit'] != "all"){ $_limit = "  limit ".$data['limit']; }
			$result =  $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array($_select, $_sql, $_order, $_limit), $sql));
			return $result;
		}			 
		
		//判断总的条数
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array('count(1) as num', $_sql,'', ''), $sql));
		$total = intval($row['num']);
		
	
		$data['page'] = !IsExiest($data['page'])?1:$data['page'];
		$data['epage'] = !IsExiest($data['epage'])?10:$data['epage'];
		$total_page = ceil($total / $data['epage']);
		$_limit = " limit ".($data['epage'] * ($data['page'] - 1)).", {$data['epage']}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL','ORDER', 'LIMIT'), array($_select,$_sql,$_order, $_limit), $sql));
		
		
		
		//返回最终的结果
		$result = array('list' => $list?$list:array(),'type_name' => empty($data['type_id'])?"全部":$list[0]['type_name'],'site_nid' => $site_nid,'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		
		return $result;
	}
	
	/**
	 * 2,查看文章
	 *
	 * @param Array $data
	 * @return Array
	 */
	public static function GetOne($data = array()){
		global $mysql,$_G;
		if (!IsExiest($data['id'])) return "borrow_flag_id_empty";
		if ($data['hits_status']==1){
			$sql ="update `{borrow_flag}` set hits =hits+1 where id={$data['id']}";
			$mysql->db_query($sql);
		}
		$_sql = " where p1.id={$data['id']}";
		
		$sql = "select p1.* ,p2.fileurl from `{borrow_flag}` as p1  left join `{users_upfiles}` as p2 on p1.upfiles_id = p2.id
				{$_sql}";
		$result = $mysql->db_fetch_array($sql);
		
		return $result;
	}
	
	
	
	
	/**
	 * 3,添加文章
	 *
	 * @param Array $result
	 * @return Boolen
	 */
	function Add($data){
		global $mysql;
		
        if (!IsExiest($data['name'])) return "borrow_flag_name_empty";
		
		$sql = "insert into `{borrow_flag}` set `addtime` = '".time()."',`addip` = '".ip_address()."',updatetime='".time()."',updateip='".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
        $mysql->db_query($sql);
    	$id = $mysql->db_insert_id();
		
		return $id;
	}
	
	/**
	 * 4,修改文章
	 *
	 * @param Array $result
	 * @return Boolen
	 */
	function Update($data){
		global $mysql;
		if (!IsExiest($data['id'])) return "borrow_flag_id_empty";
        if (!IsExiest($data['name'])) return "borrow_flag_name_empty";
		
		$sql = "update `{borrow_flag}` set updatetime='".time()."',updateip='".ip_address()."',";
		$_sql = "";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql)." where id='{$data['id']}'");
		return $data['id'];
	}
	
	
	
	/**
	 * 6，删除标签
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	public static function Delete($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "borrow_flag_id_empty";
		$id = $data['id'];
		
		$sql = "delete from `{borrow_flag}`  where id in ({$id})";
		$mysql->db_query($sql);
		
		return  $data['id'];
	}
	
	
}
?>