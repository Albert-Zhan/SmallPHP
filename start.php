<?php
namespace system;
//载入框架初始化文件
require_once __DIR__.'/init.php';
//执行自动加载
Loader::register();
//错误异常捕获
\system\Error::register();
//应用执行
\system\App::run();
