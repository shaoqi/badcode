<?php

function magic_modifier_idcard($user_id,$size){
global $db_config;
$uid = isset($user_id)?$user_id:"";
$size = isset($size)?$size:"big";
$type = isset($data['type'])?$data['type']:"";
$istrue = isset($data['istrue'])?$data['istrue']:false;
$size = in_array($size,array('big','middle','small')) ?$size : 'middle';
$uid = abs(intval($uid));
$typeadd = $type == 'real'?'_real': '';
$result =  "/data/idcard/".md5($uid.$db_config['partnerId']."dyp2pcardid").".jpg";
return $result;
}

?>