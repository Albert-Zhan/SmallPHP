<?php
/**
 * Framework:Z-PHP
 * license:MIT
 * Author:Albert Zhan(http://www.5lazy.cn)
 */
namespace system\Session\driver;
class Memcached extends \SessionHandler{

    //存放配置信息
    private $config=[];

    //Memcached对象实例
    private $memcached=null;

    //构造函数
    public function __construct($config){
        $this->config=$config;
    }

    /**
     * 打开SESSION
     * @param $session_path
     * @param $session_name
     * @return bool
     */
    public function open($session_path,$session_name){
        $this->memcached=new \system\Cache\driver\Memcached(for_key(\system\Conf::Get('Memcached'),'strtolower'));
        return true;
    }

    /**
     * 写入SESSION
     * @param $session_id SESSION ID
     * @param $session_data 写入的数据
     * @return bool
     */
    public function write($session_id,$session_data){
        $config=\system\Conf::Get('SESSION');
        return $this->memcached->Set($this->config['prefix'].$session_id,$session_data,$config['EXPIRE']);
    }

    /**
     * 读取SESSION
     * @param $session_id
     * @return string|bool
     */
    public function read($session_id){
        return $this->memcached->Get($this->config['prefix'].$session_id);
    }

    /**
     * 删除SESSION
     * @param $session_id SESSION ID
     * @return bool
     */
    public function destroy($session_id){
        return $this->memcached->Del($this->config['prefix'].$session_id);
    }

    /**
     * 关闭SESSION
     * @return bool
     */
    public function close(){
        $this->gc(ini_get('session.gc_maxlifetime'));
        $this->memcached->Close();
        $this->memcached = null;
        return true;
    }

    /**
     * SESSION垃圾回收
     * @param $sessMaxLifeTime
     * @return bool
     */
    public function gc($sessMaxLifeTime){
        return true;
    }

}