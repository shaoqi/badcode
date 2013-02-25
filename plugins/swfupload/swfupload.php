<?
if (isset($_GET['code']) && $_GET['code']!=""){
	include_once(ROOT_PATH."modules/{$_GET['code']}/{$_GET['code']}.upload.php");
	exit;
}
@header("Expires: -1");
@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
@header("Pragma: no-cache");
@header("Content-type: application/xml; charset=utf-8");
$op = empty($_GET['op'])?'':$_GET['op'];
$isupload = empty($_GET['cam']) && empty($_GET['doodle']) ? true : false;
$iscamera = isset($_GET['cam']) ? true : false;
$isdoodle = isset($_GET['doodle']) ? true : false;
if($_FILES && $_POST) {
	if($_FILES["Filedata"]['error']) {
		$_G['uploadfiles'] = "Í¼Æ¬¹ý´ó";
	} else {
		//$_FILES["Filedata"]['name'] = addslashes(diconv(urldecode($_FILES["Filedata"]['name']), 'UTF-8'));
		//$_POST['albumid'] = addslashes(diconv(urldecode($_POST['albumid']), 'UTF-8'));
		$catid = $_POST['catid'] ? intval($_POST['catid']) : 0;
		$_POST['albumid'] = addslashes(diconv(urldecode($_POST['albumid']), 'UTF-8'));
		/*
		$_aid = explode("new:",$_POST['albumid']);
			if (isset($_aid[1]) && $_aid[1]!=""){
				$data['name'] = $_aid[1];
				$data['user_id'] = $_G['user_id'];
				$_POST['albumid'] = albumsClass::AlbumsAdd($data);
			}
		*/
		$_POST['albumid'] = iconv("GBK", "UTF-8", $_POST['albumid']);
		$uploadfiles = $upload->UpfileSwfupload(array("file"=>"Filedata","filesize"=>"2048","user_id"=>$_G['user_id'],"name"=>addslashes(diconv(urldecode($_POST['title']), 'UTF-8')),"aid"=>$_POST['albumid'],"code"=>"albums"));
		//$uploadfiles = pic_save($_FILES["Filedata"], $_POST['albumid'], addslashes(diconv(urldecode($_POST['title']), 'UTF-8')), true, $catid);
	}
	$_P['proid'] = $_POST['proid'];
	$_P['uploadResponse'] = true;
	$_P['albumid'] = 0;
	if($uploadfiles && is_array($uploadfiles)) {
		$_P['status'] = "success";
		$_P['albumid'] = $uploadfiles['albumid'];
	} else {
		$_P['status'] = "failure";
	}

	
}
$magic->assign("_P",$_P);
$magic->template_dir = ROOT_PATH."/plugins/swfupload";
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
$magic->display("swfupload.html");
exit;
?> 