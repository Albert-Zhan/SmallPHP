<?php
namespace system;
define('ZPHP_VERSION','1.0.0');
define('EXT','.php');
define('ZPHP_PATH',dirname(__FILE__));
define('LIB_PATH',ZPHP_PATH.'/library/');
define('SYSTEM_PATH',LIB_PATH.'system/');
define('IS_CLI',PHP_SAPI=='cli'?true:false);
define('IS_CGI',(0 === strpos(PHP_SAPI,'cgi') || false !== strpos(PHP_SAPI,'fcgi')) ? 1 : 0 );
define('IS_WIN',strpos(PHP_OS,'WIN')!==false);
$_dirname=dirname($_SERVER['SCRIPT_NAME']);
if($_dirname=='\\'){
    $_root=str_replace('\\','/',$_dirname);
}
else{
    $_root=$_dirname.'/';
}
define('__ROOT__',$_root);
//设置时区
date_default_timezone_set('Asia/Shanghai');
//载入系统函数库
require_once SYSTEM_PATH.'Common.php';
//载入自动加载类
require_once SYSTEM_PATH.'Loader.class.php';
//载入依赖管理类
require ZPHP_PATH.'/vendor/autoload.php';
