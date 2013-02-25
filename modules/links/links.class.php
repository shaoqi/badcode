<?php
/******************************
 * $File: links.class.php
 * $Description: 友情链接
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

class linksClass {


	const ERROR = '操作有误，请不要乱操作';

	/**
	 * 列表
	 *
	 * @return Array
	 */
	function GetList($data = array()){
		global $mysql;
		
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
		$_sql = " where 1=1 ";
		if (isset($data['logo'])){
			if ($data['logo']=="true"){
				$_sql .= " and p1.logoimg!=''";
			}else{
				$_sql .= " and p1.logoimg=''";
			}
		}
		if ((isset($data['status']) && $data['status']!="" ) || $data['status']=='0'){
			$_sql .= " and p1.status={$data['status']}";
		}
		$_select = 'p1.*,p2.typename,p3.fileurl ';
		$sql = "select SELECT from `{links}` as p1 
				left join {links_type} as p2 on p1.type_id= p2.id
				left join {users_upfiles} as p3 on p1.logoimg= p3.id
				SQL ORDER LIMIT";
		
	//是否显示全部的信息
		if (IsExiest($data['limit'])!=false){
			if ($data['limit'] != "all"){ $_limit = "  limit ".$data['limit']; }
			$result = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array($_select, $_sql, $_order, $_limit), $sql));
			
			return $result;
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
	
	/**
	 * 查看
	 *
	 * @param Array $data
	 * @return Array
	 */
	public static function GetOne($data = array()){
		global $mysql;
		$id = $data['id'];
		
		$sql = "select p1.*,p3.fileurl from {links} as p1 left join {users_upfiles} as p3 on p1.logoimg= p3.id where p1.id=$id ";
		return $mysql->db_fetch_array($sql);
	}
	
	 /**
	 * 添加
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function Add($data = array()){
		global $mysql;
		$sql = "insert into `{links}` set `addtime` = '".time()."',`addip` = '".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
        return $mysql->db_query($sql);
	}
	
	
	/**
	 * 修改
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function Update($data = array()){
		global $mysql;
		$id = $data['id'];
        if ($data['id'] == "") {
            return self::ERROR;
        }
		
		$_sql = "";
		$sql = "update `{links}` set ";
		foreach($data as $key => $value){
			$_sql[] .= "`$key` = '$value'";
		}
		$sql .= join(",",$_sql)." where id = '$id'";
        return $mysql->db_query($sql);
	}
	
	/**
	 * 删除
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	public static function Delete($data = array()){
		global $mysql;
		$id = $data['id'];
		if (!is_array($id)){
			$id = array($id);
		}
		$sql = "delete from `{links}`  where id in (".join(",",$id).")";
		return $mysql->db_query($sql);
	}
	
	
	/**
	 * 类型列表
	 *
	 * @return Array
	 */
	function GetTypeList($data = array()){
		global $mysql;
		$sql = "select * from `{links_type}` ";
		return $mysql->db_fetch_arrays($sql);	
	}
	
	/**
	 * 类型列表
	 *
	 * @return Array
	 */
	function GetTypeOne($data = array()){
		global $mysql;
		$sql = "select * from `{links_type}`  where id='{$data['id']}'";
		return $mysql->db_fetch_array($sql);	
	}
}
?>
