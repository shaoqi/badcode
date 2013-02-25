<?php

function magic_function_linkages($parse_var){
global $mysql,$_G;
foreach($parse_var as $_key =>$_val) {
switch($_key) {
case 'name':
$$_key = (string)$_val;
break;
case 'nid':
$$_key = (string)$_val;
break;
case 'value':
$$_key = $_val;
break;
case 'plugins':
$$_key = $_val;
break;
case 'input':
$$_key = $_val;
break;
case 'type':
$$_key = $_val;
break;
case 'style':
$$_key = $_val;
break;
case 'class':
$$_key = $_val;
break;
case 'default':
$$_key = $_val;
break;
default:
trigger_error("linkages: extra attribute '$_key' cannot be an array",E_USER_NOTICE);
break;
}
}
if (!isset($nid) ||$nid=="") return "";
if (!isset($input)) $input =  "";
if (!isset($default)) $default =  "";
if (!isset($type)) $type =  "";
if ($type==""||$type=="id"){
$vid = "id";
}else{
$vid = "value";
}
if (isset($_G['_linkage'])){
$result = $_G['_linkage'][$nid];
if ($result=="") return " ";
}
$display = "<? \$result = \$this->magic_vars['_G']['_linkages']['$nid'];";
$display .= " if ('$plugins'=='module' && \$this->magic_vars['_G']['linkages']['$nid']!=''){\$result=''; foreach (\$this->magic_vars['_G']['linkages']['$nid'] as \$_key => \$_value) { \$result[] = array('name'=>\$_value,'value'=>\$_key);}}";
if ($input==""||$input == "select"){
$display .=  " echo \"<select name='$name' id=$name  style='$style' class='$class'>\";";
if ($default!=""){
$display .=  " echo \"<option value=''>".urldecode($default)."</option>\";";
}
}
$display .= " if (\$result!=''): foreach (\$result as \$key => \$val): ";
if ($input==""||$input == "select"){
if ($value!=""){
if ($vid=="id"){
$display .="if ($value==\$val['id'] ) { ";
}else{
$display .="if ($value==\$val['value']) { ";
}
$display .=  "echo \"<option value='{\$val['$vid']}' selected>{\$val['name']}</option>\"; ";
$display .=  "}else{echo \"<option value='{\$val['$vid']}' >{\$val['name']}</option>\";} ";
}else{
$display .=  "echo \"<option value='{\$val['$vid']}' >{\$val['name']}</option>\";";
}
}elseif ($input == "checkbox"){
$name = $name."[]";
if ($value!=""){
if ($value{0}=="$"){
$display .= "\$_value=explode(',',{$value});\n";
}else{
$display .= "\$_value=explode(',',\"{$value}\");\n";
}
if ($vid=="id"){
$display .="if ( in_array(\$val['id'],\$_value) ) { ";
}else{
$display .="if ( in_array(\$val['value'],\$_value)) { ";
}
$display .=  "echo \"<label><input  type='checkbox' name=$name value='{\$val['$vid']}' checked>{\$val['name']}</label>\"; ";
$display .=  "}else{echo \"<label><input  type='checkbox' name=$name value='{\$val['$vid']}' >{\$val['name']}</label>\";} ";
}else{
$display .=  "echo \"<label><input  type='checkbox' name=$name value='{\$val['$vid']}' >{\$val['name']}</label>\"; ";
}
}
elseif ($input == "radio"){
if ($value!=""){
if ($vid=="id"){
$display .="if ($value==\$val['id'] ) { ";
}else{
$display .="if ($value==\$val['value']) { ";
}
$display .=  "echo \"<label><input  type='radio' name=$name value='{\$val['$vid']}' checked>{\$val['name']}</label>\"; ";
$display .=  "}else{echo \"<label><input  type='radio' name=$name value='{\$val['$vid']}' >{\$val['name']}</label>\";} ";
}else{
$display .=  "echo \"<label><input  type='radio' name=$name value='{\$val['$vid']}' >{\$val['name']}</label>\"; ";
}
}
$display .= "endforeach;";
if ($input==""||$input == "select"){
$display .=  "echo \"</select>\";";
}
$display .= "endif; ?>";
return $display;
}

?>