<?php

if (!defined('ROOT_PATH'))  die('不能访问');
class Mysql {
var $db_link;
var $db_show_error;
var $db_prefix;
function Mysql($db_config){
$this->db_show_error = false;
$this->db_prefix = $db_config['prefix'];
$this->db_link = $this->db_connect($db_config);
$this->mysql_error_path = "data";
$this->ip = ip_address();
}
function db_connect($db_config) {
if (!function_exists('mysql_connect')) {
$this->db_error_msg('您的数据库还未安装此扩展');
}
$db_config['host'] = urldecode($db_config['host']);
$db_config['user'] = urldecode($db_config['user']);
$db_config['pwd'] = isset($db_config['pwd']) ?urldecode($db_config['pwd']) : '';
$db_config['name'] = urldecode($db_config['name']);
$db_config['language'] = urldecode($db_config['language']);
if (isset($db_config['port'])) {
$db_config['host'] = $db_config['host'] .':'.$db_config['port'];
}
$this->db_link = @mysql_connect($db_config['host'],$db_config['user'],$db_config['pwd'],TRUE,2);
if (!$this->db_link  ||!mysql_select_db($db_config['name'],$this->db_link )) {
$this->db_error_msg("数据库连接失败：mysql_error:".mysql_error());
}
mysql_query('SET NAMES "'.$db_config['language'].'"',$this->db_link );
return $this->db_link ;
}
function db_query($sql="",$noreplace=""){
if ($noreplace == ""){
while (preg_match ('/{([a-zA-Z0-9_-]+)}/',$sql,$regs)) {
$found = $regs[1];
$sql = preg_replace("/\{".$found."\}/",$this->db_prefix.$found,$sql);
}
}
$result = mysql_query($sql);
if(!$result){
$this->db_error_msg(mysql_error()."执行SQL语句错误!".$sql);
}
return $result;
}
function db_querys($sql="",$noreplace=""){
$_sql = explode(";",$sql);
foreach($_sql as $value){
$value = trim($value);
if (!empty($value)){
$result = $this->db_query($value.";",$noreplace);
}
}
if(!$result){
$this->db_error_msg(mysql_error()."执行SQL语句错误!".$sql);
}
return $result;
}
function db_query_memcache($sql,$type = '') {
global $memcache,$memcachelife;
$key = md5($sql);
if(!($query = $memcache->get($key))) {
$query = $this->query($sql,$type);
while($item  = $this->fetch_array($query)) {
$res[] = $item;
}
$query = $res;
$memcache->set($key,$query ,0,$memcachelife);
}
return $query;
}
function db_fetch_array($sql) {
global $memcache,$memcachelife,$memcache_result;
if($memcache_result!=0){
$key = md5($sql."one");
if(!($query = $memcache->get($key))) {
$result = $this->db_query($sql);
$res = mysql_fetch_array($result,MYSQL_ASSOC);
$_res = "";
if (is_array($res)){
foreach ($res as $key =>$value){
$_res[$key] =$value;
}
}
$memcache->set($key,$_res ,0,$memcachelife);
$query = $_res;
}
}else{
$result = $this->db_query($sql);
$res = mysql_fetch_array($result,MYSQL_ASSOC);
$_res = "";
if (is_array($res)){
foreach ($res as $key =>$value){
$_res[$key] =$value;
}
}
$query = $_res;
}
return $query;
}
function db_fetch_arrays($sql) 	{
global $memcache,$memcachelife,$memcache_result;
if($memcache_result!=0){
$key = md5($sql."more");
if(!($query = $memcache->get($key))) {
$result = $this->db_query($sql);
$i = 0;
$_res = array();
while($res = mysql_fetch_array($result,MYSQL_ASSOC)) {
foreach ($res as $key =>$value){
$_res[$i][$key] = $value;
}
$i++;
}
$this->db_free_result($result);
$memcache->set($key,$_res ,0,$memcachelife);
$query = $_res;
}
}else{
$result = $this->db_query($sql);
$i = 0;
$_res = array();
while($res = mysql_fetch_array($result,MYSQL_ASSOC)) {
foreach ($res as $key =>$value){
$_res[$i][$key] = $value;
}
$i++;
}
$this->db_free_result($result);
$query = $_res;
}
return $query;
}
function db_free_result($result){
if(is_array($result)) {
foreach($result as $key =>$value){
if($value) @mysql_free_result($value);
}
}else{
@mysql_free_result($result);
}
}
function db_num_rows($sql){
$result = $this->GetQuery($sql);
return mysql_num_rows($result);
}
function db_affected_rows($sql){
$result = $this->GetQuery($sql);
return mysql_affected_rows($result);
}
function db_insert_id(){
return mysql_insert_id();
}
function db_version() {
list($version) = explode('-',mysql_get_server_info());
return $version;
}
function db_close()	{
@mysql_close($this->db_link);
}
function db_show_msg($i=false){
$this->db_show_error = $i;
}
function db_add($code,$var,$notime="",$fields=""){
$sql = "insert into `{".$code."}` set ";
foreach ($var as $key =>$value){
$_sql[] = "`$key`='$value'";
}
$sql = $sql.join(",",$_sql);
if ($notime==""){
$sql .= ",addtime='".time()."',addip='$this->ip'";
}
$result = $this->db_query($sql);
$id = $this->db_insert_id();
if ($result!=false &&$fields!=""){
$this->db_add_fields($code,$id,$fields);
}
return $result;
}
function db_add_fields($code,$id,$fields){
$sql = "insert into `{".$code."_fields}` set ";
if (is_array($fields)){
foreach ($fields as $key =>$value){
if ($key!=""){
$sql .= "`$key`='$value',";
}
}
}
$sql .= "id=$id";
$this->db_query($sql);
}
function db_select($table,$where=""){
$sql = "select * from `{".$table."}` ";
if ($where !="") $sql .= " where $where";
return $this->db_fetch_array($sql);
}
function db_show_fields($table){
$sql = "SHOW COLUMNS FROM  `{".$table."}` ";
$result = $this->db_fetch_arrays($sql);
foreach ($result as $key =>$value){
$_result[] = $value['Field'];
}
return $_result;
}
function db_show_field($table){
$sql = "SHOW COLUMNS FROM  `".$table."` ";
$result = $this->db_fetch_arrays($sql);
foreach ($result as $key =>$value){
$_result[] = $value['Field'];
}
return $_result;
}
function db_show_tables(){
$sql = "SHOW tables ";
$result = $this->db_fetch_arrays($sql);
foreach ($result as $key =>$value){
foreach ($value as $_key =>$_value){
$_result[] = $_value;
}
}
return $_result;
}
function db_selects($table,$where="",$order=""){
$sql = "select * from `{".$table."}` ";
if ($where !="") $sql .= " where $where";
if ($order !="") $sql .= " order by $order";
return $this->db_fetch_arrays($sql);
}
function db_list($table,$where="",$page="",$epage=10,$order=""){
$_sql="";
$sql = "select count(*) as num from `{".$table."}` ";
if ($where !="") $_sql .= " where $where";
$_result = $this->db_fetch_array($sql.$_sql);
if ($page !=""){
$vpage = ($page-1)*$epage;
}
$sql = "select * from `{".$table."}` $_sql order by ";
if ($order !="") $sql .= "$order,";
$sql .= " addtime desc ";
if ($page !="") $sql .= " limit $vpage,$epage";
$result = $this->db_fetch_arrays($sql);
return array("result"=>$result,"num"=>$_result['num']);
}
function db_list_res($sql,$page="",$epage=10){
if ($page !=""){
$vpage = ($page-1)*$epage;
$sql .= " limit $vpage,$epage";
}
return $this->db_fetch_arrays($sql);
}
function db_num($sql){
$_result = $this->db_fetch_array($sql);
return $_result['num'];
}
function db_count($table,$where=""){
$sql = "select count(*) as num from `{".$table."}` ";
if ($where !="") $sql .= " where $where";
$_result = $this->db_fetch_array($sql);
return $_result['num'];
}
function db_update($code,$data,$where,$fields=""){
if ($fields!=""){
$sql = "select * from `{".$code."_fields}` where $where";
$result = $this->db_fetch_array($sql);
if  ($result==false){
$sql = "insert into `{".$code."_fields}` set $where";
$this->db_query($sql);
}
$_sql = array();
if (is_array($fields)){
$sql = "update `{".$code."_fields}` set ";
foreach ($fields as $key =>$value){
if ($key!=""){
$_sql[] = "`$key`='$value'";
}
}
$sql .= join(",",$_sql)." where $where";
$this->db_query($sql);
}
}
$sql = "update `{".$code."}` set ";
$_sql = "";
foreach ($data as $key =>$value){
$_sql[] = "`$key`='$value'";
}
$sql .= join(",",$_sql)." where $where";
return $this->db_query($sql);
}
function db_order($table,$order,$where,$type){
foreach ($type as $key =>$id){
$sql = "update `{".$table."}` set `order`='".$order[$key]."' where `$where`=$id";
$this->db_query($sql);
}
return true;
}
function db_delete($table,$where){
$sql = "delete from `{".$table."}` where $where ";
return $this->db_query($sql);
}
function db_error_msg($msg) {
if ($this->db_show_error) {
$mysql_dir = $this->mysql_error_path;
$dtime=date("Y-m-d",time());
$ip =ip_address();
$file = "http://".$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"];
if(!file_exists($mysql_dir."/mysql_error")){
mkdir($mysql_dir."/mysql_error",0777);
}
$fp =   @fopen($mysql_dir."/mysql_error/".$dtime.".log","a+");
$time=date("H:i:s");
$str="{visitedtime:$time}\t{visiterip:".$ip."}\t{errormsg:".$msg."}\t{file:".$file."}\t\r\n";
@fputs($fp,$str);
@fclose($fp);
}
die($msg);
return false;
}
}
?>