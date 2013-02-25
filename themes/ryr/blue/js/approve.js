define(function(require, exports, module) {
	
	exports.realname = function(){
		$("#approve_realname_form").live("submit",function(){
			var error = "";											   
			if ($("#realname").val()==""){
				error  = "姓名不能为空";	
			}
			if ($("#card_id").val()==""){
				error  = "身份证号码不能为空";	
			}
			if (error!=""){
			deayou.use("header",function(e){e.ajaxError(error,"false")});		
			 return false; // cancel conventional submit
			}
		 })
	}
});
