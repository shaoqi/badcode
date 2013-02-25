<?php

function magic_block_for($tag_command,$parse_var,$magic_vars){
if  ($tag_command == "for") {
$table = null;
$where = null;
$_data = "";
foreach($parse_var as $_key =>$_val) {
switch($_key) {
case 'start':
$$_key = (string)$_val;
break;
case 'end':
$$_key = (string)$_val;
break;
default:
$$_key = (string)$_val;
break;
}
}
$display .="<? for( \$this->magic_vars['$var']=$start;\$this->magic_vars['$var']<=$end;\$this->magic_vars['$var']++){";
$display .= '?>';
return $display;
}else if ($tag_command == "/for"){
return "<? };unset(\$_magic_vars); ?>";
}
}

?>