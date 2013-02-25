<?php


class accountTongjiClass
{


	function GetList($data){
		global $mysql;
       	$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
        $_select = "p1.user_id,p1.username,p2.realname";
        $_order = "order by p1.user_id desc";
		$sql = "select SELECT from `{users}` as p1 left join `{users_info}` as p2 on p1.user_id=p2.user_id ORDER LIMIT";
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num', '', ''), $sql));
        	
		$total = $row['num'];
		$total_page = ceil($total / $epage);
		$index = $epage * ($page - 1);
		$limit = " limit {$index}, {$epage}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,$_order, $limit), $sql));		
		$list = $list?$list:array();
		foreach ($list as $key => $value){
			$_user_id[] = $value['user_id'];
		}
        $user_ids = join(",",$_user_id);
        
        
        //判断添加时间开始
		if (IsExiest($data['dotime1']) != false){
			$dotime1 = ($data['dotime1']=="request")?$_REQUEST['dotime1']:$data['dotime1'];
			if ($dotime1!=""){
				$_sql .= " and p1.addtime >= ".get_mktime($dotime1);
				$_recover_sql .= " and p1.recover_yestime >= ".get_mktime($dotime1);
				$_repay_sql .= " and p1.repay_yestime >= ".get_mktime($dotime1);
			}
		}
		
		//判断添加时间结束
		if (IsExiest($data['dotime2'])!=false){
			$dotime2 = ($data['dotime2']=="request")?$_REQUEST['dotime2']:$data['dotime2'];
			if ($dotime2!=""){
				$_sql .= " and p1.addtime < ".get_mktime($dotime2);
				$_recover_sql .= " and p1.recover_yestime > ".get_mktime($dotime2);
				$_repay_sql .= " and p1.repay_yestime > ".get_mktime($dotime2);
			}
		}
        //充值
        $sql = "select sum(p1.money) as _money,sum(p1.fee) as _fee,p1.user_id from `{account_recharge}` as p1 where p1.status=1 and p1.user_id in ({$user_ids}) {$_sql} group by p1.user_id";
        $recharge_result = $mysql->db_fetch_arrays($sql);
        
         //回收本金和利息
        //$sql = "select sum(p1.recover_capital) as _recover_capital,sum(p1.recover_interest) as _recover_interest,p1.user_id from `{borrow_recover}` as p1 where p1.recover_status='1' and p1.user_id in ({$user_ids}) {$_recover_sql} group by p1.user_id";
        $sql = "select sum(p1.recover_capital) as _recover_capital,sum(p1.recover_interest) as _recover_interest,p1.user_id,p1.recover_status from `{borrow_recover}` as p1 where p1.user_id in ({$user_ids}) {$_recover_sql} group by p1.user_id";
        $borrow_recover_result = $mysql->db_fetch_arrays($sql);
        
          //已还本金和利息
        $sql = "select sum(p1.repay_capital) as _repay_capital,sum(p1.repay_interest) as _repay_interest,p1.user_id from `{borrow_repay}` as p1 where p1.repay_status='1' and p1.user_id in ({$user_ids}) {$_repay_sql} group by p1.user_id";
        $borrow_repay_result = $mysql->db_fetch_arrays($sql);
         //借款成功
        $sql = "select sum(p1.money) as _money,p1.user_id from `{account_log}` as p1 where p1.type='borrow_success' and p1.user_id in ({$user_ids}) {$_sql} group by p1.user_id";
        $borrow_result = $mysql->db_fetch_arrays($sql);
        
        //借款手续费
        $sql = "select sum(p1.money) as _money,p1.user_id from `{account_log}` as p1 where p1.type='borrow_fee' and p1.user_id in ({$user_ids}) {$_sql} group by p1.user_id";
        $borrow_fee_result = $mysql->db_fetch_arrays($sql);
        
        
        //投资金额
        $sql = "select sum(p1.money) as _money,p1.user_id from `{account_log}` as p1 where p1.type='tender' and p1.user_id in ({$user_ids}) {$_sql} group by p1.user_id";
        $tender_result = $mysql->db_fetch_arrays($sql);
        
        
        //投资成功
        $sql = "select sum(p1.money) as _money,p1.user_id from `{account_log}` as p1 where p1.type='tender_success' and p1.user_id in ({$user_ids}) {$_sql} group by p1.user_id";
        $tender_success_result = $mysql->db_fetch_arrays($sql);
        
        
        
        //提现成功
        $sql = "select sum(p1.money) as _money,p1.user_id from `{account_log}` as p1 where p1.type='cash_success' and p1.user_id in ({$user_ids}) {$_sql} group by p1.user_id";
        $cash_success_result = $mysql->db_fetch_arrays($sql);
        
       	foreach ($list as $key => $value){
       	    $list[$key]["recharge_account"] = 0;
       	    $list[$key]["recharge_fee"] = 0;
       	    foreach($recharge_result as $_key => $_value){
       	        if ($value['user_id'] == $_value['user_id']){
       	             $list[$key]["recharge_account"] = $_value['_money'];
       	             $list[$key]["recharge_fee"] = $_value['_fee'];
       	        }
       	    }
			
            $list[$key]["borrow_success"] = 0;
       	    foreach($borrow_result as $_key => $_value){
       	        if ($value['user_id'] == $_value['user_id']){
       	             $list[$key]["borrow_success"] = $_value['_money'];
       	        }
       	    }
            	
            $list[$key]["cash_success"] = 0;
       	    foreach($cash_success_result as $_key => $_value){
       	        if ($value['user_id'] == $_value['user_id']){
       	             $list[$key]["cash_success"] = $_value['_money'];
       	        }
       	    }
            
            
            $list[$key]["recover_capital"] = 0;
            $list[$key]["recover_interest"] = 0;
            $list[$key]["recover_capital_wait"] = 0;
            $list[$key]["recover_interest_wait"] = 0;
       	    foreach($borrow_recover_result as $_key => $_value){
       	        if ($value['user_id'] == $_value['user_id']){
					if ($_value['recover_status']==1){
						$list[$key]["recover_capital"] = $_value['_recover_capital'];
						$list[$key]["recover_interest"] = $_value['_recover_interest'];
					}else{
						$list[$key]["recover_capital_wait"] = $_value['_recover_capital'];
						$list[$key]["recover_interest_wait"] = $_value['_recover_interest'];
					}
       	        }
       	    }
            
            
             $list[$key]["repay_capital"] = 0;
             $list[$key]["repay_interest"] = 0;
       	    foreach($borrow_repay_result as $_key => $_value){
       	        if ($value['user_id'] == $_value['user_id']){
       	             $list[$key]["repay_capital"] = $_value['_repay_capital'];
       	             $list[$key]["repay_interest"] = $_value['_repay_interest'];
       	        }
       	    }
            
            $list[$key]["borrow_fee"] = 0;
       	    foreach($borrow_fee_result as $_key => $_value){
       	        if ($value['user_id'] == $_value['user_id']){
       	             $list[$key]["borrow_fee"] = $_value['_money'];
       	        }
       	    }
            
             $list[$key]["tender"] = 0;
       	    foreach($tender_result as $_key => $_value){
       	        if ($value['user_id'] == $_value['user_id']){
       	             $list[$key]["tender"] = $_value['_money'];
       	        }
       	    }
            
            $list[$key]["tender_success"] = 0;
       	    foreach($tender_success_result as $_key => $_value){
       	        if ($value['user_id'] == $_value['user_id']){
       	             $list[$key]["tender_success"] = $_value['_money'];
       	        }
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
	
    
    
	function GetUsersList($data){
		global $mysql;
       	$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
        $startdate=strtotime($data['dotime1']);
        $enddate=strtotime($data['dotime2']);    
        $total=round(($enddate-$startdate)/3600/24)+1;
        if ($total<0) return;
        if ($data['username']=="") return;
        //判断page是否为0
        $data['username'] = urldecode($data['username']);
        $sql = "select user_id from `{users}` where username = '{$data['username']}'";
        $result = $mysql->db_fetch_array($sql);
        if ($result=="") return "";
        $user_id = $result['user_id'];
		$total_page = ceil($total / $epage);
		$index = $epage * ($page - 1);
		$_index = $epage * ($page);
        $_i = $total - $index;
        $_j = $total - $_index;
        if ($_j<0) {$_j=0;}
        for($i=$_i;$i>$_j;$i--){
            $list[$i]['id'] = $total-$i; 
            $_time = strtotime("+".($i-1)." days",$startdate); 
            $list[$i]['time'] =  $_time; 
            $list[$i]['_time'] =  $_time + 60*60*24; 
            $list[$i]['date'] = date("Y-m-d",$_time); 
        }
        
        $_starttime = $list[$_i]['_time'];
        $_endtime = $list[($_j+1)]['time'];
       $_sql = $_recover_sql = $_repay_sql =" and p1.user_id='{$user_id}'";
         //判断添加时间开始
		
			$_sql .= " and p1.addtime < ".$_starttime;
			$_recover_sql .= " and p1.recover_yestime < ".$_starttime;
			$_repay_sql .= " and p1.repay_yestime < ".$_starttime;
		
		
	
			$_sql .= " and p1.addtime >= ".  $_endtime;
			$_recover_sql .= " and p1.recover_yestime >= ".  $_endtime;
			$_repay_sql .= " and p1.repay_yestime >= ".  $_endtime;
		
         //充值
        $sql = "select p1.money as _money,p1.fee as _fee,p1.user_id,p1.addtime from `{account_recharge}` as p1 where p1.status=1   {$_sql} ";
        $recharge_result = $mysql->db_fetch_arrays($sql);
       
         //回收本金和利息
        $sql = "select p1.recover_capital as _recover_capital,p1.recover_interest as _recover_interest,p1.user_id from `{borrow_recover}` as p1 where p1.recover_status='1'   {$_recover_sql} ";
        $borrow_recover_result = $mysql->db_fetch_arrays($sql);
        
          //已还本金和利息
        $sql = "select p1.repay_capital as _repay_capital,p1.repay_interest as _repay_interest,p1.user_id from `{borrow_repay}` as p1 where p1.repay_status='1'   {$_repay_sql} ";
        $borrow_repay_result = $mysql->db_fetch_arrays($sql);
         //借款成功
        $sql = "select p1.addtime, p1.money as _money,p1.user_id from `{account_log}` as p1 where p1.type='borrow_success'   {$_sql} group by p1.user_id";
        $borrow_result = $mysql->db_fetch_arrays($sql);
        
        //借款手续费
        $sql = "select p1.addtime,p1.money as _money,p1.user_id from `{account_log}` as p1 where p1.type='borrow_fee'   {$_sql} ";
        $borrow_fee_result = $mysql->db_fetch_arrays($sql);
        
        
        //投资金额
        $sql = "select p1.addtime,p1.money as _money,p1.user_id from `{account_log}` as p1 where p1.type='tender'   {$_sql}";
        $tender_result = $mysql->db_fetch_arrays($sql);
        
        
        //投资成功
        $sql = "select p1.addtime,p1.money as _money,p1.user_id from `{account_log}` as p1 where p1.type='tender_success'   {$_sql} ";
        $tender_success_result = $mysql->db_fetch_arrays($sql);
        
        
        //提现成功
        $sql = "select p1.addtime,p1.money as _money,p1.user_id from `{account_log}` as p1 where p1.type='cash_success'   {$_sql} ";
        $cash_success_result = $mysql->db_fetch_arrays($sql);        
   
          foreach ($list as $key => $value){
       	    $list[$key]["recharge_account"] = 0;
       	    $list[$key]["recharge_fee"] = 0;
             $_value = "";
       	    foreach($recharge_result as $_key => $_value){
       	        if ($value['time'] <= $_value['addtime'] && $value['_time'] > $_value['addtime']){
       	           
       	             $list[$key]["recharge_account"] += $_value['_money'];
       	             $list[$key]["recharge_fee"] += $_value['_fee'];
       	        }
       	    }
			 $_value = "";
            $list[$key]["borrow_success"] = 0;
       	    foreach($borrow_result as $_key => $_value){
       	        if ($value['time'] <= $_value['addtime'] && $value['_time'] > $_value['addtime']){
       	             $list[$key]["borrow_success"] += $_value['_money'];
       	        }
       	    }
            	 $_value = "";
            $list[$key]["cash_success"] = 0;
       	    foreach($cash_success_result as $_key => $_value){
       	        if ($value['time'] <= $_value['addtime'] && $value['_time'] > $_value['addtime']){
       	             $list[$key]["cash_success"] += $_value['_money'];
       	        }
       	    }
            
             $_value = "";
             $list[$key]["recover_capital"] = 0;
             $list[$key]["recover_interest"] = 0;
       	    foreach($borrow_recover_result as $_key => $_value){
       	       if ($value['time'] <= $_value['recover_yestime'] && $value['_time'] > $_value['recover_time']){
       	             $list[$key]["recover_capital"] += $_value['_recover_capital'];
       	             $list[$key]["recover_interest"] += $_value['_recover_interest'];
       	        }
       	    }
            
             $_value = "";
             $list[$key]["repay_capital"] = 0;
             $list[$key]["repay_interest"] = 0;
       	    foreach($borrow_repay_result as $_key => $_value){
       	       if ($value['time'] <= $_value['repay_time'] && $value['_time'] > $_value['repay_time']){
       	             $list[$key]["repay_capital"] += $_value['_repay_capital'];
       	             $list[$key]["repay_interest"] += $_value['_repay_interest'];
       	        }
       	    }
             $_value = "";
            $list[$key]["borrow_fee"] = 0;
       	    foreach($borrow_fee_result as $_key => $_value){
       	       if ($value['time'] <= $_value['addtime'] && $value['_time'] > $_value['addtime']){
       	             $list[$key]["borrow_fee"] += $_value['_money'];
       	        }
       	    }
             $_value = "";
             $list[$key]["tender"] = 0;
       	    foreach($tender_result as $_key => $_value){
       	        if ($value['time'] <= $_value['addtime'] && $value['_time'] > $_value['addtime']){
       	             $list[$key]["tender"] += $_value['_money'];
       	        }
       	    }
             $_value = "";
            $list[$key]["tender_success"] = 0;
       	    foreach($tender_success_result as $_key => $_value){
       	        if ($value['time'] <= $_value['addtime'] && $value['_time'] > $_value['addtime']){
       	             $list[$key]["tender_success"] += $_value['_money'];
       	        }
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
}
?>
