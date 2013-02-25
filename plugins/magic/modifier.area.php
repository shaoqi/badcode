<?php

function magic_modifier_area($area_id,$parse_var = '',$magic_vars = ''){
$province_name = $city_name = $area_name = "";
foreach ($magic_vars["_G"]['arealist'] as $key =>$value){
$arealist[$value['id']]['pid'] = $value['pid'];
$arealist[$value['id']]['name'] = $value['name'];
}
if ($area_id>0){
$area_name = $arealist[$area_id]['name'];
$city_id = $arealist[$area_id]['pid'];
if ($city_id!=0){
$city_name = $arealist[$city_id]['name'];
$province_id = $arealist[$city_id]['pid'];
if ($province_id!=0){
$province_name = $arealist[$province_id]['name'];
$province_pid = $arealist[$city_id]['pid'];
}else{
$province_name = $city_name;
$province_pid = $city_id;
$city_name = $area_name;
$city_id = $area_id;
}
}else{
$province_name = $area_name;
$province_id = $area_id;
}
}else{
return "";
}
$display = "";
$_par = array();
$_par = explode(",",$parse_var);
if ( in_array("p",$_par) ||$parse_var==""){
$display .= $province_name." ";
}
if ( in_array("c",$_par) ||$parse_var==""){
$display .= $city_name." ";
}
if ( in_array("a",$_par) ||$parse_var==""){
$display .= $area_name." ";
}
return $display;
}

?>