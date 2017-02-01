<?php
namespace vendor\phpreptile;
/**
 * DOM解析类
 */
class html{

    //DOM对象
    private $html='';

    //DOM数据
    private $data;

    //获取第几个标签
    private $num;

    /**
     * 默认实例化传入的是DOM数据
     * @param string $data
     */
    public function __construct($data=''){
        $this->html=new \vendor\phpreptile\vendor\simple_html_dom();
        $this->html->load($data);
    }

    /**
     * 获取DOM元素
     * @param $regexp 获取DOM元素规则
     * @param string $num 获取第几个元素
     * @return $this
     */
    public function query($regexp,$num=''){
        $this->data=$this->html->find($regexp);
        $this->num=$num;
        return $this;
    }

    /**
     * 获取DOM内容和属性
     * @param string $content 默认获取内容
     * @return array|bool
     * @throws \Exception
     */
    public function get($content='innertext'){
        if(!is_array($this->data)){
            return false;
        }
        foreach($this->data as $v){
            $array[]=$v->$content;
        }
        if($this->num==''){
            return !empty($array)?$array:false;
        }
        else{
           if( ($this->num-1)>count($array) ){
               \system\Error::Thrown('获取不到指定的数据条目');
           }
            return $array[$this->num-1];
        }
    }

    /**
     *释放DOM解析器内存
     */
    public function __destruct(){
        $this->html->clear();
    }

}
