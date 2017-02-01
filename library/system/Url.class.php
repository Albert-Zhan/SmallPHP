<?php
/**
 * Framework:Z-PHP
 * license:MIT
 * Author:Albert Zhan(http://www.5lazy.cn)
 */
namespace system;
class Url{

    /**
     * 普通模式
     */
    private static function General(){
        $m=isset($_GET['m'])?$_GET['m']:\system\Conf::Get('DEFAULT_MODULE','Home');
        $c=isset($_GET['c'])?$_GET['c']:\system\Conf::Get('DEFAULT_CONTROLLER','Index');
        $a=isset($_GET['a'])?$_GET['a']:\system\Conf::Get('DEFAULT_ACTION','index');
        //不区分URL大小写
        if(\system\Conf::Get('URL_INSENSITIVE')) {
            self::UrlDecode($m, $c, $a);
        }
        //定义当前模块控制器方法
        define('MODULE_NAME',$m);
        define('CONTROLLER_NAME',$c);
        define('ACTION_NAME',$a);
        //加载模块和控制器方法
        $class='\\'.$m.'\\Controller\\'.$c.'Controller';
        if(!file_exists(APP_PATH.$m.'/Controller/'.$c.'Controller.class.php')){
            \system\Error::Thrown('找不到控制器'.$c);
        }
        $controller=new $class();
        $action=get_class_methods($class);
        if(in_array('__init',$action)){
            $controller->__init();
        }
        if(!in_array($a,$action)){
            \system\Error::Thrown('找不到方法'.$a);
        }
        $controller->$a();
    }

    /**
     * PATHINFO模式
     */
    private static function Pathinfo(){
        $url=@$_SERVER['PATH_INFO'];
        if($url==null or $url=='/'){
            $m=\system\Conf::Get('DEFAULT_MODULE','Home');
            $c=\system\Conf::Get('DEFAULT_CONTROLLER','Index');
            $a=\system\Conf::Get('DEFAULT_ACTION','index');
        }
        else{
            $pathinfo = explode('/', trim($url, '/'));
            $m=$pathinfo[0];
            $c=$pathinfo[1];
            $suffix=strripos($pathinfo[2],'.');
            $a=!empty($suffix)?substr($pathinfo[2],0,$suffix):$pathinfo[2];
        }
        //解析PATHINFO多余参数
        self::Parameter($url);
        //不区分URL大小写
        if(\system\Conf::Get('URL_INSENSITIVE')) {
            self::UrlDecode($m, $c, $a);
        }
        //定义当前模块控制器方法
        define('MODULE_NAME',$m);
        define('CONTROLLER_NAME',$c);
        define('ACTION_NAME',$a);
        //加载模块和控制器方法
        $class='\\'.$m.'\\Controller\\'.$c.'Controller';
        if(!file_exists(APP_PATH.$m.'/Controller/'.$c.'Controller.class.php')){
            \system\Error::Thrown('找不到控制器'.$c);
        }
        $controller=new $class();
        $action=get_class_methods($class);
        if(in_array('__init',$action)){
            $controller->__init();
        }
        if(!in_array($a,$action)){
            \system\Error::Thrown('找不到方法'.$a);
        }
        $controller->$a();
    }

    /**
     * 解析URL多余参数
     * @param $parm URL参数
     */
    private static function Parameter($parm){
        $parameter = explode('/', trim($parm, '/'));
        $count=count($parameter)+2;
        $i=3;
        while($i<$count){
            if(isset($parameter[$i+1])) {
                $suffix=strripos($parameter[$i + 1],'.');
                $option=!empty($suffix)?substr($parameter[$i + 1],0,$suffix):$parameter[$i + 1];
                $_GET[$parameter[$i]] = $option;
            }
            $i = $i + 2;
        }
    }

    /**
     * URL伪静态模式
     */
    private static function Reweite(){
        $url=$_SERVER['REQUEST_URI'];
        $replace=$_SERVER['SCRIPT_NAME'];
        if($url=str_replace($replace,'',$url)){
            $url=str_replace(__ROOT__.'/','',$url);
        }
        if($url==null or $url=='/'){
            $m=\system\Conf::Get('DEFAULT_MODULE','Home');
            $c=\system\Conf::Get('DEFAULT_CONTROLLER','Index');
            $a=\system\Conf::Get('DEFAULT_ACTION','index');
        }
        else{
            $reweite=explode('/', trim($url, '/'));
            $m=$reweite[0];
            $c=$reweite[1];
            $suffix=strripos($reweite[2],'.');
            $a=!empty($suffix)?substr($reweite[2],0,$suffix):$reweite[2];
        }
        //解析参数
        self::Parameter($url);
        //不区分URL大小写
        if(\system\Conf::Get('URL_INSENSITIVE')) {
            self::UrlDecode($m, $c, $a);
        }
        //定义当前模块控制器方法
        define('MODULE_NAME',$m);
        define('CONTROLLER_NAME',$c);
        define('ACTION_NAME',$a);
        //加载模块和控制器方法
        $class='\\'.$m.'\\Controller\\'.$c.'Controller';
        if(!file_exists(APP_PATH.$m.'/Controller/'.$c.'Controller.class.php')){
            \system\Error::Thrown('找不到控制器'.$c);
        }
        $controller=new $class();
        $action=get_class_methods($class);
        if(in_array('__init',$action)){
            $controller->__init();
        }
        if(!in_array($a,$action)){
            \system\Error::Thrown('找不到方法'.$a);
        }
        $controller->$a();
    }

    /**
     * linux下URL大小写转换
     * @param $m 模块
     * @param $c 控制器
     * @param $a 方法
     * @throws \Exception
     */
    private static function UrlDecode(&$m,&$c,&$a){
        if(!IS_WIN) {
            $filenames = scandir(APP_PATH . $m . '/Controller/');
            $count = strlen($c);
            foreach ($filenames as $filename) {
                $file = substr($filename, 0, $count);
                if (strtolower($file) == strtolower($c)) {
                        $c=$file;
                    break;
                }
            }
            $class='\\'.$m.'\\Controller\\'.$c.'Controller';
            if(!file_exists(APP_PATH.$m.'/Controller/'.$c.'Controller.class.php')){
                \system\Error::Thrown('找不到控制器'.$c);
            }
            $actions=get_class_methods(new $class());
            foreach($actions as $action){
                    if(strtolower($a)==strtolower($action)){
                        $a=$action;
                        break;
                    }
            }
        }
    }

    /**
     * 路由管理
     */
    public static function Route(){
        $urlMod=\system\Conf::Get('URL_MODEL','1');
        switch($urlMod){
            case '1':
                self::General();
                break;
            case '2':
                self::Pathinfo();
                break;
            case '3':
                self::Reweite();
                break;
            default:
                self::General();
        }
    }

}