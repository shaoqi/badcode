<div class="module_add" >
<form method="post" action="">
	{articles module="borrow" plugins="roam" function="GetRoamOne" borrow_nid='$_A.borrow_result.borrow_nid' var="roam_var"}
	<div class="module_title"><strong>基本借款信息</strong></div>
	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="r">
		<a href="{$_A.admin_url}&q=code/users/info_view&user_id={$_A.borrow_result.user_id}">	{$_A.borrow_result.username}</a>
		</div>
		<div class="s">回购期限：</div>
		<div class="c" style="width:220px">
		{if $magic.request.first_edit!=""}<select name="borrow_period">
	{foreach from=$_A.borrow_type_result.period_result key=key item=item}<option value='{$item.value}' {if $item.value==$_A.borrow_result.borrow_period} selected=""{/if}>{$item.name}</option>{/foreach}
	</select> {else}	{$_A.borrow_result.borrow_period}个月{/if}
		</div>
		<div style="float:left;padding:4px 5px 0 0px;">状态：
		{if $magic.request.first_edit!=""}
        <input type="submit" value="确认修改"  class="submit_button"/> 
        {else}
        	{if $_A.borrow_result.borrow_status_nid=="full"}满标审核<input type="button"  src="{$_A.tpldir}/images/button.gif" align="absmiddle" value="满标审核" class="submit_button" onclick='location.href="{$_A.query_url}/full&p=verify&borrow_nid={$_A.borrow_result.borrow_nid}"'/>
            {elseif $_A.borrow_result.borrow_status_nid=="first" }
             <input type="button"  src="{$_A.tpldir}/images/button.gif" align="absmiddle" value="借款初核" class="submit_button" onclick='tipsWindown("借款初核","url:get?{$_A.query_url}/first&check={$_A.borrow_result.borrow_nid}",500,230,"true","","false","text");'/>
             {else}
             {$_A.borrow_result.borrow_status_nid|linkages:"borrow_status"}
             {/if}
        {/if}     
             </div>
	</div>	
	
	
	<div class="module_border">
		<div class="l">标题：</div>
		<div class="r">
		{if $magic.request.first_edit!=""}<input type="text" name="name" value="{$_A.borrow_result.name}" /> {else}	{$_A.borrow_result.name}{/if}
		</div>
		<div class="s">贷款号：：</div>
		<div class="c" style="width:220px">
		{$_A.borrow_result.borrow_nid}
		</div>
		<div style="float:left;padding:4px 5px 0 0px;">借款类型：{$_A.borrow_result.type_title} </div>
	</div>
	
	<div class="module_border">
		<div class="l">借款总金额：</div>
		<div class="r">
		{$_A.borrow_result.account}
		</div>
		<div class="s">年利率：</div>
		<div class="c" style="width:220px">
        	{if $magic.request.first_edit!=""}<input type="text" name="borrow_apr" size="5" value="{$_A.borrow_result.borrow_apr}" /> {else}	{$_A.borrow_result.borrow_apr} {/if}%
			</div>
		<div style="float:left;padding:4px 5px 0 0px;">还款方式：{$_A.borrow_result.style_title}<input type="hidden" value="end" name="borrow_style" /></div>
	</div>
	
	<div class="module_border">
		<div class="l">最小流转单位：</div>
		<div class="r">
        	{$roam_var.account_min}   
		</div>
		<div class="s">总流转份数：</div>
		<div class="c" style="width:220px">
			{$roam_var.portion_total}份
		</div>
		<div style="float:left;padding:4px 5px 0 0px;">添加时间：
			{$_A.borrow_result.addtime|date_format}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">担保机构：</div>
		<div class="r">
		{if $magic.request.first_edit!=""}<input size="20" type="text" name="voucher" value="{$roam_var.voucher}" /> {else}{$roam_var.voucher}     {/if}
		</div>
		<div class="s">反担保方式：</div>
		<div class="c" style="width:220px">
		{if $magic.request.first_edit!=""}<input size="20" type="text" name="vouch_style" value="{$roam_var.vouch_style}" /> {else}{$roam_var.vouch_style}     {/if}
		</div>
	
	</div>
	
	<div class="module_border">
		<div class="l">点击次数：</div>
		<div class="r">
				{$_A.borrow_result.hits|default:0}次
		</div>
		<div class="s">评论次数：</div>
		<div class="c" style="width:220px">
			{$_A.borrow_result.comment_count}次
		</div>
		<div style="float:left;padding:4px 5px 0 0px;">添加IP：{$_A.borrow_result.addip}</div>
        	
	</div>


	{if $_A.borrow_result.status>=1}
	<div class="module_title"><strong>审核情况：</strong></div>
	<table width="100%" >
  <tr >
    <td width="" class="main_td">审核类型 </td>
    <td width="" class="main_td">审核结果 </td>
    <td width="" class="main_td">审核时间 </td>
    <td width="" class="main_td">审核人员 </td>
    <td width="" class="main_td">审核备注 </td>
    <td width="" class="main_td">管理备注 </td>
  </tr>
  {loop module="borrow" plugins="loan" function="GetVerifyList" limit="all" borrow_nid='$_A.borrow_result.borrow_nid'}
  <tr >
    <td>{$var.type_name} </td>
    <td>{$var.status_name} </td>
    <td>{$var.addtime|date_format} </td>
    <td>{$var.username|default:系统} </td>
    <td>{$var.remark} </td>
    <td>{$var.contents} </td>
  </tr>
  {/loop}
  </table>
	
	
	
	<div class="module_title"><strong>投标详情：</strong></div>
  <table width="100%">
  <tr >
    <td>总流转份数：{$roam_var.portion_total}</td>
    <td >已流转份数：{$roam_var.portion_yes}</td>
    <td >待流转份数：{$roam_var.portion_wait}</td>
  </tr>
  <tr >
    <td >已投标金额：{$_A.borrow_result.borrow_account_yes}</td>
    <td colspan="2" >待投标的金额：{$_A.borrow_result.borrow_account_wait}</td>
  </tr>
  </table>
   <table width="100%">
  <tr >
    <td width="" class="main_td" >ID </td>
    <td width="" class="main_td" >投资人 </td>
    <td width="" class="main_td" >认购份数 </td>
    <td width="" class="main_td" >投资金额 </td>
    <td width="" class="main_td" >投资时间 </td>
    <td width="" class="main_td" >投资理由 </td>
  </tr>
	{ loop module="borrow" function="GetTenderList" plugins="Tender" limit="all" borrow_nid='$_A.borrow_result.borrow_nid' var="item"}
	<tr  {if $key%2==1} class="tr2"{/if}>
		<td>{ $item.id}<input type="hidden" name="id[]" value="{ $item.id}" /></td>
		<td class="main_td1" align="center"><a href="javascript:void(0)" onclick='tipsWindown("用户详细信息查看","url:get?{$_A.admin_url}&q=module/users/view&user_id={$item.user_id}",500,230,"true","","true","text");'>	{$item.username}</a></td>
		<td>{$item.account/$roam_var.account_min}份</td>
		<td>{$item.account}元</td>
		<td>{$item.addtime|date_format}</td>
		<td>{$item.contents}</td>
	</tr>
	{ /loop}
  </table>
	
	{/if}
	
		{if $_A.borrow_result.status>1}
  <div class="module_title"><strong>还款详情：</strong></div>
  <table width="100%">
  <tr >
    <td colspan="2">借款总额：￥{$_A.borrow_result.account}</td>
    <td colspan="2">应还总额：￥{$_A.borrow_result.repay_account_all}</td>
    <td colspan="2">应还利息：￥{$_A.borrow_result.repay_account_interest}</td>
   </tr>
  <tr >
    <td colspan="2">已还总额：<font style="color:#009900">￥{$_A.borrow_result.repay_account_yes}</font></td>
    <td colspan="2">已还本金：￥{$_A.borrow_result.repay_account_capital_yes}</td>
    <td colspan="2">已还利息：￥{$_A.borrow_result.repay_account_interest_yes}</td>
  </tr>
 
  <tr >
    <td colspan="2">未还总额：<font style="color:#FF0000">￥{$_A.borrow_result.repay_account_wait}</font></td>
    <td colspan="2">未还本金：￥{$_A.borrow_result.repay_account_capital_wait}</td>
    <td colspan="2">未还利息：￥{$_A.borrow_result.repay_account_interest_wait}</td>
  </tr>
 
  <tr >
    <td valign="center" class="main_td">期数 </td>
    <td valign="center" class="main_td">应还金额（本金+利息） </td>
    <td valign="center" class="main_td">应还时间 </td>
    <td valign="center" class="main_td">实还时间 </td>
    <td valign="center" class="main_td">实还本金 </td>
    <td valign="center" class="main_td">实还利息 </td>
    <td valign="center" class="main_td">逾期天数 </td>
    <td valign="center" class="main_td">状态 </td>
    <td valign="center" class="main_td">网站是否垫付 </td>
    <td valign="center" class="main_td">垫付时间 </td>
    <td valign="center" class="main_td">垫付金额 </td>
  </tr>
  {loop module="borrow" plugins="loan" function="GetRepayList" limit="all" borrow_nid='$_A.borrow_result.borrow_nid' var="item"}
  <tr >
    <td>{$item.repay_period}</td>
    <td>{$item.repay_account}</td>
    <td>{$item.repay_time|date_format:"Y-m-d"}</td>
    <td>{$item.repay_yestime|date_format:"Y-m-d"|default:'-'}</td>
    <td>{$item.repay_capital_yes}</td>
    <td>{$item.repay_interest_yes}</td>
    <td>{$item.late_days}</td>
    <td>{$item.repay_type_name}</td>
    <td>{if $item.repay_web==1}是{else}否{/if}</td>
	<td >{$item.repay_web_time|date_format:"Y-m-d"}</td>
	<td >￥{if $item.repay_web==1}{$item.repay_account}{else}0.00{/if}</td>

  </tr>
  {/loop}
  </table>
  
  	{/if}
  

	
	<div class="module_title"><strong>材料：</strong></div>
	<div class="module_border" >
    {if $magic.request.first_edit!=""}
	<span id="share_upload">上传图片</span>
    {/if}
<script type="text/javascript" src="/plugins/dyswfupload/swfupload.js"></script>
<script type="text/javascript" src="/plugins/dyswfupload/dyswfupload.js"></script>
<script type="text/javascript" src="{$tempdir}/js/jquery.dragsort-0.5.1.min.js"></script>
   <ul class="upload_pic" id="photo_items">
   
   {foreach from=$roam_var.upfiles_pic item="_item"}
	 <li  class="fin">
     <div class="pic"> <img class="img" src="{$_item.fileurl|litpic:100,100}"></div><div class="box"> {if $magic.request.first_edit==""}{$_item.contents}{else}<input type="hidden" name="upfiles_id[]" value="{$_item.id}"><span class="move ico-move"></span><input type="text" name="upfiles_content[]" value="{$_item.contents}" size="5" />  <span class="close ico-close-btn" >删除</span>{/if}</div></li>
	 {/foreach}</ul>
     
			 {literal}
             <script>
             $(".ico-close-btn").click(function(){
                $(this).parent().remove();
             })
var swfu;
SWFUpload.onload = function () {
	var settings = {
		flash_url : "/plugins/dyswfupload/swfupload_fp9.swf",
		flash9_url : "/plugins/dyswfupload/swfupload_fp9.swf",
		upload_url: "/?user&q=plugins&ac=dyswfupload",
		file_size_limit : "5 MB",
		file_types : "*.jpg;*.gif",
		file_types_description : "All Files",
		file_upload_limit : 30,
		file_queue_limit : 0,
		custom_settings : {
			progressTarget : "photo_items"
		},
		debug: false,
		button_image_url : "",
		button_placeholder_id : "share_upload",
		button_text: '<span class="button" >+上传图片</div>',
		//button_text_style: ".aa{ font-size:22px; border-radius: 3px; }",
	//	button_image_url : "/plugins/dyswfupload/upload.gif",
		button_width: 155,
		button_height: 47,
	// 	button_text : '<span class="button"><img src=""></span>',
		button_text_style : '.button {   font-size: 22px;font-family: Microsoft YaHei,SimSun; }',
		button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
		button_cursor: SWFUpload.CURSOR.HAND,
		
		file_queue_error_handler : fileQueueError,
		file_dialog_complete_handler : fileDialogComplete,
		upload_start_handler : uploadStart,
		upload_progress_handler : uploadProgress,
		upload_error_handler : uploadError,
		upload_success_handler : uploadSuccess
		
	};

	swfu = new SWFUpload(settings);
	
}
             </script>
             {/literal}
	</div>	
	
	<div class="module_title"><strong>借款方概述：</strong></div>
	<div class="module_border" >
	
   {if $magic.request.first_edit!=""} <textarea id="borrow_contents" name="borrow_contents"  style="width:530px;height:200px;">{$_A.borrow_result.borrow_contents}</textarea>{else} {$_A.borrow_result.borrow_contents}{/if}

	</div>
		<div class="module_title"><strong>借款方资产情况：</strong></div>
	<div class="module_border" >
   {if $magic.request.first_edit!=""} <textarea id="borrow_account" name="borrow_account"  style="width:530px;height:200px;">{$roam_var.borrow_account}</textarea>{else}{$roam_var.borrow_account}{/if}
	</div>
		<div class="module_title"><strong>借款方资金用途：</strong></div>
	<div class="module_border" >
{if $magic.request.first_edit!=""} <textarea id="borrow_account_use" name="borrow_account_use"  style="width:530px;height:200px;">{$roam_var.borrow_account_use}</textarea>{else}{$roam_var.borrow_account_use}{/if}
	</div>
		<div class="module_title"><strong>风险控制措施：</strong></div>
	<div class="module_border" >
{if $magic.request.first_edit!=""} <textarea id="risk" name="risk"  style="width:530px;height:200px;">{$roam_var.risk}</textarea>{else}{$roam_var.risk}{/if}
	</div>
</div>
<input type="hidden" name="borrow_nid" value="{$magic.request.first_edit}" />
{/articles}

</form>