<?php
/**
 * Framework:Z-PHP
 * license:MIT
 * Author:Albert Zhan(http://www.5lazy.cn)
 */
namespace system;
class Request{

    /**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    public function GetClientIp($type = 0,$adv=false) {
        $type       =  $type ? 1 : 0;
        static $ip  =   NULL;
        if ($ip !== NULL) return $ip[$type];
        if($adv){
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos    =   array_search('unknown',$arr);
                if(false !== $pos) unset($arr[$pos]);
                $ip     =   trim($arr[0]);
            }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip     =   $_SERVER['HTTP_CLIENT_IP'];
            }elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip     =   $_SERVER['REMOTE_ADDR'];
            }
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u",ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }

    /**
     * 返回当前URL
     */
    public function Url(){
        if($_SERVER['REQUEST_URI']==__ROOT__.'/'){
            return url();
        }
        else{
            return 'http://'.$this->Host().$_SERVER['REQUEST_URI'];
        }
    }

    /**
     * 返回当前域名
     * @return string|bool
     */
    public function Host(){
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * 返回当前端口
     * @return int
     */
    public function Port(){
        return $_SERVER['SERVER_PORT'];
    }

    /**
     * 是否GET请求
     * @return bool
     */
    public function IsGet(){
        return strtoupper($_SERVER['REQUEST_METHOD'])=='GET'?true:false;
    }

    /**
     * 是否POST请求
     * @return bool
     */
    public function IsPost(){
        return strtoupper($_SERVER['REQUEST_METHOD'])=='POST'?true:false;
    }

    /**
     * 是否PUT请求
     * @return bool
     */
    public function IsPut(){
        return strtoupper($_SERVER['REQUEST_METHOD'])=='PUT'?true:false;
    }

    /**
     * 是否DELETE请求
     * @return bool
     */
    public function IsDelete(){
        return strtoupper($_SERVER['REQUEST_METHOD'])=='DELETE'?true:false;
    }

    /**
     * 是否HEAD请求
     * @return bool
     */
    public function IsHead(){
        return strtoupper($_SERVER['REQUEST_METHOD'])=='HEAD'?true:false;
    }

    /**
     * 是否AJAX请求
     * @return bool
     */
    public function IsAjax(){
        return strtolower(@$_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest'?true:false;
    }

    /**
     * 是否PJAX请求
     * @return bool
     */
    public function IsPjax(){
        return !is_null(@$_SERVER['HTTP_X_PJAX'])?true:false;
    }

    /**
     * 检测是否使用手机访问
     * @return bool
     */
    public function IsMobile(){
        if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
            return true;
        } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
            return true;
        } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
            return true;
        } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断是否SSL协议
     * @return boolean
     */
    public function IsSsl() {
        if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
            return true;
        }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
            return true;
        }
        return false;
    }

    /**
     * 过滤表单中的表达式
     * @param string $value 表单值
     * @return void
     */
    public function FilterExp($value){
        // 过滤查询特殊字符
        if (is_string($value)) {
            $value=preg_replace('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i','',$value);
        }
        // TODO 其他安全过滤
        return $value;
    }

    /**
     * 获取POST信息
     * @param $k POST数组的键名
     * @param string $filter 过滤方式
     * @param string $regexp PHP正则匹配
     * @return string $post
     */
    public function Post($k,$filter='',$regexp=''){
        $fun=$filter==''?\system\Conf::Get('DEFAULT_FILTER'):$filter;
        if($k=='*'){
            $post=$fun==true?for_value($_POST,$fun):$_POST;
        }
        else{
            $post=$fun==true?call_user_func($fun,$_POST[$k]):$_POST[$k];
        }
        $preg=['phone'=>'pregPhone','email'=>'pregEmail','url'=>'pregURL','ic'=>'pregIc','pos'=>'pregPos','ip'=>'pregIp','is_ch'=>'pregCh'];
        if($regexp!='') {
            if (isset($preg[$regexp])) {
                $regexp=$preg[$regexp];
                $post = is_array($post) ? for_value($post, $regexp) : call_user_func($regexp, $post);
            } else {
                $post=is_array($post)?for_value($post,'preg_matchs',$regexp):call_user_func('preg_matchs',$post,$regexp);
            }
        }
        return $post;
    }

    /**
     * 获取GET信息
     * @param $k POST数组的键名
     * @param string $filter 过滤方式
     * @param string $regexp PHP正则匹配
     * @return string $get
     */
    public function Get($k,$filter='',$regexp=''){
        $fun=$filter==''?\system\Conf::Get('DEFAULT_FILTER'):$filter;
        if($k=='*'){
            $get=$fun==true?for_value($_GET,$fun):$_GET;
        }
        else{
            $get=$fun==true?call_user_func($fun,$_GET[$k]):$_GET[$k];
        }
        $preg=['phone'=>'pregPhone','email'=>'pregEmail','url'=>'pregURL','ic'=>'pregIc','pos'=>'pregPos','ip'=>'pregIp','is_ch'=>'pregCh'];
        if($regexp!='') {
            if (isset($preg[$regexp])) {
                $regexp=$preg[$regexp];
                $get=is_array($get)?for_value($get,$regexp):call_user_func($regexp,$get);
            } else {
                $get=is_array($get)?for_value($get,'preg_matchs',$regexp):call_user_func('preg_matchs',$get,$regexp);
            }
        }
        return $get;
    }

    /**
     * 获取设置COOKIE
     * @param $name COOKIE的名称
     * @param string $value COOKIE设置的值
     * @param string $prefix COOKIE作用域
     * @param string $timeout COOKIE设置的过期时间
     * @return true
     */
    public function Cookie($name,$value='',$prefix='',$timeout=''){
        $prefix=$prefix==''?'COOKIE':$prefix;
        //获取COOKIE
        if($value=='' AND $value!==null){
            return $name=='*'?(@$_COOKIE[$prefix]):(isset($_COOKIE[$prefix][$name])?$_COOKIE[$prefix][$name]:false);
        }
        //删除COOKIE
        elseif($value===null){
            if($name=='*'){
                $_COOKIE[$prefix]=[];
            }
            else{
                unset($_COOKIE[$prefix][$name]);
                setcookie($name,'',time()-100);
            }
            return true;
        }
        //设置COOKIE
        else{
            $_COOKIE[$prefix][$name]=$value;
            //设置COOKIE即时生效
            $timeout==''?setcookie($name,$value,99999999):setcookie($name,$value,time()+$timeout);
            return true;
        }
    }

    /**
     * 获取设置SESSION
     * @param $name
     * @param string $value
     * @param string $prefix SESSION作用域
     * @param string $timeout
     * @return true
     */
    public function Session($name,$value='',$prefix='',$timeout=''){
        //获取SESSION
        if($value=='' AND $value!==null){
            return \system\Session::Get($name,$prefix);
        }
        //删除SESSION
        elseif($value===null){
            return $name=='*'?\system\Session::Clean($prefix):\system\Session::Del($name,$prefix);
        }
        //设置SESSION
        else{
            return \system\Session::Set($name,$value,$prefix,$timeout);
        }
    }

}