{if $magic.request.flag_nid!=""}

<div  style="height:205px; overflow:hidden">
	<form name="form1" method="post" action="{$_A.query_url}/flag&type=update&borrow_nid={$magic.request.borrow_nid}" >
	<div class="module_border_ajax">
		<div class="l">选择属性:</div>
		<div class="c">
		{loop module="borrow" plugins="flag" function="GetFlagList" limit="all"}
		<div style="width:100%; overflow:hidden; margin-bottom:10px;"><img src="{$var.fileurl}" width="20" style="float:left" />&nbsp; <input name="flag[]" type="checkbox" value="{$var.id}"  /> {$var.name}</div>
		{/loop}</div>
	</div>
	
	<div class="module_submit_ajax" >
		<input type="hidden" name="borrow_nid" value="{ $magic.request.check}" />
		<input type="submit"  name="reset" class="submit_button" value="确认提交" />
	</div>
	
</form>
</div>
{elseif $magic.request.late_nid!=""}

<div  style=" overflow:hidden">
	<form name="form1" method="post" action="{$_A.query_url}/loan&p=lateday&borrow_nid={$magic.request.late_nid}" >
	<div class="module_border_ajax">
		<div class="l">延长的天数:</div>
		<div class="c">
        <input type="text" name="days" value="" size="10"/> 天
		<input type="hidden" name="borrow_nid" value="{ $magic.request.late_nid}" />
		<input type="submit"  name="reset" class="submit_button" value="确认提交" /><br />
        温馨提示：有效期 = 目前到期时间+延长天数
		</div>
	</div>

	
</form>
</div>

{elseif $magic.request.first_edit!=""}
{if $_A.borrow_result.borrow_type=="roam"}
    {include file="borrow.view_roam.tpl" template_dir="modules/borrow"}
{else}

<div class="module_add" >
	<form action="{$_A.query_url}/first&first_edit={$magic.request.first_edit}" enctype="multipart/form-data" method="post">
	<div class="module_title"><strong>基本借款信息</strong></div>
	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="r">
		<a href="{$_A.admin_url}&q=code/users/info_view&user_id={$_A.borrow_result.user_id}">	{$_A.borrow_result.username}</a>
		</div>
		<div class="s">标题：</div>
		<div class="c" style="width:220px">
			<input type="hidden" value="{$_A.borrow_result.borrow_nid}" name="borrow_nid" id="borrow_nid" />
			<input type="text" value="{$_A.borrow_result.name}" name="name" />
		</div>
		<div style="float:left;padding:4px 5px 0 0px;"><input type="submit" value="进行修改" /></div>
	</div>	
	
	
	<div class="module_border">
		<div class="l">贷款号：</div>
		<div class="r">
			{$_A.borrow_result.borrow_nid}
		</div>
		<!-- <div class="s">借款用途：</div>
		<div class="c" style="width:220px">
			{linkages name="borrow_use" nid="borrow_use" value="$_A.borrow_result.borrow_use" type="value"}
		</div> -->
		<div class="s">评论次数：</div>
		<div class="c" style="width:220px">
			{$_A.borrow_result.comment_count}次
		</div>
		<div style="float:left;padding:4px 5px 0 0px;">有效时间：<input type="text" name="borrow_valid_time" value="{$_A.borrow_result.borrow_valid_time}" size="5"/>天 </div>
	</div>
	
	<div class="module_border">
		<div class="l">借款类型：</div>
		<div class="r">
			{$_A.borrow_result.type_title}
		</div>
		<div class="s">还款方式：</div>
		<div class="c" style="width:220px">
        <select name='borrow_style' >{foreach from=$_A.borrow_type_result.style_result key=key item=item}<option value='{$item.nid}' {if $_A.borrow_result.borrow_style==$item.nid } selected=""{/if}>{$item.name}</option>{/foreach}</select>
			</div>
		<div style="float:left;padding:4px 5px 0 0px;">借贷总金额：￥{$_A.borrow_result.account}<input type="hidden" name="account" value="{$_A.borrow_result.account}" /> </div>
	</div>
	
	<div class="module_border">
		<div class="l">年利率：</div>
		<div class="r">
			<input type="text" name="borrow_apr" value="{$_A.borrow_result.borrow_apr}" size="5" /> % 
		</div>
		<div class="s">借款期限：</div>
		<div class="c" style="width:220px">
			<input type="text" name="borrow_period" value="{$_A.borrow_result.borrow_period}" size="5" />{if $_A.borrow_result.borrow_type=="day"}天{else}个月{/if}
		</div>
		<div style="float:left;padding:4px 5px 0 0px;">是否部分借款：
			{$_A.borrow_result.borrow_part_status|linkages:"borrow_part_status"|default:-}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">最低投标金额：</div>
		<div class="r">
			<select name='tender_account_min' >{foreach from=$_A.borrow_type_result.tender_account_min_result key=key item=item}<option value='{$item}' {if $_A.borrow_result.tender_account_min==$item } selected=""{/if}>{if $item==0}不限{else}{$item}元{/if}</option>{/foreach}</select>
		</div>
		<div class="s">最多投标总额：</div>
		<div class="c" style="width:220px">
			<select name='tender_account_max' >{foreach from=$_A.borrow_type_result.tender_account_max_result key=key item=item}<option value='{$item}'  {if $_A.borrow_result.tender_account_max==$item } selected=""{/if}>{if $item==0}不限{else}{$item}元{/if}</option>{/foreach}</select>
		</div>
		<div style="float:left;padding:4px 5px 0 0px;">点击次数：
			{$_A.borrow_result.hits|default:0}次
		</div>
	</div>
	
	<!-- <div class="module_border">
		<div class="l">奖励类型：</div>
		<div class="r">
			 {$_A.borrow_result.award_status|linkages:"borrow_award_status"}{if $_A.borrow_result.award_false==1}(审核失败也奖励){/if}
		</div>
		<div class="s">奖励方式：</div>
		<div class="c" style="width:220px">
			{if $_A.borrow_result.award_status==1}
				￥{$_A.borrow_result.award_account}
			{elseif $_A.borrow_result.award_status==2}
				{$_A.borrow_result.award_scale}%
                {else}
                不奖励
			{/if}
		</div>
		<div style="float:left;padding:4px 5px 0 0px;">评论次数：{$_A.borrow_result.comment_count}次</div>
	</div> -->
	
	<div class="module_border">
		<div class="l">添加时间：</div>
		<div class="r">
		</div>
		<div class="s">添加IP：</div>
		<div class="c" style="width:220px">
			{$_A.borrow_result.addip}
		</div>
		<div style="float:left;padding:4px 5px 0 0px;"></div>
	</div>

	
	<div class="module_title"><strong>借款详情</strong></div>
	<div class="module_border" >
    
     <textarea id="borrow_contents" name="borrow_contents"  style="width:830px;height:500px;visibility:hidden;">{$_A.borrow_result.borrow_contents}</textarea>		
	{literal}
<script src="/plugins/dyeditor/dyeditor.js" type="text/javascript"></script>
<script src="/plugins/dyeditor/lang/cn.js" type="text/javascript"></script><script>
var editor;
DyEditor.ready(function(D) {
editor = D.create('#borrow_contents',{filterMode : true,
htmlTags:{
       span : [],
div : [],
img : ['src', 'width', 'height', 'border', 'align',  '/'],
hr : ['/'],
br : ['/'],
'p,ol,ul,li' : ['align'],
'strong,b,sub,sup,em,i,u,strike' : []
},cssPath:"/plugins/dyeditor/themes/default/default.css"
				});})</script>
	{/literal}
    
	</div>
	</form>
</div>
{/if}
{elseif $magic.request.cancel_nid!=""}

<div  style="height:205px; overflow:hidden">
	<form name="form1" method="post" action="{$_A.query_url}/loan&p=cancel&borrow_nid={$magic.request.cancel_nid}" onsubmit="return confirm('你确定要撤除此标吗？');">

	<div class="module_border_ajax" >
		<div class="l">撤回理由:</div>
		<div class="c">
			<textarea name="remark" cols="45" rows="4">{ $_A.borrow_result.cancel_remark}</textarea>
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">管理备注:</div>
		<div class="c">
			<textarea name="contents" cols="45" rows="4">{ $_A.borrow_result.cancel_contents}</textarea>
		</div>
	</div>
	<div class="module_submit_ajax" >
		<input type="hidden" name="borrow_nid" value="{ $magic.request.cancel_nid}" />
		<input type="submit"  name="reset" class="submit_button" value="撤回此标" />
	</div>
	
</form>
</div>
{elseif $magic.request.check==""}
<ul class="nav3"> 
<li><a href="{$_A.query_url_all}&status_nid=first" {if  $magic.request.status_nid=="" || $magic.request.status_nid=="first"} id="c_so"{/if}>发标待审核</a></li> 
<li><a href="{$_A.query_url_all}&status_nid=loan" title="正在借款但未满的标"  {if $magic.request.status_nid=="loan"} id="c_so"{/if}>正在借款标</a></li> 
<li><a href="{$_A.query_url_all}&status_nid=false" title="指初审失败"  {if $magic.request.status_nid=="false"} id="c_so"{/if}>失败借款标</a></li> 
<li><a href="{$_A.query_url_all}&status_nid=late" title="指期限已过而未满标"  {if $magic.request.status_nid=="late"} id="c_so"{/if}>已过期</a></li> 
<li><a href="{$_A.query_url_all}&status_nid=over" title="指借款中或已过期进行了撤标"  {if $magic.request.status_nid=="over"} id="c_so"{/if}>流标</a></li> 
<li><a href="{$_A.query_url_all}&status_nid=cancel" title=指用户自己手动撤标"  {if $magic.request.status_nid=="cancel"} id="c_so"{/if}>用户撤标</a></li> 
</ul>
<form action="" method="post"> 
<div class="module_add">
	<div class="module_title"><strong>借款列表</strong>
	{if $magic.request.status_nid=='loan' ||  $magic.request.status_nid=='late'}<b style="float:right;color:red">已过期标请执行撤标，投标人资金才能返回。</b>{/if}
	</div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr >
			<td width="*" class="main_td">贷款号</td>
			<td width="*" class="main_td">用户名称</td>
			<td width="" class="main_td">借款标题</td>
			<td width="" class="main_td">借款金额</td>
			<td width="" class="main_td">利率</td>
			<td width="" class="main_td">借款期限</td>
			<td width="" class="main_td">借款类型</td>
			<td width="" class="main_td">还款方式</td>
            {if $magic.request.status_nid=="loan" || $magic.request.status_nid=="late" ||  $magic.request.status_nid=="over"}
			<td width="" class="main_td">投标次数</td>
			<td width="" class="main_td">投资进度</td>
            
            {if $magic.request.status_nid=="over"}
			<td width="" class="main_td">流标时间</td>
            {else}
			<td width="" class="main_td">到期时间</td>
            {/if}
            {elseif $magic.request.status_nid=="false"}
			<td width="" class="main_td">管理备注</td>
            {/if}
            {if $magic.request.status_nid=="cancel"}
			<td width="" class="main_td">撤标时间</td>
            {/if}
            <td width="" class="main_td">提交时间</td>
			<td width="" class="main_td">状态</td>
			<!--<td width="" class="main_td">属性</td>-->
			<td width="" class="main_td">查看</td>
		</tr>
		{ list  module="borrow" function="GetList" var="loop" borrow_name="request" query_type="first"  borrow_nid="request" username="request"  status_nid="request" borrow_type="request" }
		{foreach from="$loop.list" item="item"}
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td>{$item.borrow_nid}</td>
			<td class="main_td1" align="center"><a href="{$_A.admin_url}&q=code/users/info_view&user_id={$item.user_id}" title="查看">{$item.username}</a></td>			
			<td title="{$item.name}"><a href="{$_A.query_url}/view&borrow_nid={$item.borrow_nid}" title="查看">{$item.name|truncate:10}</a></td>			
			<td>{$item.account}元</td>
			<td>{$item.borrow_apr}</td>
			<td>{$item.borrow_period}{if $item.borrow_type=="day"}天{else}个月{/if}</td>
			
			{if $_A.query_type=="success"}
			<td width="" class="main_td">￥{$item.borrow_account_yes}</td>
			<td width="" class="main_td">{$item.tender_times}次</td>
			{/if}
			<td>{$item.type_name}</td>
			<td>{$item.style_title}</td>
             {if $magic.request.status_nid=="loan" || $magic.request.status_nid=="late" ||  $magic.request.status_nid=="over"}
			<td>{$item.tender_times}次</td>
			<td>{$item.borrow_account_scale}%</td>
            {if $magic.request.status_nid=="over"}
				<td>{$item.cancel_time|date_format:"Y/m/d H:i"}</td>
            {else}
				<td>{$item.borrow_end_time|date_format:"Y/m/d H:i"}</td>
            {/if}
		
            {elseif $magic.request.status_nid=="false"}
			<td width="" class="main_td">{$item.verify_contents}</td>
              {/if}
              {if $magic.request.status_nid=="cancel"}
			<td width="" class="main_td">{$item.cancel_time|date_format:"Y/m/d H:i"}</td>
            {/if}
            <td>{$item.addtime|date_format:"Y/m/d H:i"}</td>
			<td>{$item.borrow_status_nid|linkages:"borrow_status"}</td>
			<!--<td><a href="javascript:void(0)" onclick='tipsWindown("添加【{$item.name}】属性","url:get?{$_A.admin_url}&q=code/borrow/first&borrow_nid={$item.borrow_nid}",500,230,"true","","true","text");'>{$item.flag_name|default:添加属性}</a></td>-->
			<td title="审核并查看"><a href="{$_A.query_url}/view&borrow_nid={$item.borrow_nid}">查看</a> {if $magic.request.status_nid=="first" ||  $magic.request.status_nid==""}- <a href="{$_A.query_url_all}&first_edit={$item.borrow_nid}">修改</a>{/if} 
            {if ($magic.request.status_nid=="loan" && ($item.borrow_type!='roam' or ($item.borrow_type='roam' && $item.tender_times==0))) ||  $magic.request.status_nid=="late"  } - <a href="javascript:void(0)" onclick='tipsWindown("确认是否撤标","url:get?{$_A.query_url_all}&cancel_nid={$item.borrow_nid}",500,200,"true","","false","text");' title="撤标后，投标人资金将返还，此标变成流标"/>撤标</a>            
            {/if}
             {if  $magic.request.status_nid=="late"} - <a href="javascript:void(0)" onclick='tipsWindown("已过期借款延期时间","url:get?{$_A.query_url_all}&late_nid={$item.borrow_nid}",500,100,"true","","false","text");' />延期</a>{/if}
            </td>
			
		</tr>
		{ /foreach}
		<tr>
		<td colspan="14" class="action">
		<div class="floatl">
			
		</div>
		<div class="floatr">
			 标题：<input type="text" name="borrow_name" id="borrow_name" value="{$magic.request.borrow_name|urldecode}" size="8"/> 
             贷款号：<input type="text" name="borrow_nid" id="borrow_nid" value="{$magic.request.borrow_nid}" size="8"/> 
             用户名：<input type="text" name="username" id="username" value="{$magic.request.username|urldecode}" size="8"/>
		标种：<select name="borrow_type" id="borrow_type">
             <option value="">全部</option>
			 {loop module="borrow" plugins="Type" function="GetTypeList" limit="all" var="Tvar"}
             {if $Tvar.nid!="roam"}
			 <option value="{$Tvar.nid}" {if $Tvar.nid==$magic.request.borrow_type} selected=""{/if}>{$Tvar.name}</option>
             {/if}
		     {/loop}
			 </select>
             <input id="status_nid" value="{$magic.request.status_nid}" type="hidden" />
			<input type="button" value="搜索" class="submit" onclick="sousuo('{$_A.query_url}/first&status={$magic.request.status}')">
		</div>
		</td>
	</tr>
		<tr>
			<td colspan="14" class="page">
			{$loop.pages|showpage}
			</td>
		</tr>
		{/list}
	</form>	
</table>
</form>

<script>

var urls = '{$_A.query_url}/first';
{literal}
function sousuo(url){
	var sou = "";
	var username = $("#username").val();
	if (username!="" && username!=null){
		sou += "&username="+username;
	}
	var keywords = $("#keywords").val();
	if (keywords!="" && keywords!=null){
		sou += "&keywords="+keywords;
	}
	var borrow_name = $("#borrow_name").val();
	if (borrow_name!="" && borrow_name!=null){
		sou += "&borrow_name="+borrow_name;
	}
	var borrow_nid = $("#borrow_nid").val();
	if (borrow_nid!="" && borrow_nid!=null){
		sou += "&borrow_nid="+borrow_nid;
	}
	var borrow_type = $("#borrow_type").val();
	if (borrow_type!="" && borrow_type!=null){
		sou += "&borrow_type="+borrow_type;
	}
	var dotime1 = $("#dotime1").val();
	if (dotime1!="" && dotime1!=null){
		sou += "&dotime1="+dotime1;
	}
	var dotime2 = $("#dotime2").val();
	if (dotime2!="" && dotime2!=null){
		sou += "&dotime2="+dotime2;
	}
	var status_nid = $("#status_nid").val();
	if (status_nid!="" && status_nid!=null){
		sou += "&status_nid="+status_nid;
	}
	var is_vouch = $("#is_vouch").val();
	if (is_vouch!="" && is_vouch!=null){
		sou += "&is_vouch="+is_vouch;
	}
	
		location.href=url+sou;
	
}
</script>
{/literal}

{else}

<div  >
	<form name="form1" method="post" action="{$_A.query_url}/loan&p=verify&id={$magic.request.check}" onsubmit="return confirm('你确定要审核此借款吗？');">
	<div class="module_border_ajax">
		<div class="l">审核状态:</div>
		<div class="c">
		<input type="radio" name="status" value="1"/>初审通过 <input type="radio" name="status" value="0"  checked="checked"/>初审不通过 </div>
	</div>
	
	<div class="module_border_ajax" >
		<div class="l">审核备注:</div>
		<div class="c">
			<textarea name="verify_remark" cols="45" rows="4">{ $_A.borrow_result.verify_remark}</textarea>
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">管理备注:</div>
		<div class="c">
			<textarea name="verify_contents" cols="45" rows="4">{ $_A.borrow_result.verify_contents}</textarea>
		</div>
	</div>
	<div class="module_border_ajax" >
		<div class="l">验证码:</div>
		<div class="c">
			<input name="valicode" type="text" size="11" maxlength="4"  tabindex="3" />
		</div>
		<div class="c">
			<img src="/?plugins&q=imgcode" id="valicode" onClick="$('#valicode').attr('src','/?plugins&q=imgcode&t=' + Math.random())" alt="点击刷新"  align="absmiddle" style="cursor:pointer" />
		</div>
	</div>

	<div class="module_submit_ajax" >
		<input type="hidden" name="borrow_nid" value="{ $magic.request.check}" />
		<input type="submit"  name="reset" class="submit_button" value="审核此标" />
	</div>
	
</form>
</div>

{/if}