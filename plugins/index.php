<?php
if (!defined('ROOT_PATH'))  die('不能访问');//防止直接访问
$q = !isset($_REQUEST['q'])?"":$_REQUEST['q'];
$file = ROOT_PATH."/plugins/html/".$q.".inc.php";
/**
 * 尼玛 帝友，好搓的代码，好搓的代码 怎么好意思 说出去呀
 * 在php5.4中实现 session_register
 */
if(!function_exists('session_register')){
    function session_register(){
        $args = func_get_args();
        foreach ($args as $key){
            $_SESSION[$key]=$GLOBALS[$key];
        }
    }
}
if(!function_exists('session_is_registered'))
{
    function session_is_registered($key){
        return isset($_SESSION[$key]);
    }
}
if(!function_exists('session_unregister')){
    function session_unregister($key){
        unset($_SESSION[$key]);
    }
}

if (file_exists($file)){
    include_once ($file);exit;
}