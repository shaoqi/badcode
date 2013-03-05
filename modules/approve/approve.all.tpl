<div class="module_add">
	<div class="module_title"><strong>总体情况</strong></div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">用户名</td>
		<td width="*" class="main_td">实名认证</td>
		<td width="*" class="main_td">学历认证</td>
		<td width="*" class="main_td">手机认证</td>
		<td width="*" class="main_td">视频认证</td>	
	</tr>
	{ list module="approve" function="GetAllList" var="loop" epage=20 page=request username=request phone=request status=request }
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{$item.user_id}</td>
		<td class="main_td1" align="center">{$item.username|default:"-"}</td>
		<td class="main_td1" align="center">{if $item.realname_status.realname!=""}{$item.realname_status.status|linkages:"approve_status"|default:"待完成"}{if $item.realname_status.status!=1}[<a href="?dyjsd&q=code/approve/realname&username={$item.username}"/>审核</a>]{/if}{else}-{/if}</td>
		<td class="main_td1" align="center">{if $item.edu_status.graduate!=""}{$item.edu_status.status|linkages:"approve_status"|default:"待完成"}{else}-{/if}</td>
		<td class="main_td1" align="center">{if $item.sms_status.status==1}{$item.sms_status.phone}{else}{$item.sms_status.status|linkages:"approve_status"|default:"待完成"}{/if}</td>
		<td class="main_td1" align="center">{if $item.video_status.addtime!=""}{$item.video_status.status|linkages:"approve_status"|default:"待完成"}{else}-{/if}</td>
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
				用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}">
				<input type="button" value="搜索" onclick="sousuo()">
			</div>
		</td>
	</tr>
	<tr align="center">
		<td colspan="10" align="center"><div align="center">{$loop.pages|showpage}</div></td>
	</tr>
	{/list}
	
</table>
</div>