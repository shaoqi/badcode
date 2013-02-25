{if $_A.query_type == "new" || $_A.query_type == "edit"}

<form action="" method="post" id="frm" enctype="multipart/form-data" >
<div class="module_add">
	<div class="module_title"><strong>{if $_A.query_type == "edit" }<input type="hidden" name="id"  value="{$magic.request.id}" />修改文章{else}添加文章{/if}</strong><input type="hidden" name="user_id" value="{$_A.articles_result.user_id|default:$_G.user_id}" /></div>
	<div style="margin-top:10px;">
	<div style="float:right; width:30%;">
		
		<div style="border:1px solid #CCCCCC; margin-bottom:10px ">
			<div class="module_title"><strong>撰写新文章</strong></div>
			<div class="module_border">
				<div class="c">
					状态：<select name='status' >
					<option value="1" {if $_A.articles_result.status==1} selected="selected"{/if}>发布</option>
					<option value="2" {if $_A.articles_result.status==2} selected="selected"{/if}>草稿</option>
					<option value="3" {if $_A.articles_result.status==3} selected="selected"{/if}>等待审核</option>
					</select>
				</div>
			</div>
			<div class="module_border">
				<div class="c">
					排序：<input type="text" name="order" value="{$_A.articles_result.order|default:10}" size="5" onkeyup="value=value.replace(/[^0-9]/g,'')"/>
				</div>
			</div>
			<div class="module_border">
				<div class="c">
					公开度：<input type="radio" name="public" value="1" checked="checked" onclick="$('#password').hide()" {if $_A.articles_result.public==1} checked="checked"{/if} />公开 <input type="radio" name="public" value="2" onclick="$('#password').hide()"/{if $_A.articles_result.public==2} checked="checked"{/if} >私密 <input type="radio" name="public" value="3" onclick="$('#password').show()" {if $_A.articles_result.public==3} checked="checked"{/if} />加密  <input type="text" id="password" name='password' size="3" {if $_A.articles_result.public!=3}style="display:none"{/if} value="{$_A.articles_result.password} " />
				</div>
			</div>
			
			
			<div class="module_border">
				<div class="c">
					发布时间：<input type="text" name="publish"  class="input_border" value="{ $_A.articles_result.publish|default:"nowdate"}" size="30" onclick="change_picktime('yyyy-MM-dd HH:mm:ss')" readonly=""/>
				</div>
			</div>
			<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" name="save" onclick="submitForm()"/>
			</div>
			
		</div>
		
		<div style="border:1px solid #CCCCCC; margin-bottom:10px; ">
			<div class="module_title"><strong>文章标签</strong></div>
			<div class="module_border" style="padding:10px;">
					标签：<input type="text" name="tags"  class="input_border" value="{ $_A.articles_result.tags}" size="30"/>
			</div>
		</div>
		
		<div style="border:1px solid #CCCCCC; ">
			<div class="module_title"><strong>分类栏目</strong></div>
			<div class="module_border" style="padding-left:10px;">
					<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" style="width:94%" >
	
	{ loop module="articles" function="GetTypeMenu" var="item" limit="all" }
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{$item.var}<input type="checkbox" align="absmiddle" name="type_id[]" value="{$item.id}" {if $_A.query_type == "new"}{$_A.articles_type_id|checked:"$item.id"}{else}{$_A.articles_result.type_id|checked:"$item.id"}{/if} /> {$item.name}</td>
	</tr>
	{/loop}
	
</table>
			</div>
		</div>
		
		
		<div style="border:1px solid #CCCCCC; margin-top:10px; ">
			<div class="module_title"><strong>缩略图</strong></div>
			<div class="module_border" style="padding:10px;">
				{if $_A.articles_result.litpic!=""}<img src="{$_A.articles_result.fileurl}" width="50" height="50" align="absmiddle" /><input type="checkbox" name="clearlitpic" value="1" title="选中则会删除掉缩略图" /><input type="hidden" name="oldlitpic" value="{$_A.articles_result.litpic}" />取消 {/if}<input type="file" name="litpic" style="width:150px" />
			</div>
		</div>
		
		
		<div style="border:1px solid #CCCCCC; margin-top:10px; ">
			<div class="module_title"><strong>副标题</strong></div>
			<div class="module_border" style="padding:10px;">
					<input type="text" name="title"  class="input_border" value="{ $_A.articles_result.title}" size="30"/>
			</div>
		</div>
		
	</div>
		</div>
	<div style="float:left; width:67%; text-align:left">
	<div class="module_add">
	<div class="module_title"><strong>撰写新文章</strong></div>
	
	<div class="module_border">
		<div class="c" style="padding:10px 0">
			标&nbsp;&nbsp;题：<input type="text" name="name" value="{$_A.articles_result.name}" style="height:25px; width:400px"/>
		</div>
	</div>
	
	<div class="module_border" style="padding-top:10px;">
		<textarea id="contents" name="contents"  style="width:830px;height:500px;visibility:hidden;">{$_A.articles_result.contents}</textarea>	
		{literal}
<script src="/plugins/dyeditor/dyeditor.js" type="text/javascript"></script>
<script src="/plugins/dyeditor/lang/cn.js" type="text/javascript"></script><script>
var editor;
DyEditor.ready(function(D) {
editor = D.create('#contents',{filterMode : true});
})</script>
	{/literal}
		
		
	</div><font style="color:red">温馨提醒：上传的图片最大宽度为650px,高不限；</font>
	
		</form>
	</div>
<!--菜单列表 结束-->
</div>
</div>
{elseif $_A.query_type=="list"}
{if $magic.request.view!=""}
<div class="module_add">
	
	<div class="module_title"><strong>内容查看</strong></div>

	<div class="module_border">
		<div class="l">标题：</div>
		<div class="c">
			{ $_A.articles_result.name}
		</div>
	</div>
{ if $_A.articles_result.jumpurl!=""}
	<div class="module_border">
		<div class="l">跳转网址：</div>
		<div class="c">
			{ $_A.articles_result.jumpurl}</div>
	</div>
{/if}
	<div class="module_border">
		<div class="l">所属栏目：</div>
		<div class="c">
			{ $_A.articles_result.type_id }</select>
		</div>
	</div>

{ if $_A.articles_result.flag!=""}
	<div class="module_border">
		<div class="l">属性：</div>
		<div class="c">
			{ $_A.articles_result.flag|flag}</div>
	</div>
{/if}
	{if $_A.articles_result.is_jump!=1}
	{if $_A.articles_result.litpic!=""}
	<div class="module_border">
		<div class="l">缩略图：</div>
		<div class="c">
			{if $_A.articles_result.litpic!=""}<a href="./{ $_A.articles_result.fileurl}" target="_blank" title="点击查看大图" ><img src="{ $_A.articles_result.fileurl}" border="0" width="100" alt="点击查看大图" title="点击查看大图" /></a>{/if}</div>
	</div>

	{/if}
	<div class="module_border">
		<div class="l">状态：</div>
		<div class="c">
			{ if $_A.articles_result.status == 0 }隐藏{else}显示{/if}
		 </div>
	</div>

	<div class="module_border">
		<div class="l">排序:</div>
		<div class="c">
			{ $_A.articles_result.order|default:10}
		</div>
	</div>
{ if $_A.articles_result.source!=""}
	<div class="module_border">
		<div class="l">文章来源:</div>
		<div class="c">
			{ $_A.articles_result.source}</div>
	</div>
	{/if}
{ if $_A.articles_result.author!=""}
	<div class="module_border">
		<div class="l">作者:</div>
		<div class="c">
			{ $_A.articles_result.author}</div>
	</div>
{/if}
{ if $_A.articles_result.summary!=""}
	<div class="module_border">
		<div class="l">内容简介:</div>
		<div class="c">
			{ $_A.articles_result.summary}</div>
	</div>
{/if}
	<div class="module_border">
		<div class="l">内容:</div>
		<div class="c">
			<table><tr><td align="left">{ $_A.articles_result.contents}</td></tr></table></div>
	</div>


	<div class="module_border">
		<div class="l">点击次数/评论:</div>
		<div class="c">
			{ $_A.articles_result.hits}/{ $_A.articles_result.comment_times}</div>
	</div>

	{/if}
	<div class="module_border">
		<div class="l">添加时间/IP:</div>
		<div class="c">
			{ $_A.articles_result.addtime|date_format:'Y-m-d'}/{ $_A.articles_result.addip}</div>
	</div>

	<div class="module_border">
		<div class="l">添加人:</div>
		<div class="c">
			{ $_A.articles_result.username}</div>
	</div>

	<div class="module_submit" >
		{ if $_A.query_type == "edit" }<input type="hidden" name="id" value="{ $_A.articles_result.id }" />{/if}
		<input type="button"  name="submit" value="返回上一页" onclick="javascript:history.go(-1)" />
		<input type="button"  name="reset" value="修改内容" onclick="javascript:location.href='{$_A.query_url}/edit{$_A.site_url}&id={ $_A.articles_result.id}'"/>
	</div>
	</form>
</div>
{elseif $magic.request.check!=""}

<div  style="height:205px; overflow:hidden">
	<form name="form1" method="post" action="{$_A.query_url_all}&check={$magic.request.check}" >
	<div class="module_border_ajax">
		<div class="l">审核状态:</div>
		<div class="c">
		<input type="radio" name="status" value="1"/>审核通过 <input type="radio" name="status" value="4"  checked="checked"/>审核不通过 </div>
	</div>
	
	<div class="module_border_ajax" >
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="verify_remark" cols="45" rows="7">{ $_A.articles_result.verify_remark}</textarea>
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">验证码:</div>
		<div class="c">
	<input name="valicode" type="text" size="11" maxlength="4"  tabindex="3" />
		</div>
		<div class="c">
			<img src="/?plugins&q=imgcode" id="valicode" alt="点击刷新" onClick="this.src='/?plugins&q=imgcode&t=' + Math.random();" align="absmiddle" style="cursor:pointer" />
		</div>
	</div>

	<div class="module_submit_ajax" >
		<input type="hidden" name="borrow_nid" value="{ $magic.request.check}" />
		<input type="submit"  name="reset" class="submit_button" value="审核此标" />
	</div>
	
</form>
</div>

{else}
<div class="module_add">
	<div class="module_title" style="overflow:hidden">
	<div style="float:left"><strong>文章列表</strong> (<a href="{$_A.query_url}/new">添加文章</a>)</div>
	<div style="float:right">
				用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>  标题：<input type="text" name="name" id="name" value="{$magic.request.name|urldecode}"/> 类型：{input type="select" name="type_id" value="$_A.articles_type_result" checked="$magic.request.type_id" default="显示全部"}   <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()">
			</div>
	</div>
	
</div>

<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="{$_A.query_url_all}" method="post">
		<tr >
			<td width="" class="main_td"><input type="checkbox" name="allcheck" onclick="checkFormAll(this.form)"/></td>
			<td width="" class="main_td">{if $magic.request.order=="id_desc"}<a href="{$_A.query_url_all}&order=id_asc">ID↓</a>{elseif $magic.request.order=="id_asc"}<a href="{$_A.query_url_all}&order=id_desc">ID↑</a>{else}<a href="{$_A.query_url_all}&order=id_desc">ID</a>{/if}</td>
			<td width="*" class="main_td">标题</td>
			<td width="*" class="main_td">作者</td>
			<td width="" class="main_td">分类栏目</td>
			<td width="" class="main_td">状态</td>
			<td width="" class="main_td">{if $magic.request.order=="order_desc"}<a href="{$_A.query_url_all}&order=order_asc">排序↓</a>{elseif $magic.request.order=="order_asc"}<a href="{$_A.query_url_all}&order=order_desc">排序↑</a>{else}<a href="{$_A.query_url_all}&order=order_desc">排序</a>{/if}</td>
			<td width="" class="main_td">操作</td>
		</tr>
		{ list  module="articles" function="GetList" var="loop" epage=20  username="request" name=request  type_id=request order="id_desc" }
		{foreach from="$loop.list" item="item"}
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td class="main_td1" align="center" ><input type="checkbox" name="aid[]" id="aid[]" value="{$item.id}"/></td>
			<td class="main_td1" align="center" >{ $item.id}</td>
			<td class="main_td1" align="center"><a href="{$_A.query_url}&view={$item.id}">{$item.name|truncate:34}</a></td>
			<td class="main_td1" align="center" >{ $item.username}</td>
			<td class="main_td1" align="center" >{$item.type_id|in_array:"$_A.articles_type_result"}</td>
			<td class="main_td1" align="center" >{ if $item.status ==1}已发布{ elseif $item.status ==3}<a href="javascript:void(0)" onclick='tipsWindown("审核文章","url:get?{$_A.query_url}&check={$item.id}",500,230,"true","","false","text");'>待审核</a>{ elseif $item.status ==2}草稿{else}审核失败{/if}</td>
			<td class="main_td1" align="center" ><input type="text" name="order[]" value="{$item.order}" size="2" /><input type="hidden" name="id[]" value="{$item.id}" /></td>
			<td class="main_td1" align="center" ><a href="{$_A.query_url}&view={$item.id}">查看</a> <a href="{$_A.query_url}/edit{$_A.site_url}&id={$item.id}">修改</a> <a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url}/&del={$item.id}'">删除</a></td>
		</tr>
		{ /foreach}
		<tr>
			<td colspan="11" class="action">
				<div class="floatl"><select name="type">
			<option value="order">排序</option>
			<option value="del">删除</option>
			</select>&nbsp;&nbsp;&nbsp; <input type="submit" value="确认操作" /> 排序不用全选
			</div>
			<script>
	  var url = '{$_A.query_url_all}';
	    {literal}
	  	function sousuo(){
			var username = $("#username").val();
			var name = $("#name").val();
			var type_id = $("#type_id").val();
			location.href=url+"&username="+username+"&name="+name+"&type_id="+type_id;
		}
	  
	  </script>
	  {/literal}
			
			</td>
		</tr>
		<tr>
			<td colspan="8" class="page">
			{$loop.pages|showpage} 
			</td>
		</tr>
	</form>	
	{/list}
</table>
{/if}
{elseif $_A.query_type == "type"}
<div class="module_add">
	<div class="module_title"><strong>分类栏目</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
	<div style="border:1px solid #CCCCCC; ">
	
	<form action="{$_A.query_url_all}" method="post">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="id" value="{$_A.article_type_result.id}" />修改分类栏目 （<a href="{$_A.query_url_all}">添加</a>）{else}添加分类栏目{/if}</strong></div>
	<div class="module_border">
		<div class="c">
			名&nbsp;&nbsp;称：<input type="text" name="name" value="{$_A.article_type_result.name}"/>
		</div>
	</div>
	
	<div class="module_border">
		<div class="c">
			别&nbsp;&nbsp;名：<input type="text" name="nid" value="{$_A.article_type_result.nid}" onkeyup="value=value.replace(/[^a-z0-9]/g,'')"/>
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="c">
			父&nbsp;&nbsp;级：<select name="pid">
			<option>跟目录</option>
			{loop module="articles" function="GetTypeMenu" var="item" }
			{if $item.pid!=$_A.article_type_result.pid}
			<option value="{$item.id}" {if $_A.article_type_result.pid==$item.id}  selected="selected"{/if}>{$item._name}</option>
			{/if}
			{/loop}
			</select>
		</div>
	</div>
	
	<div class="module_border">
		<div class="c">
			排&nbsp;&nbsp;序 ：<input type="text" name="order" value="{$_A.article_type_result.order|default:10}" size="6" onkeyup="value=value.replace(/[^0-9]/g,'')"/>
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="c">
			内&nbsp;&nbsp;容：<textarea cols="30" rows="5" name="contents">{$_A.article_type_result.contents|html_format}</textarea>
		</div>
	</div>
	
	
	
	<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
		</form>
	</div>
	</div>
		</div>
	<div style="float:right; width:67%; text-align:left">
	<div class="module_add">
	<div class="module_title"><strong>分类栏目</strong></div>
	</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">名称</td>
		<td width="*" class="main_td">别名</td>
		<td width="*" class="main_td">排序</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ loop module="articles" function="GetTypeMenu" var="item" limit="all" }
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.type_name}</td>
		<td class="main_td1" align="center">{$item.nid}</td>
		<td class="main_td1" align="center">{$item.order}</td>
		<td class="main_td1" align="center"><a href="{$_A.query_url}/type&edit={$item.id}">修改</a>/<a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url}/type&del={$item.id}'">删除</a></td>
	</tr>
	{/loop}
	
</table>
<!--菜单列表 结束-->
</div>
</div>
{elseif $_A.query_type=="page_list"}

<div class="module_add">
	<div class="module_title"><strong>页面列表</strong></div>
</div>

<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="{$_A.query_url}/action{$_A.site_url}" method="post">
		<tr >
			<td width="" class="main_td">{if $magic.request.order=="id_desc"}<a href="{$_A.query_url_all}&order=id_asc">ID↓</a>{elseif $magic.request.order=="id_asc"}<a href="{$_A.query_url_all}&order=id_desc">ID↑</a>{else}<a href="{$_A.query_url_all}&order=id_desc">ID</a>{/if}</td>
			<td width="*" class="main_td">作者</td>
			<td width="*" class="main_td">标题</td>
			<td width="" class="main_td">标识名</td>
			<td width="" class="main_td">状态</td>
			<td width="" class="main_td">{if $magic.request.order=="order_desc"}<a href="{$_A.query_url_all}&order=order_asc">排序↓</a>{elseif $magic.request.order=="order_asc"}<a href="{$_A.query_url_all}&order=order_desc">排序↑</a>{else}<a href="{$_A.query_url_all}&order=order_desc">排序</a>{/if}</td>
			<td width="" class="main_td">评论</td>
			<td width="" class="main_td">发布时间</td>
			<td width="" class="main_td">操作</td>
		</tr>
		{ loop  module="articles" function="GetPageList" var="item" username="request" order=request limit="all" }
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td class="main_td1" align="center" >{ $item.id}</td>
			<td class="main_td1" align="center" >{ $item.username}</td>
			<td class="main_td1" align="center"><a href="{$_A.query_url_all}&view={$item.id}">{$item.name|truncate:34}</a></td>
			<td class="main_td1" align="center" >{$item.nid}</td>
			<td class="main_td1" align="center" >{ if $item.status ==1}发布{ elseif $item.status ==2}草稿{ elseif $item.status ==3}等待发布{/if}</td>
			<td class="main_td1" align="center" >{$item.order}</td>
			<td class="main_td1" align="center" >{$item.comment_times}</td>
			<td class="main_td1" align="center" >{$item.addtime|date_format}</td>
			<td class="main_td1" align="center" > <a href="{$_A.query_url}/page_edit&id={$item.id}">修改</a> <a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url}/page_list&del={$item.id}'">删除</a></td>
		</tr>
		{ /loop}
		
		
	</form>	
</table>
{elseif $_A.query_type == "page_new" || $_A.query_type == "page_edit"}

<form action="" method="post" id="frm" >
<div class="module_add">
	<div class="module_title"><strong>{if $_A.query_type == "page_edit" }<input type="hidden" name="id"  value="{$magic.request.id}" />修改页面{else}添加页面{/if}</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
		<div style="border:1px solid #CCCCCC; margin-bottom:10px ">
			<div class="module_title"><strong>页面基本信息</strong></div>
			<div class="module_border">
				<div class="c">
					<font color="#FF0000">标识名：</font><input type="text" name="nid"  size="12" value="{$_A.page_result.nid}"/><font color="#FF0000">*</font>
				</div>
			</div>
			
			
			<div class="module_border">
				<div class="c">
					父&nbsp;&nbsp;级 ：<select name="pid">
					<option>跟目录</option>
					{loop module="articles" function="GetPageMenu" var="item" }
					<option value="{$item.id}" {if $_A.page_result.pid==$item.id}  selected="selected"{/if}>{$item._name}</option>
					
					{/loop}
					</select>
				</div>
			</div>
		
			<div class="module_border">
				<div class="c">
					状&nbsp;&nbsp;态 ：<select name='status' >
					<option value="1" {if $_A.page_result.status==1} selected="selected"{/if}>发布</option>
					<option value="2" {if $_A.page_result.status==2} selected="selected"{/if}>草稿</option>
					<option value="3" {if $_A.page_result.status==3} selected="selected"{/if}>等待审核</option>
					</select>
				</div>
			</div>
			<div class="module_border">
				<div class="c">
					排&nbsp;&nbsp;序 ：<input type="text" name="order" value="{$_A.page_result.order|default:10}"size="5"/>
				</div>
			</div>
			
			<div class="module_border">
				<div class="c">
					公开度：<input type="radio" name="public" value="1" checked="checked" onclick="$('#password').hide()" {if $_A.page_result.public==1} checked="checked"{/if} />公开 <input type="radio" name="public" value="2" onclick="$('#password').hide()"/{if $_A.page_result.public==2} checked="checked"{/if} >私密 <input type="radio" name="public" value="3" onclick="$('#password').show()" {if $_A.page_result.public==3} checked="checked"{/if} />加密  <input type="text" id="password" name='password' size="3" {if $_A.page_result.public!=3}style="display:none"{/if} value="{$_A.page_result.password} " />
				</div>
			</div>
			<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" name="save" onclick="submitForm()"/>
			</div>
			
		</div>
		
		<div style="border:1px solid #CCCCCC; margin-bottom:10px; ">
			<div class="module_title"><strong>页面标签</strong></div>
			<div class="module_border" style="padding:10px;">
					标签：<input type="text" name="tags"  class="input_border" value="{ $_A.page_result.tags}" size="30"/>
			</div>
		</div>
		
	</div>
		</div>
	<div style="float:right; width:67%; text-align:left">
	<div class="module_add">
	<div class="module_title"><strong>页面标题内容</strong></div>
	
	<div class="module_border">
		<div class="c" style="padding:10px 0">
			标&nbsp;&nbsp;题：<input type="text" name="name" value="{$_A.page_result.name}" style="height:25px; width:400px"/><font color="#FF0000">*</font>
		</div>
	</div>

	<div class="module_border" style=" padding-top:10px;">
	
	<textarea id="bcontents" name="contents"  style="width:750px;height:500px;visibility:hidden;">{$_A.page_result.contents}</textarea>	
		{literal}
			<script src="/plugins/dyeditor/dyeditor.js" type="text/javascript"></script>
			<script src="/plugins/dyeditor/lang/cn.js" type="text/javascript"></script><script>
			var editor;
			DyEditor.ready(function(D) {
			editor = D.create('#bcontents',{filterMode : true});
			})</script>
				{/literal}
	</div>
		</form>
	</div>
<!--菜单列表 结束-->
</div>
</div>
{/if}