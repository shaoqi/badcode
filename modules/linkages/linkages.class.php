<?
/******************************
 * $File: linkage.class.php
 * $Description: 类文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

//将语言给插进来
require_once("linkages.model.php");

class linkagesClass{
	
	
	/**
	 * 获得列表
	 *
	 * @return Array
	 */
	function GetList($data = array()){
		global $mysql;
		
		
		$_sql = " where 1=1 ";
		//判断类型名称是否存在
		$data['type_id'] = isset($data['type_id'])?$data['type_id']:"";
        if (IsExiest($data['type_id'])!=false) {
            $_sql .= " and p1.type_id ='{$data['type_id']}'";
        }
		
		$data['name'] = isset($data['name'])?$data['name']:"";
		if (IsExiest($data['name'])!=false) {
            $_sql .= " and p1.name like '%{$data['name']}%'";
        }
		if (IsExiest($data['nid'])!=false) {
            $_sql .= " and p2.nid = '{$data['nid']}'";
        }
		$_select = " p1.* ,p2.code,p2.name as type_name,p2.nid as type_nid";
		$_order = " order by p1.order desc ,p1.id asc";
		$sql = "select SELECT from {linkages} as p1 
				left join {linkages_type} as p2 on p1.type_id=p2.id
				{$_sql}   ORDER ";
		
		//是否显示全部的信息
		$data['limit'] = isset($data['limit'])?$data['limit']:"";
		$_limit = "";
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
		if($id == "") return self::ERROR;
		$sql = "select * from {linkage} where id=$id";
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
        //判断类型名称是否存在
        if (!IsExiest($data['name'])) {
            return "linkages_name_empty";
        }
		
		//判断类型的标示名是否存在
		if (!IsExiest($data['value']) && $data['value']!=0) {
             return "linkages_value_empty";
        }
		
		$sql = "insert into `{linkages}` set ";
		
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql));
    	return $mysql->db_insert_id();
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
        if ($data['name'] == ""  || $data['id'] == "") {
            return self::ERROR;
        }
		$sql = "update `{linkage}` set ";
		$_sql = "";
		foreach($data as $key => $value){
			$_sql[] .= "`$key` = '$value'";
		}
		$sql .= join(",",$_sql)." where id = '$id'";
        $result = $mysql->db_query($sql);
		if ($result == false) return self::ERROR;
		return true;
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
		$sql = "delete from {linkages}  where id in (".join(",",$id).")";
		$mysql->db_query($sql);
		return $data['id'];
	}
	
	
	/**
	 * 修改信息
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	public static function Action($data = array()){
		global $mysql;
		$name = $data['name'];
		$value = $data['value'];
		$order = $data['order'];
		$type = isset($data['type'])?$data['type']:"";
		unset($data['type']);
		if ($type == "add"){
			$type_id = $data['type_id'];
			
			foreach ($name as $key => $val){
				if ($value[$key]==""){
					$value[$key] = $val;
				}
				if ($val!=""){
					$sql = "insert into `{linkages}` set `type_id`='".$type_id."',`name`='".$name[$key]."',`value`='".$value[$key]."',`order`='".$order[$key]."' ";			
					$mysql->db_query($sql);
				}
			}
		}else{
			$id = $data['id'];
			foreach ($id as $key => $val){
				if ($name[$key]!=""){
					$sql = "update `{linkages}` set `name`='".$name[$key]."',`value`='".$value[$key]."',`order`='".$order[$key]."' where id=$val";			
					$mysql->db_query($sql);
				}
			}
		}
		
		return true;
	}
	
	
	
	/**
	 * 列表
	 *
	 * @param Array $data
	 * @return Array
	 */
	public static function GetTypeList($data = array()){
		global $mysql;
		
		
		$_sql = " where 1=1 ";
		//判断类型名称是否存在
        if (IsExiest($data['name'])!=false) {
            $_sql .= " and p1.name like '%{$data['name']}%'";;
        }
		
		//判断类型的标示名是否存在
		if (IsExiest($data['nid'])!=false) {
            $_sql .= " and p1.nid  = '{$data['nid']}'";;
        }
		//搜索模块
		if (IsExiest($data['code'])!=false) {
            $_sql .= " and p1.code  = '{$data['code']}'";;
        }
		//搜索大类
		if (IsExiest($data['class_id'])!=false) {
            $_sql .= " and p1.class_id  = '{$data['class_id']}'";;
        }
		
		$_select = "p1.*,p2.name as class_name";
		$_order = " order by p1.`order` desc,p1.id desc";
		$sql = "select SELECT from `{linkages_type}` as p1 left join `{linkages_class}` as p2 on p1.class_id = p2.id SQL  ORDER LIMIT";
		
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
		return array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
	}
	
	/**
	 * 查看类型
	 *
	 * @param Array $data
	 * @return Array
	 */
	public static function GetType($data = array()){
		global $mysql;
		$_sql = " where 1=1 ";
		//判断类型名称是否存在
        if (IsExiest($data['id'])!=false) {
            $_sql .= " and p1.id ='{$data['id']}'";;
        }
		
		//判断类型的标示名是否存在
		if (IsExiest($data['nid'])!=false) {
            $_sql .= " and p1.nid  = '{$data['nid']}'";;
        }
		$sql = "select * from `{linkages_type}` as p1 {$_sql}";
		$result = $mysql->db_fetch_array($sql);
		
		return $result;
	}
	
	/**
	 * 添加联动类型
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function AddType($data = array()){
		global $mysql;
		
		//判断类型名称是否存在
        if (!IsExiest($data['name'])) {
            return "linkages_type_name_empty";
        }
		
		//判断类型的标示名是否存在
		if (!IsExiest($data['nid'])) {
            return "linkages_type_nid_empty";
        }
		
		//链接类型
		$sql = "select * from {linkages_type} where `nid` = '".$data['nid']."'";
		$result = $mysql->db_fetch_array($sql);
		if ($result !=false) return "linkages_type_nid_exiest";
		
		$_sql = "";
		$sql = "insert into `{linkages_type}` set ";
		foreach($data as $key => $value){
			$_sql[] .= "`$key` = '$value'";
		}
		$sql .= join(",",$_sql)." ";
         $mysql->db_query($sql);
		 return $mysql->db_insert_id();
	}
	
	
	/**
	 * 修改联动类型
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	public static function UpdateType($data = array()){
		global $mysql;
		
        //判断类型的ID是否存在
        if (!IsExiest($data['id'])) {
            return "linkages_type_id_empty";
        }
		
		 //判断类型名称是否存在
        if (!IsExiest($data['name'])) {
            return "linkages_type_name_empty";
        }
		
		//判断类型的标示名是否存在
		if (!IsExiest($data['nid'])) {
            return "linkages_type_nid_empty";
        }
		//链接类型
		$sql = "select * from {linkages_type} where `nid` = '".$data['nid']."' and id!='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result !=false) return "linkages_type_nid_exiest";
		
		$sql = "update `{linkages_type}` set ";
		$_sql = "";
		foreach($data as $key => $value){
			$_sql[] .= "`$key` = '$value'";
		}
		$sql .= join(",",$_sql)." where `id` = '{$data['id']}'";
        $mysql->db_query($sql);
		return $data['id'];
	}
	
	/**
	 * 删除类型
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	public static function DelType($data = array()){
		global $mysql;
		//判断类型的ID是否存在
        if (!IsExiest($data['id'])) {
            return "linkages_type_id_empty";
        }
		//判断类型是否还有子类
		$sql = "select * from `{linkages}` where `type_id` ='{$data['id']}' ";
		$result = $mysql -> db_fetch_array($sql);
		if ($result!=false) return "linkages_type_sub_exiest";
		
		//删除类型
		$sql = "delete from `{linkages_type}`  where `id` ='{$data['id']}' ";
		
		$mysql->db_query($sql);
		return $data['id'];
	}
	
	/**
	 * 修改信息
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	public static function ActionType($data = array()){
		global $mysql;
		$nid = $data['nid'];
		$name = $data['name'];
		$order = $data['order'];
		
		$id = $data['id'];
		foreach ($id as $key => $val){
			if ($name[$key]!=""){
				$sql = "update {linkages_type} set `name`='".$name[$key]."',`order`='".$order[$key]."' where id={$val}";			
				$mysql->db_query($sql);
			}
		}
		
		return true;
	}
	
	
	/**
	 * 1,添加证明资料
	 *
	 * @param array $data =array("name"=>"证明资料名称","status"=>"状态","degree"=>"学历","in_year"=>"入学时间","professional"=>"专业");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function AddLinkagesClass($data = array()){
		global $mysql;
		 //判断名称是否存在
        if (!IsExiest($data['name'])) {
            return "linkages_class_name_empty";
        }
		 //判断标识名是否存在
        if (!IsExiest($data['nid'])) {
            return "linkages_class_nid_empty";
        }
		//判断标识名是否存在
		$sql = "select 1 from `{linkages_class}` where nid='{$data['nid']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "linkages_class_nid_exiest";
		
		$sql = "insert into `{linkages_class}` set addtime='".time()."',addip='".ip_address()."',";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql));
    	return $mysql->db_insert_id();
	}
	
	/**
	 * 2,修改证明资料
	 *
	 * @param array $data =array("id"=>"id","name"=>"名称","status"=>"状态");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function UpdateLinkagesClass($data = array()){
		global $mysql;
		
		 //判断名称是否存在
        if (!IsExiest($data['name'])) {
            return "linkages_class_name_empty";
        }
		 //判断标识名是否存在
        if (!IsExiest($data['nid'])) {
            return "linkages_class_nid_empty";
		}
		
		//判断标识名是否存在
		$sql = "select 1 from `{linkages_class}` where nid='{$data['nid']}' and id!={$data['id']}";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "linkages_class_nid_exiest";
		
		$sql = "update `{linkages_class}` set ";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql)." where id='{$data['id']}' ");
		return $data['id'];
	}
	
	/**
	 * 3,删除证明资料
	 *
	 * @param Array $data = array("id"=>"ID")
	 * @return Boolen
	 */
	function DelLinkagesClass($data = array()){
		global $mysql;
		
		if (!IsExiest($data['id'])) return "linkages_class_id_empty";
		
		$sql = "select 1 from `{linkages_type}` where class_id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result != false) return "linkages_class_type_exiest";
		
		$sql = "delete from `{linkages_class}`  where id='{$data['id']}'";
    	$mysql -> db_query($sql);
		
		return $data['id'];
	}
	
	
	
	
	/**
	 * 5,获得证明资料列表
	 *
	 * @return Array
	 */
	function GetLinkagesClassList($data = array()){
		global $mysql;
		
		$_sql = " where 1=1 ";
		$_select = " p1.*";
		$_order = " order by p1.id desc";
		$sql = "select SELECT from `{linkages_class}` as p1  SQL ORDER ";
		
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
		$_limit = " limit ".($data["epage"] * ($data["page"] - 1)).", {$data['epage']}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL','ORDER', 'LIMIT'), array($_select,$_sql,$_order, $_limit), $sql));
		
		//返回最终的结果
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		return $result;
	}
	
	
	
	/**
	 * 6,获得证明资料的单条记录
	 *
	 * @param Array $data = array("id"=>"");
	 * @return Array
	 */
	 function GetLinkagesClassOne($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "linkages_class_id_empty";
		
		$sql = "select p1.* from `{linkages_class}` as p1   where p1.id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		
		if ($result==false) return "linkages_class_empty";
		return $result;
	}
	
	
}
?>