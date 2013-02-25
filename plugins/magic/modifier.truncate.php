<?php

function magic_modifier_truncate($string,$length = '20'){
if ($length == 0) {return '';}
$pa = "/[\x01-\x7f]|[\xa1-\xff][\xa1-\xff]/";
preg_match_all($pa,$string,$t_string);
if (count($t_string[0]) >$length)
return join('',array_slice($t_string[0],0,$length));
return join('',array_slice($t_string[0],0,$length));
$tmpstr = "";
for($i = 0;$i <$strlen;$i++) {
if(ord(substr($string,$i,1)) >0xa0) {
$tmpstr .= substr($string,$i,2);
$i++;
}else
$tmpstr .= substr($string,$i,1);
}
return $tmpstr;
}

?>