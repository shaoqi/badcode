<?
/******************************
 * $File: spread.php
 * $Description: 推广管理
 * $Author: Xiaowu 
 * $Time:2012-04-19
 * $Update:None 
 * $UpdateDate:None 
 * Copyright(c) 2012 by dycms.net. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$_A['list_purview']["spread"]["name"] = "推广管理";
$_A['list_purview']["spread"]["result"]["spread_user"] = array("name"=>"建立推广","url"=>"code/spread/user");
$_A['list_purview']["spread"]["result"]["spread_setting"] = array("name"=>"推广设置","url"=>"code/spread/setting&type=1");
$_A['list_purview']["spread"]["result"]["spread_tender"] = array("name"=>"投资推广","url"=>"code/spread/tender");
$_A['list_purview']["spread"]["result"]["spread_borrow"] = array("name"=>"借款推广","url"=>"code/spread/borrow");
$_A['list_purview']["spread"]["result"]["spread_verify"] = array("name"=>"审核推广","url"=>"code/spread/verify");
//$_A['list_purview']["spread"]["result"]["spread_other"] = array("name"=>"独立推广","url"=>"code/spread/other");
$_A['list_purview']["spread"]["result"]["spread_more"] = array("name"=>"其他推广","url"=>"code/spread/more");

require_once("spread.class.php");
require_once("spread.model.php");
require_once(ROOT_PATH."modules/account/account.class.php");


/**
 * 建立推广
**/
if ($_A['query_type'] == "user"){
	check_rank("spread_user");
	if (isset($_POST['submit'])){
		$var = array("spread_userid","type","alone_status");
		$data = post_var($var);
		$data['style']=0;
		if ($_POST['type']==1){
			$data['user_id']=$_POST['user_id_1'];
		}elseif ($_POST['type']==2){
			$data['user_id']=$_POST['user_id_2'];
		}elseif ($_POST['type']==3){
			$data['user_id']=$_POST['user_id_3'];
			$data['style']=$_POST['style'];
			$data['alone_status']=1;
		}elseif ($_POST['type']==6){
			$data['style']=$_POST['style'];
			$data['user_id']=$_POST['user_id_4'];
		}
		if ($data['user_id']==$data['spread_userid']){
			$msg = array($MsgInfo["spread_user_id_same"],"",$_A['query_url']."/user");
		}
		if ($msg == ""){
			$result=spreadClass::GetSpreadUserOne($data);
			if ($result>0){
				$result=spreadClass::UpdateSpreadUser($data);
			}else{
				$result=spreadClass::AddSpreadUser($data);
			}
			if ($result>0){
				$msg = array($MsgInfo["spread_action_success"],"",$_A['query_url']."/user");
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}
	}
}

/**
 * 投资推广列表
**/
if ($_A['query_type'] == "tender"){

	check_rank("spread_tender");
}

/**
 * 借款推广列表
**/
elseif ($_A['query_type'] == "borrow"){

	check_rank("spread_borrow");
}

/**
 * 借款推广列表
**/
elseif ($_A['query_type'] == "verify"){

	check_rank("spread_verify");
}

/**
 *其他推广列表
**/
elseif ($_A['query_type'] == "other"){

	check_rank("spread_other");
}

elseif ($_A['query_type'] == "addone"){

		if ($_REQUEST['style']=="borrow"){
			$name="借款推广";
		}elseif ($_REQUEST['style']=="tender"){
			$name="投资推广";
		}else{
			$name="其他推广";
		}
		$log_info["user_id"] = $_REQUEST['user_id'];//操作用户id
		$log_info["nid"] = "spread_add_".$_REQUEST['user_id'].time();//订单号
		$log_info["money"] = $_REQUEST['money'];//操作金额
		$log_info["income"] = $_REQUEST['money'];//收入
		$log_info["expend"] = 0;//支出
		$log_info["balance_cash"] = $_REQUEST['money'];//可提现金额
		$log_info["balance_frost"] = 0;//不可提现金额
		$log_info["frost"] = 0;//冻结金额
		$log_info["await"] = 0;//待收金额
		$log_info["type"] = $_REQUEST['style']."_spread_add";//类型
		$log_info["to_userid"] = 0;//付给谁
		$log_info["remark"] =  "{$_REQUEST['year']}年{$_REQUEST['month']}月{$name}费{$_REQUEST['money']}元";
		accountClass::AddLog($log_info);
		$web['money']=$_REQUEST['money'];
		$web['user_id']=$_REQUEST['user_id'];
		$web['nid']="spread_addin_".$_REQUEST['user_id']."_".time();
		$web['type']="spread_addin";
		$web['remark']="用户收到网站打入的{$_REQUEST['year']}年{$_REQUEST['month']}月{$name}费{$_REQUEST['money']}元";
		accountClass::AddAccountWeb($web);
		$data['year']=$_REQUEST['year'];
		$data['month']=$_REQUEST['month'];
		$data['money']=$_REQUEST['money'];
		$data['user_id']=$_REQUEST['user_id'];
		$result=spreadClass::AddMoney($data);
		if ($result>0){
			$msg = array("打入账户成功","",$_A['query_url']."/".$_REQUEST['style']."info&user_id={$_REQUEST['user_id']}");
		}
}

elseif ($_A['query_type'] == "delsetting"){

	check_rank("spread_setting");
	if ($_REQUEST['id']!=""){
		$data['id']=$_REQUEST['id'];
		$result=spreadClass::DelSetting($data);
		if ($result>0){
			$msg = array("删除成功","",$_A['query_url']."/setting&type=1");
		}
	}
}

/**
 *推广参数设置
**/
elseif ($_A['query_type'] == "setting"){

	check_rank("spread_setting");
	check_rank("setting");
	if (isset($_POST['submit'])){
		$var = array("month","task","admin_userid","type","id","task_fee","task_first","task_last");
		$data = post_var($var);
		if ($data['id']==""){
			$result=spreadClass::AddSetting($data);
		}else{
			$result=spreadClass::UpdateSetting($data);
		}
		if ($result>0){
			$msg = array($MsgInfo["spread_action_success"],"",$_A['query_url']."/setting&type=1");
		}else{
			$msg = array($MsgInfo[$result]);
		}
	}
}
?>