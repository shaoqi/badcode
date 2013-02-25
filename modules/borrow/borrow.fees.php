<?
/******************************
 * $File: borrow.fee.php
 * $Description: p2p的费用
 * $Author: Deayou 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

class borrowFeeClass {
     
	 function GetType($data){
	 	global $mysql;
        if ($data['account']=="") return "";//借款金额，本金
        if ($data['period']=="") return "";//借款期限，如果是天标则为天数，其他的则为月份
        if ($data['style']=="") return "";//借款期限
        if ($data['type']=="") return "";//借款期限
		$type = $data['type'];
		if($type=="0"){
			$result = self::CreditBorrowSuccess($data);
		}else if($type=="1"){
			$result = self::DiyaBorrowSuccess($data);
		}else if($type=="2"){
			$result = self::JingBorrowSuccess($data);
		}else if($type=="3"){
			$result = self::miaoBorrowSuccess($data);
		}
		return $result;
	 }
	 
     /* [信用标]
     借款成功费用 
     借款1个月管理费为本金1%，借款2个月内管理费为本金2%，
     每增加一个月管理费用增加0.2%，例如借款3个月管理费用为借款本金的2.2%，以此类推。
     管理费在用户借款成功后在借到款项内扣除；
     
     信用天标
     借款1天管理费为本金0.4%，借款1天以上每增加一天管理费增加0.04%，
     例如借款2天管理费为0.44%，以此类推。管理费在用户借款成功后在借到款项内扣除。
     */
     function CreditBorrowSuccess($data){
        global $mysql;
        if ($data['account']=="") return "";//借款金额，本金
        if ($data['period']=="") return "";//借款期限，如果是天标则为天数，其他的则为月份
        if ($data['style']=="") return "";//借款期限
        //0：表示等额本息还款
        if ($data['style']=="0"){
            if ($data['period']>2){
                $_account = $data['account']*(0.02 + ($data['period']-2)*0.002);
            }else{
                $_account = $data['account']*$data['period']*0.01;
            }
         }
         //4：表示信用天标
         else if ($data['style']=="4"){
              $_account = $data['account']*(0.004 + ($data['period']-1)*0.0004);
            
         }
		 
		 //其他
		else{
		  if ($data['period']>2){
                $_account = $data['account']*(0.02 + ($data['period']-2)*0.002);
            }else{
                $_account = $data['account']*$data['period']*0.01;
            }
		}
        return round($_account,2);
     }
     
     /* [抵押标] 
    等额本息按月还款
    借款1个月管理费为本金1%，借款2个月内管理费为本金2%，每增加一个月管理费用增加0.2%，
    例如借款3个月管理费用为借款本金的2.2%，
    以此类推。管理费在用户借款成功后在借到款项内扣除；
    
    月付息到期一次还本
    借款1个月管理费为本金1%，借款2个月内管理费为本金2.3%，每增加一个月管理费用增加0.3%，
    例如借款3个月管理费用为借款本金的2.6%，
    以此类推。管理费在用户借款成功后在借到款项内扣除；
     */
     function DiyaBorrowSuccess($data){
        global $mysql;
        if ($data['account']=="") return "";//借款金额
        if ($data['period']=="") return "";//借款期限
        if ($data['style']=="") return "";//借款期限
		
        //0：表示等额本息还款
        if ($data['style']=="0"){
            if ($data['period']>2){
                $_account = $data['account']*(0.02 + ($data['period']-2)*0.002);
            }else{
                $_account = $data['account']*$data['period']*0.01;
            }
        }
        //3月付息到期一次还本
        else if($data['style']==3){
            if ($data['period']==1){
                $_account = $data['account']*$data['period']*0.01;
            }if ($data['period']==2){
                $_account = $data['account']*$data['period']*0.23;
            }else{
                $_account = $data['account']*(0.02 + ($data['period']-1)*0.003);
            }   
        }
        //4：表示天标
        else if ($data['style']=="4"){
           $_account = $data['account']*(0.004 + ($data['period']-1)*0.0004);
        }
		
		//其他
		else{
			$_account = 0;
		}
		
        return round($_account,2);
     }
     
     
      /* [秒标] 
     为本金0.1%，
     */
     function MiaoBorrowSuccess($data){
        global $mysql;
        if ($data['account']=="") return "";//借款金额
        return round($data['account']*0.001,2);
     }
     
      
    /* [净值标] 
    等额本息按月还款。
    借款1个月管理费为本金0.5%，借款2个月内管理费为本金1%，
    每增加一个月管理费用增加0.5%，
    例如借款3个月管理费用为借款本金的1.5%，
    以此类推。管理费在用户借款成功后在借到款项内扣除；
    
    月付息到期一次还本
    借款1个月管理费为本金0.5%，借款2个月内管理费为本金1%，每增加一个月管理费用增加0.5%，
    例如借款3个月管理费用为借款本金的1.5%，以此类推。管理费在用户借款成功后在借到款项内扣除：
    
    净值天标：
    借款1天管理费为本金0.2%，借款1天以上每增加一天管理费增加0.02%，
    例如借款2天管理费为0.22%，以此类推。
    管理费在用户借款成功后在借到款项内扣除。
    
     */
     function JingBorrowSuccess($data){
        global $mysql;
        if ($data['account']=="") return "";//借款金额，本金
        if ($data['period']=="") return "";//借款期限，如果是天标则为天数，其他的则为月份
        if ($data['style']=="") return "";//借款期限
         //0：表示等额本息还款
        if ($data['style']=="0"){
            
                $_account = $data['account']*$data['period']*0.005;
            
        }
        //3月付息到期一次还本
        else if($data['style']==3){
            
                $_account = $data['account']*$data['period']*0.005;
           
            
        }
        //4：表示天标
        else if ($data['style']=="4"){
           $_account = $data['account']*0.001;
        }
		
		//其他
		else{
			$_account = 0;
		}
        return round($_account,2);
     }
     
}
?>