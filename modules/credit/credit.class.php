<?
/******************************
 * $File: credit.class.php
 * $Description: 积分类管理
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

require_once("credit.model.php");

class creditClass {
	
	
	/**
	 * 积分分类列表
	 *
	 * @return Array
	 */
	function GetClassList($data = array()){
		global $mysql;
		
		$_sql = " where 1=1 ";
		$_select = " p1.*";
		$_order = " order by p1.id desc";
		$_limit = "";
		$sql = "select SELECT from `{credit_class}` as p1  SQL ORDER LIMIT ";
		
		//是否显示全部的信息
		$data['limit'] = isset($data['limit'])?$data['limit']:"";
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
	 * 查看积分分类
	 *
	 * @param Array $data
	 * @return Array
	 */
	public static function GetClassOne($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "credit_class_id_empty";
		$sql = "select p1.* from `{credit_class}` as p1 where p1.id={$data['id']}";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "credit_class_not_exiest";
		return $result;
	}
	
	/**
	 * 添加积分分类
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function AddClass($data = array()){
		global $mysql;
		//手机号码不能为空
		if (!IsExiest($data['name'])) return "credit_class_name_empty";
		if (!IsExiest($data['nid'])) return "credit_class_nid_empty";
		
		//判断标识名是否存在
		$sql = "select 1 from `{credit_class}` where nid='{$data['nid']}'";
		$result =  $mysql->db_fetch_array($sql);
		if ($result!=false) return "credit_class_nid_exiest";
		
		$sql = "insert into `{credit_class}` set `name`='{$data['name']}',`nid`='{$data['nid']}'";
		$mysql->db_query($sql);
		$id = $mysql -> db_insert_id();
		return $id;
	}
	
	/**
	 * 修改积分类型
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function UpdateClass($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "credit_class_id_empty";
		if (!IsExiest($data['name'])) return "credit_class_name_empty";
		if (!IsExiest($data['nid'])) return "credit_class_nid_empty";
		
		//判断标识名是否存在
		$sql = "select 1  from `{credit_class}` where nid='{$data['nid']}' and id!='{$data['id']}'";
		$result =  $mysql->db_fetch_array($sql);
		if ($result!=false) return "credit_class_nid_exiest";
		
		
		$sql = "update `{credit_class}`  set `name`='{$data['name']}',`nid`='{$data['nid']}' where id='{$data['id']}'";
		$mysql->db_query($sql);
		return $data['id'];
	}
	
		
	/**
	 * 删除积分分类
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function DeleteClass($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "credit_class_id_empty";
		
		//判断是否有类型是否存在
		$sql = "select 1 from `{credit_type}` where FIND_IN_SET('{$data['id']}',class_id)";
		$result =  $mysql->db_fetch_array($sql);
		if ($result!=false) return "credit_class_del_type_exiest";
		
		
		//判断是否有等级是否存在
		$sql = "select 1 from `{credit_rank}` where FIND_IN_SET('{$data['id']}',class_id)";
		$result =  $mysql->db_fetch_array($sql);
		if ($result!=false) return "credit_class_del_rank_exiest";
		
		$sql = "delete from `{credit_class}` where id='{$data['id']}'";
		$mysql->db_query($sql);
		return $data['id'];
	}


	/**
	 * 积分类型列表
	 *
	 * @return Array
	 */
	function GetTypeList($data = array()){
		global $mysql;
		
		$_sql = " where 1=1 ";
		
		//搜到名称
		if (IsExiest($data['name']) != false){
			$_sql .= " and p1.`name` like '%{$data['name']}%'";
		}
		
		//搜到标识名
		if (IsExiest($data['nid']) != false){
			$_sql .= " and p1.nid like '%{$data['nid']}%'";
		}
		
		
		//搜到标识名
		if (IsExiest($data['code']) != false){
			$_sql .= " and p1.code = '{$data['code']}'";
		}
		
		$_select = " p1.*";
		$_order = " order by p1.id desc";
		$sql = "select SELECT from `{credit_type}` as p1  SQL ORDER LIMIT ";
		
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
	 * 查看积分类型
	 *
	 * @param Array $data
	 * @return Array
	 */
	function GetTypeOne($data = array()){
		global $mysql;
		$_sql="where 1=1 ";
		if (IsExiest($data['id']) != false){
			$_sql .= " and p1.id = '{$data['id']}'";
		}
		if (IsExiest($data['nid']) != false){
			$_sql .= " and p1.nid = '{$data['nid']}'";
		}
		
		$sql = "select p1.* from `{credit_type}` as p1 $_sql";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "credit_type_not_exiest";
		return $result;
	}
	
	/**
	 * 添加积分类型
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function AddType($data = array()){
		global $mysql;
		
		if (!IsExiest($data['name'])) return "credit_type_name_empty";
		if (!IsExiest($data['nid'])) return "credit_type_nid_empty";
		if (!IsExiest($data['value'])) return "credit_type_value_empty";
		if (!IsExiest($data['class_id'])) return "credit_type_class_id_empty";
		
		if  ($data['cycle']==2 && $data['award_times']==""){
			return "credit_type_award_times_empty";
		}
		
		if  ($data['cycle']==3 && $data['interval']==""){
			return "credit_type_interval_empty";
		}
		
		//判断标识名是否存在
		$sql = "select 1 from `{credit_type}` where nid='{$data['nid']}'";
		$result =  $mysql->db_fetch_array($sql);
		if ($result!=false) return "credit_type_nid_exiest";
		
		$sql = "insert into `{credit_type}` set ";
		foreach($data as $key => $value){
			$_sql[].= "`$key` = '$value'";
		}
		$mysql->db_query($sql.join(",",$_sql));
		$id = $mysql -> db_insert_id();
		return $id;
	}
	
	/**
	 * 修改积分类型
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function UpdateType($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "credit_type_id_empty";
		if (!IsExiest($data['name'])) return "credit_type_name_empty";
		if (!IsExiest($data['nid'])) return "credit_type_nid_empty";
		
		//判断标识名是否存在
		$sql = "select 1  from `{credit_type}` where nid='{$data['nid']}' and id!='{$data['id']}'";
		$result =  $mysql->db_fetch_array($sql);
		if ($result!=false) return "credit_type_nid_exiest";
		
		$sql = "update `{credit_log}` set value = {$data['value']} ,credit={$data['value']} where nid='{$data['nid']}'";
		$mysql->db_query($sql);
		
		$sql = "select nid  from `{credit_type}` where  id='{$data['id']}'";
		$result =  $mysql->db_fetch_array($sql);
		if ($result['nid']!=$data['nid']){
			//判断是否有类型是否存在
			$sql = "select 1 from `{credit_log}` as p1 left join `{credit_type}` as p2 on p1.nid=p2.nid where p2.id='{$data['id']}'";
			$result =  $mysql->db_fetch_array($sql);
			if ($result!=false) return "credit_type_update_credit_exiest";
		}
		
		$sql = "update `{credit_type}`  set ";
		foreach($data as $key => $value){
			$_sql[].= "`$key` = '$value'";
		}
		$mysql->db_query($sql.join(",",$_sql)." where id='{$data['id']}'");
		return $data['id'];
	}
	
		
	/**
	 * 删除积分类型
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function DeleteType($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "credit_type_id_empty";
		
		//判断标识名是否存在
		$sql = "select nid from `{credit_type}` where id='{$data['id']}'";
		$result =  $mysql->db_fetch_array($sql);
		if ($result==false) return "credit_type_not_exiest";
		
		
		
		$sql = "delete from `{credit_type}` where id='{$data['id']}'";
		$mysql->db_query($sql);
		return $data['id'];
	}


	
	/**
	 * 积分等级列表
	 *
	 * @return Array
	 */
	function GetRankList($data = array()){
		global $mysql;
		
		$_sql = " where 1=1 ";
		
		//搜到名称
		$data['class_id'] = isset($data['class_id'])?$data['class_id']:"";
		if (IsExiest($data['class_id']) != false){
			$_sql .= " and p1.`class_id` = '{$data['class_id']}'";
		}
		
		
		$_select = " p1.*,p2.nid as class_nid";
		$_order = " order by p1.id desc";
		$_limit = "";
		$sql = "select SELECT from `{credit_rank}` as p1 left join `{credit_class}` as p2 on p1.class_id=p2.id SQL ORDER LIMIT ";
		
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
	 * 查看积分等级
	 *
	 * @param Array $data
	 * @return Array
	 */
	function GetRankOne($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "credit_rank_id_empty";
		$sql = "select p1.* from `{credit_rank}` as p1 where p1.id={$data['id']}";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "credit_rank_not_exiest";
		return $result;
	}
	
	/**
	 * 添加积分等级
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function AddRank($data = array()){
		global $mysql;
		
		if (!IsExiest($data['name'])) return "credit_rank_name_empty";
		
		
		$sql = "insert into `{credit_rank}` set ";
		foreach($data as $key => $value){
			$_sql[].= "`$key` = '$value'";
		}
		$mysql->db_query($sql.join(",",$_sql));
		$id = $mysql -> db_insert_id();
		return $id;
	}
	
	/**
	 * 修改积分等级
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function UpdateRank($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "credit_rank_id_empty";
		if (!IsExiest($data['name'])) return "credit_rank_name_empty";
		
		
		$sql = "update `{credit_rank}`  set ";
		foreach($data as $key => $value){
			$_sql[].= "`$key` = '$value'";
		}
		$mysql->db_query($sql.join(",",$_sql)." where id='{$data['id']}'");
		return $data['id'];
	}
	
		
	/**
	 * 删除积分类型
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function DeleteRank($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "credit_rank_id_empty";
		
		$sql = "delete from `{credit_rank}` where id='{$data['id']}'";
		$mysql->db_query($sql);
		return $data['id'];
	}

	/**
	 * 积分记录列表
	 *
	 * @return Array
	 */
	function GetLogList($data = array()){
		global $mysql;
		
		$_sql = " where 1=1 ";
		
		//搜索积分分类
		if (IsExiest($data['class_id']) != false){
			$_sql .= " and p1.`class_id` = '{$data['class_id']}'";
		}
		
		//搜索用户id
		if (IsExiest($data['user_id']) != false){
			$_sql .= " and p1.`user_id` = '{$data['user_id']}'";
		}
		
		//搜索用户名
		if (IsExiest($data['username']) != false){
			$_sql .= " and p2.`username` like '%{$data['username']}%'";
		}
		
		//搜索标识名
		if (IsExiest($data['nid']) != false){
			$_sql .= " and p1.`nid` like '%{$data['nid']}%'";
		}
		
		
		$_select = " p1.*,p2.username,p3.name as type_name,p3.class_id";
		$_order = " order by p1.id desc";
		$sql = "select SELECT from `{credit_log}` as p1 left join `{credit_type}` as p3 on p1.nid=p3.nid left join `{users}` as p2 on p1.user_id=p2.user_id  SQL ORDER LIMIT ";
		
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
	 * 查看积分类型
	 *
	 * @param Array $data
	 * @return Array
	 */
	function GetLogOne($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "credit_log_id_empty";
		$sql = "select p1.*,p2.username,p3.name as type_name,p3.class_id from `{credit_log}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id left join `{credit_type}` as p3 on p1.nid=p3.nid where p1.id={$data['id']}";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "credit_log_not_exiest";
		return $result;
	}
	
	/**
	 * 积分操作
	 *
	 * @return Array $data = array("code"=>"模块","user_id"=>"用户","type"=>"类型","article_id"=>"文章id","code"=>"","code"=>"",);
	 */
	public static function ActionCreditLog($data){
		global $mysql;
		$_nid = explode(",",$data['nid']);
		
		//第一步先删除没有的积分记录
		$_sql = "delete from `{credit_log}` where code='{$data['code']}'  and type='{$data['type']}' and article_id='{$data['article_id']}' and nid not in ('{$data['nid']}')";
		$mysql->db_query($_sql);
		
		//第二步加入资金记录
		if (count($_nid)>0){
			foreach ($_nid as $key => $nid){
				if ($nid!=""){
				    $sql = "select * from `{credit_type}` where nid='{$nid}'";
					$result = $mysql->db_fetch_array($sql);
					if (isset($data['value']) && $data['value']>0){
					   if ($result['credit_type']==1){
					       $_value = $data['value']*$result['value_scale']*0.01;
					   }else{
						$_value = $data['value'];
                        }
					}else{
						$_value = $result['value'];
					}
					
					$sql = "select * from `{credit_log}` where code='{$data['code']}'  and type='{$data['type']}' and article_id='{$data['article_id']}' and nid='{$nid}' and user_id='{$data['user_id']}'";
					$result = $mysql->db_fetch_array($sql);
					if ($result==false){
						$sql = "insert into `{credit_log}` set code='{$data['code']}',user_id='{$data['user_id']}',`value`='{$_value}',`credit`='{$_value}',type='{$data['type']}',article_id='{$data['article_id']}',nid='{$nid}',addtime='{$data['addtime']}',remark='{$data['remark']}',update_time='".time()."'";
						$mysql->db_query($sql);
					}else{
						$sql = "update `{credit_log}` set addtime='{$data['addtime']}',user_id='{$data['user_id']}',`value`='{$_value}',update_time='".time()."' where code='{$data['code']}'  and type='{$data['type']}' and article_id='{$data['article_id']}' and nid='{$nid}'";
						
						$mysql->db_query($sql);
					}
				}
			}
			self::ActionCredit(array("user_id"=>$data['user_id']));
		}
		
	}
	
	/**
	 * 删除积分操作
	 *
	 * @return Array $data = array("code"=>"模块","type"=>"类型","article_id"=>"文章id");
	 */
	function DeleteCreditLog($data){
		global $mysql;
		
		//获取用户id
		$sql = "select user_id from `{credit_log}` where code='{$data['code']}'  and type='{$data['type']}' and article_id='{$data['article_id']}' ";
		$result = $mysql->db_fetch_array($sql);
		$user_id= $result['user_id'];
		
		//第一步先删除没有的积分记录
		$_sql = "delete from `{credit_log}` where code='{$data['code']}'  and type='{$data['type']}' and article_id='{$data['article_id']}' ";
		$mysql->db_query($_sql);
		
		self::ActionCredit(array("user_id"=>$user_id));
		
	}
	
	/**
	 * 积分操作
	 *
	 * @return Array $data = array("user_id"=>"用户");
	 */
	public static function ActionCredit($data){
		global $mysql;
		$sql = "select sum(p1.credit) as num,p2.class_id from `{credit_log}` as p1 left join `{credit_type}` as p2 on p1.nid=p2.nid  where p1.user_id='{$data['user_id']}' group by p2.class_id order by p2.class_id desc";
		$result = $mysql->db_fetch_arrays($sql);
		$credits=serialize($result); 
		$sql = "select 1 from `{credit}` where user_id='{$data['user_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false){
			$sql = "insert into `{credit}` set user_id='{$data['user_id']}',`credits`='{$credits}'";
		}else{
			$sql = "update `{credit}` set `credits`='{$credits}' where user_id='{$data['user_id']}'";
		}
		$mysql->db_query($sql);
		self::CountCredit(array("user_id"=>$data['user_id'],"type"=>"dyp2p"));
	}
	
	/**
	 * 修改操作
	 *
	 * @return Array $data = array("user_id"=>"用户","credit"=>"积分","id"=>"积分记录id");
	 */
	public static function UpdateCredit($data){
		global $mysql;
		$sql = "update `{credit_log}` set `credit`='{$data['credit']}' where id='{$data['id']}' and user_id='{$data['user_id']}'";
		$mysql->db_query($sql);
		self::ActionCredit(array("user_id"=>$data["user_id"]));
		return $data['id'];
	}
	
	/**
	 * 积分等级列表
	 *
	 * @return Array
	 */
	function GetList($data = array()){
		global $mysql;
		
		$_sql = " where 1=1 ";
		if (IsExiest($data['username']) != false){
			$_sql .= " and p2.`username` like '%{$data['username']}%'";
		}
		$_select = " p1.*,p2.username";
		$_order = " order by p1.id desc";
		$sql = "select SELECT from `{credit}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id  SQL ORDER LIMIT ";
		
		$result_type = self::GetClassList(array("limit"=>"all"));
		foreach ($result_type as $key => $value){
			$_type_credit[$value['id']]['num'] = 0;
			$_type_credit[$value['id']]['class_id'] = $value['id'];
		}
		
		//判断总的条数
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array('count(1) as num', $_sql,'', ''), $sql));
		$total = intval($row['num']);
		
		//分页返回结果
		$data['page'] = !IsExiest($data['page'])?1:$data['page'];
		$data['epage'] = !IsExiest($data['epage'])?10:$data['epage'];
		$total_page = ceil($total / $data['epage']);
		$_limit = " limit ".($data['epage'] * ($data['page'] - 1)).", {$data['epage']}";
		$_list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL','ORDER', 'LIMIT'), array($_select,$_sql,$_order, $_limit), $sql));
		if ($_list!=false){
			foreach ($_list as $key => $value){
				$list[$key]["username"] = $value['username'];
				$list[$key]["user_id"] = $value['user_id'];
				$list[$key]["credits"] = $_type_credit;
				if ($value['credits']!=""){
					$credits  = unserialize($value['credits']);
					foreach ($credits as $_key => $_value){
						if ($_value['class_id']>0){
						$list[$key]["credits"][$_value['class_id']] = $_value;
						}
					}
				}
			}
		}
		print_r($result);
		//返回最终的结果
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		return $result;
	}
	
	public static function CountCredit($data){
		global $mysql;
		if ($data['type']=="dyp2p"){
			require_once(ROOT_PATH."/modules/borrow/borrow.class.php");
			$result = borrowClass::GetBorrowCredit(array("user_id"=>$data['user_id']));
			$sql = "update `{credit}` set credit='{$result['credit_total']}' where user_id='{$data['user_id']}'";
			$mysql->db_query($sql);
		}
 	
	}
	
	
	public static function GetCreditCount($data){
		global $mysql;
		$sql = "select sum(credit) as num,type from `{credit_log}` where user_id='{$data['user_id']}' group by type";
		$result = $mysql->db_fetch_arrays($sql);
		$_result = array();
		if ($result !=false){
			foreach ($result as $key => $value){
				$_result[$value['type']] = $value['num'];
			}
		}
 		return $_result;
	}
	
	
	
	public static function GetUserCredit($data){
		global $mysql;
		$sql = "select sum(p1.credit) as num,p3.nid from `{credit_log}` as p1 ,`{credit_type}` as p2 ,{credit_class} as p3 where p1.user_id='{$data['user_id']}' and p2.nid=p1.nid and p2.class_id=p3.id group by p3.id";
		$result = $mysql->db_fetch_arrays($sql);
		$_result = array();
		if ($result !=false){
			foreach ($result as $key => $value){
				$_result[$value['nid']] = $value['num'];
			}
		}
 		return $_result;
	}
	
	public static function GetGoldCount($data = array()){
		global $mysql;
		if ($data['user_id']=="") return false;
		//邀请金币
		$sql = "select sum(credit) as invite_gold from `{credit_log}` where user_id = {$data['user_id']} and nid='invite'";
		$result = $mysql->db_fetch_array($sql);
		$gold['invite_gold'] = $result['invite_gold'];
		
		//注册金币
		$sql = "select sum(credit) as reg_gold from `{credit_log}` where user_id = {$data['user_id']} and nid='reg'";
		$result = $mysql->db_fetch_array($sql);
		$gold['reg'] = $result['reg_gold'];
		
		//邀请投标金币
		$gold['invite_tender'] = 0;
		$sql = "select * from `{users_friends_invite}` where user_id = {$data['user_id']}";
		$result = $mysql->db_fetch_arrays($sql);
		if ($result>0){
			foreach ($result as $key => $value){
				$_sql = "select * from `{borrow_count}` where user_id = {$value['friends_userid']}";
				$result = $mysql->db_fetch_array($_sql);
				$gold['invite_tender'] += $result['tender_success_account'];
			}
		}
		$gold['invite_tender'] = floor($gold['invite_tender']/5000);
		//自己投标所得的金币
		$sql = "select tender_success_account from `{borrow_count}` where user_id = {$data['user_id']}";
		$result = $mysql->db_fetch_array($sql);
		$gold['tender'] = floor($result['tender_success_account']/10000);
		
		//总所得金币
		$gold['total'] = $gold['invite_tender']+$gold['invite_gold']+$gold['tender']+$gold['reg'];
		
		return $gold;
	}
	
	/**
	 * 查看用户积分
	 *
	 * @param Array $data
	 * @return Array
	 */
	function GetOne($data = array()){
		global $mysql;
		if (!IsExiest($data['user_id'])) return "credit_user_id_empty";
		$sql = "select p1.* from `{credit}` as p1 where p1.user_id={$data['user_id']}";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) {
			$sql = "insert into `{credit}` set user_id='{$data['user_id']}'";
			$mysql -> db_query($sql);
			$result = self::GetOne($data);
		}
		return $result;
	}
	
	function GetCredits($data){
		global $mysql,$_G;
		$result = unserialize($data['credits']);
		$_result = array();
		if ($result=="") return "";
		foreach ($result as $key => $value){
			$_result[$value['class_id']] = $value['num'];
		}
		$__result = array();
		foreach ($_G['credit']['class'] as $key => $value){
			if ($_result[$value['id']]>0){
				$__result[$value['nid']] = $_result[$value['id']];
			}else{
				$__result[$value['nid']] = 0;
			}
		}
		return $__result;
	}
    
    
    //获取积分所对应的等级 credit,class
    public static function GetUserCreditRank($data){
        global $mysql,$_G;
        $integral =   $data['credit'];
        $class =   $data['class'];
        $_G['credit'] = isset($_G['credit'])?$_G['credit']:[];
    	if ($integral=="" && $integral!="0") return "";
    	if (empty($_G['credit']['rank'])) return "";
    	$_result = array();
    	foreach ($_G['credit']['rank'] as $key => $value){
    		$_result[$value['class_nid']][] = $value;
    	}
    	if ($class==""){
    		$result = $_result[0];
    	}else{
    		$result = $_result[$class];
    	}
    	if (count($result)>0){
    	foreach ($result as $key=>$value){
    		if ($value['point1']<=$integral && $value['point2']>=$integral){
    			return $value;
    		}elseif ($integral<=0 && $value['point2']==0){
    			return $value;
    		}
    	}
    	}
    }

}
?>
