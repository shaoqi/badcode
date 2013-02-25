<?php

function magic_block_list($tag_command,$parse_var,$magic_vars){
if  ($tag_command == "list") {
$module = "";
$function = "";
$_data = "";
$city_id = "";
$site_id = "";
$user_id = "";
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
$$_key = $_val;
break;
}
if ($_key !="module"&&$_key!="function"&&$_key!="city_id"&&$_key!="site_id"){
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
if ($user_id!=""){
if ($user_id=="0"){
$_data[] = "'user_id'=>\$this->magic_vars['_G']['user_id']";
}
}
if ($_data!=""){
$data = "array(".join(",",$_data).")";
}
$var = empty($var)?"var":$var;
if ($module == ""){
trigger_error("list: extra attribute 'module' cannot be not empty",E_USER_NOTICE);exit;
}
if ($function == ""){
trigger_error("list: extra attribute 'function' cannot be not empty",E_USER_NOTICE);exit;
}else{
$display = "<? \$this->magic_vars['query_type']='{$function}';\$data = {$data};\$data['page'] = intval(isset(\$_REQUEST['page'])?\$_REQUEST['page']:'');";
}
if ($plugins!=""){
$plugins = strtolower($plugins);
if (file_exists(ROOT_PATH."modules/{$module}/{$module}.{$plugins}.php")){
$display .= "  require_once(ROOT_PATH.'modules/{$module}/{$module}.{$plugins}.php');\$this->magic_vars['magic_result'] = {$module}{$plugins}Class::{$function}(\$data);";
}else{
trigger_error("loop: extra attribute 'module' cannot be not exist",E_USER_NOTICE);exit;
}
}elseif (file_exists(ROOT_PATH."modules/{$module}/{$module}.class.php")){
$display .= "  require_once(ROOT_PATH.'modules/{$module}/{$module}.class.php');\$this->magic_vars['magic_result'] = {$module}Class::{$function}(\$data);";
}else{
trigger_error("loop: extra attribute 'module' cannot be not exist",E_USER_NOTICE);exit;
}
$display .=" \$this->magic_vars['$var']=  \$this->magic_vars['magic_result'];";
$display .=" \$this->magic_vars['$var']['list'] =  \$this->magic_vars['magic_result']['list'];";
$display .=" \$this->magic_vars['$var']['page'] =  intval(\$this->magic_vars['magic_result']['page']);";
$display .=" \$this->magic_vars['$var']['epage'] =  \$this->magic_vars['magic_result']['epage'];";
$display .=" \$this->magic_vars['$var']['total'] =  \$this->magic_vars['magic_result']['total'];";
$display .=" \$this->magic_vars['$var']['pages'] =  array('total'=>\$this->magic_vars['magic_result']['total'],'page'=>\$this->magic_vars['magic_result']['page'],'epage'=>\$this->magic_vars['magic_result']['epage']);";
$display .= '?>';
return $display;
}else if ($tag_command == "/list"){
return "<? unset(\$_magic_vars); ?>";
}
}

?>