<?
/*
1,用户中心页面，需要读取所有的额度，程序在borrow.class.php function GetUserLog

2,用户中心 -额度申请页面
*/

$borrow_amount_type = array("borrow"=>"借款额度","vouch_borrow"=>"担保借款额度","vouch_tender"=>"一次性额度");

require_once(ROOT_PATH."modules/borrow/borrow.auto.php");
require_once(ROOT_PATH."modules/remind/remind.class.php");
class amountClass  extends autoClass {


	
	//添加额度的记录（borrow_amount_log）
	//user_id 用户id
	//type 操作的类型 
	//amount_type 额度的类型 ，credit 信用额度  borrow_vouch 借款额度  tender 投资额度
	//account  额度操作的金额
	//account_all 总的额度
	//account_use 可用额度
	//account_nouse 不可用额度
	//remark 额度的记录
	function  AddAmountLog($data){
		global $mysql;
		 //判断用户是否存在
        if (!IsExiest($data['user_id'])) {
            return "amount_user_id_empty";
        } 
		$sql = "select 1 from `{borrow_amount_log}` where nid='{$data['nid']}' ";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false){
			$data["account_use"] = 0;
			$data["account_all"] = 0;
			$sql = "select account_use,account_all from `{borrow_amount_log}` where user_id='{$data['user_id']}' and amount_type='{$data['amount_type']}' order by id desc";
			$result = $mysql->db_fetch_array($sql);
			if  ($result!=false){
				$data["account_use"] = $result['account_use'];
				$data["account_all"] = $result['account_all'];
			}
			if ($data['oprate']=="add"){
				$data["account_all"] = $data["account_all"] + $data["account"];
				$data["account_use"] = $data["account_use"] + $data["account"];
				$data["account_add"] = $data["account"];
			}elseif ($data['oprate']=="reduce"){
				$data["account_all"] = $data["account_all"] - $data["account"];
				$data["account_use"] = $data["account_use"] - $data["account"];
				$data["account_reduce"] = $data["account"];
			}elseif ($data['oprate']=="return"){
				$data["account_return"] = $data["account"];
				$data["account_use"] = $data["account_use"] + $data["account"];
			}elseif ($data['oprate']=="frost"){
				$data["account_frost"] = $data["account"];
				$data["account_use"] = $data["account_use"] - $data["account"];
			}
			
			//加入额度记录
			$sql = "insert into `{borrow_amount_log}` set `addtime` = '".time()."',`addip` = '".ip_address()."'";
			foreach($data as $key => $value){
				$sql .= ",`$key` = '$value'";
			}
			$mysql->db_query($sql);
			$name = $data['amount_type'];
			$name_use = $data['amount_type']."_use";
			
			$sql = "select amounts from `{borrow_amount}` where user_id='{$data['user_id']}'";
			$result = $mysql->db_fetch_array($sql);
			if ($result['amounts']!=false){
				$_amount = unserialize($result['amounts']);
			}
			$_amount[$name] = $data['account_all'];
			$_amount[$name_use] = $data['account_use'];
			$amounts = serialize($_amount);
			$sql = "update `{borrow_amount}` set `{$name_use}` ={$data['account_use']},`{$name}` ={$data['account_all']},`amounts`='{$amounts}' where user_id='{$data['user_id']}'";
			$mysql->db_query($sql);
		}
       
    	return $data['user_id'];
	}
	
	/**
	 * 4,获得额度记录列表
	 *
	 * @return Array
	 */
	function GetAmountLogList($data = array()){
		global $mysql;
		
		$_sql = " where 1=1 ";
		
		//搜索用户id
        if (IsExiest($data['user_id'])!=false) {
            $_sql .= " and p1.user_id ='{$data['user_id']}'";
        }
		
		//搜索用户名
		if (IsExiest($data['username'])!=false) {
            $_sql .= " and p2.username like '%{$data['username']}%'";
        }
		
		//搜索类型
		if (IsExiest($data['amount_type'])!=false) {
            $_sql .= " and p1.amount_type = '{$data['amount_type']}'";
        }
		
		//搜索类型
		if (IsExiest($data['type'])!=false) {
            $_sql .= " and p1.type = '{$data['type']}'";
        }
		
		$_select = " p1.*,p2.username";
		$_order = " order by p1.id desc";
		$sql = "select SELECT from `{borrow_amount_log}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id SQL ORDER LIMIT";
		
		//是否显示全部的信息
		if (IsExiest($data['limit'])!=false){
			if ($data['limit'] != "all"){ $_limit = "  limit ".$data['limit']; }
			return $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array($_select, $_sql, $_order, $_limit), $sql));
		}			 
		
		//判断总的条数
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array('count(1) as num', $_sql,'', ''), $sql));
		$total = intval($row['num']);
		
		$num_sql = "select p1.oprate,sum(p1.account) as num from `{borrow_amount_log}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id SQL group by p1.oprate ";
		$num_result =$mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array($_select, $_sql, $_order, $_limit), $num_sql));
		$_num_result = array();
		if ($num_result!=false){
			foreach ($num_result as $key => $value){
				$_num_result[$value['oprate']] = $value['num'];
			}
		}
		//分页返回结果
		$data['page'] = !IsExiest($data['page'])?1:$data['page'];
		$data['epage'] = !IsExiest($data['epage'])?10:$data['epage'];
		$total_page = ceil($total / $data['epage']);
		$_limit = " limit ".($data["epage"] * ($data["page"] - 1)).", {$data['epage']}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL','ORDER', 'LIMIT'), array($_select,$_sql,$_order, $_limit), $sql));
		
		//add 20120831 wlz
		foreach($list as $key => $value){
			if($value['account_use'] < 0){
				$list[$key]['account_use'] = number_format(0,2);
			}
		}
		//返回最终的结果
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page,"oprate_add"=>$_num_result['add'],"oprate_reduce"=>$_num_result['reduce']+$_num_result['frost']+$_num_result['return']);
		return $result;
	}
	
	
	 /**
	 * 添加用户的额度申请（borrow_amount_apply）
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function AddAmountApply($data = array()){
		global $mysql;
       //判断用户是否存在
        if (!IsExiest($data['user_id'])) {
            return "amount_user_id_empty";
        }
		$sql = "select 1 from `{borrow_amount}` where user_id='{$data['user_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false){
			$sql = "insert into `{borrow_amount}` set user_id='{$data['user_id']}'";
			$mysql->db_query($sql);
		}
		unset($data["pic_result"]);
		$data['nid'] = "apply".$user_id.time().rand(10,99);

		$sql = "insert into `{borrow_amount_apply}` set `addtime` = '".time()."',`addip` = '".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
        return $mysql->db_query($sql);
	}
	
	//获得用户的申请记录（borrow_amount_apply）
	//id id 
	//user_id 用户id 
	function GetAmountApplyOne($data){
		global $mysql;
		$_sql = " where 1=1 ";
		if (IsExiest($data['user_id'])!=false) {
			$_sql .= " and p1.user_id={$data['user_id']}  ";
		}
		if (IsExiest($data['id'])!=false) {
			$_sql .= " and p1.id={$data['id']} ";
		}
		if (IsExiest($data['amount_type'])!=false) {
			$_sql .= " and p1.amount_type='{$data['amount_type']}' ";
		}
		if (IsExiest($data['status'])!=false || $data['status']==0) {
			$_sql .= " and p1.status='{$data['status']}' ";
		}
		$sql = "select p1.*,p2.username from `{borrow_amount_apply}` as  p1 
		left join `{users}` as p2 on p1.user_id=p2.user_id $_sql ";
		$result = $mysql ->db_fetch_array($sql);
		return $result;
	}
	
	/**
	 * 列表
	 *
	 * @return Array
	 */
	function GetAmountList($data = array()){
		global $mysql;
		
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
		
		$_sql = "where 1=1 ";		 
		
		if (isset($data['status']) && $data['status']!=""){
			$_sql .= " and p1.status = {$data['status']}";
		}
		if (isset($data['user_id']) && $data['user_id']!=""){
			$_sql .= " and p1.user_id = {$data['user_id']}";
		}
		if (isset($data['username']) && $data['username']!=""){
			$_sql .= " and p2.username like '%{$data['username']}%' ";
		}
		if (isset($data['type']) && $data['type']!=""){
			$_sql .= " and p1.type like '%{$data['type']}%' ";
		}
		$_select = 'p1.*,p2.username';
		$sql = "select SELECT from {borrow_amount} as p1 
				left join {users} as p2 on p1.user_id=p2.user_id
				$_sql ORDER LIMIT";
				 
				 
		//是否显示全部的信息
		if (isset($data['limit']) ){
			$_limit = "";
			if ($data['limit'] != "all"){
				$_limit = "  limit ".$data['limit'];
			}
			return $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, $_order, $_limit), $sql));
		}	
		
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num', '', ''), $sql));
		
		$total = intval($row['num']);
		
		//分页返回结果
		$data['page'] = !IsExiest($data['page'])?1:$data['page'];
		$data['epage'] = !IsExiest($data['epage'])?10:$data['epage'];
		$total_page = ceil($total / $data['epage']);
		$_limit = " limit ".($data["epage"] * ($data["page"] - 1)).", {$data['epage']}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL','ORDER', 'LIMIT'), array($_select,$_sql,$_order, $_limit), $sql));
		if  ($list!=false){
			foreach ($list as $key => $value){
				$list[$key] = self::GetAmountUsers(array("user_id"=>$value['user_id'],"amount_result"=>$value));
				$list[$key]['username'] = $value['username'];
			}
		}
		
		//返回最终的结果
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		return $result;
		
	}
	
	
	
	function CheckAmountApply($data){
		global $mysql,$_G;
		
		 //判断ID是否存在
        if (!IsExiest($data['id'])) {
            return "amount_apply_id_empty";
        } 
		
		$result = self::GetAmountApplyOne(array("id"=>$data['id']));//获取额度的信息，看是否已经操作过
	
		if ($result['status']!=0){
			return "amount_apply_check_yes";
		}
		$amount_type = $result['amount_type'];
		$user_id = $result['user_id'];
		if ($data['status']==1){
			//添加额度记录
			$_data["user_id"] = $result['user_id'];
			$_data["amount_type"] = $result['amount_type'];
			$_data["type"] = "apply";
			$_data["oprate"] = $result['oprate'];
			$_data["nid"] = $result['nid'];
			$_data["account"] = $data['account'];
			$_data["remark"] = "申请额度审核通过";//type 操作的类型 
			self::AddAmountLog($_data);
			
			//收取成功提升额度1%的管理费.2012-9-17 by：Xiaowu
			if ($data['oprate']=="add"){
				require_once(ROOT_PATH."modules/account/account.class.php");
				$amount_fee=round($data['account']*$_G['system']['con_amount_fee']*0.01,2);
				$log_info["user_id"] = $result['user_id'];//操作用户id
				$log_info["nid"] = "borrow_amount_add_".$result['user_id']."_".$result['nid'];//订单号
				$log_info["money"] = $amount_fee;//操作金额
				$log_info["income"] = 0;//收入
				$log_info["expend"] = $amount_fee;//支出
				$log_info["balance_cash"] = -$amount_fee;//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
				$log_info["frost"] = 0;//冻结金额
				$log_info["await"] = 0;//待收金额
				$log_info["type"] = "borrow_amount_add";//类型
				$log_info["to_userid"] = 0;//付给谁
				$log_info["remark"] = "用户申请额度成功扣除管理费";
				accountClass::AddLog($log_info);
			}
			$remind['nid'] = "amount_verify_yes";
			$remind['code'] = "amount";
			$remind['article_id'] = $_data["user_id"];
			$remind['receive_userid'] = $_data["user_id"];
			$remind['title'] = "你申请的{$data["account"]}额度，审核成功";
			$remind['content'] = "你申请的{$data["account"]}额度，审核成功";
			remindClass::sendRemind($remind);
		}else{
			$remind['nid'] = "amount_verify_no";
			$remind['code'] = "amount";
			$remind['article_id'] = $user_id;
			$remind['receive_userid'] = $user_id;
			$remind['title'] = "你申请的{$data["account"]}额度，审核失败,【失败原因：{$data['verify_content']}】";
			$remind['content'] = "你申请的{$data["account"]}额度，审核失败,【失败原因：{$data['verify_content']}】";
			remindClass::sendRemind($remind);
			$data['account'] = 0;
		}
		
		//更新信息
		$sql = "update `{borrow_amount_apply}` set status={$data['status']},verify_time='".time()."',verify_user=".$_G['user_id'].",verify_remark='{$data['verify_remark']}',account='{$data['account']}' where id = {$data['id']}";
		$mysql ->db_query($sql);
		
		return $data['id'];
	
	}
	
	
	/**
	 * 列表
	 *
	 * @return Array
	 */
	function GetAmountApplyList($data = array()){
		global $mysql;
		$_sql = "where 1=1 ";		 
		
		if (IsExiest($data['status'])!=false) {
			$_sql .= " and p1.status = {$data['status']}";
		}
		if (IsExiest($data['user_id'])!=false) {
			$_sql .= " and p1.user_id = {$data['user_id']}";
		}
		if (IsExiest($data['username'])!=false) {
			$_sql .= " and p2.username like '%{$data['username']}%' ";
		}		
		$_order = " order by p1.id desc";
		$_select = 'p1.*,p2.username';
		$sql = "select SELECT from {borrow_amount_apply} as p1 
				left join {users} as p2 on p1.user_id=p2.user_id
			    $_sql ORDER LIMIT ";
				 
		//是否显示全部的信息
		if (isset($data['limit']) ){
			$_limit = "";
			if ($data['limit'] != "all"){
				$_limit = "  limit ".$data['limit'];
			}
			return $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, $_order, $_limit), $sql));
		}	
		
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num', '', ''), $sql));
		
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
	 * 1,添加额度
	 *
	 * @param array $data =array("name"=>"额度名称","nid"=>"标识名","default"=>"默认标识名","credit_nid"=>"积分类型",""=>"备注");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function AddAmountType($data = array()){
		global $mysql;
		 //判断名称是否存在
        if (!IsExiest($data['name'])) {
            return "amount_type_name_empty";
        } 
		
		//判断名称是否存在
        if (!IsExiest($data['nid'])) {
            return "amount_type_nid_empty";
        }
		
		//如果用户存在，判断用户是否存在
		if (IsExiest($data['username'])!=false){
			$sql = "select user_id from `{borrow_amount_type}` where nid ='{$data['nid']}'";
			$result = $mysql->db_fetch_array($sql);
			if ($result!=false) return "amount_type_nid_exiest";
		}
		
		
		$sql = "insert into `{borrow_amount_type}` set addtime='".time()."',addip='".ip_address()."',updatetime='".time()."',updateip='".ip_address()."',";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql));
    	return $mysql->db_insert_id();
	}
	
	/**
	 * 2,修改额度
	 *
	 * @param array $data =array("id"=>"id","name"=>"名称","status"=>"状态");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function UpdateAmountType($data = array()){
		global $mysql;
		
		 //判断名称是否存在
        if (!IsExiest($data['name'])) {
            return "amount_type_name_empty";
        } 
		
		//判断名称是否存在
        if (!IsExiest($data['nid'])) {
            return "amount_type_nid_empty";
        }
		
		//如果用户存在，判断用户是否存在
		if (IsExiest($data['username'])!=false){
			$sql = "select user_id from `{borrow_amount_type}` where nid ='{$data['nid']}' and id!='{$data['id']}'";
			$result = $mysql->db_fetch_array($sql);
			if ($result!=false) return "amount_type_nid_exiest";
		}
		
		$sql = "update `{borrow_amount_type}` set updatetime='".time()."',updateip='".ip_address()."',";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql)." where id='{$data['id']}' ");
		return $data['id'];
	}
	
	/**
	 * 3,删除额度
	 *
	 * @param Array $data = array("id"=>"ID")
	 * @return Boolen
	 */
	function DelAmountType($data = array()){
		global $mysql;
		
		if (!IsExiest($data['id'])) return "amount_type_id_empty";
		
		$sql = "delete from `{borrow_amount_type}`  where id='{$data['id']}'";
    	$mysql -> db_query($sql);
		
		return $data['id'];
	}
	
	
	
	/**
	 * 4,获得额度列表
	 *
	 * @return Array
	 */
	function GetAmountTypeList($data = array()){
		global $mysql;
		
		$_sql = " where 1=1 ";
		
		//搜索用户id
        if (IsExiest($data['user_id'])!=false) {
            $_sql .= " and p1.user_id ='{$data['user_id']}'";
        }
		
		//搜索用户名
		if (IsExiest($data['username'])!=false) {
            $_sql .= " and p2.username like '%{$data['username']}%'";
        }
		
		$_select = " p1.*,p2.name as credit_name ";
		$_order = " order by p1.id desc";
		$sql = "select SELECT from `{borrow_amount_type}` as p1 left join `{credit_class}` as p2 on p1.credit_nid=p2.nid SQL ORDER ";
		
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
	 * 6,获得额度的单条记录
	 *
	 * @param Array $data = array("id"=>"");
	 * @return Array
	 */
	 function GetAmountTypeOne($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "amount_type_id_empty";
		$sql = "select p1.* from `{borrow_amount_type}` as p1  where id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "amount_type_empty";
		return $result;
	}
	
	
	/**
	 * 7,获得用户的额度
	 *
	 * @param Array $data = array("user_id"=>"");
	 * @return Array
	 * 积分30分以下的额度统一为初始额度，大于30分的按一分加100额度累计
	 */
	 function GetAmountUsers($data = array()){
		global $mysql,$_G;
		if (!IsExiest($data['user_id'])) return "amount_user_id_empty";
		$borrow_first = isset($_G['system']['con_borrow_amount_first'])?$_G['system']['con_borrow_amount_first']:2000;
		
		if (isset($data['amount_result']) && $data['amount_result']!=""){
			$result = $data['amount_result'];
		}else{
			$sql = "select p1.* from `{borrow_amount}` as p1  where p1.user_id='{$data['user_id']}'";
			$result = $mysql->db_fetch_array($sql);
			if ($result==false){
				$sql = "insert into `{borrow_amount}` set user_id='{$data['user_id']}'";
				$mysql->db_query($sql);
				$sql = "select p1.* from `{borrow_amount}` as p1  where p1.user_id='{$data['user_id']}'";
				$result = $mysql->db_fetch_array($sql);
			}
		}
		if($_G['system']['con_borrow_credit']==1){
			$_result = borrowClass::GetBorrowCredit(array("user_id"=>$data['user_id']));
			$borrow_credit = ($_result['approve_credit']-50)*100;
			if($borrow_credit<0){
				$borrow_credit=0;
			}
		}else{
			$result["borrow"] = $borrow_first+$result['borrow'];
			$result["borrow_use"] = $borrow_first+$result['borrow_use'];
		}
		$result["borrow_nouse"] =$result["borrow"] -$result["borrow_use"];
		$result["vouch_borrow"] = $result['vouch_borrow'];
		$result["vouch_borrow_use"] = $result['vouch_borrow_use'];
		$result["vouch_borrow_nouse"] =$result["vouch_borrow"] -$result["vouch_borrow_use"];
		$result["once_amount"] = $result['once_amount'];
		$result["once_amount_use"] = $result['once_amount_use'];
		$result["once_amount_nouse"] =$result["once_amount"] -$result["once_amount_use"];
		$result["diya_borrow"] = $result['diya_borrow'];
		$result["diya_borrow_use"] = $result['diya_borrow_use'];
		$result["diya_borrow_nouse"] =$result["diya_borrow"] -$result["diya_borrow_use"];
		return $result;
	}
}
?>