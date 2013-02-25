<?php

function magic_function_editor($data){
global $mysql,$_G;
$name=$data['name'];
$_value=$data['value'];
if ($data['type']=="sina"){
$api = 'require_once(ROOT_PATH ."/plugins/editor/sinaeditor/Editor.class.php");
	$editor=new sinaEditor($name);
		$editor->Value= "$value";
	
		$editor->AutoSave=false;
		echo $editor->Create();';
$display =  "<?  \$name = \"$name\" ; \$value = $_value;".$api." ?>";
}else{
$display =  "<?  \$name = \"$name\" ; \$value = $_value; ?>";
$display .=  '<? echo "<script type=\"text/javascript\" src=\"/plugins/editor/xheditor/xheditor-1.1.12-zh-cn.min.js\"></script>";';$display .=  ' echo "<script >$(\'#elm5\').xheditor({tools:\'Cut,Copy,Paste,Pastetext,|,Source,Fullscreen,About\'});</script>";';
$display .=  ' echo \'<textarea id="elm5" name="elm5" crows="12" cols="80" style="width: 80%"></textarea>\';?>';
}
return $display;
}

?>