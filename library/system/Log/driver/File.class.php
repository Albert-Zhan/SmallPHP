<?php
namespace system\Log\driver;
class File{

    //日志配置
    private static $config=[
        'type'=>'log',
        'size'=>'20480',
    ];

    //日志类型匹配信息
    private static $msg=[
        'log'=>'错误信息:',
        'sql'=>'SQL记录:',
    ];

    /**
     * 构造函数
     * @param array $config 配置信息
     */
    public function __construct(array $config){
        self::$config=array_merge(self::$config,$config);
    }

    /**
     * 检测目录权限
     * @param $dir 目录名
     * @return bool
     */
    private function check($dir){
        if(is_dir($dir) AND file_put_contents($dir.'access.txt','access_test')){
            @unlink($dir.'access.txt');
            return true;
        }
        else{
            false;
        }
    }

    /**
     * 写入日志
     * @param $msg 日志信息
     * @param string $path 日志路径
     * @return bool
     * @throws \system\Exception
     */
    public function write($msg,$path=''){
        $dir=self::$config['type']=='log'?'Logs':'Data';
        $path=empty($path)?APP_PATH.'Runtime/'.$dir.'/':$path;
        if($this->check($path)){
            $file=$path.date('Ymd').'.log';
            $head=date('Y-m-d H:i:s').'　　　'.self::$msg[self::$config['type']].PHP_EOL;
            if(file_exists($file)  and filesize($file)<=self::$config['size']){
                file_put_contents($file,$head.$msg.PHP_EOL,FILE_APPEND);
            }
            else{
                file_put_contents($file,$head.$msg.PHP_EOL);
            }
            return true;
        }
        else{
            \system\Error::thrown('写入日志失败,权限不足');
        }
    }

    /**
     * 读取日志文件
     * @param string $fileanme 日志文件
     * @param string $path 日志路径
     * @return array|bool
     */
    public function read($fileanme='',$path=''){
        $dir=self::$config['type']=='log'?'Logs':'Data';
        $path=$path==''?APP_PATH.'Runtime/'.$dir.'/':$path;
        $fileanme=$fileanme==''?date('Ymd').'.log':$fileanme;
        if(file_exists($path.$fileanme)){
            $data=file_get_contents($path.$fileanme);
            $data=@explode(PHP_EOL,$data);
            $i=0;
            while($i<count($data)-1){
                $datas[$i]=$data[$i+1];
                $i=$i+2;
            }
            return empty($datas)?false:array_merge($datas);
        }
        else{
            return false;
        }
    }

}