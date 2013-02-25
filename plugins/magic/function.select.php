<?php

function magic_function_select($parse_var){
global $mysql,$_G;
foreach($parse_var as $_key =>$_val) {
switch($_key) {
case 'name':
$$_key = (string)$_val;
break;
case 'table':
$$_key = (string)$_val;
break;
case 'result':
$$_key = (string)$_val;
break;
case 'value':
$$_key = $_val;
break;
case 'selected':
$$_key = $_val;
break;
case 'style':
$$_key = $_val;
break;
case 'select_name':
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
if ($result!=""){
$display = "<? \$result = $result;";
}else{
$display .= "<? \$sql = 'select $name,$value from `{".$table."}`';";
$display .= "\$result = \$this->magic_vars['_G']['mysql']->db_fetch_arrays(\$sql);";
$display .= "";
}
$display .=  " echo \"<select name='$select_name' id=$select_name  style='$style'>\";";
if ($default!=""){
$display .=  " echo \"<option value=''>".urldecode($default)."</option>\";";
}
$display .= " if (IsExiest(\$result)!=false): foreach (\$result as \$key => \$val): ";
if ($selected!=""){
if ($selected{0}== '$'){
$display .="if ($selected==\$val['$value'] ) { ";
}else{
$display .="if ('$selected'==\$val['$value'] ) { ";;
}
$display .=  "echo \"<option value='{\$val['$value']}' selected>{\$val['$name']}</option>\"; ";
$display .=  "}else{echo \"<option value='{\$val['$value']}' >{\$val['$name']}</option>\";} ";
}else{
$display .=  "echo \"<option value='{\$val['$value']}' >{\$val['$name']}</option>\";";
}
$display .= "endforeach;";
$display .= "endif; ";
$display .=  "echo \"</select>\";?>";
return $display;
}

?>