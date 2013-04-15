<?
/******************************
 * $File: users.admin.php
 * $Description: 用户管理员类文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问


require_once("users.vip.php");

class usersadminClass extends usersvipClass {
	
	
	
	/**
     * 7，管理员登录（users，users_info，users_admin）
     * @param $param array('username' => '用户名','password'=>'密码')
	 * @return Array
     */
	function AdminLogin($data){
		global $mysql,$_A,$MsgInfo;
		
		//判断用户不能为空
		if (!IsExiest($data['username'])) {
            return "users_username_empty";
        }
        
		//判断密码是否为空
        if (!IsExiest($data['password'])) {
            return "users_password_empty";
        }
		
		//判断用户密码是否正确
		$sql = "select user_id,username from `{users}` where   `username` = '{$data['username']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false){
			return "users_admin_login_password_error";
		}else{
			$username = $result['username'];
			$user_id = $result['user_id'];
			$sql = "select * from `{users_admin}` where   `user_id` = '{$result['user_id']}' and `password`='".md5($data['password'])."'";
			$result = $mysql->db_fetch_array($sql);
			$type_id = $result['type_id'];
		}
		
		$admin_log["code"] = "users";
		$admin_log["type"] = "admin";
		$admin_log["operating"] = "login";
		
		//用户名密码错误
		if ($result == false){
			//加入管理员操作记录
			$admin_log["user_id"] = 0;
			$admin_log["article_id"] = "0";
			$admin_log["result"] = "0";
			$admin_log["content"] = str_replace(array('#username#', '#admin_url#'), array($data['username'],$_A['admin_url']), $MsgInfo["users_admin_login_password_error_msg"]);
			$admin_log["data"] =  $data;
			self::AddAdminLog($admin_log);
			
			return "users_admin_login_password_error";
		}else{
			//加入管理员操作记录1
			$admin_log["user_id"] = $user_id;
			$admin_log["article_id"] = $result['user_id'];
			$admin_log["result"] = 1;
			$admin_log["content"] = str_replace(array('#username#', '#admin_url#'), array($data['username'],$_A['admin_url']), $MsgInfo["users_admin_login_success_msg"]);
			$admin_log["data"] =  $data;
			self::AddAdminLog($admin_log);
			
			$sql = "update `{users_admin}` set logintimes=logintimes+1,login_time='".time()."',login_ip='".ip_address()."' where user_id='{$user_id}'";
			$mysql->db_query($sql);
			
			//查询相关后台管理员的管理权限
			if ($type_id==1){
				$admin_type_result['module'] = "all";
				$admin_type_result['purview'] = "all";
			}else{
				$sql = "select * from `{users_admin_type}` where `id`= '{$type_id}'";
				$admin_type_result = $mysql->db_fetch_array($sql);
				if ($admin_type_result==false) return "users_admin_type_empty";
			}
 			return array("user_id"=>$user_id,"username"=>$username,"admin_result"=>$result,"purview"=>$admin_type_result['purview'],"module"=>$admin_type_result['module']);
		}
	}
	
	/**
     * 8，添加管理员的操作记录（users_adminlog）
     * @param $param array('user_id' => '用户id','code' => '模块名称','type' => '所属分类,'operating' => '操作类型','article_id' => '操作id','result' => '操作结果','content' => '操作内容')
	 * @return Null
     */
	function AddAdminLog($data){
		global $mysql;
		$data["data"] = serialize($data["data"]);
		$sql = "insert into `{users_adminlog}` set  addtime='".time()."',addip='".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
		$mysql->db_query($sql);
	}
	
	/**
     * 9，获取管理员管理的信息（users_adminlog）
     * @param $param array('user_id' => '会员ID')
	 * @return Array（'list'=>"列表",page=>'当前页面','epage'=>'页数','total_page'=>'总页面'）
     */
	function GetAdminlogList($data){
		global $mysql;
		$_sql = " where 1=1 ";
		
		//判断用户id
		if (IsExiest($data['user_id']) != false){
			$_sql .= " and p1.`user_id`  = '{$data['user_id']}'";
		}
		
		//判断是否搜索用户名
		if (IsExiest($data['username']) != false){
			$_sql .= " and p2.`username` like '%{$data['username']}%'";
		}
		
		//判断是否搜索邮箱
		if (IsExiest($data['email']) != false){
			$_sql .= " and p2.`email` like '%{$data['email']}%'";
		}
		
		$_select = "p1.*,p2.username";
		$_order = " order by id desc";
		$sql = "select SELECT from `{users_adminlog}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id SQL ORDER LIMIT";
		
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
	 * 1,添加用户类型
	 *
	 * @param array $data =array("name"=>"用户类型名称","status"=>"状态","degree"=>"学历","in_year"=>"入学时间","professional"=>"专业");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function AddAdminType($data = array()){
		global $mysql;
		 //判断名称是否存在
        if (!IsExiest($data['name'])) {
            return "users_admin_type_name_empty";
        }
		 //判断标识名是否存在
        if (!IsExiest($data['nid'])) {
            return "users_admin_type_nid_empty";
        }
		
		//判断标识名是否存在
		$sql = "select 1 from `{users_admin_type}` where nid='{$data['nid']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "users_admin_type_nid_exiest";
		
		$sql = "insert into `{users_admin_type}` set addtime='".time()."',addip='".ip_address()."',update_time='".time()."',update_ip='".ip_address()."',";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql));
    	return $mysql->db_insert_id();
	}
	
	/**
	 * 2,修改用户类型
	 *
	 * @param array $data =array("id"=>"id","name"=>"名称","status"=>"状态");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function UpdateAdminType($data = array()){
		global $mysql;
		
		 //判断名称是否存在
        if (!IsExiest($data['name'])) {
            return "users_admin_type_name_empty";
        }
		 //判断标识名是否存在
        if (!IsExiest($data['nid'])) {
            return "users_admin_type_nid_empty";
		}
		
		//判断标识名是否存在
		$sql = "select 1 from `{users_admin_type}` where nid='{$data['nid']}' and id!={$data['id']}";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "users_admin_type_nid_exiest";
		
		$sql = "update `{users_admin_type}` set update_time='".time()."',update_ip='".ip_address()."',";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $result = $mysql->db_query($sql.join(",",$_sql)." where id='{$data['id']}' ");
		
		return $data['id'];
	}
	
	/**
	 * 3,删除用户类型
	 *
	 * @param Array $data = array("id"=>"ID")
	 * @return Boolen
	 */
	function DelAdminType($data = array()){
		global $mysql;
		
		if (!IsExiest($data['id'])) return "users_admin_type_id_empty";
		
		
		
		if ($data['id'] == 1) return "users_admin_type_not_delete";
		
		$sql = "select 1 from `{users_admin}` where type_id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result != false) return "users_admin_type_user_exiest";
		
		$sql = "delete from `{users_admin_type}`  where id='{$data['id']}'";
    	$mysql -> db_query($sql);
		
		return $data['id'];
	}
	
	
	
	/**
	 * 5,获得用户类型列表
	 *
	 * @return Array
	 */
	function GetAdminTypeList($data = array()){
		global $mysql;
		
		$_sql = " where 1=1 ";
		
		
		$_select = " p1.*";
		$_order = " order by p1.`order` desc,p1.id desc";
		$sql = "select SELECT from `{users_admin_type}` as p1  SQL ORDER ";
		
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
	 * 6,获得用户类型的单条记录
	 *
	 * @param Array $data = array("id"=>"");
	 * @return Array
	 */
	 function GetAdminTypeOne($data = array()){
		global $mysql;
		
		if (!IsExiest($data['id'])) return "users_admin_type_id_empty";
		
		$sql = "select p1.* from `{users_admin_type}` as p1   where p1.id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		
		if ($result==false) return "users_admin_type_empty";
		return $result;
	}
	
	
	
	/**
	 * 1,添加用户类型
	 *
	 * @param array $data =array("name"=>"用户类型名称","status"=>"状态","degree"=>"学历","in_year"=>"入学时间","professional"=>"专业");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function UpdateUsersAdmin($data = array()){
		global $mysql;
		 //判断名称是否存在
        if (!IsExiest($data['adminname'])) {
            return "users_admin_name_empty";
        }
		 //判断标识名是否存在
        if (!IsExiest($data['user_id'])) {
            return "users_admin_user_id_empty";
        }
		
		//判断标识名是否存在
		$sql = "select 1 from `{users_admin}` where user_id='{$data['user_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) {
			
			//判断标识名是否存在
			if (!IsExiest($data['password'])) {
				return "users_admin_password_empty";
			}
			$data['password'] = md5($data['password']);
			$sql = "insert into `{users_admin}` set addtime='".time()."',addip='".ip_address()."',update_time='".time()."',update_ip='".ip_address()."',";
			foreach($data as $key => $value){
				$_sql[] = "`$key` = '$value'";
			}
			$result = $mysql->db_query($sql.join(",",$_sql));
		}else{
			
			//判断标识名是否存在
			if (!IsExiest($data['password'])) {
				unset($data['password']);
			}else{
				$data['password'] = md5($data['password']);
			}
			$sql = "update `{users_admin}` set update_time='".time()."',update_ip='".ip_address()."',";
			foreach($data as $key => $value){
				$_sql[] = "`$key` = '$value'";
			}
			$mysql->db_query($sql.join(",",$_sql)." where user_id='{$data['user_id']}'");
		}
		return $data['user_id'];
	}

	
	/**
	 * 3,删除用户类型
	 *
	 * @param Array $data = array("id"=>"ID")
	 * @return Boolen
	 */
	function DeleteUsersAdmin($data = array()){
		global $mysql;
		
		if (!IsExiest($data['user_id'])) return "users_admin_user_id_empty";
		
		$sql = "delete from `{users_admin}`  where user_id='{$data['user_id']}'";
    	$mysql -> db_query($sql);
		
		return $data['user_id'];
	}
	
	/**
	 * 4，修改用户密码
	 *
	 * @param Array data
	 * @return Boolen
	 */
	function UpdateAdminPassword($data = array()){
		global $mysql,$_G,$MsgInfo;
		//判断用户id是否已经存在
		if (!IsExiest($data['user_id'])) {
			return "users_userid_empty";
		}
		if (!IsExiest($data['password'])) {
			return "users_password_empty";
		}
		$user_id= $data['user_id'];
		//判断用户是否存在
		$sql = "select 1 from `{users_admin}` where `user_id`='{$user_id}'";
		$result= $mysql->db_fetch_array($sql);
		if ($result==false){
			return "users_user_not_exiest";
		}else{
			$username = $result['username'];
		}
		
		//修改密码
		$sql = "update `{users_admin}` set `password` = '".md5($data['password'])."' where `user_id` ={$user_id}";
		$result = $mysql->db_query($sql);
		
		return $data['user_id'];
	}
	
	
	/**
	 * 5,获得用户类型列表
	 *
	 * @return Array
	 */
	function GetUsersAdminList($data = array()){
		global $mysql;
		
		$_sql = " where 1=1 ";
		$_select = " p1.*,p2.username,p3.name as type_name";
		//判断是否搜索邮箱
		if (IsExiest($data['type_id']) != false){
			$_sql .= " and p1.`type_id` = '{$data['type_id']}'";
		}
		
		$_order = " order by p1.id desc";
		$sql = "select SELECT from `{users_admin}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id  left join `{users_admin_type}` as p3 on p1.type_id=p3.id  SQL ORDER ";
		
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
	 * 6,获得用户类型的单条记录
	 *
	 * @param Array $data = array("id"=>"");
	 * @return Array
	 */
	 function GetUsersAdminOne($data = array()){
		global $mysql;
		
		$sql = "select p1.*,p2.* from `{users_admin}` as p1 left join `{users_admin_type}` as p2 on p1.type_id=p2.id  where p1.user_id='{$data['user_id']}'";
		$result = $mysql->db_fetch_array($sql);
		return $result;
	}
	
	
	
	
	
	
	
}