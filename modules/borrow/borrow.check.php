<?
/******************************
 * $File: borrow.check.php
 * $Description: 用户中心用户借款类
 * $Author: ahui 
 * $Time:2012-09-20
 * $Update:Ahui
 * $UpdateDate:2012-09-20  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问



class borrowChcekClass
{
	//回收借款跟投资的借款比较
    function CheckRecover($data = array()){
        global $mysql,$_G;
        
        $sql = "select * from `{borrow}` where status=3";
        $result = $mysql->db_fetch_arrays($sql);
        
        $sql = "select count(1) as num,borrow_nid from `{borrow_tender}` where borrow_nid in (select borrow_nid from `{borrow}` where status=3) group by borrow_nid";
        $tender_result = $mysql->db_fetch_arrays($sql);
        
        
        $sql = "select count(1) as num,borrow_nid from `{borrow_recover}` where borrow_nid in (select borrow_nid from `{borrow}` where status=3) and user_id>0 group by borrow_nid";
        $recover_result = $mysql->db_fetch_arrays($sql);
        foreach ($recover_result as $key => $value){
            $_recover_result[$value['borrow_nid']] = $value['num'];
        }
        
        foreach ($tender_result as $key => $value){
            if ($value['num']!=$_recover_result[$value['borrow_nid']]){
                $_result[$value['borrow_nid']] = array("tender_num"=>$value['num'],"recover_num"=>$_recover_result[$value['borrow_nid']]);
            }
         }
        return $_result;
    }
    
}
?>
