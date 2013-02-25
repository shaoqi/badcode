<?php

function magic_function_input($data){
$type = !IsExiest($data['type'])?"radio":$data['type'];
if ($type=="radio"){
if (!IsExiest($data['value'])){trigger_error("input: radio extra attribute 'value' cannot empty",E_USER_NOTICE);}
if (!IsExiest($data['name'])){trigger_error("input: radio extra attribute 'name' cannot empty",E_USER_NOTICE);}
$display = '<? 
		$_value = explode(",","'.$data['value'].'");
		$display = "";';
if ($data['checked']==""){
$display .= '$_che = "";';
}else{
$display .= '$_che = '.$data['checked'].';';
}
$display .= '
		
		foreach ($_value as $k => $v){
			$_check = "";
			$_v = explode("|",$v);
			if (!IsExiest($_v[1])) $_v[1] = $_v[0];
			if ($_che==$_v[0]){
				$_check = " checked=\'checked\'";
			}
			if ($k>0){
				$display .= \'<input type="radio" value="\'.$_v[0].\'"  name="'.$data['name'].'" \'.$_check.\'/> \'.$_v[1];
			}else{
				$k0 = array($_v[0],$_v[1]);
			}
		}
		if ($_che=="" || in_array($_che,$k0)){
			$display = \'<input type="radio" value="\'.$k0[0].\'"  name="'.$data['name'].'" checked="checked"/> \'.$k0[1].$display;
		}else{
			$display = \'<input type="radio" value="\'.$k0[0].\'"  name="'.$data['name'].'" /> \'.$k0[1].$display;
		}
		echo $display;
		?>';
return $display;
}elseif ($type=="select"){
if (!IsExiest($data['value'])){trigger_error("input: radio extra attribute 'value' cannot empty",E_USER_NOTICE);}
if (!IsExiest($data['name'])){trigger_error("input: radio extra attribute 'name' cannot empty",E_USER_NOTICE);}
$result = '<? $display ="<select name=\''.$data['name'].'\'  id=\''.$data['name'].'\'>";';
if ($data['default']!=""){
$result .= ' $display .= "<option value=\'\'>'.$data['default'].'</option>";';
}
if ($data['value']{0}=="$"){
$result .= 'if (count('.$data['value'].')>0):foreach ('.$data['value'].' as  $k => $v) {';
if ($data['checked']{0}=="$"){
$result .= 'if ($k=='.$data['checked'].'){';
}else{
$result .= 'if ($k==\''.$data['checked'].'\'){';
}
$result .= '$display .="<option value=\'$k\' selected >$v</option>";
				}else{
					$display .="<option value=\'$k\' >$v</option>";
				}
			};endif;';
}else{
$result .= '$_value = explode(",","'.$data['value'].'");';
$result .= 'foreach ($_value as  $k => $v) {
				$_v = explode("|",$v);
				if ($_v[0]=='.$data['checked'].'){
					$display .="<option value=\'$_v[0]\' selected >$_v[1]</option>";
				}else{
					$display .="<option value=\'$_v[0]\'  >$_v[1]</option>";
				}
			}';
}
$result .= ' $display .="</select>";echo $display; ?>';
return $result;
}
}

?>