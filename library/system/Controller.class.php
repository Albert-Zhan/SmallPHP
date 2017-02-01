<?php
/**
 * Framework:Z-PHP
 * license:MIT
 * Author:Albert Zhan(http://www.5lazy.cn)
 */
namespace system;
class Controller{

    /**
     * 获取配置文件配置
     * @param $name 配置名称
     * @param string $file 配置文件
     * @param string $return 获取为空时设置的返回值
     * @return bool|string
     */
    protected function GetConfig($name,$return='',$file=''){
        return \system\Conf::Get($name,$return,$file);
    }

    /**
     * 设置配置文件配置
     * @param $name 配置名称
     * @param $value 配置值
     * @param string $file 配置文件
     * @return bool|string
     */
    protected function SetConfig($name,$value,$file=''){
        return \system\Conf::Set($name,$value,$file);
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
    protected function Redirect($url='',$msg='',$time=0){
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
    protected function Assign($var,$value=''){
        \system\View::Assign($var,$value);
    }

    /**
     * 模板输出
     * @param string $file 模板文件
     * @param string $path 模板路径
     */
    protected function Display($file='',$path=''){
        \system\View::Display($file,$path);
    }

}