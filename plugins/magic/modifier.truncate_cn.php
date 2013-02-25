<?php

function magic_modifier_truncate_cn($string,$strlen = '20'){
$str_length=strlen($string);
while (($n<$strlen) and ($i<=$str_length))
{
$temp_str=substr($string,$i,1);
$ascnum=Ord($temp_str);
if ($ascnum>=224)
{
$returnstr=$returnstr.substr($string,$i,3);
$i=$i+3;
$n++;
}elseif ($ascnum>=192)
{
$returnstr=$returnstr.substr($string,$i,2);
$i=$i+2;
$n++;
}else
{
$returnstr=$returnstr.substr($string,$i,1);
$i=$i+1;
$n=$n+0.5;
}
}
if($i<$str_length)$returnstr.=$endstr;
return $returnstr;
}

?>