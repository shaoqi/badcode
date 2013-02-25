{if $_A.query_type=="user"}
<ul class="nav3"> 
	<li><a href="{$_A.query_url_all}" {if $_A.query_type=="user"} id="c_so"{/if}>建立推广</a></li>
	<li><a href="{$_A.query_url}/invitelog" {if $_A.query_type=="invitelog"} id="c_so"{/if}>邀请记录</a></li>
</ul>
<div class="module_add">
	<div class="module_title"><strong>建立推广</strong></div>
</div>
{if $magic.request.user_id==""}
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">用户ID</td>
		<td width="" class="main_td">用户名</td>
		<td width="" class="main_td">邮箱</td>
		<td width="" class="main_td">身份</td>
		<td width="" class="main_td">信用等级</td>
		<td width="" class="main_td">推广人</td>
		<td width="" class="main_td">推广人类型</td>
		<td width="" class="main_td">建立时间</td>
		<td width="" class="main_td">操作</td>
	</tr>
	{list module="spread" function="GetUser" var="loop" username="request" showpage="3" epage="10" spread_name="request"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td>{$item.user_id}</td>
		<td>{$item.username}</td>
		<td>{$item.email}</td>
		<td>{if $item.vip.status==1}{if $item.vip.vip_type==1}高级VIP{else}VIP会员{/if}{else}普通会员{/if}</td>
		<td>{$item.credit.approve_credit|credit:"borrow"}</td>
		<td>{$item.spread_name|default:"-"}</td>
		<td>{if $item.type==1}投资{elseif $item.type==2}借款{elseif $item.type==3}独立({if $item.style==1}借款{else}投资{/if}){elseif $item.type==6}其他({if $item.style==1}借款{else}投资{/if}){/if}</td>
		<td>{$item.spread_time|date_format:"Y-m-d H:i"|default:"-"}</td>
		<td><a href="{$_A.query_url}/user{$_A.site_url}&user_id={$item.user_id}&username={$item.username}">添加关联</a></td>
	</tr>
	{/foreach}
	<tr>
		<td colspan="9" class="action">
			<div class="floatl">{$loop.pages|showpage}</div>
		<div class="floatr">
		<script>
		var url = '{$_A.query_url_all}';
		{literal}
		function sousuo(){
		var username = $("#username").val();
		var spread_name = $("#spread_name").val();
		location.href=url+"&username="+username+"&spread_name="+spread_name;
		}
		</script>
		{/literal}
		用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}">
		推广人：<input type="text" name="spread_name" id="spread_name" value="{$magic.request.spread_name|urldecode}">
		<input type="submit" value="搜索" onClick="sousuo()">
		</div>
		</td>
	</tr>
	{/list}
</table>
{else}
{literal}
<script>
	function choose(id){
		for($i=1;$i<=4;$i++){
			$("#type_"+$i).hide();
		}
		if (id==3 || id==4){
			$("#style").show();
		}else{
			$("#style").hide();
		}
		$("#type_"+id).show();
	}
</script>
{/literal}
<div class="module_add">
	<form name="form_user" method="post" action="">
	
	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="c">
			{$magic.request.username|urldecode}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">推广类型：</div>
		<div class="c">
			<input name="type" type="radio" value="1" checked="checked" onClick="choose(1)">投资<input name="type" type="radio" value="2" onClick="choose(2)">借款<!--<input name="type" type="radio" value="3" onClick="choose(3)">独立
			<input name="type" type="radio" value="6" onClick="choose(4)">其他-->
		</div>
	</div>
	
	<div class="module_border" style="display:none" id="style">
		<div class="l">类别：</div>
		<div class="c">
			<input name="style" type="radio" value="1" checked="checked">借款<input name="style" type="radio" value="2">投资
		</div>
	</div>

	<div class="module_border">
		<div class="l">推广员：</div>
		<div class="c">
			<select name="user_id_1" id="type_1">
			<option value="">请选择</option>
			{loop module="users" function="GetUsersAdminList" limit="all" type_id="3" var="avar"}
			<option value="{$avar.user_id}">{$avar.adminname}</option>
			{/loop}
			</select>
			
			<select name="user_id_2" id="type_2" style="display:none">
			<option value="">请选择</option>
			{loop module="users" function="GetUsersAdminList" limit="all" type_id="2" var="bvar"}
			<option value="{$bvar.user_id}">{$bvar.adminname}</option>
			{/loop}
			</select>
			
			<select name="user_id_3" id="type_3" style="display:none">
			<option value="">请选择</option>
			{loop module="users" function="GetUsersAdminList" limit="all" type_id="14" var="cvar"}
			<option value="{$cvar.user_id}">{$cvar.adminname}</option>
			{/loop}
			</select>
			
			<select name="user_id_4" id="type_4" style="display:none">
			<option value="">请选择</option>
			{loop module="users" function="GetUsersAdminList" limit="all" type_id="3" var="dvar"}
			<option value="{$dvar.user_id}">{$dvar.adminname}</option>
			{/loop}
			</select>
		</div>
	</div>
	
	<!--<div class="module_border">
		<div class="l">是否为独立推广人：</div>
		<div class="c">
			<input type="checkbox" name="alone_status" value="1">设为独立推广员
		</div>
	</div>-->
	
	<input name="spread_userid" value="{$magic.request.user_id}" type="hidden">
	
	<div class="module_submit border_b" >
		<input type="submit" value="提交" name="submit"/>
		<input type="reset" name="reset" value="重置" />
	</div>
	
	</form>
	</div>
{/if}
{elseif $_A.query_type=="invitelog"}
<ul class="nav3"> 
	<li><a href="{$_A.query_url}/user" {if $_A.query_type=="user"}id="c_so"{/if}>建立推广</a></li>
	<li><a href="{$_A.query_url_all}" {if $_A.query_type=="invitelog"}id="c_so"{/if}>邀请记录</a></li>
</ul>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">注册用户</td>
		<td width="" class="main_td">邮箱</td>
		<td width="" class="main_td">身份</td>
		<td width="" class="main_td">信用等级</td>
		<td width="" class="main_td">推广人</td>
		<td width="" class="main_td">注册时间</td>
		<td width="" class="main_td">状态</td>
		<td width="" class="main_td">操作</td>
	</tr>
	{list module="users" plugins="friends" function="GetFriendsInvite" var="loop" username="request" showpage="3" epage="10" spread_name="request" dotime1="request" dotime2="request"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td>{$item.friend_username}</td>
		<td>{$item.email}</td>
		<td>{if $item.vip.status==1}{if $item.vip.vip_type==1}高级VIP{else}VIP会员{/if}{else}普通会员{/if}</td>
		<td>{$item.credit.approve_credit|credit:"borrow"}</td>
		<td>{$item.username|default:"-"}</td>
		<td>{$item.addtime|date_format:"Y-m-d H:i"|default:"-"}</td>
		<td>{if $item.con_status==1}<font color="red">已关联</font>{else}未关联{/if}</td>
		<td><a href="{$_A.query_url}/user{$_A.site_url}&user_id={$item.friends_userid}&username={$item.friend_username}">添加关联</a></td>
	</tr>
	{/foreach}
	<tr>
		<td colspan="9" class="action">
			<div class="floatl">{$loop.pages|showpage}</div>
		<div class="floatr">
		<script>
		var url = '{$_A.query_url_all}';
		{literal}
		function sousuo(){
		var username = $("#username").val();
		var spread_name = $("#spread_name").val();
		var dotime1 = $("#dotime1").val();
		var dotime2 = $("#dotime2").val();
		location.href=url+"&username="+username+"&spread_name="+spread_name+"&dotime1="+dotime1+"&dotime2="+dotime2;
		}
		</script>
		{/literal}
		用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}">
		推广人：<input type="text" name="spread_name" id="spread_name" value="{$magic.request.spread_name|urldecode}">
		操作时间：<input type="text" name="dotime1" id="dotime1" value="{$magic.request.dotime1|default:"$day7"|date_format:"Y-m-d"}" size="15" onclick="change_picktime()"/> 到 <input type="text"  name="dotime2" value="{$magic.request.dotime2|default:"$nowtime"|date_format:"Y-m-d"}" id="dotime2" size="15" onclick="change_picktime()"/>
		<input type="submit" value="搜索" onClick="sousuo()">
		</div>
		</td>
	</tr>
	{/list}
</table>
{elseif $_A.query_type=="tender"}
<ul class="nav3"> 
	<li><a href="{$_A.query_url_all}" {if $_A.query_type=="tender"}  style="color:red"{/if} >总表</a></li>
	<li><a href="{$_A.query_url}/tender_month" {if $_A.query_type=="tender_month"}  style="color:red"{/if} >月度表</a></li>
</ul>
<div class="module_add">
	<div class="module_title"><strong>投资推广部门</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">用户ID</td>
		<td width="" class="main_td">用户名</td>
		<td width="" class="main_td">邮箱</td>
		<td width="" class="main_td">总推广人数</td>
		<td width="" class="main_td">客户总投资额</td>
		<td width="" class="main_td">操作</td>
	</tr>
	{list module="spread" function="GetSpreadUser" var="loop" username="request" month="request" type="1" alone_status="0"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td>{$item.user_id}</td>
		<td>{$item.username}</td>
		<td>{$item.email}</td>
		<td>{$item.user_all|default:"0"}</td>
		{list module="spread" function="GetSpreadTenderList" var="lloop" user_id="$item.user_id"}
		<td>{$lloop.all_account|default:"0"}</td>
		{/list}
		<td><a href="{$_A.query_url}/tenderinfo{$_A.site_url}&user={$item.user_id}">详情</a></td>
	</tr>
	{/foreach}
			<tr>
			<td colspan="6" class="action">
			<div class="floatl">
			</div>
			<div class="floatr">
			<script>
			var url = '{$_A.query_url_all}';
			{literal}
			function sousuo(){
				var month = $("#month").val();
				var username = $("#username").val();
				location.href=url+"&username="+username+"&month="+month;
			}
			</script>
			{/literal}
			月份:{linkages name="month" type="value" value="$magic.request.month" nid="spread_month"}
			用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}">
			<input type="submit" value="搜索" onClick="sousuo()">
			</div>
			</td>
		</tr>
	<tr>
		<td colspan="14" class="page">{$loop.pages|showpage}</td>
	</tr>
	{/list}
</table>
{elseif $_A.query_type=="tender_month"}
<ul class="nav3"> 
	<li><a href="{$_A.query_url_all}" {if $_A.query_type=="tender"}  style="color:red"{/if} >总表</a></li>
	<li><a href="{$_A.query_url}/tender_month" {if $_A.query_type=="tender_month"}  style="color:red"{/if} >月度表</a></li>
</ul>
<div class="module_add">
	<div class="module_title"><strong>投资推广部分月度报表</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">用户名</td>
		<td width="" class="main_td">月份</td>
		<td width="" class="main_td">投资总额</td>
		<td width="" class="main_td">任务金额</td>
		<td width="" class="main_td">完成率</td>
		<td width="" class="main_td">提成比例</td>
		<td width="" class="main_td">提成收入</td>
		<td width="" class="main_td">操作</td>
	</tr>
	{list module="spread" function="GetSpreadTenderCount" var="loop" month="request"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td>{$item.username}</td>
		<td>{$key}月</td>
		<td>{$item.total|default:"-"}</td>
		<td>{$item.task|default:"-"}</td>
		<td>{$item.scale|default:"0"}%</td>
		<td>{$item.task_scale|default:"0"}%</td>
		<td>{$item.scale_fee|default:"-"}</td>
		<td><a href="{$_A.query_url}/tenderinfo{$_A.site_url}&user={$item.user_id}">详情</a></td>
	</tr>
	{/foreach}
	<tr>
		<td colspan="8" class="action">
			<div class="floatl">
				总提成：{$loop.all}
			</div>
			<div class="floatr">
			{if $magic.request.type!=4}
			<script>
			var url = '{$_A.query_url_all}';
			{literal}
			function sousuo(){
				var month = $("#month").val();
				location.href=url+"&month="+month;
			}
			</script>
			{/literal}
				月份:{linkages name="month" type="value" value="$magic.request.month" nid="spread_month"}
			<input type="submit" value="搜索" onClick="sousuo()">
			{/if}
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="14" class="page">{$loop.pages|showpage}</td>
	</tr>

	{/list}
</table>
{elseif $_A.query_type=="tenderinfo"}
{articles module="users" function="GetUsers" user_id="$magic.request.user" var="var"}
<div class="module_add">
	<div class="module_title"><strong>{$var.username}投资推广详情</strong></div>
</div>
{/articles}
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">年份</td>
		<td width="" class="main_td">月份</td>
		<td width="" class="main_td">投资总额</td>
		<td width="" class="main_td">任务金额</td>
		<td width="" class="main_td">完成率</td>
		<td width="" class="main_td">提成比例</td>
		<td width="" class="main_td">提成收入</td>
		<td width="" class="main_td">操作</td>
	</tr>
	{list module="spread" function="GetSpreadTenderCount" var="loop" user_id="$magic.request.user"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td>{$item.year}</td>
		<td>{$key}月</td>
		<td>{$item.total|default:"-"}</td>
		<td>{$item.task|default:"-"}</td>
		<td>{$item.scale|default:"0"}%</td>
		<td>{$item.task_scale|default:"0"}%</td>
		<td>{$item.scale_fee|default:"-"}</td>
		<td><a href="{$_A.query_url}/tenderone{$_A.site_url}&user={$magic.request.user}&month={$key}">详情</a>/{if $item.scale_fee!=""}{if $item.add_status==1}<font color="#ff0000">已打入</font>{else}<a href="#" onclick="javascript:if(confirm('你确定将当月提成收入打入其网站账户吗？')) location.href='{$_A.query_url}/addone{$_A.site_url}&user_id={$magic.request.user}&money={$item.scale_fee}&style=tender&month={$key}&year={$item.year}'">打入网站账户</a>{/if}{else}无提成收入{/if}</td>
	</tr>
	{/foreach}
	<tr>
		<td colspan="8" class="action">
		<div class="floatl">
		</div>
		<div class="floatr">
		</div>
		</td>
	</tr>
	<tr>
		<td colspan="14" class="page">{$loop.pages|showpage}</td>
	</tr>
	{/list}
</table>
{elseif $_A.query_type=="tenderone"}
{articles module="users" function="GetUsers" user_id="$magic.request.user" var="var"}
<div class="module_add">
	<div class="module_title"><strong>{$var.username}在{$magic.request.month}月的客户投资详情</strong></div>
</div>
{/articles}
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">id</td>
		<td width="" class="main_td">投资用户</td>
		<td width="" class="main_td">投资金额</td>
		<td width="" class="main_td">借款标题</td>
		<td width="" class="main_td">借款金额</td>
		<td width="" class="main_td">借款利率</td>
		<td width="" class="main_td">借款类型</td>
		<td width="" class="main_td">投资时间</td>
	</tr>
	{list module="spread" function="GetSpreadTenderList" var="loop" user_id="$magic.request.user" month="$magic.request.month"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td>{$item.id}</td>
		<td>{$item.username}</td>
		<td>{$item.account}</td>
		<td><a href="/invest/a{$item.borrow_nid}.html" target="_blank">{$item.borrow_name}</a></td>
		<td>{$item.borrow_account}</td>
		<td>{$item.borrow_apr}%</td>
		<td>{if $item.vouchstatus==1}<font color="#ff0000">担保标借款</font>{elseif $item.fast_status==1}<font color="#0000ff">快速标借款</font>{else}信用标借款{/if}</td>
		<td>{$item.addtime|date_format:"Y-m-d H:i:s"}</td>
	</tr>
	{/foreach}
			<tr>
			<td colspan="8" class="action">
			<div class="floatl">
			</div>
			<div class="floatr">
			</div>
			</td>
		</tr>
	<tr>
		<td colspan="14" class="page">{$loop.pages|showpage}</td>
	</tr>
	{/list}
</table>
{elseif $_A.query_type=="borrow"}
<ul class="nav3"> 
	<li><a href="{$_A.query_url_all}" {if $_A.query_type=="borrow"}  style="color:red"{/if} >总表</a></li>
	<li><a href="{$_A.query_url}/tender_month" {if $_A.query_type=="tender_month"}  style="color:red"{/if} >月度表</a></li>
</ul>
<div class="module_add">
	<div class="module_title"><strong>借款推广部门</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">用户名</td>
		<td width="" class="main_td">邮箱</td>
		<td width="" class="main_td">总推广人数</td>
		<td width="" class="main_td">客户总借款额</td>
		<td width="" class="main_td">操作</td>
	</tr>
	{list module="spread" function="GetSpreadUser" var="loop" username="request" type="2" alone_status="0"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td>{$item.id}</td>
		<td>{$item.username}</td>
		<td>{$item.email}</td>
		<td>{$item.user_all|default:"0"}</td>
		<td>{$item.borrow_all|default:"0"}</td>
		<td><a href="{$_A.query_url}/borrowinfo{$_A.site_url}&user={$item.user_id}">详情</a></td>
	</tr>
	{/foreach}
			<tr>
			<td colspan="6" class="action">
			<div class="floatl">
			</div>
			<div class="floatr">
			<script>
			var url = '{$_A.query_url_all}';
			{literal}
			function sousuo(){
				var username = $("#username").val();
				location.href=url+"&username="+username;
			}
			</script>
			{/literal}
			用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}">
			<input type="submit" value="搜索" onClick="sousuo()">
			</div>
			</td>
		</tr>
	<tr>
		<td colspan="14" class="page">{$loop.pages|showpage}</td>
	</tr>
	{/list}
</table>
{elseif $_A.query_type=="borrow_month"}
<ul class="nav3"> 
	<li><a href="{$_A.query_url_all}" {if $_A.query_type=="borrow"}  style="color:red"{/if} >总表</a></li>
	<li><a href="{$_A.query_url}/tender_month" {if $_A.query_type=="tender_month"}  style="color:red"{/if} >月度表</a></li>
</ul>
<div class="module_add">
	<div class="module_title"><strong>借款推广部分月度报表</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">用户名</td>
		<td width="" class="main_td">月份</td>
		<td width="" class="main_td">投资总额</td>
		<td width="" class="main_td">任务金额</td>
		<td width="" class="main_td">完成率</td>
		<td width="" class="main_td">提成比例</td>
		<td width="" class="main_td">提成收入</td>
		<td width="" class="main_td">操作</td>
	</tr>
	{list module="spread" function="GetSpreadBorrowCount" var="loop" month="request"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td>{$item.username}</td>
		<td>{$key}月</td>
		<td>{$item.total|default:"-"}</td>
		<td>{$item.task|default:"-"}</td>
		<td>{$item.scale|default:"0"}%</td>
		<td>{$item.task_scale|default:"0"}%</td>
		<td>{$item.scale_fee|default:"-"}</td>
		<td><a href="{$_A.query_url}/tenderinfo{$_A.site_url}&user={$item.user_id}">详情</a></td>
	</tr>
	{/foreach}
	<tr>
		<td colspan="8" class="action">
			<div class="floatl">
			 总提成：{$loop.all}
			</div>
			<div class="floatr">
			{if $magic.request.type!=4}
			<script>
			var url = '{$_A.query_url_all}';
			{literal}
			function sousuo(){
				var month = $("#month").val();
				location.href=url+"&month="+month;
			}

			</script>
			{/literal}
				月份:{linkages name="month" type="value" value="$magic.request.month" nid="spread_month"}
			<input type="submit" value="搜索" onClick="sousuo()">
			{/if}
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="14" class="page">{$loop.pages|showpage}</td>
	</tr>
	{/list}
</table>
{elseif $_A.query_type=="borrowinfo"}
{articles module="users" function="GetUsers" user_id="$magic.request.user" var="var"}
<div class="module_add">
	<div class="module_title"><strong>{$var.username}借款推广详情</strong></div>
</div>
{/articles}
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">年份</td>
		<td width="" class="main_td">月份</td>
		<td width="" class="main_td">借款总额</td>
		<td width="" class="main_td">任务金额</td>
		<td width="" class="main_td">完成率</td>
		<td width="" class="main_td">提成比例</td>
		<td width="" class="main_td">提成收入</td>
		<td width="" class="main_td">操作</td>
	</tr>
	{list module="spread" function="GetSpreadBorrowCount" var="loop" user_id="$magic.request.user"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td>{$item.year}</td>
		<td>{$key}月</td>
		<td>{$item.total|default:"-"}</td>
		<td>{$item.task|default:"-"}</td>
		<td>{$item.scale|default:"0"}%</td>
		<td>{$item.task_scale|default:"0"}%</td>
		<td>{$item.scale_fee|default:"-"}</td>
		<td><a href="{$_A.query_url}/borrowone{$_A.site_url}&user={$magic.request.user}&month={$key}">详情</a>/{if $item.scale_fee!=""}{if $item.add_status==1}<font color="#ff0000">已打入</font>{else}<a href="#" onclick="javascript:if(confirm('你确定将当月提成收入打入其网站账户吗？')) location.href='{$_A.query_url}/addone{$_A.site_url}&user_id={$magic.request.user}&money={$item.scale_fee}&style=borrow&month={$key}&year={$item.year}'">打入网站账户</a>{/if}{else}无提成收入{/if}</td>
	</tr>
	{/foreach}
			<tr>
			<td colspan="6" class="action">
			<div class="floatl">
			</div>
			<div class="floatr">
			</div>
			</td>
		</tr>
	<tr>
		<td colspan="14" class="page">{$loop.pages|showpage}</td>
	</tr>
	{/list}
</table>
{elseif $_A.query_type=="borrowone"}
{articles module="users" function="GetUsers" user_id="$magic.request.user" var="var"}
<div class="module_add">
	<div class="module_title"><strong>{$var.username}在{$magic.request.month}月的客户借款详情</strong></div>
</div>
{/articles}
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">Id</td>
		<td width="" class="main_td">借款用户</td>
		<td width="" class="main_td">借款金额</td>
		<td width="" class="main_td">借款标题</td>
		<td width="" class="main_td">借款利率</td>
		<td width="" class="main_td">借款类型</td>
		<td width="" class="main_td">通过复审时间</td>
	</tr>
	{list module="spread" function="GetSpreadBorrowList" var="loop" user_id="$magic.request.user" month="$magic.request.month"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td>{$item.id}</td>
		<td>{$item.username}</td>
		<td>{$item.account}</td>
		<td><a href="/invest/a{$item.borrow_nid}.html" target="_blank">{$item.name}</a></td>
		<td>{$item.borrow_apr}%</td>
		<td>{if $item.vouchstatus==1}<font color="#ff0000">担保标借款</font>{elseif $item.fast_status==1}<font color="#0000ff">快速标借款</font>{else}信用标借款{/if}</td>
		<td>{$item.reverify_time|date_format:"Y-m-d H:i:s"}</td>
	</tr>
	{/foreach}
			<tr>
			<td colspan="8" class="action">
			<div class="floatl">
			</div>
			<div class="floatr">
			</div>
			</td>
		</tr>
	<tr>
		<td colspan="14" class="page">{$loop.pages|showpage}</td>
	</tr>
	{/list}
</table>
{elseif $_A.query_type=="verify"}
<div class="module_add">
	<div class="module_title"><strong>审核部门</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr>
		<td width="" class="main_td">月份</td>
		<td width="" class="main_td">当月提出申请的总额度</td>
		<td width="*" class="main_td">当月实际审核的总额度</td>
		<td width="*" class="main_td">当月的完成比率</td>
		<td width="*" class="main_td">当月通过审核的总额度</td>
		<td width="*" class="main_td">当月的通过比率</td>
		<td width="*" class="main_td">当月的任务额度</td>
		<th width="" class="main_td">达成率</th>
		<td width="*" class="main_td">当月的提成率</td>
		<th width="" class="main_td">提成收入</th>
	</tr>
	{list module="spread" function="GetSpreadVerifyCount" var="loop" month="request"}
		{foreach from="$loop.list" item="item"}
		<tr {if $key%2==1} class="tr2"{/if}>
			<td class="main_td1" align="center">{$key}月份</td>
			<td class="main_td1" align="center">￥{$item.ApplyTotal|default:"0"}</td>
			<td class="main_td1" align="center">￥{$item.Apply|default:"0"}</td>
			<td class="main_td1" align="center">{$item.VerifyScale|default:"0"}%</td>
			<td class="main_td1" align="center">￥{$item.VerifyYes|default:"0"}</td>
			<td class="main_td1" align="center">{$item.VerifyYesScale|default:"0"}%</td>
			<td class="main_td1" align="center">￥{$item.VerifyTask|default:"0"}</td>
			<td class="main_td1" align="center">{$item.VerifyTaskScale|default:"0"}%</td>
			<td class="main_td1" align="center">{$item.VerifyTaskFee|default:"0"}%</td>
			<td class="main_td1" align="center">￥{$item.VerifyIncome|default:"0"}</td>
		</tr>
		{/foreach}
		<tr>
			<td colspan="11" class="action">
			<div class="floatl">
			</div>
			<div class="floatr">
			<script>
			var url = '{$_A.query_url_all}';
			{literal}
			function sousuo(){
				var month = $("#month").val();
				location.href=url+"&month="+month;
			}

			</script>
			{/literal}
				月份:{linkages name="month" type="value" value="$magic.request.month" nid="spread_month"}
			<input type="submit" value="搜索" onClick="sousuo()">
			</div>
			</td>
		</tr>
	{/list}
</table>
{elseif $_A.query_type=="more"}
<div class="module_add">
	<div class="module_title"><strong>其他推广人</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">用户ID</td>
		<td width="" class="main_td">用户名</td>
		<td width="" class="main_td">邮箱</td>
		<td width="" class="main_td">总推广人数</td>
		<td width="" class="main_td">客户总投资额</td>
		<td width="" class="main_td">客户总借款额</td>
		<td width="" class="main_td">操作</td>
	</tr>
	{list module="spread" function="GetSpreadUser" var="loop" username="request" type="6"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td>{$item.user_id}</td>
		<td>{$item.username}</td>
		<td>{$item.email}</td>
		<td>{$item.user_all|default:"0"}</td>
		{list module="spread" function="GetMoreList" var="lloop" user_id="$item.user_id"}
		<td>{$lloop.all_tender|default:"0"}</td>
		{/list}
		<td>{$item.borrow_all|default:"0"}</td>
		<td><a href="{$_A.query_url}/moreinfo{$_A.site_url}&user={$item.user_id}">详情</a></td>
	</tr>
	{/foreach}
	<tr>
		<td colspan="7" class="action">
			<div class="floatl">
			</div>
			<div class="floatr">
			<script>
			var url = '{$_A.query_url_all}';
			{literal}
			function sousuo(){
			var username = $("#username").val();
			location.href=url+"&username="+username;
			}
			</script>
			{/literal}
			用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}">
			<input type="submit" value="搜索" onClick="sousuo()">
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="14" class="page">{$loop.pages|showpage}</td>
	</tr>
	{/list}
</table>
{elseif $_A.query_type=="moreinfo"}
{articles module="users" function="GetUsers" user_id="$magic.request.user" var="var"}
<div class="module_add">
	<div class="module_title"><strong>{$var.username}其他推广详情</strong></div>
</div>
{/articles}
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr>
		<td width="" class="main_td">提成金额</td>
		<td width="" class="main_td">备注</td>
		<td width="" class="main_td">交易时间</td>
	</tr>
	{list module="spread" function="GetMoreList" var="loop" user_id="$magic.request.user" type="$magic.request.type" dotime1="request" dotime2="request"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td>{$item.money|default:"0.00"}元</td>
		<td>{$item.remark|default:"-"}</td>
		<td>{$item.addtime|date_format:"Y-m-d H:i:s"}</td>
	</tr>
	{/foreach}
	<tr>
		<td colspan="4" class="action">
		<div class="floatl">
			总计提成：{$loop.all_account|default:0.00}元
		</div>
		<div class="floatr">
			<script>
			var url = '{$_A.query_url_all}';
			{literal}
			function sousuo(){
			var dotime1 = $("#dotime1").val();
			var dotime2 = $("#dotime2").val();
			location.href=url+"&dotime1="+dotime1+"&dotime2="+dotime2;
			}
			</script>
			{/literal}
			操作时间：<input type="text" name="dotime1" id="dotime1" value="{$magic.request.dotime1|default:"$day7"|date_format:"Y-m-d"}" size="15" onclick="change_picktime()"/> 到 <input type="text"  name="dotime2" value="{$magic.request.dotime2|default:"$nowtime"|date_format:"Y-m-d"}" id="dotime2" size="15" onclick="change_picktime()"/> <input type="button" value="搜索" / onclick="sousuo()">
		</div>
		</td>
	</tr>
	<tr>
		<td colspan="14" class="page">{$loop.pages|showpage}</td>
	</tr>
	{/list}
</table>
{elseif $_A.query_type=="other"}
<div class="module_add">
	<div class="module_title"><strong>独立推广人</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">用户ID</td>
		<td width="" class="main_td">用户名</td>
		<td width="" class="main_td">类型</td>
		<td width="" class="main_td">邮箱</td>
		<td width="" class="main_td">总推广人数</td>
		<td width="" class="main_td">客户总投资额</td>
		<td width="" class="main_td">客户总借款额</td>
		<td width="" class="main_td">操作</td>
	</tr>
	{list module="spread" function="GetSpreadUser" var="loop" username="request" type="3" alone_status="1"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td>{$item.user_id}</td>
		<td>{$item.username}</td>
		<td>{if $item.style==1}借款{else}投资{/if}</td>
		<td>{$item.email}</td>
		<td>{$item.user_all|default:"0"}</td>
		<td>{if $item.style==2}{$item.tender_all|default:"0"}{else}-{/if}</td>
		<td>{if $item.style==1}{$item.borrow_all|default:"0"}{else}-{/if}</td>
		{if $item.style==1}
		<td><a href="{$_A.query_url}/otherinfo{$_A.site_url}&user={$item.user_id}&type=borrow_spread">详情</a></td>
		{else}
		<td><a href="{$_A.query_url}/otherinfo{$_A.site_url}&user={$item.user_id}&type=tender_spread">详情</a></td>
		{/if}
	</tr>
	{/foreach}
	<tr>
		<td colspan="8" class="action">
			<div class="floatl">
				总计：{$loop.all_account|default:0.00}元
			</div>
			<div class="floatr">
			<script>
			var url = '{$_A.query_url_all}';
			{literal}
			function sousuo(){
			var username = $("#username").val();
			location.href=url+"&username="+username;
			}
			</script>
			{/literal}
			用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}">
			<input type="submit" value="搜索" onClick="sousuo()">
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="14" class="page">{$loop.pages|showpage}</td>
	</tr>
	{/list}
</table>
{elseif $_A.query_type=="otherinfo"}
{articles module="users" function="GetUsers" user_id="$magic.request.user" var="var"}
<div class="module_add">
	<div class="module_title"><strong>{$var.username}独立推广详情</strong></div>
</div>
{/articles}
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr>
		<td width="" class="main_td">类型</td>
		<td width="" class="main_td">提成金额</td>
		<td width="" class="main_td">备注</td>
		<td width="" class="main_td">交易时间</td>
	</tr>
	{list module="account" function="GetLogList" var="loop" user_id="$magic.request.user" type="$magic.request.type" dotime1="request" dotime2="request"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td>{if $item.type=="borrow_spread"}借款{elseif $item.type=="tender_spread"}投资{/if}</td>
		<td>{$item.money|default:"0"}</td>
		<td>{$item.remark|default:"-"}</td>
		<td>{$item.addtime|date_format:"Y-m-d H:i:s"}</td>
	</tr>
	{/foreach}
	<tr>
		<td colspan="4" class="action">
		<div class="floatl">
			总计提成：{$loop.all_money|default:0.00}元
		</div>
		<div class="floatr">
			<script>
			var url = '{$_A.query_url_all}';
			{literal}
			function sousuo(){
			var dotime1 = $("#dotime1").val();
			var dotime2 = $("#dotime2").val();
			location.href=url+"&dotime1="+dotime1+"&dotime2="+dotime2;
			}
			</script>
			{/literal}
			操作时间：<input type="text" name="dotime1" id="dotime1" value="{$magic.request.dotime1|default:"$day7"|date_format:"Y-m-d"}" size="15" onclick="change_picktime()"/> 到 <input type="text"  name="dotime2" value="{$magic.request.dotime2|default:"$nowtime"|date_format:"Y-m-d"}" id="dotime2" size="15" onclick="change_picktime()"/> <input type="button" value="搜索" / onclick="sousuo()">
		</div>
		</td>
	</tr>
	<tr>
		<td colspan="14" class="page">{$loop.pages|showpage}</td>
	</tr>
	{/list}
</table>
{elseif $_A.query_type=="setting"}

<ul class="nav3"> 
	<li><a href="{$_A.query_url_all}&type=1"  {if $magic.request.type=="1"}  style="color:red"{/if}>投资推广</a></li>
	<li><a href="{$_A.query_url_all}&type=2"  {if $magic.request.type=="2"}  style="color:red"{/if}>借款推广</a></li>
	<li><a href="{$_A.query_url_all}&type=3"  {if $magic.request.type=="3"}  style="color:red"{/if}>审核推广</a></li>
	<!--<li><a href="{$_A.query_url_all}&type=4">独立推广</a></li>
	<li><a href="{$_A.query_url_all}&type=6">其他推广</a></li>-->
</ul>
{if $magic.request.id=="" && $magic.request.edit==""}
<div class="module_add">
	<div class="module_title"><strong>{if $magic.request.type==1}<font color="#ff0000">投资</font>{elseif $magic.request.type==2}<font color="#ff0000">借款</font>{elseif $magic.request.type==3}<font color="#ff0000">审核</font>{elseif $magic.request.type==4}<font color="#ff0000">独立</font>{elseif $magic.request.type==6}<font color="#ff0000">其他</font>{/if}推广参数列表(<a href="{$_A.query_url}/setting{$_A.site_url}&type={$magic.request.type}&edit=1">添加参数</a>)</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">操作管理员</td>
		{if $magic.request.type==4 || $magic.request.type==6}
		<td width="" class="main_td">类型</td>
		{/if}
		{if $magic.request.type!=4 && $magic.request.type!=6}
		<td width="" class="main_td">月份</td>
		<td width="" class="main_td">任务额</td>
		{/if}
		<td width="" class="main_td">提成利率</td>
		{if $magic.request.type!=3 && $magic.request.type!=4 && $magic.request.type!=6}
		<td width="" class="main_td">提成开始额度</td>
		<td width="" class="main_td">提成结束额度</td>
		{/if}
		<td width="" class="main_td">操作</td>
	</tr>
	{list module="spread" function="GetSettingList" var="loop" month="request" type="request"}
		{foreach from="$loop.list" item="item"}
		<tr {if $key%2==1} class="tr2"{/if}>
			<td class="main_td1" align="center">{$item.id}</td>
			<td class="main_td1" align="center">{$item.username}</td>
			{if $magic.request.type==4}
			<td class="main_td1" align="center">{if $item.type==4}投资{elseif $item.type==5}借款{/if}</td>
			{elseif $magic.request.type==6}
			<td class="main_td1" align="center">{if $item.type==7}投资{elseif $item.type==8}借款{/if}</td>
			{/if}
			{if $magic.request.type!=4 && $magic.request.type!=6}
			<td class="main_td1" align="center">{$item.month|linkages:"spread_month"}</td>
			<td class="main_td1" align="center">{$item.task}</td>
			{/if}
			<td class="main_td1" align="center">{$item.task_fee}%</td>
			{if $magic.request.type!=3 && $magic.request.type!=4 && $magic.request.type!=6}
			<td class="main_td1" align="center">{$item.task_first}</td>
			<td class="main_td1" align="center">{$item.task_last}</td>
			{/if}
			<td class="main_td1" align="center"><a href="{$_A.query_url}/setting{$_A.site_url}&type={$magic.request.type}&id={$item.id}">更新</a>/<a href="#" onclick="javascript:if(confirm('确定删除此条设置吗？')) location.href='{$_A.query_url}/delsetting{$_A.site_url}&id={$item.id}'">删除</a></td>
		</tr>
		{/foreach}
		<tr>
			<td colspan="10" class="action">
			<div class="floatl">
			</div>
			<div class="floatr">
			{if $magic.request.type!=4 && $magic.request.type!=6}
			<script>
			var url = '{$_A.query_url_all}';
			{literal}
			function sousuo(){
				var month = $("#month").val();
				location.href=url+"&month="+month;
			}

			</script>
			{/literal}
				月份:{linkages name="month" type="value" value="$magic.request.month" nid="spread_month"}
			<input type="submit" value="搜索" onClick="sousuo()">
			{/if}
			</div>
			</td>
		</tr>
	{/list}
</table>

{else}
<div class="module_add">
	<form name="form_user" method="post" action="">
	<div class="module_title"><strong>{if $magic.request.type==1}<font color="#ff0000">投资</font>{elseif $magic.request.type==2}<font color="#ff0000">借款</font>{elseif $magic.request.type==3}<font color="#ff0000">审核</font>{elseif $magic.request.type==4}<font color="#ff0000">其他</font>{/if}参数设置</strong></div>
	{articles module="spread" function="GetSettingOne" id="$magic.request.id" var="var"}
	{if $magic.request.type!=4 && $magic.request.type!=6}
	<div class="module_border">
		<div class="l">月份：</div>
		<div class="c">
			{linkages name="month" nid="spread_month" type="value" value="$var.month"}
		</div>
	</div>
	<div class="module_border">
		<div class="l">任务额：</div>
		<div class="c">
			<input name="task" type="text" class="input_border" value="{$var.task}" /> <font color="#FF0000">*</font>当月的任务额请设置相同
		</div>
	</div>
	<input type="hidden" name="type" value="{$magic.request.type}">
	{/if}
	{if $magic.request.type==4 || $magic.request.type==6}
	<div class="module_border">
		<div class="l">推广参数类型：</div>
		<div class="c">
			{if $magic.request.type==4}
			<input type="radio" name="type" value="4" checked="checked">投资 <input type="radio" name="type" value="5">借款
			{else}
			<input type="radio" name="type" value="7" checked="checked">投资 <input type="radio" name="type" value="8">借款
			{/if}
		</div>
	</div>
	<input name="task" type="hidden" value="1" />
	<input name="month" type="hidden" value="1" />
	{/if}
	<div class="module_border">
		<div class="l">提成利率：</div>
		<div class="c">
			<input name="task_fee" type="text" class="input_border" value="{$var.task_fee}"/>% <font color="#FF0000">*</font>
		</div>
	</div>
	{if $magic.request.type!=3 && $magic.request.type!=4 && $magic.request.type!=6}
	<div class="module_border">
		<div class="l">提成开始额度：</div>
		<div class="c">
			<input name="task_first" type="text" class="input_border" value="{$var.task_first}"/> <font color="#FF0000">*</font>请勿出现额度交差。公式为开始额度≤超出部分<结束额度
		</div>
	</div>

	<div class="module_border">
		<div class="l">提成结束额度：</div>
		<div class="c">
			<input name="task_last" type="text" class="input_border" value="{$var.task_last}"/> <font color="#FF0000">*</font>请勿出现额度交差。公式为开始额度≤超出部分<结束额度
		</div>
	</div>
	{/if}
	<input type="hidden" name="admin_userid" value="{$_G.user_id}">
	<input type="hidden" name="id" value="{$magic.request.id}">
	
	<div class="module_submit border_b" >
		<input type="submit" value="提交" name="submit"/>
		<input type="reset" name="reset" value="重置" />
	</div>
	
	</form>

</div>
{/if}
<!--
{elseif $_A.query_type=="all"}
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="60%">
	<tr>
		<td width="" class="main_td">用户名</td>
		<td width="" class="main_td">提成金额</td>
	</tr>
	<tr><td colspan="2" align="center"><strong style="color:red">投资部门</strong></td></tr>
	{list module="spread" function="GetSpreadUser" var="tender_loop" type="1" alone_status="0"}
	{foreach from="$tender_loop.list" item="tender_item"}
	<tr>
		<td>{$tender_item.username}</td>
		{list module="spread" function="GetSpreadTenderCount" var="tender_count" user_id="$tender_item.user_id"}
		<td>{$tender_count.all}</td>
		{/list}
	</tr>
	{/foreach}
	{/list}
	<tr>
		<td>&nbsp;</td>
		{list module="spread" function="GetSpreadTenderCount" var="tender_count_all"}
		<td>{$tender_count_all.all}</td>
		{/list}
	</tr>
	<tr><td colspan="2" align="center"><strong style="color:red">借款部门</strong></td></tr>
	{list module="spread" function="GetSpreadUser" var="borrow_loop" type="2" alone_status="0"}
	{foreach from="$borrow_loop.list" item="borrow_item"}
	<tr>
		<td>{$borrow_item.username}</td>
		{list module="spread" function="GetSpreadBorrowCount" var="borrow_count" user_id="$borrow_item.user_id"}
		<td>{$borrow_count.all}</td>
		{/list}
	</tr>
	{/foreach}
	{/list}
	<tr>
		<td>&nbsp;</td>
		{list module="spread" function="GetSpreadBorrowCount" var="borrow_count_all"}
		<td>{$borrow_count_all.all}</td>
		{/list}
	</tr>
	<tr><td colspan="2" align="center"><strong style="color:red">审核部门</strong></td></tr>
	<tr>
		<td>部门提成</td>
		{list module="spread" function="GetSpreadVerifyCount" var="verify_count"}
		<td>{$verify_count.all}</td>
		{/list}
	</tr>
	<tr><td colspan="2" align="center"><strong style="color:red">独立推广人</strong></td></tr>
	{list module="spread" function="GetSpreadUser" var="tender_loop" alone_status="1"}
	{foreach from="$tender_loop.list" item="tender_item"}
	<tr>
		<td>{$tender_item.username}</td>
		<td>{$tender_item.username}</td>
	</tr>
	{/foreach}
	{/list}
	<tr><td colspan="2" align="center"><strong style="color:red">总计</strong></td></tr>
	<tr>
		<td></td>
		<td></td>
	</tr>
</table>
-->
{/if}