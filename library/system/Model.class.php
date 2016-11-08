<?php
namespace system;
class Model{

    //Model对象
    private static $model=null;

    //Model配置信息
    private static $config=[];

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
        $config=in_array(\system\Conf::get('DB_TYPE','','Database.php'),['mysql','mssql','sqlite'])?\system\Conf::get('DB_TYPE','','Database.php'):'mysql';
        if($config=='sqlite'){
            self::$config=[
                'database_type' => $config,
                'database_file' => \system\Conf::get('DB_FILE','','Database.php'),
            ];
        }
        else{
            self::$config=[
                'database_type' => $config,
                'database_name' => \system\Conf::get('DB_NAME','','Database.php'),
                'server' => \system\Conf::get('DB_HOST','','Database.php'),
                'username' => \system\Conf::get('DB_USER','','Database.php'),
                'password' => \system\Conf::get('DB_PWD','','Database.php'),
                'charset' => \system\Conf::get('DB_CHARSET','','Database.php'),
                'port' => \system\Conf::get('DB_PORT','','Database.php'),
                'prefix' => \system\Conf::get('DB_PREFIX','','Database.php')==''?'':\system\Conf::get('DB_PREFIX','','Database.php'),
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
    public function clean(){
        $this->__construct(true);
    }

    /**
     * 设置查询表名
     * @param $table 表名
     * @return $this
     */
    public function table($table){
        $this->table=$table;
        return $this;
    }

    /**
     * 设置查询条件
     * @param $where 查询条件
     * @return $this
     */
    public function where($where){
        $this->where=$where;
        return $this;
    }

    /**
     * 设置数据源
     * @param array $data 数据
     * @return $this
     */
    public function data($data){
        $this->data=$data;
        return $this;
    }

    /**
     * 设置查询的字段
     * @param array $field 字段
     * @return $this
     */
    public function field($field){
        $this->field=$field;
        return $this;
    }

    /**
     * 分页
     * @param $page 当前页数
     * @param $rows 每页显示数量
     * @return $this
     */
    public function page($page,$rows){
        self::$model->page($page,$rows);
        return $this;
    }

    /**
     * 插入数据
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function insert($not=true){
        $this->datas=self::$model->insert($this->table,$this->data);
        self::error();
        $this->flushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->fachsql();
        }
    }

    /**
     * 查询一条数据
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function find($not=true){
        $field=$this->field==[]?'*':$this->field;
        if(is_array($this->table)){
            $this->datas=self::$model->selects($this->table,$field,$this->where,true);
        }
        else {
            $this->datas = self::$model->get($this->table, $field, $this->where);
        }
        self::error();
        $this->flushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->fachsql();
        }
    }

    /**
     * 查询所有数据
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function select($not=true){
        $field=$this->field==[]?'*':$this->field;
        if(is_array($this->table)){
            $this->datas=self::$model->selects($this->table,$field,$this->where,false);
        }
        else{
            $this->datas=self::$model->select($this->table,$field,$this->where);
        }
        self::error();
        $this->flushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->fachsql();
        }
    }

    /**
     * 修改数据
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function update($not=true){
        $this->datas=self::$model->update($this->table,$this->data,$this->where);
        self::error();
        $this->flushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->fachsql();
        }
    }

    /**
     * 删除数据
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function delete($not=true){
        $this->datas=self::$model->delete($this->table,$this->where);
        self::error();
        $this->flushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->fachsql();
        }
    }

    /**
     * 统计数据
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function count($not=true){
        $field=$this->field==[]?'*':$this->field;
        $this->datas=self::$model->count($this->table,$field,$this->where);
        self::error();
        $this->flushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->fachsql();
        }
    }

    /**
     * 判断数据是否存在
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function is_exist($not=true){
        $this->datas=self::$model->has($this->table,$this->where);
        self::error();
        $this->flushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->fachsql();
        }
    }

    /**
     * 获得某个列中的值最大的
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function max($not=true){
        $field=$this->field==[]?'*':$this->field;
        $this->datas=self::$model->max($this->table,$field,$this->where);
        self::error();
        $this->flushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->fachsql();
        }
    }

    /**
     * 获得某个列中的最小的值
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function min($not=true){
        $field=$this->field==[]?'*':$this->field;
        $this->datas=self::$model->min($this->table,$field,$this->where);
        self::error();
        $this->flushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->fachsql();
        }
    }

    /**
     * 获得某个列字段的平均值
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function avg($not=true){
        $field=$this->field==[]?'*':$this->field;
        $this->datas=self::$model->avg($this->table,$field,$this->where);
        self::error();
        $this->flushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->fachsql();
        }
    }

    /**
     * 某个列字段相加
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function sum($not=true){
        $field=$this->field==[]?'*':$this->field;
        $this->datas=self::$model->sum($this->table,$field,$this->where);
        self::error();
        $this->flushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->fachsql();
        }
    }

    /**
     * 将新的数据替换旧的数据
     * @param $search 查找的值
     * @param $replace 替换的值
     * @param bool $not true返回执行后的数据false返回执行的sql
     * @return bool|string
     */
    public function replace($search,$replace,$not=true){
        $this->datas=self::$model->replace($this->table,$this->field,$search,$replace,$this->where);
        self::error();
        $this->flushAll();
        if($not){
            return $this->datas;
        }
        else{
            return $this->fachsql();
        }
    }

    /**
     * 执行SQL语句
     * @param $sql SQL语句
     * @return bool|string
     */
    public function exec($sql){
        $this->datas=self::$model->exec($sql);
        self::error();
        return $this->datas;
    }

    /**
     * 查询SQL语句
     * @param $sql SQL语句
     * @return bool|string
     */
    public function query($sql){
        $this->datas=self::$model->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        self::error();
        return $this->datas;
    }

    /**
     * 返回当前执行的SQL语句
     * @return string
     */
    public function fachsql(){
        return self::$model->last_query();
    }

    /**
     *SQL错误捕获
     */
    public function error(){
        $error=self::$model->error();
        if($error[1]!==null){
            $message='SQL错误码：'.$error[1].'错误代码：'.$error[2].'SQL语句：'.$this->fachsql();
            if(\system\Conf::get('DB_DEBUG','','Database.php')){
                $log=new \system\Log('File',['type'=>'sql']);
                $log->write($message);
            }
            \system\Error::thrown('SQL错误码:'.$error[1].'  '.$error[2]);
        }
    }

    /**
     * 返回数据库版本信息
     */
    public function version(){
        $data=self::$model->info();
        return $data['version'];
    }

    /**
     * 清空当前查询数据
     */
    private function flushAll(){
        $this->where=[];
        $this->field=[];
        $this->data=[];
    }

}