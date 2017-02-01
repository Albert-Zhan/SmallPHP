<?php
/**
 * Framework:Z-PHP
 * license:MIT
 * Author:Albert Zhan(http://www.5lazy.cn)
 */
namespace system;
//载入框架初始化文件
require_once __DIR__.'/init.php';
//执行自动加载
Loader::Register();
//错误异常捕获
\system\Error::Register();
//应用执行
\system\App::Run();
