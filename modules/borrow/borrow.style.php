<?php
/******************************
 * $File: borrow.style.php
 * $Description: 还款方式
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

$MsgInfo["borrow_style_title_empty"] = "标题不能为空";

class borrowStyleClass{
    
	function GetStyleList($data = array()){
		global $mysql,$_G;		
        
		if ($_G['borrow_style_list']!="") return $_G['borrow_style_list'];
        
        $_sql = " where 1=1 ";
		$_select = "p1.*";	
        
        if ($data['status']!="" || $data['status']=="0"){
			$_sql .= " and p1.status= '{$data['status']}'";
		}
         
        if ($data['nid']!=""){
            $_nid = explode(",",$data['nid']);
            if (count($_nid)>0){
                foreach ($_nid as $_k => $_v){
                    $_nid[$_k] = "'{$_v}'";
                }
                $data['nid'] = join(",",$_nid);
            }
			$_sql .= " and p1.nid in ({$data['nid']})";
		}
		$_order = " order by p1.id ";
        
		$sql = "select SELECT from  `{borrow_style}` as p1  SQL ORDER LIMIT";
		
       ;
		//是否显示全部的信息
		if ( IsExiest($data['limit'])!= false){
			if ($data['limit'] != "all" ){ $_limit = "  limit ".$data['limit']; }
			$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array($_select, $_sql, $_order, $_limit), $sql));
			return $list;
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
	
		
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		return $result;
	}
    
     /**
	 * 获取单条还款方式
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function GetStyleOne($data = array()){
		global $mysql;
		$_sql = "where 1=1 ";
			 
		if (IsExiest($data['id'])!=false) {
			$_sql .= " and p1.id = {$data['id']}";
		}
		$_select = "p1.*";
		$sql = "select $_select from `{borrow_style}` as p1 $_sql";
		$result = $mysql->db_fetch_array($sql);
		
		return $result;
	}
    
    /**
	 * 修改还款方式
	 *
	 * @param array ;
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function UpdateStyle($data = array()){
		global $mysql;
		
		 //判断名称是否存在
        if (!IsExiest($data['title'])) {
            return "borrow_style_title_empty";
        } 
		
		$sql = "update `{borrow_style}` set ";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql)." where id='{$data['id']}' ");
		return $data['id'];
	}
	
}
?>