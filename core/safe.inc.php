<?php

if (!defined('DEAYOU_PATH'))  die('不能访问');
function safe_str($str){
if(!get_magic_quotes_gpc())	{
if( is_array($str) ) {
foreach($str as $key =>$value) {
$str[$key] = safe_str($value);
}
}else{
$str = addslashes($str);
}
}
return $str;
}
$request_uri = explode("?",$_SERVER['REQUEST_URI']);
if(isset($request_uri[1])){
$rewrite_url = explode("&",$request_uri[1]);
foreach ($rewrite_url as $key =>$value){
$_value = explode("=",$value);
if (isset($_value[1])){
$_REQUEST[$_value[0]] = addslashes($_value[1]);
}
}
}
foreach($_GET as $_key =>$_value){
$_GET[$_key] = safe_str($_value);
}
foreach($_POST as $_key =>$_value){
$_POST[$_key] = safe_str($_value);
}
foreach($_REQUEST as $_key =>$_value){
$_REQUEST[$_key] = safe_str($_value);
}
foreach($_COOKIE as $_key =>$_value){
$_COOKIE[$_key] = safe_str($_value);
}
if($_FILES){
if(isset($_FILES['GLOBALS'])) exit('不允许上传GLOBALS!');
$con_not_allowall = "php|pl|cgi|asp|aspx|jsp|php3|shtm|shtml";
$keyarr = array('name','type','tmp_name','size');
foreach($_FILES as $_key=>$_value)
{
foreach($keyarr as $k)
{
if(!isset($_FILES[$_key][$k]))
{
exit('操作有误!');
}
}
if( preg_match('#^(con_|GLOBALS)#',$_key) )
{
exit('不允许上传!');
}
$$_key = $_FILES[$_key]['tmp_name'] = str_replace("\\\\","\\",$_FILES[$_key]['tmp_name']);
${$_key.'_name'}= $_FILES[$_key]['name'];
${$_key.'_type'}= $_FILES[$_key]['type'] = preg_replace('#[^0-9a-z\./]#i','',$_FILES[$_key]['type']);
${$_key.'_size'}= $_FILES[$_key]['size'] = preg_replace('#[^0-9]#','',$_FILES[$_key]['size']);
if(empty(${$_key.'_size'}))
{
${$_key.'_size'}= @filesize($$_key);
}
$imtypes = array
(
"image/pjpeg","image/jpeg","image/gif","image/png",
"image/xpng","image/wbmp","image/bmp"
);
if(in_array(strtolower(trim(${$_key.'_type'})),$imtypes))
{
$image_dd = @getimagesize($$_key);
if (!is_array($image_dd))
{
exit('不允许上传的类型!');
}
}
}
}
?>