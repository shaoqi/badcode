<?php

function magic_block_site($tag_command,$parse_var,$this){
if  ($tag_command == "site") {
$var_name = empty($parse_var['var'])?"var":$parse_var['var'];
$site_id = empty($parse_var['site_id'])?"":$parse_var['site_id'];
$result = '<? $_magic_vars = $this->mysql->db_fetch_arrays("select * from {site} where site_id='.$site_id.'");';
$result .= "if (\$_magic_vars!=false):\n";
$result .= "    foreach (\$_magic_vars as \$this->magic_vars['$var_name']):\n";
$result .= '?>';
return $result;
}else if ($tag_command == "/site"){
return "<? endforeach; endif; unset(\$_from); ?>";
}
}

?>