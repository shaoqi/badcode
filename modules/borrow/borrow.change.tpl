{if $_A.query_type == "change"}
{if $magic.request.change_check==""}
<ul class="nav3"> 
<li><a href="{$_A.query_url_all}" {if $magic.request.status==""}id="c_so"{/if}>所有转让</a></li> 
<!--<li><a href="{$_A.query_url_all}&status=4">转让网站</a></li>-->
<li><a href="{$_A.query_url_all}&status=2" {if $magic.request.status=="2"}id="c_so"{/if}>正在转让</a></li> 
<li><a href="{$_A.query_url_all}&status=5" {if $magic.request.status=="5"}id="c_so"{/if}>撤销</a></li> 
<li><a href="{$_A.query_url_all}&status=1" {if $magic.request.status=="1"}id="c_so"{/if}>转让成功</a></li>  
<!--<li><a href="{$_A.query_url_all}&status=1&web=1">转让网站统计</a></li> 
<li><a href="{$_A.query_url}/web_repay_no">网站应收明细账</a></li>-->
</ul> 
<div class="module_add">
	<div class="module_title"><strong>转让列表</strong><div style="float:right"> <a href="{$_A.query_url_all}&page={$magic.request.page|default:1}&_type=excel&borrow_name={$magic.request.borrow_name}&username={$magic.request.username}&status={$magic.request.status}">导出当前</a> <a href="{$_A.query_url_all}&_type=excel&borrow_name={$magic.request.borrow_name}&username={$magic.request.username}&status={$magic.request.status}&repay_status=0&lateing=1">导出全部</a>&nbsp;&nbsp;&nbsp;</div></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr >
			<tr class="ytit1" >
				<td  >转让者</td>
				<td  >投标标题</td>
				<td  >利率</td>
				<td  >待收期数/总期数</td>
				<td  >待收本金</td>
				<td  >待收利息</td>
				<td  >转让价格</td>
				{if $magic.request.status==1 and $magic.request.web==1}
				<td  >转让收益</td>
				<td  >垫付金额</td>
				{/if}
				<td  >发布时间</td>
				{if $magic.request.status==1}
				<td  >购买者</td>
				<td  >购买时间</td>
				{elseif $magic.request.status==4}
				<td  >提交审核时间</td>
				{elseif $magic.request.status==5}
				<td  >撤销时间</td>
				{/if}
				<td  >操作</td>
			</tr>
			
		</tr>
		{list module='borrow' function='GetChangeList' plugins="change" status='$magic.request.status' var="item" var="loop" web='request' dotime2=request dotime1=request order="status"}
		{foreach from="$loop.list" item="item"}
		<tr  {if $key%2==1} class="tr2"{/if}>
				<td  >{$item.username}</td>
				<td  >{$item.borrow_name}</td>
				<td  >{$item.borrow_apr}%</td>
				<td  >{$item.wait_times}/{$item.borrow_period}</td>
				<td  >{$item.recover_account_capital_wait}</td>
				<td  >{if $item.interest_no>0}{$item.interest_no}{else}{$item.recover_account_interest_wait}{/if}</td>
				<td  >{$item.account}</td>
				{if $magic.request.status==1 and $magic.request.web==1}
				<td  >{$item.jingzhuan}</td>
				<td  >{$item.recover_web_account|default:0.00}元</td>
				{/if}
				<td  >{$item.addtime|date_format}</td>
				{if $magic.request.status==1}
				<td  >{$item.buy_username|default:网站}</td>
				<td  >{$item.buy_time|date_format}</td>
				{elseif $magic.request.status==4}
				<td  >{$item.web_time|date_format}</td>
				{elseif $magic.request.status==5}
				<td  >{$item.cancel_time|date_format}</td>
				{/if}
				<td  >{if $item.status==3}待审核{elseif $item.status==2}正在转让{elseif $item.status==5}撤销{elseif $item.status==6}审核不通过{elseif $item.status==1}转让成功{else}
				{if $item.recover_account_capital_wait>0}
					<a href="javascript:void(0)" onclick='tipsWindown("审核","url:get?{$_A.query_url_all}&change_check={$item.id}",500,230,"true","","false","text");'>审核</a>
				{else}
					该转让已还清
				{/if}
				{/if}</td>
			
		</tr>
		{ /foreach}
		<tr>
		<td colspan="14" class="action">
		<div class="floatl">
		{if $magic.request.status==""}
			转让总计：{$loop.change_account|default:0.00}元
		{/if}
		</div>
		<div class="floatr">
		{if $magic.request.status==1 and $magic.request.web==1}
			操作时间：<input type="text" name="dotime1" id="dotime1" value="{$magic.request.dotime1|default:"$day7"|date_format:"Y-m-d"}" size="15" onclick="change_picktime()"/> 到 <input type="text"  name="dotime2" value="{$magic.request.dotime2|default:"$nowtime"|date_format:"Y-m-d"}" id="dotime2" size="15" onclick="change_picktime()"/> <input type="button" value="搜索" / onclick="changesousuo()">
		{/if}
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


<script>

var url = '/?qqd&q=code/borrow/change&status=1&web=1';
{literal}
function changesousuo(x){
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
	var dotime1 = $("#dotime1").val();
	if (dotime1!="" && dotime1!=null){
		sou += "&dotime1="+dotime1;
	}
	var dotime2 = $("#dotime2").val();
	if (dotime2!="" && dotime2!=null){
		sou += "&dotime2="+dotime2;
	}
	var status = $("#status").val();
	if (status!="" && status!=null){
		sou += "&status="+status;
	}
	var is_vouch = $("#is_vouch").val();
	if (is_vouch!="" && is_vouch!=null){
		sou += "&is_vouch="+is_vouch;
	}
	
		location.href=url+sou;
	
}
</script>
{/literal}
{else}

<div class="module_add">
	<form name="form1" method="post" action="{$_A.query_url_all}&change_check={$magic.request.change_check}" >
	<div class="module_border">
		<div class="l">状态:</div>
		<div class="c">
		<input type="radio" name="status" value="1"/>审核通过 <input type="radio" name="status" value="0"  checked="checked"/>审核不通过 </div>
	</div>
	
	<div class="module_border" >
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="remark" cols="45" rows="5">{ $remark}</textarea>
		</div>
	</div>
	<div class="module_border" >
		<div class="l">验证码:</div>
		<div class="c">
			<input name="valicode" type="text" size="5" maxlength="4"  tabindex="3" onClick="$('#valicode').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/> <img id="valicode" src="/?plugins&q=imgcode" alt="点击刷新" onClick="this.src='/?plugins&q=imgcode&t=' + Math.random();" align="absmiddle" style="cursor:pointer" />
		</div>
	</div>

	<div class="module_submit" >
		<input type="submit"  name="reset" value="审核" class="submit" />
	</div>
	
</form>
</div>
{/if}
{elseif $_A.query_type == "web_repay_no"}
<ul class="nav3"> 
<li><a href="{$_A.query_url}/change" id="c_so">所有转让</a></li> 
<li><a href="{$_A.query_url}/change&status=4">转让网站</a></li> 
<li><a href="{$_A.query_url}/change&status=2">正在转让</a></li> 
<li><a href="{$_A.query_url}/change&status=5">撤销</a></li> 
<li><a href="{$_A.query_url}/change&status=1">转让成功</a></li>  
<li><a href="{$_A.query_url}/change&status=1&web=1">转让网站统计</a></li> 
<li><a href="{$_A.query_url_all}">网站应收明细账</a></li> 
</ul> 
<div class="module_add">
	<div class="module_title"><strong>网站应收明细账</strong><div style="float:right"> <a href="{$_A.query_url_all}&page={$magic.request.page|default:1}&_type=excel&borrow_name={$magic.request.borrow_name}&borrow_nid={$magic.request.borrow_nid}&username={$magic.request.username}&vouch_status={$magic.request.vouch_status}&repay_status=0&lateing=1">导出当前</a> <a href="{$_A.query_url_all}&_type=excel&borrow_name={$magic.request.borrow_name}&borrow_nid={$magic.request.borrow_nid}&username={$magic.request.username}&vouch_status={$magic.request.vouch_status}&repay_status=0&lateing=1">导出全部</a>&nbsp;&nbsp;&nbsp;</div></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr class="ytit1" >
			<td  >借款标题</td>
			<td  >应收日期</td>
			<td  >借款者</td>
			<td  >第几期/总期数</td>
			<td  >收款总额</td>
			<td  >应收本金</td>
			<td  >应收利息</td>
			<td  >逾期罚息</td>
			<td  >逾期天数</td>
			<td  >状态</td>
		</tr>
		{list module="borrow" var="loop" function ="GetRecoverList" showpage="3" keywords="request" dotime1="request" dotime2="request" borrow_status=3 order="recover_status" showtype="web" web=1 style="web" recover_status=request epage=20}
		{foreach from="$loop.web" item="item"}
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td  ><a href="/invest/a{$item.borrow_nid}.html" target="_blank" title="{$item.borrow_name}">{$item.borrow_name|truncate:8}</a></td>
			<td  >{$item.recover_time|date_format:"Y-m-d"}</td>
			<td  ><a href="/u/{$item.borrow_userid}" target="_blank">{$item.borrow_username}</a></td>
			<td  >{$item.recover_period+1}/{$item.borrow_period}</td>
			<td  >￥{$item.recover_account }</td>
			<td  >￥{$item.recover_capital  }</td>
			<td  >￥{$item.recover_interest  }</td>
			<td  >￥{$item.late_interest|default:0  }</td>
			<td  >{$item.late_days|default:0  }天</td>
			<td  >{if $item.recover_web==1}网站垫付{else}{if $item.recover_status==1  }<font color="#666666">已还</font>{else}<font color="#FF0000">未还</font>{/if}{/if}</td>
		</tr>
		{/foreach}
		<tr>
		<td colspan="14" class="action">
		<div class="floatl">
			已还总计：{$loop.all_recover|default:0.00}元
		</div>
		<div class="floatr">
			应收时间：<input type="text" name="dotime1" id="dotime1" value="{$magic.request.dotime1|default:"$day7"|date_format:"Y-m-d"}" size="15" onclick="change_picktime()"/> 到 <input type="text"  name="dotime2" value="{$magic.request.dotime2|default:"$nowtime"|date_format:"Y-m-d"}" id="dotime2" size="15" onclick="change_picktime()"/> 状态：<select name="recover_status" id="recover_status"><option value="" {if $magic.request.recover_status==""}selected="selected"{/if}>不限</option><option value="1" {if $magic.request.recover_status==1}selected="selected"{/if}>已还</option><option value="2" {if $magic.request.recover_status==2}selected="selected"{/if}>未还</option></select> <input type="button" value="搜索" / onclick="sousuo()">
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
<script>

var url = '/?qqd&q=code/borrow/web_repay_no';
{literal}
function sousuo(){
	var sou = "";
	var dotime1 = $("#dotime1").val();
	if (dotime1!="" && dotime1!=null){
		sou += "&dotime1="+dotime1;
	}
	var dotime2 = $("#dotime2").val();
	if (dotime2!="" && dotime2!=null){
		sou += "&dotime2="+dotime2;
	}
	var recover_status = $("#recover_status").val();
	if (recover_status!="" && recover_status!=null){
		sou += "&recover_status="+recover_status;
	}
	
		location.href=url+sou;
	
}
</script>
{/literal}
{/if}