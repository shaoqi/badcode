<?php

function magic_modifier_avatar($user_id,$size){
$uid = isset($user_id)?$user_id:"";
$size = isset($size)?$size:"big";
$type = isset($data['type'])?$data['type']:"";
$istrue = isset($data['istrue'])?$data['istrue']:false;
$size = in_array($size,array('big','middle','small')) ?$size : 'middle';
$uid = abs(intval($uid));
$typeadd = $type == 'real'?'_real': '';
if (is_file('data/avatar/'.$uid.$typeadd."_avatar_$size.jpg")){
if ($istrue) return true;
return '/data/avatar/'.$uid.$typeadd."_avatar_$size.jpg";
}else{
if ($istrue) return false;
return "/data/images/avatar/noavatar_{$size}.gif";
}
}
?>