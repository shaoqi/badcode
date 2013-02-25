<?php
/******************************
 * $File: scrollpic.unstall.php
 * $Description: 卸载管理
 * $Author: ahui 
 * $Time:2011-11-09
 * $Update:None 
 * $UpdateDate:None 
 * Copyright(c) 2012 by dycms.net. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问


$sql = "DROP TABLE `{scrollpic_type}`, `{scrollpic}`;";
$mysql->db_querys($sql);
