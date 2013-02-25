<?php
//session初始化

date_default_timezone_set('PRC');

$INFO = array();
$INFO["cookiedomain"] = '';
$INFO["cookiepath"] = '/';
$INFO["attachmentspath"] = "attachments";

if (function_exists('realpath') AND @realpath(dirname(__FILE__)) !== FALSE)
{
	$system_folder = str_replace("\\", "/", realpath(dirname(__FILE__)));//realpath返回规范化的绝对路径名字
}
define('EXT', '.'.pathinfo(__FILE__, PATHINFO_EXTENSION));
define('FCPATH', __FILE__);
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('BASEPATH', $system_folder."/");
define("Resource",BASEPATH."classes/resource");
define('AVATAR_ROOT', dirname(__FILE__).'/../../');
if (!defined('ROOT_PATH')){
	if (!defined('UC_DIR')){
		define('UC_DIR', "/dzx/uc_server/");
	 }else{
		if(UC_DIR!="UC_DIR"){
			define('UC_DIR', "/dzx/uc_server/");
		}
	 }
}

define('AVATAR_API', 'http://'.$_SERVER['HTTP_HOST'].'/plugins/avatar');
define('AVATAR_DATADIR', AVATAR_ROOT.'data/');
define('AVATAR_DATAURL', 'http://'.$_SERVER['HTTP_HOST'].'/data');
define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
$TurnDot  =  substr(PHP_OS, 0, 3) == 'WIN'  ?  ";"  :  ":"  ;


?>