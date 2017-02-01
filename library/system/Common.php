<?php
/**
 * Framework:Z-PHP
 * license:MIT
 * Author:Albert Zhan(http://www.5lazy.cn)
 */
/**
 * 遍历数组中的键并且执行第二个参数的函数
 * @param array $data 数组
 * @param $fun 函数
 * @param string $param 附加参数
 * @return array $data
 */
function for_key($data,$fun,$param=''){
foreach($data as $k=>$v){
    if(is_array($v)){
        foreach($v as $key=>$value){
            $key=$param!=''?call_user_func($fun,$key,$param):call_user_func($fun,$key);
            $data[$k][$key]=$value;
        }
    }
    else{
        $k=$param!=''?call_user_func($fun,$k,$param):call_user_func($fun,$k);
        $data[$k]=$v;
    }
}
return $data;
}

/**
 * 遍历数组中的值并且执行第二个参数的函数
 * @param array $data 数组
 * @param $fun 函数
 * @param string $param 附加参数
 * @return array $data
 */
function for_value($data,$fun,$param=''){
foreach($data as $k=>$v){
    if(is_array($v)){
        foreach($v as $key=>$value){
            $data[$k][$key]=$param!=''?call_user_func($fun,$value,$param):call_user_func($fun,$value);
        }
    }
    else{
        $data[$k]=$param!=''?call_user_func($fun,$v,$param):call_user_func($fun,$v);
    }
}
return $data;
}

/**
 * 生成URL
 * @param string $url
 * @param string $param
 * @return string $url
 */
function url($url='',$param=''){
$host='http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/';
switch(\system\Conf::Get('URL_MODEL','1')){
    case '1':
        $uri=pathinfo($_SERVER['SCRIPT_NAME']);
        $host=$host.$uri['basename'];
        if($url==''){
            $url=$host.'?m='.MODULE_NAME.'&c='.CONTROLLER_NAME.'&a='.ACTION_NAME;
        }
        else{
            $array=explode('/',$url);
            !isset($array[0]) && $array[0]='Home';
            !isset($array[1]) && $array[1]='Index';
            !isset($array[2]) && $array[2]='index';
            $url=$host.'?m='.$array[0].'&c='.$array[1].'&a='.$array[2];
        }
        if($param!='' AND is_array($param)){
            $url=$url.'&'.http_build_query($param);
        }
    break;
    case '2':
        $uri=pathinfo($_SERVER['SCRIPT_NAME']);
        $host=$host.$uri['basename'];
        if($url==''){
            $url=$host.'/'.MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        }
        else{
            $array=explode('/',$url);
            !isset($array[0]) && $array[0]='Home';
            !isset($array[1]) && $array[1]='Index';
            !isset($array[2]) && $array[2]='index';
            $url=$host.'/'.$array[0].'/'.$array[1].'/'.$array[2];
        }
        $ffix=\system\Conf::Get('URL_HTML_SUFFIX','.html');
        if($param!='' AND is_array($param)){
            $url=$url.'/'.ForPathinfo($param).$ffix;
        }
        else{
            $url=$url.$ffix;
        }
    break;
    case '3':
        if($url==''){
            $url=$host.MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        }
        else{
            $array=explode('/',$url);
            !isset($array[0]) && $array[0]='Home';
            !isset($array[1]) && $array[1]='Index';
            !isset($array[2]) && $array[2]='index';
            $url=$host.$array[0].'/'.$array[1].'/'.$array[2];
        }
        $ffix=\system\Conf::Get('URL_HTML_SUFFIX','.html');
        if($param!='' AND is_array($param)){
            $url=$url.'/'.ForPathinfo($param).$ffix;
        }
        else{
            $url=$url.$ffix;
        }
    break;
}
return $url;
}

/**
 * 循环出PATHINFO模式所需的URL参数
 * @param array $params 参数
 * @return string $param
 */
function ForPathinfo($params){
$param='';
foreach($params as $k=>$v){
$param.=$k.'/'.$v.'/';
}
$param=rtrim($param,'/');
return $param;
}

/**
 * 匹配手机号码
 * @param $data 匹配的数据
 * @return bool
 */
function pregPhone($data){
    if(!preg_match("/^((13[0-9])|147|(15[0-35-9])|180|182|(18[5-9]))[0-9]{8}$/A",$data,$result)){
        return false;
    }
    else{
        return $result[0];
    }
}

/**
 * 匹配邮箱
 * @param $data 匹配的数据
 * @return bool
 */
function pregEmail($data){
    if(!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9._-]*\@[a-zA-Z0-9]+\.[a-zA-Z0-9\.]+$/A',$data,$result)){
        return false;
    }
    else{
        return $result[0];
    }
}

/**
 * 匹配网页URL
 * @param $data 匹配的数据
 * @return bool
 */
function pregURL($data){
    if(!preg_match('/^(([a-zA-Z]+)(:\/\/))?([a-zA-Z]+)\.(\w+)\.([\w.]+)(\/([\w]+)\/?)*(\/[a-zA-Z0-9]+\.(\w+))*(\/([\w]+)\/?)*(\?(\w+=?[\w]*))*((&?\w+=?[\w]*))*$/',$data,$result)){
        return false;
    }
    return $result[0];
}

/**
 * 匹配身份证号码
 * @param $data 匹配的数据
 * @return bool
 */
function pregIC($data){
    if(!preg_match('/^(([0-9]{15})|([0-9]{18})|([0-9]{17}x))$/',$data,$result)){
        return false;
    }
    else{
        return $result[0];
    }
}

/**
 * 匹配邮编
 * @param $data 匹配的数据
 * @return bool
 */
function pregPOS($data){
    if(!preg_match('/^[1-9]\d{5}$/',$data,$result)){
        return false;
    }
    else{
        return $result[0];
    }
}

/**
 * 匹配IP
 * @param $data 匹配的数据
 * @return bool
 */
function pregIP($data){
    if(!preg_match('/^((([1-9])|((0[1-9])|([1-9][0-9]))|((00[1-9])|(0[1-9][0-9])|((1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))))\.)((([0-9]{1,2})|(([0-1][0-9]{2})|(2[0-4][0-9])|(25[0-5])))\.){2}(([1-9])|((0[1-9])|([1-9][0-9]))|(00[1-9])|(0[1-9][0-9])|((1[0-9]{2})|(2[0-4][0-9])|(25[0-5])))$/',$data,$result)){
        return false;
    }
    else{
        return $result[0];
    }
}

/**
 * 匹配是否中文
 * @param $data 匹配的数据
 * @return bool
 */
function pregCh($data){
    if(preg_match_all('/([\x{4e00}-\x{9fa5}]){1}/u',$data,$result)){
        return true;
    }
    else{
        return false;
    }
}

/**
 * 自定义PHP正则函数
 * @param $data 数据
 * @param $regexp PHP正则规则
 * @return bool
 */
function preg_matchs($data,$regexp){
if(!preg_match_all($regexp,$data,$result)){
    return false;
}
else{
    return $result[0][0];
}
}

/**
 * 时间转换函数
 * @param $datetemp 时间戳
 * @return string
 */
function smartDate($datetemp) {
    $op = '';
    $sec = time() - $datetemp;
    $hover = floor($sec / 3600);
    if ($hover == 0) {
        $min = floor($sec / 60);
        if ($min == 0) {
            $op = $sec . ' 秒前';
        } else {
            $op = "$min 分钟前";
        }
    } elseif ($hover < 24) {
        $op = "约 $hover 小时前";
    }
    elseif($hover>=24){
        $d=floor($sec / 86400);
        if($d<=31){
            $op = "约 $d 天前";
        }
        else{
            $m=floor($sec / 2678400);
            $op = "约 $m 月前";
        }
    }
    return $op;
}

/**
 * 解压ZIP文件
 * @param $file 解压的文件
 * @param null $path 保存路径
 */
function extractZip($file, $path = null)
{
    if(!isset($path)){
        $array = explode('.',$file);
        $path = reset($array);
    }

    $zip = new \ZipArchive();
    if($zip->open($file) === true){
        $zip->extractTo($path);
        $zip->close();
    }
}

/**
 * 压缩文件夹为ZIP
 * @param $path 压缩的文件夹
 * @param $save 保存的ZIP文件名
 */
function condenseZip($path, $save)
{
    $zip = new \ZipArchive();
    if ($zip->open($save,\ZipArchive::OVERWRITE) === true)
    {
        addZip(opendir($path),$zip,$path,'');
        $zip->close();
    }
}

//无限递归压缩文件夹为zip文件
function addZip($openFile,$zipObj,$sourceAbso,$newRelat = '')
{
    while(($file = readdir($openFile)) != false)
    {
        if($file=="." || $file=="..")
            continue;
        $sourceTemp = $sourceAbso.'/'.$file;
        $newTemp = $newRelat==''?$file:$newRelat.'/'.$file;
        if(is_dir($sourceTemp))
        {
            $zipObj->addEmptyDir($newTemp);
            addZip(opendir($sourceTemp),$zipObj,$sourceTemp,$newTemp);
        }
        else {
            $zipObj->addFile($sourceTemp, $newTemp);
        }
    }
}
