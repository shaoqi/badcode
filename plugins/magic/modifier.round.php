<?php

function magic_modifier_round($string,$format = ''){
if ($string<=0) return "";
if ($string != ""&&is_numeric($string) ){
return round($string,$format);
}else{
return $string;
}
}

?>