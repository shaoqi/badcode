<?php

function magic_modifier_age_format($birthday,$format = ''){
if($birthday=="") return "";
$birthday_year = date("Y",$birthday);
$now_year = date("Y",time());
return $now_year-$birthday_year;
}

?>