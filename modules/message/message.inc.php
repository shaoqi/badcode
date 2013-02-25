<?php
if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

include_once("message.class.php");

$_U['epage'] = 20;


if ($_U['query_type'] == "list"){	
	if ($_REQUEST['del']!=""){
		$data['id'] = array($_REQUEST['del']);
		$data['user_id'] = $_G['user_id'];
		$result = messageClass::DeleteMessageReceive($data);
		if ($result>0){
			$msg = array($MsgInfo["message_delete_success"],"",$_U['query_url']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
	}elseif (isset($_POST['type'])){
		if ($_POST['type']=="delete"){
			$data['id'] = $_POST['id'];
			$data['user_id'] = $_G['user_id'];
			
			$result = messageClass::DeleteMessageReceive($data);
			if ($result>0){
				$msg = array($MsgInfo["message_delete_success"],"",$_U['query_url']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
		
		}elseif ($_POST['type']=="yes" || $_POST['type']=="no"){
			$data['id'] = $_POST['id'];
			$data['user_id'] = $_G['user_id'];
			$data['status'] = ($_POST['type']=="yes")?1:0;
			$result = messageClass::ActionMessageReceive($data);
			if ($result>0){
				$msg = array($MsgInfo["message_action_success"],"",$_U['query_url']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}
	}
}


elseif($_U['query_type'] == "sent"){
	if(isset($_POST['contents'])){
		$msg = check_valicode();
		if ($msg==""){
			$var = array("receive_user","contents","name","status","sented");
			$data = post_var($var);
			$data['user_id'] = $_G['user_id'];
			$data['type'] = "user";  
			$result = messageClass::AddMessage($data);
			if ($result>0){
				$msg = array($MsgInfo["message_send_success"],"",$_U['query_url']);
			}else{
				$msg = array($MsgInfo[$result]);
			}
		}
	}
}	

//查看并回复
elseif ($_U['query_type'] == "view"){	
	if (isset($_POST['contents'])){
		$_data['id'] = $_POST['id'];
		$_data['user_id'] = $_G['user_id'];
		$result = messageClass::GetMessageReceiveOne($_data);
		$data = post_var(array("contents"));
		$data['name'] = "Re:".$result['name'];
		$data['contents'] .= "<br>------------------ 原始信息 ------------------<br>".$result['contents'];
		$data['user_id'] = $_G['user_id'];
		$data['type'] = "user";  
		$data['receive_user'] = $_POST['receive_user']; 
		$data['status'] = 0; 
		$result = messageClass::AddMessage($data);
		if ($result>0){
			$msg = array($MsgInfo["message_send_success"],"",$_U['query_url']);
		}else{
			$msg = array($MsgInfo[$result]);
		}
	}else{
		$data['user_id'] = $_G['user_id'];
		$data['id'] = $_REQUEST['id'];
		$data['status'] = 1;
		$result = messageClass::GetMessageReceiveOne($data);
		if (is_array($result)){
			$_U['message_result'] = $result;
		}else{
			$msg = array($MsgInfo[$result],"",$_U['query_url']);
		}
	}
}

//查看并回复
elseif ($_U['query_type'] == "viewed"){	
	$data['user_id'] = $_G['user_id'];
	$data['id'] = $_REQUEST['id'];
	$result = messageClass::GetMessageOne($data);
	if (is_array($result)){
		$_U['message_result'] = $result;
	}else{
		$msg = array($MsgInfo[$result],"",$_U['query_url']);
	}
}

elseif ($_U['query_type'] == "send"){	
	if ($_POST['type']=="deled"){
		$data['id'] = $_POST['id'];
		$data['user_id'] = $_G['user_id'];
		$result = messageClass::DeleteMessage($data);
		if ($result>0){
			$msg = array($MsgInfo["message_delete_success"],"",$_U['query_url']."/send");
		}else{
			$msg = array($MsgInfo[$result]);
		}
	}
}
elseif ($_U['query_type'] == "deled"){	
	if (isset($_REQUEST['id']) ){
		$data['id'] = $_REQUEST['id'];
		$data['user_id'] = $_G['user_id'];
		$result = messageClass::DeleteMessageReceive($data);
		if ($result>0){
			$msg = array($MsgInfo["message_action_success"],"","/?user&q=code/message");
		}else{
			$msg = array($MsgInfo[$result]);
		}
	}else{
		$msg = array("请选中再进行操作");
	}
}
elseif ($_U['query_type'] == "senteds"){	
		
		if (isset($_POST['type']) && $_POST['type']==2){
			
			$data['id'] = $_POST['id'];
			$data['sent_user'] = $_G['user_id'];
			$data['sented'] = 0;
			$result = messageClass::update($data);
			if ($result!==true){
				$msg = array($MsgInfo[$result],"",$_U['query_url']);
			}else{
				$msg = array("操作成功");
			}
			
		}else{
			/* $data['sent_user'] = $_G['user_id'];
			$data['page'] = $_U['page'];
			$data['epage'] = $_U['epage'];
			$data['sented'] = 1;
			$result = messageClass::GetList($data);
			if (is_array($result)){
				$pages->set_data($result);
				$_U['message_list'] = $result['list'];
				$_U['show_page'] = $pages->show(3);
			}else{
				$msg = array($result,"",$_U['query_url']);
			} */
			if (isset($_REQUEST['id']) ){
				$data['id'] = $_REQUEST['id'];
				$data['user_id'] = $_G['user_id'];
				$result = messageClass::DeleteMessageReceive($data);
				if ($result>0){
					$msg = array("删除成功","","/?user&q=code/message");
				}else{
					$msg = array($MsgInfo[$result]);
				}
			}else{
				$msg = array("请选中再进行操作");
			}
				}
	}
elseif ($_U['query_type'] == "sentdeled"){	
	if (isset($_REQUEST['id']) ){
		$data['id'] = $_REQUEST['id'];
		$data['user_id'] = $_G['user_id'];
		$result = messageClass::DeleteMessage($data);
		if ($result>0){
			$msg = array($MsgInfo["message_action_success"],"","/?user&q=code/message/sented");
		}else{
			$msg = array($MsgInfo[$result]);
		}
	}else{
		$msg = array("请选中再进行操作");
	}
}
$template = "user_message.html";
?>
