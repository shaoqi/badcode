<?php
/******************************
 * $File: admin.module.php
 * $Description: 模块类处理
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

class moduleClass {
	
	
	/**
	 * 获得模块列表
	 *
	 * @return Array
	 */
	function GetModuleList($data = array()){
		global $mysql;
		
		if ($data['type']=="system"){
			$_sql = " where type='system' ";
		}elseif ($data['type']=="all"){
			$_sql = " where 1=1 ";
		}else{
			$_sql = " where type!='system' ";
		}
		//判断类型名称是否存在
		$data['nid'] = isset($data['nid'])?$data['nid']:"";
        if (IsExiest($data['nid'])!=false) {
            $_sql .= " and p1.nid ='{$data['nid']}'";
        }
		
		$data['name'] = isset($data['name'])?$data['name']:"";
		if (IsExiest($data['name'])!=false) {
            $_sql .= " and p1.name like '%{$data['name']}%'";
        }
		
		$_select = " p1.* ";
		$_order = " order by p1.id desc";
		$sql = "select SELECT from `{modules}` as p1 SQL ORDER ";
		$_limit = "";
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
	
	function GetModule( $data){
		global $mysql;
		if (!IsExiest($data['nid'])) return "admin_module_nid_empty";
		$sql = "select * from `{modules}` where nid='{$data['nid']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false){
			$code = $data['nid'];
			$result = array_merge(self::GetModuleInfo($code),array("code"=>$code));
			$result['nid'] = $result['code'];
			return $result;	
		}else{
			return $result;
		}
	}
	
	function GetModulePurview( $data){
		global $mysql,$_G;
		$_sql = "";
		
		
		$result = $_G['module'];
		
		$_purview = array();
		if ($result!=false){
			foreach($result as $key => $value){
				if ($value['purview']!=""){
					$_purview = array_merge($_purview,unserialize(html_entity_decode($value['purview'])));
				}
			}
			if (IsExiest($data['code'])!=false){
				$_purview[$data['code']]['result'] = isset($_purview[$data['code']]['result'])?$_purview[$data['code']]['result']:"";
				$result =   $_purview[$data['code']]['result'];
				if (IsExiest($data['type_id']!=1)){
					if (IsExiest($data['purview'])!=false){
						$purview = explode(",",$data['purview']);
						$_result = array();
						if ($result!=""){
						foreach ($result as $key => $value){
							if (in_array($key,$purview)){
								$_result[$key] = $value;
							}
						}
						}
						$result = $_result;
					}
				}
				return $result;
				
			}else{
			
				return $_purview;
			}
		}else{
			return $result;
		}
	}
	
	/**
	 * 获得用户的权限
	 *
	 * @param Array $fields_id 
	 * @param Array $order 
	 * @return Integer
	 */
	function GetModuleAdmin($data){
		global $mysql;
		if (isset($data['user_id']) && $data['user_id']==""){
			return "";
		}
		
		//第一步。管理员类别，并且读取类型的所有权限
		$sql = "select p1.*,p2.purview from `{users_admin}` as p1 left join `{users_admin_type}` as p2 on p1.type_id=p2.id where p1.user_id='{$data['user_id']}'";
		$result = $mysql -> db_fetch_array($sql);
		$purview = explode(",",$result['purview']);//分解权限
		//第二步。读取除了系统参数以外的所有的模块，并按照排序
		$sql = "select * from `{modules}`  order by `order` desc,id desc";
		$module_result = $mysql->db_fetch_arrays($sql);
		
		//第三步。获取权限所有的值
		$purview_all = array();
		$purview_top = array();
		$purview_other = array();
		$_purview_top_first = array('articles','users','site','system');
		$_purview_other_first = array('areas','linkages','message');
		$i=0;
		foreach ($module_result as  $key => $value){
			if ($value['purview']!="" && $value["type"]!='system'){
				if ($value['status']==1 && $i<6){
					$purview_top = array_merge($purview_top,unserialize(html_entity_decode($value['purview'])));
					$i++;
				}else{
					$purview_other = array_merge($purview_other,unserialize(html_entity_decode($value['purview'])));
				}
			}else{
				$purview_top_other = array_merge($purview_top,unserialize(html_entity_decode($value['purview'])));
			}
			$purview_all = array_merge($purview_all,unserialize(html_entity_decode($value['purview'])));
		}
		
		if ($result['type_id']== 1){//表示系统管理员
			//第二步。读取除了系统参数以外的所有的模块，并按照排序
			$sql = "select * from `{modules}` where type='system'  order by `order` desc,id desc";
			$module_system_result = $mysql->db_fetch_arrays($sql);
			foreach ($module_system_result as $key => $value){
				if (in_array($value['nid'],$_purview_top_first) || $value['nid']=="admin"){
					$purview_top = array_merge($purview_top,unserialize(html_entity_decode($value['purview'])));
				}
				if (in_array($value['nid'],$_purview_other_first) ){
					$purview_other = array_merge($purview_other,unserialize(html_entity_decode($value['purview'])));
				}
			}
			return array("all"=>$purview_all,"top"=>$purview_top,"other"=>$purview_other,"purview"=>"");
		}else{
			$_purview_all = array();
			
			
			
			//默认的几个模块
			foreach ($purview_top_other as $key => $value){
				foreach ($value['result'] as $_key=>$_value){
					if (in_array($_key,$purview)){
						$_purview_top[$key] = $value;
					}
				}
			}
			
			foreach ($purview_all as $key => $value){
				foreach ($value['result'] as $_key=>$_value){
					if (in_array($key,$_purview_top_first) && in_array($_key,$purview)){
						$_purview_top[$key] = $value;
					}
				}
			}
			
			foreach ($purview_all as $key => $value){
				foreach ($value['result'] as $_key=>$_value){
					if (in_array($_key,$purview)){
						$_purview_all[$key] = $value;
					}
					if (in_array($key,$_purview_other_first) && in_array($_key,$purview)){
						$_purview_other[$key] = $value;
					}
				}
				
		   }
			
			
			
			foreach ($purview_other as $key => $value){
				foreach ($value['result'] as $_key=>$_value){
					if (in_array($_key,$purview)){
						$_purview_other[$key] = $value;
					}
				}
			}
		}
		
		return  array("all"=>$_purview_all,"top"=>$_purview_top,"other"=>$_purview_other,"purview"=>$purview);
	}
	 
	 
	 
	/**
	 * 修改
	 *
	 * @param Array $fields_id 
	 * @param Array $order 
	 * @return Integer
	 */
	public static function UpdateModule($data){
		global $mysql;
		if (!IsExiest($data['nid'])) return "admin_module_nid_empty";
		$sql = "select id from `{modules}` where nid='{$data['nid']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "admin_module_empty"; 
		$id = $result['id'];
		
		$sql = "update `{modules}` set ";
		foreach ($data as $key => $value){
			$_sql[] = "`$key`='$value'";
		}
		$sql .= join(",",$_sql);
		$sql .= " where nid = '{$data['nid']}'";
		$result =  $mysql->db_query($sql);
		
		//更新权限的信息
		$nid = $data['nid'];
		$_A['list_purview'] = array();
		if (file_exists(DEAYOU_PATH."modules/$nid/".$nid.".php")){
			require_once(DEAYOU_PATH."modules/$nid/".$nid.".php");
		}
       
		$purview = serialize($_A['list_purview']);
		$sql = "update `{modules}` set purview='{$purview}' where nid='{$nid}'";
		$mysql->db_query($sql);
				
		return $id;
	}
	
	
	/**
	 * 添加模块
	 *
	 * @param Array $fields_id 
	 * @param Array $order 
	 * @return Integer
	 */
	public static function AddModule($data){
		global $mysql;
		if (!IsExiest($data['nid'])) return "admin_module_nid_empty";
		$code = $nid = $data['nid'];
		//检查模块是否安装
		$sql = "select 1 from `{modules}` where nid='{$data['nid']}'";
		$result = $mysql->db_fetch_array($sql);
		if($result !=false) return "admin_module_exiest";
		
		//执行数据表
		$url = DEAYOU_PATH."modules/$code/".$code.".install.php";
		if (file_exists($url)){
			require_once($url);
		}
		
		//插入模块表
		$sql = "insert into  `{modules}` set ";
		foreach ($data as $key => $value){
			$_sql[] = "`$key`='$value'";
		}
		$sql .= join(",",$_sql);
		$sql .= ",`addtime`='".time()."',`addip`='".ip_address()."'";
		$mysql->db_query($sql);
		$id = $mysql->db_insert_id();
		//更新权限的信息
		$_A['list_purview'] = array();
		if (file_exists(DEAYOU_PATH."modules/$nid/".$nid.".php")){
			require_once(DEAYOU_PATH."modules/$nid/".$nid.".php");
		}
		$purview = serialize($_A['list_purview']);
		$sql = "update `{modules}` set purview='{$purview}' where nid='{$nid}'";
		$mysql->db_query($sql);
		return $id;
		
	}
	
	
	
	/**
	 * 卸载模块
	 */
	function DeleteModule($data = array()){
		global $mysql;
		
		if (!IsExiest($data['nid'])) return "admin_module_nid_empty";
		
		$sql = "select nid from `{modules}` where nid='{$data['nid']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "admin_module_empty"; 
			
		$code = $result['nid'];
		
		
		//执行数据表
		$url = DEAYOU_PATH."modules/$code/".$code.".unstall.php";
		if (file_exists($url)){
			require_once($url);
		}
		
		
		$sql = "delete from {modules} where nid='$code' and type!='system'";
		$mysql->db_query($sql);
	
		return true;
		
	}
	
	/**
	 * 备份数据表 data = array("table"=>"要备份的表，多个用,隔开","filedir"=>"备份的目录","page"=>"分页","limit"=>"条数")
	 * 
	 * @return Array
	 */
	public static  function SaveModules($data = array() ){
		global $mysql;
		if (!IsExiest($data['nid'])) return "";
		if (!IsExiest($data['table'])) return "";
		$table = $data['table'];
		$limit = (!IsExiest($data['limit']))?0:$data['limit'];
		
		$table_page = (!IsExiest($data['page']))?1:$data['page'];
		$filedir = "modules/{$data['nid']}/dbback/".date("Ymd",time());
		$file = $filedir."/".$table."_".$table_page.".sql";
		create_file($file);
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
		
		$sql = "select * from `{".$table."}` ";
		
		$result= $mysql->db_fetch_arrays($sql)  ; 
		if (count($result)>0){
			foreach ($result as $key => $value){
				$text .= "insert into {".$table."}` ( ";
				foreach ($fields as $fkey => $fvalue){
					$_value[$fkey] ="\"".mysql_escape_string($value[$fvalue])."\"";
					$_fie[$fkey] ="`$fvalue`";
				}
				$text .= join(",",$_fie).") values (".join(",",$_value).");\r\n\r\n";
				$limit++;
			}
			create_file($file,$text);
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
	
	
	
	//获得模块的列表
	function  GetModuleFileList($data = array()){
		global $mysql;
		
		//模块内所有的模块
		$module_file = get_file("modules");
		
		if ($data['type']=="system"){
			//对已安装的模块进行处理
			$sql = "select * from `{modules}` where type='system'  order by `order` desc,id desc ";
			$module_list = $mysql->db_fetch_arrays($sql);
			if ($module_list!=false){
				foreach ($module_list as $key => $value){
					$code = $value['nid'];
					$url = DEAYOU_PATH."modules/$code/".$code.".info";
					if (file_exists($url)){
						require_once($url);
						$module_list[$key]["version_new"] = $version;
					}
				}
			}
			return $module_list;
		}
		elseif ($data['type']=="install"){
			//对已安装的模块进行处理
			$sql = "select * from `{modules}` where type!='system'  order by `status` desc,`order` desc ";
			$module_list = $mysql->db_fetch_arrays($sql);
			if ($module_list!=false){
				foreach ($module_list as $key => $value){
					$code = $value['nid'];
					$url = DEAYOU_PATH."modules/$code/".$code.".info";
					if (file_exists($url)){
						require_once($url);
						$module_list[$key]["version_new"] = $version;
					}
				}
			}
			return $module_list;
		
		}
		
		else{
			//对已安装的模块进行处理
			$sql = "select * from `{modules}`  order by `order` desc ";
			$module_list = $mysql->db_fetch_arrays($sql);
			//对已安装的模块进行处理
			$_module_list = array();
			if ($module_list!=false){
				foreach ($module_list as $key => $value){
					$_module_list[] = $value['nid'];
				}
			}
			$result = "";
			foreach($module_file as $code){
				$type = "";
				$url = DEAYOU_PATH."modules/$code/".$code.".info";
				if (file_exists($url)){
					require_once($url);
					if (!in_array($code,$_module_list) && $type!="system"){
						$result[] = array_merge(self::GetModuleInfo($code),array("code"=>$code));
					}
				}
			}
			return $result;
		}
		
	}
	
	
	function GetModuleInfo ($module){
		$var = array("code","name","version","description","author","date","update","type");
		if ($module_dir=="") $module_dir = DEAYOU_PATH."modules/$module/";
		include ($module_dir."".$module.".info");
		foreach($var as $val){
			$result[$val] = empty($$val)?"":$$val;
		}
		return $result;
	}
	
	
	
	function UpdateModuleSystem($data){
		global $mysql;
		
		//更新某一模块
		if (IsExiest($data['nid'])!=false){
			$nid = $data['nid'];
			if (file_exists("modules/$nid/".$nid.".info")){
				$url = "modules/$nid/".$nid.".info";
				require_once($url);
				
				//更新模块的信息
				if (file_exists(DEAYOU_PATH."modules/$nid/".$nid.".update.php")){
					require_once(DEAYOU_PATH."modules/$nid/".$nid.".update.php");
				}
				$sql = "update `{modules}` set name='{$name}',version='{$version}',version_new='{$version}',date='{$date}',description='{$description}',author='{$author}',`update`='{$update}',type='{$type}' where nid='{$nid}'";
				$mysql->db_query($sql);
				
				//更新权限的信息
				$_A['list_purview'] = array();
				if (file_exists(DEAYOU_PATH."modules/$nid/".$nid.".php")){
					require_once(DEAYOU_PATH."modules/$nid/".$nid.".php");
				}
                 require(DEAYOU_PATH."libs/getdir.class.php");
        
				$purview = serialize($_A['list_purview']);
				$sql = "update `{modules}` set purview='{$purview}' where nid='{$nid}'";
				$mysql->db_query($sql);
			}
		}
		
		//模块内所有的模块
		else{
			$module_file = get_file(DEAYOU_PATH."/modules");
			foreach($module_file as $nid){
				//更新权限的信息
				
				if (file_exists(DEAYOU_PATH."modules/$nid/".$nid.".info")){
					$url = DEAYOU_PATH."modules/$nid/".$nid.".info";
					require_once($url);
					$sql = "select 1 from `{modules}` where nid='{$nid}'";
					$result = $mysql->db_fetch_array($sql);
					if ($result!=false){
						if (file_exists(DEAYOU_PATH."modules/$nid/".$nid.".update.php")){
							require_once(DEAYOU_PATH."modules/$nid/".$nid.".update.php");
						}
						$sql = "update `{modules}` set name='{$name}',nid='{$nid}',version='{$version}',version_new='{$version}',date='{$date}',description='{$description}',author='{$author}',`update`='{$update}',type='{$type}' where nid='{$nid}'";
						
						$mysql->db_query($sql);
						
					}else{
						if ($type=="system"){
							$sql = "insert into `{modules}` set name='{$name}',nid='{$nid}',version='{$version}',version_new='{$version}',date='{$date}',description='{$description}',author='{$author}',`update`='{$update}',type='{$type}'";
							$mysql->db_query($sql);
						}
					}
					$_A['list_purview'] = array();
					if (file_exists(DEAYOU_PATH."modules/$nid/".$nid.".php")){
						require_once(DEAYOU_PATH."modules/$nid/".$nid.".php");
					}
                    require_once(DEAYOU_PATH."libs/getdir.class.php");
                     $dir= DEAYOU_PATH."modules/$nid";
                    $file_result = getdirClass::getFile($dir);
                    foreach ($file_result as $_key => $_value){
                        $_file = explode(".",$_value);
                        if (count($_file)==4 && $_file[2]="inc"){
                            require_once(DEAYOU_PATH."modules/$nid/".$_value);
                        }
                        
                    }
					$purview = serialize($_A['list_purview']);
					$sql = "update `{modules}` set purview='{$purview}' where nid='{$nid}'";
					$result = $mysql->db_query($sql);
				}
			}
		}		
	}
	
	//修改模块的排序和状态
	function ActionModule($data){
		global $mysql;
		foreach ($data['id'] as $key => $value){
			$sql ="update `{modules}` set `order`='{$data['order'][$key]}',`status`='{$data['status'][$key]}' where id='{$value}'";
			$mysql->db_query($sql);
		}
		return 1;
	}
	
	//判断是否需要强制菜单，1表示需要，0表示不需要
	function GetModuleStatus($data){
		global $mysql;
		$sql = "select * from `{modules}` where nid='{$data['nid']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result['status']==0){
			if ($result['type']=="system") return 0;
			return 1;
		}else{
			$id = $result['id'];
			$sql = "select count(1) as num from `{modules}` where  status=1 and type!='system' and id>$id order by `order` desc,id asc";
			$result = $mysql->db_fetch_array($sql);
			if ($result['num']<=6){
				return 0;
			}else{
				return 1;
			}
		}
	}
}
?>
