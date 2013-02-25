<?
/******************************
 * $File: logout.php
 * $Description: 退出
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

		if ($_G['module']['ucenter_status']==1){
			$sql = "select * from `{ucenter}` where user_id='{$_G['user_id']}'";
			$result = $mysql->db_fetch_array($sql);
			$ucenter_logout = ucenterClass::UcenterLogout($result['uc_user_id']);
			echo $ucenter_logout;
		}
		
		DelCookies();
		if ($_REQUEST['type']=="index"){
		echo '<script language="javascript">window.location.href="/";</script>';
		exit();
		}else{
		echo '<script language="javascript">window.location.href="/?user&q=login";</script>';
		exit();
		}
?>