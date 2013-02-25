{if $_A.query_type == "list"}

<div class="module_add">
	<div class="module_title"><strong>证明材料类型</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
	<div style="border:1px solid #CCCCCC; ">
	
	<form action="{$_A.query_url_all}" method="post">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="id" value="{$_A.attestations_type_result.id}" />修改证明材料类型 （<a href="{$_A.query_url_all}">添加</a>）{else}添加证明材料类型{/if}</strong></div>
	
	
	<div class="module_border">
		<div class="l">类型名称：</div>
		<div class="c">
			<input type="text" name="name" value="{$_A.attestations_type_result.name}"/>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">标识名：</div>
		<div class="c">
			<input type="text" name="nid" value="{$_A.attestations_type_result.nid}"/>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">描述：</div>
		<div class="c">
			<textarea name="remark" rows="5" cols="30">{$_A.attestations_type_result.remark}</textarea>
		</div>
	</div>	
	<!-- <div class="module_border">
		<div class="l">最大积分：</div>
		<div class="c">
			<input type="text" name="credit"  value="{$_A.attestations_type_result.credit}"/>
		</div>
	</div> -->
	<div class="module_border">
		<div class="l">有效期：</div>
		<div class="c">
			<input type="text" name="validity"  value="{$_A.attestations_type_result.validity}" size="5"/>月（0表示长期）
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">排序：</div>
		<div class="c">
			<input type="text" name="order" value="{$_A.attestations_type_result.order|default:10}" size="8"/>
		</div>
	</div>
	
	<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
		</form>
	</div>
	</div>
		</div>
	<div style="float:right; width:67%; text-align:left">
	<div class="module_add">
	
	
	
	<div class="module_title"><strong>证明材料列表</strong><span style="float:right">
		证明材料名称：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>   <input type="button" value="{$MsgInfo.users_name_sousuo}"  onclick="sousuo()"/></span></div>
	</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">名称</td>
		<td width="" class="main_td">标识名</td>
		<!-- <td width="*" class="main_td">最大积分</td> -->
		<td width="*" class="main_td">有效期</td>
		<td width="*" class="main_td">添加时间</td>
		<td width="*" class="main_td">排序</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="attestations" function="GetAttestationsTypeList" var="loop" username="request" epage="20"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.name}</td>
		<td class="main_td1" align="center">{$item.nid}</td>
		<!-- <td class="main_td1" align="center">{$item.credit}</td> -->
		<td class="main_td1" align="center">
		{if $item.validity==0}长期{else}{$item.validity}个月{/if}</td>
		<td class="main_td1" align="center">{$item.addtime|date_format}</td>
		<td class="main_td1" align="center">{$item.order}</td>
		<td class="main_td1" align="center"><a href="{$_A.query_url_all}&edit={$item.id}">修改</a>/<a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url_all}&del={$item.id}'">删除</a></td>
	</tr>
	{/foreach}
	<tr>
			<td colspan="11" class="action">
			<div class="floatl">
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
</div>
</div>

<!--上传相片 结束-->
{elseif $_A.query_type == "upload"}


{if $magic.request.examine!=""}

<div  style="height:205px; overflow:hidden">
	<form name="form1" method="post" action="{$_A.query_url_all}&examine={$magic.request.examine}" >
	<div class="module_border_ajax">
		<div class="l">审核:</div>
		<div class="c">
		<input type="radio" name="status" value="1"/>审核通过 <input type="radio" name="status" value="2"  checked="checked"/>审核不通过 </div>
	</div>
	
	<div class="module_border_ajax" >
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="verify_remark" cols="45" rows="7"></textarea>
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">验证码:</div>
		<div class="c">
			<input name="valicode" type="text" size="11" maxlength="4"  tabindex="3" onClick="$('#valicode1').attr('src',/?plugins&q=imgcode&t=' + Math.random())"/>
		</div>
		<div class="c">
			<img src="/?plugins&q=imgcode" id="valicode1" alt="点击刷新" onClick="this.src=/?plugins&q=imgcode&t=' + Math.random();" align="absmiddle" style="cursor:pointer" />
		</div>
	</div>

	<div class="module_submit_ajax" >
		<input type="hidden" name="borrow_nid" value="{ $magic.request.check}" />
		<input type="submit"  name="reset" class="submit_button" value="确定审核" />
	</div>
	
</form>
</div>

{elseif $magic.request.check!=""}

	<form action="" method="post">
<div class="module_add">
	<div class="module_title"><strong>用户类型图片审核</strong>(总积分：{$_A.attestations_credit_all})总计最多积分：{$_A.attestations_type_result.credit}</div>
	</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">用户</td>
		<td width="" class="main_td">类型</td>
		<td width="" class="main_td">缩略图</td>
		<td width="*" class="main_td">名称</td>
		<!-- <td width="*" class="main_td">积分</td> -->
		<td width="*" class="main_td">备注</td>
		<td width="*" class="main_td">审核备注</td>
		 
		<td width="*" class="main_td">审核状态</td>
		<!--<td width="*" class="main_td">有效期</td> 
		
		<td width="*" class="main_td">状态</td>-->
		<td width="*" class="main_td">添加时间</td>
	</tr>
	{ loop module="attestations" function="GetAttestationsList" var="item" user_id="$magic.request.user_id"  type_id="$magic.request.check" limit="all"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}<input type="hidden" name="id[]" value="{$item.id}" /></td>
		<td class="main_td1" align="center">{$item.username}</td>
		<td class="main_td1" align="center">{$item.type_name}</td>
		<td class="main_td1" align="center">{if $item.fileurl==""}-{else}<a href="{$item.fileurl}" target="_blank"><img src="{$item.fileurl|litpic:40,40}" height="40" width="40"/></a>{/if}</td>
		<td class="main_td1" align="center">{$item.name}</td>
		<!-- <td class="main_td1" align="center"><input type="text" size="6"  value="{$item.credit}" name="credit[]" /></td> -->
		<td class="main_td1" align="center">{$item.remark}</td>
		<td class="main_td1" align="center"><input type="text" size="6"  value="{$item.verify_remark|default:'已审核'}" name="verify_remark[]" /></td>
		 <td class="main_td1" align="center"><select name="status[]"><option value="1" {if $item.status==1} selected="selected"{/if}>审核通过</option><option value="2" {if $item.status==2} selected="selected"{/if}>审核不通过</option></select></td>
		<!--<td class="main_td1" align="center">{if $item.validity_time==0}长期{elseif $item.validity_time==-1}已过期{else}{$item.validity_time|date_format:"Y-m-d"}{/if}</td> 
		<td class="main_td1" align="center">{if $item.status==0}未审核{elseif $item.status==1}审核通过{else}审核不通过{/if}</td>-->
		<td class="main_td1" align="center">{$item.addtime|date_format}</td>
	</tr>
	{/loop}
	
	<tr align="center">
		<td colspan="10" align="center"><div align="center"><!-- <select name="type_status"><option value="1">已通过</option><option value="2">不通过</option></select> --><input type="hidden" name="user_id" value="{$item.user_id}" /><input type="submit"  name="reset" class="submit_button" value="提交审核" /></div></td>
	</tr>
	</form>
</table>
<!-- 
<font style="color:red">温馨提示：材料如审核通过，填写的积分值一定要大于0，否则系统视为不通过。</font> -->
{else}
<div class="module_add">
	<div class="module_title"><strong>上传单个图片</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
	<div style="border:1px solid #CCCCCC; ">
	
	{if $magic.request.user_id==""}
	<form action="{$_A.query_url_all}" method="post">
	<div class="module_title"><strong>查找用户</strong>(将按顺序进行搜索)<input type="hidden" name="type" value="user_id" /></div>
	
	
	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="c">
			<input type="text" name="username" />
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">用户ID：</div>
		<div class="c">
			<input type="text" name="user_id" />
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">邮箱：</div>
		<div class="c">
			<input type="text" name="email" />
		</div>
	</div>
	
	<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
		</form>
	</div>
	{else}
	{if $magic.request.edit==""}
	<form action="{$_A.query_url_all}" method="post" enctype="multipart/form-data">
	<div class="module_title"><strong>上传图片</strong><input type="hidden" name="user_id" value="{$magic.request.user_id}" /></div>
	
	<div class="module_border">
		<div class="l">所属相册：</div>
		<div class="c">
			<select name="type_id">
			{loop module="attestations" function="GetAttestationsTypeList" limit="all" }
			<option value="{$var.id}">{$var.name}</option>
			{/loop}
			</select>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">相册1：</div>
		<div class="c" style=" width:100px">
			<input name="pic[]" type="file" />
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">相册2：</div>
		<div class="c" style=" width:100px">
			<input name="pic[]" type="file" />
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">相册3：</div>
		<div class="c" style=" width:100px">
			<input name="pic[]" type="file" />
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">相册4：</div>
		<div class="c" style=" width:100px">
			<input name="pic[]" type="file" />
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">相册5：</div>
		<div class="c" style=" width:100px">
			<input name="pic[]" type="file" />
		</div>
	</div>
	
	<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
		</form>
	</div>
	{else}
	<form action="" method="post" enctype="multipart/form-data">
	<div class="module_title"><strong>修改相册</strong><input type="hidden" name="user_id" value="{$magic.request.user_id}" /><input type="hidden" name="id" value="{$_A.attestations_result.id}" /><input type="hidden" name="upfiles_id" value="{$_A.attestations_result.upfiles_id}" /></div>
	
	<div class="module_border">
		<div class="l">相片ID：</div>
		<div class="c" style=" width:100px">
			{$_A.attestations_result.id}
		</div>
	</div>
	<div class="module_border">
		<div class="l">所属用户：</div>
		<div class="c" style=" width:100px">
			{$_A.attestations_result.username}
		</div>
	</div>
	<div class="module_border">
		<div class="l">名称：</div>
		<div class="c" style=" width:100px">
			<input name="name" type="text" value="{$_A.attestations_result.name}" />
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">类型：</div>
		<div class="c">
			<select name="type_id">
			{loop module="attestations" function="GetAttestationsTypeList" limit="all"}
			<option value="{$var.id}" {if $_A.attestations_result.type_id==$var.id} selected="selected"{/if}>{$var.name}</option>
			{/loop}
			</select>
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">相片：</div>
		<div class="c" style=" width:100px">
			<a href="{$_A.attestations_result.fileurl}" target="_blank"><img src="{$_A.attestations_result.fileurl}" height="50" /></a>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">排序：</div>
		<div class="c" style=" width:100px">
			<input name="order" type="text" value="{$_A.attestations_result.order}" />
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">简介：</div>
		<div class="c" style=" width:100px">
			<input name="contents" type="text" value="{$_A.attestations_result.contents}" />
		</div>
	</div>
	
	
	<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
		</form>
	</div>
	{/if}
	{/if}
	</div>
		</div>
	<div style="float:right; width:67%; text-align:left">
	<div class="module_add">
	<div class="module_title"><strong>图片列表</strong><span style="float:right">
	类型：<select name="type_id" id="type_id">
			<option value="" {if $magic.request.type_id==""}selected="selected"{/if}>不限</option>
			{loop module="attestations" function="GetAttestationsTypeList" var="Tvar" limit="all"}
			<option value="{$Tvar.id}" {if $Tvar.id==$magic.request.type_id}selected="selected"{/if}>{$Tvar.name}</option>
			{/loop}
		  </select>
	状态：<select name="status" id="status">
			<option value="" {if $magic.request.status==""}selected="selected"{/if}>不限</option>
			<option value="0" {if $magic.request.status=="0"}selected="selected"{/if}>未审核</option>
			<option value="1" {if $magic.request.status=="1"}selected="selected"{/if}>审核通过</option>
			<option value="2" {if $magic.request.status=="2"}selected="selected"{/if}>审核不通过</option>
		  </select>
	用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>   <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()"></span></div>
	</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">用户</td>
		<td width="" class="main_td">类型</td>
		<td width="" class="main_td">缩略图</td>
		<td width="*" class="main_td">名称</td>
		<!-- <td width="*" class="main_td">积分</td> -->
		<td width="*" class="main_td">状态</td>
		<td width="*" class="main_td">添加时间</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="attestations" function="GetAttestationsList" var="loop" username=request user_id="$magic.request.user_id" epage=20 type_id="request" order="status" status="request"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.username}</td>
		<td class="main_td1" align="center">{$item.type_name}</td>
		<td class="main_td1" align="center">{if $item.fileurl==""}-{else}<a href="{$item.fileurl}" target="_blank"><img src="{$item.fileurl|litpic:40,40}" height="40" width="40"/></a>{/if}</td>
		<td class="main_td1" align="center">{$item.name}</td>
		<!-- <td class="main_td1" align="center">{$item.credit}</td> -->
		<td class="main_td1" align="center">{if $item.status==0}<font color="red">未审核</font>{elseif $item.status==1}审核通过{else}审核不通过{/if}</td>
		<td class="main_td1" align="center">{$item.addtime|date_format}</td>
		<td class="main_td1" align="center"><a href="{$_A.query_url_all}&user_id={$item.user_id}&check={$item.type_id}">审核</a>/<a href="{$_A.query_url_all}&user_id={$item.user_id}&edit={$item.id}&page={$magic.request.page}">修改</a>/<a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url_all}&user_id={$item.user_id}&del={$item.id}'">删除</a></td>
	</tr>
	{/foreach}
	<tr>
			<td colspan="11" class="action">
			<div class="floatl">
			<script>
	  var url = '{$_A.query_url_all}';
	    {literal}
	  	function sousuo(){
			var username = $("#username").val();
			var type_id = $("#type_id").val();
			var status = $("#status").val();
			location.href=url+"&username="+username+"&status="+status;
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
</div>
</div>
{/if}


{elseif $_A.query_type=="uploads"}

<div class="module_add">
	<div class="module_title"><strong>多图片上传</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
	<div style="border:1px solid #CCCCCC; ">
	
	<form action="{$_A.query_url_all}" method="post">
	<div class="module_title"><strong>查找用户</strong>(将按顺序进行搜索)<input type="hidden" name="type" value="user_id" /></div>
	
	
	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="c">
			<input type="text" name="username"  value="{$_A.user_result.username}"/>
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">用户ID：</div>
		<div class="c">
			<input type="text" name="user_id" />
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">邮箱：</div>
		<div class="c">
			<input type="text" name="email" />
		</div>
	</div>
	
	<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
		</form>
	</div>
	
	</div>
		</div>
	<div style="float:right; width:67%; text-align:left">
	<div class="module_add">
	<div class="module_title"><strong>上传图片</strong></div>
	</div>
	
	{if $magic.request.user_id!=""}
	<div>
		<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="700" height="500">
  <param name="movie" value="/plugins/swfupload/swfupload.swf?config=/{$_A.admin_url|urlencode}%26q=plugins%26ac=swfupload%26code=attestations%26user_id={$magic.request.user_id}" />
  <param name="quality" value="high" />
  <embed src="/plugins/swfupload/swfupload.swf?config=/{$_A.admin_url|urlencode}%26q=plugins%26ac=swfupload%26code=attestations%26user_id={$magic.request.user_id}" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="700" height="500"></embed>
</object>
	
	</div>
	{else}
	<div class="help">请先从左边选择用户</div>
	{/if}
<!--菜单列表 结束-->
</div>
</div>
{/if}
