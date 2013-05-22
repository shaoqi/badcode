<?php
/******************************
 * $File: borrow.amount.loan.admin.php
 * $Description: 借款额度后台管理
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

require_once("borrow.loan.php");
require_once(ROOT_PATH."modules/users/users.class.php");
if ($_REQUEST['p'] == "verify"){
	if (isset($_POST['borrow_nid']) && $_POST['borrow_nid']!=""){
		$msg = check_valicode();
		if ($_POST['verify_remark']==""){
			$msg = array("审核备注不能为空");
		}
		if ($msg==""){
			$_verify["user_id"] = $_G['user_id'];
            $_verify["borrow_nid"] = $_POST['borrow_nid'];
            $_verify["status"] = $_POST['status'];
            $_verify["remark"] = $_POST['verify_remark'];
            $_verify["contents"] = $_POST['verify_contents'];
			$result = borrowLoanClass::VerifyLoan($_verify);
			if ($result>0){
				$msg = array($MsgInfo["borrow_verify_success"],"",$_A['query_url']."/first");
			}else{
				$msg = array($MsgInfo[$result]);
			}
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "borrow";
			$admin_log["type"] = "borrow";
			$admin_log["operating"] = "verify";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
    }
}

//满标复审
elseif ($_REQUEST['p'] == "reverify"){
	require_once("borrow.reverify.php");
    if ($_REQUEST['step']==""){
		if ($_POST['remark']==""){
			$msg = array("审核备注不能为空");
		}else{
			if ($_REQUEST['step']==""){
        		$borrow_result=borrowClass::GetOne(array("borrow_nid"=>$_POST['borrow_nid']));
    			$var = array("borrow_nid","status","remark","contents");
    			$data = post_var($var);
    			$data['reverify_userid'] = $_G['user_id'];
                $data["step"] = 0;
				$result = borrowReverifyClass::ReverifyInfo($data);
                
				//加入管理员操作记录
				$admin_log["user_id"] = $_G['user_id'];
				$admin_log["code"] = "borrow";
				$admin_log["type"] = "borrow";
				$admin_log["operating"] = "reverify";
				$admin_log["article_id"] = $result>0?$result:0;
				$admin_log["result"] = $result>0?1:0;
				$admin_log["content"] =  $msg[0];
				$admin_log["data"] =  $data;
				usersClass::AddAdminLog($admin_log);
				if (is_array($result)){
				    echo "<script>location.href='{$_A['query_url']}/loan&p=reverify&step=1&borrow_nid={$data['borrow_nid']}'</script>";
                }else{
					$msg = array($MsgInfo[$result]);
				}
			}
        }
	}else{
	     $data['borrow_nid'] = $_REQUEST['borrow_nid']; 
	     $data['step'] = $_REQUEST['step']; 
	     $data['key'] = $_REQUEST['key']; 
         $result = borrowreverifyClass::ReverifyInfo($data);
         if (is_array($result)){
             if ($result['result']==1){
                echo "<script>location.href='{$_A['query_url']}/loan&p=reverify&step={$result['step']}&key={$result['key']}&borrow_nid={$data['borrow_nid']}'</script>";
             }elseif ($result['result']==2){
                echo "<script>location.href='{$_A['query_url']}/loan&p=repays&user_id={$result['user_id']}&step={$result['step']}&key={$result['key']}&repay_id={$result['repay_id']}'</script>";
             }else{
                
                $msg = array("满标复审成功","","{$_A['query_url']}/view&borrow_nid=".$data['borrow_nid']);
             }
         }else{
            	$msg = array($MsgInfo[$result]);
         }
        
	}
}
//还款
elseif ($_REQUEST['p'] == "repays"){
	require_once("borrow.repay.php");
    $result = array();
    if ($_REQUEST['step']==""){
		$data['user_id'] = $_REQUEST['user_id'];
        $data["repay_id"] = $_REQUEST['repay_id'];
        $result = borrowRepayClass::RepayInfo($data);
		if (is_array($result)){
		    echo "<script>location.href='{$_A['query_url']}/loan&p=repays&step=1&user_id={$data["user_id"]}&repay_id={$data["repay_id"]}'</script>";
        }else{
			$msg = array($MsgInfo[$result]);
		}
	}else{
	     $data['repay_id'] = $_REQUEST['repay_id']; 
	     $data['step'] = $_REQUEST['step']; 
	     $data['key'] = $_REQUEST['key']; 
	     $data['user_id'] = $_REQUEST['user_id']; 
         $result = borrowRepayClass::RepayInfo($data); 
          if (is_array($result)){
             if ($result['result']==1){
                echo "<script>location.href='{$_A['query_url']}/loan&p=repays&user_id={$data["user_id"]}&step={$result['step']}&key={$result['key']}&repay_id={$data['repay_id']}'</script>";
             }else{
                
                $msg = array("秒标自动还款","","{$_A['query_url']}/full&status_nid=repay_yes");
             }
         }else{
            	$msg = array($MsgInfo[$result]);
         }
	}
}

//撤标
elseif ($_REQUEST['p'] == "cancel"){
    require_once("borrow.cancel.php");
    if ($_REQUEST['step']==""){
		if ($_POST['remark']==""){
			$msg = array("审核备注不能为空");
		}else{
			if ($_REQUEST['step']==""){
        		$borrow_result=borrowClass::GetOne(array("borrow_nid"=>$_POST['borrow_nid']));
    			$var = array("borrow_nid","remark","contents");
    			$data = post_var($var);
    			$data['cancel_userid'] = $_G['user_id'];
                $data["step"] = 0;
                $data['status'] = 6;
				$result = borrowCancelClass::CancelInfo($data);
				//加入管理员操作记录
				$admin_log["user_id"] = $_G['user_id'];
				$admin_log["code"] = "borrow";
				$admin_log["type"] = "borrow";
				$admin_log["operating"] = "cancel";
				$admin_log["article_id"] = $result>0?$result:0;
				$admin_log["result"] = $result>0?1:0;
				$admin_log["content"] =  $msg[0];
				$admin_log["data"] =  $data;
				usersClass::AddAdminLog($admin_log);
				if (is_array($result)){
				    echo "<script>location.href='{$_A['query_url']}/loan&p=cancel&step=1&borrow_nid={$data['borrow_nid']}&key=0'</script>";
                }else{
					$msg = array($MsgInfo[$result]);
				}
			}
        }
	}else{
	     $data['borrow_nid'] = $_REQUEST['borrow_nid']; 
	     $data['step'] = $_REQUEST['step']; 
	     $data['key'] = $_REQUEST['key']; 
         $result = borrowCancelClass::CancelInfo($data);
         if (is_array($result)){
             if ($result['result']==1){
                echo "<script>location.href='{$_A['query_url']}/loan&p=cancel&step={$result['step']}&key={$result['key']}&borrow_nid={$data['borrow_nid']}'</script>";
             }else{
                $msg = array("撤标成功，此标将变成流标","","{$_A['query_url']}/first&status_nid=over");
             }
         }else{
            	$msg = array($MsgInfo[$result]);
         }
        
	}
}

//网站垫付
elseif ($_REQUEST['p'] == "webpay"){
	require_once("borrow.repay_web.php");
    if ($_REQUEST['step']==""){
		if ($_POST['remark']==""){
			$msg = array("审核备注不能为空");
		}else{
			if ($_REQUEST['step']==""){
    			$var = array("borrow_nid","remark","contents");
    			$data = post_var($var);
    			$data['reverify_userid'] = $_G['user_id'];
                $data['repay_id'] = $_REQUEST['id'];
                $data["step"] = 0;
				$result = borrowRepayWebClass::RepayWebInfo($data);
				//加入管理员操作记录
				$admin_log["user_id"] = $_G['user_id'];
				$admin_log["code"] = "borrow";
				$admin_log["type"] = "borrow";
				$admin_log["operating"] = "webpay";
				$admin_log["article_id"] = $result>0?$result:0;
				$admin_log["result"] = $result>0?1:0;
				$admin_log["content"] =  $msg[0];
				$admin_log["data"] =  $data;
				usersClass::AddAdminLog($admin_log);
				if (is_array($result)){
				    echo "<script>location.href='{$_A['query_url']}/loan&p=webpay&step=1&key={$result['key']}&repay_id={$data['repay_id']}'</script>";
                }else{
					$msg = array($MsgInfo[$result]);
				}
			}
        }
	}else{
	     $data['repay_id'] = $_REQUEST['repay_id']; 
	     $data['step'] = $_REQUEST['step']; 
	     $data['key'] = $_REQUEST['key']; 
         $result = borrowRepayWebClass::RepayWebInfo($data);
         if (is_array($result)){
             if ($result['result']==1){
                echo "<script>location.href='{$_A['query_url']}/loan&p=webpay&step={$result['step']}&key={$result['key']}&repay_id={$data['repay_id']}'</script>";
             }else{
                $msg = array("网站垫付成功","","{$_A['query_url']}/late&late_type=repay");
             }
         }else{
            	$msg = array($MsgInfo[$result]);
         }
        
	}
}
//借款编辑
elseif ($_REQUEST['p'] == "edit"){
    $data['borrow_nid'] = $_REQUEST['borrow_nid'];
	$borrow_result = borrowClass::GetOne($data);
	$_A['borrow_result'] = $borrow_result;
	if ($_POST['borrow_nid']){
		$var = array("name","borrow_use","borrow_period","borrow_style","borrow_apr","borrow_contents","borrow_day","borrow_type","borrow_valid_time","borrow_nid","about_use","about_me","about_repay","tender_account_min","tender_account_max","order","borrow_hetong","tender_hetong");
		$data = post_var($var);
		if ($borrow_result['status_nid']!="first"){
			$msg = array("非初审借款，不能进行修改","",$_A['query_url']);
		}
		$result = borrowLoanClass::Update($data);
		if ($result>0){
			$msg = array("修改成功","",$_A['query_url']."/".$_A['query_type']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
   }
}


//延长有效期
elseif ($_REQUEST['p'] == "lateday"){
    $data['borrow_nid'] = $_REQUEST['borrow_nid'];
	$borrow_result = borrowClass::GetView($data);
	if ($_POST['borrow_nid']){
		if ($borrow_result['status_nid']!="late"){
			$msg = array("只有已过期才能进行修改","",$_A['query_url']."/first&status_nid=late");
		}
        $data['days'] = $_POST['days'];
		$result = borrowLoanClass::UpdateLateday($data);
		if ($result>0){
			$msg = array("修改成功","",$_A['query_url']."/first&status_nid=late");
		}else{
			$msg = array($MsgInfo[$result]);
		}
        //加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "borrow";
		$admin_log["type"] = "laon";
		$admin_log["operating"] = "lateday";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
   }
}
elseif($_A['query_type'] == "loan" ){
	
    if (isset($_POST['username']) && $_POST["username"]!=""){
        $var = array("name","borrow_type","borrow_use","borrow_password","account","borrow_period","borrow_apr","borrow_style","borrow_contents","borrow_valid_time","tender_account_min","tender_account_max","award_status","award_scale","award_account","award_false","pawnins","valicode","continued_status","continued_min");
		$data = post_var($var);
        if($data['continued_status']==1){
            $data['continued']=$_POST['continued_1'];
        }
        if($data['continued_status']==2){
            $data['continued']=$_POST['continued_2'];
        }
		$username = $_POST['username'];
		$result = usersClass::GetUsers(array("username"=>$username));
		$data['user_id'] = $result['user_id'];	
        	
	    //流转标
		if($data['borrow_type']=="roam"){
		   $var = array("account_min","voucher","vouch_style","borrow_account","borrow_account_use","risk");
		   $_data = post_var($var);
           $data["roam_data"] = $_data; 
        }
        
        
        if ($data['borrow_password']!=""){
            $data['borrow_password'] = md5($data['borrow_password']);
        }
		// 债权流转
		$data['sell'] = $_POST['sell'];
		$result = borrowLoanClass::Add($data);
		if ($result>0){
			$msg = array($MsgInfo["borrow_success_msg"],"",$_A['query_url']."/".$_A['query_type']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
    }else{	
			//借款类型
			require_once("borrow.type.php");//额度
			$_A["borrow_type_result"] = borrowTypeClass::GetTypeOne(array("nid"=>$_REQUEST["type_nid"]));
			if ($_A["borrow_type_result"]==false){
				$msg = array("您的操作有误");
			}
			//用户额度
			require_once("borrow.amount.php");//额度
			$_A["users_amount_result"] = borrowAmountClass::GetAmountUsers(array("user_id"=>$_G["user_id"]));
       
    }

}

?>