<ul class="nav3"> 
<li><a href="{$_A.query_url_all}" {if  $magic.request.repay_status==""} id="c_so"{/if}>全部还款</a></li> 
<li><a href="{$_A.query_url_all}&repay_status=1" {if  $magic.request.repay_status=="1"} id="c_so"{/if}>已还款</a></li> 
<li><a href="{$_A.query_url_all}&repay_status=0" {if  $magic.request.repay_status=="0"} id="c_so"{/if}>未还款</a></li> 
</ul> 
<div class="module_add">
	<div class="module_title"><strong>还款列表</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr >
		    <td width="*" class="main_td">贷款号</td>
			<td width="*" class="main_td">借款人</td>
			<td width="" class="main_td">借款标题</td>
			<td width="" class="main_td">借款期数</td>
			<td width="" class="main_td">借款类型</td>
			<td width="" class="main_td">应还时间</td>
			<td width="" class="main_td">应还本息</td>
			{if $magic.request.repay_status==1}
			<td width="" class="main_td">实还时间</td>
			<td width="" class="main_td">实还总额</td>
			{/if}
            <!--
			{if $magic.request.type=="yes" || $magic.request.type==""}
			<td width="" class="main_td">应缴逾期罚息</td>
			<td width="" class="main_td">应缴管理费</td>
			<td width="" class="main_td">提前还款罚金</td>
			<td width="" class="main_td">实还金额</td>
			<td width="" class="main_td">还款时间</td>
			{elseif $magic.request.type=="wait"}
			<td width="" class="main_td">逾期罚息</td>
			<td width="" class="main_td">应还管理费</td>
			<td width="" class="main_td">应还总额</td>
			{/if}
            -->
			<td width="" class="main_td">状态</td>
		</tr>
		{list module="borrow" plugins="loan" function="GetRepayList" var="loop" borrow_name="request" username="request" borrow_nid="request" is_vouch="request" borrow_type="request" order="status" repay_status="request" repay_type="request"}
		{foreach from="$loop.list" item="item"}
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td>{$item.borrow_nid}</td>
			<td class="main_td1" align="center"><a href="{$_A.admin_url}&q=code/users/info_view&user_id={$item.user_id}" title="查看">{$item.borrow_username}</a></td>
			<td title="{$item.name}"><a href="{$_A.query_url}/view&borrow_nid={$item.borrow_nid}" title="查看">{$item.borrow_name}</a></td>
			<td>第{$item.repay_period}期</td>
			<td>{$item.type_title}</td>
			<td>{$item.repay_time|date_format:"Y-m-d"}</td>
			<td>{$item.repay_account}元</td>
			{if $magic.request.repay_status==1}
			<td>{$item.repay_yestime|date_format:"Y-m-d"}</td>
			<td title="实还本金[{$item.repay_capital_yes}]+实还利息[{$item.repay_interest_yes}]+还款费用[{$item.repay_fee}]">￥{$item.repay_account_yes+$item.repay_fee}</td>
			{/if}
			<td>{$item.repay_type_name}</td>
		</tr>
		{ /foreach}
		<tr>
		<td colspan="20" class="action">
		<div class="floatl">
			
		</div>
		<div class="floatr">
			标题：<input type="text" name="borrow_name" id="borrow_name" value="{$magic.request.borrow_name|urldecode}" size="8"/> 用户名：<input type="text" name="username" id="username" value="{$magic.request.username}" size="8"/>贷款号：<input type="text" name="borrow_nid" id="borrow_nid" value="{$magic.request.borrow_nid}" size="8"/> 
			<!--
			<select id="is_vouch" ><option value="">全部</option><option value="1" {if $magic.request.is_vouch==1} selected="selected"{/if}>担保标</option><option value="0" {if $magic.request.is_vouch=="0"} selected="selected"{/if}>普通标</option></select> 
			-->
			{linkages name="borrow_type" nid="borrow_all_type" type="value" default="全部" value="$magic.request.borrow_type"}
			{if $magic.request.repay_status==1}
			状态：<select name="repay_type" id="repay_type"><option value="" {if $magic.request.repay_type==""}selected="selected"{/if}>不限</option><option value="yes" {if $magic.request.repay_type=="yes"}selected="selected"{/if}>正常还款</option><option value="advance" {if $magic.request.repay_type=="advance"}selected="selected"{/if}>提前还款</option><option value="late" {if $magic.request.repay_type=="late"}selected="selected"{/if}>逾期还款</option></select>
			{else}
			状态：<select name="repay_status" id="repay_status"><option value="" {if $magic.request.repay_status==""}selected="selected"{/if}>不限</option><option value="1" {if $magic.request.repay_status==1}selected="selected"{/if}>已还</option><option value="0" {if $magic.request.repay_status=="0"}selected="selected"{/if}>未还</option></select>
			{/if}
			<input type="button" value="搜索" class="submit" onclick="sousuo('{$_A.query_url}/repay&repay_status={$magic.request.repay_status}')">
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

var urls = '{$_A.query_url}/repay';
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
	var dotime1 = $("#dotime1").val();
	if (dotime1!="" && dotime1!=null){
		sou += "&dotime1="+dotime1;
	}
	var repay_type = $("#repay_type").val();
	if (repay_type!="" && repay_type!=null){
		sou += "&repay_type="+repay_type;
	}
	var borrow_type = $("#borrow_type").val();
	if (borrow_type!="" && borrow_type!=null){
		sou += "&borrow_type="+borrow_type;
	}
	var dotime2 = $("#dotime2").val();
	if (dotime2!="" && dotime2!=null){
		sou += "&dotime2="+dotime2;
	}
	var status = $("#status").val();
	if (status!="" && status!=null){
		sou += "&status="+status;
	}
	var repay_status = $("#repay_status").val();
	if (repay_status!="" && repay_status!=null){
		sou += "&repay_status="+repay_status;
	}
	var is_vouch = $("#is_vouch").val();
	if (is_vouch!="" && is_vouch!=null){
		sou += "&is_vouch="+is_vouch;
	}
	if (url==""){
		location.href=urls+sou;
	}else{
		location.href=url+sou;
	}
	
}
</script>
{/literal}