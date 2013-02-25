<ul class="nav3"> 
<li><a href="{$_A.query_url_all}" {if  $magic.request.p=="new" || $magic.request.p==""} id="c_so"{/if}>资金费用</a></li> 
<li><a href="{$_A.query_url_all}&p=type"   {if $magic.request.p=="type"} id="c_so"{/if}>费用类型</a></li> 
</ul>

{if $magic.request.p=="type_edit" || $magic.request.p=="type_new" ||  $magic.request.p=="type"}

{include file="account.fee_type.tpl" template_dir="modules/account"}


{elseif $magic.request.p=="edit" || $magic.request.p=="new"}

{literal}
<script>
function fee_types(s){
   if (s==0){
        $("#fee_account").hide();
   }else{
     $("#fee_account").show();
   }
    
    if (s==1){
        $("#fee_account_1").show();
        $("#fee_account_2").hide();
    }else{
        $("#fee_account_2").show();
        $("#fee_account_1").hide();
    }
    
  
}
</script>
{/literal}
<div class="module_add">
	<div class="module_title"><strong>{if $magic.request.p=="edit"}修改{else}添加{/if}资金费用</strong></div>
<form action="{$_A.query_url_all}&p={$magic.request.p}" method="post">

	<div class="module_border">
		<div class="l">费用名称：</div>
		<div class="c">
			<input type="text" name="name"  class="input_border" value="{$_A.account_fee_result.name}"   size="20" />
		</div>
	</div>
    
	<div class="module_border">
		<div class="l">标识名：</div>
		<div class="c">
			<input type="text" name="nid"  class="input_border" value="{$_A.account_fee_result.nid}"   size="20" />
		</div>
	</div>
	<div class="module_border">
		<div class="l">状态：</div>
		<div class="c">
			<input type="radio" name="status"  class="input_border" value="1" {if $_A.account_fee_result.status==1} checked=""{/if}  />开启 
			<input type="radio" name="status"  class="input_border"  value="0" {if $_A.account_fee_result.status==0} checked=""{/if} />关闭
		</div>
	</div>
	<div class="module_border">
		<div class="l">排序</div>
		<div class="c">
			<input type="text" name="order"  class="input_border" value="{$_A.account_fee_result.order}"   size="10" />(收费方式将会按照此从小到大排序扣除)
		</div>
	</div>
    
	<div class="module_border">
		<div class="l">类型：</div>
		<div class="c">
        <select name="type">
        {loop module="account" function="GetFeeTypeList" plugins="fee" limit="all" }
        <option value="{$var.nid}" {if $var.nid==$_A.account_fee_result.type} selected=""{/if}>{$var.name}</option>
        {/loop}
        </select>
		    
		</div>
	</div>
    
    <div >
    	<div class="module_border"  >
    		<div class="l">最高提现金额：</div>
    		<div class="c">
    		      VIP <input  type="text" name="vip_account_scale_max" value="{$_A.account_fee_result.vip_account_scale_max}" size="3"/>元(只限提现，0表示不限)<br />
                  会员<input  type="text" name="all_account_scale_max" value="{$_A.account_fee_result.all_account_scale_max}" size="3"/>元(只限提现，0表示不限)
    		</div>
    	</div>
        
	<div class="module_border">
		<div class="l">费用类型：</div>
		<div class="c">
			<input type="radio" name="fee_type"  class="input_border" value="0" {if $_A.account_fee_result.fee_type==0 || $_A.account_fee_result.fee_type==""} checked=""{/if} onclick="fee_types(0)"  /><span title="不收取任何费用">免费</span>
            <input type="radio" name="fee_type"  class="input_border" value="1"  {if $_A.account_fee_result.fee_type==1} checked=""{/if}   onclick="fee_types(1)"/><span title="按固定比例进行收取，比如资金本金× 5%">按比例</span>
           
            <input type="radio" name="fee_type"  class="input_border" value="2" {if $_A.account_fee_result.fee_type==2 } checked=""{/if}   onclick="fee_types(2)" /><span title="">按金额</span>
		</div>
	</div>
    
    <div id="fee_account" >
    	<div class="module_border" id="fee_account_1" {if $_A.account_fee_result.fee_type!="1" }style="display:none"{/if}>
    		<div class="l">按比例：</div>
    		<div class="c">
    		      VIP 【金额*<input value="{$_A.account_fee_result.vip_account_scale}" name="vip_account_scale" size="3" />%】 <br />
                  会员【金额*<input value="{$_A.account_fee_result.all_account_scale}" name="all_account_scale" size="3" />%】
    		</div>
    	</div>
        
        
    	<div class="module_border"  id="fee_account_2" {if $_A.account_fee_result.fee_type!="2" }style="display:none"{/if}>
    		<div class="l">按比例公式：</div>
    		<div class="c"> VIP 【<input value="{$_A.account_fee_result.vip_account_all}" name="vip_account_all" size="3" />元以内收取<input value="{$_A.account_fee_result.vip_account_all_fee}" name="vip_account_all_fee" size="3" />元费用。每增加<input value="{$_A.account_fee_result.vip_account_add}" name="vip_account_add" size="3" />元扣费<input value="{$_A.account_fee_result.vip_account_add_fee}" name="vip_account_add_fee" size="3" />元】 当日最高扣费金额：<input  type="text" name="vip_account_max" value="{$_A.account_fee_result.vip_account_max}" size="3"/>元<br />
                  会员【<input value="{$_A.account_fee_result.all_account_all}" name="all_account_all" size="3" />元以内收取<input value="{$_A.account_fee_result.all_account_all_fee}" name="all_account_all_fee" size="3" />元费用。每增加<input value="{$_A.account_fee_result.all_account_add}" name="all_account_add" size="3" />元扣费<input value="{$_A.account_fee_result.all_account_add_fee}" name="all_account_add_fee" size="3" />元】 当日最高扣费金额：<input  type="text" name="all_account_max" value="{$_A.account_fee_result.all_account_max}" size="3"/>元</div>
    		     	</div>
    	</div>
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
	<div class="module_title"><strong>所有资金费用</strong> <a href="{$_A.query_url_all}&p=new">【添加资金费用】</a></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr >
			<td width="" class="main_td">ID</td>
			<td width="*" class="main_td">名称</td>
			<td width="*" class="main_td">标识符</td>
			<td width="*" class="main_td" >类型</td>
			<td width="*" class="main_td" >状态</td>
			<td width="" class="main_td">费用类型</td>
			<td width="" class="main_td">操作</td>
		</tr>
		{loop module="account" function="GetFeeList" plugins="fee" var="item" limit="all" }
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td>{$item.id}<input type="hidden" name="id[]" value="{$item.id}" /></td>
			<td class="main_td1" align="center"><strong>{$item.name}</strong></td>
			<td class="main_td1" align="center">{$item.nid}</td>
			<td class="main_td1" align="center">{$item.type_name}</td>
			<td class="main_td1" align="center">{if $item.status==1}<font color='green'>开启</font>{else}关闭{/if}</td>
			<td class="main_td1" align="center">{if $item.fee_type==0}免费{elseif $item.fee_type==1}按比例{else}按金额{/if}</td>
			<td class="main_td1" align="center"><a href="{$_A.query_url_all}&p=edit&id={$item.id}">修改</a> | <a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url_all}&p=del&id={$item.id}'">删除</a></td>
		</tr>
		{ /loop}
	</form>	
</table>
{/if}