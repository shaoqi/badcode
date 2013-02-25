<?php

function magic_modifier_html_format($string,$type = ''){
$string = preg_replace("/<.+?>/i","",$string);
return $string;
}

?>