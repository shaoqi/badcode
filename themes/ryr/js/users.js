var is_rejest=0;
define(function(require, exports, module) {
	
	exports.check_email = function(email,re){
		var reg1 = /([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)/;
		var msg = "";
		email = $("#"+email).val();
		email = trim(email);
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
				url:'/?user',
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
		var _username = trim($("#"+username).val());
		msg = test_username(_username);
		if(msg!=true){
			$("#"+re).html(msg);
			return false;
		}
		
		if (exports.get_length(_username) <4){
			msg = "用户名不能小于2个字";
			$("#"+re).html(msg);
		}
		else if (exports.get_length(_username)>15){
			msg = "用户名不能大于15个字";
			$("#"+re).html(msg);
		}else{
			$.ajax({
				type:"get",
				url:'/?user',
				data:'&q=check_username&username='+_username,
				success:function(result){
					if (result=="1"){
						msg = "用户名已经存在";	
					}else{
						//msg = "<font color='red'>可以注册</font>";	
						msg = "<img  src='/themes/ryr/images/answer_success.jpg'>";	
					}
					$("#"+re).html(msg);
				},
				cache:false
			});
		}
	}
	exports.get_length= function (str){
		var len = str.length;
		var reLen = 0;
		for (var i = 0; i < len; i++) {        
			if (str.charCodeAt(i) < 27 || str.charCodeAt(i) > 126) {
				// 全角    
				reLen += 2;
			} else {
				reLen++;
			}
		}
		return reLen;    
	}
	exports.check_password = function(password,re){
		var s = trim($("#"+password).val());
		s = s.length;
		if (s<6 || s>15){
			$("#"+re).html("密码不能小于6位大于15位");
		}else{
			$("#"+re).html("<img  src='/themes/ryr/images/answer_success.jpg'>");
		}
	}
	
	exports.check_confirm = function(password,re){
		if (trim($("#password").val())==''){
			$("#"+re).html("确认密码不能为空");
		}else if(trim($("#password").val())!=$("#"+password).val()){
			$("#"+re).html("两次密码不一样");
		}else{
			$("#"+re).html("<img  src='/themes/ryr/images/answer_success.jpg'>");
		}
		
	}
	
	exports.check_phone = function(phone,re){
		if(isMobile(phone)){
			if(!re){
				return true;
			}
			$.ajax({
				type:"get",
				url:'/?user&q=check_phone',
				data:'&phone='+phone,
				success:function(result){
					if (result=="0"){
						msg = "手机号码已经存在";
					}else{
						//msg = "<font color='red'>可以注册</font>";	
						msg = "<img  src='/themes/ryr/images/answer_success.jpg'>";	
					}
					$("#"+re).html(msg);
				},
				cache:false
			});
		}else{
			if(re){
				$("#"+re).html("手机号码不正确");
			}else{
				return false;
			}
			
		}
		return isMobile(phone);
	}	
	 exports.reg = function (){
		$('#username').live("blur",function(){
			exports.check_username("username","username_notice");	
		})
		$('#password').live("blur",function(){
			exports.check_password("password","password_notice");	
		}) 
		$('#confirm_password').live("blur",function(){
			exports.check_confirm("confirm_password","conform_password_notice");	
		})
		$('#phone').live("blur",function(){
			exports.check_phone($('#phone').val(),"phone_notice");	
		});
		$('#email').live("blur",function(){
			exports.check_email('email','email_notice');
		});
		$("#reg_form").live("submit",function(){
			var mail=trim($("#email").val()).split('@');
			require("submitform");			
			var msg = '';
			var alt = test_username($("#username").val());
			if(alt!=true){
				msg+=alt+'\n';
			}
			if(empty(trim($("#password").val()))){
				msg+='密码不能为空'+'\n';
			}
			if(empty(trim($("#confirm_password").val()))){
				msg+='确认密码不能为空'+'\n';
			}	
			if(trim($("#password").val())!=trim($("#confirm_password").val())){
				msg+='两次密码不一样'+'\n';
			}

			if(!isMobile($("#phone").val())){
				msg+='请输入正确的手机号码'+'\n';
			}
			var mail = trim($("#email").val());
			if(empty(mail)){
				msg+='请输入电子邮箱'+'\n';
			}
			var reg1 = /([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)/;
			if(!reg1.test(mail)){
				msg+='请输入正确的电子邮箱'+'\n';
			}
			if(empty(trim($("#phone_code").val()))){
				msg+='请输入手机邀请码'+'\n';
			}
			if(msg!=''){
				alert(msg);
				return false;
			}
			if(is_rejest ==1){
				alert('数据正在提交中，请稍后');
				return false;
			}
			is_rejest = 1;
			$('#submit').attr("disabled", true);
   			$("#reg_form").ajaxSubmit({
				 success: function (result, status) {
						
					 if(parseInt(result)==1){
						 alert("尊敬的用户，恭喜您完成用户注册！\n在您完成安全信息验证后，即可投资。");
						 mail=mail.split('@');
						 is_rejest = 0;
						 $('#submit').attr("disabled", false);
						 window.location.href='http://mail.'+mail[1];
					 }else{
						is_rejest = 0;
						$('#submit').attr("disabled", false);
						alert(result);
					 }
					return false;
				}

			 });
			 return false; // cancel conventional submit
			
		 })
		 //发送验证码
		 $("#phone_send").click(function(){
			var phone = $("#phone").val();
			phone = trim(phone);
			if (phone==""){
				alert('手机号码不能为空');				
			}else{
				var phone_status = exports.check_phone(phone);
				if (phone_status){
					$.get("/?user&q=send_code",{ phone: phone},
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

function test_username(d){
	d = trim(d);
	if(empty(d)){
		return '请填写用户名';
	}else{
		if(!/^[_a-z0-9]{3,16}$/i.test(d)){
			return '用户名只能由3-16位字母、数字或下划线构成';
		} else {
			if(!/^[a-z]/i.test(d)){
				return  '用户名只能以字母开头';
			} else {
				if(/_$/.test(d)){
					return '为了您方便记住用户名，末尾不要用下划线';
				}else{
					if(- 1 != d.indexOf("xx")){
						return '用户名不能包含xx';
					}else{
						if(- 1 != d.indexOf("admin")){
							return '用户名不能包含admin';
						}else{
							if(- 1 != d.indexOf("kf")){
								return '用户名不能包含kf';
							}else{
								if(- 1 != d.indexOf("kefu")){
									return '用户名不能包含kefu';
								}else{
									return true;
								}
							}
						}
					}
				}
			}
		}
	}
}