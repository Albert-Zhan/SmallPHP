<?php

namespace system\Cache\driver;
class Redis {

    //Redis对象实例
    private static $redis=null;

    //Redis配置信息
    private static $config=[
            'host'         => '127.0.0.1', // redis主机
            'port'         => 6379, // redis端口
            'password'     => '', // 密码
            'select'       => 0, // 操作库
            'timeout'      => 0, // 超时时间(秒)
            'persistent'   => false, // 是否长连接
    ];

    /**
     * 构造函数
     * @param array $config 配置信息
     */
    public function __construct($config=[]) {
        self::$config=array_merge(self::$config,$config);
        if(self::$redis===null){
            self::$redis = new \Redis();
            $func = self::$config['persistent'] ? 'pconnect' : 'connect';
            self::$redis->$func(self::$config['host'], self::$config['port'], self::$config['timeout']);
            if ('' != self::$config['password']) {
                self::$redis->auth(self::$config['password']);
            }
            if (0 != self::$config['select']) {
                self::$redis->select(self::$config['select']);
            }
        }
    }

    /**
     * 设置值  构建一个字符串
     * @param string $key KEY名称
     * @param string $value  设置值
     * @param int $timeOut 时间  0表示无过期时间
     * @return string $retRes
     */
    public function Set($key, $value, $timeOut=0) {
        $retRes = self::$redis->set($key, $value);
        if ($timeOut > 0) {
            self::$redis->expire($key, $timeOut);
        }
        return $retRes;
    }

    /*
     * 构建一个集合(无序集合)
     * @param string $key 集合Y名称
     * @param string|array $value  值
     */
    public function Sadd($key,$value){
        return self::$redis->sadd($key,$value);
    }

    /*
     * 构建一个集合(有序集合)
     * @param string $key 集合名称
     * @param string|array $value  值
     */
    public function Zadd($key,$value){
        return self::$redis->zadd($key,$value);
    }

    /**
     * 取集合对应元素
     * @param $setName 集合名字
     * @return array|bool|string
     */
    public function Smembers($setName){
        return self::$redis->smembers($setName);
    }

    /**
     * 设置多个值
     * @param array $keyArray KEY名称
     * @param int $timeout 获取得到的数据
     * @return bool|string 时间
     */
    public function Sets($keyArray, $timeout=0) {
        if (is_array($keyArray)) {
            $retRes = self::$redis->mset($keyArray);
            if ($timeout > 0) {
                foreach ($keyArray as $key => $value) {
                    self::$redis->expire($key, $timeout);
                }
            }
            return $retRes;
        } else {
            return "Call  " . __FUNCTION__ . " method  parameter  Error !";
        }
    }

    /**
     * 通过key获取数据
     * @param string $key KEY名称
     * @return bool|string $result
     */
    public function Get($key) {
        $result=self::$redis->get($key);
        if(empty($result)){
            return false;
        }
        else{
            return $result;
        }
    }

    /**
     * 同时获取多个值
     * @param $keyArray $keyArray 获key数值
     * @return array|bool|string
     */
    public function Gets($keyArray) {
        if (is_array($keyArray)) {
            $result=self::$redis->mget($keyArray);
            if(empty($result)){
                return false;
            }
            else{
                return $result;
            }
        } else {
            return "Call  " . __FUNCTION__ . " method  parameter  Error !";
        }
    }

    /**
     * 获取所有key名，不是值
     */
    public function KeyAll() {
        return self::$redis->keys('*');
    }

    /**
     * 删除一条数据key
     * @param string $key 删除KEY的名称
     * @return bool
     */
    public function Del($key) {
        $result=self::$redis->delete($key);
        if($result){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * 同时删除多个key数据
     * @param $keyArray KEY集合
     * @return bool|string
     */
    public function Dels($keyArray) {
        if (is_array($keyArray)) {
            $result=self::$redis->del($keyArray);
            if($result){
                return true;
            }
            else{
                return false;
            }
        } else {
            return "Call  " . __FUNCTION__ . " method  parameter  Error !";
        }
    }

    /**
     * 数据自增
     * @param $key KEY名称
     * @return int
     */
    public function Increment($key) {
        return self::$redis->incr($key);
    }

    /**
     * 数据自减
     * @param $key KEY名称
     * @return int
     */
    public function Decrement($key) {
        return self::$redis->decr($key);
    }

    /**
     * 判断key是否存在
     * @param $key KEY名称
     * @return bool
     */
    public function IsExists($key){
        return self::$redis->exists($key);
    }

    /**
     * 清空数据
     */
    public function FlushAll() {
        return self::$redis->flushAll();
    }

    /**
     * 关闭Redis连接
     */
    public function Close(){
        return self::$redis->close();
    }

}