<?php
/**
 * Framework:Z-PHP
 * license:MIT
 * Author:Albert Zhan(http://www.5lazy.cn)
 */
namespace system;
class Model{

    //Model对象
    private static $model=null;

    //Model配置信息
    private static $config=[];

    // 自动验证定义
    protected $validate=[];

    //自动完成定义
    protected $auto=[];

    //错误信息
    protected $error='';

    //PDO对象实例
    public $pdo;

    //存放数据集合
    private $datas;

    //存放数据
    private $data=[];

    //存放查询条件
    private $where=[];

    //存放查询的字段
    private $field=[];

    //存放操作表名
    private $table;

    public function __construct($resetting=false){
        $config=in_array(\system\Conf::Get('DB_TYPE','','Database.php'),['mysql','mssql','sqlite'])?\system\Conf::Get('DB_TYPE','','Database.php'):'mysql';
        if($config=='sqlite'){
            self::$config=[
                'database_type' => $config,
                'database_file' => \system\Conf::Get('DB_FILE','','Database.php'),
            ];
        }
        else{
            self::$config=[
                'database_type' => $config,
                'database_name' => \system\Conf::Get('DB_NAME','','Database.php'),
                'server' => \system\Conf::Get('DB_HOST','','Database.php'),
                'username' => \system\Conf::Get('DB_USER','','Database.php'),
                'password' => \system\Conf::Get('DB_PWD','','Database.php'),
                'charset' => \system\Conf::Get('DB_CHARSET','','Database.php'),
                'port' => \system\Conf::Get('DB_PORT','','Database.php'),
                'prefix' => \system\Conf::Get('DB_PREFIX','','Database.php')==''?'':\system\Conf::Get('DB_PREFIX','','Database.php'),
            ];
        }
        $resetting===false?self::$model===null && self::$model=new \medoo(self::$config):self::$model=new \medoo(self::$config);
        $this->pdo=self::$model->pdo;
    }

    /**
     * 切换数据库
     * @param $config 配置信息
     * @return $this
     */
    public function init($config){
        if(self::$config!==$config){
            self::$model=new \medoo($config);
            return $this;
        }
        else{
            return $this;
        }
    }

    /**
     * 清除数据库配置信息
     */
    public function Clean(){
        $this->__construct(true);
    }

    /**
     * 设置查询表名
     * @param $table 表名
     * @return $this
     */
    public function Table($table){
        $this->table=$table;
        return $this;
    }

    /**
     * 设置查询条件
     * @param $where 查询条件
     * @return $this
     */
    public function Where($where){
        $this->where=$where;
        return $this;
    }

    /**
     * 设置数据源
     * @param array $data 数据
     * @return $this
     */
    public function Data($data){
        $this->data=array_merge($this->data,$data);
        return $this;
    }

    /**
     * 设置查询的字段
     * @param array $field 字段
     * @return $this
     */
    public function Field($field){
        $this->field=$field;
        return $this;
    }

    /**
     * 分页
     * @param $page 当前页数
     * @param $rows 每页显示数量
     * @return $this
     */
    public function Page($page,$rows){
        self::$model->page($page,$rows);
        return $this;
    }

    /**
     * 插入数据
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function Insert($not=true){
        $this->datas=self::$model->insert($this->table,$this->data);
        $this->Error();
        $this->FlushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->Fachsql();
        }
    }

    /**
     * 查询一条数据
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function Find($not=true){
        $field=$this->field==[]?'*':$this->field;
        if(is_array($this->table)){
            $this->datas=self::$model->selects($this->table,$field,$this->where,true);
        }
        else {
            $this->datas = self::$model->get($this->table, $field, $this->where);
        }
        $this->Error();
        $this->FlushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->Fachsql();
        }
    }

    /**
     * 查询所有数据
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function Select($not=true){
        $field=$this->field==[]?'*':$this->field;
        if(is_array($this->table)){
            $this->datas=self::$model->selects($this->table,$field,$this->where,false);
        }
        else{
            $this->datas=self::$model->select($this->table,$field,$this->where);
        }
        $this->Error();
        $this->FlushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->Fachsql();
        }
    }

    /**
     * 修改数据
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function Update($not=true){
        $this->datas=self::$model->update($this->table,$this->data,$this->where);
        $this->Error();
        $this->FlushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->Fachsql();
        }
    }

    /**
     * 删除数据
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function Delete($not=true){
        $this->datas=self::$model->delete($this->table,$this->where);
        $this->Error();
        $this->FlushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->Fachsql();
        }
    }

    /**
     * 统计数据
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function Count($not=true){
        $field=$this->field==[]?'*':$this->field;
        $this->datas=self::$model->count($this->table,$field,$this->where);
        $this->Error();
        $this->FlushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->Fachsql();
        }
    }

    /**
     * 判断数据是否存在
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function IsExist($not=true){
        $this->datas=self::$model->has($this->table,$this->where);
        $this->Error();
        $this->FlushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->Fachsql();
        }
    }

    /**
     * 获得某个列中的值最大的
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function Max($not=true){
        $field=$this->field==[]?'*':$this->field;
        $this->datas=self::$model->max($this->table,$field,$this->where);
        $this->Error();
        $this->FlushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->Fachsql();
        }
    }

    /**
     * 获得某个列中的最小的值
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function Min($not=true){
        $field=$this->field==[]?'*':$this->field;
        $this->datas=self::$model->min($this->table,$field,$this->where);
        $this->Error();
        $this->FlushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->Fachsql();
        }
    }

    /**
     * 获得某个列字段的平均值
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function Avg($not=true){
        $field=$this->field==[]?'*':$this->field;
        $this->datas=self::$model->avg($this->table,$field,$this->where);
        $this->Error();
        $this->FlushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->Fachsql();
        }
    }

    /**
     * 某个列字段相加
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function Sum($not=true){
        $field=$this->field==[]?'*':$this->field;
        $this->datas=self::$model->sum($this->table,$field,$this->where);
        $this->Error();
        $this->FlushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->Fachsql();
        }
    }

    /**
     * 将新的数据替换旧的数据
     * @param $search 查找的值
     * @param $replace 替换的值
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function Replace($search,$replace,$not=true){
        $this->datas=self::$model->replace($this->table,$this->field,$search,$replace,$this->where);
        $this->Error();
        $this->FlushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->Fachsql();
        }
    }

    /**
     * 执行SQL语句
     * @param $sql SQL语句
     * @return bool|string
     */
    public function Exec($sql){
        $this->datas=self::$model->exec($sql);
        $this->Error();
        return $this->datas;
    }

    /**
     * 查询SQL语句
     * @param $sql SQL语句
     * @return bool|string
     */
    public function Query($sql){
        $this->datas=self::$model->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        $this->Error();
        return $this->datas;
    }

    /**
     * 动态设置自动验证规则
     * @param $validate 验证规则
     * @return $this
     */
    public function Validate($validate){
        $this->auto[]=$validate;
        return $this;
    }

    /**
     * 自动验证规则处理
     * @param $data 自动验证规则
     */
    private function ValidateOperation($data){

    }

    /**
     * 动态设置自动完成规则
     * @param $auto 自动完成规则
     * @return $this
     */
    public function Auto($auto){
        $this->auto[]=$auto;
        return $this;
    }

    /**
     * 自动完成规则处理
     * @param $data 自动完成规则
     * @return array
     */
    private function AutoOperation($data){
        $rows=[];
        foreach($data as $k=>$v){
            $type=isset($v[3])?$v[3]:'string';
            switch($type){
                case 'string':
                    $rows[$v[0]]=$v[1];
                break;
                case 'function':
                    $rows[$v[0]]=is_array($v[1])?call_user_func_array($v[2],$v[1]):call_user_func($v[2],$v[1]);
                break;
                case 'callback':
                    $rows[$v[0]]=is_array($v[1])?call_user_func_array($v[2],$v[1]):call_user_func($v[2],$v[1]);
                break;
            }
        }
        $this->auto=[];
        return $rows;
    }

    /**
     * 创建数据对象
     *
     * @return bool
     */
    public function Create(){
        if(!empty($this->auto)){
            $auto=$this->AutoOperation($this->auto);
            $this->data=array_merge($this->data,$auto);
        }
        if(!empty($this->validate)){

        }
        return true;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function GetError(){
        return $this->error;
    }

    /**
     * 返回当前执行的SQL语句
     * @return string
     */
    private function Fachsql(){
        return self::$model->last_query();
    }

    /**
     *SQL错误捕获
     */
    private function Error(){
        $error=self::$model->error();
        if($error[1]!==null){
            $message='SQL错误码：'.$error[1].'错误代码：'.$error[2].'SQL语句：'.$this->Fachsql();
            if(\system\Conf::Get('DB_DEBUG','','Database.php')){
                $log=new \system\Log('File',['type'=>'sql']);
                $log->Write($message);
            }
            \system\Error::Thrown('SQL错误码:'.$error[1].'  '.$error[2]);
        }
    }

    /**
     * 返回数据库版本信息
     */
    public function Version(){
        $data=self::$model->info();
        return $data['version'];
    }

    /**
     * 清空当前查询数据
     */
    private function FlushAll(){
        $this->where=[];
        $this->field=[];
        $this->data=[];
    }

}