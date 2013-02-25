<?
session_start();//打开缓存
$op = empty($_GET['op'])?'':$_GET['op'];
$isupload = empty($_GET['cam']) && empty($_GET['doodle']) ? true : false;
$iscamera = isset($_GET['cam']) ? true : false;
$isdoodle = isset($_GET['doodle']) ? true : false;

if($_FILES && $_POST) {
	if($_FILES["Filedata"]['error']) {
		$_G['uploadfiles'] = "图片过大";
		$_P['proid'] = $_POST['proid'];
		$_P['uploadResponse'] = true;
		$_P['albumid'] = 0;
		if($uploadfiles && is_array($uploadfiles)) {
			$_P['status'] = "success";
			$_P['albumid'] = $uploadfiles['albumid'];
		} else {
			$_P['status'] = "failure";
		}

	} else {
		
		if($_FILES["Filedata"]['error']) {
			$_G['uploadfiles'] = "图片过大";
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
		$catid = $_POST['catid'] ? intval($_POST['catid']) : 0;
		$uid = $_POST['uid'] ? intval($_POST['uid']) : 0;
		
		$_POST['albumid'] = iconv("GBK", "UTF-8", $_POST['albumid']);
		if ( $_POST['albumid']==0){
			$_P['status'] = "category";
			$_P['uploadfiles'] = "图片过大";
		}else{
			$_G['upimg']['file'] = "Filedata";
			$_G['upimg']['code'] = "attestations";
			$_G['upimg']['filesize'] = "2048";
			$_G['upimg']['type'] = "attestation";
			$_G['upimg']['user_id'] = $uid;
			$_G['upimg']['article_id'] = $_POST['albumid'];
			$_G['upimg']['name'] =  addslashes(iconv('UTF-8', 'GBK',urldecode($_POST['title'])));
			$uploadfiles = $upload->UpfileSwfupload($_G['upimg']);
			//$uploadfiles = $upload->UpfileSwfupload(array("file"=>"Filedata","filesize"=>"2048","user_id"=>$uid,"name"=>"aa","article_id"=>$_POST['albumid'],"code"=>"attestations","type"=>"album"));
			//$uploadfiles = pic_save($_FILES["Filedata"], $_POST['albumid'], addslashes(diconv(urldecode($_POST['title']), 'UTF-8')), true, $catid);
			$_P['proid'] = $_POST['proid'];
			$_P['uploadResponse'] = true;
			$_P['albumid'] = $_POST['albumid'];
			
			if($uploadfiles && is_array($uploadfiles) ) {
			
				require_once("attestations.class.php");
				$data["user_id"] = $_REQUEST['user_id'];
				$data["type_id"] =  $_POST['albumid'];
				$data["order"] = 10;
				$data["pic_result"][] = $uploadfiles;
				
				$result = attestationsClass::Addattestations($data);
				$_data['user_id'] =$data['user_id'];
				$_data['type_id'] = $_POST['albumid'];
				$_data['status'] =0;
				attestationsClass::ActionAttestationsUser($_data);
				$_P['status'] = "success";
			} else {
				$_P['status'] = "failure";
			}
		}
		
	}
	
	
}else{
	$_P['user_id'] = $_REQUEST['user_id'];
}
$magic->assign("_P",$_P);
$magic->template_dir = ROOT_PATH."/modules/attestations";
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
$magic->display("attestations.upload.tpl");
exit;
?> 