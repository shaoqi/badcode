<ul class="nav3"> 
<li><a href="{$_A.query_url_all}" {if  $magic.request.recover_status==""} id="c_so"{/if}>全部收款</a></li> 
<li><a href="{$_A.query_url_all}&recover_status=1" {if  $magic.request.recover_status=="1"} id="c_so"{/if}>已收款</a></li> 
</ul> 
<div class="module_add">
	<div class="module_title"><strong>收款列表</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr >
			<td width="*" class="main_td">收款人</td>
			<td width="*" class="main_td">贷款号</td>
			<td width="" class="main_td">借款标题</td>
			<td width="" class="main_td">借款类型</td>
			<td width="" class="main_td">还款本息</td>
			<td width="" class="main_td">逾期天数</td>
			<td width="" class="main_td">应收时间</td>
            {if $magic.request.recover_status==1}
			<td width="" class="main_td">实收时间</td>
			<td width="" class="main_td" title="实收本金+实收利息+提前还款罚息+逾期罚息">实收总额</td>
            {/if}
			<td width="" class="main_td">状态</td>
		</tr>
		{list module="borrow" function="GetRecoverList" plugins="recover" var="loop" borrow_name="request" username="request" borrow_nid="request" recover_status="request" dotime1=request dotime2=request}
		{foreach from="$loop.list" item="item"}
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td class="main_td1" align="center"><a href="{$_A.admin_url}&q=code/users/info_view&user_id={$item.user_id}" title="查看">{$item.username}</a></td>
			<td>{$item.borrow_nid}</td>
			<td title="{$item.name}"><a href="{$_A.query_url}/view&borrow_nid={$item.borrow_nid}" title="查看">{$item.borrow_name}</a>(第{$item.repay_period+1}期)</td>
			<td>{$item.borrow_type|linkages:"borrow_all_type"|default:"$item.borrow_type"}</td>
			<td>{$item.recover_account}元</td>
			<td>{$item.late_days}天</td>
			<td>{$item.recover_time|date_format:"Y-m-d"}</td>
            {if $magic.request.recover_status==1}
			<td width="" class="main_td">{$item.recover_yestime|date_format:"Y-m-d"}</td>
			<td width="" class="main_td" >￥{$item.recover_account_yes}</td>
            {/if}
			<td>{if $item.recover_status==1}<font color="#ff0000">已收款</font>{else}待收款{/if}</td>
		</tr>
		{ /foreach}
		<tr>
		<td colspan="14" class="action">
		<div class="floatl">
			<a href="{$_A.query_url_all}&export=excel&page={$magic.request.page|default:1}&username={$magic.request.username|urldecode}&borrow_name={$magic.request.borrow_name|urldecode}&borrow_nid={$magic.request.borrow_nid}&borrow_type={$magic.request.borrow_type}&recover_status={$magic.request.recover_status}&dotime1={$magic.request.dotime1}&dotime2={$magic.request.dotime2}">导出当前</a>
            <a href="{$_A.query_url_all}&export=excel&username={$magic.request.username|urldecode}&borrow_name={$magic.request.borrow_name|urldecode}&borrow_nid={$magic.request.borrow_nid}&borrow_type={$magic.request.borrow_type}&recover_status={$magic.request.recover_status}&dotime1={$magic.request.dotime1}&dotime2={$magic.request.dotime2}">导出全部</a>
		</div>
		<div class="floatr">
			 标题：<input type="text" name="borrow_name" id="borrow_name" value="{$magic.request.borrow_name|urldecode}" size="8"/> 用户名：<input type="text" name="username" id="username" value="{$magic.request.username}" size="8"/>贷款号：<input type="text" name="borrow_nid" id="borrow_nid" value="{$magic.request.borrow_nid}" size="8"/> 
			
			{linkages name="borrow_type" nid="borrow_all_type" type="value" default="全部" value="$magic.request.borrow_type"}
            <select name="recover_status" id="recover_status">
            <option value="">全部</option>
            <option value="1" {if $magic.request.recover_status==1} selected=""{/if}>已收</option>
            <option value="2" {if $magic.request.recover_status==2} selected=""{/if}>未收</option>
            </select>
            应收时间：<input type="text" name="dotime1" id="dotime1" value="{$magic.request.dotime1|default:"$day7"|date_format:"Y-m-d"}" size="15" onclick="change_picktime()"/> 到 <input type="text"  name="dotime2" value="{$magic.request.dotime2|default:"$nowtime"|date_format:"Y-m-d"}" id="dotime2" size="15" onclick="change_picktime()"/>
			 <input type="button" value="搜索" class="submit" onclick="sousuo('')">
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

var urls = '{$_A.query_url}/recover';
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
	var status = $("#recover_status").val();
	if (status!="" && status!=null){
		sou += "&recover_status="+status;
	}
	var is_vouch = $("#is_vouch").val();
	if (is_vouch!="" && is_vouch!=null){
		sou += "&is_vouch="+is_vouch;
	}

		location.href=urls+sou;
	
}
</script>
{/literal}