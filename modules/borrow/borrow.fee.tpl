<ul class="nav3"> 
<li><a href="{$_A.query_url_all}" {if  $magic.request.p=="new" || $magic.request.p==""} id="c_so"{/if}>借款费用</a></li> 
<li><a href="{$_A.query_url_all}&p=type" title="正在借款但未满的标"  {if $magic.request.p=="type"} id="c_so"{/if}>费用类型</a></li> 
</ul>

{if $magic.request.p=="type_edit" || $magic.request.p=="type_new" ||  $magic.request.p=="type"}

{include file="borrow.fee_type.tpl" template_dir="modules/borrow"}


{elseif $magic.request.p=="edit" || $magic.request.p=="new"}

{literal}
<script>
function fee_types(s){
   if (s==0){
        $("#fee_borrow").hide();
   }else{
     $("#fee_borrow").show();
   }
    
    if (s==1){
        $("#fee_borrow_1").show();
        $("#fee_borrow_2").hide();
    }else{
        $("#fee_borrow_2").show();
        $("#fee_borrow_1").hide();
    }
    
  
}
</script>
{/literal}
<div class="module_add">
	<div class="module_title"><strong>{if $magic.request.p=="edit"}修改{else}添加{/if}借款费用</strong></div>
<form action="{$_A.query_url_all}&p={$magic.request.p}" method="post">

	<div class="module_border">
		<div class="l">费用名称：</div>
		<div class="c">
			<input type="text" name="name"  class="input_border" value="{$_A.borrow_fee_result.name}"   size="20" />
		</div>
	</div>
    
	<div class="module_border">
		<div class="l">标识名：</div>
		<div class="c">
			<input type="text" name="nid"  class="input_border" value="{$_A.borrow_fee_result.nid}"   size="20" />
		</div>
	</div>
	<div class="module_border">
		<div class="l">状态：</div>
		<div class="c">
			<input type="radio" name="status"  class="input_border" value="1" {if $_A.borrow_fee_result.status==1} checked=""{/if}  />开启 
			<input type="radio" name="status"  class="input_border"  value="0" {if $_A.borrow_fee_result.status==0} checked=""{/if} />关闭
		</div>
	</div>
	
    
	<div class="module_border">
		<div class="l">排序</div>
		<div class="c">
			<input type="text" name="order"  class="input_border" value="{$_A.borrow_fee_result.order}"   size="10" />(收费方式将会按照此从小到大排序扣除)
		</div>
	</div>
    
	<div class="module_border">
		<div class="l">类型：</div>
		<div class="c">
        <select name="type">
        {loop module="borrow" function="GetFeeTypeList" plugins="fee" limit="all" }
        <option value="{$var.nid}" {if $var.nid==$_A.borrow_fee_result.type} selected=""{/if}>{$var.name}</option>
        {/loop}
        </select>
		（指该费用的扣款时间点，如“借款成功”即该费用在借款成功时扣除）    
		</div>
	</div>
    
    
	<div class="module_border">
		<div class="l">扣除对象：</div>
		<div class="c">
			<input type="radio" name="user_type"  class="input_border" value="borrow" {if $_A.borrow_fee_result.user_type=='borrow' || $_A.borrow_fee_result.user_type==""} checked=""{/if}  />借款者 
			<input type="radio" name="user_type"  class="input_border"  value="tender" {if $_A.borrow_fee_result.user_type=='tender'} checked=""{/if} />投资者
		（指扣除费用的用户）</div>
	</div>
    
	<div class="module_border">
		<div class="l">是否垫付给投资人：</div>
		<div class="c">
			<input type="radio" name="pay_tender"  class="input_border" value="0" {if $_A.borrow_fee_result.pay_tender=='0' || $_A.borrow_fee_result.pay_tender==""} checked=""{/if}  />否 
			<input type="radio" name="pay_tender"  class="input_border"  value="1" {if $_A.borrow_fee_result.pay_tender=='1'} checked=""{/if} />是
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">费用类型：</div>
		<div class="c">
			<input type="radio" name="fee_type"  class="input_border" value="0" {if $_A.borrow_fee_result.fee_type==0 || $_A.borrow_fee_result.fee_type==""} checked=""{/if} onclick="fee_types(0)"  /><span title="不收取任何费用">免费</span>
            <input type="radio" name="fee_type"  class="input_border" value="1"  {if $_A.borrow_fee_result.fee_type==1} checked=""{/if}   onclick="fee_types(1)"/><span title="按固定比例进行收取，比如借款本金× 5%">按比例</span>
           
            <input type="radio" name="fee_type"  class="input_border" value="2" {if $_A.borrow_fee_result.fee_type==2 } checked=""{/if}   onclick="fee_types(2)" /><span title="按比例公式，比如借款本金× *% + 借款本金×（期限-*月）× *%">按比例公式</span>
		</div>
	</div>
    
    <div id="fee_borrow" >
    	<div class="module_border" id="fee_borrow_1" {if $_A.borrow_fee_result.fee_type!="1" }style="display:none"{/if}>
    		<div class="l">按比例：</div>
    		<div class="c">
    		      VIP 【<select name="account_scale_vip">{foreach from=$_A.account_type item='item'}<option value="{$key}" {if $_A.borrow_fee_result.account_scale_vip==$key} selected=""{/if}>{$item}</option>{/foreach}</select>*<input value="{$_A.borrow_fee_result.vip_borrow_scale}" name="vip_borrow_scale" size="3" />%】*<input type="checkbox" name="vip_rank" value="1" title="如果选中，则会跟等级的费用进行挂钩" {if $_A.borrow_fee_result.vip_rank==1} checked=""{/if} />积分等级比例 *<input type="checkbox" name="vip_period" value="1" size="3" {if $_A.borrow_fee_result.vip_period==1} checked=""{/if}/>期数<br />
                  会员【<select name="account_scale_all">{foreach from=$_A.account_type item='item'}<option value="{$key}" {if $_A.borrow_fee_result.account_scale_all==$key} selected=""{/if}>{$item}</option>{/foreach}</select>*<input value="{$_A.borrow_fee_result.all_borrow_scale}" name="all_borrow_scale" size="3" />%】 *<input type="checkbox" name="all_rank" value="1" title="如果选中，则会跟等级的费用进行挂钩" {if $_A.borrow_fee_result.all_rank==1} checked=""{/if} />积分等级比例*<input type="checkbox" name="all_period" value="1" size="3"  {if $_A.borrow_fee_result.all_period==1} checked=""{/if}/>期数
    		</div>
		（“积分等级比例”指按信用等级比例来扣费；“期数”是按借款期数来扣费）
    	</div>
        
        
    	<div class="module_border"  id="fee_borrow_2" {if $_A.borrow_fee_result.fee_type!="2" }style="display:none"{/if}>
    		<div class="l">按比例公式：</div>
    		<div class="c">
    		      VIP 【<select name="account_scales_vip">{foreach from=$_A.account_type item='item'}<option value="{$key}" {if $_A.borrow_fee_result.account_scales_vip==$key} selected=""{/if}>{$item}</option>{/foreach}</select>*（<input value="{$_A.borrow_fee_result.vip_borrow_scales}" name="vip_borrow_scales" size="1" />%+（期数-<input value="{$_A.borrow_fee_result.vip_borrow_scales_month}" name="vip_borrow_scales_month" size="1" />个月)*<input value="{$_A.borrow_fee_result.vip_borrow_scales_scale}" name="vip_borrow_scales_scale" size="1" />%】<a title="比例加起来不高于此上限">上限</a>【<input value="{$_A.borrow_fee_result.vip_borrow_scales_max}" name="vip_borrow_scales_max" size="1" />%】<br />
                  会员【<select name="account_scales_all">{foreach from=$_A.account_type item='item'}<option value="{$key}" {if $_A.borrow_fee_result.account_scales_all==$key} selected=""{/if}>{$item}</option>{/foreach}</select>*（<input value="{$_A.borrow_fee_result.all_borrow_scales}" name="all_borrow_scales" size="1" />%+（期数-<input value="{$_A.borrow_fee_result.all_borrow_scales_month}" name="all_borrow_scales_month" size="1" />个月)*<input value="{$_A.borrow_fee_result.all_borrow_scales_scale}" name="all_borrow_scales_scale" size="1" />%】<a title="比例加起来不高于此上限">上限</a>【<input value="{$_A.borrow_fee_result.all_borrow_scales_max}" name="all_borrow_scales_max" size="1" />%】
    		</div>
		（如无需根据期数递增收费，则只需配置最开始的百分比即可）
    	</div>
    </div>
     
    
    
    
	<div class="module_border">
		<div class="l">适用借款类型：</div>
		<div class="c">
            {loop module="borrow" function="GetTypeList" limit="all" plugins="type" var="type_var"}
           
			<input type="checkbox" name="borrow_types[]"  class="input_border" value="{$type_var.nid}" {$type_var.nid|checked:"$_A.borrow_fee_result.borrow_types"} /><a title="{$type_var.title}"> {$type_var.name}</a>
           
            {/loop}
		</div>
		（指该费用需针对的哪些标种进行收费）
	</div>
    
    
    
	<div class="module_border">
		<div class="l"></div>
		<div class="c">
			<input type="submit"  name="submit" value="确认提交" />
		<input type="reset"  name="reset" value="重置表单" />
        {if $magic.request.id!=""}
        <input type="hidden" name="id" value="{$magic.request.id}" />{/if}
		</div>
	</div>
</form>
 </div>   
    {/articles}
{else}
<div class="module_add">
	<div class="module_title"><strong>所有借款费用</strong> <a href="{$_A.query_url_all}&p=new">【添加借款费用】</a></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr >
			<td width="" class="main_td">ID</td>
			<td width="*" class="main_td">名称</td>
			<td width="*" class="main_td">标识符</td>
			<td width="*" class="main_td" >类型</td>
			<td width="*" class="main_td" >状态</td>
			<td width="" class="main_td">操作类型</td>
			<td width="" class="main_td">费用类型</td>
			<td width="" class="main_td">操作</td>
		</tr>
		{loop module="borrow" function="GetFeeList" plugins="fee" var="item" limit="all" }
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td>{$item.id}<input type="hidden" name="id[]" value="{$item.id}" /></td>
			<td class="main_td1" align="center"><strong>{$item.name}</strong></td>
			<td class="main_td1" align="center">{$item.nid}</td>
			<td class="main_td1" align="center">{if $item.user_type=="borrow"}借款者{else}投资人{/if}</td>
			<td class="main_td1" align="center">{if $item.status==1}<font color='green'>开启</font>{else}关闭{/if}</td>
			<td class="main_td1" align="center">{$item.type_name}</td>
			<td class="main_td1" align="center">{if $item.fee_type==0}免费{elseif $item.fee_type==1}按比例{else}按比例方式{/if}</td>
			<td class="main_td1" align="center"><a href="{$_A.query_url_all}&p=edit&id={$item.id}">修改</a> | <a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url_all}&p=del&id={$item.id}'">删除</a></td>
		</tr>
		{ /loop}
	</form>	
</table>
{/if}