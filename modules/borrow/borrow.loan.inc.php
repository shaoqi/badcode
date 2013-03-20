<?php
/******************************
 * $File: borrow.loan.inc.php
 * $Description: 用户借款用户中心处理文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

require_once("borrow.class.php");//类名
require_once("borrow.loan.php");//类名
require_once("borrow.type.php");//类名

//新的借款
if ($_REQUEST['p']=="new"){
    
    if (isset($_POST['name']) && $_POST["name"]!=""){
        $var = array("name","borrow_type","borrow_use","borrow_password","account","borrow_period","borrow_apr","borrow_style","borrow_contents","borrow_valid_time","tender_account_min","tender_account_max","award_status","award_scale","award_account","award_false","valicode");
		$data = post_var($var);
		$data['user_id'] = $_G['user_id'];
		
        //抵押标
		if($data['borrow_type']=="pawn"){
			$_G['upimg']['code'] = "borrow";
			$_G['upimg']['type'] = "pawn";
			$_G['upimg']['user_id'] = $data["user_id"];
			$_G['upimg']['article_id'] = $data["user_id"];
			
			$_G['upimg']['file'] = "borrow_pawn_app";
			$pic_result1 = $upload->upfile($_G['upimg']);			
			if ($pic_result1!=false){
				$data["borrow_pawn_app"] = $pic_result1[0]["upfiles_id"];
				$data["borrow_pawn_app_url"] = $pic_result1[0]["filename"];
			}
			
			$_G['upimg']['file'] = "borrow_pawn_auth";
			$pic_result2 = $upload->upfile($_G['upimg']);
			if ($pic_result2!=false){
				$data["borrow_pawn_auth"] = $pic_result2[0]["upfiles_id"];
				$data["borrow_pawn_auth_url"] = $pic_result2[0]["filename"];
			}
			$_G['upimg']['file'] = "borrow_pawn_formalities";
			$pic_result3 = $upload->upfile($_G['upimg']);
			if ($pic_result3!=false){
				$data["borrow_pawn_formalities"] = $pic_result3[0]["upfiles_id"];
				$data["borrow_pawn_formalities_url"] = $pic_result3[0]["filename"];
			}	
			$data['borrow_pawn_type']=$_POST['borrow_pawn_type'];
			$data['borrow_pawn_time']=$_POST['borrow_pawn_time'];
			$data['borrow_pawn_description']=$_POST['borrow_pawn_description'];
		}
        	
	    //流转标
		elseif($data['borrow_type']=="roam"){
		   $var = array("account_min","voucher","vouch_style","borrow_account","borrow_account_use","risk");
		   $_data = post_var($var);
           $data["roam_data"] = $_data; 
        }
        
        
        if ($data['borrow_password']!=""){
            $data['borrow_password'] = md5($data['borrow_password']);
        }
	   
		$result = borrowLoanClass::Add($data);
		if ($result>0){
			$msg = array($MsgInfo["borrow_success_msg"],"","/?user&q=code/borrow/loan&p=now");
		}else{
			$msg = array($MsgInfo[$result]);
		}
    }else{
		if ($_G["system"]["con_borrow_step_status"]==1){
			
			require_once(ROOT_PATH."modules/borrow/borrow.amount.php");
			$result = borrowLoanClass::CheckLoan(array("user_id"=>$_G['user_id'],"type_nid"=>$_REQUEST['type_nid']));
			$_U["type_nid"]=$_REQUEST['type_nid'];
			$type_nid=$_REQUEST['type_nid'];
			require_once("borrow.amount.php");//额度
			
			if($_U["type_nid"]=="day"){
				$borrow_type = "credit";
			}elseif($_U["type_nid"]=="roam"){
				$borrow_type = "vest";
			}else{
				$borrow_type = $_U["type_nid"];
			}			
			if($borrow_type=="worth"){
				$worth_status = borrowLoanClass::CheckWorth(array("user_id"=>$_G["user_id"]));
			}
			
			$amount = borrowAmountClass::GetAmountUsers(array("user_id"=>$_G["user_id"]));
			
			$type = array("credit"=>"信用额度","day"=>"信用额度","vouch"=>"担保额度","pawn"=>"授信额度","roam"=>"流转额度");
			if($result=="email"){
					
					header("Location:/?user&q=code/approve/email"); 
					
			}elseif($type_nid=="worth" && $amount['worth'] ==0 ){
				
				$msg = array("您的净值额度不够不能发布净值标","","/borrow/index.html");
				
			}elseif($type_nid=="worth" && $worth_status=="1" ){
				
				$msg = array("您的净值标还没复审，请等待管理员审核","","/borrow/index.html");
				
			}elseif($type_nid!="worth" && $type_nid!="second" && $amount[$borrow_type."_use"]==0){
				if ($result=="realname"){//实名认证
					
					$msg = array("$type[$type_nid]为0,请先申请","","/?user&q=code/borrow/loan&p=loan_realname&type=".$type_nid);
								
				}elseif ($result=="phone"){//手机认证	
					$msg = array("$type[$type_nid]为0,请先申请","","/?user&q=code/borrow/loan&p=loan_phone&type=".$type_nid);
							
				}elseif ($result=="info"){//基本信息填写
					$msg = array("$type[$type_nid]为0,请先申请","","/?user&q=code/borrow/loan&p=info&type=".$type_nid);
					
				}elseif ($result=="amount"){//额度申请信息填写	
					$msg = array("$type[$type_nid]为0,请先申请","","/?user&q=code/borrow/loan_amount&type=".$type_nid);
					
				}elseif ($result=="approve"){//认证信息填写
					$msg = array("$type[$type_nid]为0,请先申请","","/?user&q=code/borrow/loan&p=approve");
					
				}elseif($result=="new"){  		
					//借款类型
					$_U["borrow_type_result"] = borrowTypeClass::GetTypeOne(array("nid"=>$_REQUEST["type_nid"]));
					if ($_U["borrow_type_result"]==false){
						$msg = array("您的操作有误");
					}
					//用户额度
					require_once("borrow.amount.php");//额度
					$_U["users_amount_result"] = borrowAmountClass::GetAmountUsers(array("user_id"=>$_G["user_id"]));
				   $_G["site_nid"] = "borrow";
					$template = "users_loan_new.html";//开始发布借款
				}
			}else{
				//借款类型
					$_U["borrow_type_result"] = borrowTypeClass::GetTypeOne(array("nid"=>$_REQUEST["type_nid"]));
					if ($_U["borrow_type_result"]==false){
						$msg = array("您的操作有误");
					}
					//用户额度
					require_once("borrow.amount.php");//额度
					$_U["users_amount_result"] = borrowAmountClass::GetAmountUsers(array("user_id"=>$_G["user_id"]));
				   $_G["site_nid"] = "borrow";
					$template = "users_loan_new.html";//开始发布借款
			}
		}else{
		
			//借款类型
			$_U["borrow_type_result"] = borrowTypeClass::GetTypeOne(array("nid"=>$_REQUEST["type_nid"]));
			if ($_U["borrow_type_result"]==false){
				$msg = array("您的操作有误");
			}
			//用户额度
			require_once("borrow.amount.php");//额度
			$_U["users_amount_result"] = borrowAmountClass::GetAmountUsers(array("user_id"=>$_G["user_id"]));
		    $_G["site_result"]["nid"] = "borrow";
			$template = "users_loan_new.html";//开始发布借款
		
       } 
    }

}


//添加基本信息
elseif ($_REQUEST['p'] == "info"){	
	if ($_POST['submit']!=""){
			require_once(ROOT_PATH."/modules/rating/rating.class.php");
			require_once(ROOT_PATH."/modules/credit/credit.class.php");
			$var = array("sex","marry","children","income","birthday","edu","is_car","address","school_year","school","house","phone","jiguanprovince","jiguancity","hukouprovince","hukoucity","area","realname","card_id","phone_num","old_name","qq","post_id","house_status","borrow_password","car_status","nowhouse","houseaddress","housess","shouru","live_city");
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
			$_G["site_nid"] = "borrow";
	}else{
		$template = "users_loan_info.html";//基本信息填写
		$_G["site_nid"] = "borrow";
	}
}
//撤标的借款
elseif ($_REQUEST['p'] == "cancel"){
    require_once("borrow.cancel.php");//类名
    $data['borrow_nid'] = $_REQUEST['borrow_nid'];
	$data['user_id'] = $_G['user_id'];
	$result = borrowCancelClass::UserCancel($data);
	if ($result>0){
		$msg = array($MsgInfo["borrow_cancel_success"],"","index.php?user&q=code/borrow/loan&p=now");
	}elseif (IsExiest($MsgInfo[$result])!=""){
		$msg = array($MsgInfo[$result]);
	}else{
		$msg = array("撤销失败，请跟管理员联系");
	}
}


elseif ($_REQUEST['p'] == "repays"){
	require_once("borrow.repay.php");
    $result = array();
    if ($_REQUEST['step']==""){
		$data['user_id'] = $_G['user_id'];
        $data["repay_id"] = $_REQUEST['repay_id'];
        $data["paypassword"] = $_REQUEST['paypassword'];
        if (md5($data['paypassword'])!= $_G["user_result"]["paypassword"]){
             $result = "borrow_repay_paypassword_error";
        }else{
		     $result = borrowRepayClass::RepayInfo($data);
        }
		if (!is_array($result)){
            $result = array("result"=>2,"name"=> $MsgInfo[$result]);
		}
	}else{
	     $data['repay_id'] = $_REQUEST['repay_id']; 
	     $data['step'] = $_REQUEST['step']; 
	     $data['key'] = $_REQUEST['key']; 
	     $data['user_id'] = $_G['user_id']; 
         $result = borrowRepayClass::RepayInfo($data); 
         if (!is_array($result)){
            $result = array("result"=>2,"name"=>$MsgInfo[$result]);
         }
	}
    $_U['repay_result'] = $result;
    $msg = "";
    $template = "users_loan_view.html";//基本信息填写
}
//提前还款
elseif ($_REQUEST['p'] == "repays_advance"){
	require_once("borrow.repay_advance.php");
     $result = array();
    if ($_REQUEST['step']==""){
		$data['user_id'] = $_G['user_id'];
        $data["borrow_nid"] = $_REQUEST['borrow_nid'];
        $data["paypassword"] = $_REQUEST['paypassword'];
        if (md5($data['paypassword'])!= $_G["user_result"]["paypassword"]){
             $_result = "borrow_repay_paypassword_error";
        }else{
		     $_result = borrowRepayAdvanceClass::RepayAdvanceInfo($data);
        }
		if (is_array($_result)){
		     $result = $_result;
        }else{
            $result = array("result"=>2,"name"=> $MsgInfo[$_result]);
		}
	}else{
	     $data['borrow_nid'] = $_REQUEST['borrow_nid']; 
	     $data['period'] = $_REQUEST['period']; 
	     $data['step'] = $_REQUEST['step']; 
	     $data['key'] = $_REQUEST['key']; 
	     $data['user_id'] = $_G['user_id']; 
         $result = borrowRepayAdvanceClass::RepayAdvanceInfo($data); 
         if (!is_array($result)){
              $result = array("result"=>2,"name"=>$MsgInfo[$result]);
         }
	}
    $_U['repay_result'] = $result;
    $msg = "";
    $template = "users_loan_view_advance.html";//基本信息填写
}

//逾期还款
elseif ($_REQUEST['p'] == "repays_late"){
	require_once("borrow.repay_late.php");
    $result = array();
    if ($_REQUEST['step']==""){
		$data['user_id'] = $_G['user_id'];
        $data["repay_id"] = $_REQUEST['repay_id'];
        $data["paypassword"] = $_REQUEST['paypassword'];
        if (md5($data['paypassword'])!= $_G["user_result"]["paypassword"]){
             $result = "borrow_repay_paypassword_error";
        }else{
		     $result = borrowRepayLateClass::RepayLateInfo($data);
        }
		if (!is_array($result)){
            $result = array("result"=>2,"name"=> $MsgInfo[$result]);
		}
	}else{
	     $data['repay_id'] = $_REQUEST['repay_id']; 
	     $data['step'] = $_REQUEST['step']; 
	     $data['key'] = $_REQUEST['key']; 
	     $data['user_id'] = $_G['user_id']; 
         $result = borrowRepayLateClass::RepayLateInfo($data); 
         if (!is_array($result)){
            $result = array("result"=>2,"name"=>$MsgInfo[$result]);
         }
	}
    $_U['repay_result'] = $result;
    $msg = "";
    $template = "users_loan_view.html";//基本信息填写
}

//实名认证
elseif ($_REQUEST['p'] == "realname"){	
	require_once(ROOT_PATH."modules/approve/approve.class.php");	
	if ($_POST['realname']!=""){
		$var = array("realname","card_id");
		$data = post_var($var);
        $data['realname'] = iconv("UTF-8","GBK",$data['realname']);
		$data['user_id'] = $_G['user_id'];		
		$result = approveClass::AddRealname($data);	
       echo 1;
       exit;
	}else{
	   $template = "users_loan_ajax_realname.html";//基本信息填写
    }
}

//添加工作信息
elseif ($_REQUEST['p'] == "view_roam"){	
    if (!empty($_POST["paypassword"])){
        require_once("borrow.roam.php");//类名
        $data['user_id'] = $_G['user_id'];
        $data["borrow_nid"] = $_REQUEST['borrow_nid'];
        $data["paypassword"] = $_POST['paypassword'];
        $data["portion"] = $_POST['num'];
        $data["contents"] = $_POST['contents'];
		if($_POST['valicode']!=$_SESSION['valicode']){
			$_result = "valicode_error";
		}elseif (md5($data['paypassword'])!= $_G["user_result"]["paypassword"]){
             $_result = "borrow_roam_paypassword_error";
        }elseif (empty($_G["user_result"]["paypassword"])){
			$_result = "borrow_roam_paypassword_error";
		}else{
		     $_result = borrowRoamClass::AddRoam($data);
        }
		if (is_array($_result)){
		     $result = 1;
        }else{
            $result = $MsgInfo[$_result];
		}
        echo $result;
        exit;
    }
	$template = "users_loan_ajax_roam.html";//基本信息填写
}


/* 上传资料的审核*/
//添加工作信息
elseif ($_REQUEST['p'] == "att"){	
	$template = "users_loan_ajax_att.html";//基本信息填写
}

//实名认证
elseif ($_REQUEST['p'] == "approve"){	
	$template = "users_loan_approve.html";//基本信息填写
	$_G["site_nid"] = "borrow";
}
//手机认证
elseif ($_REQUEST['p'] == "phone"){	
	$template = "users_loan_ajax_phone.html";//基本信息填写
}
//投标页面
elseif ($_REQUEST['p'] == "tender"){	
	$template = "detail.html";
}

//还款详情
elseif ($_REQUEST['p'] == "views"){	
    
}


//普通还款详情
elseif ($_REQUEST['p'] == "view"){	
    $template = "users_loan_view.html";//开始发布借款
}
//提前还款详情
elseif ($_REQUEST['p'] == "view_advance"){	
    $template = "users_loan_view_advance.html";//开始发布借款
}
//提前还款详情
elseif ($_REQUEST['p'] == "loan_realname"){	
    $template = "users_loan_realname.html";//开始发布借款
	$_G["site_nid"] = "borrow";
}
//提前还款详情
elseif ($_REQUEST['p'] == "loan_phone"){	
    $template = "users_loan_phone.html";//开始发布借款
	$_G["site_nid"] = "borrow";
}



//投标页面
elseif ($_REQUEST['p'] == "tender"){	
	$template = "detail.html";
}


 
//正在招标的借款
elseif ($_REQUEST['p']=="now"){
    
}
 
//工作信息 财务信息 房子信息 联系信息
elseif ($_REQUEST['p']=="work" || $_REQUEST['p']=="company"|| $_REQUEST['p']=="finance"|| $_REQUEST['p']=="contact"|| $_REQUEST['p']=="houses"){
    $template = "users_loan_info.html";
	$_G["site_nid"] = "borrow";
}

//正在招标的借款
elseif ($_REQUEST['p']=="count"){
    
}
//还款安排
elseif ($_REQUEST['p']=="plan"){
    $result = borrowCountClass::GetUsersRepayCount(array("user_id"=>$_G['user_id']));
    $_U['borrow_plan'] = $result;
	
}
//正在招标的借款
elseif ($_REQUEST['p']=="repay"){
    
}else{
    $template = "error.html";//开始发布借款
}	
?>