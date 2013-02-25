<?php

function magic_modifier_areas($area_id,$parse_var = '',$magic_vars = ''){
$province_name = $city_name = $area_name = "";
foreach ($magic_vars["_G"]['areas'] as $key =>$value){
$arealist[$value['id']] = $value;
}
if ($area_id>0){
$area_result = $arealist[$area_id];
if ($area_result['province']>0){
if ($area_result['city']>0){
$province_name = $arealist[$area_result['province']]['name'];
$city_name = $arealist[$area_result['city']]['name'];
$area_name = $area_result['name'];
}
else{
$province_name = $arealist[$area_result['province']]['name'];
$city_name = $area_result['name'];
$area_name = "";
}
}
else{
$province_name = $area_result['name'];
$city_name = "";
$area_name = "";
}
}else{
return "";
}
$display = "";
$_par = array();
$_par = explode(",",$parse_var);
if ( in_array("p",$_par)){
$display .= $province_name." ";
}
if ( in_array("c",$_par)){
$display .= $city_name." ";
}
if ( in_array("a",$_par)){
$display .= $area_name." ";
}
if($parse_var==""){
$display  = $area_result['name'];
}
return $display;
}

?>