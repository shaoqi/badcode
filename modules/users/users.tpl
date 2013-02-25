<!--用户列表 开始-->
{if $_A.query_type == "list"}
<ul class="nav3"> 
<li><a href="{$_A.query_url_all}" {if $magic.request.order==""}style="color:red"{/if}>{$MsgInfo.users_name_order_default}</a></li> 
<li><a href="{$_A.query_url_all}&order=last_time" {if $magic.request.order=="last_time"}style="color:red"{/if}>{$MsgInfo.users_name_order_last_time}</a></li> 
<li><a href="{$_A.query_url_all}&order=reg_time" {if $magic.request.order=="reg_time"}style="color:red"{/if}>{$MsgInfo.users_name_order_reg_time}</a></li>
</ul> 
<div class="module_add">
	<div class="module_title"><strong>用户列表</strong><span style="float:right">
		{$MsgInfo.users_name_username}：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>  {$MsgInfo.users_name_email}：<input type="text" name="email" id="email" value="{$magic.request.email}"/>   <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()"></span></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">{$MsgInfo.users_name_id}</td>
		<td width="*" class="main_td">{$MsgInfo.users_name_username}</td>
		<td width="*" class="main_td">{$MsgInfo.users_name_email}</td>
		<td width="*" class="main_td">{$MsgInfo.users_name_logintime}</td>
		<td width="*" class="main_td">{$MsgInfo.users_name_reg_time}</td>
		<td width="*" class="main_td">{$MsgInfo.users_name_reg_ip}</td>
		<td width="*" class="main_td">{$MsgInfo.users_name_up_time}</td>
		<td width="*" class="main_td">{$MsgInfo.users_name_up_ip}</td>
		<th width="" class="main_td">{$MsgInfo.users_name_last_time}</th>
		<th width="" class="main_td">{$MsgInfo.users_name_last_ip}</th>
		<th width="" class="main_td">修改</th>
	</tr>
	{ list module="users" function="GetUsersList" var="loop" username=request email=request order="request" epage="20"}
	{foreach from=$loop.list item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.user_id}</td>
		<td class="main_td1" align="center">{$item.username}</td>
		<td class="main_td1" align="center">{$item.email}</td>
		<td class="main_td1" align="center">{$item.logintime|default:0}</td>
		<td class="main_td1" align="center" >{$item.reg_time|date_format:"Y-m-d H:i:s"}</td>
		<td class="main_td1" align="center" >{$item.reg_ip}</td>
		<td class="main_td1" align="center" >{$item.up_time|date_format:"Y-m-d H:i:s"}</td>
		<td class="main_td1" align="center" >{$item.up_ip}</td>
		<td class="main_td1" align="center" >{$item.last_time|date_format:"Y-m-d H:i:s"}</td>
		<td class="main_td1" align="center" >{$item.last_ip}</td>
		<td class="main_td1" align="center" ><a href="{$_A.query_url}/edit&user_id={$item.user_id}">修改</a></td>
	</tr>
	{/foreach}
	<tr>
			<td colspan="11" class="action">
			<div class="floatl">
			<script>
	  var url = '{$_A.query_url}';
	    {literal}
	  	function sousuo(){
			var username = $("#username").val();
			var email = $("#email").val();
			location.href=url+"&username="+username+"&email="+email;
		}
	  
	  </script>
	  {/literal}
			</div>
			<div class="floatr">
			</div>
			</td>
		</tr>
	<tr align="center">
		<td colspan="10" align="center"><div align="center">{$loop.pages|showpage}</div></td>
	</tr>
	
	{ /list}
</table>
<!--用户列表 结束-->
{elseif $_A.query_type == "invite_info"}
<div class="module_add">
	<div class="module_title"><strong>用户推荐详情表</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">用户id</td>
		<td width="*" class="main_td">用户名</td>
		<td width="*" class="main_td">是否VIP</td>
		<td width="*" class="main_td">通过时间</td>
	</tr>
	{ list module="users" plugins="Friends" function="GetUsersInviteList" var="loop" 	user_id=$magic.request.user_id}
		{foreach from=$loop.list item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{$item.user_id}</td>
		<td class="main_td1" align="center">{$item.username}</td>
		<td class="main_td1" align="center">{if $item.vip_status == 1}是{else}否{/if}</td>
		<td class="main_td1" align="center">{if $item.verify_time!=''}{$item.verify_time|date_format}{else}-{/if}</td>
	</tr>
	{/foreach}
	<tr>
			<td colspan="11" class="action">
			<div class="floatl">
			<script>
	  var url = '{$_A.query_url}/info';
	    {literal}
	  	function sousuo(){
			var username = $("#username").val();
			location.href=url+"&username="+username;
		}
	  
	  </script>
	  {/literal}
			</div>
			<div class="floatr">
				{$MsgInfo.users_name_username}：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>     <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()">
			</div>
			</td>
		</tr>
	<tr align="center">
		<td colspan="10" align="center"><div align="center">{$loop.pages|showpage}</div></td>
	</tr>
	
	{ /list}
</table>


<!--用户信息列表 开始-->
{elseif $_A.query_type == "info"}
<div class="module_add">
	<div class="module_title"><strong>用户信息</strong><span style="float:right">
		{$MsgInfo.users_name_username}：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>     <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()"></span></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="*" class="main_td">用户名</td>
		<td width="*" class="main_td">邮箱</td>
		<td width="*" class="main_td">积分</td>
		<td width="*" class="main_td">用户类型</td>
		<td width="*" class="main_td">邀请人数</td>
		<td width="*" class="main_td">推荐人</td>
		<td width="*" class="main_td">最后登录</td>
		<td width="*" class="main_td">基本信息</td>
		<td width="*" class="main_td">详细信息</td>
		<td width="*" class="main_td">材料审核</td>
		<td width="*" class="main_td">资金详情</td>
		<td width="*" class="main_td">积分详情</td>
	</tr>
	{ list module="users" function="GetUsersInfoList" var="loop" username=request email=request epage="20"}
	{foreach from=$loop.list item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.user_id}</td>
		<td class="main_td1" align="center">{ $item.username}</td>
		<td class="main_td1" align="center">{ $item.email}</td>
		<td class="main_td1" align="center">{ $item.credit|credit:"borrow"}</td>
		<td class="main_td1" align="center">{$item.type_name}</td>
		<td class="main_td1" align="center" >{$item.in_num|default:0}[<a href="{$_A.query_url}/invite_info&user_id={$item.user_id}">查看</a>]</td>
		<td class="main_td1" align="center" >{$item.invite_username|default:-}</td>
		<td class="main_td1" align="center" >{$item.last_time|date_format}</td>
		<td class="main_td1" align="center"><a href="{$_A.query_url}/info_view&user_id={$item.user_id}">查看</a></td>
		<td class="main_td1" align="center"><a href="{$_A.query_url}/viewinfo&user_id={$item.user_id}">编辑</a></td>
		<td class="main_td1" align="center"><a href="{$_A.admin_url}&q=code/attestations/upload&user_id={$item.user_id}">查看</a></td>
		<td class="main_td1" align="center"><a href="{$_A.admin_url}&q=code/account/log&username={$item.username}">查看</a></td>
		<td class="main_td1" align="center"><a href="{$_A.admin_url}&q=code/credit/log&user_id={$item.user_id}">查看</a></td>
	</tr>
	{/foreach}
	<tr>
			<td colspan="11" class="action">
			<div class="floatl">
			<script>
	  var url = '{$_A.query_url}/info';
	    {literal}
	  	function sousuo(){
			var username = $("#username").val();
			var email = $("#email").val();
			location.href=url+"&username="+username+"&email="+email;
		}
	  
	  </script>
	  {/literal}
			</div>
			<div class="floatr">
			</div>
			</td>
		</tr>
	<tr align="center">
		<td colspan="10" align="center"><div align="center">{$loop.pages|showpage}</div></td>
	</tr>
	
	{ /list}
</table>
<!--用户信息列表 结束-->

{elseif $_A.query_type == "info_edit" }
<div class="module_add">
	
	<form  name="form_user" method="post" action="" >
	<div class="module_title"><strong>修改用户信息</strong></div>
	
	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="c">
			{$_A._user_result.username}
		</div>
	</div>
	<div class="module_border">
		<div class="l">昵称：</div>
		<div class="c">
		<input name="niname" type="text"  class="input_border" value="{$_A._user_result.niname}" /> <font color="#FF0000">*</font>
		</div>
	</div>
	<div class="module_border">
		<div class="l">状态：</div>
		<div class="c">
		<select name="status">
			<option value="0" {if $_A._user_result.status=="0"} selected="selected"{/if}>申请</option>
			<option value="1" {if $_A._user_result.status=="1"} selected="selected"{/if}>正常</option>
			<option value="2" {if $_A._user_result.status=="2"} selected="selected"{/if}>关闭</option>
		</select>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">生日：</div>
		<div class="c">
		<input name="birthday" type="text"  class="input_border" value="{$_A._user_result.birthday}" /> 
		</div>
	</div>
	<div class="module_border">
		<div class="l">性别：</div>
		<div class="c">
		<input name="sex" type="radio"  class="input_border" value="男" {if $_A._user_result.sex=="男"} checked="checked"{/if} /> 男
		<input name="sex" type="radio"  class="input_border" value="女" {if $_A._user_result.sex=="女"} checked="checked"{/if} /> 女
		</div>
	</div>
	<div class="module_border">
		<div class="l">安全问题：</div>
		<div class="c">
		<input name="question" type="text"  class="input_border" value="{$_A._user_result.question}" /> 
		</div>
	</div>
	<div class="module_border">
		<div class="l">安全答案：</div>
		<div class="c">
		<input name="answer" type="text"  class="input_border" value="{$_A._user_result.answer}" /> 
		</div>
	</div>
	<div class="module_border">
		<div class="l">所在地：</div>
		<div class="c">
		{areas type="p,c,a"  value='$_A._user_result.area'}
		</div>
	</div>
	
	
	<div class="module_submit border_b" >
	<input type="hidden" name="user_id" value="{ $_A._user_result.user_id }" />
	<input type="submit" name="submit" value="提交" />
	</div>
	</form>
</div>
{elseif $_A.query_type=="info_view"}

<div class="module_add">
	<div class="module_title"><strong>用户详情查看</strong></div>
	<div style="margin-top:10px;">
		
	
	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="c">
			{$_A._user_result.username}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">邮箱：</div>
		<div class="c">
			{$_A._user_result.email}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">注册时间/注册ip：</div>
		<div class="c">
			{$_A._user_result.reg_time|date_format}/{$_A._user_result.reg_ip}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">最后登陆时间/最后登陆ip：</div>
		<div class="c">
			{$_A._user_result.last_time|date_format}/{$_A._user_result.last_ip}
		</div>
	</div>
	
	{articles module="approve" function="GetRealnameOne" user_id="$magic.request.user_id" var="Evar"}
	<div class="module_title"><strong>认证状态</strong></div>
	<div class="module_border">
		<div class="l">真实姓名/身份证号/是否认证：</div>
		<div class="c">
			{$Evar.realname|default:-}/{$Evar.card_id|default:-}/{if $Evar.status==1}已认证{else}未认证{/if}
		</div>
	</div>
	
	{/articles}
	{articles module="approve" function="GetEduOne" user_id="$magic.request.user_id" var="Evar"}
	<div class="module_border">
		<div class="l">学历/是否认证：</div>
		<div class="c">
			{$Evar.degree}/{if $Evar.status==1}已认证{else}未认证{/if}
		</div>
	</div>
	{/articles}
	<div class="module_border">
		<div class="l">手机/是否认证：</div>
		<div class="c">
			{$_A._user_result.phone|default:-}/{if $_A._user_result.phone_status==1}已认证{else}未认证{/if}
		</div>
	</div>
	<div class="module_border">
		<div class="l">视频/是否认证：</div>
		<div class="c">
			{$_A._user_result.video}{if $_A._user_result.video_status==1}已认证{else}未认证{/if}
		</div>
	</div>
	{articles module="users" function="GetUsersVip" user_id="$magic.request.user_id" var="Vvar"}
	<div class="module_border">
		<div class="l">Vip有效时间/是否认证：</div>
		<div class="c">	{$Vvar.first_date|date_format:"Y-m-d"|default:-}~{$Vvar.end_date|date_format:"Y-m-d"|default:-}/{if $Vvar.status==1}已认证{else}未认证{/if}
		</div>
	</div>
{/articles}
{articles module="account"  function="GetOne" var="Avar" user_id="$magic.request.user_id"}
	
{articles module="borrow" plugins="count" function="GetUsersRecoverCount" var="recover_var" user_id="$magic.request.user_id"}	
</div>
<div class="module_title"><strong>资金详情</strong></div>
  <table width="100%">
  <tr>
    <td width="15%" valign="top" >账户总额 </td>
    <td width="15%" valign="top" >{$Avar.total|default:0.00} </td>
    <td width="15%" valign="top" >可用余额 </td>
    <td width="15%" valign="top"  > {$Avar.balance|default:0.00}</td>
    <td width="15%" valign="top"  >冻结金额 </td>
    <td width="15%" valign="top" > {$Avar.frost|default:0.00}</td>
  </tr>
  {articles module="account" function="GetRechargeCount" var="Rvar" user_id='$magic.request.user_id'}
   {list module="account" function="GetCashList" var="loop" user_id="$magic.request.user_id" epage=20}
  <tr>
    <td width="15%" valign="top" >投标冻结总额： </td>
    <td width="15%" valign="top" >{ $recover_var.tender_now_account|default:0.00} </td>
    <td width="15%" valign="top" >充值成功总额： </td>
    <td width="15%" valign="top"  >{$Rvar.account_balance|default:0.00} </td>
    <td width="15%" valign="top"  >提现成功总额： </td>
    <td width="15%" valign="top" > {$loop.credited_all|default:0.00}</td>
  </tr>
  <tr>
    <td width="15%" valign="top" >充值手续费：</td>
    <td width="15%" valign="top" >{$Rvar.account_fee|default:0.00} </td>
    <td width="15%" valign="top" > 提现手续费：</td>
    <td width="15%" valign="top"  >{$loop.fee_all|default:0.00}</td>
    <td width="15%" valign="top"  > </td>
    <td width="15%" valign="top" > </td>
  </tr>
  {/list}
  {/articles}
  </table>
 {articles module="borrow" plugins="Amount" function="GetAmountUsers" user_id=$magic.request.user_id var="user_amount"}
<div class="module_title"><strong>额度情况：</strong></div>
  <table width="100%">
  {if $user_amount.credit_status==1}
 <tr>
    <td width="15%" valign="top" >信用总额度： </td>
    <td width="15%" valign="top" >￥{$user_amount.credit|default:"0.00"}</td>
    <td width="15%" valign="top" >可用信用额度: </td>
    <td width="15%" valign="top" >￥{$user_amount.credit_use|default:"0.00"}</td>
    <td width="15%" valign="top"  >净值额度： </td>
    <td width="15%" valign="top"  >￥{$user_amount.worth|round:"2"|default:"0.00"}</td>
  </tr>
  {/if}
  {if $user_amount.vouch_status==1}
  <tr>
    <td width="15%" valign="top" >担保总额度 </td>
    <td width="15%" valign="top" > ￥{$user_amount.vouch|round:"2"|default:"0.00"}</td>
    <td width="15%" valign="top" >可用担保额度： </td>
    <td width="15%" valign="top" > ￥{$user_amount.vouch_use|round:"2"|default:"0.00"}</td>
    <td width="15%" valign="top"  ></td>
    <td width="15%" valign="top"  > </td>
  </tr>
  {/if}
  {if $user_amount.pawn_status==1}
  <tr>
    <td width="15%" valign="top" >授信总额度： </td>
    <td width="15%" valign="top" >￥{$user_amount.pawn|round:"2"|default:"0.00"}</td>
    <td width="15%" valign="top" >可用授信额度： </td>
    <td width="15%" valign="top" > ￥{$user_amount.pawn_use|round:"2"|default:"0.00"}</td>
    <td width="15%" valign="top"  > </td>
    <td width="15%" valign="top"  > </td>
  </tr>
  {/if}
  {if $user_amount.vest_status==1}
  <tr>
	<td width="15%" valign="top" >流转额度： </td>
    <td width="15%" valign="top" >￥{$user_amount.vest|round:"2"|default:"0.00"}</td>
    <td width="15%" valign="top" >可用流转额度： </td>
    <td width="15%" valign="top" > ￥{$user_amount.vest_use|round:"2"|default:"0.00"}</td>
    <td width="15%" valign="top"  > </td>
    <td width="15%" valign="top"  > </td>
  </tr>
  {/if}
</table>
{/articles}
{/articles}
{articles module="borrow" plugins="count" function="GetUsersRepayCount" var="repay_var" user_id="$magic.request.user_id"}
{articles module="borrow" plugins="count" function="GetUsersRecoverCount" var="recover_var" user_id="$magic.request.user_id"}
<div class="module_title"><strong>借款统计</strong></div>
  <table width="100%">
  <tr>
    <td width="12%" valign="top" title="注册至今，您累计借入的总额">借入总额:</td>
    <td width="12%" valign="top" >￥{$repay_var.borrow_success_account|default:0.00}</td>
    <td width="12%" valign="top" > </td>
    <td width="12%" valign="top" > </td>
    <td width="12%" valign="top" > </td>
    <td width="12%" valign="top" > </td>
    <td width="12%" valign="top" ></td>
    <td width="12%" valign="top" > </td>
  </tr>
  <tr>
    <td width="12%" valign="top" >待还总额:</td>
    <td width="12%" valign="top" >￥{ $repay_var.repay_wait_account|default:0.00} </td>
    <td width="12%" valign="top" >待还期数:</td>
    <td width="12%" valign="top" > {$repay_var.repay_wait_num|default:0}期</td>
    <td width="12%" valign="top" >已还总额:</td>
    <td width="12%" valign="top" >￥{$repay_var.repay_yes_account|default:0.00}</td>
    <td width="12%" valign="top" >已还清期数:</td>
    <td width="16%" valign="top" >{$repay_var.repay_yes_num|default:0}期</td>
  </tr>
  <tr>
    <td width="12%" valign="top" >发布借款笔数:</td>
    <td width="12%" valign="top" >{$repay_var.borrow_loan_num|default:0}笔 </a></td>
    <td width="12%" valign="top" >待还笔数：</td>
    <td width="12%" valign="top" >{$repay_var.repay_wait_times|default:0}笔</td>
    <td width="12%" valign="top" >已还笔数：</td>
    <td width="12%" valign="top" > {$repay_var.repay_yes_times|default:0}笔</td>
    <td width="12%" valign="top" >逾期次数: </td>
    <td width="12%" valign="top" >{$repay_var.repay_late_num|default:0}次 </td>
  </tr>
    <tr>
    <td width="12%" valign="top" >最近应还款金额：</td>
	<td width="12%" valign="top" ><font>￥{$repay_var.repay_wait_now_account|default:0.00}</font></td>
    <td width="12%" valign="top" >最近还款日期：</td>
	<td width="12%" valign="top" >{$repay_var.repay_wait_now_time|date_format:"Y/m/d"|default"/"}</td>
  </tr>
</table>


<div class="module_title"><strong>投资统计</strong></div>
  <table width="100%">
  <tr >
    <td width="12%" valign="top" >总投资金额： </td>
    <td width="12%" valign="top" >￥{ $recover_var.tender_success_account|default:0.00} </td>
    <td width="12%" valign="top" title="各笔收益率的加权平均值">投资平均收益率： </td>
    <td width="12%" valign="top" > { $recover_var.tender_recover_scale|default:0.00}%</td>
    <td width="12%" valign="top" tltle="逾期投资金额/累计投资金额"> 坏账率：</td>
    <td width="12%" valign="top" >{ $recover_var.tender_false_scale|default:0.00}% </td>
    <td width="12%" valign="top" >网站垫付总额</td>
    <td width="12%" valign="top" >￥{ $recover_var.recover_web_account|default:0.00}</td>
  </tr>
  <tr >
    <td width="12%" valign="top" >已回收总额： </td>
    <td width="12%" valign="top" > ￥{ $recover_var.recover_yes_account|default:0.00}</td>
    <td width="12%" valign="top" >已回收本金 </td>
    <td width="12%" valign="top" >￥{ $recover_var.recover_yes_capital|default:0.00} </td>
    <td width="12%" valign="top" >已回收利息 </td>
    <td width="12%" valign="top" >￥{ $recover_var.recover_yes_interest|default:0.00} </td>
    <td width="12%" valign="top" >已回收期数 </td>
    <td width="12%" valign="top" > { $recover_var.recover_yes_num|default:0}期</td>
  </tr>
  <tr>
    <td width="12%" valign="top" >待回收总额： </td>
    <td width="12%" valign="top" > ￥{ $recover_var.recover_wait_account|default:0.00}</td>
    <td width="12%" valign="top" >待回收本金：</td>
    <td width="12%" valign="top" > ￥{ $recover_var.recover_wait_capital|default:0.00}</td>
    <td width="12%" valign="top" >待回收利息：</td>
    <td width="12%" valign="top" > ￥{ $recover_var.recover_wait_interest|default:0.00}</td>
    <td width="12%" valign="top" >待回收期数：</td>
    <td width="16%" valign="top" > { $recover_var.recover_wait_num|default:0}期</td>
  </tr>
  <tr>
    <td width="12%" valign="top" >已赚奖励： </td>
    <td width="12%" valign="top" >￥{ $recover_var.tender_award_fee|default:0.00}</td>
    <td width="12%" valign="top" >逾期罚金收入：</td>
    <td width="12%" valign="top" > ￥{ $recover_var.recover_fee_account|default:0.00}</td>
    <td width="12%" valign="top" >提前还款罚金收入：</td>
    <td width="12%" valign="top" >￥{ $recover_var.tender_advance_account|default:0.00}</td>
    <td width="12%" valign="top" >损失利息总额： </td>
    <td width="12%" valign="top" > ￥{ $recover_var.recover_loss_account|default:0.00}</td>
  </tr>

</table>
{/articles}
 {/articles}
<!--用户记录列表 开始-->
{elseif $_A.query_type == "log"}
<div class="module_add">
	<div class="module_title"><strong>用户记录</strong><span style="float:right">
		{$MsgInfo.users_name_username}：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>  {$MsgInfo.users_name_email}：<input type="text" name="email" id="email" value="{$magic.request.email}"/>   <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()"></span></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">{$MsgInfo.users_name_id}</td>
		<td width="*" class="main_td">{$MsgInfo.users_name_username}</td>
		<td width="*" class="main_td">{$MsgInfo.users_name_code}</td>
		<td width="*" class="main_td">{$MsgInfo.users_name_type}</td>
		<td width="*" class="main_td">{$MsgInfo.users_name_operating}</td>
		<td width="*" class="main_td">{$MsgInfo.users_name_operating_id}</td>
		<td width="*" class="main_td">{$MsgInfo.users_name_result}</td>
		<th width="" class="main_td">{$MsgInfo.users_name_content}</th>
		<th width="" class="main_td">{$MsgInfo.users_name_add_time}</th>
		<th width="" class="main_td">{$MsgInfo.users_name_add_ip}</th>
	</tr>
	{ list module="users" function="GetUserslogList" var="loop" username=request email=request epage="20" page="request"}
		{foreach from=$loop.list item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.username|default:-}</td>
		<td class="main_td1" align="center">{$item.code}</td>
		<td class="main_td1" align="center" >{$item.type}</td>
		<td class="main_td1" align="center" >{$item.operating}</td>
		<td class="main_td1" align="center" >{$item.article_id}</td>
		<td class="main_td1" align="center" >{if $item.result==1}<font color="#006600">{$MsgInfo.users_name_success}</font>{else}<font color="#FF0000">{$MsgInfo.users_name_false}</font>{/if}</td>
		<td class="main_td1" align="center" width="200" >{$item.content}</td>
		<td class="main_td1" align="center" >{$item.addtime|date_format:"Y-m-d H:i:s"}</td>
		<td class="main_td1" align="center" >{$item.addip}</td>
	</tr>
	{/foreach}
	
	<tr>
			<td colspan="11" class="action">
			<div class="floatl">
			<script>
	  var url = '{$_A.query_url_all}';
	    {literal}
	  	function sousuo(){
			var username = $("#username").val();
			var email = $("#email").val();
			location.href=url+"&username="+username+"&email="+email;
		}
	  
	  </script>
	  {/literal}
			</div>
			<div class="floatr">
			</div>
			</td>
		</tr>
	<tr align="center">
		<td colspan="10" align="center"><div align="center">{$loop.pages|showpage}</div></td>
	</tr>
	{ /list}
</table>
 
<!--用户记录列表 结束-->

{elseif $_A.query_type == "new" || $_A.query_type == "edit"}
<div class="module_add">
	
	<form  name="form_user" method="post" action="" { if $_A.query_type == "new" }onsubmit="return check_user();"{/if} >
	<div class="module_title"><strong>{ if $_A.query_type == "edit" }{$MsgInfo.users_name_edit}{else}{$MsgInfo.users_name_new}{/if}</strong></div>
	
	<div class="module_border">
		<div class="l">{$MsgInfo.users_name_username}：</div>
		<div class="c">
			{ if $_A.query_type != "edit" }<input name="username" type="text"  class="input_border" />{else}{ $_A.users_result.username}<input name="username" type="hidden"  class="input_border" value="{$_A.users_result.username}" />{/if} <font color="#FF0000">*</font>
		</div>
	</div>
	<div class="module_border">
		<div class="l">邮箱：</div>
		<div class="c">
		<input name="email" type="text"  class="input_border" value="{$_A.users_result.email}" /> <font color="#FF0000">*</font>
		</div>
	</div>
	<div class="module_border">
		<div class="l">{$MsgInfo.users_name_password}：</div>
		<div class="c">
			<input name="password" type="password" class="input_border" />{ if $_A.query_type == "edit" } {$MsgInfo.users_name_edit_not_empty}{/if} <font color="#FF0000">*</font>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">{$MsgInfo.users_name_password1}：</div>
		<div class="c">
			<input name="password1" type="password" class="input_border" />{ if $_A.query_type == "edit" } {$MsgInfo.users_name_edit_not_empty}{/if} <font color="#FF0000">*</font>
		</div>
	</div>
	<div class="module_border">
		<div class="l">支付密码：</div>
		<div class="c">
			<input name="paypassword" type="password" class="input_border" />{ if $_A.query_type == "edit" } {$MsgInfo.users_name_edit_not_empty}{/if} <font color="#FF0000">*</font>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">确认支付密码：</div>
		<div class="c">
			<input name="paypassword1" type="password" class="input_border" />{ if $_A.query_type == "edit" } {$MsgInfo.users_name_edit_not_empty}{/if} <font color="#FF0000">*</font>
		</div>
	</div>
	
	
	<div class="module_submit border_b" >
	{ if $_A.query_type == "edit" }<input type="hidden" name="user_id" value="{ $_A.users_result.user_id }" />{/if}
	<input type="submit" value="{$MsgInfo.users_name_submit}" /><input type="hidden" name="status" value="1" />
	<input type="reset" name="reset" value="{$MsgInfo.users_name_reset}" />
	</div>
	</form>
</div>
{literal}
<script>
function check_user(){
	 var frm = document.forms['form_user'];
	 var username = frm.elements['username'].value;
	 var password = frm.elements['password'].value;
	  var password1 = frm.elements['password1'].value;
	   var email = frm.elements['email'].value;
	 var errorMsg = '';
	  if (username.length == 0 ) {
		errorMsg += '<? echo $this->magic_vars['MsgInfo']['users_username_empty']; ?> \n';
	  }
	   if (username.length<4) {
		errorMsg += '<? echo $this->magic_vars['MsgInfo']['users_username_long4']; ?> \n';
	  }
	  if (password.length==0) {
		errorMsg += '<? echo $this->magic_vars['MsgInfo']['users_password_empty']; ?> \n';
	  }
	  if (password.length<6) {
		errorMsg += '<? echo $this->magic_vars['MsgInfo']['users_password_long6']; ?> \n';
	  }
	   if (password.length!=password1.length) {
		errorMsg += '<? echo $this->magic_vars['MsgInfo']['users_password_error']; ?> \n';
	  }
	   if (email.length==0) {
		errorMsg += '<? echo $this->magic_vars['MsgInfo']['users_email_empty']; ?> \n';
	  }
	  if (errorMsg.length > 0){
		alert(errorMsg); return false;
	  } else{  
		return true;
	  }
}
</script>
{/literal}




<!--审核记录列表 开始-->
{elseif $_A.query_type == "examine"}
<div class="module_add">
<div class="module_title"><strong>审核记录列表</strong><span style="float:right">
	{$MsgInfo.users_name_username}：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>    <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()"></span></div>
</div> 
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">id</td>
		<td width="*" class="main_td">审核人</td>
		<td width="*" class="main_td">模块</td>
		<td width="*" class="main_td">类型</td>
		<td width="*" class="main_td">文章</td>
		<th width="" class="main_td">结果</th>
		<td width="*" class="main_td">审核备注</td>
		<td width="*" class="main_td">审核时间</td>
	</tr>
	{ list module="users" function="GetExamineList" var="loop" username=request  epage="20" page="request"}
		{foreach from=$loop.list item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.username|default:-}</td>
		<td class="main_td1" align="center">{$item.code}</td>
		<td class="main_td1" align="center" >{$item.type}</td>
		<td class="main_td1" align="center" >{$item.article_id}</td>
		<td class="main_td1" align="center" >{if $item.result==1}<font color="#006600">{$MsgInfo.users_name_success}</font>{else}<font color="#FF0000">{$MsgInfo.users_name_false}</font>{/if}(result={$item.result})</td>
		<td class="main_td1" align="center" >{$item.remark}</td>
		<td class="main_td1" align="center" >{$item.addtime|date_format:"Y-m-d H:i:s"}</td>
	</tr>
	{/foreach}
	
	<tr>
			<td colspan="11" class="action">
			<div class="floatl">
			<script>
	  var url = '{$_A.query_url_all}';
	    {literal}
	  	function sousuo(){
			var username = $("#username").val();
			var email = $("#email").val();
			location.href=url+"&username="+username+"&email="+email;
		}
	  
	  </script>
	  {/literal}
			</div>
			<div class="floatr">
			</div>
			</td>
		</tr>
	<tr align="center">
		<td colspan="10" align="center"><div align="center">{$loop.pages|showpage}</div></td>
	</tr>
	{ /list}
</table>
<!--审核记录列表 结束-->



{elseif $_A.query_type=="type"}

<div class="module_add">
	<div class="module_title"><strong>用户类型</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
	<div style="border:1px solid #CCCCCC; ">
	
	<form action="{$_A.query_url_all}" method="post">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="id" value="{$_A.users_type_result.id}" />修改用户类型 （<a href="{$_A.query_url_all}">添加</a>）{else}添加用户类型{/if}</strong></div>
	
	
	<div class="module_border">
		<div class="l">类型名称：</div>
		<div class="c">
			<input type="text" name="name" value="{$_A.users_type_result.name}"/>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">标识名：</div>
		<div class="c">
			<input type="text" name="nid" value="{$_A.users_type_result.nid}" onkeyup="value=value.replace(/[^a-z0-9_]/g,'')"/>
		</div>
	</div>
	<div class="module_border">
		<div class="l">默&nbsp;&nbsp;认：</div>
		<div class="c">
			{input type="radio" name="checked" value="0|否,1|是" checked="$_A.users_type_result.checked"}（注册的时候用户默认的类型）
		</div>
	</div>
	<div class="module_border">
		<div class="l">描述：</div>
		<div class="c">
			<textarea name="remark" rows="5" cols="30">{$_A.users_type_result.remark}</textarea>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">排序：</div>
		<div class="c">
			<input type="text" name="order" value="{$_A.users_type_result.order|default:10}" size="8"/>
		</div>
	</div>
	
	<div class="module_border" >
		<div class="l">验证码：</div>
		<div class="c">
			<input name="valicode" type="text" size="11" maxlength="4"  onClick="$('#valicode').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
		
			<img src="/?plugins&q=imgcode" id="valicode" alt="点击刷新" onClick="this.src='/?plugins&q=imgcode&t=' + Math.random();" align="absmiddle" style="cursor:pointer" />
		</div>
	</div>
	
	
	<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
		</form>
	</div>
	</div>
		</div>
	<div style="float:right; width:67%; text-align:left">
	<div class="module_add">
	
	
	
	<div class="module_title"><strong>用户类型列表</strong></div>
	</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">名称</td>
		<td width="" class="main_td">标识名</td>
		<td width="*" class="main_td">添加时间</td>
		<td width="*" class="main_td">是否默认</td>
		<td width="*" class="main_td">排序</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="users" function="GetUsersTypeList" var="loop" username=request epage=20}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.name}</td>
		<td class="main_td1" align="center">{$item.nid}</td>
		<td class="main_td1" align="center">{$item.addtime|date_format}</td>
		<td class="main_td1" align="center">{if $item.checked==1}是{else}<a href="{$_A.query_url_all}&checked={$item.id}" title="设为默认">否</a>{/if}</td>
		<td class="main_td1" align="center">{$item.order}</td>
		<td class="main_td1" align="center"><a href="{$_A.query_url_all}&edit={$item.id}">修改</a>/<a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url_all}&del={$item.id}'">删除</a></td>
	</tr>
	{/foreach}
	
	<tr align="center">
		<td colspan="10" align="center"><div align="center">{$loop.pages|showpage}</div></td>
	</tr>
	{/list}
	
</table>

<!--菜单列表 结束-->
</div>
</div>

{elseif $_A.query_type == "rebut" }

<div class="module_add">
	<div class="module_title"><strong>用户记录</strong><span style="float:right">
		被举报用户：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>     <input type="button" value="{$MsgInfo.users_name_sousuo}"  onclick="sousuo()"/></span></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="*" class="main_td">举报用户</td>
		<td width="*" class="main_td">被举报用户</td>
		<td width="*" class="main_td">类型</td>
		<td width="*" class="main_td">举报留言</td>
		<td width="*" class="main_td">举报时间</td>		
	</tr>
	{ list module="users" function="GetUsersRebutList" var="loop" username=request epage="20"}
	{foreach from=$loop.list item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{ $item.username}</td>
		<td class="main_td1" align="center">{ $item.rebut_username}</td>
		<td class="main_td1" align="center">{if $item.type_id==1}欺诈{else}威胁{/if}</td>
		<td class="main_td1" align="center">{$item.contents}</td>		
		<td class="main_td1" align="center">{$item.addtime|date_format}</td>		
	</tr>
	{/foreach}
	<tr>
			<td colspan="11" class="action">
			<div class="floatl">
			<script>
	  var url = '{$_A.query_url}/rebut';
	    {literal}
	  	function sousuo(){
			var username = $("#username").val();			
			location.href=url+"&username="+username;
		}
	  </script>
	  {/literal}
			</div>
			<div class="floatr">
			</div>
			</td>
		</tr>
	<tr align="center">
		<td colspan="10" align="center"><div align="center">{$loop.pages|showpage}</div></td>
	</tr>
	
	{ /list}
</table>
{elseif $_A.query_type == "manage" }
	{if $magic.request.check==''}
	<ul class="nav3"> 
	<li><a href="{$_A.query_url_all}" {if $magic.request.type==""}style="color:red"{/if}>理财师</a></li> 
	<li><a href="{$_A.query_url_all}&type=award" {if $magic.request.type=="award"}style="color:red"{/if}>推广奖励</a></li> 
	</ul>
	{/if}
	{if $magic.request.check!=''}
	
	<div  >
	<form name="form1" method="post" action="{$_A.query_url_all}" >
	<div class="module_border_ajax">
		<div class="l">用户名:</div>
		<div class="c">
		{$_A.user_manage.username}
		</div>
	</div>	
	<div class="module_border_ajax" >
		<div class="l">真实姓名:</div>
		<div class="c">
		{$_A.user_manage.realname}
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">身份证号:</div>
		<div class="c">
		{$_A.user_manage.card_id}
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">性 别:</div>
		<div class="c">
		{$_A.user_manage.sex|linkages:"rating_sex"}
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">出生日期:</div>
		<div class="c">
	{$_A.user_manage.rating_birthday_year|linkages:"rating_birthday_year"}
	{$_A.user_manage.rating_birthday_mouth|linkages:"rating_birthday_mouth"}
	{$_A.user_manage.rating_birthday_day|linkages:"rating_birthday_day"}
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">学 历:</div>
		<div class="c">
		{$_A.user_manage.edu|linkages:"rating_education"}
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">联系地址:</div>
		<div class="c">
			{$_A.user_manage.address}
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">邮箱:</div>
		<div class="c">
			{$_A.user_manage.email}
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">紧急联系人:</div>
		<div class="c">
			{$_A.user_manage.linkman}
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">紧急联系人电话:</div>
		<div class="c">
			{$_A.user_manage.linktel}
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">个人简历:</div>
		<div class="c">
			{$_A.user_manage.resume}
		</div>
	</div>
		{if $_A.user_manage.status==0}
		<div class="module_border_ajax" >
			<div class="l">审核状态:</div>
			<div class="c">
			<input type="radio" name="status" checked="checked" value="1">通过	
			<input type="radio" name="status" value="2">不通过	
			</div>
		</div>
		<div class="module_border_ajax" >
			<div class="l">审核备注:</div>
			<div class="c">
			<input type="text" name="verify_remark" >
			</div>
		</div>	
		<!-- <div class="module_border_ajax" >
			<div class="l">验证码:</div>
			<div class="c">
				<input name="valicode" type="text" size="11" maxlength="4"  tabindex="3" />
			</div>
			<div class="c">
				<img src="/?plugins&q=imgcode" id="valicode" onClick="$('#valicode').attr('src','/?plugins&q=imgcode&t=' + Math.random())" alt="点击刷新"  align="absmiddle" style="cursor:pointer" />
			</div>
		</div> -->	
		<div class="module_submit_ajax" >
		<input type="hidden" name="user_id" value="{$_A.user_manage.user_id}" />
		<input type="submit" name="submit" class="submit_button" value="提交" />
		</div>
		{else}
		<div class="module_border_ajax" >
			<div class="l">审核状态:</div>
			<div class="c">
			{if $_A.user_manage.status==1}通过{else}不通过{/if}			
			</div>
		</div>
		<div class="module_border_ajax" >
			<div class="l">审核备注:</div>
			<div class="c">
			{$_A.user_manage.verify_remark}			
			</div>
		</div>	
		{/if}
	</form>
	</div>
	
	{elseif $magic.request.type=='award'}
	
	<div class="module_add">
		<div class="module_title"><strong>推广奖励</strong><span style="float:right">
			推荐人:<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>     
			发生日期：<input type="text" name="dotime1" id="dotime1" value="{$magic.request.dotime1}" size="15" onclick="change_picktime()"/> 到 <input type="text"  name="dotime2" value="{$magic.request.dotime2}" id="dotime2" size="15" onclick="change_picktime()"/>
			<input type="button" value="{$MsgInfo.users_name_sousuo}"  onclick="sousuo()"/></span></div>
	</div>
	<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
		<tr >
			<td width="" class="main_td">ID</td>
			<td width="*" class="main_td">推荐人</td>
			<td width="*" class="main_td">邀请人</td>
			<td width="*" class="main_td">发生日期</td>
			<td width="*" class="main_td">投资金额</td>
			<td width="*" class="main_td">投资期限</td>			
			<td width="*" class="main_td">年化利率</td>			
			<td width="*" class="main_td">奖金金额</td>			
		</tr>
		{list  module="users" function="GetManageAccountList" var="loop" dotime1=request username=request dotime2=request  showpage="3"}
		{foreach from=$loop.list item="item"}
		<tr {if $key%2==1} class="tr2"{/if}>
			<td class="main_td1" align="center">{ $item.id}</td>
			<td class="main_td1" align="center">{ $item.username}</td>
			<td class="main_td1" align="center">{ $item.tender_username}</td>
			<td class="main_td1" align="center">{$item.addtime|date_format:"Y-m-d"}</td>
			<td class="main_td1" align="center">{ $item.tender_account}元</td>
			<td class="main_td1" align="center">{ $item.tender_period}个月</td>
			<td class="main_td1" align="center">{ $item.tender_apr}%</td>
			<td class="main_td1" align="center">{ $item.award}元</td>
			
		</tr>
		{/foreach}
		<tr>
		    <td colspan="11" class="action">
			<div class="floatl">
			<script>
		  var url = '{$_A.query_url}/manage&type=award';
			{literal}
			var _url='';
			function sousuo(){
				var username = $("#username").val();
				var dotime1 = $("#dotime1").val();
				var dotime2 = $("#dotime2").val();
				if(username!=''){
					_url+="&username="+username;
				}
				if(dotime1!=''){
					_url+="&dotime1="+dotime1;
				}
				if(dotime2!=''){
					_url+="&dotime2="+dotime2;
				}
				
				location.href=url+_url;
			}
		  </script>
		  {/literal}
				</div>
				<div class="floatr">
				</div>
				</td>
			</tr>
		<tr align="center">
			<td colspan="10" align="center"><div align="center">{$loop.pages|showpage}</div></td>
		</tr>
		
		{ /list}
	</table>	
	
	{else}
	
	<div class="module_add">
		<div class="module_title"><strong>理财师</strong><span style="float:right">
			用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>     <input type="button" value="{$MsgInfo.users_name_sousuo}"  onclick="sousuo()"/></span></div>
	</div>
	<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
		<tr >
			<td width="" class="main_td">ID</td>
			<td width="*" class="main_td">用户名</td>
			<td width="*" class="main_td">申请时间</td>
			<td width="*" class="main_td">状态</td>
			<td width="*" class="main_td">操作(审核查看）</td>			
		</tr>
		{ list module="users" function="GetUserManageList" var="loop" username=request epage="20"}
		{foreach from=$loop.list item="item"}
		<tr {if $key%2==1} class="tr2"{/if}>
			<td class="main_td1" align="center">{ $item.id}</td>
			<td class="main_td1" align="center">{ $item.username}</td>
			<td class="main_td1" align="center">{ $item.addtime|date_format:"Y-m-d"}</td>		
			<td class="main_td1" align="center">{if $item.status==0}审核中{elseif  $item.status==1}审核成功{elseif  $item.status==2}审核失败{/if}</td>		
			<td class="main_td1" align="center"><a href="javascript:void(0)" onclick='tipsWindown("审核","url:get?{$_A.query_url_all}&check={$item.user_id}",500,700,"true","","false","text");' />{if $item.status==0}审核{else}查看{/if}</a></td>		
		</tr>
		{/foreach}
		<tr>
		    <td colspan="11" class="action">
			<div class="floatl">
			<script>
		  var url = '{$_A.query_url}/manage';
			{literal}
			function sousuo(){
				var username = $("#username").val();			
				location.href=url+"&username="+username;
			}
		  </script>
		  {/literal}
				</div>
				<div class="floatr">
				</div>
				</td>
			</tr>
		<tr align="center">
			<td colspan="10" align="center"><div align="center">{$loop.pages|showpage}</div></td>
		</tr>
		
		{ /list}
	</table>
	{/if}
{elseif $_A.query_type == "vip" ||  $_A.query_type == "vipview" }

	{include file="users.vip.tpl" template_dir = "modules/users"}
	
{elseif $_A.query_type == "viewinfo" ||  $_A.query_type == "viewinfo" }

	{include file="users.viewinfo.tpl" template_dir = "modules/users"}


{elseif $_A.query_type == "admin" ||  $_A.query_type == "admin_log"  ||  $_A.query_type == "admin_type" }

	{include file="users.admin.tpl" template_dir = "modules/users"}

{/if}