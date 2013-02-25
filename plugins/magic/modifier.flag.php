<?php

function magic_modifier_flag($id,$parse_var = '',$magic_vars = ''){
$flag_list = $magic_vars["_A"]['flag_list'];
$id = empty($id)?array():explode(",",$id);
$display = "";
if ($parse_var == "input"){
foreach ($flag_list as $key =>$value){
if (in_array($value['nid'],$id)){
$display .= "<input type='checkbox' name='flag[]' value='{$value['nid']}' checked>{$value['name']} ";
}else{
$display .= "<input type='checkbox' name='flag[]' value='{$value['nid']}' >{$value['name']} ";
}
}
}else{
foreach ($flag_list as $key =>$value){
if (in_array($value['nid'],$id)){
$display .= $value['name'] ." ";
}
}
}
return $display;
}

?>