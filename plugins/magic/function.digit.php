<?php

function magic_function_digit($parse_var){
global $mysql,$_G;
foreach($parse_var as $_key =>$_val) {
switch($_key) {
case 'name':
$$_key = (string)$_val;
break;
case 'start':
$$_key = (string)$_val;
break;
case 'end':
$$_key = $_val;
break;
case 'default':
$$_key = $_val;
break;
case 'value':
$$_key = $_val;
break;
case 'style':
$$_key = $_val;
break;
case 'class':
$$_key = $_val;
break;
default:
trigger_error("years: extra attribute '$_key' cannot be an array",E_USER_NOTICE);
break;
}
}
$display = "<? ";
$display .=  " echo \"<select name='$name' id=$name  style='$style' class='class'>\";";
if ($default!=""){
$display .=  " echo \"<option value=''>".urldecode($default)."</option>\";";
}
if ($start<$end){
$display .= "  for (\$i=$start;\$i<=$end;\$i++): ";
$display .="if (\$i==$value) { ";
$display .=  "echo \"<option value='{\$i}' selected>{\$i}</option>\"; ";
$display .=  "}else{echo \"<option value='{\$i}' >{\$i}</option>\";} ";
$display .= "endfor;";
}else{
$display .= "  for (\$i=$start;\$i>=$end;\$i--): ";
$display .="if (\$i==$value) { ";
$display .=  "echo \"<option value='{\$i}' selected>{\$i}</option>\"; ";
$display .=  "}else{echo \"<option value='{\$i}' >{\$i}</option>\";} ";
$display .= "endfor;";
}
$display .=  "echo \"</select>\";";
$display .= " ?>";
return $display;
}

?>