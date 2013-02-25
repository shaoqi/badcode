<?php

function magic_block_articleside($tag_command,$parse_var,$magic_vars){
if  ($tag_command == "articleside") {
$module = "";
$function = "";
$_data = "";
$table = "";
foreach($parse_var as $_key =>$_val) {
switch($_key) {
case 'type':
$$_key = (string)$_val;
break;
case 'style':
$$_key = (string)$_val;
break;
default:
$$_key = $_val;
break;
}
}
if (isset($city_id) &&$city_id!=""){
if ($city_id=="0"){
$_data[] = "'city_id'=>\$this->magic_vars['_G']['city_result']['id']";
}else{
$_data[] = "'city_id'=>'{$city_id}'";
}
}
if ($site_id!=""){
if ($site_id=="0"){
$_data[] = "'site_id'=>\$this->magic_vars['_G']['site_result']['site_id']";
}else{
$_data[]  = "'site_id'=>'{$site_id}'";
}
}
$_data[] = "'id'=>\$this->magic_vars['_G']['article_id']";
$_data[] = "'code'=>\$this->magic_vars['_G']['site_result']['code']";
$_data[] = "'table'=>'{$table}'";
if ($_data!=""){
$data = "array(".join(",",$_data).")";
}else{
$data = "''";
}
$var = empty($var)?"var":$var;
$display = "<? \$data = {$data};";
$display .= " \$this->magic_vars['magic_result'] = siteClass::GetArticleSide(\$data);";
$display .=" \$this->magic_vars['$var']=  \$this->magic_vars['magic_result'];";
$display .= '?>';
return $display;
}else if ($tag_command == "/articleside"){
return "<? unset(\$_magic_vars);unset(\$data); ?>";
}
}
?>