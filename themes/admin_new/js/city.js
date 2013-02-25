


var Provinces = document.getElementById('provinces');
var Cities = document.getElementById('cities');

function Location(Province, City)
{
	this.Province	= Province;
	this.City		= City;
}

// Construct the location data
var arrLocation = new Array(35);
arrLocation[0]  = new Location('请选择', ''); 
arrLocation[1]	= new Location('北京', '东城区|西城区|崇文区|宣武区|朝阳区|海淀区|丰台区|石景山');
arrLocation[2]	= new Location('上海', '宝山|金山|南市|长宁|静安|青浦|崇明|卢湾|松江|奉贤|浦东|杨浦|虹口|普陀|闸北|黄浦|闵行|徐汇|嘉定|南汇');
arrLocation[3]	= new Location('天津', '和平|河北|河西|河东|南开|红桥|塘沽|汉沽|大港|东丽|西青|津南|北辰|武清|滨海');
arrLocation[4]	= new Location('重庆', '渝中|江北|沙坪坝|南岸|九龙坡|大渡口');
arrLocation[5]	= new Location('河北', '石家庄|邯郸|保定|张家口|承德|唐山|廊坊|沧州|衡水|邢台|秦皇岛');
arrLocation[6]	= new Location('山西', '太原|长治|大同|晋城|晋中|临汾|吕梁|朔州|忻州|阳泉|运城');
arrLocation[7]	= new Location('内蒙古', '呼和浩特|阿拉善|巴彦淖尔盟|包头|赤峰|鄂尔多斯|呼伦贝尔|呼盟扎兰屯|通辽|乌海|锡林郭勒盟|兴安盟|乌兰察布');
arrLocation[8]	= new Location('辽宁', '沈阳|鞍山|本溪|朝阳|大连|丹东|抚顺|阜新|葫芦岛|锦州|辽阳|盘锦|铁岭|营口');
arrLocation[9]	= new Location('吉林', '长春|吉林|白城|白山|辽源|四平|松原|通化|延边州');
arrLocation[10]	= new Location('黑龙江', '哈尔滨|大庆|大兴安岭|鹤岗|黑河|鸡西|佳木斯|牡丹江|七台河|齐齐哈尔|双鸭山|绥化|伊春');
arrLocation[11]	= new Location('江苏', '南京|苏州|常州|淮安|连云港|南通|宿迁|泰州|无锡|徐州|盐城|扬州|镇江');
arrLocation[12]	= new Location('浙江', '杭州|绍兴|湖州|嘉兴|金华|丽水|宁波|衢州|绍兴|台州|温州|舟山');
arrLocation[13]	= new Location('安徽', '合肥|蚌埠|芜湖|安庆|阜阳|黄山|滁州|亳州|巢湖|池州|淮北|淮南|六安|马鞍山|宿州|铜陵|宣城');
arrLocation[14]	= new Location('福建', '福州|厦门|宁德|莆田|泉州|漳州|龙岩|三明|南平');
arrLocation[15]	= new Location('江西', '南昌|抚州|赣州|吉安|景德镇|九江|萍乡|上饶|新余|宜春|鹰潭');
arrLocation[16]	= new Location('山东', '济南|荷泽|青岛|淄博|德州|烟台|潍坊|济宁|泰安|临沂|滨州|东营|威海|枣庄|日照|莱芜|聊城');
arrLocation[17]	= new Location('河南', '郑州|安阳|鹤壁|焦作|开封|洛阳|漯河|南阳|平顶山|濮阳|三门峡|商丘|新乡|信阳|许昌|周口|驻马店|济源');
arrLocation[18]	= new Location('湖北', '武汉|鄂州|黄冈|黄石|十堰|随州|咸宁|襄樊|孝感|宜昌|荆州|荆门|恩施|仙桃');
arrLocation[19]	= new Location('湖南', '长沙|常德|郴州|衡阳|怀化|娄底|邵阳|湘潭|益阳|永州|岳阳|张家界|株州|湘西州');
arrLocation[20]	= new Location('广东', '广州|汕尾|阳江|揭阳|茂名|江门|韶关|惠州|梅州|汕头|深圳|珠海|佛山|肇庆|湛江|中山|清远|云浮|潮州|东莞|河源');
arrLocation[21]	= new Location('广西', '南宁|柳州|玉林|北海|桂林|贵港|百色|崇左|防城港|河池|贺州|来宾|钦州|梧州');
arrLocation[22]	= new Location('海南', '海口|三亚|五指山|琼海|儋州|文昌|万宁|东方|通什');
arrLocation[23]	= new Location('四川', '成都|阿坝州|巴中|达州|德阳|广安|广元|乐山|凉山|泸州|眉山|绵阳|内江|南充|攀枝花|遂宁|雅安|宜宾|资阳|自贡|甘孜州');
arrLocation[24]	= new Location('贵州', '贵阳|遵义|安顺|黔南|黔东南|铜仁|毕节|六盘水|黔西南');
arrLocation[25]	= new Location('云南', '昆明|保山|楚雄|大理|德宏|迪庆|红河|丽江|临沧|怒江|曲靖|文山|西双版纳|玉溪|昭通|普洱');
arrLocation[26]	= new Location('西藏', '拉萨|日喀则|林芝|昌都|那曲|阿里|山南');
arrLocation[27]	= new Location('陕西', '西安|安康|宝鸡|汉中|商洛|铜川|渭南|咸阳|延安|榆林');
arrLocation[28]	= new Location('甘肃', '兰州|平凉|张掖|酒泉|嘉峪关|天水|白银|定西|甘南藏族自治州|金昌|临夏|陇南|庆阳|武威');
arrLocation[29]	= new Location('宁夏', '银川|固原|石嘴山|吴忠|中卫');
arrLocation[30]	= new Location('青海', '西宁|玉树|海东地区|海北州|黄南州|海南州|果洛州|海西州');
arrLocation[31]	= new Location('新疆', '乌鲁木齐|哈密|和田|阿勒泰|克拉玛依|石河子|昌吉|吐鲁番|阿克苏|喀什|塔城|克孜勒苏柯尔克孜|巴音郭楞|博尔塔拉');
arrLocation[32]	= new Location('香港', '自治区');
arrLocation[33]	= new Location('澳门', '自治区');
arrLocation[34]	= new Location('台湾', '台北|高雄|台南|台中|基隆|彰化|新竹|嘉义|台东|花莲|宜兰');
arrLocation[35]	= new Location('国外', '国外');

/*
 * 执行函数
 */
function selectedCity(cit)
{
	
	var selected = Provinces.options[Provinces.selectedIndex].value;
	if(isNaN(selected)){
		 selected = Provinces.selectedIndex;
	}
	Provinces.options[Provinces.selectedIndex].value = Provinces.options[Provinces.selectedIndex].text;
	
	if (Cities!="null"){
		var arrCities = (arrLocation[selected].City).split("|");
		Cities.length = arrCities.length;
		for(var i = 0; i < arrCities.length; i++) {
			if ( cit==arrCities[i]){
				Cities.options[i].selected = 'selected';
			}
			Cities.options[i].text	= arrCities[i];
			Cities.options[i].value	= arrCities[i];
		}
	}
}   

function ProvinceCity(val,cit){
	Provinces.length = arrLocation.length;
	for (var i = 0; i < arrLocation.length; i++) {
		Provinces.options[i].text = arrLocation[i].Province;
		if (arrLocation[i].Province==val){
			Provinces.options[i].selected = 'selected';
			Provinces.options[i].value = arrLocation[i].Province;
			var j=i;
		}else{
			Provinces.options[i].value = i;
		}
	}
	selectedCity(cit);
}

function Province(val){
	Provinces.length = arrLocation.length;
	for (var i = 0; i < arrLocation.length; i++) {
		Provinces.options[i].text = arrLocation[i].Province;
		Provinces.options[i].value = arrLocation[i].Province;
		if (arrLocation[i].Province==val){
			Provinces.options[i].selected = 'selected';
		}
	}
}