<?php
/**
 * Framework:Z-PHP
 * license:MIT
 * Author:Albert Zhan(http://www.5lazy.cn)
 */
/**
* 基于Face++平台写的图像识别操作类
* 使用前请前往Face++平台注册账号
*/
namespace system;
class Orc{

    //api_key
    private $api_key;

    //api_secret
    private $api_secret;

    //请求的URL
    private $url='';

    //请求头数据
    private $data=[];

    /**
     * 构造函数
     * @param string $api_key
     * @param string $api_secret
     */
    public function __construct($api_key='',$api_secret=''){
        $this->api_key=$api_key;
        $this->api_secret=$api_secret;
    }

    /**
     * 设置ApiKey
     * @param $value 值
     */
    public function SetApiKey($value){
        $this->api_key=$value;
    }

    /**
     * 获取ApiKey
     */
    public function GetApiKey(){
        return $this->api_key;
    }

    /**
     * 设置ApiSecret
     * @param $value 值
     */
    public function SetApiSecret($value){
        $this->api_secret=$value;
    }

    /**
     * 获取ApiSecret
     */
    public function GetApiSecret(){
        return $this->api_secret;
    }

    /**
     * POST请求方法
     */
    private function Request(){
        $head=['User-Agent: Mozilla/5.0 (Windows NT 5.2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36','X-FORWARDED-FOR:154.125.25.15', 'CLIENT-IP:154.125.25.15'];
        $ch=curl_init($this->url);
        curl_setopt($ch,CURLOPT_HEADER,false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,0);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$this->data);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);
        $data=curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * 人脸识别
     * @param strin $img 识别图像
     * @param int $is_landmark 是否检测并返回人脸五官和轮廓的83个关键点
     * @param int $is_face_info 是否显示人脸信息
     * @param int $is_corp_face 是否裁剪人脸
     * @param string $save_path 裁剪人脸保存路径
     * @return string|array
     */
    public function FaceRecognition($img,$is_landmark=0,$is_face_info=1,$is_corp_face=0,$save_path=''){
        $this->url='https://api-cn.faceplusplus.com/facepp/v3/detect';
        $this->data=[
            'api_key'=>$this->api_key,
            'api_secret'=>$this->api_secret,
            'return_attributes'=>'gender,age,smiling,glass,headpose,facequality,blur',
            'return_landmark'=>$is_landmark,
        ];
        if(@substr($img,0,4)=='http'){
            $this->data['image_url']=$img;
        }
        else{
            $file=curl_file_create($img);
            $this->data['image_file']=$file;
        }
        //请求接口
        $data=$this->Request();
        //转换数据
        $array_data=json_decode($data,true);
        //做失败重试操作
        if(@$array_data['error_message']=='CONCURRENCY_LIMIT_EXCEEDED'){
            sleep(3);
            $this->FaceRecognition($img,$is_landmark,$is_face_info,$is_corp_face,$save_path);
        }
        if(empty($array_data['faces'])){
            return false;
        }
        else{
            $datas=[];
            $is_face_info && $this->FaceInfoParam($array_data['faces'],$datas['face_info']);
            $datas['count_face']=count($array_data['faces']);
            if($is_corp_face && $save_path){
                $this->FaceCrop($img,$save_path,$array_data,$datas);
            }
            return $datas;
        }
    }

    /**
     * 身份证照片识别
     * @param $img 识别图像
     * @param int $is_legality 是否返回照片合法性检查结果
     * @return bool
     */
    public function CardRecognition($img,$is_legality=0){
        $this->url='https://api-cn.faceplusplus.com/cardpp/v1/ocridcard';
        $this->data=[
            'api_key'=>$this->api_key,
            'api_secret'=>$this->api_secret,
            'legality'=>$is_legality,
        ];
        if(@substr($img,0,4)=='http'){
            $this->data['image_url']=$img;
        }
        else{
            $this->data['image_file']="@{$img}";
        }
        //请求接口
        $data=$this->Request();
        //转换数据
        $array_data=json_decode($data,true);
        //做失败重试操作
        if(@$array_data['error_message']=='CONCURRENCY_LIMIT_EXCEEDED'){
            sleep(3);
            $this->CardRecognition($img,$is_legality);
        }
        if(empty($array_data['cards'])){
            return false;
        }
        else{
            return $array_data['cards'][0];
        }
    }

    /**
     * 驾驶证识别
     * @param $img 识别图像
     * @return bool
     */
    public function LicenseRecognition($img){
        $this->url='https://api-cn.faceplusplus.com/cardpp/v1/ocrdriverlicense';
        $this->data=[
            'api_key'=>$this->api_key,
            'api_secret'=>$this->api_secret,
        ];
        if(@substr($img,0,4)=='http'){
            $this->data['image_url']=$img;
        }
        else{
            $this->data['image_file']="@{$img}";
        }
        //请求接口
        $data=$this->Request();
        //转换数据
        $array_data=json_decode($data,true);
        //做失败重试操作
        if(@$array_data['error_message']=='CONCURRENCY_LIMIT_EXCEEDED'){
            sleep(3);
            $this->LicenseRecognition($img);
        }
        if(empty($array_data['cards'])){
            return false;
        }
        else{
            return $array_data['cards'][0];
        }
    }

    /**
     * 文字识别
     * @param $img 识别图像
     * @return bool
     */
    public function TextRecognition($img){
        $this->url='https://api-cn.faceplusplus.com/imagepp/beta/recognizetext';
        $this->data=[
            'api_key'=>$this->api_key,
            'api_secret'=>$this->api_secret,
        ];
        if(@substr($img,0,4)=='http'){
            $this->data['image_url']=$img;
        }
        else{
            $this->data['image_file']="@{$img}";
        }
        //请求接口
        $data=$this->Request();
        //转换数据
        $array_data=json_decode($data,true);
        //做失败重试操作
        if(@$array_data['error_message']=='CONCURRENCY_LIMIT_EXCEEDED'){
            sleep(3);
            $this->TextRecognition($img);
        }
        if(empty($array_data['result'])){
            return false;
        }
        else{
            return $array_data['result'];
        }
    }

    /**
     * 返回参数解析
     * @param $data 需要解析的数据
     * @param $save_data 保存解析结果的数据
     */
    private function FaceInfoParam($data,&$save_data){
        foreach($data as $key=>$value){
            if(isset($value['attributes'])) {
                foreach ($value['attributes'] as $k => $v) {
                    switch ($k) {
                        case 'glass':
                            if ($v['value'] == 'Dark') {
                                $save_data[$key][$k] = '佩戴墨镜';
                            } elseif ($v['value'] == 'Normal') {
                                $save_data[$key][$k] = '佩戴普通眼镜';
                            } else {
                                $save_data[$key][$k] = '不佩戴眼镜';
                            }
                            break;
                        case 'gender':
                            if ($v['value'] == 'Female') {
                                $save_data[$key][$k] = '女';
                            } else {
                                $save_data[$key][$k] = '男';
                            }
                            break;
                        case 'age':
                            $save_data[$key][$k] = $v['value'];
                            break;
                        case 'smile':
                            $save_data[$key][$k] = (int)$v['value'];
                            break;
                        case 'headpose':
                            $save_data[$key][$k] = $v;
                            break;
                    }
                }
            }
        }
    }

    /**
     * 脸部图片裁剪
     * @param $img 裁剪原图
     * @param $save_path 裁剪保存路径
     * @param $data 需要裁剪的图片
     * @param $save_data 保存裁剪结果的数据
     */
    private function FaceCrop($img,$save_path,$data,&$save_data){
        $img_obj=new \system\Image();
        $ext=pathinfo($img,PATHINFO_EXTENSION);
        $filename=uniqid('face');
        file_put_contents(APP_PATH.$filename.'.'.$ext,file_get_contents($img));
        foreach($data['faces'] as $key=>$value){
            $face_file_name=uniqid('face_');
            $img_obj->open(APP_PATH.$filename.'.'.$ext)->crop($value['face_rectangle']['width'],$value['face_rectangle']['height'],$value['face_rectangle']['left'],$value['face_rectangle']['top'])->save($save_path.$face_file_name.'.'.$ext);
            $save_data['save_path'][$key]=$face_file_name.'.'.$ext;
        }
        @unlink(APP_PATH.$filename.'.'.$ext);
    }

}