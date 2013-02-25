
<!--管理记录列表 开始-->
{if $_A.query_type == "admin_log"}
<div class="module_add">
	<div class="module_title"><strong>管理员记录</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">{$MsgInfo.users_name_id}</td>
		<td width="*" class="main_td">{$MsgInfo.users_name_operatinger}</td>
		<td width="*" class="main_td">{$MsgInfo.users_name_code}</td>
		<td width="*" class="main_td">{$MsgInfo.users_name_type}</td>
		<td width="*" class="main_td">{$MsgInfo.users_name_operating}</td>
		<td width="*" class="main_td">{$MsgInfo.users_name_operating_id}</td>
		<td width="*" class="main_td">{$MsgInfo.users_name_result}</td>
		<th width="" class="main_td">{$MsgInfo.users_name_content}</th>
		<th width="" class="main_td">{$MsgInfo.users_name_add_time}</th>
		<th width="" class="main_td">{$MsgInfo.users_name_add_ip}</th>
	</tr>
	{ list module="users" function="GetAdminlogList" var="loop" username=request email=request epage="12" page="request"}
		{foreach from=$loop.list item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.username|default:-}</td>
		<td class="main_td1" align="center">{$item.code}</td>
		<td class="main_td1" align="center" >{$item.type}</td>
		<td class="main_td1" align="center" >{$item.operating}</td>
		<td class="main_td1" align="center" >{$item.article_id}</td>
		<td class="main_td1" align="center" >{if $item.result==1}<font color="#006600">{$MsgInfo.users_name_success}</font>{else}<font color="#FF0000">{$MsgInfo.users_name_false}</font>{/if}</td>
		<td class="main_td1" align="center" width="200" >{$item.content}</td>
		<td class="main_td1" align="center" >{$item.addtime|date_format:"Y-m-d H:i:s"}</td>
		<td class="main_td1" align="center" >{$item.addip}</td>
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
			var email = $("#email").val();
			location.href=url+"&username="+username+"&email="+email;
		}
	  
	  </script>
	  {/literal}
			</div>
			<div class="floatr">
				{$MsgInfo.users_name_username}：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>  {$MsgInfo.users_name_email}：<input type="text" name="email" id="email" value="{$magic.request.email}"/>   <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()">
			</div>
			</td>
		</tr>
	<tr align="center">
		<td colspan="10" align="center"><div align="center">{$loop.pages|showpage}</div></td>
	</tr>
	{ /list}
</table>
<!--管理记录列表 结束-->


<!--管理管理 开始-->
{elseif $_A.query_type=="admin"}

<div class="module_add">
	<div class="module_title"><strong>管理员信息</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
	<div style="border:1px solid #CCCCCC; ">
	
	{if $magic.request.user_id==""}
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
	{else}
	
	<form action="{$_A.query_url_all}&user_id={$maigc.request.user_id}" method="post">
	<div class="module_title"><strong>管理员设置</strong></div>
	
	
	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="c">
			{$_A.users_result.username}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">用户别名：</div>
		<div class="c">
			<input type="text" name="adminname" value="{$_A.users_admin_result.adminname}" />
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">管理密码：</div>
		<div class="c">
			<input type="password" name="password" value="" />
		</div>
	</div>
	
	
	
	<div class="module_border">
		<div class="l">管理QQ：</div>
		<div class="c">
			<input type="text" name="qq" value="{$_A.users_admin_result.qq}" />
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">管理电话：</div>
		<div class="c">
			<input type="text" name="phone" value="{$_A.users_admin_result.phone}" />
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">所在地：</div>
		<div class="c">
			{areas type="p,c" value='$_A.users_admin_result.city'}
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">类型：</div>
		<div class="c">
			{select table="users_admin_type" name="name" value="id" select_name="type_id" selected="$_A.users_admin_result.type_id"}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">管理员备注：</div>
		<div class="c">
			<textarea name="remark" cols="30" rows="4">{$_A.users_admin_result.remark}</textarea>
		</div>
	</div>
	
	<div class="module_submit"><input type="hidden" name="action" value="update" /><input type="hidden" name="user_id" value="{$magic.request.user_id}" /><input type="submit" value="确认提交" class="submit_button" /></div>
		</form>
	</div>
	
	{/if}
	</div>
		</div>
	<div style="float:right; width:67%; text-align:left">
	
	<div class="module_add">
		<div class="module_title"><strong>管理员列表</strong></div>
	</div>
	<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
		  <form action="" method="post">
			<tr >
				<td class="main_td">ID</td>
				<td class="main_td">用户名</td>
				<td class="main_td">管理别名</td>
				<td class="main_td">管理类型</td>
				<td class="main_td">操作</td>
			</tr>
			{ list module="users" function="GetUsersAdminList" var="loop" keywords="request"}
			{foreach from=$loop.list item="item"}
			<tr  {if $key%2==1} class="tr2"{/if}>
				<td >{ $item.id}</td>
				<td >{$item.username}</td>
				<td >{$item.adminname}</td>
				<td >{$item.type_name}</td>
				<td ><a href="{$_A.query_url_all}&action=edit&user_id={$item.user_id}">修改</a> | <a href="{$_A.query_url_all}&action=del&user_id={$item.user_id}">删除</a> </td>
			</tr>
			{ /foreach}
			
			<tr>
				<td colspan="9" class="page">
				{$loop.pages|showpage} 
				</td>
			</tr>
			{/list}
		</form>	
	</table>
</div>



<!--管理类型列表 开始-->
{elseif $_A.query_type=="admin_type"}
	
	<ul class="nav3"> 
		<li><a href="{$_A.query_url}/admin_type" {if $magic.request.action==""} id="c_so"{/if}>类型列表</a></li> 
		<li><a href="{$_A.query_url}/admin_type&action=new" id="c_so" {if $magic.request.action=="new"}style="color:red"{/if}>添加管理员类型</a></li> 
	</ul> 
	
	{if $magic.request.action=="new" || $magic.request.action=="edit"}
	<div class="module_add">
		<div style="margin-top:10px;">
		<form action="{$_A.query_url_all}" method="post">
		<div class="module_title"><strong>{if $magic.request.action=="edit"}<input type="hidden" name="id" value="{$_A.admin_type_result.id}" />修改管理员类型 （<a href="{$_A.query_url_all}">添加</a>）{else}添加管理员类型{/if}</strong></div>
		
		
		<div class="module_border">
			<div class="l">类型名称：</div>
			<div class="c">
				<input type="text" name="name" value="{$_A.admin_type_result.name}"/>
			</div>
		</div>
		
		<div class="module_border">
			<div class="l">标识名：</div>
			<div class="c">
				<input type="text" name="nid" value="{$_A.admin_type_result.nid}"/>
			</div>
		</div>
		
		<div class="module_border">
			<div class="l">描述：</div>
			<div class="c">
				<textarea name="remark" rows="5" cols="30">{$_A.admin_type_result.remark}</textarea>
			</div>
		</div>
		
		<div class="module_border">
			<div class="l">排序：</div>
			<div class="c">
				<input type="text" name="order" value="{$_A.admin_type_result.order|default:10}" size="8"/>
			</div>
		</div>
		
		<div class="module_border">
			<div class="l">权限:</div>
			<div class="c" style="overflow:hidden">
					{loop module="admin" function="GetModulePurview" var=item }
						<div style="height:auto;  overflow:hidden">
						 <div style="; border-top:1px solid #CCCCCC; height:28px; padding-top:5px"><strong>{$item.name}</strong>
						 <input type="checkbox" value="{$key}" name="module[]"  {if $magic.request.action == "edit" }{$_A.admin_type_result.module|checked:$key}{/if} /></div>
						 <div style="border-top:1px dashed #CCCCCC;  padding-top:5px">
							{ foreach from=$item.result key=_key item=_item}
							<div style="float:left; width:140px; height:28px;padding-top:3px" title="{$__key}"><input type="checkbox" value="{$_key}" name="purview[]" id="{ $key}" {if $magic.request.action == "edit"  }{$_A.admin_type_result.purview|checked:$_key}{/if}  /> {$_item.name}
							</div>
							{/foreach}
						</div>
						</div>
					{/loop}
				</div>
		</div>
		
		
		<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
			</form>
		</div>
		</div>
	</div>

	{else}
	<div class="module_add">
		<div class="module_title"><strong>管理员类型列表</strong></div>
	</div>
	<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
		<tr >
			<td width="" class="main_td">ID</td>
			<td width="" class="main_td">名称</td>
			<td width="" class="main_td">标识名</td>
			<td width="*" class="main_td">添加时间</td>
			<td width="*" class="main_td">排序</td>
			<td width="*" class="main_td">操作</td>
		</tr>
		{ list module="users" function="GetAdminTypeList" var="loop" username=request }
		{foreach from="$loop.list" item="item"}
		<tr {if $key%2==1} class="tr2"{/if}>
			<td class="main_td1" align="center">{ $item.id}</td>
			<td class="main_td1" align="center">{$item.name}</td>
			<td class="main_td1" align="center">{$item.nid}</td>
			<td class="main_td1" align="center">{$item.addtime|date_format}</td>
			<td class="main_td1" align="center">{$item.order}</td>
			<td class="main_td1" align="center"><a href="{$_A.query_url_all}&action=edit&id={$item.id}">修改</a>/<a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url_all}&action=del&id={$item.id}'">删除</a></td>
		</tr>
		{/foreach}
		
		<tr align="center">
			<td colspan="10" align="center"><div align="center">{$loop.pages|showpage}</div></td>
		</tr>
		{/list}
	</table>
	{/if}
	<!--添加管理类型 结束-->
	
	
{/if}
