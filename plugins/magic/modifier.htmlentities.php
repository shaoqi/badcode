<?php

function magic_modifier_htmlentities($string,$default = ''){
if (!isset($string) ||$string === ''||$string == null){
return "";
}else{
return htmlentities($string);
}
}

?>