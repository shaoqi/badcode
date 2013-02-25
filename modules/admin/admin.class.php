<?
/******************************
 * $File: admin.class.php
 * $Description: 管理类文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问


//将语言给插进来
require_once("admin.model.php");

require_once("admin.module.php");

class adminClass extends moduleClass {
	
	function adminClass(){
		//连接数据库基本信息
		
        
	}
	
	/**
	 * 1,修改系统信息
	 *
	 * @param array $data
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function UpdateSystem($data = array()){
		global $mysql,$upload;
		$code = $data['code'];
		unset($data['code']);
		if (IsExiest($data['con_watermark_file'])!=false){
			$sql = "select value from `{system}` where nid='con_watermark_file'";
			$result = $mysql->db_fetch_array($sql);
			if ($result['value']!=""){
				$_data['user_id'] =0;
				$_data['id'] = $result['value'];
				$upload->Delete($_data);
			}
		}
		foreach($data as $key => $value){
			
			$sql = "select * from `{system}` where nid='$key'";
			$result = $mysql->db_fetch_array($sql);
			if ($result==false){
				$sql = "insert into `{system}` set nid='$key',`value`='',code='{$code}',style=1,status=1";
				$mysql->db_query($sql);
			}
			
			$sql = "update `{system}` set `value` = '$value',code='$code' where nid='$key'";
			$mysql->db_query($sql);
		}
		
		return true;
	}
	
	/**
	 * 2,获取系统信息
	 *
	 * @param array $data
	 * @param string $data['code'],$data['status'];
	 * @return boolen(true,false)
	 */
	function GetSystem($data = array()){
		global $mysql;
		$sql = "select * from `{system}` where 1=1 ";
		if (IsExiest($data["code"])!=false){
			$sql .= " and `code` = '{$data['code']}'";
		}
		if (IsExiest($data["status"])!=false){
			$sql .= " and `status` = '{$data['status']}'";
		}
		$result = $mysql->db_fetch_arrays($sql);
		if ($result !=false){
			foreach ($result as $key => $value){
				$_result[$value['nid']] = $value['value'];
			}
		}
		return $_result;
	}
	
	
	/**
	 * 3,获得数据表
	 * 
	 * @return Array
	 */
	function GetSystemTables($data = array()){
		global $mysql;
		$_result = "";
		$sql = "show tables";
		$result = $mysql->db_fetch_arrays($sql);
		foreach ($result as $key => $value){
			foreach($value as $val){
				$_val = explode("_",$val);
				if($mysql->db_prefix!="" && $_val[0]."_"==$mysql->db_prefix){
					$num = $mysql->db_count(str_replace($mysql->db_prefix,"",$val));
					$_result[$key]['name'] = $val;
					$_result[$key]['num'] = $num;
				}else{
					$num = $mysql->db_count($val);
					$_result[$key]['name'] = $val;
					$_result[$key]['num'] = $num;
				}
			}
		}
		return  $_result;
	
	}
	
	
	/**
	 * 4,修改系统参数
	 * 
	 * @return Array
	 */
	function  ActionSystem($data = array()){
		global $mysql;
		$class = $data["class"];
		$style = $data["style"];
		if ($class == "list"){
			$sql = "select * from {system} where `style` = '$style' order by id asc ";
			return $mysql->db_fetch_arrays($sql);
		}
		
		elseif ($class == "view"){
			$id = $data["id"];
			$sql = "select * from {system} where `style` = '$style' and `id` = '$id' order by id asc";
			return $mysql->db_fetch_array($sql);
		}
		
		elseif ($class == "add"){
			unset($data['class']);
			if (!ereg ("^con_", $data['nid'])){
				return "admin_system_not_con";
			}
			$_sql = "";
			$sql = "select 1 from {system} where nid = '".$data['nid']."' ";
			$result = $mysql -> db_fetch_array($sql);
			if ($result!=false) return  "admin_system_nid_exiest";
			$sql = "insert into `{system}` set ";
			foreach($data as $key => $value){
				$_sql[] = "`$key` = '$value'";
			}
			$result =  $mysql->db_query($sql.join(",",$_sql));
			return $mysql->db_insert_id();;
		}
		
		elseif ($class == "update"){
			unset($data['class']);
			if (!ereg ("^con_", $data['nid'])){
				return self::SYSTEM_ADD_NO_CON;
			}
			
			$sql = "select * from {system} where nid = '".$data['nid']."' and id !=".$data['id'];
			$result = $mysql -> db_fetch_array($sql);
			if ($result!=false) return  self::SYSTEM_NID_IS_EXIST;
			
			$_sql = "";
			$sql = "update `{system}` set ";
			foreach($data as $key => $value){
				$_sql[] = "`$key` = '$value'";
			}
			$result =  $mysql->db_query($sql.join(",",$_sql)." where id = '".$data['id']."'");
			if ($result == false) return self::ERROR;else return true;
			
		}
		
		elseif ($class == "action"){
			foreach ($data['value'] as $key =>$val){
				$val = nl2br($val);
				$sql  = "update {system} set `value` = '{$val}',`name` = '{$data['name'][$key]}' where `nid` = '$key'";
				$mysql->db_query($sql);
			}
			return 1;
		}
		
		//删除系统参数
		elseif ($class == "del"){
			
			$_sql = "";
			if (IsExiest($data['type_id'])!=false){
				$_sql = " and type_id='{$data['type_id']}'";
			}
			$sql = "select status from `{system}`  where `id` = '{$data['id']}' $_sql";
			$result = $mysql->db_fetch_array($sql);
			if ($result==false) return "admin_system_del_error";
			if ($result['status']==0) return "admin_system_not_del";
 			$sql  = "delete from `{system}` where `id` = '{$data['id']}' $_sql";
			$result = $mysql->db_query($sql);
			if ($result==false) return "admin_system_del_error";
			return $data['id'];
		}
	}
	/**
	 * 备份数据表 data = array("table"=>"要备份的表，多个用,隔开","filedir"=>"备份的目录")
	 * 
	 * @return Array
	 */
	public static  function SaveModulesTable($data = array() ){
		global $mysql;
		if (!IsExiest($data['table'])) return "";
		$filedir = "data/".(!IsExiest($data['table']))?"":$data['table'];;
	
	
	}
	/**
	 * 备份数据表
	 * 
	 * @return Array
	 */
	public static  function BackupTables($data = array() ){
		global $mysql;
		$filedir = $data['filedir'];
		$tables = $data['table'];
		$size = $data['size'];
		$tid = $data['tid'];//读取哪个表
		$limit = $data['limit'];//表读取到那几行
		$table_page = $data['table_page'];//文件的分页
		$table = $tables[$tid];
		if ($tables == "") return self::ERROR;
		
		/*
		 *备份表结构
		*/
		if ($tid==0){
			$sql = "";
			$filename = $filedir."/show_table.sql";
			foreach ($tables as $key => $tbl){
				//$sql .="# 数据表　".$tbl."　的结构;\r\n";	
				$sql .="DROP TABLE IF EXISTS `$tbl`;\r\n";//如果表存在就删除存在的表
				$_sql = "show create table $tbl";
				$result = $mysql->db_fetch_array($_sql);
				$sql .= $result['Create Table'].";\r\n\r\n";
				mk_file($filename,$sql);
			}
		}
		
		if ($table != ""){
			$file = $filedir."/".$table."_".$table_page.".sql";
			$text = read_file($file);
			if (strlen($text) > $size * 1024) {
				 $file = $filedir."/".$table."_".($table_page+1).".sql";
				 $text = read_file($file);
			}
			/*
			 *获取表的所有字段
			*/
			$fields = $mysql->db_show_fields(str_replace($mysql->db_prefix,"",$table));
			$_fields = join(",",$fields);
			
			$sql = "select *  from `$table` limit $limit,100";
			
			$result= $mysql->db_fetch_arrays($sql)  ; 
			if (count($result)>0){
				foreach ($result as $key => $value){
					$text .= "insert into `$table` ( ";
					foreach ($fields as $fkey => $fvalue){
						$_value[$fkey] ="\"".mysql_escape_string($value[$fvalue])."\"";
						$_fie[$fkey] ="`$fvalue`";
					}
					$text .= join(",",$_fie).") values (".join(",",$_value).");\r\n\r\n";
					$limit++;
				}
				mk_file($file,$text);
				$data['limit'] = $limit;
				$data['table_page'] = $table_page;
				$data['tid'] = $tid;
			}else{
				$data['limit'] = 0;
				$data['table_page'] = 0;
				$data['tid'] = $tid+1;
			}
			return $data;
		}
		return "";
	}
	
	/**
	 * 备份数据表
	 * 
	 * @return Array
	 */
	public static function RevertTables($data = array() ){
		global $mysql;
		
		$tables = $data['table'];
		$nameid = $data['nameid'];
		if (isset($tables[$nameid]) && $tables[$nameid]!=""){
			$value = $tables[$nameid];
			if ($value!="show_table.sql"){
				$sql = file_get_contents($data['filedir']."/".$value);
				$_sql = explode("\r\n",$sql);
				foreach ($_sql as $val){
					if ($val!=""){
						$mysql->db_query($val,"true");
					}
				}
			}
			return $value;
		}else{
			return "";
		}
	}
	
	/**
	 * 添加菜单
	 *
	 * @param array $data =array("name"=>"名称","nid"=>"别名","pid"=>"父级","contents"=>"描述");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function AddSiteMenu($data = array()){
		global $mysql;
		 //判断类型名称是否存在
        if (!IsExiest($data['name'])) {
            return "admin_site_menu_name_empty";
        }
		
		//判断类型的标示名是否存在
		if (!IsExiest($data['nid'])) {
             return "admin_site_menu_nid_empty";
        }
		//判断菜单是否存在，如果存在的话则将添加的站点自动转化为默认站点
		$sql = "select 1 from `{site_menu}` where checked=1";
		$result = $mysql->db_fetch_array($sql);
	
		if ($result==false) {
			$data["checked"] = 1;
		}
		
		
		$sql = "select 1 from `{site_menu}` where nid='{$data['nid']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "admin_site_menu_nid_exiest";
		if ($data['checked']==1){
			$sql = "update `{site_menu}` set `checked`=0 ";
			$mysql->db_query($sql);
		}
		
		$sql = "insert into `{site_menu}` set ";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql));
    	return $mysql->db_insert_id();
	}
	/**
	 * 修改菜单
	 *
	 * @param array $data =array("name"=>"名称","nid"=>"别名","pid"=>"父级","contents"=>"描述");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function UpdateSiteMenu($data = array()){
		global $mysql;
		 //判断Id是否存在
        if (!IsExiest($data['id'])) {
            return "admin_site_menu_id_empty";
        } 
		
		//判断名称是否存在
        if (!IsExiest($data['name'])) {
            return "admin_site_menu_name_empty";
        }
		
		//判断标示名是否存在
		if (!IsExiest($data['nid'])) {
             return "admin_site_menu_nid_empty";
        }
		$sql = "select 1 from `{site_menu}` where nid='{$data['nid']}' and id!='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "admin_site_menu_nid_exiest";
		
		if ($data['checked']==1){
			$sql = "update `{site_menu}` set `checked`=0 ";
			$mysql->db_query($sql);
		}
		
		$sql = "update `{site_menu}` set ";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql)." where id='{$data['id']}'");
		return $data['id'];
	}
	
		/**
	 * 修改菜单
	 *
	 * @param array $data =array("name"=>"名称","nid"=>"别名","pid"=>"父级","contents"=>"描述");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function UpdateSiteMenuChecked($data = array()){
		global $mysql;
		 //判断Id是否存在
        if (!IsExiest($data['id'])) {
            return "admin_site_menu_id_empty";
        } 
		
		$sql = "update `{site_menu}` set `checked`=0 ";
		$mysql->db_query($sql);
		
		
		$sql = "update `{site_menu}` set `checked`=1 where id='{$data['id']}' ";
		$mysql->db_query($sql);
		
		return $data['id'];
	}
	
	/**
	 * 获得菜单列表
	 *
	 * @return Array
	 */
	function GetSiteMenuList($data = array()){
		global $mysql;
		
		
		$_sql = " where 1=1 ";
		//判断类型名称是否存在
        if (IsExiest($data['nid'])!=false) {
            $_sql .= " and p1.nid ='{$data['nid']}'";
        }
		
		if (IsExiest($data['name'])!=false) {
            $_sql .= " and p1.name like '%{$data['name']}%'";
        }
		
		$_select = " p1.* ";
		$_order = " order by p1.checked desc,p1.order desc ,p1.id desc";
		$sql = "select SELECT from `{site_menu}` as p1 SQL ORDER ";
		
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
		$_limit = " limit ".($epage * ($page - 1)).", {$data['epage']}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL','ORDER', 'LIMIT'), array($_select,$_sql,$_order, $_limit), $sql));
		
		//返回最终的结果
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		return $result;
	}
	
	/**
	 * 5,获得菜单的列表
	 *
	 * @param Array $data = array("id"=>"");
	 * @return Array
	 */
	public static function GetSiteMenuOne($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "admin_site_menu_id_empty";
		$sql = "select * from `{site_menu}` where id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "admin_site_menu_empty";
		return $result;
	}
	
	/**
	 * 7,删除类型
	 *
	 * @param Array $data = array("id"=>"ID")
	 * @return Boolen
	 */
	function DelSiteMenu($data = array()){
		global $mysql;
		
		if (!IsExiest($data['id'])) return "admin_site_menu_id_empty";
		$sql = "select count(1) as num from `{site_menu}`";
		$result = $mysql->db_fetch_array($sql);
		if ($result['num']==1) return "admin_site_menu_only_one";
		//判断是否有子类
		$sql = "select 1 from `{site}` where menu_id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "admin_site_menu_site_exiest";
		if ($result['checked']==1){
			$sql = "update  `{site_menu}` set checked=1 limit 1";
			$mysql->db_query($sql);
		}
		
		$sql = "delete from `{site_menu}`  where id='{$data['id']}'";
    	$mysql -> db_query($sql);
		
		return $data['id'];
	}
	
	/**
	 * 获得列表
	 *
	 * @return Array
	 */
	 /**
	 * 2,类型列表
	 * $data = array("user_id"=>"用户id","username"=>"用户名");
	 * @return Array
	 */
	function GetSiteList($data = array()){
		global $mysql;
		
		$_sql = "where 1=1 ";	
		if ($data['pid']!=""){
			$_sql .= " and p1.pid='{$data['pid']}'";
		}
		if ($data['status']!=""){
			$_sql .= " and p1.status='{$data['status']}'";
		}
		$_order = " order by p1.order desc ,p1.id asc ";
		
		$_select = " p1.*";
		$sql = "select SELECT from `{site}` as p1  SQL ORDER LIMIT ";
		
		//是否显示全部的信息
		
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
		
		return $result;
	}
	
	/**
	 * 3,添加类型
	 *
	 * @param Array $data = array("name"=>"名称","nid"=>"别名","contents"=>"内容","order"=>"排序")
	 * @return Boolen
	 */
	function AddSite($data = array()){
		global $mysql;
		if (!IsExiest($data['name'])) return "admin_site_name_empty";
		if (!IsExiest($data['nid'])) return "admin_site_nid_empty";
		$sql = "select 1 from `{site}` where nid='{$data['nid']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "admin_site_nid_exiest";
		
		
		$sql = "insert into `{site}` set ";
		$_sql = array();
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql));
    	$id = $mysql->db_insert_id();
		
		return $id;
	}
	
	
	/**
	 * 4,类型分类
	 *
	 * @param Array $data = array("name"=>"名称","nid"=>"别名","contents"=>"内容","order"=>"排序")
	 * @return Boolen
	 */
	function GetSite($data = array()){
		global $_G,$mysql;
		//获取站点的列表
		if (isset($_G["site"])){
			$result = $_G['site'];
		}else{
			//$result = self::GetSiteList(array("limit"=>"all"));
		}
		//获取站点默认的菜单
		if (!IsExiest($data['menu_id'])){
			if (isset($_G["site_menu_id"])){
				$data['menu_id'] = $_G['site_menu_id'];
			}else{
				$sql = "select * from `{site_menu}` where checked=1";
				$_result = $mysql->db_fetch_array($sql);
				$data['menu_id'] = $_result['id'];
			}
		}
		
		$_result = array();
		$_res_pid = array();
		$var = "&nbsp;&nbsp;&nbsp;&nbsp;";
		$type_var="—";
		foreach ($result as  $key => $value){
			if ($value['menu_id']==$data['menu_id']){
				$site_result[$value['id']] = $value;
				$_res_pid[$value['pid']][] = $value['id'];
			}
		}
		if (IsExiest($data['lgnore'])!=false){
			unset($_res_pid[$data['lgnore']]);
		}
		if (count($_res_pid)>0){
			foreach ($_res_pid[0] as $key => $value){
				$_result[$value] = $site_result[$value];
				$_result[$value]['_name'] = $_result[$value]['name'];
				$_result[$value]['type_name'] = $_result[$value]['name'];
				
				$_site_data['site_result'] = $site_result;
				$_site_data['result'] = $_res_pid;
				$_site_data['_result'] = $_res_pid[$value];
				$_site_data['var'] = $var;
				$_site_data['type_var'] = $type_var;
				$_result = $_result +  self::_GetSite($_site_data);
					
			}
		}
		if (IsExiest($data['lgnore'])!=false){
			unset($_result[$data['lgnore']]);
		}
		return $_result;
	}
	function _GetSite($_site_data){
		$var = "&nbsp;&nbsp;&nbsp;&nbsp;";
		$type_var="—";
		$_var = $_site_data["var"];
		$_type_var = $_site_data["type_var"];
		$_result = array();
		if (isset($_site_data['_result']) && $_site_data['_result']!=""){
			foreach ($_site_data['_result'] as $key => $value){
				$_result[$value] = $_site_data["site_result"][$value];
				$_result[$value]['_name'] = $_var.$_result[$value]['name'];
				$_result[$value]['type_name'] = $_type_var.$_result[$value]['name'];
				$_site_data["result"][$value] = isset($_site_data["result"][$value])?$_site_data["result"][$value]:"";
				$_site_data['_result'] = $_site_data["result"][$value];
				$_site_data['var'] = $_site_data['var'].$var;
				$_site_data['type_var'] = $_site_data['type_var'].$type_var;
				$_result = $_result +  self::_GetSite($_site_data);
			}
		}
		return $_result;
	}
	
	/**
	 * 4,类型分类
	 *
	 * @param Array $data = array("name"=>"名称","nid"=>"别名","contents"=>"内容","order"=>"排序")
	 * @return Boolen
	 */
	function GetSites($data = array()){
		global $_G,$mysql;
		//获取站点的列表
		if (isset($_G["site"])){
			$result = $_G['site'];
		}else{
			$result = self::GetSiteList(array("limit"=>"all"));
		}
		//获取站点默认的菜单
		$data['menu_id'] = isset($data['menu_id'])?$data['menu_id']:"";
		if (!IsExiest($data['menu_id'])){
			if (isset($_G["site_menu_id"])){
				$data['menu_id'] = $_G['site_menu_id'];
			}else{
				$sql = "select * from `{site_menu}` where checked=1";
				$_result = $mysql->db_fetch_array($sql);
				$data['menu_id'] = $_result['id'];
			}
		}
		$_result = array();
		$var = "&nbsp;&nbsp;&nbsp;&nbsp;";
		$type_var="—";
		if (IsExiest($data['menu_id'])!=false){
			foreach ($result as  $key => $value){
				$_res[$value['id']]['pid'] = $value['pid'];
				if ($value['pid']==0 && $value['menu_id']==$data['menu_id']){
					$_result[$value['id']] = $value;
					$_result[$value['id']]['_name'] = $value['name'];
					$_result[$value['id']]['type_name'] = $value['name'];
					$_result[$value['id']]['var'] = "";
					if ($value['nid']=="index"){
						$_result[$value['id']]['url'] = "/";
					}elseif ($value['type']=="url"){
						$_result[$value['id']]['url'] = $value['value'];
					}else{
						$_result[$value['id']]['url'] = "/{$value['nid']}/index.html";
					}
					$_result[$value['id']]['list_result'] = self::_GetSites($result,$value['id'],$var,$type_var);
					;
				}
			}
		}else{
			foreach ($result as  $key => $value){
				$_res[$value['id']]['pid'] = $value['pid'];
				if ($value['pid']==0){
					
					$_result[$value['id']] = $value;
					$_result[$value['id']]['_name'] = $value['name'];
					$_result[$value['id']]['type_name'] = $value['name'];
					$_result[$value['id']]['var'] = "";
					if ($value['type']=="url"){
						$_result[$value['id']]['url'] = $value['value'];
					}else{
						$_result[$value['id']]['url'] = "/{$value['nid']}/index.html";
					}
					$_result[$value['id']]['list_result'] = self::_GetSites($result,$value['id'],$var,$type_var);
					
				}
			}
		}
		return $_result;
	}
	function _GetSites($result,$pid,$var,$type_var){
		$_result = array();
		$_var = "&nbsp;&nbsp;&nbsp;&nbsp;";
		$_type_var="—";
		$opid = "";
		foreach ($result as  $key => $value){
			if ($value['pid'] == $pid){
				if ($opid==""){
					$_result[$value['id']] = $value;
					$_result[$value['id']]['_name'] = $var.$value['name'];
					$_result[$value['id']]['type_name'] = $type_var.$value['name'];
					$_result[$value['id']]['var'] = $var.$_var;	
					if ($value['type']=="url"){
						$_result[$value['id']]['url'] = $value['value'];
					}else{
						$_result[$value['id']]['url'] = "/{$value['nid']}/index.html";
					}
					$_result[$value['id']]['list_result'] = self::_GetSites($result,$value['id'],$var.$_var,$type_var.$_type_var);
				}else{
					$_result[$value['id']] = $value;
					$_result[$value['id']]['_name'] = $var.$value['name'];
					$_result[$value['id']]['type_name'] = $type_var.$value['name'];
					$_result[$value['id']]['var'] = $var.$_var;	
					if ($value['type']=="url"){
						$_result[$value['id']]['url'] = $value['value'];
					}else{
						$_result[$value['id']]['url'] = "/{$value['nid']}/index.html";
					}
					$_result[$value['id']]['list_result'] = self::_GetSites($result,$value['id'],$var.$_var,$type_var.$_type_var);
				}
			}
		}
		return $_result;
	}

	
	/**
	 * 5,获得类型的列表
	 *
	 * @param Array $data = array("id"=>"");

	 * @return Array
	 */
	public static function GetSiteOne($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "admin_site_id_empty";
		$sql = "select * from `{site}` where id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "admin_site_empty";
		return $result;
	}
	
	
	/**
	 * 6,修改类型
	 *
	 * @param Array $data = array("id"=>"ID","name"=>"名称","nid"=>"别名","contents"=>"内容","order"=>"排序")
	 * @return Boolen
	 */
	function UpdateSite($data = array()){
		global $mysql;
		if (!IsExiest($data['name'])) return "admin_site_name_empty";
		if (!IsExiest($data['nid'])) return "admin_site_nid_empty";
		$sql = "select 1 from `{site}` where nid='{$data['nid']}' and id!='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "admin_site_nid_exiest";
		$sql = "update `{site}` set ";
		$_sql = array();
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql)." where id='{$data['id']}'");
    	
		return $data['id'];
	}
	
	/**
	 * 7,删除类型
	 *
	 * @param Array $data = array("id"=>"ID")
	 * @return Boolen
	 */
	function DelSite($data = array()){
		global $mysql;
		
		//判断是否存在
		$sql = "select 1 from `{site}` where id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "admin_site_not_exiest";
		
		//判断是否有子类
		$sql = "select 1 from `{site}` where pid='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "admin_site_pid_exiest";
		
		$sql = "delete from `{site}`  where id='{$data['id']}'";
    	$mysql -> db_query($sql);
		
		return $data['id'];
	}
	/**
	 * 8,获得站点的单个信息
	 *
	 * @param Array $data = array("id"=>"");

	 * @return Array
	 */
	public static function GetSiteOnes($data = array()){
		global $_G;
		//获取站点的列表
		if (isset($_G["site"])){
			$result = $_G['site'];
		}else{
			$result = self::GetSiteList(array("limit"=>"all"));
		}
		$_result = false;
		foreach ($result as $key => $value){
			if ($value["nid"]==$data['nid']){
				$_result = $value;
			}
		}
		
		return $_result;
	}
	
	
	function ActionSite($data){
		global $mysql;
		if ($data['id']!=""){
			foreach ($data['id'] as $key => $value){
				$sql = "update `{site}` set `order` = '{$data['order'][$key]}' where id='{$value}'";
				$mysql->db_query($sql);
			}
		
		}
	
	}
	/**
	 * 1,获得类型的列表
	 *
	 * @param Array $data = array("id"=>"");

	 * @return Array
	 */
	public static function GetSystemTypeOne($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "admin_system_type_id_empty";
		$sql = "select * from `{system_type}` where id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "admin_system_type_empty";
		return $result;
	}
	
	
	/**
	 * 2,修改类型
	 *
	 * @param Array $data = array("id"=>"ID","name"=>"名称","nid"=>"别名")
	 * @return Boolen
	 */
	function UpdateSystemType($data = array()){
		global $mysql;
		if (!IsExiest($data['name'])) return "admin_system_type_name_empty";
		if (!IsExiest($data['nid'])) return "admin_system_type_nid_empty";
		$sql = "select 1 from `{system_type}` where nid='{$data['nid']}' and id!='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "admin_system_type_nid_exiest";
		$sql = "update `{system_type}` set ";
		$_sql = array();
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql)." where id='{$data['id']}'");
    	
		return $data['id'];
	}
	
	
	/**
	 * 3,添加类型
	 *
	 * @param Array $data = array("id"=>"ID","name"=>"名称","nid"=>"别名")
	 * @return Boolen
	 */
	function AddSystemType($data = array()){
		global $mysql;
		if (!IsExiest($data['name'])) return "admin_system_type_name_empty";
		if (!IsExiest($data['nid'])) return "admin_system_type_nid_empty";
		$sql = "select 1 from `{system_type}` where nid='{$data['nid']}' ";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "admin_system_type_nid_exiest";
		$sql = "insert into `{system_type}` set ";
		$_sql = array();
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql));
    	
		return $mysql->db_insert_id();
	}
	
	/**
	 * 4,删除类型
	 *
	 * @param Array $data = array("id"=>"ID")
	 * @return Boolen
	 */
	function DeleteSystemType($data = array()){
		global $mysql;
		
		//判断是否存在
		$sql = "select nid from `{system_type}` where id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "admin_system_type_empty";
		$sql = "select 1 from `{system}` where code='{$result['nid']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "admin_system_type_code_exiest";
		$sql = "delete from `{system_type}`  where id='{$data['id']}'";
    	$mysql -> db_query($sql);
		
		return $data['id'];
	}
	
	/**
	 * 5,列表
	 *
	 * @return Array
	 */
	function GetSystemTypeList($data = array()){
		global $mysql;
		
		$_sql = " where 1=1 ";
		
		
		if (IsExiest($data['status'])!=false || $data['status']=="0") {
            $_sql .= " and p1.status = '{$data['status']}'";
        }
		$_select = " p1.*,p2.name as module_name ";
		$_order = " order by p1.id desc";
		$sql = "select SELECT from `{system_type}` as p1 left join `{modules}` as p2 on p1.code=p2.nid SQL ORDER LIMIT ";
		
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
	
	
	
	/**
	 * 获得图片
	 * 
	 * @return Array
	 */
	function GetUpfiles($data = array()){
		global $mysql,$_G;
		$_sql = " where 1=1 ";
		if (isset($data['username']) && $data['username']!=""){
			$_sql .= " and p2.username like '%{$data['username']}%'";
		}
		if (isset($data['quer']) && $data['quer']!=""){
			$_sql .= " and p1.query like '%{$data['quer']}%'";
		}
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
		
		$_select = "p1.*,p2.username";
		$_order = " order by p1.id desc";
		$sql = "select SELECT from `{users_upfiles}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id  {$_sql} ORDER LIMIT";
		
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num', '', ''), $sql));
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
	 * 修改图片
	 * 
	 * @return Array
	 */
	function UpdateUpfiles($data = array()){
		global $mysql;
		if (count($data['id']>0) && $data["id"]!=""){
			foreach($data['id'] as $key => $value){
				//$contents = iconv('UTF-8', 'GB2312',$data['contents'][$key]);
                $contents = $data['contents'][$key];
				$sql = "update `{users_upfiles}` set contents='{$contents}' where id='{$value}'";
				$mysql->db_query($sql);
			}
		}
        return "";
	}
}
?>