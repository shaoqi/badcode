<?php

class alipayPayment {

    var $name = '支付宝';//支付宝（特别推荐！）
    var $logo = 'ALIPAYTRAD';
    var $version = 20070902;
    var $description = "支付宝即时到帐，是国内先进的网上支付方式。";
    var $type = 1;//1->只能启动，2->可以添加
    var $charset = 'gbk';
	
    //var $applyUrl = 'https://www.alipay.com/himalayas/practicality_profile_edit.htm';//'https://www.alipay.com/himalayas/market.htm';
   
    var $submitUrl = 'https://www.alipay.com/cooperate/gateway.do?_input_charset=gbk'; //  
    var $submitButton = 'http://img.alipay.com/pimg/button_alipaybutton_o_a.gif'; ##需要完善的地方
    var $orderby = 3;
   // var $applyProp = array("postmethod"=>"GET","type"=>"from_agent_contract","id"=>"C4335304346520951111");
    //var $applyProp = array("postmethod"=>"GET","market_type"=>"from_agent_contract","customer_external_id"=>'C433530444855584111X');
	
    function pay_alipay(&$system){
        //parent::paymentPlugin($system);
        $regIp=isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:$_SERVER['HTTP_HOST'];
        $this->intro='';
    }
	function ParaFilter($parameter) {
		$para = array();
		while (list ($key, $val) = each ($parameter)) {
			if($key == "sign" || $key == "sign_type" || $val == "")continue;
			else	$para[$key] = $parameter[$key];
		}
		return $para;
	}
	function Service($parameter,$security_code,$sign_type) {
        $gateway	      = "https://www.alipay.com/cooperate/gateway.do?";
        $security_code  = $security_code;
        $_parameter      = self::ParaFilter($parameter);
		
        //设定_input_charset的值,为空值的情况下默认为GBK
        if($parameter['_input_charset'] == '')
            $_parameter['_input_charset'] = 'GBK';

        $_input_charset   = $_parameter['_input_charset'];

        //得到从字母a到z排序后的加密参数数组
		ksort($_parameter);
 	 	reset($_parameter);
        $parame  = "";
		while (list ($key, $val) = each ($_parameter)) {
			$parame .= $key."=".$val."&";
		}
		$parame = substr($parame,0,count($parame)-2);		     //去掉最后一个&字符
		$parame = $parame.$security_code;				//把拼接后的字符串再与安全校验码直接连接起来
		
   		$mysign = self::Sign($parame,$sign_type);			    //把最终的字符串加密，获得签名结果
		
		$url  = $gateway;
		$arg = "";
		foreach($_parameter as $k=>$v)      {
            $arg .= "&{$k}={$v}";
        }
        $arg = substr($arg,1);		
	
		//把网关地址、已经拼接好的参数数组字符串、签名结果、签名类型，拼接成最终完整请求url
        $url .= $arg."&sign=" .$mysign ."&sign_type=".$sign_type;
        return  $url;
    }
	/**加密字符串
	*$prestr 需要加密的字符串
	*return 加密结果
	 */
	function Sign($prestr,$sign_type) {
		$sign='';
		if($sign_type == 'MD5') {
			$sign = md5($prestr);
		}elseif($sign_type =='DSA') {
			//DSA 签名方法待后续开发
			die("DSA 签名方法待后续开发，请先使用MD5签名方式");
		}else {
			die("支付宝暂不支持".$sign_type."类型的签名方式");
		}
		return $sign;
	}
    public static function ToSubmit($data){
		
		if (!isset($data['alipay_id'])) return -1;
		$transport       = "http";   //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
       
		$show_url        = "http://www.alipay.com";	
        $webname =   $data['webname'];//收款方名称，如：公司名称、网站名称、收款人姓名等
		
		
		$money = number_format($data['money'],2,".","");;
		$subject =  $data['subject']; //订单名称
        $subject = str_replace("'",'`',trim($subject));
        $subject = str_replace('"','`',$subject);
		
		$parameter['payment_type'] = 1;
		$parameter['partner'] = $data['alipay_id'];
		$parameter['seller_email'] = $data['alipay_email'];
		$parameter['notify_url'] = $data['notify_url'];//交易过程中服务器通知的页面
        $parameter['return_url'] = $data['return_url'];//返回地址
		$parameter['_input_charset']  = "GBK";
		$parameter['show_url']        = "";	
		
		 //从订单数据中动态获取到的必填参数
        $parameter["out_trade_no"]    = $data['trade_no'];;
        $parameter["subject"]         = $data['subject'];
        $parameter["body"]            = $data['body'];//订单描述、
        $parameter["total_fee"]       = $data['money'];
		
		$parameter['paymethod']    = "directPay";//默认支付方式，四个值可选：bankPay(网银); cartoon(卡通); directPay(余额); CASH
		$parameter["anti_phishing_key"]= "";
		$parameter["exter_invoke_ip"]  = "";
		$parameter["buyer_email"]	   = "";
        $parameter["extra_common_param"] ="";//自定义参数，可存放任何内容
		$real_method =  empty($data['real_method'])?1:$data['real_method'];
        switch ($real_method){
            case '0': 
                $parameter['service'] = 'trade_create_by_buyer';
                break;
            case '1':
                $parameter['service'] = 'create_direct_pay_by_user';
                break;
            case '2':
                $parameter['service'] = 'create_partner_trade_by_buyer';
                break;
        }

		//构造请求函数
		$url = self::Service($parameter,$data['alipay_key'],"MD5");
		return $url;
		
    }

    function callback($in,&$paymentId,&$money,&$message,&$tradeno){
        $merId = $this->getConf($in['out_trade_no'],'member_id'); //帐号
        $pKey = $this->getConf($in['out_trade_no'],'PrivateKey');
        $key = $pKey==''?'afsvq2mqwc7j0i69uzvukqexrzd0jq6h':$pKey;//私钥值
        ksort($in);
        //检测参数合法性
        $temp = array();
        foreach($in as $k=>$v){
            if($k!='sign'&&$k!='sign_type'){
                $temp[] = $k.'='.$v;
            }
        }
        $testStr = implode('&',$temp).$key;
        if($in['sign']==md5($testStr)){
            $paymentId = $in['out_trade_no'];    //支付单号
            $money = $in['total_fee'];
            $message = $in['body'];
            $tradeno = $in['trade_no'];
            switch($in['trade_status']){
                case 'TRADE_FINISHED':
                    if($in['is_success']=='T'){                        
                        return PAY_SUCCESS;
                    }else{                        
                        return PAY_FAILED;
                    }
                    break;
                case 'TRADE_SUCCESS':
                    if($in['is_success']=='T'){                        
                        return PAY_SUCCESS;
                    }else{                        
                        return PAY_FAILED;
                    }
                    break;
                case 'WAIT_SELLER_SEND_GOODS':
                    if($in['is_success']=='T'){                        
                        return PAY_PROGRESS;
                    }else{                        
                        return PAY_FAILED;
                    }
                    break;
                case 'TRADE_SUCCES':    //高级用户
                    if($in['is_success']=='T'){
                        return PAY_SUCCESS;
                    }else{
                        return PAY_FAILED;
                    }
                    break;
            }

        }else{
            $message = 'Invalid Sign';            
            return PAY_ERROR;
        }
    }

    function serverCallback($in,&$paymentId,&$money,&$message){
        exit('reserved');
    }

    function applyForm($agentfield){
      $tmp_form='<a href="javascript:void(0)" onclick="document.applyForm.submit();">立即申请支付宝</a>';
      $tmp_form.="<form name='applyForm' method='".$agentfield['postmethod']."' target='_blank'>";
      foreach($agentfield as $key => $val){
            $tmp_form.="<input type='hidden' name='".$key."' value='".$val."'>";
      }
      $tmp_form.="</form>";
      return $tmp_form;
    }

    function GetFields(){
        return array(
                'alipay_id'=>array(
                        'label'=>'合作者身份(parterID)',
                        'type'=>'string'
                    ),
                'alipay_key'=>array(
                        'label'=>'交易安全校验码(key)',
                        'type'=>'string'
                ),
                'alipay_email'=>array(
                        'label'=>'支付宝账号',
                        'type'=>'string'
                ),
                'alipay_type'=>array(
                        'label'=>'选择接口类型',
                        'type'=>'select',
                        'options'=>array('0'=>'使用标准双接口','2'=>'使用担保交易接口','1'=>'使用即时到帐交易接口')
                ),
   
            );
    }
}
?>
