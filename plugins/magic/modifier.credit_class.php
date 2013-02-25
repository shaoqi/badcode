<?php

function magic_modifier_credit_class($class_id,$parse_var = '',$magic_vars = ''){
if ($class_id=="") return "";
$class_result = $magic_vars["_G"]['credit']['_class'];
$var = explode(",",$class_id);
$result = array();
foreach ($var as $key =>$val){
$result[] = $class_result[$val];
}
return join(",",$result);
}

?>