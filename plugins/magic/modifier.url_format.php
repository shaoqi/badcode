<?php

function magic_modifier_url_format($url,$format = ''){
if ($url=="") return "?";
$_url =  explode("?",$url);
$request = $_url[1];
$_url_for = "";
if ($request != ""){
$_request = explode("&",$request);
foreach ($_request as $key =>$value){
$_value = explode("=",$value);
if (trim($_value[0])!=$format){
$_url_for .="&".$value;
}
}
}
$_url_for = "?".substr($_url_for,1,strlen($_url_for));
return $_url_for;
}

?>