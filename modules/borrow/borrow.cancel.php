<?
/******************************
 * $File: borrow.cancel.php
 * $Description: 借款类文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/
if (!defined('DEAYOU_PATH'))  die('不能访问');//防止直接访问

require_once("borrow.loan.php");
require_once("borrow.fee.php");
require_once("borrow.calculates.php");
$MsgInfo["borrow_status_error"] = "审核状态不正确，如果原来是通过，则必须还是通过";
$MsgInfo["borrow_cancel_yes"] = "已经通过，不能进行修改";
$MsgInfo["borrow_loan_cance_error"] = "您的操作有误：【error:borrow_loan_cance_error】";

class borrowCancelClass
{
    
    
    function CancelInfo($data){
        global $mysql,$_G;
        if (IsExiest($data["borrow_nid"])=="") return "borrow_nid_empty";
		//读取借款标的相关信息
		$sql = "select p1.*,p2.username  from `{borrow}` as p1 left join `{users}` as p2 on p1.user_id=p2.user_id where p1.borrow_nid='{$data['borrow_nid']}'";
		$borrow_result = $mysql->db_fetch_array($sql);
        $borrow_result['borrow_url'] = "<a href={$_G['web_domain']}/invest/a{$data['borrow_nid']}.html target=_blank style=color:blue>{$borrow_result['name']}</a>";//借款标地址
		if ($borrow_result['borrow_full_status']==1){
		  return "borrow_fullcheck_yes";
		}elseif ($borrow_result['status']==6){
		  return "borrow_cancel_yes";
		}else{
		  
            //第一步做相关的判断
            if ($data['step']==0){
            	if ($borrow_result['cancel_status']>0){
    			     return "borrow_cancel_yes";
                }
                //判断是否可以重新审核
                echo "正在撤标中，请不要关闭浏览器";
        		//更新满标时的操作人
        		$sql = " update `{borrow}` set cancel_userid='{$data['cancel_userid']}',cancel_remark='{$data['remark']}',cancel_contents='{$data['contents']}',cancel_time='".time()."',cancel_status='{$data['status']}' where borrow_nid='{$data['borrow_nid']}'";
                $mysql ->db_query($sql);
                
        		//加入审核记录
                $_verify['user_id'] = $_G['user_id'];
                $_verify['status'] = $data['status'];
                $_verify['borrow_nid'] = $data['borrow_nid'];
                $_verify['type'] = "over";
                $_verify['remark'] = $data['remark'];
                $_verify['contents'] = $data['contents'];
                borrowLoanClass::AddVerify($_verify);
                return array("result"=>1,"step"=>1,"key"=>"");	
            }else{
                $fun = "CancelStep".$data['step'];
                $borrow_result["key"] = $data['key'];
                
    		    $result = self::$fun($borrow_result);
                return $result;
            }
		}
        //基本的资料
    }
	
	
    
    //操作投资人的信息
	function CancelStep1($borrow_result){
		global $mysql,$_G;
        if($borrow_result=="") return "";
        $status = $borrow_result['cancel_status'];
        //成功借款
        $sql = "select * from `{borrow_tender}` as p1 where  p1.borrow_nid='{$borrow_result['borrow_nid']}' limit {$borrow_result['key']},1";
		$tender_result = $mysql->db_fetch_array($sql);
		if ($tender_result==false){
			return array("result"=>1,"step"=>2,"key"=>0);
		}
        
		$tender_userid = $tender_result['user_id'];
		$tender_account = $tender_result['account'];
		$tender_id = $tender_result['id'];
		$borrow_nid = $borrow_result["borrow_nid"];
		$borrow_userid = $borrow_result["user_id"];
		$borrow_url = $borrow_result["borrow_url"];
		$borrow_name = $borrow_result["name"];
 	   //更新投资的状态
       
       //投资状态撤标的值为3，
		$sql = "update `{borrow_tender}` set status=3 where id={$tender_id}";
		$mysql->db_query($sql);
		
		//返回投资资金
		$log_info["user_id"] = $tender_userid;//操作用户id
		$log_info["nid"] = "tender_over_".$borrow_nid."_".$tender_userid."_".$tender_id;//订单号
        $log_info["borrow_nid"] = $borrow_nid;//收入
		$log_info["code"] = "borrow";//
		$log_info["code_type"] = "tender_over";//
		$log_info["code_nid"] = $tender_id;//
		$log_info["money"] = $tender_account;//操作金额
		$log_info["income"] = $tender_account;//收入
		$log_info["expend"] = 0;//支出
		$log_info["balance_cash"] = $tender_account;//可提现金额
		$log_info["balance_frost"] = 0;//不可提现金额
		$log_info["frost"] = -$tender_account;//冻结金额
		$log_info["await"] = 0;//待收金额
		$log_info["repay"] = 0;//待还金额
		$log_info["type"] = "tender_over";//类型
		$log_info["to_userid"] = $borrow_userid;//付给谁
		$log_info["remark"] =  "投标[{$borrow_url}]失败流标返回的投标额";
		accountClass::AddLog($log_info);
		
		//投资者接收 
		$remind['nid'] = "tender_over";
		$remind['receive_userid'] = $tender_userid;
		$remind['remind_nid'] = "tender_over_".$borrow_nid."_".$tender_userid."_".$tender_id;
		$remind['code'] = "borrow";
		$remind['article_id'] = $borrow_nid;
		$remind['title'] = "投资的标[{$borrow_name}]失败流标";
		$remind['content'] = "你所投资的标[{$borrow_url}]在".date("Y-m-d",time())."失败流标,失败原因：{$borrow_result['cancel_remark']}";
		remindClass::sendRemind($remind);
        return array("result"=>1,"step"=>1,"key"=>$borrow_result['key']+1);
   }
   
    //奖励
    function CancelStep2($borrow_result){
		global $mysql,$_G;
        if($borrow_result=="") return "";
        $borrow_nid = $borrow_result["borrow_nid"];
        $sql = "update `{borrow}` set status='{$borrow_result['cancel_status']}'  where borrow_nid='{$borrow_result["borrow_nid"]}'";
		$mysql->db_query($sql);
        
        
        //额度返回
        if ($borrow_result['amount_account']>0){
            //额度冻结
        	$_amount["user_id"] = $borrow_result['user_id'];//用户id
        	$_amount["amount_type"] = $borrow_result["amount_type"];//额度类型
        	$_amount["amount_style"] = "forever";
        	$_amount["type"] = "borrow_over";
        	$_amount["oprate"] = "return";
            $_amount["account"] = $borrow_result['amount_account'];
        	$_amount["nid"] = $_amount["type"]."_".$borrow_result['user_id']."_".$borrow_result['borrow_nid'];
        	$_amount["remark"] = "撤回借款标[{$borrow_result["borrow_url"]}]，返回{$borrow_result['account']}元额度";
            borrowAmountClass::AddAmountLog($_amount);	
        }
        
        //借款人接收 
		$remind['nid'] = "borrow_over";
		$remind['receive_userid'] = $borrow_result["user_id"];
		$remind['remind_nid'] = "borrow_over_".$borrow_nid."_".$borrow_result["user_id"];
		$remind['code'] = "borrow";
		$remind['article_id'] = $borrow_nid;
		$remind['title'] = "借款标[{$borrow_result['name']}]失败流标";
		$remind['content'] = "你所借款的标[{$borrow_result['name']}]在".date("Y-m-d",time())."失败流标,失败原因：{$borrow_result['cancel_remark']}";
		remindClass::sendRemind($remind);
        
		return array("result"=>0,"step"=>"","key"=>"");
	}
    
    
    function UserCancel($data){
        global $mysql,$_G;
        $sql = "select * from `{borrow}` where borrow_nid='{$data['borrow_nid']}' and user_id='{$data['user_id']}'";
        $borrow_result= $mysql->db_fetch_array($sql);
        if ($borrow_result==false){
            return "borrow_loan_cance_error";
        }
        $sql = " update `{borrow}` set cancel_userid='{$data['user_id']}',cancel_remark='{$data['remark']}',cancel_contents='{$data['contents']}',cancel_time='".time()."',cancel_status='5',status='5' where borrow_nid='{$data['borrow_nid']}'";
		$mysql->db_query($sql);
        $borrow_url = "<a href={$_G['web_domain']}/view/borrow_nid={$borrow_result['borrow_nid']} target=_blank style=color:blue>{$borrow_result['name']}</a>";//借款标地址
        //额度返回
        if ($borrow_result['amount_account']>0 && $borrow_result['borrow_type']!='worth'){
            //额度冻结
        	$_amount["user_id"] = $borrow_result['user_id'];//用户id
        	$_amount["amount_type"] = $borrow_result["amount_type"];//额度类型
        	$_amount["amount_style"] = "forever";
        	$_amount["type"] = "borrow_cancel";
        	$_amount["oprate"] = "return";
            $_amount["account"] = $borrow_result['amount_account'];
        	$_amount["nid"] = $_amount["type"]."_".$borrow_result['user_id']."_".$borrow_result['borrow_nid'];
        	$_amount["remark"] = "撤回借款标[{$borrow_url}]，返回{$borrow_result['account']}元额度";
            borrowAmountClass::AddAmountLog($_amount);	
        }
        //借款人接收 
		$remind['nid'] = "borrow_cancel";
		$remind['receive_userid'] = $borrow_result["user_id"];
		$remind['remind_nid'] = "borrow_cancel_".$borrow_result["borrow_nid"]."_".$borrow_result["user_id"];
		$remind['code'] = "borrow";
		$remind['article_id'] = $borrow_result["borrow_nid"];
		$remind['title'] = "借款的标[{$borrow_result['name']}]已撤回";
		$remind['content'] = "你所借款的标[{$borrow_url}]在".date("Y-m-d",time())."撤回";
		remindClass::sendRemind($remind);
        
		return array("result"=>0,"step"=>"","key"=>"");
        
    }
}
?>
