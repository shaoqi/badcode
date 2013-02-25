define(function(require, exports, module) {
	
	exports.borrow = function (){
		if (user_id==""){
			$(".borrow_jie").live("click",function(){
				$(this).attr("href","javascript:void(0)");
				deayou.use("header",function(e){e.ajaxLogin()});							   
			})	
		}
		$("#loan_form").submit(function(){	
			var _var = [["name","借款标题不能为空"],["account","借款金额不能为空"],["borrow_apr","借款利率不能为空"]]; 
			var _var_status=1;
			for(var i=0;i<_var.length;i++) { 
				if ($("#"+_var[i][0]).val()==""){
					alert(_var[i][1]);
					_var_status = 0;
					return false;
				}
			}
			var min_apr=$("#minapr").val();
			var max_apr=$("#maxapr").val();
			var borrow_apr=$("#borrow_apr").val();
			if (borrow_apr<parseFloat(min_apr) || borrow_apr>parseFloat(max_apr)){
				alert("利率不在范围内");
				_var_status = 0;
				return false;
			}
			var borrow_apr = parseFloat(borrow_apr);
			var account = parseInt($("#account").val());
			var amount_use = parseInt($("#amount_use").val());			
			var lixi = (account*borrow_apr*0.01)/12;			
			if (account%50 != 0 || account==0){
				alert("借款金额需为50的倍数");
				_var_status = 0;
				return false;
			}else if(lixi>amount_use && $("#borrow_type").val()==4){
				alert("余额不足，不能发标");
				_var_status = 0;
				return false;
			}else if(account>amount_use && $("#borrow_type").val()!=4){
				alert("借款金额不能大于可用额度");
				_var_status = 0;
				return false;
			}
			/* if ($("#account").val()<3000 || $("#account").val()>1000000){
				alert("借款金额不在范围内");
				_var_status = 0;
				return false;
			} */
		})
	}
	
	exports.detail = function (){
		$('.borrow_content_img').live('click',function(){
			var con = $(this).attr("data-type");
			text = eval("({"+con+"})");
			var status = text.status;
			var borrow_userid = text.borrow_userid;
			var borrow_nid = text.borrow_nid;
			var borrow_account_scale = text.borrow_account_scale;
			if (user_id==borrow_userid && status==1){
				deayou.use("header",function(e){e.ajaxError('不能投自己的标','false');});		
			}else if(status==0){
				deayou.use("header",function(e){e.ajaxError('此标正等待审核。','false');});	
			}else if(status==1){
				if (borrow_account_scale==100){
					deayou.use("header",function(e){e.ajaxError('此表已投满，正等待复审。','false');});	
				}else{
					deayou.use("header",function(e){e.ajaxDialog('我要投标','/?user&q=code/borrow/tendering&borrow_nid='+borrow_nid,'');});	
				}
				return ;
			}else if(status==2){
				deayou.use("header",function(e){e.ajaxError('此标初审不通过。','false');});	
			
			}else if(status==4){
				deayou.use("header",function(e){e.ajaxError('此标复审不通过。','false');});	
			}										   
		 })		
		
		
		$(".borrow_content_care").live("click",function (){
			var borrow_nid = $(this).attr("data-nid");
			deayou.use("header",function(e){e.ajaxConfirm('是否加入收藏','/?user&q=code/borrow/add_care&code=borrow&borrow_nid='+borrow_nid,"/invest/a"+borrow_nid+".html");});								 
		})
	}
	
	exports.tendering = function (){
		$("#user_borrow_tendering").live("submit",function(){
			require("submitform");	
			var borrow_nid = $("#borrow_nid").val();
   			$("#user_borrow_tendering").ajaxSubmit({
				 success: function (result, status) {
					if(result==1){
						deayou.use("header",function(e){e.ajaxYes("投标成功","/invest/a"+borrow_nid+".html");});
					}else{
						alert(result);
					}
					return false;
				}

			 });
			 return false; // cancel conventional submit
		 })
	
	}
	
	exports.borrow_check = function (){	
		var email_status = $("#email_status").val();
		
		$("#borrow_type1").click(function(){
			if(user_id==''){
				alert("您还没登录，请先登录");
				location.href="/?user&q=login";
			}else if(email_status!=1){
				alert("您还没激活邮箱,请先激活");
				location.href="/?user&q=code/approve/email";
			}else if($("#amount").val()==0){
				alert("信用额度为0,请先申请");
				location.href="/?user&q=code/borrow/loan&type=1";
			}else{
				location.href="/?user&q=code/borrow/loan_now&type=1";
			}			
		});
		$("#borrow_type2").click(function(){
			if(user_id==''){
				alert("您还没登录，请先登录");
				location.href="/?user&q=login";
			}else if(email_status!=1){
				alert("您还没激活邮箱,请先激活");
				location.href="/?user&q=code/approve/email";
			}else if($("#vouch_amount").val()==0){
				alert("担保额度为0,请先申请");
				location.href="/?user&q=code/borrow/loan&type=2";
			}else{
				location.href="/?user&q=code/borrow/loan_now&type=2";
			}			
		});
		$("#borrow_type3").click(function(){
			if(user_id==''){
				alert("您还没登录，请先登录");
				location.href="/?user&q=login";
			}else if(email_status!=1){
				alert("您还没激活邮箱,请先激活");
				location.href="/?user&q=code/approve/email";
			}else if($("#diya_amount").val()==0){
				alert("抵押额度为0,请先申请");
				location.href="/?user&q=code/borrow/loan&type=3";
			}else{
				location.href="/?user&q=code/borrow/loan_now&type=3";
			}			
		});
		$("#borrow_type4").click(function(){
			if(user_id==''){
				alert("您还没登录，请先登录");
				location.href="/?user&q=login";
			}else if(email_status!=1){
				alert("您还没激活邮箱,请先激活");
				location.href="/?user&q=code/approve/email";
			}else{
				location.href="/?user&q=code/borrow/loan_now&type=4";
			}
			
		});		
		$("#borrow_type5").click(function(){
			if(user_id==''){
				alert("您还没登录，请先登录");
				location.href="/?user&q=login";
			}else if(email_status!=1){
				alert("您还没激活邮箱,请先激活");
				location.href="/?user&q=code/approve/email";
			}else if($("#worth_amount").val()==0){
				alert("净值额度为0,请先充值");
				location.href="/?user&q=code/account/recharge_new";
			}else{
				location.href="/?user&q=code/borrow/loan_now&type=5";
			}			
		});
		$("#borrow_type6").click(function(){
			if(user_id==''){
				alert("您还没登录，请先登录");
				location.href="/?user&q=login";
			}else if(email_status!=1){
				alert("您还没激活邮箱,请先激活");
				location.href="/?user&q=code/approve/email";
			}else if($("#amount").val()==0){
				alert("信用额度为0,请先申请");
				location.href="/?user&q=code/borrow/loan&type=6";
			}else{
				location.href="/?user&q=code/borrow/loan_now&type=6";
			}			
		});	
	}
		
	exports.amount = function (){
		$("#borrow_amount_form").live("submit",function(){
			require("submitform");	
   			$("#borrow_amount_form").ajaxSubmit({
				 success: function (result, status) {
					if(result==1){
						deayou.use("header",function(e){e.ajaxYes("申请成功，请等待审核","/?user&q=code/borrow/amount");});
					}else{
						deayou.use("header",function(e){e.ajaxError("您已提交了申请，请等待审核","false");});
					}
					return false;
				}

			 });
			 return false; // cancel conventional submit
		 })
	
	}
	
	
	exports.loan = function (){
		if (user_id==""){
			deayou.use("header",function(e){e.ajaxError("你还未登陆，请先登陆","/?user&q=login");});
		}else{
			if (realname_status==0){
				deayou.use("header",function(e){e.ajaxError("你还未通过实名认证，还不能申请借款","/?user&q=code/approve/realname");});
			}else if (amount_use<500){
				deayou.use("header",function(e){e.ajaxError("您的可用额度低于500元，请先申请额度","/?user&q=code/borrow/amount");});
			}else{
				$("#loan_form").live("submit",function(){
					require("submitform");	
					$("#loan_form").ajaxSubmit({
						 success: function (result, status) {
							if(result==1){
								deayou.use("header",function(e){e.ajaxYes("投标成功","/index.php?user&q=code/borrow/publish");});
							}else{
								deayou.use("header",function(e){e.ajaxError(result,"false");});
							}
							return false;
						}
		
					 });
					 return false; 
				 })
			}			
		}
	}
	exports.borrow_content = function (borrow_status_nid,borrow_nid,borrow_userid){

    $(".borrow_type_class").click(function(){
        if (borrow_status_nid=="loan" && user_id!=borrow_userid){
          location.href = "/?user&q=code/borrow/tender&p=invest&borrow_nid="+borrow_nid;
				
        }
    })
    
			//if (parseFloat(account)>parseFloat(borrow_account_yes)){
				//$.get("/?user&q=code/borrow/loan_check_realname", function(result){
				//	if (result==1){
						//$(".borrow_tender_type").attr("href","/?user&q=code/borrow/loan_tender&borrow_nid="+borrow_nid);
				//	}else{					
					//	$(".borrow_tender_type").live("click",function(){
					//	$(".borrow_tender_type").attr("href","javascript:void(0)");
						if(user_id==''){
							alert("您还没有登录，请先登录");
							location.href="/?user&q=login";
						//}else if($("#email_status").val()!=1){
							//alert("您还没有激活，请先激活邮箱");
							//slocation.href="/?user&q=code/approve/email";
						}else{
							//deayou.use("header",function(e){e.ajaxDialog("填写实名信息","/index.php?user&q=code/borrow/realname&borrow_nid="+borrow_nid);});
						}	
				//	}
				  //});
				
		
	}
	
	exports.loan_tender = function (){
		$("#loan_tender_form").live("submit",function(){
			
			var max=$("#max").val();
			var money=$("#money").val();
			var min=$("#min").val();
			if ($("#money").val()==""){
				alert("投资金额不能为空");
				return false;					
			}else if (parseInt(money)%50!=0 || parseInt(money)==0){
				alert("投资金额需为50的倍数");
				return false;				
			}else if (parseInt(max)<parseInt(money) && max>0){
				alert("投资金额不能大于最大投标额");
				return false;				
			}else if (parseInt(min)>parseInt(money)){
				alert("投资金额不能小于最小投标额");
				return false;
            }else if ($("#password_status_id").val()=="yes" && $("#borrow_password").val()==""){
				alert("请输入借款的密码，此密码请跟借款人索要。");
				return false;
			}else if ($("#paypassword").val()==""){
				alert("支付密码不能为空。");
				return false;
			}else if ($("#valicode").val()==""){
				alert("验证码不能为空。");
				return false;
			
			}else{					
				$("#loan_tender_form").ajaxSubmit({
					 success: function (result) {
						if(result==1){
							alert("投标成功");
							location.href = "/index.php?user&q=code/borrow/tender&p=now";
                            return false;	
						}else{
							alert(result);						
						}
						return false;
					}
	
				 });
				 return false; 	
			}
			
			return false;
		 })
	}
});
