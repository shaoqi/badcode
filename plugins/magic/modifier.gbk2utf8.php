<?php

function magic_modifier_gbk2utf8($string,$default = ''){
if ($string=="") return "";
$str = iconv("GBK","UTF-8",$string);
return $str;
}
?>