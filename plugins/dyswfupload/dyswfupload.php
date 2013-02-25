<?php

$_G['upimg']['file'] = "Filedata";
$_G['upimg']['code'] = "share";
$_G['upimg']['filesize'] = "2048";
$_G['upimg']['type'] = "pic";
$_G['upimg']['user_id'] = $_G['user_id'];
$_G['upimg']['article_id'] = $_POST['id'];
$_G['upimg']['cut_status'] = 1;
$_G['upimg']['cut_width'] = 610;
$uploadfiles = $upload->UpfileSwfupload($_G['upimg']);
$url = $uploadfiles['filename'];
$data['new_url'] = substr($url,0,strlen($url)-4)."_100_100".substr($url,-4,4);
if (!file_exists(ROOT_PATH.$data['new_url'])){
$data['cut_status'] =1;
$data['cut_type'] = 2;
$data['url'] = $url;
$data['cut_width'] =100;
$data['cut_height'] = 100;
$upload->litpic($data);
}
echo json_encode(array("url"=>$data['new_url'],"id"=>$uploadfiles['upfiles_id']));
?>