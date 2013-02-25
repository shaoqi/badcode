<?php

function magic_function_html_radios($params){
$name = null;
$values = null;
$options = null;
$checked = array();
$output = null;
$extra = '';
foreach($params as $_key =>$_val) {
switch($_key) {
case 'name':
$$_key = (string)$_val;
break;
case 'options':
$$_key = $_val;
break;
case 'values':
case 'output':
$$_key = array_values((array)$_val);
break;
case 'checked':
$$_key = $_val;
break;
default:
trigger_error("html_options: extra attribute '$_key' cannot be an array",E_USER_NOTICE);
break;
}
}
if (!isset($options) &&!isset($values))
return '';
$_html_result = '<? ';
if (isset($options)) {
$_html_result .= "if (!isset($options)) $options = array(); \$_from =$options;\$_selected='';  foreach (\$_from as \$key => \$value):";
$_html_result .= "echo \"<input type=radio value='\$key' name='".$name."' \"; ";
if (count($checked)>0){
$_html_result .= "if(isset($checked) && in_array(\$key,$checked)){ echo ' checked '; };";
}
$_html_result .="echo \" >\$value \";";
$_html_result .= ' endforeach; ?>';
}
return $_html_result;
}
?>