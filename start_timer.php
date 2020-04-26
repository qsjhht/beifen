<?php
use \Workerman\Worker;
use \Workerman\Lib\Timer;
use \Workerman\Connection\AsyncTcpConnection;
require_once __DIR__ . '/vendor/Workerman/Autoloader.php';
date_default_timezone_set('PRC'); 
class Mail
{
    //将类单例化
    //创建静态私有的变量保存该类对象
    static private $instance;
    //防止直接创建对象
    private function __construct()
    {
        $this->res();
    }
    //防止克隆对象
    private function __clone()
    {
    }
    static public function getInstance(){
        //判断$instance是否是Uni的对象
        //没有则创建
        if(!self::$instance instanceof self) 
        {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public $is_time;    //存储 id、time的关联数组
    public $exec_last_time;     //存储上次执行exec时间  保证一分钟内不执行多次
    public $dbhost = 'localhost:3306';  // mysql服务器主机地址
    public $dbuser = 'root';            // mysql用户名
    public $dbpass = 'root';          // mysql用户名密码
    public $dbname = 'cms';         // mysql数据库名
    public $list = 'yzn_timer';     //mysql 表名

    // 注意，回调函数属性必须是public timer的回调函数  定期执行
    public function exec()
    {
        if(strtotime("-1 minute") > $this->exec_last_time)
        {
            foreach ($this->is_time as $key => $value) {
                if(date('H:i',time()) == $value) {
                   $this->carry($key);
                }
            }
        }
    }
    //查库 进行判断
    public function res()
    {
        $conn = mysqli_connect($this->dbhost, $this->dbuser, $this->dbpass);
        if(! $conn )
        {
            die('Could not connect: ' . mysqli_error());
        }
        // 设置编码，防止中文乱码
        mysqli_query($conn , "set names utf8");
        $sql = 'SELECT * FROM '.$this->list.' where status = 1';
        mysqli_select_db( $conn, $this->dbname );
        $retval = mysqli_query( $conn, $sql );

        if(! $retval )
        {
            die('无法读取数据: ' . mysqli_error($conn));
        }
        while($row = mysqli_fetch_assoc($retval))
        {
            $time = substr($row['time'],0,5);
            $this->is_time[$row['id']] = $time;
        }
        // 释放结果集
        mysqli_free_result($retval);
        mysqli_close($conn);
    }
    //执行操作
    public function carry($id)
    {
        $socket = array();
        $str = '';
        $conn = mysqli_connect($this->dbhost, $this->dbuser, $this->dbpass);
        if(! $conn )
        {
            die('Could not connect: ' . mysqli_error());
        }
        // 设置编码，防止中文乱码
        mysqli_query($conn , "set names utf8");
        mysqli_select_db( $conn, $this->dbname );
        $sql = 'SELECT a.group_id,a.do,b.lamp_ids FROM '.$this->list.' as a join yzn_eqpt_management as b on a.group_id = b.id where a.id = '.$id;
        $retval = mysqli_query( $conn, $sql );

        if(! $retval )
        {
            die('无法读取数据: ' . mysqli_error($conn));
        }
        //$row=mysqli_fetch_assoc($retval);
        //printf ("%s : %s)",$row["group_id"],$row["do"]);
        while($row = mysqli_fetch_assoc($retval))
        {
            $lamp_ids = trim($row['lamp_ids'],',');
            $sql_lamps = 'SELECT lamp_num FROM yzn_lamp WHERE id in ('.$lamp_ids.')';
            $retval_lamps = mysqli_query( $conn, $sql_lamps );
            $contents = [];
            $contents['cmd'] = 'set';
            while($row_lamps = mysqli_fetch_assoc($retval_lamps))
            {
                $contents['devs'][] = $row_lamps['lamp_num'];
            }
            $contents['status'] = (bool)$row['do'];
            $contents = json_encode($contents);
            $tcp = new AsyncTcpConnection('tcp://192.168.5.15:9888');
            $tcp->connect();
            $tcp->send($contents);
            $sql_last_time = 'UPDATE '.$this->list.' SET last_time = '.time().' WHERE id = '.$id;
            $sql_change_sattus = 'UPDATE yzn_lamp SET lamp_status = '.$row['do'].' WHERE id in ('.$lamp_ids.')';
            $tcp->send($sql_change_sattus);
            mysqli_query( $conn, $sql_last_time );
            mysqli_query( $conn, $sql_change_sattus );
            $this->exec_last_time = time();
        }
        // 释放结果集
        mysqli_free_result($retval);
        mysqli_close($conn);
    }

    public function sendLater()
    {
        // 回调的方法属于当前的类，则回调数组第一个元素为$this
        Timer::add(10, array($this, 'exec'), array());
    }
}

$task = new Worker('tcp://0.0.0.0:9999');
$task->count = 2;
$task->onWorkerStart = function($task)
{
    $mail = Mail::getInstance();
    $mail->sendLater();
};
$task->onMessage = function($connection, $data)
{
    $mail = Mail::getInstance();
    $mail->res();
    $connection->send('success');
    $connection->close();
};

// 运行worker
Worker::runAll();