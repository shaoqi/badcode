<?php

function magic_modifier_week_format($data){
global $_G;
if ($data=="") return "";
$_data = date("w",$data);
$var = array("1"=>"一","2"=>"二","3"=>"三","4"=>"四","5"=>"五","6"=>"六","0"=>"天");
return "周".$var[$_data];
}

?>