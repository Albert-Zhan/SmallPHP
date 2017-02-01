<?php
/**
 * Framework:Z-PHP
 * license:MIT
 * Author:Albert Zhan(http://www.5lazy.cn)
 */
$dir=dirname(__FILE__);
require_once $dir.'/init.php';
$dir=substr($dir,'0',strlen($dir)-5);
$modul=[
    'Home'=>[
        'Controller'=>[
            'IndexController.class.php'
        ],
        'Model',
        'Views',
    ],
    'Runtime'=>[
        'Cache',
        'Data',
        'Logs',
    ],
    'file'=>'Common.php,Database.php,Config.php',
];
if(!isset($argv[1]) or $argv[1]=='-h'){
    $str=<<<TEXT
    -p parameter Create project
    -m parameter Create Model
    -d parameter Download the third party Library
    -u parameter Update framework core file
    -v version number
    -h Help\n
TEXT;
exit($str);
}
switch($argv[1]){
    //创建项目
    case '-p':
        !isset($argv[2]) && exit('Parameter error'.PHP_EOL);
        $dirname=explode('/',$argv[2]);
        if(is_dir($dir.$dirname[0].'/')){
            echo 'project'.$argv[2].'Already exist'.PHP_EOL;
        }
        else{
            mkdir($dir.$dirname[0].'/');
            $modulDir='Home';
            if(isset($dirname[1]) && $dirname[1]!=''){
                $modulDir=$dirname[1];
                $modul=[
                    $modulDir=>[
                        'Controller'=>[
                            'IndexController.class.php',
                        ],
                        'Model',
                        'Views',
                    ],
                    'Runtime'=>[
                        'Cache',
                        'Data',
                        'Logs',
                    ],
                    'file'=>'Common.php,Database.php,Config.php',
                ];
            }
            $centent=<<<Code
<?php
//检测PHP版本
if(version_compare(PHP_VERSION,'5.4.0','<'))exit('require PHP > 5.4.0 !');
//定义根目录
define('ROOT_PATH',dirname(__FILE__));
//定义项目目录
define('APP_PATH',ROOT_PATH.'/$dirname[0]/');
//开启调试模式
define('APP_DEBUG',true);
//启动框架
require_once './Z-PHP/start.php';
Code;
            file_put_contents($dir.'index.php',$centent);
            create_dir($dir.$dirname[0].'/',$modul,$modulDir);
            echo 'Project success'.PHP_EOL;
        }
    break;
    //创建模块
    case '-m':
        !isset($argv[2]) && exit('Parameter error'.PHP_EOL);
        $dirname=explode('/',$argv[2]);
        !is_dir($dir.$dirname[0]) && exit('Project directory does not exist'.PHP_EOL);
        if($dirname[1]!=''){
            $modul=[
                $dirname[1]=>[
                    'Controller',
                    'Model',
                    'Views',
                ],
            ];
            create_dir($dir.$dirname[0].'/',$modul);
            echo 'Models success'.PHP_EOL;
        }
    break;
    //下载第三方库
    case '-d':
        !isset($argv[2]) && exit('Parameter error'.PHP_EOL);
        $url='http://zphp.5lazy.cn/library/vendor/download.html?pattern=cli&libname='.$argv[2];
        $downurl=file_get_contents($url);
        $downurl=json_decode($downurl,true);
        if($downurl['status']=='fail'){
                exit('Cannot find library'.$argv[2].PHP_EOL);
        }
        $data=file_get_contents($downurl['url']);
        file_put_contents(LIB_PATH.'vendor/'.$argv[2].'.zip',$data);
        extractZip(LIB_PATH.'vendor/'.$argv[2].'.zip',LIB_PATH);
        echo 'Installation Library'.$argv[2].'success'.PHP_EOL;
        break;
    //更新框架核心文件
    case '-u':
        !isset($argv[2]) && exit('Parameter error'.PHP_EOL);

    break;
    case '-v':
        exit('Version:'.ZPHP_VERSION.PHP_EOL);
    break;
}
/**
 * 创建目录
 * @param $project 项目
 * @param array $dirs 目录数组
 * @param string $modul 创建的模块
 */
function create_dir($project,$dirs,$modul=''){
    $centent=<<<CODE
<?php
return [
    //框架核心配置
    'DEFAULT_MODULE'=>'Home',//默认模块
    'DEFAULT_CONTROLLER'=>'Index',//默认控制器
    'DEFAULT_ACTION'=>'index',//默认方法
    'URL_INSENSITIVE'  =>  true,//不区分大小写
    'URL_MODEL'=>'1',//URL模式
    'URL_HTML_SUFFIX'=>'.html',//模板后缀
    'DEFAULT_FILTER' => 'htmlspecialchars',  //默认过滤函数
    'START_TEMPLATE_CACHE'=>true,//模板缓存设置
    'TMPL_PARSE_STRING'=>[],//模板配置
    'TAG_BLOCK_LEFT'=>'',//区块函数左定界符
    'TAG_BLOCK_RIGHT'=>'',//区块函数右定界符
    'TAG_VARIABLE_LEFT'=>'',//变量输出左定界符
    'TAG_VARIABLE_RIGHT'=>'',//变量输出右定界符
    'DEFAULT_CHARSET'=>'UTF-8',//默认字符编码
    'EXTRA_FILE_LIST'=>[],//扩展函数文件配置
];
CODE;
$testData=<<<data
<?php
namespace $modul\Controller;
class IndexController extends \system\Controller{

    public function __init(){

    }

    //首页
    public function index(){
        echo('欢迎使用Z-PHP框架');
    }

}
data;
    foreach($dirs as $k=>$v){
        if($k=='file'){
            $files=explode(',',$v);
            foreach($files as $file){
                if($file!='Config.php'){
                    file_put_contents($project.$file,'');
                }
                else{
                    file_put_contents($project.$file,$centent);
                }
            }
        }
        else{
            $path=$project.$k.'/';
            mkdir($path);
            if(is_array($v)){
                foreach($v as $key=>$val){
                    if(!is_array($val)){
                        mkdir($path.$val.'/');
                    }
                    else{
                        $pathChild=$path.$key.'/';
                        mkdir($pathChild);
                        foreach($val as $value){
                            file_put_contents($pathChild.$value,$testData);
                        }
                    }
                }
            }
        }
    }
}