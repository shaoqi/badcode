<?php
/******************************
 * $File: remind.class.php
 * $Description: 提醒模块后台
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

require_once("remind.model.php");

class remindClass{	
	
	
	/**
	 * 获得提醒列表
	 *	@param $param $data
	 * @return Array ('list'=>"列表",page=>'当前页面','epage'=>'页数','total_page'=>'总页面')
	 */
	function GetList($data = array()){
		global $mysql;
		
		$name = $data['name'];
		$type_id = $data['type_id'];
		
		$_sql = " where 1=1 ";
		if ($name!=""){
			$_sql .= " and p1.`name` like '%$name%'";
		}
		if ($type_id!=""){
			$_sql .= " and p1.`type_id` = '$type_id'";
		}
		
		$_select = "p1.*,p2.name as type_name";
		$sql = "select SELECT from {remind} as p1 
				left join {remind_type} as p2 on p1.type_id=p2.id
				{$_sql}   ORDER LIMIT ";
		
	
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
	
	/**
	 * 获得列表
	 * @param $param $data
	 * @return Array
	 */
	function GetLists($data = array()){
		global $mysql,$_G;
		if (isset($data['user_id'])){
			$user_id = $data['user_id'];
		}else{
			return self::ERROR;
		}
		$sql = "select * from `{remind_user}` where user_id='{$user_id}'";
		$remind_result = $mysql->db_fetch_array($sql);
		if ($remind_result==false){
			$sql = "insert into `{remind_user}` set user_id='{$user_id}'";
			$mysql->db_query($sql);
			$sql = "select * from `{remind_user}` where user_id='{$user_id}'";
			$remind_result = $mysql->db_fetch_array($sql);
		}
		$remind_user = unserialize($remind_result['remind']);
		
		//获得用户的提醒列表
		$sql = "select id,name,nid from {remind_type} order by `order` desc";
		$type_list = $mysql->db_fetch_arrays($sql);
		
		//获得所有的列表
		$sql = "select SELECT from {remind} ORDER ";
		$remind_list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER'), array('*', ' order by `order` desc'), $sql));
		
		
		//判断是否已经进行手机认证
		$phone_status = $_G['user_info']['phone_status'];
		//$phone_module = $mysql->db_fetch_array($sql);
		
		$_result = "";
		foreach ($type_list as $key =>$value){
			$_result[$value['id']] = $value;
			foreach ($remind_list as $_key => $_value){
				if ($_value['type_id']==$value['id']){
					if ($phone_status!=1){
						$_value['phone'] = 2;
					}
					
					if ($remind_user!=false){
						if (isset($remind_user[$_value['nid']]['message'])){
							if ($_value['message']!=1 && $_value['message']!=2){
								$_value['message'] = 3;
							}
						}else{
							if ($_value['message']==3){
								$_value['message'] = 4;
							}
						}
						if (isset($remind_user[$_value['nid']]['email'])){
							if ($_value['email']!=1 && $_value['email']!=2){
								$_value['email'] = 3;
							}
						}else{
							if ($_value['email']==3){
								$_value['email'] = 4;
							}
						}
						if (isset($remind_user[$_value['nid']]['phone'])){
							if ($_value['phone']!=1 && $_value['phone']!=2){
								$_value['phone'] = 3;
							};
						}else{
							if ($_value['phone']==3){
								$_value['phone'] = 4;
							}
						}
					}
					
					$_result[$value['id']]['list'][$_value['id']] = $_value;
				}
			}
		}
		
		return $_result;
	}
	
	/**
	 * 查看提醒
	 *
	 * @param Array $data
	 * @return Array
	 */
	public static function GetOne($data = array()){
		global $mysql;
		$id = $data['id'];
		if($id == "") return "remind_error_id_empty";
		$sql = "select * from {remind} where id=$id";
		return $mysql->db_fetch_array($sql);
	}
	
	/**
	 * 查看提醒设置
	 *
	 * @param Array $data
	 * @return Array
	 */
	public static function GetNidOne($data = array()){
		global $mysql;
		$nid = $data['nid'];
		if($nid == "") return "remind_error_nid_empty";
		$sql = "select * from {remind} where nid='$nid'";
		return $mysql->db_fetch_array($sql);
	}
	
	
	
	/**
	 * 添加提醒
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function Add($data = array()){
		global $mysql;
        if (!isset($data['name']) || $data['name'] == "" ) {
            return "remind_error_name_empty";
        }if (!isset($data['nid']) || $data['nid'] == "" ) {
            return "remind_error_nid_empty";
        }
		$sql = "insert into `{remind}` set `addtime` = '".time()."',`addip` = '".ip_address()."'";
		
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
       return $mysql->db_query($sql);
	}
	
	
	
	/**
	 * 修改
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function Update($data = array()){
		global $mysql;
		$id = $data['id'];
        if ($data['name'] == ""  || $data['id'] == "") {
            return "remind_error_id_empty";
        }
		$sql = "update `{remind}` set ";
		$_sql = "";
		foreach($data as $key => $value){
			$_sql[] .= "`$key` = '$value'";
		}
		$sql .= join(",",$_sql)." where id = '$id'";
        $result = $mysql->db_query($sql);
		if ($result == false) return "remind_error_id_empty";
		return true;
	}
	
	
	/**
	 * 删除
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	public static function Delete($data = array()){
		global $mysql;
		$id = $data['id'];
		if (!is_array($id)){
			$id = array($id);
		}
		$sql = "delete from {remind}  where id in (".join(",",$id).")";
		$mysql->db_query($sql);
		return true;
	}
	
	public static function GetRemindBorrowOne($data = array()){
		global $mysql;
		if (!$data['user_id']) return false;
		$sql = "select * from {remind_borrow} where user_id='{$data['user_id']}'";
		return $mysql->db_fetch_array($sql);
	}
	
	function AddRemindBorrow($data){
		global $mysql;
		if (!$data['user_id']) return false;
		$result = $mysql->db_fetch_array("select * from `{remind_borrow}` where user_id = {$data['user_id']}");		
		$sql = "insert into `{remind_borrow}` set  addtime='".time()."',addip='".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}		
		return $mysql->db_query($sql);
	}
	public static function UpdateRemindBorrow($data = array()){
		global $mysql;
        if ($data['user_id'] == ""  || $data['id'] == "") {
            return "remind_error_id_empty";
        }
		$id = $data['id'];
		
		$sql = "update `{remind_borrow}` set ";
		$_sql = "";
		foreach($data as $key => $value){
			$_sql[] .= "`$key` = '$value'";
		}
		$sql .= join(",",$_sql)." where `id` = '$id'";
        $mysql->db_query($sql);
		return true;
	}
	public static function DeleteRemindBorrow($data = array()){
		global $mysql;
		$id = $data['id'];
		if  ($id == "") return "remind_error_id_empty";
		
		$sql = "delete from `{remind_borrow}`  where `id` =$id";
		$mysql->db_query($sql);	
		return true;
	}
	
	/**
	 * 修改提醒信息
	 *
	 * @param Array $data['name'],$data['nid'],$data['order'],$data['message'],$data['phone'],$data['email'],$data['type']
	 * @return Boolen
	 */
	public static function Action($data = array()){
		global $mysql;
		$name = $data['name'];
		$nid = $data['nid'];
		$order = $data['order'];
		$message = $data['message'];
		$phone = $data['phone'];
		$email = $data['email'];
		$type = $data['type'];
		unset($data['type']);
		if ($type == "add"){
			$type_id = $data['type_id'];
			foreach ($name as $key => $val){
				if ($val!="" && $nid[$key]!=""){
					$sql = "insert into {remind} set `type_id`='".trim($type_id)."',`name`='".trim($name[$key])."',`nid`='".trim($nid[$key])."',`message`='".$message[$key]."',`email`='".$email[$key]."',`phone`='".$phone[$key]."',`order`='".trim($order[$key])."' ";			
					$mysql->db_query($sql);
				}
			}
		}else{
			$id = $data['id'];
			foreach ($id as $key => $val){
				if ($name[$key]!="" && $nid[$key]!=""){
					$sql = "update {remind} set `name`='".trim($name[$key])."',`nid`='".trim($nid[$key])."',`order`='".$order[$key]."',`message`='".$message[$key]."',`email`='".$email[$key]."',`phone`='".$phone[$key]."' where id=$val";			
					$mysql->db_query($sql);
				}
			}
		}
		
		return true;
	}
	
	
	
	/**
	 * 提醒类型列表
	 *
	 * @param Array $data
	 * @return Array ('list'=>"列表",page=>'当前页面','epage'=>'页数','total_page'=>'总页面')
	 */
	public static function GetTypeList($data = array()){
		global $mysql;
		
		$name = $data['name'];		
		$_sql = " where 1=1 ";
		if ($name!=""){
			$_sql .= " and p1.`name` like '%$name%'";
		}
		$_select = " p1.*";
		$sql = "select SELECT from {remind_type} as p1 {$_sql}   ORDER LIMIT";
		
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
	
	public static function GetRemindBorrowList($data = array()){
		global $mysql;
		
		$name = $data['name'];	
		$status = $data['status'];		
		$_sql = " where 1=1 ";
		if ($status!=""){
			$_sql .= " and p1.`status` = '$status'";
		}
		$_select = " p1.*";
		$sql = "select SELECT from {remind_borrow} as p1 {$_sql}   ORDER LIMIT";
		
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
	/**
	 * 查看
	 *
	 * @param Array $data
	 * @return Array
	 */
	public static function GetTypeOne($data = array()){
		global $mysql;
		$id = $data['id'];
		if($id == "") return "remind_error_id_empty";
		$sql = "select * from `{remind_type}` where id=$id";
		$result = $mysql->db_fetch_array($sql);
		return $result;
	}
	
	/**
	 * 添加
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function AddType($data = array()){
		global $mysql;
        if ($data['name'] == ""  ) {
            return "remind_error_name_empty";
        }
		 if ($data['nid'] == ""  ) {
            return "remind_error_nid_empty";
        }
		$sql = "select * from {remind_type} where `nid` = '".$data['nid']."'";
		$result = $mysql->db_fetch_array($sql);
		if ($result !=false) return "remind_error_id_empty";
		
		$_sql = "";
		$sql = "insert into `{remind_type}` set ";
		foreach($data as $key => $value){
			$_sql[] .= "`$key` = '$value'";
		}
		$sql .= join(",",$_sql)." ";
        return $mysql->db_query($sql);
	}
	
	
	/**
	 * 更新提醒类型
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	public static function UpdateType($data = array()){
		global $mysql;
        if ($data['name'] == ""  || $data['id'] == "") {
            return "remind_error_id_empty";
        }
		$id = $data['id'];
		
		$sql = "update `{remind_type}` set ";
		$_sql = "";
		foreach($data as $key => $value){
			$_sql[] .= "`$key` = '$value'";
		}
		$sql .= join(",",$_sql)." where `id` = '$id'";
        $mysql->db_query($sql);
		return true;
	}
	
	/**
	 * 删除提醒类型
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	public static function DeleteType($data = array()){
		global $mysql;
		$id = $data['id'];
		if  ($id == "") return "remind_error_id_empty";
		if (!is_array($id)){
			$id = array($id);
		}
		$sql = "delete from {`remind_type`}  where `id` in (".join(",",$id).")";
		$mysql->db_query($sql);
		$sql = "delete from {`remind`}  where `type_id` in (".join(",",$id).")";
		$mysql->db_query($sql);
		return true;
	}
	
	
	
	/**
	 * 修改提醒类型
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	public static function ActionType($data = array()){
		global $mysql;
		$nid = $data['nid'];
		$name = $data['name'];
		$order = $data['order'];
		$type = $data['type'];
		unset($data['type']);
		if ($type == "add"){
			foreach ($name as $key => $val){
				if ($val!="" && $nid[$key]!=""){
					$sql = "insert into {remind_type} set `name`='".$name[$key]."',`nid`='".$nid[$key]."',`order`='".$order[$key]."' ";			
					$mysql->db_query($sql);
				}
			}
		}else{
			$id = $data['id'];
			foreach ($id as $key => $val){
				$sql = "update {remind_type} set `name`='".$name[$key]."',`order`='".$order[$key]."' where id=$val";			
				$mysql->db_query($sql);
			}
		}
		return true;
	}
	
	/**
	 * 修改用户提醒
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function ActionRemindUser($data){
		global $mysql;
		
		if  (!isset($data['user_id'])) return "remind_error_nid_empty";
		$user_id = $data['user_id'];
		$remind = $data['remind'];
		$sql = "update {remind_user} set remind='{$remind}' where user_id=$user_id";		
		
		return $mysql->db_query($sql);
		
	}
	
	
	//nid,所要操作的标识名
	//title 标题
	//content 内容
	//phone，手机号码
	//email，邮箱
	//sent_user,发送用户id
	//receive_user,接收用户id
	//type,类型
	public static function sendRemind($data){
		global $mysql,$user,$_G;
		
		//是否禁止提醒模块
		//if ($_G['system']['remind_status']==0) return "";
		$remind_user = array();
		if (!empty($data['receive_user'])){
			$data['receive_userid'] = $data['receive_user'];
		}
        //判断是否存在
        $sql = "select 1 from `{remind_log}` where remind_nid='{$data['remind_nid']}'";
        $_log_result = $mysql->db_fetch_array($sql);
        if ($_log_result==false){
            $_log["user_id"] = $data["receive_userid"];
            $_log["nid"] = $data["nid"];
            $_log["remind_nid"] = $data["remind_nid"];
            $_log["code"] = $data["code"];
            $_log["article_id"] = $data["article_id"];
            $_log["title"] = $data["title"];
            $_log["content"] = $data["content"];
            self::AddLog($_log);
        }else{
            return "";
        }
        
		$sql = "select remind from `{remind_user}` where user_id={$data['receive_userid']}";
		$result = $mysql->db_fetch_array($sql);
		
		if ($result !=false){
			$remind_user = unserialize ($result['remind']);
		}
		
		
		$remind_result = self::GetNidOne(array("nid"=>$data['nid']));
		
		/* print_r($remind_result);
		print_r($remind_user);
		exit;  */
		
		if($remind_result['message']==3){
			$message_status = isset($remind_user[$data['nid']]['message'])?$remind_user[$data['nid']]['message']:4;	
		}else{
			$message_status = $remind_result['message'];
		}
		if($remind_result['email']==3){
			$email_status = isset($remind_user[$data['nid']]['email'])?$remind_user[$data['nid']]['email']:4;	
		}else{
			$email_status = $remind_result['email'];
		}
		
		if ($data['phone_status']==""){
			$phone_status = isset($remind_user[$data['nid']]['phone'])?$remind_user[$data['nid']]['phone']:$remind_result['phone'];	
		}else{
			$phone_status = $data['phone_status'];
		}	
      
		$email = isset($data['email'])?$data['email']:$result['email'];
		$phone = isset($data['phone'])?$data['phone']:$result['phone'];
		$_result = array();
		
		if ($message_status==1 || $message_status==3){
			require_once("modules/message/message.class.php");
			$message['send_userid'] = "0";
			$message['user_id'] = $data['receive_userid'];
			$message['name'] = $data['title'];
			$message['contents'] = $data['content'];
			$message['type'] = 'user';
			$message['status'] = $data['status'];
			$_result['message_result'] = messageClass::SendMessages($message);
		}
		
		if ($email_status==1 || $email_status==3){
			$remail['user_id'] = $data['receive_userid'];
			$remail['title'] = $data['title'];
			if ($data['email_content']==""){
				$remail['msg'] =  $_G["system"]["con_webname"]."提示：".$data['content'];
			}else{
				$remail['msg'] =$data['email_content'];
			}
            require_once(ROOT_PATH."modules/users/users.function.php");
            if (function_exists("GetEmailMsg")){
                $remail['msg'] = GetEmailMsg(array("user_id"=>$remail['user_id'],"contents"=>$remail['msg']));
            }
			$_result['email_result'] = usersClass::SendEmail($remail);
		}
		if ($phone_status==1 || $phone_status==3){
			require_once("modules/approve/approve.class.php");
			$send_sms['status'] = 1;
			$send_sms['type'] = $data['type'];
			if ($data['phone_content']==""){
				$send_sms['contents'] =  $data['content']."[{$_G['system']['con_webname']}]";
			}else{
				$send_sms['contents'] =$data['phone_content'];
			}
				$send_sms['phone'] = $data['phone'];
				$send_sms['user_id'] = $data['receive_userid'];
			
			$_result['phone_result'] = approveClass::SendSMS($send_sms);
		}
		return $_result;
	}
	//添加记录
	//$data['user_id']用户id
	//$data['type']发送类型 如 message短消息 sms 短信 email邮箱
	//$data['style'] 类型，
	//$data['content']内容
	//$data['contract']联系方式
	//$data['status']联系方式
	public static function AddLog($data){
		global $mysql;
		$sql = "insert into `{remind_log}` set `addtime` = '".time()."',`addip` = '".ip_address()."',";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
		$sql .= join(",",$_sql)." ";
        return $mysql->db_query($sql);
	}
}