<?php

function OtherAddReg($user_id){
global $mysql,$_G;
require_once(ROOT_PATH."/modules/borrow/borrow.class.php");
$sql = "insert into `{borrow_amount}` set user_id='{$user_id}'";
$mysql->db_query($sql);
$_data["user_id"] = $user_id;
$_data["amount_type"] = "borrow";
$_data["type"] = "borrow_first";
$_data["oprate"] = "add";
$_data["nid"] = "borrow_first_".$user_id;
$_data["account"] = empty($_G['system']['con_borrow_amount_first'])?2000:$_G['system']['con_borrow_amount_first'];
$_data["remark"] = "³õÊ¼¶î¶È";
borrowClass::AddAmountLog($_data);
}
?>