<?php

function magic_function_minutepicker($data){
global $_G;
$name = !IsExiest($data['name'])?"":$data['name'];
$value = !IsExiest($data['value'])?"":$data['value'];
$result = "<? echo \"<select name='{$data['name']}' class='{$data['class']}'>\";\$display =\"\";";
if ($data['value']{0}=="$"){
if ($date['value']!=""){
$result .= 'if ('.$data['value'].' >60){ '.$data['value'].' = date("H",'.$data['value'].');};';
}
$result .= 'for($i=0;$i<=60;$i++){ if ($i=='.$data['value'].'){ ';
}else{
if ($date['value']!=""){
$result .= 'if (\''.$data['value'].'\' >60){ \''.$data['value'].'\' = date("H",\''.$data['value'].'\');};';
}
$result .= 'for($i=0;$i<=60;$i++){ 
		if ($i==\''.$data['value'].'\'){';
}
$result .= '$display .="<option value=\'{$i}\' selected >".sprintf("%02d", $i)."</option>";
		}else{
			$display .="<option value=\'{$i}\' >".sprintf("%02d", $i)."</option>";
		}
	}';
$result .= ' echo $display;';
$result .= 'echo "</select>" ;?>';
return $result;
}

?>