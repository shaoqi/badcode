<?
/******************************
 * $File: users.vip.php
 * $Description: 用户vip的管理中心
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

require_once(ROOT_PATH."modules/credit/credit.class.php");
require_once("users.friends.php");
class usersvipClass   {
	
	function usersvipClass(){
		//连接数据库基本信息
		
        
	}
	
	
	/**
     * 18，获取管理员管理的信息（users_adminlog）
     * @param $param array('user_id' => '会员ID')
	 * @return Array（'list'=>"列表",page=>'当前页面','epage'=>'页数','total_page'=>'总页面'）
     */
	function GetUsersVipList($data){
		global $mysql;
		$_sql = " where 1=1 and p1.status!=0 ";
		
		//判断用户id
		if (IsExiest($data['user_id']) != false){
			$_sql .= " and p1.`user_id`  = '{$data['user_id']}'";
		}
		
		//判断是否搜索用户名
		if (IsExiest($data['username']) != false){
			$_sql .= " and p2.`username` like '%{$data['username']}%'";
		}
		
		//搜索管理员
		if (IsExiest($data['adminname']) != false){
			$_sql .= " and p3.`adminname` like '%{$data['adminname']}%'";
		}
		
		//判断是否搜索邮箱
		if (IsExiest($data['status']) != false || $data['status']=="0"){
			$_sql .= " and p1.`status` = '{$data['status']}'";
		}
		
		$_select = "p1.*,p2.username,p3.adminname";
		$_order = " order by p1.status asc,p1.id desc";
		$sql = "select SELECT from `{users_vip}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id left join `{users_admin}` as p3 on p1.kefu_userid=p3.user_id SQL ORDER LIMIT";
		
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
	
	
	
	public static function GetUsersVip($data = array()){
		global $mysql;
		if (IsExiest($data['user_id'])=="") return false;
		$sql = "select p1.*,p2.adminname,p3.username from `{users_vip}` as p1 left join `{users_admin}` as p2 on p1.kefu_userid=p2.user_id left join `{users}` as p3 on p1.user_id=p3.user_id where p1.user_id={$data['user_id']}";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false){
			$sql = "insert into `{users_vip}` set user_id={$data['user_id']}";
			$mysql->db_query($sql);
			$sql = "select p1.*,p2.adminname,p3.username from `{users_vip}` as p1 left join `{users_admin}` as p2 on p1.kefu_userid=p2.user_id left join `{users}` as p3 on p1.user_id=p3.user_id where p1.user_id={$data['user_id']}";
	       	$result = $mysql->db_fetch_array($sql);
		}else{
			if ($result["status"]==1){
				if ($result["end_date"]!="" && $result["end_date"] < time()){
					$result["status"] = 3;
				}
			}
		}
         return $result;
	}
	
	public static function GetUsersVipStatus($data = array()){
		global $mysql;
		if (IsExiest($data['user_id'])=="") return false;
		$result = self::GetUsersVip($data);
		$status = $result["status"];
		if ($result["status"]==1){
			if ($result["end_date"]!="" && $result["end_date"] < time()){
				$status = 3;
			}
		}
		
		return $status;
	}
	
	/*
    * 申请成为vip
    *
    */
	public static function UsersVipApply($data = array()){
		global $mysql,$_G;
        
		if (IsExiest($data['user_id'])=="") return false;
		$result = self::GetUsersVip($data);
		if ($result["status"]==1){
			return "users_vip_status_yes";
        }elseif ($result["status"]==-1){
			return "users_vip_status_wait";
		}else{  
        
		    $_time = time();
            $years = ($data["years"]>0)?$data["years"]:1;
    		$vip_fee = $_G["system"]["con_vip_fee"]>0?$_G["system"]["con_vip_fee"]:l20;
    	    $money = $vip_fee*$years;
            
            $log_info["user_id"] = $data['user_id'];//操作用户id
            $log_info["nid"] = "vip_success_".$data['user_id']."_".$_time;
            $log_info["borrow_nid"] = "";//收入
            $log_info["account_web_status"] = 0;//
            $log_info["account_user_status"] = 0;//
            $log_info["code"] = "user";//
            $log_info["code_type"] = "vip_success";//
            $log_info["code_nid"] = $data['user_id'];//
            $log_info["money"] = $money;//操作金额
            $log_info["income"] = 0;//收入
            $log_info["expend"] = $money;//支出
            $log_info["balance_cash"] = -$log_info["money"];//可提现金额
            $log_info["balance_frost"] = 0;//不可提现金额
            $log_info["frost"] = 0;//冻结金额
            $log_info["await"] = 0;//待收金额
            $log_info["repay"] = 0;//待还金额
            $log_info["type"] = "vip_success";//类型
            $log_info["to_userid"] = 0;//付给谁
            $log_info["remark"] =  "Vip申请扣除金额";
            accountClass::AddLog($log_info);
           
            $user_log["user_id"] = $data['user_id'];
            $user_log["code"] = "users";
            $user_log["type"] = "vip_frost";
            $user_log["operating"] = "apply";
            $user_log["article_id"] = $data['user_id'];
            $user_log["result"] = 1;
            $user_log["content"] = "申请成为VIP会员";
            usersClass::AddUsersLog($user_log);
			
			$remind['nid'] = "vip_success";
			$remind['receive_userid'] = $data['user_id'];
			$remind['remind_nid'] =  "vip_success_".$data["user_id"]."_".time();
			$remind['article_id'] = $data['user_id'];
			$remind['code'] = "users";
			$remind['title'] = "申请VIP成功";
			$remind['content'] = "尊敬的用户恭喜您申请VIP成功。";
			remindClass::sendRemind($remind);
			
			
			$_result=usersFriendsClass::GetUsersInviteOne(array("user_id"=>$data['user_id']));
			
		    if ($_result['user_id']>0 && $_G["system"]["con_friend_vip_money"]>0){
					$log_info["user_id"] = $_result['user_id'];
        			$log_info["nid"] = "vip_ticheng_".$_result['user_id'].$data['user_id'];
                    $log_info["account_web_status"] = 1;//
                    $log_info["account_user_status"] = 1;//
                    $log_info["code"] = "user";//
        			$log_info["code_type"] = "friend_vip_success";//
        			$log_info["code_nid"] = $data['user_id'];//
        			$log_info['money'] = $_G["system"]["con_friend_vip_money"];
        			$log_info["income"] = $log_info['money'];
        			$log_info["expend"] = 0;
        			$log_info["balance_cash"] = $log_info['money'];
        			$log_info["balance_frost"] = 0;
        			$log_info["frost"] = 0;
        			$log_info["await"] = 0;
        			$log_info["type"] = "friend_vip_success";
        			$log_info["to_userid"] = 0;
        			$log_info["remark"] =  "邀请的好友申请VIP审核通过";
        			accountClass::AddLog($log_info);					
			}			
			$first_time = time();
            $end_time = strtotime("+12 month",$first_time);	
			$sql = "update `{users_vip}` set years={$data['years']},`addtime` = '".$_time."',`verify_time` = '".$_time."',`addip` = '".ip_address()."',status=1,kefu_userid='{$data['kefu_userid']}',remark='{$data['remark']}',first_date='".$first_time."',end_date='".$end_time."',money='{$money}' where user_id='{$data['user_id']}'";
			$mysql->db_query($sql);
			
            $sql = "insert into `{users_viplog}` set type='{$data['years']}',money='{$money}',user_id='{$data['user_id']}',first_time='{$first_time}',end_time='{$end_time}',addtime='".time()."',addip='".ip_address()."'";
            $mysql->db_query($sql);
			
			return 1;
		}
	}
	
	
	//vip统一加31天
	public static function UsersVipNew($data = array()){
		global $mysql,$_G;
		if (IsExiest($data['user_id'])=="") return false;
		
		$account_result = accountClass::GetOne(array("user_id"=>$data['user_id']));
		$balance = $account_result['balance'];
            
		$sql = "select * from  `{users_vip}` where user_id='{$data['user_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false){
			$sql = "insert into `{users_vip}` set user_id='{$data['user_id']}'";
			$mysql->db_query($sql);
			$sql = "select * from  `{users_vip}` where user_id='{$data['user_id']}'";
			$result = $mysql->db_fetch_array($sql);
		}
		$first_date = empty($result['first_date'])?time():$result['first_date'];
		
		if ($result['end_date']==""){
			$first_time = time();
		}elseif ($result['end_date']<time()){
			$first_time = time();
		}else{
			$first_time = $result['end_date'];
		}
		$year = ($data["year"]>0)?$data["year"]:1;
		$vip_fee = ($_G["system"]["con_vip_fee"]>0)?$_G["system"]["con_vip_fee"]:l20;
	    $money = $vip_fee*$year;
		if ($balance<$money){
			return "users_vip_balance_not";
		}
		if ($money>0){
	        $log_info["user_id"] = $data['user_id'];//操作用户id
			$log_info["nid"] = "vip_frost_".$data['user_id']."_".$end_time;
			$log_info["borrow_nid"] = "";//收入
            $log_info["account_web_status"] = 0;//
            $log_info["account_user_status"] = 0;//
			$log_info["code"] = "user";//
			$log_info["code_type"] = "vip_frost";//
			$log_info["code_nid"] = $data['user_id'];//
			$log_info["money"] = $money;//操作金额
			$log_info["income"] = 0;//收入
			$log_info["expend"] = 0;//支出
			$log_info["balance_cash"] = -$log_info["money"];//可提现金额
			$log_info["balance_frost"] = 0;//不可提现金额
			$log_info["frost"] = $log_info["money"];//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["repay"] = 0;//待还金额
			$log_info["type"] = "vip_frost";//类型
			$log_info["to_userid"] = 0;//付给谁
			$log_info["remark"] =  "Vip申请冻结金额";
	        accountClass::AddLog($log_info);
		}
				
		$user_log["user_id"] = $data['user_id'];
		$user_log["code"] = "users";
		$user_log["type"] = "vip_frost";
		$user_log["operating"] = "apply";
		$user_log["article_id"] = $data['user_id'];
		$user_log["result"] = 1;
		$user_log["content"] = "申请成为VIP会员";
		usersClass::AddUsersLog($user_log);
			
		$sql = "insert into `{users_viplog}` set type='{$data['type']}',money='{$money}',user_id='{$data['user_id']}',first_time='{$first_time}',end_time='{$end_time}',addtime='".time()."',addip='".ip_address()."'";
		$mysql->db_query($sql);
		
		$sql = "update `{users_vip}` set status=1,first_date='{$first_date}',end_date='{$end_time}' where user_id='{$data['user_id']}'";
		$mysql->db_query($sql);
		echo $data['user_id'];
	}
	
	
	/**
     * 18，获取管理员管理的信息（users_adminlog）
     * @param $param array('user_id' => '会员ID')
	 * @return Array（'list'=>"列表",page=>'当前页面','epage'=>'页数','total_page'=>'总页面'）
     */
	function GetUsersVipLogList($data){
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
		if (IsExiest($data['status']) != false || $data['status']=="0"){
			$_sql .= " and p1.`status` = '{$data['status']}'";
		}
		
		$_select = "p1.*,p2.username";
		$_order = " order by id desc";
		$sql = "select SELECT from `{users_viplog}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id  SQL ORDER LIMIT";
		
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
	
	
	public static function UpdateUsersVipKefu($data = array()){
		global $mysql;
		if (IsExiest($data['user_id'])=="") return false;
		
		$sql = "update `{users_vip}` set kefu_userid={$data['kefu_userid']} where user_id='{$data['user_id']}'";
		return $mysql->db_query($sql);
		
	}
	
	public static function CheckUsersVip($data = array()){
		global $mysql,$_G;
		if (IsExiest($data['user_id'])=="") return false;
		$vip_result = self::GetUsersVip($data);
		if ($vip_result["status"]==1){
			return "users_vip_status_yes";
		}else{			
			if($data['status']==1){
			    if ($vip_result["status"]==1){ 
            		if ($vip_result['end_date']==""){
            			$first_time = time();
            		}elseif ($vip_result['end_date']<time()){
            			$first_time = time();
            		}else{
            			$first_time = $vip_result['end_date'];
            		}
                }else{
                    $first_time = time();
                }
                
                $end_time = strtotime("+12 month",$first_time);
                
    			$log_info["user_id"] = $data['user_id'];
    			$log_info["nid"] = "vip_success_".$data['user_id']."_".$vip_result["addtime"];
                $log_info["account_web_status"] = 1;//
                $log_info["account_user_status"] = 1;//
                $log_info["code"] = "user";//
    			$log_info["code_type"] = "user_vip_success";//
    			$log_info["code_nid"] = $data['user_id'];//
    			$log_info['money'] = $vip_result["money"];
    			$log_info["income"] = 0;
    			$log_info["expend"] = $log_info['money'];
    			$log_info["balance_cash"] = 0;
    			$log_info["balance_frost"] = 0;
    			$log_info["frost"] = -$vip["money"];
    			$log_info["await"] = 0;
    			$log_info["type"] = "vip_success";
    			$log_info["to_userid"] = 0;
    			$log_info["remark"] =  "申请VIP审核通过";
    			accountClass::AddLog($log_info);
                
    			$user_log["user_id"] = $data['user_id'];
    			$user_log["code"] = "users";
    			$user_log["type"] = "vip_success";
    			$user_log["operating"] = "success";
    			$user_log["article_id"] = $data['user_id'];
    			$user_log["result"] = 1;
    			$user_log["content"] = "申请成为VIP会员成功";
    			usersClass::AddUsersLog($user_log);
   				
    			$credit_log['user_id'] = $data['user_id'];
    			$credit_log['nid'] = "vip_success";
    			$credit_log['code'] = "approve";
    			$credit_log['type'] = "approve";
    			$credit_log['addtime'] = time();
    			$credit_log['article_id'] =$data['user_id'];
    			$credit_log['remark'] = "申请vip通过获得的积分";
    			creditClass::ActionCreditLog($credit_log);
                
                $_result=usersFriendsClass::GetUsersInviteOne(array("friends_userid"=>$data['user_id']));
				if ($_result['user_id']>0 && $_G["system"]["con_friend_vip_money"]>0){
					$log_info["user_id"] = $_result['user_id'];
        			$log_info["nid"] = "vip_ticheng_".$_result['user_id'];
                    $log_info["account_web_status"] = 1;//
                    $log_info["account_user_status"] = 1;//
                    $log_info["code"] = "user";//
        			$log_info["code_type"] = "user_vip_success";//
        			$log_info["code_nid"] = $data['user_id'];//
        			$log_info['money'] = $_G["system"]["con_friend_vip_money"];
        			$log_info["income"] = $log_info['money'];
        			$log_info["expend"] = 0;
        			$log_info["balance_cash"] = 0;
        			$log_info["balance_frost"] = 0;
        			$log_info["frost"] = -$vip["money"];
        			$log_info["await"] = 0;
        			$log_info["type"] = "vip_success";
        			$log_info["to_userid"] = 0;
        			$log_info["remark"] =  "申请VIP审核通过";
        			accountClass::AddLog($log_info);
				}
                
               	$sql = "insert into `{users_viplog}` set type='{$data['years']}',money='{$money}',user_id='{$data['user_id']}',first_time='{$first_time}',end_time='{$end_time}',addtime='".time()."',addip='".ip_address()."'";
                $mysql->db_query($sql);
        
    			$sql = "update `{users_vip}` set status=1,kefu_userid='{$data['kefu_userid']}',years={$data['years']},verify_userid={$data['verify_userid']},first_date='".$first_time."',end_date='".$end_time."',verify_time='{$data['verify_time']}',verify_remark='{$data['verify_remark']}' where user_id='{$data['user_id']}'";
    			$result = $mysql->db_query($sql);
                
		        //print_r($log_info);exit;
                $remind['nid'] = "vip_success";
        		$remind['receive_userid'] = $data['user_id'];
                $remind['remind_nid'] =  "vip_success_".$data["user_id"]."_".$vip_result["addtime"];
        		$remind['article_id'] = $data['user_id'];
        		$remind['code'] = "users";
        		$remind['title'] = "申请VIP成功";
        		$remind['content'] = "尊敬的用户恭喜您申请VIP成功。";
        		remindClass::sendRemind($remind);
				return 1;
    				
    		}elseif($data['status']==2){
    		  
    			$log_info["user_id"] = $data['user_id'];
    			$log_info["nid"] = "vip_false_".$data['user_id']."_".$vip_result["addtime"];
                $log_info["account_web_status"] = 0;//
                $log_info["account_user_status"] = 0;//
                $log_info["code"] = "user";//
    			$log_info["code_type"] = "user_vip_false";//
    			$log_info["code_nid"] = $data['user_id'];//
    			$log_info['money'] = $vip_result["money"];
    			$log_info["income"] = 0;
    			$log_info["expend"] = 0;
    			$log_info["balance_cash"] = $log_info["money"];
    			$log_info["balance_frost"] = 0;
    			$log_info["frost"] = -$log_info["money"];
    			$log_info["await"] = 0;
    			$log_info["type"] = "vip_false";
    			$log_info["to_userid"] = 0;
    			$log_info["remark"] =  "申请VIP审核不通过";
    			accountClass::AddLog($log_info);	
              
                $sql = "update `{users_vip}` set status=2,verify_userid={$data['verify_userid']},verify_time='{$data['verify_time']}',verify_remark='{$data['verify_remark']}' where user_id='{$data['user_id']}'";
    			$mysql->db_query($sql);
                 
                $remind['nid'] = "vip_false";
        		$remind['receive_userid'] = $data['user_id'];
                $remind['remind_nid'] =  "vip_false_".$data['user_id']."_".$vip_result["addtime"];
        		$remind['article_id'] = $data['user_id'];
        		$remind['code'] = "users";
        		$remind['title'] = "申请VIP失败";
        		$remind['content'] = "尊敬的用户您申请的VIP未通过。原因【{$data['verify_remark']}】";
        		remindClass::sendRemind($remind);
    		
    		}
            return $data['user_id'];
                
        }
	}
	
}
?>