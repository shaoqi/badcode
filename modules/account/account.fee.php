<?php
/******************************
 * $File: account.php
 * $Description: 借款类型文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * 组件：account.style.php  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

$MsgInfo["account_fee_id_empty"] = "您的操作有误";
$MsgInfo["account_fee_name_empty"] = "名称不能为空";
$MsgInfo["account_fee_nid_empty"] = "标识名名称不能为空";
$MsgInfo["account_fee_nid_exiest"] = "标识名已经存在";

class accountFeeClass{
    
	function GetFeeList($data = array()){
		global $mysql,$_G;		
		
        //获取还款方式
        
		$_select = "p1.*,p2.name as type_name";
		$_order = " order by p1.id ";
		$sql = "select SELECT from  `{account_fee}` as p1 left join `{account_fee_type}` as p2 on p1.type=p2.nid  SQL ORDER LIMIT";
		
		//是否显示全部的信息
		if ( IsExiest($data['limit'])!= false){
			if ($data['limit'] != "all" ){ $_limit = "  limit ".$data['limit']; }
			$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array($_select, $_sql, $_order, $_limit), $sql));
			
            foreach ($list as $key => $value){
                 $_styles = array();
			     if ($value["styles"]!=""){
    			     foreach ($_style_result as $_key => $_value){
    			         $style = explode(",",$value["styles"]);
                         if (in_array($_value["nid"],$style)){
    			             $_styles[] = "<span title='{$_value['title']}'>".$_value['name']."</span>"; 
                         }
                     }
    			     $list[$key]['styles_name'] = join("|",$_styles); 
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
		$_limit = " limit ".($data["epage"] * ($data["page"] - 1)).", {$data['epage']}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL','ORDER', 'LIMIT'), array($_select,$_sql,$_order, $_limit), $sql));
	
		
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		return $result;
	}
    
     /**
	 * 获取单条借款类型信息
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function GetFeeOne($data = array()){
		global $mysql;
        
         //判断id是否存在
        if (!IsExiest($data['id'])) {
            return "account_fee_id_empty";
        } 
        
		$_sql = "where  p1.id = {$data['id']} ";
			 
		
		$_select = "p1.*";
		$sql = "select $_select from `{account_fee}` as p1 $_sql";
		$result = $mysql->db_fetch_array($sql);
		
		return $result;
	}
    
    
    /**
	 * 添加借款费用
	 *
	 * @param array $data;
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function AddFee($data = array()){
		global $mysql;
        
		 //判断名称是否存在
        if (!IsExiest($data['name'])) {
            return "account_fee_name_empty";
        } 
        
        //判断名称是否存在
        if (!IsExiest($data['nid'])) {
            return "account_fee_nid_empty";
        } 
        $sql = "select 1 from `{account_fee}` where nid='{$data['nid']}'";
        $result = $mysql->db_fetch_array($sql);
        if ($result!=false){
             return "account_fee_nid_exiest"; 
        }
		
		$sql = "insert into `{account_fee}` set ";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql));
        $id = $mysql->db_insert_id();
		return $id;
	}
	
    /**
	 * 修改借款费用
	 *
	 * @param array $data;
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function UpdateFee($data = array()){
		global $mysql;
		
         //判断id是否存在
        if (!IsExiest($data['id'])) {
            return "account_fee_id_empty";
        } 
         //判断名称是否存在
        if (!IsExiest($data['name'])) {
            return "account_fee_name_empty";
        } 
        
        //判断名称是否存在
        if (!IsExiest($data['nid'])) {
            return "account_fee_nid_empty";
        } 
        
        $sql = "select 1 from `{account_fee}` where nid='{$data['nid']}' and id!='{$data['id']}'";
        $result = $mysql->db_fetch_array($sql);
        if ($result!=false){
             return "account_fee_nid_exiest"; 
        }
		
		$sql = "update `{account_fee}` set ";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql)." where id='{$data['id']}' ");
		return $data['id'];
	}
    
      /**
	 * 删除单条借款类型信息
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function DeleteFee($data = array()){
		global $mysql;
        
         //判断id是否存在
        if (!IsExiest($data['id'])) {
            return "account_fee_id_empty";
        } 
		$sql = "delete  from `{account_fee}` where id = {$data['id']} ";
		$result = $mysql->db_query($sql);
		
		return $result;
	}
    
     /**
	 * 获取利息的值
	 *
	 * @param Array $data = array("account","type","account_all","fee_all")
	 * @return Boolen
	 */
    function GetFeeValue($data = array()){
        global $mysql;
        $sql = "select * from `{account_fee}` where  type='{$data['type']}' and status=1 and fee_type>0 ";
         $result = $mysql->db_fetch_arrays($sql);
        
         if ($result !=false){
            $_result = array();
             foreach($result  as $key =>$value){
                $account =0;
                //是否vip
                if ($data['vip_status']==1){
                    //按比例奖励
                    if ($value["fee_type"]==1){
                        $account = $data['account']*$value["vip_account_scale"]*0.01;
                    }
                    //按比例公式
                    elseif ($value["fee_type"]==2){
                        $account = $value["vip_account_all_fee"];
                        $_account = 0;
                        if ($data['account']>$value["vip_account_all"]){
                            $_num = ($data['account']-$value['vip_account_all'])/$value["vip_account_add"];
                            $__num =($data['account']-$value['vip_account_all'])%$value["vip_account_add"];
                            if ($__num>0){
                                $_num = floor($_num)+1;
                            }
                            $_account = $_num*$value["vip_account_add_fee"];
                        }
                        if ($_account>0){
                            $account +=$_account;
                        }
                        if($value['vip_account_max']>0 && $value['vip_account_max']<=$account){
                           $account = $value['vip_account_max'];
                        }
                        if ($account>=$data["account"]){
                            $account = 0;
                        }
                    }
                }else{
                     //按比例奖励
                    if ($value["fee_type"]==1){
                        $account = $data['account']*$value["all_account_scale"]*0.01;
                    }
                    //按比例公式
                    elseif ($value["fee_type"]==2){
                        $account = $value["all_account_all_fee"];
                        $_account = 0;
                        if ($data['account']>$value["all_account_all"]){
                            $_num =($data['account']-$value['all_account_all'])/$value["all_account_add"];
                            $__num =($data['account']-$value['all_account_all'])%$value["all_account_add"];
                            if ($__num>0){
                                $_num = floor($_num)+1;
                            }
                            $_account = $_num*$value["all_account_add_fee"];
                            
                        }
                        if ($_account>0){
                            $account +=$_account;
                        }
                        if($value['all_account_max']>0 && $value['all_account_max']<=$account){
                           $account = $value['all_account_max'];
                        }
                        if ($account>=$data["account"]){
                            $account = 0;
                        }
                    }
                }
                $value['account_fee'] = round($account,2);
                 if ($account>0){
                    $_result[$key] = $value;
                 }
             }
         } 
         return $_result;
    }
    
    
    
	function GetFeeTypeList($data = array()){
		global $mysql,$_G;		
		
        //获取还款方式
        
		$_select = "p1.*";
		$_order = " order by p1.id ";
		$sql = "select SELECT from  `{account_fee_type}` as p1  SQL ORDER LIMIT";
		
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
	 * 获取单条借款类型信息
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function GetFeeTypeOne($data = array()){
		global $mysql;
        
         //判断id是否存在
        if (!IsExiest($data['id'])) {
            return "account_fee_type_id_empty";
        } 
        
		$_sql = "where  p1.id = {$data['id']} ";
			 
		
		$_select = "p1.*";
		$sql = "select $_select from `{account_fee_type}` as p1 $_sql";
		$result = $mysql->db_fetch_array($sql);
		
		return $result;
	}
    
    
    /**
	 * 添加借款费用类型
	 *
	 * @param array $data;
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function AddFeeType($data = array()){
		global $mysql;
        
		 //判断名称是否存在
        if (!IsExiest($data['name'])) {
            return "account_fee_type_name_empty";
        } 
        
        //判断名称是否存在
        if (!IsExiest($data['nid'])) {
            return "account_fee_type_nid_empty";
        } 
        $sql = "select 1 from `{account_fee_type}` where nid='{$data['nid']}'";
        $result = $mysql->db_fetch_array($sql);
        if ($result!=false){
             return "account_fee_type_nid_exiest"; 
        }
		
		$sql = "insert into `{account_fee_type}` set ";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql));
        $id = $mysql->db_insert_id();
		return $id;
	}
	
    /**
	 * 修改借款费用
	 *
	 * @param array $data;
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function UpdateFeeType($data = array()){
		global $mysql;
		
         //判断id是否存在
        if (!IsExiest($data['id'])) {
            return "account_fee_type_id_empty";
        } 
         //判断名称是否存在
        if (!IsExiest($data['name'])) {
            return "account_fee_type_name_empty";
        } 
        
        //判断名称是否存在
        if (!IsExiest($data['nid'])) {
            return "account_fee_type_nid_empty";
        } 
        
        $sql = "select 1 from `{account_fee_type}` where nid='{$data['nid']}' and id!='{$data['id']}'";
        $result = $mysql->db_fetch_array($sql);
        if ($result!=false){
             return "account_fee_type_nid_exiest"; 
        }
		
		$sql = "update `{account_fee_type}` set ";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql)." where id='{$data['id']}' ");
		return $data['id'];
	}
    
      /**
	 * 删除单条借款类型信息
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function DeleteFeeType($data = array()){
		global $mysql;
        
         //判断id是否存在
        if (!IsExiest($data['id'])) {
            return "account_fee_type_id_empty";
        } 
		$sql = "delete  from `{account_fee_type}` where id = {$data['id']} ";
		$result = $mysql->db_query($sql);
		
		return $result;
	}
    
}
?>