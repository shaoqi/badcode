<?
/******************************
 * $File: areas.class.php
 * $Description: 地区的类文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

require_once("areas.model.php");

class areasClass{
	
	const ERROR = '操作有误，请不要乱操作';
	
	function areasClass(){
		//连接数据库基本信息
		
	}
	
	/**
	 * 获得列表
	 *
	 * @return Array
	 */
	function GetAreas($data = array()){
		global $mysql;
		
		$_sql = "where 1=1 ";	
		$_select = "p1.*";
		$_order = " order by p1.`order` desc,p1.id asc";
		$sql = "select SELECT from `{areas}` as p1 SQL ORDER LIMIT";
		
		//导出数据
		$data['excel'] = isset($data['excel'])?$data['excel']:"";
		$_limit = "";
		if (IsExiest($data['excel'])=="true"){
			
		} 
		//是否显示全部的信息
		elseif (IsExiest($data['limit'])!=false){
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
		$_limit = " limit ".($epage * ($page - 1)).", {$data['epage']}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL','ORDER', 'LIMIT'), array($_select,$_sql,$_order, $_limit), $sql));
		
		//返回最终的结果
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		
		return $result;
		
	}
	
	/**
	 * 列表
	 *
	 * @return Array
	 */
	function GetList($data = array()){
		global $mysql;
		
		$_sql = "where 1=1 ";	
			 
		//搜索用户id
		if (IsExiest($data['user_id'])!=false) {
			$_sql .= " and p1.user_id = '{$data['user_id']}'";
		}
		
		//类型
		if ($data['type']=="province"){
			$result = self::GetProvince($data);
		}elseif ($data['type']=="city"){
			$result = self::GetCity($data);
		}elseif ($data['type']=="area"){
			$result = self::GetArea($data);
		}
		
		//分页返回结果
		$total = count($result);
		$data['page'] = !IsExiest($data['page'])?1:$data['page'];
		$data['epage'] = !IsExiest($data['epage'])?10:$data['epage'];
		$total_page = ceil($total / $data['epage']);
		$_epage = $data['epage'] * ($data['page'] - 1);
	
		if (is_array($result)){
			foreach ($result as $key => $value){
				if ($key>=$_epage && $key<$_epage+$data['epage']){
					$_result[$key] = $value;
				}
			}
		}
		//返回最终的结果
		$result = array('list' => $_result?$_result:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		return $result;
	}
	
	/**
	 * 获得省份列表
	 *
	 * @param Array $data = array("page"=>"分页","epage"=>"每页的数量","limit"=>"显示的页数")
	 * @return Array 
	 */
	function GetProvince($data = array()){
		global $_G;
		if (!IsExiest($_G['areas'])) return "areas_data_empty"; 
		$result = array();
		$i=0;
		foreach ($_G['areas'] as $key => $value){
			if ($value['province']==0){
				if (IsExiest($data['limit'])!=false){
					if ($data['limit']=="all"){
						$result[] = $value;
					}else{
						if ($data["limit"]>$i){
							$result[] = $value;
						}
						$i++;
					}
				}else{
					$result[] = $value;
				}
			}
		}
		return $result;
	}
	
	/**
	 * 获得城市列表
	 *
	 * @return Array
	 */
	function GetCity($data = array()){
		global $_G;
		if (!IsExiest($_G['areas'])) return "areas_data_empty"; 
		$result = array();
		if (IsExiest($data['id'])!=false){
			foreach ($_G['areas'] as $key => $value){
				if (($value['province']==$data['id'] && $value['city']==0)){
					if (IsExiest($data['limit'])!=false){
						if ($data['limit']=="all"){
							$result[] = $value;
						}else{
							if ($data["limit"]>$i){
								$result[] = $value;
							}
							$i++;
						}
					}else{
						$result[] = $value;
					}
				}
			}
		}else{
			foreach ($_G['areas'] as $key => $value){
				if (($value['province']>0 && $value['city']==0)){
					if (IsExiest($data['limit'])!=false){
						if ($data['limit']=="all"){
							$result[] = $value;
						}else{
							if ($data["limit"]>$i){
								$result[] = $value;
							}
							$i++;
						}
					}else{
						$result[] = $value;
					}
				}
			}
		}
		return $result;
	}
	
	
	/**
	 * 获得地区列表
	 *
	 * @return Array
	 */
	function GetArea($data = array()){
		global $_G;
		if (!IsExiest($_G['areas'])) return "areas_data_empty"; 
		$result = array();
		if (IsExiest($data['id'])!=false){
			foreach ($_G['areas'] as $key => $value){
				if ($value['province']>0 && $value['city']==$data['id'] ){
					if (IsExiest($data['limit'])!=false){
						if ($data['limit']=="all"){
							$result[] = $value;
						}else{
							if ($data["limit"]>$i){
								$result[] = $value;
							}
							$i++;
						}
					}else{
						$result[] = $value;
					}
				}
			}
		}else{
			foreach ($_G['areas'] as $key => $value){
				if ($value['province']>0 && $value['city']>0){
					if (IsExiest($data['limit'])!=false){
						if ($data['limit']=="all"){
							$result[] = $value;
						}else{
							if ($data["limit"]>$i){
								$result[] = $value;
							}
							$i++;
						}
					}else{
						$result[] = $value;
					}
				}
			}
		}
		return $result;
	}
	/**
	 * 查看用户
	 *
	 * @param Array $data
	 * @return Array
	 */
	public static function GetOne($data = array()){
		global $mysql;
		$id = $data['id'];
		if($id == "" && $data['nid']=="") return "";
		$_sql = " where 1=1 ";
		if ($data['id']!=""){
			$_sql .= " and id='{$data['id']}'";
		}
		if ($data['nid']!=""){
			$_sql .= " and nid='{$data['nid']}'";
		}
		$sql = "select * from `{areas}` {$_sql}";
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
		
       if (!IsExiest($data['name'])) {
			return "areas_name_empty";
		}
		
		if (!IsExiest($data['nid'])) {
			return "areas_nid_empty";
		} 
		
		$sql = "select 1 from `{areas}` where nid='{$data['nid']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "areas_nid_exiest";
		$_sql = "";
		$sql = "insert into `{areas}` set ";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql));
    	$id = $mysql->db_insert_id();
		
		return $id;
	}
	
	
	/**
	 * 添加
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function Update($data = array()){
		global $mysql;
        if (!IsExiest($data['id'])) {
			return "areas_id_empty";
		} 
		
		if (!IsExiest($data['name'])) {
			return "areas_name_empty";
		} 
		
		if (!IsExiest($data['nid'])) {
			return "areas_nid_empty";
		} 
		
		
		$sql = "select 1 from `{areas}` where nid='{$data['nid']}' and id != '{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "areas_nid_exiest";
		
		$sql = "update `{areas}` set ";
		$_sql = "";
		foreach($data as $key => $value){
			$_sql[] .= "`$key` = '$value'";
		}
		$sql .= join(",",$_sql)." where id = '{$data['id']}'";
        $mysql->db_query($sql);
		
		return $data['id'];
	}
	
	
	
	/**
	 * 删除
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	public static function Delete($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "areas_id_empty";
		//判断城市是否还有，否则不能删除省份
		$sql = "select 1 from `{areas}` where province='{$data[id]}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "areas_del_city_not_empty";
		
		
		//判断地区是否还有，否则不能删除城市
		$sql = "select 1 from `{areas}` where city='{$data[id]}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "areas_del_area_not_empty";
		
		$sql = "delete from `{areas}`  where id  ='{$data['id']}'";
		$mysql->db_query($sql);
		return $data['id'];
	}
	
	/**
	 * 修改排序
	 *
	 * @param Array $data = array("id"=>"","order"=>"排序");
	 * @return Boolen
	 */
	public static function Action($data = array()){
		global $mysql;
		if (count($data['order'])>0){
			foreach ($data['order'] as $key => $value){
				$sql = "update `{areas}` set `order` = '{$value}'  where id ='{$data['id'][$key]}'";
				$mysql->db_query($sql);
			}
		}
		return true;
	}
	
	function GetProvinceAll($data = array()){
		global $_G;
		$_result = array();
		$areas  = $data['areas'];
		foreach ($areas as $key => $value){
			if ($value['pid']==0){
				$letter = $value['nid']{0};
				$_result[$letter][$key]['letter']=$letter;
				$_result[$letter][$key]['id']=$value['id'];
				$_result[$letter][$key]['name']=$value['name'];
				$_result[$letter][$key]['nid']=$value['nid'];
				
			}
		}
		ksort($_result);
		return $_result;
		
	}
	
	
	function GetCityAll($data = array()){
		global $_G;
		$_result = array();
		$areas  = $data['areas'];
		foreach ($areas as $key => $value){
			if ($value['province']>0 && $value['city']==0  ){
				$letter = $value['nid']{0};
				$letter = strtoupper($letter);
				$_result[$letter][$key]['letter']=$letter;
				$_result[$letter][$key]['id']=$value['id'];
				$_result[$letter][$key]['status']=$value['status'];
				$_result[$letter][$key]['name']=$value['name'];
				$_result[$letter][$key]['nid']=$value['nid'];
				
			}
		}
		ksort($_result);
		return $_result;
		
	}
	
	
}
?>