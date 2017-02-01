<?php
namespace vendor\phpreptile;
class phpreptile{

    //存放抓取和解析的数据
    private static $data;

    public function __construct($url=''){
        if($url!=''){
            $this->grab($url);
        }
    }

    /**
     *抓取网页
     * @param $url get抓取传入字符串 post抓取传入数组
     * @return $this
     */
    public function grab($url){
        $curl=new \vendor\phpreptile\curl;
        self::$data=is_array($url)?$curl->post($url):$curl->get($url);
        return $this;
    }

    /**
     * php正则匹配
     * @param $regexps 匹配规则为一个二维数组
     * @param string $data $data 抓取的数据
     * @return $this
     * @throws \Exception
     */
    public function phpregexp($regexps,$data=''){
        $data==''?$datas=self::$data:$datas=$data;
        foreach($regexps as $k=>$regexp){
            if(!preg_match_all($regexp[0],$datas,$datas)){
                \system\Error::Thrown('PHP正则表达式匹配不到数据');
            }
            if(isset($regexp[2])){
                $datas=$datas[$regexp[1]][$regexp[2]];
            }
            else{
                $datas=$datas[$regexp[1]][0];
            }
        }
        self::$data=$datas;
        return $this;
    }

    /**
     * html标签匹配
     * @param $regexps 匹配规则为一个二维数组
     * @param string $data 抓取的数据
     * @return $this
     */
    public function htmlregexp($regexps,$data=''){
        $data==''?$datas=self::$data:$datas=$data;
        $regexp=@explode(',',$regexps['label']);
        $html=new \vendor\phpreptile\html($datas);
        if($regexp){
            $datas=$html->query($regexp['0'],@$regexp['1'])->get($regexps['property']);
        }
        else{
            $datas=$html->query($regexps['label'])->get($regexps['property']);
        }
        self::$data=$datas;
        return $this;
    }

    /**
     * 开始执行
     * @return mixed
     */
    public function run(){
        return self::$data;
    }

}