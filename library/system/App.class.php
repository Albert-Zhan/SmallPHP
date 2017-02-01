<?php
/**
 * Framework:Z-PHP
 * license:MIT
 * Author:Albert Zhan(http://www.5lazy.cn)
 */
namespace system;
class App{

    /**
     * 应用管理
     */
    public static function Run(){
        //开启session
        \system\Session::init();
        //设置页面字符集
        $charset=\system\Conf::Get('DEFAULT_CHARSET','UTF-8');
        header('Content-Type: text/html; charset='.$charset);
        //载入用户函数库
        if(file_exists(APP_PATH.'Common.php')){
            require_once APP_PATH.'Common.php';
        }
        global $FunctIons;
        $FunctIons=get_defined_functions();
        //载入扩展函数库
        $extfiles=\system\Conf::Get('EXTRA_FILE_LIST',[]);
        foreach($extfiles as $k=>$extfile){
            if(file_exists($extfile)){
                require_once $extfile;
            }
        }
        //开启路由管理
        \system\Url::Route();
    }

}