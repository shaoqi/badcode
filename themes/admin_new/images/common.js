/*common js    added by c1o*/

//字符串
String.prototype.bytes = function(){
	return this.replace(/[^\\x00-\\xff]/g, "  ").length;
}

function CheckAll(c,s)
{
	s = (typeof s == "boolean") ? s : true;
	$("input[name='"+c+"']").each(function(){$(this).attr("checked",s);});
}

function getCheckBoxValues(c)
{
	var result ='';
	$("input[name='"+c+"']").each(function(){result += $(this).attr("checked") ? $(this).val() + "," : "";});
	return result.substring(0, result.length-1);
}

function ppup(msg,c,fn,img,t,y,n) 
{
	msg = msg||'';img = img||'/images/cbox_a.gif';y = y||'确定';n = n||'取消';t = t||'提示';
	if (c=='confirm') {
		bt = '<input class="cbox_close cconfirmY" type="button" value="'+y+'" />&nbsp;&nbsp;<input class="cbox_close cconfirmN" type="button" value="'+n+'" />';
	} else {
		bt = '<input class="cbox_close calertC" type="button" value="'+y+'" />';
	}
	$.cbox('<div style="top:20px;left:10px;position:relative;float:left;"><image align="absmiddle" src="'+img+'" /></div><div style="left:30px;top:25px;position:relative;float:left;height:60px;width:200px;font-size:14px;">'+msg+'</div><div style="right:18px;bottom:18px;position:absolute;padding:0;float:right">'+bt+'</div>',{tt:t});
	if (fn) {
		$('.cconfirmY').click(function(){fn(true)});
		$('.cconfirmN').click(function(){fn(false)});
		$('.calertC').click(function(){fn(true)});
	}
}

function calert(msg,fn,t,y,n,img)
{
	img = img||'/images/cbox_a.gif';
	ppup(msg,'alert',fn,img,t,y,n);
}

function cconfirm(msg,fn,t,y,n,img)
{
	img = img||'/images/cbox_c.gif';
	ppup(msg,'confirm',fn,img,t,y,n);
}

function redirect(to, ttl)
{
	var a = '',b = 'window.location';
	if(to === 0 || to === '0') a = b + '.reload()';
	else if(to === -1 || to === '-1') a = 'history.go(-1)';
	else if(to) a = b + '.href="' + to + '"';
	setTimeout(a,ttl);
}

function aftsbmt(j)
{
	if(j.s) {
		calert(j.m);
		redirect(j.u, j.ttl)
	} else {
		calert(j.m)
	}
}