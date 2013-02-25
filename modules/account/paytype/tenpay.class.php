<?php

class tenpayPayment  {

    var $name = '财付通';//支付宝（特别推荐！）
    var $logo = 'TENPAY';
    var $version = 20070902;
    var $description = "腾讯财付通。";
    var $type = 1;//1->只能启动，2->可以添加
    var $charset = 'GB2312';
	
    var $submitUrl = 'http://service.tenpay.com/cgi-bin/v3.0/payservice.cgi'; //  
    var $orderby = 3;
 
    public static function ToSubmit($data){
		require_once ("modules/account/payclasses/tenpay/PayRequestHandler.class.php");
		/* 商户号 */
		$data["money"] =$data["money"]*100;
		$bargainor_id = $data['member_id'];
		
		/* 密钥 */
		$key = $data['PrivateKey'];
		$cmdno = 2;
		/* 返回处理地址 */
		$return_url = $data['return_url'];
		
		//date_default_timezone_set(PRC);
		$strDate = date("Ymd");
		$strTime = date("His");
		
		//4位随机数
		$randNum = rand(1000, 9999);
		
		//10位序列号,可以自行调整。
		$strReq = $strTime . $randNum;
		
		/* 商家订单号,长度若超过32位，取前32位。财付通只记录商家订单号，不保证唯一。 */
		$sp_billno = $data['trade_no'];
		
		/* 财付通交易单号，规则为：10位商户号+8位时间（YYYYmmdd)+10位流水号 */
		$transaction_id = $bargainor_id . $strDate . $strReq;
		
		/* 商品价格（包含运费），以分为单位 */
		$total_fee = (int)$data['money'] ;
		
		/* 商品名称 */
		$desc = $data['subject'];
		
		/* 创建支付请求对象 */
		$reqHandler = new PayRequestHandler();
		$reqHandler->init();
		$reqHandler->setKey($key);
		
		//----------------------------------------
		//设置支付参数
		//----------------------------------------
		$reqHandler->setParameter("bargainor_id", $bargainor_id);			//商户号
		$reqHandler->setParameter("sp_billno", $sp_billno);					//商户订单号
		$reqHandler->setParameter("transaction_id", $transaction_id);		//财付通交易单号
		$reqHandler->setParameter("total_fee", $total_fee);					//商品总金额,以分为单位
		$reqHandler->setParameter("return_url", $return_url);				//返回处理地址
		$reqHandler->setParameter("desc", $data['body']);	//商品名称
		
		//用户ip,测试环境时不要加这个ip参数，正式环境再加此参数
		$reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);
		
		//请求的URL
		$reqUrl = $reqHandler->getRequestURL();
		return $reqUrl;
    }

   function GetFields(){
        return array(
                'member_id'=>array(
                        'label'=>'客户号',
                        'type'=>'string'
                ),
                'PrivateKey'=>array(
                        'label'=>'私钥',
                        'type'=>'string'
                ),
                'authtype'=>array(
                    'label'=>'商家支付模式',
                    'type'=>'select',
                    'options'=>array('0'=>'套餐包量商家','1'=>'单笔支付商家')
                )
            );
    }
}
?>
