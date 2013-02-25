<?php

class offlinePayment {

    var $name = '线下支付';    //线下支付
    var $logo = 'pay_offline';
    var $version = 200080519;
    var $charset = 'gb2312';
 
    var $description = '线下支付';
	
    var $type = 2;
    var $orderby = 6;
    
    function getfields(){
        return array();
    }
}
?>
