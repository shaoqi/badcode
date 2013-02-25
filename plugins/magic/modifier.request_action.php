<?php

function magic_modifier_request_action($string,$action = ''){
if ($string =="") return "";
return "&{$action}={$string}";
}

?>