define(function(require, exports, module) {
				
	exports.bank_del = function(){
		$(".bank_del").attr('href',"javascript:;");
		$(".bank_del").live("click",function(){
			var hr = $(this).attr('data-href');
			deayou.use("header",function(e){e.ajaxConfirm("确定要删除此账号",hr,"/?user&q=code/account/bank");});
		 })
	}
	
		
	exports.cash = function(){
		$(".cach_cancel").attr('href',"javascript:;");
		$(".cach_cancel").live("click",function(){
			var hr = $(this).attr('data-href');
			deayou.use("header",function(e){e.ajaxConfirm("确定要取消此笔提现",hr,"/?user&q=code/account/cash");});
		 })
		
		$("#account_cash_form").live("submit",function(){
			require("submitform");	
			$("#account_cash_form").ajaxSubmit({
				 success: function(result, status) {
					 if (result==1){
						  deayou.use("header",function(e){e.ajaxYes("提现成功，请等待管理员的审核","false");});
						  location.href='/?user&q=code/account/cash';
					 }else{
						 deayou.use("header",function(e){e.ajaxError(result,"false");});
					 }
				}
			 });
			return false;
		 })
	}
});
