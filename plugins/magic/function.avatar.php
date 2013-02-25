<?php

function magic_function_avatar($params){
global $_G;
$path = dirname(__FILE__).'/../avatar/';
require_once($path.'configs.php');
require_once($path.'avatar.class.php');
$objAvatar = new Avatar();
$user_id = $_G['user_id'];
if ($user_id==""){
echo "ERROR";
exit;
}
$uc_avatarflash = $objAvatar->uc_avatar($user_id,(empty($_SCONFIG['avatarreal'])?'virtual':'real'));
$display =  '
	<script>
	function updateavatar() {
		location.href="index.php?user";
	}
	</script>'.$uc_avatarflash;
return $display;
}

?>