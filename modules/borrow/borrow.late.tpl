{if $magic.request.p=="webpay"}

	{articles module="borrow" plugins="loan"  function="GetRepayView" id='$magic.request.id' var="var"}
<div class="module_add">
	<div class="module_title"><strong>确认对此借款标进行垫付</strong></div>
	<div class="module_border">
		<div class="l"></div>
		<div class="c"><!-- <strong>点确认之前请看好此流程，防止标太多而出现错误。</strong> --></div>
	</div>
    <div class="module_border">
		<div class="l"><strong>借款标题：</strong></div>
		<div class="c">{$var.borrow_name}</div>
	</div> 
    <div class="module_border">
		<div class="l"><strong>应还时间：</strong></div>
		<div class="c">{$var.repay_time|date_format:"Y-m-d"}</div>
	</div> 
    <div class="module_border">
		<div class="l"><strong>逾期天数：</strong></div>
		<div class="c">{$var.days}天</div>
	</div> 
    {loop module="borrow" plugins="loan" function="GetRepayLate" repay_id='$magic.request.id' var="rvar"}
	<div class="module_border">
		<div class="l"><strong>投资人[{$rvar.username}]：</strong></div>
		<div class="c">应收本息：{$rvar.recover_account}，应收本金：{$rvar.recover_capital}，{if $rvar.vip_status==1}vip,{else}普通会员，{/if}垫付金额：{$rvar.recover_late_account}</div>
	</div>
    {/loop}
<form name="form1" method="post" action="{$_A.query_url}/loan&p=webpay&id={$magic.request.id}" onsubmit="return confirm('你确定要垫付此借款吗？');">
	<div class="module_border" >
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="remark" cols="45" rows="5"></textarea>
		</div>
	</div>
	<div class="module_border" >
		<div class="l">管理备注:</div>
		<div class="c">
			<textarea name="contents" cols="45" rows="5"></textarea>
		</div>
	</div>
	<div class="module_submit" >
		<input type="hidden" name="borrow_nid" value="{ $var.borrow_nid}" />
		
		<input type="submit"  name="reset" value="立即垫付" class="submit" />
	</div>
	
</form>
{/articles}
</div>	
{else}

<ul class="nav3"> 
<li><a href="{$_A.query_url_all}" {if  $magic.request.late_type==""} id="c_so"{/if}>逾期借款</a></li> 
<li><a href="{$_A.query_url_all}&late_type=repay" {if  $magic.request.late_type=="repay"} id="c_so"{/if}>网站垫付</a></li> 
<li><a href="{$_A.query_url_all}&late_type=recover" {if  $magic.request.late_type=="recover"} id="c_so"{/if}>网站应收明细账</a></li> 
</ul> 
{if $magic.request.late_type==""}

<div class="module_add">
	<div class="module_title"><strong>逾期借款列表</strong><div style="float:right">
				 借款标题：<input type="text" name="borrow_name" id="borrow_name" value="{$magic.request.borrow_name|urldecode}" size="8"/> 借款人：<input type="text" name="username" id="username" value="{$magic.request.username}" size="8"/>
				 类型：{linkages name="borrow_type" nid="borrow_all_type" type="value" default="全部" value="$magic.request.borrow_type"}应还时间：<input type="text" name="dotime1" id="dotime1" value="{$magic.request.dotime1|default:"$day7"|date_format:"Y-m-d"}" size="15" onclick="change_picktime()"/> 到 <input type="text"  name="dotime2" value="{$magic.request.dotime2|default:"$nowtime"|date_format:"Y-m-d"}" id="dotime2" size="15" onclick="change_picktime()"/>
				 <input type="button" value="搜索" class="submit" onclick="sousuo('{$_A.query_url}/late')"></div></div>
</div>

<!--逾期 开始-->
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr >
			<td width="*" class="main_td">贷款号</td>
			<td width="*" class="main_td">借款人</td>
			<td width="*" class="main_td">借款标题</td>
			<td width="" class="main_td">期数</td>
			<td width="" class="main_td">类型</td>
			<td width="" class="main_td">应还时间</td>
			<td width="" class="main_td">应还本息</td>
			<td width="" class="main_td">逾期天数</td>
			<td width="" class="main_td">网站是否垫付</td>
			<td width="" class="main_td">状态</td>
			<td width="" class="main_td">实际还款时间</td>
			<!-- <td width="" class="main_td">操作</td> -->
		</tr>
		{list module="borrow" plugins="loan" function="GetRepayList" var="loop" late_days=request repay_status=0 borrow_name="request" username="request" status_nid="late" dotime1="request" dotime2="request" order="late" borrow_type=request}
		{ foreach  from=$loop.list key=key item=item }
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td>{$item.borrow_nid}</td>
			<td><a href="{$_A.admin_url}&q=code/users/info_view&user_id={$item.user_id}" title="查看">{ $item.borrow_username}</a></td>
			<td><a href="{$_A.query_url}/view&borrow_nid={$item.borrow_nid}" title="查看">{$item.borrow_name}</a></td>
			<td>{$item.repay_period}/{$item.borrow_period}</td>
			<td>{$item.type_title}</td>
			<td >{$item.repay_time|date_format:"Y-m-d"}</td>
			<td >￥{$item.repay_account}</td>
			<td >{$item.late_days}天</td>
			<td >{if $item.repay_web==1}已垫付{else}未垫付{/if}</td>
			<td >{if $item.repay_status==1}已还{else}未还{/if}</td>
			<td >{$item.repay_yestime|default:-}</td>
            <!-- 
			<td >{if $item.repay_web==0}<a href="{$_A.query_url_all}&p=webpay&id={$item.id}">垫付</a>{else}-{/if}</td> -->
		</tr>
		{ /foreach}
		<tr>
			<td colspan="15" class="page">
			{$loop.pages|showpage} 
			</td>
		</tr>
	</form>	
</table>
<!--逾期 结束-->
{elseif $magic.request.late_type=="recover"}
<div class="module_add">
	<div class="module_title"><strong>网站应收明细账</strong><div style="float:right">
			应收时间：<input type="text" name="dotime1" id="dotime1" value="{$magic.request.dotime1|default:"$day7"|date_format:"Y-m-d"}" size="15" onclick="change_picktime()"/> 到 <input type="text"  name="dotime2" value="{$magic.request.dotime2|default:"$nowtime"|date_format:"Y-m-d"}" id="dotime2" size="15" onclick="change_picktime()"/> 状态：<select name="recover_status" id="recover_status"><option value="" {if $magic.request.recover_status==""}selected="selected"{/if}>不限</option><option value="1" {if $magic.request.recover_status==1}selected="selected"{/if}>已还</option><option value="2" {if $magic.request.recover_status==2}selected="selected"{/if}>未还</option></select> <input type="button" value="搜索" / onclick="sousuo('{$_A.query_url}/late&late_type=recover')">&nbsp;&nbsp;&nbsp;</div></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr class="ytit1" >
			<td  >借款标题</td>
			<td  >应收日期</td>
			<td  >借款者</td>
			<td  >第几期/总期数</td>
			<td  >垫付金额</td>
			<td  >应收本金</td>
			<td  >应收利息</td>
			<td  >逾期天数</td>
			<td  >实还时间</td>
			<td  >实还总额</td>
			<td  >状态</td>
		</tr>
		{list module="borrow" var="loop" function ="GetRepayList" plugins="Loan" showpage="3" keywords="request" dotime1="request" dotime2="request" borrow_status=3 type="web" order="recover_status" recover_status=request epage="20" showtype="web"}
		{foreach from="$loop.list" item="item"}
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td  ><a href="/invest/a{$item.borrow_nid}.html" target="_blank" title="{$item.borrow_name}">{$item.borrow_name|truncate:8}</a></td>
			<td  >{$item.repay_time|date_format:"Y-m-d"}</td>
			<td  ><a href="/u/{$item.borrow_userid}" target="_blank">{$item.borrow_username}</a></td>
			<td  >{$item.repay_period}/{$item.borrow_period}</td>
			<td  >￥{$item.repay_web_account}</td>
			<td  >￥{$item.repay_capital  }</td>
			<td  >￥{$item.repay_interest  }</td>
			<td  >{$item.late_days|default:0  }天</td>
			<td  >{$item.repay_yestime|date_format:"Y-m-d"}</td>
			<td title="实还本金[{$item.repay_capital_yes}]+实还利息[{$item.repay_interest_yes}]+还款费用[{$item.repay_fee}]">￥{$item.repay_account_yes+$item.repay_fee}</td>
			<td  >{if $item.repay_status==1}<font color="#666666">已还</font>{else}<font color="#FF0000">未还</font>{/if}</td>			
		</tr>
		{/foreach}
		<tr>
		<td colspan="14" class="action">
		<div class="floatl">
			应收总额：{$loop.repay_all|default:0.00}元,已收总额：{$loop.repay_yes_all|default:0.00}元
		</div>
		<div class="floatr">
			 <a href="{$_A.query_url_all}&action=repay&_type=excel&dotime1={$magic.request.dotime1}&dotime2={$magic.request.dotime2}&borrow_status=3&type=web&order=recover_status&recover_status={$magic.request.recover_status}&epage=15&show_type=web&page={$magic.request.page|default:1}">导出当前</a> <a href="{$_A.query_url_all}&action=repay&_type=excel&dotime1={$magic.request.dotime1}&dotime2={$magic.request.dotime2}&borrow_status=3&type=web&order=recover_status&recover_status={$magic.request.recover_status}&epage=15&show_type=web">导出全部</a>
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
{elseif $magic.request.late_type=="repay"}

<div class="module_add">
	<div class="module_title">
		<strong>网站垫付</strong>
		<div style="float:right">
		借款标题：<input type="text" name="borrow_name" id="borrow_name" value="{$magic.request.borrow_name|urldecode}" size="8"/> 借款人：<input type="text" name="username" id="username" value="{$magic.request.username}" size="8"/>
		类型：{linkages name="borrow_type" nid="borrow_all_type" type="value" default="全部" value="$magic.request.borrow_type"}操作时间：<input type="text" name="dotime1" id="dotime1" value="{$magic.request.dotime1|default:"$day7"|date_format:"Y-m-d"}" size="15" onclick="change_picktime()"/> 到 <input type="text"  name="dotime2" value="{$magic.request.dotime2|default:"$nowtime"|date_format:"Y-m-d"}" id="dotime2" size="15" onclick="change_picktime()"/>
		<input type="button" value="搜索" class="submit" onclick="sousuo('{$_A.query_url}/late&late_type=repay')">
		</div>
	</div>
</div>

<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<form action="" method="post">
		<tr >
			<td width="" class="main_td">ID</td>
			<td width="*" class="main_td">借款人</td>
			<td width="*" class="main_td">借款标题</td>
			<td width="" class="main_td">期数</td>
			<td width="" class="main_td">类型</td>
			<td width="" class="main_td">应还时间</td>
			<td width="" class="main_td">应还金额</td>
			<td width="" class="main_td">逾期天数</td>
			<td width="" class="main_td">网站垫付金额</td>
			<td width="" class="main_td">垫付时间</td>
			<td width="" class="main_td">状态</td>
			<td width="" class="main_td">操作</td>
		</tr>
		{list module="borrow" function="GetRepayList" plugins="Loan" var="loop" late_days=request repay_status=0 borrow_name="request" username="request" borrow_type="request" dotime1="request" dotime2="request" order="late" status_nid="late"}
		{ foreach  from=$loop.list key=key item=item }
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td >{ $item.id}</td>
			<td >{ $item.borrow_username}</td>
			<td><a href="/invest/a{$item.borrow_nid}.html" target="_blank">{$item.borrow_name}</a></td>
			<td>{$item.repay_period}/{$item.borrow_period}</td>
			<td>{$item.borrow_type|linkages:"borrow_all_type"|default:"$item.borrow_type"}</td>
			<td >{$item.repay_time|date_format:"Y-m-d"}</td>
			<td >￥{$item.repay_account }</td>
			<td >{$item.late_days}天</td>
			<td >￥{$item.repay_web_account}</td>
			<td >{$item.repay_web_time|date_format:"Y-m-d"|default:-}</td>
			<td >{if $item.repay_web==1}网站已垫付{else}未垫付{/if}</td>
			<td >
			{if $item.webpay_status==1  &&  $item.repay_web==0 }
				<a href="{$_A.query_url_all}&p=webpay&id={$item.id}&borrow_nid={$item.borrow_nid}">垫付</a>
			{else}
				-
			{/if}
			</td>
		</tr>
		{ /foreach}
		<tr>
			<td colspan="15" class="page">
			{$loop.pages|showpage} 
			</td>
		</tr>
	</form>	
</table>
{/if}
{/if}

<script>

var urls = '{$_A.query_url}/late&late_days={$magic.request.late_days}';
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
	var status = $("#status").val();
	if (status!="" && status!=null){
		sou += "&status="+status;
	}
	var is_vouch = $("#is_vouch").val();
	if (is_vouch!="" && is_vouch!=null){
		sou += "&is_vouch="+is_vouch;
	}
	if(url==""){
		location.href=urls+sou;
	}else{
		location.href=url+sou;
	}
	
}
</script>
{/literal}