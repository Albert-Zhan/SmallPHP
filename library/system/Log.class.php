<?php
/**
 * Framework:Z-PHP
 * license:MIT
 * Author:Albert Zhan(http://www.5lazy.cn)
 */
namespace system;
class Log{

    //日志驱动
    private static $driver=['File'];

    //日志对象实例
    private static $log=null;

    /**
     * 初始化
     * @param string $driver 日志驱动
     * @param array $config 日志类型
     * @return mixed
     * @throws \Exception
     */
    public function __construct($driver='File',$config=[]){
        if(in_array($driver,self::$driver)){
            $class='\system\Log\driver\\'.$driver;
            if(self::$log===null){
                self::$log=new $class($config);
            }
        }
        else{
            \system\Error::Thrown($driver.'日志驱动不存在');
        }
    }

    /**
     * 写入日志
     * @param $mssage 写入日志信息
     * @param string $path 写入路径
     */
    public function Write($mssage,$path=''){
        return self::$log->Write($mssage,$path);
    }

    /**
     * 读取日志文件
     * @param string $filename 日志文件
     * @param string $path 日志路径
     * @return array|bools
     */
    public function Read($filename='',$path=''){
        return self::$log->Read($filename,$path);
    }

}