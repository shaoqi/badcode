<?php

function magic_modifier_idedu($user_id,$size){
global $db_config;
if ($user_id=="") return "";
$result =  "/data/idcard/".md5($user_id.$db_config['partnerId']."dyp2peducation").".jpg";
return $result;
}

?>