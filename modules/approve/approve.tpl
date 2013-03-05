
{if $_A.query_type == "list" }


{elseif $_A.query_type == "realname" || $_A.query_type=="realname_id5list" || $_A.query_type=="realname_id5set"}

	{include file="approve.realname.tpl" template_dir = "modules/approve"}
{elseif $_A.query_type=="all"}
	{include file="approve.all.tpl" template_dir = "modules/approve"}
{elseif $_A.query_type == "edu" || $_A.query_type=="edu_id5" || $_A.query_type=="edu_set"}

	{include file="approve.edu.tpl" template_dir = "modules/approve"}
{elseif $_A.query_type == "video" }

	{include file="approve.video.tpl" template_dir = "modules/approve"}

{elseif $_A.query_type == "sms" || $_A.query_type == "sms_log" || $_A.query_type == "sms_set" }

{if $magic.request.examine!=""}

<div  style="height:205px; overflow:hidden">
	<form name="form1" method="post" action="{$_A.query_url_all}&examine={$magic.request.examine}" >
	<div class="module_border_ajax">
		<div class="l">审核:</div>
		<div class="c">
		{if $_A.approve_result.status==1}
		审核通过<input type="hidden" name="status" value="1" />
		{else}
		<input type="radio" name="status" value="1"/>审核通过 <input type="radio" name="status" value="2"  checked="checked"/>审核不通过{/if} </div>
	</div>
	
	
	<div class="module_border_ajax" >
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="verify_remark" cols="45" rows="5">{$_A.approve_result.verify_remark}</textarea>
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">验证码:</div>
		<div class="c">
			<input name="valicode" type="text" size="11" maxlength="4"  tabindex="3"/>
		</div>
		<div class="c">
			<img src="/?plugins&q=imgcode" id="valicode1" alt="点击刷新" onClick="this.src='/?plugins&q=imgcode&t=' + Math.random();" align="absmiddle" style="cursor:pointer" />
		</div>
	</div>

	<div class="module_submit_ajax" >
		<input type="hidden" name="borrow_nid" value="{ $magic.request.check}" />
		<input type="submit"  name="reset" class="submit_button" value="审核此标" />
	</div>
	
</form>
</div>


{elseif $magic.request.view!=""}

<div  style="height:205px; overflow:scroll">
	<div class="module_border_ajax">
		<div class="l">用户名:</div>
			<div class="c">{$_A.approve_result.username}
		</div>
		<div class="l">手机:</div>
			<div class="c">{$_A.approve_result.phone}
		</div>
	</div>
	
	<div class="module_border_ajax">
		<div class="l">类型:</div>
		<div class="c">{$_A.approve_result.type|linkages:"approve_sms_type"}
		</div>
		<div class="l">状态:</div>
		<div class="c">{$_A.approve_result.status|linkages:"approve_sms_status"}
		</div>
	</div>
	<div class="module_border_ajax">
		<div class="l">内容:</div>
		<div class="c">{$_A.approve_result.contents}
		</div>
	</div>
	
	<div class="module_border_ajax">
		<div class="l">发送信息:</div>
		<div class="c">代码：{$_A.approve_result.send_code|default:"-"} | 返回：{$_A.approve_result.send_return|default:"-"} | 状态：{$_A.approve_result.send_status|default:"-"}
		</div>
	</div>
	
	<div class="module_border_ajax">
		<div class="l">验证码:</div>
		<div class="c">验证码：{$_A.approve_result.code|default:"-"} | 状态：{$_A.approve_result.code_status} | 时间：{$_A.approve_result.check_time|date_format|default:"-"}
		</div>
	</div>
	
	<div class="module_border_ajax">
		<div class="l">添加时间:</div>
		<div class="c">{$_A.approve_result.addtime|date_format|default:"-"}
		</div>
		<div class="l">添加ip:</div>
		<div class="c">{$_A.approve_result.addip|default:"-"}
		</div>
	</div>
</div>

{else}
<ul class="nav3"> 
<li><a href="{$_A.query_url}/sms"  {if $_A.query_type=="sms"}style="color:red"{/if}>手机认证</a></li> 
<li><a href="{$_A.query_url}/sms_log" {if $_A.query_type=="sms_log"}style="color:red"{/if}>发送记录</a></li> 
<li><a href="{$_A.query_url}/sms_set" {if $_A.query_type=="sms_set"}style="color:red"{/if}>手机设置</a></li> 
</ul> 
	{if $_A.query_type=="sms_log"}

<div class="module_add">
	<div class="module_title"><strong>手机短信发送记录</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
	<div style="border:1px solid #CCCCCC; ">
	
	<form action="{$_A.query_url_all}" method="post">
	<div class="module_title"><strong>手机短信群发</strong></div>
	
	<div class="module_border">
		<div class="c">
			用 户 名 ：<input type="text" name="username" />
		</div>
	</div>
	
	<div class="module_border">
		<div class="c">
			手 机 号 ：<input type="text" name="phone" />
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="c">
			用 户 id ：<input type="text" name="user_id1" size="5" /> 到 <input type="text" name="user_id2"  size="5"/>
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="c">
			发送状态 ：<select name="status"><option value="0">待发送</option><option value="1">立即发送</option></select>
		</div>
	</div>
	
	
	<div class="module_border_ajax" >
		<div class="c">发送内容：<textarea name="contents" cols="30" rows="5"></textarea>
		</div>
	</div>
	
	<div class="module_border_ajax" >
		<div class="c">
			验 证 码 ：<input name="valicode" type="text" size="11" maxlength="4"/>
		
			<img src="/?plugins&q=imgcode" id="valicode" alt="点击刷新" onClick="this.src='/?plugins&q=imgcode&t=' + Math.random();" align="absmiddle" style="cursor:pointer" />
		</div>
	</div>
	
	
	<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
		</form>
	</div>
	</div>
		</div>
	<div style="float:right; width:67%; text-align:left">
	<div class="module_add">
	<div class="module_title"><strong>手机短信发送记录列表</strong><span style="float:right">
		用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>  手机号：<input type="text" name="phone" id="phone" value="{$magic.request.phone}"/> 状态：{linkages nid="approve_sms_status" name="status" value="$magic.request.status" default="查看全部" type="value"}   <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()"></span></div>
	</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">用户</td>
		<td width="*" class="main_td">手机号码</td>
		<td width="*" class="main_td">类型</td>
		<td width="*" class="main_td">发送状态</td>
		<td width="*" class="main_td">添加时间</td>	
		<td width="*" class="main_td">发送时间</td>		
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="approve" function="GetSmslogList" var="loop" epage=20 page=request username=request phone=request status=request }
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.username|default:"-"}</td>
		<td class="main_td1" align="center">{$item.phone}</td>
		<td class="main_td1" align="center">{$item.type|linkages:"approve_sms_type"}</td>
		<td class="main_td1" align="center">{$item.status|linkages:"approve_sms_status"}</td>
		<td class="main_td1" align="center">{$item.addtime|date_format}</td>
		<td class="main_td1" align="center">{$item.send_time|date_format}</td>
		<td class="main_td1" align="center"><a href="javascript:void(0)" onclick='tipsWindown("短信记录查看","url:get?{$_A.query_url_all}&view={$item.id}",500,230,"true","","false","text");'/>查看</a></td>
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
			var phone = $("#phone").val();
			var status = $("#status").val();
			location.href=url+"&username="+username+"&phone="+phone+"&status="+status;
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
	{elseif $_A.query_type=="sms_set"}
	
<div class="module_add">
<form action="" method="post"  enctype="multipart/form-data" >
	<div class="module_title"><strong>手机短信设置</strong></div>
	
	
	<div class="module_border">
	<div class="d">是否开启手机发送功能：</div>
		<div class="c">
			{input type="radio" name="con_sms_status" value="0|否,1|是"  checked="$_G.system.con_sms_status}
		</div>
	</div>
	
	<div class="module_border">
		<div class="d">手机短信发送地址：</div>
		<div class="c">
			<input type="text" name="con_sms_url" value="{$_G.system.con_sms_url}" size="60"/>
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="d">手机短信尾部文字：</div>
		<div class="c">
			<input type="text" name="con_sms_text" value="{$_G.system.con_sms_text}" size="20"/>
		</div>
	</div>
	
	
	<div class="module_border">
	<div class="d">是否UTF-8转换：</div>
		<div class="c">
			{input type="radio" name="con_sms_utf_status" value="0|否,1|是"  checked="$_G.system.con_sms_utf_status}
		</div>
	</div>
	
	
	<div class="module_submit"><input type="submit" value="{$MsgInfo.admin_name_submit}"  class="submit_button" /></div>
		</form>
	</div>
</div>

	{else}
<div class="module_add">
	<div class="module_title"><strong>手机短信认证</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
	<div style="border:1px solid #CCCCCC; ">
	
	<form action="{$_A.query_url_all}" method="post">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="id" value="{$_A.approve_result.id}" />修改手机短信认证 （<a href="{$_A.query_url_all}">添加</a>）{else}添加手机短信认证{/if}</strong></div>
	
	<div class="module_border">
		<div class="c">
			用 户 名 ：{if $magic.request.edit!=""}<input type="hidden" name="username" value="{$_A.approve_result.username}" />{$_A.approve_result.username}{else}<input type="text" name="username" />{/if}
		</div>
	</div>
	
	
	{if $magic.request.edit!=""}
	<div class="module_border">
		<div class="c">
			状&nbsp;&nbsp;态：{$_A.approve_result.status|linkages:"approve_status"}
		</div>
	</div>
	{/if}
	
	<div class="module_border">
		<div class="c">
			手机号码：<input type="text" name="phone" value="{$_A.approve_result.phone}"/>
		</div>
	</div>
	
	<div class="module_border_ajax" >
		<div class="c">
			验 证 码 ：<input name="valicode" type="text" size="11" maxlength="4"/>
		
			<img src="/?plugins&q=imgcode" id="valicode" alt="点击刷新" onClick="this.src='/?plugins&q=imgcode&t=' + Math.random();" align="absmiddle" style="cursor:pointer" />
		</div>
	</div>
	
	
	<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
		</form>
	</div>
	</div>
		</div>
	<div style="float:right; width:67%; text-align:left">
	<div class="module_add">
	<div class="module_title"><strong>手机短信认证列表</strong><span style="float:right">
		用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>  手机号：<input type="text" name="phone" id="phone" value="{$magic.request.phone}"/> 状态：{linkages nid="approve_status" name="status" value="$magic.request.status" default="审核全部" type="value"}   <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()"></span></div>
	</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">用户</td>
		<td width="*" class="main_td">手机号码</td>
		<td width="*" class="main_td">状态</td>
		<td width="*" class="main_td">添加时间</td>
		<td width="*" class="main_td">通过时间</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="approve" function="GetSmsList" var="loop" epage=20 page=request username=request phone=request status=request }
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.username}</td>
		<td class="main_td1" align="center">{$item.phone}</td>
		<td class="main_td1" align="center">{$item.status|linkages:"approve_status"}</td>
		<td class="main_td1" align="center">{$item.addtime|date_format:"Y-m-d"}</td>
		<td class="main_td1" align="center">{$item.verify_time|date_format:"Y-m-d"|default:-}</td>
		<td class="main_td1" align="center">{if $item.status==0 ||  $item.status==1}<a href="javascript:void(0)" onclick='tipsWindown("手机短信认证审核","url:get?{$_A.query_url_all}&examine={$item.id}",500,230,"true","","false","text");'/>审核</a> {/if}{if $item.status==0}<a href="{$_A.query_url_all}&edit={$item.id}">修改</a>{/if}</td>
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
			var phone = $("#phone").val();
			var status = $("#status").val();
			location.href=url+"&username="+username+"&phone="+phone+"&status="+status;
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
</div>
		
	{/if}
{/if}
{/if}