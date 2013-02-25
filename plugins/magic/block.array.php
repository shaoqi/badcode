<?php

function magic_block_array($tag_command,$parse_var,$magic_vars){
if  ($tag_command == "array") {
$from = null;
$_data = "";
foreach($parse_var as $_key =>$_val) {
switch($_key) {
case 'from':
$$_key = (string)$_val;
break;
default:
$$_key = (string)$_val;
break;
}
}
$var = empty($var)?"var":$var;
$_display = "";
$display .="<? \$_from = explode(',',$from); if (!is_array(\$_from) && !is_object(\$_from)) { settype(\$_from,'array'); } ";
$display .= "if (count(\$_from)):\n \$i=0;";
$display .= "    foreach (\$_from as \$this->magic_vars['key'] => \$this->magic_vars['$var']):\n";
$display .= " if ( \$this->magic_vars['$var']!=''):";
$display .= " \$this->magic_vars['key'] =\$i";
$display .= '?>';
return $display;
}else if ($tag_command == "/array"){
return "<? \$i++;endif;endforeach; endif;  unset(\$_from);unset(\$_magic_vars); ?>";
}
}
?>