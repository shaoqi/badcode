{if $magic.request.p=="edit"}
<div class="module_add">
	<div class="module_title"><strong>修改还款方式</strong></div>
    <form action="{$_A.query_url_all}&p=edit" method="post">
    {articles module="borrow" function="GetStyleOne" plugins="style" id="$magic.request.id" var="item"}
	
	<div class="module_border">
		<div class="l">方式还款名称：</div>
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
		<div class="l">状态</div>
		<div class="c">
			<input type="radio" name="status"  class="input_border" value="1" {if $item.status==1} checked=""{/if}  />开启 
			<input type="radio" name="status"  class="input_border"  value="0" {if $item.status==0} checked=""{/if} />关闭
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">算法：</div>
		<div class="c">
        <textarea name="contents" cols="50" rows="5">{$item.contents}</textarea>
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
	<div class="module_title"><strong>所有借款还款方式</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr >
			<td width="" class="main_td">ID</td>
			<td width="*" class="main_td">还款方式</td>
			<td width="*" class="main_td">标识符</td>
			<td width="*" class="main_td">标题</td>
			<td width="*" class="main_td" title="一旦关闭，整个网站将不可用">状态</td>
			<td width="" class="main_td">算法信息</td>
			<td width="" class="main_td">操作</td>
		</tr>
		{loop module="borrow" function="GetStyleList" plugins="style" var="item" limit="all" }
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td>{$item.id}<input type="hidden" name="id[]" value="{$item.id}" /></td>
			<td class="main_td1" align="center"><strong>{$item.name}</strong></td>
			<td class="main_td1" align="center">{$item.nid}</td>
			<td class="main_td1" align="center">{$item.title}</td>
			<td class="main_td1" align="center">{if $item.status==1}开启{else}关闭{/if}</td>
			<td class="main_td1" align="center">{$item.contents}</td>
			<td class="main_td1" align="center"><a href="{$_A.query_url_all}&p=edit&id={$item.id}">修改</a></td>
		</tr>
		{ /loop}
	</form>	
</table>
{/if}
