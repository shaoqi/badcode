<?php

if (!defined('DEAYOU_PATH'))  die('不能访问');
require_once("phpmailer/class.phpmailer.php");
class Mail {
public static $msg = '';
public static function Send ($subject,$body,$to) {
global $mysql,$_G;
$mail = new PHPMailer();
$mail->IsSMTP();
# 必填，SMTP服务器是否需要验证，true为需要，false为不需要
$mail->SMTPAuth   = $_G['system']['con_email_auth']?true:false;
# 必填，设置SMTP服务器
$mail->Host       = $_G['system']['con_email_host'];
# 必填，开通SMTP服务的邮箱；任意一个163邮箱均可
$mail->Username   = $_G['system']['con_email_url'];
# 必填， 以上邮箱对应的密码
$mail->Password   = $_G['system']['con_email_password'];
# 必填，发件人Email
$mail->From       = $_G['system']['con_email_from'];
# 必填，port
$mail->Port       = $_G['system']['con_email_port'];
# 必填，发件人昵称或姓名
$mail->FromName   = $_G['system']['con_email_from_name'];
# 必填，邮件标题（主题）
$mail->Subject    = $subject;
# 可选，纯文本形势下用户看到的内容
$mail->AltBody    = "";
# 自动换行的字数
$mail->WordWrap   = 50;
$mail->MsgHTML($body);
# 回复邮箱地址
$mail->AddReplyTo($mail->From,$mail->FromName);
# 添加附件,注意路径
# $mail->AddAttachment("/path/to/file.zip");
# $mail->AddAttachment("/path/to/image.jpg","new.jpg");
# 收件人地址。参数一：收信人的邮箱地址，可添加多个。参数二：收件人称呼
$mail->AddAddress(join(",",$to));
# 是否以HTML形式发送，如果不是，请删除此行
if(!$mail->Send()) {
self::$msg = $mail->ErrorInfo;
return false;
}
return true;
}
}

?>