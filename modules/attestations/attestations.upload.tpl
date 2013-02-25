{if $_P.uploadResponse!=""}
<uploadResponse>
	<message>ssada</message>
	<status>{$_P.status}</status>
	<proid>{$_P.proid}</proid>
	<albumid>{$_P.albumid}</albumid>
	<picid>{$_P.picid}</picid>
	{if $_P.fileurl}<filepath>{$_P.fileurl}</filepath>{/if}
</uploadResponse>
{else}
<parameter>
	<allowsExtend>
		<extend depict="All Image File(*.jpg,*.jpeg,*.gif)">*.jpg,*.gif,*.png,*.jpeg</extend>
	</allowsExtend>
	<language>
		<create>创建</create> 
  <notCreate>取消</notCreate> 
  <albumName>相册名</albumName> 
  <createTitle>创建新册</createTitle> 
  <categoryDesc>类型分类</categoryDesc> 
  <categoryPrompt>请选择分类</categoryPrompt> 
  <okbtn>继续</okbtn> 
  <cancelbtn>查看</cancelbtn> 
  <fileName>文件名</fileName> 
  <depict>描述(单击修改)</depict> 
  <size>文件大小</size> 
  <stat>上传进度</stat> 
  <aimAlbum>上传到:</aimAlbum> 
  <browser>浏览</browser> 
  <delete>删除</delete> 
  <upload>上传</upload> 
  <okTitle>上传完成</okTitle> 
  <okMsg>所有文件上传完成!</okMsg> 
  <uploadTitle>正在上传</uploadTitle> 
  <uploadMsg1>总共有</uploadMsg1> 
  <uploadMsg2>个文件等待上传,正在上传第</uploadMsg2> 
  <uploadMsg3>个文件</uploadMsg3> 
  <bigFile>文件过大</bigFile> 
  <uploaderror>上传失败,请选择类型或者上传jpg,gif的图片</uploaderror> 

	</language>
	<config>
		<userid>{$magic.request.user_id}</userid> 
		<hash>7408f59938e16974e9787c980ac99de3</hash> 
		<maxupload>20971520</maxupload> 
		{if $_U.member_url!=""}
		<uploadurl>{$_U.member_url|urlencode}%26q=plugins%26ac=swfupload%26code=attestations&user_id={$magic.request.user_id}</uploadurl> 
		<feedurl></feedurl> 
		<albumurl>{$_U.member_url|urlencode}%26q=code/attestations%26type_id=</albumurl> 
		{else}
		<uploadurl>/{$_A.admin_url|urlencode}%26q=plugins%26ac=swfupload%26code=attestations&user_id={$magic.request.user_id}</uploadurl> 
		<feedurl></feedurl> 
		<albumurl>/{$_A.admin_url|urlencode}%26q=code/attestations/list%26edit=</albumurl> 
		{/if}
		<categoryStat>0</categoryStat> 
		<categoryRequired>0</categoryRequired> 
	</config>
	<albums>
	{if $magic.request.type_id==""}
	<album id="0" >选择类型</album> 
	{/if}
	{loop module="attestations"  function="GetAttestationsTypeList" limit="all"  }
		{if $magic.request.type_id==$var.id}
		<album id="{$var.id}"  >{$var.name|gbk2utf8}</album> 
		{/if}
		 {/loop}
		{loop module="attestations"  function="GetAttestationsTypeList" limit="all"  }
	
	  <album id="{$var.id}"  >{$var.name|gbk2utf8}</album> 
	  {/loop}
	  </albums>
</parameter>
{/if}