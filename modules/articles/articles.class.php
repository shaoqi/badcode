<?
/******************************
 * $File: articles.class.php
 * $Description: 文章的类文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

require_once("articles.model.php");

$articles_flag = array("index"=>"首页","ding"=>"置顶","tuijian"=>"推荐");

class articlesClass{
	

	/**
	 * 1,获得文章列表
	 *
	 * @return Array
	 */
	 /**
	 * $data = array("user_id"=>"用户id","username"=>"用户名");
	 * @return Array
	 */
	function GetList($data = array()){
		global $mysql;
		
		$_sql = "where 1=1 ";	
		
		//判断用户id
		if (IsExiest($data['user_id']) != false){
			$_sql .= " and p1.user_id = {$data['user_id']}";
		}
		
		//搜到用户名
		if (IsExiest($data['username']) != false){
			$_sql .= " and p2.username like '%{$data['username']}%'";
		}
		
		//搜到用户名
		if (IsExiest($data['name']) != false){
			$_sql .= " and p1.`name` like '%".addslashes(urldecode($data['name']))."%'";
		}
		//判断用户id
		if (IsExiest($data['public']) != false){
			$_sql .= " and p1.public = {$data['public']}";
		}
		
		//判断用户id
		if (IsExiest($data['type_pid']) != false){
			$_asql = "select id from `{articles_type}` where pid='{$data['type_pid']}'";
			$result = $mysql->db_fetch_arrays($_asql);
			if ($result!=false){
				$_sql .= " and (  FIND_IN_SET('{$data['type_pid']}',p1.type_id) ";
				foreach ($result as  $key => $value){
					$_sql .= "  or FIND_IN_SET('{$value['id']}',p1.type_id) ";
				}
				$_sql .= " )";
			}
		}
		
		//判断用户id
		if (IsExiest($data['type_nid']) != false){
			$_asql = "select id from `{articles_type}` where nid='{$data['type_nid']}'";
			$result = $mysql->db_fetch_array($_asql);
			if ($result!=false){
				$_sql .= "  and FIND_IN_SET('{$result['id']}',p1.type_id) ";
				
			}
		}
		
		//搜到用户名
		if (IsExiest($data['type_id']) != false){
			$_sql .= " and FIND_IN_SET('{$data['type_id']}',p1.type_id)";
		}
		//搜到用户名
		if (IsExiest($data['site_id']) != false){
			$sql = "select `value`,nid from `{site}` where id='{$data['site_id']}'";
			$result = $mysql->db_fetch_array($sql);
			$site_nid = $result['nid'];
			if ($result!=false){
				$_sql .= " and FIND_IN_SET('{$result['value']}',p1.type_id)";
			}
		}elseif (IsExiest($data['site_nid']) != false){
			$sql = "select `value`,nid from `{site}` where nid='{$data['site_nid']}'";
			$result = $mysql->db_fetch_array($sql);
			$site_nid = $result['nid'];
			if ($result!=false){
				$_sql .= " and FIND_IN_SET('{$result['value']}',p1.type_id)";
			}
		}
		
		$_order = " order by p1.order desc,p1.id desc ";
		if (IsExiest($data['order']) != false){
			if ($data['order'] == "id_desc"){
				$_order = " order by p1.id desc ";
			}elseif ($data['order'] == "id_asc"){
				$_order = " order by p1.id asc ";
			}elseif ($data['order'] == "order_desc"){
				$_order = " order by p1.`order` desc ,p1.id desc";
			}elseif ($data['order'] == "order_asc"){
				$_order = " order by p1.`order` asc,p1.id desc";
			}
		}
		
		$_select = " p1.*,p0.name as type_name,p0.nid as type_nid,p2.username,p3.fileurl";
		$sql = "select SELECT from `{articles}` as p1 
				  left join {articles_type} as p0 on p1.type_id=p0.id
				  left join {users} as p2 on p1.user_id=p2.user_id
				 left join {users_upfiles} as p3 on p1.litpic=p3.id
				 SQL ORDER LIMIT
				";
		
		//是否显示全部的信息
		if (IsExiest($data['limit'])!=false){
			if ($data['limit'] != "all"){ $_limit = "  limit ".$data['limit']; }
			$result =  $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array($_select, $_sql, $_order, $_limit), $sql));
			if ($site_nid!=""){
				foreach ($result as $key =>$value){
					$result[$key]["site_nid"] = $site_nid;
				}
			}
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
		$result = array('list' => $list?$list:array(),'site_nid' => $site_nid,'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		
		return $result;
	}
	
	/**
	 * 2，获得类型类别列表
	 * $data = array("user_id"=>"用户id","username"=>"用户名");
	 * @return Array
	 */
	function GetTypeList($data = array()){
		global $mysql;
		
		$_sql = "where 1=1 ";	
		//判断用户id
		if (IsExiest($data['pid']) != false){
			$_sql .= " and p1.pid = {$data['pid']}";
		}
		if (IsExiest($data['type_id']) != false){
			$_sql .= " and p1.id in({$data['type_id']})";
		}
		$_order = " order by p1.order desc ,p1.id asc ";
		
		
		$_select = " p1.*";
		$sql = "select SELECT from `{articles_type}` as p1  SQL ORDER LIMIT ";
		
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
	 * 3,添加文章类型
	 *
	 * @param Array $data = array("name"=>"名称","nid"=>"别名","contents"=>"内容","order"=>"排序")
	 * @return Boolen
	 */
	function AddType($data = array()){
		global $mysql;
		if (!IsExiest($data['name'])) return "articls_type_name_empty";
		if (!IsExiest($data['nid'])) return "articls_type_nid_empty";
		$sql = "select 1 from `{articles_type}` where nid='{$data['nid']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "articles_type_nid_exiest";
		$sql = "insert into `{articles_type}` set ";
		$_sql = array();
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql));
    	$id = $mysql->db_insert_id();
		
		return $id;
	}
	
	
	/**
	 * 4,类型分类菜单
	 *
	 * @param Array $data = array("name"=>"名称","nid"=>"别名","contents"=>"内容","order"=>"排序")
	 * @return Boolen
	 */
	function GetTypeMenu($data = array()){
		$result = self::GetTypeList(array("limit"=>"all"));
		$_result = array();
		$var = "&nbsp;&nbsp;&nbsp;&nbsp;";
		$type_var="—";
		
		foreach ($result as  $key => $value){
			$site_result[$value['id']] = $value;
			$_res_pid[$value['pid']][] = $value['id'];
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
				$_result = $_result +  self::_GetTypeMenu($_site_data);
			}
		}
		if (IsExiest($data['lgnore'])!=false){
			unset($_result[$data['lgnore']]);
		}
		return $_result;
	}
	function _GetTypeMenu($_site_data){
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
					
				$_site_data['_result'] = $_site_data["result"][$value];
				$_site_data['var'] = $_site_data['var'].$var;
				$_site_data['type_var'] = $_site_data['type_var'].$type_var;
				$_result = $_result +  self::_GetTypeMenu($_site_data);
			}
		}
		return $_result;
	}
	
	/**
	 * 5,获得单个类型
	 *
	 * @param Array $data = array("id"=>"");
	 * @return Array
	 */
	public static function GetTypeOne($data = array()){
		global $mysql;
		$_sql="where 1=1 ";
		if (!IsExiest($data['id']) && !IsExiest($data['nid'])) return "articls_type_id_empty";
		if (IsExiest($data['nid'])!=false){
			$_sql.=" and nid = '{$data['nid']}'";
		}elseif (IsExiest($data['id'])!=false){
			$_sql.=" and id = '{$data['id']}'";
		}
		$sql = "select * from `{articles_type}` $_sql";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "article_type_empty";		
		return $result;
	}
	
	
	/**
	 * 6,修改类型
	 *
	 * @param Array $data = array("id"=>"ID","name"=>"名称","nid"=>"别名","contents"=>"内容","order"=>"排序")
	 * @return Boolen
	 */
	function UpdateType($data = array()){
		global $mysql;
		if (!IsExiest($data['name'])) return "articls_type_name_empty";
		if (!IsExiest($data['nid'])) return "articls_type_nid_empty";
		$sql = "select 1 from `{articles_type}` where nid='{$data['nid']}' and id!='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "articles_type_nid_exiest";
		$sql = "update `{articles_type}` set ";
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
	function DelType($data = array()){
		global $mysql;
		
		//判断是否存在
		$sql = "select 1 from `{articles_type}` where id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "articles_type_not_exiest";
		
		//判断是否有子类
		$sql = "select 1 from `{articles_type}` where pid='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "articles_type_del_pid_exiest";
		
		
		//判断是否存在
		$sql = "select 1 from `{articles}` where FIND_IN_SET('{$data['id']}',type_id)";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "articles_type_del_article_exiest";
		
		$sql = "delete from `{articles_type}`  where id='{$data['id']}'";
    	$mysql -> db_query($sql);
		
		return $data['id'];
	}
	
	/**
	 * 8,添加文章
	 *
	 * @param Array $result
	 * @return Boolen
	 */
	function Add($data){
		global $mysql;
		
        if (!IsExiest($data['name'])) return "articles_name_empty";
        if (!IsExiest($data['type_id'])) return "articles_type_id_empty";
		if ($data['public']==3 && !IsExiest($data['password'])){
			return "articles_password_empty";
		}
		$sql = "insert into `{articles}` set `addtime` = '".time()."',`addip` = '".ip_address()."',update_time='".time()."',update_ip='".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
        $mysql->db_query($sql);
    	$id = $mysql->db_insert_id();
		
		return $id;
	}
	
	/**
	 * 9,修改文章
	 *
	 * @param Array $result
	 * @return Boolen
	 */
	function Update($data){
		global $mysql;
		if (!IsExiest($data['id'])) return "articles_id_empty";
        if (!IsExiest($data['name'])) return "articles_name_empty";
        if (!IsExiest($data['type_id'])) return "articles_type_id_empty";
		if ($data['public']==3 && !IsExiest($data['password'])){
			return "articles_password_empty";
		}
		$sql = "select user_id from `{articles}` where id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		$user_id = $result['user_id'];
		if ($data['user_id']!="" && $data['user_id']!=$user_id){
			return "articles_error";
		}
		$sql = "update `{articles}` set update_time='".time()."',update_ip='".ip_address()."',";
		$_sql = "";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql)." where id='{$data['id']}'");
		
		
		return $data['id'];
	}
	/**
	 * 9,修改文章
	 *
	 * @param Array $data = array("type"=>"","id"=>"");
	 * @return Boolen
	 */
	function Action($data){
		global $mysql;
		if (count($data['id'])<=0) return 1;
		if ($data['type']=='order'){
			foreach ($data['id'] as $key => $value){
				$sql = "update `{articles}` set `order`='{$data['order'][$key]}' where id='{$value}'";
				$mysql->db_query($sql);
			}
		}elseif ($data['type']=='del'){
			if (count($data['aid'])>0) {
				foreach ($data['aid'] as $key => $value){
					$sql = "delete from `{articles}` where id='{$value}'";
					$mysql->db_query($sql);
				}
			}
		}
		return 1;
	
	}
	/**
	 * 10,查看文章
	 *
	 * @param Array $data
	 * @return Array
	 */
	public static function GetOne($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "articles_id_empty";
		if ($data['hits_status']==1){
			$sql ="update `{articles}` set hits =hits+1 where id={$data['id']}";
			$mysql->db_query($sql);
		}
		$_sql = " where p1.id={$data['id']}";
		if ($data['user_id']!=""){
			$_sql .= " and p1.user_id='{$data['user_id']}'";
		}
		$sql = "select p1.*,p2.username,p3.fileurl from `{articles}` as p1 
				left join `{users}` as p2 on p2.user_id=p1.user_id 
				left join `{users_upfiles}` as p3 on p3.id=p1.litpic 
				{$_sql}";
		$result = $mysql->db_fetch_array($sql); 
		
		if ($result==false) return "articles_not_exiest";
		$result['contents'] = htmlspecialchars_decode($result['contents']);
		return $result;
	}
	
	
	
	/**
	 * 11，删除文章
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	public static function Delete($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "articles_id_empty";
		$id = $data['id'];
		if (!is_array($id)){
			$id = array($id);
		}
		$sql = "delete from `{articles}`  where id in (".join(",",$id).")";
		$mysql->db_query($sql);
		
		
	}
	
	
		
	/**
	 * 11，审核文章
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	public static function Verify($data = array()){
		global $mysql;
		
		//判断验证码
		if ($data['valicode']=="" ){
				return "users_valicode_empty";
			}
		if (isset($data['valicode'])){
			if ($data['valicode'] != $_SESSION['valicode']){
				return "users_valicode_error";
			}
		}
		unset($data['valicode']);	
		
		if (!IsExiest($data['id'])) return "articles_id_empty";
		$id = $data['id'];
		$sql = "select user_id,name from `{articles}` where id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return  "";
		$user_id =$result['user_id'];
		if ($result['status']==1) return "articles_verify_yes";
		$sql = "update `{articles}` set status='{$data['status']}',verify_time='".time()."',verify_remark='{$data['verify_remark']}',verify_userid='{$data['verify_userid']}'  where id={$id}";
		$mysql->db_query($sql);
		
		if($data['status']==1){
						
			//加入用户操作记录
			$user_log["user_id"] = 	$user_id;
			$user_log["code"] = "articles";
			$user_log["type"] = "article";
			$user_log["operating"] = "add";
			$user_log["article_id"] = $data['id'];
			$user_log["result"] = 1;
			$user_log["content"] =  "发表了[{$result['name']}]";;
			usersClass::AddUsersLog($user_log);	
		}
		return  $data['id'];
	}
	
		/**
	 * 10,查看文章
	 *
	 * @param Array $data
	 * @return Array
	 */
	public static function GetArticlesSide($data = array()){
		global $mysql;
		if ($data['site_nid']!=""){
			$sql = "select value from `{site}` where nid='{$data['site_nid']}'";
			$result = $mysql->db_fetch_array($sql);
			if ($result==false) return "";
			$data['type_id'] = $result['value'];
		}
		$_result = array("nid"=>$data['site_nid']);
		if ($data['type_id']!=""){
			$sql = "select id,name  from`{articles}` where type_id='{$data['type_id']}' and id>{$data['id']} and status='{$data['status']}' order by id asc";
			$result = $mysql->db_fetch_array($sql);
			$_result["down_id"] = $result['id'];
			$_result["down_name"] = $result['name'];
			$sql = "select id,name  from`{articles}` where type_id='{$data['type_id']}' and status='{$data['status']}' and id<{$data['id']} order by id desc";
			$result = $mysql->db_fetch_array($sql);
			$_result["up_id"] = $result['id'];
			$_result["up_name"] = $result['name'];
		
		}
		return $_result;
	}
	
	/**
	 * 12,添加页面
	 *
	 * @param Array $result
	 * @return Boolen
	 */
	function AddPage($data){
		global $mysql;
		
        if (!IsExiest($data['name'])) return "articles_page_name_empty";
        if (!IsExiest($data['nid'])) return "articles_page_nid_empty";
		if ($data['public']==3 && !IsExiest($data['password'])){
			return "articles_page_password_empty";
		}
		$sql = "select 1 from `{articles_pages}` where nid='{$data['nid']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "articles_page_nid_exiest";
		
		$sql = "insert into `{articles_pages}` set `addtime` = '".time()."',`addip` = '".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
        $mysql->db_query($sql);
    	$id = $mysql->db_insert_id();
		return $id;
	}
	
	/**
	 * 13,修改页面
	 *
	 * @param Array $result
	 * @return Boolen
	 */
	function UpdatePage($data){
		global $mysql;
		if (!IsExiest($data['id'])) return "articles_page_id_empty";
        if (!IsExiest($data['name'])) return "articles_page_name_empty";
		if ($data['public']==3 && !IsExiest($data['password'])){
			return "articles_page_password_empty";
		}
		
		$sql = "select 1 from `{articles_pages}` where nid='{$data['nid']}' and id!='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "articles_page_nid_exiest";
		
		$sql = "update `{articles_pages}` set ";
		$_sql = "";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql)." where id='{$data['id']}'");
    	
		return $data['id'];
	}
	
	/**
	 * 14,查看页面
	 *
	 * @param Array $data
	 * @return Array
	 */
	public static function GetPageOne($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "articles_page_id_empty";
		$sql = "select p1.*,p2.username from `{articles_pages}` as p1 
				left join `{users}` as p2 on p2.user_id=p1.user_id 
				where p1.id={$data['id']}";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "articles_page_not_exiest";
		$result['contents'] = htmlspecialchars_decode($result['contents']);
		return $result;
	}
	
	/**
	 * 15，获取页面列表
	 *
	 * $data = array("user_id"=>"用户id","username"=>"用户名");
	 * @return Array
	 */
	function GetPageList($data = array()){
		global $mysql;
		
		$_sql = "where 1=1 ";	
		
		$_order = " order by p1.order desc ,p1.id asc ";
		
		
		$_select = " p1.*,p2.username";
		$sql = "select SELECT from `{articles_pages}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id  SQL ORDER LIMIT ";
		
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
	 * 16,页面菜单
	 *
	 * @param Array $data = array("name"=>"名称","nid"=>"别名","contents"=>"内容","order"=>"排序")
	 * @return Boolen
	 */
	function GetPageMenu($data = array()){
		global $mysql;
		$_select = " p1.*,p2.username";
		$sql = "select $_select from `{articles_pages}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id   ";
		$result = $mysql->db_fetch_arrays($sql);
		$var = "&nbsp;&nbsp;&nbsp;&nbsp;";
		$type_var="—";
		
		foreach ($result as  $key => $value){
			$_res[$value['id']]['pid'] = $value['pid'];
			if ($value['pid']==0){
				$_result[$value['id']] = $value;
				$_result[$value['id']]['_name'] = $value['name'];
				$_result[$value['id']]['type_name'] = $value['name'];
				$_result[$value['id']]['var'] = "";
				$_result1 = self::_GetPageMenu($result,$value['id'],$var,$type_var);
				$_result = array_merge($_result,$_result1);
				
			}
		}
		return $_result;
	}
	function _GetPageMenu($result,$pid,$var,$type_var){
		$_result = array();
		$_var = "&nbsp;&nbsp;&nbsp;&nbsp;";
		$_type_var="—";
		foreach ($result as  $key => $value){
			if ($value['pid'] == $pid){
				if ($opid==""){
					$_result[$value['id']] = $value;
					$_result[$value['id']]['_name'] = $var.$value['name'];
					$_result[$value['id']]['type_name'] = $type_var.$value['name'];
					$_result[$value['id']]['var'] = $var.$_var;	
					$_result1 = self::_GetPageMenu($result,$value['id'],$var.$_var,$type_var.$_type_var);
					$_result = array_merge($_result,$_result1);
				}else{
					$_result[$value['id']] = $value;
					$_result[$value['id']]['_name'] = $var.$value['name'];
					$_result[$value['id']]['type_name'] = $type_var.$value['name'];
					$_result[$value['id']]['var'] = $var.$_var;	
					$_result1 = self::_GetPageMenu($result,$value['id'],$var.$_var,$type_var.$_type_var);
					$_result = array_merge($_result,$_result1);
				}
			}
		}
		return $_result;
	}
	
	
	
	/**
	 * 17,删除页面
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function DeletePage($data = array()){
		global $mysql;
		
		//判断是否存在
		$sql = "select 1 from `{articles_pages}` where id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "articles_page_not_exiest";
		
		//判断是否有子类
		$sql = "select 1 from `{articles_pages}` where pid='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "articles_page_del_pid_exiest";
		
		$sql = "delete from `{articles_pages}`  where id='{$data['id']}'";
    	$mysql -> db_query($sql);
		
		return $data['id'];
	}
	
}
?>