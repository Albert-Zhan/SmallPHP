<?php
namespace vendor\phpreptile;
/**
 * CURL抓取类
 */
class curl{

    //浏览器UA
    private $header= array('User-Agent: Mozilla/5.0 (Windows NT 5.2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36','X-FORWARDED-FOR:154.125.25.15', 'CLIENT-IP:154.125.25.15');

    //存放cookie路径
    private $cookie_path=null;

    /**
     * get抓取
     * @param string $url
     * @return string $data
     */
    public function get($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch,CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data=curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * post抓取
     * @param array $url 登录地址
     * @return string $data
     */
    public function post($url){
        $this->cookie_path=tempnam($url['path'],'cookie');
        $this->postvali($url['lasturl'],$url['data']);
        $ch2=curl_init($url['nowurl']);
        curl_setopt($ch2,CURLOPT_HEADER,true);
        curl_setopt($ch2,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch2,CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch2,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch2,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch2,CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch2,CURLOPT_COOKIEFILE,$this->cookie_path);
        $data=curl_exec($ch2);
        curl_close($ch2);
        return $data;
    }

    /**
     * post登录验证
     * @param $url 登录需要验证的地址
     * @param $data 登录所需的数据
     */
    private function postvali($url,$data){
        $ch=curl_init($url);
        $data=http_build_query($data);
        curl_setopt($ch,CURLOPT_HEADER, 0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch,CURLOPT_COOKIEJAR,$this->cookie_path);
        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * 析构方法
     */
    public function __destruct()
    {
        @unlink($this->cookie_path);
    }

}
