<?php
use Workerman\Worker;
use Workerman\Connection\AsyncUdpConnection;
use Workerman\Lib\Timer;
require_once __DIR__ . '/vendor/workerman/workerman/Autoloader.php';

class Send
{
    //创建静态私有变量保存单例
    static private $instance;
    //防止直接创建对象
    private function __construct(){}
    //单例类
    static public function getInstance(){
        //判断$instance是否是Uni的对象
        //没有则创建
        if(!self::$instance instanceof self)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function ws($data)
    {
        $content = json_encode($data,JSON_UNESCAPED_UNICODE);
        $content =  str_replace('"',"'",$content);
        $push_api_url = "http://192.168.5.100:2121/";
        $post_data = array(
            "type" => "publish",
            "content" => $content,
            "to" => '',
        );
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $push_api_url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        $return = curl_exec ( $ch );
        curl_close ( $ch );
    }

    public function doEncoding($str){
        $encode = strtoupper(mb_detect_encoding($str, ["ASCII",'UTF-8',"GB2312","GBK",'BIG5']));
        if($encode!='UTF-8'){
            $str = mb_convert_encoding($str, 'UTF-8', $encode);
        }
        return $str;
    }

}

$worker = new Worker('udp://0.0.0.0:2019');
$worker->count = 4;
$worker->onWorkerStart = function(){
    // 1秒后启动一个udp客户端，连接1234端口并发送字符串 hi
    echo "task run\n";
//    $udp_connection2 = new AsyncUdpConnection('udp://192.168.5.111:2008');
//    $udp_connection2->onConnect = function($udp_connection){
//        $udp_connection->send('hi');
//    };
//    $udp_connection2->onMessage = function($udp_connection, $data){
//        $send = Send::getInstance();
//        // 收到服务端返回的数据 hello
//        echo "recv $data\r\n";

//        $vvv = explode(' ',$data);
//        $status = $send->doEncoding($vvv[0]);
//        $msg['category'] = $status;
//        unset($vvv[0]);
//        foreach ($vvv as $value) {
//            $value = $send->doEncoding($value);
//            $val = explode('=',$value);
//            $val[1] = rtrim($val[1],'\n');
//            $msg[$val[0]] = $val[1];
//        }
////        $msgs = json_encode($msg,JSON_UNESCAPED_UNICODE);
////        $msgs =  str_replace('"',"'",$msgs);
////        print_r(json_last_error());
////        $arr = explode(' ',$data);
////        $udp_connection->send($msg);
////        $udp_connection->send($msgs);
//
////        $data = iconv('gb2312//IGNORE','utf8',$data);
////        $datas = $send->doEncoding($msg);
//        $send->ws($msg);
////    $sql_g= iconv ( "utf-8", "gb2312//IGNORE", $sql_g );//第二次转换，不可省略
//        // 关闭连接
////        $udp_connection->close();
//    };
//    $udp_connection2->connect();


//
//        $udp_connection = new AsyncUdpConnection('udp://192.168.5.111:2008');
//        $udp_connection->onConnect = function($udp_connection){
//            $udp_connection->send('start');
//        };
//
////        $time_interval = 2.5;
////        Timer::add($time_interval, function($udp_connection)
////        {
////            $udp_connection->send('start');
////            echo "task run\n";
////        });
//
//        $udp_connection->onMessage = function($udp_connection, $data){
//            // 收到服务端返回的数据 hello
//
////            $send = Send::getInstance();
////            $send->ws($data);
////            $content = file_get_contents("http://www.new.com/real/status_refresh");
////            if($content == 'true'){
//            echo "recv $data\r\n";
//            $send = Send::getInstance();
//            $send->ws($data);
//
//
//
////                echo "recv $content\r\n";
////            }else{
////                echo "recv $content\r\n";
////            }
//            // 关闭连接
////            $udp_connection->send("recv:".$data.$content);
//        };
//        $udp_connection->connect();

};
$worker->onMessage = function($connection, $data)
{
    // 收到AsyncUdpConnection客户端发来的数据，返回字符串 hello
    // echo "recv $data\r\n";
    // $udp_connection->send("hello");
    $send = Send::getInstance();
    // 收到服务端返回的数据 hello
    echo "recv $data\r\n";

    $vvv = explode(' ',$data);
    if($vvv[0] == '号码更新'){
        $msg = $vvv[0];
    }else{
        $status = $send->doEncoding($vvv[0]);
        $msg['category'] = $status;
        unset($vvv[0]);
        foreach ($vvv as $value) {
            $value = $send->doEncoding($value);
            $val = explode('=',$value);
            $val[1] = rtrim($val[1],'\n');
            $msg[$val[0]] = $val[1];
        }
    }
    $send->ws($msg);
    $connection->close();
};
Worker::runAll();