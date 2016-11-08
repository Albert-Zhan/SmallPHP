<?php
namespace system;
class Controller{

    //http库
    protected $http=null;

    //Model库
    protected $model=null;

    /**
     * 控制器初始化
     */
    public function __construct(){
        $this->http===null && $this->http=new \system\Http();
        if(file_exists(APP_PATH.'Database'.EXT)){
            $this->model===null && $this->model=new \system\Model();
        }
    }

    /**
     * 获取配置文件配置
     * @param $name 配置名称
     * @param string $file 配置文件
     * @return bool|string
     */
    protected function getconfig($name,$file=''){
        return \system\Conf::get($name,$file);
    }

    /**
     * 设置配置文件配置
     * @param $name 配置名称
     * @param $value 配置值
     * @param string $file 配置文件
     * @return bool|string
     */
    protected function setconfig($name,$value,$file=''){
        return \system\Conf::set($name,$value,$file);
    }

    /**
     * AJAX返回数据到客户端
     * @param array $data 数组
     */
    protected function AjaxReturn($data){
        echo json_encode($data);
    }

    /**
     * 重定向URL
     * @param string $url 重定向的URL
     * @param string $msg 重定向前输出的信息
     * @param int $time 跳转时间
     */
    protected function redirect($url='',$msg='',$time=0){
        if(!pregURL($url)){
            $url=url($url);
        }
        if($msg!=''){
            echo $msg;
        }
        echo "<meta http-equiv=\"refresh\" content=\"$time;url=$url\" />";
    }

    /**
     * 模板赋值
     * @param $var 模板变量名 支持数组
     * @param string $value 模板变量值
     */
    protected function assign($var,$value=''){
        \system\View::assign($var,$value);
    }

    /**
     * 模板输出
     * @param string $file 模板文件
     * @param string $path 模板路径
     */
    protected function display($file='',$path=''){
        \system\View::display($file,$path);
    }

}