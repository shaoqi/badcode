var con_id = Array();
function checkFormAll(form) {	
	if(form.allcheck.checked==true){
		con_id.length=0;
	}
	for (var i=1;i<form.elements.length;i++)    {
		 if(form.elements[i].type=="checkbox"){ 
            e=form.elements[i]; 
            e.checked=(form.allcheck.checked)?true:false; 
			if(form.allcheck.checked==true){
				con_id[con_id.length] = e.value;
			}else{
				con_id.length=0;
			}
        } 
	}
}
function checkFormOne(val){
	if(con_id.chec(val)){
		con_id.chec(val);
	}else {
		con_id[con_id.length] = val;
	}
}
function goUrl(url,str){
	if (!str){
		window.location.href = url;	
	}else{
		var id = con_id.join();
		if (id!=""){
			window.location.href = url+con_id.join();
		}
	}
}

function  uploadImg(id){
	window.showModalDialog("/plugins/index.php?q=uploadimg&id="+id, window, "dialogWidth: 400px; dialogHeight: 230px; help: no; scroll: no; status: no");
}
function  uploadAnnex(id){
	window.showModalDialog("/plugins/?q=uploadannex&id="+id, window, "dialogWidth: 400px; dialogHeight: 230px; help: no; scroll: no; status: no");
}
Array.prototype.chec = function(find)	{
	for (i=0; i<this.length; i++)
	{
			if (this[i].toString() == find.toString())
			{
					this.splice(i, 1);
					return true;
			}
	}
	return false;
}
function change_tr(siteid,pid){
	var imgopen_id = document.getElementById("imgopen_"+siteid);
	var imgclose_id = document.getElementById("imgclose_"+siteid);
	if (imgopen_id.style.display==""){ 
		imgopen_id.style.display = "none";
		imgclose_id.style.display = "";
	}
	else{ 
		imgopen_id.style.display = "";
		imgclose_id.style.display = "none";
	}
	var arrAll=document.getElementsByTagName("tr"); //获取所有的tr
	for(i=2;i <arrAll.length-1;i++) {   
		var strId = arrAll[i].id;
		var _strId=strId.split("_"); 
		//alert(strId);
		 if (_strId[1] == siteid){
			if (imgopen_id.style.display=="none"){
				document.getElementById(strId).style.display = "";
			}else{
				document.getElementById(strId).style.display = "none";
			}
			
			cha(_strId[2],imgopen_id.style.display);
		 } 
	}  
}
function cha(siteid,dis){	
	var aa = document.getElementById("ppd_"+siteid);
	if (aa.value==1){
		var imgopen_id = document.getElementById("imgopen_"+siteid);
		var imgclose_id = document.getElementById("imgclose_"+siteid);
		if (dis=="none"){ 
			imgopen_id.style.display = "none";
			imgclose_id.style.display = "";
		}
		else{ 
			imgopen_id.style.display = "";
			imgclose_id.style.display = "none";
		}
		var arrAll=document.getElementsByTagName("tr"); 
		for(j=2;j <arrAll.length-1;j++) {   
			var strId = arrAll[j].id;
			var _strId=strId.split("_"); 
			 if (_strId[1] == siteid){
				if (dis=="none"){
					document.getElementById(strId).style.display = "";
				}else{
					document.getElementById(strId).style.display = "none";
				}
				cha(_strId[2],dis);	
			 }
			 
		}  
	}
}

function jump_url(){
	if (document.getElementById('jump_url').style.display == ""){
	document.getElementById('jump_url').style.display = "none";
	}else{
	document.getElementById('jump_url').style.display = "";
	}
}
function fillCheckBox (fill_value, check_box) {

    fill_arr = fill_value.split(',');
    cnt = check_box.length;
    for(i=0;i<cnt;i++) {
        chk = check_box[i];

        if (fill_arr.join().indexOf(chk.value) >= 0) {
            chk.checked = 'true';
        }
    }
}

function change_menu(id,aid){
	var id = document.getElementById(id);
	if (id.style.display=="none"){
		aid.innerHTML = "+";
		id.style.display=""	;
	}else{
		id.style.display="none";
		aid.innerHTML = "-";
	}
}
function change_display(id,aid){
	var id = document.getElementById(id);
	if (aid=='show'){
		id.style.display = ""	;
	}else if (aid=='hide'){
		id.style.display="none"	;
	}else{
		if (id.style.display=="none"){
			id.style.display=""	;
		}else{
			id.style.display="none";
		}
	}
}

//yyyy-MM-dd HH:mm:ss
function change_picktime(date){
		if (date){
		WdatePicker({dateFmt:''+date+''});
		}else{
			WdatePicker();
		}
}	

function change_menu_tab(id){
	var id = "#"+id;
	$(id+" a").click(
		function (){
			$(id+" a").removeClass('current');
			$(this).addClass('current');
			var tab = id+"_tab";
			var menu = id+"_"+$(this).attr('tab');
			$(tab+" > div").hide();
			$(menu).show();
		}
	)
}
function on_submit(path,id){
	$('#type').val(id);
	 $('#form1').action=path;
	 $('#form1').submit();
}
function updateavatar() {
	history.go(0);
}