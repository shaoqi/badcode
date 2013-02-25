<?php

function magic_function_html_options($params){
$name = null;
$values = null;
$options = null;
$selected = null;
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
case 'selected':
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
$_html_result .= "echo \"<option value='\$key'\"; ";
if ($selected!=""){
$_html_result .= "if(isset($selected) && \$key == $selected){ echo ' selected '; };";
}
$_html_result .="echo \" >\$value</option>\";";
$_html_result .= ' endforeach; ?>';
}
if(!empty($name)) {
$_html_result = '<select name="'.$name .'">'."\n".$_html_result .'</select>'."\n";
}
return $_html_result;
}

?>