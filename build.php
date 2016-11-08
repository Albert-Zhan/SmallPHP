<?php
$dir=dirname(__FILE__);
require_once $dir.'/init.php';
$dir=substr($dir,'0',strlen($dir)-5);
$model=[
    'Home'=>[
        'file'=>'Common.php,Database.php,Config.php',
        'Controller',
        'Views',
    ],
    'Runtime'=>[
        'Cache',
        'Data',
        'Logs',
    ],
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
        if(isset($dirname[1]) AND $dirname[1]!=''){
            if(is_dir($dir.$dirname[0].'/')){
                echo 'project'.$argv[2].'Already exist'.PHP_EOL;
            }
            else {
                isset($argv[3]) ? mkdir($dir . $dirname[0], $argv[4]) : mkdir($dir . $dirname[0]);
                create_dir($dir . $dirname[0], $model, $dirname[1]);
                echo 'Project success'.PHP_EOL;
            }
        }
        else{
            if(is_dir($dir.$dirname[0].'/')){
                echo 'project'.$argv[2].'Already exist'.PHP_EOL;
            }
            else{
                isset($argv[3])?mkdir($dir.$dirname[0],$argv[4]):mkdir($dir.$dirname[0]);
                create_dir($dir.$dirname[0],$model);
                echo 'Project success'.PHP_EOL;
            }
        }
    break;
    //创建模块
    case '-m':
        !isset($argv[2]) && exit('Parameter error'.PHP_EOL);
        $dirname=explode('/',$argv[2]);
        !is_dir($dir.$dirname[0]) && exit('Project directory does not exist'.PHP_EOL);
        if($dirname[1]!=''){
            create_dir($dir.$dirname[0],$model,$dirname[1]);
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
 * @param string $model 模块
 */
function create_dir($project,$dirs,$model=''){
foreach($dirs as $k=>$v){
    if($k=='Home'){
        $model==''?mkdir($project.'/'.$k):mkdir($project.'/'.$model);
    }
    else{
        !is_dir($project.'/'.$k) && mkdir($project.'/'.$k);
    }
    foreach($v as $key=>$value){
        if($key!=='file'){
            if(is_array($value)){
                mkdir($project.'/'.$k.'/'.$key);
                foreach($value as $d=>$dir){
                    mkdir($project.'/'.$k.'/'.$key.'/'.$dir);
                }
            }
            else{
                if($k=='Home'){
                    $model==''?mkdir($project.'/'.$k.'/'.$value):mkdir($project.'/'.$model.'/'.$value);
                }
                else{
                    !is_dir($project.'/'.$k.'/'.$value) && mkdir($project.'/'.$k.'/'.$value);
                }
            }
        }
        else{
            $files=explode(',',$value);
            foreach($files as $file){
                if(!file_exists($project.'/'.$file)){
                    file_put_contents($project.'/'.$file,'');
                }
            }
        }
    }
}
}