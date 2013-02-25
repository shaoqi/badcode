<?php

	
// page  typeclass msg
function magic_function_show_avatar($params){
	global $_G;
	$path = dirname(__FILE__).'/../avatar/';
	$display = " <?php \n ";
	$display .= "require_once(\$path.'configs.php');\n";
	$display .= "require_once(\$path.'avatar.class.php');\n";
	$display .= "\$objAvatar = new Avatar();\n";
	$display .= "echo \$objAvatar->uc_avatar(\$this->magic_vars['_G']['user_id'], 'virtual');\n?>";
	return $display;
}

?>
