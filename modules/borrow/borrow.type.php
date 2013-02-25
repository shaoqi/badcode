<?php
/******************************
 * $File: borrow.php
 * $Description: 借款类型文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * 组件：borrow.style.php  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

$MsgInfo["borrow_type_title_empty"] = "标题不能为空";
$MsgInfo["borrow_type_apr_error"] = "最大利率不能小于最小利率";
$MsgInfo["borrow_type_period_error"] = "借款期限的最高不能小于最低";
$MsgInfo["borrow_type_validate_error"] = "有效期的最高不能小于最低";
$MsgInfo["borrow_type_check_error"] = "审核期限的最高不能小于最低";
$MsgInfo["borrow_type_award_scale_error"] = "奖励比例的最高不能小于最低";
$MsgInfo["borrow_type_award_account_error"] = "奖励金额的最高不能小于最低";

class borrowTypeClass{
    
	function GetTypeList($data = array()){
		global $mysql,$_G;		
		
        //获取还款方式
        require_once("borrow.style.php");
        $_style_result = borrowStyleClass::GetStyleList(array("limit"=>"all"));
        $_sql = " where 1=1 ";
		$_select = "p1.*";
        if ($data['status']!="" || $data['status']=="0"){
			$_sql .= " and p1.status = '{$data['status']}'";
		}
		$_order = " order by p1.id ";
		$sql = "select SELECT from  `{borrow_type}` as p1  SQL ORDER LIMIT";
		
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
    			             $_styles[] = "<span title='{$_value['title']}'>".$_value['title']."</span>"; 
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
	function GetTypeOne($data = array()){
		global $mysql;
		$_sql = "where 1=1 ";
			 
		if (IsExiest($data['id'])!=false) {
			$_sql .= " and p1.id = '{$data['id']}'";
		}
		
		if (IsExiest($data['nid'])!=false) {
			$_sql .= " and p1.nid = '{$data['nid']}'";
		}
        
		require_once("borrow.style.php");//还款方式
		$_select = "p1.*";
		$sql = "select $_select from `{borrow_type}` as p1 $_sql";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false){
            $result['style_result'] = borrowStyleClass::GetStyleList(array("limit"=>"all","nid"=>$result['styles']));
            
            for($i=$result['period_first'];$i<=$result['period_end'];$i++){
                if ($result['nid']=="day"){
                    $result['period_result'][] = array("name"=>$i."天","value"=>$i);
                }else{
                    $result['period_result'][] = array("name"=>$i."月","value"=>$i);
                }
            }
            
            for($i=$result['validate_first'];$i<=$result['validate_end'];$i++){
                $result['validate_result'][] = array("name"=>$i."天","value"=>$i);
            }
            
            $result['tender_account_min_result'] = explode(",",$result['tender_account_min']);
            $result['tender_account_max_result'] = explode(",",$result['tender_account_max']);
		}
		return $result;
	}
    
    
    /**
	 * 修改借款类型
	 *
	 * @param array $data;
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function UpdateType($data = array()){
		global $mysql;
		
		 //判断名称是否存在
        if (!IsExiest($data['title'])) {
            return "borrow_type_title_empty";
        } 
		//判断借款期限
        if ($data['period_first']>$data['period_end']){
            return "borrow_type_period_error";
        }
        //判断利率
         if ($data['apr_first']>$data['apr_end']){
            return "borrow_type_apr_error";
        }
        //判断有效期
         if ($data['validate_first']>$data['validate_end']){
            return "borrow_type_validate_error";
        }//判断审核时间
         if ($data['check_first']>$data['check_end']){
            return "borrow_type_check_error";
        }
        //判断奖励比例
         if ($data['award_scale_first']>$data['award_scale_end']){
            return "borrow_type_award_scale_error";
        }
        //判断奖励金额
         if ($data['award_account_first']>$data['award_account_end']){
            return "borrow_type_award_account_error";
        }
        
		$sql = "update `{borrow_type}` set ";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql)." where id='{$data['id']}' ");
		return $data['id'];
	}
	
}
?>