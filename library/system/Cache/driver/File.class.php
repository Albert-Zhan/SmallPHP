<?php
/**
 * Framework:Z-PHP
 * license:MIT
 * Author:Albert Zhan(http://www.5lazy.cn)
 */
namespace system\Cache\driver;
class File {

    private $config=[
        'temp'=>'',
        'prefix'=>'Z-PHP',
        'expire'=>0,
        'length'=>0,
        'key'=>'Z-PHP',
        'check'=>true,
        'compress'=>false,
    ];

    /**
     * 构造函数
     * @param array $options
     */
    public function __construct($options=[]) {
        $this->config=array_merge($this->config,$options);
        !is_dir($this->config['temp']) && mkdir($this->config['temp']);
    }

    /**
     * 取得变量的存储文件名
     * @access private
     * @param string $name 缓存变量名
     * @return string
     */
    private function FileName($name) {
        $key=$this->config['key'];
        $name	=	md5($key.$name);
        $filename	=	$this->config['prefix'].$name.'.php';
        return $this->config['temp'].$filename;
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function Get($name) {
        $filename   =   $this->FileName($name);
        if (!is_file($filename)) {
           return false;
        }
        $content    =   file_get_contents($filename);
        $expire  =  (int)substr($content,8, 12);
        if($expire != 0 && time() > filemtime($filename) + $expire) {
            unlink($filename);
            return false;
        }
        if($this->config['check']) {//开启数据校验
            $check  =  substr($content,20, 32);
            $content   =  substr($content,52, -3);
            if($check != md5($content)) {//校验错误
                return false;
            }
        }else {
            $content   =  substr($content,20, -3);
        }
        if($this->config['compress'] && function_exists('gzcompress')) {
            //启用数据压缩
            $content   =   gzuncompress($content);
        }
        $content    =   unserialize($content);
        return $content;
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param int $expire  有效时间 0为永久
     * @return boolean
     */
    public function Set($name,$value,$expire=0) {
        if(is_null($expire)) {
            $expire =  $this->config['expire'];
        }
        $filename   =   $this->FileName($name);
        $data   =   serialize($value);
        if($this->config['compress'] && function_exists('gzcompress')) {
            //数据压缩
            $data   =   gzcompress($data,3);
        }
        if($this->config['check']) {//开启数据校验
            $check  =  md5($data);
        }else {
            $check  =  '';
        }
        $data    = "<?php\n//".sprintf('%012d',$expire).$check.$data."\n?>";
        $result  =   file_put_contents($filename,$data);
        if($result>$this->config['length'] AND $this->config['length']!=0){
            return false;
        }
        else{
            return true;
        }
    }

    /**
     * 判断缓存变量是否存在
     * @param $name 缓存变量名
     * @return mixed
     */
    public function IsExists($name){
        if($this->get($name)){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function Del($name) {
        return unlink($this->FileName($name));
    }

    /**
     * 清除所有缓存
     */
    public function FlushAll(){
        if(!is_dir($this->config['temp']))
        {
            return false;
        }
        $handle = @opendir($this->config['temp']);
        while(($file = @readdir($handle)) !== false)
        {
            if($file != '.' && $file != '..')
            {
                $dir = $this->config['temp'] . '/' . $file;
                is_dir($dir) ? $this->flushAll($dir) : @unlink($dir);
            }
        }
        closedir($handle);
        return rmdir($this->config['temp']) ;
    }

}