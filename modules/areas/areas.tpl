<ul class="nav3"> 
<li><a href="{$_A.query_url}/province">{$MsgInfo.areas_menu_province}</a></li> 
<li><a href="{$_A.query_url}/city">{$MsgInfo.areas_menu_city}</a></li> 
<li><a href="{$_A.query_url}/area">{$MsgInfo.areas_menu_area}</a></li> 
<li><a href="{$_A.query_url}/province&action=new">{$MsgInfo.areas_menu_province_new}</a></li> 
</ul> 
{if $magic.request.action!=""}
<div class="module_add">

	<form action="" method="post" onsubmit="return check_form()" name="form1">
	<div class="module_title"><strong>{ if $magic.request.action == "edit" }{$MsgInfo.areas_name_edit}{else}{$MsgInfo.areas_name_new}{/if}</strong></div>
	
	<div class="module_border">
		<div class="l">{$MsgInfo.areas_name_aname}：</div>
		<div class="c">
		{ if $magic.request.action == "edit" }
		{$_A.area_result.id|areas:"p,c,a"}<input type="hidden" name="province" value="{$_A.area_result.province}" /><input type="hidden" name="city" value="{$_A.area_result.city}" />
		{else}
			{$_A.area_results.id|areas:"p,c,a"|default:"全国"}
			
			{if $_A.query_type=="city"}<input type="hidden" name="province" value="{$_A.area_results.id}" />
			{elseif $_A.query_type=="area"}
			<input type="hidden" name="province" value="{$_A.area_results.province}" /><input type="hidden" name="city" value="{$_A.area_results.id}" />
			{/if}
			<input type="hidden" name="pid" value="{$_A.area_results.id}" />
		{/if}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">{if $_A.query_type=="city"}{$MsgInfo.areas_name_city}{elseif $_A.query_type=="area"}{$MsgInfo.areas_name_area}{else}{$MsgInfo.areas_name_province}{/if}{$MsgInfo.areas_name_name}：</div>
		<div class="c">
			<input type="text" name="name"  class="input_border" value="{$_A.area_result.name}" size="30" />  
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">{$MsgInfo.areas_name_nid}：</div>
		<div class="c">
			<input type="text" name="nid"  class="input_border" value="{$_A.area_result.nid}" size="30" />
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">是否显示：</div>
		<div class="c">
			{input name="status" value="1|显示,2|隐藏" checked="$_A.area_result.status"}
		</div>
	</div>
	<!--
	<div class="module_border">
		<div class="l">{$MsgInfo.areas_name_domain}：</div>
		<div class="c">
			<input type="text" name="domain"  class="input_border" value="{$_A.area_result.domain|default:".hycms.com"}" size="30" /> 比如：beijing.hycms.com
		</div>
	</div>
	-->
	<div class="module_border">
		<div class="l">{$MsgInfo.areas_name_order}：</div>
		<div class="c">
			<input type="text" name="order"  class="input_border" value="{ $_A.area_result.order|default:10}" size="10" />
		</div>
	</div>
	
	<div class="module_submit border_b" >
		{ if $magic.request.action == "edit" }<input type="hidden" name="id" value="{ $_A.area_result.id }" />{/if}
		<input type="submit"  name="submit" value="{$MsgInfo.areas_name_submit}" /> <input type="button" value="添加数据" onclick="location.href='{$_A.admin_url}&q=code/areas/data'"/>
	</div>
	</form>
</div>
{else}
<div class="module_add">
	<div class="module_title"><strong>{if $_A.query_type=="province"}{$MsgInfo.areas_menu_province}{elseif $_A.query_type=="city"}{$MsgInfo.areas_menu_city}{elseif $_A.query_type=="area"}{$MsgInfo.areas_menu_area}{/if}</strong></div>

<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<form action="{$_A.query_url}/action&id={$magic.request.id}" method="post">
	<tr >
		<td class="main_td">{$MsgInfo.areas_name_id}</td>
		<td class="main_td">{$MsgInfo.areas_name_name}</td>
		{if $_A.query_type=="province"}
		{elseif $_A.query_type=="city"}
		<td class="main_td">{$MsgInfo.areas_name_province}</td>
		{elseif $_A.query_type=="area"}
		<td class="main_td">{$MsgInfo.areas_name_city}</td>
		<td class="main_td">{$MsgInfo.areas_name_province}</td>
		{/if}
		<td class="main_td">{$MsgInfo.areas_name_nid}</td>
		<td class="main_td">{$MsgInfo.areas_name_order}</td>
		<td class="main_td">{$MsgInfo.areas_name_manage}</td>
	</tr>
	{ list module="areas" function="GetList" type="$_A.query_type" var="loop" epage=10 id=request}
		{foreach from=$loop.list item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center" >{$item.name}</td>
		{if $_A.query_type=="province"}
		{elseif $_A.query_type=="city"}
		<td class="main_td1" align="center" >{$item.province|areas}</td>
		{elseif $_A.query_type=="area"}
		<td class="main_td1" align="center" >{$item.city|areas}</td>
		<td class="main_td1" align="center" >{$item.province|areas}</td>
		{/if}
		<td class="main_td1" align="center" >{$item.nid}</td>
		<td class="main_td1" align="center" ><input type="text" value="{$item.order|default:10}" name="order[]" size="5" /><input type="hidden" name="id[]" value="{$item.id}" /><input type="hidden" name="query_type" value="{$_A.query_type}" /></td>
		<td class="main_td1" align="center" >
		{if $_A.query_type=="province"}
		<a href="{$_A.query_url}/city&id={$item.id}">{$MsgInfo.areas_name_city}</a> / <a href="{$_A.query_url}/{ $_A.query_type}&action=edit&edit_id={$item.id}&id={$magic.request.id}">{$MsgInfo.areas_name_edit}</a> / <a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url}/{ $_A.query_type}&action=del&del_id={$item.id}&id={$magic.request.id}'">{$MsgInfo.areas_name_del}</a> / <a href="{$_A.query_url}/city&action=new&new_id={$item.id}">{$MsgInfo.areas_name_city_new}</a>
		
		{elseif $_A.query_type=="city"}
		 <a href="{$_A.query_url}/area&id={$item.id}">{$MsgInfo.areas_name_area}</a> / <a href="{$_A.query_url}/{ $_A.query_type}&action=edit&edit_id={$item.id}&id={$magic.request.id}">{$MsgInfo.areas_name_edit}</a> / <a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url}/{ $_A.query_type}&action=del&del_id={$item.id}&id={$magic.request.id}'">{$MsgInfo.areas_name_del}</a> / 
		 <a href="{$_A.query_url}/area&action=new&new_id={$item.id}&id={$magic.request.id}">{$MsgInfo.areas_name_area_new}</a>
		{else}
		<a href="{$_A.query_url}/{ $_A.query_type}&action=edit&edit_id={$item.id}&id={$magic.request.id}">{$MsgInfo.areas_name_edit}</a> / <a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url}/{ $_A.query_type}&action=del&del_id={$item.id}&id={$magic.request.id}'">{$MsgInfo.areas_name_del}</a></td>
		{/if}
	</tr>
	{ /foreach}
	<tr >
		<td colspan="8" class="submit"  height="30">
			<input type="submit" name="submit" value="{$MsgInfo.areas_name_submit}" />
		</td>
	</tr>
	<tr >
		<td colspan="8" class="page"  height="30">
			{$loop.pages|showpage}
		</td>
	</tr>
	{/list}
	</form>	
</table>


{literal}
<script>
function check_form(){
	 var frm = document.forms['form1'];
	 var title = frm.elements['name'].value;
	 var nid = frm.elements['nid'].value;
	 var errorMsg = '';
	  if (title.length == 0 ) {
		errorMsg += '名称必须填写' + '\n';
	  }
	  if (nid.length == 0 ) {
		errorMsg += '标识名必须填写' + '\n';
	  }
	  if (errorMsg.length > 0){
		alert(errorMsg); return false;
	  } else{  
		return true;
	  }
}
</script>
{/literal}
{/if}
