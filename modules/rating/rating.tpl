
{if $_A.query_type == "educations"}

{if $magic.request.examine!=""}

<div  style="height:205px; overflow:hidden">
	<form name="form1" method="post" action="{$_A.query_url_all}&examine={$magic.request.examine}" >
	<div class="module_border_ajax">
		<div class="l">审核:</div>
		<div class="c">
		<input type="radio" name="status" value="1"/>初审通过 <input type="radio" name="status" value="2"  checked="checked"/>初审不通过 </div>
	</div>
	
	<div class="module_border_ajax" >
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="verify_remark" cols="45" rows="7"></textarea>
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">验证码:</div>
		<div class="c">
			<input name="valicode" type="text" size="11" maxlength="4"  tabindex="3" onClick="$('#valicode1').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
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

{else}
<div class="module_add">
	<div class="module_title"><strong>学历资料</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
	<div style="border:1px solid #CCCCCC; ">
	
	<form action="{$_A.query_url_all}" method="post">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="id" value="{$_A.rating_result.id}" />修改学历 （<a href="{$_A.query_url_all}">添加</a>）{else}添加学历{/if}</strong></div>
	
	<div class="module_border">
		<div class="c">
			用户名：{if $magic.request.edit!=""}<input type="hidden" name="username" value="{$_A.rating_result.username}" />{$_A.rating_result.username}{else}<input type="text" name="username" />{/if}
		</div>
	</div>
	
	
	{if $magic.request.edit!=""}
	<div class="module_border">
		<div class="c">
			状&nbsp;&nbsp;态：{$_A.rating_result.status|linkages:"rating_approve_status"}
		</div>
	</div>
	{/if}
	
	<div class="module_border">
		<div class="c">
			学&nbsp;&nbsp;历 ：{linkages type="value" nid="rating_education" name="degree" value="$_A.rating_result.degree"}
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="c">
			入学年份：{digit name="in_year" start="2012" end="1970"  value="$_A.rating_result.in_year" }
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			学&nbsp;&nbsp;校 ：<input type="text" name="name" value="{$_A.rating_result.name}"/>
		</div>
	</div>
	
	<div class="module_border">
		<div class="c">
			专&nbsp;&nbsp;业 ：<input type="text" name="professional" value="{$_A.rating_result.professional}"/>
		</div>
	</div>

	<div class="module_border_ajax" >
		<div class="c">
			验证码：<input name="valicode" type="text" size="11" maxlength="4"  onClick="$('#valicode').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
		
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
	<div class="module_title"><strong>学历列表</strong><span style="float:right">
		用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>   <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()"></span></div>
	</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">用户</td>
		<td width="*" class="main_td">学校</td>
		<td width="*" class="main_td">学历</td>
		<td width="*" class="main_td">专业</td>
		<td width="*" class="main_td">入学时间</td>
		<td width="*" class="main_td">状态</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="rating" function="GetEducationsList" var="loop" username=request epage="20"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.username}</td>
		<td class="main_td1" align="center">{$item.name}</td>
		<td class="main_td1" align="center">{$item.degree|linkages:"rating_education"}</td>
		<td class="main_td1" align="center">{$item.professional}</td>
		<td class="main_td1" align="center">{$item.in_year}</td>
		<td class="main_td1" align="center">{$item.status|linkages:"rating_approve_status"}</td>
		<td class="main_td1" align="center"><a href="javascript:void(0)" onclick='tipsWindown("学历审核","url:get?{$_A.query_url_all}&examine={$item.id}",500,230,"true","","false","text");'/>审核</a>/<a href="{$_A.query_url_all}&edit={$item.id}">修改</a>/<a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url_all}&del={$item.id}'">删除</a></td>
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
			location.href=url+"&username="+username;
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


{elseif $_A.query_type == "job"}

{if $magic.request.examine!=""}

<div  style="height:205px; overflow:hidden">
	<form name="form1" method="post" action="{$_A.query_url_all}&examine={$magic.request.examine}" >
	<div class="module_border_ajax">
		<div class="l">工作经历审核:</div>
		<div class="c">
		<input type="radio" name="status" value="1"/>初审通过 <input type="radio" name="status" value="2"  checked="checked"/>初审不通过 </div>
	</div>
	
	<div class="module_border_ajax" >
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="verify_remark" cols="45" rows="7"></textarea>
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">验证码:</div>
		<div class="c">
			<input name="valicode" type="text" size="11" maxlength="4"  tabindex="3" onClick="$('#valicode1').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
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

{else}
<div class="module_add">
	<div class="module_title"><strong>工作经历</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
	<div style="border:1px solid #CCCCCC; ">
	
	<form action="{$_A.query_url_all}" method="post">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="id" value="{$_A.rating_result.id}" />修改工作经历 （<a href="{$_A.query_url_all}">添加</a>）{else}添加工作经历{/if}</strong></div>
	
	<div class="module_border">
		<div class="c">
			用 户 名 ：{if $magic.request.edit!=""}<input type="hidden" name="username" value="{$_A.rating_result.username}" />{$_A.rating_result.username}{else}<input type="text" name="username" />{/if}
		</div>
	</div>
	
	
	{if $magic.request.edit!=""}
	<div class="module_border">
		<div class="c">
			状&nbsp;&nbsp;态：{$_A.rating_result.status|linkages:"rating_approve_status"}
		</div>
	</div>
	{/if}
	
	
	<div class="module_border">
		<div class="c">
			入职年份：{digit name="in_year" start="2012" end="1970"  value="$_A.rating_result.in_year" }
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			公司名称：<input type="text" name="name" value="{$_A.rating_result.name}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			部&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;门 ：<input type="text" name="department" value="{$_A.rating_result.department}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			职&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;位 ：<input type="text" name="office" value="{$_A.rating_result.office}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			证 明 人 ：<input type="text" name="prover" value="{$_A.rating_result.prover}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			证明电话：<input type="text" name="prover_tel" value="{$_A.rating_result.prover_tel}"/>
		</div>
	</div>

	<div class="module_border_ajax" >
		<div class="c">
			验 证 码 ：<input name="valicode" type="text" size="11" maxlength="4"  onClick="$('#valicode').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
		
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
	<div class="module_title"><strong>学历列表</strong><span style="float:right">
				用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>   <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()"></span></div>
	</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">用户</td>
		<td width="*" class="main_td">公司</td>
		<td width="*" class="main_td">部门</td>
		<td width="*" class="main_td">职位</td>
		<td width="*" class="main_td">入职时间</td>
		<td width="*" class="main_td">证明人</td>
		<td width="*" class="main_td">证明人电话</td>
		<td width="*" class="main_td">状态</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="rating" function="GetJobList" var="loop" username=request epage="20"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.username}</td>
		<td class="main_td1" align="center">{$item.name}</td>
		<td class="main_td1" align="center">{$item.department}</td>
		<td class="main_td1" align="center">{$item.office}</td>
		<td class="main_td1" align="center">{$item.in_year}</td>
		<td class="main_td1" align="center">{$item.status|linkages:"rating_approve_status"}</td>
		<td class="main_td1" align="center">{$item.prover}</td>
		<td class="main_td1" align="center">{$item.prover_tel}</td>
		<td class="main_td1" align="center"><a href="javascript:void(0)" onclick='tipsWindown("工作经历审核","url:get?{$_A.query_url_all}&examine={$item.id}",500,230,"true","","false","text");'/>审核</a>/<a href="{$_A.query_url_all}&edit={$item.id}">修改</a>/<a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url_all}&del={$item.id}'">删除</a></td>
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
			location.href=url+"&username="+username;
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



<!--审核记录列表 开始-->
{elseif $_A.query_type == "examine"}
<div class="module_add">
<div class="module_title"><strong>审核记录列表</strong><span style="float:right">
	{$MsgInfo.users_name_username}：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>    <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()"></span></div>
</div> 
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">id</td>
		<td width="*" class="main_td">审核人</td>
		<td width="*" class="main_td">模块</td>
		<td width="*" class="main_td">类型</td>
		<td width="*" class="main_td">文章</td>
		<th width="" class="main_td">结果</th>
		<td width="*" class="main_td">审核备注</td>
		<td width="*" class="main_td">审核时间</td>
	</tr>
	{ list module="users" function="GetExamineList" var="loop" username=request  epage="20" page="request"}
		{foreach from=$loop.list item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.username|default:-}</td>
		<td class="main_td1" align="center">{$item.code}</td>
		<td class="main_td1" align="center" >{$item.type}</td>
		<td class="main_td1" align="center" >{$item.article_id}</td>
		<td class="main_td1" align="center" >{if $item.result==1}<font color="#006600">{$MsgInfo.users_name_success}</font>{else}<font color="#FF0000">{$MsgInfo.users_name_false}</font>{/if}(result={$item.result})</td>
		<td class="main_td1" align="center" >{$item.remark}</td>
		<td class="main_td1" align="center" >{$item.addtime|date_format:"Y-m-d H:i:s"}</td>
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
			var email = $("#email").val();
			location.href=url+"&username="+username+"&email="+email;
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
	{ /list}
</table>
<!--审核记录列表 结束-->




{elseif $_A.query_type == "house"}

{if $magic.request.examine!=""}

<div  style="height:205px; overflow:hidden">
	<form name="form1" method="post" action="{$_A.query_url_all}&examine={$magic.request.examine}" >
	<div class="module_border_ajax">
		<div class="l">房产资料审核:</div>
		<div class="c">
		<input type="radio" name="status" value="1"/>初审通过 <input type="radio" name="status" value="2"  checked="checked"/>初审不通过 </div>
	</div>
	
	<div class="module_border_ajax" >
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="verify_remark" cols="45" rows="7"></textarea>
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">验证码:</div>
		<div class="c">
			<input name="valicode" type="text" size="11" maxlength="4"  tabindex="3" onClick="$('#valicode1').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
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

{else}
<div class="module_add">
	<div class="module_title"><strong>房产资料</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
	<div style="border:1px solid #CCCCCC; ">
	
	<form action="{$_A.query_url_all}" method="post">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="id" value="{$_A.rating_result.id}" />修改房产资料 （<a href="{$_A.query_url_all}">添加</a>）<input type="hidden" name="name" value="1" />{else}添加房产资料<input type="hidden" name="name" value="1" />{/if}</strong></div>
	
	<div class="module_border">
		<div class="c">
			用 户 名 ：{if $magic.request.edit!=""}<input type="hidden" name="username" value="{$_A.rating_result.username}" />{$_A.rating_result.username}{else}<input type="text" name="username" />{/if}
		</div>
	</div>
	
	
	{if $magic.request.edit!=""}
	<div class="module_border">
		<div class="c">
			状&nbsp;&nbsp;态：{$_A.rating_result.status|linkages:"rating_approve_status"}
		</div>
	</div>
	{/if}
	
	
	<div class="module_border">
		<div class="c">
			房产地址：<input type="text" name="address" value="{$_A.rating_result.address}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			建筑面积：<input type="text" name="areas" value="{$_A.rating_result.areas}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			建筑年份：<input type="text" name="in_year" value="{$_A.rating_result.in_year}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			供款状况：<input type="text" name="repay" value="{$_A.rating_result.repay}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			所有权人1：<input type="text" name="holder1" value="{$_A.rating_result.holder1}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			产权份额：<input type="text" name="right1" value="{$_A.rating_result.right1}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			所有权人：<input type="text" name="holder2" value="{$_A.rating_result.holder2}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			产权份额：<input type="text" name="right2" value="{$_A.rating_result.right2}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			贷款年限：<input type="text" name="load_year" value="{$_A.rating_result.load_year}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			每月供款：<input type="text" name="repay_month" value="{$_A.rating_result.repay_month}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			尚欠贷款余额：<input type="text" name="balance" value="{$_A.rating_result.balance}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			按揭银行：<input type="text" name="bank" value="{$_A.rating_result.bank}"/>
		</div>
	</div>

	<div class="module_border_ajax" >
		<div class="c">
			验 证 码 ：<input name="valicode" type="text" size="11" maxlength="4"  onClick="$('#valicode').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
		
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
	<div class="module_title"><strong>房产资料列表</strong><span style="float:right">
		{$MsgInfo.users_name_username}：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>    <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()"></span></div>
	</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">用户</td>
		<td width="*" class="main_td">房产地址</td>
		<td width="*" class="main_td">建筑面积</td>
		<td width="*" class="main_td">建筑年份</td>
		<td width="*" class="main_td">每月供款</td>
		<td width="*" class="main_td">供款状况</td>
		<td width="*" class="main_td">贷款年限</td>
		<td width="*" class="main_td">尚欠余额</td>
		<td width="*" class="main_td">状态</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="rating" function="GetHouseList" var="loop" username=request epage="20"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.username}</td>
		<td class="main_td1" align="center">{$item.address}</td>
		<td class="main_td1" align="center">{$item.areas}</td>
		<td class="main_td1" align="center">{$item.in_year}</td>
		<td class="main_td1" align="center">{$item.repay_month}</td>
		<td class="main_td1" align="center">{$item.repay}</td>
		<td class="main_td1" align="center">{$item.load_year}</td>
		<td class="main_td1" align="center">{$item.balance}</td>
		<td class="main_td1" align="center">{$item.status|linkages:"rating_approve_status"}</td>
		<td class="main_td1" align="center"><a href="javascript:void(0)" onclick='tipsWindown("房产资料审核","url:get?{$_A.query_url_all}&examine={$item.id}",500,230,"true","","false","text");'/>审核</a>/<a href="{$_A.query_url_all}&edit={$item.id}">修改</a>/<a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url_all}&del={$item.id}'">删除</a></td>
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
			var email = $("#email").val();
			location.href=url+"&username="+username+"&email="+email;
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

{elseif $_A.query_type == "company"}

{if $magic.request.examine!=""}

<div  style="height:205px; overflow:hidden">
	<form name="form1" method="post" action="{$_A.query_url_all}&examine={$magic.request.examine}" >
	<div class="module_border_ajax">
		<div class="l">工作单位审核:</div>
		<div class="c">
		<input type="radio" name="status" value="1"/>初审通过 <input type="radio" name="status" value="2"  checked="checked"/>初审不通过 </div>
	</div>
	
	<div class="module_border_ajax" >
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="verify_remark" cols="45" rows="7"></textarea>
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">验证码:</div>
		<div class="c">
			<input name="valicode" type="text" size="11" maxlength="4"  tabindex="3" onClick="$('#valicode1').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
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

{else}
<div class="module_add">
	<div class="module_title"><strong>工作单位资料</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
	<div style="border:1px solid #CCCCCC; ">
	
	<form action="{$_A.query_url_all}" method="post">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="id" value="{$_A.rating_result.id}" />修改工作单位资料 （<a href="{$_A.query_url_all}">添加</a>）{else}添加工作单位资料{/if}</strong></div>
	
	<div class="module_border">
		<div class="c">
			用 户 名 ：{if $magic.request.edit!=""}<input type="hidden" name="username" value="{$_A.rating_result.username}" />{$_A.rating_result.username}{else}<input type="text" name="username" />{/if}
		</div>
	</div>
	
	
	{if $magic.request.edit!=""}
	<div class="module_border">
		<div class="c">
			状&nbsp;&nbsp;态：{$_A.rating_result.status|linkages:"rating_approve_status"}
		</div>
	</div>
	{/if}
	
	
	<div class="module_border">
		<div class="c">
			公司名称：<input type="text" name="name" value="{$_A.rating_result.name}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			公司类型：<input type="text" name="type" value="{$_A.rating_result.type}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			所属行业：<input type="text" name="industry" value="{$_A.rating_result.industry}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			工作职位：<input type="text" name="office" value="{$_A.rating_result.office}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			所属级别：<input type="text" name="rank" value="{$_A.rating_result.rank}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			服务开始时间：<input type="text" name="worktime1" value="{$_A.rating_result.worktime1}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			服务结束时间：<input type="text" name="worktime2" value="{$_A.rating_result.worktime2}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			工作年限：{linkages name="workyear" nid="rating_workyear" type="value" value="$_A.rating_result.workyear"}
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			公司电话：<input type="text" name="tel" value="{$_A.rating_result.tel}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			公司地址：<input type="text" name="address" value="{$_A.rating_result.address}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			公司网址：<input type="text" name="weburl" value="{$_A.rating_result.weburl}"/>
		</div>
	</div>

	<div class="module_border_ajax" >
		<div class="c">
			验 证 码 ：<input name="valicode" type="text" size="11" maxlength="4"  onClick="$('#valicode').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
		
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
	<div class="module_title"><strong>工作单位资料列表</strong><span style="float:right">
				{$MsgInfo.users_name_username}：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>    <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()"></span></div>
	</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">用户</td>
		<td width="*" class="main_td">公司名称</td>
		<td width="*" class="main_td">公司类型</td>
		<td width="*" class="main_td">所属行业</td>
		<td width="*" class="main_td">工作职位</td>
		<td width="*" class="main_td">工作年限</td>
		<td width="*" class="main_td">状态</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="rating" function="GetCompanyList" var="loop" username=request epage="20"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.username}</td>
		<td class="main_td1" align="center">{$item.name}</td>
		<td class="main_td1" align="center">{$item.type}</td>
		<td class="main_td1" align="center">{$item.industry}</td>
		<td class="main_td1" align="center">{$item.office}</td>
		<td class="main_td1" align="center">{$item.workyear|linkages:"rating_workyear"}</td>
		<td class="main_td1" align="center">{$item.status|linkages:"rating_approve_status"}</td>
		<td class="main_td1" align="center"><a href="javascript:void(0)" onclick='tipsWindown("工作单位审核","url:get?{$_A.query_url_all}&examine={$item.id}",500,230,"true","","false","text");'/>审核</a>/<a href="{$_A.query_url_all}&edit={$item.id}">修改</a>/<a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url_all}&del={$item.id}'">删除</a></td>
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
			var email = $("#email").val();
			location.href=url+"&username="+username+"&email="+email;
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

{elseif $_A.query_type == "contact"}

{if $magic.request.examine!=""}

<div  style="height:205px; overflow:hidden">
	<form name="form1" method="post" action="{$_A.query_url_all}&examine={$magic.request.examine}" >
	<div class="module_border_ajax">
		<div class="l">联系方式审核:</div>
		<div class="c">
		<input type="radio" name="status" value="1"/>初审通过 <input type="radio" name="status" value="2"  checked="checked"/>初审不通过 </div>
	</div>
	
	<div class="module_border_ajax" >
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="verify_remark" cols="45" rows="7"></textarea>
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">验证码:</div>
		<div class="c">
			<input name="valicode" type="text" size="11" maxlength="4"  tabindex="3" onClick="$('#valicode1').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
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

{else}
<div class="module_add">
	<div class="module_title"><strong>联系方式</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
	<div style="border:1px solid #CCCCCC; ">
	
	<form action="{$_A.query_url_all}" method="post">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="id" value="{$_A.rating_result.id}" />修改联系方式（<a href="{$_A.query_url_all}">添加</a>）{else}添加联系方式{/if}</strong></div>
	
	<div class="module_border">
		<div class="c">
			用 户 名 ：{if $magic.request.edit!=""}<input type="hidden" name="username" value="{$_A.rating_result.username}" />{$_A.rating_result.username}{else}<input type="text" name="username" />{/if}
		</div>
	</div>
	
	
	{if $magic.request.edit!=""}
	<div class="module_border">
		<div class="c">
			状&nbsp;&nbsp;态：{$_A.rating_result.status|linkages:"rating_approve_status"}
		</div>
	</div>
	{/if}
	
	
	<div class="module_border">
		<div class="c">
			第二联系人姓名：<input type="text" name="linkman2" value="{$_A.rating_result.linkman2}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			第二联系人关系：{linkages name="relation2" nid="rating_relation" type="value" value="$_A.rating_result.relation2"}
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			第二联系人手机：<input type="text" name="phone2" value="{$_A.rating_result.phone2}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			第三联系人姓名：<input type="text" name="linkman3" value="{$_A.rating_result.linkman3}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			第三联系人关系：{linkages name="relation3" nid="rating_relation" type="value" value="$_A.rating_result.relation3"}
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			第三联系人手机：<input type="text" name="phone3" value="{$_A.rating_result.phone3}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			QQ：<input type="text" name="qq" value="{$_A.rating_result.qq}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			阿里旺旺：<input type="text" name="wangwang" value="{$_A.rating_result.wangwang}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			MSN：<input type="text" name="msn" value="{$_A.rating_result.msn}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			其他联系方式：<input type="text" name="other" value="{$_A.rating_result.other}"/>
		</div>
	</div>

	<div class="module_border_ajax" >
		<div class="c">
			验 证 码 ：<input name="valicode" type="text" size="11" maxlength="4"  onClick="$('#valicode').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
		
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
	<div class="module_title"><strong>联系方式列表</strong><span style="float:right">
				{$MsgInfo.users_name_username}：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>    <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()"></span></div>
	</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">用户</td>
		<td width="*" class="main_td">第二联系人</td>
		<td width="*" class="main_td">第二联系人手机</td>
		<td width="*" class="main_td">第三联系人</td>
		<td width="*" class="main_td">第三联系人手机</td>
		<td width="*" class="main_td">QQ</td>
		<td width="*" class="main_td">阿里旺旺</td>
		<td width="*" class="main_td">MSN</td>
		<td width="*" class="main_td">状态</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="rating" function="GetContactList" var="loop" username=request epage="20"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{ $item.id}</td>
		<td class="main_td1" align="center">{$item.username}</td>
		<td class="main_td1" align="center">{$item.linkman2}</td>
		<td class="main_td1" align="center">{$item.phone2}</td>
		<td class="main_td1" align="center">{$item.linkman3}</td>
		<td class="main_td1" align="center">{$item.phone3}</td>
		<td class="main_td1" align="center">{$item.qq}</td>
		<td class="main_td1" align="center">{$item.wangwang}</td>
		<td class="main_td1" align="center">{$item.msn}</td>
		<td class="main_td1" align="center">{$item.status|linkages:"rating_approve_status"}</td>
		<td class="main_td1" align="center"><a href="javascript:void(0)" onclick='tipsWindown("联系方式审核","url:get?{$_A.query_url_all}&examine={$item.id}",500,230,"true","","false","text");'/>审核</a>/<a href="{$_A.query_url_all}&edit={$item.id}">修改</a>/<a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url_all}&del={$item.id}'">删除</a></td>
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
			var email = $("#email").val();
			location.href=url+"&username="+username+"&email="+email;
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

{elseif $_A.query_type == "info"}

{if $magic.request.examine!=""}

<div  style="height:205px; overflow:hidden">
	<form name="form1" method="post" action="{$_A.query_url_all}&examine={$magic.request.examine}" >
	<div class="module_border_ajax">
		<div class="l">个人资料审核:</div>
		<div class="c">
		<input type="radio" name="status" value="1"/>初审通过 <input type="radio" name="status" value="2"  checked="checked"/>初审不通过 </div>
	</div>
	
	<div class="module_border_ajax" >
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="verify_remark" cols="45" rows="7"></textarea>
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">验证码:</div>
		<div class="c">
			<input name="valicode" type="text" size="11" maxlength="4"  tabindex="3" onClick="$('#valicode1').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
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

{else}
<div class="module_add">
	<div class="module_title"><strong>个人资料</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
	<div style="border:1px solid #CCCCCC; ">
	
	<form action="{$_A.query_url_all}" method="post">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="id" value="{$_A.rating_result.id}" />修改个人资料（<a href="{$_A.query_url_all}">添加</a>）{else}添加个人资料{/if}</strong></div>
	
	<div class="module_border">
		<div class="c">
			用 户 名 ：{if $magic.request.edit!=""}<input type="hidden" name="username" value="{$_A.rating_result.username}" />{$_A.rating_result.username}{else}<input type="text" name="username" />{/if}
		</div>
	</div>
	
	
	{if $magic.request.edit!=""}
	<div class="module_border">
		<div class="c">
			状&nbsp;&nbsp;态：{$_A.rating_result.status|linkages:"rating_approve_status"}
		</div>
	</div>
	{/if}
	
	
	<div class="module_border">
		<div class="c">
			性别：{linkages name="sex" nid="rating_sex" type="value" value="$_A.rating_result.sex"}
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			婚姻状况：{linkages name="marry" nid="rating_marry" type="value" value="$_A.rating_result.marry"}
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			有没有孩子：{linkages name="children" nid="rating_children" type="value" value="$_A.rating_result.children"}
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			每月收入：{linkages name="income" nid="rating_income" type="value" value="$_A.rating_result.income"}
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			目前身份：{linkages name="dignity" nid="rating_dignity" type="value" value="$_A.rating_result.dignity"}
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			户口所在地：<input type="text" name="qq" value="{$_A.rating_result.qq}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			是否购车：{linkages name="is_car" nid="rating_car" type="value" value="$_A.rating_result.is_car"}
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			现居住地址：<input type="text" name="address" value="{$_A.rating_result.address}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			手机号码：<input type="text" name="phone" value="{$_A.rating_result.phone}"/>
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			个人描述：<textarea cols="30" rows="5" name="remark"></textarea>
		</div>
	</div>

	<div class="module_border_ajax" >
		<div class="c">
			验 证 码 ：<input name="valicode" type="text" size="11" maxlength="4"  onClick="$('#valicode').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
		
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
	<div class="module_title"><strong>个人资料列表</strong><span style="float:right">
				{$MsgInfo.users_name_username}：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>    <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()"></span></div>
	</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">用户</td>
		<td width="*" class="main_td">性别</td>
		<td width="*" class="main_td">婚姻</td>
		<td width="*" class="main_td">是否有小孩</td>
		<td width="*" class="main_td">手机号码</td>
		<td width="*" class="main_td">居住地址</td>
		<td width="*" class="main_td">是否购车</td>
		<td width="*" class="main_td">身份</td>
		<td width="*" class="main_td">状态</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="rating" function="GetInfoList" var="loop" username=request epage="20"}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{$item.id}</td>
		<td class="main_td1" align="center">{$item.username}</td>
		<td class="main_td1" align="center">{$item.sex|linkages:"rating_sex"}</td>
		<td class="main_td1" align="center">{$item.marry|linkages:"rating_marry"}</td>
		<td class="main_td1" align="center">{$item.children|linkages:"rating_children"}</td>
		<td class="main_td1" align="center">{$item.phone|linkages:"rating_phone"}</td>
		<td class="main_td1" align="center">{$item.address|linkages:"rating_address"}</td>
		<td class="main_td1" align="center">{$item.is_car|linkages:"rating_car"}</td>
		<td class="main_td1" align="center">{$item.dignity|linkages:"rating_dignity"}</td>
		<td class="main_td1" align="center">{$item.status|linkages:"rating_approve_status"}</td>
		<td class="main_td1" align="center"><a href="javascript:void(0)" onclick='tipsWindown("个人资料审核","url:get?{$_A.query_url_all}&examine={$item.id}",500,230,"true","","false","text");'/>审核</a>/<a href="{$_A.query_url_all}&edit={$item.id}">修改</a>/<a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url_all}&del={$item.id}'">删除</a></td>
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
			var email = $("#email").val();
			location.href=url+"&username="+username+"&email="+email;
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

{elseif $_A.query_type == "assets"}

{if $magic.request.examine!=""}

<div  style="height:205px; overflow:hidden">
	<form name="form1" method="post" action="{$_A.query_url_all}&examine={$magic.request.examine}" >
	<div class="module_border_ajax">
		<div class="l">资产状况审核:</div>
		<div class="c">
		<input type="radio" name="status" value="1"/>初审通过 <input type="radio" name="status" value="2"  checked="checked"/>初审不通过 </div>
	</div>
	
	<div class="module_border_ajax" >
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="verify_remark" cols="45" rows="7"></textarea>
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">验证码:</div>
		<div class="c">
			<input name="valicode" type="text" size="11" maxlength="4"  tabindex="3" onClick="$('#valicode1').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
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

{else}
<div class="module_add">
	<div class="module_title"><strong>资产状况</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
	<div style="border:1px solid #CCCCCC; ">
	
	<form action="{$_A.query_url_all}" method="post">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="id" value="{$_A.rating_result.id}" />修改资产状况（<a href="{$_A.query_url_all}">添加</a>）{else}添加资产状况{/if}</strong></div>
	
	<div class="module_border">
		<div class="c">
			用 户 名 ：{if $magic.request.edit!=""}<input type="hidden" name="username" value="{$_A.rating_result.username}" />{$_A.rating_result.username}{else}<input type="text" name="username" />{/if}
		</div>
	</div>
	
	
	{if $magic.request.edit!=""}
	<div class="module_border">
		<div class="c">
			状&nbsp;&nbsp;态：{$_A.rating_result.status|linkages:"rating_approve_status"}
		</div>
	</div>
	{/if}
	
	
	<div class="module_border">
		<div class="c">
			负债类型：{linkages name="assetstype" nid="rating_assetstype" type="value" value="$_A.rating_result.assetstype"}
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			负债名称：<input type="text" name="name" value="{$_A.rating_result.name}">
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			金额：<input type="text" name="account" value="{$_A.rating_result.account}">
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			其他说明：<textarea colspan="20" rowspan="5" name="other"></textarea>
		</div>
	</div>

	<div class="module_border_ajax" >
		<div class="c">
			验 证 码 ：<input name="valicode" type="text" size="11" maxlength="4"  onClick="$('#valicode').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
		
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
	<div class="module_title"><strong>资产状况列表</strong><span style="float:right">
				{$MsgInfo.users_name_username}：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>    <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()"></span></div>
	</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">用户</td>
		<td width="*" class="main_td">负债类型</td>
		<td width="*" class="main_td">负债名称</td>
		<td width="*" class="main_td">金额</td>
		<td width="*" class="main_td">其他说明</td>
		<td width="*" class="main_td">状态</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="rating" function="GetAssetsList" var="loop" username=request epage=20}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{$item.id}</td>
		<td class="main_td1" align="center">{$item.username}</td>
		<td class="main_td1" align="center">{$item.assetstype|linkages:"rating_assetstype"}</td>
		<td class="main_td1" align="center">{$item.name}</td>
		<td class="main_td1" align="center">{$item.account}</td>
		<td class="main_td1" align="center">{$item.other}</td>
		<td class="main_td1" align="center">{$item.status|linkages:"rating_approve_status"}</td>
		<td class="main_td1" align="center"><a href="javascript:void(0)" onclick='tipsWindown("资产状况审核","url:get?{$_A.query_url_all}&examine={$item.id}",500,230,"true","","false","text");'/>审核</a>/<a href="{$_A.query_url_all}&edit={$item.id}">修改</a>/<a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url_all}&del={$item.id}'">删除</a></td>
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
			var email = $("#email").val();
			location.href=url+"&username="+username+"&email="+email;
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

{elseif $_A.query_type == "finance"}

{if $magic.request.examine!=""}

<div  style="height:205px; overflow:hidden">
	<form name="form1" method="post" action="{$_A.query_url_all}&examine={$magic.request.examine}" >
	<div class="module_border_ajax">
		<div class="l">财务状况审核:</div>
		<div class="c">
		<input type="radio" name="status" value="1"/>初审通过 <input type="radio" name="status" value="2"  checked="checked"/>初审不通过 </div>
	</div>
	
	<div class="module_border_ajax" >
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="verify_remark" cols="45" rows="7"></textarea>
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">验证码:</div>
		<div class="c">
			<input name="valicode" type="text" size="11" maxlength="4"  tabindex="3" onClick="$('#valicode1').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
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

{else}
<div class="module_add">
	<div class="module_title"><strong>财务状况</strong></div>
	<div style="margin-top:10px;">
	<div style="float:left; width:30%;">
		
	<div style="border:1px solid #CCCCCC; ">
	
	<form action="{$_A.query_url_all}" method="post">
	<div class="module_title"><strong>{if $magic.request.edit!=""}<input type="hidden" name="id" value="{$_A.rating_result.id}" />修改财务状况（<a href="{$_A.query_url_all}">添加</a>）{else}添加财务状况{/if}</strong></div>
	
	<div class="module_border">
		<div class="c">
			用 户 名 ：{if $magic.request.edit!=""}<input type="hidden" name="username" value="{$_A.rating_result.username}" />{$_A.rating_result.username}{else}<input type="text" name="username" />{/if}
		</div>
	</div>
	
	
	{if $magic.request.edit!=""}
	<div class="module_border">
		<div class="c">
			状&nbsp;&nbsp;态：{$_A.rating_result.status|linkages:"rating_approve_status"}
		</div>
	</div>
	{/if}
	
	
	<div class="module_border">
		<div class="c">
			财务类型：{linkages name="type" nid="rating_finance" type="value" value="$_A.rating_result.type"}
		</div>
	</div>
	
	<div class="module_border">
		<div class="c">
			财务名称：<input type="text" name="name" value="{$_A.rating_result.name}">
		</div>
	</div>
	
	<div class="module_border">
		<div class="c">
			资金流向：{linkages name="use_type" nid="rating_use_type" type="value" value="$_A.rating_result.use_type"}
		</div>
	</div>
	<div class="module_border">
		<div class="c">
			金额：<input type="text" name="account" value="{$_A.rating_result.account}">
		</div>
	</div>
	
	<div class="module_border">
		<div class="c">
			其他说明：<textarea colspan="20" rowspan="5" name="other"></textarea>
		</div>
	</div>

	<div class="module_border_ajax" >
		<div class="c">
			验 证 码 ：<input name="valicode" type="text" size="11" maxlength="4"  onClick="$('#valicode').attr('src','/?plugins&q=imgcode&t=' + Math.random())"/>
		
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
	<div class="module_title"><strong>财务状况列表</strong><span style="float:right">
				{$MsgInfo.users_name_username}：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}"/>    <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="sousuo()"></span></div>
	</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	<tr >
		<td width="" class="main_td">ID</td>
		<td width="" class="main_td">用户</td>
		<td width="*" class="main_td">财务类型</td>
		<td width="*" class="main_td">财务名称</td>
		<td width="*" class="main_td">资金流向</td>
		<td width="*" class="main_td">金额</td>
		<td width="*" class="main_td">其他说明</td>
		<td width="*" class="main_td">状态</td>
		<td width="*" class="main_td">操作</td>
	</tr>
	{ list module="rating" function="GetFinanceList" var="loop" username=request epage=20}
	{foreach from="$loop.list" item="item"}
	<tr {if $key%2==1} class="tr2"{/if}>
		<td class="main_td1" align="center">{$item.id}</td>
		<td class="main_td1" align="center">{$item.username}</td>
		<td class="main_td1" align="center">{$item.type|linkages:"rating_finance"}</td>
		<td class="main_td1" align="center">{$item.name}</td>
		<td class="main_td1" align="center">{$item.use_type|linkages:"rating_use_type"}</td>
		<td class="main_td1" align="center">{$item.account}</td>
		<td class="main_td1" align="center">{$item.other}</td>
		<td class="main_td1" align="center">{$item.status|linkages:"rating_approve_status"}</td>
		<td class="main_td1" align="center"><a href="javascript:void(0)" onclick='tipsWindown("资产状况审核","url:get?{$_A.query_url_all}&examine={$item.id}",500,230,"true","","false","text");'/>审核</a>/<a href="{$_A.query_url_all}&edit={$item.id}">修改</a>/<a href="#" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='{$_A.query_url_all}&del={$item.id}'">删除</a></td>
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
			var email = $("#email").val();
			location.href=url+"&username="+username+"&email="+email;
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