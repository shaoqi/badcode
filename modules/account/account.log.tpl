<div class="module_add">
	<div class="module_title">	<strong><font color="#FF0000">{$magic.request.username|urldecode}</font>{$MsgInfo.account_name_log}</strong> <div style="float:right"><a href="{$_A.query_url_all}&type=excel&page={$magic.request.page|default:1}&username={$magic.request.username}&dotime1={$magic.request.dotime1}&dotime2={$magic.request.dotime2}&epage=15&_type={$magic.request.type}">导出当前</a> <a href="{$_A.query_url_all}&type=excel&username={$magic.request.username}&dotime1={$magic.request.dotime1}&dotime2={$magic.request.dotime2}&_type={$magic.request.type}">导出全部</a>
		&nbsp;&nbsp;&nbsp;</div></div>
</div>
	<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr >
			<td class="main_td">ID</td>
			<td class="main_td">用户名</td>
			<td class="main_td">类型</td>
			<td class="main_td">操作金额</td>
			<td class="main_td">收入</td>
			<td class="main_td">支出</td>
			<td class="main_td" title="=可用余额+冻结总额+待收总额">账户总额</td>
			<td class="main_td">备注</td>
			<td class="main_td">操作时间</td>
		</tr>
		{ list module="account" function="GetLogList" var="loop" type="request" username=request email=request status=request order=request dotime1="request" dotime2="request" epage="15"}
		{foreach from=$loop.list item="item"}
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td >{ $item.id}</td>
			<td><a href="{$_A.query_url}/log&username={$item.username}">{$item.username}</a></td>
			<td>{$item.type|linkages:"account_type"|default:"$item.type"}</td>
			<td >￥{$item.money}</td>
			<td >{if $item.income_new>0}￥{$item.income_new}{else}-{/if}</td>
			<td >{if $item.expend_new>0}￥{$item.expend_new}{else}-{/if}</td>
			<td title="可用金额[￥{$item.balance}]+待收金额[￥{$item.await}]+冻结金额[￥{$item.frost}]" >￥{$item.total}</td>
			<!--<td >￥{$item.repay}</td>-->
			<td >{$item.remark}</td>
			<td >{$item.addtime|date_format:"Y-m-d H:i:s"}</td>
		</tr>
		{ /foreach}
		<tr>
			<td colspan="14" class="action">
			<div class="floatl">
			
			</div>
			<div class="floatr" style="width:1120px;">
			<div style="float:left;"><span>总额:{$loop.total_num} </span><span>总收入:{$loop.total_income}</span><span>总支出:{$loop.total_expend}</span></div>
			<div style="float:right">资金类型:{linkages default="全部" name="type" nid="account_type" type="value" style="width:120px; value="$magic.request.type"}	用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}" style="width:120px;"/>
				操作时间：<input type="text" name="dotime1" id="dotime1" value="{$magic.request.dotime1|default:"$day7"|date_format:"Y-m-d"}" size="15" onclick="change_picktime()"/> 到 <input type="text"  name="dotime2" value="{$magic.request.dotime2|default:"$nowtime"|date_format:"Y-m-d"}" id="dotime2" size="15" onclick="change_picktime()"/> <input type="button" value="搜索" / onclick="sousuo()"> 
			</div>
			</td>
		</tr>
		<tr>
			<td colspan="14" class="page">
			{$loop.pages|showpage} 
			</td>
		</tr>
		{/list}
	</form>
	</table>
