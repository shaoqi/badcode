<?
include_once("../../core/config.inc.php");
include_once(ROOT_PATH."/modules/borrow/borrow.class.php");
include_once(ROOT_PATH."/modules/account/account.class.php");
include_once(ROOT_PATH."/modules/remind/remind.class.php");

//充值
$sql = "select * from `{account_recharge}` where status=1";
$result = $mysql->db_fetch_arrays($sql);
foreach ($result as $key => $value){
	$sql = "select 1 from `{borrow_return}` where type='recharge' and aid='{$value['id']}'";
	$result = $mysql->db_fetch_array($sql);
	if ($result==false){
		$sql = "insert into `{borrow_return}` set type='recharge',pid=1,aid='{$value['id']}',user_id='{$value['user_id']}',addtime='{$value['verify_time']}'";
		$mysql->db_query($sql);
	}
}


//提现
$sql = "select * from `{account_cash}` ";
$result = $mysql->db_fetch_arrays($sql);
foreach ($result as $key => $value){
	$sql = "select 1 from `{borrow_return}` where type='cash_frost' and status=1 and aid='{$value['id']}'";
	$result = $mysql->db_fetch_array($sql);
	if ($result==false){
		$sql = "insert into `{borrow_return}` set type='cash_frost',pid=2,aid='{$value['id']}',user_id='{$value['user_id']}',addtime='{$value['addtime']}'";
		$mysql->db_query($sql);
	}
}



//提现
$sql = "select * from `{account_cash}` where status=1";
$result = $mysql->db_fetch_arrays($sql);
foreach ($result as $key => $value){
	$sql = "select 1 from `{borrow_return}` where type='cash_success' and status=1 and aid='{$value['id']}'";
	$result = $mysql->db_fetch_array($sql);
	if ($result==false){
		$sql = "insert into `{borrow_return}` set type='cash_success',pid=3,aid='{$value['id']}',user_id='{$value['user_id']}',addtime='{$value['addtime']}'";
		$mysql->db_query($sql);
	}
}


//提现
$sql = "select * from `{account_cash}` where status=2";
$result = $mysql->db_fetch_arrays($sql);
foreach ($result as $key => $value){
	$sql = "select 1 from `{borrow_return}` where type='cash_false' and status=3 and aid='{$value['id']}'";
	$result = $mysql->db_fetch_array($sql);
	if ($result==false){
		$sql = "insert into `{borrow_return}` set type='cash_false',pid=4,aid='{$value['id']}',user_id='{$value['user_id']}',addtime='{$value['addtime']}'";
		$mysql->db_query($sql);
	}
}



//提现
$sql = "select * from `{account_cash}` where status=3";
$result = $mysql->db_fetch_arrays($sql);
foreach ($result as $key => $value){
	$sql = "select 1 from `{borrow_return}` where type='cash_cancel' and status=3 and aid='{$value['id']}'";
	$result = $mysql->db_fetch_array($sql);
	if ($result==false){
		$sql = "insert into `{borrow_return}` set type='cash_cancel',pid=5,aid='{$value['id']}',user_id='{$value['user_id']}',addtime='{$value['addtime']}'";
		$mysql->db_query($sql);
	}
}



//借款成功6
$sql = "select * from `{borrow}` where status=3";
$result = $mysql->db_fetch_arrays($sql);
foreach ($result as $key => $value){
	$sql = "select 1 from `{borrow_return}` where type='borrow_success' and aid='{$value['id']}'";
	$result = $mysql->db_fetch_array($sql);
	if ($result==false){
		$sql = "insert into `{borrow_return}` set type='borrow_success',pid=6,aid='{$value['id']}',user_id='{$value['user_id']}',addtime='{$value['addtime']}'";
		$mysql->db_query($sql);
	}
}


//借款成功7
$sql = "select * from `{borrow}` where status=4";
$result = $mysql->db_fetch_arrays($sql);
foreach ($result as $key => $value){
	$sql = "select 1 from `{borrow_return}` where type='borrow_false' and aid='{$value['id']}'";
	$result = $mysql->db_fetch_array($sql);
	if ($result==false){
		$sql = "insert into `{borrow_return}` set type='borrow_false',pid=7,aid='{$value['id']}',user_id='{$value['user_id']}',addtime='{$value['addtime']}'";
		$mysql->db_query($sql);
	}
}




//借款成功8
$sql = "select * from `{borrow_repay}` where repay_status=1";
$result = $mysql->db_fetch_arrays($sql);
foreach ($result as $key => $value){
	$sql = "select 1 from `{borrow_repay}` where type='borrow_repay' and aid='{$value['id']}'";
	$result = $mysql->db_fetch_array($sql);
	if ($result==false){
		$sql = "insert into `{borrow_return}` set type='borrow_repay',pid=8,aid='{$value['id']}',user_id='{$value['user_id']}',addtime='{$value['addtime']}'";
		$mysql->db_query($sql);
	}
}


?>