<?php
/**
 * @author Tissot.Cai
 * @copyright 厦门帝网信息科技有限公司
 * @version 1.0
 */

/**
 * 评论
 *
 * @author TissotCai
 */
class commentClass {


    const NOT_EXISTS_USER   = '用户不存在';
    const NOT_EXISTS_MODULE = '模块不存在';
    
    /**
     * 发表评论
     * @param $user_id 会员ID
     * @param $module_code 模块
     * @param $article_id 文章ID
     * @param $comment 内容
     */
    public static function AddComment($data = array()) {
        global $mysql, $_G;
        
		$sql = "insert into `{comment}` set `addtime` = '".time()."',`addip` = '".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
        $mysql->db_query($sql);
		 return $mysql->db_insert_id();
    }
	
	function AddLy($data = array()) {
        global $mysql, $_G;
        
		$sql = "insert into `{comment}` set `addtime` = '".time()."',`addip` = '".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
        $mysql->db_query($sql);
		return $mysql->db_insert_id();
    }

    /**
     * 获取评论列表
     * @param $module 模块
     * @param $article_id 文章ID
     * @param $statu 状态
     * @param $page 页码
     * @param $page_size 每页记录数
     */
    public static function GetList ($data = array()) {
        global $mysql;
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
		$_sql = "where 1=1 ";//直接对文章的评论
		
		//判断模块
        if(IsExiest($data['code'])!=""){
			$_sql .= " and  c.code = '{$data['code']}' "; 
		}
		
		//判断文章id
        if(IsExiest($data['article_id'])!=""){
			$_sql .= " and  c.article_id in ('{$data['article_id']}') "; 
		}
		
		//判断评论状态
        if(IsExiest($data['status'])!=""){
			$_sql .= " and  c.status = '{$data['status']}' "; 
		}
		//判断评论人
        if(IsExiest($data['user_id'])!=""){
			$_sql .= " and  c.user_id = '{$data['user_id']}' "; 
		}
		
		if(IsExiest($data['reply_userid'])!=""){
			$_sql .= " and  c.user_id = '{$data['user_id']}' "; 
		}
		
		$_select = "c.*, u.username";
		 $sql = "select SELECT from {comment} as c
                    left join {users} as u on c.user_id = u.user_id {$_sql} ";
		$__sql = "";
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
	 * 查看
	 *
	 * @param Array $data
	 * @return Array
	 */
	public static function GetOne($data = array()){
		global $mysql;
		$_sql = " where 1=1 ";
		
        if(IsExiest($data['id'])!=""){
			$_sql .= " and  c.id = '{$data['id']}' "; 
		}
		if(IsExiest($data['code'])!=""){
			$_sql .= " and  c.code = '{$data['code']}' "; 
		}
        if(IsExiest($data['article_id'])!=""){
			$_sql .= " and  c.article_id in ('{$data['article_id']}') "; 
		}
        if(IsExiest($data['status'])!=""){
			$_sql .= " and  c.status = '{$data['status']}' "; 
		}
        if(IsExiest($data['user_id'])!=""){
			$_sql .= " and  c.user_id = '{$data['user_id']}' "; 
		}
		
        if(IsExiest($data['article_userid'])!=""){
			$_sql .= " and  c.article_userid = '{$data['article_userid']}' "; 
		}
		
		$sql = "select m.name as module_name,u.username ,c.* from {comment} c 
				left join {user} u on c.user_id = u.user_id 
				left join {module} m on c.code = m.code 
				{$_sql}";
		$result =  $mysql->db_fetch_array($sql);
				if ($result!=false){
			$result["comment"] =  preg_replace('[\[\:([0-9]+)*\:\]]',"<img src=/data/images/face/$1.gif>", $result["comment"]);
		}
		return $result;
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
		$sql = "update `{comment}` set ";
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
		$sql = "delete from {comment}  where id in (".join(",",$id).")";
		return $mysql->db_query($sql);
	}
	
	
    /**
     * 删除评论
     * @param $id 评论ID
     */
    public static function DeleteComment ($id) {
        global $mysql;

        $mysql->db_query("delete from {comment} where id={$id}");

        return true;
    }

    /**
     * 修改评论状态
     * @param $id 评论ID
     */
    public static function ChangeCommentStatus ($id) {
        global $mysql;

        $mysql->db_query("update {comment} set status=1-status where id={$id}");

        return true;
    }

	/**
	 * 获取评论的子评论
	 * @param $id 评论ID
	 */
	public static function GetSubComment ($id) {
		global $mysql;

		$sql = "select c.*, u.username,u.realname,u.litpic, m.name as module_name from {comment} c
                    left join {user} u on c.user_id = u.user_id
                    left join {module} m on c.module_code=m.code where c.pid={$id}";
		
		$rows = $mysql->db_fetch_arrays($sql);
		foreach ($rows as $key => $row) {
			$row['sub'] = self::GetSubComment($row['id']);
			$rows[$key] = $row;
		}

		return $rows;
	}
	
	 public static function ReplyComment ($data = array()) {
        global $mysql, $_G;
		$_sql = "";
		
		
		$sql = "update `{comment}` set reply_status=1,`reply_time` = '".time()."',`reply_userid` = '{$data['reply_userid']}',`reply_remark` = '{$data['reply_remark']}' where article_userid = {$data['article_userid']} and id={$data['id']}";
        return $mysql->db_query($sql);
    }
	
	function AddRe($data = array()){
		global $mysql;
		$sql = "insert into `{comment}` set `addtime` = '".time()."',`addip` = '".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
		$result=$mysql->db_query($sql);
		return $result;
	}
	
		// 增加回复
	function AddReplay($data = array()){
		global $mysql;
		$sql = "insert into `{comment}` set `addtime` = '".time()."',`addip` = '".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
		$result=$mysql->db_query($sql);
		return $result;
	}
}
?>
