<?php
/**
 * Framework:Z-PHP
 * license:MIT
 * Author:Albert Zhan(http://www.5lazy.cn)
 */
namespace system;
class View{

    /**
     * 模板变量
     * @var array
     */
    private static $data=[];
    //模板配置
    private static $config=null;
    //twig对象
    private static $twig=null;
    //twig配置信息
    private static $twigConf=null;

    /**
     * 模板初始化
     */
    private static function init(){
        if(self::$config===null) {
            self::$config['ext'] = \system\Conf::Get('URL_HTML_SUFFIX','.html');
            //模板缓存设置
            if (\system\Conf::Get('START_TEMPLATE_CACHE')) {
                $cache =APP_PATH . 'Runtime/Cache';
            } else {
                $cache = false;
            }
            self::$config['cache'] = $cache;
            self::$config['cache_dir'] = APP_PATH . 'Runtime/Cache/' . MODULE_NAME . '/' . CONTROLLER_NAME;
            self::$config['debug'] = APP_DEBUG;
            $template_set=\system\Conf::Get('TMPL_PARSE_STRING');
            \Twig_Autoloader::register();
            if(self::$twig===null){
                self::$twigConf=self::$twigConf===null?new \Twig_Loader_Filesystem():self::$twigConf;
                self::$twig=new \Twig_Environment(self::$twigConf,array(
                    'cache' => self::$config['cache'],
                    'debug' => self::$config['debug'],
                    'charset' =>\system\Conf::Get('DEFAULT_CHARSET','UTF-8'),
                    'auto_reload' =>true,
                    'cache_dir'=>self::$config['cache_dir'],
                ),array(
                    'tag_block' => array(\system\Conf::Get('TAG_BLOCK_LEFT','{%'),\system\Conf::Get('TAG_BLOCK_RIGHT','%}')),
                    'tag_variable' => array(\system\Conf::Get('TAG_VARIABLE_LEFT','{{'),\system\Conf::Get('TAG_VARIABLE_RIGHT','}}')),
                ));
                self::$twig->addGlobal('__ROOT__',__ROOT__);
                if(is_array($template_set)){
                    foreach($template_set as $k=>$v){
                        self::$twig->addGlobal($k,$v);
                    }
                }
            }
        }
    }

    /**
     * 模板赋值
     * @param $var 模板变量名 支持数组
     * @param string $value 模板变量值
     */
    public static function Assign($var,$value=''){
        self::init();
        if(is_array($var)){
            foreach($var as $k=>$v){
                self::$data[$k]=$v;
            }
        }
        else{
            self::$data[$var]=$value;
        }
    }

    /**
     * 模板输出
     * @param string $file 模板文件
     * @param string $path 模板路径
     */
    public static function Display($file='',$path=''){
        self::init();
        $path=$path==''?APP_PATH.MODULE_NAME.'/Views/'.CONTROLLER_NAME:$path;
        $file=$file==''?ACTION_NAME.self::$config['ext']:$file;
        self::$twig->setpath($path);
        self::$twig->display($file,self::$data);
    }

}