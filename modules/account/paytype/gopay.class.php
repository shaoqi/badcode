<?php
require_once (ROOT_PATH."modules/account/payclasses/gopay/HttpClient.class.php");
class gopayPayment  {

    var $name = '国付宝';//支付宝（特别推荐！）
    var $logo = 'GOPAY';
    var $version = 2.1;
    var $description = "国付宝。";
    var $type = 1;//1->只能启动，2->可以添加
    var $charset = 'GB2312';
	
    var $orderby = 3;
 
    public static function ToSubmit($data){
    	$submitUrl = 'https://www.gopay.com.cn/PGServer/Trans/WebClientAction.do?'; //    
		$backgroundMerUrl = "http://www.rongerong.com/modules/account/return.php"; 
		$frontMerUrl = "";  
		$gopayServerTime= trim(HttpClient::getGopayServerTime());
		$ServerTime=str_ireplace(" ","",$gopayServerTime);
		$tranCode = 8888;
		$merchantID = $data["merchantID"];
		$VerficationCode = $data["VerficationCode"];
		$merOrderNum = $data["trade_no"];
		$tranAmt = $data["money"];
		$feeAmt = 0;
		$currencyType = 156;
		$merURL = $data["return_url"];
		$tranDateTime =  trim(date("YmdHis",time()));
		$virCardNoIn = $data["virCardNoIn"];
		$tranIP = ip_address();
		$msgExt = '';
		$bankCode = $data["bankCode"];
		$userType = 1;
		$url = $submitUrl;
		$url .= "version=2.1&";//交易代码
		$url .= "tranCode={$tranCode}&";//交易代码
		$url .= "language=1&";//交易代码
		$url .= "signType=1&";//交易代码
		$url .= "merchantID={$merchantID}&";//商户ID
		$url .= "virCardNoIn={$virCardNoIn}&";//国付宝转入账号
		$url .= "merOrderNum={$merOrderNum}&";//订单号
		$url .= "tranAmt={$tranAmt}&";//交易金额
		$url .= "feeAmt={$feeAmt}&";//手续费
		$url .= "currencyType={$currencyType}&";//币种，156 人民币
		$url .= "frontMerUrl={$frontMerUrl}&";//商户url
		$url .= "backgroundMerUrl={$backgroundMerUrl}&";//商户url
		$url .= "gopayServerTime={$gopayServerTime}&";//交易时间
		$url .= "tranDateTime={$tranDateTime}&";//交易时间
		$url .= "tranIP={$tranIP}&";//ip
		$url .= "bankCode={$bankCode}&";//商户url
		$url .= "userType={$userType}&";//商户url
$signStr="version=[2.1]tranCode=[$tranCode]merchantID=[$merchantID]merOrderNum=[$merOrderNum]tranAmt=[$tranAmt]feeAmt=[$feeAmt]tranDateTime=[{$tranDateTime}]frontMerUrl=[$frontMerUrl]backgroundMerUrl=[$backgroundMerUrl]orderId=[]gopayOutOrderId=[]tranIP=[$tranIP]respCode=[]gopayServerTime=[{$ServerTime}]VerficationCode=[{$VerficationCode}]";
		$signValue = md5($signStr);
		$url .= "signValue=$signValue";//商户url
		return array("url"=>$url,"sign"=>$signStr);
    }

   function GetFields(){
        return array(
                'merchantID'=>array(
                        'label'=>'商户ID',
                        'type'=>'string'
                ),
                'virCardNoIn'=>array(
                        'label'=>'国付宝帐号',
                        'type'=>'string'
                ),
                'VerficationCode'=>array(
                        'label'=>'识别码',
                        'type'=>'string'
                )
            );
    }
}
?>
