<?
/******************************
 * $File: borrow.php
 * $Description: 借款说明用户中心文件
 * $Author: ahui 
 * $Time:2010-08-09
 * $Update:None 
 * $UpdateDate:None 
 * Copyright(c) 2012 by dycms.net. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$_A['list_purview']["borrow"]["name"] = "借款管理";
$_A['list_purview']["borrow"]["result"]["borrow_first"] = array("name"=>"初审借款","url"=>"code/borrow/first");
$_A['list_purview']["borrow"]["result"]["borrow_full"] = array("name"=>"满标借款","url"=>"code/borrow/full");
$_A['list_purview']["borrow"]["result"]["borrow_late"] = array("name"=>"逾期借款","url"=>"code/borrow/late");
$_A['list_purview']["borrow"]["result"]["borrow_amount"] = array("name"=>"借款额度","url"=>"code/borrow/amount");
//$_A['list_purview']["borrow"]["result"]["borrow_flag"] = array("name"=>"借款属性","url"=>"code/borrow/flag");
$_A['list_purview']["borrow"]["result"]["borrow_repay"] = array("name"=>"还款信息","url"=>"code/borrow/repay");
$_A['list_purview']["borrow"]["result"]["borrow_recover"] = array("name"=>"收款信息","url"=>"code/borrow/recover");
//$_A['list_purview']["borrow"]["result"]["borrow_tool"] = array("name"=>"检测工具","url"=>"code/borrow/tool");

require_once("borrow.class.php");
require_once("borrow.reverify.php");

$_A['borrow_amount_type'] = $borrow_amount_type;
/**
 * 如果类型为空的话则显示所有的文件列表
**/


	
if ($_A['query_type'] == "first" ){
	check_rank("borrow_first");
	if ($_REQUEST['check']!=""){
		if (isset($_POST['borrow_nid']) && $_POST['borrow_nid']!=""){
			$msg = check_valicode();
			if ($_POST['verify_remark']==""){
				$msg = array("审核备注不能为空","",$_A['query_url_all']);
			}
			if ($msg==""){
				$var = array("borrow_nid","status","verify_remark");
				$data = post_var($var);
				
				$result = borrowClass::Verify($data);
				if ($result>0){
					$msg = array($MsgInfo["borrow_verify_success"],"",$_A['query_url_all']);
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
		}else{
			$data['borrow_nid'] = $_REQUEST['check'];
			$result = borrowClass::GetOne($data);
			if (!is_array($result)){
				$msg = array($MsgInfo[$result]);
			}elseif ($result['status']!=0){
				$msg = array($MsgInfo["borrow_not_exiest"]);
			}else{
				$_A['borrow_result'] = $result;
			}
		}
	}elseif ($_REQUEST['view']!=""){
		$data['borrow_nid'] = $_REQUEST['view'];
		$result = borrowClass::GetOne($data);
		if (!is_array($result)){
			$msg = array($MsgInfo[$result]);
		}else{
			$_A['borrow_result'] = $result;
		}
	}elseif ($_REQUEST['first_edit']!=""){
		$data['borrow_nid'] = $_REQUEST['first_edit'];
		$borrow_result = borrowClass::GetOne($data);
		$_A['borrow_result'] = $borrow_result;
		if ($_POST['borrow_nid']){
			$var = array("name","borrow_use","borrow_period","borrow_style","borrow_apr","borrow_contents","borrow_day","borrow_type","borrow_valid_time","borrow_nid","about_use","about_me","about_repay","tender_account_min","tender_account_max","order","borrow_hetong","tender_hetong");
			$data = post_var($var);
			if ($borrow_result['status']!=0){
				$msg = array("非初审借款，不能进行修改","",$_A['query_url'].$_A['site_url']);
			}
			$result = borrowClass::Update($data);
			if ($result>0){
				$msg = array("修改成功","",$_A['query_url'].$_A['site_url']."/".$_A['query_type']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}else{
			$data['borrow_nid'] = $_REQUEST['first_edit'];
			$result = borrowClass::GetOne($data);
			if (!is_array($result)){
				$msg = array($MsgInfo[$result]);
			}else{
				$_A['borrow_result'] = $result;
			}
		}
	}elseif ($_REQUEST['cancel']!=""){
	   if ($_POST['borrow_nid']!=""){
	       
    		$data['borrow_nid'] = $_POST['borrow_nid'];
    		$data['cancel_remark'] = $_POST['cancel_remark'];
    		$data['cancel_status'] = 1;
          
    		$result = borrowClass::Cancel($data);
    		
    		if($result>0){
    			 $msg = array("撤回成功","",$_A['query_url'].$_A['site_url']."/".$_A['query_type']);
    		 }else{
    			$msg = array($MsgInfo[$result]);
    		 }
    		 
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
      }
	}elseif ($_POST['borrow_apr']!=""){
		$data['id'] = $_POST['id'];
		$data['borrow_apr'] = $_POST['borrow_apr'];
		$result=borrowClass::ActionBorrowApr($data);
		if($result>0){
			$msg = array("修改利率成功","",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
	}

}
elseif ($_A['query_type'] == "full" ){
	if ($_REQUEST['fullcheck']!=""){
		require_once("borrow.reverify.php");
		if (isset($_POST['borrow_nid']) && $_POST['borrow_nid']!=""){
		     $msg = check_valicode();
    		if ($_POST['reverify_remark']==""){
    			$msg = array("审核备注不能为空","",$_A['query_url_all']);
    		}
    		if ($msg==""){
        		$borrow_result=borrowClass::GetOne(array("borrow_nid"=>$_POST['borrow_nid']));
        		if ($borrow_result['borrow_type']==6){
        			$var = array("borrow_nid","status","reverify_remark");
        			$data = post_var($var);
        			$data['reverify_userid'] = $_G['user_id'];
        			require_once("borrow.reverifyday.php");
        			borrowreverifydayClass::ReverifyDay($data);
        			$msg = array("满标审核成功","",$_A["query_url_all"]);
        		}else{
    				$var = array("borrow_nid","status","reverify_remark");
    				$data = post_var($var);
    				$data['reverify_userid'] = $_G['user_id'];
    				if ($_REQUEST['step']==""){
    
    					$result = borrowreverifyClass::ReverifyStep0($data);
    					
    					
    					//加入管理员操作记录
    					$admin_log["user_id"] = $_G['user_id'];
    					$admin_log["code"] = "borrow";
    					$admin_log["type"] = "borrow";
    					$admin_log["operating"] = "verify_step0";
    					$admin_log["article_id"] = $result>0?$result:0;
    					$admin_log["result"] = $result>0?1:0;
    					$admin_log["content"] =  $msg[0];
    					$admin_log["data"] =  $data;
    					usersClass::AddAdminLog($admin_log);
    					
    					if ($result>0){
    						echo "第一步，正在确认是否可以审核，请不要关闭浏览器。。。<script>location.href='{$_A['query_url_all']}&fullcheck={$_REQUEST['fullcheck']}&step=1'</script>";
    					}else{
    						$msg = array($MsgInfo[$result]);
    					}
    				}
    			}
         }
		}elseif ($_REQUEST['step']==1){
			$data['borrow_nid'] =$_REQUEST['fullcheck'];
			$result = borrowreverifyClass::ReverifyStep1($data);
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "borrow";
			$admin_log["type"] = "borrow";
			$admin_log["operating"] = "verify_step1";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
			if ($result>0){
				echo "第二步，正在审核借款信息，请不要关闭浏览器。。。<script>location.href='{$_A['query_url_all']}&fullcheck={$_REQUEST['fullcheck']}&step=2&key=0'</script>";
			}else{
				$msg = array($MsgInfo[$result]);
			}
		
		}elseif ($_REQUEST['step']==2){
			$data['borrow_nid'] =$_REQUEST['fullcheck'];
			$data['key'] = $_REQUEST['key'];
			$result = borrowreverifyClass::ReverifyStep2($data);
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "borrow";
			$admin_log["type"] = "borrow";
			$admin_log["operating"] = "verify_step2";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data; 
			usersClass::AddAdminLog($admin_log);
			if ($result>0){
				$key = $data['key']+1;
				echo "第三步，正在审核投资信息，，请不要关闭浏览器。。。{$key}<script>location.href='{$_A['query_url_all']}&fullcheck={$_REQUEST['fullcheck']}&step=3&key={$key}'</script>";
			}else{
				echo "第四步，审核担保标信息，请不要关闭浏览器。。。{$key}<script>location.href='{$_A['query_url_all']}&fullcheck={$_REQUEST['fullcheck']}&step=3&key=0'</script>";
			}
		
		}elseif ($_REQUEST['step']==3){
			$data['borrow_nid'] = $_REQUEST['fullcheck'];
			$data['key'] = $_REQUEST['key'];
			$result = borrowreverifyClass::ReverifyStep3($data);
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "borrow";
			$admin_log["type"] = "borrow";
			$admin_log["operating"] = "verify_step3";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
			if ($result>0){
				$key = $_REQUEST['key']+1;
				echo "第四步，审核担保标信息，{$key}<script>location.href='{$_A['query_url_all']}&fullcheck={$_REQUEST['fullcheck']}&step=4&key={$key}'</script>";
			}else{
				echo "第五步，正在处理借款奖励信息，请不要关闭浏览器。。。{$key}<script>location.href='{$_A['query_url_all']}&fullcheck={$_REQUEST['fullcheck']}&step=4&key=0'</script>";
			}
		
		}elseif ($_REQUEST['step']==4){
			$data['borrow_nid'] = $_REQUEST['fullcheck'];
			$data['key'] = $_REQUEST['key'];
			$result = borrowreverifyClass::ReverifyStep4($data);
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "borrow";
			$admin_log["type"] = "borrow";
			$admin_log["operating"] = "verify_step4";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
			if ($result>0){
				$key = $_REQUEST['key']+1;
				echo "第五步，正在处理借款奖励信息，请不要关闭浏览器。。。{$key}<script>location.href='{$_A['query_url_all']}&fullcheck={$_REQUEST['fullcheck']}&step=5&key={$key}'</script>";
			}else{
				echo "第六步，正在还原基本信息，请不要关闭浏览器。。。{$key}<script>location.href='{$_A['query_url_all']}&fullcheck={$_REQUEST['fullcheck']}&step=5'</script>";
			}
		
		}elseif ($_REQUEST['step']==5){
			$data['borrow_nid'] = $_REQUEST['fullcheck'];
			$result = borrowreverifyClass::ReverifyStep5($data);
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "borrow";
			$admin_log["type"] = "borrow";
			$admin_log["operating"] = "verify_step5";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
			$msg = array("满标审核成功","",$_A["query_url_all"]);
		
		}else{
			$data['borrow_nid'] = $_REQUEST['fullcheck'];
			$result = borrowClass::GetOne($data);
			if (!is_array($result)){
				$msg = array($MsgInfo[$result]);
			}elseif ($result['borrow_full_status']==1){
				$msg = array($MsgInfo["borrow_fullcheck_error"]);
			}else{
				$_A['borrow_result'] = $result;
			}
		}
	}elseif ($_REQUEST['view']!=""){
		$data['borrow_nid'] = $_REQUEST['view'];
		$result = borrowClass::GetOne($data);
		if (!is_array($result)){
			$msg = array($MsgInfo[$result]);
		}else{
			$_A['borrow_result'] = $result;
		}
	}
}

elseif($_A['query_type'] == "amount"){

}

elseif($_A['query_type'] == "amount_log"){

}	
elseif($_A['query_type'] == "amountview"){

}	
/**
 * 额度手工审核管理
**/

elseif ($_A['query_type'] == "amount_apply" ){
	$_A['borrow_amount_type'] = $borrow_amount_type;
	if($_REQUEST['examine']!="" || $_REQUEST['amountview']!=""){
		if ($_POST['status']!=""){
			$msg = check_valicode();
			if ($msg==""){
				$var = array("verify_remark","verify_content","status","account","user_id","id","nid");
				$data = post_var($var);
				$data['verify_userid'] = $_G['user_id'];
				
				$result = borrowClass::CheckAmountApply($data);
				
				if ($result>0){
					$msg = array("操作成功","",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				//加入管理员操作记录
				$admin_log["user_id"] = $_G['user_id'];
				$admin_log["code"] = "borrow";
				$admin_log["type"] = "amount_apply";
				$admin_log["operating"] = "check";
				$admin_log["article_id"] = $result>0?$result:0;
				$admin_log["result"] = $result>0?1:0;
				$admin_log["content"] =  $msg[0];
				$admin_log["data"] =  $data;
				usersClass::AddAdminLog($admin_log);
			}
		}else{
			$id=isset($_REQUEST['examine'])?$_REQUEST['examine']:$_REQUEST['amountview'];
			$data["id"] = $id;
			$result = borrowClass::GetAmountApplyOne($data);
			if (is_array($result)){
				$_A["amount_apply_result"] = $result;
			}else{
				$msg = array($MsgInfo[$result],"",$_A['query_url_all']);
			}
		}
	}
	
	elseif ($_POST['type']=="user_id"){
		$var = array("username","user_id","email");
		$data = post_var($var);
		$data["limit"] = "all";
		$result = usersClass::GetUserid($data);
		if ($result>0){
			echo "<script>location.href='{$_A['query_url_all']}&user_id={$result}'</script>";
		}else{
			$msg = array($MsgInfo[$result],"",$_A['query_url_all']);
		}
	}
	
	
	elseif (isset($_POST['oprate'])){
		$msg = check_valicode();
		if ($msg==""){
			$var = array("amount_type","oprate","amount_account","content");
			$data = post_var($var);
			$data['status'] = 0;
			$data['user_id'] = $_REQUEST['user_id'];
			$result = borrowClass::AddAmountApply($data);
			if ($result>0){
				$msg = array($MsgInfo["amount_apply_update_success"],"",$_A['query_url_all']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
			$admin_log["operating"] = "add";
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "borrow";
			$admin_log["type"] = "amount_apply";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}
	
	
	elseif ($_REQUEST['user_id']!=""){
		$data["user_id"] = $_REQUEST['user_id'];
		$result = usersClass::GetUsers($data);
		if (is_array($result)){
			$_A["users_result"] = $result;
		}else{
			$msg = array("用户不存在");
		}
		
	}
	
	
}

elseif ($_A['query_type'] == "amount_type" ){
	if (isset($_POST['name'])){
		$msg = check_valicode();
		if ($msg==""){
			$var = array("name","nid","default","credit_nid","amount_type","multiples","remark");
			$data = post_var($var);
			if ($_POST['id']!=""){
				$data['id'] = $_POST['id'];
				$result = borrowClass::UpdateAmountType($data);
				if ($result>0){
					$msg = array($MsgInfo["amount_type_update_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "update";
			}else{
				$result = borrowClass::AddAmountType($data);
				if ($result>0){
					$msg = array($MsgInfo["amount_type_add_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "add";
			}
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "borrow";
			$admin_log["type"] = "amount_type";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		}
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = borrowClass::GetAmountTypeOne($data);
		if (is_array($result)){
			$_A["amount_type_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	
	}elseif($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$result = borrowClass::DelAmountType($data);
		if ($result>0){
			$msg = array($MsgInfo["amount_type_del_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "borrow";
		$admin_log["type"] = "amount_type";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}
}

elseif ($_A['query_type'] == "repay"){
	if ($_REQUEST['id']!=""){
		$data['id']=$_REQUEST['id'];
		$result = borrowClass::LateRepay($data);
		if ($result>0){
			$msg = array($MsgInfo["web_late_repay"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
	}elseif ($_REQUEST['view']!=""){
		$data['borrow_nid']=$_REQUEST['view'];
		$result = borrowClass::GetOne($data);
		$_A['borrow_result']=$result;
	}
}

elseif ($_A['query_type'] == "tender" ){
	if ($_REQUEST['id']!=""){
		$_A['borrow_tender_result'] = borrowClass::GetTenderOne(array("id"=>$_REQUEST['id']));
	}

}
elseif ($_A['query_type'] == "recover"){
	if ($_REQUEST['id']!=""){
		$data['id']=$_REQUEST['id'];
		$result = borrowClass::LateRepay($data);
		if ($result>0){
			$msg = array($MsgInfo["web_late_repay"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
	}elseif ($_REQUEST['view']!=""){
		$data['borrow_nid']=$_REQUEST['view'];
		$result = borrowClass::GetOne($data);
		$_A['borrow_result']=$result;
	}
}


elseif ($_A['query_type'] == "flag" ){
	require_once("borrow.flag.php");
	if (isset($_POST['name'])){
			$var = array("name","nid","remark","order");
			$data = post_var($var);
			$_G['upimg']['file'] = "upfile";
			$_G['upimg']['mask_status'] = 0;
			$_G['upimg']['code'] = "borrow";
			$_G['upimg']['type'] = "flag";
			$_G['upimg']['user_id'] = $_G['user_id'];
			$_G['upimg']['article_id'] = "0";
			$pic_result = $upload->upfile($_G['upimg']);
			if (is_array($pic_result)){
			$data["upfiles_id"] = $pic_result[0]['upfiles_id'];
			}
			if ($_POST['id']!=""){
				$data['id'] = $_POST['id'];
				$result = borrowflagClass::Update($data);
				if ($result>0){
					$msg = array($MsgInfo["borrow_flag_update_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "update";
			}else{
				$result = borrowflagClass::Add($data);
				if ($result>0){
					$msg = array($MsgInfo["borrow_flag_add_success"],"",$_A['query_url_all']);
				}else{
					$msg = array($MsgInfo[$result]);
				}
				$admin_log["operating"] = "add";
			}
			
			//加入管理员操作记录
			$admin_log["user_id"] = $_G['user_id'];
			$admin_log["code"] = "borrow";
			$admin_log["type"] = "flag";
			$admin_log["article_id"] = $result>0?$result:0;
			$admin_log["result"] = $result>0?1:0;
			$admin_log["content"] =  $msg[0];
			$admin_log["data"] =  $data;
			usersClass::AddAdminLog($admin_log);
		
	}elseif ($_REQUEST['borrow_nid']!=""){
		$data['borrow_nid'] = $_REQUEST['borrow_nid'];
		if ($_POST['flag']==""){
		$data['flag'] ="";
		}else{
		$data['flag'] = join(",",$_POST['flag']);
		}
		$result = borrowClass::Update($data);
		$msg = array("修改成功");
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "borrow";
		$admin_log["type"] = "borrow";
		$admin_log["operating"] = "flag";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}elseif ($_REQUEST['edit']!=""){
		$data['id'] = $_REQUEST['edit'];
		$result = borrowflagClass::GetOne($data);
		if (is_array($result)){
			$_A["borrow_flag_result"] = $result;
		}else{
			$msg = array($MsgInfo[$result]);
		}
	
	}elseif($_REQUEST['del']!=""){
		$data['id'] = $_REQUEST['del'];
		$result = borrowflagClass::Delete($data);
		if ($result>0){
			$msg = array($MsgInfo["borrow_flag_del_success"],"",$_A['query_url_all']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
		
		//加入管理员操作记录
		$admin_log["user_id"] = $_G['user_id'];
		$admin_log["code"] = "borrow";
		$admin_log["type"] = "flag";
		$admin_log["operating"] = "del";
		$admin_log["article_id"] = $result>0?$result:0;
		$admin_log["result"] = $result>0?1:0;
		$admin_log["content"] =  $msg[0];
		$admin_log["data"] =  $data;
		usersClass::AddAdminLog($admin_log);
	}
	
}


elseif ($_A['query_type'] == "tool" ){
	if ($_REQUEST['key']!=""){
		require_once("borrow.tool.php");
		$data['key'] = $_REQUEST['key'];
		$result = borrowtoolClass::Check($data);
		
		echo json_encode($result);
		
		exit;
	}
}


//担保还款
elseif ($_A['query_type'] == "repay_vouch"){
	require_once("borrow.repay_vouch.php");
	if ($_REQUEST['repay_id']=="" || $_REQUEST['borrow_nid']==""){
		$msg = array("您的操作有误");
	}else{
		$data['borrow_nid'] = $_REQUEST['borrow_nid'];
		$data['repay_id'] = $_REQUEST['repay_id'];
		$data['user_id'] = $_REQUEST['user_id'];
		if ($_REQUEST["step"]==""){
			$result =  borrowrepayvouchClass::RepayStep0($data);//获取当前用户的余额
			if ($result>0){
				echo "第一步，判断是否可以审核。。。<script>location.href='{$_A['query_url']}/repay_vouch&repay_id={$_REQUEST['repay_id']}&borrow_nid={$_REQUEST['borrow_nid']}&user_id={$_REQUEST['user_id']}&step=1&key=0'</script>";
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}elseif ($_REQUEST["step"]=="1"){
			$data['key'] = $_REQUEST['key'];
			$result =  borrowrepayvouchClass::RepayStep1($data);//获取当前用户的余额
			if ($result>0){
				$key = $_REQUEST['key']+1;
				echo "第二步，进行担保垫付。。。<script>location.href='{$_A['query_url']}/repay_vouch&repay_id={$_REQUEST['repay_id']}&borrow_nid={$_REQUEST['borrow_nid']}&user_id={$_REQUEST['user_id']}&step=1&key={$key}'</script>";
			}elseif ($result==-1){
				echo "第三步，将本息还给投资人。。。<script>location.href='{$_A['query_url']}/repay_vouch&repay_id={$_REQUEST['repay_id']}&borrow_nid={$_REQUEST['borrow_nid']}&user_id={$_REQUEST['user_id']}&step=2&key=0'</script>";
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}elseif ($_REQUEST["step"]=="2"){
			$data['key'] = $_REQUEST['key'];
			$result =  borrowrepayvouchClass::RepayStep2($data);//获取当前用户的余额
			if ($result>0){
				$key = $_REQUEST['key']+1;
				echo "第三步，将本息还给投资人。。。<script>location.href='{$_A['query_url']}/repay_vouch&repay_id={$_REQUEST['repay_id']}&borrow_nid={$_REQUEST['borrow_nid']}&user_id={$_REQUEST['user_id']}&step=2&key={$key}'</script>";
			}elseif ($result==-1){
				echo "第四步，更新借款信息。。。<script>location.href='{$_A['query_url']}/repay_vouch&repay_id={$_REQUEST['repay_id']}&borrow_nid={$_REQUEST['borrow_nid']}&user_id={$_REQUEST['user_id']}&step=3'</script>";
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}else{
			$result =  borrowrepayvouchClass::RepayStep3($data);//获取当前用户的余额
			if ($result==1){
				$msg = array("还款成功","",$_A['query_url']."/late");
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}
	}
	
}



//担保还款
elseif ($_A['query_type'] == "repay_web"){
	require_once("borrow.repay_web.php");
	if ($_REQUEST['repay_id']=="" || $_REQUEST['borrow_nid']==""){
		$msg = array("您的操作有误");
	}else{
		$data['borrow_nid'] = $_REQUEST['borrow_nid'];
		$data['repay_id'] = $_REQUEST['repay_id'];
		$data['user_id'] = $_REQUEST['user_id'];
		$data['type'] = $_REQUEST['type'];
		if ($_REQUEST["step"]==""){
			$result =  borrowrepaywebClass::RepayStep0($data);//获取当前用户的余额
			if ($result>0){
				echo "第一步，判断是否可以审核。。。<script>location.href='{$_A['query_url']}/repay_web&repay_id={$_REQUEST['repay_id']}&borrow_nid={$_REQUEST['borrow_nid']}&user_id={$_REQUEST['user_id']}&step=1&key=0&type={$_REQUEST['type']}'</script>";
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}elseif ($_REQUEST["step"]=="1"){
			$data['key'] = $_REQUEST['key'];
			$result =  borrowrepaywebClass::RepayStep1($data);//获取当前用户的余额
			if ($result>0){
				$key = $_REQUEST['key']+1;
				echo "第二步，将本息还给投资人。。。<script>location.href='{$_A['query_url']}/repay_web&repay_id={$_REQUEST['repay_id']}&borrow_nid={$_REQUEST['borrow_nid']}&user_id={$_REQUEST['user_id']}&step=1&key={$key}&type={$_REQUEST['type']}'</script>";
			}elseif ($result==-1){
				echo "第三步，更新借款信息。。。<script>location.href='{$_A['query_url']}/repay_web&repay_id={$_REQUEST['repay_id']}&borrow_nid={$_REQUEST['borrow_nid']}&user_id={$_REQUEST['user_id']}&step=2&type={$_REQUEST['type']}'</script>";
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}else{
			$result =  borrowrepaywebClass::RepayStep2($data);//获取当前用户的余额
			if ($result==1){
				$msg = array("还款成功","",$_A['query_url']."/late");
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}
	}
	
}
?>