<?php
/******************************
 * $File: credit.inc.php
 * $Description: 积分处理文件
 * $Author: ahui 
 * $Time:2010-06-06
 * $Update:Ahui
 * $UpdateDate:2012-06-10  
 * Copyright(c) 2010 - 2012 by deayou.com. All rights reserved
******************************/

if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问

require_once 'credit.class.php';

class CreditModule {

	/**
	 * 更新积分
	 * @param $param
	 *		array(
	 *			'user_id' => '会员ID',
	 *			'credit_type_code => '积分类型代码',
	 *			'value' => '[OPTION]变更加分值',
	 *			'op_user' => '[OPTION]操作者ID'
	 *		)
	 * @return bool
	 */
	public static function UpdateCredit ($param) {

		$user_id = (int)$param['user_id'];
		$credit_type_code = trim($param['credit_type_code']);
		$value   = isset($param['value'])?(int)$param['value']:0;
		$op_user = isset($param['op_user'])?(int)$param['op_user']:(int)$_SESSION['user_id'];
		if ($user_id <= 0 || !$credit_type_code) {
			return false;
		}
		
		return true === Credit::Update($user_id, $credit_type_code, $value, $op_user)?true:false;
	}

	/**
	 * 获取积分
	 * @param $user_id 会员ID
	 * @return int
	 */
	public static function GetCredit ($user_id) {

		return Credit::Get($user_id);
	}

	/**
	 * 更新积分类型
	 * @param $param
	 *		array(
	 *			'id' => 0,
	 *			'code' => '积分类型代码',
	 *			'name' => '积分类型名称',
	 *			'value' => '积分值',
	 *			'cycle' => '[OPTIOIN]积分周期',
	 *			'award_times' => '[OPTION]奖励次数',
	 *			'interval' => '[OPTION]时间间隔',
	 *			'remark' => '[OPTION]备注',
	 *			'op_user' => '[OPTION]操作者ID'
	 *		)
	 */
	public static function UpdateCreditType ($param) {

		$id     = isset($param['id'])?(int)$param['id']:0;
		$code   = isset($param['code'])?$param['code']:'';
		$name   = isset($param['name'])?$param['name']:'';
		$value  = isset($param['value'])?(int)$param['value']:0;
		$cycle  = isset($param['cycle'])?(int)$param['cycle']:0;
		$award_times = isset($param['award_times'])?(int)$param['award_times']:0;
		$interval    = isset($param['interval'])?(int)$param['interval']:0;
		$remark      = isset($param['remark'])?$param['remark']:'';
		$op_user     = isset($param['op_user'])?$param['op_user']:$_SESSION['user_id'];

		if (!$name || !$code || $cycle <= 0) {
			return false;
		}
		
		return Credit::UpdateCreditType(
					$id,
					$code,
					$name,
					$value,
					$cycle,
					$award_times,
					$interval,
					$remark,
					$op_user
				)?true:false;
	}
}



if ($template==""){
$template = "user_credit.html";
}
?>
