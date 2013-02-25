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
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

$MsgInfo["tender_borrow_nid_empty"] = "您的操作有误。【error:tender_borrow_nid_empty】";
$MsgInfo["tender_borrow_not_exiest"] = "您的操作有误。【error:tender_borrow_not_exiest】";
$MsgInfo["tender_user_lock"] = "您的账号被锁定，请跟管理员联系。";
$MsgInfo["tender_late_yes"] = "此标已过期";
$MsgInfo["tender_full_yes"] = "此标已满标";
$MsgInfo["tender_self_yes"] = "不能投资自己的标";
$MsgInfo["tender_verify_no"] = "您的操作有误。【error:tender_verify_no】";
$MsgInfo["tender_money_error"] = "投资金额不正确";
$MsgInfo["borrow_paypassword_error"] = "支付密码不正确";
$MsgInfo["tender_borrowtype_error"] = "您的操作有误。【error:tender_borrowtype_error】";
$MsgInfo["borrow_password_error"] = "借款密码不正确";
$MsgInfo["borrow_tender_valicode_error"] = "验证码不正确";

require_once(DEAYOU_PATH."modules/account/account.class.php");
require_once(ROOT_PATH."modules/users/users.class.php");
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
        
		$borrow_result = self::CheckTender(array("borrow_nid"=>$data['borrow_nid'],"user_id"=>$data['user_id']));
        if (!is_array($borrow_result)){
            return $borrow_result;
        }
		
		//判断金额是否正确【不可改】
		if(!is_numeric($data['account']) || $data['account']<0){
			return "tender_money_error";
		}
		
		//判断是否小于最小投资金额【可改】
		if($data['account']<$borrow_result['tender_account_min']){
			return "最小的投资金额不能小于{$borrow_result['tender_account_min']}。";
		}
		
		//支付密码不正确
		if ($data['auto_status']!=1){
			if (md5($data['paypassword'])!=$_G['user_result']['paypassword']){
				return "borrow_paypassword_error";
			}
			//验证码
			if ($data['valicode']!=$_SESSION["valicode"]){
				return "borrow_tender_valicode_error";
			}
		}
		unset($data['valicode']);
		unset($data['auto_status']);
		unset($data['paypassword']);
		$_SESSION["valicode"] = "";
         //判断类型是否正确
        if ($borrow_result['borrow_password']!="" && md5($data["borrow_password"])!=$borrow_result['borrow_password']){
            return "borrow_password_error";
        }
		unset($data['borrow_password']);
		
        
        
		//判断是否大于投资金额【可改】
		$tender_account_all = self::GetUserTenderAccount(array("user_id"=>$data["user_id"],"borrow_nid"=>$data['borrow_nid']));
        if ($data['account']>$borrow_result['tender_account_max'] && $borrow_result['tender_account_max']>0 ){
			return "此标最大投标金额不能大于{$borrow_result['tender_account_max']}。";
        }elseif ($tender_account_all+$data['account']>$borrow_result['tender_account_max'] && $borrow_result['tender_account_max']>0){
			$tender_account = $borrow_result['tender_account_max']-$tender_account_all;
			return "您已经投标了{$tender_account_all},最大投标总金额不能大于{$borrow_result['tender_account_max']}，你最多还能投资{$tender_account}";
		}else{
			$data['account_tender'] = $data['account'];
			
			//判断投资的金额是否大于待借的金额
			if ($borrow_result['borrow_account_wait']<$data['account']){
				$data['account'] = $borrow_result['borrow_account_wait'];
			}
            
            
			//判断可用金额是否足够投资
			$account_result =  accountClass::GetAccountUsers(array("user_id"=>$data['user_id']));//获取当前用户的余额
			if ($account_result['balance']<$data['account']){
				return "tender_money_no";
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
            $log_info["account_web_status"] = 0;//
            $log_info["account_user_status"] = 0;//
			$log_info["nid"] = "tender_frost_".$data['user_id']."_".$data['borrow_nid']."_".$tender_id;
			$log_info["borrow_nid"] = $data['borrow_nid'];//收入
			$log_info["code"] = "borrow";//
			$log_info["code_type"] = "tender";//
			$log_info["code_nid"] = $tender_id;//
			$log_info["money"] = $data['account'];//操作金额
			$log_info["income"] = 0;//收入
			$log_info["expend"] = 0;//支出
			$log_info["balance_cash"] = 0;//可提现金额
			$log_info["balance_frost"] = -$data['account'];//不可提现金额
			$log_info["frost"] = $data['account'];//冻结金额
			$log_info["await"] = 0;//待收金额
			$log_info["repay"] = 0;//待还金额
			$log_info["type"] = "tender";//类型
			$log_info["to_userid"] = $borrow_result['user_id'];//付给谁
			if ($data['auto_status']==1){
				$log_info["remark"] = "自动投标[{$borrow_url}]所冻结资金";//备注
			}else{
				$log_info["remark"] = "投标[{$borrow_url}]所冻结资金";//备注
			}
			$result = accountClass::AddLog($log_info);
			//2，更新借款的信息
			$sql = "update  `{borrow}`  set borrow_account_yes=borrow_account_yes+{$data['account']},borrow_account_wait=borrow_account_wait-{$data['account']},borrow_account_scale=(borrow_account_yes/account)*100,tender_times=tender_times+1  where borrow_nid='{$data['borrow_nid']}'";
			$mysql->db_query($sql);//更新已经投标的钱
			
			//3，更新统计信息
			borrowCountClass::UpdateBorrowCount(array("user_id"=>$data['user_id'],"borrow_nid"=>$data['borrow_nid'],"nid"=>"tender_frost_".$data['user_id']."_".$data['borrow_nid']."_".$tender_id,"tender_times"=>1,"tender_account"=>$data['account'],"tender_frost_account"=>$data['account']));
		
		
			//4，提醒设置 投资人
			$borrow_url = "<a href={$_G['web_domain']}/invest/a{$borrow_result['borrow_nid']}.html target=_blank>{$borrow_result['name']}</a>";
			$remind['nid'] = "tender"; 
            $remind['remind_nid'] =  "tender_".$data['user_id']."_".$tender_id;
			$remind['code'] = "invest";
			$remind['article_id'] = $tender_id;
			$remind['receive_userid'] = $data['user_id'];
			$remind['title'] = "成功投资[{$borrow_result['name']}]";
			$remind['content'] = "您成功投资了{$borrow_url}，请等待管理员审核";
			remindClass::sendRemind($remind);
			
			//5，提醒设置 借款人
			$borrow_url = "<a href={$_G['web_domain']}/invest/a{$borrow_result['borrow_nid']}.html target=_blank>{$borrow_result['name']}</a>";
			$remind['nid'] = "borrow_tender";
            $remind['remind_nid'] =  "borrow_tender_".$borrow_result['user_id']."_".$tender_id;
			$remind['code'] = "borrow";
			$remind['article_id'] = $data["user_id"];
			$remind['receive_userid'] = $borrow_result['user_id'];
			$remind['title'] = "借款标[{$borrow_result['name']}]有人投资";
			$remind['content'] = "您的借款标{$borrow_url}有人投资。";
			remindClass::sendRemind($remind);
			
			
		}
		return $tender_id;
	}
	
	//获取用户的总投资额，可以是全部的，也可以单独的某个标
	function GetUserTenderAccount($data){
		global $mysql;
		$_sql = " where 1=1 ";
		if (IsExiest($data['user_id'])!=""){
			$_sql .= " and user_id='{$data['user_id']}' ";
		}
		if (IsExiest($data['borrow_nid'])!=""){
			$_sql .= " and borrow_nid='{$data['borrow_nid']}' ";
		}
		$sql = "select sum(account) as account_all from `{borrow_tender}` {$_sql}";
       
		$result = $mysql->db_fetch_array($sql);
		if ($result!=fasle ) {
			return $result["account_all"];
		}
		return 0;
	}
    
    
	/**
	 * 检查是否可以投资
	 *
	 * @param Array $data
	 * @return Boolen
	 */
	public static function CheckTender($data = array()){
		global $mysql,$_G;
		//第一步，判断borrow_nid是否为空
		if (IsExiest($data['borrow_nid']) ==""){
			return "tender_borrow_nid_empty";
		}
		
		//第二步，判断是否存在借款标
		$borrow_result = borrowClass::GetView(array("borrow_nid"=>$data['borrow_nid']));
		if (!is_array($borrow_result)){
			return "tender_borrow_not_exiest";
		}
		
		//第三步，判断账号是否锁定
		if ($_G['user_result']['islock']==1){
			return "tender_user_lock";
		}
		
		//第五步，判断是否已经通过初审审核【不可改】
		if ($borrow_result['verify_time'] == "" || $borrow_result['status'] != "1"){
			return "tender_verify_no";
		}
		
		//第六步，判断是否过期
		if ($borrow_result['verify_time'] <time() - $borrow_result['borrow_valid_time']*60*60*24){
			return "tender_late_yes";
		}
		
		//判断是否满标
		if ($borrow_result['account'] <=$borrow_result['borrow_account_yes']){
			return "tender_full_yes";
		}
        
		//借款人不能自己投资
		if ($borrow_result['user_id'] == $data['user_id']){
			return "tender_self_yes";
		}
        
		//第六步，判断是否已经过期【不可改】
		if ($borrow_result['verify_time']<time() - $borrow_result['borrow_valid_time']*60*60*24){
			
			return "tender_late_yes";
		}
		return $borrow_result;
	}
    
    
    
	/**
	 * 投资列表
	 *
	 * @return Array
	 */
	function GetTenderList($data = array()){
		global $mysql;
		
		$_sql = "where 1=1 ";	
		
		//判断用户id
		if (IsExiest($data['user_id']) != false){
			if ($data['change_show']==1){
				$_sql .= " and (p1.change_status=1 and p1.change_userid={$data['user_id']}) or (p1.change_status!=1 and p1.user_id={$data['user_id']})";
			}else{
				$_sql .=" and p1.user_id={$data['user_id']}" ;
			}
		}
		
		//判断借款用户
		if (IsExiest($data['borrow_userid']) != false){
			$_sql .= " and p3.user_id = {$data['borrow_userid']}";
		}
		
		//搜到用户名
		if (IsExiest($data['username']) != false){
			$_sql .= " and p2.username like '%{$data['username']}%'";
		}
		
		//搜索借款名称
		if (IsExiest($data['borrow_status']) != false){
			$_sql .= " and p3.`status` in ({$data['borrow_status']})";
		}
		
		if ($data['change_status']!=""){
			$_sql .= " and p1.`change_status` in  ({$data['change_status']})";
		}
		//搜索借款名称
		if (IsExiest($data['borrow_name']) != false){
			$_sql .= " and p3.`name` like '%".urldecode($data['borrow_name'])."%'";
		}
		//搜索借款名称
		if (IsExiest($data['borrow_nid']) != false){
			$_sql .= " and p3.`borrow_nid` = '{$data['borrow_nid']}'";
		}
		
		//投资类型
		if (IsExiest($data['tender_type']) != false){
            if ($data['tender_type']=="wait"){
                $_sql .= " and p3.`status` = 3 and p3.repay_full_status=0";
            }elseif ($data['tender_type']=="over"){
                 $_sql .= " and p3.`status` = 3 and p1.account=p1.recover_account_capital_yes";
            }
			
		}
		
		if (IsExiest($data['keywords'])!=""){
			$_sql .= " and (p3.name like '%".urldecode($data['keywords'])."%') ";
		}
        
		//判断添加时间开始
		if (IsExiest($data['dotime1']) != false){
			$dotime1 = ($data['dotime1']=="request")?$_REQUEST['dotime1']:$data['dotime1'];
			if ($dotime1!=""){
				$_sql .= " and p1.addtime > ".get_mktime($dotime1);
			}
		}
		
		//判断添加时间结束
		if (IsExiest($data['dotime2'])!=false){
			$dotime2 = ($data['dotime2']=="request")?$_REQUEST['dotime2']:$data['dotime2'];
			if ($dotime2!=""){
				$_sql .= " and p1.addtime < ".get_mktime($dotime2);
			}
		}
		
		//判断借款状态
		if (IsExiest($data['status'])!=""){
			$_sql .= " and p1.status in ({$data['status']})";
		}
		//判断是否担保借款
		if (IsExiest($data['vouch_status'])!=""){
			$_sql .= " and p3.vouch_status in ({$data['vouch_status']})";
		}
		
		//借款期数
		if (IsExiest($data['borrow_period'])!=""){
			$_sql .= " and p3.borrow_period = {$data['borrow_period']}";
		}
		
		//借款类别
		if (IsExiest($data['flag'])!=""){
			$_sql .= " and p3.flag = {$data['flag']}";
		}
		
		//借款用途
		if (IsExiest($data['borrow_use']) !=""){
			$_sql .= " and p3.borrow_use in ({$data['borrow_use']})";
		}
		
		//借款用户类型
		if (IsExiest($data['borrow_usertype']) !=""){
			$_sql .= " and p3.borrow_usertype = '{$data['borrow_usertype']}'";
		}
		
		
		//借款
		if (IsExiest($data['borrow_style']) ){
			$_sql .= " and p3.borrow_style in ({$data['borrow_style']})";
		}
		
		//判断流转标是否已经被回收
		if (IsExiest($data['roam_status']) ){			
			$_sql .= " and ((p3.borrow_type='roam' and p1.recover_account_all !=  p1.recover_account_yes) or p3.borrow_type='pawn' ) ";
		}
		
		//金额权限
		if (IsExiest($data['account1'])!=""){
			$_sql .= " and p1.account >= {$data['account1']}";
		}
		if (IsExiest($data['account2'])!=""){
			$_sql .= " and p1.account <= {$data['account2']}";
		}
		//快速查找
		if (IsExiest($data['dodate'])!=""){
			if($data['dodate']=='oneweek'){
				$dodate = time()-7*24*60*60; 				
			}elseif($data['dodate']=='twoweek'){
				$dodate = time()-2*7*24*60*60; 				
			}elseif($data['dodate']=='onemonth'){
				$dodate = time()-30*24*60*60; 				
			}
			$_sql .= " and p1.addtime >= {$dodate} and p1.addtime <= ".time();
		}
		//排序
		$_order = " order by p1.id desc ";
		
		$_select = " p1.*,p2.username,
        p3.name as borrow_name,p3.account as borrow_account,p3.borrow_type,
        p4.username as borrow_username,p3.repay_account_wait as borrow_account_wait_all,
        p3.repay_account_interest_wait as borrow_interest_wait_all,p4.user_id as borrow_userid,p3.borrow_apr,p3.borrow_period,p3.borrow_account_scale,p5.credits,p7.name as borrow_type_name,p3.verify_time as borrow_verify_time";
		$sql = "select SELECT from `{borrow_tender}` as p1 
				 left join `{users}` as p2 on p1.user_id=p2.user_id
				 left join `{borrow}` as p3 on p1.borrow_nid=p3.borrow_nid
				 left join `{borrow_type}` as p7 on p7.nid=p3.borrow_type
				 left join `{users}` as p4 on p4.user_id=p3.user_id
				 left join `{credit}` as p5 on p5.user_id=p3.user_id
				 SQL ORDER LIMIT
				";
		//是否显示全部的信息
		if (IsExiest($data['limit'])!=false){
			if ($data['limit'] != "all"){ $_limit = "  limit ".$data['limit']; }
			$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array($_select, $_sql, $_order, $_limit), $sql));
            foreach ($list as $key => $value){
                $period_name = "个月";
                if ($value["borrow_type"]=="day"){
                    $period_name = "天";
                }
                $list[$key]["borrow_period_name"] =$value["borrow_period"].$period_name;
                if ($value['borrow_type']=="roam"){
                    $list[$key]['repay_last_time'] = strtotime("{$value["borrow_period"]} month",$value['verify_time']);
					
                }                                                 
            }
            return $list;                                            
        }			 
		
		//判断总的条数
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'SQL', 'ORDER', 'LIMIT'), array('count(1) as num', $_sql,'', ''), $sql));
		$total = intval($row['num']);
		
		//分页返回结果
		$data['page'] = !IsExiest($data['page'])?1:$data['page'];
		$data['epage'] = !IsExiest($data['epage'])?10:$data['epage'];
		
		$total_page = ceil($total / $data['epage']);
		$_limit = " limit ".($data['epage'] * ($data['page'] - 1)).", {$data['epage']}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'SQL','ORDER', 'LIMIT'), array($_select,$_sql,$_order, $_limit), $sql));
		foreach ($list as $key => $value){
		      
            $period_name = "个月";
            if ($value["borrow_type"]=="day"){
                $period_name = "天";
            }
           $list[$key]["borrow_period_name"] =$value["borrow_period"].$period_name;
           $list[$key]["credit"] = borrowClass::GetBorrowCredit(array("user_id"=>$value['borrow_userid']));
		  
			$chsql="select status,buy_time from `{borrow_change}` where tender_id={$value['id']}";
			$chresult=$mysql->db_fetch_array($chsql);
			if ($chresult['status']==1){
				$recsql="select count(1) as count_all,
				sum(recover_account_yes) as recover_account_yes_all,
				sum(recover_interest_yes) as recover_interest_yes_all
				from `{borrow_recover}` where user_id={$value['user_id']} and borrow_nid={$value['borrow_nid']} and recover_yestime<{$chresult['buy_time']} and tender_id={$value['id']} and recover_status=1";
				$recresult=$mysql->db_fetch_array($recsql);
				$list[$key]["recover_interest_yes_all"] = $recresult['recover_interest_yes_all'];
				$list[$key]["recover_account_yes_all"] = $recresult['recover_account_yes_all'];
				$list[$key]["count_all"] = $recresult['count_all'];
			}
			$recoversql="select count(1) as num from `{borrow_repay}` where borrow_nid={$value['borrow_nid']} and (repay_status=1 or repay_web=1)";
			$recoverresult=$mysql->db_fetch_array($recoversql);
			$list[$key]['norepay_num'] = $value['borrow_period'] - $recoverresult['num'];
            
            if ($value['borrow_type']=="roam"){
                $list[$key]['repay_last_time'] = strtotime("{$value["borrow_period"]} month",$value['verify_time']);;
            }
		}
      
		//返回最终的结果
		$result = array('list' => $list?$list:array(),'total' => $total,'page' => $data['page'],'epage' => $data['epage'],'total_page' => $total_page);
		
		return $result;
	}
    
    
    
	/**
	 * 6,查看投资标 
	 *
	 * @param Array $data = array("id"=>"投资序号","tender_nid"=>"投资标识名");
	 * @return Array
	 */
	public static function GetTenderOne($data = array()){
		global $mysql;
		$_sql = "where 1=1 ";
		
		if (IsExiest($data['id'])!=""){
			$_sql .= " and  p1.id = '{$data['id']}' ";
		}
		
		if (IsExiest($data['tender_id'])!=""){
			$_sql .= " and  p1.tender_id = '{$data['tender_id']}' ";
		}
		
		$_select = " p1.*,p2.username,p3.name as borrow_name,p3.account as borrow_account,p3.borrow_period,p3.borrow_style,p3.borrow_use,p3.borrow_flag,p3.borrow_apr";
		$sql = "select {$_select} from `{borrow_tender}` as p1 
				 left join `{users}` as p2 on p1.user_id=p2.user_id
				 left join `{borrow}` as p3 on p1.borrow_nid=p3.borrow_nid
				 {$_sql}";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "borrow_tender_not_exiest";
		return $result;
	}
	
	
	
	/**
	 * 7,投资撤销，只要运用在投资人不想投的情况下可以手动的撤回，撤回需缴纳一定的 
	 *
	 * @param Array $data = array("id"=>"投资序号","tender_nid"=>"投资标识名");
	 * @return Array
	 */
	public static function CancelTender($data = array()){
		global $mysql;
		$sql = "select * from `{borrow_tender}` where tender_nid='{$data['tender_nid']}'";
		$result = $mysql->db_fetch_array($sql);
		if ($result==false) return "borrow_tender_not_exiest";
		if ($result['tender_status']>0) return "borrow_tender_verify_yes";
		
		$sql = "update `{borrow_tender}` set status=0 where tender_nid='{$data['tender_nid']}'";
		$mysql->db_query($sql);
		
		
		return $data['tender_nid'];
	}
	
	
    
	
	
	//收款明细
	function GetRecoverList($data){
		global $mysql,$_G;
		
		$_sql = " where 1=1 ";
		if (IsExiest($data['user_id'])!=false){
			if ($data['change_show']==1){
				$_sql .= " and (p5.change_status=1 and p5.change_userid={$data['user_id']} and (p1.recover_yestime>p7.buy_time or p1.recover_yestime is NULL)) or (p5.change_status!=1 and p1.user_id={$data['user_id']})";
			}else{
				$_sql .= " and p1.user_id={$data['user_id']}";
			}
		}
		if (IsExiest($data['status'])!=false){
			$_sql .= " and p1.status={$data['status']}";
		}
		if (IsExiest($data['recover_status'])!=false){
			if($data['recover_status']==2){
				$_sql .= " and p1.recover_status=0";
			}else{
				$_sql .= " and p1.recover_status={$data['recover_status']}";
			}
		}
		if (IsExiest($data['borrow_status'])!=false){
			$_sql .= " and p2.status={$data['borrow_status']}";
		}
		if (IsExiest($data['username'])!=false){
			$_sql .= " and p3.username like '%{$data['username']}%' ";
		}
		//搜索标题 
		if (IsExiest($data['borrow_name'])!=false){
			$data['borrow_name'] = urldecode($data['borrow_name']);
			$_sql .= " and p2.name like '%{$data['borrow_name']}%' ";
		}
		//搜索贷款号
		if (IsExiest($data['borrow_nid'])!=false){
			$_sql .= " and p1.borrow_nid = {$data['borrow_nid']}";
		}
		//搜索贷款类型
		if (IsExiest($data['borrow_type'])!=false){
			$_sql .= " and p2.borrow_type='{$data['borrow_type']}'";
		}
		
		if (IsExiest($data['web'])!=false){
			$_sql .= " and p6.web_status=2";
		}
		
		if (IsExiest($data['recover_status'])!=false || $data['recover_status']=="0"){
			$_sql .= " and p1.recover_status='{$data['recover_status']}'";
		}
		
		if (IsExiest($data['dotime1'])!=false){
			$dotime1 = ($data['dotime1']=="request")?$_REQUEST['dotime1']:$data['dotime1'];
			if ($dotime1!=""){
				$_sql .= " and p1.recover_time > ".get_mktime($dotime1);
			}
		}
		
		if (IsExiest($data['dotime2'])!=false){
			$dotime2 = ($data['dotime2']=="request")?$_REQUEST['dotime2']:$data['dotime2'];
			if ($dotime2!=""){
				$_sql .= " and p1.recover_time < ".get_mktime($dotime2);
			}
		}
        
		if (IsExiest($data['late'])!=false){
			$_sql .= " and (p1.recover_time < ".time()." and p1.recover_status!=1) or (p1.recover_status=1 and p1.late_days>0)";
		}
        
        
		if (IsExiest($data['yestime1'])!=false){
			$yestime1 = ($data['yestime1']=="request")?$_REQUEST['yestime1']:$data['yestime1'];
			if ($yestime1!=""){
				$_sql .= " and p1.recover_yestime > ".get_mktime($yestime1);
			}
		}
		
		if (IsExiest($data['yestime2'])!=false){
			$yestime2 = ($data['yestime2']=="request")?$_REQUEST['yestime2']:$data['yestime2'];
			if ($yestime2!=""){
				$_sql .= " and p1.recover_yestime < ".get_mktime($yestime2);
			}
		}
        
		if (IsExiest($data['type'])!=false){
			if ($data['type']=="yes"){
				$_sql .= " and p1.recover_status =1 or p1.recover_web=1";
			}elseif ($data['type']=="wait"){
				$_sql .= " and p1.recover_status !=1 and p1.recover_web!=1";
			}elseif ($data['type']=="web"){
				$_sql .= " and p1.recover_web=1";
			}
		}
		if (IsExiest($data['change'])!=false){
			$_sql .= " and p1.recover_status =1 and p5.change_status=1";
		}
		if (IsExiest($data['money1'])!=false){
			$_sql .= " and p1.recover_account >= '{$data['money1']}'";
		}
		if (IsExiest($data['money2'])!=false){
			$_sql .= " and p1.recover_account <= '{$data['money2']}'";
		}
		if (IsExiest($data['borrow_nid'])!=false){
			$_sql .= " and p1.borrow_nid = '{$data['borrow_nid']}'";
		}
		$onetime = time()+1*30*24*60*60;
		$threetime = time()+3*30*24*60*60;
		$sixtime = time()+6*30*24*60*60;
		if (IsExiest($data['dodate'])!=false){
			if($data['dodate']=="onemonth"){
				$dodate = $onetime;
			}elseif($data['dodate']=="threemonth"){
				$dodate = $threetime;
			}elseif($data['dodate']=="sixmonth"){
				$dodate = $sixtime;
			}
			$_sql .= " and p1.recover_time > ".time()." and p1.recover_time <= '{$dodate}' ";
		}		
		if (IsExiest($data['keywords'])!=""){
			$_sql .= " and (p2.name like '%".urldecode($data['keywords'])."%') ";
		}
		
		$_order = " order by p2.id ";
		if (IsExiest($data['order'])!="" ){
			if ($data['order'] == "repay_time"){
				$_order = " order by p2.id desc,p1.recover_time asc";
			}elseif ($data['order'] == "order"){
				$_order = " order by p1.`order` desc,p1.id desc ";
			}elseif ($data['order'] == "recover_status"){
				$_order = " order by p1.`recover_status` asc,p1.id desc ";
			}
		}
		if($data['protocol']==1){
			$_select = 'p1.recover_period,p1.recover_time,sum(p1.recover_account) as recover_account,sum(p1.recover_capital) as recover_capital,sum(p1.recover_interest) as recover_interest';
			$_order = " order by p1.`recover_period` asc ";
			$group = "group by p1.recover_period";
		}else{
			$_select = 'p1.*,p6.name as borrow_type_name,p6.title as type_title,p1.recover_account_yes as recover_recover_account_yes,p2.name as borrow_name,p2.borrow_period,p2.borrow_type,p2.borrow_apr,p3.username,p4.username as borrow_username,p4.user_id as borrow_userid,p5.recover_account_yes as tender_recover_account_yes';
			$group = "";
		}
		
		
		
		
	
		$sql = "select SELECT from `{borrow_recover}` as p1 
				left join `{borrow}` as p2 on  p2.borrow_nid = p1.borrow_nid
				left join `{borrow_type}` as p6 on  p6.nid = p2.borrow_type
				left join `{users}` as p3 on  p3.user_id = p1.user_id
				left join `{users}` as p4 on  p4.user_id = p2.user_id
				left join `{borrow_tender}` as p5 on  p1.tender_id = p5.id
				left join `{borrow_change}` as p7 on  p1.tender_id = p7.id
			   {$_sql} $group ORDER LIMIT";		   
		//是否显示全部的信息
		if (isset($data['limit']) ){
			$_limit = "";
			if ($data['limit'] != "all"){
				$_limit = "  limit ".$data['limit'];
			}
			$list  = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,  $_order, $_limit), $sql));
			return $list;
		}	
		
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array(" count(*) as num ","",""),$sql));
		
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
		$total = $row['num'];
		$total_page = ceil($total / $epage);
		$index = $epage * ($page - 1);
		$limit = " limit {$index}, {$epage}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, $_order , $limit), $sql));
	   foreach ($list as $key => $value){
	       $type_name = "";
	       if ($value["recover_type"]=="advance"){
	           $type_name = "提前还款";
	       }elseif ($value["recover_type"]=="yes"){
	           $type_name = "正常还款";
	       }elseif ($value["recover_type"]=="late"){
	           $type_name = "逾期还款";
	       }elseif ($value["recover_type"]=="web"){
	           $type_name = "网站垫付";
	       }
		   $days= borrowClass::GetDays(array("repay_time"=>$value["recover_time"]));
			if ($days>0){
				$list[$key]['late_days'] = $days;
			}
            if ($value["borrow_type"]=="roam"){
                 $list[$key]["borrow_period"] = 1;
            }
	       $list[$key]["recover_type_name"] = $type_name;
		   //计算利息服务费
		   $vip_status = usersClass::GetUsersVipStatus(array("user_id"=>$value["user_id"]));
		   if($vip_status==1){
			  $list[$key]["interest_fee"] = round($list[$key]["recover_interest"]*0.08,2);
		   }else{
			  $list[$key]["interest_fee"] = round($list[$key]["recover_interest"]*0.1,2);
		   }
		   $list[$key]["account_wait"]=$list[$key]["recover_account"]-$list[$key]["interest_fee"];
		   
		   
	   }
	   $lists = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, $_order , ''), $sql));
	   foreach ($lists as $key => $value){
			//统计待收金额  $onetime $threetime $sixtime
		   if($value["recover_time"] <= $onetime){
				$onemonth += $value["recover_account"];
		   }
		   if($value["recover_time"] <= $threetime){
				$threemonth += $value["recover_account"];
		   }
		   if($value["recover_time"] <= $sixtime){
				$sixmonth += $value["recover_account"];
		   }
		   $allmonth += $value["recover_account"];
	   }
		return array(
            'list' => $list,
            'change' => $change,
            'all_capital' => $all_capital,
            'all_recover' => $all_recover,
            'web' => $web,
            'total' => $total,
            'page' => $page,
            'epage' => $epage,
            'total_page' => $total_page,
			'onemonth' => $onemonth,
			'threemonth' => $threemonth,
			'sixmonth' => $sixmonth,
			'allmonth' => $allmonth			
        );
	}
    
    
    	
	//已成功的借款
	function GetTenderBorrowList($data){
		global $mysql,$_G;
		$user_id =$data['user_id'];
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
		$_sql = "where 1=1";
		if (IsExiest($data['type'])!=""){
			if ($data['type']=="wait"){
				$_sql .= " and p1.recover_times<p2.borrow_period and p1.user_id={$user_id} and p1.change_status!=1";
			}elseif ($data['type']=="change"){
				$_sql .= " and p1.recover_account_all!=p1.recover_account_yes and  p1.change_userid={$user_id} and p1.change_status=1";
			}elseif ($data['type']=="yes"){
				$_sql .= " and p1.recover_times=p2.borrow_period and p1.user_id={$user_id} and p1.change_status=0";
			}
		}
		
		
		if (IsExiest($data['dotime1'])!=""){
			$dotime1 = ($data['dotime1']=="request")?$_REQUEST['dotime1']:$data['dotime1'];
			if ($dotime1!=""){
				$_sql .= " and p1.addtime > ".get_mktime($dotime1);
			}
		}
		
		if (IsExiest($data['dotime2'])!=""){
			$dotime2 = ($data['dotime2']=="request")?$_REQUEST['dotime2']:$data['dotime2'];
			if ($dotime2!=""){
				$_sql .= " and p1.addtime < ".get_mktime($dotime2);
			}
		}
		if (IsExiest($data['tender_status'])!=""){
			$_sql .= " and p1.status = {$data['tender_status']}";
		}
		if (IsExiest($data['keywords']) !=""){
			$_sql .= " and (p2.`name` like '%".urldecode($data['keywords'])."%') ";
		}
		if (IsExiest($data['borrow_status']) !=""){
			$_sql .= " and p2.status = {$data['borrow_status']}";
		}
		if (IsExiest($data['change_status']) !=""){
			$_sql .= " and p1.change_status = {$data['change_status']}";
		}
		if (IsExiest($data['change_userid']) !=""){
			$_sql .= " and p1.change_userid = '{$data['change_userid']}'";
		}
		if (IsExiest($data['user_id']) !=""){
			$_sql .= " and p1.user_id = '{$data['user_id']}'";
		}
		$_select  = "p1.id,p1.recover_account_yes,p2.borrow_nid,p2.borrow_nid,p2.name,p2.borrow_apr,p2.user_id,p2.borrow_type,p2.borrow_period,p1.recover_times,p1.account as tender_account,p1.recover_account_wait,p1.user_id as tuser,p2.account as borrow_account,p2.borrow_account_yes,p3.username as borrow_username,p4.credits,p5.account as change_account,p5.id as change_id";
		
		$sql = "select SELECT from `{borrow_tender}` as p1 left join `{borrow}` as p2 on p1.borrow_nid=p2.borrow_nid left join `{users}` as p3 on p2.user_id=p3.user_id left join `{credit}` as p4 on p2.user_id=p4.user_id left join `{borrow_change}` as p5 on p5.tender_id=p1.id {$_sql} ORDER";
	
		//是否显示全部的信息
		if (isset($data['limit']) ){
			$_limit = "";
			if ($data['limit'] != "all"){
				$_limit = "  limit ".$data['limit'];
			}
			return $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,  'order by p1.`order` desc,p1.id desc', $_limit), $sql));
		}	
		
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array("count(*) as  num","",""),$sql));
		
		$total = $row['num'];
		$total_page = ceil($total / $epage);
		$index = $epage * ($page - 1);
		$limit = " limit {$index}, {$epage}";
		
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, 'order by p2.id desc', $limit), $sql));		
		$list = $list?$list:array();
		foreach ($list as $key => $value){
			$recoversql="select count(1) as num from `{borrow_repay}` where borrow_nid={$value['borrow_nid']} and (repay_status=1 or repay_web=1)";
			$recoverresult=$mysql->db_fetch_array($recoversql);
			$list[$key]['wait_times'] = $value['borrow_period'] - $recoverresult['num'];
			$list[$key]["credit"] = borrowClass::GetBorrowCredit(array("user_id"=>$value['user_id']));
			$chsql="select status,buy_time from `{borrow_change}` where tender_id={$value['id']}";
			$chresult=$mysql->db_fetch_array($chsql);
			if ($chresult['status']==1){
				$recsql="select count(1) as count_all,sum(recover_account_yes) as recover_account_yes_all from `{borrow_recover}` where user_id={$value['tuser']} and borrow_nid={$value['borrow_nid']} and (recover_yestime>{$chresult['buy_time']} or recover_yestime is NULL) and tender_id={$value['id']}";
				$recresult=$mysql->db_fetch_array($recsql);
				$list[$key]["recover_account_yes_all"] = $recresult['recover_account_yes_all'];
				$list[$key]["count_all"] = $recresult['count_all'];
			}
		}
		return array(
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'epage' => $epage,
            'total_page' => $total_page
        );
	}
    
	function GetRecoverVouchList($data = array()){
		global $mysql;
	
		$page = empty($data['page'])?1:$data['page'];
		$epage = empty($data['epage'])?10:$data['epage'];
		
		$_sql = " where p1.borrow_nid=p2.borrow_nid and p2.user_id=p3.user_id ";
		if (IsExiest($data['borrow_nid'])!=""){
			if ($data['borrow_nid'] == "request"){
				$_sql .= " and p1.borrow_nid= '{$_REQUEST['borrow_nid']}'";
			}else{
				$_sql .= " and p1.borrow_nid= '{$data['borrow_nid']}'";
			}
		}
		
		if (IsExiest($data['user_id'])!=""){
			$_sql .= " and p2.user_id = '{$data['user_id']}'";
		}	 
		
		if (IsExiest($data['vouch_userid']) !=""){
			$_sql .= " and p1.user_id = '{$data['vouch_userid']}'";
		}	 
		
		if (IsExiest($data['username'])!=""){
			$_sql .= " and p3.username like '%{$data['username']}%'";
		}	 
		
		if (IsExiest($data['type'])=="late"){
			$_sql .= " and p1.repay_time<".time() ." and p1.status=0";
		}	 
		
		if (IsExiest($data['repay_time'])!=""){
			if ($date['repay_time']<=0) $data['repay_time'] = time();
			$_repayment_time = get_mktime(date("Y-m-d",$data['repay_time']));
			$_sql .= " and p1.repay_time < '{$_repayment_time}'";
		}	 
		
		if (IsExiest($data['dotime2'])!=""){
			$dotime2 = ($data['dotime2']=="request")?$_REQUEST['dotime2']:$data['dotime2'];
			if ($dotime2!=""){
				$_sql .= " and p2.addtime < ".get_mktime($dotime2);
			}
		}
		if (IsExiest($data['dotime1'])!=""){
			$dotime1 = ($data['dotime1']=="request")?$_REQUEST['dotime1']:$data['dotime1'];
			if ($dotime1!=""){
				$_sql .= " and p2.addtime > ".get_mktime($dotime1);
			}
		}
		
		if (IsExiest($data['status'])!=""){
			$_sql .= " and p1.status in ({$data['status']})";
		}
		
		if (IsExiest($keywords)!=""){
		    if ($keywords=="request"){
				if (isset($_REQUEST['keywords']) && $_REQUEST['keywords']!=""){
					$_sql .= " and p2.name like '%".urldecode($_REQUEST['keywords'])."%'";
				}
			}else{
				$_sql .= " and p2.name like '%".$keywords."%'";
			}
			
		}
		
		$_order = " order by p1.id desc";
		if (isset($data['order']) && $data['order']!="" ){
			if ($data['order'] == "repayment_time"){
				$_order = " order by p1.repay_time asc ";
			}elseif ($data['order'] == "order"){
				$_order = " order by p1.order asc ,p1.id desc";
			}
		}
		
		$_select = " p1.*,p2.name as borrow_name,p2.borrow_period,p3.username as borrow_username";
		$sql = "select SELECT from `{borrow_vouch_recover}` as p1 left join `{borrow}` as p2 on p1.borrow_nid = p2.borrow_nid left join `{users}` as p3 on p3.user_id=p2.user_id {$_sql} ORDER LIMIT";
		
		//是否显示全部的信息
		if (isset($data['limit']) ){
			$_limit = "";
			if ($data['limit'] != "all"){
				$_limit = "  limit ".$data['limit'];
			}
			$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,  $_order, $_limit), $sql));
			
			foreach ($list as $key => $value){
				$late = self::LateInterest(array("time"=>$value['repay_time'],"account"=>$value['reapy_account']));
				if ($value['status']!=1){
					$list[$key]['late_days'] = $late['late_days'];
					$list[$key]['late_interest'] = $late['late_interest'];
				}
			}
			return $list;
		}			 
		
		$row = $mysql->db_fetch_array(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num', '', ''), $sql));
		
		$total = $row['num'];
		$total_page = ceil($total / $epage);
		$index = $epage * ($page - 1);
		$limit = " limit {$index}, {$epage}";
		$list = $mysql->db_fetch_arrays(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select,$_order, $limit), $sql));		
		$list = $list?$list:array();
		foreach ($list as $key => $value){
			$late = self::LateInterest(array("time"=>$value['repay_time'],"account"=>$value['capital']));
			if ($value['status']!=1){
				$list[$key]['late_days'] = $late['late_days'];
				$list[$key]['late_interest'] = $late['late_interest'];
			}
		}
		return array(
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'epage' => $epage,
            'total_page' => $total_page
        );
		
	}
	
}
?>
