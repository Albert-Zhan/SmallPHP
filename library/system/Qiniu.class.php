<?php
namespace system;
class Qiniu{

    //配置信息
    private $config=[
        'ak'=>'',
        'sk'=>'',
        'size'=>'0',
        'domin'=>'',
        'bucket'=>'',
        'timeout'=>3600,
    ];

    public function __construct($config){
        $this->config=array_merge($this->config,$config);
    }

    /**
     * 七牛文件上传
     * @return array|bool|string
     */
    public function QiniuUpload(){
        $config=[
            'maxSize' => $this->config['size'],
            'rootPath' => './',
            'saveName' => ['uniqid', ''],
            'driver' => 'Qiniu',
            'driverConfig' => [
                'secretKey' => $this->config['sk'],
                'accessKey' => $this->config['ak'],
                'domain' => $this->config['domin'],
                'bucket' => $this->config['bucket'],
                'timeout'=>$this->config['timeout'],
            ],
        ];
        $Upload = new \system\Upload($config);
        $info = $Upload->upload($_FILES);
        if($info){
            return $info;
        }
        else{
            return $Upload->getError();
        }
    }

    /**
     * 删除七牛上的文件
     * @param $file 删除的文件
     * @return bool
     */
    public function QiniuDel($file){
        $config=[
            'driverConfig' => [
                'secretKey' => $this->config['ak'],
                'accessKey' => $this->config['sk'],
                'domain' => $this->config['domin'],
                'bucket' => $this->config['bucket'],
            ],
        ];
        $Upload = new \system\Upload\driver\Qiniu\QiniuStorage($config['driverConfig']);
        $info = $Upload->del($file);
        if(is_array($info)){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * 七牛抓取网页文件
     * @param $url 抓取的URL
     * @param $host 空间域名
     * @return string
     */
    public function fetch($url,$host){
        $extension = substr($url, strrpos($url, '.') + 1);
        $fetch = $this->urlsafe_base64_encode($url);
        $time = date("Ymd");
        $uniqid = uniqid();
        $key = 'file_'.$time.'_'.$uniqid.'.'.$extension;
        $enurl = $this->config['bucket'].':'.$key;
        $to = $this->urlsafe_base64_encode($enurl);
        $url = 'http://iovip.qbox.me/fetch/'. $fetch .'/to/' . $to;
        $access_token = $this->generate_access_token($url);
        $header[] = 'Content-Type: application/json';
        $header[] = 'Authorization: QBox '. $access_token;
        $con=$this->send('http://iovip.qbox.me/fetch/'.$fetch.'/to/'.$to, $header);
        return $host.'file_'.$time.'_'.$uniqid.'.'.$extension;
    }

    /**
     * URL安全形式的base64编码
     * @param $str
     * @return mixed
     */
    private function urlsafe_base64_encode($str){
        $find = ["+","/"];
        $replace = ["-", "_"];
        return str_replace($find, $replace, base64_encode($str));
    }

    /*运算签名
     * @param $access_key
     * @param $secret_key
     * @param $url
     * @param string $params
     * @return string
     */
    private function generate_access_token($url, $params = ''){
        $parsed_url = parse_url($url);
        $path = $parsed_url['path'];
        $access = $path;
        if (isset($parsed_url['query'])) {
            $access .= "?" . $parsed_url['query'];
        }
        $access .= "\n";
        if($params){
            if (is_array($params)){
                $params = http_build_query($params);
            }
            $access .= $params;
        }
        $digest = hash_hmac('sha1', $access, $this->config['sk'], true);
        return $this->config['ak'].':'.$this->urlsafe_base64_encode($digest);
    }

    /**发送
     * @param $url
     * @param string $header
     * @return mixed
     */
    private function send($url, $header = '') {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER,1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        $con = curl_exec($curl);
        if ($con === false) {
            echo 'CURL ERROR: ' . curl_error($curl);
        } else {
            return $con;
        }
    }

}