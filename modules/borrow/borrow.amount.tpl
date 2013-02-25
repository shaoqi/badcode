{if $magic.request.p!= "check"}
<ul class="nav3">
  <li><a href="{$_A.query_url_all}" {if $magic.request.p==""}id="c_so"{/if}>额度列表</a></li>
  <li><a href="{$_A.query_url_all}&p=apply" {if $magic.request.p=="apply" || $magic.request.p=="view"  }id="c_so"{/if}>额度申请审核</a></li>
  <li><a href="{$_A.query_url_all}&p=log" {if $magic.request.p=="log"}id="c_so"{/if}>额度记录</a></li>
  <li><a href="{$_A.query_url_all}&p=type" {if $magic.request.p=="type"}id="c_so"{/if}>额度类型</a></li>
  <li><a href="{$_A.query_url_all}&p=new" {if $magic.request.p=="new"}id="c_so"{/if}>增减额度</a></li>
</ul>
{/if}
{if $magic.request.p == ""}
<div class="module_add">
  <div class="module_title"><strong>用户额度列表</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
 
  <tr >
    <td width="" class="main_td">用户名</td>
    <td class="main_td">信用额度</td>
    <td class="main_td" >可用|冻结 </td>
    <td class="main_td">担保额度</td>
    <td class="main_td" >可用|冻结 </td>
    <td class="main_td">授信额度</td>
    <td class="main_td" >可用|冻结 </td>
    <td class="main_td">流转额度</td>
    <td class="main_td" >可用|冻结 </td>
    <td class="main_td" title="">操作</td>
  </tr>
  {list module="borrow" plugins="Amount" function="GetAmountList" var="loop" username=request user_id=request }
  {foreach from="$loop.list" item="item"}

  <tr {if $key%2==1} class="tr2"{/if}>
    <td class="main_td1" align="center">{$item.username}</td>
    <td class="main_td1" align="center">{$item.credit} </td>
    <td class="main_td1" align="center">{$item.credit_use}|{$item.credit_frost} </td>
    <td class="main_td1" align="center">{$item.vouch}</td>
    <td class="main_td1" align="center">{$item.vouch_use}|{$item.vouch_frost}</td>
    <td class="main_td1" align="center">{$item.pawn}</td>
    <td class="main_td1" align="center">{$item.pawn_use}|{$item.pawn_frost}</td>
    <td class="main_td1" align="center">{$item.vest}</td>
    <td class="main_td1" align="center">{$item.vest_use}|{$item.vest_frost}</td>
    <td class="main_td1" align="center"><a href="{$_A.query_url_all}&p=log&username={$item.username}">记录</a> | <a href="{$_A.query_url_all}&p=apply&username={$item.username}">申请</a> | <a href="{$_A.query_url_all}&p=new&user_id={$item.user_id}">添加</a> </td>
  </tr>
  {/foreach}
    <tr>
      <td colspan="27" class="action"><div class="floatl">
          <script>
			  var url = '{$_A.query_url_all}';
				{literal}
				function amount_sou(){
					var username = $("#username").val();
					location.href=url+"&username="+username;
				}
			  </script>
          {/literal} </div>
        <div class="floatr"> 用户名：
          <input type="text" name="username" id="username" value="{$magic.request.username|urldecode}" size="7"/>
          <input type="button" value="{$MsgInfo.users_name_sousuo}" / onclick="amount_sou()">
        </div></td>
    </tr>
  <tr align="center">
    <td colspan="27" align="center"><div align="center">{$loop.pages|showpage}</div></td>
  </tr>
  {/list}
</table>

{elseif  $magic.request.p=="view"}	

  <div class="module_add">
    <div class="module_title"><strong>申请额度详情</strong></div>
  </div>
{articles module="borrow" plugins="Amount" function="GetAmountApplyOne" id="$magic.request.id"  var="var"}	
			<div class="module_border_ajax" >
				<div class="l"> 申请人：</div>
				<div class="c">{$var.username}</div>
			</div>
            	<div class="module_border_ajax" >
				<div class="l"> 申请类型：</div>
				<div class="c">{$var.type_name}</div>
			</div>
			<div class="module_border_ajax" >
				<div class="l"> 申请状态：</div>
				<div class="c">{if $var.status==0}待审核{elseif $var.status==1}审核通过{else}审核不通过{/if}</div>
			</div>
			<div class="module_border_ajax" >
				<div class="l">申请额度：</div>
				<div class="c">{$var.amount_account}</div>
			</div>
			<div class="module_border_ajax" >
				<div class="l">通过额度：</div>
				<div class="c">{$var.account}</div>
			</div>
			<div class="module_border_ajax" >
				<div class="l">借款用途：</div>
				<div class="c">{$var.borrow_use|linkages:"borrow_use"}</div>
			</div>
			<div class="module_border_ajax" >
				<div class="l">详细说明：</div>
				<div class="c">{$var.content}</div>
			</div>	
			<div class="module_border_ajax" >
				<div class="l">有无其他借款：</div>
				<div class="c">{if $var.otherborrow==1}有{else}无{/if}</div>
			</div>	
			<div class="module_border_ajax" >
				<div class="l">审核时间：</div>
				<div class="c">{$var.verify_time|date_format}</div>
			</div>	
			<div class="module_border_ajax" >
				<div class="l">审核备注：</div>
				<div class="c">{$var.verify_remark}</div>
			</div>	
			<div class="module_border_ajax" >
				<div class="l">管理备注：</div>
				<div class="c">{$var.verify_contents}</div>
			</div>		
			
		{/articles}

    
    
    
{elseif $magic.request.p== "apply"}
<div>
  <div class="module_add">
    <div class="module_title"><strong>用户额度申请列表</strong></div>
  </div>
  <table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
    <tr >
      <td width="" class="main_td">ID</td>
      <td width="" class="main_td">用户</td>
      <td width="" class="main_td">类型</td>
      <td width="" class="main_td">种类</td>
      <td class="main_td">操作</td>
      <td width="" class="main_td">申请类型</td>
      <td class="main_td">申请金额</td>
      <td class="main_td">通过额度</td>
      <td class="main_td">申请备注</td>
      <td class="main_td">状态</td>
      <td class="main_td">申请时间</td>
      <td class="main_td">审核时间</td>
      <td class="main_td">操作</td>
    </tr>
    { list module="borrow" plugins="Amount" function="GetAmountApplyList" var="loop" user_id=request  username=request order="1" epage=8 status="request" amount_type="request" }
    {foreach from="$loop.list" item="item"}
    <tr {if $key%2==1} class="tr2"{/if}>
      <td class="main_td1" align="center">{$item.id}</td>
      <td class="main_td1" align="center"><a href="{$_A.query_url_all}&username={$item.username}">{$item.username}</a></td>
      <td class="main_td1" align="center">{if $item.amount_type=="credit"}信用借款额度{elseif $item.amount_type=="vouch"}担保借款额度{elseif $item.amount_type=="pawn"}授信额度{elseif $item.amount_type=="vest"}流转额度{/if}</td>
      <td class="main_td1" align="center">{if $item.amount_style=="once"}一次性额度{else}永久额度{/if}</td>
      <td class="main_td1" align="center">{if $item.oprate=="add"}增加{else}减少{/if}</td>
      <td class="main_td1" align="center">{if $item.type=="webapply"}网站申请{else}用户申请{/if}</td>
      <td class="main_td1" align="center">{$item.amount_account}</td>
      <td class="main_td1" align="center">{$item.account}</td>
      <td class="main_td1" align="center">{$item.content}</td>
      <td class="main_td1" align="center">{if $item.status==0}待审核{elseif $item.status==1}审核通过{else}审核不通过{/if}</td>
      <td class="main_td1" align="center">{$item.addtime|date_format:"Y/m/d"}</td>
      <td class="main_td1" align="center">{$item.verify_time|date_format:"Y/m/d H:i"|default:"-"}</td>
      <td class="main_td1" align="center">{if $item.status==0}<a href="javascript:void(0)" onclick='tipsWindown("审核用户申请额度","url:get?{$_A.query_url_all}&p=check&id={$item.id}",500,330,"true","","false","text");'><font color="red">审核</font></a>{else}<a href="{$_A.query_url_all}&p=view&id={$item.id}" />查看</a>{/if}<!-- /<a href="javascript:void(0)" onclick='tipsWindown(" ","url:get?{$_A.query_url_all}&amountview={$item.id}",500,500,"true","","false","text");'/>查看</a> --></td>
    </tr>
    {/foreach}
    <tr>
      <td colspan="15" class="action"><div class="floatl">
          <script>
			  var url = '{$_A.query_url_all}&p=apply';
				{literal}
				function amount_sou(){
					var username = $("#username").val();
					var status = $("#status").val();
					var amount_type = $("#amount_type").val();
					location.href=url+"&username="+username+"&amount_type="+amount_type+"&status="+status;
				}
			  </script>
          {/literal} </div>
        <div class="floatr"> 用户名：
          <input type="text" name="username" id="username" value="{$magic.request.username|urldecode}" size="7"/>
          状态：
          <select name="status" id="status">
          <option value="">全部</option>
          <option value="1" {if $magic.request.status=="1"} selected=""{/if}>通过</option>
          <option value="2" {if $magic.request.status=="2"} selected=""{/if}>不通过</option>
          <option value="0"{if $magic.request.status=="0"} selected=""{/if}>待审核</option>
          </select>
           额度类型：
        <select name="amount_type" id="amount_type">
        <option value="">全部</option>
        {loop module="borrow" function="GetAmountTypeList" limit="all" plugins="amount" var="item"}
        <option value="{$item.nid}" {if $item.nid==$magic.request.amount_type} selected=""{/if}>{$item.name}</option>
        {/loop}
        </select>
          <input type="button" value="{$MsgInfo.users_name_sousuo}"  onclick="amount_sou()">
        </div></td>
    </tr>
    <tr align="center">
      <td colspan="14" align="center"><div align="center">{$loop.pages|showpage}</div></td>
    </tr>
    {/list}
  </table>
  <!--菜单列表 结束-->
</div>
</div>

{elseif $magic.request.p== "log"}
<div class="module_add">
  <div class="module_title"><strong>额度记录列表</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
  <tr >
    <td width="">ID</td>
    <td width="">用户</td>
    <td width="">类型</td>
    <td width="">种类</td>
    <td>操作</td>
    <td>金额</td>
    <td>备注</td>
    <td>时间</td>
  </tr>
  { list module="borrow"  plugins="Amount" function="GetAmountLogList" var="loop" user_id=request  username=request  amount_type=request  epage=8 }
  {foreach from="$loop.list" item="item"}
  <tr {if $key%2==1} class="tr2"{/if}>
    <td class="main_td1" align="center">{ $item.id}</td>
    <td class="main_td1" align="center"><a href="{$_A.query_url_all}&username={$item.username}">{$item.username}</a></td>
    <td class="main_td1" align="center">{if $item.amount_type=="credit"}信用借款额度{elseif $item.amount_type=="vouch"}担保借款额度{elseif $item.amount_type=="pawn"}授信额度{elseif $item.amount_type=="vest"}流转额度额度{/if}</td>
      <td class="main_td1" align="center">{if $item.amount_style=="once"}一次性额度{else}永久额度{/if}</td>
    <td class="main_td1" align="center">{if $item.oprate=="add"}增加{elseif $item.oprate=="return"}返回{elseif $item.oprate=="frost"}冻结{else}减少{/if}</td>
    <td class="main_td1" align="center">{$item.account}</td>
    <td class="main_td1" align="center">{$item.remark}</td>
    <td class="main_td1" align="center">{$item.addtime|date_format}</td>
  </tr>
  {/foreach}
  <tr>
    <td colspan="15" class="action"><div class="floatl"> 增加的额度：{$loop.oprate_add|default:0} 减少的额度：{$loop.oprate_reduce|default:0}
        <script>
			  var url = '{$_A.query_url_all}&p=log';
				{literal}
				function amount_sou(){
					var username = $("#username").val();
					var amount_type = $("#amount_type").val();
					location.href=url+"&username="+username+"&amount_type="+amount_type;
				}
			  </script>
        {/literal} </div>
      <div class="floatr"> 用户名：
        <input type="text" name="username" id="username" value="{$magic.request.username|urldecode}" size="7"/>
        额度类型：
        <select name="amount_type" id="amount_type">
        <option value="">全部</option>
        {loop module="borrow" function="GetAmountTypeList" limit="all" plugins="amount" var="item"}
        <option value="{$item.nid}" {if $item.nid==$magic.request.amount_type} =""{/if}>{$item.name}</option>
        {/loop}
        </select>
        <input type="button" value="{$MsgInfo.users_name_sousuo}"  onclick="amount_sou()">
      </div></td>
  </tr>
  <tr align="center">
    <td colspan="14" align="center"><div align="center">{$loop.pages|showpage}</div></td>
  </tr>
  {/list}
</table>
<!--菜单列表 结束-->
</div>



{elseif $magic.request.p=="new"}
<div class="module_add">
  <div style="margin-top:10px;">
    <div >
      <div style="border:1px solid #CCCCCC; "> {if $magic.request.user_id==""}
        <form action="" method="post">
          <div class="module_title">添加额度:<strong>请先查找用户</strong>(将按顺序进行搜索)
            <input type="hidden" name="type" value="user_id" />
          </div>
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
          <div class="module_submit">
            <input type="submit" value="确认提交" class="submit_button" />
          </div>
        </form>
      </div>
      {else}
      <form action="" method="post" enctype="multipart/form-data" onsubmit="return check_new();">
        <div class="module_title"><strong>添加用户额度</strong>
          <input type="hidden" name="user_id" value="{$magic.request.user_id}" />
        </div>
        <div class="module_border">
          <div class="l">用 户 名 ：</div>
          <div class="c"> {$_A.users_result.username} </div>
        </div>
        <div class="module_border">
          <div class="l">额度类型：</div>
          <div class="c"><select name='amount_type'  id='amount_type'><option value='credit' >信用额度</option><option value='vouch' >担保借款额度</option><option value='pawn' >授信额度</option><option value='vest' >流转额度</option></select></div>
        </div>
        
        <div class="module_border">
          <div class="l">额度种类：</div>
          <div class="c"><select name='amount_style'  id='amount_style'><option value='forever' >永久额度</option>
          </select>
          
          </div>
        </div>
        <div class="module_border">
          <div class="l">操作：</div>
          <div class="c"> {input type="radio" value="add|增加,reduce|减少" name="oprate" checked="$_A.amount_apply_result.oprate"} </div>
        </div>
        <div class="module_border">
          <div class="l">金额额度：</div>
          <div class="c">
            <input type="text" name="amount_account"  id="amount_account" value="{$_A.amount_apply_result.amount_account}" onkeyup="value=value.replace(/[^0-9]/g,'')"/>
          </div>
        </div>
        <div class="module_border">
          <div class="l">添加信息：</div>
          <div class="c">
            <textarea name="content" cols="30" rows="4" id="content" >{$_A.amount_apply_result.content}</textarea>
          </div>
        </div>
        <div class="module_submit">
          <input type="submit" value="确认提交" class="submit_button" />
        </div>
      </form>
    </div>
    {literal}
    <script>
    function check_new(){
        if ($("#amount_account").val()==""){
            alert("申请金额不能为空");
            return false;
        }
        if ($("#content").val()==""){
            alert("添加信息不能为空");
            return false;
        }
        
        return true;
    }
    </script>
    {/literal}
    {/if} 
{elseif $magic.request.p== "check"}

<div style="overflow:hidden">
  <form name="form1" method="post" action="{$_A.query_url_all}&p=check&id={$magic.request.id}" onsubmit="return check_amount()">    
    <div class="module_border_ajax">
        <div class="l">审核:</div>
        <div class="c"><input type="radio" name="status" value="1" />通过 <input type="radio" name="status" value="2" checked="" />不通过</div>
    </div>
    <div class="module_border_ajax">
    <div class="l">操作:</div>
    <div class="c">{if $_A.amount_apply_result.amount_style=="once"}一次性额度{else}永久额度{/if} {if $_A.amount_apply_result.oprate=="add"}增加{else}减少{/if} </div>
    </div>
    {if $_A.amount_apply_result.borrow_use>0}
    <div class="module_border_ajax">
    <div class="l">借款用途:</div>
    <div class="c"> {$_A.amount_apply_result.borrow_use|linkages:"borrow_use"} </div>
    </div>
    {/if}
    <div class="module_border_ajax">
    <div class="l">详细说明:</div>
    <div class="c"> {$_A.amount_apply_result.content|html_format}  </div>
    </div>
     {if $_A.amount_apply_result.remark!=""}
    <div class="module_border_ajax">
    <div class="l">其它地方说明:</div>
    <div class="c"> {$_A.amount_apply_result.remark|html_format} </div>
    </div>
    {/if}
    <div class="module_border_ajax">
    <div class="l">申请额度:</div>
    <div class="c"> {$_A.amount_apply_result.amount_account} </div>
    </div>
    <div class="module_border_ajax">
    <div class="l">通过额度:</div>
    <div class="c">
      <input type="text" value="{$_A.amount_apply_result.amount_account}" name="account"  id="account" />
    </div>
    </div>
  
    <div class="module_border_ajax" >
      <div class="l">审核备注:</div>
      <div class="c">
        <textarea name="verify_remark"  id="verify_remark" cols="45" rows="7">{$_A.amount_apply_result.verify_remark}</textarea>
      </div>
    </div>
    
    
    <div class="module_border_ajax" >
      <div class="l">管理备注:</div>
      <div class="c">
        <textarea name="verify_contents"  id="verify_contents" cols="45" rows="7">{$_A.amount_apply_result.verify_contents}</textarea>
      </div>
    </div>
     <div class="module_border_ajax" >
      <div class="l"></div>
      <div class="c">
        <input type="hidden" name="user_id" value="{$_A.amount_apply_result.user_id}" />
      <input type="hidden" name="nid" value="{$_A.amount_apply_result.nid}" />
      <input type="hidden" name="id" value="{$magic.request.id}" />
      <input type="submit"  name="reset" class="submit_button" value="确认审核" />
      </div>
    </div>
  </form>
</div>
{literal}
    <script>
    function check_amount(){
        if ($("#account").val()==""){
            alert("通过金额不能为空");
            return false;
        }
        if ($("#verify_remark").val()==""){
            alert("审核备注不能为空");
            return false;
        }
        
        return true;
    }
    </script>
    {/literal}
{elseif $magic.request.p=="type_edit"}
<div class="module_add">
	<div class="module_title"><strong>修改还款方式</strong></div>
    <form action="{$_A.query_url_all}&p=type_edit" method="post">
    {articles module="borrow" function="GetAmountTypeOne" plugins="Amount" id="$magic.request.id" var="item"}
	
	<div class="module_border">
		<div class="l">借款额度类型名称：</div>
		<div class="c">
	       	{$item.name}
		</div>
	</div>
    
    
    <div class="module_border">
		<div class="l">标识名：</div>
		<div class="c">
			{$item.nid}<input type="hidden" name="id" value="{$item.id}" />
		</div>
	</div>
    
	<div class="module_border">
		<div class="l">名称：</div>
		<div class="c">
			<input type="text" name="title"  class="input_border" value="{$item.title}"   size="20" />
		</div>
	</div>
    
	<div class="module_border">
		<div class="l">描述：</div>
		<div class="c">
	       	<input type="text" name="description"  class="input_border" value="{$item.description}"   size="20" />
		</div>
	</div>
	<div class="module_border">
		<div class="l">状态</div>
		<div class="c">
			<input type="radio" name="status"  class="input_border" value="1" {if $item.status==1} checked=""{/if}  />开启 
			<input type="radio" name="status"  class="input_border"  value="0" {if $item.status==0} checked=""{/if} />关闭
		</div>
	</div>
	<!--
	<div class="module_border">
		<div class="l">是否启用一次性额度：</div>
		<div class="c">
			<input type="radio" name="once_status"  class="input_border" value="1" {if $item.once_status==1} checked=""{/if}  />开启 
			<input type="radio" name="once_status"  class="input_border"  value="0" {if $item.once_status==0} checked=""{/if} />关闭
	       (应用于用户中心)
    	</div>
	</div>
    
    -->
	<div class="module_border">
		<div class="l">备注：</div>
		<div class="c">
        <textarea name="remark" cols="50" rows="5">{$item.remark}</textarea>
		</div>
	</div>
    
    
	<div class="module_border">
		<div class="l"></div>
		<div class="c">
			<input type="submit"  name="submit" value="确认提交" />
		<input type="reset"  name="reset" value="重置表单" />
		</div>
	</div>
</form>
 </div>   
    {/articles}
{elseif $magic.request.p=="type"}
<div class="module_add">
	<div class="module_title"><strong>借款额度类型</strong></div>
</div>
<table  border="0"  cellspacing="1" bgcolor="#CCCCCC" width="100%">
	  <form action="" method="post">
		<tr >
			<td width="" class="main_td">ID</td>
			<td width="*" class="main_td">还款方式</td>
			<td width="*" class="main_td">标识符</td>
			<td width="*" class="main_td">标题</td>
			<td width="*" class="main_td" title="一旦关闭，整个网站将不可用">状态</td>
			<td width="" class="main_td">描述</td>
			<td width="" class="main_td">操作</td>
		</tr>
		{loop module="borrow" function="GetAmountTypeList" plugins="Amount" var="item" limit="all" }
		<tr  {if $key%2==1} class="tr2"{/if}>
			<td>{$item.id}<input type="hidden" name="id[]" value="{$item.id}" /></td>
			<td class="main_td1" align="center"><strong>{$item.name}</strong></td>
			<td class="main_td1" align="center">{$item.nid}</td>
			<td class="main_td1" align="center">{$item.title}</td>
			<td class="main_td1" align="center">{if $item.status==1}开启{else}关闭{/if}</td>
			<td class="main_td1" align="center">{$item.description}</td>
			<td class="main_td1" align="center"><a href="{$_A.query_url_all}&p=type_edit&id={$item.id}">修改</a></td>
		</tr>
		{ /loop}
	</form>	
</table>

{/if}