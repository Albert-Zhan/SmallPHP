<?php
/**
 * Framework:Z-PHP
 * license:MIT
 * Author:Albert Zhan(http://www.5lazy.cn)
 */
namespace system;
use \system\Controller;
class Rbac extends Controller{

    //当前URL地址
    protected $current_url;

    //当前URL上级地址
    private $current_nav;

    //存放当前用户信息
    protected $user_info;

    //当前URL地址信息
    protected $current_menuinfo;

    //存放RBAC配置信息
    protected $config_info=[];

    /**
     * 初始化(构造函数)
     */
    protected function __init(){
        //生成当前地址
        $this->current_url = MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        //获取配置信息
        $this->config_info=array_merge($this->config_info,\system\Conf::Get('RBAC',[]));
        if(empty($this->current_menuinfo)){
            $this->current_menuinfo=$this->GetMenuInfo($this->current_url);
        }
    }

    /**
     * 获取当前URL菜单信息
     * @param $current_url 当前url
     * @return array
     */
    private function GetMenuInfo($current_url){
        $model=new \system\Model();
        return $model->Table($this->config_info['TABLE']['menu'])->Where(['control'=>$current_url])->Find();
    }

    /**
     * 判断是否登录
     */
    protected function IsLogin(){
        $model=new \system\Model();
        if(!\system\Session::get($this->config_info['USER_ID'])){
            return false;
        }
        $userInfo=$model->Table($this->config_info['TABLE']['user'])->Where([$this->config_info['KEY_ID']=>\system\Session::Get($this->config_info['USER_ID'])])->Find();
        if(empty($userInfo)){
            return false;
        }
        $roleInfo=$model->Table($this->config_info['TABLE']['role'])->Where([$this->config_info['KEY_ID']=>$userInfo['fk_role_id']])->Field(['menu_auth','name(role_name)'])->Find();
        $this->user_info=array_merge($userInfo,$roleInfo);
        return true;
    }

    /**
     * 检测用户权限
     * @param [] $public 公共的控制器方法
     */
    protected function CheckAuth($public=[]){
        //判断是否登录
        if(!$this->IsLogin()){
            $this->Redirect($this->config_info['LOGOUT_URL'],$this->config_info['LOGOUT_MSG']);
            exit;
        }
        //过滤超级用户
        $not=$this->user_info['fk_role_id']==1 && $this->config_info['IS_ROOT']===true;
        //检测权限
        if(!$not){
            if(!in_array($this->current_menuinfo['pk_id'],explode(',',$this->user_info['menu_auth'])) && !in_array(ACTION_NAME,$public)){
                $this->redirect($this->config_info['NO_ACCESS_URL'],$this->config_info['NO_ACCESS_MSG']);
                exit;
            }
        }
    }

    /**
     * 获取PID为0的菜单
     */
    private function GetNavList(){
        $model=new \system\Model();
        $not=$this->user_info['fk_role_id']==1 && $this->config_info['IS_ROOT']===true;
        //检测权限
        if(!$not){
            $map[$this->config_info['KEY_ID']]=[$this->user_info['menu_auth']];
        }
        $map['pid']=0;
        return $model->Table($this->config_info['TABLE']['menu'])->Where(['AND'=>$map])->Field([$this->config_info['KEY_ID'],'pid','name','control'])->Select();
    }

    /**
     * 递归获取当前URL上级地址
     * @param string $pid PID
     */
    private function GetTopUrl($pid=''){
        $model=new \system\Model();
        $info=$model->Table($this->config_info['TABLE']['menu'])->Where([$this->config_info['KEY_ID']=>$pid])->Field(['control','pid'])->Find();
        if ($info['pid']){
            $this->GetTopUrl($info['pid']);
        }else {
            $this->current_nav = $info['control'];
            return $info['control'];
        }
    }

    /**
     * 获取菜单树
     * @param string $control 当前菜单的url
     * @param $userInfo 当前用户信息
     * @return strin|bool
     */
    private function GetMenuTree($control='',$userInfo){
        $model=new \system\Model();
        $not=$userInfo['fk_role_id']==1 && $this->config_info['IS_ROOT']===true;
        //检测权限
        if(!$not){
            $map[$this->config_info['KEY_ID']]=[$userInfo['menu_auth']];
        }
        $map['is_hidden']=0;
        $field=[$this->config_info['KEY_ID'],'pid','soft','name','control'];
        $rows=$model->Table($this->config_info['TABLE']['menu'])->Where(['AND'=>$map,'ORDER'=>"soft DESC"])->Field($field)->Select();
        $rows=$this->list_to_tree($rows,$this->config_info['KEY_ID']);
        if($control){
            $id=$model->Table($this->config_info['TABLE']['menu'])->Where(['control'=>$control])->Field($this->config_info['KEY_ID'])->Find();
            if ($id){
                foreach ($rows as $key=>$value){
                    if ($value[$this->config_info['KEY_ID']] == $id){
                        $rows = $value;
                        break;
                    }
                }
            }
            $rows = $rows['_child'];
        }
        return $rows;
    }

    /**
     * 把返回的数据集转换成Tree
     * @param $list 数据集
     * @param string $pk 主键ID
     * @param string $pid 上级ID
     * @param string $child 下级循环键
     * @param int $root
     * @return array
     */
    private function list_to_tree($list, $pk='pk_id', $pid = 'pid', $child = '_child', $root = 0){
        $tree = array();
        if(is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId =  $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                }else{
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }

    /**
     * Rbac专用的显示视图方法
     * @param array|string $data 模板赋值变量
     * @param string $file 模板文件
     */
    protected function ShowView($data=[],$file=''){
        //当前URL
        $data['currentUrl']=$this->current_url;
        //获取菜单树
        $data['navList']=$this->GetNavList();
        //获取当前URL上级地址
        if ($this->current_menuinfo['pid']){
            $this->GetTopUrl($this->current_menuinfo['pid']);
        }else {
            $this->current_nav = $this->current_menuinfo['control'];
        }
        $data['currentNav']=$this->current_nav;
        //获取菜单列表
        $data['menu_list']=$this->GetMenuTree($this->current_nav,$this->user_info);
        //用户信息
        $data['userInfo']=$this->user_info;
        $this->Assign($data);
        $this->Display($file);
    }

}