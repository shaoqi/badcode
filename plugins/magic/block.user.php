<?php

function magic_block_user($tag_command,$parse_var,$magic_vars){
if  ($tag_command == "user") {
$table = null;
$where = null;
$type = null;
$order = null;
$sql = null;
$_data = "";
foreach($parse_var as $_key =>$_val) {
switch($_key) {
case 'module':
$$_key = (string)$_val;
break;
case 'function':
$$_key = (string)$_val;
break;
default:
$$_key = (string)$_val;
break;
}
if ($_key !="module"&&$_key!="function"&&$_key!="city_id"&&$_key!="site_id"){
$_data[] = "'{$_key}'=>'{$_val}'";
}
}
if ($_data!=""){
$data = "array(".join(",",$_data).")";
}
$var = empty($var)?"var":$var;
if ($function == ""){
trigger_error("loop: extra attribute 'function' cannot be not empty",E_USER_NOTICE);exit;
}else{
$display = "<? \$this->magic_vars['query_type']='{$function}';\$data = {$data};";
}
if (file_exists(ROOT_PATH."core/user.class.php")){
$display .= "  include_once(ROOT_PATH.'core/user.class.php');\$this->magic_vars['magic_result'] = userClass::{$function}(\$data);";
}else{
trigger_error("loop: extra attribute 'module' cannot be not exist",E_USER_NOTICE);exit;
}
$display .="\$_from = \$this->magic_vars['magic_result']; if (!is_array(\$_from) && !is_object(\$_from)) { settype(\$_from,'array'); } ";
$display .= "if (count(\$_from)):\n";
$display .= "    foreach (\$_from as \$this->magic_vars['key'] => \$this->magic_vars['$var']):\n";
$display .= '?>';
return $display;
}else if ($tag_command == "/user"){
return "<? endforeach;  endif; unset(\$_from);unset(\$_magic_vars); ?>";
}
}

?>