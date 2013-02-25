<?php

function magic_modifier_date_format($string,$format = ''){
if ($string<=0) return "";
if ($string != ""&&is_numeric($string) ){
if ($format=="") $format= "Y-m-d H:i:s";
return date($format,$string);
}else{
return $string;
}
}
?>