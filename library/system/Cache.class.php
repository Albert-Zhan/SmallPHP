<?php
/**
 * Framework:Z-PHP
 * license:MIT
 * Author:Albert Zhan(http://www.5lazy.cn)
 */
namespace system;
class Cache{

    //缓存对象实例
    private static $handle=null;

    /**
     * 构造函数
     * @param string $config 配置信息
     */
    public function __construct($config=''){
        if(empty($config)){
            $config=\system\Conf::Get('CACHE');
        }
        $driver=empty($config['DRIVER'])?'File':$config['DRIVER'];
        $class='\system\Cache\driver\\'.$driver;
        if(self::$handle===null) {
            if($driver!='File'){
                $config=\system\Conf::Get($driver);
            }
            self::$handle=new $class(for_key($config,'strtolower'));
        }
    }

    /**
     * 设置缓存
     * @param $key KEY名称
     * @param $value 设置缓存值
     * @param int $expire 失效时间
     * @return bool
     */
    public function Set($key,$value,$expire=0){
        return self::$handle->Set($key,$value,$expire);
    }

    /**
     * 获取缓存值
     * @param $key KEY名称
     * @return bool|string
     */
    public function Get($key){
        return self::$handle->Get($key);
    }

    /**
     * 检测KEY是否存在
     * @param $key KEY名称
     * @retunr bool
     */
    public function Has($key){
        return self::$handle->IsExists($key);
    }

    /**
     * 删除缓存
     * @param $key KEY名称
     * @return bool|string
     */
    public function Del($key){
        return self::$handle->Del($key);
    }

    /**
     * 清除所有缓存
     * @return bool
     */
    public function Clean(){
        if(self::$handle->FlushAll()){
            return true;
        }
        else{
            return false;
        }
    }

}