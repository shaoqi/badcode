<?php
/******************************
 * $File: return.php
 * $Description: 资金类文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
require_once ('../../core/config.inc.php');
error_reporting(E_ALL);
require_once ('account.class.php');
require_once ("account.payment.php");
//国付宝
if (isset($_POST['respCode']) && !empty($_POST['respCode'])){
    // 重新计算 加密的校验值
    $sql = "select `config` from `{account_payment}` where id = 13";
	$result = $mysql->db_fetch_array($sql);
    $payment_config = unserialize($result['config']);
    $signStr = 'version=[2.1]tranCode=['.$_POST['tranCode'].']merchantID=['.$_POST['merchantID'].']merOrderNum=['.$_POST['merOrderNum'].']tranAmt=['.$_POST['tranAmt'].']feeAmt=['.$_POST['feeAmt'].']tranDateTime=['.$_POST['tranDateTime'].']frontMerUrl=[]backgroundMerUrl=['.$_POST['backgroundMerUrl'].']orderId=['.$_POST['orderId'].']gopayOutOrderId=['.$_POST['gopayOutOrderId'].']tranIP=['.$_POST['tranIP'].']respCode=['.$_POST['respCode'].']gopayServerTime=[]VerficationCode=['.$payment_config['VerficationCode'].']';
	$signValue = md5($signStr);
    //error_log('$signStr=>'.$signStr.' $signValue=>'.$signValue.' $_POST_signValue=>'.$_POST['signValue']."\n", 3, "/var/www/rongerong/my-errors.log");
    if($_POST['signValue'] == $signValue){
        $result = accountClass::GetRecharge(array("nid"=>$_POST['merOrderNum']));
        if ($result==false){
            $msg = "支付失败";
        }elseif ($_POST['respCode']=="0000"){
            accountClass::OnlineReturn(array("trade_no"=>$_POST['merOrderNum']));
            $msg = "支付成功";
        } else {
            $msg = "支付失败";
        }
    }
	echo "RespCode=".$_POST['respCode']."|JumpURL=http://www.rongerong.com/?user&q=code/account/recharge";

}
/*elseif (isset($_REQUEST['ipsbillno']) && !empty($_REQUEST['ipsbillno'])){
	$billno = $_GET['billno'];
	$amount = $_GET['amount'];
	$mydate = $_GET['date'];
	$succ = $_GET['succ'];
	$msg = $_GET['msg'];
	$attach = $_GET['attach'];
	$ipsbillno = $_GET['ipsbillno'];
	$retEncodeType = $_GET['retencodetype'];
	$currency_type = $_GET['Currency_type'];
	$signature = $_GET['signature'];
	$content = $billno . $amount . $mydate . $succ . $ipsbillno . $currency_type;
	$result = accountpaymentClass::GetOne(array("nid"=>"ips"));
	$cert = $result['fields']['PrivateKey']['value'];
	$signature_1ocal = md5($content . $cert);
	
	if ($signature_1ocal == $signature){
	
		if ($succ == 'Y'){
			accountClass::OnlineReturn(array("trade_no"=>$billno));
			
			$msg = '交易成功';
		}else{
			$msg = '交易失败！';
		}
	}else{
		$msg = '签名不正确！';
	}
	echo "<script>alert('{$msg}');location.href='/index.php?user&q=code/account/recharge';</script>";
}*/