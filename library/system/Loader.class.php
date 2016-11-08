<?php
namespace system;
class Loader{

    //命名空间映射
    private static $classMap=[];

    /**
     * 添加命名空间映射
     * @param $name 映射名
     * @param $value 映射值
     */
    private static function addmap($name,$value){
        self::$classMap[$name]=$value;
    }

    /**
     * 导入多继承扩展包
     * @param $class 命名空间名称
     * @throws \Exception
     */
    public static function import($class){
        $classfile=LIB_PATH.'traits/'.$class.EXT;
        if(!file_exists($classfile)){
            \system\Error::thrown('多继承扩展包'.$class.'不存在');
        }
        else{
            $classname='traits/'.$class;
            self::addmap($classname,$classfile);
        }
    }

    /**
     * 查找类文件
     * @param $files 命名空间文件
     * @return bool|string
     */
    private static function find($files){
            if(file_exists(LIB_PATH.$files.'.class'.EXT)){
                    return LIB_PATH.$files.'.class'.EXT;
            }
            elseif(file_exists(APP_PATH.$files.'.class'.EXT)){
                    return APP_PATH.$files.'.class'.EXT;
            }
            else{
                    spl_autoload_unregister("self::authload");
                    return false;
            }
    }

    /**
     * 自动加载实现方法
     * @param $class 类名
     */
    public static function authload($class){
            $class=str_replace('\\','/',$class);
            if(isset(self::$classMap[$class])){
                require_once self::$classMap[$class];
            }
            else{
                $filepath=self::find($class);
                if($filepath){
                    self::addmap($class,$filepath);
                    require_once $filepath;
                }
            }
    }

    /**
     *类的加载注册器
     */
    public static function register(){
        spl_autoload_register("self::authload");
    }

}