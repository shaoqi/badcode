<?
if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

include_once("scrollpic.class.php");


$_A['list_purview']["scrollpic"]["name"] = "flash大图管理";
$_A['list_purview']["scrollpic"]["result"]["scrollpic_list"] = array("name"=>"列表管理","url"=>"code/scrollpic/list");
$_A['list_purview']["scrollpic"]["result"]["scrollpic_new"] = array("name"=>"添加滚动","url"=>"code/scrollpic/new");
$_A['list_purview']["scrollpic"]["result"]["scrollpic_type"] = array("name"=>"类型管理","url"=>"code/scrollpic/type");


check_rank("scrollpic_list");//检查权限
/**
 * 如果类型为空的话则显示所有的文件列表
**/
if ($_A['query_type'] == "list"){
	
}
	
	
/**
 * 链接类型
**/
elseif ($_A['query_type'] == "type"){
	if (isset($_REQUEST['del_id'])){
		if ($_REQUEST['del_id'] !=1){
			$mysql->db_delete("scrollpic_type","id=".$_REQUEST['del_id']);
			$msg = array("删除成功","",$_A['query_url']."/type");
		}else{
			$msg = array("类型ID1为系统类型，不能删除","",$_A['query_url']."/type");
		}
	}elseif (!isset($_POST['submit'])){
		$_A['scrollpic_type_list'] = scrollpicClass::GetTypeList();
	}else{
	   if (count($_POST['id'])>0){
    		foreach ($_POST['id'] as $key => $val){
    			$mysql->db_query("update {scrollpic_type} set typename='".$_POST['typename'][$key]."' where id=".$val);
    		}
        }
		if ($_POST['typename1']!=""){
			$index['typename'] = $_POST['typename1'];
			$mysql->db_add("scrollpic_type",$index,"notime");
		}
		$msg = array("类型操作成功","",$_A['query_url']."/type");
	}
}

/**
 * 添加
**/
elseif ($_A['query_type'] == "new" || $_A['query_type'] == "edit" ){
	if (isset($_POST['type_id']) && $_POST['type_id']!=""){
		$var = array("type_id","status","order","url","name","summary");
		foreach ( $var as $val){
			$data[$val] = !isset($_POST[$val])?"":$_POST[$val];
		}
		
		$datapic['file'] = "pic";
		$datapic['code'] = "scrollpic";
		$datapic['user_id'] = $_G['user_id'];
		$datapic['type'] = "new";
		$datapic['aid'] = $data['type_id'];
		$pic_result = $upload->upfile($datapic);
		if ($pic_result!=""){
			$data['pic'] = $pic_result[0]['filename'];
		}
		
		
		if ($_A['query_type'] != "new"){
			$data['id'] = $_POST['id'];
			$result = scrollpicClass::Update($data);
		}else{
			$result = scrollpicClass::Add($data);
		}
		if ($result == false){
			$msg = array("输入有误，请跟管理员联系");
		}else{
			$msg = array("操作成功","返回上一页",$_A['query_url']);
		}
	
	
	}else{
		$_A['scrollpic_type_list'] = scrollpicClass::GetTypeList();
		if ($_A['query_type'] == "edit"){
			$_A['scrollpic_result'] = scrollpicClass::GetOne(array("id"=>$_REQUEST['id']));
		}
	}
}
	
	
/**
 * 删除
**/
elseif ($_A['query_type'] == "del"){
	$data['id'] = $_REQUEST['id'];
	$result = scrollpicClass::Delete($data);
	if ($result !== true){
		$msg = array($result);
	}else{
		$msg = array("删除成功","返回上一页",$_A['query_url']);
	}
}


?>