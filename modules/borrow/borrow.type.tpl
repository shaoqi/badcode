{if $magic.request.p=="edit"}
<div class="module_add">
	<div class="module_title"><strong>修改标种类型</strong></div>
<form action="{$_A.query_url_all}&p=edit" method="post">
    {articles module="borrow" function="GetTypeOne" plugins="type" id="$magic.request.id" var="item"}

	
	<div class="module_border">
		<div class="l">标种类型：</div>
		<div class="c">
		  {$item.name}
		</div>
	</div>
    	
     <div class="module_border">
		<div class="l">标识名：</div>
		<div class="c">
			{$item.nid}<input type="hidden" name="id" value="{$item.id}" />
		</div>
	</div>
    
	<div class="module_border">
		<div class="l">名称：</div>
		<div class="c">
			<input type="text" name="title"  class="input_border" value="{$item.title}"   size="20" />
		</div>
	</div>
    
    
	<div class="module_border">
		<div class="l">描述：</div>
		<div class="c">
            <textarea name="description" rows="4" cols="40">{$item.description}</textarea>
		</div>
	</div>
    
	<div class="module_border">
		<div class="l">状态：</div>
		<div class="c">
			<input type="radio" name="status"  class="input_border" value="1" {if $item.status==1} checked=""{/if}  />开启 
			<input type="radio" name="status"  class="input_border"  value="0" {if $item.status==0} checked=""{/if} />关闭
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">额度类型：</div>
		<div class="c">
        {if $item.nid=="worth"}
        净值额度(公式=（账户可用金额+投资冻结金额+待收本金）*0.8 -待还总额)<input type="hidden" name="amount_type" value="worth" />
        {elseif $item.nid=="second"}
        秒标不需要额度<input type="hidden" name="amount_type" value="second" />
        {else}
        {loop module="borrow" plugins="amount" status=1 function="GetAmountTypeList" limit="all"}
			<input type="radio" name="amount_type"  class="input_border" value="{$var.nid}" {if $var.nid==$item.amount_type} checked=""{/if}  />{$var.name}
           {/loop}
           {/if}
		</div>
	</div>
   
	<div class="module_border">
		<div class="l">借款额度：</div>
		<div class="c">
			<input type="text" name="amount_first"  class="input_border" value="{$item.amount_first}"   size="4" onkeyup="value=value.replace(/[^0-9]/g,'')" />元 ~  <input type="text" name="amount_end"  class="input_border" value="{$item.amount_end}"   size="4" onkeyup="value=value.replace(/[^0-9]/g,'')" />元
		</div>
	</div>
    
    
	<div class="module_border">
		<div class="l">借款金额倍数：</div>
		<div class="c">
			<input type="text" name="account_multiple"  class="input_border" value="{$item.account_multiple}"   size="4" onkeyup="value=value.replace(/[^0-9]/g,'')" />元 (0表示不限)
		</div>
	</div>
    
	<div class="module_border">
		<div class="l">年利率：</div>
		<div class="c">
			<input type="text" name="apr_first"  class="input_border" value="{$item.apr_first}"   size="4" />~  <input type="text" name="apr_end"  class="input_border" value="{$item.apr_end}"   size="4" onkeyup="value=value.replace(/[^0-9.]/g,'')" /> %
		</div>
	</div>
    
    
	<div class="module_border">
		<div class="l">借款期限：</div>
		<div class="c">
			<input type="text" name="period_first"  class="input_border" value="{$item.period_first}"   size="4" onkeyup="value=value.replace(/[^0-9]/g,'')" />~  <input type="text" name="period_end"  class="input_border" value="{$item.period_end}"   size="4" onkeyup="value=value.replace(/[^0-9]/g,'')" /> {if $item.nid=="day"}天{else}月{/if}
		</div>
	</div>
    
	<div class="module_border">
		<div class="l">有效期：</div>
		<div class="c">
			<input type="text" name="validate_first"  class="input_border" value="{$item.validate_first}"   size="4" onkeyup="value=value.replace(/[^0-9]/g,'')" />~  <input type="text" name="validate_end"  class="input_border" value="{$item.validate_end}"   size="4" onkeyup="value=value.replace(/[^0-9]/g,'')" /> 天
		</div>
	</div>
    
	<div class="module_border">
		<div class="l">审核时间：</div>
		<div class="c">
			<input type="text" name="check_first"  class="input_border" value="{$item.check_first}"   size="4" onkeyup="value=value.replace(/[^0-9]/g,'')" />~  <input type="text" name="check_end"  class="input_border" value="{$item.check_end}"   size="4" onkeyup="value=value.replace(/[^0-9]/g,'')" /> 天
		</div>
	</div>
    
	<div class="module_border">
		<div class="l" title="多个请用,隔开">最低投标金额：</div>
		<div class="c">
			<input type="text" name="tender_account_min"  class="input_border" value="{$item.tender_account_min}"   size="25" />元(多个请用,号隔开)
		</div>
	</div>
    
    
	<div class="module_border">
		<div class="l" title="多个请用,隔开">最高投标金额：</div>
		<div class="c">
			<input type="text" name="tender_account_max"  class="input_border" value="{$item.tender_account_max}"   size="25" />元(多个请用,号隔开，0表示不限)
		</div>
	</div>
    
   	<div class="module_border">
		<div class="l">是否启用奖励：</div>
		<div class="c">
			<input type="radio" name="award_status"  class="input_border" value="1" {if $item.award_status==1 || $item.award_status=="" } checked=""{/if}  />开启 
			<input type="radio" name="award_status"  class="input_border"  value="0" {if $item.award_status==0} checked=""{/if} />关闭
		</div>
	</div>
    
    
    
   	<div class="module_border">
		<div class="l">是否启用部分借款：</div>
		<div class="c">
			<input type="radio" name="part_status"  class="input_border" value="1" {if $item.part_status==1 || $item.part_status=="" } checked=""{/if}  />开启 
			<input type="radio" name="part_status"  class="input_border"  value="0" {if $item.part_status==0} checked=""{/if} />关闭
		</div>
	</div>
    
   	<div class="module_border">
		<div class="l">是否启用借款密码：</div>
		<div class="c">
			<input type="radio" name="password_status"  class="input_border" value="1" {if $item.password_status==1 || $item.award_status=="" } checked=""{/if}  />开启 
			<input type="radio" name="password_status"  class="input_border"  value="0" {if $item.password_status==0} checked=""{/if} />关闭
		</div>
	</div>
    
    
   	<div class="module_border">
		<div class="l">是否启用借款失败也奖励</div>
		<div class="c">
			<input type="radio" name="award_false_status"  class="input_border" value="1" {if $item.award_false_status==1 || $item.award_false_status=="" } checked=""{/if}  />开启 
			<input type="radio" name="award_false_status"  class="input_border"  value="0" {if $item.award_false_status==0} checked=""{/if} />关闭
		</div>
	</div>
    
    
	<div class="module_border">
		<div class="l">奖励比例：</div>
		<div class="c">
			<input type="text" name="award_scale_first"  class="input_border" value="{$item.award_scale_first}"   size="4" onkeyup="value=value.replace(/[^0-9.]/g,'')" />~<input type="text" name="award_scale_end"  class="input_border" value="{$item.award_scale_end}"   size="4" onkeyup="value=value.replace(/[^0-9.]/g,'')" /> %（如果按借款金额的比例进行奖励）
		</div>
	</div>
    
    
	<div class="module_border">
		<div class="l">奖励金额：</div>
		<div class="c">
			<input type="text" name="award_account_first"  class="input_border" value="{$item.award_account_first}"   size="4" onkeyup="value=value.replace(/[^0-9.]/g,'')" />~  <input type="text" name="award_account_end"  class="input_border" value="{$item.award_account_end}"   size="4" onkeyup="value=value.replace(/[^0-9.]/g,'')" />元（如果选择金额的话，则按此奖励的金额范围）
		</div>
	</div>
    
    
    
   	<div class="module_border">
		<div class="l">初审自动通过：</div>
		<div class="c">
			<input type="radio" name="verify_auto_status"  class="input_border" value="1" {if $item.verify_auto_status==1 || $item.verify_auto_status=="" } checked=""{/if}  />开启 
			<input type="radio" name="verify_auto_status"  class="input_border"  value="0" {if $item.verify_auto_status==0} checked=""{/if} />关闭
		</div>
	</div>
    
    
   	<div class="module_border">
		<div class="l">初审自动通过的审核备注：</div>
		<div class="c">
    		<input type="text" name="verify_auto_remark"  value="{$item.verify_auto_remark}" />
		</div>
	</div>
    
	<div class="module_border">
		<div class="l" title="如果不冻结请为空，或者填写0.00即可">vip冻结保证金：</div>
		<div class="c">
			<input type="text" name="frost_scale_vip"  class="input_border" value="{$item.frost_scale_vip}"   size="4" onkeyup="value=value.replace(/[^0-9.]/g,'')" />%
		</div>
	</div>


	<div class="module_border">
		<div class="l" title="如果不冻结请为空，或者填写0.00即可">普通会员冻结保证金：</div>
		<div class="c">
			<input type="text" name="frost_scale"  class="input_border" value="{$item.frost_scale}"   size="4" onkeyup="value=value.replace(/[^0-9.]/g,'')" />%
		</div>
	</div>
    
    
	<div class="module_border">
		<div class="l" title="逾期多久了网站进行垫付">垫付逾期天数：</div>
		<div class="c">
			<input type="text" name="late_days"  class="input_border" value="{$item.late_days}"   size="4" onkeyup="value=value.replace(/[^0-9]/g,'')" />天
		</div>
	</div>
    
	<div class="module_border">
		<div class="l" title="vip会员垫付本息的比例">vip垫付本息比例：</div>
		<div class="c">
			<input type="text" name="vip_late_scale"  class="input_border" value="{$item.vip_late_scale}"   size="4" onkeyup="value=value.replace(/[^0-9.]/g,'')" />%
		</div>
	</div>
    
	<div class="module_border">
		<div class="l" title="普通会员垫付本金的比例">普通会员垫付本金比例：</div>
		<div class="c">
			<input type="text" name="all_late_scale"  class="input_border" value="{$item.all_late_scale}"   size="4" onkeyup="value=value.replace(/[^0-9.]/g,'')" />%
		</div>
	</div>

	<div class="module_border">
		<div class="l">还款方式：</div>
		<div class="c">
            {loop module="borrow" function="GetStyleList" status=1 limit="all" plugins="style" var="style_var"}
            {if $item.id==3}
            {if $style_var.nid=="endday"}
            <input type="checkbox" name="styles[]"  class="input_border" value="{$style_var.nid}" checked="" readonly="" /> {$style_var.title}
            {/if}
            {else}
            {if $style_var.nid!="endday"}
			<input type="checkbox" name="styles[]"  class="input_border" value="{$style_var.nid}" {$style_var.nid|checked:"$item.styles"} /> {$style_var.title}
            {/if}
            {/if}
            {/loop}
		</div>
	</div>
    <div class="module_title"><strong>以下功能需要跟服务器进行结合</strong></div>
   	<div class="module_border">
		<div class="l">系统满标审核</div>
		<div class="c">
			<input type="radio" name="system_borrow_full_status"  class="input_border" value="1" {if $item.system_borrow_full_status==1} checked=""{/if}  />是 
			<input type="radio" name="system_borrow_full_status"  class="input_border"  value="0" {if $item.system_borrow_full_status==0 || $item.system_borrow_full_status==""} checked=""{/if} />否
           
		</div>
	</div>
	<div class="module_border">
		<div class="l">系统用户还款</div>
		<div class="c">
			<input type="radio" name="system_borrow_repay_status"  class="input_border" value="1" {if $item.system_borrow_repay_status==1} checked=""{/if}  />是 
			<input type="radio" name="system_borrow_repay_status"  class="input_border"  value="0" {if $item.system_borrow_repay_status==0 || $item.system_borrow_repay_status==""} checked=""{/if} />否
           
		</div>
	</div>
	<div class="module_border">
		<div class="l">系统逾期自动垫付</div>
		<div class="c">
			<input type="radio" name="system_web_repay_status"  class="input_border" value="1" {if $item.system_web_repay_status==1} checked=""{/if}  />是 
			<input type="radio" name="system_web_repay_status"  class="input_border"  value="0" {if $item.system_web_repay_status==0 || $item.system_web_repay_status==""} checked=""{/if} />否
           
		</div>
	</div>
    
    
	<div class="module_border">
		<div class="l"></div>
		<div class="c">
			<input type="submit"  name="submit" value="确认提交" />
		<input type="reset"  name="reset" value="重置表单" />
		</div>
	</div>
</form>
 </div>   
    {/articles}
{else}
<div class="module_add">
	<div class="module_title"><strong>所有标种类型</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr >
			<td width="" class="main_td">ID</td>
			<td width="*" class="main_td">借款类型</td>
			<td width="*" class="main_td">名称</td>
			<td width="*" class="main_td">标识符</td>
			<td width="*" class="main_td" title="一旦关闭，前台将不能借此标的款">状态</td>
			<td width="" class="main_td">年利率</td>
			<td width="" class="main_td">借款期限</td>
			<td width="" class="main_td">有效期</td>
			<td width="" class="main_td">冻结保证金</td>
			<td width="" class="main_td">还款方式</td>
			<td width="" class="main_td">操作</td>
		</tr>
		{loop module="borrow" function="GetTypeList" plugins="type" var="item" limit="all" }
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td>{$item.id}<input type="hidden" name="id[]" value="{$item.id}" /></td>
			<td class="main_td1" align="center"><strong>{$item.name}</strong></td>
			<td class="main_td1" align="center">{$item.title}</td>
			<td class="main_td1" align="center">{$item.nid}</td>
			<td class="main_td1" align="center">{if $item.status==1}开启{else}关闭{/if}</td>
			<td class="main_td1" align="center">{$item.apr_first}~{$item.apr_end}%</td>
			<td class="main_td1" align="center">{$item.period_first}~{$item.period_end}{if $item.id==3}天{else}月{/if}</td>
			<td class="main_td1" align="center">{$item.validate_first}~{$item.validate_end}天</td>
			<td class="main_td1" align="center">{$item.frost_scale}%</td>
			<td class="main_td1" align="center">{$item.styles_name}</td>
			<td class="main_td1" align="center"><a href="{$_A.query_url_all}&p=edit&id={$item.id}">修改</a></td>
		</tr>
		{ /loop}
	</form>	
</table>
{/if}
