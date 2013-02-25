
<div class="module_add">
	<div class="module_title"><strong>账号信息管理</strong><div style="float:right">
			用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/> <input type="button" value="搜索" / onclick="sousuo()"> </div></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr >
			<td class="main_td">ID</td>
			<td class="main_td">用户名</td>
			<td class="main_td" title="可用金额+冻结金额+待收金额">资产总额 </td>
			<td class="main_td">可用金额</td>
			<td class="main_td">冻结金额</td>
			<td class="main_td">待收金额</td>
			<td class="main_td">待还金额</td>
			<td class="main_td">操作</td>
		</tr>
		{list module="account" function="GetList"  var="loop" username=request epage="20"}
		{foreach from=$loop.list item="item"}
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td >{$item.id}</td>
			<td ><a href="{$_A.admin_url}&q=code/users/info_view&user_id={$item.user_id}" title="查看">{$item.username}</a></td>
			<td >￥{$item.total}</td>
			<td >￥{$item.balance}</td>
			<td >￥{$item.frost}</td>
			<td >￥{$item.await}</td>
			<td >￥{$item.repay}</td>
			<td ><a href="{$_A.query_url}/recharge&username={$item.username}" >充值记录</a> <a href="{$_A.query_url}/cash&username={$item.username}" >提现记录</a> <a href="{$_A.query_url}/log&username={$item.username}" >资金记录</a></td>
		</tr>
		{/foreach}
		<tr>
		<td colspan="12" class="action">
			<span>总可用金额:{$loop.total_balance} </span>
			<span>总冻结金额:{$loop.total_frost} </span>
		<div class="floatr">
			<a href="{$_A.query_url_all}&type=excel&page={$magic.request.page|default:1}&username={$magic.request.username}&epage=20">导出当前</a> <a href="{$_A.query_url_all}&type=excel&username={$magic.request.username}">导出全部</a>&nbsp;&nbsp;&nbsp;
		</div>
		</td>
	</tr>
		<tr>
			<td colspan="12" class="page">
			{$loop.pages|showpage} 
			</td>
		</tr>
		{/list}
	</form>	
</table>

