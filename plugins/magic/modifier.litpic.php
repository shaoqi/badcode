<?php

function magic_modifier_litpic($url,$format = ''){
global $upload;
if ($url=="") return "/data/images/base/no_pic.gif";
$_format = explode(",",$format);
$width = (isset($_format[0]) &&$_format[0]!="")?$_format[0]:100;
$height = (isset($_format[1]) &&$_format[1]!="")?$_format[1]:100;
$data['new_url'] = substr($url,0,strlen($url)-4)."_{$width}_{$height}".substr($url,-4,4);
if (file_exists(ROOT_PATH.$url)){
if (!file_exists(ROOT_PATH.$data['new_url'])){
$data['cut_status'] =1;
$data['cut_type'] = 2;
$data['url'] = $url;
$data['cut_width'] = $width;
$data['cut_height'] = $height;
$upload->litpic($data);
}
}
return $data['new_url'];
}

?>