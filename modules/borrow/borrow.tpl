{if $_A.sub_dir!=""}
	{include file="$_A.sub_dir" template_dir = "modules/borrow"}
{elseif $_A.query_type == "view"  }
	{include file="borrow.view.tpl" template_dir = "modules/borrow"}
    
{elseif $_A.query_type == "flag" }
	{include file="borrow.flag.tpl" template_dir = "modules/borrow"}
{elseif $_A.query_type == "type" }
	{include file="borrow.type.tpl" template_dir = "modules/borrow"}
{elseif $_A.query_type == "manage" }
	{include file="borrow.manage.tpl" template_dir = "modules/borrow"}
{elseif $_A.query_type == "style" }
	{include file="borrow.style.tpl" template_dir = "modules/borrow"}
{elseif $_A.query_type == "first" }
	{include file="borrow.first.tpl" template_dir = "modules/borrow"}
{elseif $_A.query_type == "roam" }
	{include file="borrow.roam.tpl" template_dir = "modules/borrow"}
{elseif $_A.query_type == "repay" }
	{include file="borrow.repay.tpl" template_dir = "modules/borrow"}
{elseif $_A.query_type == "recover" }
	{include file="borrow.recover.tpl" template_dir = "modules/borrow"}
{elseif $_A.query_type == "full" }
	{include file="borrow.full.tpl" template_dir = "modules/borrow"}
{elseif $_A.query_type == "tender" }
	{include file="borrow.tender.tpl" template_dir = "modules/borrow"}
{elseif $_A.query_type == "fengxianchi" }
	{include file="borrow.fengxianchi.tpl" template_dir = "modules/borrow"}
{elseif $_A.query_type == "change" || $_A.query_type == "web_repay_no"}
	{include file="borrow.change.tpl" template_dir = "modules/borrow"}
{elseif $_A.query_type == "tool"}
	{include file="borrow.tool.tpl" template_dir = "modules/borrow"}
{elseif $_A.query_type == "late" || $_A.query_type == "web_recover" || $_A.query_type == "web_repay"}
	{include file="borrow.late.tpl" template_dir = "modules/borrow"}
{elseif $_A.query_type == "loan"}
	{include file="borrow.loan.tpl" template_dir = "modules/borrow"}
{elseif $_A.query_type == "new" || $_A.query_type == "edit"}
<div class="module_add">
	{if $magic.request.user_id==""}
	<form name="form1" method="post" action="" enctype="multipart/form-data" >
	<div class="module_title"><strong>请输入此信息的用户名或ID</strong></div>
	

	<div class="module_border">
		<div class="l">用户ID：</div>
		<div class="c">
			<input type="text" name="user_id"  class="input_border"  size="20" />
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="c">
			<input type="text" name="username"  class="input_border"  size="20" />
		</div>
	</div>
	
	<div class="module_submit" >
		<input type="submit"  name="submit" value="确认提交" />
		<input type="reset"  name="reset" value="重置表单" />
	</div>
	</form>
	{else}
	<div class="module_title"><strong>添加用户信息</strong></div>
	
	<form name="form1" method="post" action=""  enctype="multipart/form-data" onsubmit="return check_form();" >
	
	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="c">
			{$_A.borrow_result.username}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">借款用途：</div>
		<div class="c">
		{linkages nid="borrow_use" value="$_A.borrow_result.borrow_use" name="borrow_use"  }
			 <span >说明借款成功后的具体用途。</span>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">借款期限：</div>
		<div class="c">
			{linkages nid="borrow_time_limit" value="$_A.borrow_result.borrow_period" name="borrow_period" type="value" }<span >借款成功后,打算以几个月的时间来还清贷款。 </span>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">还款方式：</div>
		<div class="c">
			{linkages nid="borrow_style" value="$_A.borrow_result.borrow_style" name="borrow_style" type="value" }
		<span >按季度分期还款是指贷款者借款成功后,每月还息，按季还本。</span>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">借贷总金额：</div>
		<div class="c"><input type="text" name="account" value="{$_A.borrow_result.account}"  size="10"/>
<span >借款金额应在500元至50,000元之间。交易币种均为人民币。</span>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">年利率：</div>
		<div class="c">
			<input type="text" name="borrow_apr" value="{$_A.borrow_result.borrow_apr}" /> % <span >按季度分期还款是指贷款者借款成功后,每月还息，按季还本。</span>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">最低投标金额：</div>
		<div class="c">
			{linkages nid="borrow_lowest_account" value="$_A.borrow_result.tender_account_min" name="tender_account_min" type="value" }
		<span >允许投资者对一个借款标的投标总额的限制。</span>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">最多投标总额：</div>
		<div class="c">
			{linkages nid="borrow_most_account" value="$_A.borrow_result.tender_account_max" name="tender_account_max" type="value" }
			<span >设置此次借款融资的天数。融资进度达到100%后直接进行网站的复审</span>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">有效时间：</div>
		<div class="c">
			{linkages nid="borrow_valid_time" value="$_A.borrow_result.borrow_valid_time" name="borrow_valid_time" type="value" }
			 <span>设置此次借款融资的天数。融资进度达到100%后直接进行网站的复审 </span>
		</div>
	</div>
	<div class="module_title"><strong>设置奖励</strong></div>
	<div class="module_border">
		<div class="w"><input type="radio" name="award" value="0" {if $_A.borrow_result.award_status==0 || $_A.borrow_result.award_status==""} checked="checked"{/if}>不设置奖励</div>
		<div class="c">
			 <span>如果您设置了奖励金额，将会冻结您帐户中相应的账户余额。如果要设置奖励，请确保您的帐户有足够 的账户余额。 </span>
		</div>
	</div>
	
	<div class="module_border">
		<div class="w"><input type="radio" name="award_status" value="1" {if $_A.borrow_result.award_status==1 } checked="checked"{/if}/>按固定金额分摊奖励：</div>
		<div class="c">
			<input type="text" name="award_account" value="{$_A.borrow_result.award_account}" size="5" />元 <span>不能低于5元,不能高于总标的金额的2%，且请保留到“元”为单位。这里设置本次标的要奖励给所有投标用户的总金额。  </span>
		</div>
	</div>
	
	<div class="module_border">
		<div class="w"><input type="radio" name="award_status" value="2" {if $_A.borrow_result.award_status==2 } checked="checked"{/if}/>按投标金额比例奖励：</div>
		<div class="c">
			<input type="text" name="award_scale" value="{$_A.borrow_result.award_scale}" size="5" />%  <span>范围：0.1%~2% ，这里设置本次标的要奖励给所有投标用户的奖励比例。  </span>
		</div>
	</div>
	
	<div class="module_border">
		<div class="w"><input type="checkbox" name="award_false" value="1" {if $_A.borrow_result.award_false==1 } checked="checked"{/if}/>标的失败时也同样奖励：</div>
		<div class="c">
			  <span>如果您勾选了此选项，到期未满标或复审失败时同样会奖励给投标用户。如果没有勾选，标的失败时会把奖励金额解冻回账户余额。   </span>
		</div>
	</div>
	
	<div class="module_title"><strong>帐户信息公开</strong></div>
	<div class="module_border">
		<div class="w">公开我的帐户资金情况：</div>
		<div class="c">
			<input type="checkbox" name="open_account" value="1" {if $_A.borrow_result.open_account==1 } checked="checked"{/if}/> <span> 如果您勾上此选项，将会实时公开您帐户的：账户总额、可用余额、冻结总额。  </span>
		</div>
	</div>
	
	<div class="module_border">
		<div class="w">公开我的借款资金情况：</div>
		<div class="c">
			<input type="checkbox" name="open_borrow" value="1" {if $_A.borrow_result.open_borrow==1 } checked="checked"{/if}/> <span>如果您勾上此选项，将会实时公开您帐户的：借款总额、已还款总额、未还款总额、迟还总额、逾期总额。 </span>
		</div>
	</div>
	
	<div class="module_border">
		<div class="w">公开我的投标资金情况：</div>
		<div class="c">
			<input type="checkbox" name="open_tender" value="1" {if $_A.borrow_result.open_tender==1 } checked="checked"{/if}/> <span>如果您勾上此选项，将会实时公开您帐户的：投标总额、已收回总额、待收回总额。  </span>
		</div>
	</div>
	
	<div class="module_border">
		<div class="w">公开我的信用额度情况：</div>
		<div class="c">
			<input type="checkbox" name="open_credit" value="1" {if $_A.borrow_result.open_credit==1 } checked="checked"{/if}/> <span>如果您勾上此选项，将会实时公开您帐户的：最低信用额度、最高信用额度。  </span>
		</div>
	</div>
	
	<div class="module_title"><strong>详细信息</strong></div>
	<div class="module_border">
		<div class="l">标题：</div>
		<div class="c">
			<input type="text" name="name" value="{$_A.borrow_result.name}" size="50" /> 
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">信息：</div>
		<div class="c">
			
		</div>
	</div>
	<!--基本资料 结束-->
		
	<div class="module_submit" >
		{if $_A.query_type == "edit"}<input type="hidden"  name="borrow_nid" value="{$_A.borrow_result.borrow_nid}" />{/if}
		<input type="hidden" name="status" value="{ $_A.borrow_result.status }" />
		<input type="hidden"  name="user_id" value="{$magic.request.user_id}" />
		<input type="hidden"  name="vouch_status" value="{$_A.borrow_result.vouch_status}" />
		<input type="hidden"  name="vouch_award_scale" value="{$_A.borrow_result.vouch_award_scale}" />
		<input type="hidden"  name="vouch_users" value="{$_A.borrow_result.vouch_users}" />
		<input type="submit"  name="submit" value="确认提交" />
		<input type="reset"  name="reset" value="重置表单" />
	</div>
	</form>
	
	
	{/if}
</div>
{literal}
<script>


function check_form(){
	 var frm = document.forms['form1'];
	 var name = frm.elements['name'].value;
	 var award = frm.elements['award'].value;
	 var part_account = frm.elements['part_account'].value;
	 var errorMsg = '';
	  if (name.length == 0 ) {
		errorMsg += '标题必须填写' + '\n';
	  }
	   if (award ==1 && part_account<5) {
		errorMsg += '奖励金额不能小于5元' + '\n';
	  }
	  
	  if (errorMsg.length > 0){
		alert(errorMsg); return false;
	  } else{  
		return true;
	  }
}

</script>
{/literal}
{elseif $_A.query_type == "view"}

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
{elseif $_A.query_type=="list" ||  $_A.query_type=="wait"||  $_A.query_type=="success"||  $_A.query_type=="false"||  $_A.query_type=="full_check" ||  $_A.query_type=="full_success" ||  $_A.query_type=="full_false" ||  $_A.query_type=="cancel"}

{if $magic.request.check!=""}
{elseif $magic.request.full!=""}
<div  style="height:205px; overflow:hidden">
	<form name="form1" method="post" action="{$_A.query_url_all}&full={$magic.request.full}" >
	<div class="module_border_ajax">
		<div class="l">审核状态:</div>
		<div class="c">
		<input type="radio" name="status" value="3"/>复审通过 <input type="radio" name="status" value="4"  checked="checked"/>复审不通过 </div>
	</div>
	
	<div class="module_border_ajax" >
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="reverify_remark" cols="45" rows="7">{ $_A.borrow_result.reverify_remark}</textarea>
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">验证码:</div>
		<div class="c">
			<input name="valicode" type="text" size="11" maxlength="4"  tabindex="3" onClick="$('#valicode').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
		</div>
		<div class="c">
			<img src="/?plugins&q=imgcode" alt="点击刷新" id="valicode" onClick="this.src='/?plugins&q=imgcode&t=' + Math.random();" align="absmiddle" style="cursor:pointer" />
		</div>
	</div>

	<div class="module_submit_ajax" >
		<input type="hidden" name="borrow_nid" value="{ $magic.request.check}" />
		<input type="submit"  name="reset" class="submit_button" value="审核此标" />
	</div>
	
</form>
</div>
{elseif $magic.request.view!=""}

{else}
<div class="module_add">
	<div class="module_title"><strong>{$_A.query_type|linkages:"borrow_query_type"}</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr >
			<td width="" class="main_td"><input type="checkbox" name="check_all"  /></td>
			<td width="" class="main_td">ID</td>
			<td width="*" class="main_td">用户名称</td>
			<td width="*" class="main_td">贷款号</td>
			<td width="" class="main_td">借款标题</td>
			<td width="" class="main_td">借款金额</td>
			<td width="" class="main_td">利率</td>
			<td width="" class="main_td">借款期限</td>
			{if $_A.query_type=="success"}
			<td width="" class="main_td">已投金额</td>
			<td width="" class="main_td">投资次数</td>
			{/if}
			<td width="" class="main_td">还款方式</td>
			<td width="" class="main_td">借款类型</td>
			<td width="" class="main_td">类型</td>
			<td width="" class="main_td">状态</td>
			<td width="" class="main_td">查看</td>
			
		</tr>
		{ list  module="borrow" function="GetList" var="loop" borrow_name="request"  borrow_nid="request" username="request" query_type=$_A.query_type }
		{foreach from="$loop.list" item="item"}
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td><input type="checkbox" name="check_all[]" value="{$item.id}"  /></td>
			<td>{ $item.id}<input type="hidden" name="id[]" value="{ $item.id}" /></td>
			<td class="main_td1" align="center"><a href="javascript:void(0)" onclick='tipsWindown("用户详细信息查看","url:get?{$_A.admin_url}&q=module/user/view&user_id={$item.user_id}&type=scene",500,230,"true","","true","text");'>	{$item.username}</a></td>
			<td>{$item.borrow_nid}</td>
			<td title="{$item.name}"><a href="{$_A.query_url}&view={$item.borrow_nid}">{$item.name|truncate:10}</a></td>
			<td>{$item.account}元</td>
			<td>{$item.borrow_apr}%</td>
			<td>{$item.borrow_period}个月</td>
			{if $_A.query_type=="success"}
			<td width="" class="main_td">￥{$item.borrow_account_yes}</td>
			<td width="" class="main_td">{$item.tender_times}次</td>
			{/if}
			<td>{$item.borrow_style|linkages:"borrow_style"}</td>
			<td>{if $item.vouchstatus =="1"}<font color="#FF0000">担保标借款</font>{else}普通标借款{/if}</td>
			<td>{$item.borrow_flag|linkages:"borrow_flag"}</td>
			<td>{$item.status|linkages:"borrow_status"}</td>
			<td title="{$item.name}"><a href="{$_A.query_url_all}&view={$item.borrow_nid}">查看</a></td>
			
		</tr>
		{ /foreach}
		<tr>
		<td colspan="14" class="action">
		<div class="floatl">
			<input type="submit" value="确定提交" class="submit"/>
		</div>
		<div class="floatr">
			 标题：<input type="text" name="borrow_name" id="borrow_name" value="{$magic.request.borrow_name}" size="8"/> 用户名：<input type="text" name="username" id="username" value="{$magic.request.username}" size="8"/>贷款号：<input type="text" name="borrow_nid" id="borrow_nid" value="{$magic.request.borrow_nid}" size="8"/> 状态<select id="status" ><option value="">全部</option><option value="1" {if $magic.request.status==1} selected="selected"{/if}>已通过</option><option value="0" {if $magic.request.status=="0"} selected="selected"{/if}>未通过</option></select> <select id="is_vouch" ><option value="">全部</option><option value="1" {if $magic.request.is_vouch==1} selected="selected"{/if}>担保标</option><option value="0" {if $magic.request.is_vouch=="0"} selected="selected"{/if}>普通标</option></select> <input type="button" value="搜索" class="submit" onclick="sousuo('{$_A.query_url}{$_A.site_url}')">
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

{elseif $_A.query_type=="tender1"}


{elseif $_A.query_type=="cancel_list"}
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr >
			<td width="" class="main_td">ID</td>
			<td width="*" class="main_td">用户名称</td>
			<td width="*" class="main_td">信用积分</td>
			<td width="" class="main_td">借款标题</td>
			<td width="" class="main_td">借款金额</td>
			<td width="" class="main_td">利率</td>
			<td width="" class="main_td">借款期限</td>
			<td width="" class="main_td">借款类型</td>
			<td width="" class="main_td">申请时间</td>
			<td width="" class="main_td">撤回状态</td>
			<td width="" class="main_td">操作</td>
		</tr>
		{ foreach  from=$_A.borrow_list key=key item=item}
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td>{ $item.id}<input type="hidden" name="id[]" value="{ $item.id}" /></td>
			<td class="main_td1" align="center"><a href="javascript:void(0)" onclick='tipsWindown("用户详细信息查看","url:get?{$_A.admin_url}&q=module/user/view&user_id={$item.user_id}&type=scene",500,230,"true","","true","text");'>	{$item.username}</a></td>
			<td>{$item.credit.approve_credit}分</td>
			<td title="{$item.name}">{$item.name|truncate:10}</td>
			<td>{$item.account}元</td>
			<td>{$item.borrow_apr}%</td>
			<td>{$item.borrow_period}个月</td>
			<td>{if $item.vouchstatus =="1"}<font color="#FF0000">担保标借款</font>{elseif $item.fast_status =="1"}<font color="#0000FF">快速标借款</font>{else}普通标借款{/if}</td>
			
			<td>{$item.cancel_time|date_format}</td>
			<td>{if $item.cancel_status ==2}<font color="#FF0000">申请中</font>{else}已撤回{/if}</td>
			
			<td>{ if $item.cancel_status ==2 }<a href="{$_A.query_url}/cancel_edit{$_A.site_url}&user_id={$item.user_id}&id={$item.id}">审核</a>{else}-{/if}</td>
		</tr>
		{ /foreach}
		<tr>
		<td colspan="14" class="action">
		<div class="floatl">
			<input type="submit" value="确定提交" class="submit"/>
		</div>
		<div class="floatr">
			用户名：<input type="text" name="username" id="username" value="{$magic.request.username}"/> 状态<select id="status" ><option value="">全部</option><option value="1" {if $magic.request.status==1} selected="selected"{/if}>已通过</option><option value="0" {if $magic.request.status=="0"} selected="selected"{/if}>未通过</option></select> <select id="is_vouch" ><option value="">全部</option><option value="1" {if $magic.request.is_vouch==1} selected="selected"{/if}>担保标</option><option value="0" {if $magic.request.is_vouch=="0"} selected="selected"{/if}>普通标</option></select> <input type="button" value="搜索" class="submit" onclick="sousuo('{$_A.query_url}{$_A.site_url}')">
		</div>
		</td>
	</tr>
		<tr>
			<td colspan="14" class="page">
			{$_A.showpage} 
			</td>
		</tr>
	</form>	
</table>

{elseif $_A.query_type=="full"}
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr >
			<td width="" class="main_td">ID</td>
			<td width="*" class="main_td">用户名称</td>
			<td width="*" class="main_td">信用积分</td>
			<td width="" class="main_td">借款标题</td>
			<td width="" class="main_td">借款金额</td>
			<td width="" class="main_td">年利率</td>
			<td width="" class="main_td">投标次数</td>
			<td width="" class="main_td">借款期限</td>
			<td width="" class="main_td">状态</td>
			<td width="" class="main_td">操作</td>
		</tr>
		{ foreach  from=$_A.borrow_list key=key item=item}
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td>{ $item.id}</td>
			<td class="main_td1" align="center"><a href="javascript:void(0)" onclick='tipsWindown("用户详细信息查看","url:get?{$_A.admin_url}&q=module/user/view&user_id={$item.user_id}&type=scene",500,230,"true","","true","text");'>	{$item.username}</a></td>
			<td>{$item.credit_jifen}</td>
			<td title="{$item.name}">{$item.name|truncate:10}</td>
			<td>{$item.account}元</td>
			<td>{$item.borrow_apr}%</td>
			<td>{$item.tender_times|default:0}</td>
			<td>{$item.borrow_period}个月</td>
			<td>{if $item.status==3}满额发布成功{elseif $item.status==4}满额发布失败{else}满标审核中{/if}</td>
			<td><a href="{$_A.query_url}/full_view{$_A.site_url}&user_id={$item.user_id}&borrow_nid={$item.borrow_nid}">审核</a></td>
		</tr>
		{ /foreach}
		<tr>
		<td colspan="10" class="action">
		<div class="floatl">
		
		</div>
		<div class="floatr">
			用户名：<input type="text" name="username" id="username" value="{$magic.request.username}"/><input type="button" value="搜索" class="submit" onclick="sousuo('{$_A.query_url}/full&status=1{$_A.site_url}')">
		</div>
		</td>
	</tr>
		<tr>
			<td colspan="9" class="page">
			{$_A.showpage} 
			</td>
		</tr>
	</form>	
</table>
{elseif $_A.query_type == "full_view" }
<div class="module_add">
	<div class="module_title"><strong>已满额借款标审核</strong></div>
	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="c">
			<a href="javascript:void(0)" onclick='tipsWindown("用户详细信息查看","url:get?{$_A.admin_url}&q=module/user/view&user_id={$_A.borrow_result.user_id}&type=scene",500,230,"true","","true","text");'>	{$_A.borrow_result.username}</a>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">真实姓名：</div>
		<div class="c">
			{$_A.borrow_result.realname}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">标题：</div>
		<div class="c">
			{$_A.borrow_result.name}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">借款金额：</div>
		<div class="h">
			￥{$_A.borrow_result.account}
		</div>
		<div class="l">年利率：</div>
		<div class="h">
			{$_A.borrow_result.borrow_apr} %
		</div>
	</div>
	<div class="module_border">
		<div class="l">借款期限：</div>
		<div class="h">
			{$_A.borrow_result.borrow_period}个月
		</div>
		<div class="l">借款用途：</div>
		<div class="h">
			{$_A.borrow_result.borrow_use|linkage:}
		</div>
	</div>
	<div class="module_border">
		<div class="l">借款类型：</div>
		<div class="h">
			{$_A.borrow_result.borrow_style|linkage:"borrow_style"}
		</div>
	</div>
	<div class="module_border">
	<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">

		<tr >
			<td width="" class="main_td">ID</td>
			<td width="*" class="main_td">用户名称</td>
			<td width="*" class="main_td">信用积分</td>
			<td width="" class="main_td">投资金额</td>
			<td width="" class="main_td">有效金额</td>
			<td width="" class="main_td">状态</td>
			<td width="" class="main_td">投资理由</td>
			<td width="" class="main_td">投标时间</td>
		</tr>
		{ foreach  from=$_A.borrow_tender_list key=key item=item}
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td>{ $item.id}</td>
			<td class="main_td1" align="center"><a href="javascript:void(0)" onclick='tipsWindown("用户详细信息查看","url:get?{$_A.admin_url}&q=module/user/view&user_id={$item.user_id}&type=scene",500,230,"true","","true","text");'>	{$item.username}</a></td>
			<td>{$item.credit_jifen}分</td>
			<td>{$item.account_tender}元</td>
			<td><font color="#FF0000">{$item.account}元</font></td>
			<td>{if $item.account == $item.account_tender}全部通过{else}部分通过{/if}</td>
			<td>{$item.contents}</td>
			<td>{$item.addtime|date_format:"Y-m-d H:i:s"}</td>
		</tr>
		{ /foreach}
		<tr>
			<td colspan="9" class="page">
			{$key+1}条投资 
			</td>
		</tr>
</table>

	</div>
	{if $_A.borrow_result.vouch_status==1}
	<div class="module_border">
	<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">

		<tr >
			<td width="" class="main_td">ID</td>
			<td width="*" class="main_td">担保人</td>
			<td width="*" class="main_td">担保有效金额</td>
			<td width="" class="main_td">投资担保金额</td>
			<td width="" class="main_td">担保奖励比例</td>
			<td width="" class="main_td">担保奖励金额</td>
			<td width="" class="main_td">担保理由</td>
			<td width="" class="main_td">担保时间</td>
		</tr>
		{ foreach  from=$_A.borrow_vouch_result key=key item=item}
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td>{ $item.id}</td>
			<td><a href="javascript:void(0)" onclick='tipsWindown("用户详细信息查看","url:get?{$_A.admin_url}&q=module/user/view&user_id={$item.user_id}&type=scene",500,230,"true","","true","text");'>	{ $item.username}</a></td>
			<td>￥{ $item.account}</td>
			<td>￥{ $item.account_vouch}</td>
			<td>{ $item.award_scale}%</td>
			<td>￥{ $item.award_account}</td>
			<td>{ $item.contents}</td>
			<td>{$item.addtime|date_format:"Y-m-d H:i:s"}</td>
		</tr>
		{ /foreach}
		<tr>
			<td colspan="9" class="page">
			{$key+1}条担保 
			</td>
		</tr>
</table>

	</div>
	{/if}
	<div class="module_border">
	<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
		<tr >
			<td width="" class="main_td">ID</td>
			<td width="*" class="main_td">计划还款日</td>
			<td width="*" class="main_td">每期还款本息</td>
			<td width="" class="main_td">每期还款本金</td>
			<td width="" class="main_td">每期还款利息</td>
		</tr>
		{ foreach  from=$_A.borrow_repayment key=key item=item}
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td>{ $key+1}</td>
			<td >{$item.repay_time|date_format:"Y-m-d"}</td>
			<td>￥{$item.account_all}</td>
			<td>￥{$item.account_capital}</td>
			<td>￥{$item.account_interest}</td>
		</tr>
		{ /foreach}
</table>

	</div>
	{ if $_A.borrow_result.status==1}
	<div class="module_title"><strong>审核此借款</strong></div>
	<form name="form1" method="post" action="" >
	<div class="module_border">
		<div class="l">状态:</div>
		<div class="c">
		<input type="radio" name="status" value="3"/>复审通过 <input type="radio" name="status" value="4"  checked="checked"/>复审不通过 </div>
	</div>
	
	<div class="module_border" >
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="reverify_remark" cols="45" rows="5">{ $_A.borrow_result.reverify_remark}</textarea>
		</div>
	</div>
	<div class="module_border" >
		<div class="l">验证码:</div>
		<div class="c">
			<input name="valicode" type="text" size="11" maxlength="4"  tabindex="3" onClick="$('#valicode').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>&nbsp;<img id="valicode" src="/?plugins&q=imgcode" alt="点击刷新" onClick="this.src='/?plugins&q=imgcode&t=' + Math.random();" align="absmiddle" style="cursor:pointer" />
		</div>
	</div>

	<div class="module_submit" >
		<input type="hidden" name="id" value="{ $_A.borrow_result.id }" />
		<input type="hidden" name="borrow_nid" value="{ $_A.borrow_result.borrow_nid}" />
		
		<input type="submit"  name="reset" value="审核此借款标" class="submit" />
	</div>
	
</form>
	{/if}
	<div class="module_title"><strong>其他详细内容</strong></div>
	<div class="module_border">
		<div class="l">投标奖励：</div>
		<div class="h">
			{if $_A.borrow_result.award_status==0}无奖励{elseif $_A.borrow_result.award_status==1}金额：{$_A.borrow_result.award_account}元{else}比例{$_A.borrow_result.award_scale}%{/if}
		</div>
		<div class="l">投标失败是否奖励：</div>
		<div class="h">
			{if $_A.borrow_result.award_false==1}是{else}否{/if}
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">添加时间：</div>
		<div class="h">
			{$_A.borrow_result.addtime|date_format:"Y-m-d H:i:s"}
		</div>
		<div class="l">招标时间：</div>
		<div class="h">
			{$_A.borrow_result.verify_time|date_format:"Y-m-d H:i:s"}
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">内容：</div>
		<div class="hb" >
			<table><tr><td align="left">{$_A.borrow_result.borrow_contents}</td></tr></table>
		</div>
	</div>
	
</div>
<!---已还款--->
{elseif $_A.query_type=="repayment"}
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr >
			<td width="" class="main_td">ID</td>
			<td width="*" class="main_td">借款人</td>
			<td width="" class="main_td">借款标题</td>
			<td width="" class="main_td">期数</td>
			<td width="" class="main_td">到期时间</td>
			<td width="" class="main_td">还款金额</td>
			<td width="" class="main_td">还款时间</td>
			<td width="" class="main_td">状态</td>
		</tr>
		{ foreach  from=$_A.borrow_list key=key item=item}
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td>{ $item.id}</td>
			<td class="main_td1" align="center">{$item.borrow_username}</td>
			<td title="{$item.borrow_name}"><a href="/invest/a{$item.borrow_nid}.html" target="_blank">{$item.borrow_name|truncate:10}</a></td>
			<td>{$item.repay_period+1 }/{$item.borrow_period }</td>
			<td>{$item.repay_time|date_format:"Y-m-d"}</td>
			<td>{$item.repay_account  }元</td>
			<td>{$item.repay_yestime|date_format:"Y-m-d"|default:-}</td>
			<td>{if $item.repay_status==1}<font color="#006600">已还</font>{else}<font color="#FF0000">未还</font>{/if}</td>
		</tr>
		{ /foreach}
		<tr>
		<td colspan="10" class="action">
		<div class="floatl">
		
		</div>
		<div class="floatr">
			还款时间：<input type="text" name="dotime1" id="dotime1" value="{$magic.request.dotime1}" size="15" onclick="change_picktime()"/> 到 <input type="text"  name="dotime2" value="{$magic.request.dotime2}" id="dotime2" size="15" onclick="change_picktime()"/>  用户名：<input type="text" name="username" id="username" value="{$magic.request.username}"/>关键字：
			<input type="text" name="keywords" id="keywords" value="{$magic.request.keywords}"/><select id="status" name="status" >
			<option value="">不限</option>
			<option value="1" {if $magic.request.status==1} selected="selected"{/if}>已还</option>
			<option value="0" {if $magic.request.status=="0"} selected="selected"{/if}>未还</option>
			</select><input type="button" value="搜索" class="submit" onclick="sousuo('{$_A.query_url}/repayment{$_A.site_url}')">
		</div>
		</td>
	</tr>
		<tr>
			<td colspan="9" class="page">
			{$_A.showpage} 
			</td>
		</tr>
	</form>	
</table>


<!---流标--->
{elseif $_A.query_type=="liubiao"}
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr >
			<td width="" class="main_td">ID</td>
			<td width="*" class="main_td">借款人</td>
			<td width="" class="main_td">借款标题</td>
			<td width="" class="main_td">借款期限</td>
			<td width="" class="main_td">借款金额</td>
			<td width="" class="main_td">已投金额</td>
			<td width="" class="main_td">开始时间</td>
			<td width="" class="main_td">结束时间</td>
			<td width="" class="main_td">状态</td>
		</tr>
		{ foreach  from=$_A.borrow_list key=key item=item}
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td>{ $item.id}</td>
			<td class="main_td1" align="center">{$item.username}</td>
			<td title="{$item.borrow_name}"><a href="/invest/a{$item.borrow_nid}.html" target="_blank">{$item.name|truncate:10}</a></td>
			<td>{$item.borrow_period }个月</td>
			<td>{$item.account }元</td>
			<td>{$item.borrow_account_yes }元</td>
			<td>{$item.verify_time|date_format:"Y-m-d"}</td>
			<td>{$item.verify_time+$item.borrow_valid_time*24*60*60|date_format:"Y-m-d"}</td>
			<td><a href="{$_A.query_url}/liubiao_edit&borrow_nid={$item.borrow_nid}{$_A.site_url}">修改</a></td>
		</tr>
		{ /foreach}
		<tr>
		<td colspan="10" class="action">
		<div class="floatl">
		
		</div>
		<div class="floatr">
			用户名：<input type="text" name="username" id="username" value="{$magic.request.username}"/>关键字：<input type="text" name="keywords" id="keywords" value="{$magic.request.keywords}"/><select id="status" >
			<option value="">不限</option>
			<option value="1" {if $magic.request.status==1} selected="selected"{/if}>已还</option>
			<option value="0" {if $magic.request.status==0} selected="selected"{/if}>未还</option>
			</select><input type="button" value="搜索" class="submit" onclick="sousuo('{$_A.query_url}/repayment{$_A.site_url}')">
		</div>
		</td>
	</tr>
		<tr>
			<td colspan="9" class="page">
			{$_A.showpage} 
			</td>
		</tr>
	</form>	
</table>


<!--额度审核 开始-->
{elseif $_A.query_type=="liubiao_edit"}
<div class="module_title"><strong>流标管理</strong></div>
	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="h">
			{$_A.borrow_result.username}
		</div>
	</div>
	<div class="module_border">
		<div class="l">标题：</div>
		<div >
			<a href="/invest/a{$_A.borrow_result.id}.html" target="_blank">{$_A.borrow_result.name}</a>
		</div>
	</div>
	<div class="module_border">
		<div class="l">借款额度：</div>
		<div class="h">
			{$_A.borrow_result.account}
		</div>
	</div>
	<div class="module_border">
		<div class="l">已借额度：</div>
		<div class="h">
			{$_A.borrow_result.borrow_account_yes}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">申请时间：</div>
		<div class="h">
			{$_A.borrow_result.verify_time|date_format}
		</div>
	</div>
	<div class="module_border">
		<div class="l">结束时间：</div>
		<div class="h">
			{$_A.borrow_result.verify_time+$_A.borrow_result.borrow_valid_time*24*60*60|date_format}
		</div>
	</div>
	<div class="module_title"><strong>审核</strong></div>
	<form method="post" action="">
	<div class="module_border">
		<div class="l">审核状态：</div>
		<div >
			<input type="radio" name="status" value="1" />流标返回金额<input type="radio" name="status" value="2" checked="checked" />延长借款期限
		</div>
	</div>
	<div class="module_border">
		<div class="l">延长天数：</div>
		<div >
			<input type="text" name="days" value="" size="5" value="0" />天
		</div>
	</div>
	
	<div class="module_border">
		<div class="l"></div>
		<div class="h">
			<input type="submit" value="确定审核" class="submit"/>
		</div>
	</div>
	</form>



<!--额度审核 开始-->
{elseif $_A.query_type=="cancel_edit"}
<div class="module_title"><strong>撤标管理</strong></div>
	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="h">
			{$_A.borrow_result.username}
		</div>
	</div>
	<div class="module_border">
		<div class="l">标题：</div>
		<div >
			<a href="/invest/a{$_A.borrow_result.id}.html" target="_blank">{$_A.borrow_result.name}</a>
		</div>
	</div>
	<div class="module_border">
		<div class="l">借款额度：</div>
		<div class="h">
			{$_A.borrow_result.account}
		</div>
	</div>
	<div class="module_border">
		<div class="l">已借额度：</div>
		<div class="h">
			{$_A.borrow_result.borrow_account_yes}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">申请理由：</div>
		<div class="h">
			{$_A.borrow_result.cancel_remark}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">申请时间：</div>
		<div class="h">
			{$_A.borrow_result.cancel_time|date_format}
		</div>
	</div>
	
	<div class="module_title"><strong>审核</strong></div>
	<form method="post" action="{$_A.query_url}/cancel_edit{$_A.site_url}&user_id={$_A.borrow_result.user_id}&borrow_nid={$_A.borrow_result.borrow_nid}">
	<div class="module_border">
		<div class="l">审核状态：</div>
		<div >
			<input type="radio" name="cancel_status" value="3" />不同意<input type="radio" name="cancel_status" value="1" checked="checked" />同意撤回
		</div>
	</div>
	<div class="module_border">
		<div class="l">审核原因：</div>
		<div >
			<input type="text" name="cancel_verify_remark" value="" size="20" value="0" />
		</div>
	</div>
	
	<div class="module_border">
		<div class="l"></div>
		<div class="h">
			<input type="submit" value="确定审核" class="submit"/>
		</div>
	</div>
	</form>



<!--额度审核 开始-->
{elseif $_A.query_type=="amount_view"}
<div class="module_title"><strong>额度审核</strong></div>
	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="h">
			{$_A.borrow_amount_result.username}
		</div>
	</div>
	<div class="module_border">
		<div class="l">借款类型：</div>
		<div class="h">
			{if $_A.borrow_amount_result.type=="tender_vouch"}<font color="#FF0000">投资担保额度</font>{elseif $_A.borrow_amount_result.type=="borrow_vouch"}<font color="#FF0000">借款担保额度</font>{else}信用额度{/if}
		</div>
	</div>
	<div class="module_border">
		<div class="l">原来金额：</div>
		<div class="h">
			{$_A.borrow_amount_result.account_old|default:0}
		</div>
	</div>
	<div class="module_border">
		<div class="l">申请额度：</div>
		<div class="h">
			{$_A.borrow_amount_result.account}
		</div>
	</div>
	<div class="module_border">
		<div class="l">内容：</div>
		<div class="h">
			{$_A.borrow_amount_result.content}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">备注：</div>
		<div class="h">
			{$_A.borrow_amount_result.remark}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">申请时间：</div>
		<div class="h">
			{$_A.borrow_amount_result.addtime|date_format}
		</div>
	</div>
	<div class="module_title"><strong>审核</strong></div>
	<form method="post" action="">
	<div class="module_border">
		<div class="l">审核状态：</div>
		<div class="h">
			<input type="radio" name="status" value="1" />通过  <input type="radio" name="status" value="0" checked="checked" />不通过
		</div>
	</div>
	<div class="module_border">
		<div class="l">通过额度：</div>
		<div class="h">
			<input type="text" name="account" value="{$_A.borrow_amount_result.account}" />
			<input type="hidden" name="type" value="{ $_A.borrow_amount_result.type}" />
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">审核备注：</div>
		<div >
			<textarea name="verify_remark" rows="5" cols="40" ></textarea>
		</div>
	</div>
	<div class="module_border">
		<div class="l"></div>
		<div class="h">
			<input type="submit" value="确定审核" class="submit"/>
		</div>
	</div>
	</form>


<!--统计 开始-->
{elseif $_A.query_type=="amount" || $_A.query_type=="amount_type" || $_A.query_type=="amount_apply" || $_A.query_type=="amount_log"}
	{include file="borrow.amount.tpl" template_dir="modules/borrow"}


<!--统计 开始-->
{elseif $_A.query_type=="tongji"}

<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
	  {foreach from="$_A.account_tongji" key=key  item="item"}
		<tr >
			<td width="*" class="main_td">类型名称</td>
			<td width="*" class="main_td">{$key}</td>
			<td width="" class="main_td">金额</td>
		</tr>
		{foreach from="$item" key="_key" item="_item"}
		<tr  class="tr2">
			<td >{$_item.type_name}</td>
			<td >{$_item.type}</td>
			<td >￥{$_item.num}</td>
		</tr>
		{/foreach}
	{/foreach}
	</form>	
</table>
<!--统计 结束-->
{/if}
