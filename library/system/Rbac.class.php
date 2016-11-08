<?php
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
        $this->config_info=array_merge($this->config_info,\system\Conf::get('RBAC',[]));
        if(empty($this->current_menuinfo)){
            $this->current_menuinfo=$this->getMenuInfo($this->current_url);
        }
    }

    /**
     * 获取当前URL菜单信息
     * @param $current_url 当前url
     */
    private function getMenuInfo($current_url){
        return $this->model->table($this->config_info['TABLE']['menu'])->where(['control'=>$current_url])->find();
    }

    /**
     * 判断是否登录
     */
    protected function isLogin(){
        if(!\system\Session::get($this->config_info['USER_ID'])){
            return false;
        }
        $userInfo=$this->model->table($this->config_info['TABLE']['user'])->where([$this->config_info['KEY_ID']=>\system\Session::get($this->config_info['USER_ID'])])->find();
        if(empty($userInfo)){
            return false;
        }
        $roleInfo=$this->model->table($this->config_info['TABLE']['role'])->where([$this->config_info['KEY_ID']=>$userInfo['fk_role_id']])->field(['menu_auth','name(role_name)'])->find();
        $this->user_info=array_merge($userInfo,$roleInfo);
        return true;
    }

    /**
     * 检测用户权限
     * @param [] $public 公共的控制器方法
     */
    protected function checkAuth($public=[]){
        //判断是否登录
        if(!$this->isLogin()){
            $this->redirect($this->config_info['LOGOUT_URL'],$this->config_info['LOGOUT_MSG']);
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
    private function getNavList(){
        $not=$this->user_info['fk_role_id']==1 && $this->config_info['IS_ROOT']===true;
        //检测权限
        if(!$not){
            $map[$this->config_info['KEY_ID']]=[$this->user_info['menu_auth']];
        }
        $map['pid']=0;
        return $this->model->table($this->config_info['TABLE']['menu'])->where(['AND'=>$map])->field([$this->config_info['KEY_ID'],'pid','name','control'])->select();
    }

    /**
     * 递归获取当前URL上级地址
     * @param string $pid PID
     */
    private function getTopUrl($pid=''){
        $info=$this->model->table($this->config_info['TABLE']['menu'])->where([$this->config_info['KEY_ID']=>$pid])->field(['control','pid'])->find();
        if ($info['pid']){
            $this->getTopUrl($info['pid']);
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
    private function getMenuTree($control='',$userInfo){
        $not=$userInfo['fk_role_id']==1 && $this->config_info['IS_ROOT']===true;
        //检测权限
        if(!$not){
            $map[$this->config_info['KEY_ID']]=[$userInfo['menu_auth']];
        }
        $map['is_hidden']=0;
        $field=[$this->config_info['KEY_ID'],'pid','soft','name','control'];
        $rows=$this->model->table($this->config_info['TABLE']['menu'])->where(['AND'=>$map,'ORDER'=>"soft DESC"])->field($field)->select();
        $rows=$this->list_to_tree($rows,$this->config_info['KEY_ID']);
        if($control){
            $id=$this->model->table($this->config_info['TABLE']['menu'])->where(['control'=>$control])->field($this->config_info['KEY_ID'])->find();
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
    protected function showView($data=[],$file=''){
        //当前URL
        $data['currentUrl']=$this->current_url;
        //获取菜单树
        $data['navList']=$this->getNavList();
        //获取当前URL上级地址
        if ($this->current_menuinfo['pid']){
            $this->getTopUrl($this->current_menuinfo['pid']);
        }else {
            $this->current_nav = $this->current_menuinfo['control'];
        }
        $data['currentNav']=$this->current_nav;
        //获取菜单列表
        $data['menu_list']=$this->getMenuTree($this->current_nav,$this->user_info);
        //用户信息
        $data['userInfo']=$this->user_info;
        $this->assign($data);
        $this->display($file);
    }

}