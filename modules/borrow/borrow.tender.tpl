
{if $magic.request.cancel!=""}
<div  style="height:205px; overflow:hidden">
	<form name="form1" method="post" action="{$_A.query_url_all}&cancel={$magic.request.cancel}" >
	
	
	<div class="module_border_ajax" >
		<div class="l">撤销理由:</div>
		<div class="c">
			<textarea name="remark" cols="45" rows="7">{ $_A.borrow_result.reverify_remark}</textarea><br />请将撤回的理由写清楚
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">验证码:</div>
		<div class="c">
			<input name="valicode" type="text" size="11" maxlength="4"  tabindex="3" onClick="$('#valicode').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
		</div>
		<div class="c">
			<img src="/?plugins&q=imgcode" id="valicode" alt="点击刷新" onClick="this.src='/?plugins&q=imgcode&t=' + Math.random();" align="absmiddle" style="cursor:pointer" />
		</div>
	</div>

	<div class="module_submit_ajax" >
		<input type="hidden" name="tender_nid" value="{ $magic.request.cancel}" />
		<input type="submit"  name="reset" class="submit_button" value="审核此标" />
	</div>
	
</form>
</div>
{elseif $magic.request.id!=""}

<div class="module_add" >
	
	<div class="module_title"><strong>投资详细信息</strong></div>


	<div class="module_border">
		<div class="l">投资人：</div>
		<div class="r">
		<a href="javascript:void(0)" onclick='tipsWindown("用户详细信息查看","url:get?{$_A.admin_url}&q=module/user/view&user_id={$_A.borrow_tender_result.user_id}&type=scene",500,230,"true","","true","text");'>	{$_A.borrow_tender_result.username}</a>
		</div>
		<div class="s"></div>
		<div class="c">
			
		</div>
	</div>
	

	<div class="module_border">
		<div class="l">投资状态：</div>
		<div class="r">
			{$_A.borrow_tender_result.status|linkages:"borrow_tender_status"}<!--{if $_A.borrow_tender_result.status==1 && $_A.borrow_tender_result.tender_status==0} <input type="button"  src="{$_A.tpldir}/images/button.gif" align="absmiddle" value="投资撤回" class="submit_button" onclick='tipsWindown("投资撤回","url:get?{$_A.query_url_all}&cancel={$_A.borrow_tender_result.id}",500,230,"true","","false","text");'/>{/if}-->
		</div>
		<div class="s">审核状态：</div>
		<div class="c">
			{$_A.borrow_tender_result.tender_status|linkages:"borrow_tender_verify_status"} 
		</div>
	</div>
	{if $_A.borrow_tender_result.tender_status==1} 
	<div class="module_title"><strong>投资成功信息</strong></div>
	<div class="module_border">
		<div class="l">投资金额：</div>
		<div class="r">
		￥{$_A.borrow_tender_result.account_tender}
		</div>
		<div class="s">收款总额：</div>
		<div class="c">
			<strong>￥{$_A.borrow_tender_result.account}</strong>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">已收总额：</div>
		<div class="r">
		￥{$_A.borrow_tender_result.account_tender}
		</div>
		<div class="s">未收总额：</div>
		<div class="c">
			<strong>￥{$_A.borrow_tender_result.account}</strong>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">已收本金：</div>
		<div class="r">
		￥{$_A.borrow_tender_result.account_tender}
		</div>
		<div class="s">未收本金：</div>
		<div class="c">
			<strong>￥{$_A.borrow_tender_result.account}</strong>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">已收利息：</div>
		<div class="r">
		￥{$_A.borrow_tender_result.account_tender}
		</div>
		<div class="s">未收利息：</div>
		<div class="c">
			<strong>￥{$_A.borrow_tender_result.account}</strong>
		</div>
	</div>
	{/if}
	<div class="module_border">
		<div class="l">投资金额：</div>
		<div class="r">
		￥{$_A.borrow_tender_result.account_tender}
		</div>
		<div class="s">实投金额：</div>
		<div class="c">
			<font color="#FF0000"><strong>￥{$_A.borrow_tender_result.account}</strong></font>
		</div>
	</div>
	
	

	<div class="module_border">
		<div class="l">是否自动投标：</div>
		<div class="r">
		{$_A.borrow_tender_result.auto_status|linkages:"borrow_tender_auto_status"}
		</div>
		<div class="s">投资理由：</div>
		<div class="c">
			{$_A.borrow_tender_result.contents}
		</div>
	</div>
	
	

	<div class="module_border">
		<div class="l">投资时间：</div>
		<div class="r">
		{$_A.borrow_tender_result.addtime|date_format}
		</div>
		<div class="s">投资IP：</div>
		<div class="c">
			{$_A.borrow_tender_result.addip}
		</div>
	</div>
	
	
	<div class="module_title"><strong>借款详细信息</strong></div>
	<div class="module_border">
		
		<div class="l">标题：</div>
		<div class="r">
			<strong><a href="{$_A.query_url}&view={$_A.borrow_tender_result.borrow_nid}">{$_A.borrow_tender_result.borrow_name}</a></strong>
		</div>
		
		<div class="s">贷款号：</div>
		<div class="c">
			{$_A.borrow_tender_result.borrow_nid}
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">借贷总金额：</div>
		<div class="r">
			￥{$_A.borrow_tender_result.borrow_account}
		</div>
		<div class="s">借款用途：</div>
		<div class="c">
			{$_A.borrow_tender_result.borrow_use|linkages:"borrow_use"}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">借款类型：</div>
		<div class="r">
			{$_A.borrow_tender_result.borrow_flag|linkages:"borrow_flag"|default:"-"}
		</div>
		
		<div class="s">还款方式：</div>
		<div class="c">
			{$_A.borrow_tender_result.borrow_style|linkages:"borrow_style"}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">借款期限：</div>
		<div class="r">
				{$_A.borrow_tender_result.borrow_period}个月
		</div>
		
		<div class="s">年利率：</div>
		<div class="c">
			{$_A.borrow_tender_result.borrow_apr} %
		</div>

	</div>
</div>

<div class="module_add">
	<div class="module_title"><strong>{$_A.borrow_tender_result.borrow_name}</strong> 投资列表</div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr >
			<td width="*" class="main_td">投资人</td>
			<td width="*" class="main_td">投资金额</td>
			<td width="" class="main_td">投资时间</td>
			<td width="" class="main_td">审核状态</td>
			<td width="" class="main_td">投资状态</td>
			<td width="" class="main_td">投资理由</td>
			<td width="" class="main_td">自动投标</td>
		</tr>
		{ loop  module="borrow" plugins="tender" function="GetTenderList" var="item" borrow_name="request"  username="request"  limit="all" borrow_nid=$_A.borrow_tender_result.borrow_nid}
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td class="main_td1" align="center"><a href="javascript:void(0)" onclick='tipsWindown("用户详细信息查看","url:get?{$_A.admin_url}&q=module/user/view&user_id={$item.user_id}&type=scene",500,230,"true","","true","text");'>	{$item.username}</a></td>
			<td>￥{$item.account}</td>
			<td>{$item.addtime|date_format}</td>
			<td>{$item.status|linkages:"borrow_tender_status"}</td>
			<td>{$item.tender_status|linkages:"borrow_tender_verify_status"}</td>
			<td>{$item.contents|default:"-"}</td>
			<td>{$item.auto_status|linkages:"borrow_tender_auto_status"}</td>
			
		</tr>
		{ /loop}
	</form>	
</table>

{else}
<div class="module_add">
	<div class="module_title"><strong>投资管理</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr >
			<td width="" class="main_td">投资ID</td>
			<td width="*" class="main_td">投资人</td>
			<td width="*" class="main_td">投资金额</td>
			<td width="" class="main_td">投资时间</td>
			<td width="" class="main_td">投资状态</td>
			<td width="" class="main_td">是否转让</td>
			<td width="" class="main_td">投资理由</td>
			<td width="*" class="main_td">借款标</td>
			<td width="" class="main_td">借款标识名</td>
			<td width="" class="main_td">借款总额</td>
			<td width="" class="main_td">自动投标</td>
			<!--<td width="" class="main_td">查看</td>-->
		</tr>
		{ list  module="borrow" plugins="tender" function="GetTenderList" var="loop" borrow_name="request"  borrow_nid="request" username="request" dotime1=request dotime2=request query_type=$_A.query_type }
		{foreach from="$loop.list" item="item"}
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td>{ $item.id}<input type="hidden" name="id[]" value="{ $item.id}" /></td>
			<td class="main_td1" align="center">	{$item.username}</td>
			<td>￥{$item.account}</td>
			<td>{$item.addtime|date_format}</td>
			<td>{if $item.status==1}成功{elseif $item.status==2}失败{else}待审核{/if}</td>
			<td>{if $item.change_status==1}是{else}否{/if}</td>
			<td>{$item.contents|default:"-"}</td>
			<td><a href="{$_A.admin_url}&q=code/borrow/view&borrow_nid={$item.borrow_nid}">{$item.borrow_name}</a></td>
			<td>{$item.borrow_nid}</td>
			<td>￥{$item.borrow_account}</td>
			<td>{if $item.auto_status==1}是{else}否{/if}</td>
			<!--<td title="{$item.name}"><a href="{$_A.query_url_all}&id={$item.id}">查看</a></td>-->
			
		</tr>
		{ /foreach}
		<tr>
		<td colspan="14" class="action">
		<div class="floatl">
			<a href="{$_A.query_url_all}&export=excel&page={$magic.request.page|default:1}&username={$magic.request.username}&borrow_name={$magic.request.borrow_name}&borrow_nid={$magic.request.borrow_nid}&status={$magic.request.status}&dotime1={$magic.request.dotime1}&dotime2={$magic.request.dotime2}">导出当前</a>
            <a href="{$_A.query_url_all}&export=excel&username={$magic.request.username}&borrow_name={$magic.request.borrow_name}&borrow_nid={$magic.request.borrow_nid}&status={$magic.request.status}&dotime1={$magic.request.dotime1}&dotime2={$magic.request.dotime2}">导出全部</a>
		</div>
		<div class="floatr">
			 借款标题：<input type="text" name="borrow_name" id="borrow_name" value="{$magic.request.borrow_name}" size="8"/> 用户名：<input type="text" name="username" id="username" value="{$magic.request.username}" size="8"/>贷款号：<input type="text" name="borrow_nid" id="borrow_nid" value="{$magic.request.borrow_nid}" size="8"/> 状态<select id="status" ><option value="">全部</option><option value="1" {if $magic.request.status==1} selected="selected"{/if}>已通过</option><option value="0" {if $magic.request.status=="0"} selected="selected"{/if}>未通过</option></select>投资时间：<input type="text" name="dotime1" id="dotime1" value="{$magic.request.dotime1|default:"$day7"|date_format:"Y-m-d"}" size="15" onclick="change_picktime()"/> 到 <input type="text"  name="dotime2" value="{$magic.request.dotime2|default:"$nowtime"|date_format:"Y-m-d"}" id="dotime2" size="15" onclick="change_picktime()"/><input type="button" value="搜索" onclick="sousuo('{$_A.query_url}/tender')" />
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
{/if}

<script>

var urls = '{$_A.query_url}/tender';
{literal}
function sousuo(url){
	var sou = "";
	var username = $("#username").val();
	if (username!="" && username!=null){
		sou += "&username="+username;
	}
	var keywords = $("#keywords").val();
	if (keywords!="" && keywords!=null){
		sou += "&keywords="+keywords;
	}
	var borrow_name = $("#borrow_name").val();
	if (borrow_name!="" && borrow_name!=null){
		sou += "&borrow_name="+borrow_name;
	}
	var borrow_nid = $("#borrow_nid").val();
	if (borrow_nid!="" && borrow_nid!=null){
		sou += "&borrow_nid="+borrow_nid;
	}
	var borrow_type = $("#borrow_type").val();
	if (borrow_type!="" && borrow_type!=null){
		sou += "&borrow_type="+borrow_type;
	}
	var dotime1 = $("#dotime1").val();
	if (dotime1!="" && dotime1!=null){
		sou += "&dotime1="+dotime1;
	}
	var dotime2 = $("#dotime2").val();
	if (dotime2!="" && dotime2!=null){
		sou += "&dotime2="+dotime2;
	}
	var status_nid = $("#status").val();
	if (status_nid!="" && status_nid!=null){
		sou += "&status="+status_nid;
	}
	var is_vouch = $("#is_vouch").val();
	if (is_vouch!="" && is_vouch!=null){
		sou += "&is_vouch="+is_vouch;
	}
	
		location.href=url+sou;
	
}
</script>
{/literal}