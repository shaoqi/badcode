<?
/******************************
 * $File: borrow.php
 * $Description: 借款类文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
 

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$_A['list_purview']["borrow"]["name"] = "借款管理";
$_A['list_purview']["borrow"]["result"]["borrow_manage"] = array("name"=>"借款管理","url"=>"code/borrow/manage","title"=>"可查看前台用户所有的借款");
$_A['list_purview']["borrow"]["result"]["borrow_first"] = array("name"=>"初审借款","url"=>"code/borrow/first","title"=>"对提交的借款进行初审、编辑、撤消、延长等操作和管理");
$_A['list_purview']["borrow"]["result"]["borrow_full"] = array("name"=>"满标借款","url"=>"code/borrow/full","title"=>"对满标借款进行审核；查看还款中和已还完借款");
$_A['list_purview']["borrow"]["result"]["borrow_roam"] = array("name"=>"流转标借款","url"=>"code/borrow/roam","title"=>"对流转标的初审和流转（回购）进行查看和管理");
$_A['list_purview']["borrow"]["result"]["borrow_late"] = array("name"=>"逾期借款","url"=>"code/borrow/late","title"=>"查看逾期借款、执行网站垫付、对应收账的管理");
$_A['list_purview']["borrow"]["result"]["borrow_amount"] = array("name"=>"借款额度","url"=>"code/borrow/amount","title"=>"用于查看和管理用户的各种借款额度");
$_A['list_purview']["borrow"]["result"]["borrow_repay"] = array("name"=>"还款信息","url"=>"code/borrow/repay","title"=>"用于查看所有用户的还款明细");
$_A['list_purview']["borrow"]["result"]["borrow_recover"] = array("name"=>"收款信息","url"=>"code/borrow/recover","title"=>"用于查看所有用户的收款信息");
$_A['list_purview']["borrow"]["result"]["borrow_tender"] = array("name"=>"投资信息","url"=>"code/borrow/tender","title"=>"用于查看所有用户的投资信息");
$_A['list_purview']["borrow"]["result"]["borrow_fee"] = array("name"=>"借款费用","url"=>"code/borrow/fee","title"=>"用于配置借贷过程中产生的各种费用规则");
$_A['list_purview']["borrow"]["result"]["borrow_type"] = array("name"=>"标种类型","url"=>"code/borrow/type","title"=>"用于配置和管理所有借款标种的借款规则");
$_A['list_purview']["borrow"]["result"]["borrow_style"] = array("name"=>"还款方式","url"=>"code/borrow/style","title"=>"用于查看和管理所有借款的还款方式");
$_A['list_purview']["borrow"]["result"]["loan_pawn"] = array("name"=>"发布抵押标","url"=>"code/borrow/loan&type_nid=pawn","title"=>"发布抵押标");
$_A['list_purview']["borrow"]["result"]["loan_roam"] = array("name"=>"发布流转标","url"=>"code/borrow/loan&type_nid=roam","title"=>"发布流转标");
require_once("borrow.class.php");
require_once("borrow.reverify.php");
require_once("borrow.excel.php");

$_A['borrow_amount_type'] = $borrow_amount_type;
/**
 * 如果类型为空的话则显示所有的文件列表
**/

if(file_exists(ROOT_PATH."modules/borrow/borrow.".$_A['query_type'].".admin.php")){
	echo ROOT_PATH."modules/borrow/borrow.".$_A['query_type'].".admin.php";
    require_once(ROOT_PATH."modules/borrow/borrow.".$_A['query_type'].".admin.php");
}

//查看
elseif ($_A['query_type'] == "view"){
		$data['borrow_nid'] = $_REQUEST['borrow_nid'];
		$result = borrowClass::GetView($data);
		if (!is_array($result)){
			$msg = array($MsgInfo[$result]);
		}else{
			$_A['borrow_result'] = $result;
		}
}

//初审并编辑
elseif ($_A['query_type'] == "first" ){
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
		$result = borrowClass::GetView($data);
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
           if ($borrow_result["borrow_type"]=="roam"){
                require_once("borrow.roam.php");
                $var = array("name","borrow_use","borrow_period","borrow_apr","borrow_contents","borrow_nid");
    			$data = post_var($var);
                
                $_var = array("borrow_nid","voucher","vouch_style","borrow_account","borrow_account_use","risk","upfiles_id");
                $_data = post_var($_var);
                $result = borrowRoamClass::UpdateRoam($_data);
                $file_data["id"] = $_POST["upfiles_id"];
                $file_data["contents"] = $_POST["upfiles_content"];
                adminClass::UpdateUpfiles($file_data);
           }else{
    			$var = array("name","borrow_use","borrow_period","borrow_style","borrow_apr","borrow_contents","borrow_day","borrow_valid_time","borrow_nid","tender_account_min","tender_account_max","order","borrow_pawn_app","borrow_pawn_auth","borrow_pawn_formalities","borrow_pawn_type","borrow_pawn_value","borrow_pawn_time","borrow_pawn_xin","borrow_pawn_description");
    			$data = post_var($var);
    			
    			$_G['upimg']['code'] = "borrow";
    			$_G['upimg']['type'] = "diya";
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
    			
    		}
			if ($borrow_result['status']!=0){
				$msg = array("非初审借款，不能进行修改","",$_A['query_url'].$_A['site_url']);
			}
			$result = borrowLoanClass::Update($data);
			if ($result>0){
			     if ($borrow_result["borrow_type"]=="roam"){
			         $msg = array("修改成功","",$_A['query_url']."/roam&status_nid=first");
			     }else{
				$msg = array("修改成功","",$_A['query_url']."/".$_A['query_type']);
                }
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}else{
            require_once("borrow.type.php");  
			$data['borrow_nid'] = $_REQUEST['first_edit'];
			$result = borrowClass::GetView($data);
			if (!is_array($result)){
				$msg = array($MsgInfo[$result]);
			}else{
			    $type_result =  borrowTypeClass::GetTypeOne(array("nid"=>$result['borrow_type']));
				$_A['borrow_result'] = $result;
				$_A['borrow_type_result'] = $type_result;
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
//借款管理
elseif ($_A['query_type'] == "manage" ){
    check_rank("borrow_manage");//检查权限
}
//流标标
elseif ($_A['query_type'] == "roam" ){
    check_rank("borrow_roam");//检查权限
}
//转让标
elseif ($_A['query_type'] == "change" ){
    check_rank("borrow_change");//检查权限
	if($_REQUEST['_type']!='' && $_REQUEST['_type']=='excel'){
		$data['page'] = $_REQUEST['page'];
		$data['epage'] = $_REQUEST['epage'];
		$data['status'] = $_REQUEST['status'];
		$data['username'] = $_REQUEST['username'];
		$data['dotime1'] = $_REQUEST['dotime1'];
		$data['dotime2'] = $_REQUEST['dotime2'];
		borrowexcel::borrowChangeList($data);
		exit;
	}
}

//满标复审
elseif ($_A['query_type'] == "full" ){
     check_rank("borrow_full");//检查权限
}

//还款
elseif ($_A['query_type'] == "repay"){
     check_rank("borrow_repay");//检查权限
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

//投资
elseif ($_A['query_type'] == "tender" ){
     check_rank("borrow_tender");//检查权限
	if ($_REQUEST['id']!=""){
		$_A['borrow_tender_result'] = borrowTenderClass::GetTenderOne(array("id"=>$_REQUEST['id']));
	}

}

//回收
elseif ($_A['query_type'] == "recover"){
     check_rank("borrow_recover");//检查权限
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
     check_rank("borrow_flag");//检查权限
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
     check_rank("borrow_tool");//检查权限
	if ($_REQUEST['key']!=""){
		require_once("borrow.tool.php");
		$data['key'] = $_REQUEST['key'];
		$result = borrowtoolClass::Check($data);
		
		echo json_encode($result);
		
		exit;
	}
}


?>