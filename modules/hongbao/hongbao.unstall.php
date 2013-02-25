<?php
/******************************
 * $File: hongbao.unstall.php
 * $Description: 卸载红包数据库
 * $Author: ada 
 * $Time:2012-12-11
 * $Update:
 * $UpdateDate: 
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

$sql = "DROP TABLE `deayou_hongbao` ,`deayou_hongbao_mingxi` ,`deayou_hongbao_type` ;";
$mysql->db_querys($sql);
?>