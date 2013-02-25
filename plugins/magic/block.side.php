<?php

function magic_block_side($tag_command,$parse_var,$magic_vars){
$var = "";
if  ($tag_command == "side") {
$module = "";
$function = "";
$_data = "";
$user_id = "";
$site_id = "";
$city_id  = "";
$article_id = "";
foreach($parse_var as $_key =>$_val) {
switch($_key) {
case 'module':
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
if ($user_id!=""){
if ($user_id=="0"){
$_data[] = "'user_id'=>\$this->magic_vars['_G']['user_id']";
}
}
if ($site_id!=""){
if ($site_id=="0"){
$_data[] = "'site_id'=>\$this->magic_vars['_G']['site_result']['site_id']";
}else{
$_data[]  = "'site_id'=>'{$site_id}'";
}
}
if ($article_id!=""){
if ($article_id=="0"){
$_data[] = "'id'=>\$this->magic_vars['_G']['article_id']";
}elseif ($article_id=="request"){
$_data[] = "'id'=>isset(\$_REQUEST['article_id'])?\$_REQUEST['article_id']:''";
}else{
$_data[]  = "'id'=>'{$article_id}'";
}
}
if ($_data!=""){
$data = "array(".join(",",$_data).")";
}else{
$data = "''";
}
$var = empty($var)?"var":$var;
if ($module == ""){
trigger_error("list: extra attribute 'module' cannot be not empty",E_USER_NOTICE);exit;
}
if ($function == ""){
trigger_error("list: extra attribute 'function' cannot be not empty",E_USER_NOTICE);exit;
}else{
$display = "<? \$data = {$data};";
}
if($module=="user"){
$display .= "  include_once(ROOT_PATH.'core/user.class.php');\$this->magic_vars['$var'] = userClass::{$function}(\$data);";
}elseif($module=="apply"){
$display .= "  include_once(ROOT_PATH.'core/apply.class.php');\$this->magic_vars['$var'] = applyClass::{$function}(\$data);";
}else{
if (file_exists(ROOT_PATH."modules/{$module}/{$module}.class.php")){
$display .= "  include_once(ROOT_PATH.'modules/{$module}/{$module}.class.php');\$this->magic_vars['$var'] = {$module}Class::{$function}(\$data);";
}else{
trigger_error("loop: extra attribute 'module' cannot be not exist",E_USER_NOTICE);exit;
}
}
$display .="if(!is_array(\$this->magic_vars['$var'])){ \$this->magic_vars['$var']=array();}";
$display .= '?>';
return $display;
}else if ($tag_command == "/side"){
return "<? unset(\$_magic_vars);unset(\$data); ?>";
}
}

?>