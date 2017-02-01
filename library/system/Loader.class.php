<?php
/**
 * Framework:Z-PHP
 * license:MIT
 * Author:Albert Zhan(http://www.5lazy.cn)
 */
namespace system;
class Loader{

    //命名空间映射
    private static $classMap=[];

    /**
     * 添加命名空间映射
     * @param $name 映射名
     * @param $value 映射值
     */
    private static function Addmap($name,$value){
        self::$classMap[$name]=$value;
    }

    /**
     * 导入多继承扩展包
     * @param $class 命名空间名称
     * @throws \Exception
     */
    public static function Import($class){
        $classfile=LIB_PATH.'traits/'.$class.EXT;
        if(!file_exists($classfile)){
            \system\Error::Thrown('多继承扩展包'.$class.'不存在');
        }
        else{
            $classname='traits/'.$class;
            self::addmap($classname,$classfile);
        }
    }

    /**
     * 导入扩展包
     * @param $package 包名
     */
    public static function Vendor($package){
        if(is_array($package)){
            foreach($package as $k=>$v){
                require_once LIB_PATH.'vendor/'.$v.EXT;
            }
        }
        else{
            require_once LIB_PATH.'vendor/'.$package.EXT;
        }
    }

    /**
     * 查找类文件
     * @param $files 命名空间文件
     * @return bool|string
     */
    private static function Find($files){
        if(file_exists(LIB_PATH.$files.'.class'.EXT)){
            return LIB_PATH.$files.'.class'.EXT;
        }
        elseif(file_exists(APP_PATH.$files.'.class'.EXT)){
            return APP_PATH.$files.'.class'.EXT;
        }
        else{
            spl_autoload_unregister("self::AuthLoad");
            return false;
        }
    }

    /**
     * 自动加载实现方法
     * @param $class 类名
     */
    private static function AuthLoad($class){
        $class=str_replace('\\','/',$class);
        if(isset(self::$classMap[$class])){
            require_once self::$classMap[$class];
        }
        else{
            $filepath=self::Find($class);
            if($filepath){
                self::Addmap($class,$filepath);
                require_once $filepath;
            }
        }
    }

    /**
     *类的加载注册器
     */
    public static function Register(){
        spl_autoload_register("self::AuthLoad");
    }

}