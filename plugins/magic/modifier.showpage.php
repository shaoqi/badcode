<?php

function magic_modifier_showpage($result,$show_type = '',$magic_vars = ''){
require_once(ROOT_PATH."/core/showpage.class.php");
$data["total"] = $result['total'];
$data["perpage"] = $result['epage'];
$type = 1;
if ($show_type!=""){
$_show_type = explode(",",$show_type);
$type = $_show_type[0];
if ($_show_type[1]!=""){
if ($_show_type[1]=="dy"){
$data["ajax"] = "dy";
}else{
$data["ajax"] = "ajax_page";}
}elseif ($_show_type[2]!=""){
}
}
$showpage = new showpageClass($data);
$result = $showpage->show( $type );
echo $result;
}
?>