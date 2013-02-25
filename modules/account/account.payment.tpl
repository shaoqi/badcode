<ul class="nav3"> 
<li><a href="{$_A.query_url}/payment&type=list" {if $magic.request.type=="list" || $magic.request.type==""}style="color:red"{/if}>{$MsgInfo.payment_name_list}</a></li> 
<li><a href="{$_A.query_url}/payment&type=all" {if $magic.request.type=="all"}style="color:red"{/if}>{$MsgInfo.payment_name_all}</a></li> 
</ul> 

{if $magic.request.type == "new" || $magic.request.type == "edit" || $magic.request.type == "start" }
<div class="module_add">
<form name="form1" method="post" action=""  enctype="multipart/form-data">
	<div class="module_title"><strong>{ if $magic.request.type == "edit" }{$MsgInfo.payment_name_edit}{else}{$MsgInfo.payment_name_new}{/if}</strong></div>
	
	
	<div class="module_border">
		<div class="w"><!-- {$MsgInfo.payment_name_name} -->银行名称：</div>
		<div class="c">
			<input type="text" name="name"  class="input_border" value="{ $_A.payment_result.name}" size="30" />
		</div>
	</div>
	
	<div class="module_border" >
		<div class="w">{$MsgInfo.payment_name_litpic}：</div>
		<div class="c">
			<input type="file" name="litpic" size="30" class="input_border"/>{if $_A.payment_result.litpic!=""}<a href="./{ $_A.payment_result.litpic}" target="_blank" title="有图片">查看</a><input type="checkbox" name="clearlitpic" value="1" />去掉缩略图{/if}</div>
	</div>
	
	{foreach from="$_A.payment_result.fields" item="item" }
	<div class="module_border">
		<div class="w">{$item.label}</div>
		<div class="c">
			{if $item.type=="string"}
			<input type="text" name="config[{$key}]"  class="input_border" value="{ $item.value}" size="30" />
			{elseif $item.type=="select"}
			<select name="config[{$key}]">
				{foreach from="$item.options" key="_key" item="var"}
				<option value="{$_key}" {if $item.value==$_key} selected="selected"{/if}>{$var}</option>
				{/foreach}
			</select>
			{/if}
		</div>
	</div>
	{/foreach}
	
	
	<div class="module_border">
		<div class="w">{$MsgInfo.payment_name_order}:</div>
		<div class="c">
			<input type="text" name="order"  class="input_border" value="{ $_A.payment_result.order|default:10}" size="10" />
		</div>
	</div>

	
	<div class="module_border">
		<div class="w"><!-- {$MsgInfo.payment_name_description} -->银行明细：</div>
		<div class="c">
		<textarea id="bcontents" name="description"  style="width:750px;height:500px;visibility:hidden;">{$_A.payment_result.description}</textarea>	
		{literal}
			<script src="/plugins/dyeditor/dyeditor.js" type="text/javascript"></script>
			<script src="/plugins/dyeditor/lang/cn.js" type="text/javascript"></script><script>
			var editor;
			DyEditor.ready(function(D) {
			editor = D.create('#bcontents',{filterMode : true});
			})</script>
				{/literal}
		</div>
	</div>
	
	<div class="module_submit" >
		<input type="hidden" name="nid" value="{ $_A.payment_result.nid }" />
		<input type="hidden" name="status" value="{ $_A.payment_result.status|default:1 }" />
		<input type="hidden" name="type" value="{ $_A.payment_result.type }" />
		{if $magic.request.type == "edit"}
		<input type="hidden" name="id" value="{ $magic.request.id }" />
		{/if}
		<input type="submit"  name="submit" value="{$MsgInfo.payment_name_submit}" />
		<input type="reset"  name="reset" value="{$MsgInfo.payment_name_reset}" />
	</div>
	
</div>
</form>
{literal}
<script>
function change(type){
	if (type==1){
		$("#fee").hide();
		$("#fee_money").show();
	}else{
		$("#fee_money").hide();
		$("#fee").show();
	}

}
function check_form(){
/*
	 var frm = document.forms['form1'];
	 var title = frm.elements['name'].value;
	 var errorMsg = '';
	  if (title.length == 0 ) {
		errorMsg += '标题必须填写' + '\n';
	  }
	  if (errorMsg.length > 0){
		alert(errorMsg); return false;
	  } else{  
		return true;
	  }
	  */
}

</script>
{/literal}

{elseif $magic.request.type == "all" }

<div class="module_add">
	<div class="module_title"><strong>{$MsgInfo.payment_name_all}</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<form action="{$_A.query_url}/payment&type=action" method="post">
	<tr >
		<td width="*" class="main_td">{$MsgInfo.payment_name_logo}</td>
		<td width="*" class="main_td">{$MsgInfo.payment_name_name}</td>
		<td width="*" class="main_td">{$MsgInfo.payment_name_description}</td>
		<td width="" class="main_td">{$MsgInfo.payment_name_manage}</td>
	</tr>
	{ foreach  from=$_A.payment_list key=key item=item}
		<tr class="tr1">
		<td><img src="{if $item.litpic==""}{ $item.logo}{else}{ $item.litpic}{/if}"  /></td>
		<td>{$item.name}</td>
		<td>{$item.description}</td>
		<td>{if $item.type==1}<a href="{$_A.query_url}/payment&type=start&nid={$item.nid}" >{$MsgInfo.payment_name_open}</a>{else}<a href="{$_A.query_url}/payment&type=new&nid={$item.nid}" >{$MsgInfo.payment_name_new}</a>{/if}</td>
		</tr>
		{ /foreach}
		
	</form>	
</table>

{elseif $magic.request.type == "list" || $magic.request.type == ""  }
<div class="module_add">
	<div class="module_title"><strong>{$MsgInfo.payment_name_list}</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<form action="{$_A.query_url}/payment&type=action" method="post">
	<tr >
		<td width="*" class="main_td">{$MsgInfo.payment_name_logo}</td>
		<td width="*" class="main_td">{$MsgInfo.payment_name_name}</td>
		<td width="*" class="main_td">{$MsgInfo.payment_name_description}</td>
		<td width="" class="main_td">{$MsgInfo.payment_name_manage}</td>
	</tr>
	{ foreach  from=$_A.payment_list key=key item=item}
		<tr class="tr1">
		<td><img src="{if $item.litpic==""}{ $item.logo}{else}{ $item.litpic}{/if}" /></td>
		<td>{$item.name}</td>
		<td>{$item.description}</td>
		<td><a href="{$_A.query_url}/payment&type=edit&nid={$item.nid}&id={$item.id}" >{$MsgInfo.payment_name_edit}</a> |  <a href="#" onClick="javascript:if(confirm('{$MsgInfo.payment_name_del_msg}')) location.href='{$_A.query_url}/payment&type=del&id={$item.id}'">{$MsgInfo.payment_name_del}</a> | {if $item.status==1}<a href="{$_A.query_url}/payment&type=list&nid={$item.nid}&id={$item.id}&status=0" >{$MsgInfo.payment_name_close}</a>{else}<a href="{$_A.query_url}/payment&type=list&nid={$item.nid}&id={$item.id}&status=1" >{$MsgInfo.payment_name_open}</a>{/if} </td>
		</tr>
		{ /foreach}
		
	</form>
</table>
{/if}