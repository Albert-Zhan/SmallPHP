<?php
namespace system;
/**
 * 模板视图类
 * Class View
 * @package system
 */
class View{

    /**
     * 模板变量
     * @var array
     */
    private static $data=[];
    //当前模板引擎
    private static $template=null;
    //模板配置
    private static $config=null;
    //smarty对象
    private static $smarty=null;
    //twig对象
    private static $twig=null;
    //twig配置信息
    private static $twigConf=null;

    /**
     * 模板初始化
     */
    private static function init(){
        self::$template = \system\Conf::get('TEMPLATE_SET','twig');
        if(self::$config===null) {
            self::$config['ext'] = \system\Conf::get('URL_HTML_SUFFIX','.html');
            //模板缓存设置
            if (\system\Conf::get('START_TEMPLATE_CACHE')) {
                $cache = self::$template == 'twig' ? APP_PATH . 'Runtime/Cache' : true;
            } else {
                $cache = false;
            }
            self::$config['cache'] = $cache;
            self::$config['cache_dir'] = APP_PATH . 'Runtime/Cache/' . MODULE_NAME . '/' . CONTROLLER_NAME;
            self::$config['debug'] = APP_DEBUG;
        }
        if(self::$template=='twig'){
            self::twig();
        }
        else {
            self::smarty();
        }
    }

    /**
     * twig模板引擎配置
     */
    private static function twig(){
        $template_set=\system\Conf::get('TMPL_PARSE_STRING');
        \Twig_Autoloader::register();
        if(self::$twig===null){
            self::$twigConf=self::$twigConf===null?new \Twig_Loader_Filesystem():self::$twigConf;
            self::$twig=new \Twig_Environment(self::$twigConf,array(
                'cache' => self::$config['cache'],
                'debug' => self::$config['debug'],
                'charset' =>\system\Conf::get('DEFAULT_CHARSET','UTF-8'),
                'auto_reload' =>true,
                'cache_dir'=>self::$config['cache_dir'],
            ));
            self::$twig->addGlobal('__ROOT__',__ROOT__);
            if(is_array($template_set)){
                foreach($template_set as $k=>$v){
                    self::$twig->addGlobal($k,$v);
                }
            }
        }
    }

    /**
     * smarty模板引擎配置
     */
    private static function smarty(){
        $template_set=\system\Conf::get('TMPL_PARSE_STRING');
        if(self::$smarty===null){
            self::$smarty=new \Smarty();
            self::$smarty->compile_dir=self::$config['cache_dir'];
            self::$smarty->setForce_compile(self::$config['debug']);
            self::$smarty->caching=self::$config['cache'];
            self::$smarty->cache_dir=self::$config['cache_dir'];
            if(is_array($template_set)){
                foreach($template_set as $k=>$v){
                    define($k,$v);
                }
            }
        }
    }

    /**
     * 模板赋值
     * @param $var 模板变量名 支持数组
     * @param string $value 模板变量值
     */
    public static function assign($var,$value=''){
        self::init();
        if(is_array($var)){
            if(self::$template=='twig'){
                foreach($var as $k=>$v){
                    self::$data[$k]=$v;
                }
            }
            else{
                foreach($var as $k=>$v){
                    self::$smarty->assign($k,$v);
                }
            }
        }
        else{
            self::$template=='twig'?self::$data[$var]=$value:self::$smarty->assign($var,$value);
        }
    }

    /**
     * 模板输出
     * @param string $file 模板文件
     * @param string $path 模板路径
     */
    public static function display($file='',$path=''){
        self::init();
        $path=$path==''?APP_PATH.MODULE_NAME.'/Views/'.CONTROLLER_NAME:$path;
        $file=$file==''?ACTION_NAME.self::$config['ext']:$file;
        if(self::$template=='twig'){
            self::$twig->setpath($path);
            self::$twig->display($file,self::$data);
        }
        else{
            self::$smarty->template_dir=$path;
            self::$smarty->display($file);
        }
    }

}