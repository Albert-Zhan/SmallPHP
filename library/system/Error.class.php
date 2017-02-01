<?php
/**
 * Framework:Z-PHP
 * license:MIT
 * Author:Albert Zhan(http://www.5lazy.cn)
 */
namespace system;
class Error{

    //存放错误配置
    private static $headr=null;

    //存放错误对象
    private static $whoops=null;

    /**
     * 抛出错误信息
     * @param $message 错误信息
     * @param int $lever 错误等级
     * @throws Exception
     */
    public static function Thrown($message,$lever=E_NOTICE){
        $exception=new \system\Exception($message,$lever,$lever);
        if(@APP_DEBUG){
            $log = new \system\Log();
            $message = '错误代码：' . $exception->getMessage() . '错误文件：' . $exception->getFile() . '错误行数：' .$exception->getLine();
            $log->Write($message);
        }
        throw $exception;
    }

    /**
     * 注册错误信息捕获
     */
   public static function Register(){
        if(self::$whoops===null){
            self::$whoops=new \Whoops\Run();
        }
        if(self::$headr===null){
            self::$headr=new \Whoops\Handler\PrettyPageHandler();
        }
        //设置错误标题
        self::$headr->setPageTitle('Z-PHP运行出现错误！');
        self::$whoops->pushHandler(self::$headr);
        self::$whoops->register();
    }

}