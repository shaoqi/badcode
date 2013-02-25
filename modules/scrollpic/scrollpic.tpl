<ul class="nav3"> 
<li><a href="{$_A.query_url}" {if $_A.query_type=="list"} id="c_so"{/if}>列表管理</a></li> 
<li><a href="{$_A.query_url}/new" {if  $_A.query_type=="new"} id="c_so"{/if}>添加滚动 </a></li> 
<li><a href="{$_A.query_url}/type" {if $_A.query_type=="type"} id="c_so"{/if}>类型管理</a></li> 
</ul> 
{if $_A.query_type == "new" || $_A.query_type == "edit"}
<div class="module_add">
	<form name="form1" method="post" action="" onsubmit="return check_form();" enctype="multipart/form-data" >
	<div class="module_title"><strong>{ if $_A.query_type == "edit" }编辑{else}添加{/if}滚动图片</strong></div>
	
	<div class="module_border">
		<div class="l">标题：</div>
		<div class="c">
			<input type="text" name="name"  class="input_border" value="{ $_A.scrollpic_result.name}" size="30" />  
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">类别：</div>
		<div class="c">
			<select name="type_id">
			{foreach from=$_A.scrollpic_type_list item=item}
			<option  value="{ $item.id}" {if $item.id==$_A.scrollpic_result.type_id} selected="selected"{/if} />{ $item.typename}</option>
			
			{/foreach}
			</select>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">状态：</div>
		<div class="c">
			<input type="radio" name="status" value="0"  { if $_A.scrollpic_result.status == 0 }checked="checked"{/if}/>隐藏 <input type="radio" name="status" value="1"  { if $_A.scrollpic_result.status ==1 ||$_A.scrollpic_result.status ==""}checked="checked"{/if}/>显示 </div>
	</div>
	
	<div class="module_border">
		<div class="l">排序：</div>
		<div class="c">
			<input type="text" name="order"  class="input_border" value="{ $_A.scrollpic_result.order|default:10}" size="10" />
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">上传图片:</div>
		<div class="c">
			<input type="file" name="pic"  class="input_border" size="20" />{if $_A.scrollpic_result.pic!=""}<a href="./{$_A.scrollpic_result.pic}" target="_blank" title="有图片"><img src="{ $tpldir }/images/ico_1.jpg" border="0"  /></a>{/if}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">链接地址:</div>
		<div class="c">
			<input type="text" name="url"  class="input_border" value="{ $_A.scrollpic_result.url}" size="30" />请填写url地址
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">网站简介:</div>
		<div class="c">
			<textarea name="summary" cols="40" rows="5">{$_A.scrollpic_result.summary}</textarea>
		</div>
	</div>
	
	<div class="module_submit" >
		{ if $_A.query_type == "edit" }<input type="hidden" name="id" value="{ $_A.scrollpic_result.id }" />{/if}
		<input type="submit"  name="submit" value="确认提交" />
		<input type="reset"  name="reset" value="重置表单" />
		</div>
	</div>
	</form>
</div>

{literal}
<script>
function check_form(){
	 var frm = document.forms['form1'];
	 var webname = frm.elements['webname'].value;
	 var url = frm.elements['url'].value;
	 var errorMsg = '';
	  if (webname.length == 0 ) {
		errorMsg += '标题必须填写' + '\n';
	  }
	  if (url.length == 0 ) {
		errorMsg += '地址不能为空' + '\n';
	  }
	  if (errorMsg.length > 0){
		alert(errorMsg); return false;
	  } else{  
		return true;
	  }
}

</script>
{/literal}
{elseif $_A.query_type == "type"}
<div class="module_add">
	<div class="module_title"><strong>类型管理</strong></div>
 </div>
<form name="form1" method="post" action="" >
<table width="100%" border="0"  cellspacing="1" bgcolor="#CCCCCC">
<tr >
		<td width="" class="main_td">ID</td>
		<td width="*" class="main_td">名称</td>
		<td width="" class="main_td">操作</td>
	</tr>
	{ foreach  from=$_A.scrollpic_type_list key=key item=item}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="left">&nbsp;&nbsp;&nbsp;<input type="text" value="{$item.typename}" name="typename[]" /><input type="hidden" name="id[]" value="{$item.id}" /></td>
		<td class="main_td1" align="center" width="160"><a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url}/type&del_id={$item.id}'">删除</a> </td>
	</tr>
	{ /foreach}
	<tr >
		<td width="" class="main_td" colspan="3">新增一个类型：<input type="text" name="typename1" /></td>
	</tr>

<tr>
	<td bgcolor="#ffffff" colspan="3"  align="center">
	<input type="submit"  name="submit" value="确认提交" />
	</tr>
<tr>
</table>
</form>
{elseif $_A.query_type == "view"}

{else}
<div class="module_add">
	<div class="module_title"><strong>管理列表</strong></div>
 </div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
  <form action="{$_A.query_url}/order" method="post">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="*" class="main_td">标题</td>
		<td width="" class="main_td">类型</td>
		<td width="" class="main_td">状态</td>
		<td width="" class="main_td">排序</td>
		<td width="" class="main_td">添加时间</td>
		<td width="" class="main_td">图片</td>
		<td width="" class="main_td">操作</div>
	</div>
	
	{list module="scrollpic" function="GetList" var="loop"}
	{ foreach  from=$loop.list key=key item=item}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.name}</td>
		<td class="main_td1" align="center" width="60">{$item.typename }</td>
		<td class="main_td1" align="center" width="50">{ if $item.status ==1}显示{else}隐藏{/if}</td>
		<td class="main_td1" align="center" width="50"><input type="text" value="{$item.order|default:10}" name="order[]" size="5" /><input type="hidden" name="id[]" value="{$item.id}" /></td>
		<td class="main_td1" align="center" width="90">{$item.addtime|date_format:"Y-m-d"}</td>
		<td class="main_td1" align="center" width="80">{if $item.pic!=""}<a href="./{$item.pic}" target="_blank"><img height="20" src="./{$item.pic}" border="0" /></a>{else}无图片{/if}</td>
		<td class="main_td1" align="center" width="130"><a href="{$_A.query_url}/edit&id={$item.id}">修改</a> / <a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url}/del&id={$item.id}'">删除</a></div>
	</div>
	{ /foreach}
	<tr >
		<td colspan="8" class="submit">
			<input type="submit" name="submit" value="确认提交" />
		</td>
	</tr>
	<tr >
		<td colspan="8" class="page">
			{$loop.pages|showpage}
		</td>
	</tr>
	</form>	{/list}
</table>
{/if}