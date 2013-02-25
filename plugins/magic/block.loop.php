<?php

function magic_block_loop($tag_command,$parse_var,$magic_vars){
if  ($tag_command == "loop") {
$table = null;
$where = null;
$type = null;
$order = null;
$sql = null;
$site_id = null;
$city_id = null;
$site_var = null;
$user_id = null;
$default = null;
$_data = "";
foreach($parse_var as $_key =>$_val) {
switch($_key) {
case 'module':
$$_key = (string)$_val;
break;
case 'plugins':
$$_key = (string)$_val;
break;
case 'function':
$$_key = (string)$_val;
break;
default:
$$_key = (string)$_val;
break;
}
if ($_key !="module"&&$_key!="function"&&$_key!="city_id"&&$_key!="site_id"&&$_key!="user_id"){
if ($_val == "request"){
$_data[] = "'{$_key}'=>isset(\$_REQUEST['{$_key}'])?\$_REQUEST['{$_key}']:''";
}elseif ($_val{0}=="$"){
$_data[] = "'{$_key}'=>{$_val}";
}else{
$_data[] = "'{$_key}'=>'{$_val}'";
}
}
}
if ($city_id!=""){
if ($city_id=="0"){
$_data[] = "'city_id'=>\$this->magic_vars['_G']['city_result']['id']";
}else{
$_data[] = "'city_id'=>'{$city_id}'";
}
}
if ($site_id!=""){
if ($site_id=="this"){
$_data[] = "'site_id'=>\$this->magic_vars['_G']['site_result']['id']";
}else{
$_data[]  = "'site_id'=>'{$site_id}'";
}
}
if ($site_var!=""){
$_data[] = "'site_id'=>\$this->magic_vars['{$site_var}']['site_id']";
}
if ($user_id!=""){
if ($user_id=="0"){
$_data[] = "'user_id'=>\$this->magic_vars['_G']['user_id']";
}else{
$_data[]  = "'user_id'=>{$user_id}";
}
}
if ($_data!=""){
$data = "array(".join(",",$_data).")";
}else{
$data = "array()";
}
$var = empty($var)?"var":$var;
if ($module == ""){
trigger_error("loop: extra attribute 'module' cannot be not empty",E_USER_NOTICE);exit;
}
if ($function == ""){
trigger_error("loop: extra attribute 'function' cannot be not empty",E_USER_NOTICE);exit;
}else{
$display = "<? \$this->magic_vars['query_type']='{$function}';\$data = {$data};\$default = '{$default}';";
}
if ($plugins!=""){
$plugins = strtolower($plugins);
if (file_exists(ROOT_PATH."modules/{$module}/{$module}.{$plugins}.php")){
$display .= "  require_once(ROOT_PATH.'modules/{$module}/{$module}.{$plugins}.php');\$this->magic_vars['magic_result'] = {$module}{$plugins}Class::{$function}(\$data);";
}else{
trigger_error("loop: extra attribute '".ROOT_PATH."modules/{$module}/{$module}.{$plugins}.php"."' cannot be not exist",E_USER_NOTICE);exit;
}
}elseif($module=="user"||$module=="apply"){
$display .= "  require_once(ROOT_PATH.'core/{$module}.class.php');\$this->magic_vars['magic_result'] = {$module}Class::{$function}(\$data);";
}else{
if (file_exists(ROOT_PATH."modules/{$module}/{$module}.class.php")){
$display .= "  require_once(ROOT_PATH.'modules/{$module}/{$module}.class.php');\$this->magic_vars['magic_result'] = {$module}Class::{$function}(\$data);";
}else{
trigger_error("loop: extra attribute 'module'".ROOT_PATH."modules/{$module}/{$module}.class.php"." cannot be not exist",E_USER_NOTICE);exit;
}
}
$display .="if(!isset(\$this->magic_vars['magic_result'])) \$this->magic_vars['magic_result']= array(); \$_from = \$this->magic_vars['magic_result']; if (!is_array(\$_from) && !is_object(\$_from)) {\$_from =array(); } ";
$display .= "if (count(\$_from)>0):\n;";
$display .= "    foreach (\$_from as \$this->magic_vars['key'] => \$this->magic_vars['$var']):\n";
$display .= '?>';
return $display;
}else if ($tag_command == "/loop"){
return "<? endforeach;else:echo \$default; endif; unset(\$_from);unset(\$_magic_vars); ?>";
}
}

?>