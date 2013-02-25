<?php

function magic_function_html_checkboxes($params){
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
case 'kname':
$$_key = $_val;
break;
default:
trigger_error("html_options: extra attribute '$_key' cannot be an array",E_USER_NOTICE);
break;
}
}
if (isset($checked)) $_checked = $checked;
if (!isset($options) &&!isset($values))
return '';
if (!is_array($checked) &&$checked{0}!="$"){
$checked = explode(",",$checked);
$_check = array();
foreach ($checked as $key =>$value){
$_check[] = "\"".$value."\"";
}
$checked = "array(".join(",",$_check).")";
}
if (isset($kname) &&$kname!=""){
$_name = $name."[\$kname][]";
}else{
$kname = "\"\"";
$_name = $name."[]";
}
$_html_result = '<? ';
if (isset($options)) {
$_html_result .= "if (!isset($options)) $options = array();\n \$_from =$options;\n ";
if ( $checked{0}=="$"){
$_html_result .= "\$checked=isset($checked)?$checked:'';";
}else{
$_html_result .= "\$checked=$checked;";
}
$_html_result .= "\$kname=$kname;\n if (!is_array(\$checked)) \$checked = explode(',',\$checked);\n foreach (\$_from as \$key => \$value):";
$_html_result .= "echo \"<input type=checkbox value='\$key' name='".$_name."' \"; ";
if ($_checked =="all"){
$_html_result .= " echo ' checked '; ";
}else{
$_html_result .= "if(isset(\$checked) && isset(\$checked) && in_array(\$key,\$checked)){ echo ' checked '; };";
}
$_html_result .="echo \" >\$value \";";
$_html_result .= ' endforeach; ?>';
}
return $_html_result;
}

?>