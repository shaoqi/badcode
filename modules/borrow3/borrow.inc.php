<?
/******************************
 * $File: borrow.inc.php
 * $Description: 借款用户中心处理文件
 * $Author: ahui 
 * $Time:2010-08-09
 * $Update:None 
 * $UpdateDate:None 
 * Copyright(c) 2012 by dycms.net. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

include_once("borrow.class.php");
include_once("borrow.auto.php");
require_once(ROOT_PATH."modules/rating/rating.class.php");


//发布借款前是否可以检测
if ($_U['query_type'] == "loan"){	
	//借款类
	require_once("borrow.loan.php");
	
	//检查借款的条件
	$result = borrowLoanClass::CheckLoan(array("user_id"=>$_G['user_id'],"borrow_type"=>$_REQUEST['type']));
	$amount=amountClass::GetAmountUsers(array("user_id"=>$_G['user_id']));	
	
	if ($result=="info"){
		$template = "users_loan_info.html";//基本信息填写
	}elseif ($result=="work"){
		$template = "users_loan_work.html";//工作信息填写
	}elseif ($result=="diya"){
		$template = "users_loan_diya.html";//抵押物信息填写
	}elseif ($result=="amount"){
		$template = "users_loan_amount.html";//额度申请信息填写	
	}elseif ($result=="approve"){
		$template = "users_loan_approve.html";//认证信息填写
	}	
	$_G['site_result']['id'] = "14";
	$magic->assign("_G",$_G);
}
elseif ($_U['query_type'] == "loan_now"){
	$amount=amountClass::GetAmountUsers(array("user_id"=>$_G['user_id']));	
	if($amount["borrow_use"]==0){		
		header("Location: /?user&q=code/borrow/loan&type=1"); 
	}else{
		$template = "users_loan.html";//开始发布借款
		
		$_G['site_result']['id'] = "14";
		$magic->assign("_G",$_G);
	} 	
}
elseif ($_U['query_type'] == "loan_seconds"){
	$amount=amountClass::GetAmountUsers(array("user_id"=>$_G['user_id']));
	
	if($amount["borrow_use"]==0){		
		header("Location: /?user&q=code/borrow/loan&type=1&miao=1"); 
	}else{
		$template = "users_loan.html";//开始发布借款
		$_G['site_result']['id'] = "14";
		$magic->assign("_G",$_G);
	} 	
}
elseif ($_U['query_type'] == "loan_diya"){
	$amount=amountClass::GetAmountUsers(array("user_id"=>$_G['user_id']));	
	if($amount["diya_borrow_use"]==0){		
		header("Location: /?user&q=code/borrow/loan&type=2"); 
	}else{
		$template = "users_loan.html";//开始发布借款
		$_G['site_result']['id'] = "14";
		$magic->assign("_G",$_G);
	} 	
}

//添加基本信息
elseif ($_U['query_type'] == "loan_info"){	
	if ($_POST['submit']!=""){
			require_once(ROOT_PATH."/modules/rating/rating.class.php");
			require_once(ROOT_PATH."/modules/credit/credit.class.php");
			$var = array("sex","marry","children","income","birthday","edu","is_car","address","school_year","school","house","phone","jiguanprovince","jiguancity","hukouprovince","hukoucity","area","realname","card_id","phone_num","old_name","qq","post_id","house_status","car_status","nowhouse","houseaddress","housess","shouru","live_city");
			$data = post_var($var);
			$data['user_id'] = $_G['user_id'];
			$data['status'] = 1;
			$result = ratingClass::GetInfoOne($data);
			if (is_array($result)){
				$_result = ratingClass::UpdateInfo($data);
			}else{
				$_result = ratingClass::AddInfo($data);
				
				$credit_log['user_id'] = $_G['user_id'];
				$credit_log['nid'] = "info_credit";
				$credit_log['code'] = "borrow";
				$credit_log['type'] = "info_credit";
				$credit_log['addtime'] = time();
				$credit_log['article_id'] =$_G['user_id'];
				$credit_log['remark'] = "填写个人详情获得的积分";
				creditClass::ActionCreditLog($credit_log);
			}
			$template = "users_loan_work.html";//基本信息填写
	}else{
		$template = "users_loan_info.html";//基本信息填写
	}
}

//添加工作信息
elseif ($_U['query_type'] == "loan_work"){	
	if ($_POST['submit']!=""){
			require_once(ROOT_PATH."/modules/rating/rating.class.php");
			$var = array("name","type","industry","peoples","worktime1","office","address","tel","rating_income","other_income","juzhu_status","juzhu_out","month_fee","cardrepay","info","work_status","province","city","com_type","com_hy","com_gm","rating_workyear","work_email","z_name","z_gx","z_tel","q_name","q_gx","q_tel");
			$data = post_var($var);
			$data['user_id'] = $_G['user_id'];
			$data['status'] = 1;
			$result = ratingClass::GetJobOne($data);
			if (is_array($result)){
				$_result = ratingClass::UpdateJob($data);
			}else{
				$_result = ratingClass::AddJob($data);
				
				$credit_log['user_id'] = $_G['user_id'];
				$credit_log['nid'] = "work_credit";
				$credit_log['code'] = "borrow";
				$credit_log['type'] = "work_credit";
				$credit_log['addtime'] = time();
				$credit_log['article_id'] =$_G['user_id'];
				$credit_log['remark'] = "填写工作详情获得的积分";
				creditClass::ActionCreditLog($credit_log);
			}
			$template = "users_loan_amount.html";//基本信息填写
	}else{
		$template = "users_loan_work.html";//基本信息填写
	}
}


/* 上传资料的审核*/
//添加工作信息
elseif ($_U['query_type'] == "loan_att"){	
	$template = "users_loan_ajax_att.html";//基本信息填写
}
//实名认证
elseif ($_U['query_type'] == "loan_realname"){	
	$template = "users_loan_ajax_realname.html";//基本信息填写
}
//手机认证
elseif ($_U['query_type'] == "loan_phone"){	
	$template = "users_loan_ajax_phone.html";//基本信息填写
}
//投标页面
elseif ($_U['query_type'] == "loan_tender"){	
	$template = "detail.html";
}

//添加额度申请信息
elseif ($_U['query_type'] == "loan_amount"){	
	if ($_POST['submit']!=""){
		$var = array("amount_account","content","amount_type","remark","borrow_use","borrow_period","otherborrow");
		$data = post_var($var);
		
		$data['user_id'] = $_G['user_id'];
	
		$result = borrowClass::GetAmountApplyOne(array("user_id"=>$data['user_id'],"amount_type"=>$data['amount_type'],"status"=>0));
		if ($result!=false){			
			$msg = array("您已经提交了申请，请等待审核");
		}else{
			$data['status'] = 0;
			$data['oprate'] = "add";
			$result = borrowClass::AddAmountApply($data);			
		}
		$template = "users_loan_approve.html";//基本信息填写
	}else{
		$template = "users_loan_amount.html";//基本信息填写
	}
}
elseif ($_U['query_type'] == "loan_diyaw"){	
	if ($_POST['submit']!=""){
		$var = array("type","address","name","areas","in_year","balance","chanquan","car_pp","car_xh","car_money","car_year","car_lc","car_holder","miaoshu");
		$data = post_var($var);	
		$data['user_id'] = $_G['user_id'];	
		$data['status'] = 1;
		$result = ratingClass::GetHouseOne($data);	
		
		if (is_array($result)){
				$_result = ratingClass::UpdateHouse($data);
			}else{
				$_result = ratingClass::AddHouse($data);
			}
		$template = "users_loan_amount.html";//基本信息填写
	}else{
		$template = "users_loan_diya.html";//基本信息填写
	}
}

elseif ($_U['query_type'] == "loan_add_realname"){	
	require_once(ROOT_PATH."modules/approve/approve.class.php");	
	if ($_POST['realname']!=""){
		$var = array("realname","card_id");
		$data = post_var($var);
		$data['user_id'] = $_G['user_id'];		
		$result = approveClass::AddRealname($data);		
		$template="detail.html";
	}
}
//判断是否实名认证或者是否已经填写姓名和身份证号码
elseif ($_U['query_type'] == "loan_check_realname"){	
	require_once("borrow.loan.php");
	//检查借款的条件
	$result = borrowLoanClass::CheckRealname(array("user_id"=>$_G['user_id']));
	echo $result;
	exit;
}



//借款标的添加和修改
elseif ($_U['query_type'] == "add" || $_U['query_type'] == "update"){	
	$msg = check_valicode();
	if ($msg!=""){
		$msg = array("验证码错误");
	}elseif (!isset($_POST['name'])){
		$msg = array($MsgInfo["borrow_name_empty"]);
	}/* elseif($_POST['borrow_style']==1 && $_POST['borrow_period']%3!=0){
		$msg = array($MsgInfo["borrow_period_season_error"]);
	} */elseif($_POST['account']<500 || $_POST['account']>1000000){
		$msg = array("金额不在范围内");
	}elseif (($_POST['borrow_type']==1 || $_POST['borrow_type']==4) && ($_POST['borrow_apr']<$_G['system']['con_borrow_apr_min'] || $_POST['borrow_apr']>$_G['system']['con_borrow_apr_max'])){		
		$msg = array("利率不在范围内");
	}elseif($_POST['borrow_type']==2 && ($_POST['borrow_apr']<$_G['system']['con_diya_apr_min'] || $_POST['borrow_apr']>$_G['system']['con_diya_apr_max'])){
		$msg = array("抵押利率不在范围内");
	}else{		
		$var = array("name","borrow_use","borrow_period","borrow_style","account","borrow_apr","borrow_contents","borrow_type","about_me","about_use","about_repay","com_status","office_status","tender_account_min");
		
		$data = post_var($var);
		$data['open_account'] = 1;
		$data['open_borrow'] = 1;
		$data['open_credit'] = 1;
		$data['borrow_account_wait'] = $data['account'];
		$data['vouch_account'] = $data['account'];
		$data['vouch_account_wait'] = $data['account'];
		//生成借款序号流水
		$sql="select max(id) as maxid from `{borrow}`";
		$nid=$mysql->db_fetch_array($sql);
		if ($nid['maxid']==""){
			$today = date("Ym");
			$data["borrow_nid"]=$today."00001";
		}else{
			$sql="select borrow_nid from `{borrow}` where id={$nid['maxid']}";
			$borrow_nid=$mysql->db_fetch_array($sql);
			$today = date("Ym");
			$pid = str_replace($today,'',$borrow_nid['borrow_nid']);
			if (strlen($pid)==strlen($borrow_nid['borrow_nid'])){
				$data["borrow_nid"]=$today."00001";
			}else{
				$pid = $today.str_pad($pid,5,"0",STR_PAD_LEFT);
				$data["borrow_nid"]=$pid+1;
			}
		}
		if ($_POST['submit']=="保存草稿"){
			$data['status'] = -1;
		}else{
			$data['status'] =0;
		}
		if ($_G['system']['con_borrow_not_check']==1){
			$data['status'] = 1;
		}			
        
		if ($_POST['borrow_type']==2){
			$data['borrow_valid_time'] = 7;
			$data['vouchstatus'] = 1;
		}elseif ($_POST['borrow_type']==3){
			$data['borrow_valid_time'] = 2;
			$data['fast_status'] = 1;
		}elseif ($_POST['borrow_type']==5){
			$data['borrow_valid_time'] = 4;			
		}elseif ($_POST['borrow_type']==4){
			$data['order'] = 1;			
		}else{
			$data['borrow_valid_time'] = 7;
		}
        
        $data['borrow_valid_time'] = $_POST['borrow_valid_time'];
		if ($data["award_status"]==0){
			$data["award_false"] = 0;
		}
		$data['user_id'] = $_G['user_id'];
        
       $data['name'] =  iconv("UTF-8", "GB2312", $data['name']);
       $data['about_me'] =  iconv("UTF-8", "GB2312", $data['about_me']);
       $data['about_use'] =  iconv("UTF-8", "GB2312", $data['about_use']);
       $data['about_repay'] =  iconv("UTF-8", "GB2312", $data['about_repay']);
		if ($_U['query_type'] == "add"){
			if (isset($_POST['type']) && $_POST['type']=="tiyan"){
				$data['borrow_style'] = 5;
				$data['borrow_apr'] = 20;
				$result = borrowClass::AddBorrowTiyan($data);
			}elseif (isset($_POST['type']) && $_POST['type']=="vouch"){				
				$result = borrowClass::AddBorrowVouch($data);
			}else{			
				$result = borrowClass::Add($data);
			}
			//借款标的审核额度的
		}else{
			$data['borrow_nid'] = $_POST['id'];
			$data['user_id'] = $_G['user_id'];
			$result = borrowClass::Update($data);
		}
		$_SESSION['valicode'] = "";		
		if ($result>0){
			$msg = array($MsgInfo["borrow_success_msg"],"","/index.php?user&q=code/borrow/publish");
			if ($_REQUEST['ajax']=="1"){
			echo 1;exit;
			}
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
	}
		if ($_REQUEST['ajax']=="1"){
			echo $msg[0];exit;
		}
	
}

//借款标的撤回

elseif ($_U['query_type'] == "cancel"){
	$data['borrow_nid'] = $_REQUEST['borrow_nid'];
	$data['user_id'] = $_G['user_id'];
	$result = borrowClass::GetOne($data);//获取借款标的单独信息
	
	//如果借款进度大于70
	if ($result['borrow_account_scale']==100){
		$msg = array($MsgInfo["borrow_scale100_not_cancel"]);
	}else{
		$result = borrowClass::Cancel($data);
		if ($result>0){
			$msg = array($MsgInfo["borrow_cancel_success"],"","index.php?user&q=code/borrow/publish");
		}elseif (IsExiest($MsgInfo[$result])!=""){
			$msg = array($MsgInfo[$result]);
		}else{
			$msg = array("撤销失败，请跟管理员联系");
		}	
	}	
}


//借款标的撤回

elseif ($_U['query_type'] == "user_cancel"){
	echo "<br>请输入申请撤销的理由：<br><form method='post' action='index.php?user&q=code/borrow/cancel&id=".$_REQUEST['borrow_nid']."'>";
	echo "<br><textarea cols='35' rows='4' name='cancel_remark'></textarea><br><br>";
	echo "<input type='submit' value='申请撤销'><input type=hidden name='nid' ></form>";
	exit;
	
}

//借款标的垫付

elseif ($_U['query_type'] == "vouch_dianfu"){
	$data['id'] = $_REQUEST['id'];
	$data['user_id'] = $_G['user_id'];
	$result = borrowClass::VouchDianfu($data);
	if ($result===true){
		$msg = array($MsgInfo["vouch_late_repay"],"","index.php?user&q=code/borrow/tender_vouch_late");
	}else{
		$msg = array($MsgInfo[$result]);
	}
}
//删除
elseif ($_U['query_type'] == "del"){
	$data['id'] = $_REQUEST['id'];
	$data['user_id'] = $_G['user_id'];
	$data['status'] = -1;
	$result = borrowClass::Delete($data);
	if ($result==false){
		$msg = array($result);
	}else{
		$msg = array("招标删除成功!","","?user&q=code/borrow/unpublish");
	}
}

//用户投标
elseif ($_U['query_type'] == "tender"){
	$msg = check_valicode();
	if ($msg!=""){
		$msg = array("验证码错误");
	}else{
		include_once(ROOT_PATH."modules/account/account.class.php");
		if ($_POST['money']==""){
			$msg = array("投标金额不能为空");
		}elseif ($_G['user_result']['islock']==1){
			$msg = array("您账号已经被锁定，不能进行投标，请跟管理员联系");
		}elseif (md5($_POST['paypassword'])!=$_G['user_result']['paypassword']){
			$msg = array("支付交易密码不正确");
		}elseif($_POST['money']%50!=0){
			$msg = array($MsgInfo["tender_50_no"]);
		}else{
			//将借款标添加进去
			$_tender['borrow_nid'] = $_POST['borrow_nid'];
			$_tender['user_id'] = $_G['user_id'];
			$_tender['account'] = $_POST['money'];
			$_tender['contents'] = $_POST['contents'];
			$_tender['status'] = 0;
			$_tender['nid'] = "tender_".$data['user_id'].time().rand(10,99);//订单号
			$result = borrowClass::AddTender($_tender);
			
			if ($result>0){
				if ($_REQUEST['ajax']=="1"){
					$msg = array(1);
				}else{
					$msg = array("投标成功","","/index.php?user&q=code/borrow/gettender");
				}
			}elseif ($result=="tender_money_no"){
				$msg = array($MsgInfo[$result],"","/?user&q=code/account/recharge_new");
			}elseif (IsExiest($MsgInfo[$result])!=""){
				$msg = array($MsgInfo[$result],"","/index.php?user&q=code/borrow/gettender");
			}else{
				$msg = array($result,"","/index.php?user&q=code/borrow/gettender");
			}	
		}
	}
	
	if ($_REQUEST['ajax']=="1"){
		echo $msg[0];
		exit;
	}
}



//担保标投标
elseif ($_U['query_type'] == "vouch"){
	$msg = "";
	if ($_SESSION['valicode']!=$_POST['valicode']){
		$msg = array("验证码错误");
	}else{
		$borrow_result = borrowClass::GetOne(array("borrow_nid"=>$_POST['borrow_nid']));//获取借款标的单独信息
		$vouch_account = $_POST['money'];
		if ($borrow_result['vouch_account_wait']<$vouch_account){
			$account_money = $borrow_result['vouch_account_wait'];
		}else{
			$account_money = $vouch_account;
		}
		if ($vouch_account<0){
			$msg = array("请输入正确的金额");
		}elseif ($borrow_result["borrow_nid"]!=$_POST['borrow_nid']){
			$msg = array("你的输入有误，请勿乱操作");
		}elseif ($_G['user_result']['islock']==1){
			$msg = array("您账号已经被锁定，不能进行担保，请跟管理员联系");
		}elseif (!is_array($borrow_result)){
			$msg = array($borrow_result);
		}elseif ($borrow_result['vouch_account']==$borrow_result['vouch_account_yes']){
			$msg = array("此担保标担保金额已满，请勿再担保");
		}elseif ($borrow_result['verify_time'] == "" || $borrow_result['status'] != 1){
			$msg = array("此标尚未通过审核");
		}elseif ($borrow_result['verify_time'] + $borrow_result['borrow_valid_time']>time()){
			$msg = array("此标已过期");
		}elseif (md5($_POST['paypassword'])!=$_G['user_result']['paypassword']){
			$msg = array("支付交易密码不正确");
		}else{
			//获取投资的担保额度
			$amount_result =  borrowClass::GetAmountUsers(array("user_id"=>$_G['user_id']));
			
			if ($amount_result['vouch_tender_use']<$account_money){
				$msg = array("您的担保金额不足");
			}else{
				
				//判断是否是担保人
				if ($borrow_result['vouch_users']!=""){
					$_vouch_user = explode("|",$borrow_result['vouch_users']);
					if (!in_array($_G['user_result']['username'],$_vouch_user)){
						$msg = array("此担保标已经指定了担保人，你不是此担保人，不能进行担保");
					}
				}
				if ($msg==""){
					$data['borrow_nid'] = $_POST['borrow_nid'];
					$data['account_vouch'] = $vouch_account;
					$data['account'] = $account_money;
					$data['user_id'] = $_G['user_id'];
					$data['award_scale'] = $borrow_result['vouch_award_scale'];
					$data['award_account'] = round($data['award_scale']*0.01*$account_money,2);
					$data['contents'] = $_POST['contents'];
					$data['status'] = 0;
					$result = borrowClass::AddVouch($data);//添加担保标
					if ($result>0){
						$msg = array("担保成功","","/index.php?user&q=code/borrow/tender_vouch");
						$_SESSION['valicode'] = "";
					}else{
						$msg = array($MsgInfo[$result]);
					}
				}
			}
		}
	}
}


//查看投标
elseif ($_U['query_type'] == "repayment_view"){
	$data['borrow_nid'] = $_REQUEST['borrow_nid'];
	if ($data['borrow_nid']==""){
		$msg = array("您的输入有误");
	}
	$data['user_id'] = $_G['user_id'];
	$result =  borrowClass::GetOne($data);//获取当前用户的余额
	if ($result==false){
		$msg = array("您的操作有误");
	}else{
		$_U['borrow_result'] = $result;
	}
}

//还款
elseif ($_U['query_type'] == "repay"){
	if ($_REQUEST['id']!=""){
		$data['borrow_nid'] = $_REQUEST['borrow_nid'];
		$data['id'] = $_REQUEST['id'];
		$data['user_id'] = $_G['user_id'];
		$result =  borrowClass::BorrowRepay($data);//获取当前用户的余额
		if ($result>0){
			$msg = array("还款成功","","/index.php?user&q=code/borrow/repayment_view&borrow_nid=".$_REQUEST['borrow_nid']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
	}else{
		$data['borrow_nid'] = $_REQUEST['borrow_nid'];
		$data['user_id'] = $_G['user_id'];
		$result =  borrowClass::BorrowAdvanceRepay($data);//提前还款
		if ($result>0){
			$msg = array("还款成功","","/index.php?user&q=code/borrow/repayment_view&borrow_nid=".$_REQUEST['borrow_nid']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
	}
}


//还款
elseif ($_U['query_type'] == "repays"){
	require_once("borrow.repay.php");
	if ($_REQUEST['id']=="" || $_REQUEST['borrow_nid']==""){
		$msg = array("您的操作有误");
	}else{
		$data['borrow_nid'] = $_REQUEST['borrow_nid'];
		$data['repay_id'] = $_REQUEST['id'];
		$data['user_id'] = $_G['user_id'];
		if ($_REQUEST["step"]==""){
			$result =  borrowrepayClass::RepayStep0($data);//获取当前用户的余额
			if ($result>0){
				$_U['borrow_title'] = "第一步，判断是否可以审核";
				$_U['borrow_url'] = $_U['query_url']."/repays&id={$data['repay_id']}&borrow_nid={$data['borrow_nid']}&step=1&key=0";
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}elseif ($_REQUEST["step"]=="1"){
			$data['key'] = $_REQUEST['key'];
			$result =  borrowrepayClass::RepayStep1($data);//获取当前用户的余额
			if ($result>0){
				$key = $_REQUEST['key']+1;
				$_U['borrow_title'] = "第二步，对投资人的还款";
				$_U['borrow_url'] = $_U['query_url']."/repays&id={$data['repay_id']}&borrow_nid={$data['borrow_nid']}&step=1&key={$key}";
			}elseif ($result==-1){
				$_U['borrow_title'] = "第三步，网站代还款操作";
				$_U['borrow_url'] = $_U['query_url']."/repays&id={$data['repay_id']}&borrow_nid={$data['borrow_nid']}&step=2";
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}elseif ($_REQUEST["step"]=="2"){
			$result =  borrowrepayClass::RepayStep2($data);//获取当前用户的余额
			if ($result>0){
				$_U['borrow_title'] = "第四步，担保还款操作";
				$_U['borrow_url'] = $_U['query_url']."/repays&id={$data['repay_id']}&borrow_nid={$data['borrow_nid']}&step=3&key=0";
			}else{
				$msg = array($MsgInfo[$result]);
			}
			
		}elseif ($_REQUEST["step"]=="3"){
			$data['key'] = $_REQUEST['key'];
			$result =  borrowrepayClass::RepayStep3($data);//获取当前用户的余额
			if ($result>0){
				$key = $_REQUEST['key']+1;
				$_U['borrow_title'] = "第四步，担保还款操作";
				$_U['borrow_url'] = $_U['query_url']."/repays&id={$data['repay_id']}&borrow_nid={$data['borrow_nid']}&step=3&key={$key}";
			}elseif ($result==-1){
				$_U['borrow_title'] = "第五步，成功还款额度返回";
				$_U['borrow_url'] = $_U['query_url']."/repays&id={$data['repay_id']}&borrow_nid={$data['borrow_nid']}&step=4&key=0";
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}elseif ($_REQUEST["step"]=="4"){
			$data['key'] = $_REQUEST['key'];
			$result =  borrowrepayClass::RepayStep4($data);//获取当前用户的余额
			if ($result>0){
				$_U['borrow_title'] = "第五步，成功还款额度返回";
				$_U['borrow_url'] = $_U['query_url']."/repays&id={$data['repay_id']}&borrow_nid={$data['borrow_nid']}&step=4&key={$key}";
			}elseif ($result==-1){
				$_U['borrow_title'] = "第六步，更新网站信息";
				$_U['borrow_url'] = $_U['query_url']."/repays&id={$data['repay_id']}&borrow_nid={$data['borrow_nid']}&step=5";
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}else{
			$result =  borrowrepayClass::RepayStep5($data);//获取当前用户的余额
			
			if ($result==1){
				$msg = array("还款成功","",$_U['query_url']."/repaymentplan");
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}
	}
	
}
//还款
elseif ($_U['query_type'] == "limitapp"){
	if (isset($_POST['amount_account']) && $_POST['amount_account']>0){
		$var = array("amount_account","content","amount_type","remark");
		$data = post_var($var);
		$data['user_id'] = $_G['user_id'];
		$result = borrowClass::GetAmountApplyOne(array("user_id"=>$data['user_id'],"amount_type"=>$data['amount_type']));
		
		if ($result!=false && $result['addtime']+60*60*24*30 >time() && $result['status']==0){
			$msg = array("您已经提交了申请，请等待审核");
		}elseif ($result!=false && $result['verify_time']+60*60*24*30 >time()){
			$msg = array("请一个月后再申请");
		}else{
			$data['status'] = 0;
			$data['oprate'] = "add";
			$result = borrowClass::AddAmountApply($data);
			if ($result>0){
				$msg = array("申请成功，请等待管理员审核","",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}
	}
	
	$result =  borrowClass::GetBorrowVip(array("user_id"=>$_G['user_id']));
}
elseif ($_U['query_type'] == "auto_new"){
	if ($_REQUEST['id']!=""){
		$data['user_id'] = $_G['user_id'];
		$data['id'] = $_REQUEST['id'];
		$_U['auto_result'] = borrowClass::GetAutoOne($data);
	}

}
//自动投标添加
elseif ($_U['query_type'] == "auto_add"){
	$var = array("status","tender_type","tender_account","tender_scale","order","account_min","first_date","last_date","account_min_status","date_status","account_use_status","account_use","video_status","realname_status","phone_status","my_friend","not_black","late_status","late_times","dianfu_status","dianfu_times","black_status","black_user","black_times","not_late_black","borrow_credit_status","borrow_credit_first","borrow_credit_last","tender_credit_status","tender_credit_first","tender_credit_last","user_rank","first_credit","last_credit","webpay_statis","webpay_times","borrow_style","timelimit_status","timelimit_month_first","timelimit_month_last","timelimit_day_first","timelimit_day_last","apr_status","apr_first","apr_last","award_status","award_first","award_last","vouch_status","tuijian_status","min_account");
	$data = post_var($var);
	$data['user_id'] = $_G['user_id'];
	if ($data['tender_type']==2 && ($data['tender_scale']<1 || $data['tender_scale']>20)){
		$msg = array("按比例投标比例不能小于1%大于20%");
	}else{
		if (IsExiest($_POST['id']!="")){
			$data['id'] = $_POST['id'];
			$result = borrowClass::UpdateAuto($data);
			$msg = array("自动投标信息修改成功","","/index.php?user&q=code/borrow/auto");
		}else{
			$result = autoClass::AddAuto($data);
			if ($result == -2){
				$msg = array("你最多只能发布三个自动投标信息");
			}elseif ($result==-1){
				$msg = array("你的操作有误，请不要乱操作");
			}else{
				$msg = array("自动投标信息添加成功","","/index.php?user&q=code/borrow/auto");
				
			}
		}
	}
}

//自动投标删除
elseif ($_U['query_type'] == "auto_del"){
	
	$data['user_id'] = $_G['user_id'];
	$data["id"] = $_REQUEST['id'];
	$result = borrowClass::DelAuto($data);
	if ($result!=1){
		$msg = array("你的操作有误，请不要乱操作");
	}else{
		$msg = array("自动投标信息删除成功","","/index.php?user&q=code/borrow/auto");
		
	}
}


//借款关注添加
elseif ($_U['query_type'] == "add_care"){
	
	$data['user_id'] = $_G['user_id'];
	$data["article_id"] = $_REQUEST['article_id'];
	$result = borrowClass::AddCare($data);
	if ($result==1){
		$msg = array("加入关注成功","","/watchlist/index.html");
	}else{
		$msg = array("已关注此标","","/watchlist/index.html");
		
	}
}

//黑名单添加
elseif ($_U['query_type'] == "add_black"){
	
	$data['user_id'] = $_G['user_id'];
	$data["blackuser"] = $_REQUEST['user_id'];
	$data["code"] = borrow;
	$result = usersClass::AddBlack($data);
	if ($result == -2){
		$msg = array("你已经将其加入了黑名单，不能重复操作");
	}elseif ($result==-1){
		$msg = array("你的操作有误，请不要乱操作");
	}else{
		$msg = array("加入黑名单成功","","/watchlist/index.html");
		
	}
}

//借款关注删除
elseif ($_U['query_type'] == "del_care"){
	
	$data['user_id'] = $_G['user_id'];
	$data["article_id"] = $_REQUEST['article_id'];
	$data["code"] ="borrow";
	$result = userClass::DelCare($data);
	if ($result!=1){
		$msg = array("你的操作有误，请不要乱操作");
	}else{
		$msg = array("关注的借款删除成功","","/index.php?user&q=code/borrow/care");
		
	}
}


//借款关注删除
elseif ($_U['query_type'] == "tender_comment"){
	if ($_REQUEST['id']!=""){
		require_once(ROOT_PATH."modules/comment/comment.class.php");
		if ($_POST['reply_remark']==""){
			$_comment["code"] = "borrow";
			$_comment["id"] = $_REQUEST["id"];
			$_comment["article_userid"] = $_G["user_id"];
			$_U['comment_result'] = commentClass::GetOne($_comment);
			
			if ($_U['comment_result']=="") {
				$msg = array("请不要乱操作");
			}
		}else{
			if ($_G["user_id"]!=$_POST["article_userid"]){
				$msg = array("请不要乱操作");
			}else{
				$_comment["id"] = $_REQUEST["id"];
				$_comment["code"] = "borrow";
				$_comment["reply_userid"] = $_G["user_id"];
				$_comment["article_userid"] = $_POST["article_userid"];
				$_comment["reply_remark"] = $_POST['reply_remark'];
				commentClass::ReplyComment($_comment);
				$msg = array("回复成功","","/?user&q=code/borrow/tender_comment");
			}
		}
	}
}


//其他网站的借款
elseif ($_U['query_type'] == "otherloan_new"){
	if ($_REQUEST['id']!=""){
		if ($_POST['agency']!=""){
			$var = array("agency","username","url","amount_credit","amount_vouch","repay_nouse","repay_month","remark");
			$data = post_var($var);
			$data["user_id"] = $_G["user_id"];
			$data["id"] = $_REQUEST["id"];
			if ($data["agency"]==""){
				$msg = array("组织机构名称不能为空","","");
			}else{
				$result = borrowClass::UpdateOtherloan($data);
				if ($result===true){
					$msg = array("修改成功","","/?user&q=code/borrow/otherloan");
				}else{
					$msg = array("修改失败","","/?user&q=code/borrow/otherloan");
				
				}
			}
		}else{
			$data["user_id"] = $_G["user_id"];
			$data["id"] = $_REQUEST["id"];
			$_U["otherloan_result"] = borrowClass::GetOtherloanOne($data);
			if ($_U["otherloan_result"]==""){
				$msg = array("请不要乱操作");
			}
		
		}
	}else{
		if ($_POST['agency']!=""){
			$var = array("agency","username","url","amount_credit","amount_vouch","repay_nouse","repay_month","remark");
			$data = post_var($var);
			$data["user_id"] = $_G["user_id"];
			if ($data["agency"]==""){
				$msg = array("组织机构名称不能为空","","");
			}else{
				$result = borrowClass::AddOtherloan($data);
				if ($result===true){
					$msg = array("添加成功","","/?user&q=code/borrow/otherloan");
				}else{
					$msg = array("添加失败","","/?user&q=code/borrow/otherloan");
				
				}
			}
		}
	}
}

//其他借款删除
elseif ($_U['query_type'] == "otherloan_del"){
	
	$data['user_id'] = $_G['user_id'];
	$data["id"] = $_REQUEST['id'];
	$result = borrowClass::DelOtherloan($data);
	if ($result!=1){
		$msg = array("你的操作有误，请不要乱操作");
	}else{
		$msg = array("删除成功","","/index.php?user&q=code/borrow/otherloan");
		
	}
}

//其他借款删除
elseif ($_U['query_type'] == "change"){
	require_once('borrow.change.inc.php');
}



//我要投资的ajax页面
elseif ($_U['query_type'] == "tendering"){
	$template = "user_borrow_tendering.html";
}


//额度管理
elseif ($_U['query_type'] == "amount"){

	if (isset($_POST['amount_type']) && $_POST['amount_type']!=""){

		$var = array("amount_account","content","amount_type","remark");
		$data = post_var($var);

		if ($_POST['amount_type']=="vouch_borrow"){
			$data['type'] = $_POST['type'];
			if ($data['type']==2){
				$data['voucher_name'] = $_POST['voucher_name'];
				$data['voucher_lianxi'] = $_POST['voucher_lianxi'];
				$_G['upimg']['file'] = "pic";
				$_G['upimg']['code'] = "amount_apply";
				$_G['upimg']['type'] = "album";
				$_G['upimg']['user_id'] = $_G["user_id"];
				$_G['upimg']['article_id'] = $_G["user_id"];
				$data["pic_result"] = $upload->upfile($_G['upimg']);
				if (is_array($data["pic_result"])){
					foreach ($data["pic_result"] as $key => $value){
						$data["voucher_att"] = $value['upfiles_id'];
					}
				}
			}elseif($data['type']==3){
				$data['vouchjg_name'] = $_POST['vouchjg_name'];
				$data['vouchjg_lianxi'] = $_POST['vouchjg_lianxi'];
				$data['vouchjg_js'] = $_POST['vouchjg_js'];
				$_G['upimg']['file'] = "vouch_pic[]";
				$_G['upimg']['code'] = "amount_apply";
				$_G['upimg']['type'] = "album";
				$_G['upimg']['user_id'] = $_G["user_id"];
				$_G['upimg']['article_id'] = $_G["user_id"];
				$data["pic_result"] = $upload->upfile($_G['upimg']);
				if (is_array($data["pic_result"])){
					foreach ($data["pic_result"] as $key => $value){
						$data["vouchjg_xy"] = $value['upfiles_id'];
					}
				}
			}
		}
		$data['user_id'] = $_G['user_id'];
		//$data['content'] = iconv('UTF-8', 'GB2312',$data['content']);
		//$data['remark'] = iconv('UTF-8', 'GB2312',$data['remark']);
		$result = borrowClass::GetAmountApplyOne(array("user_id"=>$data['user_id'],"amount_type"=>$data['amount_type']));
		
		if ($result!=false && $result['addtime']+60*60*24*30 >time() && $result['status']==0){
			//$msg = array("您已经提交了申请，请等待审核");
			$msg = array("您已经提交了申请，请等待审核","","/?user&q=code/borrow/amount");
		}else{
			$data['status'] = 0;
			$data['oprate'] = "add";
			$result = borrowClass::AddAmountApply($data);
			if ($result>0){
				$msg = array("您的申请已经被提交","",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}
		//echo $msg[0];
		//exit;
	}
	
}

if ($template==""){
	$template = "user_borrow.html";
}

?>
