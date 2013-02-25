<?
@header("Expires: -1");
@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
@header("Pragma: no-cache");
@header("Content-type: application/xml; charset=utf-8");

?> <parameter>
	<allowsExtend>
		<extend depict="All Image File(*.jpg,*.jpeg,*.gif,*.png)">*.jpg,*.gif,*.png,*.jpeg</extend>
	</allowsExtend>
	<language>
		<create>创建</create> 
  <notCreate>取消</notCreate> 
  <albumName>相册名</albumName> 
  <createTitle>创建新相册</createTitle> 
  <categoryDesc>相册分类</categoryDesc> 
  <categoryPrompt>请选择相册分类</categoryPrompt> 
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
  <uploaderror>上传失败</uploaderror> 

	</language>
	<config>
		<userid>1</userid> 
		<hash>7408f59938e16974e9787c980ac99de3</hash> 
		<maxupload>20971520</maxupload> 
		<uploadurl>http%3A%2F%2Fdzx.com%2Fhome.php%3Fmod%3Dmisc%26ac%3Dswfupload</uploadurl> 
		<feedurl>http%3A%2F%2Fdzx.com%2Fhome.php%3Fmod%3Dmisc%26ac%3Dswfupload%26op%3Dfinish%26random%3DLP45BmiU%26albumid%3D</feedurl> 
		<albumurl>http%3A%2F%2Fdzx.com%2Fhome.php%3Fmod%3Dspace%26do%3Dalbum%26id%3D</albumurl> 
		<categoryStat>0</categoryStat> 
		<categoryRequired>0</categoryRequired> 
	</config>
	<albums>
	  <album id="-1">请选择相册</album> 
	  <album id="2">esf</album> 
	  <album id="1">123</album> 
	  <album id="add">+创建新相册</album> 
	  </albums>
</parameter>