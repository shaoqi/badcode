<?php

function magic_modifier_user_url($user_id,$atr){
if ($user_id!=""){
return "/user/".$user_id.$atr;
}else{
return "javascript:void(0)";
}
}

?>