<ul class="nav3"> 
<li><a href="{$_A.query_url_all}" {if  $magic.request.p=="new" || $magic.request.p=="edit" || $magic.request.p==""} id="c_so"{/if}>红包列表</a></li> 
<li><a href="{$_A.query_url_all}&p=type" title="中红包的条件"  {if $magic.request.p=="type" || $magic.request.p=="type_new" || $magic.request.p=="type_edit"} id="c_so"{/if}>红包类型</a></li> 
<li><a href="{$_A.query_url_all}&p=mingxi"  {if $magic.request.p=="mingxi"} id="c_so"{/if}>红包明细</a></li> 
<li><a href="{$_A.query_url_all}&p=count"  {if $magic.request.p=="count"} id="c_so"{/if}>红包统计</a></li> 
</ul>

{if $magic.request.p=="type_edit" || $magic.request.p=="type_new" ||  $magic.request.p=="type"}

{include file="hongbao.type.tpl" template_dir="modules/hongbao"}

{elseif $magic.request.p=="edit" || $magic.request.p=="new"}

<div class="module_add">
	<div class="module_title"><strong>{if $magic.request.p=="edit"}修改{else}添加{/if}红包费用</strong></div>
<form action="{$_A.query_url_all}&p={$magic.request.p}" method="post">

	<div class="module_border">
		<div class="l">红包名称：</div>
		<div class="c">
			<input type="text" name="name"  class="input_border" value="{$_A.hongbao_result.name}"   size="20" />
		</div>
	</div>
    
	<div class="module_border">
		<div class="l">标识名：</div>
		<div class="c">
			<input type="text" name="nid"  class="input_border" value="{$_A.hongbao_result.nid}"   size="20" />
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">状态：</div>
		<div class="c">
			<input type="radio" name="status"  class="input_border" value="1" {if $_A.hongbao_result.status==1} checked=""{/if}  />开启 
			<input type="radio" name="status"  class="input_border"  value="0" {if $_A.hongbao_result.status==0} checked=""{/if} />关闭
		</div>
	</div>
    
	<div class="module_border">
		<div class="l">排序</div>
		<div class="c">
			<input type="text" name="order"  class="input_border" value="{$_A.hongbao_result.order}"   size="10" />(红包将会按照此从小到大排序扣除)
		</div>
	</div>
    
	<div class="module_border">
		<div class="l">类型：</div>
		<div class="c">
        <select name="type_id">
        {loop module="hongbao" function="GetTypeList" limit="all" }
        <option value="{$var.id}" {if $var.id==$_A.hongbao_result.type_id} selected=""{/if}>{$var.name}</option>
        {/loop}
        </select>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">中奖金额设置</div>
		<div class="c">
			<input type="text" name="money"  class="input_border" value="{$_A.hongbao_result.money}"   size="10" />(1元-1000元自由配置)
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">中红包机率</div>
		<div class="c">
			<input type="text" name="percent"  class="input_border" value="{$_A.hongbao_result.percent}"   size="10" />% (1-100可自由配置)
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">有效时间</div>
		<div class="c">
			<input type="text" name="available_time"  class="input_border" value="{$_A.hongbao_result.available_time}"   size="10" /> 小时
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">时间间隔</div>
		<div class="c">
			<input type="text" name="explode_time"  class="input_border" value="{$_A.hongbao_result.explode_time}"   size="10" /> 分钟
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">启动模式：</div>
		<div class="c">
			<input type="radio" name="mode"  class="input_border" value="0" {if $_A.hongbao_result.mode==0} checked=""{/if}  />手动模式
			<input type="radio" name="mode"  class="input_border"  value="1" {if $_A.hongbao_result.mode==1} checked=""{/if} />自动模式
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
	<div class="module_title"><strong>所有红包</strong> <a href="{$_A.query_url_all}&p=new">【添加红包】</a></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr >
			<td width="" class="main_td">ID</td>
			<td width="*" class="main_td">名称</td>
			<td width="*" class="main_td">标识符</td>
			<td width="*" class="main_td">类型</td>
			<td width="*" class="main_td">状态</td>
			<td width="" class="main_td">中奖金额</td>
			<td width="" class="main_td">中奖机率</td>
			<td width="" class="main_td">有效时间</td>
			<td width="" class="main_td">时间间隔</td>
			<td width="" class="main_td">启动模式</td>
			<td width="" class="main_td">操作</td>
		</tr>
		{loop module="hongbao" function="GetHongbaoList" var="item" limit="all" }
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td>{$item.id}<input type="hidden" name="id[]" value="{$item.id}" /></td>
			<td class="main_td1" align="center"><strong>{$item.name}</strong></td>
			<td class="main_td1" align="center">{$item.nid}</td>
			<td class="main_td1" align="center">{$item.type_name}</td>
			<td class="main_td1" align="center">{if $item.status==1}<font color='green'>开启</font>{else}关闭{/if}</td>
			<td class="main_td1" align="center">{$item.money}</td>
			<td class="main_td1" align="center">{$item.percent}%</td>
			<td class="main_td1" align="center">{$item.available_time}小时</td>
			<td class="main_td1" align="center">{$item.explode_time}分钟</td>
			<td class="main_td1" align="center">{if $item.status==1}自动模式{else}手动模式{/if}</td>
			<td class="main_td1" align="center"><a href="{$_A.query_url_all}&p=edit&id={$item.id}">修改</a> | <a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url_all}&p=del&id={$item.id}'">删除</a></td>
		</tr>
		{ /loop}
	</form>	
</table>
{/if}