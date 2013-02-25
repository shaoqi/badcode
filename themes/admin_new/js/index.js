
var agt = navigator.userAgent.toLowerCase();

var is_ie = ((agt.indexOf('msie')!=-1) && (agt.indexOf('opera')==-1));
var is_gecko= (navigator.product == "Gecko");
var is_ns = (document.layers);
var is_w3 = (document.getElementById && !is_ie);
var sch = 0;


var guides = {'common' : {'common' : {}},
	'menu_1' : {'menu_1' : {
			'user_1' : ['所有用户',admin_url+'&q=module/user'],
			'user_2' : ['用户类型',admin_url+'&q=module/user/type']
			}
		},
	'menu_2' : {'menu_2' : {
			'borrow_1' : ['所有借款',admin_url+'&q=module/borrow&title=所有借款'],
			'borrow_2' : ['待审核借款',admin_url+'&q=module/borrow&status=0&title=待审核借款'],
			'borrow_3' : ['正在招标的借款',admin_url+'&q=module/borrow&status=1&title=正在招标的借款'],
			'borrow_4' : ['满标审核',admin_url+'&q=module/borrow/full&status=1&title=满标审核'],
			'borrow_5' : ['额度管理',admin_url+'&q=module/borrow/amount&title=额度管理'],
			'borrow_6' : ['逾期还款',admin_url+'&q=module/borrow/late&title=逾期还款'],
			'borrow_7' : ['已还款',admin_url+'&q=module/borrow&repayment&repay_status=1&title=已还款'],
			'borrow_8' : ['未还款',admin_url+'&q=module/borrow&repay_status=0&title=未还款'],
			'borrow_9' : ['满标审核通过',admin_url+'&q=module/borrow/full&status=3&title=满标审核通过'],
			'borrow_10' : ['满标审核未通过',admin_url+'&q=module/borrow/full&status=4&title=满标审核未通过'],
			'borrow_11' : ['流标',admin_url+'&q=module/borrow/liubiao&title=流标']
			}
		},
	'menu_3' : {'menu_3' : {
			'setmember' : ['会员管理','admin.php?action=member&job=set&cat=0'],
			'ckmember' : ['经纪人审核','admin.php?action=member&job=ck&cat=0'],
			'addmember' : ['注册会员','admin.php?action=member&job=add'],
			'listagent' : ['中介管理','admin.php?action=agent&job=list'],
			'listlog' : ['锁定记录','admin.php?action=member&job=llog'],
			'ckivt' : ['邀请审核','admin.php?action=member&job=ckivt'],
			'xqzj' : ['小区专家','admin.php?action=member&job=xqzj&c=1'],
			'pos_tj' : ['首页推荐','admin.php?action=member&job=pos_tj']
			}
		},
	'menu_4' : {'menu_4' : {
			'credit_1' : ['积分列表',admin_url+'&q=module/credit'],
			'credit_2' : [' 等级管理',admin_url+'&q=module/credit/rank'],
			'credit_3' : ['积分类型列表',admin_url+'&q=module/credit/type'],
			'credit_4' : ['添加积分类型',admin_url+'&q=module/credit/type_new']
			}
		},
	'menu_5' : {'menu_5' : {
			'admin_1' : ['所有管理员',admin_url+'&q=module/manager'],
			'admin_2' : ['管理员类型',admin_url+'&q=module/manager/type'],
			'admin_3' : ['添加管理员',admin_url+'&q=module/manager/new']
			}
		},
	'menu_6' : {'menu_6' : {
			'setsite' : ['站点管理',admin_url+'&q=site/loop&a=loop'],
			'addsite' : ['创建站点',admin_url+'&q=site/new']
			}
		},
	'menu_7' : {'menu_7' : {
			'system_1' : ['系统参数',admin_url+'&q=system/info'],
			'system_0' : ['清空缓存',admin_url+'&q=system/clearcache'],
			'system_2' : ['图片水印',admin_url+'&q=system/watermark'],
			'system_3' : ['附近设置',admin_url+'&q=system/fujian'],
			'system_4' : ['邮箱设置',admin_url+'&q=system/email'],
			'system_5' : ['上传图片',admin_url+'&q=system/upfiles'],
			'system_6' : ['数据库备份',admin_url+'&q=system/dbbackup/back'],
			'system_7' : ['数据库还原',admin_url+'&q=system/dbbackup/revert']
			}
		},
	'menu_8' : {'menu_8' : {
			'listbusele' : ['短消息',admin_url+'&q=module/message'],
			}
		},
	'menu_9' : {'menu_9' : {
			'account_1' : ['帐户信息列表',admin_url+'&q=module/account'],
			'account_2' : ['申请提现',admin_url+'&q=module/account/cash'],
			'account_3' : ['提现成功',admin_url+'&q=module/account/cash&status=1'],
			'account_4' : ['提现失败',admin_url+'&q=module/account/cash&status=0'],
			'account_5' : ['充值记录',admin_url+'&q=module/account/recharge'],
			'account_6' : ['资金记录',admin_url+'&q=module/account/log'],
			'account_7' : ['添加充值',admin_url+'&q=module/account/recharge_new'],
			'account_7' : ['扣除费用',admin_url+'&q=module/account/deduct']
			}
		}
}
var titles = {'common' : '首页',
	'menu_1' : '用户管理',
	'menu_2' : '借款管理',
	'menu_3' : '会员',
	'menu_4' : '积分管理',
	'menu_5' : '管理员',
	'menu_6' : '站点管理',
	'menu_7' : '系统管理',
	'menu_8' : '扩展模块',
	'menu_9' : '资金管理'}
var cate   = 'common';
var action = '';
var type   = '';
document.getElementById('mainright');
function showguide(id){
	var obj = document.getElementById('showmenu');
	var guide = guides[id];
	var html  = '<dl>';
	for(i in guide){
		var subs = guide[i];
		html += '<dd>';
		for(j in subs){
			var sub = subs[j];
			html += '<a href="#" onclick="return initguide(\''+id+'\',\''+j+'\')">'+sub[0]+'</a>';
		}
		html += '</dd>';
	}
	obj.innerHTML = html + '</dl>';
	var obj1  = document.getElementById(id);
	var left  = findPosX(obj1) + getLeft();
	var top   = findPosY(obj1) + getTop() + 22;
	obj.style.display = "";
	obj.style.top	= top + 'px';
	obj.style.left	= left + 'px';
	addEvent(document,"mouseout",doc_mouseout);
}
function closeguide(){
	var obj = document.getElementById('showmenu');
	obj.style.display = "none";
	removeEvent(document,"mouseout",doc_mouseout);
}
function upleft(t){
	var obj  = document.getElementById('left');
	var objli = obj.getElementsByTagName('li');
	var obja = obj.getElementsByTagName('a');
	for(var i=0;i<obja.length;i++){
		objli[i].className = obja[i].id==t ? 'one' : '';
	}
}
var tree;
var isflag =0;
function showleft(id,t,url){
	cate = id;
	var obj = document.getElementById('left');
	
	var html = '';
	var guide = guides[id];
	url = typeof url != 'undefined' ? url : '';
	type = typeof t != 'undefined' ? t : '';
 
	for(i in guide){
		var subs = guide[i];
		html += '<h1>' + titles[i] + '</h1><div class="cc"></div><ul>';
		for(j in subs){
			var sub = subs[j];
			html += '<li><a id="'+j+'" href="#" onclick="return initguide(\''+id +'\',\''+j+'\')">'+sub[0]+'</a></li>';
			if(url==''){
				if(type == ''){
					url = sub[1];
					type = j;
				} else if(j == type){
					url = sub[1];
				}
				action = i;
			}
		}
		html += '</ul>';
 
		obj.innerHTML = html;
 
		upleft(type);
		if (is_ie && sch>0) sch = 0;
		parent.main.location = url;
		return false;
	}
}
function showtitle(){
	var obj = document.getElementById('guide');
	var guide = guides[cate];
	var html = '';
	if (cate && action && type) {
		
		if (cate=='common') {
			html += '<div class="wei fl"><a href="admin.php">首页</a> &raquo; <a href="#" onclick="return initguide(cate,type,\'admin.php?adminjob=admin\')">'+titles[action]+'</a> &raquo; <a href="#">'+guide[action][type][0]+'</a></div>';
		} else {
			html += '<div class="wei fl"><a href="admin.php">首页</a> &raquo; <a href="#" onclick="return initguide(\''+cate+'\')">'+titles[action]+'</a> &raquo; <a href="#" onclick="return initguide(\''+cate+'\',\''+type+'\')">'+guide[action][type][0]+'</a></div>';
		}
	}
	html += '<ul class="fr"><li class="home">用户名：'+admin_username+'</li><li><a class="s0" style="cursor:pointer" onclick="parent.main.location.reload();" title="刷新主页面">刷新</a></li><li><a class="s0" style="cursor:pointer" onclick="parent.main.history.go(-1);" title="后退到前一页">后退</a></li><li><a href="'+admin_url+'&q=logout">注销</a></li></ul>';
	
	obj.innerHTML = html;
}
function site_change(val) {
	location.href ="admin.php?action=site_change&siteid="+val;
}
 
function initguide(id,t,url){
	
	showleft(id,t,url);
	showtitle();
	return false;
}
function showmenu(){
		
	var obj = document.getElementById('menu');
	top.main.showselect('hidden');
	if(!IsElement('menubg')){
		var html = '<div><div><a style="cursor:pointer;" onclick="closemenu();" class="fr"><img src="/themes/admin/images/close.gif" /></a><h1>按"ESC"键关闭或开启此菜单</h1>';
 
		for(i in guides){
			if(i=='common') continue;
			var guide = guides[i];
			html += "<dl>";
			for(j in guide){
				html += "<dt><h3>" + titles[j] + "</h3></dt><dd>";
				var subs = guide[j];
				for(k in subs){
					var sub = subs[k];
					html += '<a href="#" onclick="return toguide(\''+i+'\',\''+k+'\')">'+sub[0]+'</a>';
				}
				html += "</dd>";
			}
			html += '</dl>';
		}
		html += '<div class="c"></div></div></div>';
		
		obj.innerHTML = html;
		
		var obj2 = document.createElement("div");
		obj2.id = "menubg";
		obj.parentNode.insertBefore(obj2,obj);
	} else {
		var obj2 = document.getElementById('menubg');
		obj2.style.display = "";
	}
	obj.style.display = "";
	addEvent(document,"mousedown",doc_mousedown);
}
function closemenu(){
	var obj = document.getElementById('menu');
	obj.style.display = "none";
	var obj2 = document.getElementById('menubg');
	obj2.style.display = "none";
	removeEvent(document,"mousedown",doc_mousedown);
	top.main.showselect('');
}
function toguide(id,t){
	closemenu();
	initguide(id,t);
	return false;
}
function doc_mousedown(e){
	var e = is_ie ? event: e;
	obj	= document.getElementById("menu");
	_x	= is_ie ? e.x : e.pageX;
	_y	= is_ie ? e.y + getTop() : e.pageY;
	_x1 = obj.offsetLeft;
	_x2 = obj.offsetLeft + obj.offsetWidth;
	_y1 = obj.offsetTop - 20;
	_y2 = obj.offsetTop + obj.offsetHeight;
 
	if(_x<_x1 || _x>_x2 || _y<_y1 || _y>_y2){
	   closemenu();
	}
}
function doc_mouseout(e){
	var e = is_ie ? event: e;
	obj	= document.getElementById("showmenu");
	_x	= is_ie ? e.x : e.pageX;
	_y	= is_ie ? e.y + getTop() : e.pageY;
	_x1 = obj.offsetLeft + 2;
	_x2 = obj.offsetLeft + obj.offsetWidth;
	_y1 = obj.offsetTop - 20;
	_y2 = obj.offsetTop + obj.offsetHeight;
 
	if(_x<_x1 || _x>_x2 || _y<_y1 || _y>_y2){
		closeguide();
	}
}
function IsElement(id){
	return document.getElementById(id)!=null ? true : false;
}
function addEvent(el,evname,func){
	if(is_ie){
		el.attachEvent("on" + evname,func);
	} else{
		el.addEventListener(evname,func,true);
	}
};
function removeEvent(el,evname,func){
	if(is_ie){
		el.detachEvent("on" + evname,func);
	} else{
		el.removeEventListener(evname,func,true);
	}
};
function findPosX(obj){
	var curleft = 0;
	if (obj.offsetParent){
		while (obj.offsetParent){
			curleft += obj.offsetLeft
			obj = obj.offsetParent;
		}
	}
	else if (obj.x)
		curleft += obj.x;
	return curleft - getLeft();
}
function findPosY(obj){
	var curtop = 0;
	if (obj.offsetParent){
		while (obj.offsetParent){
			curtop += obj.offsetTop
			obj = obj.offsetParent;
		}
	} else if (obj.y){
		curtop += obj.y;
	}
	return curtop - getTop();
}
function ietruebody(){
	return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body;
}
function getTop() {
	return typeof window.pageYOffset != 'undefined' ? window.pageYOffset:ietruebody().scrollTop;
}
function getLeft() {
	return (typeof window.pageXOffset != 'undefined' ? window.pageXOffset:ietruebody().scrollLeft)
}
function keyCodes(id,e){
	if (e.keyCode==27) {
		if (IsElement(id) && document.getElementById(id).style.display=='') {
			closemenu();
		} else {
			try{
				showmenu();
			} catch(e){}
		}
	} else if (is_ie && (e.keyCode==116 || (e.ctrlKey && e.keyCode==82))) {
		e.keyCode = 0;
		e.returnValue = false;
		parent.main.location.reload();
	}
	return false;
}
function PwFindInPage(){
	if (document.getElementById('schstring').value!='') {
		FindInPage(document.getElementById('schstring').value);
		document.getElementById('schbt').disabled = false;
	} else {
		document.getElementById('schbt').disabled = true;
	}
}
function FindInPage(str){
	if (!str) {
		alert('未找到指定内容');
		return false;
	}
	if (is_w3 || is_ns) {
		if (!parent.main.find(str)) {
			alert('到达页尾，从页首继续');
			while (1) {
				if (!parent.main.find(str,false,true)) break;
			}
			return false;
		}
	} else if (is_ie) {
		var found;
		var txt = parent.main.document.body.createTextRange();
		for (var i = 0; i <= sch && (found = txt.findText(str)) != false; i++) {
			txt.moveStart('character',1);
			txt.moveEnd('textedit');
		}
		if (found) {
			sch++;
			txt.moveStart('character',-1);
			txt.findText(str);
			try {
				txt.select();
				txt.scrollIntoView();
			} catch(e) {FindInPage(str);}
		} else {
			if (sch > 0) {
				sch = 0;
				alert('到达页尾，从页首继续');
				FindInPage(str);
			} else {
				alert('未找到指定内容');
			}
			return false;
		}
	}
	return true;
}
function getPageHeight(){
	if (self.innerHeight) {
		return self.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight) {
		return document.documentElement.clientHeight;
	} else if (document.body) {
		return document.body.clientHeight;
	}
}