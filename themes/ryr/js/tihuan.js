window.onload = function(){
	var newHTML=document.documentElement.innerHTML;
	alert(newHTML);
	var reg=new RegExp("融资","g");
	newHTML = replace(reg,'借款');
	document.write(newHTML);
}