<?php

function magic_modifier_surplus($begin_time,$parse_var = '',$magic_vars = ''){
$end_time = time();
if($begin_time <$end_time){
$starttime = $begin_time;
$endtime = $end_time;
}
else{
$starttime = $end_time;
$endtime = $begin_time;
}
$timediff = $endtime-$starttime;
$days = intval($timediff/86400);
$remain = $timediff%86400;
$hours = intval($remain/3600);
$remain = $remain%3600;
$mins = intval($remain/60);
$secs = $remain%60;
$res = array("day"=>$days,"hour"=>$hours,"min"=>$mins,"sec"=>$secs);
$_res = "";
if ($days>0){
$_res .= $days."Ìì";
}
if ($hours>0){
$_res .= $hours."Ê±";
}
if ($mins>0){
$_res .= $mins."·Ö";
}
if ($mins>0){
$_res .= $mins."Ãë";
}
return $_res;
}
?>