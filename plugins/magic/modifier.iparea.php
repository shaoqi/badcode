<?php

function magic_modifier_iparea($string)
{
require_once(ROOT_PATH.'libs/qqwry.class.php');
$qqwry=new QQWry;
$qqwry->QQWry($string);
$get_ip =$qqwry->Country;
return $get_ip;
}
?>