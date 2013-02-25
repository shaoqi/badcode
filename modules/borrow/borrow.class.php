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

if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

require_once(ROOT_PATH."modules/account/account.class.php");
require_once(ROOT_PATH."modules/credit/credit.class.php");
require_once(ROOT_PATH."modules/remind/remind.class.php");
require_once(ROOT_PATH."modules/borrow/borrow.model.php");
require_once(ROOT_PATH."modules/users/users.class.php");
require_once("borrow.count.php");
require_once("borrow.amount.php");
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
		}elseif ($data['status']==6){
			$borrow_status_nid = "over";
		}elseif ($data['status']==2){
			$borrow_status_nid = "false";
		}elseif ($data['status']==3){
			if ($data['repay_advance_status']==1){
				$borrow_status_nid = "repay_advance";
			}elseif ($data['repay_account_wait']==0){
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
			$_sql .= " and p1.`borrow_nid` like '%{$data['borrow_nid']}%'";
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
        //满标的借款
        if ($data['query_type']=="full" && $data['status_nid']==""){
            $data['status_nid'] = "full";
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
                $_sql .= " and  ((p1.borrow_type!='roam' and p1.status=1) or (p1.borrow_type='roam' and (p1.status=3 or p1.status=1) )) and p1.borrow_status=1 and p1.borrow_end_time >".time()." and p1.borrow_account_wait>0";
            }  
            //初审失败
            elseif ($status_nid=="false"){
                $_sql .= " and p1.status=2 ";
            }
            //已过期
            elseif ($status_nid=="late"){
                $_sql .= " and p1.status=1 and p1.borrow_status=1 and p1.borrow_end_time <".time()." and p1.borrow_account_wait>0 ";
            }
            //逾期借款
            elseif ($status_nid=="lates"){
                //$_sql .= " and p1.status=3 and p1.borrow_full_status=1 and p1.repay_next_time >".time()."  ";
            }
            //满标
            elseif ($status_nid=="full"){
                $_sql .= " and p1.status=1 and p1.borrow_status=1 and ( p1.account=p1.borrow_account_yes or p4.part_status=1) ";
            }
            //满标失败
            elseif ($status_nid=="full_false"){
                $_sql .= " and p1.status = 4 ";
            }
            //还款中
            elseif ($status_nid=="repay"){
                $_sql .= " and p1.status=3  and p1.borrow_status=1 and p1.borrow_full_status=1 and p1.borrow_account_yes>p1.repay_account_yes ";
            } 
            //已还完
            elseif ($status_nid=="repay_yes"){
                $_sql .= " and p1.status=3  and p1.borrow_status=1 and p1.borrow_full_status=1 and p1.repay_advance_status=0 and p1.repay_account_wait=0 ";
            }
             //已还完
            elseif ($status_nid=="repay_over"){
                $_sql .= " and p1.status=3  and p1.borrow_status=1 and p1.borrow_full_status=1 and repay_full_status=1 ";
            }
             //已还完
            elseif ($status_nid=="repay_advance"){
                $_sql .= " and p1.status=3  and p1.borrow_status=1 and p1.borrow_full_status=1 and p1.repay_advance_status=1 ";
            }
            //提前还完
            elseif ($status_nid=="repay_advance"){
                $_sql .= " and p1.status=3  and p1.borrow_status=1 and p1.borrow_full_status=1 and p1.repay_full_status=1 and repay_advance_status=1 ";
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
            } //正在招标的借款
            elseif ($status_nid=="invest" ){
                $_sql .= " and ((p1.status=1 and p1.borrow_end_time> ".time().") or p1.status=3)";
                //$data['order'] = "all";
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
				
		//判断流转标是否流转完，抵押标是否还完
		if (IsExiest($data['roam_wait']) ){
			$_sql .= " and ((p1.borrow_account_scale !=100 and p1.borrow_type = 'roam')or ( p1.borrow_type = 'pawn' and ( p1.status=3 and p1.repay_account_wait!=0 ) or (p1.status=1) ))";
		}
		
		
		//忽略类型
		if (IsExiest($data['omit_type']) ){
			$_sql .= " and p1.borrow_type != '{$data['omit_type']}'";
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
        if (IsExiest($data['sou_type']!="")){
			$_sql .= " and p1.borrow_type = '{$data['sou_type']}'";
		}
		if (IsExiest($data['sou_apr']!="")){
			$apr = explode(',',$data['sou_apr']);
			$aprmin = $apr[0];
			$aprmax = $apr[1];			
			$_sql .= " and p1.borrow_apr >= '{$aprmin}' and p1.borrow_apr <= '{$aprmax}'";
		}
		if (IsExiest($data['sou_period']!="")){
			$apr = explode(',',$data['sou_period']);
			$periodmin = $apr[0];
			$periodmax = $apr[1];			
			$_sql .= " and p1.borrow_period >= '{$periodmin}' and p1.borrow_period <= '{$periodmax}'";
		}
		if (IsExiest($data['sou_account']!="")){
			$apr = explode(',',$data['sou_account']);
			$accountmin = 10000*$apr[0];
			$accountmax = 10000*$apr[1];	
			if($accountmax!=0){	
				$_sql .= " and p1.account >= '{$accountmin}' and p1.account <= '{$accountmax}'";
			}else{
				$_sql .= " and p1.account >= '{$accountmin}'";
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
			if ($order == "apr_up"){
				$_order = " order by p1.`borrow_apr` desc,p1.id desc ";
			}else if ($order == "apr_down"){
				$_order = " order by p1.`borrow_apr` asc,p1.id desc ";
			}
			if ($order == "scale_up"){
				$_order = " order by p1.`borrow_account_scale` desc,p1.id desc ";
				
			}else if ($order == "scale_down"){
				$_order = " order by p1.`borrow_account_scale` asc,p1.id desc ";
			}
			if ($order == "period_up"){
				$_order = " order by p1.`borrow_period` desc,p1.id desc ";
				
			}else if ($order == "period_down"){
				$_order = " order by p1.`borrow_period` asc,p1.id desc ";
			}
			if ($order == "datetime_up"){
				$_order = " order by p1.`verify_time` desc,p1.id desc ";
				
			}else if ($order == "datetime_down"){
				$_order = " order by p1.`verify_time` asc,p1.id desc ";
			}
			if ($order == "flag"){
				$_order = " order by p1.vouch_status desc,p1.`flag` desc,p1.id desc ";
			}
			if ($order == "index"){
				$_order = " order by p1.`flag` desc,p1.id desc ";
			}	
			if ($order == "all"){
				$_order = " order by p1.`status` asc,p1.addtime desc";
			}			
		}
		
		$flag_sql = "select p1.*,p2.fileurl from `{borrow_flag}` as p1 left join `{users_upfiles}` as p2 on p1.upfiles_id=p2.id ";
		$flag_result = $mysql->db_fetch_arrays($flag_sql);
		if (is_array($flag_result)){
			foreach ($flag_result as $key => $value){
				$_flag_result[$value['id']] = $value;
			}
		}
		//print_r($data);print_r($_sql);
		$_select = " p1.*,p2.username,p3.credits,p3.credit,p4.name as type_name,p4.title as type_title,p5.name as style_name,p5.title as style_title,p6.live_city as city";
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
			$list=$mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array($_select, $_sql, $_order, $_limit), $sql));
		      foreach ($list as $key => $value){
        			$list[$key]["borrow_status_nid"] = self::GetBorrowStatusNid(array("status"=>$value['status'],"account"=>$value['account'],"borrow_end_status"=>$borrow_end_status,"borrow_account_wait"=>$value["borrow_account_wait"],"repay_account_wait"=>$value["repay_account_wait"],"repay_full_status"=>$value["repay_full_status"],"repay_advance_status"=>$value["repay_advance_status"])) ;
        			 $period_name = "个月";
                    if ($value["borrow_type"]=="day"){
                        $period_name = "天";
                    }
                    $list[$key]["borrow_period_name"] =$value["borrow_period"].$period_name;
                    if (file_exists(DEAYOU_PATH."modules/borrow/borrow.roam.php")){
                        if ($value["borrow_type"]=="roam"){
                            $sql = "select * from `{borrow_roam}` where borrow_nid = '{$value['borrow_nid']}'";
                            $roam_result = $mysql->db_fetch_array($sql);
                            $list[$key]["roam"] = $roam_result;
                        }
                    }
            }
			return $list;
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
            $period_name = "个月";
            if ($value["borrow_type"]=="day"){
                $period_name = "天";
            }
            $list[$key]["borrow_period_name"] =$value["borrow_period"].$period_name;
            //判断有加入流转标不
            if (file_exists(DEAYOU_PATH."modules/borrow/borrow.roam.php")){
                if ($value["borrow_type"]=="roam"){
                    $sql = "select * from `{borrow_roam}` where borrow_nid = '{$value['borrow_nid']}'";
                    $roam_result = $mysql->db_fetch_array($sql);
                    $list[$key]["roam"] = $roam_result;
                }
            }
            /*
            //借款的属性
			if ($value['flag']!=""){
				$_flag = explode(",",$value['flag']);
				foreach ($_flag as $_k => $_v){
					$list[$key]["_flag"][] = $_flag_result[$_v];
					$flag_name[] = $_flag_result[$_v]['name'];
				}
				$list[$key]["flag_name"] = join(",",$flag_name);
			}
		      */
            //借款状态id的属性
			$list[$key]["borrow_status_nid"] = self::GetBorrowStatusNid(array("status"=>$value['status'],"account"=>$value['account'],"borrow_end_status"=>$borrow_end_status,"borrow_account_wait"=>$value["borrow_account_wait"],"repay_account_wait"=>$value["repay_account_wait"],"repay_full_status"=>$value["repay_full_status"],"repay_advance_status"=>$value["repay_advance_status"])) ;
			
			//获取商业图片
			$resutlimg = usersClass::GetUsersImagesOne(array("user_id"=>$value["user_id"]));
			if($resutlimg==true){
				$list[$key]["fileurl"] = $resutlimg['fileurl'];
           }		   
		   //获取额度
		   $result_amount = borrowAmountClass::GetAmountUsers(array("user_id"=>$value["user_id"]));
		   $list[$key]["pawn_amount"] = $result_amount['pawn'];
		   
		   //格式数据 borrow_account_wait
		   if($list[$key]["pawn_amount"]%10000==0){
			 $list[$key]['pawn_status']['key']=1;
			 $list[$key]['pawn_status']['value']=round($list[$key]["pawn_amount"]/10000);			 
		   }
		   if($list[$key]["account"]%10000==0 ){
			 $list[$key]['account_status']['key']=1;
			 $list[$key]['account_status']['value']=round($list[$key]["account"]/10000);			 
		   }
		   if($list[$key]["borrow_account_wait"]%10000==0&&$list[$key]["borrow_account_wait"]!=0){
			 $list[$key]['account_wait_status']['key']=1;
			 $list[$key]['account_wait_status']['value']=round($list[$key]["borrow_account_wait"]/10000);			 
		   }
		   
		   
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
		
		$sql = "select  p1.*,p2.username,p3.credits,p3.credit,p4.name as type_name,p4.title as type_title,p4.part_status as type_part_status,p5.name as style_name,p5.title as style_title  from `{borrow}` as p1 
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
        $period_name = "个月";
	    if ($result["borrow_type"]=="day"){
            $period_name = "天";
        }
       $result["borrow_period_name"] =$result["borrow_period"].$period_name;
        //借款状态id的属性
		$result["borrow_status_nid"] = self::GetBorrowStatusNid(array("status"=>$result['status'],"account"=>$result['account'],"borrow_end_status"=>$result["borrow_end_status"],"borrow_account_wait"=>$result["borrow_account_wait"],"repay_account_wait"=>$result["repay_account_wait"],"repay_full_status"=>$result["repay_full_status"],"repay_advance_status"=>$result["repay_advance_status"]));
		
		require_once(ROOT_PATH."modules/borrow/borrow.calculates.php");
		if ($result['borrow_type']!="day" && $result['borrow_type']!="second"){
			$_equal["account"] = $result["account"];
			$_equal["period"] = $result["borrow_period"];
			$_equal["apr"] = $result["borrow_apr"];
			$_equal["style"] = $result["borrow_style"];
			$_equal["type"] = "all";
			$equal_result = borrowCalculateClass::GetType($_equal);
			$result["repay_month_account"] = $equal_result['repay_month'];
			$_equal["account"] = "100";
			$equal_result = borrowCalculateClass::GetType($_equal);
			$result["borrow_100_interest"] = $equal_result['interest_total'];
		}else{
			$result["repay_month_account"] = $result["account"]+round($result['borrow_apr']/100/365*$result['borrow_period']*$result['account'],2);
		}
		if($result["borrow_type"]=="roam"){
			
			$result["repay_last_time"]=strtotime("{$result["borrow_period"]} month",$result['verify_time']);
		}
		
        $result['borrow_other_time'] = $result['borrow_end_time']-time();
        $result['repay_account_interest_lost'] = $result['repay_account_interest']-$result['repay_account_interest_yes'];
		$result['borrow_contents'] = htmlspecialchars_decode($result['borrow_contents']);
        return $result;
        
	}
   
	/**
	 * 查看借款标
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
		$sql = "select p1.* ,p2.username,p3.username as verify_username,p4.* from `{borrow}` as p1 
				  left join `{users}` as p2 on p1.user_id=p2.user_id 
				  left join `{users}` as p3 on p1.verify_userid = p3.user_id 
				  left join `{borrow_roam}` as p4 on p1.borrow_nid = p4.borrow_nid 
				  $_sql
				";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "borrow_not_exiest";
		return $result;
	}
	
	//获取借款的积分
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
		$_result[1] = empty($attcredit['num'])?0:$attcredit['num'];
		$_result[2] = empty($credit_log['creditnum'])?0:$credit_log['creditnum'];
		$_result[3] = empty($approve['creditnum'])?0:$approve['creditnum'];
        
        $credit_total =$_result[1]+$credit_tongji['tongji'];
        $_credit['credit'] = $credit_total;
        $_credit['class'] = "borrow";
        $credit = creditClass::GetUserCreditRank($_credit);
		$result = array("credit_total"=>$credit_total,"borrow_credit"=>$_result[2]+$_result[3],"tender_credit"=>$credit_tongji['tongji']-$_result[2]-$_result[3],"approve_credit"=>$_result[3],"att_credit"=>$_result[1],"credit"=>$credit);
		//$result = array("approve_credit"=>$_result[1]+$credit_tongji['tongji'],"borrow_credit"=>$_result[2],"approve"=>$_result[3]);
		return $result;
	}
    
    function GetDays($data = array()){
        global $mysql;
        if ($data["now_time"]==""){
            $data["now_time"] = time();
        }
        $_repay_time = get_mktime(date("Y-m-d",$data["repay_time"]));
        $_now_time = get_mktime(date("Y-m-d",$data["now_time"]));                
        $late_days = ($_now_time - $_repay_time)/(60*60*24);
        return $late_days;
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
			return -2;
		}
	}
	//逾期还款列表
	function GetLateList($data = array()){
		global $mysql,$_G;
		
		$page = (!isset($data['page']) || $data['page']=="")?1:$data['page'];
		$epage = (!isset($data['epage']) || $data['epage']=="")?10:$data['epage'];
		
		$_select = 'p1.*,p3.*,p5.card_id,p6.name as job_name,p6.tel as job_tel,p6.work_city as job_address,p7.province,p7.city,p8.*,p9.live_area';
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
		left join `{rating_contact}` as p9 on p1.user_id=p9.user_id
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
}
?>