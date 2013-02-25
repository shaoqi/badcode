<?
/******************************
 * $File: account.count.php
 * $Description: 资金类文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

//借款的统计
class accountCountClass
{
	
	
	/**
	 * 统计所有用户的资金记录
	 *
	 * @return Array
	 */
	public static function GetAccoutAll($data = array()){
		global $mysql;
		$sql = "select sum(use_money) as use_money_all,sum(no_use_money) as no_use_money_all,sum(collection) as collection_all,sum(total) as total_all from `{account}`";
		$result = $mysql->db_fetch_array($sql);
		return $result;
	}
	
	
	/**
	 * 统计资金记录的全部
	 *
	 * @return Array
	 */
	public static function GetAccoutLogAll($data = array()){
		global $mysql;
		$sql = "select sum(p1.money) as account,count(1) as num,p1.type,p2.name as type_name from `{account_log}` as p1 left join `{linkage}` as p2 on p1.type=p2.value where p2.type_id=30  group by  p1.type ";
		$result = $mysql->db_fetch_arrays($sql);
		if ($result!=false){
			foreach ($result as $key => $value){
				$_result[$value['type']] = $value['account'];
			}
		}	
		$_result["tender_recover"]  = $_result["tender_success_frost"]-$_result["tender_success"] ;
		return $_result;
	}
	
	function GetRechargeCount($data=array()){
		global $mysql;
		if (IsExiest($data['user_id'])!=false) {
			$_sql = " and p1.user_id = {$data['user_id']}";
		}
		$sql = "select sum(p1.money) as account,count(1) as num,p1.type from `{account_recharge}` as p1 where p1.status=1  $_sql group by  p1.type ";
		$result = $mysql->db_fetch_arrays($sql);
		if ($result!=false){
			foreach ($result as $key => $value){
				if ($value['stauts']==1){
					if ($value['type']==2){
						$_result['recharge_all_down'] += $value['account'];
					}elseif ($value['type']==1){
						$_result['recharge_all_up'] += $value['account'];
					}else{
						$_result['recharge_all_other'] += $value['account'];
					}
					$_result['recharge_all'] += $value['account'];
				}
			}
		}	
		return $_result;
	
	}
	
	
	function GetCashCount(){
		global $mysql;
		$sql = "select sum(p1.total) as account,sum(p1.credited) as credited_all,sum(p1.fee) as fee_all,count(1) as num from `{account_cash}` as p1 where p1.status=1  ";
		$result = $mysql->db_fetch_arrays($sql);
		
		return $result;
	
	}
	
    
    //用户资金统计
    function GetUsersCounts(){
		global $mysql;
		
        //获取还款方式
        
		$_select = "p2.*,p1.*,p3.username,p3.email";
		$_order = " order by p1.id ";
		$sql = "select SELECT from  `{account}` as p1 left join `{borrow_count}` as p2 on p1.user_id=p2.user_id left join `{users}` as p3 on p1.user_id=p3.user_id and p1.user_id>0 and p2.user_id>0  SQL ORDER LIMIT";
	
		//判断总的条数
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array('count(1) as num', $_sql,'', ''), $sql));
		$total = intval($row['num']);
		
		//分页返回结果
		$data['page'] = !IsExiest($data['page'])?1:$data['page'];
		$data['epage'] = !IsExiest($data['epage'])?10:$data['epage'];
		$total_page = ceil($total / $data['epage']);
		$_limit = " limit ".($data["epage"] * ($data["page"] - 1)).", {$data['epage']}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL','ORDER', 'LIMIT'), array($_select,$_sql,$_order, $_limit), $sql));
        $_user_id = array();
        if ($list!=false){
    	    foreach ($list as $key => $value){
    	       $_user_id[] = $value["user_id"];
    	    }
    		
            $user_ids = join(",",$_user_id);
            
            //充值成功的统计
            $_recharge_result = array();
            $sql = "select sum(money) as mnum,user_id from `{account_recharge}` where user_id in ({$user_ids}) group by user_id";
            $result = $mysql->db_fetch_arrays($sql);
            foreach($result as $key => $value){
                $_recharge_result[$value["user_id"]] = $value["mnum"];
            }
            
            //提现成功的统计
            $_cash_result = array();
            $sql = "select sum(total) as mnum,user_id from `{account_cash}` where status=1 and user_id in ({$user_ids}) group by user_id";
            $result = $mysql->db_fetch_arrays($sql);
            foreach($result as $key => $value){
                $_cash_result[$value["user_id"]] = $value["mnum"];
            }
            //已回收的统计
            $_recover_yes_result = array();
            $sql = "select sum(recover_account) as mnum,user_id from `{borrow_recover}` where recover_status=1 and  user_id in ({$user_ids}) group by user_id";
            $result = $mysql->db_fetch_arrays($sql);
            foreach($result as $key => $value){
                $_recover_yes_result[$value["user_id"]] = $value["mnum"];
            }
            
            //已借到的金额
            $_borrow_yes_result = array();
            $sql = "select sum(repay_capital) as mnum,user_id from `{borrow_repay}` where   user_id in ({$user_ids}) group by user_id";
            $result = $mysql->db_fetch_arrays($sql);
            foreach($result as $key => $value){
                $_borrow_yes_result[$value["user_id"]] = $value["mnum"];
            }
            
            //已借到的金额
            $_repay_yes_result = array();
            $sql = "select sum(repay_capital) as mnum,user_id from `{borrow_repay}` where repay_status=1 and  user_id in ({$user_ids}) group by user_id";
            $result = $mysql->db_fetch_arrays($sql);
            foreach($result as $key => $value){
                $_repay_yes_result[$value["user_id"]] = $value["mnum"];
            }
            
            //已借到的金额
            $_repay_wait_result = array();
            $sql = "select sum(repay_capital) as mnum,user_id from `{borrow_repay}` where repay_status=0 and  user_id in ({$user_ids}) group by user_id";
            $result = $mysql->db_fetch_arrays($sql);
            foreach($result as $key => $value){
                $_repay_wait_result[$value["user_id"]] = $value["mnum"];
            }
            
            //已投的金额
            $_tender_success_result = array();
            $sql = "select sum(account) as mnum,user_id from `{borrow_tender}` where status=1 and  user_id in ({$user_ids}) group by user_id";
            $result = $mysql->db_fetch_arrays($sql);
            foreach($result as $key => $value){
                $_tender_success_result[$value["user_id"]] = $value["mnum"];
            }
            
            foreach ($list as $key => $value){
    	       $list[$key]["rechare_success"] = $_recharge_result[$value["user_id"]];
    	       $list[$key]["recover_yes"] = $_borrow_yes_result[$value["user_id"]];
    	       $list[$key]["borrow_success"] = $_recharge_result[$value["user_id"]];
    	       $list[$key]["cash_success"] = $_cash_result[$value["user_id"]];
    	       $list[$key]["tender_success"] = $_tender_success_result[$value["user_id"]];
    	       $list[$key]["repay_yes"] = $_repay_yes_result[$value["user_id"]];
    	       $list[$key]["repay_wait"] = $_repay_wait_result[$value["user_id"]];
    	    }
        
        }
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		return $result;
	
	}
}
?>
