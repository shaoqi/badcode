var xmlHttp;
function createXMLHttpRequest()
{
　if (window.ActiveXObject) {
		xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");}
	else if (window.XMLHttpRequest) {
		xmlHttp =  new XMLHttpRequest();}
}

function getAjax(url,div) {
	createXMLHttpRequest();
	xmlHttp.onreadystatechange = function() {;
		if (xmlHttp.readyState == 4) {	
			if (xmlHttp.status == 200) {
			document.getElementById(div).innerHTML = xmlHttp.responseText;
			}else{
				document.getElementById(div).innerHTML = "...";
			}
		}
	}
	xmlHttp.open("GET",url, true); 
	xmlHttp.send(null); 
}
function getAjaxNone(url) {
	createXMLHttpRequest();
	xmlHttp.onreadystatechange = function() {;
	if (xmlHttp.readyState == 4) {	
		if (xmlHttp.status == 200) {
		return true;
			}
		}
	}
	xmlHttp.open("GET",url, true); 
	xmlHttp.send(null); 
}	
function getAjaxAlert(url,str) {
	alert(str);
	createXMLHttpRequest();	
	xmlHttp.onreadystatechange = function() {;
		if (xmlHttp.readyState == 4) {	
			if (xmlHttp.status == 200) {
			  alert(str);
			}
		}
	}
	xmlHttp.open("GET",url, true); 
	xmlHttp.send(null); 
}		
function getAjaxTable(url) {
	createXMLHttpRequest();
	if (document.getElementById('div_id')){
		cle_div();
	}
	createTabel(1,200,300);
	xmlHttp.onreadystatechange = function() {;
	if (xmlHttp.readyState == 4) {
	if (xmlHttp.status == 200) {
		document.getElementById("ajax_div").innerHTML =  xmlHttp.responseText;
		}
	}
	}
	xmlHttp.open("GET",url, true); 
	xmlHttp.send(null); 
}
function getAjaxTable2(url,top,left) {
	createXMLHttpRequest();
	if (document.getElementById('div_id')){
		cle_div();
	}
	createTabel(top,left);
	xmlHttp.onreadystatechange = function() {;
	if (xmlHttp.readyState == 4) {
	if (xmlHttp.status == 200) {
		document.getElementById("ajax_div").innerHTML +=  xmlHttp.responseText;
		}
	}
	}
	xmlHttp.open("GET",url, true); 
	xmlHttp.send(null); 
}
function createTabel(atop,aleft){
	var htmlDiv = "";
	var  div   =   document.createElement("DIV"); //创建div层
	div.setAttribute( "id" , "div_id"); //层的唯一id
	sub_id = "sub_div"; //
	div.style.width   =   300; //层宽度
	div.style.top   =  getScrollTop() + (document.body.scrollHeight-200)/2 -60; //层宽度
	div.style.left   =   (document.body.scrollWidth-200)/2; //层高度
	div.style.position = "absolute";
	div.style.zIndex= 100;
	div.innerHTML =  '<div '
	+ "style='cursor:move;height:40px;padding:10px;width:300px' "
	+ " onmousedown='startDrag(this)' "
	+ " onmouseup='stopDrag(this)' "
	+ " onmousemove='drag(this)' "
	+ " align='left' "
	+ '><strong style="font-size:13px"><a href="#" onclick="cle_div()">[关闭]</a></strong></div> <div  '
	+ 'id="sub_div"'
	+ " style='"
	+ "width:100%;"
	+ "height:auto;"
	+ "border:2px solid red;" 
	+ "background-color:#fff9cc;"
	+ "position:absolute;top:0px; left:0px; "
	+ "z-index:-1;"
	+ "' "
	
	+ ">"
	+ '<div style="height:23px;background-color:#FF3366;margin:2px;"></div><div id="ajax_div" style="font-size:13px;margin:5px;" align="left">'
	
	+'</div></div>'
	+ '<iframe src="javascript:false" style="position:absolute; visibility:inherit; top:0px; left:0px; width:320px; height:330px; z-index:-3;filter=progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0);"></iframe>'
	//div.attachEvent('onmousedown', new Function("getFocus('sub_div')"));

	//div.addEventListener("onmousedown",new Function("getFocus('"+sub_id+"')")); //鼠标拖动函数
	document.body.appendChild(div);
	var   div_sub   =   document.createElement("DIV"); //创建div层
	div_sub.style.zIndex= 100;
	div_sub.style.position = "absolute";
	document.body.appendChild(div_sub);
	//div_position("div_" + uid);
}
///////////////////////////////////////////////该部分为层拖动代码///////////////////////////////////////
//可以打包为js文件;
var x0=0,y0=0,x1=0,y1=0;
var offx=6,offy=6; 
var moveable=false;
var hover='orange',normal='slategray';//color;
var index=100;//z-index; 
//开始拖动;
function startDrag(obj)
{
	//锁定标题栏;
	obj.setCapture();
	//定义对象;
	var win = obj.parentNode;
	var sha = win.nextSibling;
	//记录鼠标和层位置;
	x0 = event.clientX;
	y0 = event.clientY;
	x1 = parseInt(win.style.left);
	y1 = parseInt(win.style.top);
	sha.style.left = x1 + offx;
	sha.style.top = y1 + offy;
	moveable = true;
}
//拖动;
function drag(obj)
{
	var win = obj.parentNode;
	var sha = win.nextSibling;
	if(moveable)
	{
		win.style.left = x1 + event.clientX - x0;
		win.style.top = y1 + event.clientY - y0;
		sha.style.left = parseInt(win.style.left) + offx;
		sha.style.top = parseInt(win.style.top) + offy;
	}
}
//停止拖动;
function stopDrag(obj)
{
	var win = obj.parentNode;
	var sha = win.nextSibling;
	sha.style.left = obj.parentNode.style.left;
	sha.style.top = obj.parentNode.style.top;
	//放开标题栏;
	obj.releaseCapture();
	moveable = false;
}

function min(obj_id)
{
	var obj = document.getElementById("corp_card_" +obj_id);
	var win = obj.parentNode.parentNode;
	var sha = win.nextSibling;
	var tit = obj.parentNode;
	var msg = tit.nextSibling;
	var flg = msg.style.display;

	if(flg == "none")
	{
		win.style.height = parseInt(msg.style.height) + parseInt(tit.style.height) + 2*2;
		sha.style.height = win.style.height;
		msg.style.display = "block";
		obj.innerHTML = "0";
	}
	else
	{
		win.style.height = parseInt(tit.style.height) + 2*2;
		sha.style.height = win.style.height;
		obj.innerHTML = "2";
		msg.style.display = "none";
	}
}
function cle_div() //关闭层，要绝对的删除
{
	var obj = document.getElementById("div_id");
	var sha = obj.nextSibling;
	var obj_sub = document.getElementById("div_sub");
	obj.parentNode.removeChild(sha);
	obj.parentNode.removeChild(obj);
}

//获得焦点;
function getFocus(e)
{	
	obj = document.getElementById(e);
	index = index + 2;
	var idx = index;
	obj.style.zIndex=idx;
}
function getScrollTop() {  //获取滚动条的高度  
    var scrollPos = 0;     
    if (typeof window.pageYOffset != 'undefined') {     
       scrollPos = window.pageYOffset;     
    }     
    else if (typeof window.document.compatMode != 'undefined' &&     
       window.document.compatMode != 'BackCompat') {     
       scrollPos = window.document.documentElement.scrollTop;     
    }     
    else if (typeof window.document.body != 'undefined') {     
       scrollPos = window.document.body.scrollTop;     
    }     
    return scrollPos;    
} 

