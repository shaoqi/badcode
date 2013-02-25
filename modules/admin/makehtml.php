<?php
/******************************
 * $File: malehtml.php
 * $Description: 生成静态页
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

check_rank("makehtml_".$_t);//检查权限

$con_template = empty($system['con_template'])?"themes/default":"themes/".$system['con_template'];
$system['con_rewrite'] = 2;
/**
 *首页更新
**/
if ($t == "index"){	
	$magic->assign("site_id","0");
	if (isset($_REQUEST['action'])){
   		$content = $magic->gethtml("index.html",$con_template);
		mk_file("index.html",$content);
		$msg = array("更新成功");
	}
}

/**
 *栏目更新
**/
elseif ($t == "site"){	
	if (isset($_POST['site_id']) ){		
		$sitelist = $module->get_sites($_POST['site_id'],1,$_POST['zilanmu']);//获得站点的列表信息
		foreach ($sitelist['result'] as $key => $value){
			$magic->assign("site_id",$value['site_id']);
			$url = $value['sitedir'];
			$format_var = array("code"=>$value['code'],"site_id"=>$value['site_id'],"nid"=>$value['nid'],"page"=>$page);
			if ($value['pid']==0){
				$list_name = "index.html";
				$template = format_tpl($value['index_tpl'],$format_var);
			}else{
				if ($value['list_name']!=""){
				$list_name = format_tpl($value['list_name'],$format_var);
				}
				$template = format_tpl($value['list_tpl'],$format_var);
			}
			$content = $magic->gethtml($template,$con_template);
			mk_file(format_tpl($url."/".$list_name,$format_var),$content);
			
			$msg = array("更新成功");
		}
	}else{
		$magic->assign("sitelist",$module->get_site_li());
	}
	
}

if ($msg!="") {
	$template_tpl = show_msg($msg,$msg_tpl);//如果是信息的则直接读取系统的信息模板
	$magic->assign("module_tpl",$template_tpl);
}
if ($msg==""){
		$template_tpl = "admin_makehtml.html";
	}
?>