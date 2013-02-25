<?
/******************************
 * $File: borrow.class.php
 * $Description: 借款类文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

require_once(ROOT_PATH."modules/account/account.class.php");
require_once(ROOT_PATH."modules/credit/credit.class.php");
require_once(ROOT_PATH."modules/remind/remind.class.php");
require_once(ROOT_PATH."modules/borrow/borrow.model.php");
require_once(ROOT_PATH."modules/borrow/borrow.amount.php");
require_once(ROOT_PATH."modules/borrow/borrow.calculate.php");
require_once(ROOT_PATH."modules/users/users.class.php");


class borrowClass extends amountClass {

	/**
	 * 0,用户添加基本的借款信息
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function Add($data = array()){
		global $mysql,$_G;
		
		//判断用户是否存在
        if (!IsExiest($data['user_id'])) {
            return "borrow_user_id_empty";
        } 
		//判断标题是否存在
        if (!IsExiest($data['name'])) {
            return "borrow_name_empty";
        }
		//判断金额是否存在
        if (!IsExiest($data['account'])) {
            return "borrow_account_empty";
        } 
		
		//判断是否多余可用借款额度
		$result = self::GetAmountUsers(array("user_id"=>$data["user_id"]));
		$lixi = $data['account']*$data['borrow_apr']*0.01/12;
		$_balance =  accountClass::GetAccountUsers(array("user_id"=>$data['user_id']));//获取当前用户余额
		if($_balance==''){$_balance['balance']=0;}
		if ($data['account']>$result['borrow_use'] && $data['borrow_type']==1){			
			return "borrow_account_over_credituse";
		}elseif($data['account']>$result['diya_borrow_use'] && $data['borrow_type']==2){
			return "borrow_account_over_diya";
		}elseif( $data['borrow_type']==4 && $lixi > $_balance['balance']){
			return "borrow_account_no";
		}	
		//判断是否有未审核的标
		$sql = "select count(1) as num from `{borrow}` where user_id={$data['user_id']} and status=0";
		$result = $mysql->db_fetch_array($sql);
		if ($result["num"]>0){
			//return "borrow_is_exist";
		}
		
		//判断是否大于最大的投标额
		$max = isset($_G['system']['con_borrow_maxaccount'])?$_G['system']['con_borrow_maxaccount']:"2000000";//最大的投资额度
		if($data['account'] > $max){
			return  "borrow_account_over_max";
		}
		
		$borrow_url = "<a href=http://www.hcdai.com/invest/a{$data['borrow_nid']}.html target=_blank>{$data['name']}</a>";
		
		//判断是否低于最小的投资额
		$min = isset($_G['system']['con_borrow_minaccount'])?$_G['system']['con_borrow_minaccount']:"100";//最小的投资额度
		if($data['account'] < $min){
			return  "borrow_account_over_min";
		}
		
		//判断利率是否存在
		 if (!IsExiest($data['borrow_apr'])) {
			return "borrow_apr_empty";
		}
		if ($data['borrow_type']==1 || $data['borrow_type']==4){		
			if($data['borrow_apr']<$_G['system']['con_borrow_apr_min'] || $data['borrow_apr']>$_G['system']['con_borrow_apr_max']){
				$msg = array("利率不在范围内");
			}
		}elseif($data['borrow_type']==2){
			if($data['borrow_apr']<$_G['system']['con_diya_apr_min'] || $data['borrow_apr']>$_G['system']['con_diya_apr_max']){
				$msg = array("抵押利率不在范围内");
			}
		}
				
		if ($data['borrow_type'] == 4) {
            $account_sql = "select * from `{account}` where user_id={$data['user_id']}";
            $account_result = $mysql->db_fetch_array($account_sql);
            $_equal["account"] = $data["account"];
            $_equal["period"] = $data["borrow_period"];
            $_equal["apr"] = $data["borrow_apr"];
            $_equal["style"] = $data["borrow_style"];
            $equal_result = EqualInterest($_equal);
            $manage_fee = $data["account"] * 0.001;
            $money = $equal_result[0]['account_interest'] + $manage_fee;
            if ($account_result['balance'] < $money) {
                return "borrow_miao_account_no";
            }
            $log_info["user_id"] = $data['user_id']; //操作用户id
            $log_info["nid"] = "borrow_miao_success_" . $data['borrow_nid']; //订单号
            $log_info["money"] = $lixi; //操作金额
            $log_info["income"] = 0; //收入
            $log_info["expend"] = 0; //支出
            $log_info["balance_cash"] = -$lixi; //可提现金额
            $log_info["balance_frost"] = 0; //不可提现金额
            $log_info["frost"] = $lixi; //冻结金额
            $log_info["await"] = 0; //待收金额
            $log_info["repay"] = 0; //待收金额
            $log_info["type"] = "borrow_miao_success"; //类型
            $log_info["to_userid"] = 0; //付给谁
            $log_info["remark"] = "发起秒标冻结{$lixi}元";
            accountClass::AddLog($log_info);

            $data['verify_userid'] = 0;
            $data['verify_time'] = time();
        }
		
		$sql = "insert into `{borrow}` set `addtime` = '".time()."',`addip` = '".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
        $mysql->db_query($sql);
		if( $data['borrow_type']==1 || $data['borrow_type']==4){		
			$_data["user_id"] = $data['user_id'];
			$_data["amount_type"] = "borrow";
			$_data["type"] = "borrow_success";
			$_data["oprate"] = "frost";
			$_data["nid"] = "borrow_success_credit_".$data['user_id']."_".$data['borrow_nid'];
			$_data["account"] = $data['account'];
			$_data["remark"] = "发布借款标[{$borrow_url}]，冻结{$data['account']}借款信用额度";
			borrowClass::AddAmountLog($_data);			
		}else{
			$_data["user_id"] = $data['user_id'];
			$_data["amount_type"] = "diya_borrow";
			$_data["type"] = "borrow_success";
			$_data["oprate"] = "frost";
			$_data["nid"] = "borrow_success_vouch_".$borrow_userid."_".$data['borrow_nid']."_".$vouch_id;
			$_data["account"] = $data['account'];
			$_data["remark"] = "抵押借款[{$borrow_url}]审核通过扣去抵押额度";//type 操作的类型 
			borrowClass::AddAmountLog($_data);
		}
		
		
		return $data['user_id'];	
		
	}

/**
	 * 0,用户添加基本的借款信息
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function Update($data = array()){
		global $mysql,$_G;
		
		$sql = "update `{borrow}` set ";
		foreach($data as $key => $value){
			$_sql[] .= "`$key` = '$value'";
		}
        $mysql->db_query($sql.(join(",",$_sql))." where borrow_nid='{$data['borrow_nid']}'");
		return $data['borrow_nid'];	
		
	}	
	/**
	 * 0.1,用户添加基本的借款信息
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function AddBorrowTiyan($data = array()){
		global $mysql,$_G;
		
		//判断用户是否存在
        if (!IsExiest($data['user_id'])) {
            return "borrow_user_id_empty";
        } 
		//判断标题是否存在
        if (!IsExiest($data['name'])) {
            return "borrow_name_empty";
        }
		
		$data['account'] = 100;
		$data['borrow_period'] = 1;
		$data['borrow_valid_time'] = 1;
		$data['tiyan_status'] = 1;
		
		//判断是否有借款标
		$sql = "select count(1) as num from `{borrow}` where user_id={$data['user_id']} ";
		$result = $mysql->db_fetch_array($sql);
		if ($result["num"]>0){
			return "borrow_tiyan_not_public";
		}
		
		$sql = "insert into `{borrow}` set `addtime` = '".time()."',`addip` = '".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
         $mysql->db_query($sql);
		return $mysql->db_insert_id();	
	}
	
	/**
	 * 0.2,用户借款信息
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function AddBorrowVouch($data = array()){
		global $mysql,$_G;
		
		//判断用户是否存在
        if (!IsExiest($data['user_id'])) {
            return "borrow_user_id_empty";
        } 
		//判断标题是否存在
        if (!IsExiest($data['name'])) {
            return "borrow_name_empty";
        }
		//判断金额是否存在
        if (!IsExiest($data['account'])) {
            return "borrow_account_empty";
        } 
		$data['vouch_status']  = 1;
		//判断是否多余可用借款额度
		$result = self::GetAmountUsers(array("user_id"=>$data["user_id"]));
		if ($data['account']>$result['vouch_borrow_use']){
			return "borrow_account_over_vouchuse";
		}
		
		//判断是否有未审核的标
		$sql = "select count(1) as num from `{borrow}` where user_id={$data['user_id']} and status=0";
		$result = $mysql->db_fetch_array($sql);
		if ($result["num"]>0){
			return "borrow_is_exist";
		}
		
		//判断是否大于最大的投标额
		$max = isset($_G['system']['con_borrow_maxaccount'])?$_G['system']['con_borrow_maxaccount']:"50000";//最大的投资额度
		if($data['account'] > $max){
			return  "borrow_account_over_max";
		}
		
		//判断是否低于最小的投资额
		$min = isset($_G['system']['con_borrow_minaccount'])?$_G['system']['con_borrow_minaccount']:"500";//最小的投资额度
		if($data['account'] < $min){
			return  "borrow_account_over_min";
		}
		
		//判断利率是否存在
		 if (!IsExiest($data['borrow_apr'])) {
			return "borrow_apr_empty";
		}
		$apr = isset($_G['system']['con_borrow_apr_highest'])?$_G['system']['con_borrow_apr_highest']:"22.18";
		if ($data['borrow_apr']>$apr){
			return "borrow_apr_over_max";
		}
		
		$sql = "insert into `{borrow}` set `addtime` = '".time()."',`addip` = '".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
        $mysql->db_query($sql);
		$id = $mysql->db_insert_id();
		return $id;
	}
	
	/**
	 * 1,列表
	 * $data = array("user_id"=>"用户id","username"=>"用户名","borrow_name"=>"借款名称","borrow_nid"=>"标识名","query_type"=>"借款类型","dotime1"=>"发布时间1","dotime2"=>"发布时间2");
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
		
		//搜索借款名称
		if (IsExiest($data['borrow_name']) != false){
			$data['borrow_name']=urldecode($data['borrow_name']);
			$_sql .= " and p1.`name` like '%{$data['borrow_name']}%'";
		}
		//搜索借款ID
		if (IsExiest($data['borrow_nid']) != false){
			$_sql .= " and p1.`borrow_nid` = '{$data['borrow_nid']}'";
		}
		
		//搜索利率
		if (IsExiest($data['borrow_interestrate']) != false){
			if($data['borrow_interestrate']==1){
				$_sql .= " and p1.`borrow_apr` > 0 and p1.`borrow_apr` <= 5";
			}
			if($data['borrow_interestrate']==2){
				$_sql .= " and p1.`borrow_apr` > 5 and p1.`borrow_apr` <= 10";
			}
			if($data['borrow_interestrate']==3){
				$_sql .= " and p1.`borrow_apr` > 10 and p1.`borrow_apr` <= 15";
			}
			if($data['borrow_interestrate']==4){
				$_sql .= " and p1.`borrow_apr` > 15 and p1.`borrow_apr` <= 20";
			}
	
		}
		
		
		//搜索借款类型
		if (IsExiest($data['borrow_type']) != false){
			$_sql .= " and p1.borrow_type in ({$data['borrow_type']})";
		}
		
		/* if (IsExiest($data['borrow_type']) != false){
			if ($data['borrow_type']=="credit"){
				$_sql .= " and p1.`vouchstatus`!=1 and `fast_status`!=1";
			}elseif($data['borrow_type']=="vouch"){
				$_sql .= " and p1.`vouchstatus`=1";
			}elseif($data['borrow_type']=="fast"){
				$_sql .= " and p1.`fast_status`=1";
			}
		} */
		
		//判断类型
		if (IsExiest($data['query_type'])!=false){
			$type = $data['query_type'];
			//等待审核
			if ($type=="wait"){
				$_sql .= " and p1.status=0";
			}
			//成功审核
			elseif ($type=="success"){
				$_sql .= " and p1.status=1";
			}
			elseif ($type=="invest"){
				$_sql .= " and p1.status=1 and p1.verify_time >".time()." - p1.borrow_valid_time*60*60*24 and p1.account>p1.borrow_account_yes";
			}
			elseif ($type=="vouch"){
				$_sql .= " and p1.vouchstatus=1 and p1.verify_time >".time()." - p1.borrow_valid_time*60*60*24 and p1.status=1";
			}
			//初审失败
			elseif ($type=="false"){
				$_sql .= " and p1.status=2";
			}
			//满标待审核
			elseif ($type=="full_check"){
				$_sql .= " and p1.status=1 and p1.account=p1.borrow_account_yes ";
			}
			
			//满标审核成功
			elseif ($type=="full_success"){
				$_sql .= " and p1.status=3";
			}
			
			elseif ($type=="repay_yes"){
				$_sql .= " and p1.status=3 and p1.repay_account_wait='0.00'";
			}
			
			elseif ($type=="repay_no"){
				$_sql .= " and p1.status=3 and p1.repay_account_wait!='0.00'";
			}
			//满标审核失败
			elseif ($type=="full_false"){
				$_sql .= " and p1.status=4";
			}
			//用户撤标
			elseif ($type=="cancel"){
				$_sql .= " and p1.cancel_status!=0 ";
			}			
			//初审
			elseif ($type=="first"){
				if (IsExiest($data['status'])==""){
					$_sql .= " and p1.status = 0 ";
				}elseif($data['status']==1){
					$_sql .= " and p1.status=1 and p1.borrow_account_yes!=p1.account and p1.borrow_valid_time*60*60*24 + p1.verify_time >=".time();
				}elseif($data['status']==5){
					$_sql .= " and p1.status = 5 ";
				}elseif($data['status']==6){
					$data['status'] = 1;
					$_sql .= " and  p1.borrow_valid_time*60*60*24 + p1.verify_time <".time();
				}else{
					$_sql .= " and p1.status in (0,1,2) ";
				}
			}
			//复审
			elseif ($type=="full"){
				if ($data['type']=="repay"){
				//用户撤标
					$_sql .= " and p1.status = 3 and repay_account_wait>0";
				}elseif ($data['type']=="full_error"){
					$_sql .= " and p1.status in (3,4) and p1.borrow_full_status=0 ";
				}elseif ($data['type']=="repayyes"){
					$_sql .= " and p1.status = 3 and repay_account_wait=0";
				}elseif (IsExiest($data['status'])==""){
					$_sql .= " and p1.status = 1 and  p1.borrow_account_yes=p1.account ";
				}elseif (IsExiest($data['status'])!=""){
					$_sql .= " and p1.status = {$data['status']} ";
				}
			
			}
		}
		
		//判断添加时间开始
		if (IsExiest($data['dotime1']) != false){
			$dotime1 = ($data['dotime1']=="request")?$_REQUEST['dotime1']:$data['dotime1'];
			if ($dotime1!=""){
				$_sql .= " and p1.addtime > ".get_mktime($dotime1);
			}
		}
		
		//判断添加时间结束
		if (IsExiest($data['dotime2'])!=false){
			$dotime2 = ($data['dotime2']=="request")?$_REQUEST['dotime2']:$data['dotime2'];
			if ($dotime2!=""){
				$_sql .= " and p1.addtime < ".get_mktime($dotime2);
			}
		}
		//判断添加时间结束
		if (IsExiest($data['keywords'])!=false){
			$data['keywords']=urldecode($data['keywords']);
			$_sql .= " and p1.name like '%{$data['keywords']}%'";
		}
		
		//判断借款状态
		if (IsExiest($data['status'])!=""){
			if ($data['status']==-1){
				$_sql .= " and p1.status = 1 and p1.borrow_valid_time*60*60*24 + p1.verify_time <".time();
			}else{
				$_sql .= " and p1.status in ({$data['status']})";
			}
		}
		
		//判断是否逾期
		if (IsExiest($data['late_display'])==1 ){
			$_sql .= " and ((p1.status=1 and p1.verify_time >".time()." - p1.borrow_valid_time*60*60*24 ) or (p1.status=3 and p1.repay_account_wait>0))";
		}
		
		//判断是否担保借款
		if (IsExiest($data['vouch_status'])!=""){
			$_sql .= " and p1.vouch_status in ({$data['vouch_status']})";
		}
		
		
		//判断是体验标
		if (IsExiest($data['tiyan_status'])!=""){
			$_sql .= " and p1.tiyan_status in ({$data['tiyan_status']})";
		}
		
		//借款期数
		if (IsExiest($data['borrow_period'])!=""){
			$_sql .= " and p1.borrow_period = {$data['borrow_period']}";
		}
		
		//借款类别
		if (IsExiest($data['flag'])!=""){
			$_sql .= " and p1.flag = {$data['flag']}";
		}
		
		//圈子借款
		if (IsExiest($data['group_id'])!=""){
			if($data['group_id']!="all"){ 
				$_sql .= " and p1.group_status=1 and p1.group_id = {$data['group_id']}";
			}else{ 
				$_sql .= " and p1.group_status=1 and p1.group_id in (select group_id from `{group_member}` where user_id='{$data['my_userid']}')";
			}
		}
		
		//借款用途
		if (IsExiest($data['borrow_use']) !=""){
			$_sql .= " and p1.borrow_use in ('{$data['borrow_use']}')";
		}
		
		//借款用户类型
		if (IsExiest($data['borrow_usertype']) !=""){
			$_sql .= " and p1.borrow_usertype = '{$data['borrow_usertype']}'";
		}
		
		//是否奖励
		if (IsExiest($data['award_status'])!=""){
			if($data['award_status']==1){
			$_sql .= " and p1.award_status >0";
			}else{
			$_sql .= " and p1.award_status = 0";
			}
		}
		
		//借款
		if (IsExiest($data['borrow_style']) ){
			$_sql .= " and p1.borrow_style in ({$data['borrow_style']})";
		}
		
		if (IsExiest($data['account_status']!="")){
			if ($data['account_status']==1){
				$_sql .= " and p1.account >= 2000 and p1.account <= 5000";
			}elseif($data['account_status']==2){
				$_sql .= " and p1.account >= 5000 and p1.account <= 10000";
			}elseif($data['account_status']==3){
				$_sql .= " and p1.account >= 10000 and p1.account <= 30000";
			}elseif($data['account_status']==4){
				$_sql .= " and p1.account >= 30000 and p1.account <= 50000";
			}elseif($data['account_status']==5){
				$_sql .= " and p1.account >= 50000";
			}
		}
		
		if (IsExiest($data['period_area']!="")){
			if ($data['period_area']==1){
				$_sql .= " and p1.borrow_period >= 1 and p1.borrow_period <= 6";
			}elseif($data['period_area']==2){
				$_sql .= " and p1.borrow_period >= 6 and p1.borrow_period <= 12";
			}elseif($data['period_area']==3){
				$_sql .= " and p1.borrow_period >= 12 and p1.borrow_period <= 18";
			}elseif($data['period_area']==4){
				$_sql .= " and p1.borrow_period >= 18 and p1.borrow_period <= 24";
			}
		}
		//排序
		$_order = " order by p1.`order` desc,p1.addtime desc ";
		
		if (IsExiest($data['status'])!="" && $data['status']==1){
			$_order = " order by p1.`order` desc,p1.addtime desc ";
		}
		if (IsExiest($data['publish'])!="" ){
			$_order = " order by p1.`order` desc,p1.addtime desc ";
		}
		if (IsExiest($data['order'])!=""){
			$order = $data['order'];
			$type = $data['query_type'];
			if ($order == "account_up"){
				$_order = " order by p1.`account` desc ";
			}else if ($order == "account_down"){
				$_order = " order by p1.`account` asc";
			}
			if ($order == "credit_up"){
				$_order = " order by p3.`credit` desc,p1.id desc ";
			}else if ($order == "credit_down"){
				$_order = " order by p3.`credit` asc,p1.id desc ";
			}
			if ($order == "apr_up"){
				$_order = " order by p1.`borrow_apr` desc,p1.id desc ";
			}else if ($order == "apr_down"){
				$_order = " order by p1.`borrow_apr` asc,p1.id desc ";
			}
			if ($order == "jindu_up"){
				$_order = " order by p1.`borrow_account_scale` desc,p1.id desc ";
				
			}else if ($order == "jindu_down"){
				$_order = " order by p1.`borrow_account_scale` asc,p1.id desc ";
			}
			if ($order == "qixian_up"){
				$_order = " order by p1.`borrow_period` desc,p1.id desc ";
				
			}else if ($order == "qixian_down"){
				$_order = " order by p1.`borrow_period` asc,p1.id desc ";
			}
			if ($order == "flag"){
				$_order = " order by p1.vouch_status desc,p1.`flag` desc,p1.id desc ";
			}
			if ($order == "index"){
				$_order = " order by p1.`flag` desc,p1.id desc ";
			}	
			if ($order == "all"){
				$_order = " order by p1.`status` asc";
			}			
		}
		$flag_sql = "select p1.*,p2.fileurl from `{borrow_flag}` as p1 left join `{users_upfiles}` as p2 on p1.upfiles_id=p2.id ";
		$flag_result = $mysql->db_fetch_arrays($flag_sql);
		if (is_array($flag_result)){
			foreach ($flag_result as $key => $value){
				$_flag_result[$value['id']] = $value;
			}
		}
		
		$_select = " p1.*,p2.username,p3.credits";
		$sql = "select SELECT from `{borrow}` as p1 
				 left join {users} as p2 on p1.user_id=p2.user_id
				 left join {credit} as p3 on p1.user_id=p3.user_id
				 SQL ORDER LIMIT
				";
		
		//是否显示全部的信息
		if (IsExiest($data['limit'])!=false){
			if ($data['limit'] != "all"){ $_limit = "  limit ".$data['limit']; }
			$result=$mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array($_select, $_sql, $_order, $_limit), $sql));
			foreach($result as $key => $value){
				$result[$key]["credit"] = self::GetBorrowCredit(array("user_id"=>$value['user_id']));
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
		$result['borrow_end_status'] = 0;
		if ($result['status']==1 && $result['borrow_end_time']<time()){
			$result['borrow_end_status'] = 1;
		}
		foreach ($list as $key => $value){
			$borrow_end_status = 0;
			if ($value['status']==1 && $value['borrow_end_time']<time()){
				$borrow_end_status = 1;
			}
			if ($value['flag']!=""){
				$_flag = explode(",",$value['flag']);
				foreach ($_flag as $_k => $_v){
					$list[$key]["_flag"][] = $_flag_result[$_v];
					$flag_name[] = $_flag_result[$_v]['name'];
				}
				$list[$key]["flag_name"] = join(",",$flag_name);
			}
			if ($value['status']==0){
				if ($borrow_end_status==1){
					$borrow_type_nid = "valid_yes";
				}else{
					$borrow_type_nid = "check_wait";
				}
			}elseif ($value['status']==2){
				$borrow_type_nid = "verify_false";
			}elseif ($value['status']==3){
				if ($value['repay_account_wait']==0){
					$borrow_type_nid = "repay_yes";
				}else{
					$borrow_type_nid = "repay_now";
				}
			}elseif ($value['status']==4){
				$borrow_type_nid = "reverify_false";
			}elseif ($value['status']==5){
				$borrow_type_nid = "cancel";
			}elseif ($value['status']==1){
				if ($value['vouch_status']==1 && $value['vouch_account_wait']!=0){
					$borrow_type_nid = "vouch_now";
				}else{
					if ($value['borrow_account_wait']==0){
						$borrow_type_nid = "reverify";
					}else{
						$borrow_type_nid = "tender_now";
					}
				}
			}
			$list[$key]["borrow_type_nid"] = $borrow_type_nid;
			$list[$key]["borrow_end_status"] = $borrow_end_status;
			$list[$key]["borrow_valid_end_time"] = $value["borrow_valid_time"]*60*60*24+$value['verify_time'];
			$list[$key]["credit"] = self::GetBorrowCredit(array("user_id"=>$value['user_id']));
			
			if($value['borrow_type'] == 4){
				$list[$key]["borrow_period"] = $value['borrow_period'];
			}
			$latesql="select sum(late_interest) as all_interest from `{borrow_repay}` where borrow_nid={$value['borrow_nid']}";
			$late=$mysql->db_fetch_array($latesql);
			$list[$key]['late_interest'] = $late['all_interest'];
		}
		
		//$sql="select SELECT from `{borrow}` as p1 left join {users} as p2 on p1.user_id=p2.user_id left join {credit} as p3 on p1.user_id=p3.user_id $_sql";
		//返回最终的结果
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		
		return $result;
	}
	
	/**
	 * 2,查看借款标
	 *
	 * @param Array $data
	 * @return Array
	 */
	public static function GetOne($data = array()){
		global $mysql;
		$_sql = "where 1=1 ";
		if (IsExiest($data['user_id'])!=""){
			$_sql .= " and  p1.user_id = '{$data['user_id']}' ";
		}
		if (IsExiest($data['id'])!=""){
			$_sql .= " and  p1.id = '{$data['id']}' ";
		}
		if (IsExiest($data['borrow_nid'])!=""){
			$_sql .= " and  p1.borrow_nid = '{$data['borrow_nid']}' ";
		}
		$sql = "select p1.* ,p2.username,p3.username as verify_username from `{borrow}` as p1 
				  left join `{users}` as p2 on p1.user_id=p2.user_id 
				  left join `{users}` as p3 on p1.verify_userid = p3.user_id 
				  $_sql
				";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "borrow_not_exiest";
		return $result;
	}
	
	
	
	/**
	 * 2.1,查看借款的详情，用到detail页面上
	 *
	 * @param Array $data
	 * @return Array
	 */
	public static function GetDetail($data = array()){
		global $mysql;
		$_sql = "where 1=1 ";
		if (IsExiest($data['user_id'])!=""){
			$_sql .= " and  p1.user_id = '{$data['user_id']}' ";
		}
		if (IsExiest($data['id'])!=""){
			$_sql .= " and  p1.id = '{$data['id']}' ";
		}
		if (IsExiest($data['borrow_nid'])!=""){
			$_sql .= " and  p1.borrow_nid = '{$data['borrow_nid']}' ";
		}
		if (IsExiest($data['hits'])!=""){
			$hsql="update `{borrow}` set hits=hits+1 where borrow_nid={$data['borrow_nid']}";
			$mysql->db_query($hsql);
		}
		$_result = array();
		//获取借款信息
		$sql = "select p1.* ,p2.* from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id  $_sql ";
		$result = $mysql->db_fetch_array($sql);
		$result['borrow_end_status'] = 0;
		
		if ($result['borrow_end_time']!="" && $result['borrow_end_time']<time()){
			$result['borrow_end_status'] = 1;
		}
		$result['borrow_other_time'] = $result['borrow_end_time']-time();
		//添加还款信息开始
		
		//计算每月还款金额
		if ($result['borrow_type']!=4){
			$_equal["account"] = $result["account"];
			$_equal["period"] = $result["borrow_period"];
			$_equal["apr"] = $result["borrow_apr"];
			$_equal["style"] = $result["borrow_style"];
			$_equal["type"] = "all";
			$equal_result = EqualInterest($_equal);
			$result["borrow_repay_month_account"] = $equal_result['repay_month'];
			$_equal["account"] = "100";
			$equal_result = EqualInterest($_equal);
			$result["borrow_100_interest"] = $equal_result['interest_total'];
		}else{
			$result["borrow_repay_month_account"] = round($result['account']*$result['borrow_apr']/365/100*$result['borrow_day'],2);
		}
		//check_wait = 审核中
		//verify_false = 审核失败
		//repay_now = 还款中
		//repay_yes = 已还完
		//reverify_false = 复审失败
		//cancel = 流标
		//vouch_now = 马上担保
		//valid_yes = 已到期
		//reverify = 复审中
		//tender_now = 马上投标
		if ($result['status']==0){
			if ($result['borrow_end_status']==1){
				$borrow_type_nid = "valid_yes";
			}else{
				$borrow_type_nid = "check_wait";
			}
		}elseif ($result['status']==2){
			$borrow_type_nid = "verify_false";
		}elseif ($result['status']==3){
			if ($result['repay_account_wait']==0.00){
				$borrow_type_nid = "repay_yes";
			}else{
				$borrow_type_nid = "repay_now";
			}
		}elseif ($result['status']==4){
			$borrow_type_nid = "reverify_false";
		}elseif ($result['status']==5){
			$borrow_type_nid = "cancel";
		}elseif ($result['status']==1){
			if ($result['vouch_status']==1 && $result['vouch_account_wait']!=0){
				$borrow_type_nid = "vouch_now";
			}else{
				if ($result['borrow_account_wait']==0){
					$borrow_type_nid = "reverify";
				}else{
					$borrow_type_nid = "tender_now";
				}
			}
		}
		$result['borrow_type_nid'] = $borrow_type_nid;
		$user_id = $result['user_id'];
		$_result['borrow'] = $result;
		
		//获取用户基本信息
		$sql = "select * from `{users_info}` where user_id='{$user_id}'";
		$_result['user_info'] = $mysql->db_fetch_array($sql);
		
		
		//获取用户基本资料
		$sql = "select * from `{rating_info}` where user_id='{$user_id}'";
		$_result['rating_info'] = $mysql->db_fetch_array($sql);
		
		//获取借款统计
		$_result['borrow_count'] = self::GetBorrowCount(array("user_id"=>$user_id));
		
		//用户积分
		$_user_id = array("user_id"=>$user_id);
		$_result['borrow_credit'] = self::GetBorrowCredit($_user_id);

		return $_result;
	}
	
	
	/**
	 * 3，借款初审
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function Verify($data = array()){
		global $mysql,$MsgInfo,$_G;
		$sql = "select borrow_type,borrow_nid,status,`name`,borrow_valid_time,user_id,account from `{borrow}` where borrow_nid='{$data['borrow_nid']}'";
		$result = $mysql->db_fetch_array($sql);

		//判断借款标是否存在
		if ($result==false){
			return "borrow_not_exiest";
		}else{
			$borrow_url = "<a href=http://www.hcdai.com/invest/a{$result['borrow_nid']}.html target=_blank>{$result['name']}</a>";
		}	
		
		//判断借款是否已经通过初审，也只有状态0才可以进行初审。
		if($result['status']!=0){
			return "borrow_verify_error";
		}		
		
		//更新统计信息
		self::UpdateBorrowCount(array("user_id"=>$result['user_id'],"borrow_times"=>1));
		
		
		if($data['status']==1){
			$status=1;
		}else{
			$status = 2;
		}
		
		$borrow_end_time = $result['borrow_valid_time']*60*60*24+time();
		
		//修改相应的信息
		$sql = "update `{borrow}` set verify_time='".time()."',verify_userid='{$_G['user_id']}',verify_remark='{$data['verify_remark']}',borrow_end_time='{$borrow_end_time}',status={$status},borrow_status='{$data['status']}' where  borrow_nid='{$data['borrow_nid']}' ";
		$mysql->db_query($sql);
		
		
		//如果审核通过，则加入用户操作记录
		if ($data['status']==1){
			$user_log["user_id"] = $_G['user_id'];
			$user_log["code"] = "borrow";
			$user_log["type"] = "borrow";
			$user_log["operating"] = "verify";
			$user_log["article_id"] = $data['borrow_nid'];
			$user_log["result"] = $result>0?1:0;
			$user_log["content"] =  str_replace(array('#borrow_url#'), array($borrow_url), $MsgInfo["borrow_verify_user_msg"]);
			usersClass::AddUsersLog($user_log);	
			
			$remind['nid'] = "borrow_yes";
			$remind['receive_userid'] = $result['user_id'];
			$remind['article_id'] = $result['user_id'];
			$remind['code'] = "borrow";
			$remind['title'] = "您的借款标({$result['name']})初审成功";
			$remind['content'] = "您的借款标[{$borrow_url}]在".date("Y-m-d",time())."已经初审成功";
			remindClass::sendRemind($remind);
		
		}else{
			if( $result['borrow_type']==1 || $result['borrow_type']==4){
					$_data["user_id"] = $result['user_id'];
					$_data["amount_type"] = "borrow";
					$_data["type"] = "borrow_false";
					$_data["oprate"] = "return";
					$_data["nid"] = "borrow_false_".$result['id']."_".$result['borrow_nid'];
					$_data["account"] = $result['account'];
					$_data["remark"] = "借款[{$borrow_url}]审核不通过借款信用额度返回";//type 操作的类型 
					borrowClass::AddAmountLog($_data);
			}else{
				$_data["user_id"] = $result['user_id'];
				$_data["amount_type"] = "diya_borrow";
				$_data["type"] = "borrow_false";
				$_data["oprate"] = "return";
				$_data["nid"] = "borrow_false_vouch_"."_".$result['borrow_nid']."_".$result['id'];
				$_data["account"] = $result['account'];
				$_data["remark"] = "抵押借款[{$borrow_url}]初审不通过增加抵押额度";//type 操作的类型 
				borrowClass::AddAmountLog($_data);
			}
			
			$remind['nid'] = "borrow_no";
			$remind['receive_userid'] = $result['user_id'];
			$remind['article_id'] = $result['user_id'];
			$remind['code'] = "borrow";
			$remind['title'] = "您的借款标({$result['name']})初审失败";
			$remind['content'] = "您的借款标[{$borrow_url}]在".date("Y-m-d",time())."初审失败。失败原因：";
			remindClass::sendRemind($remind);
		}
		
		
		
		
		
		//自动投标加入
		$res =autoClass::AutoTender(array("borrow_nid"=>$result['borrow_nid']));
		if ($res != false){
			foreach ($res as  $key => $value){
				$_tender['borrow_nid'] = $result['borrow_nid'];
				$_tender['user_id'] = $key;
				$_tender['account'] = $value;
				$_tender['contents'] = "自动投标";
				$_tender['status'] = 0;
				$_tender['auto_status'] = 1;
				$_tender['nid'] = "tender_".$key.time().rand(10,99);//订单号
				$_result = self::AddTender($_tender);
				$sql = "insert into `{borrow_autolog}` set borrow_nid='{$result['borrow_nid']}',user_id='{$key}',account='{$value}',remark='{$_result}',addtime='".time()."',addip='".ip_address()."'";
				$mysql->db_query($sql);
				$user_log["user_id"] = $_tender['user_id'];
				$user_log["code"] = "tender";
				$user_log["type"] = "tender";
				$user_log["operating"] = "auto_tender";
				$user_log["article_id"] = $_tender['user_id'];
				$user_log["result"] = 1;
				$user_log["content"] = date("Y-m-d H:i:s")."自动投标[{$borrow_url}]成功,金额为{$_tender['account']}";
				usersClass::AddUsersLog($user_log);	
			}
		}
        return $data['borrow_nid'];
	}
	
	/**
	 * 5，投资列表
	 *
	 * @return Array
	 */
	function GetTenderList($data = array()){
		global $mysql;
		
		$_sql = "where 1=1 ";	
		
		//判断用户id
		if (IsExiest($data['user_id']) != false){
			$_sql .= " and p1.user_id = {$data['user_id']}";
		}
		
		//判断借款用户
		if (IsExiest($data['borrow_userid']) != false){
			$_sql .= " and p3.user_id = {$data['borrow_userid']}";
		}
		
		//搜到用户名
		if (IsExiest($data['username']) != false){
			$_sql .= " and p2.username like '%{$data['username']}%'";
		}
		
		//搜索借款名称
		if (IsExiest($data['borrow_status']) != false){
			$_sql .= " and p3.`status` in ({$data['borrow_status']})";
		}
		
		if ($data['change_status']!=""){
			$_sql .= " and p1.`change_status` in  ({$data['change_status']})";
		}
		//搜索借款名称
		if (IsExiest($data['borrow_name']) != false){
			$_sql .= " and p3.`name` like '%".urldecode($data['borrow_name'])."%'";
		}
		//搜索借款名称
		if (IsExiest($data['borrow_nid']) != false){
			$_sql .= " and p3.`borrow_nid` = '{$data['borrow_nid']}'";
		}
		
		
		//判断添加时间开始
		if (IsExiest($data['dotime1']) != false){
			$dotime1 = ($data['dotime1']=="request")?$_REQUEST['dotime1']:$data['dotime1'];
			if ($dotime1!=""){
				$_sql .= " and p1.addtime > ".get_mktime($dotime1);
			}
		}
		
		//判断添加时间结束
		if (IsExiest($data['dotime2'])!=false){
			$dotime2 = ($data['dotime2']=="request")?$_REQUEST['dotime2']:$data['dotime2'];
			if ($dotime2!=""){
				$_sql .= " and p1.addtime < ".get_mktime($dotime2);
			}
		}
		
		//判断借款状态
		if (IsExiest($data['status'])!=""){
			$_sql .= " and p1.status in ({$data['status']})";
		}
		//判断是否担保借款
		if (IsExiest($data['vouch_status'])!=""){
			$_sql .= " and p3.vouch_status in ({$data['vouch_status']})";
		}
		
		//借款期数
		if (IsExiest($data['borrow_period'])!=""){
			$_sql .= " and p3.borrow_period = {$data['borrow_period']}";
		}
		
		//借款类别
		if (IsExiest($data['flag'])!=""){
			$_sql .= " and p3.flag = {$data['flag']}";
		}
		
		//借款用途
		if (IsExiest($data['borrow_use']) !=""){
			$_sql .= " and p3.borrow_use in ({$data['borrow_use']})";
		}
		
		//借款用户类型
		if (IsExiest($data['borrow_usertype']) !=""){
			$_sql .= " and p3.borrow_usertype = '{$data['borrow_usertype']}'";
		}
		
		
		//借款
		if (IsExiest($data['borrow_style']) ){
			$_sql .= " and p3.borrow_style in ({$data['borrow_style']})";
		}
		
		//金额权限
		if (IsExiest($data['account1'])!=""){
			$_sql .= " and p1.account >= {$data['account1']}";
		}
		if (IsExiest($data['account2'])!=""){
			$_sql .= " and p1.account <= {$data['account2']}";
		}
		//排序
		$_order = " order by p1.id desc ";
	
		$_select = " p1.*,p2.username,p3.name as borrow_name,p3.account as borrow_account,p4.username as borrow_username,p3.repay_account_wait as borrow_account_wait_all,p3.repay_account_interest_wait as borrow_interest_wait_all,p4.user_id as borrow_userid,p3.borrow_apr,p3.borrow_period,p3.borrow_account_scale,p5.credits,p6.status";
		$sql = "select SELECT from `{borrow_tender}` as p1 
				 left join `{users}` as p2 on p1.user_id=p2.user_id
				 left join `{borrow}` as p3 on p1.borrow_nid=p3.borrow_nid
				 left join `{users}` as p4 on p4.user_id=p3.user_id
				 left join `{credit}` as p5 on p5.user_id=p3.user_id
				 left join `{borrow_change}` as p6 on p1.id=p6.tender_id
				 SQL ORDER LIMIT
				";
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
		foreach ($list as $key => $value){
			$repaysql="select * from `{borrow_repay}` where repay_time<".time()." and repay_status=0 and borrow_nid={$value['borrow_nid']}";
			$repayresult=$mysql->db_fetch_array($repaysql);
			if ($repayresult==true){
				$list[$key]['change_no']=1;
			}
			$latesql="select sum(late_interest) as all_interest from `{borrow_repay}` where borrow_nid={$value['borrow_nid']}";
			$late=$mysql->db_fetch_array($latesql);
			$list[$key]['late_interest'] = $late['all_interest'];
			$list[$key]["credit"] = self::GetBorrowCredit(array("user_id"=>$value['user_id']));
			$recoversql="select count(1) as num from `{borrow_repay}` where borrow_nid={$value['borrow_nid']} and (repay_status=1 or repay_web=1)";
			$recoverresult=$mysql->db_fetch_array($recoversql);
			$list[$key]['norepay_num'] = $value['borrow_period'] - $recoverresult['num'];
			$list[$key]['repay_num'] = $recoverresult['num'];
			$chsql="select status,buy_time from `{borrow_change}` where tender_id={$value['id']}";
			$chresult=$mysql->db_fetch_array($chsql);
			if ($chresult['status']==1){
				$recsql="select count(1) as count_all,
				sum(recover_account_yes) as recover_account_yes_all,
				sum(recover_interest_yes) as recover_interest_yes_all
				from `{borrow_recover}` where user_id={$value['user_id']} and borrow_nid={$value['borrow_nid']} and recover_yestime<{$chresult['buy_time']} and tender_id={$value['id']}";
				$recresult=$mysql->db_fetch_array($recsql);
				$list[$key]["recover_interest_yes_all"] = $recresult['recover_interest_yes_all'];
				$list[$key]["recover_account_yes_all"] = $recresult['recover_account_yes_all'];
				$list[$key]["count_all"] = $recresult['count_all'];
			}
			//add 20120831 wlz
			if($value['recover_account_wait'] < 0 ){
				$list[$key]['recover_account_wait'] = number_format(0,2);
			}
			if($value['recover_account_interest_wait'] < 0){
				$list[$key]['recover_account_interest_wait'] = number_format(0,2);
			}
		}
		//返回最终的结果
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		
		return $result;
	}
	
	
	
	/**
	 * 6,查看投资标 
	 *
	 * @param Array $data = array("id"=>"投资序号","tender_nid"=>"投资标识名");
	 * @return Array
	 */
	public static function GetTenderOne($data = array()){
		global $mysql;
		$_sql = "where 1=1 ";
		
		if (IsExiest($data['id'])!=""){
			$_sql .= " and  p1.id = '{$data['id']}' ";
		}
		
		if (IsExiest($data['tender_id'])!=""){
			$_sql .= " and  p1.tender_id = '{$data['tender_id']}' ";
		}
		
		$_select = " p1.*,p2.username,p3.name as borrow_name,p3.account as borrow_account,p3.borrow_period,p3.borrow_style,p3.borrow_use,p3.borrow_flag,p3.borrow_apr";
		$sql = "select {$_select} from `{borrow_tender}` as p1 
				 left join `{users}` as p2 on p1.user_id=p2.user_id
				 left join `{borrow}` as p3 on p1.borrow_nid=p3.borrow_nid
				 {$_sql}";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "borrow_tender_not_exiest";
		return $result;
	}
	
	
	
	/**
	 * 7,投资撤销，只要运用在投资人不想投的情况下可以手动的撤回，撤回需缴纳一定的 
	 *
	 * @param Array $data = array("id"=>"投资序号","tender_nid"=>"投资标识名");
	 * @return Array
	 */
	public static function CancelTender($data = array()){
		global $mysql;
		$sql = "select * from `{borrow_tender}` where tender_nid='{$data['tender_nid']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "borrow_tender_not_exiest";
		if ($result['tender_status']>0) return "borrow_tender_verify_yes";
		
		$sql = "update `{borrow_tender}` set status=0 where tender_nid='{$data['tender_nid']}'";
		$mysql->db_query($sql);
		
		
		return $data['tender_nid'];
	}
	
	//7.1投资撤销
	public static function Cancel($data = array()){
		global $mysql,$MsgInfo;
		$_sql = " where 1=1 ";
		if (IsExiest($data['borrow_nid'])!=false){
			$_sql .= " and borrow_nid='{$data['borrow_nid']}'";
		}else{
			return "borrow_nid_empty";
		}
		
		$borrow_nid = $data['borrow_nid'];
		
		$borrow_result=self::GetOne(array("borrow_nid"=>$borrow_nid));
		if (IsExiest($data['user_id'])!=""){
			$_sql .= " and user_id={$data['user_id']} ";
		}
		$sql = "select `name`,borrow_account_scale,borrow_nid,status,vouch_status,user_id from `{borrow}` $_sql";
		$result = $mysql->db_fetch_array($sql);
		if ($result["status"]==5) return "borrow_cancel_has" ;
		if ($result["status"]!=1 && $result["status"]!=0) return "borrow_cancel_error" ;
		if ($result["tender_times"]>0) return "borrow_cancel_yestender";
		$vouch_status = $result['vouch_status'];
		
		
			
		if ($data['cancel_status']!=""){
			$sql = "update `{borrow}` set cancel_remark='{$data['cancel_remark']}',cancel_time='".time()."',cancel_status='{$data['cancel_status']}' where borrow_nid = '{$borrow_nid}'";
			$mysql->db_query($sql);
             $sql = "update  `{borrow}`  set status=5,reverify_time='".time()."',reverify_remark='网站撤销' $_sql";
	       	$result = $mysql->db_query($sql);
		}else{
		  $sql = "update  `{borrow}`  set status=5,reverify_time='".time()."',reverify_remark='用户撤销' $_sql";
	       	$result = $mysql->db_query($sql);
		}
		
		$borrow_url = "<a href={$_G['weburl']}/invest/a{$result['borrow_nid']}.html target=_blank>{$result['name']}</a>";
	
		if( $borrow_result['borrow_type']==1 || $borrow_result['borrow_type']==4){		
			$_data["user_id"] = $borrow_result['user_id'];
			$_data["amount_type"] = "borrow";
			$_data["type"] = "borrow_success";
			$_data["oprate"] = "return";
			$_data["nid"] = "borrow_return_credit_".$borrow_result['user_id']."_".$borrow_result['borrow_nid'];
			$_data["account"] = $borrow_result['account'];
			$_data["remark"] = "借款标[{$borrow_url}]撤消，返还{$borrow_result['account']}借款信用额度";
			borrowClass::AddAmountLog($_data);	
			
			if($borrow_result['borrow_type']==4){
				$money = $borrow_result['account']*$borrow_result['borrow_apr']*0.01/12;
				$log_info["user_id"] = $borrow_result['user_id']; //操作用户id
				$log_info["nid"] = "borrow_miao_return_" . $borrow_result['borrow_nid']; //订单号
				$log_info["money"] = $money; //操作金额
				$log_info["income"] = $money; //收入
				$log_info["expend"] = 0; //支出
				$log_info["balance_cash"] = $money; //可提现金额
				$log_info["balance_frost"] = 0; //不可提现金额
				$log_info["frost"] = -$money; //冻结金额
				$log_info["await"] = 0; //待收金额
				$log_info["repay"] = 0; //待收金额
				$log_info["type"] = "borrow_miao_repay"; //类型
				$log_info["to_userid"] = 0; //付给谁
				$log_info["remark"] = "秒标撤消解冻{$money}元";
				accountClass::AddLog($log_info);
			}
			
		}else{
			$_data["user_id"] = $borrow_result['user_id'];
			$_data["amount_type"] = "diya_borrow";
			$_data["type"] = "borrow_success";
			$_data["oprate"] = "return";
			$_data["nid"] = "borrow_return_vouch_".$borrow_userid."_".$borrow_result['borrow_nid']."_".$vouch_id;
			$_data["account"] = $borrow_result['account'];
			$_data["remark"] = "抵押借款[{$borrow_url}]撤消返还抵押额度";//type 操作的类型 
			borrowClass::AddAmountLog($_data);
		}
		
		//撤标站内信提醒 wdf 20121026
		$remind['nid'] = "borrow_cancel";				
		$remind['code'] = "borrow";
		$remind['article_id'] = $borrow_result['borrow_nid'];
		$remind['receive_userid'] = $borrow_result['user_id'];
		$remind['title'] = "借款标[{$borrow_result['name']}]撤标";
		$remind['content'] = "借款标[{$borrow_url}]在".date("Y-m-d",time())."撤标";
		remindClass::sendRemind($remind);
		
		//第17步，担保标的操作
		if ($vouch_status==1){
			
			$result = self::GetVouchList(array("limit"=>"all","borrow_nid"=>$borrow_nid));
			if ($result!=""){
				foreach ($result as $key => $value){
				
					//1,首先更新担保的状态为2，表示担保失败
					$vouch_account = $value['account'];
					$vouch_userid = $value['user_id'];
					$vouch_id = $value['id'];
					$vouch_award = $value['award_account'];
					
					$sql = "update `{borrow_vouch}` set status=2 where id = '{$vouch_id}'";
					
					$mysql -> db_query($sql);
					
					//2,投资担保人的担保额度返回
					//添加额度记录
					//扣除借款担保额度
					$_data["user_id"] = $vouch_userid;
					$_data["amount_type"] = "vouch_tender";
					$_data["type"] = "borrow_false";
					$_data["oprate"] = "add";
					$_data["nid"] = "borrow_false_vouch_".$vouch_userid."_".$borrow_nid.$value["id"];
					$_data["account"] = $vouch_account;
					$_data["remark"] = "担保借款[{$borrow_url}]审核失败借款担保额度返回";//type 操作的类型 
					borrowClass::AddAmountLog($_data);
		
				}	
			}
		}
				
		//返回所有投资者的金钱。
		$result = self::GetTenderList(array("borrow_nid"=>$borrow_nid,"limit"=>"all"));
		foreach ($result as $key => $value){
			if ($value['status']==0){
				
				//同时更新投标的状态为3
				$sql = "update `{borrow_tender}` set status=3 where id = '{$value['id']}'";
				$mysql->db_query($sql);
				
				//加入资金积累
				$log_info["user_id"] = $value['user_id'];//操作用户id
				$log_info["nid"] = "tender_user_cancel_".$value['user_id']."_".$borrow_nid.$value['id'];//订单号
				$log_info["money"] = $value['account'];//操作金额
				$log_info["income"] = 0;//收入
				$log_info["expend"] = 0;//支出
				$log_info["balance_cash"] = 0;//可提现金额
				$log_info["balance_frost"] = $value['account'];//不可提现金额
				$log_info["frost"] = -$value['account'];//冻结金额
				$log_info["await"] = 0;//待收金额
				$log_info["type"] = "tender_user_cancel";//类型
				$log_info["to_userid"] = $data['user_id'];//付给谁
				$log_info["remark"] = str_replace("#borrow_url#",$borrow_url,$MsgInfo["account_tender_user_cancel"]);
				$result = accountClass::AddLog($log_info);
				
				
				//提醒设置
				$remind['nid'] = "tender_cancel";
				$remind['code'] = "borrow";
				$remind['article_id'] = $data['id'];
				$remind['receive_userid'] = $value['user_id'];
				$remind['title'] = "投资的借款标[{$borrow_result['name']}]撤标";
				$remind['content'] = "投资的借款标[{$borrow_url}]在".date("Y-m-d",time())."撤标";
				remindClass::sendRemind($remind);
				
				//更新统计信息
				self::UpdateBorrowCount(array("user_id"=>$value['user_id'],"tender_frost_account"=>-$value['account']));
					
			}
		}
		
		
		
		return $data['borrow_nid'];
	}
	/**
	 * 添加投标
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	public static function AddTender($data = array()){
		global $mysql,$_G;
		
		//判断id是否为空
		if (IsExiest($data['borrow_nid']) ==""){
			return "borrow_nid_empty";
		}		
		
		//判断是否存在借款标
		$borrow_result = borrowClass::GetOne(array("borrow_nid"=>$data['borrow_nid']));
		
		if (!is_array($borrow_result)){
			return $borrow_result;
		}
		
		if ($borrow_result["user_id"]==$data['user_id']){
			return "borrow_tender_user_id_re";
		}
		
		//判断是否已经投资满额
		if ($borrow_result['borrow_account_yes']>=$borrow_result['account']){
			return "tender_full_yes";
		}
		
		//判断是否已经审核
		if ($borrow_result['verify_time'] == "" || $borrow_result['status'] != 1){
			return "tender_verify_no";
		}
		
		//判断是否已经过期
		if ($borrow_result['verify_time'] + $borrow_result['borrow_valid_time']*60*60*24<time()){
			return "tender_late_yes";
		}
		
		//判断金额是否正确
		if(!is_numeric($data['account'])){
			return "tender_money_error";
		}
		
	
		//判断是否小于投资金额
		if($data['account']<$borrow_result['tender_account_min']){
			return "最小的投资金额不能小于{$borrow_result['tender_account_min']}。";
		}
		
		
		//判断是否大于投资金额
		if($data['account']>$borrow_result['account']){
			return "最大的投资金额不能大于".($borrow_result['account'])."。";
		}
		
		
		//如果是担保标，先判断担保是否已完成
		if($borrow_result['vouch_status']==1 && $borrow_result['vouch_account']!=$borrow_result['vouch_account_yes']){
			return "tender_vouch_full_no";
		}
		
		//判断投资的总金额
		$tender_account_all = borrowClass::GetUserTenderAccount(array("user_id"=>$data["user_id"],"borrow_nid"=>$data['borrow_nid']));
		
		if ($tender_account_all+$data['account']>$borrow_result['tender_account_max'] && $borrow_result['tender_account_max']>0){
			$tender_account = $borrow_result['tender_account_max']-$tender_account_all;
			return"您已经投标了{$tender_account_all},最大投标总金额不能大于{$borrow_result['tender_account_max']}，你最多还能投资{$tender_account}";
		}else{
			$data['account_tender'] = $data['account'];
			
			//判断投资的金额是否大于待借的金额
			if ($borrow_result['borrow_account_wait']<$data['account']){
				$data['account'] = $borrow_result['borrow_account_wait'];
			}
			//判断金额是否是一样的
			$account_result =  accountClass::GetAccountUsers(array("user_id"=>$data['user_id']));//获取当前用户的余额
			if ($account_result['balance']<$data['account']){
				return "tender_money_no";
			}
		}
		
		//判断是否是友情借款
		if ($borrow_result['tender_friends']!=""){
			$_tender_friends = explode("|",$borrow_result['tender_friends']);
			$sql = "select username from {users} where user_id='{$data['user_id']}'";
			$result = $mysql->db_fetch_array($sql);
			if (!in_array($result['username'],$_tender_friends)){
				return "tender_friends_error";
			}
		}
		
		//判断投资金额是否大于待还金额
		if ($_G['system']['con_repay_no']==0){
			$moresql="select * from `{borrow}` where user_id={$data['user_id']} and repay_account_wait!=0";
			$more=$mysql->db_fetch_array($moresql);
			if ($more==true){
				//return "borrow_no_more";
			}
		}else{
			$acc=$data['account']*2;
			$moresql="select sum(repay_account_wait) as account_all from `{borrow}` where user_id={$data['user_id']}";
			$more=$mysql->db_fetch_array($moresql);
			if ($more['account_all']<$acc && $more['account_all']!=0){
				return "borrow_no_more";
			}
		}

		//更新借款的信息
		$sql = "update  `{borrow}`  set borrow_account_yes=borrow_account_yes+{$data['account']},borrow_account_wait=borrow_account_wait-{$data['account']},borrow_account_scale=(borrow_account_yes/account)*100,tender_times=tender_times+1  where borrow_nid='{$data['borrow_nid']}'";
		$mysql->db_query($sql);//更新已经投标的钱
		
		
		//投标金额冻结
		$borrow_url = "<a href=http://www.hcdai.com/invest/a{$data['borrow_nid']}.html target=_blank>{$borrow_result['name']}</a>";
		$log_info["user_id"] = $data["user_id"];//操作用户id
		$log_info["nid"] = "tender_frost_".$data['user_id']."_".time();
		$log_info["money"] = $data['account'];//操作金额
		$log_info["income"] = 0;//收入
		$log_info["expend"] = 0;//支出
		$log_info["balance_cash"] = 0;//可提现金额
		$log_info["balance_frost"] = -$data['account'];//不可提现金额
		$log_info["frost"] = $data['account'];//冻结金额
		$log_info["await"] = 0;//待收金额
		$log_info["type"] = "tender";//类型
		$log_info["to_userid"] = $borrow_result['user_id'];//付给谁
		if ($data['auto_status']==1){
			$log_info["remark"] = "自动投标[{$borrow_url}]所冻结资金";//备注
		}else{
			$log_info["remark"] = "投标[{$borrow_url}]所冻结资金";//备注
		}
		accountClass::AddLog($log_info);
		
		
		
		$remind['nid'] = "borrow_join";
		$remind['code'] = "borrow";
		$remind['article_id'] = $data['borrow_nid'];
		$remind['receive_userid'] = $borrow_result['user_id'];
		$remind['title'] = "您的借款标({$borrow_result['name']})有人投标";
		$remind['content'] = "您的借款标[{$borrow_url}]在".date("Y-m-d",time())."有人投标";
		remindClass::sendRemind($remind);
		
		
		
		//更新统计信息
		self::UpdateBorrowCount(array("user_id"=>$data['user_id'],"tender_times"=>1,"tender_account"=>$data['account'],"tender_frost_account"=>$data['account']));
		
		
		//添加投资的借款信息
		$sql = "insert into `{borrow_tender}` set `addtime` = '".time()."',`addip` = '".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
		 $mysql->db_query($sql);
		return $mysql->db_insert_id();
	}
	
	//满标审核
	public static function Reverify($data = array()){
		global $mysql,$_G;
		
		
		
		//第一步，更新满标时的操作人
		$sql = " update `{borrow}` set reverify_userid='{$data['reverify_userid']}',reverify_remark='{$data['reverify_remark']}',reverify_time='".time()."',status='{$data['status']}' where borrow_nid='{$borrow_nid}'";
		 $mysql ->db_query($sql);
		 
		 
		//基本的资料
		$borrow_userid = $borrow_result["user_id"];//借款用户id
		$borrow_username = $borrow_result["username"];//借款用户id
		$borrow_account = $borrow_result["account"];//借款金额
		$borrow_period = $borrow_result["borrow_period"];//借款期限
		$borrow_name = $borrow_result["name"];//借款 标题
		$borrow_cash_status = $borrow_result["cash_status"];//是否提现标
		$borrow_url = "<a href=http://www.hcdai.com/invest/a{$data['borrow_nid']}.html target=_blank style=color:blue>{$borrow_result['name']}</a>";//借款标地址
		$borrow_url = htmlspecialchars($borrow_url,ENT_QUOTES);
		
		if ($status == 3){
		
			
			//第二步，更新满标时的操作人
			$sql = " update `{borrow}` set borrow_full_status='1' where borrow_nid='{$borrow_nid}'";
			$mysql ->db_query($sql);
			
			//第三步，如果成功，则将还款信息输进表里面去
			$_equal["account"] = $borrow_result["account"];
			$_equal["period"] = $borrow_result["borrow_period"];
			$_equal["apr"] = $borrow_result["borrow_apr"];
			$_equal["style"] = $borrow_result["borrow_style"];
			$equal_result = EqualInterest($_equal);
			foreach ($equal_result as $key => $value){
				//防止重复添加还款信息
				$sql = "select 1 from `{borrow_repay}` where user_id='{$borrow_userid}' and repay_period='{$key}' and borrow_nid='{$borrow_nid}'";
				$result = $mysql->db_fetch_array($sql);
				if ($result==false){
					$sql = "insert into `{borrow_repay}` set `addtime` = '".time()."',";
					$sql .= "`addip` = '".ip_address()."',user_id='{$borrow_userid}',status=1,`borrow_nid`='{$borrow_nid}',`repay_period`='{$key}',";
					$sql .= "`repay_time`='{$value['repay_time']}',`repay_account`='{$value['account_all']}',";
					$sql .= "`repay_interest`='{$value['account_interest']}',`repay_capital`='{$value['account_capital']}'";
					$mysql ->db_query($sql);
				}else{
					$sql = "update `{borrow_repay}` set `addtime` = '".time()."',";
					$sql .= "`addip` = '".ip_address()."',user_id='{$borrow_userid}',status=1,`borrow_nid`='{$borrow_nid}',`repay_period`='{$key}',";
					$sql .= "`repay_time`='{$value['repay_time']}',`repay_account`='{$value['account_all']}',";
					$sql .= "`repay_interest`='{$value['account_interest']}',`repay_capital`='{$value['account_capital']}'";
					$sql .= " where user_id='$borrow_userid' and repay_period='{$key}' and borrow_nid='{$borrow_nid}'";
					$mysql ->db_query($sql);
				}
			}
			$repay_times = count($equal_result);
			$_equal["type"] = "all";
			$equal_result = EqualInterest($_equal);
			$repay_all = $equal_result['account_total'];
			//添加还款信息结束
			
			//第十二步，借款者总金额增加。
			$log_info["user_id"] = $borrow_userid;//操作用户id
			$log_info["nid"] = "borrow_success_".$borrow_nid;//订单号
			$log_info["money"] = $borrow_account;//操作金额
			$log_info["income"] = $borrow_account;//收入
			$log_info["expend"] = 0;//支出
			if ($borrow_result["borrow_style"]==5){
				$log_info["balance_cash"] = 0;//可提现金额
				$log_info["balance_frost"] = $borrow_account;//不可提现金额
			}else{
				$log_info["balance_cash"] = $borrow_account;//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
			}
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["repay"] = $repay_all;//待收金额
			$log_info["type"] = "borrow_success";//类型
			$log_info["to_userid"] = 0;//付给谁
			$log_info["remark"] =  "通过[{$borrow_url}]借到的款";
			accountClass::AddLog($log_info);
			
				
			//第十三步，借款管理费： 借款本金的1.5%（一次 借款人）
			$log_info["user_id"] = $borrow_userid;//操作用户id
			$log_info["nid"] = "borrow_success_manage_".$borrow_nid.$borrow_userid;//订单号
			$fee_account = round($borrow_account*0.015,2);
			$log_info["money"] = $fee_account;//操作金额
			$log_info["income"] = 0;//收入
			$log_info["expend"] = $fee_account;//支出
			$log_info["balance_cash"] = -$fee_account;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["type"] = "borrow_success_manage";//类型
			$log_info["to_userid"] = 0;//付给谁
			$log_info["remark"] =  "成功借款[{$borrow_url}]的成交费";
			accountClass::AddLog($log_info);
			
			//第十四步，账号管理费： 借款本金的0.3%（一次 借款人）
			$log_info["user_id"] = $borrow_userid;//操作用户id
			$log_info["nid"] = "borrow_success_account_".$borrow_nid.$borrow_userid;//订单号
			$fee_account = round($borrow_account*0.003*$borrow_period,2);
			$log_info["money"] = $fee_account;//操作金额
			$log_info["income"] = 0;//收入
			$log_info["expend"] = $fee_account;//支出
			$log_info["balance_cash"] = -$fee_account;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["type"] = "borrow_success_account";//类型
			$log_info["to_userid"] = 0;//付给谁
			$log_info["remark"] =  "成功借款[{$borrow_url}]的账户管理费";
			accountClass::AddLog($log_info);
			
			/*
			//第十五步，风险池
			$result = creditClass::GetUserRank(array('user_id'=>$borrow_userid,"nid"=>"credit","code"=>"approve"));
			$log_info["user_id"] = $borrow_userid;//操作用户id
			$log_info["nid"] = "fengxianchi_".$borrow_nid.$borrow_userid;//订单号
			$fee_account = round($borrow_account*$result['nid'],2);
			$log_info["money"] = $fee_account;//操作金额
			$log_info["income"] = 0;//收入
			$log_info["expend"] = $fee_account;//支出
			$log_info["balance_cash"] = -$fee_account;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["type"] = "fengxianchi_borrow";//类型
			$log_info["to_userid"] = 0;//付给谁
			$log_info["remark"] =  "成功借款[{$borrow_url}]的进入风险池";
			accountClass::AddLog($log_info);
			*/
			//更新统计信息
			self::UpdateBorrowCount(array("user_id"=>$borrow_userid,"borrow_success_times"=>1,"borrow_repay_times"=>$repay_times,"borrow_repay_wait_times"=>$repay_times,"borrow_account"=>$borrow_result["account"],"borrow_repay_account"=>$repay_all,"borrow_repay_wait"=>$repay_all,"borrow_repay_interest"=>$equal_result['interest_total'],"borrow_repay_interest_wait"=>$equal_result['interest_total'],"borrow_repay_capital"=>$equal_result['capital_total'],"borrow_repay_capital_wait"=>$equal_result['capital_total']));
			
			//第四步，如果成功，将用户投资加入详细表
			$tender_result = self::GetTenderList(array("borrow_nid"=>$borrow_nid,"limit"=>"all"));
			foreach ($tender_result as $_key => $_value){
				
				$tender_id = $_value['id'];
				
				//更新投资人的状态
				$sql = "update `{borrow_tender}` set status=1 where id={$tender_id}";
				$mysql->db_query($sql);
				
				//添加投资的收款纪录
				$_equal["account"] = $_value['account'];
				$_equal["period"] = $borrow_result["borrow_period"];
				$_equal["apr"] = $borrow_result["borrow_apr"];
				$_equal["style"] = $borrow_result["borrow_style"];
				$_equal["type"] = "";
				$equal_result = EqualInterest($_equal);
				
				$tender_userid = $_value['user_id'];
				$tender_account = $_value['account'];
				
				foreach ($equal_result as $period_key => $value){
					$repay_month_account = $value['account_all'];
					//防止重复添加还款信息
					$sql = "select 1 from `{borrow_recover}` where user_id='{$tender_userid}' and borrow_nid='{$borrow_nid}' and recover_period='{$period_key}' and tender_id='{$tender_id}'";
					$result = $mysql->db_fetch_array($sql);
					if ($result==false){
						$sql = "insert into `{borrow_recover}` set `addtime` = '".time()."',";
						$sql .= "`addip` = '".ip_address()."',user_id='{$tender_userid}',status=1,`borrow_nid`='{$borrow_nid}',`borrow_userid`='{$borrow_userid}',`tender_id`='{$tender_id}',`recover_period`='{$period_key}',";
						$sql .= "`recover_time`='{$value['repay_time']}',`recover_account`='{$value['account_all']}',";
						$sql .= "`recover_interest`='{$value['account_interest']}',`recover_capital`='{$value['account_capital']}'";
						$mysql ->db_query($sql);
					}else{
						$sql = "update `{borrow_recover}` set `addtime` = '".time()."',";
						$sql .= "`addip` = '".ip_address()."',user_id='{$tender_userid}',status=1,`borrow_nid`='{$borrow_nid}',`borrow_userid`='{$borrow_userid}',`tender_id`='{$tender_id}',`recover_period`='{$period_key}',";
						$sql .= "`recover_time`='{$value['repay_time']}',`recover_account`='{$value['account_all']}',";
						$sql .= "`recover_interest`='{$value['account_interest']}',`recover_capital`='{$value['account_capital']}'";
						$sql .= " where user_id='{$tender_userid}' and recover_period='{$period_key}' and borrow_nid='{$borrow_nid}' and tender_id='{$tender_id}'";
						$mysql ->db_query($sql);
					}
				}
				$recover_times = count($equal_result);
				//第五步,更新投资标的信息
				$_equal["type"] = "all";
				$equal_result = EqualInterest($_equal);
				$recover_all = $equal_result['account_total'];
				$recover_interest_all = $equal_result['interest_total'];
				$recover_capital_all = $equal_result['capital_total'];
				$sql = "update `{borrow_tender}` set recover_account_all='{$equal_result['account_total']}',recover_account_interest='{$equal_result['interest_total']}',recover_account_wait='{$equal_result['account_total']}',recover_account_interest_wait='{$equal_result['interest_total']}',recover_account_capital_wait='{$equal_result['capital_total']}'  where id='{$tender_id}'";
				$mysql->db_query($sql);
				
				
				//第六步,扣除投资人的资金
				$log_info["user_id"] = $tender_userid;//操作用户id
				$log_info["nid"] = "tender_success_".$borrow_nid.$tender_userid.$tender_id.$period_key;//订单号
				$log_info["money"] = $tender_account;//操作金额
				$log_info["income"] = 0;//收入
				$log_info["expend"] = $tender_account;//支出
				$log_info["balance_cash"] = 0;//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
				$log_info["frost"] = -$tender_account;//冻结金额
				$log_info["await"] = 0;//待收金额
				$log_info["type"] = "tender_success";//类型
				$log_info["to_userid"] = $borrow_userid;//付给谁
				$log_info["remark"] = "投标[{$borrow_url}]成功投资金额扣除";
				accountClass::AddLog($log_info);
				
				//第七步,添加待收的金额
				$log_info["user_id"] = $tender_userid;//操作用户id
				$log_info["nid"] = "tender_success_frost_".$borrow_nid.$tender_userid.$tender_id.$period_key;//订单号
				$log_info["money"] = $recover_all;//操作金额
				$log_info["income"] = 0;//收入
				$log_info["expend"] = 0;//支出
				$log_info["balance_cash"] = 0;//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
				$log_info["frost"] = 0;//冻结金额
				$log_info["await"] = $recover_all;//待收金额
				$log_info["type"] = "tender_success_frost";//类型
				$log_info["to_userid"] = $borrow_userid;//付给谁
				$log_info["remark"] =  "投标[{$borrow_url}]成功待收金额增加";
				accountClass::AddLog($log_info);
				
				
				
				
				//第九步,提醒设置
				$remind['nid'] = "tender_success";
				
				$remind['receive_userid'] = $tender_userid;
				$remind['article_id'] = $borrow_nid;
				$remind['code'] = "borrow";
				$remind['title'] = "投资({$borrow_username})的标[<font color=red>{$borrow_name}</font>]满标审核成功";
				$remind['content'] = "你所投资的标[{$borrow_url}]在".date("Y-m-d",time())."已经审核通过";				
				remindClass::sendRemind($remind);
				
				
				
			
				//加入用户操作记录
				$user_log["user_id"] = $tender_userid;
				$user_log["code"] = "tender";
				$user_log["type"] = "tender_success";
				$user_log["operating"] = "tender";
				$user_log["article_id"] = $tender_userid;
				$user_log["result"] = 1;
				$user_log["content"] = "借款标[{$borrow_url}]通过了复审,[<a href=/protocol/a{$data['borrow_nid']}.html target=_blank>点击此处</a>]查看协议书";
				usersClass::AddUsersLog($user_log);	
				
				
				//第十步,投资者的信用积分增加
				/*
				$credit_log['user_id'] = $tender_userid;
				$credit_log['nid'] = "tender_success";
				$credit_log['code'] = "borrow";
				$credit_log['type'] = "tender";
				$credit_log['addtime'] = time();
				$credit_log['value'] = round($repay_account/100);
				$credit_log['article_id'] =$tender_id;				
				$result = creditClass::ActionCreditLog($credit_log);
				
				//第十一步，增加投资推广人金额
				//获取投资人的独立投资推广人
				$spreadsql="select * from `{spread_user}` where spread_userid={$tender_userid} and style=2 and type=3";
				$spread_result=$mysql->db_fetch_array($spreadsql);
				
				if ($spread_result==true){
					//获取独立投资推广人的提成利率
					$feesql="select `task_fee` from `{spread_setting}` where type=5";
					$fee_result=$mysql->db_fetch_array($feesql);
					$tenderusername=usersClass::GetUsers(array("user_id"=>$tender_userid));
					$log_info["user_id"] = $spread_result['user_id'];//推广员
					$log_info["nid"] = "tender_spread_".$borrow_nid.$tender_userid.$spread_result['user_id'];//订单号
					$log_info["money"] = $tender_account/100*$fee_result['task_fee'];//操作金额
					$log_info["income"] = $log_info["money"];//收入
					$log_info["expend"] = 0;//支出
					$log_info["balance_cash"] = $log_info["money"];//可提现金额
					$log_info["balance_frost"] = 0;//不可提现金额
					$log_info["frost"] = 0;//冻结金额
					$log_info["await"] = 0;//待收金额
					$log_info["type"] = "tender_spread";//类型
					$log_info["to_userid"] = $spread_result['user_id'];//付给谁
					$log_info["remark"] = "投资推广客户[{$tenderusername['username']}]投标[{$borrow_url}]成功所得的推广提成，投资金额{$tender_account}，提成率{$fee_result['task_fee']}%";
					accountClass::AddLog($log_info);
				}
				*/
				//更新统计信息
				self::UpdateBorrowCount(array("user_id"=>$tender_userid,"tender_success_times"=>1,"tender_success_account"=>$tender_account,"tender_frost_account"=>-$tender_account,"tender_recover_account"=>$recover_all,"tender_recover_wait"=>$recover_all,"tender_capital_account"=>$recover_capital_all,"tender_capital_wait"=>$recover_capital_all,"tender_interest_account"=>$recover_interest_all,"tender_interest_wait"=>$recover_interest_all,"tender_recover_times"=>$recover_times,"tender_recover_times_wait"=>$recover_times));
						
			}	
			
			//第十一步，更新借款标的信息$nowtime = time();
			$nowtime= time();
			$endtime = get_times(array("num"=>$borrow_result["borrow_period"],"time"=>$nowtime));
			
			if ($borrow_result["borrow_style"]==1){
				$_each_time = "每三个月后".date("d",$nowtime)."日";
				$nexttime = get_times(array("num"=>3,"time"=>$nowtime));
			}else{
				$_each_time = "每月".date("d",$nowtime)."日";
				$nexttime = get_times(array("num"=>1,"time"=>$nowtime));
			}
			$_equal["account"] = $borrow_result['account'];
			$_equal["period"] = $borrow_result["borrow_period"];
			$_equal["apr"] = $borrow_result["borrow_apr"];
			$_equal["type"] = "all";
			$equal_result = EqualInterest($_equal);
			$sql = "update `{borrow}` set repay_account_all='{$equal_result['account_total']}',repay_account_interest='{$equal_result['interest_total']}',repay_account_capital='{$equal_result['capital_total']}',repay_account_wait='{$equal_result['account_total']}',repay_account_interest_wait='{$equal_result['interest_total']}',repay_account_capital_wait='{$equal_result['capital_total']}',repay_last_time='{$endtime}',repay_next_time='{$nexttime}',borrow_success_time='{$nowtime}',repay_each_time='{$_each_time}',repay_times='{$repay_times}'  where borrow_nid='{$borrow_nid}'";
			$mysql->db_query($sql);
			
			
			
			//第17步，担保标的操作
			if ($borrow_result["vouch_status"]==1){
				
				$result = self::GetVouchList(array("limit"=>"all","borrow_nid"=>$borrow_nid));
				if ($result!=""){
					foreach ($result as $key => $value){
					
						//1,首先更新担保的状态为1，表示担保成功
						$vouch_account = $value['account'];
						$vouch_userid = $value['user_id'];
						$vouch_id = $value['id'];
						$vouch_award = $value['award_account'];
						$sql = "update `{borrow_vouch}` set status=1 where id = {$vouch_id}";
						$mysql -> db_query($sql);
						
						//2,判断是否进行担保奖励担保奖励借款成功的奖励。
						if ($borrow_result["vouch_award_status"]==1){
							$vouch_award_money = $vouch_account*$borrow_result["vouch_award_scale"]*0.01;
							$log_info["user_id"] = $vouch_userid;//操作用户id
							$log_info["nid"] = "vouch_success_award_".$vouch_userid."_".$value['id'].$borrow_nid;//订单号
							$log_info["money"] = $vouch_award_money;//操作金额
							$log_info["income"] = $vouch_award_money;//收入
							$log_info["expend"] = 0;//支出
							$log_info["balance_cash"] = $vouch_award_money;//可提现金额
							$log_info["balance_frost"] = 0;//不可提现金额
							$log_info["frost"] = 0;//冻结金额
							$log_info["await"] = 0;//待收金额
							$log_info["type"] = "vouch_success_award";//类型
							$log_info["to_userid"] = $borrow_userid;//付给谁
							$log_info["remark"] =  "担保借款标[{$borrow_url}]借款成功的担保奖励";
							accountClass::AddLog($log_info);
						
							//借款成功的奖励支出。
							$log_info["user_id"] = $borrow_userid;//操作用户id
							$log_info["nid"] = "vouch_success_awardpay_".$borrow_userid."_".$value['id'].$borrow_nid;//订单号
							$log_info["money"] = $vouch_award_money;//操作金额
							$log_info["income"] = 0;//收入
							$log_info["expend"] = $vouch_award_money;//支出
							$log_info["balance_cash"] = -$vouch_award_money;//可提现金额
							$log_info["balance_frost"] = 0;//不可提现金额
							$log_info["frost"] = 0;//冻结金额
							$log_info["await"] = 0;//待收金额
							$log_info["type"] = "vouch_success_awardpay";//类型
							$log_info["to_userid"] = $vouch_userid;//付给谁
							$log_info["remark"] =  "担保借款标的[{$borrow_url}]借款成功的担保奖励支出";
							accountClass::AddLog($log_info);
							
						}
						
						
						//将还款数据添加到vouch_collection标里面去
						
						$_equal["account"] = $vouch_account;
						$_equal["period"] = $borrow_result["borrow_period"];
						$_equal["apr"] = $borrow_result["borrow_apr"];
						$_equal["type"] = "";
						$_equal["style"] = $borrow_result["borrow_style"];
						$equal_result = EqualInterest($_equal);
						foreach ($equal_result as $period_key => $value){
							$sql = "insert into `{borrow_vouch_recover}` set `addtime` = '".time()."',";
							$sql .= "`addip` = '".ip_address()."',user_id='{$vouch_userid}',status=0,vouch_id={$vouch_id},`borrow_nid`='{$borrow_nid}',`borrow_userid`='{$borrow_userid}',`order`='{$period_key}',";
							$sql .= "`repay_time`='{$value['repay_time']}',`repay_account`='{$value['account_all']}',";
							$sql .= "`repay_interest`='{$value['account_interest']}',`repay_capital`='{$value['account_capital']}'";
							$mysql->db_query($sql);
						}
						
					}
					
					$_borrow_account = round($borrow_account/$borrow_period,2);
					for ($i=0;$i<$borrow_period;$i++){
						if ($i==$borrow_period-1){
							$_borrow_account = $borrow_account - $_borrow_account*$i;
						}
						$repay_time = get_times(array("time"=>time(),"num"=>$i+1));
						$sql = "insert into `{borrow_vouch_repay}` set borrow_nid={$borrow_nid},`addtime` = '".time()."',`addip` = '".ip_address()."',user_id=$borrow_userid ,`order` = {$i},status=0,repay_account = '{$_borrow_account}',repay_time='{$repay_time}'";	
						$mysql->db_query($sql);
					}
				}
				
				//扣除借款担保额度
				$_data["user_id"] = $borrow_userid;
				$_data["amount_type"] = "vouch_borrow";
				$_data["type"] = "borrow_success";
				$_data["oprate"] = "reduce";
				$_data["nid"] = "borrow_success_vouch_".$borrow_userid."_".$borrow_nid.$value["id"];
				$_data["account"] = $borrow_account;
				$_data["remark"] = "担保借款[{$borrow_url}]审核通过扣去借款担保额度";//type 操作的类型 
				borrowClass::AddAmountLog($_data);
			
				//更新统计信息
				self::UpdateBorrowCount(array("user_id"=>$borrow_userid,"borrow_vouch_times"=>1));
				
			}else{
				//扣除借款信用额度
				
				//添加额度记录
				$_data["user_id"] = $borrow_userid;
				$_data["amount_type"] = "borrow";
				$_data["type"] = "borrow_success";
				$_data["oprate"] = "reduce";
				$_data["nid"] = "borrow_success_credit_".$borrow_userid."_".$borrow_nid.$value["id"];
				$_data["account"] = $borrow_account;
				$_data["remark"] = "借款标[{$borrow_url}]满标审核通过，借款信用额度减少";;//type 操作的类型 
				borrowClass::AddAmountLog($_data);
				
			}
			
			
			
			//提醒设置
			$remind['nid'] = "borrow_review_yes";
			
			$remind['receive_userid'] = $borrow_userid;
			$remind['code'] = "borrow";
			$remind['article_id'] = $borrow_nid;
			$remind['title'] = "招标[{$borrow_name}]满标审核成功";
			$remind['content'] = "你的借款标[{$borrow_url}]在".date("Y-m-d",time())."已经审核通过";
			
			remindClass::sendRemind($remind);
			
			//加入用户操作记录
			$user_log["user_id"] = $borrow_userid;
			$user_log["code"] = "borrow";
			$user_log["type"] = "borrow_reverify_success";
			$user_log["operating"] = "success";
			$user_log["article_id"] = $borrow_userid;
			$user_log["result"] = 1;
			$user_log["content"] = "借款标[{$borrow_url}]通过了复审,[<a href=/protocol/a{$data['borrow_nid']}.html target=_blank>点击此处</a>]查看协议书";
			usersClass::AddUsersLog($user_log);	

			
			//添加用户的动态
			$_trend['user_id'] = $borrow_userid;
			$_trend["type"] = "borrow_reverify_success";
			$_trend['content'] = "借款标[{$borrow_url}]通过了复审";
			//$result = userClass::AddUserTrend($_trend);
			
		}
		
		//满标审核失败
		elseif ($status == 4){
			//返回所有投资者的金钱。
			$tender_result = self::GetTenderList(array("borrow_nid"=>$borrow_nid,"limit"=>"all"));
			foreach ($tender_result as $key => $value){
				$tender_userid = $value['user_id'];
				$tender_account= $value['account'];
				$tender_id= $value['id'];
				$log_info["user_id"] = $tender_userid;//操作用户id
				$log_info["nid"] = "tender_false_".$tender_userid."_".$tender_id.$borrow_nid;//订单号
				$log_info["money"] = $tender_account;//操作金额
				$log_info["income"] = 0;//收入
				$log_info["expend"] = 0;//支出
				$log_info["balance_cash"] = 0;//可提现金额
				$log_info["balance_frost"] = $tender_account;//不可提现金额
				$log_info["frost"] = -$tender_account;//冻结金额
				$log_info["await"] = 0;//待收金额
				$log_info["type"] = "tender_false";//类型
				$log_info["to_userid"] = $borrow_userid;//付给谁
				$log_info["remark"] =  "招标[{$borrow_url}]失败返回的投标额";
				accountClass::AddLog($log_info);
				
				
				//提醒设置
				$remind['nid'] = "tender_false";
				
				$remind['code'] = "borrow";
				$remind['article_id'] = $borrow_nid;
				$remind['receive_userid'] = $value['user_id'];
				$remind['title'] = "投资的标[<font color=red>{$borrow_name}</font>]满标审核失败";
				$remind['content'] = "你所投资的标[{$borrow_url}]在".date("Y-m-d",time())."审核失败,失败原因：{$data['reverify_remark']}";
				
				remindClass::sendRemind($remind);
				
				//第十步,更新投资人的状态
				$sql = "update `{borrow_tender}` set status=2 where id={$tender_id}";
				$mysql->db_query($sql);
				
				//更新统计信息
				self::UpdateBorrowCount(array("user_id"=>$tender_userid,"tender_frost_account"=>-$tender_account));
				
				//第17步，担保标的操作
				if ($borrow_result["vouch_status"]==1){
					
					$result = self::GetVouchList(array("limit"=>"all","borrow_nid"=>$borrow_nid));
					if ($result!=""){
						foreach ($result as $key => $value){
						
							//1,首先更新担保的状态为2，表示担保失败
							$vouch_account = $value['account'];
							$vouch_userid = $value['user_id'];
							$vouch_id = $value['id'];
							$vouch_award = $value['award_account'];
							
							$sql = "update `{borrow_vouch}` set status=2 where id = '{$vouch_id}'";
							
							$mysql -> db_query($sql);
							
							//2,投资担保人的担保额度返回
							//添加额度记录
							//扣除借款担保额度
							$_data["user_id"] = $vouch_userid;
							$_data["amount_type"] = "vouch_tender";
							$_data["type"] = "borrow_false";
							$_data["oprate"] = "add";
							$_data["nid"] = "borrow_false_vouch_".$vouch_userid."_".$borrow_nid.$value["id"];
							$_data["account"] = $vouch_account;
							$_data["remark"] = "担保借款[{$borrow_url}]审核失败借款担保额度返回";//type 操作的类型 
							borrowClass::AddAmountLog($_data);
				
						}	
					}
				}
			}
			
			//提醒设置
			$remind['nid'] = "borrow_review_no";
			
			$remind['code'] = "borrow";
			$remind['article_id'] = $borrow_nid;
			$remind['receive_userid'] = $borrow_userid;
			$remind['title'] = "你所申请的标[<font color=red>{$borrow_name}</font>]满标审核失败";
			$remind['content'] = "你所申请的标[{$borrow_url}]在".date("Y-m-d",time())."审核失败,失败原因：{$data['repayment_remark']}";
			
			remindClass::sendRemind($remind);
		}
		
		//如果有设置奖励并且招标成功，或者失败也奖励
		if ($borrow_result['award_status']!=0){
			if ($status == 3 || $borrow_result['award_false']==1){
				$tender_result = self::GetTenderList(array("borrow_nid"=>$borrow_nid,"limit"=>"all"));
				foreach ($tender_result as $key => $value){
					//投标奖励扣除和增加。
					if ($borrow_result['award_status']==1){
						$money = round(($value['account']/$borrow_account)*$borrow_result['award_account'],2);
					}elseif ($borrow_result['award_status']==2){
						$money = round((($borrow_result['award_scale']/100)*$value['account']),2);
					}
					$tender_id = $value['id'];
					$tender_userid = $value['user_id'];
					$log_info["user_id"] = $tender_userid;//操作用户id
					$log_info["nid"] = "tender_award_add_".$tender_userid."_".$tender_id.$borrow_nid;//订单号
					$log_info["money"] = $money;//操作金额
					$log_info["income"] = $money;//收入
					$log_info["expend"] = 0;//支出
					$log_info["balance_cash"] = $money;//可提现金额
					$log_info["balance_frost"] = 0;//不可提现金额
					$log_info["frost"] = 0;//冻结金额
					$log_info["await"] = 0;//待收金额
					$log_info["type"] = "tender_award_add";//类型
					$log_info["to_userid"] = $borrow_userid;//付给谁
					$log_info["remark"] =  "借款[{$borrow_url}]的借款奖励";
					accountClass::AddLog($log_info);
				
					$log_info["user_id"] = $borrow_userid;//操作用户id
					$log_info["nid"] = "borrow_award_lower_".$borrow_userid."_".$tender_id.$borrow_nid;//订单号
					$log_info["money"] = $money;//操作金额
					$log_info["income"] = 0;//收入
					$log_info["expend"] = $money;//支出
					$log_info["balance_cash"] = -$money;//可提现金额
					$log_info["balance_frost"] = 0;//不可提现金额
					$log_info["frost"] = 0;//冻结金额
					$log_info["await"] = 0;//待收金额
					$log_info["type"] = "borrow_award_lower";//类型
					$log_info["to_userid"] = $tender_userid;//付给谁
					$log_info["remark"] =  "扣除借款[{$borrow_url}]的奖励";
					accountClass::AddLog($log_info);
				}
			}
		}
		
		 return $borrow_nid;
	}
	
	
	/**
	 * 查看
	 *
	 * @param Array $data
	 * @return Array
	 */
	public static function GetInvest($data = array()){
		global $mysql,$_G;
		$borrow_nid = $data['id'];
		//第一步，获取借款标的信息
		$sql = "select * from `{borrow}`  where  borrow_nid = '{$borrow_nid}'";
		$result_borrow = $mysql->db_fetch_array($sql);
		
		if ($result_borrow==false){
			return "borrow_nid_empty";
		}
		$user_id = $result_borrow['user_id'];
		
		$_data["account"] = 100;
		$_data["period"] = $result_borrow["borrow_period"];
		$_data["apr"] = $result_borrow["borrow_apr"];
		$_data["style"] = $result_borrow["borrow_style"];
		$_data["type"] = "all";
		$_result = EqualInterest($_data);
		$result_borrow["borrow_interest"] = $_result["interest_total"];
		
		if (IsExiest($data["hits"])=="auto"){
			$sql = "update `{borrow}` set hits = hits+1 where  borrow_nid = '{$borrow_nid}'";
			$mysql->db_query($sql);
		}
		
		$result_borrow["other_time"] = $result_borrow["verify_time"]+$result_borrow["borrow_valid_time"]*60*60*24-time();
		
		//第二步，获取用户的基本信息
		$sql = "select p1.* as credit_pic from `{users}` as p1 
								 where  p1.user_id=$user_id";
		$result['user'] = $mysql->db_fetch_array($sql);
		
		//第三步，获取借款用户的资金账号信息
		$sql = "select * from `{account}` where  user_id={$user_id}";
		$result['account'] = $mysql->db_fetch_array($sql);
		
		if($_G['user_id']>0){
		//第四步，获取当前用户的资金账号信息
		$sql = "select * from `{account}`  where  user_id={$_G['user_id']}";
		$result['user_account'] = $mysql->db_fetch_array($sql);
		}
		
		//第五步，获取投资的担保额度
		$result['amount'] =  self::GetAmountUsers($user_id);
		
		//第六步，统计
		$result["count"] = self::GetBorrowCount(array("user_id"=>$user_id));
		
		//第七步，获取当前用户的资金账号信息
		$sql = "select p1.*,p2.username as kefu_username from `{users_vip}` as p1 left join `{users}` as p2 on p1.kefu_userid = p2.user_id  where  p1.user_id={$user_id}";
		$result['users_vip'] = $mysql->db_fetch_array($sql);
		
		//第八步，获取借款用户的资金账号信息
		$sql = "select * from `{users_info}` where  user_id={$user_id}";
		$result['users_info'] = $mysql->db_fetch_array($sql);
		
		//第九步，判断用户是否逾期
		$sql = "select 1 from `{borrow_repay}` where  borrow_nid = '{$borrow_nid}' and user_id='{$user_id}' and repay_status=0";
		$_result = $mysql->db_fetch_array($sql);
		$result_borrow['late_status'] = 0;
		if ($_result!=false){
			foreach ($_result as $key => $value){
				$late = self::LateInterest(array("time"=>$value['repay_time'],"account"=>$value['capital']));
				if ($late_result['late_days']>0){
					$result_borrow['late_status'] = 1;
				}
			}
		}
		$result['borrow'] = $result_borrow;
		return $result;
		
		
	}
	
	//已成功的借款
	function GetTenderBorrowList($data){
		global $mysql,$_G;
		$user_id =$data['user_id'];
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
		$_sql = "where 1=1";
		if (IsExiest($data['type'])!=""){
			if ($data['type']=="wait"){
				$_sql .= " and p1.recover_times<p2.borrow_period and p1.user_id={$user_id} and p1.change_status!=1";
			}elseif ($data['type']=="change"){
				$_sql .= " and p1.recover_account_all!=p1.recover_account_yes and  p1.change_userid={$user_id} and p1.change_status=1";
			}elseif ($data['type']=="yes"){
				$_sql .= " and p1.recover_times=p2.borrow_period and p1.user_id={$user_id} and p1.change_status=0";
			}
		}else{
			$_sql .= " and p1.user_id={$user_id}";
		}
		
		
		if (IsExiest($data['dotime1'])!=""){
			$dotime1 = ($data['dotime1']=="request")?$_REQUEST['dotime1']:$data['dotime1'];
			if ($dotime1!=""){
				$_sql .= " and p1.addtime > ".get_mktime($dotime1);
			}
		}
		
		if (IsExiest($data['dotime2'])!=""){
			$dotime2 = ($data['dotime2']=="request")?$_REQUEST['dotime2']:$data['dotime2'];
			if ($dotime2!=""){
				$_sql .= " and p1.addtime < ".get_mktime($dotime2);
			}
		}
		if (IsExiest($data['tender_status'])!=""){
			$_sql .= " and p1.status = {$data['tender_status']}";
		}
		if (IsExiest($data['keywords']) !=""){
			$_sql .= " and (p2.`name` like '%".urldecode($data['keywords'])."%') ";
		}
		if (IsExiest($data['borrow_status']) !=""){
			$_sql .= " and p2.status = {$data['borrow_status']}";
		}
		if (IsExiest($data['change_status']) !=""){
			$_sql .= " and p1.change_status != {$data['change_status']}";
		}
		
		$_select  = "p2.*,p1.recover_times,p1.account as tender_account,p1.recover_account_yes,p1.recover_account_wait,p1.user_id as tuser,p1.recover_account_all,p1.account_tender,p1.id as tid,p2.account as borrow_account,p2.borrow_account_yes,p3.username as borrow_username,p4.credits,p5.account as change_account,p5.id as change_id";
		
		$sql = "select SELECT from `{borrow_tender}` as p1 left join `{borrow}` as p2 on p1.borrow_nid=p2.borrow_nid left join `{users}` as p3 on p2.user_id=p3.user_id left join `{credit}` as p4 on p2.user_id=p4.user_id left join `{borrow_change}` as p5 on p5.tender_id=p1.id {$_sql} ORDER";
	
		//是否显示全部的信息
		if (isset($data['limit']) ){
			$_limit = "";
			if ($data['limit'] != "all"){
				$_limit = "  limit ".$data['limit'];
			}
			return $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,  'order by p1.`order` desc,p1.id desc', $_limit), $sql));
		}	
		
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array("count(*) as  num","",""),$sql));
		
		$total = $row['num'];
		$total_page = ceil($total / $epage);
		$index = $epage * ($page - 1);
		$limit = " limit {$index}, {$epage}";
		
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, 'order by p2.id desc', $limit), $sql));		
		$list = $list?$list:array();
		
		foreach ($list as $key => $value){
			$recoversql="select count(1) as num from `{borrow_repay}` where borrow_nid={$value['borrow_nid']} and (repay_status=1 or repay_web=1)";
			$recoverresult=$mysql->db_fetch_array($recoversql);
			$list[$key]['wait_times'] = $value['borrow_period'] - $recoverresult['num'];
			$list[$key]["credit"] = self::GetBorrowCredit(array("user_id"=>$value['user_id']));
			$chsql="select status,buy_time from `{borrow_change}` where tender_id={$value['tid']}";
			$chresult=$mysql->db_fetch_array($chsql);
			if ($chresult['status']==1){
				$recsql="select count(1) as count_all,sum(recover_account_yes) as recover_account_yes_all from `{borrow_recover}` where user_id={$value['tuser']} and borrow_nid={$value['borrow_nid']} and (recover_yestime>{$chresult['buy_time']} or recover_yestime is NULL) and tender_id={$value['tid']}";
				$recresult=$mysql->db_fetch_array($recsql);
				$list[$key]["recover_account_yes_all"] = $recresult['recover_account_yes_all'];
				$list[$key]["count_all"] = $recresult['count_all'];
			}
		}
		return array(
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'epage' => $epage,
            'total_page' => $total_page
        );
	}
	
	//收款明细
	function GetRecoverList($data){
		global $mysql,$_G;
		
		$_sql = " where 1=1 ";
		if (IsExiest($data['user_id'])!=false){
			$_sql .= " and p1.user_id={$data['user_id']}";
		}
		if (IsExiest($data['status'])!=false){
			$_sql .= " and p1.status={$data['status']}";
		}
		if (IsExiest($data['recover_status'])!=false){
			if($data['recover_status']==2){
				$_sql .= " and p1.recover_status=0";
			}else{
				$_sql .= " and p1.recover_status={$data['recover_status']}";
			}
		}
		if (IsExiest($data['borrow_status'])!=false){
			$_sql .= " and p2.status={$data['borrow_status']}";
		}
		if (IsExiest($data['username'])!=false){
			$_sql .= " and p3.username like '%{$data['username']}%' ";
		}
		//搜索标题 
		if (IsExiest($data['borrow_name'])!=false){
			$data['borrow_name'] = urldecode($data['borrow_name']);
			$_sql .= " and p2.name like '%{$data['borrow_name']}%' ";
		}
		//搜索贷款号
		if (IsExiest($data['borrow_nid'])!=false){
			$_sql .= " and p1.borrow_nid = {$data['borrow_nid']}";
		}
		//搜索贷款类型
		if (IsExiest($data['borrow_type'])!=false){
			$_sql .= " and p2.borrow_type='{$data['borrow_type']}'";
		}
		
		if (IsExiest($data['web'])!=false){
			$_sql .= " and p6.web_status=2";
		}
		
		if (IsExiest($data['dotime1'])!=false){
			$dotime1 = ($data['dotime1']=="request")?$_REQUEST['dotime1']:$data['dotime1'];
			if ($dotime1!=""){
				$_sql .= " and p1.recover_time > ".get_mktime($dotime1);
			}
		}
		
		if (IsExiest($data['dotime2'])!=false){
			$dotime2 = ($data['dotime2']=="request")?$_REQUEST['dotime2']:$data['dotime2'];
			if ($dotime2!=""){
				$_sql .= " and p1.recover_time < ".get_mktime($dotime2);
			}
		}
		if (IsExiest($data['type'])!=false){
			if ($data['type']=="yes"){
				$_sql .= " and p1.recover_status =1 or p1.recover_web=1";
			}elseif ($data['type']=="wait"){
				$_sql .= " and p1.recover_status !=1 and p1.recover_web!=1";
			}elseif ($data['type']=="web"){
				$_sql .= " and (p1.recover_web=1 or p1.recover_web_ten_status=1 or p1.recover_web_five_status=1)";
			}
		}
		if (IsExiest($data['change'])!=false){
			$_sql .= " and p1.recover_status =1 and p5.change_status=1";
		}
		
		
		
		if (IsExiest($data['keywords'])!=""){
			$_sql .= " and (p2.name like '%".urldecode($data['keywords'])."%') ";
		}
		$_order = " order by p2.id ";
		if (IsExiest($data['order'])!="" ){
			if ($data['order'] == "repay_time"){
				$_order = " order by p2.id desc,p1.recover_time desc";
			}elseif ($data['order'] == "order"){
				$_order = " order by p1.`order` desc,p1.id desc ";
			}elseif ($data['order'] == "recover_status"){
				$_order = " order by p1.`recover_status` asc,p1.id desc ";
			}
		}
		$_select = 'p1.*,p1.recover_account_yes as recover_recover_account_yes,p2.name as borrow_name,p2.borrow_period,p2.borrow_type,p3.username,p4.username as borrow_username,p4.user_id as borrow_userid,p5.*,p5.recover_account_yes as tender_recover_account_yes';
		/*$sql = "select SELECT from `{borrow_recover}` as p1 
				left join `{borrow}` as p2 on  p2.borrow_nid = p1.borrow_nid
				left join `{users}` as p3 on  p3.user_id = p1.user_id
				left join `{users}` as p4 on  p4.user_id = p2.user_id
				left join `{borrow_tender}` as p5 on  p1.tender_id = p5.id
				left join `{borrow_change}` as p6 on  p1.tender_id = p6.tender_id
			   {$_sql} ORDER LIMIT";*/
		$sql = "select SELECT from `{borrow_recover}` as p1 
				left join `{borrow}` as p2 on  p2.borrow_nid = p1.borrow_nid
				left join `{users}` as p3 on  p3.user_id = p1.user_id
				left join `{users}` as p4 on  p4.user_id = p2.user_id
				left join `{borrow_tender}` as p5 on  p1.tender_id = p5.id
			   {$_sql} ORDER LIMIT";		   
		//是否显示全部的信息
		if (isset($data['limit']) ){
			$_limit = "";
			if ($data['limit'] != "all"){
				$_limit = "  limit ".$data['limit'];
			}
			$list  = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,  $_order, $_limit), $sql));
			foreach ($list as $key => $value){
				$late = self::LateInterest(array("time"=>$value['recover_time'],"account"=>$value['recover_capital']));
				if ($data['type']=="web"){
					if ($value['recover_status']==0){
						$list[$key]['late_days'] = $late['late_days'];
						if ($late['late_days']<30){
							$list[$key]['late_interest'] = round($value['recover_account']*0.008*$late['late_days'],2);
						}else{
							$list[$key]['late_interest'] = round($value['recover_account']*0.008*$late['late_days'],2);
						}
					}else{
					$late = self::LateInterest(array("time"=>$value['recover_time'],"account"=>$value['recover_capital'],"yestime"=>$value['recover_yestime']));
						if ($late['late_days']<30){
							$list[$key]['late_interest'] = round($value['recover_account']*0.008*$late['late_days'],2);
						}else{
							$list[$key]['late_interest'] = round($value['recover_account']*0.008*$late['late_days'],2);
						}
						$list[$key]['late_days'] = $value['late_days'];
					}
				}else{
					if ($value['recover_status']==0){
						$list[$key]['late_days'] = $late['late_days'];
						if ($late['late_days']<30){
							/* $list[$key]['late_interest'] = 0; */
							$list[$key]['late_interest'] = round($value['recover_account']*0.008*$late['late_days'],2);
						}else{
							$list[$key]['late_interest'] = round($value['recover_account']*0.008*$late['late_days'],2);
						}
					}else{
						$list[$key]['late_interest'] = $value['late_interest'];
						$list[$key]['late_days'] = $value['late_days'];
					}
				}
				$list[$key]['all_recover']=$value['recover_capital']+$value['recover_interest']+$value['late_interest'];
			}
			return $list;
		}	
		
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array(" count(*) as num ","",""),$sql));
		
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
		if ($data['style']=="change"){
			$total = count($change);
			$total_page = ceil($total / $epage);
			$index = $epage * ($page - 1);
			$limit = " limit {$index}, {$epage}";
		}elseif ($data['style']=="web"){
			$total = count($web);
			$total_page = ceil($total / $epage);
			$index = $epage * ($page - 1);
			$limit = " limit {$index}, {$epage}";
		}else{
			$total = $row['num'];
			$total_page = ceil($total / $epage);
			$index = $epage * ($page - 1);
			$limit = " limit {$index}, {$epage}";
		}
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, $_order , $limit), $sql));
		foreach ($list as $key => $value){
			$all_capital+=$value['recover_capital'];
			$late = self::LateInterest(array("time"=>$value['recover_time'],"account"=>$value['recover_capital']));
			if ($data['showtype']=="web"){
				if ($value['recover_status']==1){
					$list[$key]['late_days'] = $value['late_days'];
					if ($late['late_days']<30){
						$list[$key]['late_interest'] = round($value['recover_account']*0.008*$late['late_days'],2);
					}else{
						$list[$key]['late_interest'] = round($value['recover_account']*0.008*$late['late_days'],2);
					}
				}else{
					$list[$key]['late_days'] = $late['late_days'];
					$late = self::LateInterest(array("time"=>$value['recover_time'],"account"=>$value['recover_capital'],"yestime"=>$value['recover_yestime']));
					if ($late['late_days']<30){
						$list[$key]['late_interest'] = round($value['recover_account']*0.008*$late['late_days'],2);
					}else{
						$list[$key]['late_interest'] = round($value['recover_account']*0.008*$late['late_days'],2);
					}
				}
			}else{
				if ($value['recover_status']==1){
					$list[$key]['late_interest'] = $value['late_interest'];
					$list[$key]['late_days'] = $value['late_days'];
				}else{
					$list[$key]['late_days'] = $late['late_days'];
					if ($late['late_days']<30){
						/* $list[$key]['late_interest'] = 0; */
						$list[$key]['late_interest'] = round($value['recover_account']*0.008*$late['late_days'],2);
					}else{
						$list[$key]['late_interest'] = round($value['recover_account']*0.008*$late['late_days'],2);
					}
				}
			}
			$list[$key]["credit"] = self::GetBorrowCredit(array("user_id"=>$value['user_id']));
			$list[$key]['all_recover']=$value['recover_capital']+$value['recover_interest']+$value['late_interest'];
			/*if ($value['recover_yestime']<$value['buy_time']){
				$change[$key]['recover_interest_yes']=$value['recover_interest_yes'];
				$change[$key]['borrow_name']=$value['borrow_name'];
				$change[$key]['recover_time']=$value['recover_time'];
				$change[$key]['borrow_userid']=$value['borrow_userid'];
				$change[$key]['borrow_username']=$value['borrow_username'];
				$change[$key]['borrow_nid']=$value['borrow_nid'];
				$change[$key]['recover_period']=$value['recover_period'];
				$change[$key]['borrow_period']=$value['borrow_period'];
				$change[$key]['recover_account']=$value['recover_account'];
				$change[$key]['recover_capital']=$value['recover_capital'];
				$change[$key]['recover_interest']=$value['recover_interest'];
				$change[$key]['late_interest']=$value['late_interest'];
				$change[$key]['late_days']=$value['late_days'];
				$change[$key]['recover_status']=$value['recover_status'];
			}
			if ($value['recover_yestime']>$value['buy_time'] || $value['recover_yestime']==""){
				$web[$key]['recover_interest_yes']=$value['recover_interest_yes'];
				$web[$key]['borrow_name']=$value['borrow_name'];
				$web[$key]['recover_time']=$value['recover_time'];
				$web[$key]['borrow_userid']=$value['borrow_userid'];
				$web[$key]['borrow_username']=$value['borrow_username'];
				$web[$key]['borrow_nid']=$value['borrow_nid'];
				$web[$key]['recover_period']=$value['recover_period'];
				$web[$key]['borrow_period']=$value['borrow_period'];
				$web[$key]['recover_account']=$value['recover_account'];
				$web[$key]['recover_capital']=$value['recover_capital'];
				$web[$key]['recover_interest']=$value['recover_interest'];
				$web[$key]['late_interest']=$list[$key]['late_interest'];
				$web[$key]['late_days']=$list[$key]['late_days'];
				$web[$key]['recover_status']=$value['recover_status'];
				$web[$key]['recover_web']=$list[$key]['recover_web'];
				if ($web[$key]['recover_status']==1 || $web[$key]['recover_web']==1){
					$all_recover+=$web[$key]['recover_account'];
				}
			}*/
		}
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
		if ($data['style']=="change"){
			$total = count($change);
			$total_page = ceil($total / $epage);
			$index = $epage * ($page - 1);
			$limit = " limit {$index}, {$epage}";
		}elseif ($data['style']=="web"){
			$total = count($web);
			$total_page = ceil($total / $epage);
			$index = $epage * ($page - 1);
			$limit = " limit {$index}, {$epage}";
		}else{
			$total = $row['num'];
			$total_page = ceil($total / $epage);
			$index = $epage * ($page - 1);
			$limit = " limit {$index}, {$epage}";
		}
		return array(
            'list' => $list,
            'change' => $change,
            'all_capital' => $all_capital,
            'all_recover' => $all_recover,
            'web' => $web,
            'total' => $total,
            'page' => $page,
            'epage' => $epage,
            'total_page' => $total_page
        );
	}
	
	//逾期利息计算,金额按本金来计算
	//account 金额 time 还款时间,yestime,已还时间
	//返回late_days,late_interest
	function LateInterest($data){
		global $mysql,$_G;
		if (IsExiest($data['yestime'])!=""){
			$now_time = get_mktime(date("Y-m-d",$data['yestime']));
		}else{
			$now_time = get_mktime(date("Y-m-d",time()));
		}
		$repayment_time = get_mktime(date("Y-m-d",$data['time']));
		$late_days = ($now_time - $repayment_time)/(60*60*24);
		$_late_days = explode(".",$late_days);
		$late_days = ($_late_days[0]<0)?0:$_late_days[0];
		
		//逾期罚息
		if ($late_days>0 && $late_days<=3){
			$late_fee = isset($_G['system']['con_borrow_late_fee_3'])?$_G['system']['con_borrow_late_fee_3']:0.005;
		}elseif ($late_days>3 && $late_days<=30){
			$late_fee = isset($_G['system']['con_borrow_late_fee_30'])?$_G['system']['con_borrow_late_fee_30']:0.007;
		}elseif ($late_days>30 && $late_days<=90){
			$late_fee = isset($_G['system']['con_borrow_late_fee_90'])?$_G['system']['con_borrow_late_fee_90']:0.008;
		}elseif ($late_days>90){
			$late_fee = isset($_G['system']['con_borrow_late_fee_all'])?$_G['system']['con_borrow_late_fee_all']:0.01;
		}
		
		
		//催缴管理费
		if ($late_days>4 && $late_days<=10){
			$manage_fee = isset($_G['system']['con_borrow_late_manage_fee_10'])?$_G['system']['con_borrow_late_manage_fee_10']:0.002;
		}elseif ($late_days>10 && $late_days<=30){
			$manage_fee = isset($_G['system']['con_borrow_late_manage_fee_30'])?$_G['system']['con_borrow_late_manage_fee_30']:0.003;
		}elseif ($late_days>30 && $late_days<=90){
			$manage_fee = isset($_G['system']['con_borrow_late_manage_fee_90'])?$_G['system']['con_borrow_late_manage_fee_90']:0.004;
		}elseif ($late_days>90){
			$manage_fee = isset($_G['system']['con_borrow_late_manage_fee_all'])?$_G['system']['con_borrow_late_manage_fee_all']:0.005;
		}
		
		//逾期罚息（逾期费率*还款本金*逾期天数）
		$late_interest = round($data['capital']*$late_fee*$late_days,2);
		$late_manage = round($data['account']*$manage_fee*$late_days,2);
		return array("late_days"=>$late_days,"late_interest"=>$late_interest ,"late_reminder"=>$late_manage);
	}
	/**
	 * 添加担保
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	public static function AddVouch($data = array()){
		global $mysql,$_G;
		if (!isset($data['borrow_nid']) || $data['borrow_nid']==""){
			return "borrow_nid_empty";
		}	
		if (!isset($data['user_id']) || $data['user_id']==""){
			return "borrow_user_id_empty";
		}		
		
		$sql = "select * from `{borrow}`  where  borrow_nid = '{$data['borrow_nid']}'";
		$result_borrow = $mysql->db_fetch_array($sql);
		if ($result_borrow == false){
			return "borrow_not_exiest";
		}
		if ($data['user_id']==$result_borrow['user_id']){
			return "borrow_vouch_not_self";
		}
		$borrow_url = "<a href=http://www.hcdai.com/invest/a{$result_borrow['borrow_nid']}.html target=_blank>{$result_borrow['name']}</a>";
		
		if ($_G['user_result']['islock']==1){
			return "user_islock";
		}
		
		
		$sql = "insert into `{borrow_vouch}` set `addtime` = '".time()."',`addip` = '".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
		$mysql->db_query($sql);
		$vouch_id = $mysql->db_insert_id();
		
		if ($vouch_id>0){
			$sql = "update  {borrow}  set vouch_account_yes=vouch_account_yes+{$data['account']},vouch_account_wait=vouch_account_wait-{$data['account']},vouch_times=vouch_times+1,vouch_account_scale = 100*(vouch_account_yes/vouch_account)  where borrow_nid='{$data['borrow_nid']}'";
			$mysql->db_query($sql);//更新已经担保的钱
			
			//添加额度记录
			//扣除借款担保额度
			$_data["user_id"] = $data['user_id'];
			$_data["amount_type"] = "vouch_tender";
			$_data["type"] = "vouch_tender";
			$_data["oprate"] = "reduce";
			$_data["nid"] = "vouch_tender_".$data['user_id']."_".time();
			$_data["account"] = $data['account'];
			$_data["remark"] = "担保借款[{$borrow_url}]审核通过扣去借款担保额度";//type 操作的类型 
			borrowClass::AddAmountLog($_data);
			
		}
		return $vouch_id;
	}
	
	
	/**
	 * 查看投标的信息
	 *
	 * @param Array $data
	 * @return Array
	 */
	public static function BorrowRepay($data = array()){
		global $mysql,$_G;
		if (IsExiest($data['id'])==""){
			return "borrow_repay_id_empty";
		}
		if (IsExiest($data['user_id'])==""){
			return "borrow_user_id_empty";
		}
		if (IsExiest($data['borrow_nid'])==""){
			return "borrow_nid_empty";
		}
		
		$_sql = "";
		
		//第一步，获取还款的信息
		$sql = "select p1.*,p2.username from `{borrow_repay}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.id={$data['id']} and p1.user_id='{$data['user_id']}' and p1.borrow_nid='{$data['borrow_nid']}'";
		$result= $mysql->db_fetch_array($sql);
		if ($result==""){
			return "borrow_repay_id_empty";
		}
		if ($result["user_id"]!=$data["user_id"]){
			return "borrow_user_id_empty";
		}
		if ($result["status"]!=1){
			return "borrow_repay_error";
		}
		if ($result["repay_status"]==1){
			return "borrow_repay_yes";
		}
		
		$repay_id = $data["id"];
		$borrow_userid = $data["user_id"];
		$borrow_username = $result["username"];
		$borrow_nid = $result["borrow_nid"];
		$repay_web = $result["repay_web"];
		$repay_vouch = $result["repay_vouch"];
		$repay_period = $result["repay_period"];
		$repay_account = $result["repay_account"];//还款总额
		$repay_capital = $result["repay_capital"];//还款本金
		$repay_interest = $result["repay_interest"];//还款利息
		$repay_time = $result["repay_time"];//还款时间
		
		
		
		//第二步，判断上一期是否已还款
		if ($repay_period!=0){
			$_repay_period = $repay_period-1;
			$sql = "select repay_status from `{borrow_repay}` where `repay_period`=$_repay_period and borrow_nid={$borrow_nid}";
			$result = $mysql->db_fetch_array($sql);
			if ($result!=false && $result['repay_status']!=1){
				return "borrow_repay_up_notrepay";
			}
		}
		//第三步，得到借款标的信息
		$sql = "select * from `{borrow}` where borrow_nid = '{$borrow_nid}'";
		$result = $mysql->db_fetch_array($sql);
		$borrow_frost_account = $result["borrow_frost_account"];
		$borrow_name = $result['name'];
		$vouch_status = $result["vouch_status"];
		$borrow_period = $result["borrow_period"];
		$repay_times = $result["repay_times"];
		$borrow_account = $result["account"];
		$borrow_style = $result["borrow_style"];
		$borrow_url = "<a href=http://www.hcdai.com/invest/a{$result['borrow_nid']}.html target=_blank>{$result['name']}</a>";//借款的地址
		
		//第三步，检查是否逾期,并且计算逾期的费用
		$late = self::LateInterest(array("time"=>$repay_time,"account"=>$repay_account,"capital"=>$repay_capital));
		$late_days = $late['late_days'];
		$late_interest = round($repay_account/100*0.4*$late_days,2);
		$late_reminder = $late['late_reminder'];
		$late_account = $late_interest;
		
				
		//第四步，判断可用余额是否够还款
		$account_result =  accountClass::GetAccountUsers(array("user_id"=>$borrow_userid));//获取当前用户的余额;
		if ($account_result['balance']<$repay_account+$late_account){
			return "borrow_repay_account_use_none";
		}
		$log_info["user_id"] = $borrow_userid;//操作用户id
		$log_info["nid"] = "repay_".$borrow_userid."_".$borrow_nid.$repay_id;//订单号
		$log_info["money"] = $repay_account;//操作金额
		$log_info["income"] = 0;//收入
		$log_info["expend"] = $repay_account;//支出
		$log_info["balance_cash"] = 0;//可提现金额
		$log_info["balance_frost"] = -$repay_account;//不可提现金额
		$log_info["frost"] = 0;//冻结金额
		$log_info["await"] = 0;//待收金额
		$log_info["type"] = "borrow_repay";//类型
		$log_info["to_userid"] = 0;//付给谁
		$log_info["remark"] = "对[{$borrow_url}]借款标还款";
		accountClass::AddLog($log_info);
		
		//第七步，判断是否是最后的还款，是则解冻借款担保金
		if ($repay_period+1 == $borrow_period){
			if ($borrow_frost_account>0){
				//第八步，最后一起解除冻结的金额
				$log_info["user_id"] = $borrow_userid;//操作用户id
				$log_info["nid"] = "borrow_frost_repay_".$borrow_userid."_".$borrow_nid.$borrow_period;//订单号
				$log_info["money"] = $borrow_frost_account;//操作金额
				$log_info["income"] =0;//收入
				$log_info["expend"] = 0;//支出
				$log_info["balance_cash"] = $borrow_frost_account;//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
				$log_info["frost"] = -$borrow_frost_account;//冻结金额
				$log_info["await"] = 0;//待收金额
				$log_info["type"] = "borrow_frost_repay";//类型
				$log_info["to_userid"] = 0;//付给谁
				$log_info["remark"] = "对[{$borrow_url}]借款的解冻";
				accountClass::AddLog($log_info);
			}
	
			// ** 最后还款积分
			$credit_log['user_id'] = $borrow_userid;
			$credit_log['nid'] = "borrow_success";
			$credit_log['code'] = "borrow";
			$credit_log['type'] = "repay_all";
			$credit_log['addtime'] = time();
			$credit_log['article_id'] =$repay_id;
			$credit_log['value'] = round($repay_account/100);
			$credit_log['remark'] =  "借款[{$borrow_url}]还款所得积分";;
			creditClass::ActionCreditLog($credit_log);
			
			
		}
			
		if ($repay_web==1){
			$log_info["user_id"] = 0;//操作用户id
			$log_info["nid"] = "repay_web_0_".$borrow_nid.$repay_id;//订单号
			$log_info["money"] = $repay_account;//操作金额
			$log_info["income"] = 0;//收入
			$log_info["expend"] = $repay_account;//支出
			$log_info["balance_cash"] = $repay_account;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["type"] = "web_repay";//类型
			$log_info["to_userid"] = 0;//付给谁
			$log_info["remark"] = "对[{$borrow_url}]还款，网站垫付后所得收入".$borrow_username;
			accountClass::AddLog($log_info);
			
			$log_info["user_id"] = 0;//操作用户id
			$log_info["nid"] = "repay_late_web_0_".$borrow_nid.$repay_id;//订单号
			$log_info["money"] = $late_interest;//操作金额
			$log_info["income"] = 0;//收入
			$log_info["expend"] = $late_interest;//支出
			$log_info["balance_cash"] = $late_interest;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["type"] = "repay_late_web";//类型
			$log_info["to_userid"] = 0;//付给谁
			$log_info["remark"] = "网站垫付后的还款罚息收入".$borrow_username;
			accountClass::AddLog($log_info);
			
			
			$sql = "select p1.*,p2.change_status,p2.change_userid from `{borrow_recover}` as p1 left join `{borrow_tender}` as p2 on p1.tender_id=p2.id  where p1.`recover_period` = '{$repay_period}' and p1.borrow_nid='{$borrow_nid}'";
			$_recover=$mysql->db_fetch_arrays($sql);
			foreach ($_recover as $key => $value){
				$_sql = "update  `{borrow_recover}` set recover_status=1 where id = '{$value['id']}'";
				$mysql->db_query($_sql);
			}
		}
		
		// * 除了体验标外，借款管理费的扣除
		$vip_result = self::GetBorrowVip(array("user_id"=>$borrow_userid));
		$vip_fee = $vip_result['fee'];
		if ($borrow_style!=5){
			if ($vip_result['vip']==0){
				$borrow_manage_fee = isset($_G['system']["con_borrow_manage_fee"])?$_G['system']["con_borrow_manage_fee"]:0.5;
			}else{
				$borrow_manage_fee = (isset($_G['system']["con_borrow_manage_vip_fee"])?$_G['system']["con_borrow_manage_vip_fee"]:0.4)*$vip_fee;
			}
			
			$manage_fee = $repay_capital*$borrow_manage_fee*0.01;
			//用户对借款标的还款
			/*
			$log_info["user_id"] = $borrow_userid;//操作用户id
			$log_info["nid"] = "borrow_manage_fee_".$borrow_userid."_".$borrow_nid.$repay_id;//订单号
			$log_info["money"] = $manage_fee;//操作金额
			$log_info["income"] = 0;//收入
			$log_info["expend"] = $manage_fee;//支出
			$log_info["balance_cash"] = -$manage_fee;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["type"] = "borrow_manage_fee";//类型
			$log_info["to_userid"] = 0;//付给谁
			$log_info["remark"] = "用户成功还款[$borrow_url]扣除借款管理费";
			accountClass::AddLog($log_info);*/
		}
		
		// * 逾期费用扣除还款
		if ($late_interest>0){
			$log_info["user_id"] = $borrow_userid;//操作用户id
			$log_info["nid"] = "borrow_repay_late_".$borrow_userid."_".$borrow_nid.$repay_id;//订单号
			$log_info["money"] = $late_interest;//操作金额
			$log_info["income"] = 0;//收入
			$log_info["expend"] = $late_interest;//支出
			$log_info["balance_cash"] = -$late_interest;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["type"] = "borrow_repay_late";//类型
			$log_info["to_userid"] = 0;//付给谁
			$log_info["remark"] = "对[{$borrow_url}]借款第".($repay_period+1)."期的逾期金额的扣除";
			accountClass::AddLog($log_info);
		}
		if($repay_period+1 == $borrow_period){
			$credit_log['user_id'] = $borrow_userid;
			$credit_log['nid'] = "borrow_success";
			$credit_log['code'] = "borrow";
			$credit_log['type'] = "repay_all";
			$credit_log['addtime'] = time();
			$credit_log['article_id'] =$repay_id;
			$credit_log['value'] = round($borrow_account/100);
			$credit_log['remark'] =  "借款[{$borrow_url}]全部还完所得积分";
			creditClass::ActionCreditLog($credit_log);
		}
		
		// * 逾期催缴管理费还款
		/*if ($late_reminder>0){
			$log_info["user_id"] = $borrow_userid;//操作用户id
			$log_info["nid"] = "borrow_repay_reminder_".$borrow_userid."_".$borrow_nid.$repay_id;//订单号
			$log_info["money"] = $late_reminder;//操作金额
			$log_info["income"] = 0;//收入
			$log_info["expend"] = $late_reminder;//支出
			$log_info["balance_cash"] = -$late_reminder;//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["type"] = "borrow_repay_reminder";//类型
			$log_info["to_userid"] = 0;//付给谁
			$log_info["remark"] = "对[{$borrow_url}]借款第".($repay_period+1)."期的逾期催缴管理费的扣除";;
			accountClass::AddLog($log_info);
		}
		*/
			
		// * 更新逾期的信息
		$sql = "update`{borrow_repay}` set late_days = '{$late_days}',late_interest = '{$late_interest}',late_reminder = '{$late_reminder}' where id = {$repay_id}";
		$mysql->db_query($sql);
			
		//更新统计信息
		self::UpdateBorrowCount(array("user_id"=>$borrow_userid,"borrow_repay_yes_times"=>1,"borrow_repay_wait_times"=>-1,"borrow_repay_yes"=>$repay_account,"borrow_repay_wait"=>-$repay_account,"borrow_repay_interest_yes"=>$repay_interest,"borrow_repay_interest_wait"=>-$repay_interest,"borrow_repay_capital_yes"=>$repay_capital,"borrow_repay_capital_wait"=>-$repay_capital));		
		
		//第十三步，如果担保已还，就不用还给投资人
		if ($repay_vouch==1){
			$sql = "select p1.* from `{borrow_vouch_recover}` as p1  where p1.`order` = '{$repay_period}' and p1.borrow_nid='{$borrow_nid}'";
			$result = $mysql->db_fetch_arrays($sql);
			$late_rate = isset($_G['system']['con_late_rate'])?$_G['system']['con_late_rate']:0.008;
		
			foreach ($result as $key => $value){
				
				//用户对借款标的还款
				$account_result =  accountClass::GetOne(array("user_id"=>$value['user_id']));
				$log['user_id'] =$value['user_id'];
				$log['type'] = "vouch_tender_repay_yes";
				$log['money'] = $value['repay_account'];
				$log['total'] = $account_result['total']+$log['money'];
				$log['use_money'] = $account_result['use_money']+$log['money'];
				$log['no_use_money'] = $account_result['no_use_money'];
				$log['collection'] =$account_result['collection'] ;
				$log['use_money_yes'] = $account_result['use_money_yes']+$log['money'];
				$log['use_money_no'] = $account_result['use_money_no'];
				$log['to_user'] = $borrow_userid;
				$log['remark'] = "客户（{$borrow_username}）对[{$borrow_url}]借款担保垫付的还款";
				accountClass::AddLog($log);
				
				
				//扣除投资的管理费
				
				$account_result =  accountClass::GetOne(array("user_id"=>$value['user_id']));
				$log['user_id'] = $value['user_id'];
				$log['type'] = "tender_interest_fee";//
				$_fee = isset($_G['system']['con_integral_fee'])?$_G['system']['con_integral_fee']:0.1;
				if ($_fee>0 && $_fee!="0") {
					$log['money'] = $value['recover_interest']*$_fee;
					$log['total'] = $account_result['total']-$log['money'];
					$log['use_money'] = $account_result['use_money']-$log['money'];
					$log['no_use_money'] = $account_result['no_use_money'];
					$log['collection'] = $account_result['collection'];
					$log['use_money_yes'] = $account_result['use_money_yes']-$log['money'];
					$log['use_money_no'] = $account_result['use_money_no'];
					$log['to_user'] = 0;
					$log['remark'] = "用户成功还款[$borrow_url]扣除利息的管理费";
					accountClass::AddLog($log);
				}
				
				
				//提醒设置
				$remind['nid'] = "loan_pay";
				
				$remind['receive_userid'] = $value['user_id'];
				$remind['title'] = "客户({$borrow_username})对[{$borrow_name}]借款的还款";
				$remind['content'] = "客户({$borrow_username})在".date("Y-m-d H:i:s")."对[{$borrow_url}}</a>]借款的还款,还款金额为￥{$value['repay_account']}";
				
				remindClass::sendRemind($remind);
				
				if ($late_days>30){
					//担保人网站各得一半
					$account_result =  accountClass::GetOne(array("user_id"=>$value['user_id']));
					$log['user_id'] =$value['user_id'];
					$log['type'] = "vouch_repay_late_recover";
					$log['money'] = round((($value['repay_capital']*$late_rate*$late_days)/2),2);
					$log['total'] = $account_result['total']+$log['money'];
					$log['use_money'] = $account_result['use_money']+$log['money'];
					$log['no_use_money'] = $account_result['no_use_money'];
					$log['collection'] =$account_result['collection'];
					$log['use_money_yes'] = $account_result['use_money_yes']+$log['money'];
					$log['use_money_no'] = $account_result['use_money_no'];
					$log['to_user'] = $data['user_id'];
					$log['remark'] = "[{$borrow_url}]第".($repay_period+1)."期借款标逾期并少于30天的担保垫付逾期利息收入";
					accountClass::AddLog($log);
				}
			}
		}
		//第十三步，用户正常还款
		if ($repay_web!=1 && $repay_vouch!=1){
			$sql = "select p1.*,p2.change_status,p2.change_userid from `{borrow_recover}` as p1 left join `{borrow_tender}` as p2 on p1.tender_id=p2.id  where p1.`recover_period` = '{$repay_period}' and p1.borrow_nid='{$borrow_nid}'";
			$result = $mysql->db_fetch_arrays($sql);
			$re_time = (strtotime(date("Y-m-d",$repay_time))-strtotime(date("Y-m-d",time())))/(60*60*24);
			foreach ($result as $key => $value){
				//更新投资人的分期信息
				$late = self::LateInterest(array("time"=>$value['recover_time'],"capital"=>$value['recover_capital']));
				if ($late['late_days']>30){
					$late_interest = 0;
					$money=round($value['recover_account']*0.004*$late['late_days'],2);
				}else{
					$late_interest = round($value['recover_account']*0.004*$late['late_days']/2,2);
					$money=round($value['recover_account']*0.004*$late['late_days']/2,2);
				}
				$sql = "update  `{borrow_recover}` set recover_yestime='".time()."',recover_account_yes = recover_account ,recover_capital_yes = recover_capital ,recover_interest_yes = recover_interest ,status=1,recover_status=1,late_days={$late['late_days']},late_interest={$late_interest} where id = '{$value['id']}'";
				$mysql->db_query($sql);
				
				if($late['late_days']>0){
					$log_info["user_id"] = 0;//操作用户id
					$log_info["nid"] = "repay_0_".$borrow_nid.$repay_id.$value['id'];//订单号
					$log_info["money"] = $money;//操作金额
					$log_info["income"] = 0;//收入
					$log_info["expend"] = $money;//支出
					$log_info["balance_cash"] = -$money;//可提现金额
					$log_info["balance_frost"] = 0;//不可提现金额
					$log_info["frost"] = 0;//冻结金额
					$log_info["await"] = 0;//待收金额
					$log_info["type"] = "late_repay_web";//类型
					$log_info["to_userid"] = 0;//付给谁
					$log_info["remark"] = "对[{$borrow_url}]还款，网站逾期罚息收入".$borrow_username;
					accountClass::AddLog($log_info);
				}else{
					$vip=usersClass::GetUsersVip(array("user_id"=>$value['user_id']));
					if ($vip['status']==1){
						$service_fee_vip=isset($_G['system']['con_repay_interest_service_vip'])?$_G['system']['con_repay_interest_service_vip']:8;
					}else{
						$service_fee=isset($_G['system']['con_repay_interest_service'])?$_G['system']['con_repay_interest_service']:10;
					}					
					$service_fee=round($value['recover_interest']*$service_fee*0.01,2);
					$log_info["user_id"] = $value['user_id'];//操作用户id
					$log_info["nid"] = "repay_interest_service_".$value['user_id']."_".$borrow_nid.$repay_id.$value['id'];//订单号
					$log_info["money"] = $service_fee;//操作金额
					$log_info["income"] = 0;//收入
					$log_info["expend"] = $service_fee;//支出
					$log_info["balance_cash"] = -$service_fee;//可提现金额
					$log_info["balance_frost"] = 0;//不可提现金额
					$log_info["frost"] = 0;//冻结金额
					$log_info["await"] = 0;//待收金额
					$log_info["type"] = "repay_interest_service";//类型
					$log_info["to_userid"] = 0;//付给谁
					$log_info["remark"] = "[{$borrow_url}]还款，扣除投资利息服务费";
					//accountClass::AddLog($log_info);					
				}
				//更新投资的信息
				$sql = "update  `{borrow_tender}` set recover_times=recover_times+1,recover_account_yes= recover_account_yes + {$value['recover_account']},recover_account_capital_yes = recover_account_capital_yes  + {$value['recover_capital']} ,recover_account_interest_yes = recover_account_interest_yes + {$value['recover_interest']},recover_account_wait= recover_account_wait - {$value['recover_account']},recover_account_capital_wait = recover_account_capital_wait  - {$value['recover_capital']} ,recover_account_interest_wait = recover_account_interest_wait - {$value['recover_interest']}  where id = '{$value['tender_id']}'";
				$mysql->db_query($sql);
				
				if ($value['change_status']==1){
					$value['user_id'] = $value['change_userid'];
					if ($value['change_userid']==0 || $value['change_userid']==""){
						$value['user_id']=0;
					}
				}
				//用户对借款标的还款
				$log_info["user_id"] = $value['user_id'];//操作用户id
				$log_info["nid"] = "tender_repay_yes_".$value['user_id']."_".$borrow_nid.$value['id'];//订单号
				$log_info["money"] = $value['recover_account'];//操作金额
				$log_info["income"] = $value['recover_account'];//收入
				$log_info["expend"] = 0;//支出
				$log_info["balance_cash"] = $value['recover_account'];//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
				$log_info["frost"] = $value['recover_account'];;//冻结金额
				$log_info["await"] = -$value['recover_account'];//待收金额
				$log_info["type"] = "tender_repay_yes";//类型
				$log_info["to_userid"] = $borrow_userid;//付给谁
				$log_info["remark"] = "客户（{$borrow_username}）对[{$borrow_url}]借款标的还款";
				accountClass::AddLog($log_info);
				
				$vip_result = self::GetBorrowVip(array("user_id"=>$value['user_id']));
				$vip_fee = $vip_result['fee'];
				
				if ($value['user_id']!=0){
					//扣除投资的管理费
					//第十四步，扣除成功管理手续费
					$tender_fee = 0;
					$tender_fee = $value['recover_interest']*0.05;
					//用户对借款标的还款
					$log_info["user_id"] = $value['user_id'];//操作用户id
					$log_info["nid"] = "fengxianchi_".$value['user_id']."_".$borrow_nid.$value['id'];//订单号
					$log_info["money"] = $tender_fee;//操作金额
					$log_info["income"] = 0;//收入
					$log_info["expend"] = $tender_fee;//支出
					$log_info["balance_cash"] = -$tender_fee;//可提现金额
					$log_info["balance_frost"] = 0;//不可提现金额
					$log_info["frost"] = 0;//冻结金额
					$log_info["await"] = 0;//待收金额
					$log_info["type"] = "fengxianchi";//类型
					$log_info["to_userid"] = 0;//付给谁
					$log_info["remark"] = "用户成功对借款标[$borrow_url]第".($repay_period+1)."期进行还款利息风险金扣除";
					//accountClass::AddLog($log_info);
					//更新统计信息
				}
					
				if ($re_time!=0){
					if ($re_time<0){
						if ($re_time>=-3 && $re_time<=-1){
							$borrow_credit_nid = "borrow_repay_slow";
							//$tender_credit_nid = "tender_repay_slow";
						}
						elseif ($re_time>=-30 && $re_time<-3){
							$borrow_credit_nid = "borrow_repay_late_common";
							//$tender_credit_nid = "tender_repay_late_common";
						}
						elseif ($re_time>=-90 && $re_time<-30){
							$borrow_credit_nid = "borrow_repay_late_serious";
							//$tender_credit_nid = "tender_repay_late_serious";
						}
						elseif ( $re_time<-90){
							$borrow_credit_nid = "borrow_repay_late_spite";
							//$tender_credit_nid = "tender_repay_late_spite";
						}
					}else{
						if ($re_time<=15 && $re_time>=3){
							$borrow_credit_nid = "borrow_repay_advance";
							$tender_credit_nid = "tender_repay_advance";
						}elseif ($re_time>=1 && $re_time<=3){
							$borrow_credit_nid = "borrow_repay_ontime";
							$tender_credit_nid = "tender_repay_ontime";
						}
					}
				}
			
				//第十步,投资者积分
				if($tender_credit_nid!=""){
					$credit_blog['user_id'] = $value['user_id'];
					$credit_blog['nid'] = $tender_credit_nid;
					$credit_blog['code'] = "borrow";
					$credit_blog['type'] = "tender_repay";
					$credit_blog['addtime'] = time();
					$credit_blog['article_id'] =$repay_id;
					$credit_blog['remark'] = "用户还款[{$borrow_url}]第{$repay_period}期投资积分";
					creditClass::ActionCreditLog($credit_blog);
				}

								
				if($value['repay_period']+1==$borrow_period){
					$credit_blog['user_id'] = $value['user_id'];
					$credit_blog['nid'] = "borrow_success";
					$credit_blog['code'] = "borrow";
					$credit_blog['type'] = "repay_all";
					$credit_blog['addtime'] = time();
					$credit_blog['article_id'] =$repay_id;
					$credit_blog['remark'] = "收到借款[{$borrow_url}]完整本息还款积分";
					//creditClass::ActionCreditLog($credit_blog);
				}
				
				if ($late_days>0 && $late_days<31){
					if ($value['user_id']!=0){
						$log_info["user_id"] = $value['user_id'];//操作用户id
						$log_info["nid"] = "tender_late_repay_yes_".$value['user_id']."_".$borrow_nid.$value['id'];//订单号
						$log_info["money"] = $late_interest;//操作金额
						$log_info["income"] = $late_interest;//收入
						$log_info["expend"] = 0;//支出
						$log_info["balance_cash"] = $late_interest;//可提现金额
						$log_info["balance_frost"] = 0;//不可提现金额
						$log_info["frost"] = 0;//冻结金额
						$log_info["await"] = 0;//待收金额
						$log_info["type"] = "tender_late_repay_yes";//类型
						$log_info["to_userid"] = $value['user_id'];//付给谁
						$log_info["remark"] = "客户（{$borrow_username}）对[{$borrow_url}]借款逾期还款的逾期利息";
						accountClass::AddLog($log_info);
					}else{
						$log_info["user_id"] = 0;//操作用户id
						$log_info["nid"] = "web_tender_late_repay_yes_0_".$borrow_nid.$value['id'];//订单号
						$log_info["money"] = $late_interest;//操作金额
						$log_info["income"] = 0;//收入
						$log_info["expend"] = $late_interest;//支出
						$log_info["balance_cash"] = $late_interest;//可提现金额
						$log_info["balance_frost"] = 0;//不可提现金额
						$log_info["frost"] = 0;//冻结金额
						$log_info["await"] = 0;//待收金额
						$log_info["type"] = "web_tender_late_repay_yes";//类型
						$log_info["to_userid"] = 0;//付给谁
						$log_info["remark"] = "借款人对[{$borrow_url}]借款逾期还款的逾期利息{$borrow_username}";
						accountClass::AddLog($log_info);
					}
				}
				
				if ($value['change_status']!=1){
					self::UpdateBorrowCount(array("user_id"=>$value['user_id'],"tender_recover_times_yes"=>1,"tender_recover_times_wait"=>-1,"tender_recover_yes"=>$value['recover_account'],"tender_recover_wait"=>-$value['recover_account'],"tender_capital_yes"=>$value['recover_capital'],"tender_capital_wait"=>-$value['recover_capital'],"tender_interest_yes"=>$value['recover_interest'],"tender_interest_wait"=>-$value['recover_interest'],"fee_account"=>$tender_fee,"fee_tender_account"=>$tender_fee));
				}else{
					self::UpdateBorrowCount(array("user_id"=>$value['user_id'],"tender_interest_yes"=>$value['recover_interest']));
				}	
				
				//提醒设置
				$remind['nid'] = "loan_pay";
				
				$remind['receive_userid'] = $value['user_id'];
				$remind['title'] = "客户（{$borrow_username}）对[{$borrow_name}]借款的还款";
				$remind['content'] = "客户（{$borrow_username}）在".date("Y-m-d H:i:s")."对[{$borrow_url}}</a>]借款的还款,还款金额为￥{$value['recover_account']}";
				
				remindClass::sendRemind($remind);
			}
		}
		
		
		//第六步，更新借款人的还款积分,判断还款时间。
		$re_time = (strtotime(date("Y-m-d",$repay_time))-strtotime(date("Y-m-d",time())))/(60*60*24);
		$borrow_credit_nid = "";
		$tender_credit_nid = "";
		if ($re_time!=0){
			if ($re_time<0){
				if ($re_time>=-3 && $re_time<=-1){
					$borrow_credit_nid = "borrow_repay_slow";
					//$tender_credit_nid = "tender_repay_slow";
				}
				elseif ($re_time>=-30 && $re_time<-3){
					$borrow_credit_nid = "borrow_repay_late_common";
					//$tender_credit_nid = "tender_repay_late_common";
				}
				elseif ($re_time>=-90 && $re_time<-30){
					$borrow_credit_nid = "borrow_repay_late_serious";
					//$tender_credit_nid = "tender_repay_late_serious";
				}
				elseif ( $re_time<-90){
					$borrow_credit_nid = "borrow_repay_late_spite";
					//$tender_credit_nid = "tender_repay_late_spite";
				}
			}else{
				if ($re_time<=15 && $re_time>=3){
					$borrow_credit_nid = "borrow_repay_advance";
				}elseif ($re_time>=1 && $re_time<=3){
					$borrow_credit_nid = "borrow_repay_ontime";
				}
			}
			
			//第十步,借款者积分
			if($borrow_credit_nid!=""){
				$credit_blog['user_id'] = $borrow_userid;
				$credit_blog['nid'] = $borrow_credit_nid;
				$credit_blog['code'] = "approve";
				$credit_blog['type'] = "repay";
				$credit_blog['addtime'] = time();
				$credit_blog['article_id'] =$repay_id;
				$credit_blog['remark'] = "还款[{$borrow_url}]第{$repay_period}期积分";
				creditClass::ActionCreditLog($credit_blog);
			}
			
		}
		
		//判断是否是担保标
		if ($vouch_status=="1"){
		
			//第十步，投资人投资担保额度的增加
			$sql = "select * from `{borrow_vouch_recover}` where borrow_nid='{$borrow_nid}' and `order`={$repay_period}";
			$result = $mysql->db_fetch_arrays($sql);
			
			if ($result!=""){
				foreach ($result as $key => $value){
					//添加额度记录
					//第十二步，信用投资额度的增加
					$_data["user_id"] = $value['user_id'];
					$_data["amount_type"] = "vouch_tender";
					$_data["type"] = "borrrow_vouch_recover";
					$_data["oprate"] = "add";
					$_data["nid"] = "borrrow_vouch_recover_".$value['user_id']."_".$borrow_nid.$value['id'];
					$_data["account"] = $value['repay_capital'];
					$_data["remark"] =  "担保标[{$borrow_url}]还款成功，投资担保额度返还";
					borrowClass::AddAmountLog($_data);
					
					$sql = "update `{borrow_vouch_recover}` set repay_yestime = ".time().",repay_yesaccount = {$value['repay_account']},status=1 where id = {$value['id']}";
					$mysql->db_query($sql);
					
				}
			}
			
			//第十一步，借款人担保额度的增加
			$sql = "select * from `{borrow_vouch_repay}` where borrow_nid='{$borrow_nid}' and `order`={$repay_period}";
			$result = $mysql->db_fetch_array($sql);
			if ($result!=""){
				$_data["user_id"] = $borrow_userid;
				$_data["amount_type"] = "vouch_borrow";
				$_data["type"] = "borrrow_vouch_repay";
				$_data["oprate"] = "add";
				$_data["nid"] = "borrrow_vouch_repay_".$value['user_id']."_".$borrow_nid.$repay_period;
				$_data["account"] = $value['repay_capital'];
				$_data["remark"] =   "担保[{$borrow_url}]还款完成，借款担保额度返回";
				borrowClass::AddAmountLog($_data);
			}
			$sql = "update `{borrow_vouch_repay}` set repay_yestime = ".time().",repay_yesaccount = {$value['repay_account']},status=1 where id = {$result['id']}";
			$mysql->db_query($sql);
		}else{
			//第十二步，信用投资额度的增加
			$_data["user_id"] = $borrow_userid;
			$_data["amount_type"] = "borrow";
			$_data["type"] = "borrrow_repay";
			$_data["oprate"] = "add";
			$_data["nid"] = "borrrow_repay_".$borrow_userid."_".$borrow_nid.$repay_id;
			$_data["account"] = $repay_capital;
			$_data["remark"] = "借款标[{$borrow_url}]成功还款，额度增加";
			borrowClass::AddAmountLog($_data);
		}
	
		
		
		//添加最后的还款金额
		$sql = "update `{borrow}` set repay_account_yes= repay_account_yes + {$repay_account},repay_account_capital_yes= repay_account_capital_yes + {$repay_capital},repay_account_interest_yes= repay_account_interest_yes + {$repay_interest},repay_account_wait= repay_account_wait - {$repay_account},repay_account_capital_wait= repay_account_capital_wait - {$repay_capital},repay_account_interest_wait= repay_account_interest_wait - {$repay_interest} where borrow_nid='{$borrow_nid}'";
		$result = $mysql -> db_query($sql);
		
		
		$sql = "update `{borrow_repay}` set repay_status=1,repay_yestime='".time()."',repay_account_yes=repay_account,repay_interest_yes=repay_interest,repay_capital_yes=repay_capital where id='{$repay_id}'";
		$mysql->db_query($sql);
		return $result;
	}
	
	/**
	 * 担保列表
	 *
	 * @return Array
	 */
	function GetVouchList($data = array()){
		global $mysql;
		$user_id = empty($data['user_id'])?"":$data['user_id'];
		$username = empty($data['username'])?"":$data['username'];
	
		$_sql = "where 1=1";		 
		if (IsExiest($data['user_id'])!=""){
			$_sql .= " and p1.user_id = {$data['user_id']}";
		}
		if (IsExiest($data['username'])!=""){
			$_sql .= " and p2.username = '{$data['user_id']}'";
		}
		if (IsExiest($data['borrow_nid'])!=""){
			$_sql .= " and p1.borrow_nid = '{$data['borrow_nid']}'";
		}
	
		if (IsExiest($data['dotime1'])!=""){
			$dotime1 = ($data['dotime1']=="request")?$_REQUEST['dotime1']:$data['dotime1'];
			if ($dotime1!=""){
				$_sql .= " and p1.addtime > ".get_mktime($dotime1);
			}
		}
		if (IsExiest($data['dotime2'])!=""){
			$dotime2 = ($data['dotime2']=="request")?$_REQUEST['dotime2']:$data['dotime2'];
			if ($dotime2!=""){
				$_sql .= " and p1.addtime < ".get_mktime($dotime2);
			}
		}
		if (IsExiest($data['status'])!=""){
			$_sql .= " and p1.status in ({$data['status']})";
		}
		if (IsExiest($data['borrow_status']) !=""){
			$_sql .= " and p3.status in ({$data['borrow_status']})";
		}
		
	
		$_select = "p1.*,p2.username,p3.name as borrow_name,p3.borrow_period,p3.account as borrow_account,p4.username as borrow_username";
		$sql = "select SELECT from `{borrow_vouch}` as p1
				left join `{users}` as p2 on p2.user_id = p1.user_id
				left join `{borrow}` as p3 on p1.borrow_nid = p3.borrow_nid
				left join `{users}` as p4 on p4.user_id = p3.user_id
		 {$_sql}  order by p1.addtime desc LIMIT";
				
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
	
	function GetVouchRepayList($data = array()){
		global $mysql;
	
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
		
		$_sql = " where p1.borrow_nid=p2.borrow_nid and p2.user_id=p3.user_id ";
		if (IsExiest($data['borrow_nid'])!=""){
			if ($data['borrow_nid'] == "request"){
				$_sql .= " and p1.borrow_nid= '{$_REQUEST['borrow_nid']}'";
			}else{
				$_sql .= " and p1.borrow_nid= '{$data['borrow_nid']}'";
			}
		}
		
		if (IsExiest($data['user_id'])!=""){
			$_sql .= " and p2.user_id = '{$data['user_id']}'";
		}	 
		
		if (IsExiest($data['vouch_userid']) !=""){
			$_sql .= " and p2.borrow_nid in (select borrow_nid from `{borrow_vouch}` where user_id={$data['vouch_userid']})";
		}	 
		
		if (IsExiest($data['username'])!=""){
			$_sql .= " and p3.username like '%{$data['username']}%'";
		}	 
		
		if (IsExiest($data['repay_time'])!=""){
			if ($date['repay_time']<=0) $data['repay_time'] = time();
			$_repayment_time = get_mktime(date("Y-m-d",$data['repay_time']));
			$_sql .= " and p1.repay_time < '{$_repayment_time}'";
		}	 
		
		if (IsExiest($data['dotime2'])!=""){
			$dotime2 = ($data['dotime2']=="request")?$_REQUEST['dotime2']:$data['dotime2'];
			if ($dotime2!=""){
				$_sql .= " and p2.addtime < ".get_mktime($dotime2);
			}
		}
		if (IsExiest($data['dotime1'])!=""){
			$dotime1 = ($data['dotime1']=="request")?$_REQUEST['dotime1']:$data['dotime1'];
			if ($dotime1!=""){
				$_sql .= " and p2.addtime > ".get_mktime($dotime1);
			}
		}
		
		if (IsExiest($data['status'])!=""){
			$_sql .= " and p1.status in ({$data['status']})";
		}
		
		if (IsExiest($keywords)!=""){
		    if ($keywords=="request"){
				if (isset($_REQUEST['keywords']) && $_REQUEST['keywords']!=""){
					$_sql .= " and p2.name like '%".urldecode($_REQUEST['keywords'])."%'";
				}
			}else{
				$_sql .= " and p2.name like '%".$keywords."%'";
			}
			
		}
		
		$_order = " order by p1.id desc";
		if (isset($data['order']) && $data['order']!="" ){
			if ($data['order'] == "repayment_time"){
				$_order = " order by p1.repay_time asc ";
			}elseif ($data['order'] == "order"){
				$_order = " order by p1.order asc ,p1.id desc";
			}
		}
		
		$_select = " p1.*,p2.name as borrow_name,p2.borrow_period,p3.username as borrow_username";
		$sql = "select SELECT from `{borrow_vouch_repay}` as p1 left join `{borrow}` as p2 on p1.borrow_nid = p2.borrow_nid left join `{users}` as p3 on p3.user_id=p2.user_id {$_sql} ORDER LIMIT";
		
		//是否显示全部的信息
		if (isset($data['limit']) ){
			$_limit = "";
			if ($data['limit'] != "all"){
				$_limit = "  limit ".$data['limit'];
			}
			$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,  $_order, $_limit), $sql));
			foreach ($list as $key => $value){
				$late = self::LateInterest(array("time"=>$value['repay_time'],"account"=>$value['capital']));
				if ($value['status']!=1){
					$list[$key]['late_days'] = $late['late_days'];
					$list[$key]['late_interest'] = $late['late_interest'];
				}
			}
			return $list;
		}			 
		
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num', '', ''), $sql));
		
		$total = $row['num'];
		$total_page = ceil($total / $epage);
		$index = $epage * ($page - 1);
		$limit = " limit {$index}, {$epage}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,$_order, $limit), $sql));		
		$list = $list?$list:array();
		foreach ($list as $key => $value){
			$late = self::LateInterest(array("time"=>$value['repay_time'],"account"=>$value['capital']));
			if ($value['status']!=1){
				$list[$key]['late_days'] = $late['late_days'];
				$list[$key]['late_interest'] = $late['late_interest'];
			}
		}
		return array(
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'epage' => $epage,
            'total_page' => $total_page
        );
		
	}
	
	
	function GetVouchRecoverList($data = array()){
		global $mysql;
	
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
		
		$_sql = " where p1.borrow_nid=p2.borrow_nid and p2.user_id=p3.user_id ";
		if (IsExiest($data['borrow_nid'])!=""){
			if ($data['borrow_nid'] == "request"){
				$_sql .= " and p1.borrow_nid= '{$_REQUEST['borrow_nid']}'";
			}else{
				$_sql .= " and p1.borrow_nid= '{$data['borrow_nid']}'";
			}
		}
		
		if (IsExiest($data['user_id'])!=""){
			$_sql .= " and p2.user_id = '{$data['user_id']}'";
		}	 
		
		if (IsExiest($data['vouch_userid']) !=""){
			$_sql .= " and p1.user_id = '{$data['vouch_userid']}'";
		}	 
		
		if (IsExiest($data['username'])!=""){
			$_sql .= " and p3.username like '%{$data['username']}%'";
		}	 
		
		if (IsExiest($data['type'])=="late"){
			$_sql .= " and p1.repay_time<".time() ." and p1.status=0";
		}	 
		
		if (IsExiest($data['repay_time'])!=""){
			if ($date['repay_time']<=0) $data['repay_time'] = time();
			$_repayment_time = get_mktime(date("Y-m-d",$data['repay_time']));
			$_sql .= " and p1.repay_time < '{$_repayment_time}'";
		}	 
		
		if (IsExiest($data['dotime2'])!=""){
			$dotime2 = ($data['dotime2']=="request")?$_REQUEST['dotime2']:$data['dotime2'];
			if ($dotime2!=""){
				$_sql .= " and p2.addtime < ".get_mktime($dotime2);
			}
		}
		if (IsExiest($data['dotime1'])!=""){
			$dotime1 = ($data['dotime1']=="request")?$_REQUEST['dotime1']:$data['dotime1'];
			if ($dotime1!=""){
				$_sql .= " and p2.addtime > ".get_mktime($dotime1);
			}
		}
		
		if (IsExiest($data['status'])!=""){
			$_sql .= " and p1.status in ({$data['status']})";
		}
		
		if (IsExiest($keywords)!=""){
		    if ($keywords=="request"){
				if (isset($_REQUEST['keywords']) && $_REQUEST['keywords']!=""){
					$_sql .= " and p2.name like '%".urldecode($_REQUEST['keywords'])."%'";
				}
			}else{
				$_sql .= " and p2.name like '%".$keywords."%'";
			}
			
		}
		
		$_order = " order by p1.id desc";
		if (isset($data['order']) && $data['order']!="" ){
			if ($data['order'] == "repayment_time"){
				$_order = " order by p1.repay_time asc ";
			}elseif ($data['order'] == "order"){
				$_order = " order by p1.order asc ,p1.id desc";
			}
		}
		
		$_select = " p1.*,p2.name as borrow_name,p2.borrow_period,p3.username as borrow_username";
		$sql = "select SELECT from `{borrow_vouch_recover}` as p1 left join `{borrow}` as p2 on p1.borrow_nid = p2.borrow_nid left join `{users}` as p3 on p3.user_id=p2.user_id {$_sql} ORDER LIMIT";
		
		//是否显示全部的信息
		if (isset($data['limit']) ){
			$_limit = "";
			if ($data['limit'] != "all"){
				$_limit = "  limit ".$data['limit'];
			}
			$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,  $_order, $_limit), $sql));
			
			foreach ($list as $key => $value){
				$late = self::LateInterest(array("time"=>$value['repay_time'],"account"=>$value['reapy_account']));
				if ($value['status']!=1){
					$list[$key]['late_days'] = $late['late_days'];
					$list[$key]['late_interest'] = $late['late_interest'];
				}
			}
			return $list;
		}			 
		
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num', '', ''), $sql));
		
		$total = $row['num'];
		$total_page = ceil($total / $epage);
		$index = $epage * ($page - 1);
		$limit = " limit {$index}, {$epage}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,$_order, $limit), $sql));		
		$list = $list?$list:array();
		foreach ($list as $key => $value){
			$late = self::LateInterest(array("time"=>$value['repay_time'],"account"=>$value['capital']));
			if ($value['status']!=1){
				$list[$key]['late_days'] = $late['late_days'];
				$list[$key]['late_interest'] = $late['late_interest'];
			}
		}
		return array(
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'epage' => $epage,
            'total_page' => $total_page
        );
		
	}
	
	
	//担保垫付
	function VouchDianfu($data = array()){
		global $mysql;
		$sql = "select p1.*,p2.name as borrow_name from `{borrow_vouch_recover}` as p1 left join `{borrow}` as p2 on p1.borrow_nid = p2.borrow_nid where p1.user_id='{$data['user_id']}' and  p1.id='{$data['id']}' and p1.repay_time< ".time()."";
		$result = $mysql->db_fetch_array($sql);
		
		//第一步，判断担保信息是否存在
		if ($result==false){
			return "error";
		}
		//第二步，判断担保是否逾期30天
		$late = self::LateInterest(array("time"=>$result['repay_time'],"account"=>$result['repay_account']));
		if ($late["late_days"]<10){
			return "vouch_late_days_30no";
		}
		
		$borrow_nid = $result["borrow_nid"];
		$borrow_name = $result["borrow_name"];
		$repay_period = $result["order"];
		$borrow_period = $result["borrow_period"];
		$borrow_url = "<a href={$_G['weburl']}/invest/a{$result['borrow_nid']}.html target=_blank>{$result['borrow_name']}</a>";
		
		
		//第三步，更新担保信息垫付信息为1
		$sql = "update `{borrow_vouch_recover}` set advance_status =1,advance_time='".time()."' where id='{$data['id']}'";
		$mysql->db_query($sql);
	
		//第四步，判断是否已经垫付了
		$sql = "select * from `{borrow_repay}` where borrow_nid = '{$borrow_nid}' and repay_period='{$repay_period}'";
		$result = $mysql->db_fetch_array($sql);
		
		if ($result["repay_vouch"]!=1 && $result["repay_status"]!=1){
			
			//第六步，判断已经全部都担保垫付完毕
			$sql = "select * from `{borrow_vouch_recover}` where borrow_nid = '{$borrow_nid}' and `order`='{$repay_period}' and advance_status=0";
			$result = $mysql->db_fetch_array($sql);
			
			if ($result==false || $result==""){
				//第五步，更新还款者的担保待还信息。
				$sql = "update `{borrow_repay}` set repay_vouch=1,repay_vouch_time='".time()."' where borrow_nid='{$borrow_nid}' and repay_period='{$repay_period}'";
				$mysql->db_query($sql);
			
				$sql = "select p1.*,p2.status as vip_status from `{borrow_recover}` as p1 left join `{users_vip}` as p2 on p1.user_id=p2.user_id  where p1.`recover_period` = '{$repay_period}' and p1.borrow_nid='{$borrow_nid}'";
				$result = $mysql->db_fetch_arrays($sql);
				
				foreach ($result as $key => $value){
				
					//第七步，更新投资人的分期信息
					$sql = "update  `{borrow_recover}` set recover_yestime='".time()."',recover_account_yes = recover_account ,recover_capital_yes = recover_capital ,recover_interest_yes = recover_interest ,status=1,recover_status=1,recover_vouch=1   where id = '{$value['id']}'";
					$mysql->db_query($sql);
					
					//第八步，更新投资人的信息的信息
					$sql = "update  `{borrow_tender}` set recover_times=recover_times+1,recover_account_yes= recover_account_yes + {$value['recover_account']},recover_account_capital_yes = recover_account_capital_yes  + {$value['recover_capital']} ,recover_account_interest_yes = recover_account_interest_yes + {$value['recover_interest']},recover_account_wait= recover_account_wait - {$value['recover_account']},recover_account_capital_wait = recover_account_capital_wait  - {$value['recover_capital']} ,recover_account_interest_wait = recover_account_interest_wait - {$value['recover_interest']}  where id = '{$value['tender_id']}'";
					$mysql->db_query($sql);
					
					//第九步，担保者对借款标的还款
					$account_result =  accountClass::GetOne(array("user_id"=>$value['user_id']));
					$log['user_id'] =$value['user_id'];
					$log['type'] = "vouch_recover_yes";
					if($value['vip_status']==1){
						$log['money'] = $value['recover_account'];
					}else{
						$log['money'] = round($value['recover_capital']/2,2);
					}
					
					$log['total'] = $account_result['total'];
					$log['use_money'] = $account_result['use_money']+$log['money'];
					$log['no_use_money'] = $account_result['no_use_money'];
					$log['collection'] =$account_result['collection'] -$log['money'];
					$log['use_money_yes'] = $account_result['use_money_yes']+$log['money'];
					$log['use_money_no'] = $account_result['use_money_no'];
					$log['to_user'] = $borrow_userid;
					$log['remark'] = "担保者对[{$borrow_url}]借款的垫付";
					$result = accountClass::AddLog($log);
					
					
					//第十步，扣除投资的管理费
				
					$account_result =  accountClass::GetOne(array("user_id"=>$value['user_id']));
					$log['user_id'] = $value['user_id'];
					$log['type'] = "tender_interest_fee";//
					$_fee = isset($_G['system']['con_integral_fee'])?$_G['system']['con_integral_fee']:0.1;
					if ($_fee>0 && $_fee!="0") {
						$log['money'] = $value['recover_interest']*$_fee;
						$log['total'] = $account_result['total']-$log['money'];
						$log['use_money'] = $account_result['use_money']-$log['money'];
						$log['no_use_money'] = $account_result['no_use_money'];
						$log['collection'] = $account_result['collection'];
						$log['use_money_yes'] = $account_result['use_money_yes']-$log['money'];
						$log['use_money_no'] = $account_result['use_money_no'];
						$log['to_user'] = 0;
						$log['remark'] = "担保者成功还垫付$borrow_url]扣除利息的管理费";
						accountClass::AddLog($log);
					}
					//提醒设置
					$remind['nid'] = "loan_pay";
					
					$remind['receive_userid'] = $value['user_id'];
					$remind['title'] = "担保者对[{$borrow_name}]借款的还款";
					$remind['content'] = "担保者在".date("Y-m-d H:i:s")."对[{$borrow_url}}</a>]借款的还款,还款金额为￥{$value['recover_account']}";
					
					//remindClass::sendRemind($remind);
					
				}
				//第十一步，扣除担保人的可用金额
				$sql = "select * from `{borrow_vouch_recover}` where borrow_nid = '{$borrow_nid}' and `order`='{$repay_period}' ";
				$result = $mysql->db_fetch_arrays($sql);
				
				foreach ($result as $key => $value){
					
					//第八步，更新投资人的分期信息
					//用户对借款标的还款
					$account_result =  accountClass::GetOne(array("user_id"=>$value['user_id']));
					$log['user_id'] =$value['user_id'];
					$log['type'] = "vouch_repay_yes";
					$log['money'] = $value['repay_account'];
					$log['total'] = $account_result['total'] -$log['money'];
					$log['use_money'] = $account_result['use_money']-$log['money'];
					$log['no_use_money'] = $account_result['no_use_money'];
					$log['collection'] =$account_result['collection'];
					$log['use_money_yes'] = $account_result['use_money_yes'];
					$log['use_money_no'] = $account_result['use_money_no']-$log['money'];
					$log['to_user'] = $vouch_userid;
					$log['remark'] = "对[{$borrow_url}]借款的垫付金额的扣除";
					accountClass::AddLog($log);
					
					
					//提醒设置
					$remind['nid'] = "loan_pay";
					
					$remind['receive_userid'] = $value['user_id'];
					$remind['title'] = "担保者对[{$borrow_name}]借款的垫付金额的扣除";
					$remind['content'] = "担保者在".date("Y-m-d H:i:s")."对[{$borrow_url}}</a>]借款的还款,垫付金额为￥{$value['repay_account']}";
					
					//remindClass::sendRemind($remind);
					
				}
			}
		}
		return true;
	}
	
	public static function BorrowAdvanceRepay($data = array()){
		global $mysql,$_G;
		
		if (IsExiest($data['user_id'])==""){
			return "borrow_user_id_empty";
		}
		
		if (IsExiest($data['borrow_nid'])==""){
			return "borrow_nid_empty";
		}
		
		$sql = "select count(1) as num,sum(repay_account) as all_account,sum(repay_capital) as all_capital,sum(repay_interest) as all_interest,user_id from `{borrow_repay}` where user_id='{$data['user_id']}' and borrow_nid='{$data['borrow_nid']}' and repay_status=0";
		$result= $mysql->db_fetch_array($sql);
		
		$borrow_userid = $data["user_id"];
		$borrow_username = $result["username"];
		$borrow_nid = $data["borrow_nid"];
		$repay_period = $result["num"];
		$repay_account = $result["all_account"];//还款总额
		$repay_capital = $result["all_capital"];//还款本金
		$repay_interest = $result["all_interest"];//还款利息
		
		$sql = "select * from `{borrow}` where borrow_nid = '{$borrow_nid}'";
		$result = $mysql->db_fetch_array($sql);
		$borrow_forst_account = $result["borrow_forst_account"];
		$borrow_name = $result['name'];
		$borrow_period = $result["borrow_period"];
		$repay_times = $result["repay_times"];
		$borrow_account = $result["account"];
		$borrow_style = $result["borrow_style"];
		$borrow_url = "<a href=http://www.hcdai.com/invest/a{$result['borrow_nid']}.html target=_blank>{$result['name']}</a>";//借款的地址
				
		//第四步，判断可用余额是否够还款
		$account_result =  accountClass::GetAccountUsers(array("user_id"=>$borrow_userid));//获取当前用户的余额;
		if ($account_result['balance']<$repay_account){
			return "borrow_repay_account_use_none";
		}
		$log_info["user_id"] = $borrow_userid;//操作用户id
		$log_info["nid"] = "advance_repay_".$borrow_userid."_".$borrow_nid;//订单号
		$log_info["money"] = $repay_capital;//操作金额
		$log_info["income"] = 0;//收入
		$log_info["expend"] = $repay_capital;//支出
		$log_info["balance_cash"] = -$repay_capital;//可提现金额
		$log_info["balance_frost"] = 0;//不可提现金额
		$log_info["frost"] = 0;//冻结金额
		$log_info["await"] = 0;//待收金额
		$log_info["type"] = "borrow_advance_repay";//类型
		$log_info["to_userid"] = 0;//付给谁
		$log_info["remark"] = "对[{$borrow_url}]提前全额还款";
		accountClass::AddLog($log_info);
		
		$log_info["user_id"] = $borrow_userid;//操作用户id
		$log_info["nid"] = "advance_interest_repay_".$borrow_userid."_".$borrow_nid;//订单号
		$log_info["money"] = round($repay_capital/100,2);//操作金额
		$log_info["income"] = 0;//收入
		$log_info["expend"] = $log_info["money"];//支出
		$log_info["balance_cash"] = -$log_info["money"];//可提现金额
		$log_info["balance_frost"] = 0;//不可提现金额
		$log_info["frost"] = 0;//冻结金额
		$log_info["await"] = 0;//待收金额
		$log_info["type"] = "borrow_interest_advance_repay";//类型
		$log_info["to_userid"] = 0;//付给谁
		$log_info["remark"] = "对[{$borrow_url}]提前全额还款,扣除本金1%的违约金";
		accountClass::AddLog($log_info);
		
		//第十二步，增加借款推广人金额
		//获取投资人的独立投资推广人
		$spread_sql="select * from `{spread_user}` where spread_userid={$borrow_userid} and style=1 and type=3";
		$result_spread=$mysql->db_fetch_array($spread_sql);
		
		if ($result_spread==true){
			//获取独立投资推广人的提成利率
			$feesql="select `task_fee` from `{spread_setting}` where type=4";
			$fee_result=$mysql->db_fetch_array($feesql);
			
			$log_info["user_id"] = $result_spread['user_id'];//推广员
			$log_info["nid"] = "borrow_spread_".$borrow_nid.$borrow_userid.$result_spread['user_id'];//订单号
			$log_info["money"] = $repay_capital/100*$fee_result['task_fee'];//操作金额
			$log_info["income"] = $log_info["money"];//收入
			$log_info["expend"] = 0;//支出
			$log_info["balance_cash"] = $log_info["money"];//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = 0;//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["type"] = "borrow_spread";//类型
			$log_info["to_userid"] = $result_spread['user_id'];//付给谁
			$log_info["remark"] = "借款推广客户[{$borrow_username}]借款[{$borrow_url}]成功所得的推广提成，借款金额{$borrow_account}，提成率{$fee_result['task_fee']}%";
			accountClass::AddLog($log_info);
		}
		
		// * 除了体验标外，借款管理费的扣除
		$vip_result = self::GetBorrowVip(array("user_id"=>$borrow_userid));
		$vip_fee = $vip_result['fee'];
		if ($borrow_style!=5){
			if ($vip_result['vip']==0){
				$borrow_manage_fee = isset($_G['system']["con_borrow_manage_fee"])?$_G['system']["con_borrow_manage_fee"]:0.5;
			}else{
				$borrow_manage_fee = (isset($_G['system']["con_borrow_manage_vip_fee"])?$_G['system']["con_borrow_manage_vip_fee"]:0.4)*$vip_fee;
			}
			$manage_fee = $repay_capital*$borrow_manage_fee*0.01;
		}
			
		// * 更新逾期的信息
		$sql = "update `{borrow_repay}` set late_days = '0',late_interest = '0',late_reminder = '0' where user_id='{$data['user_id']}' and borrow_nid='{$data['borrow_nid']}' and repay_status=0";
		$mysql->db_query($sql);
		
		$all_account=round($repay_capital/100+$repay_capital,2);
		
		//更新统计信息
		self::UpdateBorrowCount(array("user_id"=>$borrow_userid,"advance_repay_times"=>$repay_period,"borrow_repay_wait_times"=>-$repay_period,"borrow_repay_yes"=>$all_account,"borrow_repay_wait"=>-$repay_account,"borrow_repay_interest_yes"=>$repay_interest,"borrow_repay_interest_wait"=>-$repay_interest,"borrow_repay_capital_yes"=>$repay_capital,"borrow_repay_capital_wait"=>-$repay_capital,"borrow_weiyue"=>$log_info["money"]));		

		$sql = "select p1.*,p2.change_status,p2.change_userid from `{borrow_recover}` as p1 left join `{borrow_tender}` as p2 on p1.tender_id=p2.id  where p1.borrow_nid='{$borrow_nid}' and p1.recover_status=0";
		$result = $mysql->db_fetch_arrays($sql);
		foreach ($result as $key => $value){
			$lixi=round($value['recover_capital']/100,2);
			$all=round($value['recover_capital']/100+$value['recover_capital'],2);
			
			$sql = "update  `{borrow_recover}` set recover_yestime='".time()."',recover_account_yes = {$value['recover_capital']} ,recover_capital_yes = recover_capital ,recover_interest_yes =0 ,status=1,recover_status=1,advance_status=1 where id = '{$value['id']}'";
			$mysql->db_query($sql);
			
			
			//更新投资的信息
			$sql = "update  `{borrow_tender}` set recover_times=recover_times+1,recover_account_yes= recover_account_yes + {$value['recover_capital']},recover_account_capital_yes = recover_account_capital_yes  + {$value['recover_capital']} ,recover_account_interest_yes = recover_account_interest_yes,recover_account_wait= recover_account_wait - {$value['recover_account']},recover_account_capital_wait = recover_account_capital_wait  - {$value['recover_capital']} ,recover_account_interest_wait = recover_account_interest_wait - {$value['recover_interest']}  where id = '{$value['tender_id']}'";
			$mysql->db_query($sql);
			
			if ($value['change_status']==1){
				$value['user_id'] = $value['change_userid'];
				if ($value['change_userid']=="" || $value['change_userid']==0){
					$value['user_id']=0;
				}
			}
			if ($value['user_id']!=0){
				//用户对借款标的还款
				$log_info["user_id"] = $value['user_id'];//操作用户id
				$log_info["nid"] = "tender_advance_repay_yes_".$value['user_id']."_".$borrow_nid.$value['id'];//订单号
				$log_info["money"] = $value['recover_capital'];//操作金额
				$log_info["income"] = $value['recover_capital'];//收入
				$log_info["expend"] = 0;//支出
				$log_info["balance_cash"] = $value['recover_capital'];//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
				$log_info["frost"] = 0;//冻结金额
				$log_info["await"] = -$value['recover_account'];//待收金额
				$log_info["type"] = "tender_advance_repay_yes";//类型
				$log_info["to_userid"] = $borrow_userid;//付给谁
				$log_info["remark"] = "借款人对[{$borrow_url}]借款的提前还款,本金回收";
				accountClass::AddLog($log_info);
				
				//用户对借款标的还款
				$log_info["user_id"] = $value['user_id'];//操作用户id
				$log_info["nid"] = "tender_advance_repay_interest_".$value['user_id']."_".$borrow_nid.$value['id'];//订单号
				$log_info["money"] = round($value['recover_capital']/100,2);//操作金额
				$log_info["income"] = $log_info["money"];//收入
				$log_info["expend"] = 0;//支出
				$log_info["balance_cash"] = $log_info["money"];//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
				$log_info["frost"] = 0;//冻结金额
				$log_info["await"] = 0;//待收金额
				$log_info["type"] = "tender_advance_repay_interest";//类型
				$log_info["to_userid"] = $borrow_userid;//付给谁
				$log_info["remark"] = "[{$borrow_url}]借款提前还款收取本金1%的违约金。";
				accountClass::AddLog($log_info);
				
				if ($value['change_status']!=1){
					self::UpdateBorrowCount(array("user_id"=>$value['user_id'],"tender_recover_times_yes"=>1,"tender_recover_times_wait"=>-1,"tender_recover_yes"=>$all,"tender_recover_wait"=>-$value['recover_account'],"tender_capital_yes"=>$value['recover_capital'],"tender_capital_wait"=>-$value['recover_capital'],"tender_interest_yes"=>0,"tender_interest_wait"=>-$value['recover_interest'],"weiyue"=>$lixi));
				}else{
					self::UpdateBorrowCount(array("user_id"=>$value['user_id'],"weiyue"=>$lixi));
				}
				
				//提醒设置
				$remind['nid'] = "loan_pay";
				$remind['receive_userid'] = $value['user_id'];
				$remind['title'] = "借款人对[{$borrow_name}]借款的提前还款";
				$remind['content'] = "客户（{$borrow_username}）在".date("Y-m-d H:i:s")."对[{$borrow_url}}</a>]借款的还款,还款金额为￥{$value['recover_account']}";
				remindClass::sendRemind($remind);
				
			}else{
				$log_info["user_id"] = 0;//操作用户id
				$log_info["nid"] = "advance_repay_0_".$borrow_nid.$value['id'];//订单号
				$log_info["money"] = $lixi;//操作金额
				$log_info["income"] = 0;//收入
				$log_info["expend"] = $lixi;//支出
				$log_info["balance_cash"] = -$lixi;//可提现金额
				$log_info["balance_frost"] = 0;//不可提现金额
				$log_info["frost"] = 0;//冻结金额
				$log_info["await"] = 0;//待收金额
				$log_info["type"] = "advance_web";//类型
				$log_info["to_userid"] = 0;//付给谁
				$log_info["remark"] = "对[{$borrow_url}]还款，网站违约金收入".$borrow_username;
				accountClass::AddLog($log_info);
			}
		}
			
		//最后还款积分
		$credit_log['user_id'] = $borrow_userid;
		$credit_log['nid'] = "borrow_repay_advance";
		$credit_log['code'] = "borrow";
		$credit_log['type'] = "borrow_repay_advance";
		$credit_log['addtime'] = time();
		$credit_log['article_id'] =$borrow_userid;
		$credit_log['value'] = 3;
		$credit_log['remark'] =  "借款[{$borrow_url}]还款所得积分";;
		creditClass::ActionCreditLog($credit_log);
		
		//信用投资额度的增加
		$_data["user_id"] = $borrow_userid;
		$_data["amount_type"] = "borrow";
		$_data["type"] = "borrrow_repay";
		$_data["oprate"] = "add";
		$_data["nid"] = "borrrow_repay_".$borrow_userid."_".$borrow_nid.$repay_id;
		$_data["account"] = $repay_capital;
		$_data["remark"] = "借款标[{$borrow_url}]成功还款，额度增加";
		borrowClass::AddAmountLog($_data);
		
		//添加最后的还款金额
		$sql = "update `{borrow}` set repay_account_yes= repay_account_yes + {$all_account},repay_account_capital_yes= repay_account_capital_yes + {$repay_capital},repay_account_interest_yes= repay_account_interest_yes,repay_account_wait=0,repay_account_capital_wait=0,repay_account_interest_wait=0 where borrow_nid='{$borrow_nid}'";
		$result = $mysql -> db_query($sql);
		
		$sql="select * from `{borrow_repay}` where user_id='{$data['user_id']}' and borrow_nid='{$data['borrow_nid']}' and repay_status=0";
		$repayresult=$mysql->db_fetch_arrays($sql);
		foreach($repayresult as $key => $value){
			$lixi=round($value['repay_capital']/100,2);
			$all=round($value['repay_capital']/100+$value['repay_capital'],2);
			$_sql = "update `{borrow_repay}` set repay_status=1,repay_yestime='".time()."',repay_account_yes={$all},repay_interest_yes=0,repay_capital_yes=repay_capital where id='{$value['id']}'";
			$mysql->db_query($_sql);
		}
		return $result;
	}
	
	function GetBorrowRepayList($data = array()){
		global $mysql;

		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
		
		$_sql = " where p1.borrow_nid=p2.borrow_nid and p2.user_id=p3.user_id ";
		if (IsExiest($data['borrow_nid'])!=""){
			if ($data['borrow_nid'] == "request"){
				$_sql .= " and p1.borrow_nid= '{$_REQUEST['borrow_nid']}'";
			}else{
				$_sql .= " and p1.borrow_nid= '{$data['borrow_nid']}'";
			}
		}
		
		if (IsExiest($data['user_id'])!=""){
			$_sql .= " and p1.user_id = '{$data['user_id']}'";
		}	
		if(IsExiest($data['borrow_type'])!=""){
			$_sql .=" and p2.borrow_type = '{$data['borrow_type']}'";
		}
		if (IsExiest($data['vouch_userid']) !=""){
			$_sql .= " and p2.borrow_nid in (select borrow_nid from `{borrow_vouch}` where user_id={$data['vouch_userid']})";
		}	 
		
		if (IsExiest($data['username'])!=""){
			$_sql .= " and p3.username like '%{$data['username']}%'";
		}	 
		
		//add 20120830 wlz 
		//筛选借款标题
		if (IsExiest($data['borrow_name'])!=""){
			$data['borrow_name'] = urldecode($data['borrow_name']);
			$_sql .= " and p2.name like '%{$data['borrow_name']}%'";
		}	
				
		if (IsExiest($data['repay_time'])!=""){
			if ($date['repay_time']<=0) $data['repay_time'] = time();
			$_repayment_time = get_mktime(date("Y-m-d",$data['repay_time']));
			$_sql .= " and p1.repay_time < '{$_repayment_time}'";
		}	 
		
		if (IsExiest($data['dotime2'])!=""){
			$dotime2 = ($data['dotime2']=="request")?$_REQUEST['dotime2']:$data['dotime2'];
			if ($dotime2!=""){
				$_sql .= " and p1.repay_time < ".get_mktime($dotime2);
			}
		}
		if (IsExiest($data['dotime1'])!=""){
			$dotime1 = ($data['dotime1']=="request")?$_REQUEST['dotime1']:$data['dotime1'];
			if ($dotime1!=""){
				$_sql .= " and p1.repay_time > ".get_mktime($dotime1);
			}
		}
		
		
		if (IsExiest($data['status'])!=""){
			$_sql .= " and p1.status in ({$data['status']})";
		}
		if (IsExiest($data['repay_status'])!="" || $data['repay_status']=="0"){
			$_sql .= " and p1.repay_status in ({$data['repay_status']})";
		}
		
		
		if (IsExiest($data['borrow_status'])!=""){
			$_sql .= " and p2.status = '{$data['borrow_status']}'";
		}	
		
		if (IsExiest($keywords)!=""){
		    if ($keywords=="request"){
				if (isset($_REQUEST['keywords']) && $_REQUEST['keywords']!=""){
					$_sql .= " and p2.name like '%".urldecode($_REQUEST['keywords'])."%'";
				}
			}else{
				$_sql .= " and p2.name like '%".$keywords."%'";
			}
		}
		
		if (IsExiest($data['lateing'])!=""){
			$_sql .= " and p1.repay_time<".time();
		}
		
		if (IsExiest($data['type'])!=""){
			$_sql .= " and (p1.repay_web=1 or p1.repay_web_five_status=1 or p1.repay_web_ten_status=1) ";
		}
		
		
		if (IsExiest($data['late_days'])!="" || $data['late_days']=="0"){
			$_sql .= " and (TO_DAYS(FROM_UNIXTIME(".time()."))-TO_DAYS(FROM_UNIXTIME(p1.repay_time))  )>".$data['late_days'];
		}
		
		$_order = " order by p1.repay_time asc";
		if (isset($data['order']) && $data['order']!="" ){
			if ($data['order'] == "repay_time"){
				$_order = " order by p1.repay_time asc ";
			}elseif ($data['order'] == "order"){
				$_order = " order by p1.repay_period asc ,p1.id desc";
			}elseif ($data['order'] == "status"){
				$_order = " order by p1.repay_status asc ,p1.repay_time asc,p1.id desc";
			}elseif ($data['order'] == "late"){
				$_order = " order by p1.repay_web asc";
			}
		}
		$_select = " p1.*,p2.name as borrow_name,p2.borrow_period,p2.vouch_status,p2.account,p2.borrow_apr,p2.borrow_type,p2.borrow_style,p3.username as borrow_username";
		$sql = "select SELECT from `{borrow_repay}` as p1 left join `{borrow}` as p2 on p1.borrow_nid = p2.borrow_nid left join `{users}` as p3 on p3.user_id=p2.user_id {$_sql} ORDER LIMIT";
		//是否显示全部的信息
		if (isset($data['limit']) ){
			$_limit = "";
			if ($data['limit'] != "all"){
				$_limit = "  limit ".$data['limit'];
			}
			$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,  $_order, $_limit), $sql));
			foreach ($list as $key => $value){
				$late = self::LateInterest(array("time"=>$value['repay_time'],"account"=>$value['capital']));
				$list[$key]['late_days'] = $late['late_days'];
				if ($value['repay_status']!=1){
					if ($late['late_days']>0){
						$list[$key]['late_interest'] = round($value['repay_account']/100*0.8*$late['late_days'],2);
						$list[$key]['late_reminder'] = round($value['repay_account']/100*0.8*$late['late_days'],2);
					}
				}else{
					$list[$key]['late_reminder'] = $value['late_reminder'];
					$list[$key]['late_interest'] = $value['late_interest'];
					$list[$key]['late_days'] = $value['late_days'];
				}
			}
			return $list;
		}			 
		
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num', '', ''), $sql));
		
		$total = $row['num'];
		$total_page = ceil($total / $epage);
		$index = $epage * ($page - 1);
		$limit = " limit {$index}, {$epage}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,$_order, $limit), $sql));	
		$list = $list?$list:array();
		foreach ($list as $key => $value){
		$late = self::LateInterest(array("time"=>$value['repay_time'],"account"=>$value['capital']));
		$list[$key]['late_days'] = $late['late_days'];
				if ($value['repay_status']!=1){
					if ($late['late_days']>0){
						$list[$key]['late_interest'] = round($value['repay_account']/100*0.8*$late['late_days'],2);
						$list[$key]['late_reminder'] = round($value['repay_account']/100*0.8*$late['late_days'],2);
					}
				}else{
					$list[$key]['late_reminder'] = $value['late_reminder'];
					$list[$key]['late_interest'] = $value['late_interest'];
					$list[$key]['late_days'] = $value['late_days'];
				}
			$list[$key]['credit']=self::GetBorrowCredit(array("user_id"=>$value['user_id']));
		}
		return array(
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'epage' => $epage,
            'total_page' => $total_page
        );
		
	}
	
	public static function GetBorrowComment($data){
		global $mysql,$_G;
		
		require_once(ROOT_PATH."modules/comment/comment.class.php");
		$user_id = $data["user_id"];
		if ($data["type"]=="tender"){
			$sql = "select borrow_nid from `{borrow}` where user_id={$user_id}";
			$result = $mysql->db_fetch_arrays($sql);
			foreach ($result as  $key => $value){
				$_result[] = $value["borrow_nid"];
			}
			$_comment["code"] = "borrow";
			if (count($_result)>0){
				$_comment["article_id"] = join(",",$_result);
			}
			$_comment["reply_status"] = $data["reply_status"];
			$result = commentClass::GetList($_comment);
			
			return $result;
		}elseif ($data["type"]=="borrow"){
			$_comment["user_id"] = $_G["user_id"];
			$_comment["code"] = "borrow";
			$_comment["reply_status"] = $data["reply_status"];
			$result = commentClass::GetList($_comment);
			
			return $result;
		
		}
	
	}
	
	
	/**
	 * 担保列表
	 *
	 * @return Array
	 */
	function GetOtherloanList($data = array()){
		global $mysql;
	
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
		
		$_sql = "where 1=1";		 
		if (IsExiest($data['user_id'])!=""){
			$_sql .= " and p1.user_id = {$data['user_id']}";
		}
		if (IsExiest($data['username'])!=""){
			$_sql .= " and p2.username = '{$data['user_id']}'";
		}
	
	
		$_select = "p1.*";
		$sql = "select SELECT from `{borrow_otherloan}` as p1
				left join `{users}` as p2 on p2.user_id = p1.user_id
		 {$_sql}  order by p1.addtime desc LIMIT";
				
		//是否显示全部的信息
		if (isset($data['limit']) ){
			$_limit = "";
			if ($data['limit'] != "all"){
				$_limit = "  limit ".$data['limit'];
			}
			$result= $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,  'order by p1.id desc', $_limit), $sql));
			return $result;
		}			 
		
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num', '', ''), $sql));
		
		$total = $row['num'];
		$total_page = ceil($total / $epage);
		$index = $epage * ($page - 1);
		$limit = " limit {$index}, {$epage}";
		
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, 'order by p1.id desc', $limit), $sql));		
		$list = $list?$list:array();
		return array(
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'epage' => $epage,
            'total_page' => $total_page
        );
		
	}
	
	
	/**
	 * 用户添加基本的借款信息
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function AddOtherloan($data = array()){
		global $mysql;
		
		$sql = "insert into `{borrow_otherloan}` set `addtime` = '".time()."',`addip` = '".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
        return $mysql->db_query($sql);
	}
	
	function UpdateOtherloan($data = array()){
		global $mysql;
		
		$sql = "update `{borrow_otherloan}` set id = {$data['id']}";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
		$sql .= " where id = {$data['id']} and user_id={$data['user_id']}";
        return $mysql->db_query($sql);
	}
	
	
	function DelOtherloan($data){
		global $mysql;
		if ($data["id"]=="" || $data["user_id"]==""){ return -1;}
		$sql = "delete from `{borrow_otherloan}` where user_id={$data['user_id']} and id={$data['id']}";
		$result = $mysql->db_query($sql);
		if ($result) return 1;
		return -2;
	}
	
	
	function GetOtherloanOne($data){
		global $mysql;
		if ($data["id"]=="" || $data["user_id"]==""){ return "";}
		$sql = "select * from `{borrow_otherloan}` where user_id={$data['user_id']} and id={$data['id']}";
		$result = $mysql->db_fetch_array($sql);
		return $result;
	}
	
	
	function GetUserBorrowCount($data){
		global $mysql;
		$week_t=date("w",time());
		$nowtime = mktime(0,0,0,date("n",time()),date("j",time()),date("Y",time()));
		$weektime=($week_t-1)*60*60*24;
		$first_time=$nowtime-$weektime;
		$sql = "select sum(p1.account) as account_all from `{borrow}`  as p1 where p1.status=3 and p1.addtime>$first_time ";
		$result = $mysql->db_fetch_array($sql);
		$_sql = "select * from `{borrow}` where status=3";
		$borrow_result = $mysql->db_fetch_arrays($_sql);
		foreach($borrow_result as $key => $value){
			if(date("Ymd",$value['reverify_time'])==date("Ymd",time())){
				$all_borrow_account+=$value['account'];
			}
		}
		$result['all_borrow_account']=$all_borrow_account;
		$result['time']=time();
		return $result;
	}
	
	
	//逾期还款列表
	function GetLateList($data = array()){
		global $mysql,$_G;
		
		$page = (!isset($data['page']) || $data['page']=="")?1:$data['page'];
		$epage = (!isset($data['epage']) || $data['epage']=="")?10:$data['epage'];
		
		$_select = 'p1.*,p3.*,p5.card_id,p6.name as job_name,p6.address as job_address,p7.province,p7.city,p8.*';
		$_order = " order by p1.id ";
		if (isset($data['late_day']) && $data['late_day']!=""){
			$_repayment_time = time()-60*60*24*$data['late_day'];
		}else{
			$_repayment_time = time();
		}
		
		$_sql = " where p1.repay_time < '{$_repayment_time}' and p1.repay_status!=1";
		
		if (IsExiest($data['username']) != false){
			$_sql .= " and p3.`username`='".urldecode($data['username'])."'";
		}
		if (IsExiest($data['group_id']) != false){
			$_sql .= " and p2.`group_id` = '{$data['group_id']}'";
		}
		
		$sql = "select SELECT from `{borrow_repay}` as p1 
		left join `{borrow}` as p2 on p1.borrow_nid=p2.borrow_nid
		left join `{users}` as p3 on p2.user_id=p3.user_id
		left join `{approve_realname}` as p5 on p1.user_id=p5.user_id
		left join `{rating_job}` as p6 on p1.user_id=p6.user_id
		left join `{rating_info}` as p7 on p1.user_id=p7.user_id
		left join `{users_info}` as p8 on p1.user_id=p8.user_id
	   {$_sql} ORDER LIMIT ";
		
		$_list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, $_order , ""), $sql));
		foreach ($_list as $key => $value){
			$late = self::LateInterest(array("time"=>$value['repay_time'],"account"=>$value['capital']));
			$list[$value['user_id']]['username'] = $value['username'];
			$list[$value['user_id']]['realname'] = $value['realname'];
			$list[$value['user_id']]['phone'] = $value['phone'];
			$list[$value['user_id']]['user_id'] = $value['user_id'];
			$list[$value['user_id']]['email'] = $value['email'];
			$list[$value['user_id']]['job_name'] = $value['job_name'];
			$list[$value['user_id']]['job_address'] = $value['job_address'];
			$list[$value['user_id']]['qq'] = $value['qq'];
			$list[$value['user_id']]['sex'] = $value['sex'];
			$list[$value['user_id']]['card_id'] = $value['card_id'];
			$list[$value['user_id']]['province'] = $value['province'];
			$list[$value['user_id']]['repay_period'] = $value['repay_period']+1;
			$list[$value['user_id']]['city'] = $value['city'];
			$list[$value['user_id']]['late_days'] += $late['late_days'];//总逾期天数
			if ($list[$value['user_id']]['late_numdays']<$late['late_days']){
				$list[$value['user_id']]['late_numdays'] +=  $late['late_days'];
			}
			$list[$value['user_id']]['late_interest'] += round($late['late_interest']/2,2);
			$list[$value['user_id']]['late_account'] +=  $value['repay_account'];//逾期总金额
			$list[$value['user_id']]['late_num'] ++;//逾期笔数
			if ($value['repay_web']==1){
				$list[$value['user_id']]['late_webnum'] +=1;//逾期笔数
			}
		}
		//是否显示全部的信息
		if (isset($data['limit']) ){
			if (count($list)>0){
			return array_slice ($list,0,$data['limit']);
			}else{
			return array();
			}
		}	
		
		$total = count($list);
		$total_page = ceil($total / $epage);
		$index = $epage * ($page - 1);
		if (is_array($list)){
			$list = array_slice ($list,$index,$epage);
		}
		return array(
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'epage' => $epage,
            'total_page' => $total_page
        );
	
	}
	
	//统计
	function Tongji($data = array()){
		global $mysql;
		
		//成功借款
		$sql = " select sum(account) as num from `{borrow}` where status=3 ";
		$result = $mysql->db_fetch_array($sql);
		$_result['success_num'] = $result['num'];
		
		//逾期未还款
		$_repayment_time = time();;
		$sql = " select p1.repay_capital,p1.repay_yestime,p1.status  from  `{borrow_repay}` as p1 left join `{borrow}` as p2 on p1.borrow_nid=p2.borrow_nid where p2.status=3 ";
		$result = $mysql->db_fetch_arrays($sql);
		foreach ($result as $key => $value){
			$_result['success_sum'] += $value['repay_capital'];//借款总额
			if ($value['status']==1){
				$_result['success_num1'] += $value['repay_capital'];//成功还款总额
				if (date("Ymd",$value['repay_time']) < date("Ymd",$value['repay_yestime'])){	
					$_result['success_laterepay'] += $value['repay_capital'];
				}
			}
			if ($value['status']==0){
				$_result['success_num0'] += $value['account'];//未还款总额
				if (date("Ymd",$value['repay_time']) < date("Ymd",time())){	
					$_result['false_laterepay'] += $value['repay_capital'];
				}
			}
		}
		$_result['laterepay'] = $_result['success_laterepay'] + $_result['false_laterepay'];
		
		return $_result;
	}
	
	//逾期网站垫付
	function LateRepay($data){
		global $mysql,$_G;
		$sql = "select p1.*,p2.name as borrow_name,p2.vouchstatus,p2.fast_status from `{borrow_repay}` as p1 left join `{borrow}` as p2 on p1.borrow_nid = p2.borrow_nid where p1.id = {$data['id']}";
		$result = $mysql->db_fetch_array($sql);
		//判断是否已经还款，如果还款返回空
		if ($result['repay_status']==1){
			return -1;
		}elseif ($result['repay_web']==1){
			return -2;
		}elseif ($result['repay_status']==0){
			$late_result = self::LateInterest(array("account"=>$result['repay_account'],"time"=>$result['repay_time']));
			if ($late_result['late_days']<10){
				return -3;
			}else{
				//更新还款的状态为，表示网站已经待还
				//第一步，将状态改为网站已还
				$sql = "update `{borrow_repay}` set repay_web=1 where id = {$data['id']}";
				$mysql -> db_query($sql);
				
				$repay_period = $result['repay_period'];
				$borrow_nid = $result['borrow_nid'];
				$borrow_name = $result['borrow_name'];
				$borrow_url = "<a href=http://www.hcdai.com/invest/a{$borrow_nid}.html target=_blank>{$borrow_name}</a>";
				
				$sql = "select p1.*,p2.change_status,p2.change_userid from `{borrow_recover}` as p1 left join `{borrow_tender}` as p2 on p2.id=p1.tender_id where p1.`recover_period` = '{$repay_period}' and p1.borrow_nid='{$borrow_nid}'";
				$result = $mysql->db_fetch_arrays($sql);
				foreach ($result as $key => $value){
					
					if ($value['change_status']==1){
						if ($value['change_userid']=="" || $value['change_userid']==0){
							$value['user_id']=0;
						}else{
							$value['user_id']=$value['change_userid'];
						}
					}
					
					if ($result['vouchstatus']==1){
						$money=$value['recover_account'];
					}elseif($result['fast_status']==1){
						$money=$value['recover_account'];
					}else{
						if ($value['user_id']==0){
							$sql = "update  `{borrow_tender}` set recover_times='recover_times'+1,recover_account_yes= recover_account_yes + {$value['recover_capital']},recover_account_capital_yes = recover_account_capital_yes  + {$value['recover_capital']} ,recover_account_interest_yes = recover_account_interest_yes + 0,recover_account_wait= recover_account_wait - {$value['recover_account']},recover_account_capital_wait = recover_account_capital_wait  - {$value['recover_capital']} ,recover_account_interest_wait = recover_account_interest_wait - {$value['recover_interest']}  where id = '{$value['tender_id']}'";
							$mysql->db_query($sql);
					$_sql = "update  `{borrow_recover}` set recover_yestime='".time()."',recover_account_yes = recover_account ,recover_capital_yes = recover_capital ,recover_interest_yes = recover_interest,late_days={$late_result['late_days']} ,status=1,recover_web=1   where id = '{$value['id']}'";
					$mysql->db_query($_sql);
							$money=$value['recover_account'];
							$more="金额为本息。";
						}else{
						$Vip=usersClass::GetUsersVip(array("user_id"=>$value['user_id']));
						if ($Vip['status']==1){
							if ($Vip['vip_type']==1){
							$sql = "update  `{borrow_tender}` set recover_times='recover_times'+1,recover_account_yes= recover_account_yes + {$value['recover_capital']},recover_account_capital_yes = recover_account_capital_yes  + {$value['recover_capital']} ,recover_account_interest_yes = recover_account_interest_yes + 0,recover_account_wait= recover_account_wait - {$value['recover_account']},recover_account_capital_wait = recover_account_capital_wait  - {$value['recover_capital']} ,recover_account_interest_wait = recover_account_interest_wait - {$value['recover_interest']}  where id = '{$value['tender_id']}'";
							$mysql->db_query($sql);
					//第二步，更新投资人的分期信息
					$sql = "update  `{borrow_recover}` set recover_yestime='".time()."',recover_account_yes = recover_capital ,recover_capital_yes = recover_capital,late_days={$late_result['late_days']} ,recover_interest_yes = 0 ,status=1,recover_web=1   where id = '{$value['id']}'";
					$mysql->db_query($sql);
								$money=$value['recover_capital'];
								$more="金额为本金。";
							}else{
					$sql = "update  `{borrow_recover}` set recover_yestime='".time()."',recover_account_yes = recover_account ,recover_capital_yes = recover_capital ,recover_interest_yes = recover_interest,late_days={$late_result['late_days']} ,status=1,recover_web=1   where id = '{$value['id']}'";
					$mysql->db_query($sql);
							
					//第三步，更新投资的信息
					$sql = "update  `{borrow_tender}` set recover_times='recover_times'+1,recover_account_yes= recover_account_yes + {$value['recover_account']},recover_account_capital_yes = recover_account_capital_yes  + {$value['recover_capital']} ,recover_account_interest_yes = recover_account_interest_yes + {$value['recover_interest']},recover_account_wait= recover_account_wait - {$value['recover_account']},recover_account_capital_wait = recover_account_capital_wait  - {$value['recover_capital']},recover_account_interest_wait = recover_account_interest_wait - {$value['recover_interest']}  where id = '{$value['tender_id']}'";
					$mysql->db_query($sql);
								$money=$value['recover_account'];
								$more="金额为本息。";
								self::UpdateBorrowCount(array("user_id"=>$value['user_id'],"tender_interest_yes"=>$value['recover_interest']));
							}
						}else{
							$money=$value['recover_capital']/2;
					//第三步，更新投资的信息
					$sql = "update  `{borrow_tender}` set recover_times='recover_times'+1,recover_account_yes= recover_account_yes + {$money},recover_account_capital_yes = recover_account_capital_yes  + {$money} ,recover_account_interest_yes = recover_account_interest_yes + 0,recover_account_wait= recover_account_wait - {$value['recover_account']},recover_account_capital_wait = recover_account_capital_wait  - {$value['recover_capital']} ,recover_account_interest_wait = recover_account_interest_wait - {$value['recover_interest']}  where id = '{$value['tender_id']}'";
					$mysql->db_query($sql);
					$sql = "update  `{borrow_recover}` set recover_yestime='".time()."',recover_account_yes = {$money} ,recover_capital_yes = {$money} ,recover_interest_yes = 0 ,late_days={$late_result['late_days']},status=1,recover_web=1   where id = '{$value['id']}'";
					$mysql->db_query($sql);
							$more="金额为本金的一半。";
						}
					}
					}
					$log_info["user_id"] = $value['user_id'];
					$log_info["nid"] = "system_repayment_".time()."_".$value['id'];
					$log_info["money"] = $money;
					$log_info["income"] = $log_info['money'];//收入
					$log_info["expend"] = 0;//支出
					$log_info["balance_cash"] = $log_info['money'];//可提现金额
					$log_info["balance_frost"] = 0;//不可提现金额
					$log_info["frost"] = 0;//冻结金额
					$log_info["await"] = -$value['recover_account'];//待收金额
					$log_info["type"] = "system_repayment";//类型
					$log_info["to_userid"] = 0;//付给谁
					$log_info["remark"] =  "客户逾期超过30天，系统自动对[{$borrow_url}]借款的还款,{$more}";
					accountClass::AddLog($log_info);
					
					$bad=$value['recover_account']-$money;
					
					
					if ($value['change_status']!=1){
						self::UpdateBorrowCount(array("user_id"=>$value['user_id'],"tender_recover_yes"=>$money,"tender_recover_times_yes"=>1,"tender_recover_wait"=>-$value['recover_account'],"tender_recover_times_wait"=>-1,"bad_account"=>$bad));
					}else{
						self::UpdateBorrowCount(array("user_id"=>$value['user_id'],"bad_account"=>$bad));
					}
					$web['money']=$money;
					$web['user_id']=$value['user_id'];
					$web['nid']="web_repay_".time();
					$web['type']="web_repay";
					$web['remark']="用户投资{$borrow_url}第".($repay_period+1)."期逾期收到网站垫付金{$money}元，{$more}";
					accountClass::AddAccountWeb($web);
					
					
					$log_info["user_id"] = 0;//操作用户id
					$log_info["nid"] = "fengxianchi_0_".time()."_".$value['id'];//订单号
					$log_info["money"] = -$money;//操作金额
					$log_info["income"] = 0;//收入
					$log_info["expend"] = 0;//支出
					$log_info["balance_cash"] = 0;//可提现金额
					$log_info["balance_frost"] = 0;//不可提现金额
					$log_info["frost"] = 0;//冻结金额
					$log_info["await"] = 0;//待收金额
					$log_info["type"] = "fengxianchi_dianfu";//类型
					$log_info["to_userid"] = 0;//付给谁
					$log_info["remark"] =  "系统账户垫付[{$borrow_url}]逾期借款金{$money}元,{$more}";
					accountClass::AddLog($log_info);
					
					/*
					$log_info["user_id"] = $value['user_id'];
					$log_info["nid"] = "tender_late_fee_".$value['user_id'].$value['borrow_nid'];
					//当期逾期本息的0.4%/天
					$log_info["money"] = round($value['repay_account']/100*0.2*$list[$key]['late_days'],2);
					$log_info["income"] = $log_info['money'];//收入
					$log_info["expend"] = 0;//支出
					$log_info["balance_cash"] = $log_info['money'];//可提现金额
					$log_info["balance_frost"] = 0;//不可提现金额
					$log_info["frost"] = 0;//冻结金额
					$log_info["await"] = 0;//待收金额
					$log_info["type"] = "tender_late_fee";//类型
					$log_info["to_userid"] = 0;//付给谁
					$log_info["remark"] =  "客户逾期超过30天的[{$borrow_url}]借款标的利息扣除";
					accountClass::AddLog($log_info);
					*/
					
					
					//提醒设置
					/*$remind['nid'] = "loan_pay";
					$remind['receive_userid'] = $value['user_id'];
					$remind['title'] = "网站对[{$borrow_name}]借款的垫付还款";
					$remind['content'] = "网站在".date("Y-m-d H:i:s")."对[{$borrow_url}}</a>]借款进行垫付还款,还款金额为{$value['repay_account']}";
					remindClass::sendRemind($remind);*/
				}
			}
		}
		return 1;
	}
	
	//获取用户的总投资额，可以是全部的，也可以单独的某个标
	function GetUserTenderAccount($data){
		global $mysql;
		$_sql = " where 1=1 ";
		if (IsExiest($data['user_id'])!=""){
			$_sql .= " and user_id='{$data['user_id']}' ";
		}
		if (IsExiest($data['borrow_nid'])!=""){
			$_sql .= " and borrow_nid='{$data['borrow_nid']}' ";
		}
		$sql = "select sum(account) as account_all from `{borrow_tender}` {$_sql}";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=fasle ) {
			return $result["account_all"];
		}
		return 0;
	}
	
	//获取担保逾期信息
	
	//获取统计信息
	function GetCount($data = array()){
		global $mysql;
		
		
	}
	
	function GetVouchUsersList($data){
		global $mysql;
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
		$_select = " p1.*,p2.credit,p3.tender_vouch";
		$sql = "select SELECT from `{users}` as p1 left join `{credit}` as p2 on p1.user_id=p2.user_id left join `{user_amount}` as p3 on p1.user_id=p3.user_id where p1.user_id in (select user_id from `{user_amount}` where tender_vouch >0)  ";
		if (isset($data['limit']) ){
			$_limit = "";
			if ($data['limit'] != "all"){
				$_limit = "  limit ".$data['limit'];
			}
			$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,  $_order, $_limit), $sql));
			return $list;
		}			 
		
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num', '', ''), $sql));

		$total = $row['num'];
		$total_page = ceil($total / $epage);
		$index = $epage * ($page - 1);
		$limit = " limit {$index}, {$epage}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,$_order, $limit), $sql));		
		$list = $list?$list:array();
	
		return array(
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'epage' => $epage,
            'total_page' => $total_page
        );
	}
	
	//网站统计
	public static function GetAll($data=array()){
		global $mysql;
		$sql = "select sum(account) as account,count(1) as times from `{borrow}`";
		$result = $mysql->db_fetch_array($sql);
		$_result['borrow_times'] = $result['times'];
		$_result['borrow_account'] = $result['account'];
		
		$sql = "select sum(account) as account,count(1) as times  from `{borrow}` where status=3";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false){
			$_result['borrow_success_times'] = 0;
			$_result['borrow_success_account'] = 0;
			$_result['borrow_success_scale']=0;
		}else{
			$_result['borrow_success_times'] = $result['times'];
			$_result['borrow_success_account'] = $result['account'];
			$_result['borrow_success_scale'] = round($_result['borrow_success_times']/$_result['borrow_times'],2);
		}
		return $_result;
	}
	
	//删除，只能删除草稿的标
	public static function Delete($data = array()){
		global $mysql;
		$id = $data['id'];
		if (!is_array($id)){
			$id = array($id);
		}
		if (isset($data['status']) && $data['status']!=""){
			$_sql .= " and status ='".$data['status']."'";
		}
		if (isset($data['user_id'])  && $data['user_id']!=""){
			$_sql = " and user_id={$data['user_id']} ";
		}
		$sql = "delete from {borrow}  where borrow_nid in (".join(",",$id).") $_sql";
		return $mysql->db_query($sql);
	}
	
	
	//流标处理
	function ActionLiubiao($data){
		global $mysql;
		$status= $data['status'];
		if ($status==1){
			$result = self::Cancel($data);
		}elseif($status==2){
			$valid_time = $data['days'];
			$sql = "update `{borrow}` set borrow_valid_time=borrow_valid_time +{$valid_time} where borrow_nid={$data['borrow_nid']}";
			$mysql->db_query($sql);
		}
		return true;
	}
	
	//修改年利率
	function ActionBorrowApr($data){
		global $mysql;
		if (count($data['id'])<=0) return 1;
		foreach ($data['id'] as $key => $value){
			$borrow_result=self::GetOne(array("id"=>$value));
			if ($borrow_result['status']==1 || $borrow_result['status']==0){
				$sql = "update `{borrow}` set `borrow_apr`='{$data['borrow_apr'][$key]}' where id='{$value}'";
				$mysql->db_query($sql);
			}
		}
		return 1;
	}
	
	function GetLiucheng($data){
		global $mysql;
		$user_id= $data['user_id'];
		$sql = "select * from `{attestation}` where user_id='{$user_id}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false){
			$_result['attestion_status']=0;
		}else{
			$_result['attestion_status']=1;
		}
		
		
		$sql = "select * from `{borrow}` where user_id='{$user_id}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false){
			$_result['borrow_status']=0;
		}else{
			$_result['borrow_status']=1;
		}
		
		
		$sql = "select * from `{borrow}` where status=3 and user_id='{$user_id}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false){
			$_result['borrow_success_status']=0;
		}else{
			$_result['borrow_success_status']=1;
		}
		
		
		$sql = "select * from `{borrow_repay}` where status=1 and user_id='{$user_id}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false){
			$_result['borrow_repay_status']=0;
		}else{
			$_result['borrow_repay_status']=1;
		}
		return $_result;
	}
	
	public static function GetOther($data = array()){
		global $mysql;
		$_sql = "where 1=1 ";
		if (IsExiest($data['user_id'])!=""){
			$_sql .= " and  user_id = '{$data['user_id']}' ";
		}
		$sql = "select * from `{borrow_other}` $_sql ";
		$result = $mysql->db_fetch_array($sql);
		
		return $result;
	}
	
	function GetBorrowCreditUsers($data){
		global $mysql;
		$_sql = " where 1=1 ";
		
		//搜索用户id
        if (IsExiest($data['user_id'])!=false) {
            $_sql .= " and p1.user_id ='{$data['user_id']}'";
        }
		
		//搜索
        if (IsExiest($data['type'])!=false) {
            $_sql .= " and p1.type ='{$data['type']}'";
        }
		
		//搜索
        if (IsExiest($data['nid'])!=false) {
            $_sql .= " and p1.nid ='{$data['nid']}'";
        }
		
		$sql = "select sum(p1.credit) as num from `{borrow_credit}`  as p1 {$_sql}";
		$result = $mysql->db_fetch_array($sql);
		return $result['num'];
	}
	
	function GetBorrowTimes($data){
		global $mysql;
		$_sql = " where 1=1 ";
		
		//搜索用户id
        if (IsExiest($data['user_id'])!=false) {
            $_sql .= " and p1.user_id ='{$data['user_id']}'";
        }
		
		//搜索
        if (IsExiest($data['type'])!=false) {
            $_sql .= " and p1.type ='{$data['type']}'";
        }
		
		//搜索
        if (IsExiest($data['nid'])!=false) {
            $_sql .= " and p1.nid ='{$data['nid']}'";
        }
		
		$sql = "select count(1) as num from `{borrow_credit}`  as p1 {$_sql}";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) $result['num'] = 0;
		return $result['num'];
	}
	
	//data = array("user_id"=>"")
	function GetBorrowVip($data){
		global $mysql,$_G;
		
		if (IsExiest($_G["borrow_vip_result"])!=false) return $_G["borrow_vip_result"];//防止重复读取
		
		$result = usersClass::GetUsersVipStatus(array("user_id"=>$data['user_id']));
		$late_repay_times = 0;//逾期还款次数*800
		$delay_repay_times =  0;//延迟还款次数*300
		
		if ($result!=1) return array("vip"=>0,"fee"=>0);
		$vip_status=1;
		$vip_fee = isset($_G['system']['con_vip1_fee'])?$_G['system']['con_vip1_fee']:1;
		$_result = self::GetBorrowCredit(array("user_id"=>$data['user_id']));
		$credit = $_result['credit_total'];
		$borrow_credit = $_result['borrow_credit'];
		//vip2
		if ($credit>=500+$delay_reapy_times*300+$late_reapy_times*800 && $borrow_credit>=300){
			$vip_status = 2;
			$vip_fee = isset($_G['system']['con_vip2_fee'])?$_G['system']['con_vip2_fee']:0.95;
		}
		//vip3
		if ($credit>=1500+$delay_reapy_times*800+$late_reapy_times*1500 && $borrow_credit>=1200){
			$vip_status = 3;
			$vip_fee = isset($_G['system']['con_vip3_fee'])?$_G['system']['con_vip3_fee']:0.9;
		}
		
		//vip4
		if ($credit>=5000 && $borrow_credit>=3500 && dealy_reapy_times==0 && $delay_repay_times==0){
			$vip_status = 4;
			$vip_fee = isset($_G['system']['con_vip4_fee'])?$_G['system']['con_vip4_fee']:0.85;
		}
		
		//vip5
		if ($credit>=20000 && $borrow_credit>=16000 && dealy_reapy_times==0 && $delay_repay_times==0){
			$vip_status = 5;
			$vip_fee = isset($_G['system']['con_vip5_fee'])?$_G['system']['con_vip5_fee']:0.8;
		}
		
		
		//vip6
		if ($credit>=100000 && $borrow_credit>=60000 && dealy_reapy_times==0 && $delay_repay_times==0){
			$vip_status = 6;
			$vip_fee = isset($_G['system']['con_vip6_fee'])?$_G['system']['con_vip6_fee']:0.75;;
		}
		
		return array("vip"=>$vip_status,"fee"=>$vip_fee);
	}
	//data=(user_id=>)
	function GetBorrowCreditOne($data){
		global $mysql,$_G;
		
		if (IsExiest($_G["borrow_credit_result"])!=false) return $_G["borrow_credit_result"];//防止重复读取
		
		if (!isset($data['credits']) || $data['credits']==""){
			if ($data['user_id']=="") return "";
			$result = creditClass::GetOne(array("user_id"=>$data['user_id']));
			$data['credits'] = $result['credits'];
		}
		
		if ($data['credits']==false){
			return array("credit_total"=>0,"approve_credit"=>0,"borrow_credit"=>0,"tender_credit"=>0,"vouch_credit"=>0);
		}
		$result = unserialize($data['credits']);
		$_result = array();
		$sql = "select sum(credit) as num from `{attestations}` where user_id='{$data['user_id']}' and status=1";
		$attcredit = $mysql->db_fetch_array($sql);
		
		foreach ($result as $key=>$value){
			$_result[$value['class_id']] = $value['num'];
		}
		$_result[6] = $attcredit['num'];
		$result = array("credit_total"=>$_result[2]+$_result[3]+$_result[4]+$_result[5]+$_result[6],"approve_credit"=>$_result[2],"borrow_credit"=>$_result[2]+$_result[3]+$_result[6],"tender_credit"=>$_result[2]+$_result[4],"vouch_credit"=>$_result[2]+$_result[5]);
		
		return $result;
	}
	
	function GetBorrowCredit($data){
		global $mysql,$_G;
		if (IsExiest($_G["borrow_credit_result"])!=false) return $_G["borrow_credit_result"];//防止重复读取\
		
		if ($data['user_id']=="") return false;
		
		$_result = array();
		$sql = "select sum(credit) as num from `{attestations}` where user_id='{$data['user_id']}' and status=1";
		$attcredit = $mysql->db_fetch_array($sql);
		
		$sql = "select sum(credit) as tongji from `{credit_log}` where user_id='{$data['user_id']}'";
		$credit_tongji = $mysql->db_fetch_array($sql);
		
		$sql = "select sum(credit) as creditnum from `{credit_log}` where user_id='{$data['user_id']}' and code='borrow'";
		$credit_log = $mysql->db_fetch_array($sql);
		$sql = "select sum(credit) as creditnum from `{credit_log}` where user_id='{$data['user_id']}' and code='approve'";
		$approve = $mysql->db_fetch_array($sql);
		$_result[1] = $attcredit['num'];
		$_result[2] = $credit_log['creditnum'];
		$_result[3] = $approve['creditnum'];
		
		//$result = array("credit_total"=>$_result[1]+$credit_tongji['tongji'],"borrow_credit"=>$_result[2],"approve_credit"=>$_result[3]+$_result[1],"approve"=>$_result[3]);
		$result = array("approve_credit"=>$_result[1]+$credit_tongji['tongji'],"borrow_credit"=>$_result[2],"approve"=>$_result[3]);
		return $result;
	}
	

	
	function GetBorrowCount($data){
		global $mysql;
		//获取借款统计
		$latesql = "select count(1) as late_nums from `{account_log}` where user_id='{$data['user_id']}' and type='borrow_repay_late'";
		$late_nums = $mysql->db_fetch_array($latesql);
		$latemoneysql = "select sum(money) as latemoney from `{account_log}` where user_id='{$data['user_id']}' and type='borrow_repay_late'";
		$latemoney = $mysql->db_fetch_array($latemoneysql);
		$sql = "select * from `{borrow_count}` where user_id='{$data['user_id']}'";
		$_result = $mysql->db_fetch_array($sql);
		$_result['interest_scale'] = 0;
		if ($_result!=false && $_result['tender_capital_account']>0){
			$_result['interest_scale'] = round($_result['tender_interest_account']/$_result['tender_capital_account']*100,2);
		}
		$lixi="select sum(late_interest) as all_lixi from `{borrow_repay}` where user_id={$data['user_id']}";
		$lxre=$mysql->db_fetch_array($lixi);
		$all=$_result['weiyue']+$_result['borrow_repay_interest']+$lxre['all_lixi'];
		if ($_result!=false && $_result['borrow_account']>0){
			$_result['borrow_interest_scale'] = round($all/$_result['borrow_account']*100,2);
		}
		//坏账计提
		$sql = "select sum(recover_account) as num from `{borrow_recover}` where recover_status=0 and user_id='{$data['user_id']}' and recover_time<".(time()-60*60*24*90)." and recover_time<".time();
		$result = $mysql->db_fetch_array($sql);
		$_result['bad_recover_account'] = $result['num'];
		$_result['late_nums'] = $late_nums['late_nums'];
		$_result['latemoney'] = $latemoney['latemoney'];
		return $_result;
	}
	
	//data = array("user_id"=>"");
	function UpdateBorrowCount($data = array()){
		global $mysql;
		if ($data['user_id']=="") return "";
		$user_id =$data['user_id'];
		$result = $mysql->db_fetch_array("select 1 from `{borrow_count}` where user_id='{$data['user_id']}'");
		if ($result==false){
			$sql= "insert into `{borrow_count}` set user_id='{$data['user_id']}'";
			$mysql->db_query($sql);
			
		}
		$sql = "update `{borrow_count}` set user_id='{$data['user_id']}'";
		unset ($data['user_id']);
		foreach ($data as $key => $value){
			$sql .= ",`{$key}`=`{$key}`+{$value}";
		}
		$sql .= " where user_id='{$user_id}'";
		$mysql->db_query($sql);
		return "";		
	}
	
	
	function GetUserCount($data){
		global $mysql;
		//获取借款统计
		$sql="select count(1) as all_times from `{borrow}` where user_id={$data['user_id']} and repay_account_all=repay_account_yes";
		//$result=$mysql->db_fetch_arrays($sql);
		$latesql="select sum(p2.late_interest) as all_late_interest from `{borrow_tender}` as p1 left join `{borrow_recover}` as p2 on p1.id=p2.tender_id where (p1.user_id='{$data['user_id']}' and p1.change_status=0) or (p1.change_userid='{$data['user_id']}' and p1.change_status=1)";
		$late=$mysql->db_fetch_array($latesql);	
		
				
		
		$borrow_repays="select sum(credit) as borrow_repays_credit,count(1) as borrow_repays from `{credit_log}` where user_id={$data['user_id']} and nid='borrow_repay' ";
		$result1=$mysql->db_fetch_array($borrow_repays);
		$borrow_repay_late_common="select sum(credit) as borrow_common_credit,count(1) as borrow_repay_late_common from `{credit_log}` where  user_id={$data['user_id']} and nid='borrow_repay_late_common' ";
		$result2=$mysql->db_fetch_array($borrow_repay_late_common);
		$borrow_repay_late_serious="select sum(credit) as borrow_serious_credit,count(1) as borrow_repay_late_serious from `{credit_log}` where  user_id={$data['user_id']} and nid='borrow_repay_late_serious' ";
		$result3=$mysql->db_fetch_array($borrow_repay_late_serious);
		
		//总借出笔数  wdf
		$num_recover="select count(1) as num_recover from `{borrow_recover}` where user_id={$data['user_id']}";
		$result_num=$mysql->db_fetch_array($num_recover);			
		$_result = self::GetBorrowCount(array("user_id"=>$data['user_id']));
		
		$_result['num_recover']=$result['num_recover'];
		$_result['borrow_repays']=$result1['borrow_repays'];
		$_result['borrow_repays_credit']=$result1['borrow_repays_credit'];
		$_result['borrow_common_credit']=$result2['borrow_common_credit'];
		$_result['borrow_repay_late_common']=$result2['borrow_repay_late_common'];
		$_result['borrow_repay_late_serious']=$result3['borrow_repay_late_serious'];
		$_result['borrow_serious_credit']=$result3['borrow_serious_credit'];		
		$_result['all_late_interest']=$late['all_late_interest'];	
		
		return $_result;
	}
	
	
	
	function GetCareList($data = array()){
		global $mysql;
		$_sql = " where 1=1 ";
		
		//判断用户id
		if (IsExiest($data['user_id']) != false){
			$_sql .= " and p1.`user_id`  = '{$data['user_id']}'";
		}
		
		//判断是否搜索用户名
		if (IsExiest($data['borrow_nid']) != false){
			$_sql .= " and p1.`borrow_nid` = '{$data['borrow_nid']}'";
		}
		
		
		$_select = "p1.*,p2.*,p3.username";
		$sql = "select SELECT from `{borrow_care}` as p1
				left join `{borrow}` as p2 on p1.borrow_nid = p2.borrow_nid
				left join `{users}` as p3 on p1.user_id = p3.user_id
				SQL ORDER LIMIT";
		
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
	
	
	function AddCare($data){
		global $mysql;
		$sql = "select 1 from `{users_care}` where user_id='{$data['user_id']}' and article_id='{$data['article_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false){
			$sql = "insert into `{users_care}` set  addtime='".time()."',addip='".ip_address()."'";
			foreach($data as $key => $value){
				$sql .= ",`$key` = '$value'";
			}
			$mysql->db_query($sql);
			return 1;
		}else{
			return "";
		}
	}
	
	
	function GetRepayRecover($data){
		global $mysql;
	//最近还款时间和总额
		$sql = "select recover_account,recover_time from `{borrow_recover}` where recover_status !=1 and user_id='{$data['user_id']}'  order by recover_time ";
		$result = $mysql->db_fetch_array($sql);
		$_result['recover_time'] = $result['recover_time'];
		$_result['recover_account'] = $result['recover_account'];
		
		$sql = "select repay_account,repay_time from `{borrow_recover}` where repay_status !=1  and user_id='{$data['user_id']}' order by repay_time ";
		$result = $mysql->db_fetch_array($sql);
		$_result['repay_time'] = $result['repay_time'];
		$_result['repay_account'] = $result['repay_account'];
		
		return $_result;
	}
}



?>