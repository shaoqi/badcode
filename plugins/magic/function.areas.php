<?php

function magic_function_areas($data){
global $_G;
$type = !IsExiest($data['type'])?"p,c":$data['type'];
$name = !IsExiest($data['name'])?"":$data['name'];
$ajax = !IsExiest($data['ajax'])?"":$data['ajax'];
$class = !IsExiest($data['class'])?"":$data['class'];
$value = !IsExiest($data['value'])?"":$data['value'];
$areas_result = $_G['areas'];
$display = "<script src=\"/?plugins&q=areas&name=$name&type=$type&class=$class&area=<? echo $value; ?>\" ></script>";
return $display;
}

?>