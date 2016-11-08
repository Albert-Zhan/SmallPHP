<?php
namespace system\Cache\driver;
class Memcached
{
    //Memcached对象实例
    private static $Memcached=null;

    //存放配置信息
    private static $config=[
        'host'         => '127.0.0.1', // memcache主机
        'port'         => 11211, // memcache端口
        'timeout'      => 0, // 连接超时时间（单位：毫秒）
        'username'     => '', //账号
        'password'     => '', //密码
    ];

    /**
     * 构造函数
     * @param array $config 配置信息
     */
    public function __construct($config=[])
    {
        self::$config=array_merge(self::$config,$config);
        if(self::$Memcached===null){
            self::$Memcached = new \Memcached();
            // 设置连接超时时间（单位：毫秒）
            if (self::$config['timeout'] > 0) {
                self::$Memcached->setOption(\Memcached::OPT_CONNECT_TIMEOUT, self::$config['timeout']);
            }
            // 支持集群
            $hosts = explode(',', self::$config['host']);
            $ports = explode(',', self::$config['port']);
            if (empty($ports[0])) {
                $ports[0] = 11211;
            }
            // 建立连接
            $servers = [];
            foreach ((array) $hosts as $i => $host) {
                $servers[] = [$host, (isset($ports[$i]) ? $ports[$i] : $ports[0]), 1];
            }
            self::$Memcached->addServers($servers);
            if ('' != self::$config['username']) {
                self::$Memcached->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);
                self::$Memcached->setSaslAuthData(self::$config['username'], self::$config['password']);
            }
        }
    }

    /**
     * 写入缓存
     * @param string $name 缓存变量名
     * @param string $value 缓存的值
     * @param int $expire 有效时间（秒）
     * @return bool
     */
    public function set($name, $value, $expire = 0)
    {
        if (self::$Memcached->set($name, $value, $expire)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 读取缓存
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function get($name)
    {
        return self::$Memcached->get($name);
    }

    /**
     * 判断缓存变量名是否存在
     * @param $name 缓存变量名
     * @return bool
     */
    public function isExists($name){
        if($this->get($name)){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * 删除缓存
     * @param string $name 缓存变量名
     * @return bool
     */
    public function del($name)
    {
        if (self::$Memcached->delete($name)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *  往一个KEY中追加数据
     * @param $key KEY名称
     * @param $value 追加的值
     * @return bool
     */
    public function append($key,$value){
        return self::$Memcached->append($key,$value);
    }

    /**
     * 数据自增
     * @param $key KEY名称
     * @return bool
     */
    public function increment($key){
        return self::$Memcached->increment($key);
    }

    /**
     * 数据自减
     * @param $key KEY名称
     * @return bool
     */
    public function decrement($key){
        return self::$Memcached->decrement($key);
    }

    /**
     * 清除缓存
     * @return void
     */
    public function flushAll()
    {
        if (self::$Memcached->flush()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 关闭Memcached连接
     */
    public function close(){
        self::$Memcached->quit();
    }

}