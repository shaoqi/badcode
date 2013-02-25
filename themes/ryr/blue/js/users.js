define(function(require, exports, module) {
	
	exports.check_email = function(email,re){
		var reg1 = /([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)/;
		var msg = "";
		email = $("#"+email).val();
		if (email == ''){
			msg = "邮箱不能为空";
			$("#"+re).html(msg);
		}
		else if (!reg1.test(email)){
			msg = "邮箱格式错误";
			$("#"+re).html(msg);
		}else{
			$.ajax({
				type:"get",
				url:'/?user&q=login',
				data:'&q=check_email&email='+email,
				success:function(result){
					if (result=="1"){
						msg = "邮箱已经存在";	
						status = "1";
					}else{
						msg = "可以注册";	
						status = "0";
					}
					$("#"+re).html(msg);
				},
				cache:false
			});
		}
	}
 
 
	exports.check_username = function(username,re){
		var msg = "";
		var s = $("#"+username).val().length;
		var _username = $("#"+username).val();
		if (s <3){
			msg = "用户名不能小于3个字";
			$("#"+re).html(msg);
		}
		else if (s>15){
			msg = "用户名不能大于15个字";
			$("#"+re).html(msg);
		}else{
			$.ajax({
				type:"get",
				url:'/?user&q=login',
				data:'&q=check_username&username='+_username,
				success:function(result){
					if (result=="1"){
						msg = "用户名已经存在";	
					}else{
						msg = "<font color='red'>可以注册</font>";	
					}
					$("#"+re).html(msg);
				},
				cache:false
			});
		}
	}
	
	exports.check_password = function(password,re){
		var s = $("#"+password).val().length;
		if (s<6 || s>15){
			$("#"+re).html("密码不能小于6位大于15位");
		}else{
			$("#"+re).html("<font color='red'>可以注册</font>");
		}
	}
	
	exports.check_confirm = function(password,re){
		if ($("#password").val()!=$("#"+password).val()){
			$("#"+re).html("密码不一样");
		}else{
			$("#"+re).html("<font color='red'>可以注册</font>");
		}
		
	}
	
	exports.check_phone = function(phone){
		var patrn = /(^0{0,1}1[3|4|5|6|7|8|9][0-9]{9}$)/;
		if (!patrn.exec(phone)) {
			return 0;
		}
		return 1;
	}
	
	 exports.reg = function (){
		 $('#email').live("blur",function(){
			exports.check_email("email","email_notice");	
		 })
		  $('#username').live("blur",function(){
			exports.check_username("username","username_notice");	
		})
		   $('#password').live("blur",function(){
			exports.check_password("password","password_notice");	
		})
		   
		 $('#confirm_password').live("blur",function(){
			exports.check_confirm("confirm_password","conform_password_notice");	
		})
		 
		$("#reg_form").live("submit",function(){
			require("submitform");			
			var msg = '';			
			if($("#email").val()==''){
				msg+='邮箱不能为空'+'\n';
			}
			if($("#username").val()==''){
				msg+='用户名不能为空'+'\n';
			}
			if($("#password").val()==''){
				msg+='密码不能为空'+'\n';
			}			
			if(msg!=''){
				alert(msg);
				return false;
			}
			
   			$("#reg_form").ajaxSubmit({
				 success: function (result, status) {
					 if(parseInt(result)>0){
						 alert("注册成功");
						 //deayou.use("header",function(e){e.ajaxYes("注册成功","false");});
						 location.href="/?user&q=reg&type=email";
					 }else{
						deayou.use("header",function(e){e.ajaxError(result,"false");});
					 }
					return false;
				}

			 });
			 return false; // cancel conventional submit
			
		 })
		 //发送验证码
		 $("#phone_send").click(function(){
			var phone = $("#phone").val();
			var username = $("#username").val();
			if (phone==""){
				alert('手机号码不能为空');				
			}else{
				var phone_status = exports.check_phone(phone);
				if (phone_status==1){
					$.post("/?user&q=reg&type=ajax",{ phone: phone,username: username},
						function (result){
							if (result==1){
								alert("短信已经发送到你的手机");
							}else{
								alert(result);								
							}
					})
				}else{					
					alert('手机号码填写不正确');					
				}
			}
		})
		
	 }
	
	
	 exports.info_vip = function (){
				$(".user_info_vip").live("click",function(){
													var con = "";
													var vip = "";
				 con = $(this).attr("data-account");
				text = eval("({"+con+"})");
					var balance = text.balance;
					 vip = text.vip;
					var account = text.account;
					if (account>balance){
						deayou.use("header",function(e){e.ajaxConfirm("您的余额不足，是否马上进行充值","false","/?user&q=code/account/recharge_new");});
					}else{
						deayou.use("header",function(e){e.ajaxDialog("成为VIP会员","/?user&q=code/users/vip_new&vip="+vip+"&_time="+Math.random(1,9));});
					}
									  
				})
	 }
	 
	 
	exports.info_vip_new = function (){
		$("#user_info_vip_form").die();
		$("#user_info_vip_form").live("submit",function(){
			require("submitform");	
   			$("#user_info_vip_form").ajaxSubmit({
				 success: function (result, status) {
					 if(parseInt(result)>0){
						 deayou.use("header",function(e){e.ajaxYes("申请VIP成功","/?user&q=code/users/vip_log");});
						
					 }else{
						 alert(result);
					 }
				}

			 });
			 return false; // cancel conventional submit
		 })
	
	}
});

