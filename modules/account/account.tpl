
{if $_A.sub_dir!=""}
	{include file="$_A.sub_dir" template_dir = "modules/account"}
{elseif $_A.query_type=="list"}
	{include file="account.list.tpl" template_dir = "modules/account"}
{elseif $_A.query_type=="log"}
	{include file="account.log.tpl" template_dir = "modules/account"}
{elseif $_A.query_type=="recharge"}
	{include file="account.recharge.tpl" template_dir = "modules/account"}
{elseif $_A.query_type=="cash"}
	{include file="account.cash.tpl" template_dir = "modules/account"}
{elseif $_A.query_type=="web" || $_A.query_type=="web_count"}
	{include file="account.web.tpl" template_dir = "modules/account"}
{elseif $_A.query_type=="users" || $_A.query_type=="users_count"}
	{include file="account.users.tpl" template_dir = "modules/account"}




{elseif $_A.query_type=="bank"}
<ul class="nav3"> 
<li><a href="{$_A.query_url}/bank" style="color:red">用户账户信息</a></li> 
<!--<li><a href="{$_A.query_url}/bank&action=bank">银行账户列表</a></li> 
<li><a href="{$_A.query_url}/bank&action=new">添加银行账户</a></li>-->
</ul> 


{if $magic.request.action==""}

<div class="module_add">
	<div class="module_title"><strong>用户账户信息</strong></div>
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
	<div class="module_title"><strong>修改用户银行账户</strong></div>
	
	
	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="c">
			{$_A.account_bank_result.username}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">真实姓名：</div>
		<div class="c">
			<a href="{$_A.admin_url}&q=code/approve/realname&user_id={$_A.account_bank_result.user_id}">{$_A.account_bank_result.realname|default:"未填"}</a>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">所在地：</div>
		<div class="c">
			{areas type="p,c" value="$_A.account_bank_result.city"}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">所属银行：</div>
		<div class="c">
		{linkages nid="account_bank" name="bank" value="$_A.account_bank_result.bank" type="value"}
			
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">支行：</div>
		<div class="c">
			<input type="text" name="branch" value="{$_A.account_bank_result.branch}" />
		</div>
	</div>
	<div class="module_border">
		<div class="l">银行账户：</div>
		<div class="c">
			<input type="text" name="account"   value="{$_A.account_bank_result.account}"/>
		</div>
	</div>
	
	<div class="module_submit"><input type="hidden" name="type" value="update" />
	<input type="hidden" name="user_id" value="{$magic.request.user_id}" />
	<input type="hidden" name="id" value="{if $magic.request.id!=''}{$magic.request.id}{else}{$_A.account_bank_result.id}{/if}" />
	<input type="submit" value="确认提交" class="submit_button" /></div>
		</form>
	</div>
	
	{/if}
	</div>
		</div>
	<div style="float:right; width:67%; text-align:left">
	
	<div class="module_add">
		<div class="module_title"><strong>用户银行账户列表</strong></div>
	</div>
	<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
		  <form action="" method="post">
			<tr >
				<td class="main_td">ID</td>
				<td class="main_td">用户名</td>
				<td class="main_td">真实姓名</td>
				<td class="main_td">所属银行</td>
				<td class="main_td">所在地</td>
				<td class="main_td">支行</td>
				<td class="main_td">银行账户</td>
				<td class="main_td">操作</td>
			</tr>
			{ list module="account" function="GetUsersBankList" var="loop" username="request" realname="request"}
			{foreach from=$loop.list item="item"}
			<tr  {if $key%2==1} class="tr2"{/if}>
				<td >{ $item.id}</td>
				<td >{$item.username}</td>
				<td >{$item.realname}</td>
				<td >{$item.bank|linkages:"account_bank"|default:"$item.bank"}</td>
				<td >{$item.province|areas} {$item.city|areas}</td>
				<td >{$item.branch}</td>
				<td >{$item.account}</td>
				<td ><a href="{$_A.query_url}/bank&user_id={$item.user_id}&id={$item.id}">修改</a></td>
			</tr>
			{ /foreach}
			<tr>
			<td colspan="12" class="action">
			<div class="floatl">			
			</div>
			<div class="floatr">
				用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>真实姓名:<input type="text" name="realname" id="realname" value="{$magic.request.realname|urldecode}"/>
				<input type="button" value="搜索"  onclick="sousuo('{$_A.query_url}/bank')"/>
			</div>
			</td>
		</tr>
			<tr>
				<td colspan="9" class="page">
				{$loop.pages|showpage} 
				</td>
			</tr>
			{/list}
		</form>	
	</table>
</div>
{elseif $magic.request.action=="bank"}
	<div class="module_add">
		<div class="module_title"><strong>{$MsgInfo.account_name_bank_list}</strong></div>
	</div>
	<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
		  <form action="" method="post">
			<tr >
				<td class="main_td">ID</td>
				<td class="main_td">{$MsgInfo.account_name_bank_name}</td>
				<td class="main_td">{$MsgInfo.account_name_bank_status}</td>
				<td class="main_td">{$MsgInfo.account_name_bank_nid}</td>
				<td class="main_td">{$MsgInfo.account_name_bank_litpic}</td>
				<td class="main_td">{$MsgInfo.account_name_bank_cash_money}</td>
				<td class="main_td">{$MsgInfo.account_name_bank_reach_day}</td>
				<td class="main_td">{$MsgInfo.account_name_bank_manage}</td>
			</tr>
			{ list module="account" function="GetBankList" var="loop" keywords="request"}
			{foreach from=$loop.list item="item"}
			<tr  {if $key%2==1} class="tr2"{/if}>
				<td >{ $item.id}</td>
				<td >{$item.name}</td>
				<td >{$item.status|linkages:"account_bank_status"}</td>
				<td >{$item.nid}</td>
				<td >{$item.litpic}</td>
				<td >{$item.cash_money}</td>
				<td >{$item.reach_day}</td>
				<td ><a href="{$_A.query_url}/bank&action=edit&id={$item.id}">{$MsgInfo.linkages_name_edit}</a>  <a href="#" onClick="javascript:if(confirm('{$MsgInfo.account_name_bank_del_msg}')) location.href='{$_A.query_url}/bank&action=del&id={$item.id}'">{$MsgInfo.linkages_name_del}</a></td>
			</tr>
			{ /foreach}
			<tr>
			<td colspan="12" class="action">
			<div class="floatl">
			
			</div>
			<div class="floatr">
				名称：<input type="text" name="keywords" id="keywords" value="{$magic.request.keywords}"/> <input type="button" value="搜索" / onclick="sousuo()">
			</div>
			</td>
		</tr>
			<tr>
				<td colspan="9" class="page">
				{$loop.pages|showpage} 
				</td>
			</tr>
			{/list}
		</form>	
	</table>

<!--添加充值记录 开始-->
{elseif $magic.request.action == "new" || $magic.request.action == "edit"}

<div class="module_add">
	
	<form name="form1" method="post" action="" onsubmit="return check_form();" enctype="multipart/form-data" >
	<div class="module_title"><strong>{$MsgInfo.account_name_bank_new}</strong></div>

	<div class="module_border">
		<div class="l">{$MsgInfo.account_name_bank_name}：</div>
		<div class="c">
			<input type="text" name="name" value="{$_A.account_bank_result.name}" />
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">{$MsgInfo.account_name_bank_status}：</div>
		<div class="c">
			{input name="status" type="radio" value="1|开启,0|关闭" checked="$_A.account_bank_result.status"}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">{$MsgInfo.account_name_bank_nid}：</div>
		<div class="c">
			<input type="text" name="nid"  value="{$_A.account_bank_result.nid}"/>
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">{$MsgInfo.account_name_bank_litpic}：</div>
		<div class="c">
			<input type="text" name="litpic"  value="{$_A.account_bank_result.litpic}"/>
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">{$MsgInfo.account_name_bank_cash_money}：</div>
		<div class="c">
			<input type="text" name="cash_money"  value="{$_A.account_bank_result.cash_money}"/>
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">{$MsgInfo.account_name_bank_reach_day}：</div>
		<div class="c">
			<input type="text" name="reach_day"  value="{$_A.account_bank_result.reach_day}"/>
		</div>
	</div>
	
	<div class="module_submit" >
		
		<input type="submit"  name="reset" value="{$MsgInfo.account_name_submit}" />
	</div>
</form>
</div>
{/if}
<!--添加充值记录 结束-->

<!--提现记录列表 开始-->
<!--添加充值记录 开始-->
{elseif $_A.query_type == "recharge_new"}

<div class="module_add">
	
	<form name="form1" method="post" action="" onsubmit="return check_form();" enctype="multipart/form-data" >
	<div class="module_title"><strong>添加充值</strong></div>

	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="c">
			<input type="text" name="username" />
		</div>
	</div>
	<div class="module_border">
		<div class="l">类型：</div>
		<div class="c">
			线下充值<input type="hidden" name="type" />
		</div>
	</div>
	<div class="module_border">
		<div class="l">金额：</div>
		<div class="c">
			<input type="text" name="money" />
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">备注：</div>
		<div class="c">
			<input type="text" name="remark" />
		</div>
	</div>
	
	<div class="module_submit" >
		
		<input type="submit"  name="reset" value="确认充值" />
	</div>
</form>
	<form name="form1" method="post" action="?dyryr&q=code/account/batch_recharge_new" enctype="multipart/form-data" >
	<div class="module_title"><strong>批量添加充值</strong></div>
	<div class="module_border">批量添加，仅供使用电子表格</div>
	<input type="file" name="file">
	<div class="module_submit" >
		<input type="submit"  name="reset" value="开始批处理" />
	</div>
</form>
</div>

<!--添加充值记录 结束-->




<!--添加充值记录 开始-->
{elseif $_A.query_type == "deduct"}

<div class="module_add">
	
	<form name="form1" method="post" action="" onsubmit="return check_form();" enctype="multipart/form-data" >
	<div class="module_title"><strong>费用扣除</strong></div>

	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="c">
			<input type="text" name="username" />
		</div>
	</div>
	<div class="module_border">
		<div class="l">类型：</div>
		<div class="c">
			{linkages name="type" type="value" nid="account_deduct_type"}
		</div>
	</div>
	<div class="module_border">
		<div class="l">金额：</div>
		<div class="c">
			<input type="text" name="money" />
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">备注：</div>
		<div class="c">
			<input type="text" name="remark" />比如，现场费用扣除200元
		</div>
	</div>
	<div class="module_border">
		<div class="l">验证码：</div>
		<div class="c"><input  class="user_aciton_input"  name="valicode" type="text" size="8" maxlength="4" style=" padding-top:4px; height:16px; width:70px;"/>&nbsp;<img src="/?plugins&q=imgcode" alt="点击刷新" onClick="this.src='/?plugins&q=imgcode&t=' + Math.random();" align="absmiddle" style="cursor:pointer" />
		</div>
	</div>
	<div class="module_submit" >
		
		<input type="submit"  name="reset" value="确定扣除" />
	</div>
</form>
</div>

<!--添加充值记录 结束-->
{elseif $_A.query_type == "payment"}
	{include file="account.payment.tpl" template_dir="modules/account"}
{/if}
<script>
var url = '{$_A.query_url}/{$_A.query_type}';
{literal}
function sousuo(){
	var sou = "";
	
	
	 if ($("#email")[0]){
		var email = $("#email").val();
		if (email!=""){
			sou += "&email="+email;
		}
	}
	if ($("#status")[0]){
		var status = $("#status").val();
		if (status!="" && status!=null){
			sou += "&status="+status;
		}
	}
	var dotime1 = $("#dotime1").val();
	var keywords = $("#keywords").val();
	var dotime2 = $("#dotime2").val();
	var type = $("#type").val();
	var username = $("#username").val();
	var realname = $("#realname").val();
	var nid = $("#nid").val();
	if (username!=null){
		sou += "&username="+username;
	}
	if (realname!=null){
		sou += "&realname="+realname;
	}
	
	if (keywords!=null){
		 sou += "&keywords="+keywords;
	}
	if (dotime1!=null){
		 sou += "&dotime1="+dotime1;
	}
	if (dotime2!=null){
		 sou += "&dotime2="+dotime2;
	}
	if (type!=null){
		 sou += "&type="+type;
	}
	if (nid!=null){
		 sou += "&nid="+nid;
	}
	if (sou!=""){
	location.href=url+sou;
	}
}

</script>
{/literal}