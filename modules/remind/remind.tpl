<ul class="nav3"> 
<li><a href="{$_A.query_url}" id="c_so">提醒设置</a></li> 
<li><a href="{$_A.query_url}/type_new">添加类型</a></li> 
</ul> 
{if $_A.query_type == "list" }
<div class="module_add">
	<div class="module_title"><strong>提醒列表</strong></div>

</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<form action="{$_A.query_url}/type_action" method="post">
	<tr >
		<td class="main_td">{$MsgInfo.remind_id}</td>
		<td class="main_td">{$MsgInfo.remind_type}</td>
		<td class="main_td">{$MsgInfo.remind_nid}</td>
		<td class="main_td">{$MsgInfo.remind_order}</td>
		<td class="main_td">{$MsgInfo.remind_action}</td>
	</tr>
	{list module="remind" function="GetTypeList" var="loop"}
	{ foreach  from=$loop.list key=key item=item}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td  >{ $item.id}</td>
		<td  ><input type="text" value="{$item.name}" name="name[]" /></td>
		<td  width="*">{$item.nid}</td>
		<td  ><input type="text" value="{$item.order|default:10}" name="order[]" size="5" /><input type="hidden" name="id[]" value="{$item.id}" /></td>
		<td  width="130"><a href="{$_A.query_url}/new&id={$item.id}">{$MsgInfo.remind_manager}</a> / <a href="{$_A.query_url}/type_edit&id={$item.id}">{$MsgInfo.remind_update}</a> / <a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url}/type_del&id={$item.id}'">{$MsgInfo.remind_delete}</a></td>
	</tr>
	{ /foreach}
	<tr >
		<td colspan="8"  class="page">
			{$loop.pages|showpage}
		</td>
	</tr>
	{/list}
	<tr >
		<td colspan="8"  class="submit">
			<input type="submit" name="submit" value="{$MsgInfo.remind_submit}" />
		</td>
	</tr>
	</form>	
</table>

{elseif $_A.query_type == "new" || $_A.query_type == "edit"}
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
<form action="{$_A.query_url}/actions" method="post">
	<tr >
		<td class="main_td">{$MsgInfo.remind_action}</td>
		<td class="main_td">{$MsgInfo.remind_type}</td>
		<td class="main_td">{$MsgInfo.remind_name}</td>
		<td class="main_td">{$MsgInfo.remind_nid}</td>
		<td class="main_td">{$MsgInfo.remind_message}</td>
		<td class="main_td">{$MsgInfo.remind_email}</td>
		<td class="main_td">{$MsgInfo.remind_phone}</td>
		<td class="main_td">{$MsgInfo.remind_order}</td>
		<td class="main_td">{$MsgInfo.remind_action}</td>
	</tr>
	{loop module="remind" function="GetList" var="item" limit="all" type_id='$magic.request.id'}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td >{$item.id}</td>
		<td >{$_A.remind_type_result.name}</td>
		<td ><input type="text" value="{$item.name}" name="name[]" size="15" /></td>
		<td ><input type="text" value="{$item.nid}" name="nid[]" size="15" /></td>
		<td >
			<select name="message[]">
				<option value="1" {if $item.message==1} selected="selected"{/if}>{$MsgInfo.remind_choose_yes}</option>
				<option value="2" {if $item.message==2} selected="selected"{/if}>{$MsgInfo.remind_choose_no}</option>
				<option value="3" {if $item.message==3} selected="selected"{/if}>{$MsgInfo.remind_choose_or_yes}</option>
				<option value="4" {if $item.message==4} selected="selected"{/if}>{$MsgInfo.remind_choose_or_no}</option>
			</select>
			{if $item.message==1}<input type="checkbox" disabled="disabled" checked="checked" />{elseif $item.message==2} <input type="checkbox" disabled="disabled"/>{elseif $item.message==3} <input type="checkbox" checked="checked" />{elseif $item.message=4} <input type="checkbox" />{/if}
		</td>
		<td >
			<select name="email[]">
				<option value="1" {if $item.email==1} selected="selected"{/if}>{$MsgInfo.remind_choose_yes}</option>
				<option value="2" {if $item.email==2} selected="selected"{/if}>{$MsgInfo.remind_choose_no}</option>
				<option value="3" {if $item.email==3} selected="selected"{/if}>{$MsgInfo.remind_choose_or_yes}</option>
				<option value="4" {if $item.email==4} selected="selected"{/if}>{$MsgInfo.remind_choose_or_no}</option>
			</select>
			{if $item.email==1}<input type="checkbox" disabled="disabled" checked="checked" />{elseif $item.email==2} <input type="checkbox" disabled="disabled"/>{elseif $item.email==3} <input type="checkbox" checked="checked" />{elseif $item.email=4} <input type="checkbox" />{/if}
		</td>
		<td >
			<select name="phone[]">
				<option value="1" {if $item.phone==1} selected="selected"{/if}>{$MsgInfo.remind_choose_yes}</option>
				<option value="2" {if $item.phone==2} selected="selected"{/if}>{$MsgInfo.remind_choose_no}</option>
				<option value="3" {if $item.phone==3} selected="selected"{/if}>{$MsgInfo.remind_choose_or_yes}</option>
				<option value="4" {if $item.phone==4} selected="selected"{/if}>{$MsgInfo.remind_choose_or_no}</option>
			</select>
			{if $item.phone==1}<input type="checkbox" disabled="disabled" checked="checked" />{elseif $item.phone==2} <input type="checkbox" disabled="disabled"/>{elseif $item.phone==3} <input type="checkbox" checked="checked" />{elseif $item.phone=4} <input type="checkbox" />{/if}
		</td>
		<td  ><input type="text" value="{$item.order|default:10}" name="order[]" size="5" /><input type="hidden" name="id[]" value="{$item.id}" /></td>
		<td ><!--<a href="{$_A.query_url}/subnew&id={$item.type_id}&pid={$item.id}">管理</a> /--> <a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url}/del&id={$item.id}'">{$MsgInfo.remind_delete}</a></td>
	</tr>
	{ /loop}
<tr >
	<td colspan="6"  class="submit">
		<input type="submit" name="submit" value="{$MsgInfo.remind_submit}" />
	</td>
</tr>
</form>	
</table>

<div class="module_add">
<form name="form1" method="post" action="" onsubmit="return check_form()" >
	<div class="module_title"><strong>{ if $_A.query_type == "edit" }{$MsgInfo.remind_edit}{else}{$MsgInfo.remind_add}{/if} ({$_A.remind_type_result.name}) 分类下的提醒</strong></div>
	
	<div class="module_border">
		<div class="l">{$MsgInfo.remind_type}：</div>
		<div class="c">
			{$_A.remind_type_result.name}
		</div>
	</div>

	<div class="module_border">
		<div class="l">{$MsgInfo.remind_name}：</div>
		<div class="c">
			<input type="text" name="name"  value="{$_A.remind_result.name}"/> *
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">{$MsgInfo.remind_nid}：</div>
		<div class="c">
			<input type="text" name="nid"  value="{$_A.remind_result.nid}" /> *
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">{$MsgInfo.remind_order}：</div>
		<div class="c">
			<input type="text" name="order"  value="{$_A.remind_result.order|default:10}" onkeyup="value=value.replace(/[^0-9]/g,'')"/>
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">{$MsgInfo.remind_message}：</div>
		<div class="c">
			<select name="message">
				<option value="1">{$MsgInfo.remind_choose_yes}</option>
				<option value="2">{$MsgInfo.remind_choose_no}</option>
				<option value="3">{$MsgInfo.remind_choose_or_yes}</option>
				<option value="4">{$MsgInfo.remind_choose_or_no}</option>
			</select>
			({$MsgInfo.remind_type}：<input type="checkbox" disabled="disabled" checked="checked" />{$MsgInfo.remind_choose_yes} <input type="checkbox" disabled="disabled"/>{$MsgInfo.remind_choose_no} <input type="checkbox" checked="checked" />{$MsgInfo.remind_choose_or_yes} <input type="checkbox" />{$MsgInfo.remind_choose_or_no}）
		</div>
	</div>
	
	
	
	<div class="module_border">
		<div class="l">{$MsgInfo.remind_email}：</div>
		<div class="c">
			<select name="email">
				<option value="1">{$MsgInfo.remind_choose_yes}</option>
				<option value="2">{$MsgInfo.remind_choose_no}</option>
				<option value="3">{$MsgInfo.remind_choose_or_yes}</option>
				<option value="4">{$MsgInfo.remind_choose_or_no}</option>
			</select>
		</div>
	</div>
	
	
	
	<div class="module_border">
		<div class="l">{$MsgInfo.remind_phone}：</div>
		<div class="c">
			<select name="phone">
				<option value="1">{$MsgInfo.remind_choose_yes}</option>
				<option value="2">{$MsgInfo.remind_choose_no}</option>
				<option value="3">{$MsgInfo.remind_choose_or_yes}</option>
				<option value="4">{$MsgInfo.remind_choose_or_no}</option>
			</select>
		</div>
	</div>
	
	<div class="module_submit" >
		<input type="hidden" name="type_id" value="{$magic.request.id}" />
		<input type="submit"  name="submit" value="{$MsgInfo.remind_submit}" />
		<input type="reset"  name="reset" value="{$MsgInfo.remind_reset}" />
	</div>
</form>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
<form action="{$_A.query_url}/actions" method="post">
	<tr >
		<td class="main_td" colspan="6" align="left">&nbsp;{$MsgInfo.remind_add_more}</td>
	</tr>
	<tr  class="tr2">
		<td >{$MsgInfo.remind_name}</td>
		<td >{$MsgInfo.remind_nid}</td>
		<td>{$MsgInfo.remind_message}</td>
		<td >{$MsgInfo.remind_email}</td>
		<td >{$MsgInfo.remind_phone}</td>
		<td >{$MsgInfo.remind_order}</td>
	</tr>
	<tr >
		<td ><input type="text"  name="name[]" /></td>
		<td ><input type="text" name="nid[]" /></td>
		<td>
			<select name="message[]">
				<option value="1">{$MsgInfo.remind_choose_yes}</option>
				<option value="2">{$MsgInfo.remind_choose_no}</option>
				<option value="3">{$MsgInfo.remind_choose_or_yes}</option>
				<option value="4">{$MsgInfo.remind_choose_or_no}</option>
			</select>
		</td>
		<td>
			<select name="email[]">
				<option value="1">{$MsgInfo.remind_choose_yes}</option>
				<option value="2">{$MsgInfo.remind_choose_no}</option>
				<option value="3">{$MsgInfo.remind_choose_or_yes}</option>
				<option value="4">{$MsgInfo.remind_choose_or_no}</option>
			</select>
		</td>
		<td>
			<select name="phone[]">
				<option value="1">{$MsgInfo.remind_choose_yes}</option>
				<option value="2">{$MsgInfo.remind_choose_no}</option>
				<option value="3">{$MsgInfo.remind_choose_or_yes}</option>
				<option value="4">{$MsgInfo.remind_choose_or_no}</option>
			</select>
		</td>
		<td  ><input type="text" name="order[]" value="10" size="5" /></td>
	</tr>
	<tr >
		<td ><input type="text"  name="name[]" /></td>
		<td ><input type="text" name="nid[]" /></td>
		<td>
			<select name="message[]">
				<option value="1">{$MsgInfo.remind_choose_yes}</option>
				<option value="2">{$MsgInfo.remind_choose_no}</option>
				<option value="3">{$MsgInfo.remind_choose_or_yes}</option>
				<option value="4">{$MsgInfo.remind_choose_or_no}</option>
			</select>
		</td>
		<td>
			<select name="email[]">
				<option value="1">{$MsgInfo.remind_choose_yes}</option>
				<option value="2">{$MsgInfo.remind_choose_no}</option>
				<option value="3">{$MsgInfo.remind_choose_or_yes}</option>
				<option value="4">{$MsgInfo.remind_choose_or_no}</option>
			</select>
		</td>
		<td>
			<select name="phone[]">
				<option value="1">{$MsgInfo.remind_choose_yes}</option>
				<option value="2">{$MsgInfo.remind_choose_no}</option>
				<option value="3">{$MsgInfo.remind_choose_or_yes}</option>
				<option value="4">{$MsgInfo.remind_choose_or_no}</option>
			</select>
		</td>
		<td  ><input type="text" name="order[]" value="10" size="5" /></td>
	</tr>
	<tr >
		<td ><input type="text"  name="name[]" /></td>
		<td ><input type="text" name="nid[]" /></td>
		<td>
			<select name="message[]">
				<option value="1">{$MsgInfo.remind_choose_yes}</option>
				<option value="2">{$MsgInfo.remind_choose_no}</option>
				<option value="3">{$MsgInfo.remind_choose_or_yes}</option>
				<option value="4">{$MsgInfo.remind_choose_or_no}</option>
			</select>
		</td>
		<td>
			<select name="email[]">
				<option value="1">{$MsgInfo.remind_choose_yes}</option>
				<option value="2">{$MsgInfo.remind_choose_no}</option>
				<option value="3">{$MsgInfo.remind_choose_or_yes}</option>
				<option value="4">{$MsgInfo.remind_choose_or_no}</option>
			</select>
		</td>
		<td>
			<select name="phone[]">
				<option value="1">{$MsgInfo.remind_choose_yes}</option>
				<option value="2">{$MsgInfo.remind_choose_no}</option>
				<option value="3">{$MsgInfo.remind_choose_or_yes}</option>
				<option value="4">{$MsgInfo.remind_choose_or_no}</option>
			</select>
		</td>
		<td  ><input type="text" name="order[]" value="10" size="5" /></td>
	</tr>
	<tr >
		<td ><input type="text"  name="name[]" /></td>
		<td ><input type="text" name="nid[]" /></td>
		<td>
			<select name="message[]">
				<option value="1">{$MsgInfo.remind_choose_yes}</option>
				<option value="2">{$MsgInfo.remind_choose_no}</option>
				<option value="3">{$MsgInfo.remind_choose_or_yes}</option>
				<option value="4">{$MsgInfo.remind_choose_or_no}</option>
			</select>
		</td>
		<td>
			<select name="email[]">
				<option value="1">{$MsgInfo.remind_choose_yes}</option>
				<option value="2">{$MsgInfo.remind_choose_no}</option>
				<option value="3">{$MsgInfo.remind_choose_or_yes}</option>
				<option value="4">{$MsgInfo.remind_choose_or_no}</option>
			</select>
		</td>
		<td>
			<select name="phone[]">
				<option value="1">{$MsgInfo.remind_choose_yes}</option>
				<option value="2">{$MsgInfo.remind_choose_no}</option>
				<option value="3">{$MsgInfo.remind_choose_or_yes}</option>
				<option value="4">{$MsgInfo.remind_choose_or_no}</option>
			</select>
		</td>
		<td  ><input type="text" name="order[]" value="10" size="5" /></td>
	</tr>
	<tr >
		<td ><input type="text"  name="name[]" /></td>
		<td ><input type="text" name="nid[]" /></td>
		<td>
			<select name="message[]">
				<option value="1">{$MsgInfo.remind_choose_yes}</option>
				<option value="2">{$MsgInfo.remind_choose_no}</option>
				<option value="3">{$MsgInfo.remind_choose_or_yes}</option>
				<option value="4">{$MsgInfo.remind_choose_or_no}</option>
			</select>
		</td>
		<td>
			<select name="email[]">
				<option value="1">{$MsgInfo.remind_choose_yes}</option>
				<option value="2">{$MsgInfo.remind_choose_no}</option>
				<option value="3">{$MsgInfo.remind_choose_or_yes}</option>
				<option value="4">{$MsgInfo.remind_choose_or_no}</option>
			</select>
		</td>
		<td>
			<select name="phone[]">
				<option value="1">{$MsgInfo.remind_choose_yes}</option>
				<option value="2">{$MsgInfo.remind_choose_no}</option>
				<option value="3">{$MsgInfo.remind_choose_or_yes}</option>
				<option value="4">{$MsgInfo.remind_choose_or_no}</option>
			</select>
		</td>
		<td  ><input type="text" name="order[]" value="10" size="5" /></td>
	</tr>
	
	<input type="hidden" name="type_id" value="{$magic.request.id}" />
<tr >
	<td colspan="6"  class="submit">
		<input type="submit" name="submit" value="{$MsgInfo.remind_submit}" />
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
		errorMsg += '提醒的{$MsgInfo.remind_name}必须填写' + '\n';
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
		<td class="main_td">{$MsgInfo.remind_name}</td>
		<td class="main_td">{$MsgInfo.remind_type}</td>
		<td class="main_td">{$MsgInfo.remind_fenlei}</td>
		<td class="main_td">{$MsgInfo.remind_order}</td>
		<td class="main_td">{$MsgInfo.remind_action}</td>
	</tr>
	{ foreach  from=$result key=key item=item}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td  width="250"><input type="text" value="{$item.name}" name="name[]" /></td>
		<td  width="150">{$liandong_type.typename}</td>
		<td  width="150">{$liandong_sub.name}</td>
		<td  ><input type="text" value="{$item.order|default:10}" name="order[]" size="5" /><input type="hidden" name="id[]" value="{$item.id}" /></td>
		<td  width="130"><a href="{$_A.query_url}/subnew&id={$item.type_id}&pid={$item.id}">{$MsgInfo.remind_manager}</a> / <a href="{$_A.query_url}/edit&id={$item.id}">{$MsgInfo.remind_update}</a> / <a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url}/del&id={$item.id}'">{$MsgInfo.remind_delete}</a></td>
	</tr>
	{ /foreach}
<tr >
	<td colspan="6"  class="submit">
		<input type="submit" name="submit" value="{$MsgInfo.remind_submit}" />
	</td>
</tr>
</form>	

<form action="" method="post">
	<tr >
		<td colspan="6" class="action">
			<strong>{$MsgInfo.remind_type}：</strong>{$liandong_type.typename} -> <input type="text" name="name" /> <input type="submit" name="submit" value="添加" /> <input type="hidden" name="pid" value="{$magic.request.pid|default:0}" /><input type="hidden" name="type_id" value="{$magic.request.id}" />
		</td>
	</tr>
	</form>	
</table>
{elseif $_A.query_type == "type_new" || $_A.query_type == "type_edit"}
<div class="module_add">

	<form name="form1" method="post" action="" >
	<div class="module_title"><strong>{ if $_A.query_type == "type_edit" }{$MsgInfo.remind_edit}{else}{$MsgInfo.remind_add}{/if}{$MsgInfo.remind_type}</strong></div>
	
	<div class="module_border">
		<div class="l">{$MsgInfo.remind_type}：</div>
		<div class="c">
			<input type="text" name="name" value="{$_A.remind_type_result.name}" />
		</div>
	</div>

	<div class="module_border">
		<div class="l">{$MsgInfo.remind_nid}：</div>
		<div class="c">
			<input type="text" name="nid"  value="{$_A.remind_type_result.nid}" onkeyup="value=value.replace(/[^a-z_]/g,'')"/>
		</div>
	</div>

	<div class="module_border">
		<div class="l">{$MsgInfo.remind_order}：</div>
		<div class="c">
			<input type="text" name="order"  value="{$_A.remind_type_result.order|default:10}"  onkeyup="value=value.replace(/[^0-9]/g,'')"/>
		</div>
	</div>
	
	<div class="module_submit" >
		{if $_A.query_type=="type_edit"}<input type="hidden" name="id" value="{$magic.request.id}" />{/if}
		<input type="submit"  name="submit" value="{$MsgInfo.remind_submit}" />
		<input type="reset"  name="reset" value="{$MsgInfo.remind_reset}" />
	</div>
	</form>
</div>
{if $_A.query_type == "type_new" }
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
<form action="{$_A.query_url}/type_action" method="post">
	<tr >
		<td class="main_td" colspan="6" align="left">&nbsp;{$MsgInfo.remind_add_more}</td>
	</tr>
	<tr  class="tr2">
		<td >{$MsgInfo.remind_type}</td>
		<td >{$MsgInfo.remind_nid}</td>
		<td  >{$MsgInfo.remind_order}</td>
	</tr>
	<tr >
		<td ><input type="text"  name="name[]" /></td>
		<td ><input type="text" name="nid[]" /></td>
		<td  ><input type="text" name="order[]" value="10" size="5" /></td>
	</tr>
	<tr >
		<td ><input type="text"  name="name[]" /></td>
		<td ><input type="text" name="nid[]" /></td>
		<td  ><input type="text" name="order[]" value="10" size="5" /></td>
	</tr>
	<tr >
		<td ><input type="text"  name="name[]" /></td>
		<td ><input type="text" name="nid[]" /></td>
		<td  ><input type="text" name="order[]" value="10" size="5" /></td>
	</tr>
	<tr >
		<td ><input type="text"  name="name[]" /></td>
		<td ><input type="text" name="nid[]" /></td>
		<td  ><input type="text" name="order[]"  value="10"size="5" /></td>
	</tr>
	<tr >
		<td ><input type="text"  name="name[]" /></td>
		<td ><input type="text" name="nid[]" /></td>
		<td  ><input type="text" name="order[]" value="10" size="5" /></td>
	</tr>
	
<tr >
	<td colspan="6"  class="submit">
		<input type="hidden" name="type" value="add" />
		<input type="submit" name="submit" value="{$MsgInfo.remind_submit}" />
	</td>
</tr>
</form>	
</table>
{/if}
{/if}