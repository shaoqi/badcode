<?php

function magic_block_site($tag_command,$parse_var,$magic_vars){
$var = "";
if  ($tag_command == "site") {
$table = null;
$where = null;
$type = null;
$order = null;
$sql = null;
$pid = null;
$site_id = null;
$lgnore  = null;
$display   = null;
$_data = "";
foreach($parse_var as $_key =>$_val) {
switch($_key) {
case 'site_id':
$$_key = (string)$_val;
break;
case 'lgnore':
$$_key = (string)$_val;
break;
case 'pid':
$$_key = (int)$_val;
break;
case 'status':
$$_key = (int)$_val;
break;
case 'type':
$$_key = (string)$_val;
break;
default:
$$_key = (string)$_val;
break;
}
}
$var = empty($var)?"var":$var;
$_display = "";
if ($pid!=""||$pid=="0"){
if ($pid == "0") $pid = "\$this->magic_vars['_G']['site_result']['site_id']";
$_display .= " && \$this->magic_vars['$var']['pid']==$pid";
}
if ($site_id==""&&$pid == ""&&$type==""){
$_display .= " && \$this->magic_vars['$var']['pid']==0";
}
if ($site_id!=""||$site_id=="0"){
if ($site_id == "0") {
$_display .= " && \$this->magic_vars['$var']['site_id']==\$this->magic_vars['_G']['site_result']['site_id']";
}else{
$_site_id = "\$this->magic_vars['$var']['site_id']";
$_display .= " && in_array({$_site_id},\$this->magic_vars['{$var}site_id'])";
}
}
if ($type!=null){
$_display .= " && \$this->magic_vars['$var']['pid']==\$this->magic_vars['$type']['site_id']";
}
if ($lgnore!=null){
$_display .= " && !in_array(\$this->magic_vars['$var']['site_id'],\$this->magic_vars['{$var}lgnore'])";
}
if ($status!=null){
$_display .= " && \$this->magic_vars['$var']['status'] == {$status}";
}
$display .="<? \$this->magic_vars['{$var}lgnore'] = array($lgnore);\$this->magic_vars['{$var}site_id'] = array($site_id);";
$display .=" if(!isset(\$this->magic_vars['_G']['site_list'])) \$this->magic_vars['_G']['site_list']= array(); \$_from = \$this->magic_vars['_G']['site_list']; if (!is_array(\$_from) && !is_object(\$_from)) { settype(\$_from,'array'); } ";
$display .= "if (count(\$_from)):\n \$i=0;";
$display .= "    foreach (\$_from as \$this->magic_vars['key'] => \$this->magic_vars['$var']):\n";
$display .= " if (\$this->magic_vars['$var']['pid']!='' {$_display} && \$this->magic_vars['$var']['status']==1):";
$display .= " \$this->magic_vars['key'] =\$i";
$display .= '?>';
return $display;
}else if ($tag_command == "/site"){
return "<? \$i++;endif;endforeach; endif;  unset(\$_from);unset(\$_magic_vars);unset(\$this->magic_vars['{$var}lgnore']); ?>";
}
}
?>