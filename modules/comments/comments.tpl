{if $magic.request.examine==""}
<ul class="nav3"> 
<li><a href="{$_A.query_url}/list" id="c_so">评论管理</a></li> 
<li><a href="{$_A.query_url}/set">评论设置</a></li> 
</ul> 
{/if}
{if $_A.query_type=="set"}
<div class="module_add">
		<form action="" method="post"  enctype="multipart/form-data" >
		<div class="module_title"><strong>评论设置</strong></div>
		<div class="module_border">
			<div class="d">是否开启评论：</div>
			<div class="c">
				{input type="radio" name="con_comments_status" value="1|是,0|否" checked="$_G.system.con_comments_status"}
			</div>
		</div>
		
		<div class="module_border">
			<div class="d">评论是否审核：</div>
			<div class="c">
				{input type="radio" name="con_comments_check_status" value="1|是,0|否" checked="$_G.system.con_comments_check_status"}
			</div>
		</div>
		
		<div class="module_border">
		<div class="d">可以评论的时间：</div>
			<div class="c">
				<input type="text" name="con_comments_time" value="{$_G.system.con_comments_time}"/>分 （用户注册多长时间才可以进行评论，0为不限）
			</div>
		</div>
		
		<div class="module_border">
		<div class="d">评论屏蔽关键字：</div>
			<div class="c">
				<textarea name="con_comments_keywords" cols="40" rows="6">{$_G.system.con_comments_keywords}</textarea><br />多个关键字用 “|” 隔开
			</div>
		</div>
		
		
		<div class="module_border">
		<div class="d">评论屏蔽用户：</div>
			<div class="c">
				<textarea name="con_comments_users" cols="40" rows="6">{$_G.system.con_comments_users}</textarea><br />多个用户用 “|” 隔开
			</div>
		</div>
			
		
		<div class="module_submit"><input type="submit" value="确认提交" class="submit_button"  /></div>
			</form>
		</div>
	</div>

{else}
	{if $magic.request.edit!=""}
<div class="module_add">
<div class="module_title"><strong>评论修改回复</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
	<div style="border:1px solid #CCCCCC; ">
	
	<form action="" method="post">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="id" value="{$_A.comments_result.id}" /><input type="hidden" name="user_id" value="{$_A.comments_result.user_id}" />修改评论 {/if}</strong></div>
	
	
	<div class="module_border">
		<div class="l">评论ID：</div>
		<div class="c">
			{$_A.comments_result.id}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">状态：</div>
		<div class="c">
			{input type="radio" name="status" value="1|已审核,2|禁止" checked="$_A.comments_result.status"}
		</div>
	</div>
	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="c">
			{$_A.comments_result.username}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">评论内容：</div>
		<div class="c">
			<textarea name="contents" rows="5" cols="30">{$_A.comments_result.contents|html_format}</textarea>
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">评论模块：</div>
		<div class="c">
			{$_A.comments_result.code|module}  | 
			{$_A.comments_result.type} |
			{$_A.comments_result.article_id} 
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">时间IP：</div>
		<div class="c">
			{$_A.comments_result.addtime|date_format} | {$_A.comments_result.addip}
		</div>
	</div>
	
	<div class="module_title"><strong>回复</strong></div>
	<div class="module_border">
		<div class="l">回复内容：</div>
		<div class="c">
			<textarea name="comments" rows="5" cols="30"></textarea><br />不回复请为空
		</div>
	</div>
	
	<div class="module_border_ajax" >
		<div class="l">验证码：</div>
		<div class="c">
			<input name="valicode" type="text" size="11" maxlength="4"  onClick="$('#valicode').attr('src','/plugins/index.php?q=imgcode&t=' + Math.random())"/>
		
			<img src="/plugins/index.php?q=imgcode" id="valicode" alt="点击刷新" onClick="this.src='/plugins/index.php?q=imgcode&t=' + Math.random();" align="absmiddle" style="cursor:pointer" />
		</div>
	</div>
	
	
	<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
		</form>
	</div>
	</div>
		</div>
	<div style="float:right; width:67%; text-align:left">
{else}
<div>
{/if}
	
	<div class="module_add">
	<div class="module_title"><strong>评论管理列表</strong></div>
	</div><form action="" method="post" id="form1">
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td"><input type="checkbox" name="allcheck" onclick="checkFormAll(this.form)"/></td>
		<td width="" class="main_td">评论人</td>
		<td width="" class="main_td">模块</td>
		<td width="" class="main_td">类型</td>
		<td width="" class="main_td">内容</td>
		<td width="" class="main_td">评论id</td>
		<td width="" class="main_td">所评论id</td>
		<td width="*" class="main_td">状态</td>
		<td width="*" class="main_td">添加时间</td>
	</tr>
	{ list module="comments" function="GetCommentsList" var="loop" username=request }
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center"><input type="checkbox" name="id[{$key}]" value="{$item.id}"/></td>
		<td class="main_td1" align="center">{$item.username}</td>
		<td class="main_td1" align="center">{$item.code}</td>
		<td class="main_td1" align="center">{$item.type}</td>
		<td class="main_td1" align="center">{$item.contents}</td>
		<td class="main_td1" align="center">{$item.pid}</td>
		<td class="main_td1" align="center">{$item.reply_id}</td>
		<td class="main_td1" align="center">{if $item.status==0}待审核{elseif $item.status==1}已通过{elseif $item.status==2}不通过{else}回收站{/if}</td>
		<td class="main_td1" align="center">{$item.addtime|date_format}</td>
	</tr>
	{/foreach}
	<tr>
			<td colspan="11" class="action">
			<div class="floatl"><select name="type"><option value="yes">审核通过</option><option value="no">不通过</option><option value="delete">删除</option>
			<option value="over">回收站</option>
			</select> <input type="submit"  value=" 操作 " />
			<script>
	  var url = '{$_A.query_url_all}';
	    {literal}
	  	function sousuo(){
			var username = $("#username").val();
			location.href=url+"&username="+username;
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

	</form>
<!--菜单列表 结束-->
</div>
</div>
{/if}