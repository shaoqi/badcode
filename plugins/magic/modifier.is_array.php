<?php

function magic_modifier_is_array($string,$arr = '',$parse = ''){
if (!is_array($string)){
$string = explode(",",$string);
}
var_dump($arr);
if (in_array($arr,$string)){
return "true";
}else{
return "false";
}
}

?>