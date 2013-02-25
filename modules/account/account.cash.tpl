
	{if $magic.request.action==""}
<ul class="nav3"> 
<li><a href="{$_A.query_url_all}&username={$magic.request.username}"  {if $magic.request.status==""}style="color:red"{/if}>全部提现</a></li> 
<li><a href="{$_A.query_url_all}&status=0&username={$magic.request.username}" {if $magic.request.status=="0"}style="color:red"{/if}>待审核</a></li> 
<li><a href="{$_A.query_url_all}&status=1&username={$magic.request.username}" {if $magic.request.status=="1"}style="color:red"{/if}>审核通过</a></li> 
<li><a href="{$_A.query_url_all}&status=2&username={$magic.request.username}" {if $magic.request.status=="2"}style="color:red"{/if}>审核未通过</a></li>
</ul> 
<div class="module_add">
	<div class="module_title"><font color="#FF0000">{$magic.request.username|urldecode} </font><strong>提现管理</strong><div style="float:right">时间：<input type="text" name="dotime1" id="dotime1" value="{$magic.request.dotime1|default:"$day7"|date_format:"Y-m-d"}" size="15" onclick="change_picktime()"/> 到 <input type="text"  name="dotime2" value="{$magic.request.dotime2|default:"$nowtime"|date_format:"Y-m-d"}" id="dotime2" size="15" onclick="change_picktime()"/> 用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/> 状态<select id="status" ><option value="">全部</option><option value="1" {if $magic.request.status==1} selected="selected"{/if}>已通过</option><option value="0" {if $magic.request.status=="0"} selected="selected"{/if}>未通过</option></select> <input type="button" value="搜索" / onclick="sousuo()"></div></div>
</div>
<form action="" method="post" >
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
		<tr >
			<td class="main_td">ID</td>
			<td class="main_td">用户名称</td>
			<td class="main_td">真实姓名</td>
			<td class="main_td">提现银行</td>
			<td class="main_td">支行</td>
			<td class="main_td">所在地</td>
			<td class="main_td">提现账号</td>
			<td class="main_td">提现总额</td>
			<td class="main_td">到账金额</td>
			<td class="main_td">手续费</td>
			<td class="main_td">提现时间</td>
			<td class="main_td">状态</td>
			<td class="main_td">操作</td>
		</tr>
		{ list module="account" function="GetCashList" var="loop" username="request" status="request" dotime1=request  dotime2=request epage=20}
			{foreach from=$loop.list item="item"}
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td >{ $item.id}</td>
			<td><a href="{$_A.query_url}/cash&username={$item.username}">{$item.username}</a></td>
			<td >{ $item.realname}</td>
			<td >{ $item.bank|linkages:"account_bank"|default:"$item.bank"}</td>
			<td >{ $item.branch}</td>
			<td >{ $item.city|areas:"p,c"}</td>
			<td >{ $item.account}</td>
			<td >{ $item.total}</td>
			<td >{ $item.credited}</td>
			<td >{ $item.fee}</td>	
			<td >{ $item.addtime|date_format:"Y-m-d H:i"}</td>
			<td >{if $item.status==3}失败{else}{$item.status|linkages:"account_recharge_status"|default:"$item.status"}{/if}</td>
			<td ><a href="{$_A.query_url}/cash&action=view&id={$item.id}">审核/查看</a></td>
		</tr>
		{ /foreach}
		<tr>
		<td colspan="14" class="action">
		<div class="floatl">
			<div style="float:left; margin-left:0px; width:490px;">
				提现总金额:{$loop.all|default:0}元&nbsp;&nbsp;提现总手续费:{$loop.fee_all|default:0}元
			</div>
		</div>
		<div class="floatr">
			<a href="{$_A.query_url_all}&type=excel&page={$magic.request.page|default:1}&username={$magic.request.username}&status={$magic.request.status}&dotime1={$magic.request.dotime1}&dotime2={$magic.request.dotime2}&epage=20">导出当前</a> <a href="{$_A.query_url_all}&type=excel&username={$magic.request.username}&status={$magic.request.status}&dotime1={$magic.request.dotime1}&dotime2={$magic.request.dotime2}">导出全部</a>
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
<!--提现记录列表 结束-->
	{else}
	

<div class="module_add">
	
	<form name="form1" method="post" action="" onsubmit="return check_form();" enctype="multipart/form-data" >
	<div class="module_title"><strong>提现审核/查看</strong></div>

	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="c">
			{ $_A.account_cash_result.username}
		</div>
	</div>

	<div class="module_border">
		<div class="l">提现银行：</div>
		<div class="c">
			{ $_A.account_cash_result.bank_name|linkages:"account_bank"|default:"$_A.account_cash_result.bank_name"}
		</div>
	</div>

	<div class="module_border">
		<div class="l">提现支行：</div>
		<div class="c">
			{ $_A.account_cash_result.branch }
		</div>
	</div>

	<div class="module_border">
		<div class="l">提现账号：</div>
		<div class="c">
			{ $_A.account_cash_result.account }
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">提现总额：</div>
		<div class="c">
			{ $_A.account_cash_result.total }
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">到账金额：</div>
		<div class="c">
			{ $_A.account_cash_result.credited }
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">费率：</div>
		<div class="c">
			{ $_A.account_cash_result.fee }
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">状态：</div>
		<div class="c">
		{if $_A.account_cash_result.status==0}提现审核中{elseif $_A.account_cash_result.status==1} 提现已通过 {elseif $_A.account_cash_result.status==2}提现被拒绝{/if}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">添加时间/IP:</div>
		<div class="c">
			{ $_A.account_cash_result.addtime|date_format:'Y-m-d H:i:s'}/{ $_A.account_cash_result.addip}</div>
	</div>
	
	{if $_A.account_cash_result.status==0}
	<div class="module_title"><strong>审核此提现信息</strong></div>
	
	<div class="module_border">
		<div class="l">状态:</div>
		<div class="c">
		 <input type="radio" name="status" value="1"/>审核通过 <input type="radio" name="status" value="2"  checked="checked"/>审核不通过 </div>
	</div>
	
	<div class="module_border" >
		<div class="l">到账金额:</div>
		<div class="c">
			<input type="hidden" name="credited" value="{ $_A.account_cash_result.credited}" size="10">{ $_A.account_cash_result.credited}
		</div>
	</div>
	
	<div class="module_border" >
		<div class="l">费率:</div>
		<div class="c">
			<input type="hidden" name="fee" value="{ $_A.account_cash_result.fee}" size="5">{ $_A.account_cash_result.fee}
		</div>
	</div>
	
	<div class="module_border" >
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="verify_remark" cols="45" rows="5">{ $_A.account_result.verify_remark}</textarea>
		</div>
	</div>
<div class="module_border" >
		<div class="l">验证码：</div>
		<div class="c">
			<input name="valicode" type="text" size="11" maxlength="4"  onClick="$('#valicode').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
		
			<img src="/?plugins&q=imgcode" id="valicode" alt="点击刷新" onClick="this.src='/?plugins&q=imgcode&t=' + Math.random();" align="absmiddle" style="cursor:pointer" />
		</div>
	</div>
	<div class="module_submit" >
		<input type="submit"  name="reset" value="审核此提现信息" />
	</div>
	{else}
	<div class="module_border">
		<div class="l">审核人：</div>
		<div class="c">
			{ $_A.account_cash_result.verify_username }
		</div>
	</div>
	<div class="module_border">
		<div class="l">审核时间：</div>
		<div class="c">
			{ $_A.account_cash_result.verify_time|date_format:"Y-m-d H:i" }
		</div>
	</div>
	<div class="module_border">
		<div class="l">审核备注：</div>
		<div class="c">
			{ $_A.account_cash_result.verify_remark}
		</div>
	</div>
	{/if}
	</form>
</div>
{literal}
<script>
function check_form(){
	 var frm = document.forms['form1'];
	 var verify_remark = frm.elements['verify_remark'].value;
	 var errorMsg = '';
	  if (verify_remark.length == 0 ) {
		errorMsg += '备注必须填写' + '\n';
	  }
	  
	  if (errorMsg.length > 0){
		alert(errorMsg); return false;
	  } else{  
		return true;
	  }
}
</script>
{/literal}
	
	{/if}