<?
/******************************
 * $File: reg.php
 * $Description: 注册
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

if (!defined('IN_ECS'))  die('Hacking attempt');

require(ROOT_PATH . '/servers/address.class.php');
$address = new address();


/* 收货地址列表和编辑 */
if ($t == ''  || $t == 'edit'){
	$result = $address->get($p,$epage,$user_id);
	$smarty->assign("result",$result['res']);
	
	$pager->set_data(array('total'=>$result['sum'],'perpage'=>$epage));
	$smarty->assign("pager",$pager->show(3));
	
	if ($_REQUEST['address_id'] != ""){
		$_result = $address->view($_REQUEST['address_id'],$user_id);
		$smarty->assign("_result",$_result);
		if ($_result == false){
			$msg = "请不要乱操作";
			show_msg($msg,"返回上一页","?s=address");
		}
	}
}

/* 获取收货地址 */
elseif ($t == 'get_address'){
	$result = $address->view($_REQUEST['address_id'],$user_id);
	if ($result<0 || $result == false){
		echo false;
	}else{
		$res = "";
		foreach ($result as $key => $result){
			$res .= $result."|@*"; 
		}
		echo $res;
	}
	exit;
}

/* 收货地址添加和修改 */
elseif ($t == 'add' || $t == 'update'){
	$index['realname'] = $_POST['realname'];
	$index['email'] = $_POST['email'];
	$index['postcode'] = $_POST['postcode'];
	$index['qq'] = $_POST['qq'];
	$index['wangwang'] = $_POST['wangwang'];
	$index['tel'] = $_POST['tel'];
	$index['phone'] = $_POST['phone'];
	$index['province'] = $_POST['province'];
	$index['city'] = $_POST['city'];
	$index['area'] = $_POST['area'];
	$index['address'] = $_POST['address'];
	$index['building'] = $_POST['building'];
	$index['besttime'] = $_POST['besttime'];
	
	if ($t == 'update'){
		$address_id = $_POST['address_id'];
		$result = $address->update($index,$address_id,$user_id);
	}else{
		$result = $address->add($index,$user_id);
	}
	if ($result < 0 || $result == false){
		$msg = "输入有误，请跟管理员联系";
		show_msg($msg,"返回上一页",-1);
	}else{
		$msg = "收货地址操作成功";
		show_msg($msg,"返回上一页",-1);
	}
}

//ajax查看
elseif ($t == 'view'){
	$epage = 3;
	$result = $address->get($p,$epage,$user_id);
	$smarty->assign("result",$result['res']);
	
	$pager->set_data(array('total'=>$result['sum'],'perpage'=>$epage));
	$pager->open_ajax("getAjaxTable");
	$smarty->assign("pager",$pager->show(3));
}

/* 收货地址删除 */
elseif ($t == 'del'){
	$address->del($_REQUEST['address_id'],$user_id);
	$msg = "删除成功";
	show_msg($msg,"返回上一页","?s=address");
}

//错误
else{
	show_msg("请不要乱操作","返回上一页","?s=address");
}

$tpl = "user_address.html";
?>