<?php

if ($_COOKIE["dy_cookie_status"]==1){
$_G["user_id"] = GetCookies(array("cookie_status"=>1));
}else{
$_G["user_id"] = GetCookies(array("cookie_status"=>$_G['system']['con_cookie_status']));
}
$_G['upimg']['file'] = "imgFile";
$_G['upimg']['code'] = "dyeditor";
$_G['upimg']['filesize'] = "2048";
$_G['upimg']['type'] = "article";
$_G['upimg']['user_id'] = $_REQUEST['user_id'];
$_G['upimg']['article_id'] = $_POST['id'];
$_G['upimg']['cut_status'] = 1;
$_G['upimg']['cut_width'] = 680;
$uploadfiles = $upload->UpfileSwfupload($_G['upimg']);
$url = $uploadfiles['filename'];
echo  $_G['user_id'];
echo json_encode(array('error'=>0,'url'=>$url));
exit;

?>