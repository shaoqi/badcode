<?php

function magic_modifier_default($string,$default = ''){
if (!isset($string) ||$string === ''||$string == null){
if ($default == "nowtime") return time();
if ($default == "nowdate") return date("Y-m-d H:i:s",time());
return $default;
}else{
return $string;
}
}

?>