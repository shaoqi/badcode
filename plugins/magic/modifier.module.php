<?php

function magic_modifier_module($nid,$parse_var = '',$magic_vars = ''){
if ($nid=="") return "";
return $magic_vars["_G"]['_module'][$nid];
}
?>