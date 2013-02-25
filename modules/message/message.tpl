<ul class="nav3"> 
<li><a href="{$_A.query_url}" {if $_A.query_type==""}style="color:red"{/if}>发送记录</a></li> 
<li><a href="{$_A.query_url}/receive" {if $_A.query_type=="receive"}style="color:red"{/if}>接收记录</a></li> 
<li><a href="{$_A.query_url}/new" {if $_A.query_type=="new"}style="color:red"{/if}>发消息</a></li> 
</ul> 

{literal}
<script>
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

function on_submit(path,id){
	$('#type').val(id);
	 $('#form1').action=path;
	 $('#form1').submit();
}
</script>
{/literal}
{if $_A.query_type == "list"}

<div class="module_add">
	<div class="module_add">
	<div class="module_title"><strong>短消息发送记录列表</strong><span style="float:right">
		发送人：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>  <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()"></span></div>
	</div>
	<form action="" method="post" id="form1">
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td"><input type="checkbox" name="allcheck" onclick="checkFormAll(this.form)"/></td>
		<td width="" class="main_td">发件人</td>
		<td width="*" class="main_td">发送类型</td>
		<td width="*" class="main_td">标题</td>
		<td width="*" class="main_td">内容</td>
		<td width="*" class="main_td">发送时间</td>	
		<td width="*" class="main_td">状态</td>		
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="message" function="GetMessageList" var="loop" epage=20 page=request username=request  }
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center"><input type="checkbox" name="id[{$key}]" value="{$item.id}"/></td>
		<td class="main_td1" align="center">{$item.username|default:"系统"}</td>
		<td class="main_td1" align="center">{$item.type|in_array:"$_A.message_type"}</td>
		<td class="main_td1" align="center">{$item.name}</td>
		<td class="main_td1" align="center">{$item.contents}</td>
		<td class="main_td1" align="center">{$item.addtime|date_format}</td>
		<td class="main_td1" align="center">{if $item.status==1}已发送{elseif $item.status==2}未保存{else}草稿{/if}</td>
		<td class="main_td1" align="center">{if $item.status==0}<a href="#" onClick="javascript:if(confirm('确定是否立即发送')) location.href='{$_A.query_url_all}&send={$item.id}'">发送</a> / {/if}<a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url_all}&del={$item.id}&type={$item.type}'">删除</a></td>
	</tr>
	{/foreach}
	<tr>
			<td colspan="11" class="action">
			<div class="floatl"><input type="button"  value="删除" onclick="on_submit('{$_A.query_url}/list','delete')" /><input type="hidden" value="0" name="type" id="type" />
			<script>
	  var url = '{$_A.query_url_all}';
	    {literal}
	  	function sousuo(){
			var username = $("#username").val();
			location.href=url+"&username="+username;
		}
	  
	  </script>
	  {/literal}
			</div>
			<div class="floatr">
			</div>
			</td>
		</tr>
	<tr align="center">
		<td colspan="10" align="center"><div align="center">{$loop.pages|showpage}</div></td>
	</tr>
	{/list}
	
</table>
<!--菜单列表 结束-->
</form>

{elseif $_A.query_type == "new"}

<div class="module_add">
	
	<form action="{$_A.query_url_all}" method="post">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="id" value="{$_A.approve_result.id}" />修改短消息 （<a href="{$_A.query_url_all}">添加</a>）{else}发送短消息{/if}</strong></div>
	
	<div class="module_border">
		<div class="l">收件人 ：</div>
		<div class="c">
			<select name="type" id="type">
			<option value="all">所有人</option>
			<option value="users">多用户</option>
			<option value="user_type">用户类型</option>
			<option value="admin_type">管理类型</option>
			</select>
		</div>
	</div>
	{literal}
	<script>
	$("#type").change(function(){
		var checkValue=$("#type").val();
		if (checkValue=="users"){
			$("#receive_users").show();
			$("#receive_user_type").hide();
			$("#receive_admin_type").hide();
		}else if (checkValue=="user_type"){
			$("#receive_users").hide();
			$("#receive_user_type").show();
			$("#receive_admin_type").hide();
		}else if (checkValue=="admin_type"){
			$("#receive_users").hide();
			$("#receive_user_type").hide();
			$("#receive_admin_type").show();
		}else{
			$("#receive_users").hide();
			$("#receive_user_type").hide();
			$("#receive_admin_type").hide();
		}
	
	});
	
	</script>
	{/literal}
	
	
	<div class="module_border" id="receive_users" style="display:none">
		<div class="l">用户名：</div>
		<div class="c">
			<input type="text" name="receive_users" value=""/>多个请用英文,隔开，只判断存在的用户发送
		</div>
	</div>
	
	<div class="module_border"  id="receive_user_type" style="display:none">
		<div class="l">用户类型：</div>
		<div class="c">
			<select name="receive_user_type">
			{loop module="users" function="GetUsersTypeList" limit="all"}
			<option value="{$var.id}">{$var.name}</option>
			{/loop}
			</select>
		</div>
	</div>
	
	
	
	<div class="module_border"  id="receive_admin_type" style="display:none">
		<div class="l">管理类型：</div>
		<div class="c">
			<select name="receive_admin_type">
			{loop module="users" function="GetAdminTypeList" limit="all"}
			<option value="{$var.id}">{$var.name}</option>
			{/loop}
			</select>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">状态 ：</div>
		<div class="c">
			{input value="1|立即发送,0|保存草稿" name="status" }
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">标 题 ：</div>
		<div class="c">
			<input type="text" name="name" value="{$_A.message_result.name}"/>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">内容 ：</div>
		<div class="c">
			<textarea name="contents" rows="5" cols="30">{$_A.message_result.contents}</textarea>
		</div>
	</div>
	
	<div class="module_border" >
		<div class="l">验 证 码 ：</div>
		<div class="c">
			<input name="valicode" type="text" size="11" maxlength="4"  />
		
			<img src="/?plugins&q=imgcode" id="valicode" alt="点击刷新" onClick="this.src='/?plugins&q=imgcode&t=' + Math.random();" align="absmiddle" style="cursor:pointer" />
		</div>
	</div>
	
	
	<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
		</form>
	</div>
	
	

{else}


<div class="module_add">
	<div class="module_add">
	<div class="module_title"><strong>短消息接收记录列表</strong><span style="float:right">
				接收人：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>  <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()"></span></div>
	</div>
	<form action="" method="post" id="form1">
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td"><input type="checkbox" name="allcheck" onclick="checkFormAll(this.form)"/></td>
		<td width="" class="main_td">接收人</td>
		<td width="*" class="main_td">发送类型</td>
		<td width="" class="main_td">发送人</td>
		<td width="*" class="main_td">标题</td>
		<td width="*" class="main_td">内容</td>
		<td width="*" class="main_td">接收时间</td>	
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="message" function="GetMessageReceiveList" var="loop" epage=20 page=request username=request  }
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center"><input type="checkbox" name="id[{$key}]" value="{$item.id}"/></td>
		<td class="main_td1" align="center">{$item.receive_username}</td>
		<td class="main_td1" align="center">{$item.type|in_array:"$_A.message_type"}</td>
		<td class="main_td1" align="center">{$item.send_username|default:"系统"}</td>
		<td class="main_td1" align="center">{$item.name}</td>
		<td class="main_td1" align="center">{$item.contents}</td>
		<td class="main_td1" align="center">{$item.addtime|date_format}</td>
		<td class="main_td1" align="center"><a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url_all}&del={$item.id}&type={$item.type}'">删除</a></td>
	</tr>
	{/foreach}
	<tr>
			<td colspan="11" class="action">
			<div class="floatl"><input type="button"  value="删除" onclick="on_submit('{$_A.query_url}/receive','deled')" /><input type="hidden" value="0" name="type" id="type" />
			<script>
	  var url = '{$_A.query_url_all}';
	    {literal}
	  	function sousuo(){
			var username = $("#username").val();
			location.href=url+"&username="+username;
		}
	  
	  </script>
	  {/literal}
			</div>
			<div class="floatr">
			</div>
			</td>
		</tr>
	<tr align="center">
		<td colspan="10" align="center"><div align="center">{$loop.pages|showpage}</div></td>
	</tr>
	{/list}
	
</table>
<!--菜单列表 结束-->
</form>
		
{/if}