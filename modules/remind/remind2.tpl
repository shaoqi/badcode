gf{if $_A.query_type == "list" }
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<form action="{$_A.query_url}/type_action" method="post">
	<tr >
		<td class="main_td">ID</td>
		<td class="main_td">联动类型</td>
		<td class="main_td">标示名</td>
		<td class="main_td">排序</td>
		<td class="main_td">操作</td>
	</tr>
	{ foreach  from=$_A.linkage_list key=key item=item}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center" width="50">{ $item.id}</td>
		<td class="main_td1" align="center" width="250"><input type="text" value="{$item.name}" name="name[]" /></td>
		<td class="main_td1" align="center" width="*">{$item.nid}</td>
		<td class="main_td1" align="center" ><input type="text" value="{$item.order|default:10}" name="order[]" size="5" /><input type="hidden" name="id[]" value="{$item.id}" /></td>
		<td class="main_td1" align="center" width="130"><a href="{$_A.query_url}/new&id={$item.id}">管理</a> / <a href="{$_A.query_url}/type_edit&id={$item.id}">修改</a> / <a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url}/type_del&id={$item.id}'">删除</a></td>
	</tr>
	{ /foreach}
	<tr >
		<td colspan="8"  class="page">
			{$_A.showpage}
		</td>
	</tr>
	<tr >
		<td colspan="8"  class="submit">
			<input type="submit" name="submit" value="修改资料" />
		</td>
	</tr>
	</form>	
</table>

{elseif $_A.query_type == "new" || $_A.query_type == "edit"}
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
<form action="{$_A.query_url}/actions" method="post">
	<tr >
		<td class="main_td">ID</td>
		<td class="main_td">联动类型</td>
		<td class="main_td">联动名</td>
		<td class="main_td">联动值</td>
		<td class="main_td">排序</td>
		<td class="main_td">操作</td>
	</tr>
	{ foreach  from=$_A.linkage_list key=key item=item}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{$item.id}</td>
		<td class="main_td1" align="center">{$_A.linkage_type_result.name}</td>
		<td class="main_td1" align="center"><input type="text" value="{$item.name}" name="name[]" /></td>
		<td class="main_td1" align="center"><input type="text" value="{$item.value}" name="value[]" /></td>
		<td class="main_td1" align="center" ><input type="text" value="{$item.order|default:10}" name="order[]" size="5" /><input type="hidden" name="id[]" value="{$item.id}" /></td>
		<td class="main_td1" align="center" width="130"><!--<a href="{$_A.query_url}/subnew&id={$item.type_id}&pid={$item.id}">管理</a> /--> <a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url}/del&id={$item.id}'">删除</a></td>
	</tr>
	{ /foreach}
<tr >
	<td colspan="6"  class="submit">
		<input type="submit" name="submit" value="修改排序" />
	</td>
</tr>
</form>	
</table>

<div class="module_add">
<form name="form1" method="post" action="" onsubmit="return check_form()" >
	<div class="module_title"><strong>{ if $_A.query_type == "edit" }编辑{else}添加{/if} ({$_A.linkage_type_result.name}) 分类下的联动</strong></div>
	
	<div class="module_border">
		<div class="l">所属类别：</div>
		<div class="c">
			{$_A.linkage_type_result.name}
		</div>
	</div>

	<div class="module_border">
		<div class="l">联动的名称：</div>
		<div class="c">
			<input type="text" name="name"  value="{$_A.linkage_result.name}"/>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">联动的值：</div>
		<div class="c">
			<input type="text" name="value"  value="{$_A.linkage_result.value}" /> 如果值不写，则将为联动的名称
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">排序：</div>
		<div class="c">
			<input type="text" name="order"  value="{$_A.linkage_result.order|default:10}" onkeyup="value=value.replace(/[^0-9]/g,'')"/>
		</div>
	</div>
	
	<div class="module_submit" >
		<input type="hidden" name="pid" value="{$magic.request.pid|default:0}" />
		<input type="hidden" name="type_id" value="{$magic.request.id}" />
		<input type="submit"  name="submit" value="确认提交" />
		<input type="reset"  name="reset" value="重置表单" />
	</div>
</form>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
<form action="{$_A.query_url}/actions" method="post">
	<tr >
		<td class="main_td" colspan="6" align="left">&nbsp;批量添加</td>
	</tr>
	<tr  class="tr2">
		<td class="main_td1" align="center">名称</td>
		<td class="main_td1" align="center">值</td>
		<td class="main_td1" align="center" >排序</td>
	</tr>
	<tr >
		<td class="main_td1" align="center"><input type="text"  name="name[]" /></td>
		<td class="main_td1" align="center"><input type="text" name="value[]" /></td>
		<td class="main_td1" align="center" ><input type="text" name="order[]" value="10" size="5" /></td>
	</tr>
	<tr >
		<td class="main_td1" align="center"><input type="text"  name="name[]" /></td>
		<td class="main_td1" align="center"><input type="text" name="value[]" /></td>
		<td class="main_td1" align="center" ><input type="text" name="order[]" value="10" size="5" /></td>
	</tr>
	<tr >
		<td class="main_td1" align="center"><input type="text"  name="name[]" /></td>
		<td class="main_td1" align="center"><input type="text" name="value[]" /></td>
		<td class="main_td1" align="center" ><input type="text" name="order[]" value="10" size="5" /></td>
	</tr>
	<tr >
		<td class="main_td1" align="center"><input type="text"  name="name[]" /></td>
		<td class="main_td1" align="center"><input type="text" name="value[]" /></td>
		<td class="main_td1" align="center" ><input type="text" name="order[]"  value="10"size="5" /></td>
	</tr>
	<tr >
		<td class="main_td1" align="center"><input type="text"  name="name[]" /></td>
		<td class="main_td1" align="center"><input type="text" name="value[]" /></td>
		<td class="main_td1" align="center" ><input type="text" name="order[]" value="10" size="5" /></td>
	</tr>
	<tr >
		<td class="main_td1" align="center"><input type="text"  name="name[]" /></td>
		<td class="main_td1" align="center"><input type="text" name="value[]" /></td>
		<td class="main_td1" align="center" ><input type="text" name="order[]" value="10" size="5" /></td>
	</tr>
	<tr >
		<td class="main_td1" align="center"><input type="text"  name="name[]" /></td>
		<td class="main_td1" align="center"><input type="text" name="value[]" /></td>
		<td class="main_td1" align="center" ><input type="text" name="order[]" value="10" size="5" /></td>
	</tr>
	<tr >
		<td class="main_td1" align="center"><input type="text"  name="name[]" /></td>
		<td class="main_td1" align="center"><input type="text" name="value[]" /></td>
		<td class="main_td1" align="center" ><input type="text" name="order[]" value="10" size="5" /></td>
	</tr>
	<tr >
		<td class="main_td1" align="center"><input type="text"  name="name[]" /></td>
		<td class="main_td1" align="center"><input type="text" name="value[]" /></td>
		<td class="main_td1" align="center" ><input type="text" name="order[]"  value="10"size="5" /></td>
	</tr>
	<tr >
		<td class="main_td1" align="center"><input type="text"  name="name[]" /></td>
		<td class="main_td1" align="center"><input type="text" name="value[]" /></td>
		<td class="main_td1" align="center" ><input type="text" name="order[]" value="10" size="5" /></td>
	</tr>
	<input type="hidden" name="type_id" value="{$magic.request.id}" />
<tr >
	<td colspan="6"  class="submit">
		<input type="submit" name="submit" value="确认添加" />
	</td>
</tr>
</form>	
</table>
{literal}
<script>
function check_form(){
	
	var frm = document.forms['form1'];
	var title = frm.elements['name'].value;
	
	 var errorMsg = '';
	  if (title == "") {
		errorMsg += '联动的名称必须填写' + '\n';
	  }
	 
	  
	  if (errorMsg.length > 0){
		alert(errorMsg); return false;
	  } else{  
		return true;
	  }
}

</script>
{/literal}
{elseif $_A.query_type == "subnew" || $_A.query_type == "subedit"}

<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	 <form action="{$_A.query_url}/order" method="post">
	<tr >
		<td class="main_td">名称</td>
		<td class="main_td">类型</td>
		<td class="main_td">所属分类</td>
		<td class="main_td">排序</td>
		<td class="main_td">操作</td>
	</tr>
	{ foreach  from=$result key=key item=item}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center" width="250"><input type="text" value="{$item.name}" name="name[]" /></td>
		<td class="main_td1" align="center" width="150">{$liandong_type.typename}</td>
		<td class="main_td1" align="center" width="150">{$liandong_sub.name}</td>
		<td class="main_td1" align="center" ><input type="text" value="{$item.order|default:10}" name="order[]" size="5" /><input type="hidden" name="id[]" value="{$item.id}" /></td>
		<td class="main_td1" align="center" width="130"><a href="{$_A.query_url}/subnew&id={$item.type_id}&pid={$item.id}">管理</a> / <a href="{$_A.query_url}/edit&id={$item.id}">修改</a> / <a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url}/del&id={$item.id}'">删除</a></td>
	</tr>
	{ /foreach}
<tr >
	<td colspan="6"  class="submit">
		<input type="submit" name="submit" value="修改排序" />
	</td>
</tr>
</form>	

<form action="" method="post">
	<tr >
		<td colspan="6" class="action">
			<strong>所属联动类型：</strong>{$liandong_type.typename} -> <input type="text" name="name" /> <input type="submit" name="submit" value="添加" /> <input type="hidden" name="pid" value="{$magic.request.pid|default:0}" /><input type="hidden" name="type_id" value="{$magic.request.id}" />
		</td>
	</tr>
	</form>	
</table>
{elseif $_A.query_type == "type_new" || $_A.query_type == "type_edit"}
<div class="module_add">

	<form name="form1" method="post" action="" >
	<div class="module_title"><strong>{ if $_A.query_type == "type_edit" }编辑{else}添加{/if}联动类型</strong></div>
	
	<div class="module_border">
		<div class="l">联动类型名称：</div>
		<div class="c">
			<input type="text" name="name" value="{$_A.linkage_type_result.name}" />
		</div>
	</div>

	<div class="module_border">
		<div class="l">联动的标识名：</div>
		<div class="c">
			<input type="text" name="nid"  value="{$_A.linkage_type_result.nid}" onkeyup="value=value.replace(/[^a-z_]/g,'')"/>
		</div>
	</div>

	<div class="module_border">
		<div class="l">排序：</div>
		<div class="c">
			<input type="text" name="order"  value="{$_A.linkage_type_result.order|default:10}"  onkeyup="value=value.replace(/[^0-9]/g,'')"/>
		</div>
	</div>
	
	<div class="module_submit" >
		{if $_A.query_type=="type_edit"}<input type="hidden" name="id" value="{$magic.request.id}" />{/if}
		<input type="submit"  name="submit" value="确认提交" />
		<input type="reset"  name="reset" value="重置表单" />
	</div>
	</form>
</div>
{if $_A.query_type == "type_new" }
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
<form action="{$_A.query_url}/type_action" method="post">
	<tr >
		<td class="main_td" colspan="6" align="left">&nbsp;批量添加</td>
	</tr>
	<tr  class="tr2">
		<td class="main_td1" align="center">联动类型名称</td>
		<td class="main_td1" align="center">联动的标识名</td>
		<td class="main_td1" align="center" >排序</td>
	</tr>
	<tr >
		<td class="main_td1" align="center"><input type="text"  name="name[]" /></td>
		<td class="main_td1" align="center"><input type="text" name="nid[]" /></td>
		<td class="main_td1" align="center" ><input type="text" name="order[]" value="10" size="5" /></td>
	</tr>
	<tr >
		<td class="main_td1" align="center"><input type="text"  name="name[]" /></td>
		<td class="main_td1" align="center"><input type="text" name="nid[]" /></td>
		<td class="main_td1" align="center" ><input type="text" name="order[]" value="10" size="5" /></td>
	</tr>
	<tr >
		<td class="main_td1" align="center"><input type="text"  name="name[]" /></td>
		<td class="main_td1" align="center"><input type="text" name="nid[]" /></td>
		<td class="main_td1" align="center" ><input type="text" name="order[]" value="10" size="5" /></td>
	</tr>
	<tr >
		<td class="main_td1" align="center"><input type="text"  name="name[]" /></td>
		<td class="main_td1" align="center"><input type="text" name="nid[]" /></td>
		<td class="main_td1" align="center" ><input type="text" name="order[]"  value="10"size="5" /></td>
	</tr>
	<tr >
		<td class="main_td1" align="center"><input type="text"  name="name[]" /></td>
		<td class="main_td1" align="center"><input type="text" name="nid[]" /></td>
		<td class="main_td1" align="center" ><input type="text" name="order[]" value="10" size="5" /></td>
	</tr>
	
<tr >
	<td colspan="6"  class="submit">
		<input type="hidden" name="type" value="add" />
		<input type="submit" name="submit" value="确认添加" />
	</td>
</tr>
</form>	
</table>
{/if}
{/if}