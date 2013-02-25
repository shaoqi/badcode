<?php

function magic_function_checkbox($parse_var){
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
case 'checked':
$$_key = $_val;
break;
case 'style':
$$_key = $_val;
break;
case 'checkbox_name':
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
}
if ($checked!=""){
if ($checked{0}== '$'){
$display .= " \$_checked =  explode(',',$checked);";
}else{
$display .= " \$_checked =  explode(',','$checked');";
}
}
$display .= " if (IsExiest(\$result)!=false): foreach (\$result as \$key => \$val): ";
if ($checked!=""){
$display .=" if (in_array(\$val['$value'],\$_checked)) { ";
$display .=  "echo \"<input type='checkbox' name='".$checkbox_name."[]' value='{\$val['$value']}' checked>{\$val['$name']} \"; ";
$display .=  "}else{echo \"<input type='checkbox' name='".$checkbox_name."[]' value='{\$val['$value']}' >{\$val['$name']} \";} ";
}else{
$display .=  "echo \"<input type='checkbox' name='".$checkbox_name."[]' value='{\$val['$value']}' >{\$val['$name']} \";";
}
$display .= "endforeach;";
$display .= "endif; ?>";
return $display;
}

?>