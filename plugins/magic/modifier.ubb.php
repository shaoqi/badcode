<?php

function magic_modifier_ubb($string,$default = ''){
global $ubb;
return $ubb->ubb2html($string);
}
?>