<?php

function magic_modifier_checked($string,$arr = ''){
if (!isset($string) ||!isset($arr) ){
return "";
}else{
if (is_array($arr) &&$arr[$string]!=""){
return "checked";
}else{
$_arr = explode(",",$arr);
if (in_array($string,$_arr)){
return "checked";
}
$string = explode(",",$string);
if (in_array($arr,$string)){
return "checked";
}
}
}
return "";
}

?>