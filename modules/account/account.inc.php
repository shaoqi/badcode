<?
/******************************
 * $File: account.inc.php
 * $Description: 资金用户处理文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

require_once("account.class.php");
require_once("account.count.php");
require_once(ROOT_PATH."modules/users/users.class.php");

if ($_U['query_type']=="bank_new"){
	if (IsExiest($_POST['account'])!=false){
		$var = array("province","city","account","bank","branch");
		$data = post_var($var);
		$data['user_id'] = $_G['user_id'];
		$bank_id = intval($_POST['id']);
		if (!empty($bank_id)){
			$data['id'] = $bank_id;
			$result = accountClass::UpdateUsersBank($data);
		}else{
			$result = accountClass::AddUsersBank($data);
		}
		if ($result>0){
			$msg = array("资金账户操作成功","",$_U['query_url']."/bank");
		}else{
			$msg = array($MsgInfo[$result],"",$_U['query_url_all']);
		}
	}elseif($_REQUEST['id']!=""){
		$_U['account_bank_result'] = accountClass::GetUsersBankOne(array("user_id"=>$_G['user_id'],"id"=>$_REQUEST['id']));
		
	}
}

//银行账号删除
elseif ($_U['query_type']=="bank_del"){
	$data['id'] = $_REQUEST['id'];
	$data['user_id'] = $_G['user_id'];
	$result = accountClass::DeleteUsersBank($data);
	if ($result>0){
			$msg = array("资金账户删除成功","",$_U['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result],"",$_U['query_url_all']);
		}
}


elseif ($_U['query_type']=="recharge_new"){
	require_once("account.payment.php");

	if (isset($_POST['money'])){

		
		if ($_POST['valicode']!=$_SESSION["valicode"]){
		  $msg = array("验证码错误。");
		}elseif ($_POST['money']<0){
			$msg = array("金额填写有误");
		}

		if ($msg==""){
			$var = array("money","type","remark","beizhu");
			$data = post_var($var);
			$data['user_id'] = $_G['user_id'];
			$data['status'] = 0;
            
			if (!is_numeric($data['money'])){
				$msg = array("金额填写有误","",$_U['query_url']."/".$_U['query_type']);
			}

			if ($msg==""){
				$url = "";
               
				if ($_POST['type']==1){
				     require_once("account.fee.php");
                    $_fee["vip_status"] = $_G["user_result"]["vip_status"];
                    $_fee["type"] = "recharge_success";
                    $_fee["account"] = $data['money'];
                     $result = accountFeeClass::GetFeeValue($_fee);
                     $data['fee'] = $result[0]['account_fee'];
					$data['payment'] = $_POST['payment_type'];
					$data['remark'] = $_POST['payname'.$_POST['payment_type']]."在线充值";
				}else{
					$data['payment'] = $_POST['payment_bank'];
                    $data['fee'] = 0;
				}
				$data['balance'] = $data['money'] - $data['fee'];

				$data['nid'] = time().rand(1000,9999).$_G['user_id'];
				$result = accountClass::AddRecharge($data);
		  	    $url = "";
				if ($_POST['type']==1){
					$data['recharge_id'] = $result;
					$data['subject'] = "账号充值";
					$data['trade_no'] =$data['nid'];
                    $paymentnid = $_POST['paymentnid'.$_POST['payment_type']];
					$data['bankCode'] = $_POST['bankCode_'.$paymentnid];
					//$data['subject'] = $_G['system']['con_webname']."账号充值";
					$data['body'] = "账号充值";
					$url = accountpaymentClass::ToSubmit($data);
				}
				if ($url!=""){
					header("Location: {$url}");
					exit;
					$msg = array("网站正在转向支付网站<br>如果没反应，请点击下面的支付网站接口","支付网站",$url);
				}else{
					$msg = array("你已经成功提交了充值，请等待管理员的审核。","",$_U['query_url']."/".$_U['query_type']);
				}
				
				
			}else{
				$msg = array("金额填写有误","",$_U['query_url']."/".$_U['query_type']);
			
			}
		}
	}else{
		$_U['account_payment_list'] = accountpaymentClass::GetList(array("status"=>1));
	}
	
}


elseif ($_U['query_type'] == "recharge"){
	if ($_REQUEST["excel"]=="true"){
		include_once("account.excel.php");
		accountexcel::RechargeLog(array("user_id"=>$_G['user_id']));
		exit;
	}
}
elseif ($_U['query_type'] == "cash"){
	if ($_REQUEST["excel"]=="true"){
		include_once("account.excel.php");
		accountexcel::CashLog(array("user_id"=>$_G['user_id']));
		exit;
	}
}



elseif ($_U['query_type'] == "cash_cancel"){	
	if ($_REQUEST["id"]!=""){                                            //add wdf 20120905
		$data['user_id'] = $_G["user_id"];
		$data['id'] = $_REQUEST["id"];
		$result = accountClass::CancelCash($data);
		$msg = array("操作成功","","/?user&q=code/account/cash");
	}
}


elseif ($_U['query_type'] == "cash_new"){	
	
	$data['user_id'] = $_G["user_id"];
	$data['id'] = $_REQUEST["id"];
	$result = accountClass::GetUsersBankOne($data);
	
	$_U['account_bank_result'] = $result;
	
		if(isset($_POST['money'])){
			require_once("account.fee.php");
			$vip_status = usersvipClass::GetUsersVip(array("user_id"=>$_G['user_id']));
			$result_fee = accountFeeClass::GetFeeOne(array("id"=>2));		
			if ($_POST['money']<0){
				$msg = array("金额填写有误","",$_U['query_url']."/".$_U['query_type']);
			}
			$msg = check_valicode(1);
			if ($msg!=""){
				
			}elseif (!isset($_POST['user_bank_id']) || $_POST['user_bank_id']==""){
				$msg = array("请选择银行账号");
			}elseif ($_G['user_info']['paypassword']!=md5($_POST['paypassword'])){
				$msg = array("交易密码填写有误");
			}elseif (!is_numeric($_POST['money'])){
				$msg = array("金额填写有误");
			}elseif ($_POST['money']<0){
				$msg = array("金额必须为正数");	
			}elseif ($_POST['money'] > $result['balance']){            
				$msg = array("您的提现金额大于你所有的可用余额");
			} elseif ( $vip_status['status'] == 1 && ( $_POST['money'] > $result_fee['vip_account_scale_max'] || $_POST['money'] < 100)){
				$msg = array("您的提现金额应在100-".$result_fee['vip_account_scale_max']."之间");
			}elseif ($vip_status['status'] == 0 && ( $_POST['money'] > $result_fee['all_account_scale_max'] || $_POST['money'] < 100)){
				$msg = array("您的提现金额应在100-".$result_fee['all_account_scale_max']."之间");
			}else{
				$_data['user_id'] = $_G["user_id"];
				$_data['id'] = $_POST["user_bank_id"];
				$result = accountClass::GetUsersBankOne($_data);
				$data['status'] = 0;
				$data['total'] = $_POST['money'];
				$data['account'] = $result['account'];
				$data['bank'] = $result['bank'];
				$data['branch'] = $result['branch'];
				$data['province'] = $result['province'];
				$data['city'] = $result['city'];
				$data['nid'] = time().rand(100,999).$_G['user_id'];
                /*
				$vip=usersClass::GetUsersVip(array("user_id"=>$data['user_id']));
				//判断系统是否设置VIP提现免费 by XiaoWu 2012-08-28
				$fee_vip=isset($_G['system']['con_cash_fee_vip'])?$_G['system']['con_cash_fee_vip']:1;
				//判断用户是否为VIP by XiaoWu 2012-08-28
				if ($fee_vip==0 && $vip['status']==1){
					$data['fee'] = 0;
				}else{
				//判断系统设置的手续费收取方式 by XiaoWu 2012-08-28
					$fee_status=isset($_G['system']['con_cash_fee_status'])?$_G['system']['con_cash_fee_status']:1;
					if ($fee_status==1){
						//获取系统设置的手续费收费比例 by XiaoWu 2012-08-28
						$fee_scale=isset($_G['system']['con_cash_fee_scale'])?$_G['system']['con_cash_fee_scale']:0.5;
						$data['fee'] = $data['money']*$fee_scale*0.01;
					}else{
						//获取系统设置的手续费收费金额 by XiaoWu 2012-08-28
						$fee=isset($_G['system']['con_cash_fee'])?$_G['system']['con_cash_fee']:10;
						$data['fee'] = $fee;
					}
				}
                */
                require_once("account.fee.php");
                $_fee["vip_status"] = $_G["user_result"]["vip_status"];
                $_fee["type"] = "cash_success";
                $_fee["account"] = $_POST['money'];
                $result = accountFeeClass::GetFeeValue($_fee);
                $data['fee'] = $result[0]['account_fee'];
                
				$data['credited'] = $data['total']-$data['fee'];
				$result = accountClass::AddCash($data);
				if ($result>0){
					
					if ($_POST['type']=="ajax"){
						
						$msg = array("您的提现已经提交，我们将尽快为你审核","","{$_U['query_url']}/cash");
					}else{
						$msg = array(1);
					}
					
				}else{
					$msg = array($MsgInfo[$result],"","{$_U['query_url']}/cash");
				}
			}
		
	}
	/*if ($_POST['type']=="ajax"){
		
			echo $msg[0];
		
		exit;
	}*/
}


elseif ($_U['query_type'] == "get_fee"){	
    require_once("account.fee.php");
    $_fee["type"] = $_REQUEST["type"];
    $_fee["account"] = $_REQUEST["account"];
    $_fee["vip_status"] = $_REQUEST["vip_status"];
    $result = accountFeeClass::GetFeeValue($_fee);
    if ($result==false){
        echo 0;exit;
    }
    echo $result[0]["account_fee"];
    exit;
    
    
}
$template = "user_account.html";
?>
