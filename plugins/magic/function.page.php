<?php

function magic_function_page($params){
$msg = empty($vars['params'])?"暂时没有信息":$params['msg'];
$type = empty($params['type'])?3:$params['type'];
$style = !isset($params['style'])?"":$params['style'];
$listvar = !isset($params['listvar'])?"":$params['listvar'];
$page_name = isset($params['page_name'])?$params['page_name']:"";
$page_url = isset($params['url'])?$params['url']:"";
$very_page = isset($params['very_page'])?$params['very_page']:"";
$rewrite =isset($params['rewrite'])?true:"";
$suffix = isset($params['suffix'])?$params['suffix']:"";
$canshu = isset($params['canshu'])?$params['canshu']:"";
$total = "\$this->magic_vars['{$listvar}']['total']";
$epage = "\$this->magic_vars['{$listvar}']['epage']";
$page = "\$this->magic_vars['{$listvar}']['page']";
return "<div class='".$style."'><? if (isset(\$this->magic_vars['{$listvar}']['page'])): \$pages = new Pages();\$pages->set_data(array('total'=>$total,'epage'=>$epage,'page'=>$page,'page_name'=>'{$page_name}','url'=>'$page_url','rewrite'=>'$rewrite','very_page'=>'$very_page','suffix'=>'$suffix','canshu'=>'$canshu'));\n
	if (\$this->magic_vars['{$listvar}']['total']>0) echo \$pages->show($type);else echo '暂没文章';endif;?></div>";
}
?>