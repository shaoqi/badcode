{if $_A.query_type == "list"}
<ul class="nav3"> 
<li><a href="{$_A.query_url_all}" {if $magic.request.status=="" &&$magic.request.action==""}  style="color:red"{/if}>所有圈子</a></li> 
<li><a href="{$_A.query_url_all}&status=0" {if $magic.request.status=="0"} style="color:red"{/if}>待审核</a></li> 
<li><a href="{$_A.query_url_all}&status=1" {if $magic.request.status=="1"} style="color:red"{/if}>已审核</a></li> 
<li><a href="{$_A.query_url_all}&status=2" {if $magic.request.status=="2"} style="color:red"{/if}>不通过</a></li> 
<li><a href="{$_A.query_url_all}&action=new" {if $magic.request.action=="new"} style="color:red"{/if}>添加圈子</a></li> 
</ul> 
<div class="module_add">

{if $magic.request.action=="new" || $magic.request.edit!=""}

<div >
	
	<form action="{$_A.query_url_all}" method="post" enctype="multipart/form-data">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="id" value="{$_A.group_result.id}" />修改圈子类型 （<a href="{$_A.query_url_all}&action=new">添加</a>）{else}添加圈子类型{/if}</strong></div>
	
	<div class="module_border">
		<div class="l">圈子所有人（用户名）：</div>
		<div class="c">
			{if $magic.request.edit!=""}
			<input type="hidden" name="user_id" value="{$_A.group_result.user_id}"/>{$_A.group_result.username}
			{else}
			<input type="text" name="username" value="{$_A.group_result.username}"/>
			{/if}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">圈子名称：</div>
		<div class="c">
			<input type="text" name="name" value="{$_A.group_result.name}"/>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">圈子类型：</div>
		<div class="c">
			<select name="type_id">
			{loop module="group" function="GetGroupTypeList" limit="all" }
			<option value="{$var.id}" {if $var.id==$_A.group_result.type_id} selected="selected"{/if}>{$var.name}</option>
			{/loop}
			</select>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">浏览权限：</div>
		<div class="c">
			{input value='1|公开群组,2|秘密群组,3|封闭群组' name="public" checked="$_A.group_result.public"}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">上传头像：</div>
		<div class="c">
			<input type="file" name="pic" /><input type="hidden" name="litpic" value="{$_A.group_result.litpic}"/>{if $_A.group_result.litpic_url!=""}<a href="{$_A.group_result.litpic_url}" target="_blank"><img src="{$_A.group_result.litpic_url}" width="50" height="50" /></a>{/if}
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">简介：</div>
		<div class="c">
			<textarea name="remark" rows="5" cols="30">{$_A.group_result.remark}</textarea>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">排序：</div>
		<div class="c">
			<input type="text" name="order" value="{$_A.group_result.order|default:10}" size="8"/>
		</div>
	</div>
	
	
	<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
		</form>
	</div>
	</div>


{elseif  $magic.request.view!=""}

<div >
	<div class="module_title"><strong>审核圈子</strong></div>
	
	<div class="module_border">
		<div class="l">圈子所有人（用户名）：</div>
		<div class="c">{$_A.group_result.username}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">圈子名称：</div>
		<div class="c">
			{$_A.group_result.name}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">圈子类型：</div>
		<div class="c">
			{$_A.group_result.type_name}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">浏览权限：</div>
		<div class="c">
			{if $_A.group_result.public==1}公开{elseif $_A.group_result.public==2}秘密{else}封闭{/if}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">上传头像：</div>
		<div class="c">
			{if $_A.group_result.litpic_url!=""}<a href="{$_A.group_result.litpic_url}" target="_blank"><img src="{$_A.group_result.litpic_url}" width="50" height="50" /></a>{/if}
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">简介：</div>
		<div class="c">
			{$_A.group_result.remark}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">排序：</div>
		<div class="c">
			{$_A.group_result.order|default:10}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">状态：</div>
		<div class="c">
			{if $_A.group_result.status==1}通过{elseif $_A.group_result.status==2}不通过{elseif $_A.group_result.status==3}封闭{else}待审核{/if}
		</div>
	</div>
	{if $_A.group_result.status==0}
	<div  style="height:205px; overflow:hidden">
	<form name="form1" method="post" action="{$_A.query_url_all}&view={$magic.request.view}" >
	<div class="module_border_ajax">
		<div class="l">审核状态:</div>
		<div class="c">
		<input type="radio" name="status" value="1"/>审核通过 <input type="radio" name="status" value="2"  checked="checked"/>审核不通过 </div>
	</div>
	
	<div class="module_border_ajax" >
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="verify_remark" cols="45" rows="7">{ $_A.borrow_result.verify_remark}</textarea>
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">验证码:</div>
		<div class="c">
			<input name="valicode" type="text" size="11" maxlength="4"  tabindex="3" onClick="$('#valicode').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
		</div>
		<div class="c">
			<img src="/?plugins&q=imgcode" id="valicode" alt="点击刷新" onClick="this.src='/?plugins&q=imgcode&t=' + Math.random();" align="absmiddle" style="cursor:pointer" />
		</div>
	</div>

	<div class="module_submit_ajax" >
		<input type="hidden" name="id" value="{ $magic.request.view}" />
		<input type="submit"  name="reset" class="submit_button" value="确认审核" />
	</div>
	
</form>
</div>
	{/if}

	</div>
	</div>


{else}
<div class="module_title"><strong>圈子管理</strong></div>
	</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">缩略图</td>
		<td width="" class="main_td">圈子名称</td>
		<td width="" class="main_td">创建人</td>
		<td width="*" class="main_td">类型</td>
		<td width="*" class="main_td">公开度</td>
		<td width="*" class="main_td">排序</td>
		<td width="*" class="main_td">状态</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="group" function="GetGroupList" var="loop" username=request status="request"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{if  $item.litpic_url!=""}<a href="{$item.litpic_url}" target="_blank"><img src="{$item.litpic_url}" width="40" height="40" /></a>{else}-{/if}</td>
		<td class="main_td1" align="center">{$item.name}</td>
		<td class="main_td1" align="center">{$item.username}</td>
		<td class="main_td1" align="center">{$item.type_name}</td>
		<td class="main_td1" align="center">{if $item.public==1}公开{elseif $item.public==2}秘密{else}封闭{/if}</td>
		<td class="main_td1" align="center">{$item.order}</td>
		<td class="main_td1" align="center">{if $item.status==1}通过{elseif $item.status==2}不通过{elseif $item.status==3}封闭{else}待审核{/if}</td>
		<td class="main_td1" align="center">{if $item.status==0}<a href="{$_A.query_url_all}&view={$item.id}&user_id={$item.user_id}">审核</a>/{/if}<a href="{$_A.query_url_all}&edit={$item.id}&user_id={$item.user_id}">修改</a>/<a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url_all}&user_id={$item.user_id}&del={$item.id}'">删除</a></td>
	</tr>
	{/foreach}
	<tr>
			<td colspan="11" class="action">
			<div class="floatl">
			<script>
	  var url = '{$_A.query_url_all}';
	  var status = '{$magic.request.status}';
	    {literal}
	  	function sousuo(){
			var username = $("#username").val();
			location.href=url+"&username="+username+"&status="+status;
		}
	  
	  </script>
	  {/literal}
			</div>
			<div class="floatr">
				用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>   <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()">
			</div>
			</td>
		</tr>
	<tr align="center">
		<td colspan="10" align="center"><div align="center">{$loop.pages|showpage}</div></td>
	</tr>
	{/list}
	
</table>
{/if}


{elseif $_A.query_type == "articles"}
<ul class="nav3"> 
<li><a href="{$_A.query_url_all}" {if $magic.request.status=="" &&$magic.request.action==""}  style="color:red"{/if}>所有话题</a></li> 
<li><a href="{$_A.query_url_all}&status=0" {if $magic.request.status=="0"}  style="color:red"{/if}>待审核</a></li> 
<li><a href="{$_A.query_url_all}&status=1" {if $magic.request.status=="1" }  style="color:red"{/if}>已通过</a></li> 
<li><a href="{$_A.query_url_all}&status=2" {if $magic.request.status=="2"}  style="color:red"{/if}>不通过</a></li> 
<li><a href="{$_A.query_url_all}&status=3" {if $magic.request.status=="3"}  style="color:red"{/if}>关闭</a></li> 
<li><a href="{$_A.query_url_all}&action=new" {if $magic.request.action=="new"}  style="color:red"{/if}>添加话题</a></li> 
</ul> 
<div class="module_add">

{if $magic.request.action=="new" || $magic.request.edit!=""}

<div >
	
	<form action="{$_A.query_url_all}" method="post" enctype="multipart/form-data">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="id" value="{$_A.group_articles_result.id}" />修改圈子话题 （<a href="{$_A.query_url_all}&action=new">添加</a>）{else}添加圈子话题{/if}</strong></div>
	
	<div class="module_border">
		<div class="l">话题所有人（用户名）：</div>
		<div class="c">
			{if $magic.request.edit!=""}
			<input type="hidden" name="user_id" value="{$_A.group_articles_result.user_id}"/>{$_A.group_articles_result.username}
			{else}
			<input type="text" name="username" value="{$_A.group_articles_result.username}"/>
			{/if}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">所属圈子：</div>
		<div class="c">{if $magic.request.edit==""}
		<select name="group_id">
			{loop module="group" function="GetGroupList" limit="all" var="_var"}
			<option value="{$_var.id}" {if $_A.group_articles_result.group_id==$_var.id} selected="selected"{/if}>{$_var.name}</option>
			{/loop}
			</select>
		{else}
			{$_A.group_articles_result.group_name}<input type="hidden" name="group_id" value="{$_A.group_articles_result.group_id}"/>
			{/if}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">话题状态：</div>
		<div class="c">
			{input name="status" value="1|已通过,2|不通过,0|待审核,3|关闭" checked="$_A.group_articles_result.status"}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">话题名称：</div>
		<div class="c">
			<input type="text" name="name" value="{$_A.group_articles_result.name}"/>
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">简介：</div>
		<div class="c">
		<textarea id="bcontents" name="contents"  style="width:750px;height:500px;visibility:hidden;">{$_A.group_articles_result.contents}</textarea>	
		{literal}
			<script src="/plugins/dyeditor/dyeditor.js" type="text/javascript"></script>
			<script src="/plugins/dyeditor/lang/cn.js" type="text/javascript"></script><script>
			var editor;
			DyEditor.ready(function(D) {
			editor = D.create('#bcontents',{filterMode : true});
			})</script>
				{/literal}
			
		
		</div>
	</div>
	
	<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
		</form>
	</div>
	</div>


{elseif  $magic.request.view!=""}

<div >
	<div class="module_title"><strong>审核圈子</strong></div>
	
	<div class="module_border">
		<div class="l">圈子所有人（用户名）：</div>
		<div class="c">{$_A.group_articles_result.username}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">圈子名称：</div>
		<div class="c">
			{$_A.group_articles_result.name}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">圈子类型：</div>
		<div class="c">
			{$_A.group_articles_result.type_name}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">浏览权限：</div>
		<div class="c">
			{if $_A.group_articles_result==1}公开{elseif $_A.group_articles_result==2}秘密{else}封闭{/if}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">上传头像：</div>
		<div class="c">
			{if $_A.group_articles_result.litpic_url!=""}<a href="{$_A.group_articles_result.litpic_url}" target="_blank"><img src="{$_A.group_articles_result.litpic_url}" width="50" height="50" /></a>{/if}
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">简介：</div>
		<div class="c">
			{$_A.group_articles_result.remark}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">排序：</div>
		<div class="c">
			{$_A.group_articles_result.order|default:10}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">状态：</div>
		<div class="c">
			{if $_A.group_articles_result.status==1}通过{elseif $_A.group_articles_result.status==2}不通过{elseif $_A.group_articles_result.status==3}封闭{else}待审核{/if}
		</div>
	</div>
	{if $_A.group_articles_result.status==0}
	<div  style="height:205px; overflow:hidden">
	<form name="form1" method="post" action="{$_A.query_url_all}&view={$magic.request.view}" >
	<div class="module_border_ajax">
		<div class="l">审核状态:</div>
		<div class="c">
		<input type="radio" name="status" value="1"/>审核通过 <input type="radio" name="status" value="2"  checked="checked"/>审核不通过 </div>
	</div>
	
	<div class="module_border_ajax" >
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="verify_remark" cols="45" rows="7">{ $_A.borrow_result.verify_remark}</textarea>
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">验证码:</div>
		<div class="c">
			<input name="valicode" type="text" size="11" maxlength="4"  tabindex="3" onClick="$('#valicode').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
		</div>
		<div class="c">
			<img src="/?plugins&q=imgcode" id="valicode" alt="点击刷新" onClick="this.src='/?plugins&q=imgcode&t=' + Math.random();" align="absmiddle" style="cursor:pointer" />
		</div>
	</div>

	<div class="module_submit_ajax" >
		<input type="hidden" name="id" value="{ $magic.request.view}" />
		<input type="submit"  name="reset" class="submit_button" value="确认审核" />
	</div>
	
</form>
</div>
	{/if}

	</div>
	</div>


{else}
<div class="module_title"><strong>话题管理</strong></div>
	</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">用户名</td>
		<td width="" class="main_td">标题</td>
		<td width="" class="main_td">所属圈子</td>
		<td width="*" class="main_td">添加时间</td>
		<td width="*" class="main_td">回复次数</td>
		<td width="*" class="main_td">回复时间</td>
		<td width="*" class="main_td">状态</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="group" function="GetGroupArticlesList" var="loop" username=request status="request"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.username}</td>
		<td class="main_td1" align="center">{$item.name}</td>
		<td class="main_td1" align="center">{$item.group_name}</td>
		<td class="main_td1" align="center">{$item.addtime|date_format}</td>
		<td class="main_td1" align="center">{$item.comment_count}</td>
		<td class="main_td1" align="center">{$item.comment_time|date_format|default:-}</td>
		<td class="main_td1" align="center">{if $item.status==1}<font color="#006600">通过</font>{elseif $item.status==2}不通过{elseif $item.status==3}<font color="#666666">关闭</font>{else}待审核{/if}</td>
		<td class="main_td1" align="center"><a href="{$_A.query_url_all}&edit={$item.id}&user_id={$item.user_id}">修改</a></td>
	</tr>
	{/foreach}
	<tr>
			<td colspan="11" class="action">
			<div class="floatl">
			<script>
	  var url = '{$_A.query_url_all}';
	  var status = '{$magic.request.status}';
	    {literal}
	  	function sousuo(){
			var username = $("#username").val();
			location.href=url+"&username="+username+"&status="+status;
		}
	  
	  </script>
	  {/literal}
			</div>
			<div class="floatr">
				用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>   <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()">
			</div>
			</td>
		</tr>
	<tr align="center">
		<td colspan="10" align="center"><div align="center">{$loop.pages|showpage}</div></td>
	</tr>
	{/list}
	
</table>
{/if}



{elseif $_A.query_type == "type"}
<div class="module_add">
	<div class="module_title"><strong>圈子类型</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
	<div style="border:1px solid #CCCCCC; ">
	
	<form action="{$_A.query_url_all}" method="post">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="id" value="{$_A.group_type_result.id}" />修改圈子类型 （<a href="{$_A.query_url_all}">添加</a>）{else}添加圈子类型{/if}</strong></div>
	
	
	<div class="module_border">
		<div class="l">类型名称：</div>
		<div class="c">
			<input type="text" name="name" value="{$_A.group_type_result.name}"/>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">标识名：</div>
		<div class="c">
			<input type="text" name="nid" value="{$_A.group_type_result.nid}"/>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">状态：</div>
		<div class="c">
			{input name="status" value="1|开启,2|关闭" checked="$_A.group_type_result.status"}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">描述：</div>
		<div class="c">
			<textarea name="remark" rows="5" cols="30">{$_A.group_type_result.remark}</textarea>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">排序：</div>
		<div class="c">
			<input type="text" name="order" value="{$_A.group_type_result.order|default:10}" size="8"/>
		</div>
	</div>
	
	
	<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
		</form>
	</div>
	</div>
		</div>
	<div style="float:right; width:67%; text-align:left">
	<div class="module_add">
	
	
	
	<div class="module_title"><strong>圈子类型列表</strong></div>
	</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">名称</td>
		<td width="" class="main_td">标识名</td>
		<td width="*" class="main_td">状态</td>
		<td width="*" class="main_td">排序</td>
		<td width="*" class="main_td">描述</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="group" function="GetGroupTypeList" var="loop" username=request }
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.name}</td>
		<td class="main_td1" align="center">{$item.nid}</td>
		<td class="main_td1" align="center">{if $item.status==1}开启{else}关闭{/if}</td>
		<td class="main_td1" align="center">{$item.order}</td>
		<td class="main_td1" align="center">{$item.remark}</td>
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

{elseif $_A.query_type == "member"}

<ul class="nav3"> 
<li><a href="{$_A.query_url_all}" {if $magic.request.status==""}  style="color:red"{/if}>所有成员</a></li> 
<li><a href="{$_A.query_url_all}&status=0" {if $magic.request.status=="0" }  style="color:red"{/if}>申请中</a></li> 
<li><a href="{$_A.query_url_all}&status=1" {if $magic.request.status=="1" }  style="color:red"{/if}>已通过</a></li> 
<li><a href="{$_A.query_url_all}&status=2" {if $magic.request.status=="2" }  style="color:red"{/if}>不通过</a></li> 
<li><a href="{$_A.query_url_all}&status=3" {if $magic.request.status=="3" }  style="color:red"{/if}>关闭</a></li> 
<li><a href="{$_A.query_url_all}&status=4" {if $magic.request.status=="4" }  style="color:red"{/if}>退出</a></li> 
</ul> 
<div class="module_add">




{if $magic.request.action=="new" || $magic.request.edit!=""}

<div >
	
	<form action="{$_A.query_url_all}" method="post" enctype="multipart/form-data">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="user_id" value="{$_A.group_member_result.user_id}" /><input type="hidden" name="group_id" value="{$_A.group_member_result.group_id}" />修改圈子成员 （<a href="{$_A.query_url_all}&action=new">添加</a>）{else}添加圈子类型{/if}</strong></div>
	
	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="c">
		{$_A.group_member_result.username}
			
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">所属圈子：</div>
		<div class="c">
			{$_A.group_member_result.group_name}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">申请理由：</div>
		<div class="c">
			{$_A.group_member_result.remark}
		</div>
	</div>
	<div class="module_border">
		<div class="l">申请时间：</div>
		<div class="c">
			{$_A.group_member_result.addtime|date_format}
		</div>
	</div>
	<div class="module_border">
		<div class="l">申请IP：</div>
		<div class="c">
			{$_A.group_member_result.addip}
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">状态：</div>
		<div class="c">
			{input value='1|已通过,2|不通过,0|申请中,3|关闭,4|退出' name="status" checked="$_A.group_member_result.status"}
		</div>
	</div>
	<div class="module_border">
		<div class="l">是否管理员：</div>
		<div class="c">
			{input value='0|不是,1|是' name="admin_status" checked="$_A.group_member_result.admin_status"}
		</div>
	</div>
	
	
	
	<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
		</form>
	</div>
	</div>


{else}
<div class="module_title"><strong>成员管理</strong></div>
	</div>

<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">用户名</td>
		<td width="" class="main_td">所属圈子</td>
		<td width="" class="main_td">是否管理员</td>
		<td width="*" class="main_td">状态</td>
		<td width="*" class="main_td">申请备注</td>
		<td width="*" class="main_td">添加时间</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="group" function="GetGroupMemberList" var="loop" username=request status="request"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.username}</td>
		<td class="main_td1" align="center">{$item.group_name}({$item.group_id})</td>
		<td class="main_td1" align="center">{if $item.admin_status==1}是{else}否{/if}</td>
		<td class="main_td1" align="center">{if $item.status==1}<font color="#006600">通过</font>{elseif $item.status==2}不通过{elseif $item.status==3}<font color="#999999">关闭</font>{else}待审核{/if}</td>
		<td class="main_td1" align="center">{$item.remark}</td>
		<td class="main_td1" align="center">{$item.addtime|date_format:"Y-m-d"}</td>
		<td class="main_td1" align="center"><a href="{$_A.query_url_all}&edit={$item.id}&group_id={$item.group_id}">修改</a>/<a href="#" onClick="javascript:if(confirm('确定要关闭吗?关闭后将不可恢复')) location.href='{$_A.query_url_all}&close={$item.user_id}&group_id={$item.group_id}'">关闭</a></td>
	</tr>
	{/foreach}
	<tr>
			<td colspan="11" class="action">
			<div class="floatl">
			<script>
	  var url = '{$_A.query_url_all}';
	  var status = '{$magic.request.status}';
	    {literal}
	  	function sousuo(){
			var username = $("#username").val();
			location.href=url+"&username="+username+"&status="+status;
		}
	  
	  </script>
	  {/literal}
			</div>
			<div class="floatr">
				用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>   <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()">
			</div>
			</td>
		</tr>
	<tr align="center">
		<td colspan="10" align="center"><div align="center">{$loop.pages|showpage}</div></td>
	</tr>
	{/list}
	
</table>
{/if}




{elseif $_A.query_type == "comments"}

<ul class="nav3"> 
<li><a href="{$_A.query_url_all}" {if $magic.request.status==""}  style="color:red"{/if}>所有评论</a></li> 
<li><a href="{$_A.query_url_all}&status=1"{if $magic.request.status=="1"}  style="color:red"{/if}>已通过</a></li> 
<li><a href="{$_A.query_url_all}&status=2" {if $magic.request.status=="2"}  style="color:red"{/if}>不通过</a></li> 
<li><a href="{$_A.query_url_all}&status=0" {if $magic.request.status=="0"}  style="color:red"{/if}>待审核</a></li> 
<li><a href="{$_A.query_url_all}&status=3" {if $magic.request.status=="3"}  style="color:red"{/if}>关闭</a></li> 
</ul> 
<div class="module_add">




{if $magic.request.action=="new" || $magic.request.edit!=""}

<div >
	
	<form action="{$_A.query_url_all}" method="post" enctype="multipart/form-data">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="id" value="{$_A.group_comments_result.id}" /><input type="hidden" name="group_id" value="{$_A.group_comments_result.group_id}" /><input type="hidden" name="articles_id" value="{$_A.group_comments_result.articles_id}" />修改评论 （<a href="{$_A.query_url_all}&action=new">添加</a>）{else}添加评论{/if}</strong></div>
	
	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="c">
		{$_A.group_comments_result.username}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">所属圈子：</div>
		<div class="c">
			{$_A.group_comments_result.group_name}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">所属文章：</div>
		<div class="c">
			{$_A.group_comments_result.articles_name}
		</div>
	</div>
	<div class="module_border">
		<div class="l">申请时间：</div>
		<div class="c">
			{$_A.group_comments_result.addtime|date_format}
		</div>
	</div>
	<div class="module_border">
		<div class="l">申请IP：</div>
		<div class="c">
			{$_A.group_comments_result.addip}
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">状态：</div>
		<div class="c">
			{input value='1|已通过,2|不通过,0|待审核,3|关闭,4|退出' name="status" checked="$_A.group_comments_result.status"}
		</div>
	</div>
	<div class="module_border" >
		<div class="l">回复内容:</div>
		<div class="c">
			<textarea name="contents" cols="45" rows="7">{ $_A.group_comments_result.contents}</textarea>
		</div>
	</div>
	
	
	<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
		</form>
	</div>
	</div>


{else}
<div class="module_title"><strong>评论管理</strong></div>
	</div>

<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">用户名</td>
		<td width="" class="main_td">所属圈子</td>
		<td width="" class="main_td">所属话题</td>
		<td width="*" class="main_td">回复内容</td>
		<td width="*" class="main_td">回复时间</td>
		<td width="*" class="main_td">状态</td>
		<td width="*" class="main_td">添加时间</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="group" function="GetGroupCommentsList" var="loop" username=request status="request" type="all"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.username}</td>
		<td class="main_td1" align="center">{$item.group_name}({$item.group_id})</td>
		<td class="main_td1" align="center">{$item.articles_name}</td>
		<td class="main_td1" align="center"><div style="height:50px; width:200px; overflow:auto">{$item.contents}</div></td>
		<td class="main_td1" align="center">{$item.addtime|date_format}</td>
		<td class="main_td1" align="center">{if $item.status==1}<font color="#006600">已通过</font>{elseif $item.status==2}不通过{elseif $item.status==3}<font color="#999999">关闭</font>{else}待审核{/if}</td>
		<td class="main_td1" align="center">{$item.addtime|date_format:"Y-m-d"}</td>
		<td class="main_td1" align="center"><a href="{$_A.query_url_all}&edit={$item.id}&group_id={$item.group_id}">修改</a></td>
	</tr>
	{/foreach}
	<tr>
			<td colspan="11" class="action">
			<div class="floatl">
			<script>
	  var url = '{$_A.query_url_all}';
	  var status = '{$magic.request.status}';
	    {literal}
	  	function sousuo(){
			var username = $("#username").val();
			location.href=url+"&username="+username+"&status="+status;
		}
	  
	  </script>
	  {/literal}
			</div>
			<div class="floatr">
				用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>   <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()">
			</div>
			</td>
		</tr>
	<tr align="center">
		<td colspan="10" align="center"><div align="center">{$loop.pages|showpage}</div></td>
	</tr>
	{/list}
	
</table>
{/if}




{/if}