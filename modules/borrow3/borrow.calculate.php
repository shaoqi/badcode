<?php
 function EqualInterest ($data = array()){	
	//借款方式,account,period,apr,time,style,type
	//return account_all,account_interest，account_capital,repay_time
	if (IsExiest($data["account"])=="") return "equal_account_empty";
	if (IsExiest($data["period"])=="") return "equal_period_empty";
	if (IsExiest($data["apr"])=="") return "equal_apr_empty";
	if (isset($data['time']) && $data['time']>0){
		$data['time'] = $data['time'];
	}else{
		$data['time'] = time();
	}
	$borrow_style =$data['style'];
	if ($borrow_style==0){
		return EqualMonth($data);
	}elseif ($borrow_style==1){
		return EqualSeason($data);
	}elseif ($borrow_style==2){
		return EqualEnd($data);
	}elseif ($borrow_style==3){
		return EqualEndMonth($data);
	}elseif ($borrow_style==4){
		return EqualDeng($data);
	}
	//体验标
	elseif ($borrow_style==5){
		return EqualTiyan($data);
	}

}

//等额本息法
//贷款本金×月利率×（1+月利率）还款月数/[（1+月利率）还款月数-1] 
//a*[i*(1+i)^n]/[(1+I)^n-1] 
//（a×i－b）×（1＋i）
 function EqualMonth ($data = array()){
	
	$account = $data['account'];
	$year_apr = $data['apr'];
	$period = $data['period'];
    $time = $data['time'];
	
	$month_apr = $year_apr/(12*100);
	$_li = pow((1+$month_apr),$period);
	if ($account<0) return;
	$repay_account = round($account * ($month_apr * $_li)/($_li-1),2);//515.1
		
	$_result = array();
	//$re_month = date("n",$borrow_time);
	$_capital_all = 0;
	$_interest_all = 0;
	$_account_all = 0.00;
	for($i=0;$i<$period;$i++){
		if ($i==0){
			$interest = round($account*$month_apr,2);
		}else{
			$_lu = pow((1+$month_apr),$i);
			$interest = round(($account*$month_apr - $repay_account)*$_lu + $repay_account,2);
		}
		
		//echo $repay_account."<br>";
		//防止一分钱的问题
		if ($i==$period-1)
		{
			$capital = $account - $_capital_all;
			$interest = $repay_account-$capital;
		}else{
			$capital =  $repay_account - $interest;
		}
		
		//echo $capital."<br>";
		$_account_all +=  $repay_account;
		$_interest_all +=  $interest;
		$_capital_all +=  $capital;
		
		$_result[$i]['account_all'] =  round($repay_account,2);
		$_result[$i]['account_interest'] = round( $interest,2);
		$_result[$i]['account_capital'] =  round($capital,2);
		$_result[$i]['account_other'] =  round($repay_account*$period-$repay_account*($i+1),2);
		$_result[$i]['repay_month'] =  round($repay_account,2);
		$_result[$i]['repay_time'] = get_times(array("time"=>$time,"num"=>$i+1));
	}
	if ($data["type"]=="all"){
		$_result_all['account_total'] =  round($_account_all,2);
		$_result_all['interest_total'] =  round($_interest_all,2);
		$_result_all['capital_total'] =  round($_capital_all,2);
		$_result_all['repay_month'] =  round($repay_account,2);
		$_result_all['month_apr'] = round($month_apr*100,2);
		return $_result_all;
	}
	return $_result;
}

//按季等额本息法
 function EqualSeason ($data = array()){
	
	//借款的月数
	if (isset($data['period']) && $data['period']>0){
		$period = $data['period'];
	}
	
	//按季还款必须是季的倍数
	if ($period%3!=0){
		return false;
	}
 
	//借款的总金额
	if (isset($data['account']) && $data['account']>0){
		$account = $data['account'];
	}else{
		return "";
	}
	
	//借款的年利率
	if (isset($data['apr']) && $data['apr']>0){
		$year_apr = $data['apr'];
	}else{
		return "";
	}
	
	
	//借款的时间
	if (isset($data['borrow_time']) && $data['borrow_time']>0){
		$borrow_time = $data['borrow_time'];
	}else{
		$borrow_time = time();
	}
	
	//月利率
	$month_apr = $year_apr/(12*100);
	
	//得到总季数
	$_season = $period/3;
	
	//每季应还的本金
	$_season_money = round($account/$_season,2);
	
	//$re_month = date("n",$borrow_time);
	$_yes_account = 0 ;
	$repay_account = 0;//总还款额
	$_capital_all = 0;
	$_interest_all = 0;
	$_account_all = 0.00;
	for($i=0;$i<$period;$i++){
		$repay = $account - $_yes_account;//应还的金额
		
		$interest = round($repay*$month_apr,2);//利息等于应还金额乘月利率
		$repay_account = $repay_account+$interest;//总还款额+利息
		$capital = 0;
		if ($i%3==2){
			$capital = $_season_money;//本金只在第三个月还，本金等于借款金额除季度
			$_yes_account = $_yes_account+$capital;
			$repay = $account - $_yes_account;
			$repay_account = $repay_account+$capital;//总还款额+本金
		}
		$_repay_account = $interest+$capital;
		$_result[$i]['account_interest'] = round($interest,2);
		$_result[$i]['account_capital'] = round($capital,2);
		$_result[$i]['account_all'] =round($_repay_account,2);
		
		$_account_all +=  $_repay_account;
		$_interest_all +=  $interest;
		$_capital_all +=  $capital;
		
		$_result[$i]['account_other'] = round($repay,2);
		$_result[$i]['repay_month'] = round($repay_account,2);
		$_result[$i]['repay_time'] = get_times(array("time"=>$time,"num"=>$i+1));
	}
	if ($data["type"]=="all"){
		$_result_all['account_total'] =  round($_account_all,2);
		$_result_all['interest_total'] =  round($_interest_all,2);
		$_result_all['capital_total'] =  round($_capital_all,2);
		$_result_all['repay_month'] = "-";
		$_result_all['repay_season'] = $_season_money;
		$_result_all['month_apr'] = round($month_apr*100,2);
		return $_result_all;
	}
	return $_result;
}


//到期还本还息
 function EqualEnd ($data = array()){
	
	//借款的月数
	if (isset($data['period']) && $data['period']>0){
		$period = $data['period'];
	}
	
 
	//借款的总金额
	if (isset($data['account']) && $data['account']>0){
		$account = $data['account'];
	}else{
		return "";
	}
	
	//借款的年利率
	if (isset($data['apr']) && $data['apr']>0){
		$year_apr = $data['apr'];
	}else{
		return "";
	}
	
	
	//借款的时间
	if (isset($data['borrow_time']) && $data['borrow_time']>0){
		$borrow_time = $data['borrow_time'];
	}else{
		$borrow_time = time();
	}
	
	//月利率
	$month_apr = $year_apr/(12*100);
	
	$interest = $month_apr*$period*$account;
	
	if (isset($data['type']) && $data['type']=="all"){
		$_result_all['account_total'] =   round($account + $interest,2);
		$_result_all['interest_total'] =  round($interest,2);
		$_result_all['capital_total'] =  round($account,2);
		$_result_all['repay_month'] =  round($account + $interest,2);
		$_result_all['month_apr'] = round($month_apr*100,2);
		return $_result_all;
	}else{
		$_result[0]['account_all'] = round($interest+$account,2);
		$_result[0]['account_interest'] = round($interest,2);
		$_result[0]['account_capital'] = round($account,2);
		$_result[0]['account_other'] = round($account,2);
		$_result[0]['repay_month'] = round($interest+$account,2);
		$_result[0]['repay_time'] = get_times(array("time"=>$borrow_time,"num"=>$period));
		
		return $_result;
	}
}


//到期还本，按月付息
 function EqualEndMonth ($data = array()){
	
	//借款的月数
	if (isset($data['period']) && $data['period']>0){
		$period = $data['period'];
	}
 
	//借款的总金额
	if (isset($data['account']) && $data['account']>0){
		$account = $data['account'];
	}else{
		return "";
	}
	
	//借款的年利率
	if (isset($data['apr']) && $data['apr']>0){
		$year_apr = $data['apr'];
	}else{
		return "";
	}
	
	
	//借款的时间
	if (isset($data['time']) && $data['time']>0){
		$borrow_time = $data['time'];
	}else{
		$borrow_time = time();
	}
	
	//月利率
	$month_apr = $year_apr/(12*100);
	
	
	
	//$re_month = date("n",$borrow_time);
	$_yes_account = 0 ;
	$repayment_account = 0;//总还款额
	
	$interest = round($account*$month_apr,2);//利息等于应还金额乘月利率
	for($i=0;$i<$period;$i++){
		$capital = 0;
		if ($i+1 == $period){
			$capital = $account;//本金只在第三个月还，本金等于借款金额除季度
		}
		$_account_all +=  $_repay_account;
		$_interest_all +=  $interest;
		$_capital_all +=  $capital;
		
		$_result[$i]['account_all'] = $interest+$capital;
		$_result[$i]['account_interest'] = $interest;
		$_result[$i]['account_capital'] = $capital;
		$_result[$i]['account_other'] = round($account-$capital,2);
		$_result[$i]['repay_year'] = $account;
		$_result[$i]['repay_time'] = get_times(array("time"=>$borrow_time,"num"=>$i+1));
	}
	if ($data["type"]=="all"){
		$_result_all['account_total'] =  $account + $interest*$period;
		$_result_all['interest_total'] = $_interest_all;
		$_result_all['capital_total'] = $account;
		$_result_all['repay_month'] = $interest;
		$_result_all['month_apr'] = round($month_apr*100,2);
		return $_result_all;
	}
		return $_result;
}


//等本等息法
 function EqualDeng ($data = array()){
	
	$account = $data['account'];
	$year_apr = $data['apr'];
	$period = $data['period'];
    $time = $data['time'];
	
	$month_apr = $year_apr/(12*100);
	$_li = pow((1+$month_apr),$period);
	if ($account<0) return;
	$repay_account = round($account * ($month_apr * $_li)/($_li-1),2);//515.1
		
	$_result = array();
	//$re_month = date("n",$borrow_time);
	$_capital_all = 0;
	$_interest_all = 0;
	$_account_all = 0.00;
	for($i=0;$i<$period;$i++){
		
		$interest = round($account*$month_apr,2);
		
		$capital = round($account/$period,2);
			
		
		//echo $capital."<br>";
		$repay_account = $interest+$capital;
		$_account_all +=  $repay_account;
		$_interest_all +=  $interest;
		$_capital_all +=  $capital;
		
		$_result[$i]['account_all'] =  $repay_account;
		$_result[$i]['account_interest'] = $interest;
		$_result[$i]['account_capital'] =  $capital;
		$_result[$i]['account_other'] =  round($repay_account*$period-$repay_account*($i+1),2);
		$_result[$i]['repay_month'] =  round($repay_account,2);
		$_result[$i]['repay_time'] = get_times(array("time"=>$time,"num"=>$i+1));
	}
	if ($data["type"]=="all"){
		$_result_all['account_total'] =  round($_account_all,2);
		$_result_all['interest_total'] =  round($_interest_all,2);
		$_result_all['capital_total'] =  round($_capital_all,2);
		$_result_all['repay_month'] =  round($repay_account,2);
		$_result_all['month_apr'] = round($month_apr*100,2);
		return $_result_all;
	}
	return $_result;
}



//体验标
 function EqualTiyan ($data = array()){
	
	$account = 100;
	$year_apr = 20;
	$period = 1;
    $time = $data['time'];
	
		
	$_result = array();
	//$re_month = date("n",$borrow_time);
	$_capital_all = 0;
	$_interest_all = 0;
	$_account_all = 0.00;
	
		
		$interest = 2;
		
		$capital = 100;
			
		
	//echo $capital."<br>";
	$repay_account = 102;
	$_account_all =  $repay_account;
	$_interest_all =  $interest;
	$_capital_all =  $capital;
	
	$_result[0]['account_all'] =  $repay_account;
	$_result[0]['account_interest'] = $interest;
	$_result[0]['account_capital'] =  $capital;
	$_result[0]['account_other'] =  102;
	$_result[0]['repay_month'] =  102;
	$_result[0]['repay_time'] = get_times(array("time"=>$time,"num"=>$i+1));
	
	if ($data["type"]=="all"){
		$_result_all['account_total'] =  102;
		$_result_all['interest_total'] = 2;
		$_result_all['capital_total'] =  100;
		$_result_all['repay_month'] =  102;
		$_result_all['month_apr'] = round($month_apr*100,2);
		return $_result_all;
	}
	return $_result;
}

?>
