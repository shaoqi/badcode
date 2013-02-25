<?php
/******************************
 * $File: group.class.php
 * $Description: 圈子管理
 * $Author: ahui 
 * $Time:2011-08-09
 * $Update:None 
 * $UpdateDate:None 
 * Copyright(c) 2012 by dycms.net. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

require_once("group.model.php");

class groupClass {
	
	/**
	 * 1,圈子类型
	 *
	 * @param array $data =array("name"=>"名称","status"=>"状态");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function AddGroupType($data = array()){
		global $mysql;
		 //判断名称是否存在
        if (!IsExiest($data['name'])) {
            return "group_type_name_empty";
        }
		 //判断标识名是否存在
        if (!IsExiest($data['nid'])) {
            return "group_type_nid_empty";
        }
		//判断标识名是否存在
		$sql = "select 1 from `{group_type}` where nid='{$data['nid']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "group_type_nid_exiest";
		
		$sql = "insert into `{group_type}` set addtime='".time()."',addip='".ip_address()."',";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql));
    	return $mysql->db_insert_id();
	}
	
	/**
	 * 2,修改圈子类型
	 *
	 * @param array $data =array("id"=>"id","name"=>"名称","status"=>"状态");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function UpdateGroupType($data = array()){
		global $mysql;
		
		 //判断名称是否存在
        if (!IsExiest($data['name'])) {
            return "group_type_name_empty";
        }
		 //判断标识名是否存在
        if (!IsExiest($data['nid'])) {
            return "group_type_nid_empty";
		}
		
		//判断标识名是否存在
		$sql = "select 1 from `{group_type}` where nid='{$data['nid']}' and id!={$data['id']}";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "group_type_nid_exiest";
		
		$sql = "update `{group_type}` set ";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql)." where id='{$data['id']}' ");
		return $data['id'];
	}
	
	/**
	 * 3,删除圈子类型
	 *
	 * @param Array $data = array("id"=>"ID")
	 * @return Boolen
	 */
	function DeleteGroupType($data = array()){
		global $mysql;
		
		if (!IsExiest($data['id'])) return "group_type_id_empty";
		
		$sql = "select 1 from `{group}` where type_id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result != false) return "group_type_group_exiest";
		
		$sql = "delete from `{group_type}`  where id='{$data['id']}'";
    	$mysql -> db_query($sql);
		
		return $data['id'];
	}
	
	
	
	
	/**
	 * 5,获得圈子列表
	 *
	 * @return Array
	 */
	function GetGroupTypeList($data = array()){
		global $mysql;
		
		$_sql = " where 1=1 ";
		$_select = " p1.*";
		$_order = " order by p1.id desc";
		$sql = "select SELECT from `{group_type}` as p1  SQL ORDER LIMIT";
		
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
	 * 6,获得圈子的单条记录
	 *
	 * @param Array $data = array("id"=>"");
	 * @return Array
	 */
	 function GetGroupTypeOne($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "group_type_id_empty";
		
		$sql = "select p1.* from `{group_type}` as p1   where p1.id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		
		if ($result==false) return "group_type_empty";
		return $result;
	}
	
	
	
	/**
	 * 1,创建圈子
	 *
	 * @param array $data =array("name"=>"名称","status"=>"状态");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function AddGroup($data = array()){
		global $mysql,$_G,$upload;
		 //判断名称是否存在
        if (!IsExiest($data['name'])) {
            return "group_name_empty";
        }
		
		//判断类型
        if (!IsExiest($data['type_id'])) {
            return "group_type_id_empty";
        }
		
		if (isset($data['username']) && $data['username']!=""){
			$sql = "select user_id from `{users}` where username='{$data['username']}'";
			$result = $mysql->db_fetch_array($sql);
			
			if ($result==false) return "group_username_empty";
			unset($data['username']);
			$data['user_id'] = $result['user_id'];
		}
		
			
		//判断用户
        if (!IsExiest($data['user_id'])) {
            return "group_user_id_empty";
        }
		//个人创建的圈子不能多于10个
		$sql = "select count(1) as num from `{group}` where user_id='{$data['user_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result['num']>10) return "group_more_10";
		
		$sql = "insert into `{group}` set addtime='".time()."',addip='".ip_address()."',";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql));
    	$id = $mysql->db_insert_id();
		
		$_G['upimg']['file'] = "pic";
		$_G['upimg']['mask_status']=0;
		$_G['upimg']['code'] = "group";
		$_G['upimg']['type'] = "group";
		$_G['upimg']['user_id'] = $data["user_id"];
		$_G['upimg']['article_id'] = $id;
		$pic_result = $upload->upfile($_G['upimg']);
		if ($pic_result!=""){
			$sql = "update `{group}` set litpic ='{$pic_result[0]['upfiles_id']}'  where id={$id}";
			$mysql->db_query($sql);
		}
		return $id;
	}
	
	/**
	 * 2,修改圈子
	 *
	 * @param array $data =array("id"=>"id","name"=>"名称","status"=>"状态");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function UpdateGroup($data = array()){
		global $mysql,$_G,$upload;
		
		 //判断名称是否存在
        if (!IsExiest($data['name'])) {
            return "group_name_empty";
        }
		//判断用户
        if (!IsExiest($data['user_id'])) {
            return "group_user_id_empty";
        }
		
		//判断类型
        if (!IsExiest($data['type_id'])) {
            return "group_type_id_empty";
        }
		
		$sql = "update `{group}` set ";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql)." where user_id='{$data['user_id']}' and id='{$data['id']}' ");
		
		$_G['upimg']['file'] = "pic";
		$_G['upimg']['mask_status']=0;
		$_G['upimg']['code'] = "group";
		$_G['upimg']['type'] = "group";
		$_G['upimg']['user_id'] = $data["user_id"];
		$_G['upimg']['article_id'] = $id;
		$pic_result = $upload->upfile($_G['upimg']);
		if ($pic_result!=""){
			$_data['user_id'] = $data['user_id'];
			$_data['id'] = $data['litpic'];
			$upload->Delete($_data);
			$sql = "update `{group}` set litpic ={$pic_result[0]['upfiles_id']}  where id={$data['id']}";
			$mysql->db_query($sql);
		}
		
		return $data['id'];
	}
	
	/**
	 * 3,删除圈子类型
	 *
	 * @param Array $data = array("id"=>"ID")
	 * @return Boolen
	 */
	function DeleteGroup($data = array()){
		global $mysql,$upload;
		
		if (!IsExiest($data['id'])) return "group_type_id_empty";
		if ($data['admin']!=1){
			$sql = "select 1 from `{group_articles}` where group_id='{$data['id']}'";
			$result = $mysql->db_fetch_array($sql);
			if ($result != false) return "group_articles_exiest";
			
			$sql = "select user_id,litpic from `{group}` where user_id='{$data['user_id']}' && id='{$data['id']}'";
			$result = $mysql->db_fetch_array($sql);
			if ($result==false) return "group_empty";
			$_data['user_id'] = $result['user_id'];
			$_data['id'] = $result['litpic'];
			$upload->Delete($_data);
			
			$sql = "delete from `{group}`  where user_id='{$data['user_id']}' && id='{$data['id']}'";
			$mysql -> db_query($sql);
		}else{
			$sql = "delete from `{group_articles}`  where group_id='{$data['id']}'";
			$result = $mysql->db_query($sql);
			
			$sql = "select user_id,litpic from `{group}` where id='{$data['id']}'";
			$result = $mysql->db_fetch_array($sql);
			if ($result==false) return "group_empty";
			$_data['user_id'] = $result['user_id'];
			$_data['id'] = $result['litpic'];
			$upload->Delete($_data);
			
			$sql = "delete from `{group}`  where id='{$data['id']}'";
			$mysql -> db_query($sql);
		}
		
		return $data['id'];
	}
	
	
	
	
	/**
	 * 5,获得圈子列表
	 *
	 * @return Array
	 */
	function GetGroupList($data = array()){
		global $mysql,$_G;
		
		$_sql = " where 1=1 ";
		if ((isset($data['status']) && $data['status']!="") || $data['status']=="0"){
			$_sql .= " and p1.status={$data['status']}";
		}
		
		if (isset($data['username']) && $data['username']!=""){
			$_sql .= " and p3.username like '%{$data['username']}%' ";
		}
		if (isset($data['nid']) && $data['nid']!=""){
			if ($data['nid']=="my"){
				$data['my_userid'] = $_G['user_id'];
			}else{
				$_sql .= " and p2.nid = '{$data['nid']}' ";
				$sql = "select name from `{group_type}` where nid='{$data['nid']}'";
				$result = $mysql->db_fetch_array($sql);
				$type_name = $result['name'];
			}
		}
		
		if (isset($data['my_userid']) && $data['my_userid']!=""){
			$_sql .= " and p1.id in (select group_id from `{group_member}` where user_id='{$data['my_userid']}' and status=1)";
		}
		$_select = " p1.*,p2.name as type_name,p3.username,p4.fileurl as litpic_url";
		$_order = " order by p1.id desc";
		$sql = "select SELECT from `{group}` as p1 left join `{group_type}` as p2 on p1.type_id=p2.id left join `{users_upfiles}` as p4 on p1.litpic=p4.id left join `{users}` as p3 on p1.user_id=p3.user_id  SQL ORDER ";
		
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
		
		foreach($list as $key => $value){
			$sql="select count(1) as num from `{group_articles}` where group_id={$value['id']}";
			$_result=$mysql->db_fetch_array($sql);
			$list[$key]['articles_count']=$_result['num'];
		}
		
		//返回最终的结果
		$result = array('list' => $list?$list:array(),'total' => $total,'type_name' => $type_name,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		return $result;
	}
	
	
	
	/**
	 * 6,获得圈子的单条记录
	 *
	 * @param Array $data = array("id"=>"");
	 * @return Array
	 */
	 function GetGroupOne($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "group_id_empty";
		$_select = " p1.*,p2.name as type_name,p3.username,p4.fileurl as litpic_url";
		
		$sql = "select {$_select} from `{group}` as p1 left join `{group_type}` as p2 on p1.type_id=p2.id left join `{users_upfiles}` as p4 on p1.litpic=p4.id left join `{users}` as p3 on p1.user_id=p3.user_id  where p1.id='{$data['id']}'";
		
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "group_empty";
		return $result;
	}
	
	
	/**
	 * 7,审核圈子
	 *
	 * @param Array $data = array("id"=>"");
	 * @return Array
	 */
	 function VerifyGroup($data = array()){
		global $mysql;
		
		if (!IsExiest($data['id'])) return "group_id_empty";
		$sql = "select p1.*,p2.username from `{group}` as p1  left join `{users}` as p2 on p1.user_id=p2.user_id where id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "group_empty";
		$sql = "update `{group}` set verify_time='".time()."',verify_userid='{$data['verify_userid']}',verify_remark='{$data['verify_remark']}',status={$data['status']} where  id='{$data['id']}' ";
		$mysql->db_query($sql);
		$_sql = "insert into `{group_member}` set user_id='{$result['user_id']}',group_id='{$result['id']}',remark='圈主',status=1,admin_status=2";
		$mysql->db_query($_sql);
		
		//加入审核记录
		$_data["user_id"] = $result["user_id"];
		$_data["result"] = $data["status"];
		$_data["code"] = "group";
		$_data["type"] = "group";
		$_data["article_id"] = $data["id"];
		$_data["verify_userid"] = $data["verify_userid"];
		$_data["remark"] = $data["verify_remark"];
		usersClass::AddExamine($_data);
		return $data['id'];
	}
	
	/**
	 * 8,获得圈子分类列表
	 *
	 * @return Array
	 */
	function GetGroupLists($data = array()){
		global $mysql;
		
		$_sql = " where 1=1 ";
		if ((isset($data['status']) && $data['status']!="") || $data['status']=="0"){
			$_sql .= " and p1.status={$data['status']}";
		}
		
		if (isset($data['username']) && $data['username']!=""){
			$_sql .= " and p3.username like '%{$data['username']}%' ";
		}
		
		if (isset($data['public']) && $data['public']!=""){
			$_sql .= " and p1.public={$data['public']}";
		}
		
		
		if (isset($data['username']) && $data['username']!=""){
			$_sql .= " and p3.username like '%{$data['username']}%' ";
		}
		
		$type_result = self::GetGroupTypeList(array("limit"=>"all"));
		foreach ($type_result as $key => $value){
			$_select = " p1.*,p2.name as type_name,p3.username,p4.fileurl as litpic_url";
			$_order = " order by p1.id desc";
			$sql = "select SELECT from `{group}` as p1 left join `{group_type}` as p2 on p1.type_id=p2.id left join `{users_upfiles}` as p4 on p1.litpic=p4.id left join `{users}` as p3 on p1.user_id=p3.user_id  SQL and p1.type_id={$value['id']} ORDER LIMIT";
		
			if ($data['limit'] != "all"){ $_limit = "  limit ".$data['limit']; }
			$type_result[$key]['list'] = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array($_select, $_sql, $_order, $_limit), $sql));
			foreach($type_result[$key]['list'] as $k => $v){
				$sql="select count(1) as num from `{group_articles}` where group_id={$v['id']}";
				$_result=$mysql->db_fetch_array($sql);
				$type_result[$key]['list'][$k]['articles_count']=$_result['num'];
			}
		}			 
		
		return $type_result;
	}
	
	
	
	/**
	 * 9,获得圈子的相关信息
	 *
	 * @param Array $data = array("id"=>"");
	 * @return Array
	 */
	 function GetGroupOnes($data = array()){
		global $mysql,$_G;
		if (!IsExiest($data['article_id'])) return "group_id_empty";
		$result = self::GetGroupOne(array("id"=>$data['article_id']));
		$_manager = $result['manager'];
		if ($_manager!=""){
			$sql = "select username,user_id from `{users}` where user_id in ($_manager)";
			$result['manager_result'] = $mysql->db_fetch_arrays($sql);
		}
		$rank_result = self::CheckGroupRank(array("user_id"=>$_G['user_id'],"group_id"=>$data['article_id']));
		$result['member_status'] = $rank_result;
		$result['member_result'] = self::GetGroupMemberOne(array("id"=>$data['article_id']));
		return $result;
	}
	
	/**
	 * 10,修改圈子的统计
	 *
	 * @param Array $data = array("id"=>"");
	 * @return Array
	 */
	function UpdateGroupCount($data){
		global $mysql;
		if ($data['group_id']=="") return "";
		$sql = "select count(1) as num from `{group_member}` where status=1 and group_id='{$data['group_id']}'";
		$result_member = $mysql->db_fetch_array($sql);
		$sql = "select count(1) as num from `{group_articles}` where status=1 and group_id='{$data['group_id']}'";
		$result_articles = $mysql->db_fetch_array($sql);
		$sql = "select count(1) as num from `{group_comments}` where status=1 and group_id='{$data['group_id']}'";
		$result_comments = $mysql->db_fetch_array($sql);
		$sql = "select user_id,admin_status from `{group_member}` where status=1 and group_id='{$data['group_id']}'";
		$result = $mysql->db_fetch_arrays($sql);
		$_result = array();
		$_result_admin = array();
		foreach ($result as $key => $value){
			$_result[] = $value['user_id'];
			if ($value['admin_status']==1){
					$_result_admin[] = $value['user_id'];
			}
		}
		$_users = join(",",$_result);
		$_manager = join(",",$_result_admin);
		$sql = "update `{group}` set member_count='{$result_member['num']}',articles_count='{$result_member['num']}',comment_count='{$result_comments['num']}',users='{$_users}',manager='{$_manager}' where  id='{$data['group_id']}'";
	 	$mysql->db_query($sql);
		
		//更新下面的所有的文章和评论
	
	}
	/**
	 * 0,判断权限
	 *
	 * @param array $data =array("group_id"=>"名称","user_id"=>"用户","articles_id"=>"话题","comments_id"=>"评论");
	 * @param string $data;
	 * @return boolen(true,false)
	 * 3,表示圈主，拥有最高等级，2，表示管理员等级,1，表示圈子成员，0表示非圈子成员
	 */
	function CheckGroupRank($data){
		global $mysql;
		if (isset($data['group_id']) && $data['group_id']!=""){
			$sql = "select user_id from `{group}` where id='{$data['group_id']}' " ;
			$result = $mysql->db_fetch_array($sql);	
			if ($data['user_id']==$result['user_id']) {
				return 3;
			}
		
			$sql = "select admin_status from `{group_member}` where user_id='{$data['user_id']}' and group_id='{$data['group_id']}' and status=1";
			$result = $mysql->db_fetch_array($sql);
			if ($result!=false ){
				if ($result['admin_status']==1){
					return 2;
				}else{
					return 1;
				}
			}else{
				return 0;
			}
		}
		
		
	}
	
	
	
	/**
	 * 1,创建圈子主题
	 *
	 * @param array $data =array("name"=>"名称","status"=>"状态");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function AddGroupArticles($data = array()){
		global $mysql;
		 //判断名称是否存在
        if (!IsExiest($data['name'])) {
            return "group_articles_name_empty";
        }
		
		if (isset($data['username']) && $data['username']!=""){
			$sql = "select user_id from `{users}` where username='{$data['username']}'";
			$result = $mysql->db_fetch_array($sql);
			if ($result==false) return "group_username_empty";
			unset($data['username']);
			$data['user_id'] = $result['user_id'];
		}
		
		
		//判断用户
        if (!IsExiest($data['user_id'])) {
            return "group_articles_user_id_empty";
        }
		
		
		$sql = "insert into `{group_articles}` set addtime='".time()."',addip='".ip_address()."',";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql));
    	$id = $mysql->db_insert_id();
		
		return $id;
	}
	
	/**
	 * 2,修改圈子主题
	 *
	 * @param array $data =array("id"=>"id","name"=>"名称","status"=>"状态");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function UpdateGroupArticles($data = array()){
		global $mysql;
		
		 //判断名称是否存在
        if (!IsExiest($data['id'])) {
            return "group_articles_name_empty";
        }
		
		$sql = "select group_id from `{group_articles}` where id={$data['id']}";
		$result=$mysql->db_fetch_array($sql);
		
		$sql = "update `{group_articles}` set ";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql)." where id='{$data['id']}' ");
		
		return $result['group_id'];
	}
	
	/**
	 * 3,删除圈子类型
	 *
	 * @param Array $data = array("id"=>"ID")
	 * @return Boolen
	 */
	function DeleteGroupArticles($data = array()){
		global $mysql,$upload;
		
		if (!IsExiest($data['id'])) return "group_articles_id_empty";
		
		$sql = "select 1 from `{group_articles}` where id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result== false) return "group_articles_empty";
		
		$sql = "delete from `{group_articles}`  where id='{$data['id']}'";
    	$mysql -> db_query($sql);
		
		return $data['id'];
	}
	
	
	
	
	/**
	 * 5,获得圈子列表
	 *
	 * @return Array
	 */
	function GetGroupArticlesList($data = array()){
		global $mysql;
		
		$_sql = " where 1=1 ";
		if ((isset($data['status']) && $data['status']!="") || $data['status']=="0"){
			$_sql .= " and p1.status={$data['status']}";
		}
		
		if (isset($data['group_id']) && $data['group_id']!=""){
			$_sql .= " and p1.group_id='{$data['group_id']}'";
		}
		
		if (isset($data['username']) && $data['username']!=""){
			$_sql .= " and p2.username like '%{$data['username']}%' ";
		}
		$_select = " p1.*,p2.username,p3.name as group_name";
		$_order = " order by p1.id desc";
		$sql = "select SELECT from `{group_articles}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id left join `{group}` as p3 on p1.group_id=p3.id  SQL ORDER LIMIT";
		
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
		foreach ($list as $key => $value){
				$list[$key]["contents"] =  preg_replace('[\[\:([0-9]+)*\:\]]',"<img src=/data/images/face/$1.gif>", $value["contents"]);
			}
		//返回最终的结果
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		return $result;
	}
	
	
	
	/**
	 * 6,获得圈子的单条记录
	 *
	 * @param Array $data = array("id"=>"");
	 * @return Array
	 */
	 function GetGroupArticlesOne($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "group_articles_id_empty";
		$_select = " p1.*,p2.username,p3.name as group_name";
		
		$sql = "select {$_select} from `{group_articles}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id  left join `{group}` as p3 on p1.group_id=p3.id  where p1.id='{$data['id']}'";
		
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "group_articles_empty";
		$result["contents"] =  preg_replace('[\[\:([0-9]+)*\:\]]',"<img src=/data/images/face/$1.gif>", $result["contents"]);
		return $result;
	}
	
	
	/**
	 * 7,获得圈子的单条记录
	 *
	 * @param Array $data = array("id"=>"");
	 * @return Array
	 */
	 function GetGroupArticlesOnes($data = array()){
		global $mysql,$_G;
		if (!IsExiest($data['id'])) return "group_articles_id_empty";
		$_select = " p1.*,p2.username,p3.name as group_name";
		
		$sql = "select {$_select} from `{group_articles}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id  left join `{group}` as p3 on p1.group_id=p3.id  where p1.id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "group_articles_empty";
		//判断是否是圈子里面的人
		$rank_result = self::CheckGroupRank(array("user_id"=>$_G['user_id'],"group_id"=>$data['group_id']));
		$result['member_status'] = $rank_result;
		
		
		
		return $result;
	}
	
	
		
	/**
	 * 1,添加圈子成员
	 *
	 * @param array $data =array("name"=>"名称","status"=>"状态");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function AddGroupMember($data = array()){
		global $mysql;
		 //判断名称是否存在
        if (!IsExiest($data['user_id'])) {
            return "group_member_userid_empty";
        }
		 //判断标识名是否存在
        if (!IsExiest($data['group_id'])) {
            return "group_member_groupid_empty";
        }
		$sql = "select user_id from `{group}` where id='{$data['group_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result['user_id']==$data['user_id']){
			return "group_member_owner_not_join";	
		}
		//判断标识名是否存在
		$sql = "select status,id from `{group_member}` where user_id='{$data['user_id']}' and  group_id='{$data['group_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false){
			if ($result['status']==1) return "group_member_yes_group";
			if ($result['status']==0) return "group_member_wait_group";
			$sql = "update `{group_member}` set remark='{$data['remark']}',update_time='".time()."',update_ip='".ip_address()."',times = times+1,status=0 where user_id='{$data['user_id']}' and  group_id='{$data['group_id']}'";
			$mysql->db_query($sql);
			return $result['id'];
		}
		
		$sql = "insert into `{group_member}` set addtime='".time()."',addip='".ip_address()."',times = 1,";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
		$mysql->db_query($sql.join(",",$_sql));
    	return $mysql->db_insert_id();
	}
	
	/**
	 * 2,修改圈子成员
	 *
	 * @param array $data =array("id"=>"id","name"=>"名称","status"=>"状态");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function UpdateGroupMember($data = array()){
		global $mysql;
		
		 //判断名称是否存在
		//判断标识名是否存在
        if (!IsExiest($data['group_id'])) {
            return "group_member_groupid_empty";
        }
		//判断标识名是否存在
        if (!IsExiest($data['user_id'])) {
            return "group_member_userid_empty";
        }
		$sql = "update `{group_member}` set ";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
		$sql = $sql.join(",",$_sql)." where user_id='{$data['user_id']}' and group_id='{$data['group_id']}'   ";
        $mysql->db_query($sql);
		
		
		
		$sql = "update `{group_articles}` set status='{$data['status']}' where user_id='{$data['user_id']}' and group_id='{$data['group_id']}'";
	 	$mysql->db_query($sql);
		
		$sql = "update `{group_comments}` set status='{$data['status']}' where user_id='{$data['user_id']}' and group_id='{$data['group_id']}'";
	 	$mysql->db_query($sql);
		
		self::UpdateGroupCount(array("group_id"=>$data['group_id']));
		return $data['user_id'];
	}
	
	/**
	 * 3,获得圈子成员列表
	 *
	 * @return Array
	 */
	function GetGroupMemberList($data = array()){
		global $mysql;
		
		$_sql = " where 1=1 ";
		if ((isset($data['status']) && $data['status']!="") || $data['status']=="0"){
			$_sql .= " and p1.status={$data['status']}";
		}
		
		if (isset($data['group_id']) && $data['group_id']!=""){
			if ($data['group_id']=="all"){
				$_sql .= " and p1.group_id in (select group_id from `{group_member}` where user_id='{$data['my_userid']}')";
			}else{
				$_sql .= " and p1.group_id='{$data['group_id']}'";
			}
		}
		
		if (isset($data['username']) && $data['username']!=""){
			$_sql .= " and p2.username like '%{$data['username']}%' ";
		}
		
		$_select = " p1.*,p2.username,p3.name as group_name,p4.name as user_typename";
		$_order = " order by p1.status asc,p1.id desc";
		$sql = "select SELECT from `{group_member}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id left join `{users_info}` as p5 on p5.user_id=p1.user_id left join `{users_type}` as p4 on p5.type_id=p4.id left join `{group}` as p3 on p1.group_id=p3.id  SQL ORDER LIMIT";
		
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
	 * 4,获得圈子成员的单条信息
	 *
	 * @param Array $data = array("id"=>"");
	 * @return Array
	 */
	 function GetGroupMemberOne($data = array()){
		global $mysql;
		$_sql = " where 1=1 ";
		if (isset($data['id']) && $data['id']!=""){
			$_sql .= " and p1.id={$data['id']}";
		}
		if (isset($data['group_id']) && $data['group_id']!=""){
			$_sql .= " and p1.group_id={$data['group_id']}";
		}
		if (isset($data['user_id']) && $data['user_id']!=""){
			$_sql .= " and p1.user_id={$data['user_id']}";
		}
		if (isset($data['status']) && $data['status']!=""){
			$_sql .= " and p1.status={$data['status']}";
		}
		$_select = " p1.*,p2.username,p3.name as group_name";
		
		$sql = "select {$_select} from `{group_member}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id  left join `{group}` as p3 on p1.group_id=p3.id  $_sql";
		
		$result = $mysql->db_fetch_array($sql);
		
		if ($result==false) return "group_member_empty";
		return $result;
	}
	
	/*
	 * 5,审核圈子成员的信息
	 *
	 * @param Array $data = array("group_id"=>"群组","type"=>"审核类型","user_id"=>"申请人","verify_remark"=>"审核备注","verify_userid"=>"审核人");
	 * @return Array
	 */
	 function VerifyGroupMember($data = array()){
		global $mysql;
		if (!IsExiest($data['group_id'])) return "group_id_empty";
		$sql = "select user_id,manager from `{group}` where id='{$data['group_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "group_empty";
		//判断是否是管理员才可以进行审核
		$_manager = array();
		if ($result['manager']!=""){
			$_manager = explode(",",$result['manager']);
		}
		if ($data['verify_userid']!=$result['user_id'] && !in_array($data['verify_userid'],$_manager)){
			return "group_member_check_admin_no";
		
		}
		
		$_log['user_id'] = $data['verify_userid'];
		$_log['verify_remark'] = $data['verify_remark'];
		$_log['group_id'] = $data['group_id'];
		$_log['type'] = "member_verify";
		$_log['to_userid'] = $data['user_id'];
		if (!is_array($data['user_id'])){
			if ($data['type']=="yes"){
				$sql = "update `{group_member}` set verify_userid='{$data['verify_userid']}', verify_time='".time()."',verify_remark='{$data['verify_remark']}',status=1 where group_id='{$data['group_id']}' and user_id='{$data['user_id']}'";
				$mysql->db_query($sql);
				$_log['result'] = "yes";
				$_log['remark'] = "通过审核";
			}elseif ($data['type']=="no"){
				$sql = "update `{group_member}` set verify_userid='{$data['verify_userid']}', verify_time='".time()."',verify_remark='{$data['verify_remark']}',status=2 where group_id='{$data['group_id']}' and user_id='{$data['user_id']}'";
				$mysql->db_query($sql);
				$_log['result'] = "no";
				$_log['remark'] = "通过不审核";
			}elseif ($data['type']=="admin_yes"){
				$sql = "update `{group_member}` set admin_status=1 where group_id='{$data['group_id']}' and user_id='{$data['user_id']}'";
				$mysql->db_query($sql);
				$_log['result'] = "admin_yes";
				$_log['remark'] = "设为管理员";
			}elseif ($data['type']=="admin_no"){
				$sql = "update `{group_member}` set admin_status=0 where group_id='{$data['group_id']}' and user_id='{$data['user_id']}'";
				$mysql->db_query($sql);
				$_log['result'] = "admin_no";
				$_log['remark'] = "取消管理员";
			}elseif ($data['type']=="out"){
				$sql = "delete  from `{group_member}` where group_id='{$data['group_id']}' and user_id='{$data['user_id']}'";
				$mysql->db_query($sql);
				$_log['result'] = "admin";
				$_log['remark'] = "踢出圈子";
			}
			self::AddGroupLog($_log);
		}else{
			foreach($data['user_id'] as $key => $value){
				$sql = "update `{group_member}` set verify_userid='{$data['verify_userid']}', verify_time='".time()."',verify_remark='{$data['verify_remark']}',status=1 where group_id='{$data['group_id']}' and user_id='{$value}'";
				$mysql->db_query($sql);
				$_log['result'] = "yes";
				$_log['remark'] = "通过审核";
				self::AddGroupLog($_log);
			}
		}
		$sql = "select user_id,admin_status from `{group_member}` where group_id='{$data['group_id']}' and status=1";
		$result = $mysql->db_fetch_arrays($sql);
		$_result = array();
		$_result_admin = array();
		foreach ($result as $key => $value){
			$_result[] = $value['user_id'];	
			if ($value['admin_status']=="1"){
				$_result_admin[] = $value['user_id'];	
			}
		}
		$sql = "update `{group}` set member_count='".count($_result)."',users='".(join(",",$_result))."',manager='".(join(",",$_result_admin))."' where id='{$data['group_id']}'";
		$mysql->db_query($sql);
		
		return $data['user_id'];
	}
	
	/*
	 * 6,关闭成员
	 *
	 * @param Array $data = array("group_id"=>"群组","type"=>"审核类型","user_id"=>"申请人","verify_remark"=>"审核备注","verify_userid"=>"审核人");
	 * @return Array
	 */
	 function CloseGroupMember($data){
	 	global $mysql;
		$sql = "select 1 from `{group_member}` where user_id='{$data['user_id']}' and group_id='{$data['group_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result ==false) return "group_member_empty";
		
		$sql = "update `{group_member}` set status=3 where user_id='{$data['user_id']}' and group_id='{$data['group_id']}'";
	 	$mysql->db_query($sql);
		
		
		$sql = "update `{group_articles}` set status=3 where user_id='{$data['user_id']}' and group_id='{$data['group_id']}'";
	 	$mysql->db_query($sql);
		
		
		$sql = "update `{group_comments}` set status=3 where user_id='{$data['user_id']}' and group_id='{$data['group_id']}'";
	 	$mysql->db_query($sql);
		
		self::UpdateGroupCount(array("group_id"=>$data['group_id']));
		return 1;
	 }
	
	
	/*
	 * 7,退出圈子
	 *
	 * @param Array $data = array("group_id"=>"群组","type"=>"审核类型","user_id"=>"申请人","verify_remark"=>"审核备注","verify_userid"=>"审核人");
	 * @return Array
	 */
	 function ExitGroupMember($data){
	 	global $mysql;
		$sql = "select 1 from `{group_member}` where user_id='{$data['user_id']}' and group_id='{$data['group_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result ==false) return "group_member_empty";
		
		$sql = "update `{group_member}` set status=4 where user_id='{$data['user_id']}' and group_id='{$data['group_id']}'";
	 	$mysql->db_query($sql);
		
		
		$sql = "update `{group_articles}` set status=4 where user_id='{$data['user_id']}' and group_id='{$data['group_id']}'";
	 	$mysql->db_query($sql);
		
		
		$sql = "update `{group_comments}` set status=4 where user_id='{$data['user_id']}' and group_id='{$data['group_id']}'";
	 	$mysql->db_query($sql);
		
		self::UpdateGroupCount(array("group_id"=>$data['group_id']));
		return 1;
	 }
	
	/**
	 * 1,圈子评论
	 *
	 * @param array $data =array("name"=>"名称","status"=>"状态");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function AddGroupComments($data = array()){
		global $mysql;
		 //判断名称是否存在
        if (!IsExiest($data['user_id'])) {
            return "group_comments_user_id_empty";
        }
		//判断标识名是否存在
        if (!IsExiest($data['group_id'])) {
            return "group_comments_group_id_empty";
        }
		//判断标识名是否存在
        if (!IsExiest($data['articles_id'])) {
            return "group_comments_articles_id_empty";
        }
		//判断标识名是否存在
        if (!IsExiest($data['contents'])) {
            return "group_comments_contents_empty";
        }
		
		$sql = "insert into `{group_comments}` set addtime='".time()."',addip='".ip_address()."',";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql));
    	$result = $mysql->db_insert_id();
		if ($result>0){
			$sql = "update `{group_articles}` set comment_count=comment_count+1,comment_time='".time()."' where id='{$data['articles_id']}' ";
			$mysql->db_query($sql);
		}
		return $result;
	}
	
	/**
	 * 2,修改圈子评论
	 *
	 * @param array $data =array("id"=>"id","name"=>"名称","status"=>"状态");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function UpdateGroupComments($data = array()){
		global $mysql;
		
		 //判断名称是否存在
		//判断标识名是否存在
        if (!IsExiest($data['group_id'])) {
            return "group_comments_group_id_empty";
        }
		//判断标识名是否存在
        if (!IsExiest($data['articles_id'])) {
            return "group_comments_articles_id_empty";
        }
		//判断标识名是否存在
        if (!IsExiest($data['contents'])) {
            return "group_comments_contents_empty";
        }
		
		$sql = "update `{group_comments}` set ";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
		$sql = $sql.join(",",$_sql)." where id='{$data['id']}' and group_id='{$data['group_id']}'  and articles_id='{$data['articles_id']}' ";
        $mysql->db_query($sql);
		return $data['id'];
	}
	
	/**
	 * 3,删除圈子评论
	 *
	 * @param Array $data = array("id"=>"ID")
	 * @return Boolen
	 */
	function DeleteGroupComments($data = array()){
		global $mysql;
		
		 //判断名称是否存在
        if (!IsExiest($data['id'])) {
            return "group_comments_id_empty";
        }
		//判断标识名是否存在
        if (!IsExiest($data['group_id'])) {
            return "group_comments_group_id_empty";
        }
		
		//判断标识名是否存在
        if (!IsExiest($data['articles_id'])) {
            return "group_comments_articles_id_empty";
        }
		
		$sql = "delete from `{group_comments}` where id='{$data['id']}' and group_id='{$data['group_id']}'";
    	$mysql -> db_query($sql);
		
		$sql = "select addtime from `{group_articles}` where group_id='{$data['group_id']}' and id='{$data['articles_id']}'";
		$result = $mysql->db_fetch_array($sql);
		
		$sql = "update `{group_articles}` set comment_count=comment_count-1,comment_time='{$result['addtime']}' where id ='{$data['articles_id']}'";
		$mysql->db_query($sql);
		return $data['id'];
	}
	
	
	
	
	/**
	 * 5,获得圈子评论列表
	 *
	 * @return Array
	 */
	function GetGroupCommentsList($data = array()){
		global $mysql;
		
		$_sql = " where p1.pid=0 ";
		if (isset($data['articles_id']) && $data['articles_id']!=""){
			$_sql .= " and p1.articles_id='{$data['articles_id']}'";
		}
		if (isset($data['status']) && ($data['status']!="" || $data['status']=="0")){
			$_sql .= " and p1.status='{$data['status']}'";
		}
		if ($data['type']=="all"){
			$_select = " p1.*,p2.username,p3.name as group_name ,p4.name as articles_name";
			$_order = " order by p1.status asc,p1.id desc";
			$sql = "select SELECT from `{group_comments}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id left join `{group}` as p3 on p1.group_id=p3.id left join `{group_articles}` as p4 on p1.articles_id= p4.id SQL ORDER LIMIT";
		}else{
			$_select = " p1.*,p2.username";
			$_order = " order by p1.status asc,p1.id desc";
			$sql = "select SELECT from `{group_comments}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id  SQL ORDER LIMIT";
		}
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
		$list =  $list?$list:array();
		$_users = array();
		foreach ($list as $key => $value){
			$_users[] = $value['id'];
		}
		$_list = array();
		if (count($_users)>0){
			$_users = join(',',$_users);
			$sql = "select p1.*,p2.username from `{group_comments}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where tid in ({$_users})";
			$result = $mysql->db_fetch_arrays($sql);
			$__list = array();
			foreach ($result as $_key => $_value){
				$__list[$_value['tid']][$_value['id']] = $_value;
			}
			foreach ($list as $key => $value){
				$_list[$key] = $value;
				$_list[$key]['sub_result'] = $__list[$value['id']];
				$_list[$key]["contents"] =  preg_replace('[\[\:([0-9]+)*\:\]]',"<img src=/data/images/face/$1.gif>", $value["contents"]);	
				
			}
		}else{
			$_list = $list;
		}
		//返回最终的结果
		$result = array('list' =>$_list,'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		
		return $result;
	}
	
	
	
	/**
	 * 6,获得圈子评论的单条记录
	 *
	 * @param Array $data = array("id"=>"");
	 * @return Array
	 */
	 function GetGroupCommentsOne($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "group_comments_id_empty";
		
		$sql = "select p1.* from `{group_comments}` as p1   where p1.id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		
		if ($result==false) return "group_comments_empty";
		return $result;
	}
	
	
	
	
	/**
	 * 7,获得圈子评论的单条记录
	 *
	 * @param Array $data = array("id"=>"");
	 * @return Array
	 */
	 function GetGroupCommentsOnes($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "group_comments_id_empty";
		
		$_select = " p1.*,p2.username,p3.name as group_name ,p4.name as articles_name";
			$sql = "select {$_select} from `{group_comments}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id left join `{group}` as p3 on p1.group_id=p3.id left join `{group_articles}` as p4 on p1.articles_id= p4.id where p1.id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		
		if ($result==false) return "group_comments_empty";
		return $result;
	}
	
	
	
	/*
	 * 1,添加各种记录
	 *
	 * @param Array $data = array("group_id"=>"群组","type"=>"审核类型","user_id"=>"申请人","verify_remark"=>"审核备注","verify_userid"=>"审核人");
	 * @return Array
	 */
	 function AddGroupLog($data = array()){
		global $mysql;
		
		$sql = "insert into `{group_log}` set addtime='".time()."',addip='".ip_address()."',";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
		$mysql->db_query($sql.join(",",$_sql));
    	return $mysql->db_insert_id();
	}
	
	
}
?>
