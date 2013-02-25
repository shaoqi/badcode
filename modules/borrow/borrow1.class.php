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
require_once(ROOT_PATH."modules/borrow/borrow.calculate.php");
require_once(ROOT_PATH."modules/users/users.class.php");

$MsgInfo["borrow_not_exiest"] = "借款不存在";

class borrowClass  {
    function borrowClass(){
        global $_G;
    }
    
    /**
	 * 获取借款标所属的状态
	 *
	 * @param Array $data = status,account,borrow_end_status,repay_account_wait,borrow_account_wait
	 * @return Array
	 */
    function GetBorrowStatusNid($data = array()){
        global $mysql;
        //如果是0表示正在初审
       	if ($data['status']==0){
			$borrow_status_nid = "first";
		}elseif ($data['status']==2){
			$borrow_status_nid = "false";
		}elseif ($data['status']==3){
			if ($data['repay_account_wait']==0.00){
				$borrow_status_nid = "repay_yes";
			}else{
				$borrow_status_nid = "repay";
			}
		}elseif ($data['status']==4){
			$borrow_status_nid = "full_false";
		}elseif ($data['status']==5){
			$borrow_status_nid = "cancel";
		}elseif ($data['status']==1){
			if ($data['borrow_end_status']==1 && $data['borrow_account_wait']>0){
				$borrow_status_nid = "late";
			}elseif ($data['borrow_account_wait']==0){
				$borrow_status_nid = "full";
			}else{
				$borrow_status_nid = "loan";
			}
			
		}
        return $borrow_status_nid;
    }
	
	/**
	 * 1,列表
	 * $data = array("user_id"=>"用户id","username"=>"用户名","borrow_name"=>"借款名称","borrow_nid"=>"标识名","query_type"=>"借款类型","dotime1"=>"发布时间1","dotime2"=>"发布时间2");
	 * @return Array
	 */
	public static function GetList($data = array()){
		global $mysql,$_G;
		
		$_sql = "where 1=1 ";	
		
		//判断用户id
		if (IsExiest($data['user_id']) != false){
			$_sql .= " and p1.user_id = {$data['user_id']}";
		}
		
		//搜到用户名
		if (IsExiest($data['username']) != false){		
			$data['username']= addslashes(urldecode($data['username']));	
			$_sql .= " and p2.username like '%{$data['username']}%'";
		}
		
		//搜索借款名称
		if (IsExiest($data['borrow_name']) != false){
			$data['borrow_name']= addslashes(urldecode($data['borrow_name']));
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
			$_sql .= " and p1.borrow_type = '{$data['borrow_type']}'";
		}
		
        //初审的借款
        if ($data['query_type']=="first" && $data['status_nid']==""){
            $data['status_nid'] = "first";
        }
        
		//搜索借款状态
		if (IsExiest($data['status_nid']) != false){
			$status_nid = $data['status_nid'];
            //初审中
            if ($status_nid=="first"){
                $_sql .= " and p1.status=0 and p1.borrow_status=0";
            }
            //初审中
            elseif ($status_nid=="loan"){
                $_sql .= " and p1.status=1 and p1.borrow_status=1 and p1.borrow_end_time >".time()." and p1.account>p1.borrow_account_yes";
            }  
            //初审失败
            elseif ($status_nid=="false"){
                $_sql .= " and p1.status=2 ";
            }
            //已过期
            elseif ($status_nid=="late"){
                $_sql .= " and p1.status=1 and p1.borrow_status=1 and p1.borrow_end_time <".time()."  and p1.account>p1.borrow_account_yes ";
            }
            //逾期借款
            elseif ($status_nid=="lates"){
                $_sql .= " and p1.status=3 and p1.borrow_full_status=1 and p1.repay_next_time <".time()."  ";
            }
            //满标
            elseif ($status_nid=="full"){
                $_sql .= " and p1.status=1 and p1.borrow_status=1 and p1.account=p1.borrow_account_yes ";
            }
            //还款中
            elseif ($status_nid=="repay"){
                $_sql .= " and p1.status=3  and p1.borrow_status=1 and p1.borrow_full_status=1 and p1.account>p1.repay_account_yes ";
            } 
            //已还完
            elseif ($status_nid=="repay_yes"){
                $_sql .= " and p1.status=3  and p1.borrow_status=1 and p1.borrow_full_status=1 and p1.account=p1.repay_account_yes ";
            }
            //流标
            elseif ($status_nid=="over"){
                $_sql .= " and p1.status=6 ";
            }
            //流标
            elseif ($status_nid=="cancel"){
                $_sql .= " and p1.status=5 ";
            }
            //成功的借款
            elseif ($status_nid=="success"){
                $_sql .= " and p1.status=3 ";
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
		
		$_select = " p1.*,p2.username,p3.credits,p3.credit,p4.name as type_name,p5.name as style_name,p6.live_city as city";
		$sql = "select SELECT from `{borrow}` as p1 
				 left join {borrow_type} as p4 on p1.borrow_type=p4.nid
				 left join {borrow_style} as p5 on p1.borrow_style=p5.nid
				 left join {users} as p2 on p1.user_id=p2.user_id
				 left join {rating_contact} as p6 on p1.user_id=p6.user_id
				 left join {credit} as p3 on p1.user_id=p3.user_id
				 SQL ORDER LIMIT
				";
		
		//是否显示全部的信息
		if (IsExiest($data['limit'])!=false){
			if ($data['limit'] != "all"){ $_limit = "  limit ".$data['limit']; }
			$result=$mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array($_select, $_sql, $_order, $_limit), $sql));
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
		
		foreach ($list as $key => $value){
		    //借款是否到期
			$borrow_end_status = 0;
			if ($value['status']==1 && $value['borrow_end_time']<time()){
				$borrow_end_status = 1;
			}
			$list[$key]["borrow_end_status"] = $borrow_end_status;
            
            //借款的属性
			if ($value['flag']!=""){
				$_flag = explode(",",$value['flag']);
				foreach ($_flag as $_k => $_v){
					$list[$key]["_flag"][] = $_flag_result[$_v];
					$flag_name[] = $_flag_result[$_v]['name'];
				}
				$list[$key]["flag_name"] = join(",",$flag_name);
			}
		
            //借款状态id的属性
			$list[$key]["borrow_status_nid"] = self::GetBorrowStatusNid(array("status"=>$value['status'],"account"=>$value['account'],"borrow_end_status"=>$borrow_end_status,"borrow_account_wait"=>$value["borrow_account_wait"],"repay_account_wait"=>$value["repay_account_wait"])) ;
            
		}
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		
		return $result;
	}
    
        
	/**
	 * 查看借款标，此函数用在大部分的借款上面，慎用    
	 *
	 * @param Array $data
	 * @return Array
	 */
    public static function GetView($data = array()){
		global $mysql;
		$_sql = "where 1=1 ";
        
        //增加点击次数
        if (IsExiest($data['hits'])!=""){
			$hsql="update `{borrow}` set hits=hits+1 where borrow_nid={$data['borrow_nid']}";
			$mysql->db_query($hsql);
		}
        
        
		if (IsExiest($data['user_id'])!=""){
			$_sql .= " and  p1.user_id = '{$data['user_id']}' ";
		}
		if (IsExiest($data['id'])!=""){
			$_sql .= " and  p1.id = '{$data['id']}' ";
		}
		if (IsExiest($data['borrow_nid'])!=""){
			$_sql .= " and  p1.borrow_nid = '{$data['borrow_nid']}' ";
		}
		$sql = "select  p1.*,p2.username,p3.credits,p3.credit,p4.name as type_name,p5.name as style_name  from `{borrow}` as p1 
				 left join {borrow_type} as p4 on p1.borrow_type=p4.nid
				 left join {borrow_style} as p5 on p1.borrow_style=p5.nid
				 left join {users} as p2 on p1.user_id=p2.user_id
				 left join {credit} as p3 on p1.user_id=p3.user_id
				  $_sql
				";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "borrow_not_exiest";
        //借款是否到期
		$borrow_end_status = 0;
		if ($result['status']==1 && $result['borrow_end_time']<time()){
			$borrow_end_status = 1;
		}
		$result["borrow_end_status"] = $borrow_end_status;
        //借款的属性
		if ($result['flag']!=""){
			$_flag = explode(",",$result['flag']);
			foreach ($_flag as $_k => $_v){
				$result["_flag"][] = $_flag_result[$_v];
				$flag_name[] = $_flag_result[$_v]['name'];
			}
			$result["flag_name"] = join(",",$flag_name);
		}
	
        //借款状态id的属性
		$result["borrow_status_nid"] = self::GetBorrowStatusNid(array("status"=>$result['status'],"account"=>$result['account'],"borrow_end_status"=>$result["borrow_end_status"],"borrow_account_wait"=>$result["borrow_account_wait"],"repay_account_wait"=>$result["repay_account_wait"]));
		
        
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