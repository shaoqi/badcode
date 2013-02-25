<?php

function magic_modifier_credit($integral,$parse_var = '',$magic_vars = ''){
global $mysql,$_G;
if ($integral==""&&$integral!="0") return "";
if ($_G['credit']['rank']=="") return "";
$_result = array();
foreach ($_G['credit']['rank'] as $key =>$value){
$_result[$value['class_nid']][] = $value;
}
if ($parse_var==""){
$result = $_result[0];
}else{
$result = $_result[$parse_var];
}
if (count($result)>0){
foreach ($result as $key=>$value){
if ($value['point1']<=$integral &&$value['point2']>=$integral){
return "<img src='/data/images/credit/".$value['pic']."' title='{$integral}·Ö'>";
}elseif ($integral<=0 &&$value['point2']==0){
return "<img src='/data/images/credit/".$value['pic']."' title='{$integral}·Ö'>";
}
}
}
}
?>