<?php
/******************************
 * $File: approve.class.php
 * $Description: 认证说明
 * $Author: ahui 
 * $Time:2010-08-09
 * $Update:None 
 * $UpdateDate:None 
 * Copyright(c) 2012 by dycms.net. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

//将语言给插进来
require_once("approve.model.php");

require_once("approve.id5.php");

class approveClass{
	
	
	function ActionSms($data){
		global $mysql;
		
		if (!IsExiest($data['user_id'])) return "approve_sms_user_id_empty";
		if (!IsExiest($data['phone'])) return "approve_sms_phone_empty";
		
		//判断手机号码是否存在,状态0表示申请中，1表示通过，2表示审核不通过，3表示过期
		$sql = "select id,status,id from `{approve_sms}` where user_id='{$data['user_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false){
			
			$_data['status'] = 1;
			$_data['user_id'] = $data['user_id'];
			$_data['phone'] = $data['phone'];
			$_data['type'] = "smscode";
			$_data['code'] = rand(100000,999999);
			$str="您的手机验证码为:".$_data['code']."【ssp2p.com】";
			$_data['contents'] = iconv("GBK", "UTF-8",$str);
			$_result = approveClass::SendSMS($_data);
			
			$sql = "insert into `{approve_sms}` set `addtime` = '".time()."',`addip` = '".ip_address()."',user_id='{$data['user_id']}',status=0,`phone`='{$data['phone']}'";
			$mysql->db_query($sql);
			$id = $mysql -> db_insert_id();
			return $id;
		}else{
			//判断手机号码是否存在,状态0表示申请中，1表示通过，2表示审核不通过，3表示过期
			$sql = "select 1 from `{approve_sms}` where `phone`='{$data['phone']}' and status=1 and user_id!='{$data['user_id']}'";
			$_result = $mysql->db_fetch_array($sql);
			if ($_result!=false) return "approve_sms_phone_exiest";
			
			$sql = "update `{approve_sms}` set `phone`='{$data['phone']}' where user_id='{$data['user_id']}'";
			$mysql->db_query($sql);
			$_data['status'] = 1;
			$_data['phone'] = $data['phone'];
			$_data['user_id'] = $data['user_id'];
			$_data['type'] = "smscode";
			$_data['code'] = rand(100000,999999);
			$str="您的手机验证码为:".$_data['code']."【ssp2p.com】";
			$_data['contents'] = iconv("GBK", "UTF-8",$str);
			$_result = approveClass::SendSMS($_data);
			return $result['id'];
		}
	
	}
	/**
	 * 列表
	 *
	 * @return Array
	 */
	function GetSmsList($data = array()){
		global $mysql;
		
		$_sql = " where 1=1 ";
		//判断类型名称是否存在
        if (IsExiest($data['user_id'])!=false) {
            $_sql .= " and p1.user_id ='{$data['user_id']}'";
        }
		
		if (IsExiest($data['username'])!=false) {
            $_sql .= " and p2.username like '%{$data['username']}%'";
        }
		
		
		if (IsExiest($data['phone'])!=false) {
            $_sql .= " and p1.phone like '%{$data['phone']}%'";
        }
		
		
		if (IsExiest($data['status'])!=false || $data['status']=="0") {
            $_sql .= " and p1.status = '{$data['status']}'";
        }
		$_select = " p1.*,p2.username ";
		$_order = " order by p1.id desc";
		$sql = "select SELECT from `{approve_sms}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id SQL ORDER LIMIT ";
		
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
	 * 检查短信的验证码，读取最早的一个
	 *
	 * @param Array $data = array("user_id"=>"用户id","type"=>"类型");
	 * @return Array
	 */
	function CheckSmsCode($data){
		global $mysql;
		$sql = "select * from `{approve_smslog}` where user_id={$data['user_id']} and type='{$data['type']}' and phone='{$data['phone']}' order by id desc";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "approve_sms_not_exiest";
		if ($data['code']=="") return "approve_sms_code_error";
		if ($result['code_status']==1) return "approve_sms_check_yes";
		if ($result['phone']!=$data['phone']) return "approve_sms_phone_error";
		if ($result['code']!=$data['code']) return "approve_sms_code_error";
		
		$sql = "update `{approve_smslog}` set code_status=1,code_time='".time()."' where id={$result['id']}";
		$mysql->db_query($sql);
		
		$sql = "select id from `{approve_sms}` where user_id='{$data['user_id']}' order by addtime desc";
		$result = $mysql->db_fetch_array($sql);
		$_data['id'] = $result['id'];
		$_data['verify_remark'] = "用户手机认证通过";
		$_data['status'] = 1;
		$_data['verify_userid'] = 0;
		self::CheckSms($_data);
		return $data['user_id'];
	
	}
	
	/**
	 * 查看
	 *
	 * @param Array $data = array("id"=>"");
	 * @return Array
	 */
	function GetSmsOne($data = array()){
		global $mysql;
		$_sql=" where 1=1 ";
		if (IsExiest($data['id']!= false)){ 
			$_sql.=" and p1.id='{$data['id']}' ";
		}
		if (IsExiest($data['user_id']!= false)){
			$_sql.=" and p1.user_id='{$data['user_id']}' ";
		}
		if (IsExiest($data['status']!= false)){
			$_sql.=" and p1.status='{$data['status']}' ";
		}
		$sql = "select p1.*,p2.username from `{approve_sms}` as p1  left join `{users}` as p2 on p1.user_id=p2.user_id $_sql order by addtime desc";
		$result = $mysql->db_fetch_array($sql);
		
		if($result['phone']!=''){
			$result['phone'] = substr($result['phone'],0,4)."****".substr($result['phone'],-3,3);
		}
		if ($result==false) return "approve_sms_not_exiest";
		//$result['contents']=iconv("UTF-8","GBK",$result['contents']);
		return $result;
	}
	
	/**
	 * 添加
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function AddSms($data = array()){
		global $mysql;
		//手机号码不能为空
		if (!IsExiest($data['phone'])) return "approve_sms_phone_empty";
		//判断用户名是否存在
		if (IsExiest($data['username']) != false){
			$sql = "select user_id from `{users}` where username='{$data['username']}'";
			$result =  $mysql->db_fetch_array($sql);
			if ($result==false) return "approve_sms_username_not_exiest";
			$data['user_id'] = $result['user_id'];
		}
		//判断用户id是否存在
		if (!IsExiest($data['user_id'])){
			return "approve_sms_userid_not_exiest";
		}
		
		//判断手机号码是否存在,状态0表示申请中，1表示通过，2表示审核不通过，3表示过期
		$sql = "select 1 from `{approve_sms}` where `phone`='{$data['phone']}' and status=1";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "approve_sms_phone_exiest";
		$status=isset($data['status'])?$data['status']:0;
		$sql = "insert into `{approve_sms}` set `addtime` = '".time()."',`addip` = '".ip_address()."',user_id='{$data['user_id']}',status={$status},`phone`='{$data['phone']}'";
		$mysql->db_query($sql);
		$id = $mysql -> db_insert_id();
		return $id;
	}
	
	/**
	 * 修改
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function UpdateSms($data = array()){
		global $mysql;
		//id不能为空
		if (!IsExiest($data['id'])) return "approve_sms_id_empty";
		
		//手机号码不能为空
		if (!IsExiest($data['phone'])) return "approve_sms_phone_empty";
		
		//判断用户名是否存在
		if (IsExiest($data['username']) != false){
			$sql = "select user_id from `{users}` where username='{$data['username']}'";
			$result =  $mysql->db_fetch_array($sql);
			if ($result==false) return "approve_sms_username_not_exiest";
			$data['user_id'] = $result['user_id'];
		}
		//判断用户id是否存在
		if (!IsExiest($data['user_id'])){
			return "approve_sms_userid_not_exiest";
		}
		//判断是否有待审核的短信验证码
		$sql = "select * from `{approve_sms}` where `id`='{$data['id']}' ";
		$result = $mysql->db_fetch_array($sql);
		if ($data['user_id']!=$result['user_id'] && $data['phone']==$result['phone'] ){
			return "approve_sms_update_success";
		}
		
		//判断手机号码是否存在,状态0表示申请中，1表示通过，2表示审核不通过，3表示过期
		$sql = "select 1 from `{approve_sms}` where `phone`='{$data['phone']}' and status=1";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) return "approve_sms_phone_exiest";
		
		$sql = "update `{approve_sms}` set status=3 where id='{$data['id']}'";
		$mysql->db_query($sql);
		
		
		return $id;
	}
	
	/**
	 * 5,审核学历
	 *
	 * @param Array $data = array("id"=>"");
	 * @return id
	 */
	 function CheckSms($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "approve_sms_id_empty";
		
		$sql = "select p1.* from `{approve_sms}` as p1  where id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "approve_sms_not_exiest";
		$user_id = $result['user_id'];
		$phone = $result['phone'];
		//如果不通过的话，则积分变为0
		if ($data['status']==2) $data['credit'] = 0;
		
		//如果通过的话则将短信认证状态都变为3
		if ($data['status']==1){
			$sql = "update `{approve_sms}` set status=3,credit=0 where user_id='{$result['user_id']}' and status=1";
			$result = $mysql->db_query($sql);
		}
		
		$sql = "update `{approve_sms}` set verify_userid='{$data['verify_userid']}',verify_remark='{$data['verify_remark']}', verify_time='".time()."',status='{$data['status']}',credit='{$data['credit']}' where id='{$data['id']}'";
		$result = $mysql->db_query($sql);
		
		if ($result!=false){
			$user_info['user_id'] = $user_id;
			if ($data['status']!=1){
				$phone = "";
			}
			$user_info['phone'] = $phone;
			$user_info['phone_status'] = $data['status'];
			$result = usersClass::UpdateUsersInfo($user_info);
		}
		//加入审核记录
		$_data["user_id"] = $user_id;
		$_data["result"] = $data["status"];
		$_data["code"] = "approve";
		$_data["type"] = "sms";
		$_data["article_id"] = $data["id"];
		$_data["verify_userid"] = $data["verify_userid"];
		$_data["remark"] = $data["verify_remark"];
		usersClass::AddExamine($_data);
		
		//添加积分记录
		$credit_log['user_id'] = $user_id;
		$credit_log['nid'] = "phone";
		$credit_log['code'] = "approve";
		$credit_log['type'] = "phone";
		$credit_log['addtime'] = time();
		$credit_log['article_id'] =$user_id;
		$credit_log['remark'] = "手机认证通过所得积分";
		//creditClass::ActionCreditLog($credit_log);
		
		return $data['id'];
	}
	
	/**
	 * 发送短信
	 *
	 * @param Array $data = array("type"=>"类型","type"=>"类型","user_id"=>"用户","phone"=>"电话","content"=>"内容","time"=>"发送时间");
	 * @return Array
	 */
	public static  function SendSMS($data)
	{
		global $mysql,$_G;
        $url ='';
        if(!preg_match('/^1380000/',$data['phone'])){
            $url = str_replace("#phone#",iconv("GBK", "UTF-8",$data['phone']),iconv("GBK", "UTF-8",trim($_G['system']['con_sms_url'])));
            $url = str_replace("#content#",$data['contents'].iconv("GBK", "UTF-8",'【融易融】'),$url);
            /*$urls = explode('?',$url);
            $filed = explode('&',$url[1]);
            foreach($filed as $value){
                $value = explode('=',$value);
                $post[$value[0]] = $value[1];
            }*/
            $request = Requests::get(trim($url),[],['transport'=>'Requests_Transport_cURL']);
            if($request->status_code==200){
                $data['status'] = self::postSMS($request->body,$data['phone']);
            }else{
                $data['status'] = 0;
                error_log('短信['.$phone."]发送失败HTTP状态为[".$request->status_code."]\n",3, ROOT_PATH."/data/log/sms.".date('Y-m-d').".log");
            }
        }else{
            $data['status'] = 1;
        }
		$sql = "insert into `{approve_smslog}` set  addtime='".time()."',addip='".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
		$mysql->db_query($sql);
		if ($data['status']) return array(1,$url,$data['status']);
		return array(2,$url,$data['status']);
	}
	
	public static function postSMS($xml,$phone){
        $p = xml_parser_create();
        xml_parse_into_struct($p, $xml, $vals);
        xml_parser_free($p);
        // 发送状态写入日志
        if($vals['1']['value']!=0){
            error_log('短信['.$phone."]发送失败接口状态为[".$vals['1']['value']."][".iconv("UTF-8","GBK",$vals[5]['value'])."]\n",3, ROOT_PATH."/data/log/sms.".date('Y-m-d').".log");
            return 0;
        }
        return 1;
	}
		
	/**
	 * 列表
	 *
	 * @return Array
	 */
	function GetSmslogList($data = array()){
		global $mysql;
		global $mysql;
		
		$_sql = " where 1=1 ";
		//判断类型名称是否存在
        if (IsExiest($data['user_id'])!=false) {
            $_sql .= " and p1.user_id ='{$data['user_id']}'";
        }
		
		if (IsExiest($data['username'])!=false) {
            $_sql .= " and p2.username like '%{$data['username']}%'";
        }
		
		
		if (IsExiest($data['phone'])!=false) {
            $_sql .= " and p1.phone like '%{$data['phone']}%'";
        }
		
		
		if (IsExiest($data['status'])!=false || $data['status']=="0") {
            $_sql .= " and p1.status = '{$data['status']}'";
        }
		$_select = " p1.*,p2.username ";
		$_order = " order by p1.id desc";
		$sql = "select SELECT from `{approve_smslog}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id SQL ORDER LIMIT ";
		
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
	 * 添加短信发送记录
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	function AddSmslogGroup($data = array()){
		global $mysql,$_G;
		//手机号码不能为空
		if (!IsExiest($data['type'])) return "approve_sms_send_type_empty";
		if (!IsExiest($data['contents'])) return "approve_sms_send_contents_empty";
		
		if (IsExiest($data['user_id1']) != false && IsExiest($data['user_id2']) != false){
			$sql = "select p1.phone,p1.user_id from `{approve_sms}` as p1 where p1.user_id>='{$data['user_id1']}' or  p1.user_id<='{$data['user_id2']}' and p1.status=1";
			$result =  $mysql->db_fetch_arrays($sql);
			
			if ($result !=false){
				foreach ($result as $key => $value){
					$sql = "insert into `{approve_smslog}` set `addtime` = '".time()."',`addip` = '".ip_address()."',user_id='{$value['user_id']}',status='{$data['status']}',`phone`='{$value['phone']}',`type`='{$data['type']}',`contents`='{$data['contents']}'";
					$mysql->db_query($sql);
					$id = $mysql -> db_insert_id();
					if ($data['status']==1){
						if ($_G["system"]["con_sms_status"]==1){
							//先将所有的状态改为2
							$sql = "update `{approve_smslog}` set status=2 where id='{$id}'";
							$mysql->db_query($sql);
							
							$send_sms["phone"] = $value['phone'];
							$send_sms["contents"] = $data['contents'];
							$send_sms["time"] = $value['user_id'];
							$result = self::SendSMS($send_sms);
							$sql = "update `{approve_smslog}` set status='{$result[0]}',send_code='{$result[1]}',send_time='".time()."',send_return='{$result[2]}',send_status=1 where id='{$id}'";
							$mysql->db_query($sql);
						}else{
							//先将所有的状态改为3,表示短信记录关闭期间发送的信息
							$sql = "update `{approve_smslog}` set status=3 where id='{$id}'";
							$mysql->db_query($sql);
						}
					}
					return 1;
				}
			}
		}elseif (IsExiest($data['username']) != false){
			$sql = "select p1.phone,p1.user_id from `{approve_sms}` as p1 left join `{users}`as p2 on p1.user_id = p2.user_id where p2.username='{$data['username']}' and p1.status=1";
			$result =  $mysql->db_fetch_array($sql);
			if ($result==false ) return "approve_sms_phone_not_check";
			$data["phone"] = $result['phone'];
			$data["user_id"] = $result['user_id'];
		}elseif (IsExiest($data['phone']) == false){
			$data["user_id"] = 0;
		}elseif (IsExiest($data['user_id']) != false){
			
		}else{
			return "approve_sms_send_not_select";
		}
		
		$sql = "insert into `{approve_smslog}` set `addtime` = '".time()."',`addip` = '".ip_address()."',user_id='{$data['user_id']}',status='{$data['status']}',`code`='{$data['code']}',`phone`='{$data['phone']}',`type`='{$data['type']}',`contents`='{$data['contents']}'";
		
		$mysql->db_query($sql);
		$id = $mysql -> db_insert_id();
		
		if ($data['status']==1){
			if ($_G["system"]["con_sms_status"]==1){
				//先将所有的状态改为2
				$sql = "update `{approve_smslog}` set status=2 where id='{$id}'";
				$mysql->db_query($sql);
				
				$send_sms["phone"] = $data['phone'];
				$send_sms["contents"] = $data['contents'];
				$send_sms["time"] = $data['user_id'];
				$result = self::SendSMS($send_sms);
				$sql = "update `{approve_smslog}` set status='{$result[0]}',send_code='{$result[1]}',send_time='".time()."',send_return='{$result[2]}',send_status=1 where id='{$id}'";
				$mysql->db_query($sql);
			}else{
				//先将所有的状态改为3,表示短信记录关闭期间发送的信息
				$sql = "update `{approve_smslog}` set status=3 where id='{$id}'";
				$mysql->db_query($sql);
			}
		}
		return $id;
	}
	
	
	/**
	 * 查看
	 *
	 * @param Array $data = array("id"=>"");
	 * @return Array
	 */
	public static function GetSmslogOne($data = array()){
		global $mysql;
		if (!IsExiest($data['id'])) return "approve_smslog_id_empty";
		$sql = "select p1.*,p2.username from `{approve_smslog}` as p1  left join `{users}` as p2 on p1.user_id=p2.user_id where id='{$data['id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "approve_smslog_not_exiest";
		$result['contents']=iconv("UTF-8","GBK",$result['contents']);
		return $result;
	}
	
	
	

	
	/**
	 * 5,获得用户相册列表
	 *
	 * @return Array
	 */
	function GetUserid($data = array()){
		global $mysql;
		
		
		$_sql = " where 1=1 ";
		
		//搜索用户id
        if (IsExiest($data['user_id'])!=false) {
            $_sql .= " and p2.user_id ='{$data['user_id']}'";
        }
		
		//搜索用户名
		elseif (IsExiest($data['username'])!=false) {
            $_sql .= " and p2.username = '{$data['username']}'";
        }
		
		//搜索用户名
		elseif (IsExiest($data['email'])!=false) {
            $_sql .= " and p2.email = '{$data['email']}'";
        }
		
		$sql = "select p2.user_id from `{users}` as p2 {$_sql}";
		$result = $mysql -> db_fetch_array($sql);
		if ($result == false  || (!IsExiest($data['username']) && !IsExiest($data['user_id']) && !IsExiest($data['email']))){
			return "realname_user_not_exiest";
		}
		return $result['user_id'];
	}
	
	
	function AddRealname($data){
		global $mysql;
		if ($data["pic_result"]!=""){
			foreach ($data["pic_result"] as $key => $value){
				$sql = "insert into `{approve_realname}` set addtime='".time()."',addip='".ip_address()."',user_id='{$data['user_id']}',upfiles_id='{$value['upfiles_id']}',`order`='{$value['order']}',type_id='{$data['type_id']}'";
				$mysql->db_query($sql);
			}
		}else{
			$sql = "select 1 from `{approve_realname}` where user_id='{$data['user_id']}'";
			$result=$mysql->db_fetch_array($sql);	
			if ($result==false){
				$sql = "insert into `{approve_realname}` set addtime='".time()."',addip='".ip_address()."',user_id='{$data['user_id']}',realname='{$data['realname']}',card_id='{$data['card_id']}'";
				$mysql->db_query($sql);
			}else{
				$sql = "Update `{approve_realname}` set realname='{$data['realname']}',card_id='{$data['card_id']}' where user_id='{$data['user_id']}'";
				$mysql->db_query($sql);
			}
		}
		$user_info['realname'] = $data["realname"];
		$user_info['realname_status'] = 0;
		$result = usersClass::UpdateUsersInfo($user_info);
		return true;
	}
	
	
	function GetRealnameList($data = array()){
		global $mysql;
		
		
		$_sql = " where 1=1 ";
		
		//搜索用户id
        if (IsExiest($data['user_id'])!=false) {
            $_sql .= " and p1.user_id ='{$data['user_id']}'";
        }
		
		
		if (IsExiest($data['realname'])!=false) {
            $_sql .= " and p1.realname like '%".urldecode($data['realname'])."%'";
        }
		
		if (IsExiest($data['card_id'])!=false) {
            $_sql .= " and p1.card_id like '%{$data['card_id']}%'";
        }
		
		//搜索用户名
		if (IsExiest($data['username'])!=false) {
            $_sql .= " and p2.username = '".urldecode($data['username'])."'";
        }
		
		//搜索用户名
		if ($data['status']!=='') {
            $_sql .= " and p1.status = '{$data['status']}'";
            // 更新 判断条件
            if($data['status']=='0'){
                $_sql .=' and p1.card_pic1!=\'\' and p1.card_pic2!=\'\'';
            }
        }
				//判断添加时间开始
		if (IsExiest($data['dotime1']) != false){
			if (!empty($data['dotime1'])){
				$_sql .= " and p1.addtime > ".get_mktime($data['dotime1']);
			}
		}
		
		//判断添加时间结束
		if (IsExiest($data['dotime2'])!=false){
			if (!empty($data['dotime2'])){
				$_sql .= " and p1.addtime < ".get_mktime($data['dotime2']);
			}
		}
		
		$_order = " order by p1.status asc,p1.addtime desc";
		$_select = " p1.*,p2.username,p3.fileurl as card_pic1_url,p4.fileurl as card_pic2_url";
		$sql = "select SELECT from `{approve_realname}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id left join `{users_upfiles}` as p3 on p1.card_pic1 = p3.id left join `{users_upfiles}` as p4 on p1.card_pic2 = p4.id  SQL ORDER LIMIT";
		
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
		$_limit = " limit ".($data["epage"] * ($data["page"] - 1)).", {$data['epage']}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL','ORDER', 'LIMIT'), array($_select,$_sql,$_order, $_limit), $sql));
		
		//返回最终的结果
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		
		return $result;
	}
	
	
	/**
	 * 6,获得实名认证单条记录
	 *
	 * @param Array $data = array("id"=>"");
	 * @return Array
	 */
	 function GetRealnameOne($data = array()){
		global $mysql;
		if (!IsExiest($data['user_id']) ) return "approve_realname_user_id_empty";
		
		$_sql = "where 1=1 ";
		
		if (IsExiest($data['user_id'])!=false) {
            $_sql .= " and p1.user_id = '{$data['user_id']}'";
        }
		if (IsExiest($data['status'])!=false) {
            $_sql .= " and p1.status = '{$data['status']}' ";
        }
		
		$_select = " p1.*,p2.username,p3.fileurl as card_pic1_url,p4.fileurl as card_pic2_url";
		$sql = "select {$_select} from `{approve_realname}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id left join `{users_upfiles}` as p3 on p1.card_pic1 = p3.id left join `{users_upfiles}` as p4 on p1.card_pic2 = p4.id  $_sql";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false){
			$sql = " insert into `{approve_realname}` set user_id='{$data['user_id']}',status=0";
			$mysql->db_query($sql);
			
			$user_info['realname'] = $data["realname"];
			$user_info['realname_status'] = 0;
			$result = usersClass::UpdateUsersInfo($user_info);
			
			$result = self::GetRealnameOne($data);
			return ;
		}
		$result['card_id_admin'] = $result['card_id'];
		if($result['card_id']!=''){
			$result['card_id'] = substr($result['card_id'],0,4)."****".substr($result['card_id'],-3,3);
		}
		return $result;
	}
	
	/**
	 * 2,修改实名认证
	 *
	 * @param array $data =array("id"=>"id","name"=>"名称","status"=>"状态");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function UpdateRealname($data = array()){
		global $mysql,$upload;
		
		//id
		if (!IsExiest($data['user_id'])) return "approve_realname_user_id_empty";
		
         //判断真实姓名是否存在
        if (!IsExiest($data['realname'])) {
            return "approve_realname_realname_empty";
        }
         //判断身份证号是否存在
        if (!IsExiest($data['card_id'])) {
            return "approve_realname_card_id_empty";
        }
		if (!self::isIdCard($data['card_id'])) {
			return "approve_realname_card_id_error";
		}
		
		$sql = "select * from `{approve_realname}` where card_id='{$data['card_id']}' and status=1 and user_id!='{$data['user_id']}'" ;
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false) { return "approve_realname_card_id_exiest";}
		
		$result = self::GetRealnameOne(array("user_id"=>$data['user_id']));
   		if (IsExiest($data['card_pic1'])!=false){
			$_data['user_id'] = $result["user_id"];
			$_data['id'] = $result["card_pic1"];
			$upload->Delete($_data);
		}
		if (IsExiest($data['card_pic2'])!=false){
			$_data['user_id'] = $result["user_id"];
			$_data['id'] = $result["card_pic2"];
			$upload->Delete($_data);
		}
		$sql = "update `{approve_realname}` set addtime='".time()."',addip='".ip_address()."',";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql)." where user_id='{$data['user_id']}'");
		
		$user_info['realname'] = $data["realname"];
		$user_info['user_id'] = $data['user_id'];
		$result = usersClass::UpdateUsersInfo($user_info);
		
		return $data["user_id"];
	}
	

	
	/**
	 *4,审核实名认证
	 *
	 * @param Array $data = array("id"=>"");
	 * @return id
	 */
	 function CheckRealname($data = array()){
		global $mysql;
		if (!IsExiest($data['user_id'])) return "approve_realname_user_id_empty";
		
		$sql = "select p1.*,p2.username from `{approve_realname}` as p1  left join `{users}` as p2 on p1.user_id=p2.user_id where p1.user_id='{$data['user_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "approve_realname_empty";
		$realname = $result['realname'];
		if ($data['status']==1){
			$sql = "select * from `{approve_realname}` where card_id='{$result['card_id']}' and status=1 and user_id!='{$data['user_id']}'";
			$result = $mysql->db_fetch_array($sql);
			if ($result!=false) { return "approve_realname_card_id_exiest";}
		}
		$birthyear = $birthmonth = $birthday = 0;
        if(!empty($result['card_id'])){
            $birthyear = substr($result['card_id'],6,4);
            $birthmonth = substr($result['card_id'],10,2);
            $birthday = substr($result['card_id'],12,2);
        }
		$sql = "update `{approve_realname}` set verify_userid='{$data['verify_userid']}',verify_remark='{$data['verify_remark']}', verify_time='".time()."',status='{$data['status']}' where user_id='{$data['user_id']}'";
		$mysql->db_query($sql);
        $sql = "update `{users_info}` set birthyear='{$birthyear}',birthmonth='{$birthmonth}',birthday='{$birthday}' where user_id='{$data['user_id']}'";
        $mysql->db_query($sql);
		$user_info['user_id'] = $data["user_id"];
		if ($data['status']==1){
			//充值失败站内信提醒 
			$remind['nid'] = "approve_realname_success";
			$remind['receive_userid'] = $data["user_id"];
            $remind['remind_nid'] =  "approve_realname_success_".$data["user_id"]."_".time();
			$remind['code'] = "approve";
			$remind['article_id'] = $data["user_id"];
			$remind['title'] = "实名认证审核成功";
			$remind['content'] = "您在平台上的实名认证已经审核通过。";
			remindClass::sendRemind($remind);
		}else{
		  	//充值失败站内信提醒 
			$remind['nid'] = "approve_realname_false";
			$remind['receive_userid'] = $data["user_id"];
            $remind['remind_nid'] =  "approve_realname_false_".$result["user_id"]."_".time();
			$remind['code'] = "approve";
			$remind['article_id'] = $data["user_id"];
			$remind['title'] = "实名认证审核失败";
			$remind['content'] = "实名认证审核失败。失败原因：{$data['verify_remark']}";
			remindClass::sendRemind($remind);
		  $realname = "";
		}
		$user_info['realname'] = $realname;
		$user_info['realname_status'] = $data['status'];
		$result = usersClass::UpdateUsersInfo($user_info);
		//加入审核记录
		$_data["user_id"] = $result["user_id"];
		$_data["result"] = $data["status"];
		$_data["code"] = "approve";
		$_data["type"] = "realname";
		$_data["article_id"] = $data["user_id"];
		$_data["verify_userid"] = $data["verify_userid"];
		$_data["remark"] = $data["verify_remark"];
		usersClass::AddExamine($_data);
		
		//添加积分记录
		$credit_log['user_id'] = $data['user_id'];
		$credit_log['nid'] = "realname";
		$credit_log['code'] = "approve";
		$credit_log['type'] = "realname";
		$credit_log['addtime'] = time();
		$credit_log['article_id'] =$data['user_id'];
		$credit_log['remark'] = "实名认证通过所得积分";
		//creditClass::ActionCreditLog($credit_log);
		
		return $data['user_id'];
	}
	
	
	/**
	 *4,审核实名认证ID5
	 *
	 * @param Array $data = array("id"=>"");
	 * @return id
	 */
	 function CheckRealnameId5($data = array()){
		global $mysql;
		if (!IsExiest($data['user_id'])) return "approve_realname_user_id_empty";
		
		$sql = "select p1.*,p2.username from `{approve_realname}` as p1  left join `{users}` as p2 on p1.user_id=p2.user_id where p1.user_id='{$data['user_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "approve_realname_empty";
		
		//已经通过审核，将不再进行id5的审核
		if ($result['status']>0){
			return "approve_realname_check_yes";
		}
		
		if ($data['status']==1){
			$sql = "select * from `{approve_realname}` where card_id='{$result['card_id']}' and status=1 and user_id!='{$data['user_id']}'";
			$_result = $mysql->db_fetch_array($sql);
			if ($_result!=false) { return "approve_realname_card_id_exiest";}
		}
		$_id5['realname'] = $result['realname'];
		$_id5['card_id'] = $result['card_id'];
		$_id5['user_id'] = $result['user_id'];
		$_id5['type'] = 'realname';
		$status = id5Class::CheckId5($_id5);
		$id5_status = $status>0?$status:0;
		if ($id5_status==3){
			$status=1;
		}else{
			$status =2;
		}
		$sql = "update `{approve_realname}` set verify_id5_userid='{$data['verify_id5_userid']}',verify_id5_remark='{$data['verify_id5_remark']}', verify_id5_time='".time()."',id5_status='{$id5_status}',status='{$status}' where user_id='{$data['user_id']}'";
		$mysql->db_query($sql);
		
		//加入审核记录
		$_data["user_id"] = $result["user_id"];
		$_data["result"] = $data["status"];
		$_data["code"] = "approve";
		$_data["type"] = "realname";
		$_data["article_id"] = $data["user_id"];
		$_data["verify_userid"] = $data["verify_userid"];
		$_data["remark"] = $data["verify_remark"];
		usersClass::AddExamine($_data);
		
		//添加积分记录
		$credit_log['user_id'] = $data['user_id'];
		$credit_log['nid'] = "realname";
		$credit_log['code'] = "approve";
		$credit_log['type'] = "realname";
		$credit_log['addtime'] = time();
		$credit_log['article_id'] =$data['user_id'];
		$credit_log['remark'] = "实名认证通过所得积分";
		//creditClass::ActionCreditLog($credit_log);
		return $data['user_id'];
	}
	
	/**
	 *4,审核实名认证ID5
	 *
	 * @param Array $data = array("id"=>"");
	 * @return id
	 */
	 function CheckEduId5($data = array()){
		global $mysql;
		if (!IsExiest($data['user_id'])) return "approve_edu_user_id_empty";
		
		$sql = "select p1.*,p2.username from `{approve_edu}` as p1  left join `{users}` as p2 on p1.user_id=p2.user_id where p1.user_id='{$data['user_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "approve_edu_empty";
		
		//已经通过审核，将不再进行id5的审核
		if ($result['status']>0){
			return "approve_edu_check_yes";
		}
		//判断edu的实名是否已经通过
		$sql = "select * from `{approve_realname}` where user_id='{$data['user_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result['status']!=1) return "approve_edu_realname_not_check";
		
		$_id5['realname'] = $result['realname'];
		$_id5['card_id'] = $result['card_id'];
		$_id5['user_id'] = $result['user_id'];
		$_id5['type'] = 'edu';
		$status = id5Class::CheckId5Edu($_id5);
		$id5_status = $status>0?$status:0;
		if ($id5_status==3){
			$status=1;
		}else{
			$status =2;
		}
		$sql = "update `{approve_edu}` set verify_id5_userid='{$data['verify_id5_userid']}',verify_id5_remark='{$data['verify_id5_remark']}', verify_id5_time='".time()."',id5_status='{$id5_status}',status='{$status}' where user_id='{$data['user_id']}'";
		$mysql->db_query($sql);
		
		//加入审核记录
		$_data["user_id"] = $result["user_id"];
		$_data["result"] = $data["status"];
		$_data["code"] = "approve";
		$_data["type"] = "realname";
		$_data["article_id"] = $data["user_id"];
		$_data["verify_userid"] = $data["verify_userid"];
		$_data["remark"] = $data["verify_remark"];
		usersClass::AddExamine($_data);
	
		//添加积分记录
		$credit_log['user_id'] = $data['user_id'];
		$credit_log['nid'] = "education";
		$credit_log['code'] = "approve";
		$credit_log['type'] = "education";
		$credit_log['addtime'] = time();
		$credit_log['article_id'] =$data['user_id'];
		$credit_log['remark'] = "学历认证通过所得积分";
		//creditClass::ActionCreditLog($credit_log);
		
		$user_info['user_id'] = $data['user_id'];
		$user_info['education_status'] = $data['status'];
		$result = usersClass::UpdateUsersInfo($user_info);
		return $data['user_id'];
	}
	
	function isIdCard($number) {
		//加权因子 
		$wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
		//校验码串 
		$ai = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
		//按顺序循环处理前17位 
		for ($i = 0;$i < 17;$i++) { 
			//提取前17位的其中一位，并将变量类型转为实数 
			$b = (int) $number{$i}; 
	 
			//提取相应的加权因子 
			$w = $wi[$i]; 
	 
			//把从身份证号码中提取的一位数字和加权因子相乘，并累加 
			$sigma += $b * $w; 
		}
		//计算序号 
		$snumber = $sigma % 11; 
	 
		//按照序号从校验码串中提取相应的字符。 
		$check_number = $ai[$snumber];
	 
		if ($number{17} == $check_number) {
			return true;
		} else {
			return false;
		}
	}
	
	
	/**
	 * 列表
	 *
	 * @return Array
	 */
	function GetId5List($data = array()){
		global $mysql;
		
		$_sql = " where 1=1 ";
		//判断类型名称是否存在
        if (IsExiest($data['user_id'])!=false) {
            $_sql .= " and p1.user_id ='{$data['user_id']}'";
        }
		
		if (IsExiest($data['username'])!=false) {
            $_sql .= " and p2.username like '%{$data['username']}%'";
        }
		
		
		if (IsExiest($data['realname'])!=false) {
            $_sql .= " and p1.realname = '".urldecode($data['realname'])."'";
        }
		
		if (IsExiest($data['card_id'])!=false) {
            $_sql .= " and p1.card_id like '%{$data['card_id']}%'";
        }
		
		
		if (IsExiest($data['status'])!=false || $data['status']=="0") {
            $_sql .= " and p1.status = '{$data['status']}'";
        }
		$_select = " p1.*,p2.username ";
		$_order = " order by p1.id desc";
		$sql = "select SELECT from `{approve_id5}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id SQL ORDER LIMIT ";
		
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
	 * 列表
	 *
	 * @return Array
	 */
	function GetEduId5List($data = array()){
		global $mysql;
		
		$_sql = " where 1=1 ";
		//判断类型名称是否存在
        if (IsExiest($data['user_id'])!=false) {
            $_sql .= " and p1.user_id ='{$data['user_id']}'";
        }
		
		if (IsExiest($data['username'])!=false) {
            $_sql .= " and p2.username like '%{$data['username']}%'";
        }
		
		if (IsExiest($data['realname'])!=false) {
            $_sql .= " and p1.realname = '".urldecode($data['realname'])."'";
        }
		
		if (IsExiest($data['card_id'])!=false) {
            $_sql .= " and p1.card_id like '%{$data['card_id']}%'";
        }
		
		if (IsExiest($data['status'])!=false || $data['status']=="0") {
            $_sql .= " and p1.status = '{$data['status']}'";
        }
		$_select = " p1.*,p2.username ";
		$_order = " order by p1.id desc";
		$sql = "select SELECT from `{approve_edu_id5}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id SQL ORDER LIMIT ";
		
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
	
	function GetAllList($data = array()){
		global $mysql;
		
		$_sql = " where 1=1 ";
		
		//搜索用户id
        if (IsExiest($data['user_id'])!=false) {
            $_sql .= " and p1.user_id ='{$data['user_id']}'";
        }
		
		//搜索用户名
		if (IsExiest($data['username'])!=false) {
            $_sql .= " and p1.username = '".urldecode($data['username'])."'";
        }
		
		$_order = " order by p1.user_id";
		$_select = " p1.*";
		$sql = "select SELECT from `{users}` as p1 SQL ORDER LIMIT";
		
		//是否显示全部的信息
		if (IsExiest($data['limit'])!=false){
			if ($data['limit'] != "all"){ $_limit = "  limit ".$data['limit']; }
			$result=$mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array($_select, $_sql, $_order, $_limit), $sql));
			foreach($result as $key => $value){
				$result[$key]['realname_status']=self::GetRealnameOne(array("user_id"=>$value['user_id']));
				$result[$key]['edu_status']=self::GetEduOne(array("user_id"=>$value['user_id']));
				$result[$key]['sms_status']=self::GetSmsOne(array("user_id"=>$value['user_id']));
				$result[$key]['video_status']=self::GetVideoOne(array("user_id"=>$value['user_id']));
			}
			return $result;
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
		foreach($list as $key => $value){
			$list[$key]['realname_status']=self::GetRealnameOne(array("user_id"=>$value['user_id']));
			$list[$key]['edu_status']=self::GetEduOne(array("user_id"=>$value['user_id']));
			$list[$key]['sms_status']=self::GetSmsOne(array("user_id"=>$value['user_id']));
			$list[$key]['video_status']=self::GetVideoOne(array("user_id"=>$value['user_id']));
		}
		//返回最终的结果
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		
		return $result;
	}
	
	function GetEduList($data = array()){
		global $mysql;
		
		
		$_sql = " where 1=1 ";
		
		//搜索用户id
        if (IsExiest($data['user_id'])!=false) {
            $_sql .= " and p1.user_id ='{$data['user_id']}'";
        }
		
		//搜索用户名
		if (IsExiest($data['username'])!=false) {
            $_sql .= " and p2.username like '%{$data['username']}%'";
        }
		
		//搜索用户名
		if (IsExiest($data['status'])!=false) {
            $_sql .= " and p1.status = '{$data['status']}'";
        }
		
		$_order = " order by p1.status asc, p1.addtime desc";
		$_select = " p1.*,p2.username,p3.fileurl as edu_pic_url,p4.realname";
		$sql = "select SELECT from `{approve_edu}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id left join `{approve_realname}` as p4 on p1.user_id=p4.user_id left join `{users_upfiles}` as p3 on p1.edu_pic = p3.id  SQL ORDER LIMIT";
		
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
		$_limit = " limit ".($data["epage"] * ($data["page"] - 1)).", {$data['epage']}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL','ORDER', 'LIMIT'), array($_select,$_sql,$_order, $_limit), $sql));
		
		//返回最终的结果
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		
		return $result;
	}
	
	
	/**
	 * 6,获得相册的单条记录
	 *
	 * @param Array $data = array("id"=>"");
	 * @return Array
	 */
	 function GetEduOne($data = array()){
		global $mysql;
		if (!IsExiest($data['user_id'])) return "approve_edu_user_id_empty";
		
		$_select = " p1.*,p2.username,p3.fileurl as edu_pic_url,p4.realname";
		$sql = "select {$_select} from `{approve_edu}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id left join `{approve_realname}` as p4 on p1.user_id=p4.user_id left join `{users_upfiles}` as p3 on p1.edu_pic = p3.id  where p1.user_id={$data['user_id']}";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false){
			$sql = " insert into `{approve_edu}` set user_id='{$data['user_id']}',status=0";
			$mysql->db_query($sql);
			$result = self::GetRealnameOne($data);
		}
		
		return $result;
	}
	
	/**
	 * 2,修改学历
	 *
	 * @param array $data =array("id"=>"id","name"=>"名称","status"=>"状态");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function UpdateEdu($data = array()){
		global $mysql,$upload;
		
		//id
		if (!IsExiest($data['user_id'])) return "approve_edu_user_id_empty";
		
		$result = self::GetEduOne(array("user_id"=>$data['user_id']));
   		if (IsExiest($data['edu_pic'])!=false){
			$_data['user_id'] = $result["user_id"];
			$_data['id'] = $result["edu_pic"];
			$upload->Delete($_data);
		}
		
		$sql = "update `{approve_edu}` set addtime='".time()."',addip='".ip_address()."',";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql)." where user_id='{$data['user_id']}'");
		return $data["user_id"];
	}
	
	
	
	/**
	 *4,审核实名认证
	 *
	 * @param Array $data = array("id"=>"");
	 * @return id
	 */
	 function CheckEdu($data = array()){
		global $mysql;
		if (!IsExiest($data['user_id'])) return "approve_edu_user_id_empty";
		
		$sql = "select p1.*,p2.username from `{approve_edu}` as p1  left join `{users}` as p2 on p1.user_id=p2.user_id where p1.user_id='{$data['user_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "approve_edu_empty";
		
		
		$sql = "update `{approve_edu}` set verify_userid='{$data['verify_userid']}',verify_remark='{$data['verify_remark']}', verify_time='".time()."',status='{$data['status']}' where user_id='{$data['user_id']}'";
		$mysql->db_query($sql);
		
		//加入审核记录
		$_data["user_id"] = $result["user_id"];
		$_data["result"] = $data["status"];
		$_data["code"] = "approve";
		$_data["type"] = "edu";
		$_data["article_id"] = $data["user_id"];
		$_data["verify_userid"] = $data["verify_userid"];
		$_data["remark"] = $data["verify_remark"];
		usersClass::AddExamine($_data);
		
		if ($data['status']==1){
			//添加积分记录
			$sql="select * from `{credit_log}` where user_id={$data['user_id']} and type='education'";
			$cre_result=$mysql->db_fetch_array($sql);
			if ($cre_result==false){
				$credit_log['user_id'] = $data['user_id'];
				$credit_log['nid'] = "education";
				$credit_log['code'] = "approve";
				$credit_log['type'] = "education";
				$credit_log['addtime'] = time();
				$credit_log['article_id'] =$data['user_id'];
				$credit_log['remark'] = "学历认证通过所得积分";
				//creditClass::ActionCreditLog($credit_log);
			}
            
            //充值失败站内信提醒 
			$remind['nid'] = "approve_edu_success";
			$remind['receive_userid'] = $data["user_id"];
            $remind['remind_nid'] =  "approve_edu_success_".$data["user_id"]."_".time();
			$remind['code'] = "approve";
			$remind['article_id'] = $data["user_id"];
			$remind['title'] = "学历认证审核成功";
			$remind['content'] = "您所申请的学历认证已经通过。";
			remindClass::sendRemind($remind);
		}else{
		    //充值失败站内信提醒 
			$remind['nid'] = "approve_edu_false";
			$remind['receive_userid'] = $data["user_id"];
            $remind['remind_nid'] =  "approve_edu_false_".$data["user_id"]."_".time();
			$remind['code'] = "approve";
			$remind['article_id'] = $data["user_id"];
			$remind['title'] = "学历认证审核失败";
			$remind['content'] = "学历认证审核失败。失败原因：{$data['verify_remark']}";
			remindClass::sendRemind($remind);  
			$sql="delete from `{credit_log}` where user_id={$data['user_id']} and type='education'";
			$mysql->db_query($sql);
		}
		
		$user_info['user_id'] = $data['user_id'];
		$user_info['education_status'] = $data['status'];
		$result = usersClass::UpdateUsersInfo($user_info);
		
		return $data['user_id'];
	}
	
	public static function CheckCard($data = array()){
		global $mysql;
		if (!IsExiest($data['user_id'])) return "approve_user_id_empty";
		$sql = "select * from `{approve_realname}` where user_id!='{$data['user_id']}' and card_id='{$data['card_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return false;
		return true;
	}
	
	function GetVideoList($data = array()){
		global $mysql;
		
		
		$_sql = " where 1=1 ";
		
		//搜索用户id
        if (IsExiest($data['user_id'])!=false) {
            $_sql .= " and p1.user_id ='{$data['user_id']}'";
        }
		
		
		//搜索用户名
		if (IsExiest($data['username'])!=false) {
            $_sql .= " and p2.username like '%{$data['username']}%'";
        }
		
		$_order = " order by p1.status asc, p1.addtime desc";
		$_select = " p1.*,p2.username,p4.realname";
		$sql = "select SELECT from `{approve_video}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id left join `{approve_realname}` as p4 on p1.user_id=p4.user_id SQL ORDER LIMIT";
		
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
		$_limit = " limit ".($data["epage"] * ($data["page"] - 1)).", {$data['epage']}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL','ORDER', 'LIMIT'), array($_select,$_sql,$_order, $_limit), $sql));
		
		//返回最终的结果
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		
		return $result;
	}
	
	function GetVideoOne($data = array()){
		global $mysql;
		if (!IsExiest($data['user_id'])) return "approve_realname_user_id_empty";
		
		$_select = " p1.*,p2.username";
		$sql = "select {$_select} from `{approve_video}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.user_id={$data['user_id']}";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false){
			$sql = " insert into `{approve_video}` set user_id='{$data['user_id']}',status=0";
			$mysql->db_query($sql);
			$result = self::GetVideoOne($data);
		}
		
		return $result;
	}
	function GetEmailOne($data = array()){
		global $mysql;
		if (!IsExiest($data['user_id'])) return false;
		
		$_select = " p1.*,p2.username";
		$sql = "select {$_select} from `{users_email}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.user_id={$data['user_id']}";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false){
			return false;
		}		
		return $result;
	}
	/**
	 * 2,修改学历
	 *
	 * @param array $data =array("id"=>"id","name"=>"名称","status"=>"状态");
	 * @param string $data;
	 * @return boolen(true,false)
	 */
	function UpdateVideo($data = array()){
		global $mysql,$upload;
		
		//id
		if (!IsExiest($data['user_id'])) return "approve_video_user_id_empty";
		
		$result = self::GetVideoOne(array("user_id"=>$data['user_id']));
   		
		$sql = "update `{approve_video}` set addtime='".time()."',addip='".ip_address()."',";
		foreach($data as $key => $value){
			$_sql[] = "`$key` = '$value'";
		}
        $mysql->db_query($sql.join(",",$_sql)." where user_id='{$data['user_id']}'");
		return $data["user_id"];
	}


	/**
	 *4,审核视频
	 *
	 * @param Array $data = array("id"=>"");
	 * @return id
	 */
	 function CheckVideo($data = array()){
		global $mysql;
		if (!IsExiest($data['user_id'])) return "approve_edu_user_id_empty";
		
		$sql = "select p1.*,p2.username from `{approve_video}` as p1  left join `{users}` as p2 on p1.user_id=p2.user_id where p1.user_id='{$data['user_id']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "approve_video_empty";
		
		
		$sql = "update `{approve_video}` set verify_userid='{$data['verify_userid']}',verify_remark='{$data['verify_remark']}', verify_time='".time()."',status='{$data['status']}' where user_id='{$data['user_id']}'";
		$mysql->db_query($sql);
		
		//加入审核记录
		$_data["user_id"] = $result["user_id"];
		$_data["result"] = $data["status"];
		$_data["code"] = "approve";
		$_data["type"] = "video";
		$_data["article_id"] = $data["user_id"];
		$_data["verify_userid"] = $data["verify_userid"];
		$_data["remark"] = $data["verify_remark"];
		usersClass::AddExamine($_data);
		
		//添加积分记录
		$credit_log['user_id'] = $data['user_id'];
		$credit_log['nid'] = "video";
		$credit_log['code'] = "approve";
		$credit_log['type'] = "video";
		$credit_log['addtime'] = time();
		if ($data['verify_credit']>0){
			$credit_log['value'] = $data['verify_credit'];
		}
		$credit_log['article_id'] =$data['user_id'];
		$credit_log['remark'] = "视频认证通过所得积分";
		//creditClass::ActionCreditLog($credit_log);
		
		$user_info['user_id'] = $data['user_id'];
		$user_info['video_status'] = $data['status'];
		$result = usersClass::UpdateUsersInfo($user_info);
		
		return $data['user_id'];
	}
	
}

?>