<?php
namespace system;
class Conf{

    //存储配置项
    private static $conf=[];

    /**
     * 临时设置框架配置项
     * @param $name 配置名称
     * @param $value 配置值
     * @param string $file 配置文件
     * @return bool
     */
    public static function Set($name,$value,$file=''){
        $file=empty($file)?APP_PATH.'Config.php':APP_PATH.$file;
        if(is_array($value)){
            foreach($value as $k=>$v){
                self::$conf[md5($file)][$name][$k]=$v;
            }
        }
        else{
            self::$conf[md5($file)][$name]=$value;
        }
        return true;
    }

    /**
     * 获取框架项目配置项
     * @param $name 配置名称
     * @param string $file 配置文件
     * @param string $return 获取不到返回值
     * @return mixed
     * @throws \Exception
     */
    public static function Get($name,$return='',$file=''){
        $file=empty($file)?APP_PATH.'Config.php':APP_PATH.$file;
        if(isset(self::$conf[md5($file)][$name])){
            return self::$conf[md5($file)][$name];
        }
        if(file_exists($file)){
            $conf=require $file;
            if(!empty($conf[$name])){
                self::$conf[md5($file)][$name]=$conf[$name];
                return $conf[$name];
            }
            else{
                return $return!=''?$return:false;
            }
        }
        else{
            \system\Error::Thrown('找不到配置文件');
        }
    }

}