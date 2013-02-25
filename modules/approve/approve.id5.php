<?php
//id5测试

class id5Class{
 
	
	/**
	 * 格式化数据
	 * @param string $type
	 * @param srring $data
	 * @return array
	 */
	function formatData($type, $data) {
		switch ($type) {
			case "realname" :
				$detailInfo = $data ['policeCheckInfos'] ['policeCheckInfo'];
				$birthDay = $this->getBirthDay ( $detailInfo ['identitycard'] );
				$sex = $this->getSex ( $detailInfo ['identitycard'] );
				$info = array (
						'name' => $detailInfo ['name'], 
						'identitycard' => $detailInfo ['identitycard'], 
						'sex' => $sex, 
						'compStatus' => $detailInfo ['compStatus'], 
						'compResult' => $detailInfo ['compResult'], 
						'policeadd' => $detailInfo ['policeadd'], 
						//'checkPhoto' => $detailInfo ['checkPhoto'], 
						'birthDay' => $birthDay, 
						'idcOriCt2' => $detailInfo ['idcOriCt2'], 
						'resultStatus' => $detailInfo ['compStatus'] );
			break;
			default :
				$info = array (false );
			break;
		}
		return $info;
	}
	
	/**
	 * 取得数据
	 * @param string $type	查询类型
	 * @param string $param	查询参数
	 * @return string
	 */
	function getData($type, $param) {
		global $_G;
		$wsdlURL = "http://gboss.id5.cn/services/QueryValidatorServices?wsdl";
		$Key = "12345678";
		$iv = "12345678";
		$DES = new DES ( $Key, $iv );
		try {
			$soap = new SoapClient ( $wsdlURL );
		} catch ( Exception $e ) {
			return "Linkerror";
		}
		
		
		//var_dump ( $soap->__getTypes () );
		//@todo 加密数据
		$partner = $DES->encrypt ( $_G['system']['con_id5_username'] );
		$partnerPW = $DES->encrypt (  $_G['system']['con_id5_password']);
		$type = $DES->encrypt ( $type );
		//先将中文转码
		
		//$param = mb_convert_encoding ( $param, "GBK", "UTF-8" );
		
		$param = $DES->encrypt ( $param );
		$params = array ("userName_" => $partner, "password_" => $partnerPW, "type_" => $type, "param_" => $param );
		//请求查询
		$data = $soap->querySingle ( $params );
		
		//@todo 解密数据
		$resultXML = $DES->decrypt ( $data->querySingleReturn );
		
		//$resultXML = mb_convert_encoding ( $resultXML, "UTF-8", "GBK" );
		return $resultXML;
	}
	
	//data = array("realname"=>"真实姓名"，"card_id"=>"身份证号"，"user_id"=>"用户的id"，"type"=>"类型")
	function CheckId5($data){
		global $mysql,$_G,$db_config;
		$data['card_id'] = trim($data['card_id']);
		$ids_fee_status = 0;
		if ($data['realname']=="" || $data["card_id"]==""){
			return -1;
		}
		$data['card_id'] = strtoupper($data['card_id']);
		
		if ($data['type']=="realname"){
			$card_type = "1A020201";
		}elseif ($data['type']=="edu"){
			$card_type = "1B010101";
		}
		$sql = "select * from `{approve_id5}` where realname='{$data['realname']}' and  card_id='{$data['card_id']}'  and type='{$data['type']}' and message_status>=0";
		$result = $mysql->db_fetch_array($sql);
		if ($result!=false){
			$contents = $result['contents'];
			$id = $result['id'];
		}else{
			
			$contents = self::getData($card_type, "{$data['realname']},{$data['card_id']}") ;
			$sql = "insert into `{approve_id5}` set user_id='{$data['user_id']}',`addtime` = '".time()."',`addip` = '".ip_address()."',contents='{$contents}',realname='{$data['realname']}',card_id='{$data['card_id']}',type='{$data['type']}',card_type='{$card_type}'";
			$mysql->db_query($sql);
			$id = $mysql->db_insert_id();
			
		}
		
		$contents = str_replace('<?xml version="1.0" encoding="UTF-8"?>',"", $contents); 
		
		//$content = mb_convert_encoding ( $content, "GBK" , "UTF-8");
		$result = xmltoarray($contents);
		
		$status = $result['message']['status'];
		$data['message_status'] = $status;
		$data['value'] = $result['message']['value'];
		$data['idname'] =  $data['realname'];
		$data['identitycard'] =   $result['identitycard'];
		$data['compstatus'] =  $result['compStatus'];
		$data['checkphoto'] =  $result['checkPhoto'];
		$data['status'] = $data['compstatus'];
		$data['type'] = "realname";
		$data['user_id'] = $data['user_id'];
		$sql = "update `{approve_id5}` set `addip` = '".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
		$sql .= " where id={$id}";
		$mysql->db_query($sql);
		
		
		if ($data['status']  == 3){
			$temp = base64_decode($data['checkphoto']); 
			file_put_contents("data/idcard/".md5($data['user_id'].$db_config['partnerId']."dyp2pcardid").".jpg", $temp);
		}
		return $data['status'];
	}
	
	
	
	//data = array("realname"=>"真实姓名"，"card_id"=>"身份证号"，"user_id"=>"用户的id"，"type"=>"类型")
	function CheckId5Edu($data){
		global $mysql,$_G,$db_config;
		$data['card_id'] = trim($data['card_id']);
		$ids_fee_status = 0;
		if ($data['realname']=="" || $data["card_id"]==""){
			return -1;
		}
		$data['card_id'] = strtoupper($data['card_id']);
		
		if ($data['type']=="edu"){
			$card_type = "1B010101";
		}
		$sql = "select * from `{approve_edu_id5}` where realname='{$data['realname']}' and  card_id='{$data['card_id']}'  and type='{$data['type']}' and message_status>=0";
		$result = $mysql->db_fetch_array($sql);
		
		if ($result!=false){
			$contents = $result['contents'];
			$id = $result['id'];
		}else{
			
			$contents = self::getData($card_type, "{$data['realname']},{$data['card_id']}") ;
			$sql = "insert into `{approve_edu_id5}` set user_id='{$data['user_id']}',`addtime` = '".time()."',`addip` = '".ip_address()."',contents='{$contents}',realname='{$data['realname']}',card_id='{$data['card_id']}',type='{$data['type']}',card_type='{$card_type}'";
			$mysql->db_query($sql);
			$id = $mysql->db_insert_id();
			
		}
		$contents = str_replace('<?xml version="1.0" encoding="UTF-8"?>',"", $contents); 
		
		//$content = mb_convert_encoding ( $content, "GBK" , "UTF-8");
		$result = xmltoarray($contents);
		
		if ($result['message']['value']=="未查到数据"){
			$result['message']['status'] = 1;
		}
		elseif ($result['message']['value']=="查询成功"){
			$result['message']['status'] = 0;
		}
		$status = $result['message']['status'];
		$data['message_status'] = $status;
		$data['value'] = $result['message']['value'];
		$_data['graduate'] = $data['graduate'] =  $result['graduate'];
		$_data['speciality'] = $data['speciality'] =   $result['specialityName'];
		$_data['degree'] = $data['degree'] =  $result['educationDegree'];
		$_data['enrol_date'] = $data['enrol_date'] =  $result['enrolDate'];
		$_data['graduate_date'] = $data['graduate_date'] =  $result['graduateTime'];
		$data['result'] =  $result['studyResult'];
		$data['style'] =  $result['studyStyle'];
		$data['photo'] =  $result['photo'];
		if ($status==0){
			$data['status'] = 3;
		}elseif ($status==1){
			$data['status'] = 2;
		}else{
			$data['status'] = 0;
		}
		$data['type'] = "edu";
		$data['user_id'] = $data['user_id'];
		$sql = "update `{approve_edu_id5}` set `addip` = '".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
		$sql .= " where id={$id}";
		$mysql->db_query($sql);
		
		if ($data['status']  == 3){
			$temp = base64_decode($data['photo']); 
			file_put_contents("data/idcard/".md5($data['user_id'].$db_config['partnerId']."dyp2peducation").".jpg", $temp);
			$sql = "update `{approve_edu}` set user_id='{$data['user_id']}'";
			foreach($_data as $key => $value){
				$sql .= ",`$key` = '$value'";
			}
			$sql .= " where user_id={$data['user_id']}";
			$mysql->db_query($sql);
		}
		return $data['status'];
	}
}

class DES {
	var $key;
	var $iv; //偏移量
	
	function DES($key, $iv = 0) {
		$this->key = $key;
		if ($iv == 0) {
			$this->iv = $key;
		} else {
			$this->iv = $iv;
		}
	}
	
	//加密
	function encrypt($str) {
		$size = mcrypt_get_block_size ( MCRYPT_DES, MCRYPT_MODE_CBC );
		
		$str = $this->pkcs5Pad ( $str, $size );
		
		$data = mcrypt_cbc ( MCRYPT_DES, $this->key, $str, MCRYPT_ENCRYPT, $this->iv );
		//$data=strtoupper(bin2hex($data)); //返回大写十六进制字符串
		return base64_encode ( $data );
	}
	
	//解密
	function decrypt($str) {
		$str = base64_decode ( $str );
		//$strBin = $this->hex2bin( strtolower($str));
		$str = mcrypt_cbc ( MCRYPT_DES, $this->key, $str, MCRYPT_DECRYPT, $this->iv );
		$str = $this->pkcs5Unpad ( $str );
		return $str;
	}
	
	function hex2bin($hexData) {
		$binData = "";
		for($i = 0; $i < strlen ( $hexData ); $i += 2) {
			$binData .= chr ( hexdec ( substr ( $hexData, $i, 2 ) ) );
		}
		return $binData;
	}
	
	function pkcs5Pad($text, $blocksize) {
		$pad = $blocksize - (strlen ( $text ) % $blocksize);
		return $text . str_repeat ( chr ( $pad ), $pad );
	}
	
	function pkcs5Unpad($text) {
		$pad = ord ( $text {strlen ( $text ) - 1} );
		if ($pad > strlen ( $text ))
			return false;
		if (strspn ( $text, chr ( $pad ), strlen ( $text ) - $pad ) != $pad)
			return false;
		return substr ( $text, 0, - 1 * $pad );
	}
}
?>