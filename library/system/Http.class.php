<?php
/**
 * Framework:Z-PHP
 * license:MIT
 * Author:Albert Zhan(http://www.5lazy.cn)
 */
namespace system;
class Http{

    private static $cookie_path='';

    /**
     * 获取请求消息对象
     * @return Request
     */
    public static function Request(){
        return new \system\Request();
    }

    /**
     * Get请求
     * @param $url 请求地址
     * @param array $header 请求头信息
     * @param bool $is_validate 该请求是否验证
     * @return mixed
     */
    public static function Get($url,$header=[],$is_validate=false){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if($is_validate){
            curl_setopt($ch,CURLOPT_COOKIEFILE,self::$cookie_path);
        }
        $data=curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * Post请求
     * @param $url 请求地址
     * @param array $header 请求头信息
     * @param array $param 请求参数
     * @param bool $is_validate 该请求是否验证
     * @return mixed
     */
    public static function Post($url,$header=[],$param=[],$is_validate=false){
        $ch=curl_init($url);
        curl_setopt($ch,CURLOPT_HEADER,false);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$param);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);
        if($is_validate){
            curl_setopt($ch,CURLOPT_COOKIEFILE,self::$cookie_path);
        }
        $data=curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * Post验证请求
     * @param $url 请求地址
     * @param array $header 请求头信息
     * @param array $param 请求参数
     * @param null $dir COOKIE保存路径
     * @return mixed
     */
    public static function PostValiDate($url,$header=[],$param=[],$dir=null){
        if($dir===null){
            self::$cookie_path=tempnam(ini_get('session.save_path'),'cookie');
        }
        else{
            self::$cookie_path=tempnam($dir,'cookie');
        }
        $ch1=curl_init($url);
        curl_setopt($ch1,CURLOPT_HEADER, false);
        curl_setopt($ch1,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch1,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch1,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch1,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch1,CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch1,CURLOPT_POST,1);
        curl_setopt($ch1,CURLOPT_POSTFIELDS,$param);
        curl_setopt($ch1,CURLOPT_COOKIEJAR,self::$cookie_path);
        curl_exec($ch1);
        curl_close($ch1);
    }

    /**
     * 下载文件
     * @param $url 下载文件地址
     * @param $path 下载文件保存路径
     * @param null $prefix 下载文件保存文件名前缀
     * @return array
     */
    public static function DownFile($url,$path,$prefix=null){
        $prefix=$prefix===null?'zphp_':$prefix;
        $rows=[];
        if(is_array($url)){
            foreach($url as $k=>$v){
                $ext=pathinfo($v,PATHINFO_EXTENSION);
                $save_path=$path.$prefix.uniqid().'.'.$ext;
                file_put_contents($save_path,file_get_contents($v));
                $rows[]=$save_path;
            }
        }
        else{
            $ext=pathinfo($url,PATHINFO_EXTENSION);
            $save_path=$path.$prefix.uniqid().'.'.$ext;
            file_put_contents($save_path,file_get_contents($url));
            $rows[]=$save_path;
        }
        return $rows;
    }

}