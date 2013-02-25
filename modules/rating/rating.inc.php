<?php
if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

include_once("rating.class.php");
$valicode = isset($_POST['valicode'])?$_POST['valicode']:"";

$url = $_U['query_url']."/{$_U['query_type']}";
if (isset($_G['query_string'][2])){
	$url .= "&".$_G['query_string'][2];
}
if (isset($_POST['valicode']) && $valicode!=$_SESSION['valicode']){
		
		$msg = array("验证码错误","",$url);
}else{
	$_SESSION['valicode'] = "";
	//密码保护功能
	if  ($_U['query_type'] == "info" || $_U['query_type'] == "basic"){		
		if (isset($_POST['submit'])){			
			$var = array("children","birthday","is_car","address","school_year","school","house","province","city","area","realname","card_id","phone","sex","marry","edu","income","remark","rating_birthday_year","rating_birthday_mouth","rating_birthday_day");
			$data = post_var($var);
			if($_REQUEST['type']=="base"){
				$data['realname']=iconv("UTF-8","gb2312",$data['realname']);
			}
			$data['user_id'] = $_G['user_id'];
			$data['status'] = 1;
			$result = ratingClass::GetInfoOne($data);
			if (is_array($result)){
				$_result = ratingClass::UpdateInfo($data);
			}else{
				$_result = ratingClass::AddInfo($data);
			}
			if ($_result == true){
				$credit_log['user_id'] = $data['user_id'];
				$credit_log['nid'] = "info_credit";
				$credit_log['code'] = "approve";
				$credit_log['type'] = "info_credit";
				$credit_log['addtime'] = time();
				$credit_log['article_id'] =$data['user_id'];
				$credit_log['remark'] = "填写个人详情获得的积分";
				//creditClass::ActionCreditLog($credit_log);
				if ($_POST['type']=="basic"){
					header("Location: /?user&q=code/borrow/jrsh&type=contact"); 
					//$msg = array("提交成功","","/?user&q=code/borrow/jrsh&type=contact");
				}else{
					$msg = array("提交成功","",$url);
				}
			}else{
				if ($_POST['type']=="basic"){
					$msg = array("提交失败","","/?user&q=code/borrow/jrsh&type=basic");
				}else{
					$msg = array("提交失败","","");
				}
			}
		}else{
			$data['user_id'] = $_G['user_id'];
			$result = ratingClass::GetInfoOne($data);
			if (is_array($result)){
				$_U["rating_result"] = $result;
			}
		}
	}elseif($_U['query_type'] == "contact"){
		if (isset($_POST['submit'])){
			$var = array("live_address","live_tel","linkman2","relationship2","phone2","linkman3","relationship3","phone3");
			$data = post_var($var);
			$data['user_id'] = $_G['user_id'];
			$data['status'] = 1;
			$result = ratingClass::GetContactOne($data);
			if (is_array($result)){
				$_result = ratingClass::UpdateContact($data);
			}else{
				$_result = ratingClass::AddContact($data);
			}
			if ($_result == true){
				if ($_POST['type']=="contact"){
					header("Location: /?user&q=code/borrow/jrsh&type=job"); 
					//$msg = array("提交成功","","/?user&q=code/borrow/jrsh&type=job");
				}else{
					$msg = array("提交成功","",$url);
				}
			}else{
				if ($_POST['type']=="contact"){
					$msg = array("提交失败","","/?user&q=code/borrow/jrsh&type=contact");
				}else{
					$msg = array("提交失败","",$url);
				}			
			}
		}else{
			$data['user_id'] = $_G['user_id'];
			$result = ratingClass::GetContactOne($data);
			if (is_array($result)){
				$_U["rating_result"] = $result;
			}
		}
	}elseif($_U['query_type'] == "job"){
		if (isset($_POST['submit'])){
			$var = array("name","tel","work_year","work_city","work_province","reterence","reterence_tel");
			$data = post_var($var);
			$data['user_id'] = $_G['user_id'];
			$data['status'] = 1;
			$result = ratingClass::GetJobOne($data);
			if (is_array($result)){
				$_result = ratingClass::UpdateJob($data);
			}else{
				$_result = ratingClass::AddJob($data);
			}
			if ($_result == true){
				$credit_log['user_id'] = $data['user_id'];
				$credit_log['nid'] = "work_credit";
				$credit_log['code'] = "approve";
				$credit_log['type'] = "work_credit";
				$credit_log['addtime'] = time();
				$credit_log['article_id'] =$data['user_id'];
				$credit_log['remark'] = "填写工作信息获得的积分";
				//creditClass::ActionCreditLog($credit_log);
				
				
				if ($_POST['type']=="job"){
					header("Location: /?user&q=code/borrow/jrsh&type=finance"); 
					//$msg = array("提交成功","","/?user&q=code/borrow/jrsh&type=finance");
				}else{
					$msg = array("提交成功","",$url);
				}
			}else{
				if ($_POST['type']=="job"){
					$msg = array("提交失败","","/?user&q=code/borrow/jrsh&type=job");
				}else{
					$msg = array("提交失败","",$url);
				}
				
			}
		}else{
			$data['user_id'] = $_G['user_id'];
			$result = ratingClass::GetJobOne($data);
			if (is_array($result)){
				$_U["rating_result"] = $result;
			}
		}
	}elseif($_U['query_type'] == "company"){
		if (isset($_POST['submit'])){
			$var = array("name","license_num","tax_num_di","tax_num_guo","address","rent_time","rent_money","hangye","people","time","type");
			$data = post_var($var);
			$data['user_id'] = $_G['user_id'];
			$data['status'] = 1;
			$result = ratingClass::GetCompanyOne($data);
			if (is_array($result)){
				$_result = ratingClass::UpdateCompany($data);
			}else{
				$_result = ratingClass::AddCompany($data);
			}
			if ($_result == true){
				$credit_log['user_id'] = $data['user_id'];
				$credit_log['nid'] = "work_credit";
				$credit_log['code'] = "approve";
				$credit_log['type'] = "work_credit";
				$credit_log['addtime'] = time();
				$credit_log['article_id'] =$data['user_id'];
				$credit_log['remark'] = "填写公司信息获得的积分";
				//creditClass::ActionCreditLog($credit_log);
				if ($_POST['web']=="borrow"){
					$msg = array("提交成功","","/?user&q=code/borrow/loan&p=contact&type=".$_POST['borrow_type']);
				}else{
					$msg = array("提交成功","",$url);
				}
			}else{
				$msg = array("提交失败","","/amount_company/index.html");
			}
		}else{
			$data['user_id'] = $_G['user_id'];
			$result = ratingClass::GetCompanyOne($data);
			if (is_array($result)){
				$_U["rating_result"] = $result;
			}
		}
	}elseif($_U['query_type'] == "lianbao"){
		if (isset($_POST['submit'])){
			$var = array("name1","relationship5","tel1","name2","relationship6","tel2");
			$data = post_var($var);
			$data['user_id'] = $_G['user_id'];			
			$result = ratingClass::GetLianbaoOne($data);
			if (is_array($result)){
				$_result = ratingClass::UpdateLianbao($data);
			}else{
				$_result = ratingClass::AddLianbao($data);
			}
			if ($_result == true){				
				if ($_POST['type']=="lianbao"){
					header("Location: /?user&q=code/borrow/jrsh&type=avatar"); 
					//$msg = array("提交成功","","/?user&q=code/borrow/jrsh&type=avatar");
				}else{
					$msg = array("提交成功","",$url);
				}
			}else{
				if ($_POST['type']=="lianbao"){
					$msg = array("提交失败","","/?user&q=code/borrow/jrsh&type=lianbao");
				}else{
					$msg = array("提交失败","",$url);
				}				
			}
		}else{
			$data['user_id'] = $_G['user_id'];
			$result = ratingClass::GetLianbaoOne($data);
			if (is_array($result)){
				$_U["rating_result"] = $result;
			}
		}
	}elseif($_U['query_type'] == "addassets"){
		if (!isset($_REQUEST['edit'])){
			if ($_POST['submit']){
				$var = array("name","account","assetstype","other");
				$data = post_var($var);
				$data['user_id'] = $_G['user_id'];
				$data['status'] = 0;
				$_result = ratingClass::AddAssets($data);
				if ($result>0){
					if($_REQUEST['type']=="amount"){
						$msg = array("提交成功","","/amount_caiwu/loan&p=finance&type=".$_POST['borrow_type']);
					}else{
						$msg = array("提交成功");
					}
				}else{
					if($_REQUEST['type']=="amount"){
						$msg = array("提交失败","","/amount_caiwu/index.html");
					}else{
						$msg = array("提交失败");
					}
				}
			}
		}elseif (isset($_REQUEST['edit'])){
			if ($_POST['submit']==""){
				$result = ratingClass::GetAssetsOne($data);
				if (is_array($result)){
					$_U["rating_result"] = $result;
				}
			}else{
				$var = array("name","account","assetstype","other");
				$data = post_var($var);
				$data['id'] = $_REQUEST['edit'];
				$data['status'] = 0;
				$_result = ratingClass::UpdateAssets($data);
				if ($result>0){
					if($_REQUEST['type']=="amount"){
						$msg = array("更新成功","","/amount_caiwu/index.html");
					}else{
						$msg = array("更新成功");
					}
				}else{
					if($_REQUEST['type']=="amount"){
						$msg = array("更新失败","","/amount_caiwu/index.html");
					}else{
						$msg = array("更新失败");
					}
				}
			}
		}
	}elseif($_U['query_type'] == "addrevenue" || $_U['query_type'] == "addpayment"){
		if (isset($_POST['submit'])){
			$var = array("month_income","month_income_describe","month_pay","month_pay_describe","house","house_value","is_car","car_value","cangu","cangu_account","describe");
			$data = post_var($var);
			$data['user_id'] = $_G['user_id'];
			$data['status'] = 1;
			$result = ratingClass::GetFinanceOne($data);
			
			if (is_array($result)){
				//$data['id'] = $_REQUEST['edit'];
				$_result = ratingClass::UpdateFinance($data);
			}else{
				$_result = ratingClass::AddFinance($data);
			}
			if ($_result == true){
				$credit_log['user_id'] = $data['user_id'];
				$credit_log['nid'] = "finance_credit";
				$credit_log['code'] = "approve";
				$credit_log['type'] = "finance_credit";
				$credit_log['addtime'] = time();
				$credit_log['article_id'] =$data['user_id'];
				$credit_log['remark'] = "填写财务信息获得的积分";
				//creditClass::ActionCreditLog($credit_log);
				if ($_POST['type']!=""){
					header("Location: /?user&q=code/borrow/jrsh&type=lianbao"); 
					//$msg = array("提交成功","","/?user&q=code/borrow/jrsh&type=lianbao");
					
				}else{
					$msg = array("提交成功","","");
				}
			}else{
				if ($_POST['type']!=""){
					$msg = array("提交失败","","/?user&q=code/borrow/jrsh&type=lianbao");
				}else{
					$msg = array("提交失败","","");
				}
			}
		}elseif (isset($_REQUEST['edit'])){
			$data['id'] = $_REQUEST['edit'];
			$result = ratingClass::GetFinanceOne($data);
			if (is_array($result)){
				$_U["rating_result"] = $result;
			}else{
				if($_REQUEST['type']=="amount"){
					$msg = array("读取失败","","/amount_finance/index.html");
				}else{
					$msg = array("读取失败","","/borrow_finance/index.html");
				}
			}
		}
	}elseif($_U['query_type'] == "assets"){
		if($_REQUEST['del']!=""){
			$data['id'] = $_REQUEST['del'];
			$data['user_id'] = $_G['user_id'];
			$result = ratingClass::DelAssets($data);
			if ($result>0){
				if($_REQUEST['type']=="amount"){
					$msg = array("删除成功","","/amount_caiwu/index.html");
				}else{
					$msg = array("删除成功");
				}
			}else{
				if($_REQUEST['type']=="amount"){
					$msg = array("删除失败","","/amount_caiwu/index.html");
				}else{
					$msg = array("删除失败");
				}
			}
		}
	}elseif($_U['query_type'] == "finance"){
		if($_REQUEST['del']!=""){
			$data['id'] = $_REQUEST['del'];
			$data['user_id'] = $_G['user_id'];
			$result = ratingClass::DelFinance($data);
			if ($result>0){
				if ($_REQUEST['type']=="amount"){
					$msg = array("删除成功","","/amount_finance/loan&p=houses");
				}elseif ($_REQUEST['type']=="borrow"){
					$msg = array("删除成功","","/borrow_finance/index.html");
				}else{
					$msg = array("删除成功","",$url);
				}
			}else{
				$msg = array("删除失败","","/borrow_finance/index.html");
			}
		}
	}
	elseif ($_U['query_type'] == "houses"){	
		if ($_POST['submit']!=""){
			$var = array("name","areas","in_year","repay","holder1","right1","holder2","right2","load_year","repay_month","balance","bank");
			$data = post_var($var);	
			$data['user_id'] = $_G['user_id'];	
			$data['status'] = 1;
			$result = ratingClass::GetHousesOne($data);	
			if (is_array($result)){
					$_result = ratingClass::UpdateHouse($data);
				}else{
					$_result = ratingClass::AddHouses($data);
				}
			if($_POST['web']=='borrow'){
				$msg = array("提交成功","","/?user&q=code/borrow/loan_amount&type=".$_POST['borrow_type']);				
			}else{
				$msg = array("提交成功","",$url);
			}		
		}else{
			$data['user_id'] = $_G['user_id'];
			$result = ratingClass::GetHousesOne($data);
			if (is_array($result)){
				$_U["rating_result"] = $result;
			}
		}
	}
}


$template = "user_rating.html";
?>
