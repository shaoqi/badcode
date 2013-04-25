<?php
/******************************
 * $File: borrow.remind.php
 * $Description: 借款提醒
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

require_once(ROOT_PATH."core/config.inc.php");
require_once(ROOT_PATH."modules/remind/remind.class.php");
require_once(ROOT_PATH."modules/sms/sms.class.php");
class borrowRemindClass  {
	
	function RepayLate($data){
		global $mysql;
		$sql ="select p1.*,p2.phone,p2.email,p3.borrow_name from {borrow_repay} as p1,{borrow} as p3,{user} as p2 where p1.borrow_nid = p3.borrow_nid and p2.user_id=p1.user_id and p1.repay_status=1 and p1.repay_time<".time()."";
		$result= $mysql->db_fetch_arrays($sql);
		foreach ($result as $key => $value){
			$content = "友情提醒：你的借款（标题）快逾期了请不要忘记哟,提前还款将有奖励";
			$resms['user_id'] = $value['user_id'];
			$resms['phone'] = $value['phone'];
			$resms['content'] =  $content;
			$resms['type'] =  "repay_late";
			smsClass::SendSMS($resms);
			
			$relog['user_id'] = = $value['user_id'];
			$relog['type'] = "sms";
			$relog['style'] = "repay_late";
			$relog['content']= $content;
			$relog['contract'] = $value['phone'];
			$relog['status'] = 0;
			remindClass::AddLog($relog);
		}
	}

}



?>