{literal}
<script>
function CheckExamine(){
	if ($("#verify_remark").val()==""){
		alert("备注不能为空");
		return false;
	}
}
</script>
{/literal}


{if $magic.request.id5!=""}
<div  style="height:205px; overflow:hidden">
	<form name="form1" method="post" action="{$_A.query_url_all}&id5={$magic.request.id5}" onsubmit="return CheckExamine()" >
	<div class="module_border_ajax">
		<div class="c">
		<strong>说明：</strong>如果要进行ID5认证，请先确保你的ID5已经签订好协议，同时后台这边的id5认证将不扣除用户的任何费用。</div>
	</div>
	
	<div class="module_border_ajax" >
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="verify_remark" cols="45" rows="5" id="verify_remark"></textarea>
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
		<input type="hidden" name="borrow_nid" value="{ $magic.request.check}" />{literal}
		<input type="submit"  name="submit" class="submit_button" value="确认审核"  />{/literal}
	</div>
	
</form>
</div>



{elseif $magic.request.examine!=""}
<div  style="height:205px; overflow:hidden">
	<form name="form1" method="post" action="{$_A.query_url_all}&examine={$magic.request.examine}" onsubmit="return CheckExamine()">
	<div class="module_border_ajax">
		<div class="l">审核:</div>
		<div class="c">
		{input type="radio" value="0|待审核,1|审核通过,2|审核不通过" checked="$_A.approve_result.status" name="status"}
	</div>
	
	<div class="module_border_ajax" >
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="verify_remark" cols="45" rows="7">{$_A.approve_result.verify_remark}</textarea>
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
		<input type="submit"  name="reset" class="submit_button" value="确认审核" />
	</div>
	
</form>
</div>



{else}
	<ul class="nav3"> 
	<li><a href="{$_A.query_url}/edu" {if $_A.query_type=="edu"}style="color:red"{/if}>学历认证</a></li> 
	<li><a href="{$_A.query_url}/edu_id5" {if $_A.query_type=="edu_id5"}style="color:red"{/if}>认证记录</a></li> 
	</ul> 

	{if $_A.query_type=="edu_id5"}
	<div class="module_add">
		<div class="module_title"><strong>认证记录列表</strong>
			<span style="float:right">用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/> 真实姓名：<input type="text" name="realname" id="realname" value="{$magic.request.realname|urldecode}"/>  身份证号：<input type="text" name="card_id" id="card_id" value="{$magic.request.card_id}"/>   <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()"></span></div>
		</div>
		<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
		<tr >
			<td width="" class="main_td">ID</td>
			<td width="" class="main_td">用户</td>
			<td width="*" class="main_td">真实姓名</td>
			<td width="*" class="main_td">身份证号码</td>
			<td width="*" class="main_td">毕业院校</td>
			<td width="*" class="main_td">专业</td>
			<td width="*" class="main_td">学历</td>
			<td width="*" class="main_td">入学年份</td>
			<td width="*" class="main_td">毕业时间</td>
			<td width="*" class="main_td">毕业结论</td>
			<td width="*" class="main_td">学历类型</td>
			<td width="*" class="main_td">审核状态</td>
			<td width="*" class="main_td">审核结果</td>
			<td width="*" class="main_td">图像</td>
			<td width="*" class="main_td">时间</td>
		</tr>
		{ list module="approve" function="GetEduId5List" var="loop" username=request  realname=request  card_id=request  epage=20 }
		{foreach from="$loop.list" item="item"}
		<tr {if $key%2==1} class="tr2"{/if}>
			<td class="main_td1" align="center">{ $item.id}</td>
			<td class="main_td1" align="center">{$item.username}</td>
			<td class="main_td1" align="center">{$item.realname}</td>
			<td class="main_td1" align="center">{$item.card_id}</td>
			<td class="main_td1" align="center">{$item.graduate}</td>
			<td class="main_td1" align="center">{$item.speciality}</td>
			<td class="main_td1" align="center">{$item.degree}</td>
			<td class="main_td1" align="center">{$item.enrol_date}</td>
			<td class="main_td1" align="center">{$item.graduate_date}</td>
			<td class="main_td1" align="center">{$item.result}</td>
			<td class="main_td1" align="center">{$item.style}</td>
			<td class="main_td1" align="center">{$item.status|linkages:"approve_cardid_status"}</td>
			<td class="main_td1" align="center">{$item.value}</td>
			<td class="main_td1" align="center">{if $item.status==3}<a href="{$item.user_id|idedu}" target="_blank"><img src="{$item.user_id|idedu}" width="40" /></a>{elseif $item.fileurl!=""}<img src="{$item.fileurl}" width="40" />{else}-}{/if}</td>
			<td class="main_td1" align="center">{$item.addtime|date_format}</td>
		</tr>
		{/foreach}
		<tr>
				<td colspan="15" class="action">
				<div class="floatl">
				<script>
				  var url = '{$_A.query_url_all}';
					{literal}
					function sousuo(){
						var username = $("#username").val();
						var realname = $("#realname").val();
						var card_id = $("#card_id").val();
						location.href=url+"&username="+username+"&realname="+realname+"&card_id="+card_id;
					}
				  </script>
				  {/literal}
				</div>
				<div class="floatr">
				</div>
				</td>
			</tr>
			
		<tr align="center">
			<td colspan="18" align="center"><div align="center">{$loop.pages|showpage}</div></td>
		</tr>
		{/list}
	</table>
	
	
	
	{elseif $_A.query_type=="edu_set"}
	<div class="module_add">
	<form action="" method="post"  enctype="multipart/form-data" >
		<div class="module_title"><strong>ID5认证设置</strong></div>
		
		
		<div class="module_border">
		<div class="d">是否开启id5学历认证：</div>
			<div class="c">
				{input type="radio" name="con_id5_edu_status" value="0|否,1|是"  checked="$_G.system.con_id5_edu_status}
			</div>
		</div>
		
		
		<div class="module_border">
		<div class="d">ID5学历认证费用：</div>
			<div class="c">
				<input type="text" name="con_id5_edu_fee" value="{$_G.system.con_id5_edu_fee}"/>
			</div>
		</div>
		
		<div class="module_submit"><input type="submit" value="{$MsgInfo.admin_name_submit}"  class="submit_button" /></div>
			</form>
		</div>
	</div>
	
	{else}
	
	<div class="module_add">
		<div class="module_title"><strong>学历认证</strong></div>
		<div style="margin-top:10px;">
		<div style="float:left; width:30%;">
			
		<div style="border:1px solid #CCCCCC; ">
		
		{if $magic.request.user_id==""}
		<form action="{$_A.query_url_all}" method="post">
		<div class="module_title"><strong>查找用户</strong>(将按顺序进行搜索)<input type="hidden" name="type" value="user_id" /></div>
		
		
		<div class="module_border">
			<div class="l">用户名：</div>
			<div class="c">
				<input type="text" name="username" />
			</div>
		</div>
		
		
		<div class="module_border">
			<div class="l">用户ID：</div>
			<div class="c">
				<input type="text" name="user_id" />
			</div>
		</div>
		
		
		<div class="module_border">
			<div class="l">邮箱：</div>
			<div class="c">
				<input type="text" name="email" />
			</div>
		</div>
		
		<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
			</form>
		</div>
		{else}
		<form action="" method="post" enctype="multipart/form-data">
		<div class="module_title"><strong>修改身份认证信息</strong><input type="hidden" name="user_id" value="{$magic.request.user_id}" /></div>
		
		<div class="module_border">
		<div class="l">用 户 名 ：</div>
			<div class="c">
				{$_A.approve_result.username}
			</div>
		</div>
		
		
		<div class="module_border">
		<div class="l">状&nbsp;&nbsp;态：</div>
			<div class="c">
				{$_A.approve_result.status|linkages:"approve_status"}
			</div>
		</div>
		
		{if $_A.approve_result.verify_remark!=""}
		<div class="module_border">
		<div class="l"><font color="#FF0000">审核备注：</font></div>
			<div class="c">
				{$_A.approve_result.verify_remark}
			</div>
		</div>
		{/if}
		
		<div class="module_border">
		<div class="l">真实姓名：</div>
			<div class="c">
				{$_A.approve_result.realname}
			</div>
		</div>
		
		<div class="module_border">
			<div class="l">毕业学校：</div>
			<div class="c">
				<input type="text" name="graduate" value="{$_A.approve_result.graduate}"  />
			</div>
		</div>
		
		<div class="module_border">
			<div class="l">专业：</div>
			<div class="c">
				<input type="text" name="speciality" value="{$_A.approve_result.speciality}"  />
			</div>
		</div>
		
		<div class="module_border">
			<div class="l">学历：</div>
			<div class="c">
				<input type="text" name="degree" value="{$_A.approve_result.degree}" />
			</div>
		</div>
		
		<div class="module_border">
			<div class="l">入学时间：</div>
			<div class="c">
				<input type="text" name="enrol_date" value="{$_A.approve_result.enrol_date}" />
			</div>
		</div>
		
		<div class="module_border">
			<div class="l">毕业时间：</div>
			<div class="c">
				<input type="text" name="graduate_date" value="{$_A.approve_result.graduate_date}" />
			</div>
		</div>
		
		<div class="module_border">
		<div class="l">学历证件：</div>
			<div class="c">
				<input type="file" name="edu_pic" style=" width:200px" value="$_A.approve_result.edu_pic"/>{if $_A.approve_result.edu_pic_url!=""}<a href="./{ $_A.approve_result.edu_pic_url}" target="_blank" title="有图片"><img src="{ $_A.tpldir  }/images/ico_yes.gif" border="0"  /></a>{/if}
			</div>
		</div>
		
		
		
		<div class="module_border" >
		<div class="l">验 证 码 ：</div>
			<div class="c">
				<input name="valicode" type="text" size="11" maxlength="4" />
			
				<img src="/?plugins&q=imgcode" id="valicode" alt="点击刷新" onClick="this.src='/?plugins&q=imgcode&t=' + Math.random();" align="absmiddle" style="cursor:pointer" />
			</div>
		</div>
		
		
		<div class="module_submit"><input type="submit" value="确认提交" class="submit_button" /></div>
			</form>
		</div>
		{/if}
		</div>
			</div>
		<div style="float:right; width:67%; text-align:left">
		<div class="module_add">
		<div class="module_title"><strong>学历认证列表</strong><span style="float:right">
			状态：<select name="status" id="status">
			<option value="" {if $magic.request.status==""}selected="selected"{/if}>不限</option>
			<option value="0" {if $magic.request.status=="0"}selected="selected"{/if}>未审核</option>
			<option value="1" {if $magic.request.status=="1"}selected="selected"{/if}>审核通过</option>
			<option value="2" {if $magic.request.status=="2"}selected="selected"{/if}>审核不通过</option>
		  </select>用户名：<input type="text" name="username" id="username" size="7" value="{$magic.request.username|urldecode}"/>    <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()"></span></div>
		</div>
	<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
		<tr >
			<td width="" class="main_td">用户</td>
			<td width="" class="main_td">真实姓名</td>
			<td width="*" class="main_td">学校</td>
			<td width="*" class="main_td">专业</td>
			<td width="*" class="main_td">学历</td>
			<td width="*" class="main_td">入学时间</td>
			<td width="*" class="main_td">毕业时间</td>
			<td width="*" class="main_td">学历图片</td>
			<td width="*" class="main_td">ID5审核</td>
			<td width="*" class="main_td">状态</td>
			<td width="*" class="main_td">操作</td>
		</tr>
		{ list module="approve" function="GetEduList" var="loop" username=request  realname=request status="request"  card_id=request  epage=20 }
		{foreach from="$loop.list" item="item"}
		<tr {if $key%2==1} class="tr2"{/if}>
			<td class="main_td1" align="center">{$item.username}</td>
			<td class="main_td1" align="center">{$item.realname}</td>
			<td class="main_td1" align="center">{$item.graduate}</td>
			<td class="main_td1" align="center">{$item.speciality}</td>
			<td class="main_td1" align="center">{$item.degree}</td>
			<td class="main_td1" align="center">{$item.enrol_date}</td>
			<td class="main_td1" align="center">{$item.graduate_date}</td>
			<td class="main_td1" align="center">{if $item.edu_pic_url==""}-{else}<a href="./{ $item.edu_pic_url}" target="_blank" title="有图片"><img src="{ $_A.tpldir  }/images/ico_yes.gif" border="0"  /></a>{/if}</td>
			<td class="main_td1" align="center"><a href="javascript:void(0)" onclick='tipsWindown("ID5学历认证审核","url:get?{$_A.query_url_all}&id5={$item.user_id}",500,230,"true","","false","text");'>{$item.id5_status|linkages:"approve_cardid_status"}</a></td>
			<td class="main_td1" align="center">{$item.status|linkages:"approve_status"}</td>
			<td class="main_td1" align="center"><a href="javascript:void(0)" onclick='tipsWindown("学历人工审核","url:get?{$_A.query_url_all}&examine={$item.user_id}",500,230,"true","","false","text");'/>审核</a>/<a href="{$_A.query_url_all}&user_id={$item.user_id}&page={$magic.request.page}">修改</a></td>
		</tr>
		{/foreach}
		<tr>
				<td colspan="15" class="action">
				<div class="floatl">
				<script>
				  var url = '{$_A.query_url_all}';
					{literal}
					function sousuo(){
						var username = $("#username").val();
						var status = $("#status").val();
						location.href=url+"&username="+username+"&status="+status;
					}
				  </script>
				  {/literal}
				</div>
				<div class="floatr">
				</div>
				</td>
			</tr>
			
		<tr align="center">
			<td colspan="14" align="center"><div align="center">{$loop.pages|showpage}</div></td>
		</tr>
		{/list}
		
	</table>
	<!--菜单列表 结束-->
	</div>
	</div>
	
		{/if}
{/if}
