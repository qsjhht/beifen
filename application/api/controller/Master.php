<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/21
 * Time: 17:49
 */

namespace app\api\controller;
use think\Controller;
use think\Db;

class Master extends Common
{
    protected function initialize()
    {
        parent::initialize();
        $this->Appkey = 'ZHENGDING';
        $this->Secret = 'zhengdingguanlang@2019!%';
        $this->Format = 'json';
        $this->Sign_method = 'md5';

        $this->equiptype = ['FF','FJ'];
        $this->alarmtype = ['2','3','5','10'];

        $this->area["XCA13"] = "BC-9F-C6";
        $this->area["XCB13"] = "BC-9F-C6";
        $this->area["XCA14"] = "BC-2D-9C";
        $this->area["XCB14"] = "BC-2D-9C";
        $this->area["XCA16"] = "BD-66-2B";
        $this->area["XCB16"] = "BD-66-2B";

//        $this->areacode = 'md5';
    }
    public function last_alarm()
    {
        $url = "http://10.1.3.18:90/monitorNewAlarmTimes";

        $this->params['app_key'] = $this->Appkey;
        $this->params['timestamp'] = date('Y-m-d H:i:s',time());
//        $this->params['timestamp'] = '2019-10-21 12:00:00';
        $this->params['format'] = $this->Format;
        $this->params['sign_method'] = $this->Sign_method;
        $callback = $this->asc_sort($this->params);
        $sign = strtoupper(md5($callback));
        $this->params['sign'] = $sign;
        //dump($this->params);


        $return = $this->get_send($this->params,$url);
        dump($return);
    }

    public function add_alarm()
    {
        $url = 'http://10.1.3.18:90/monitorAlarms';
        $this->params['app_key'] = $this->Appkey;
        $this->params['timestamp'] = date('Y-m-d H:i:s',time());
//        $this->params['timestamp'] = '2019-10-21 12:00:00';
        $this->params['format'] = $this->Format;
        $this->params['sign_method'] = $this->Sign_method;

        $this->params['alarmtime'] = date('Y-m-d H:i:s',time()-5);
        $this->params['alarmlevel'] = 4;

        $this->params['equiptype'] = $this->equiptype[array_rand($this->equiptype)];

        $this->params['alarmtype'] = $this->alarmtype[array_rand($this->alarmtype)];


        $this->params['areacode'] = array_rand($this->area);
        $this->params['cameracodes'] = $this->area[$this->params['areacode']];

        $callback = $this->asc_sort($this->params);
        $sign = strtoupper(md5($callback));
        $this->params['sign'] = $sign;

        $return = $this->post_send($this->params,$url);
        dump($return);
        unset($this->params['app_key']);
        unset($this->params['timestamp']);
        unset($this->params['format']);
        unset($this->params['sign_method']);
        unset($this->params['sign']);
        $this->params['alarmtime'] = strtotime($this->params['alarmtime']);
        Db('alarmsend')->insert($this->params);
    }

    public function asc_sort($params = array())
    {
        if (!empty($params)){
            $p = ksort($params);
            if($p){
                $str = $this->Secret;
                foreach ($params as $k => $val) {
                    $str .= $k . $val;
                }
                $strs = $str.$this->Secret;
                return $strs;
            }
        }
        return false;
    }

    public function post_send($arr,$url){

        /*$post_data = array(
            "params" => $arr,
        );*/

        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $arr);
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        $output = json_decode($return,true);
        return $return;
    }

    public function get_send($arr,$url){
        $url = $url .= '?';

        foreach ($arr as $key=>$value) {
            $url = $url .= $key . "=" . $value .htmlspecialchars('&');
            //dump($key);
        }
        $url = rtrim($url,htmlspecialchars('&'));

        //return $url;
        //echo $url;
        $ch = curl_init ();
        if(substr($url,0,5)=='https'){
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,true);
        }
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ( $ch, CURLOPT_URL, $url );
//        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );


        //curl_setopt ( $ch, CURLOPT_POSTFIELDS, $arr);
        //curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        $data = curl_exec ( $ch );
        //echo '</br>'.$data;
        curl_close ( $ch );
        //$output = json_decode($return,true);
        //return $output;
        return $data;
    }
}