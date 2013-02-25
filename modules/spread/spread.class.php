<?php
/******************************
 * $File: spread.class.php
 * $Description: 推广管理
 * $Author: XiaoWu 
 * $Time:2012-04-19
 * $Update:None 
 * $UpdateDate:None 
 * Copyright(c) 2012 by dycms.net. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

require_once(ROOT_PATH."modules/borrow/borrow.class.php");
require_once("spread.model.php");

class spreadClass {
	
	//添加推广设置
	function AddSetting($data){
		global $mysql;
		
		$sql="select * from `{spread_setting}` where month={$data['month']} and type={$data['type']} and task!={$data['task']}";
		$result=$mysql->db_fetch_array($sql);
		if ($result==true) return "spread_task_same"; 
		$sql = "insert into `{spread_setting}` set `addtime` = '".time()."',`addip` = '".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
        return $mysql->db_query($sql);
	}
	
	//更新推广设置
	function UpdateSetting($data){
		global $mysql;
		
		if ($data['id']=="" && $data['id']!=0) return "spread_id_empty";
		
		/*$sql="select * from `{spread_setting}` where month={$data['month']} and type={$data['type']} and task!={$data['task']}";
		$result=$mysql->db_fetch_array($sql);
		if ($result==true) return "spread_task_same";*/
		
		$sql = "update `{spread_setting}` set id = {$data['id']}";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
		$sql .= " where id = {$data['id']}";
        return $mysql->db_query($sql);
	}
	
	function DelSetting($data){
		global $mysql;
		
		if ($data['id']=="" && $data['id']!=0) return "spread_id_empty";
		
		$sql = "delete from `{spread_setting}` where id = {$data['id']}";
        $mysql->db_query($sql);
		return $data['id'];
	}
	
	//获取设置列表
	function GetSettingList($data = array()){
		global $mysql;
		
		$_sql = "where 1=1 ";	
		
		//判断类型
		if (IsExiest($data['type']) != false){
			if ($data['type']==4){
				$_sql .= " and p1.type in (4,5)";
			}elseif ($data['type']==6){
				$_sql .= " and p1.type in (7,8)";
			}else{
				$_sql .= " and p1.type = {$data['type']}";
			}
		}
		
		if (IsExiest($data['month']) != false){
			$_sql .= " and p1.month = {$data['month']}";
		}
		
		//排序
		$_order = " order by p1.id desc ";
	
		$_select = " p1.*,p2.username";
		$sql = "select SELECT from `{spread_setting}` as p1
				 left join `{users}` as p2 on p1.admin_userid=p2.user_id
				 SQL ORDER LIMIT
				";
		
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
	
	//更新推广设置
	function GetSettingOne($data){
		global $mysql;
		
		if ($data['id']=="") return "spread_id_empty";
		
		$sql = "select p1.*,p2.username from `{spread_setting}` as p1 left join `{users}` as p2 on p1.admin_userid=p2.user_id where p1.id={$data['id']}";
		
		$result=$mysql->db_fetch_array($sql);
		return $result;
	}
	
	//投资推广统计
	function GetSpreadUserList($data = array()){
		global $mysql;
		
		$_sql = " where 1=1 ";
		$_select = " p1.*,p2.username,p2.email,p3.name as type_name";
		//判断是否搜索邮箱
		if (IsExiest($data['type_id']) != false){
			$_sql .= " and p1.`type_id` = '{$data['type_id']}'";
		}
		
		$_order = " order by p1.id desc";
		$sql = "select SELECT from `{users_admin}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id  left join `{users_admin_type}` as p3 on p1.type_id=p3.id  SQL ORDER ";
		
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
	
	
	//投资推广客户统计
	function GetSpreadTenderCount($data=array()){
		global $mysql;
		if (IsExiest($data['user_id']) != false){
			$sql="select user_id,addtime,account,status from `{borrow_tender}` where user_id in (select spread_userid from `{spread_user}` where user_id ={$data['user_id']})";
		}else{
			$sql="select user_id,addtime,account,status from `{borrow_tender}` where user_id in (select spread_userid from `{spread_user}` where type=1)";
		}
		$result=$mysql->db_fetch_arrays($sql);
		foreach($result as $key =>$value){
			if (IsExiest($data['user_id'])== false){
				$m=!IsExiest($data['month'])?date("n",time()):$data['month'];
				$suser="select user_id from `{spread_user}` where spread_userid={$value['user_id']}";
				$suser_result=$mysql->db_fetch_array($suser);
				$username=usersClass::GetUsers(array("user_id"=>$suser_result['user_id']));
				if ($m==date("m",$value['addtime'])){
					if ($value['status']==1){
						$Tender[$m]['account']+=$value['account'];//审核通过
					}
				}
				$sql="select task from `{spread_setting}` where month={$m} and type=1";
				$result=$mysql->db_fetch_array($sql);
				$TenderCount[$m]['year']=date("Y",$value['addtime']);//任务额
				$total=date("Y",$value['addtime'])-2012+1;
				$TenderCount[$m]['task']=$result['task'];//任务额
				$TenderCount[$m]['user_id']=$suser_result['user_id'];//任务额
				$TenderCount[$m]['username']=$username['username'];//任务额
				if ($result['task']>0 && $Tender[$m]['account']>0){
					$TenderCount[$m]['task']=$result['task'];//任务额
					$TenderCount[$m]['total']=$Tender[$m]['account'];//当月投资总额
					$TenderCount[$m]['scale']=round($TenderCount[$m]['total']/$TenderCount[$m]['task'],2)*100;//完成率
					$over=$TenderCount[$m]['total']-$TenderCount[$m]['task'];//超出部分
					$_sql="select task_fee from `{spread_setting}` where month={$m} and task_first<={$over} and task_last>{$over} and type=1";
					$_result=$mysql->db_fetch_array($_sql);
					$TenderCount[$m]['task_scale']=$_result['task_fee'];//提成比例
					$TenderCount[$m]['scale_fee']=round($over*$TenderCount[$m]['task_scale']/100,2);//提成收入
					$TenderCountAll=round($over*$TenderCount[$m]['task_scale']/100,2);
				}
			}else{
				for ($m=1;$m<=12;$m++){
					if ($m==date("m",$value['addtime'])){
						if ($value['status']==1){
							$Tender[$m]['account']+=$value['account'];//审核通过
						}
					}
					$sql="select task from `{spread_setting}` where month={$m} and type=1";
					$result=$mysql->db_fetch_array($sql);
					$TenderCount[$m]['year']=date("Y",$value['addtime']);//任务额
					$total=date("Y",$value['addtime'])-2012+1;
					$_sql="select * from `{spread_add}` where month={$m} and year={$TenderCount[$m]['year']} and user_id={$data['user_id']}";
					$_addresult=$mysql->db_fetch_array($_sql);
					if ($_addresult==true){
						$TenderCount[$m]['add_status']=1;
					}
					$TenderCount[$m]['task']=$result['task'];//任务额
					if ($result['task']>0 && $Tender[$m]['account']>0){
						$TenderCount[$m]['task']=$result['task'];//任务额
						$TenderCount[$m]['total']=$Tender[$m]['account'];//当月投资总额
						$TenderCount[$m]['scale']=round($TenderCount[$m]['total']/$TenderCount[$m]['task'],2)*100;//完成率
						$over=$TenderCount[$m]['total']-$TenderCount[$m]['task'];//超出部分
						$_sql="select task_fee from `{spread_setting}` where month={$m} and task_first<={$over} and task_last>{$over} and type=1";
						$_result=$mysql->db_fetch_array($_sql);
						$TenderCount[$m]['task_scale']=$_result['task_fee'];//提成比例
						$TenderCount[$m]['scale_fee']=round($over*$TenderCount[$m]['task_scale']/100,2);//提成收入
						$TenderCountAll=$TenderCount[$m]['scale_fee'];
					}
				}
			}
		}
		$data['page'] = !IsExiest($data['page'])?1:$data['page'];
		$data['epage'] = !IsExiest($data['epage'])?10:$data['epage'];
		$total_page = ceil($total / $data['epage']);
		return array(
            'list' => $TenderCount,'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page,'all'=>$TenderCountAll
        );
	}
	
	function GetMoreList($data){
		global $mysql;
			$feesql="select * from `{spread_setting}`  where type=8";
			$fee=$mysql->db_fetch_array($feesql);
			$sql="select * from `{spread_user}` where user_id={$data['user_id']} and style=1 and type=6";
			$result=$mysql->db_fetch_arrays($sql);
			foreach($result as $key => $value){
				$_sql1="where p1.repay_status=1 and p1.user_id={$value['spread_userid']} and p1.repay_yestime>{$value['updatetime']}";
				if (IsExiest($data['dotime1']) != false){
					$dotime1 = ($data['dotime1']=="request")?$_REQUEST['dotime1']:$data['dotime1'];
					if ($dotime1!=""){
						$_sql1 .= " and p1.addtime > ".get_mktime($dotime1);
					}
				}
				
				if (IsExiest($data['dotime2'])!=false){
					$dotime2 = ($data['dotime2']=="request")?$_REQUEST['dotime2']:$data['dotime2'];
					if ($dotime2!=""){
						$_sql1 .= " and p1.addtime < ".get_mktime($dotime2);
					}
				}
				$_sql="select p1.*,p2.username,p3.name,p3.account as borrow_account from `{borrow_repay}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id left join `{borrow}` as p3 on p1.borrow_nid=p3.borrow_nid $_sql1";
				$_result=$mysql->db_fetch_arrays($_sql);
				foreach($_result as $_key => $_value){
					$list[$_key]['remark']="{$_value['username']}对{$_value['name']}还款，本金{$_value['repay_capital_yes']}元";
					$list[$_key]['addtime']=$_value['repay_yestime'];
					$list[$_key]['money']=round($_value['repay_capital_yes']/100*$fee['task_fee'],2);
					$all_account+=$list[$key]['money'];
					$all_borrow+=$_value['borrow_account'];
				}
			}
			$feesql="select * from `{spread_setting}`  where type=7";
			$fee=$mysql->db_fetch_array($feesql);
			$sql="select * from `{spread_user}` where user_id={$data['user_id']} and style=2 and type=6";
			$result=$mysql->db_fetch_arrays($sql);
			foreach($result as $key => $value){
				$_sql1="where p1.status=1 and p1.user_id={$value['spread_userid']} and p1.addtime>{$value['updatetime']}";
				if (IsExiest($data['dotime1']) != false){
					$dotime1 = ($data['dotime1']=="request")?$_REQUEST['dotime1']:$data['dotime1'];
					if ($dotime1!=""){
						$_sql1 .= " and p1.addtime > ".get_mktime($dotime1);
					}
				}
				
				if (IsExiest($data['dotime2'])!=false){
					$dotime2 = ($data['dotime2']=="request")?$_REQUEST['dotime2']:$data['dotime2'];
					if ($dotime2!=""){
						$_sql1 .= " and p1.addtime < ".get_mktime($dotime2);
					}
				}
				$_sql="select p1.*,p2.username,p3.name from `{borrow_tender}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id left join `{borrow}` as p3 on p1.borrow_nid=p3.borrow_nid $_sql1";
				$_result=$mysql->db_fetch_arrays($_sql);
				foreach($_result as $_key => $_value){
					$list[$key]['remark']="{$_value['username']}对{$_value['name']}投资{$_value['account']}元";
					$list[$key]['addtime']=$_value['addtime'];
					$list[$key]['money']=round($_value['account']/100*$fee['task_fee'],2);
					$all_account+=$list[$key]['money'];
					$all_tender+=$_value['account'];
				}
			}
			$total=count($list);
			$data['page'] = !IsExiest($data['page'])?1:$data['page'];
			$data['epage'] = !IsExiest($data['epage'])?10:$data['epage'];
			$total_page = ceil($total / $data['epage']);
			return array(
				'list' => $list,'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page,'all_account' => $all_account,'all_tender' => $all_tender,'all_borrow' => $all_borrow
			);
	}
	//投资推广客户投资列表
	function GetSpreadTenderList($data){
		global $mysql;
		
		$_select="p1.*,p2.name as borrow_name,p2.account as borrow_account,p2.borrow_apr,p2.borrow_period,p2.vouchstatus,p2.fast_status,p3.username";
		$sql="select $_select from `{borrow_tender}` as p1 left join `{borrow}` as p2 on p1.borrow_nid=p2.borrow_nid left join `{users}` as p3 on p1.user_id=p3.user_id where p1.user_id in (select spread_userid from `{spread_user}` where user_id ={$data['user_id']})";
		$result=$mysql->db_fetch_arrays($sql);
		foreach($result as $key =>$value){
			if ($data['month']==date("m",$value['addtime']) && $value['status']==1){
				$list[$key]['id']=$value['id'];//ID
				$list[$key]['account']=$value['account'];//投标金额
				$list[$key]['borrow_apr']=$value['borrow_apr'];//借款利率
				$list[$key]['borrow_name']=$value['borrow_name'];//借款名称
				$list[$key]['username']=$value['username'];//借款人
				$list[$key]['borrow_nid']=$value['borrow_nid'];//借款编号
				$list[$key]['addtime']=$value['addtime'];//投标成功时间
				$list[$key]['borrow_period']=$value['borrow_period'];//借款期限
				$list[$key]['borrow_account']=$value['borrow_account'];//借款金额
				$list[$key]['fast_status']=$value['fast_status'];//快借标
				$list[$key]['vouchstatus']=$value['vouchstatus'];//担保标
			}
			if ($value['status']==1){
				$all_account+=$value['account'];
			}
		}
		$total=count($list);
		$data['page'] = !IsExiest($data['page'])?1:$data['page'];
		$data['epage'] = !IsExiest($data['epage'])?10:$data['epage'];
		$total_page = ceil($total / $data['epage']);
		return array(
            'list' => $list,'total' => $total,'page' => $data['page'],'all_account' => $all_account,'epage' => $data['epage'],'total_page' => $total_page
        );
	}
	
	//借款推广客户借款列表
	function GetSpreadBorrowList($data){
		global $mysql;
		
		$_select="p1.*,p2.username";
		$sql="select $_select from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.user_id in (select spread_userid from `{spread_user}` where user_id ={$data['user_id']})";
		$result=$mysql->db_fetch_arrays($sql);
		foreach($result as $key =>$value){
			if ($data['month']==date("m",$value['reverify_time']) && $value['status']==3){
				$list[$key]['id']=$value['id'];//ID
				$list[$key]['account']=$value['account'];//借款金额
				$list[$key]['borrow_apr']=$value['borrow_apr'];//借款利率
				$list[$key]['name']=$value['name'];//借款名称
				$list[$key]['username']=$value['username'];//借款人
				$list[$key]['borrow_nid']=$value['borrow_nid'];//借款编号
				$list[$key]['reverify_time']=$value['reverify_time'];//借款复审
				$list[$key]['borrow_period']=$value['borrow_period'];//借款期限
				$list[$key]['fast_status']=$value['fast_status'];//快借标
				$list[$key]['vouchstatus']=$value['vouchstatus'];//担保标
			}
		}
		$total=count($list);
		$data['page'] = !IsExiest($data['page'])?1:$data['page'];
		$data['epage'] = !IsExiest($data['epage'])?10:$data['epage'];
		$total_page = ceil($total / $data['epage']);
		return array(
            'list' => $list,'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page
        );
	}
	
	
	//借款推广统计
	function GetSpreadBorrowCount($data){
		global $mysql;
		if (IsExiest($data['user_id']) != false){
			$sql="select user_id,reverify_time,account,status from `{borrow}` where user_id in (select spread_userid from `{spread_user}` where user_id ={$data['user_id']})";
		}else{
			$sql="select user_id,reverify_time,account,status from `{borrow}` where user_id in (select spread_userid from `{spread_user}`)";
		}
		$result=$mysql->db_fetch_arrays($sql);
		foreach($result as $key =>$value){
			if (IsExiest($data['user_id'])== false){
					$m=!IsExiest($data['month'])?date("n",time()):$data['month'];
					$suser="select user_id from `{spread_user}` where spread_userid={$value['user_id']}";
					$suser_result=$mysql->db_fetch_array($suser);
					$username=usersClass::GetUsers(array("user_id"=>$suser_result['user_id']));
					if ($m==date("m",$value['reverify_time'])){
						if ($value['status']==3){
							$Borrow[$m]['account']+=$value['account'];//审核通过
						}
					}
					$sql="select task from `{spread_setting}` where month={$m} and type=2";
					$result=$mysql->db_fetch_array($sql);
					$BorrowCount[$m]['year']=date("Y",$value['reverify_time']);//任务额
					$total=date("Y",$value['reverify_time'])-2012+1;
					$BorrowCount[$m]['task']=$result['task'];//任务额
					$BorrowCount[$m]['user_id']=$suser_result['user_id'];//任务额
					$BorrowCount[$m]['username']=$username['username'];//任务额
					if ($result['task']>0 && $Borrow[$m]['account']>0){
						$BorrowCount[$m]['task']=$result['task'];//任务额
						$BorrowCount[$m]['total']=$Borrow[$m]['account'];//当月投资总额
						$BorrowCount[$m]['scale']=round($BorrowCount[$m]['total']/$BorrowCount[$m]['task'],2)*100;//完成率
						$over=$BorrowCount[$m]['total']-$BorrowCount[$m]['task'];//超出部分
						$_sql="select task_fee from `{spread_setting}` where month={$m} and task_first<={$over} and task_last>{$over} and type=2";
						$_result=$mysql->db_fetch_array($_sql);
						$BorrowCount[$m]['task_scale']=$_result['task_fee'];//提成比例
						$BorrowCount[$m]['scale_fee']=round($over*$BorrowCount[$m]['task_scale']/100,2);//提成收入
					}
					$BorrowCountAll=$BorrowCount[$m]['scale_fee'];
			}else{
				for ($m=1;$m<=12;$m++){
					if ($m==date("m",$value['reverify_time'])){
						if ($value['status']==3){
							$Borrow[$m]['account']+=$value['account'];//审核通过
						}
					}
					$sql="select task from `{spread_setting}` where month={$m} and type=2";
					$result=$mysql->db_fetch_array($sql);
					$BorrowCount[$m]['year']=date("Y",$value['reverify_time']);//任务额
					$total=date("Y",$value['reverify_time'])-2012+1;
					$BorrowCount[$m]['task']=$result['task'];//任务额
					$_sql="select * from `{spread_add}` where month={$m} and year={$BorrowCount[$m]['year']} and user_id={$data['user_id']}";
					$_addresult=$mysql->db_fetch_array($_sql);
					if ($_addresult==true){
						$BorrowCount[$m]['add_status']=1;
					}
					if ($result['task']>0 && $Borrow[$m]['account']>0){
						$BorrowCount[$m]['task']=$result['task'];//任务额
						$BorrowCount[$m]['total']=$Borrow[$m]['account'];//当月投资总额
						$BorrowCount[$m]['scale']=round($BorrowCount[$m]['total']/$BorrowCount[$m]['task'],2)*100;//完成率
						$over=$BorrowCount[$m]['total']-$BorrowCount[$m]['task'];//超出部分
						$_sql="select task_fee from `{spread_setting}` where month={$m} and task_first<={$over} and task_last>{$over} and type=2";
						$_result=$mysql->db_fetch_array($_sql);
						$BorrowCount[$m]['task_scale']=$_result['task_fee'];//提成比例
						$BorrowCount[$m]['scale_fee']=round($over*$BorrowCount[$m]['task_scale']/100,2);//提成收入
					}
					$BorrowCountAll=$BorrowCount[$m]['scale_fee'];
				}
			}
		}
		$data['page'] = !IsExiest($data['page'])?1:$data['page'];
		$data['epage'] = !IsExiest($data['epage'])?10:$data['epage'];
		$total_page = ceil($total / $data['epage']);
		return array(
            'list' => $BorrowCount,'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page,'all'=>$BorrowCountAll
        );
	}
	
	
	//推广审核部门统计
	function GetSpreadVerifyCount($data){
		global $mysql,$_G;
		
		$sql="select reverify_time,account,status from `{borrow}`";
		
		$result=$mysql->db_fetch_arrays($sql);
		$month=date("m",time());
		foreach($result as $key =>$value){
			if (IsExiest($data['month'])!= false){
				$m=$data['month'];
				if ($m==date("m",$value['reverify_time'])){
					if ($value['status']==3){
						$Verify[$m]['yes']+=$value['account'];//审核通过
					}else{
						$Verify[$m]['no']+=$value['account'];//审核未通过
					}
					if ($value['status']!=0){
						$Verify[$m]['verifyall']+=$value['account'];
					}
				}
				$sql="select task,task_fee from `{spread_setting}` where month={$m} and type=3";
				$_result=$mysql->db_fetch_array($sql);
				if ($Verify[$m]['yes']>0 && $Verify[$m]['no']>0 && $Verify[$m]['verifyall']>0 && $_result['task']>0){
					//年份
					$VerifyCount[$m]['Year']=date("Y",$value['reverify_time']);
					//当月提出申请的总额度
					$VerifyCount[$m]['ApplyTotal']=$Verify[$m]['yes']+$Verify[$m]['no'];
					//当月提出申请的总额度
					$VerifyCount[$m]['Apply']=$Verify[$m]['verifyall'];
					//当月审核比例
					$VerifyCount[$m]['VerifyScale']=round($VerifyCount[$m]['Apply']/$VerifyCount[$m]['ApplyTotal'],2)*100;
					//当月通过审核总额度
					$VerifyCount[$m]['VerifyYes']=$Verify[$m]['yes'];
					//当月通过比例
					$VerifyCount[$m]['VerifyYesScale']=round($VerifyCount[$m]['VerifyYes']/$VerifyCount[$m]['Apply'],2)*100;
					//当月任务额度
					$VerifyCount[$m]['VerifyTask']=$_result['task'];
					//当月提成利率
					$VerifyCount[$m]['VerifyTaskFee']=$_result['task_fee'];
					//当月达成率
					$VerifyCount[$m]['VerifyTaskScale']=round($VerifyCount[$m]['VerifyYes']/$VerifyCount[$m]['VerifyTask'],2)*100;
					//当月提成收入
					if ($VerifyCount[$m]['VerifyYes']-$VerifyCount[$m]['VerifyTask']>0){
						$VerifyCount[$m]['VerifyIncome']=($VerifyCount[$m]['VerifyYes']-$VerifyCount[$m]['VerifyTask'])/100*$_result['task_fee'];
					}else{
						$VerifyCount[$m]['VerifyIncome']=0;
					}
				}
			}else{
				for ($m=1;$m<=12;$m++){
					if ($m==date("n",$value['reverify_time'])){
						if ($value['status']==3){
							$Verify[$m]['yes']+=$value['account'];//审核通过
						}else{
							$Verify[$m]['no']+=$value['account'];//审核未通过
						}
						if ($value['status']!=0){
							$Verify[$m]['verifyall']+=$value['account'];
						}
					}
					$sql="select task,task_fee from `{spread_setting}` where month={$m} and type=3";
					$_result=$mysql->db_fetch_array($sql);
					//当月任务额度
					$VerifyCount[$m]['VerifyTask']=$_result['task'];
					if ($Verify[$m]['yes']>0 && $Verify[$m]['verifyall']>0 && $_result['task']>0){
						//年份
						$VerifyCount[$m]['Year']=date("Y",$value['reverify_time']);
						//当月提出申请的总额度
						$VerifyCount[$m]['ApplyTotal']=$Verify[$m]['yes']+$Verify[$m]['no'];
						//当月提出申请的总额度
						$VerifyCount[$m]['Apply']=$Verify[$m]['verifyall'];
						//当月审核比例
						$VerifyCount[$m]['VerifyScale']=round($VerifyCount[$m]['Apply']/$VerifyCount[$m]['ApplyTotal'],2)*100;
						//当月通过审核总额度
						$VerifyCount[$m]['VerifyYes']=$Verify[$m]['yes'];
						//当月通过比例
						$VerifyCount[$m]['VerifyYesScale']=round($VerifyCount[$m]['VerifyYes']/$VerifyCount[$m]['Apply'],2)*100;
						//当月任务额度
						$VerifyCount[$m]['VerifyTask']=$_result['task'];
						//当月提成利率
						$VerifyCount[$m]['VerifyTaskFee']=$_result['task_fee'];
						//当月达成率
						$VerifyCount[$m]['VerifyTaskScale']=round($VerifyCount[$m]['VerifyYes']/$VerifyCount[$m]['VerifyTask'],2)*100;
						//当月提成收入
						if ($VerifyCount[$m]['VerifyYes']-$VerifyCount[$m]['VerifyTask']>0){
							$VerifyCount[$m]['VerifyIncome']=($VerifyCount[$m]['VerifyYes']-$VerifyCount[$m]['VerifyTask'])/100*$_result['task_fee'];
						}else{
							$VerifyCount[$m]['VerifyIncome']=0;
						}
					}
					$VerifyCountAll+=$VerifyCount[$m]['VerifyIncome'];
				}
			}
		}
		return array(
            'list' => $VerifyCount,'all'=>$VerifyCountAll
        );
	}
	
	function GetSpreadUserOne($data){
		global $mysql;
		if ($data['spread_userid']=="") return "spread_spread_userid_empty";
		
		$sql = "select * from `{spread_user}` where spread_userid={$data['spread_userid']}";
		
		$result=$mysql->db_fetch_array($sql);
		return $result['id'];
	}
	
	function AddSpreadUser($data){
		global $mysql;
		$sql = "insert into `{spread_user}` set `addtime` = '".time()."',`updatetime` = '".time()."',`addip` = '".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
		$_data['user_id']=$data['user_id'];
		$_data['spread_userid']=$data['spread_userid'];
		$_data['type']=$data['type'];
		$_data['style']=$data['style'];
		$_data['status']=1;
		self::AddSpreadLog($_data);
        return $mysql->db_query($sql);
	}
	
	function AddSpreadLog($data){
		global $mysql;
		$sql = "insert into `{spread_log}` set `start_time` = '".time()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
        return $mysql->db_query($sql);
	}
	
	function UpdateSpreadUser($data){
		global $mysql;
		
		if ($data['user_id']=="") return "spread_user_id_empty";
		if ($data['spread_userid']=="") return "spread_spread_userid_empty";
		
		$sql = "update `{spread_user}` set user_id = {$data['user_id']},updatetime =".time();
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
		$sql .= " where spread_userid={$data['spread_userid']}";
		self::UpdateSpreadLog($data);
        return $mysql->db_query($sql);
	}
	
	function UpdateSpreadLog($data){
		global $mysql;
		
		if ($data['user_id']=="") return "spread_user_id_empty";
		if ($data['spread_userid']=="") return "spread_spread_userid_empty";
		$sel="select max(id) as maxid from `{spread_log}` where spread_userid={$data['spread_userid']}";
		$res=$mysql->db_fetch_array($sel);
		if ($res['maxid']>0){
			$sql ="update `{spread_log}` set status = 2,end_time='".time()."' where id ={$res['maxid']}";
			$mysql->db_query($sql);
		}
		$_sql = "insert into `{spread_log}` set `start_time` = '".time()."',status = 1,spread_userid={$data['spread_userid']},user_id={$data['user_id']},type={$data['type']},style={$data['style']}";
        return $mysql->db_query($_sql);
	}
	
	function AddMoney($data){
		global $mysql;
		$sql = "insert into `{spread_add}` set `user_id`={$data['user_id']},`money`={$data['money']},`month`={$data['month']},`year`={$data['year']},`addtime` = '".time()."',`addip` = '".ip_address()."'";
		$mysql->db_query($sql);
		return $data['user_id'];
	}
	
	function GetUser($data){
		global $mysql;
		
		$_sql = " where 1=1 ";
		
		$_select = "p1.user_id,p1.username,p1.email,p2.type,p2.style,p2.addtime as spread_time,p3.username as spread_name";
		
		if (IsExiest($data['username']) != false){
			$_sql .= " and p1.`username` = '".urldecode($data['username'])."'";
		}
		
		if (IsExiest($data['spread_name']) != false){
			$_sql .= " and p3.`username` = '".urldecode($data['spread_name'])."'";
		}
		
		$_order = " order by p1.user_id asc";
		$sql = "select SELECT from `{users}` as p1 left join `{spread_user}` as p2 on p1.user_id=p2.spread_userid  left join `{users}` as p3 on p2.user_id=p3.user_id  SQL ORDER ";
		
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
		foreach($list as $key => $value){
			$list[$key]['credit']=borrowClass::GetBorrowCredit(array("user_id"=>$value['user_id']));
			$list[$key]['vip']=usersClass::GetUsersVip(array("user_id"=>$value['user_id']));
		}
		//返回最终的结果
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		return $result;
	}
	
	function GetSpreadUser($data){
		global $mysql;
		
		$_sql = " where 1=1 and p1.user_id!=0";
		
		$_select = "p1.*,p2.username,p2.email,p3.username as spread_name";
		
		if (IsExiest($data['type']) != false){
			$_sql .= " and p1.`type` in ({$data['type']})";
		}
		
		if (IsExiest($data['username']) != false){
			$_sql .= " and p2.`username` = '".urldecode($data['username'])."'";
		}
		
		if (IsExiest($data['spread_username']) != false){
			$_sql .= " and p3.`spread_username` = '".urldecode($data['spread_username'])."'";
		}
		
		if (IsExiest($data['alone_status']) != false || $data['alone_status']==0){
			$_sql .= " and p1.`alone_status` = '{$data['alone_status']}'";
		}
		$_order = " order by p1.user_id asc";
		$sql = "select SELECT from `{spread_user}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id  left join `{users}` as p3 on p1.spread_userid=p3.user_id  SQL ORDER ";
		
		//是否显示全部的信息
		if (IsExiest($data['limit'])!=false){
			if ($data['limit'] != "all"){ $_limit = "  limit ".$data['limit']; }
			return $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array($_select, $_sql, $_order, $_limit), $sql));
		}
		
		$result = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL','ORDER', 'LIMIT'), array($_select,$_sql,$_order, $_limit), $sql));
		foreach($result as $key => $value){
			$sql="select count(1) as num from `{spread_user}` where user_id={$value['user_id']}";
			$result=$mysql->db_fetch_array($sql);
			$logsql="select * from `{spread_log}` where user_id={$value['user_id']} and status=1";
			$logres=$mysql->db_fetch_array($logsql);
			$borrowsql="select sum(account) as num from `{borrow}` where user_id={$value['spread_userid']} and status=3 and addtime>{$logres['start_time']}";
			$borrow_result=$mysql->db_fetch_array($borrowsql);
			$tendersql="select sum(account) as num from `{borrow_tender}` where user_id={$value['spread_userid']} and tender_status=1 and addtime>{$logres['start_time']}";
			$tender_result=$mysql->db_fetch_array($tendersql);
			$list[$value['user_id']]['id']=$value['id'];
			$list[$value['user_id']]['type']=$value['type'];
			$list[$value['user_id']]['user_id']=$value['user_id'];
			$list[$value['user_id']]['username']=$value['username'];
			$list[$value['user_id']]['email']=$value['email'];
			$list[$value['user_id']]['style']=$value['style'];
			$list[$value['user_id']]['spread_name']=$value['spread_name'];
			$list[$value['user_id']]['user_all']=$result['num'];
			//$_sql="select tender_success_account,borrow_account from `{borrow_count}` where user_id={$value['spread_userid']}";
			//$_result=$mysql->db_fetch_array($_sql);
			//$list[$value['user_id']]['tender_all']+=$_result['tender_success_account'];
			//$list[$value['user_id']]['borrow_all']+=$_result['borrow_account'];
			$list[$value['user_id']]['tender_all']+=$tender_result['num'];
			$list[$value['user_id']]['borrow_all']+=$borrow_result['num'];
			if ($value['style']==1){
				$all_account+=$list[$value['user_id']]['borrow_all'];
			}else{
				$all_account+=$list[$value['user_id']]['tender_all'];
			}
		}
		
		//分页返回结果
		$total=count($list);
		$data['page'] = !IsExiest($data['page'])?1:$data['page'];
		$data['epage'] = !IsExiest($data['epage'])?10:$data['epage'];
		$total_page = ceil($total / $data['epage']);
		$_limit = " limit ".($data["epage"] * ($data["page"] - 1)).", {$data['epage']}";
		//返回最终的结果
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page,'all_account' => $all_account);
		return $result;
	}
}
?>
