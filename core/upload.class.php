<?php

if (!defined('ROOT_PATH'))  die('不能访问');
class uploadClass
{
var $dst_img;
var $h_src;
var $h_dst;
var $h_mask;
var $img_create_quality = 100;
var $img_display_quality = 100;
var $img_scale = 0;
var $dst_w = 0;
var $dst_h = 0;
var $fill_w;
var $fill_h;
var $copy_w;
var $copy_h;
var $src_x = 0;
var $src_y = 0;
var $start_x;
var $start_y;
var $mask_word;
var $mask_img;
var $mask_pos_x = 0;
var $mask_pos_y = 0;
var $mask_offset_x = 5;
var $mask_offset_y = 5;
var $font_w;
var $font_h;
var $mask_w;
var $mask_h;
var $mask_font_color = "#ffffff";
var $mask_font = 2;
var $font_size;
var $mask_position = 0;
var $mask_img_pct = 50;
var $mask_txt_pct = 50;
var $img_border_size = 0;
var $img_border_color;
var $_flip_x=0;
var $_flip_y=0;
var $pic_w;
var $pic_h;
var $file_upname;
var $src_w = "";
var $src_h = "";
var $file_name="";
var $file_maxsize="300";
var $file_dir="data/upfiles";
var $file_type = array('jpg','gif','bmp','png');
var $file_quality = "100";
var $cut_width="";
var $cut_height="";
var $cut_scale="50";
var $cut_type = "";
var $img_type;
var $all_type = array(
"jpg"=>array("output"=>"imagejpeg"),
"gif"=>array("output"=>"imagegif"),
"png"=>array("output"=>"imagepng"),
"wbmp"=>array("output"=>"image2wbmp"),
"jpeg"=>array("output"=>"imagejpeg"));
function upload() {
$this->mask_font_color = "#ffffff";
$this->font = 2;
$this->font_size = 12;
}
function setData($data = array()){
if (isset($data['file_newname']) &&$data['file_newname']!=""){
$this->setDstImg($data['file_newname']);
}
$this->file_dir = isset($data['file_dir'])?$data['file_dir']:"data/upfiles/images/";
$this->file_dir .= date("Y-m",time())."/".date("d",time())."/";
$this->file_type = isset($data['file_type'])?$data['file_type']:array('jpg','gif','JPG','bmp');
$this->file_size = isset($data['file_size'])?$data['file_size']:"2048";
$this->file_quality = isset($data['file_quality'])?$data['file_quality']:"80";
if (isset($data['file_newname']) &&$data['file_newname']!=""){
$this->file_newname = $data['file_newname'];
}else{
$this->file_newname = (isset($data['user_id'])?$data['user_id']:"0")."_".(isset($data['code'])?$data['code']:"system")."_{$data['type']}_".time().rand(100,999);
}
$this->cut_status = isset($data['cut_status'])?$data['cut_status']:"";
if ($this->cut_status==1){
$this->cut_type = isset($data['cut_type'])?$data['cut_type']:"";
$this->cut_scale = isset($data['cut_scale'])?$data['cut_scale']:"";
$this->cut_width = isset($data['cut_width'])?$data['cut_width']:"";
$this->cut_height = isset($data['cut_height'])?$data['cut_height']:"";
$this->cut_quality = isset($data['cut_quality'])?$data['cut_quality']:"";
}
$this->mask_status = isset($data['mask_status'])?$data['mask_status']:"";
if ($this->mask_status==1){
$this->mask_word = isset($data['mask_word'])?$data['mask_word']:"";
$this->mask_font_color = isset($data['mask_color'])?$data['mask_color']:"#ffffff";
$this->font_size = isset($data['mask_size'])?$data['mask_size']:"";
$this->font = isset($data['mask_font'])?$data['mask_font']:"";
$this->mask_img = isset($data['mask_img'])?$data['mask_img']:"";
$this->mask_position = isset($data['mask_position'])?$data['mask_position']:"1";
}
}
function upfile($data = array()){
global $mysql;
$error = "";
if (!isset($data['file'])) return "";
$filename = isset($data['filename'])?$data['filename']:"";
$file = $data['file'];
if(!isset($_FILES[$file]['name'])) return "";
$this->setData($data);
$newDir = ROOT_PATH.$this->file_dir;
$err_var = array("-2"=>"文件不存在","-3"=>"图片类型不正确","-4"=>"不是图片类型","-5"=>"上传图片过大");
if (is_array($_FILES[$file]['name'])){
$_result = array();
foreach($_FILES[$file]['name'] as $i =>$value){
if ($value!=""){
$this->img_type = strtolower(substr($_FILES[$file]['name'][$i],-3,3));
if ($_FILES[$file]['size'][$i]==0)	$error = -2;
if(!in_array($this->img_type,$this->file_type)) $error = -3;
if(strpos($_FILES[$file]['type'][$i],'image')===false) $error = -4;
if($_FILES[$file]['size'][$i] >$this->file_size*1024) $error = -5;
if($_FILES[$file]['error'][$i] !=0 ) $error = -2;
$this->_mkdirs($this->file_dir);
$newFile = $this->file_newname.$i.substr($_FILES[$file]['name'][$i],-4,4);
$oldFile = $_FILES[$file]['name'][$i];
$allFile = $newDir.$newFile;
if ($error<0){
echo "<script>alert('".$err_var[$error]."');history.go(-1);</script>";
exit;
}
if(function_exists('move_uploaded_file')){
$result = move_uploaded_file($_FILES[$file]['tmp_name'][$i],$allFile);
}else{
@copy($_FILES[$file]['tmp_name'][$i],$allFile);
}
$this->setSrcImg($allFile);
$this->setCutimg();
$this->setDstImg($allFile);
$this->_createMask();
$this->_output();
if($data['name'][$i]==""){
$_name = $oldFile;
}elseif(!is_array($data['name'])){
$_name = $data['name'];
}else{
$_name = $data['name'][$i];
}
if ($error==""){
$sql = "insert into `{users_upfiles}` set code='{$data['code']}',type='{$data['type']}',article_id='{$data['article_id']}',user_id='{$data['user_id']}',`name`='{$_name}',filesize='{$_FILES[$file]['size'][$i]}',filetype='{$this->img_type}',fileurl='".$this->file_dir.$newFile."',filename='".$newFile."',`addtime` = '".time()."', `updatetime` = '".time()."',`addip` = '".ip_address()."',`updateip` = '".ip_address()."'";
$mysql ->db_query($sql);
$upfiles_id = $mysql->db_insert_id();
$_result[$i]['filename'] = $this->file_dir.$newFile;
$_result[$i]['upfiles_id'] = $upfiles_id;
}
}
}
return $_result;
}else{
$this->img_type = strtolower(substr($_FILES[$file]['name'],-3,3));
if ($_FILES[$file]['size']==0)	return "";
if(!in_array($this->img_type,$this->file_type)) $error = -3;
if(strpos($_FILES[$file]['type'],'image')===false) $error = -4;
if($_FILES[$file]['size'] >$this->file_size*1024) $error = -5;
if($_FILES[$file]['error'] !=0 ) $error = -2;
$this->_mkdirs($this->file_dir);
$newFile = $this->file_newname.substr($_FILES[$file]['name'],-4,4);
$oldFile = $_FILES[$file]['name'];
$allFile = $newDir.$newFile;
if ($error!=""){
echo "<script>alert('".$err_var[$error]."');history.go(-1);</script>";
exit;
}
if(function_exists('move_uploaded_file')){
$result = move_uploaded_file($_FILES[$file]['tmp_name'],$allFile);
}else{
@copy($_FILES[$file]['tmp_name'],$allFile);
}
$this->setSrcImg($allFile);
$this->setCutimg();
$this->setDstImg($allFile);
$this->_createMask();
$this->_output();
if($data['name']==""){
$_name = $oldFile;
}else{
$_name = $data['name'];
}
if ($error==""){
$sql = "insert into `{users_upfiles}` set code='{$data['code']}',type='{$data['type']}',article_id='{$data['article_id']}',user_id='{$data['user_id']}',`name`='{$_name}',filesize='{$_FILES[$file]['size'][$i]}',filetype='{$this->img_type}',fileurl='".$this->file_dir.$newFile."',filename='".$newFile."',`addtime` = '".time()."', `updatetime` = '".time()."',`addip` = '".ip_address()."',`updateip` = '".ip_address()."'";
$mysql ->db_query($sql);
$upfiles_id = $mysql->db_insert_id();
return array(0=>array("filename"=>$this->file_dir.$newFile,"upfiles_id"=>$upfiles_id));
}
return "";
}
}
function litpic ($data = array()){
$this->img_type = strtolower(substr($data['url'],-3,3));
$this->setData($data);
$this->setSrcImg(ROOT_PATH.$data['url']);
$this->setCutimg();
$this->setDstImg(ROOT_PATH.$data['new_url']);
$this->_createMask();
$this->_output();
}
function UpfileSwfupload($data = array()){
global $mysql,$_G;
$error = "";
$file = $data['file'];
$dateFile = date("Y-m",time());
$this->setData($data);
$newDir = ROOT_PATH.$this->file_dir;
$count = $num = 0;
$error_msg = array();
$err_var = array("-2"=>"文件不存在","-3"=>"图片类型不正确","-4"=>"不是图片类型","-5"=>"上传图片过大");
$_result = array();
$this->img_type = strtolower(substr($_FILES[$file]['name'],-3,3));
if ($_FILES[$file]['size']==0)	$error = -2;
if ($error<0) return;
$this->_mkdirs($this->file_dir);
if ($data['user_id']<=0 ||$data['user_id']==""){
$data['user_id'] = $_G['user_id'];
}
$newFile = $this->file_newname.$i.rand(100,999).substr($_FILES[$file]['name'],-4,4);
$oldFile = $_FILES[$file]['name'];
$allFile = $newDir.$newFile;
if(function_exists('move_uploaded_file')){
$result = move_uploaded_file($_FILES[$file]['tmp_name'],$allFile);
}else{
@copy($_FILES[$file]['tmp_name'],$allFile);
}
$this->setSrcImg($allFile);
$this->setCutimg();
$this->setDstImg($allFile);
$this->_createMask();
$this->_output();
if($data['name']==""){
$_name = $oldFile;
}else{
$_name = $data['name'];
}
$this->file_dir = "/".$this->file_dir;
$sql = "insert into `{users_upfiles}` set code='{$data['code']}',type='{$data['type']}',article_id='{$data['article_id']}',user_id='{$data['user_id']}',`name`='{$_name}',filesize='{$_FILES[$file]['size'][$i]}',filetype='{$this->img_type}',fileurl='".$this->file_dir.$newFile."',filename='".$newFile."',`addtime` = '".time()."', `updatetime` = '".time()."',`addip` = '".ip_address()."',`updateip` = '".ip_address()."'";
$mysql ->db_query($sql);
$upfiles_id = $mysql->db_insert_id();
return array("filename"=>$this->file_dir.$newFile,"upfiles_id"=>$upfiles_id);
}
function UpfileXheditorUpload($data = array()){
global $mysql,$_G;
$error = "";
$file = $data['file'];
$dateFile = date("Y-m",time());
$this->setData($data);
$newDir = ROOT_PATH.$this->file_dir;
$count = $num = 0;
$error_msg = array();
if ($data['user_id']<=0 ||$data['user_id']==""){
$data['user_id'] = $_G['user_id'];
}
$err_var = array("-2"=>"文件不存在","-3"=>"图片类型不正确","-4"=>"不是图片类型","-5"=>"上传图片过大");
$_result = array();
$tempPath=$this->file_dir.'/'.date("YmdHis").mt_rand(10000,99999).'.tmp';
if(isset($_SERVER['HTTP_CONTENT_DISPOSITION'])&&preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i',$_SERVER['HTTP_CONTENT_DISPOSITION'],$info)){
file_put_contents($tempPath,file_get_contents("php://input"));
$localName=urldecode($info[2]);
$bytes=filesize($tempPath);
if ($bytes >$this->file_size*1024 ) $error = -5;
$fileInfo=pathinfo($localName);
$extension=$fileInfo['extension'];
$newFile = $this->file_newname.".".$extension;
$oldFile = $localName;
$allFile = $newDir.$newFile;
rename($tempPath,$allFile);
}
else{
$this->img_type = strtolower(substr($_FILES[$file]['name'],-3,3));
if ($_FILES[$file]['size']==0)	$error = -2;
if($_FILES[$file]['size'] >$this->file_size*1024) $error = -5;
if ($error<0) return $err_var[$error];
$newFile = $this->file_newname.$i.substr($_FILES[$file]['name'],-4,4);
$oldFile = $_FILES[$file]['name'];
$allFile = $newDir.$newFile;
}
$this->_mkdirs($this->file_dir);
@chmod($allFile,0755);
if(function_exists('move_uploaded_file')){
$result = move_uploaded_file($_FILES[$file]['tmp_name'],$allFile);
}else{
@copy($_FILES[$file]['tmp_name'],$allFile);
}
$this->setSrcImg($allFile);
$this->setCutimg();
$this->setDstImg($allFile);
$this->_createMask();
$this->_output();
$this->file_dir = "/".$this->file_dir;
$sql = "insert into `{users_upfiles}` set code='{$data['code']}',type='{$data['type']}',article_id='{$data['article_id']}',user_id='{$data['user_id']}',`name`='{$_name}',filesize='{$_FILES[$file]['size'][$i]}',filetype='{$this->img_type}',fileurl='".$this->file_dir.$newFile."',filename='".$newFile."',`addtime` = '".time()."', `updatetime` = '".time()."',`addip` = '".ip_address()."',`updateip` = '".ip_address()."'";
$mysql ->db_query($sql);
$upfiles_id = $mysql->db_insert_id();
return array("filename"=>$this->file_dir.$newFile,"upfiles_id"=>$upfiles_id);
}
function UpfileVinSwfUpload($data = array()){
global $mysql,$_G;
$error = "";
$file = $data['file'];
$dateFile = date("Y-m",time());
$this->setData($data);
$this->file_dir = "upfiles/";
$newDir = $this->file_dir;
$this->img_type = strtolower(substr($_FILES[$file]['name'],-3,3));
if ($_FILES[$file]['size']==0)	$error = -2;
$newFile = $this->file_newname.$i.substr($_FILES[$file]['name'],-4,4);
$oldFile = $_FILES[$file]['name'];
$allFile = $newDir.$newFile;
$this->_mkdirs($this->file_dir);
@chmod($allFile,0755);
$save_path = getcwd() ."/upfiles/";
$file_name = $this->file_newname.$i.substr($_FILES[$file]['name'],-4,4);
if (!@move_uploaded_file($_FILES[$data['file']]["tmp_name"],$save_path.$file_name)) {
}
$this->setSrcImg($allFile);
$this->setCutimg();
$this->setDstImg($allFile);
$this->_createMask();
$this->_output();
$this->file_dir = "/".$this->file_dir;
$sql = "insert into `{users_upfiles}` set code='{$data['code']}',type='{$data['type']}',article_id='{$data['article_id']}',user_id='{$data['user_id']}',`name`='{$_name}',filesize='{$_FILES[$file]['size'][$i]}',filetype='{$this->img_type}',fileurl='"."/plugins/vinswfupload/".$this->file_dir.$newFile."',filename='".$newFile."',`addtime` = '".time()."', `updatetime` = '".time()."',`addip` = '".ip_address()."',`updateip` = '".ip_address()."'";
$mysql ->db_query($sql);
$upfiles_id = $mysql->db_insert_id();
return array("filename"=>$this->file_dir.$newFile,"upfiles_id"=>$upfiles_id);
}
function upfiles($data = array()){
global $mysql;
$error = "";
$file = $data['file'];
$dateFile = date("Y-m",time());
$this->setData($data);
$newDir = ROOT_PATH.$this->file_dir;
$count = $num = 0;
$error_msg = array();
$err_var = array("-2"=>"文件不存在","-3"=>"图片类型不正确","-4"=>"不是图片类型","-5"=>"上传图片过大");
$_result = array();
foreach($_FILES[$file]['name'] as $i =>$value){
if ($value!=""){
$count ++;
$this->img_type = strtolower(substr($_FILES[$file]['name'][$i],-3,3));
if ($_FILES[$file]['size'][$i]==0)	$error = -2;
if(!in_array($this->img_type,$this->file_type)) $error = -3;
if(strpos($_FILES[$file]['type'][$i],'image')===false) $error = -4;
if($_FILES[$file]['size'][$i] >$this->file_size*1024) $error = -5;
if($_FILES[$file]['error'][$i] !=0 ) $error = -2;
$this->_mkdirs($this->file_dir);
$newFile = md5(time().rand(1,9)).$i.substr($_FILES[$file]['name'][$i],-4,4);
$oldFile = $_FILES[$file]['name'][$i];
$allFile = $newDir.$newFile;
if ($error<0){
$error_msg[] = $oldFile.$err_var[$error];
}else{
if(function_exists('move_uploaded_file')){
$result = move_uploaded_file($_FILES[$file]['tmp_name'][$i],$allFile);
}else{
@copy($_FILES[$file]['tmp_name'][$i],$allFile);
}
$this->setSrcImg($allFile);
$this->setCutimg();
$this->setDstImg($allFile);
$this->_createMask();
$this->_output();
if($data['name'][$i]==""){
$_name = $oldFile;
}else{
$_name = $data['name'][$i];
}
$sql = "insert into `{users_upfiles}` set code='{$data['code']}',aid='{$data['aid']}',user_id='{$data['user_id']}',`name`='{$_name}',filesize='{$_FILES[$file]['size'][$i]}',filetype='{$this->img_type}',fileurl='".$this->file_dir.$newFile."',filename='".$newFile."',`addtime` = '".time()."', `updatetime` = '".time()."',`addip` = '".ip_address()."',`updateip` = '".ip_address()."'";
$mysql ->db_query($sql);
$file_id = $mysql->db_insert_id();
$num++;
}
}
}
if ($num>0){
$sql = "select * from `{users_upfiles}` where code = '{$data['code']}' and aid = '{$data['aid']}' and if_cover = 1";
$result = $mysql->db_fetch_array($sql);
if($result==false){
$sql = "update `{users_upfiles}` set if_cover=1 where id= {$file_id}";
$mysql->db_query($sql);
}
}
$display = "有{$count}张相片上传，其中{$num}上传成功。".join(",",$error_msg);
return $display;
}
function UpdateMore($data = array()){
global $mysql;
foreach ($data['id'] as $key =>$value){
$sql = "update `{users_upfiles}` set `name` = '{$data['name'][$key]}' where `id` = '{$value}' and user_id = '{$data['user_id']}'";
$mysql->db_query($sql);
}
}
function GetOne($data = array()){
global $mysql;
$_sql = "where 1=1 ";
if (isset($data['user_id']) &&$data['user_id']!=""){
$_sql .= " and  p1.user_id='{$data['user_id']}'";
}
if (isset($data['id']) &&$data['id']!=""){
$_sql .= " and  p1.id='{$data['id']}'";
}
$sql = "select * from `{users_upfiles}` as p1 {$_sql}";
return $mysql->db_fetch_array($sql);
}
function Delete($data = array()){
global $mysql;
$_sql = "where id='{$data['id']}'";
if (isset($data['user_id']) &&$data['user_id']!=""){
$_sql .= " and user_id = '{$data['user_id']}'";
}
$sql = "select * from `{users_upfiles}`  {$_sql}";
$result = $mysql->db_fetch_array($sql);
if ($result!=false){
$_dir = explode($result['filename'],$result['fileurl']);
self::DelPic($_dir[0],$result['filename']);
$sql = "delete from `{users_upfiles}` {$_sql}";
$mysql ->db_query($sql);
}
}
function  DelPic($dir,$filename){
$_filename = substr($filename,0,strlen($filename)-4);
if (is_dir(ROOT_PATH.$dir)) {
$dh=opendir(ROOT_PATH.$dir);
while (false !== ( $file = readdir ($dh))) {
if($file!="."&&$file!="..") {
$fullpath=$dir."/".$file;
$_url = explode($_filename,$file);
if(!is_dir($fullpath) &&isset($_url[0]) &&$_url[0]=="") {
unlink(ROOT_PATH.$fullpath);
}
}
}
closedir($dh);
}
}
function _output(){
$img_type  = $this->img_type;
if ($img_type!=""){
$func_name = $this->all_type[$img_type]['output'];
if(function_exists($func_name))        {
if(isset($_SERVER['HTTP_USER_AGENT']))            {
$ua = strtoupper($_SERVER['HTTP_USER_AGENT']);
if(!preg_match('/^.*MSIE.*\)$/i',$ua))                {
header("Content-type:$img_type");
}
}
@$func_name($this->h_dst,$this->dst_img,$this->file_quality);
}
else{
return false;
}
}else{
return false;
}
}
function cutImg(){
$this->src_w = $this->getImgWidth($this->h_src);
$this->src_h = $this->getImgHeight($this->h_src);
$this->h_dst = imagecreatetruecolor($this->dst_w,$this->dst_h);
}
function setSrcImg($src_img,$img_type=null)  {
if(!file_exists($src_img))   return -1 ;
if(!empty($img_type)){
$this->img_type = $img_type;
}
else{
$img_info = $this->getImgInfo($src_img);
}
$this->h_src =$img_info['img_src'];
$this->src_w = $img_info['img_width'];
$this->src_h = $img_info['img_height'];
}
function setCutimg(){
if ($this->cut_status==1){
if ($this->cut_type==1){
$this->fill_w = round($this->src_w * $this->cut_scale / 100) -$this->img_border_size*2;
$this->fill_h = round($this->src_h * $this->cut_scale / 100) -$this->img_border_size*2;
$this->src_x  = 0;
$this->src_y  = 0;
$this->copy_w = $this->src_w;
$this->copy_h = $this->src_h;
$this->dst_w   = $this->fill_w +$this->img_border_size*2;
$this->dst_h   = $this->fill_h +$this->img_border_size*2;
}
elseif ($this->cut_type==2){
$fill_w   = (int)$this->cut_width -$this->img_border_size*2;
$fill_h   = (int)$this->cut_height -$this->img_border_size*2;
if($fill_w <0 ||$fill_h <0){
return -8 ;
}
$rate_w = $this->src_w/$fill_w;
if ($fill_h>0){
$rate_h = $this->src_h/$fill_h;
}
if($rate_w <1 &&$rate_h <1){
$this->fill_w = (int)$this->src_w;
$this->fill_h = (int)$this->src_h;
}
else{
if($rate_w >= $rate_h)	{
$this->fill_w = (int)$fill_w;
$this->fill_h = round($this->src_h/$rate_w);
}
else{
$this->fill_w = round($this->src_w/$rate_h);
$this->fill_h = (int)$fill_h;
}
}
$this->src_x  = 0;
$this->src_y  = 0;
$this->copy_w = $this->src_w;
$this->copy_h = $this->src_h;
$this->dst_w   = $this->fill_w +$this->img_border_size*2;
$this->dst_h   = $this->fill_h +$this->img_border_size*2;
}
elseif ($this->cut_type==3){
$fill_w   = (int)$this->cut_width -$this->img_border_size*2;
$fill_h   = (int)$this->cut_height -$this->img_border_size*2;
if($fill_w <0 ||$fill_h <0){
return -8 ;
}
$rate_w = $this->src_w/$fill_w;
$rate_h = $this->src_h/$fill_h;
if($rate_w >= 1 &&$rate_h >=1){
if($this->src_w >$this->src_h)	{
$src_x = round($this->src_w-$this->src_h)/2;
$this->src_x  = $src_x;
$this->src_y  = 0;
$this->fill_w = $this->fill_h;
$this->fill_h = $this->fill_h;
$this->copy_w = $this->src_h;
$this->copy_h = $this->src_h;
}
elseif($this->src_w <$this->src_h){
$src_y = round($this->src_h-$this->src_w)/2;
$this->src_x  = 0;
$this->src_y  = $src_y;
$this->fill_w = $this->fill_w;
$this->fill_h = $this->fill_h;
$this->copy_w = $this->src_w;
$this->copy_h = $this->src_w;
}
else{
$this->src_x  = 0;
$this->src_y  = 0;
$this->copy_w = $this->src_w;
$this->copy_h = $this->src_w;
$this->fill_w = $this->fill_w;
$this->fill_h = $this->fill_h;
}
}
else{
$this->src_x  = 0;
$this->src_y  = 0;
$this->fill_w = $this->src_w;
$this->fill_h = $this->src_h;
$this->copy_w = $this->src_w;
$this->copy_h = $this->src_h;
}
$this->dst_w   = $this->fill_w +$this->img_border_size*2;
$this->dst_h   = $this->fill_h +$this->img_border_size*2;
}
elseif ($this->cut_type==4){
$fill_w   = $this->cut_width -$this->img_border_size*2;
$fill_h   = $this->cut_height -$this->img_border_size*2;
if($fill_w <0 ||$fill_h <0){
return -8 ;
}
$this->copy_w = $fill_w;
$this->copy_h = $fill_h;
$this->dst_w   = $fill_w +$this->img_border_size*2;
$this->dst_h   = $fill_h +$this->img_border_size*2;
$this->fill_w = $fill_w;
$this->fill_h = $fill_h;
}
}else{
$this->src_x  = 0;
$this->src_y  = 0;
$this->fill_w = $this->src_w;
$this->fill_h = $this->src_h;
$this->copy_w = $this->src_w;
$this->copy_h = $this->src_h;
$this->dst_w   = $this->fill_w +$this->img_border_size*2;
$this->dst_h   = $this->fill_h +$this->img_border_size*2;
}
$this->start_x = $this->img_border_size;
$this->start_y = $this->img_border_size;
}
function setDstImg($dst_img)    {
$arr  = explode('/',$dst_img);
$last = array_pop($arr);
$path = implode('/',$arr);
$this->_mkdirs(ROOT_PATH.$path);
$this->dst_img = $dst_img;
}
function getImgInfo($src_img){
if ($src_img=="") return -1;
if(function_exists("file_get_contents"))   {
$src = file_get_contents($src_img);
}
else {
$handle = fopen ($src_img,"r");
while (!feof ($handle))
{
$src .= fgets($fd,4096);
}
fclose ($handle);
}
if(empty($src))   {
return -1;
}
$h_src = @ImageCreateFromString($src);
$img_info = @getimagesize ($src_img);
return array("img_width"=>$img_info[0],"img_height"=>$img_info[1],"img_type"=>$img_info[2],"img_src"=>$h_src);
}
function _checkValid($img_type)
{
if(!array_key_exists($img_type,$this->all_type))
{
return false;
}
}
function _mkdirs($path)  {
$adir = explode('/',$path);
$dirlist = '';
$rootdir = array_shift($adir);
if(($rootdir!='.'||$rootdir!='..')&&!file_exists($rootdir))
{
@mkdir($rootdir);
}
foreach($adir as $key=>$val)
{
if($val!='.'&&$val!='..')
{
$dirlist .= "/".$val;
$dirpath = $rootdir.$dirlist;
if(!file_exists($dirpath))
{
@mkdir($dirpath);
@chmod($dirpath,0777);
}
}
}
}
function _parseColor($color)
{
$arr = array();
for($ii=1;$ii<strlen($color);$ii++)
{
$arr[] = hexdec(substr($color,$ii,2));
$ii++;
}
Return $arr;
}
function _countMaskPos()
{
if($this->_isFull())
{
switch($this->mask_position)
{
case 1:
$this->mask_pos_x = $this->mask_offset_x +$this->img_border_size;
$this->mask_pos_y = $this->mask_offset_y +$this->img_border_size;
break;
case 2:
$this->mask_pos_x = $this->mask_offset_x +$this->img_border_size;
$this->mask_pos_y = $this->src_h -$this->mask_h -$this->mask_offset_y;
break;
case 3:
$this->mask_pos_x = $this->src_w -$this->mask_w -$this->mask_offset_x;
$this->mask_pos_y = $this->mask_offset_y +$this->img_border_size;
break;
case 4:
$this->mask_pos_x = $this->src_w -$this->mask_w -$this->mask_offset_x;
$this->mask_pos_y = $this->src_h -$this->mask_h -$this->mask_offset_y;
break;
default:
$this->mask_pos_x = $this->src_w -$this->mask_w -$this->mask_offset_x;
$this->mask_pos_y = $this->src_h -$this->mask_h -$this->mask_offset_y;
break;
}
}
else
{
switch($this->mask_position)
{
case 1:
$this->mask_pos_x = $this->mask_offset_x +$this->img_border_size;
$this->mask_pos_y = $this->mask_offset_y +$this->img_border_size;
break;
case 2:
$this->mask_pos_x = $this->mask_offset_x +$this->img_border_size;
$this->mask_pos_y = $this->dst_h -$this->mask_h -$this->mask_offset_y -$this->img_border_size;
break;
case 3:
$this->mask_pos_x = $this->dst_w -$this->mask_w -$this->mask_offset_x -$this->img_border_size;
$this->mask_pos_y = $this->mask_offset_y +$this->img_border_size;
break;
case 4:
$this->mask_pos_x = $this->dst_w -$this->mask_w -$this->mask_offset_x -$this->img_border_size;
$this->mask_pos_y = $this->dst_h -$this->mask_h -$this->mask_offset_y -$this->img_border_size;
break;
default:
$this->mask_pos_x = $this->dst_w -$this->mask_w -$this->mask_offset_x -$this->img_border_size;
$this->mask_pos_y = $this->dst_h -$this->mask_h -$this->mask_offset_y -$this->img_border_size;
break;
}
}
}
function _createMask(){
if($this->mask_status!=1 ||(empty($this->mask_word) &&empty($this->mask_img)))  {
$this->h_dst = @imagecreatetruecolor($this->dst_w,$this->dst_h);
$white = @imageColorAllocate($this->h_dst,255,255,255);
@imagefilledrectangle($this->h_dst,0,0,$this->dst_w,$this->dst_h,$white);
$this->_drawBorder();
@imagecopyresampled( $this->h_dst,$this->h_src,
$this->start_x,$this->start_y,
$this->src_x,$this->src_y,
$this->fill_w,$this->fill_h,
$this->copy_w,$this->copy_h);
}else {
if($this->mask_word!="") {
$this->_setFontInfo();
if($this->_isFull()) {
}
else{
$this->h_dst = imagecreatetruecolor($this->dst_w,$this->dst_h);
$white = ImageColorAllocate($this->h_dst,255,255,255);
imagefilledrectangle($this->h_dst,0,0,$this->dst_w,$this->dst_h,$white);
$this->_drawBorder();
imagecopyresampled( $this->h_dst,$this->h_src,
$this->start_x,$this->start_y,
$this->src_x,$this->src_y,
$this->fill_w,$this->fill_h,
$this->copy_w,$this->copy_h);
$this->_createMaskWord($this->h_dst);
}
}else if($this->mask_img){
$this->_loadMaskImg();
if($this->_isFull()) {
$this->h_dst = @imagecreatetruecolor($this->dst_w,$this->dst_h);
$white = @imageColorAllocate($this->h_dst,255,255,255);
@imagefilledrectangle($this->h_dst,0,0,$this->dst_w,$this->dst_h,$white);
$this->_drawBorder();
@imagecopyresampled( $this->h_dst,$this->h_src,
$this->start_x,$this->start_y,
$this->src_x,$this->src_y,
$this->fill_w,$this->fill_h,
$this->copy_w,$this->copy_h);
}else{
$this->h_dst = imagecreatetruecolor($this->dst_w,$this->dst_h);
$white = ImageColorAllocate($this->h_dst,255,255,255);
imagefilledrectangle($this->h_dst,0,0,$this->dst_w,$this->dst_h,$white);
$this->_drawBorder();
imagecopyresampled( $this->h_dst,$this->h_src,
$this->start_x,$this->start_y,
$this->src_x,$this->src_y,
$this->fill_w,$this->fill_h,
$this->copy_w,$this->copy_h);
$this->_createMaskImg($this->h_dst);
}
}
}
}
function _setFontInfo()
{
if(is_numeric($this->font)) {
$this->font_w  = imagefontwidth($this->font);
$this->font_h  = imagefontheight($this->font);
$word_length   = strlen($this->mask_word);
$this->mask_w  = $this->font_w*$word_length;
$this->mask_h  = $this->font_h;
}
else{
$arr = imagettfbbox ($this->font_size,0,$this->font,$this->mask_word);
$this->mask_w  = abs($arr[0] -$arr[2]);
$this->mask_h  = abs($arr[7] -$arr[1]);
}
}
function _drawBorder()
{
if(!empty($this->img_border_size))
{
$c = $this->_parseColor($this->img_border_color);
$color = ImageColorAllocate($this->h_src,$c[0],$c[1],$c[2]);
imagefilledrectangle($this->h_dst,0,0,$this->dst_w,$this->dst_h,$color);
}
}
function _createMaskWord($src)
{
$this->_countMaskPos();
$result = $this->_checkMaskValid();
if ($result != false){
$c = $this->_parseColor($this->mask_font_color);
$color = imagecolorallocatealpha($src,$c[0],$c[1],$c[2],$this->mask_txt_pct);
if(is_numeric($this->font)){
imagestring($src,
$this->font,
$this->mask_pos_x,$this->mask_pos_y,
$this->mask_word,
$color);
}
else{
imagettftext($src,
$this->font_size,0,
$this->mask_pos_x,$this->mask_pos_y,
$color,
$this->font,
$this->mask_word);
}
}
}
function _createMaskImg($src) {
$this->_countMaskPos();
$result = $this->_checkMaskValid();
if ($result != false){
imagecopymerge($src,
$this->h_mask,
$this->mask_pos_x ,$this->mask_pos_y,
0,0,
$this->mask_w,$this->mask_h,
$this->mask_img_pct);
imagedestroy($this->h_mask);
}
}
function _loadMaskImg(){
$mask_type = $this->_getImgType($this->mask_img);
$this->_checkValid($this->img_type);
$src = '';
if(function_exists("file_get_contents")){
$src = file_get_contents(ROOT_PATH.$this->mask_img);
}else {
$handle = fopen ($this->mask_img,"r");
while (!feof ($handle)){
$src .= fgets($fd,4096);
}
fclose ($handle);
}
if(empty($this->mask_img))  {
return false;
}else{
$this->h_mask = ImageCreateFromString($src);
$this->mask_w = $this->getImgWidth($this->h_mask);
$this->mask_h = $this->getImgHeight($this->h_mask);
}
}
function _getImgType($file_path){
$type_list = array("1"=>"gif","2"=>"jpg","3"=>"png","4"=>"swf","5"=>"psd","6"=>"bmp","15"=>"wbmp");
if(file_exists($file_path)){
$img_info = @getimagesize ($file_path);
if(isset($type_list[$img_info[2]])){
return $type_list[$img_info[2]];
}
}else{
return  false;
}
}
function _isFull()
{
Return (   $this->mask_w +$this->mask_offset_x >$this->fill_w
||$this->mask_h +$this->mask_offset_y >$this->fill_h)
?true:false;
}
function _checkMaskValid()
{
if($this->mask_w +$this->mask_offset_x >$this->src_w
||$this->mask_h +$this->mask_offset_y >$this->src_h)
{
return false;
}else{
return true;
}
}
function getImgWidth($src)
{
return imagesx($src);
}
function getImgHeight($src)
{
return imagesy($src);
}
}

?>