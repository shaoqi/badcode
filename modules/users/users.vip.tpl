<ul class="nav3"> 
<li><a href="{$_A.query_url_all}" {if $magic.request.status==""}id="c_so"{/if}>全部</a></li> 
<li><a href="{$_A.query_url_all}&status=-1" {if $magic.request.status=="-1"}id="c_so"{/if}>待审核</a></li> 
<li><a href="{$_A.query_url_all}&status=1" {if $magic.request.status=="1"}id="c_so"{/if}>已审</a></li>
<li><a href="{$_A.query_url_all}&status=2" {if $magic.request.status=="2"}id="c_so"{/if}>审核失败</a></li>
<li><a href="javascript:addvip();" title="新增VIP会员">新增VIP会员</a></li>
</ul> 
{if $magic.request.action == ""  }
<div class="module_add">
	<div class="module_title"><strong>VIP会员列表</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="*" class="main_td">用户名</td>
        {if $magic.request.status==1}
		<td width="*" class="main_td">客服名称</td>
        {/if}
		<td width="*" class="main_td">vip期限</td>
		<td width="*" class="main_td">开始时间</td>
		<th width="" class="main_td">结束时间</th>
		<th width="" class="main_td">状态</th>
		<th width="" class="main_td">是否缴费</th>
		<td width="" class="main_td">操作</td>
	</tr>
	{list module="users" plugins="vip" function="GetUsersVipList" var="loop" status='request'  username='request'  adminname='request' }
	{ foreach  from=$loop.list key=key item=item}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.user_id}</td>
		<td class="main_td1" align="center">{$item.username}</td>
        {if $magic.request.status==1}
		<td class="main_td1" align="center">{$item.adminname}</td>
        {/if}
		<td class="main_td1" align="center">{$item.years}年</td>
		<td class="main_td1" align="center" >{$item.first_date|date_format:"Y-m-d"|default:"-"}</td>
		<td class="main_td1" align="center" >{$item.end_date|date_format:"Y-m-d"|default:"-"}</td>
		<td class="main_td1" align="center">{if $item.status==-1}待审核{elseif $item.status==2}不通过{elseif $item.status==0}未申请{else}VIP会员{/if}</td>
		<td class="main_td1" align="center">{if $item.money>0}{$item.money}元{else}无{/if}</td>
		<td class="main_td1" align="center"><a href="{$_A.query_url}/vip&action=view&user_id={$item.user_id}{$_A.site_url}">审核查看</a> </td>
	</tr>
	{ /foreach}
	<tr>
			<td colspan="10" class="action">
			<div class="floatl">
			<script>
	  var url = '{$_A.query_url}/vip&type={$magic.request.type}';
	    {literal}
	  	function sousuo(){
			var username = $("#username").val();
			var adminname = $("#adminname").val();
			var status = $("#status").val();
			location.href=url+"&username="+username+"&adminname="+adminname+"&status="+status;
		}
	  
	  </script>
	  {/literal}
			</div>
			<div class="floatr">
				用户名：<input type="text" name="username" id="username" value="{$magic.request.username}"/> 	客服用户名：<input type="text" name="adminname" id="adminname" value="{$magic.request.adminname|urldecode}"/>	状态：<select name="status" id="status"><option value="">全部</option><option value="0" {if $magic.request.status=="0"} selected="selected"{/if}>未申请</option><option value="1"  {if $magic.request.status==1} selected="selected"{/if}>审核通过</option><option value="2"  {if $magic.request.status==2} selected="selected"{/if}>审核不通过</option><option value="-1"  {if $magic.request.status==-1} selected="selected"{/if}>待审核</option></select><input type="button" value="搜索" / onclick="sousuo()">
			</div>
			</td>
		</tr>
	<tr>
		<td colspan="10" class="page">
		{$loop.pages|showpage}
		</td>
	</tr>
	{/list}
</table>
<div  style="height:205px; overflow:hidden;display:none;" id="addvip">
	<div class="module_border_ajax">
		<div class="l">用户名:</div>
		<div class="c">
		<input type="text" id="uname" name="uname">
	</div>
	<div class="module_border_ajax" >
		<div class="l">验证码:</div>
		<div class="c">
			<input name="valicode" type="text" size="11" maxlength="4"  tabindex="3" id="valicode" />
		</div>
		<div class="c">
			<img src="/?plugins&q=imgcode" id="valicode1" alt="点击刷新" onClick="this.src='/?plugins&q=imgcode&t=' + Math.random();" align="absmiddle" style="cursor:pointer" />
		</div>
	</div>

	<div class="module_submit_ajax" >
		<input type="button"  name="reset" class="submit_button" value="确认审核" onclick="doaddvip()"/>
	</div>
</div>
{literal}
<script>
function addvip(){
	tipsWindown('新增vip','id:addvip',"280","120","true","","1","");
}
function doaddvip(){
	var uname = $('#windown-content #uname').val();
	if(!uname){
		alert('请输入用户名');
		return false;
	}
	var valicode = $('#windown-content #valicode').val();
	if(!valicode){
		alert('请输入验证码');
		return false;
	}
	$.post('/?dyryr&q=code/users/vip&action=add',{uname:uname,valicode:valicode},function(data){
		alert(data);
	});
}
</script>
{/literal}
{ elseif $magic.request.action == "view"  }
<div class="module_add">
	
	<form enctype="multipart/form-data" name="form1" method="post" action=""  >
	<div class="module_title"><strong>VIP审核查看</strong></div>
	
	<div class="module_border">
		<div class="l">用户名:</div>
		<div class="c">
			{$_A.vip_result.username}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">审核:</div>
		<div class="c">{if $_A.vip_result.status=="1"}
		已通过<input type="hidden" value="1" name="status" />
		{else}
			<input type="radio" value="1" name="status" {if $_A.vip_result.status=="1"} checked="checked"{/if} />审核通过 <input type="radio" value="2" name="status"  {if $_A.vip_result.status=="2" || $_A.vip_result.status==""} checked="checked"{/if}/>审核不通过 
			{/if}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">客服:</div>
		<div class="c">
			<select name="kefu_userid" id="kefu_userid">
				<option value="">请选择</option>
				{loop module="users" function="GetUsersAdminList" limit="all" type_id="9"}
				<option value="{$var.user_id}" {if $var.user_id==$_A.vip_result.kefu_userid} selected="selected"{/if}>{$var.adminname}</option>
				{/loop}
				</select>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">申请期限:</div>
		<div class="c">
			1年
			<input type="hidden" value="1" name="years" value="{$_A.vip_result.years}" />
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="verify_remark" cols="55" rows="6" >{$_A.vip_result.verify_remark}</textarea>
		</div>
	</div>
	
	<div class="module_submit" >
	<input type="hidden" name="user_id" value="{$_A.vip_result.user_id}" />
		<input type="submit" value="确认提交" />
		<input type="reset" name="reset" value="重置表单" />
	</div>
	</form>
{/if}