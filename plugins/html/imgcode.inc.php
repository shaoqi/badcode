<?php

session_register("valicode");
$width=50;
$height= isset($_REQUEST['height'])?$_REQUEST['height']:18;
$rand_str = "";
for($i=0;$i<4;$i++){
$rand_str .= chr(mt_rand(48,57));
}
if(function_exists("imagecreate")){
$_SESSION["valicode"]=strtolower($rand_str);
$img = imagecreate($width,$height);
imagecolorallocate($img,255,255,255);
$black = imagecolorallocate($img,127,157,185);
for ($i=1;$i<=50;$i++) {
imagestring($img,1,mt_rand(1,$width),mt_rand(1,$height),"#",imagecolorallocate($img,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255)));
}
for($i=0;$i<4;$i++){
imagestring($img,mt_rand(2,5),$i*10+6,mt_rand(2,5),$rand_str[$i],imagecolorallocate($img,mt_rand(0,100),mt_rand(0,150),mt_rand(0,200)));
}
imagerectangle($img,0,0,$width-1,$height-1,$black);
if(function_exists("imagejpeg")){
header("content-type:image/jpeg\r\n");imagejpeg($img);
}else{
header("content-type:image/png\r\n");imagepng($img);
}
imagedestroy($img);
}else{
$_SESSION["valicode"]="1234";
header("content-type:image/jpeg\r\n");
$fp = fopen("./valicode.bmp","r");
echo fread($fp,filesize("./validate.bmp"));
fclose($fp);
}
?>