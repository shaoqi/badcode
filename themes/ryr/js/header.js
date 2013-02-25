define(function(require, exports, module) {
	
	
	//登录窗口弹出
	$(".user_login").live("click", function(){
		$(".user_login").attr("href","javascript:void(0)");
		exports.ajaxLogin();
	});
	
	exports.ajaxLogin = function(){
		$.ajax({
			url:'/?user&q=login&type=ajax',
			ifModified:true,
			success:function(data){
				$(".windows_div_bg").remove();	
				$("#popDiv").remove()
				$(".back_box").after(data);
				$(".pop_div_close a").attr("href","javascript:void(0)");
				$(".pop_div_close").live("click", function(){
					$("#user_login").remove();
					location.href='/?user&q=login';
					$("#login").attr("href","/?user&q=login");
				})
				$("#keywords").focus(function(){
					if ($(this).val()==""){
						
					}else{
						$(this).val("");
						$(this).css("class","login_text1");
					}
					
				});
				$("#login_text").attr("type","password");
				$("#login_form").submit(function(){
				if($("#cookie_status").attr("checked")=="checked"){
						cookie_status=1;
					}else{
						cookie_status = 0;
					}
					$.ajax({
						type: "POST",
						dataType: "text",
						url: '/?user&q=login',
						data: "&ajax=1&keywords=" + $("#keywords").val()+"&password="+$("#password").val()+"&cookie_status="+cookie_status,
						success: function(data) {
							if (data=="登录成功。"){
								location.href='/';
							}else{
								$('#login_error').html(data);
							}
						}
					});
					return false;
				});
			},
			cache:true
		});		
	}
	
	exports.ajaxYes = function(content,url){
		$('.windows_div_bg').remove();	
		$('.deayou_ajax_dialog').remove();
		var data= ' <div id="deayou_ajax_dialog"><div class="windows_div_bg" style="display:block" ></div>	 <div class="pop_div3 warning_bor111"> <span class="w_success">'+content+'</span><div></div';
		$(".back_box").after(data);
		if (url==""){
		setTimeout('$("#deayou_ajax_dialog").remove()',1000);
		}else{
			setTimeout("location.href ='"+url+"'",1000);
			
		}
	}
	
	exports.ajaxInfor = function(data){
		
		$(".user_avatar").hover(function() {
				$('.person_infor_bor').remove();
				var ptop = $(window).scrollTop(); 
			   var pleft = $(window).scrollLeft(); 
				var mtop = $(this).offset().top;
				
				var mleft = $(this).offset().left+3;
				var _userid = $(this).attr('data-user');
				var _data = '<div class="person_infor_bor" style="z-index:10000;position:absolute;left:'+mleft+'px;top:'+mtop+'px"><div class="person_infor"><div class="right_arrow11"></div><div class="per_in_view in_right11" ><div style="text-align:center"><br><br>正在加载中<br><br><br></div></div></div></div>';
				$('.back_box').fadeIn("slow").show(1000).after(_data);
				$.ajax({
						type: "post",
						dataType: "text",
						url: '/?code&q=users&p=getone',
						data: "&user_id=" + _userid,
						success: function(obj) {
							if (obj==""){
							}else{
								var _obj = eval("("+obj+")");
								var __data = '<div class="per_h_i" data-username="'+decodeURI(_obj.username)+'">'+
												   ' <div class="per_in_user"><img src="'+_obj.user_avatar+'" /></div>'+
													'<div class="per_in_right">'+
											         ' <div><a href="/user/'+_userid+'">'+decodeURI(_obj.username)+'</a> <img src="/themes/xiayizhan/images/v.gif" /> <img src="/themes/xiayizhan/images/lv4.gif" /></div>'+
													 ' <div><span class="ico_'+decodeURI(_obj._sex)+'">'+decodeURI(_obj.sex)+'</span>&nbsp;&nbsp;&nbsp;<span class="yinbi" title="印币">1234</span><a href="#">初级驴友</a></div>'+
													'  <div>粉丝 <a href="#">52万</a>&nbsp;&nbsp;活动 <a href="#">3258</a>&nbsp;&nbsp;分享 <a href="/user/'+_obj.user_id+'/share">'+_obj.share_num+'</a></div>'+
												'	</div>'+
												'</div>'+
												'<div class="per_infor"><span>简介：</span>'+decodeURI(_obj.intro)+'</div>'+
												'<div class="per_message">'+
												     '<span><a href="javascript:;" class="users_message_send">给TA发消息</a></span>'+
													 '<h1><a href="#" class="guanzhu">+加关注</a></h1>'+
												'</div>';
								$(".per_in_view").html(__data);
								var username = $('.per_h_i').attr('data-username');
								$(".users_message_send").live("click",function(){	
									$(".person_infor_bor").remove();
									if (user_id==""){
										deayou.use("header",function(e){e.ajaxLogin();});
										return "";
									}
									exports.ajaxMessage(username);
								})
								$(".guanzhu").live("click",function(){	
									if (user_id==""){
										$(".person_infor_bor").remove();
										deayou.use("header",function(e){e.ajaxLogin();});
										return "";
									}
									$(this).attr("href","javascript:;")
									var mtop = $(this).offset().top+30;
									var mleft =$(this).offset().left-50;
									 $.ajax({
											type:'post',
											url:'/?user&q=code/fans/add_ajax',
											data:'&user_id='+_userid,
											success:function(data){
												if (data==1){
													deayou.use("header",function(e){e.ajaxMsg("关注成功",mtop,mleft);});
												}else{
													deayou.use("header",function(e){e.ajaxMsg("已关注",mtop,mleft);});
												}
												
											},
											cache:false
										});
										
														   
																   
								})
								$(".person_infor_bor").hover(function() {
									$(".person_infor_bor").show();
								 },function(){
									 $(".person_infor_bor").remove();
								 })
							}
						}
					});
				
			}, function() {
				$(".person_infor_bor").hide();
			});
		
		
	}
	
	exports.ajaxConfirm = function(data,url,ret){
		
		var _data = ' <div class="windows_div_bg" style="display:block" ></div>'+
	' <div class="pop_div4 warning_bor222">'+
      '  <div class="p_w_tit">'+
		 '       <h1>提示</h1>'+
		'     <span><a href="#"></a></span>'+
	  '  </div>'+
	  '  <div class="p_w_content">'+
	   '      <h1>'+data+'</h1>'+
		' 	<span><input name="" type="button" value="确认" class="btn_ok" />&nbsp;&nbsp;<input name="" type="button" value="取消" class="btn_cc"/></span>'+
	  '  </div>'+
	'  </div>';
		$('.back_box').fadeIn("slow").show(1000).after(_data);
		$(".btn_ok").live("click",function(){	
										   if (url=="false"){
											   location.href=ret;	
										   }else{
				$.get(url,'',function(data){
								   location.href=ret;	
					  });
										   }
										  
		})
		$(".btn_cc,.p_w_tit span a").live("click",function(){
			$('.windows_div_bg').remove();	
			$('.pop_div4').remove();					  
		})
	}

	
	
	exports.ajaxDialog = function(title,url,ret){
		$('.windows_div_bg').remove();	
		$('.deayou_ajax_dialog_contents').html(" ");
			$('.deayou_ajax_dialog').remove();		
		var _data = '<div class="windows_div_bg" style="display:block" ></div>'+
	' <div class="deayou_ajax_dialog" id="deayou_ajax_dialog"><div class="deayou_ajax_dialog_content">'+
      '  <div class="deayou_ajax_dialog_title">'+
		 '       <h1>'+title+'</h1>'+
		'     <span><a href="javascript:;"></a></span>'+
	  '  </div>'+
	  '  <div class="deayou_ajax_dialog_contents">'+
	  '  </div>'+
	'  </div></div>';
		$('.back_box').fadeIn("slow").show(1000).after(_data);
		$.ajax({
		  type: "GET",
		  url: url,
		  dataType: "html",
		  cache:false,
		  success: function(data){
						 $('.deayou_ajax_dialog_contents').html(data);	
						 var		owidth = document.body.offsetWidth 
		var ptop = $(window).scrollTop(); 
		   var pleft = $(window).scrollLeft(); 
			var mtop = $(".deayou_ajax_dialog").offset().top;
			var mleft = $(".deayou_ajax_dialog").offset().left;
			var awidth =$(".deayou_ajax_dialog_contents").width();
			var lef = (owidth-awidth)/2;
				$("#deayou_ajax_dialog").attr("margin-left","10px");
			  }
		  });
		$(".deayou_ajax_dialog_title span a").live("click",function(){
			$('.windows_div_bg').remove();	
			$('.deayou_ajax_dialog').remove();					  
		})
	}

	
	
	
	exports.ajaxError = function(data,ret){
		
		var _data = ' <div class="windows_div_bg" style="display:block" ></div>'+
	'  <div class="deayou_ajax_dialog"><div class=deayou_ajax_dialog_boolen>'+
          '<span class="deayou_ajax_false">'+data+'</span>'+
	 '</div></div>';
	  time = 2000;
		$('.back_box').fadeIn("slow").show(0).after(_data);
		if (ret!="false"){
			setTimeout("location.href ='"+ret+"'",time);
		}else{
		$('.windows_div_bg').remove();	
			$('.deayou_ajax_dialog').remove();	
		}
	}
	
	exports.ajaxMsg = function(content,mtop,mleft){
		$(".sm_warning").remove();
		var _data = '<div class="sm_warning" style="z-index:10000;left:'+mleft+'px;top:'+mtop+'px"><span class="sm_succ">'+content+'</span></div>';	
		
		$('.back_box').fadeIn("slow").show(1000).after(_data);
		setTimeout('$(".sm_warning").fadeOut("slow").delay(1000).hide(1000)',1000)
		
	}
	
	
	
	exports.ajaxMessage = function(username){
		$('.windows_div_bg').remove();	
		$('.pop_div1').remove();	
		var _data = ' <div class="windows_div_bg"  style="display:block" ></div><div class="pop_div1 site_mail" id="send_message" style="display:block">'+
	     ' <div class="pop_div_rel"><div class="pop_div_close"><a  href="javascript:;" title="关闭窗口"></a></div></div>'+
		  '<div class="site_mail_view"><form id="message_form" action="/?user&q=code/users/add_messages" method=post>'+
		    '   <h1>发送消息给 <a href="#">'+username+'</a><input type="hidden" value="'+username+'" name="username"></h1>'+
			 '  <span><textarea name="contents" cols="" rows="" class="site_mail_text"></textarea></span>'+
			 '  <em><input name="" type="submit" value="发送" class="btn"/></form></em>'+
		 ' </div>'+
	 '</div>';	
		
		$('.back_box').after(_data);
		$(".pop_div_close a").live("click",function(){
			$('.windows_div_bg').remove();	
			$('.pop_div1').remove();					  
		})
		$("#message_form").submit(function(){
			require("submitform");				
   			$("#message_form").ajaxSubmit({
                     success: function (result, status) {
						  $('.windows_div_bg').remove();	
						$('.pop_div1').remove();
					 	if (result>0){
							
							exports.ajaxYes("短消息发送成功");	
							
							
						 }else{
							exports.ajaxYes(result);
						 }
						
                    }

                 });
			 return false; // cancel conventional submit
		 })
	}
	
		exports.ajaxDrap = function(obj){
            
        }
		
		exports.goUrl = function(url){
            location.href=url;
        }
});

