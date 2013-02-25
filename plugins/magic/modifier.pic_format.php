<?php

function magic_modifier_pic_format($string,$type = ''){
preg_match_all("/<img.*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)/i",str_ireplace("\\","",$string),$arr);
$_type = explode(",",$type);
$pic_num = isset($_type[0])?$_type[0]:"";
$string_num = isset($_type[1])?$_type[1]:"";
$pic_width = (isset($_type[2]) &&$_type[2]!="")?" width='".$_type[2]."'":"";
$pic_height = isset($_type[3])?" height='".$_type[3]."'":"";
if (count($arr[1])>0){
$result = $arr[1];
if ($pic_num!=""){$result = array_slice($result,0,$pic_num);}
$_result = "";
foreach ($result as $key =>$value){
$_result .= "<img ".$pic_width.$pic_height." src='".$value."'/>";
}
return $_result;
}else{
if ($string_num!=""){
require_once(ROOT_PATH."plugins/magic/modifier.truncate_cn.php");
require_once(ROOT_PATH."plugins/magic/modifier.html_format.php");
return magic_modifier_truncate_cn(magic_modifier_html_format($string),$string_num)."...";
}else{
return $string;
}
}
}

?>