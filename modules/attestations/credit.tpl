<ul class="nav3"> 
<li><a href="{$_A.query_url}">用户积分</a></li> 
<li><a href="{$_A.query_url}/log">积分记录</a></li> 
<li><a href="{$_A.query_url}/rank">积分等级</a></li> 
<li><a href="{$_A.query_url}/type">积分类型</a></li> 
<li><a href="{$_A.query_url}/class">积分分类</a></li> 
</ul> 



{if $_A.query_type == "class"}

<div class="module_add">
	<div class="module_title"><strong>积分分类</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
	<div style="border:1px solid #CCCCCC; ">
	
	<form action="{$_A.query_url_all}" method="post">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="id" value="{$_A.credit_result.id}" />修改积分分类 （<a href="{$_A.query_url_all}">添加</a>）{else}添加积分分类{/if}</strong></div>
	
	<div class="module_border">
		<div class="c">
			<font color="#FF0000">标识名 ：<input type="text" name="nid"  value="{$_A.credit_result.nid}" onkeyup="value=value.replace(/[^a-zA-Z_]/g,'')" /></font>
		</div>
	</div>
	
	<div class="module_border">
		<div class="c">
			名&nbsp;&nbsp;&nbsp;称 ：<input type="text" name="name"  value="{$_A.credit_result.name}"/>
		</div>
	</div>
	
	
	
	<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
		</form>
	</div>
	</div>
		</div>
	<div style="float:right; width:67%; text-align:left">
	<div class="module_add">
	<div class="module_title"><strong>积分分类列表</strong></div>
	</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">名称</td>
		<td width="*" class="main_td">标识名</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ loop module="credit" function="GetClassList"  limit="all" var="item" }
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.name}</td>
		<td class="main_td1" align="center">{$item.nid}</td>
		<td class="main_td1" align="center"><a href="{$_A.query_url_all}&edit={$item.id}">修改</a> <a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url_all}&del={$item.id}'">删除</a></td>
	</tr>
	{/loop}
	
	
</table>
<!--菜单列表 结束-->
</div>
</div>


{elseif $_A.query_type=="list"}

<div class="module_add">
	<div class="module_title"><strong>用户积分</strong>	</div>
</div>

<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td class="main_td">用户名</td>
		<td class="main_td">借出积分</td>
		<td class="main_td">借入积分</td>
		<td class="main_td">材料积分</td>
		<td class="main_td">操作</td>
	</tr>
	{ list module="credit" function="GetList" var="loop" username=request }
	{foreach from=$loop.list item="item"}
	<tr {if $key%2==1}class="tr2"{/if}>
		<td>{ $item.username|default:"$item.user_id"}</td>
		{articles module="borrow" function="GetBorrowCredit" var="Cvar" user_id="$item.user_id" }
		<td >{$Cvar.borrow_credit|default:0}</td>
		<td >{$Cvar.approve_credit|default:0}</td>
		{ list module="attestations" function="GetAttestationsList" var="var"  user_id="$item.user_id"  }
		<td ><!--{$Cvar.att_credit|default:0}-->{$var.nums}</td>
		{/list}
		{/articles}
		<td><a href="{$_A.query_url}/log&username={$item.username}">(认证+还款)积分记录</a>/<a href="{$_A.query_url}/attlog&user={$item.user_id}">材料积分记录</a></td>
	</tr>
	{/foreach}
	<tr>
		<td colspan="7" class="action">
		<div class="floatr">
			用户名：<input type="text" name="username" id="username" value="{$magic.request.username}"/> <input type="button" value="搜索" / onclick="sousuo()">
		</div>
		</td>
	</tr>
	<tr>
		<td colspan="7"  class="page">
		{$loop.pages|showpage} 
		</td>
	</tr>
	{/list}
</table>
<script>
var url = '{$_A.query_url}';
{literal}
function sousuo(){
	var username = $("#username").val();
	location.href=url+"&username="+username;
}

</script>
{/literal}




{elseif $_A.query_type == "type"}
<div class="module_add">
	<div class="module_title"><strong>积分类型</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
	<div style="border:1px solid #CCCCCC; ">
	
	<form action="{$_A.query_url_all}" method="post">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="id" value="{$_A.credit_type_result.id}" />修改积分类型（<a href="{$_A.query_url_all}">添加</a>）{else}添加积分类型{/if}</strong></div>
	
	<div class="module_border">
		<div class="l">类型名称：</div>
		<div class="c">
			<input type="text" name="name"  class="input_border" value="{ $_A.credit_type_result.name}" size="20" />
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">积分代码：</div>
		<div class="c">
			<input type="text" name="nid"  class="input_border" value="{ $_A.credit_type_result.nid}" size="20" onkeyup="value=value.replace(/[^a-zA-Z_]/g,'')" />
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">所属模块：</div>
		<div class="c">
			{select result="$_G.module" name="name" value="nid" select_name="code" selected="$_A.credit_type_result.code" }
		</div>
	</div>
	
	
	
	<div class="module_border">
		<div class="l">积分分类：</div>
		<div class="c">
			{select result="$_G.credit.class" name="name" value="id" select_name="class_id" selected="$_A.credit_type_result.class_id" }
		</div>
	</div>
	
	
	
	<div class="module_border">
		<div class="l">积分状态：</div>
		<div class="c">
		{input type="radio" name="status" value="1|启用,0|关闭" checked="$result.cycle"}
		</div>
	</div>
	

	<div class="module_border">
		<div class="l">积分值：</div>
		<div class="c">
			<input type="text" name="value"  class="input_border" value="{ $_A.credit_type_result.value|default:5}" size="10" onkeyup="value=value.replace(/[^0-9-]/g,'')"/> 
		</div>
	</div>
	<div class="module_border">
		<div class="l">周期：</div>
		<div class="c">
			{input type="radio" name="cycle" value="1|一次,2|每天,3|时间间隔,4|不限" checked="$_A.credit_type_result.cycle"}
		</div>
	</div>

	<div class="module_border">
		<div class="l">奖励次数：</div>
		<div class="c">
			<input type="text" name="award_times"  class="input_border" value="{ $_A.credit_type_result.award_times|default:1}" size="8"  onkeyup="value=value.replace(/[^0-9]/g,'')"/>
		</div>
	</div>

	<div class="module_border">
		<div class="l">时间间隔：</div>
		<div class="c">
			<input type="text" name="interval"  class="input_border" value="{ $_A.credit_type_result.interval|default:60}" size="8"  onkeyup="value=value.replace(/[^0-9]/g,'')"/> 分钟
		</div>
	</div>
	
	
	<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
		</form>
	</div>
	</div>
		</div>
	<div style="float:right; width:67%; text-align:left">
	<div class="module_add">
	<div class="module_title"><strong>积分类型列表</strong></div>
	</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">名称</td>
		<td width="*" class="main_td">标识名</td>
		<td width="*" class="main_td">积分</td>
		<td width="*" class="main_td">所属模块</td>
		<td width="*" class="main_td">积分分类</td>
		<td width="*" class="main_td">周期</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="credit" function="GetTypeList" var="loop" epage=10 name=request nid=request }
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.name}</td>
		<td class="main_td1" align="center">{$item.nid}</td>
		<td class="main_td1" align="center">{$item.value}</td>
		<td class="main_td1" align="center">{$item.code|module}</td>
		<td class="main_td1" align="center">{$item.class_id|credit_class}</td>
		<td class="main_td1" align="center">{if $item.cycle==1}1次{elseif $item.cycle==2}每天{elseif $item.cycle==3}间隔{elseif $item.cycle==4}不限{/if}</td>
		<td class="main_td1" align="center"><a href="{$_A.query_url_all}&edit={$item.id}">修改</a> <a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url_all}&del={$item.id}'">删除</a></td>
	</tr>
	{/foreach}
	<tr>
			<td colspan="11" class="action">
			<div class="floatl">
			<script>
	  var url = '{$_A.query_url_all}';
	    {literal}
	  	function sousuo(){
			var name = $("#name").val();
			var nid = $("#nid").val();
			location.href=url+"&name="+name+"&nid="+nid;
		}
	  
	  </script>
	  {/literal}
			</div>
			<div class="floatr">
				名称：<input type="text" name="name" id="name" value="{$magic.request.name|urldecode}"/>  标识名：<input type="text" name="nid" id="nid" value="{$magic.request.nid}" onkeyup="value=value.replace(/[^a-zA-Z_]/g,'')"/>   <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()">
			</div>
			</td>
		</tr>
	<tr align="center">
		<td colspan="10" align="center"><div align="center">{$loop.pages|showpage}</div></td>
	</tr>
	{/list}
	
</table>
<!--菜单列表 结束-->
</div>
</div>
{elseif $_A.query_type == "rank"}
<div class="module_add">
	<div class="module_title"><strong>积分等级</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
	<div style="border:1px solid #CCCCCC; ">
	
	<form action="{$_A.query_url_all}" method="post">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="id" value="{$_A.credit_rank_result.id}" />修改积分等级 （<a href="{$_A.query_url_all}">添加</a>）{else}添加积分等级{/if}</strong></div>
	
	<div class="module_border">
		<div class="l">名称：</div>
		<div class="c">
			<input type="text" name="name"  class="input_border" value="{ $_A.credit_rank_result.name}" size="12"  />
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">等级：</div>
		<div class="c">
			<input type="text" name="rank"  class="input_border" value="{ $_A.credit_rank_result.rank}" size="12"  />
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">分类：</div>
		<div class="c">
			{select result="$_G.credit.class" name="name" value="id" select_name="class_id" selected="$_A.credit_rank_result.class_id" }
		</div>
	</div>
	
	<div class="module_border">
		<div class="l"><a title="分值的等式为 分值1 <= 分值 <= 分值2" href="#"><strong>？</strong></a>分值：</div>
		<div class="c">
			<input type="text" name="point1"  class="input_border" value="{ $_A.credit_rank_result.point1}" size="8"  /> 到 <input type="text" name="point2"  class="input_border" value="{ $_A.credit_rank_result.point2}" size="8"  /> 
		</div>
	</div>
	
	<div class="module_border">
		<div class="l"><a title="图片的地址在data/images/credit/下面。注意后缀也同样要填写。如credit.gif" href="#"><strong>？</strong></a>图片：</div>
		<div class="c">
			<input type="text" name="pic"  class="input_border" value="{ $_A.credit_rank_result.pic}" size="18"  />
		</div>
	</div>
	
	<div class="module_border">
	<div class="l">其他：</div>
		<div class="c">
			<font color="#FF0000"><input type="text" name="nid"  value="{$_A.credit_rank_result.nid}" size="12"  /></font>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">备注：</div>
		<div class="c">
			<textarea name="remark" cols="30" rows="3">{ $_A.credit_rank_result.remark}</textarea>
		</div>
	</div>
	
	
	
	<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
		</form>
	</div>
	</div>
		</div>
	<div style="float:right; width:67%; text-align:left">
	<div class="module_add">
	<div class="module_title"><strong>积分等级列表</strong>：<select onchange="changese(this)" name="class_id">
	<option>全部</option>
	{ foreach  from="$_G.credit.class" item="item" }
	<option value="{$item.id}" {if $item.id==$magic.request.class_id} selected="selected"{/if}>{$item.name}</option>
	{/loop}
	</select>
	<script language="javascript">
var url = "{$_A.query_url_all}";
{literal}
 function changese(obj){
  window.location.href=url+"&class_id="+obj.value
 }
</script>
{/literal}</div>
	</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">名称</td>
		<td width="*" class="main_td">等级</td>
		<td width="*" class="main_td">分类</td>
		<td width="*" class="main_td">积分开始</td>
		<td width="*" class="main_td">积分结束</td>
		<td width="*" class="main_td">图片</td>
		<td width="*" class="main_td">其他</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ loop module="credit" function="GetRankList"  limit="all" var="item" class_id=request }
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.name}</td>
		<td class="main_td1" align="center">{$item.rank}</td>
		<td class="main_td1" align="center">{$item.class_id|credit_class}</td>
		<td class="main_td1" align="center"> {$item.point1}</td>
		<td class="main_td1" align="center"> {$item.point2}</td>
		<td class="main_td1" align="center"><img src="/data/images/credit/{$item.pic}"></td>
		<td class="main_td1" align="center"> {$item.nid}</td>
		<td class="main_td1" align="center"><a href="{$_A.query_url_all}&edit={$item.id}">修改</a> <a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url_all}&del={$item.id}'">删除</a></td>
	</tr>
	{/loop}
	
	
</table>
<!--菜单列表 结束-->
</div>
</div>
{elseif $_A.query_type == "attlog"}
<div class="module_add">

	<div class="module_title"><strong>审核积分记录</strong></div>
	
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="*" class="main_td">类型</td>
		<td width="*" class="main_td">积分</td>
	</tr>
	{ list module="attestations" function="GetAttestationsList" var="loop" epage=10 user_id="$magic.request.user" status=1 }
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{$item.type_name}</td>
		<td class="main_td1" align="center"><font color="#FF0000" style="font-weight:bold">{if $item.credit==""}{$item.value}{else}{$item.credit}{/if}</font></td>
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
			var nid = $("#nid").val();
			location.href=url+"&username="+username+"&nid="+nid;
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
	{/list}
	
	
</table>
<!--菜单列表 结束-->
</div>

{elseif $_A.query_type == "log"}

{if $magic.request.examine!=""}

<div  style="height:205px; overflow:hidden">
	<form name="form1" method="post" action="{$_A.query_url_all}&examine={$magic.request.examine}" >
	<div class="module_border_ajax">
		<div class="l">用户:</div>
		<div class="c">{$_A.credit_result.username}
		</div>
	</div>
	<div class="module_border_ajax">
		<div class="l">类型:</div>
		<div class="c">{$_A.credit_result.type_name}({$_A.credit_result.nid})
		</div>
	</div>
	<div class="module_border_ajax">
		<div class="l">所属信息:</div>
		<div class="c">{$_A.credit_result.class_id|credit_class} {$_A.credit_result.code|module} {$_A.credit_result.type} {$_A.credit_result.id}
		</div>
	</div>
	<div class="module_border_ajax">
		<div class="l">默认值:</div>
		<div class="c">{$_A.credit_result.value} （表示在积分类型中设定的基本值）
		</div>
	</div>
	<div class="module_border_ajax">
		<div class="l">最终值:</div>
		<div class="c"><input type="text" name="credit" value="{$_A.credit_result.credit}" />
		</div>
	</div>

	<div class="module_submit_ajax" >
		<input type="hidden" name="user_id" value="{$_A.credit_result.user_id}" />
		<input type="submit"  name="reset" class="submit_button" value="确认提交" />
	</div>
	
</form>
</div>

{else}
<div class="module_add">

	<div class="module_title"><strong>积分记录</strong></div>
	
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">用户</td>
		<td width="*" class="main_td">类型</td>
		<td width="*" class="main_td">标识名</td>
		<td width="*" class="main_td">分类</td>
		<td width="*" class="main_td">模块</td>
		<td width="*" class="main_td">类型</td>
		<td width="*" class="main_td">文章id</td>
		<td width="*" class="main_td">积分</td>
		<td width="*" class="main_td">添加时间</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="credit" function="GetLogList" var="loop" epage=10 username=request nid=request class_id=request }
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.username}</td>
		<td class="main_td1" align="center">{$item.type_name}</td>
		<td class="main_td1" align="center">{$item.nid}</td>
		<td class="main_td1" align="center">{$item.class_id|credit_class}</td>
		<td class="main_td1" align="center"> {$item.code|module}</td>
		<td class="main_td1" align="center"> {$item.type}</td>
		<td class="main_td1" align="center"> {$item.article_id}</td>
		<td class="main_td1" align="center"><font color="#FF0000" style="font-weight:bold">{if $item.credit==""}{$item.value}{else}{$item.credit}{/if}</font></td>
		<td class="main_td1" align="center"> {$item.addtime|date_format}</td>
		<td class="main_td1" align="center"> <a href="javascript:void(0)" onclick='tipsWindown("修改积分","url:get?{$_A.query_url_all}&examine={$item.id}",500,230,"true","","false","text");'/>修改积分</a></td>
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
			var nid = $("#nid").val();
			location.href=url+"&username="+username+"&nid="+nid;
		}
	  
	  </script>
	  {/literal}
			</div>
			<div class="floatr">
				用户名：<input type="text" name="name" id="username" value="{$magic.request.username|urldecode}"/>  标识名：<input type="text" name="nid" id="nid" value="{$magic.request.nid}" onkeyup="value=value.replace(/[^a-zA-Z_]/g,'')"/>   <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()">
			</div>
			</td>
		</tr>
	<tr align="center">
		<td colspan="10" align="center"><div align="center">{$loop.pages|showpage}</div></td>
	</tr>
	{/list}
	
	
</table>
<!--菜单列表 结束-->
</div>

{/if}


{/if}