<?
/******************************
 * $File: borrow.tender.php
 * $Description: 投资类文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-08-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

require_once(ROOT_PATH."modules/borrow/borrow.class.php");

$MsgInfo["borrow_flag_nid_exiest"] = "标识名已经存在";
class borrowTenderClass
{
	/**
	 * 添加投标
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	public static function AddTender($data = array()){
		global $mysql,$_G;
		
		
		
	
		
		//第六步，判断是否已经过期【不可改】
		if ($borrow_result['verify_time']<time() - $borrow_result['borrow_valid_time']*60*60*24){
			
			return "tender_late_yes";
		}
		//第七步，判断金额是否正确【不可改】
		if(!is_numeric($data['account']) || $data['account']<0){
			return "tender_money_error";
		}
		
		
		//第八步，判断是否小于最小投资金额【可改】
		if($data['account']<$borrow_result['tender_account_min']){
			return "最小的投资金额不能小于{$borrow_result['tender_account_min']}。";
		}
		
		//支付密码不正确
		if (md5($data['paypassword'])!=$_G['user_result']['paypassword']){
			return "borrow_paypassword_error";
		}
		unset($data['paypassword']);
		
		//第九步,判断是否大于投资金额【可改】
		/*
		if($data['account']>$borrow_result['tender_account_max']){
			return "最大的投资金额不能大于".($borrow_result['tender_account_max'])."。";
		}
		*/
		$tender_account_all = borrowClass::GetUserTenderAccount(array("user_id"=>$data["user_id"],"borrow_nid"=>$data['borrow_nid']));
		if ($tender_account_all+$data['account']>$borrow_result['tender_account_max'] && $borrow_result['tender_account_max']>0){
			$tender_account = $borrow_result['tender_account_max']-$tender_account_all;
			return"您已经投标了{$tender_account_all},最大投标总金额不能大于{$borrow_result['tender_account_max']}，你最多还能投资{$tender_account}";
		}else{
			$data['account_tender'] = $data['account'];
			
			//判断投资的金额是否大于待借的金额
			if ($borrow_result['borrow_account_wait']<$data['account']){
				$data['account'] = $borrow_result['borrow_account_wait'];
			}
			//判断金额是否是一样的
			$account_result =  accountClass::GetAccountUsers(array("user_id"=>$data['user_id']));//获取当前用户的余额
			if ($account_result['balance']<$data['account']){
				return "tender_money_no";
			}
		}
		
		//第十步，如果是担保标，先判断担保是否已完成【可改】
		if($borrow_result['vouch_status']==1 && $borrow_result['vouch_account']!=$borrow_result['vouch_account_yes']){
			return "tender_vouch_full_no";
		}
		
		//第十一步，判断是否是友情借款【可改】
		if ($borrow_result['tender_friends']!=""){
			$_tender_friends = explode("|",$borrow_result['tender_friends']);
			$sql = "select username from {users} where user_id='{$data['user_id']}'";
			$result = $mysql->db_fetch_array($sql);
			if (!in_array($result['username'],$_tender_friends)){
				return "tender_friends_error";
			}
		}
		
		
		
		//添加投资的借款信息
		$sql = "insert into `{borrow_tender}` set `addtime` = '".time()."',`addip` = '".ip_address()."'";
		foreach($data as $key => $value){
			$sql .= ",`$key` = '$value'";
		}
		$mysql->db_query($sql);
		$tender_id = $mysql->db_insert_id();
		if ($tender_id>0){
			//1，扣除可用金额
			$borrow_url = "<a href=/invest/a{$data['borrow_nid']}.html target=_blank>{$borrow_result['name']}</a>";
			$log_info["user_id"] = $data["user_id"];//操作用户id
			$log_info["nid"] = "tender_frost_".$data['user_id']."_".time();
			$log_info["money"] = $data['account'];//操作金额
			$log_info["income"] = 0;//收入
			$log_info["expend"] = 0;//支出
			$log_info["balance_cash"] = 0;//可提现金额
			$log_info["balance_frost"] = -$data['account'];//不可提现金额
			$log_info["frost"] = $data['account'];//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["type"] = "tender";//类型
			$log_info["to_userid"] = $borrow_result['user_id'];//付给谁
			if ($data['auto_status']==1){
				$log_info["remark"] = "自动投标[{$borrow_url}]所冻结资金";//备注
			}else{
				$log_info["remark"] = "投标[{$borrow_url}]所冻结资金";//备注
			}
			accountClass::AddLog($log_info);
			
		
			//2，更新借款的信息
			$sql = "update  `{borrow}`  set borrow_account_yes=borrow_account_yes+{$data['account']},borrow_account_wait=borrow_account_wait-{$data['account']},borrow_account_scale=(borrow_account_yes/account)*100,tender_times=tender_times+1  where borrow_nid='{$data['borrow_nid']}'";
			$mysql->db_query($sql);//更新已经投标的钱
			
			//3，更新统计信息
			borrowClass::UpdateBorrowCount(array("user_id"=>$data['user_id'],"tender_times"=>1,"tender_account"=>$data['account'],"tender_frost_account"=>$data['account']));
		
		
			//4，提醒设置
			$borrow_url = "<a href=/invest/a{$borrow_result['borrow_nid']}.html target=_blank>{$borrow_result['name']}</a>";
			$remind['nid'] = "tender";
			$remind['code'] = "borrow";
			$remind['article_id'] = $tender_id;
			$remind['receive_userid'] = $data['user_id'];
			$remind['title'] = "成功投资{$borrow_result['name']}";
			$remind['content'] = "您成功投资了{$borrow_url}，请等待管理员审核";
			remindClass::sendRemind($remind);
		}
		$borrow=borrowClass::GetOne(array("borrow_nid"=>$data['borrow_nid']));
		if ($borrow['borrow_account_wait']==0 && $borrow['borrow_type']==4){
			$reverify['borrow_nid']=$data['borrow_nid'];
			$reverify['reverify_userid']=0;
			$reverify['reverify_remark']="自动审核";
			$reverify['status']=3;
			$rever=borrowClass::Reverify($reverify);
		}		
		return $tender_id;
	}
	
	
	/**
	 * 添加投标
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	public static function CheckTender($data = array()){
		global $mysql,$_G;
		//第一步，判断borrow_nid是否为空
		if (IsExiest($data['borrow_nid']) ==""){
			return "borrow_nid_empty";
		}
		
		//第二步，判断是否存在借款标
		$borrow_result = borrowClass::GetOne(array("borrow_nid"=>$data['borrow_nid']));
		if (!is_array($borrow_result)){
			return "borrow_not_exiest";
		}
		
		//第三步，判断账号是否锁定
		if ($_G['user_result']['islock']==1){
			return "borrow_user_lock";
		}
		
		//第四步，判断账号账号是否一致
		if (IsExiest($data['user_id']) ==""){
			return "borrow_user_id_empty";
		}
		
        //第五步，判断是否已经通过初审审核【不可改】
		if ($borrow_result['verify_time'] == "" || $borrow_result['status'] != 1){
			return "tender_verify_no";
		}
		
		//第六步，判断是否过期
		if ($borrow_result['verify_time'] <time() - $borrow_result['borrow_valid_time']*60*60*24){
		
			return "tender_late_yes";
		}
		
		
		return $borrow_result;
	}
}
?>
