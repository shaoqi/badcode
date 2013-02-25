<?php

function magic_modifier_in_array($string,$arr = ''){
if (!is_array($string)){
$string = explode(",",$string);
}
$_result = array();
if ($string !=""){
foreach ($string as $key =>$value){
$_result[] = $arr[$value];
}
}
return join(",",$_result);
}
?>