define(function(require, exports, module) {
	
	//检测基本信息
	exports.check_info= function (form_id){
		 $("#"+form_id).submit(function(){	
			var _var = [["realname","姓名不能为空"],["card_id","身份证号码不能为空"],["phone_num","手机号码不能为空"],["birthday","生日必须填写"],["edu","学历要选择"],["school_year","入学年份不能为空"],["school","学校不能为空"],["jiguan_city","籍贯要选择"],["live_city","所在地要选择"],["address","地址要填写"],["phone","电话要填写"],["post_id","邮政编号要填写"]]; 
			var _var_status=1;
			for(var i=0;i<_var.length;i++) { 
				if ($("#"+_var[i][0]).val()==""){
					deayou.use("header",function(e){e.ajaxError(_var[i][1],'false');});
					_var_status = 0;
					return false;
				}
			} 
			
		})
	}
	
		
	//检测工作信息
	exports.check_work= function (form_id){
		 $("#"+form_id).submit(function(){	
			var _var = [["name","公司名称不能为空"],["work_style","职业状态不能为空"],["work_city","工作城市不能为空"],["company_type","公司类别不能为空"],["company_size","公司规模不能为空"],["worktime1","入职时间不能为空"],["office","职务不能为空"],["address","工作地址不能为空"],["work_email","工作邮箱不能为空"],["tel","工作电话不能为空"],["family_name","直系亲属姓名不能为空"],["family_relation","直系亲属关系不能为空"],["family_phone","直系亲属电话不能为空"],["other_name","其他人姓名不能为空"],["other_relation","其他人关系不能为空"],["other_phone","其他人电话不能为空"]]; 
			var _var_status=1;
			for(var i=0;i<_var.length;i++) { 
				if ($("#"+_var[i][0]).val()==""){
					deayou.use("header",function(e){e.ajaxError(_var[i][1],'false');});
					_var_status = 0;
					return false;
				}
			} 
			
		})
	}
		
	exports.check_job= function (form_id){
		 $("#"+form_id).submit(function(){	
			var _var = [["name","公司名称不能为空"],["worktime1","入职时间不能为空"],["office","职务不能为空"],["address","工作地址不能为空"],["work_email","工作邮箱不能为空"],["tel","工作电话不能为空"],["family_name","直系亲属姓名不能为空"],["family_relation","直系亲属关系不能为空"],["family_phone","直系亲属电话不能为空"],["other_name","其他人姓名不能为空"],["other_relation","其他人关系不能为空"],["other_phone","其他人电话不能为空"]]; 
			var _var_status=1;
			for(var i=0;i<_var.length;i++) { 
				if ($("#"+_var[i][0]).val()==""){
					deayou.use("header",function(e){e.ajaxError(_var[i][1],'false');});
					_var_status = 0;
					return false;
				}
			} 
			
		})
	}
	
	
	//上传资料
	exports.check_approve= function (form_id){
		
		//证明材料上传
		$(".loan_approve_url").click(function(){
				var nid = $(this).attr('data-nid');
				if (form_id=="app"){
				deayou.use("header",function(e){e.ajaxDialog("上传资料","/?user&q=code/borrow/loan_att&type=app&nid="+nid,"");});
				}else{
				deayou.use("header",function(e){e.ajaxDialog("上传资料","/?user&q=code/borrow/loan_att&nid="+nid,"");});
				}
		 })
		
		//实名认证
		$(".loan_approve_realname").click(function(){
				deayou.use("header",function(e){e.ajaxDialog("实名认证","/?user&q=code/borrow/loan_realname","");});
		 })
		
		
		//手机认证
		$(".loan_approve_phone").click(function(){
				deayou.use("header",function(e){e.ajaxDialog("手机认证","/?user&q=code/borrow/loan_phone","");});
		 })
	}
	
	
	//手机认证
	exports.check_phone= function (){
		
		//手机获取验证码
		$("#ajax_phone_get").click(function(){
			var patrn = /(^0{0,1}1[3|4|5|6|7|8|9][0-9]{9}$)/;
			if ($("#ajax_phone").val()==""){
				$("#ajax_phone_msg").html('手机号码不能为空');
			}else if (!patrn.exec($("#ajax_phone").val())) {
				$("#ajax_phone_msg").html('手机号码格式不正确');
			}else{
				var phone = $("#ajax_phone").val();
				$.post("/?user&q=code/approve/phone&style=ajax",{ phone: phone},
				function (result){
					if(result==1){
						alert("短信已经发送到你的手机,请注意查收");
						var seconds = 59;
						var speed = 1000;
						countDown(seconds,speed);//$("#ajax_phone_msg").html('短信已经发送到你的手机,请注意查收');
					}else{
						alert(result);
					}					
				});
			}
				
		 })
		//验证码认证
		$("#ajax_phone_submit").click(function(){
			var patrn = /(^0{0,1}1[3|4|5|6|7|8|9][0-9]{9}$)/;
			var type = $("#type").val();
			if ($("#ajax_phone").val()==""){
				$("#ajax_phone_msg").html('手机号码不能为空');
			}else if (!patrn.exec($("#ajax_phone").val())) {
				$("#ajax_phone_msg").html('手机号码格式不正确');
			}else if ($("#ajax_phone_code").val()=="") {
				$("#ajax_phone_msg").html('手机验证码不能为空');
			}else{
				var phone_code = $("#ajax_phone_code").val();
				var phone_new = $("#ajax_phone").val();
				$.post("/?user&q=code/approve/phone&_type=borrow",{sms_code:phone_code,phone_new:phone_new},
					function (result){
						if (result==1){
							alert("手机验证成功");	
							if(type==2){
								location.href="/renzheng/index.html?type=email";
							}else{
								location.href="/?user&q=code/approve/phone";
							}
						}else{
							alert("验证码错误");
							//$("#ajax_phone_msg").html(result);
						}
				});
				
			}
				
		 })
	}
});
	
