{if $_A.borrow_result.borrow_type=="roam"}
    {include file="borrow.view_roam.tpl" template_dir="modules/borrow"}
{else}
<div class="module_add" >
	
	<div class="module_title"><strong>基本借款信息</strong></div>
	<div class="module_border">
		<div class="l">用户名：</div>
		<div class="r">
		<a href="{$_A.admin_url}&q=code/users/info_view&user_id={$_A.borrow_result.user_id}">	{$_A.borrow_result.username}</a>
		</div>
		<div class="s">标题：</div>
		<div class="c" style="width:220px">
			{$_A.borrow_result.name}
		</div>
		<div style="float:left;padding:4px 5px 0 0px;">状态：
        	 {if $_A.borrow_result.borrow_status_nid=="first" }
             <input type="button"  src="{$_A.tpldir}/images/button.gif" align="absmiddle" value="借款初核" class="submit_button" onclick='tipsWindown("借款初核","url:get?{$_A.query_url}/first&check={$_A.borrow_result.borrow_nid}",500,300,"true","","false","text");'/>
        	{elseif $_A.borrow_result.borrow_full_status!=1 && ($_A.borrow_result.borrow_status_nid=="full" || $_A.borrow_result.type_part_status==1)}满标审核<input type="button"  src="{$_A.tpldir}/images/button.gif" align="absmiddle" value="满标审核" class="submit_button" onclick='location.href="{$_A.query_url}/full&p=verify&borrow_nid={$_A.borrow_result.borrow_nid}"'/>          
             {else}
             {$_A.borrow_result.borrow_status_nid|linkages:"borrow_status"}
             {/if}</div>
	</div>	
	
	
	<div class="module_border">
		<div class="l">贷款号：</div>
		<div class="r">
			{$_A.borrow_result.borrow_nid}
		</div>
		<!-- <div class="s">借款用途：</div>
		<div class="c" style="width:220px">
			{$_A.borrow_result.borrow_use|linkages:"borrow_use"}
		</div> -->
		<div class="s">评论次数：</div>
		<div class="c" style="width:220px">
			{$_A.borrow_result.comment_count}次
		</div>
		<div style="float:left;padding:4px 5px 0 0px;">有效时间：{$_A.borrow_result.borrow_valid_time}天 </div>
	</div>
	
	<div class="module_border">
		<div class="l">借款类型：</div>
		<div class="r">
			{$_A.borrow_result.type_title}
		</div>
		<div class="s">还款方式：</div>
		<div class="c" style="width:220px">
        {$_A.borrow_result.style_name}
			</div>
		<div style="float:left;padding:4px 5px 0 0px;">借贷总金额：￥{$_A.borrow_result.account}<input type="hidden" name="account" value="{$_A.borrow_result.account}" /> </div>
	</div>
	
	<div class="module_border">
		<div class="l">年利率：</div>
		<div class="r">
			{$_A.borrow_result.borrow_apr} %
		</div>
		<div class="s">借款期限：</div>
		<div class="c" style="width:220px">
			{$_A.borrow_result.borrow_period_name}
		</div>
		<div style="float:left;padding:4px 5px 0 0px;">是否部分借款：
			{$_A.borrow_result.borrow_part_status|linkages:"borrow_part_status"|default:-}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">最低投标金额：</div>
		<div class="r">
			{$_A.borrow_result.tender_account_min}
		</div>
		<div class="s">最多投标总额：</div>
		<div class="c" style="width:220px">
			{$_A.borrow_result.tender_account_max}
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
			{$_A.borrow_result.addtime|date_format}
		</div>
		<div class="s">添加IP：</div>
		<div class="c" style="width:220px">
			{$_A.borrow_result.addip}
		</div>
		<div style="float:left;padding:4px 5px 0 0px;"></div>
	</div>
	
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
    <td>{$var.type_name}</td>
    <td>{$var.status_name} </td>
    <td>{$var.addtime|date_format} </td>
    <td>{$var.username|default:系统} </td>
    <td>{$var.remark} </td>
    <td>{$var.contents} </td>
  </tr>
  {/loop}
  </table>
	
	{if $_A.borrow_result.status>0}
	<div class="module_title"><strong>借款状态</strong> <!--(<a href="{$_A.query_url}/tender&borrow_nid={$_A.borrow_result.borrow_nid}">查看投资信息</a>)--></div>
	
	
	<div class="module_border">
		<div class="l">已借到的金额：</div>
		<div class="r">
			<font color="#009900">￥{$_A.borrow_result.borrow_account_yes}</font>
		</div>
		<div class="s">未借到的金额：</div>
		<div class="c" style="width:220px">
			<font color="#FF0000">￥{$_A.borrow_result.borrow_account_wait}</font>
		</div>
	</div>
	

	{if $_A.borrow_result.status>=1}
	
	<div class="module_title"><strong>投标详情：</strong></div>
  <table width="100%">
  <tr >
    <td colspan="2" >已投标的金额：<font style="color:#009900">￥{$_A.borrow_result.borrow_account_yes}</font></td>
    <td colspan="2" >待投标的金额：<font style="color:#FF0000">￥{$_A.borrow_result.borrow_account_wait}</font></td>
    <td colspan="2" >投标次数：{$_A.borrow_result.tender_times}次</td>
  </tr>
  <tr >
    <td width="" class="main_td" >ID </td>
    <td width="" class="main_td" >投资人 </td>
    <td width="" class="main_td" >投资金额 </td>
    <td width="" class="main_td" >有效投资金额 </td>
    <td width="" class="main_td" >投资时间 </td>
    <td width="" class="main_td" >投资理由 </td>
  </tr>
	{ loop module="borrow" function="GetTenderList" plugins="Tender" limit="all" borrow_nid='$_A.borrow_result.borrow_nid' var="item"}
	<tr  {if $key%2==1} class="tr2"{/if}>
		<td>{ $item.id}<input type="hidden" name="id[]" value="{ $item.id}" /></td>
		<td class="main_td1" align="center"><a href="{$_A.admin_url}&q=code/users/info_view&user_id={$item.user_id}" title="查看">{$item.username}</a></td>
		<td>{$item.account_tender}元</td>
		<td>{$item.account}元</td>
		<td>{$item.addtime|date_format}</td>
		<td>{$item.contents}</td>
	</tr>
	{ /loop}
  </table>
	
	{/if}
	
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
  {if $_A.borrow_result.repay_advance_status==1}
  <tr >
    <td colspan="2">未还总额：0</td>
    <td colspan="2">未还本金：￥{$_A.borrow_result.repay_account_capital_wait}</td>
    <td colspan="2">损失利息：￥{$_A.borrow_result.repay_account_interest_lost}</td>
  </tr>
  {else}
  <tr >
    <td colspan="2">未还总额：<font style="color:#FF0000">￥{$_A.borrow_result.repay_account_wait}</font></td>
    <td colspan="2">未还本金：￥{$_A.borrow_result.repay_account_capital_wait}</td>
    <td colspan="2">未还利息：￥{$_A.borrow_result.repay_account_interest_wait}</td>
  </tr>
  {/if}
  <tr >
    <td colspan="2">已还正常还款费用：￥{$_A.borrow_result.repay_fee_normal}</td>
    <td colspan="2">已还提前还款费用：￥{$_A.borrow_result.repay_fee_advance}</td>
    <td colspan="2">已还逾期还款费用：￥{$_A.borrow_result.repay_fee_late}</td>
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
	<td >￥{$item.repay_web_account}</td>

  </tr>
  {/loop}
  </table>
  
  	{/if}
  

	
	
	<div class="module_title"><strong>借款详情</strong></div>
	<div class="module_border" >
		{$_A.borrow_result.borrow_contents}	
	</div>
	
	<!--div class="module_title"><strong>抵押物详情</strong></div>
	<div class="module_border" >
		<textarea id="diya_contents" name="diya_contents" rows="22" cols="200" style="width: 80%">{$_A.borrow_result.diya_contents}</textarea>
	</div-->
	
	{if $_A.borrow_result.vouch_status==1}
	<div class="module_title"><strong>担保奖励</strong></div>
	<div class="module_border">
		<div class="l">是否进行奖励：</div>
		<div class="c" style="width:220px">
			{if $_A.borrow_result.vouch_award_status==1}是{else}否{/if}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">是否固定所要担保人：</div>
		<div class="r">
			{ $_A.borrow_result.vouch_user_status|linkages:"borrow_vouch_user_status"}
		</div>
		<div class="s">固定担保人：</div>
		<div class="c" style="width:220px">
			{if $_A.borrow_result.vouch_user_status==0}-{else}{ $_A.borrow_result.vouch_users|deault:-}{/if}
		</div>
	</div>
	
	<div class="module_border">
		<div class="l">总担保金额：</div>
		<div class="r">
			￥{$_A.borrow_result.vouch_account}
		</div>
		<div class="s">已担保比例：</div>
		<div class="c" style="width:220px">
			{$_A.borrow_result.vouch_account_scale }%
		</div>
	</div>
	<div class="module_border">
		
		<div class="l">已担保金额：</div>
		<div class="r">
			￥{$_A.borrow_result.vouch_account_yes }
		</div>
		<div class="s">未担保金额：</div>
		<div class="c" style="width:220px">
			￥{$_A.borrow_result.vouch_account_wait }
		</div>
	</div>
	
	
	<div class="module_border">
		<div class="l">是否担保奖励：</div>
		<div class="r">
			{$_A.borrow_result.vouch_award_status|linkages:"borrow_vouch_award_status" }
		</div>
		<div class="s">担保奖励方式：</div>
		<div class="c" style="width:220px">
			{if $_A.borrow_result.vouch_award_status==2}
			 ￥{$_A.borrow_result.vouch_award_account}
			 {else}
			 {$_A.borrow_result.vouch_award_scale}%
			 {/if}
		</div>
	</div>

	
	<div class="module_title"><strong>担保列表</strong></div>
	<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
		<tr >
			<td width="" class="main_td">ID</td>
			<td width="*" class="main_td">担保人</td>
			<td width="*" class="main_td">担保金额</td>
			<td width="" class="main_td">有效金额</td>
			<td width="" class="main_td">担保时间</td>
			<td width="" class="main_td">担保理由</td>
		</tr>
		{ loop module="borrow" function="GetVouchList" limit="all" borrow_nid='$_A.borrow_result.borrow_nid' var="item"}
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td>{ $item.id}<input type="hidden" name="id[]" value="{ $item.id}" /></td>
			<td class="main_td1" align="center"><a href="javascript:void(0)" onclick='tipsWindown("用户详细信息查看","url:get?{$_A.admin_url}&q=module/users/view&user_id={$item.user_id}",500,230,"true","","true","text");'>{$item.username}</a></td>
			<td>{$item.account_vouch}元</td>
			<td>{$item.account}元</td>
			<td>{$item.addtime|date_format}</td>
			<td>{$item.contents}</td>
		</tr>
		{ /loop}
</table>
	{/if}
</div>
{/if}