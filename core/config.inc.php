<?php
session_start();
define('ROOT_PATH',dirname(__FILE__) .'/../');
define('DEAYOU_PATH',dirname(__FILE__) .'/../');
header('Content-Type:text/html;charset=gbk');
if (DIRECTORY_SEPARATOR == '\\'){
@ini_set('include_path','.;'.DEAYOU_PATH);
}else{
@ini_set('include_path','.:'.DEAYOU_PATH);
}
date_default_timezone_set('Asia/Shanghai');
require_once(DEAYOU_PATH.'core/common.inc.php');
require_once(DEAYOU_PATH.'core/function.inc.php');
require_once(DEAYOU_PATH.'core/safe.inc.php');
require_once(DEAYOU_PATH.'core/mysql.class.php');
$mysql = new Mysql($db_config);
require_once(DEAYOU_PATH.'core/magic.class.php');
$magic = new Magic();
require_once(DEAYOU_PATH.'core/upload.class.php');
$upload = new uploadClass();
require_once (DEAYOU_PATH."modules/admin/admin.class.php");
require_once (DEAYOU_PATH."modules/users/users.class.php");