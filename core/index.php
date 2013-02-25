<?php
 
include ("magic.class.php");
$magic = new magic();
$magic->template_dir = "";
$magic->force_compile = true;
$magic->assign("aa",array("aa"=>"11"));
$magic->display("tpl.html");
?>