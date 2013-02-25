<?

//按月等额本息法
	//贷款本金×月利率×（1+月利率）还款月数/[（1+月利率）还款月数-1] 
	//a*[i*(1+i)^n]/[(1+I)^n-1] 
	//（a×i－b）×（1＋i）
	 function EqualInterest ($data = array()){
		//借款的总金额
		if (isset($data['account']) && $data['account']>0){
			$account = $data['account'];
		}else{
			return "";
		}
		
		//借款的年利率
		if (isset($data['year_apr']) && $data['year_apr']>0){
			$year_apr = $data['year_apr'];
		}else{
			return "";
		}
		
		//借款的月数
		if (isset($data['month_times']) && $data['month_times']>0){
			$month_times = $data['month_times'];
		}
		
		//借款的时间
		if (isset($data['borrow_time']) && $data['borrow_time']>0){
			$borrow_time = $data['borrow_time'];
		}else{
			$borrow_time = time();
		}
		
		//月利率
		$month_apr = $year_apr/(12*100);
		
		//幂，月利率的幂
		$_li = pow((1+$month_apr),$month_times);
		
		$repayment = round($account * ($month_apr * $_li)/($_li-1),2);
		$_result = array();
		if (isset($data['type']) && $data['type']=="all"){
			$_result['repayment_account'] = $repayment*$month_times;
			$_result['monthly_repayment'] = $repayment;
			$_result['month_apr'] = round($month_apr*100,2);
		}else{
			//$re_month = date("n",$borrow_time);
			for($i=0;$i<$month_times;$i++){
				if ($i==0){
					$interest = round($account*$month_apr,2);
				}else{
					$_lu = pow((1+$month_apr),$i);
					$interest = round(($account*$month_apr - $repayment)*$_lu + $repayment,2);
				}
				$_result[$i]['repayment_account'] = $repayment;
				$_result[$i]['repayment_time'] = get_times(array("time"=>$borrow_time,"num"=>$i+1));
				$_result[$i]['interest'] = $interest;
				$_result[$i]['capital'] = $repayment-$interest;
			}
		}
		return $_result;
	}
	
	
	//按季等额本息法
	//贷款本金×月利率×（1+月利率）还款月数/[（1+月利率）还款月数-1] 
	//a*[i*(1+i)^n]/[(1+I)^n-1] 
	//（a×i－b）×（1＋i）
	 function EqualSeason ($data = array()){
	 	
		//借款的月数
		if (isset($data['month_times']) && $data['month_times']>0){
			$month_times = $data['month_times'];
		}
		
		//按季还款必须是季的倍数
		if ($month_times%3!=0){
			return false;
		}
	 
	 	//借款的总金额
		if (isset($data['account']) && $data['account']>0){
			$account = $data['account'];
		}else{
			return "";
		}
		
		//借款的年利率
		if (isset($data['year_apr']) && $data['year_apr']>0){
			$year_apr = $data['year_apr'];
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
		$_season = $month_times/3;
		
		//每季应还的本金
		$_season_money = round($account/$_season,2);
		
		//$re_month = date("n",$borrow_time);
		$_yes_account = 0 ;
		for($i=0;$i<$month_times;$i++){
			$repay = $account - $_yes_account;//应还的金额
			
			$interest = round($repay*$month_apr,2);//利息等于应还金额乘月利率
			
			$capital = 0;
			if ($i%3==2){
				$capital = $_season_money;//本金只在第三个月还，本金等于借款金额除季度
				$_yes_account = $_yes_account+$capital;
				$repay = $account - $_yes_account;
			}
			
			$_result[$i]['repayment_account'] = $repay;
			$_result[$i]['repayment_time'] = "";
			$_result[$i]['interest'] = $interest;
			$_result[$i]['capital'] = $capital;
		}
		return $_result;
	}
	$data['account'] = 10000;
	$data['month_times'] = 12;
	$data['year_apr'] = 12;
	$result = EqualSeason($data);
?>