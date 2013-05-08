<?php
/**
 * 生日提醒的计划任务
 * 远程提交采用curl函数 请确认你的机器中含有curl环境，以及pdo环境，连接数据库采用pdo形式
 *
 * 在这里我引入了一个第三方类库 详见http://requests.ryanmccue.info/
 *
 * 根据帝友数据表进行的编写（吐槽一下，狗血的数据结构冗余很大）
 * 假如你的 deayou_users_info 表中没有 birthyear 和 birthmonth 字段请运行如下语句
 * ALTER TABLE `deayou_users_info` ADD `birthyear` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `status` ,
 *   ADD `birthmonth` TINYINT( 2 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `birthyear` ;
 * 然后
 * 如果你的birthday字段中没有数据的话
 * ALTER TABLE `deayou_users_info` CHANGE `birthday` `birthday` TINYINT( 2 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT '生日';
 * 然后 为新加的字段创建索引
 * ALTER TABLE `deayou_users_info` ADD INDEX ( `birthmonth` , `birthday` ) ;
 *
 * 温馨提示 本文件的编码是UTF-8
 *
 * linux 计划任务
 * 00 09 * * * php -f /var/www/rongerong/task/birthday.php
 */
header("Content-type: text/html; charset=GBK");
define('ROOT_PATH',dirname(__FILE__));
$lock = ROOT_PATH.'/task.birthday.lock';
require(ROOT_PATH.'/../core/common.inc.php');
require ROOT_PATH.'/../libs/Requests.php';
$lock_date = is_file($lock)?date('Ymd',fileatime($lock)):0;
if(date('Ymd')==$lock_date){
    exit('ok');
}
$mysql = new PDO('mysql:host='.$db_config['host'].';dbname='.$db_config['name'].';charset='.$db_config['language'], $db_config['user'], $db_config['pwd']);
// 短信的内容 请根据需要自行编辑
define('MSG','尊敬的#xxx#，今天是您的生日。融易融祝您生日快乐、身体健康！感谢您对融易融的长期支持，我们将用更好的服务实现您财富长期稳健的增值！【融易融】');
// 短信触发的地址 请根据需要自行编辑
define('MSG_URL','*******');
// 检索条件
$where = '`birthmonth`='.date('m').' AND `birthday`='.date('d').' AND `realname_status`=1 AND `phone_status`=1 AND `realname`!=\'\' AND `phone`!=\'\'';
$data = $mysql->query('SELECT COUNT(`user_id`) as total FROM `deayou_users_info` WHERE '.$where);
$total = 0;
foreach($data as $value){
    $total = $value['total'];
}
unset($data);
if(empty($total)){
    touch($lock);
    exit('no birth');
}
$ps = intval($total/30);
$ps = $total%30?$ps+1:$ps;
Requests::register_autoloader();
for($i=1;$i<=$ps;$i++){
    $sql = 'SELECT `phone`,`realname` FROM `deayou_users_info` WHERE '.$where.' ORDER BY `id` ASC LIMIT '.(($i-1)*30).','.$i*30;
    $data = $mysql->query($sql);
    foreach($data as $value){
        if($value['phone'] && $value['realname']){
            $url = str_replace("#phone#",$value['phone'],MSG_URL);
            $content = str_replace('#xxx#',iconv('GBK', 'UTF-8', $value['realname']),MSG);
            $url = str_replace("#content#",$content,$url);
            $request = Requests::get($url);
            if($request->status_code==200){
                $res = request_back($request->body,$value['phone']);
                $logs[]='('.$res.',0,\'birthdaysms\',0,\''.iconv('UTF-8', 'GBK', $content).'\','.time().',\'127.0.0.1\')';
            }else{
                $res = 0;
                error_log('http请求失败'."\n",3, ROOT_PATH."/task.birthday.".date('Y-m-d').".log");
            }
        }
    }
    if(!empty($logs)){
        $sql = 'INSERT INTO `deayou_approve_smslog` (`status`,`user_id`,`type`,`code`,`contents`,`addtime`,`addip`) VALUES '.implode(',',$logs);
        $mysql->exec($sql);
    }
}
touch($lock);
function request_back($xml,$phone){
    $p = xml_parser_create();
    xml_parse_into_struct($p, $xml, $vals);
    xml_parser_free($p);
    // 发送状态写入日志
    if($vals['1']['value']!=0){
        error_log('生日祝福短信['.$phone."]发送失败接口状态为[".$vals['1']['value']."]\n",3, ROOT_PATH."/task.birthday.".date('Y-m-d').".log");
        return 0;
    }
    return 1;
}