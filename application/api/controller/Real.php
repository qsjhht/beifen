<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/18
 * Time: 16:33
 */

namespace app\api\controller;
use app\index\model\Realsql as RealsqlModel;
use app\index\model\Phone as PhoneModel;
use think\Controller;
use think\Db;
use think\Request;
use think\Validate;

class Real extends Common
{
    protected function initialize()
    {
        parent::initialize();
    }
    public function real_time()
    {
        $zone = $this->request->param('zone');

        //$last_time = Db::connect('yk_config')->table('history')->field('TIME')->order('TIME','desc')->limit(1)->find();
        /*$real =Db::connect('yk_config')
            ->table('history')
            ->where('TIME','=',function($query){
                $query->table('history')->field('TIME')->order('TIME','desc')->limit(1);
            })
            ->select();*/
        /*$realdata = [];
        $real = Db::connect('yk_config')->table('newtable')
            ->where('TIME','=',function($query){
                $query->table('newtable')->field('TIME')->order('TIME','desc')->limit(1);
            })
            ->field('NAME,VALUE')
            ->where('ZONE',$zone)
            ->select();
        foreach ($real as $value){
            $realdata[$value['NAME']] = $value['VALUE'];
        }*/
        $conn=odbc_connect('kinghistory','sa','sa');
        if (!$conn)
        {
            exit("连接失败: " . $conn);
        }
        $sql="SELECT * FROM realtime where TagName = 'M_".$zone."'OR TagName = 'T_".$zone."'OR TagName like 'LT_".$zone."___'OR TagName = 'CH4_".$zone."'OR TagName = 'H2S_".$zone."'OR TagName = 'O2_".$zone."'";
        $rs=odbc_exec($conn,$sql);

        if (!$rs)
        {
            exit("SQL 语句错误");
        }
        $yk_arr = array();
        while (odbc_fetch_row($rs))
        {
//            $name = substr(odbc_result($rs,"TagName"),-5);
            /* $compname=odbc_result($rs,"TagName");*/
            //$conname=odbc_result($rs,"DataTime");
//            if($name == $zone){
//            dump($sql);
//            dump(odbc_result($rs,"TagName"));
            // 传感器数据小于0时 赋值为0

            if(odbc_result($rs,"DataValue") < 0 || odbc_result($rs,"DataValue") > 9000){
                $yk_arr[odbc_result($rs,"TagName")] = 0;
            }else{
                if(strpos(odbc_result($rs,"TagName"),'LT') !== false){
                    $yk_arr[odbc_result($rs,"TagName")] = round(odbc_result($rs,"DataValue"),5) * 100;
                }else{
                    $yk_arr[odbc_result($rs,"TagName")] = round(odbc_result($rs,"DataValue"),3);
                }
            }
//            }
//            $yk_arr[$name]['TagName'] = $name;
//            $yk_arr[$name]['DataTime'] = odbc_result($rs,"DataTime");
//            $yk_arr[$name]['DataValue'] = odbc_result($rs,"DataValue");
//            echo "<tr><td>$compname</td>";
            //echo "<td>$conname</td></tr>";
//            $yk_arr[substr(odbc_result($rs,"TagName"),16)] = odbc_result($rs,"DataTime");
//            $yk_arr[odbc_result($rs,"DataTime")] =odbc_result($rs,"DataValue");

        }
        odbc_close($conn);
        /*dump($yk_arr);
        die;*/
        //dump($realdata);
        /*$return_data['code'] = 200;
        $return_data['msg']  = '查询成功！';
        $return_data['data'] = $real;*/
        echo json_encode($yk_arr,JSON_UNESCAPED_UNICODE);die;
    }
    public function real_times()
    {
        $zone = $this->request->param('zone');
        $name = $this->request->param('name');
        //$last_time = Db::connect('yk_config')->table('newtable')->field('TIME')->order('TIME','desc')->limit(1)->find();
        /*$real =Db::connect('yk_config')
            ->table('history')
            ->where('TIME','=',function($query){
                $query->table('history')->field('TIME')->order('TIME','desc')->limit(1);
            })
            ->select();*/
//        $realdata = [];
        /*$real = Db::connect('yk_config')->table('newtable')
            ->where('TIME','in',function($query){
                $query->table('newtable')->field('TIME')->order('TIME','desc')->limit(1000);
            })
            ->field('TIME,VALUE')
            ->where('ZONE',$zone)
           // ->where('NAME',$name)
            ->select();*/
//        $real_times = Db::connect('yk_config')
//            ->table('newtable')
//            ->field('TIME,VALUE')
//            ->order('TIME','esc')
//            ->where('NAME',$name)
//            ->limit(100)
//            ->select();
        //dump($last_time);
        /*$return_data['code'] = 200;
        $return_data['msg']  = '查询成功！';
        $return_data['data'] = $real;*/
//        foreach ($real_times as $value){
//            $realdata[$value['TIME']] = $value['VALUE'];
//        }
//        echo json_encode($realdata,JSON_UNESCAPED_UNICODE);die;


        $conn=odbc_connect('kinghistory','sa','sa');
        if (!$conn)
        {
            exit("连接失败: " . $conn);
        }
        $dateo = date("Y-m-d",strtotime("-1 day"));
        if($name == 'LT'){
            $pars ='LT_'.$zone.'___';  //区分 T LT
        }else{
            $pars = $name.'_'.$zone;  //区分 T LT
        }

        $sql="SELECT top 200 * FROM history where TagName like '".$pars."' and DataTime > '".$dateo."'  order by DataTime desc";

//        dump($sql);die;
        $rs=odbc_exec($conn,$sql);

        if (!$rs)
        {
            exit("SQL 语句错误");
        }

        $yk_arr = array();
        $num = 0;
        while (odbc_fetch_array($rs))
        {
//            $yk_arr[substr(odbc_result($rs,"TagName"),16)] =odbc_result($rs,"DataValue");
//            $yk_arr[] =  array(odbc_result($rs,"DataTime") => odbc_result($rs,"DataValue"));
            if(odbc_result($rs,"DataValue") < 9000){
                // 传感器数据小于0时 赋值为0
                if(odbc_result($rs,"DataValue") < 0){
                    $yk_arr[odbc_result($rs,"DataTime")] = 0;
                }else{
                    $yk_arr[odbc_result($rs,"DataTime")] = round(odbc_result($rs,"DataValue"),3);
                }
//                $yk_arr[odbc_result($rs,"DataTime")] = round(odbc_result($rs,"DataValue"),3);
            }
//            $name = substr(odbc_result($rs,"TagName"),-5);
            /* $compname=odbc_result($rs,"TagName");*/
            //$conname=odbc_result($rs,"DataTime");
//            if($name == $zone){
//            }
//            $yk_arr[$name]['TagName'] = $name;
//            $yk_arr[$name]['DataTime'] = odbc_result($rs,"DataTime");
//            $yk_arr[$name]['DataValue'] = odbc_result($rs,"DataValue");
//            echo "<tr><td>$compname</td>";
            //echo "<td>$conname</td></tr>";
//            $yk_arr[substr(odbc_result($rs,"TagName"),16)] = odbc_result($rs,"DataTime");
//            $yk_arr[odbc_result($rs,"DataTime")] =odbc_result($rs,"DataValue");
        }
        odbc_close($conn);
        /*dump($yk_arr);
        die;*/
        //dump($realdata);
        /*$return_data['code'] = 200;
        $return_data['msg']  = '查询成功！';
        $return_data['data'] = $real;*/
        echo json_encode(array_reverse($yk_arr) ,JSON_UNESCAPED_UNICODE);die;

    }

    public function real_timest()
    {
        $zone = $this->request->param('zone');
        $name = $this->request->param('name');
        //$last_time = Db::connect('yk_config')->table('newtable')->field('TIME')->order('TIME','desc')->limit(1)->find();
        /*$real =Db::connect('yk_config')
            ->table('history')
            ->where('TIME','=',function($query){
                $query->table('history')->field('TIME')->order('TIME','desc')->limit(1);
            })
            ->select();*/
//        $realdata = [];
        /*$real = Db::connect('yk_config')->table('newtable')
            ->where('TIME','in',function($query){
                $query->table('newtable')->field('TIME')->order('TIME','desc')->limit(1000);
            })
            ->field('TIME,VALUE')
            ->where('ZONE',$zone)
           // ->where('NAME',$name)
            ->select();*/
//        $real_times = Db::connect('yk_config')
//            ->table('newtable')
//            ->field('TIME,VALUE')
//            ->order('TIME','esc')
//            ->where('NAME',$name)
//            ->limit(100)
//            ->select();
        //dump($last_time);
        /*$return_data['code'] = 200;
        $return_data['msg']  = '查询成功！';
        $return_data['data'] = $real;*/
//        foreach ($real_times as $value){
//            $realdata[$value['TIME']] = $value['VALUE'];
//        }
//        echo json_encode($realdata,JSON_UNESCAPED_UNICODE);die;

        $pars = $name.'_'.$zone.'___';  //区分 T LT
        $conn=odbc_connect('kinghistory','sa','sa');
        if (!$conn)
        {
            exit("连接失败: " . $conn);
        }
        $dateo = date("Y-m-d",strtotime("-1 day"));
        $sql="SELECT top 200 * FROM history where TagName like '".$pars."' and DataTime > '".$dateo."' order by DataTime desc";
        $rs=odbc_exec($conn,$sql);

        if (!$rs)
        {
            exit("SQL 语句错误");
        }

        $yk_arr = array();
        $num = 0;
        while (odbc_fetch_array($rs))
        {
//            $yk_arr[substr(odbc_result($rs,"TagName"),16)] =odbc_result($rs,"DataValue");
//            $yk_arr[] =  array(odbc_result($rs,"DataTime") => odbc_result($rs,"DataValue"));
            if(odbc_result($rs,"DataValue") < 9000) {

                // 传感器数据小于0时 赋值为0
                if(odbc_result($rs,"DataValue") < 0){
                    $yk_arr[] = ['time' => odbc_result($rs, "DataTime"), 'data' => 0];
                }else{
                    $yk_arr[] = ['time' => odbc_result($rs, "DataTime"), 'data' => number_format(odbc_result($rs, "DataValue"), '3')];
                }
            }
//            $yk_arr[odbc_result($rs,"DataTime")] = odbc_result($rs,"DataValue");
//            $name = substr(odbc_result($rs,"TagName"),-5);
            /* $compname=odbc_result($rs,"TagName");*/
            //$conname=odbc_result($rs,"DataTime");
//            if($name == $zone){
//            }
//            $yk_arr[$name]['TagName'] = $name;
//            $yk_arr[$name]['DataTime'] = odbc_result($rs,"DataTime");
//            $yk_arr[$name]['DataValue'] = odbc_result($rs,"DataValue");
//            echo "<tr><td>$compname</td>";
            //echo "<td>$conname</td></tr>";
//            $yk_arr[substr(odbc_result($rs,"TagName"),16)] = odbc_result($rs,"DataTime");
//            $yk_arr[odbc_result($rs,"DataTime")] =odbc_result($rs,"DataValue");
        }
        odbc_close($conn);
        /*dump($yk_arr);
        die;*/
        //dump($realdata);
        /*$return_data['code'] = 200;
        $return_data['msg']  = '查询成功！';
        $return_data['data'] = $real;*/
        echo json_encode(array_reverse($yk_arr) ,JSON_UNESCAPED_UNICODE);die;

    }

    public function get_last_real()
    {
        $zone = $this->request->param('zone');
        $name = $this->request->param('name');
        $pars = $name.'_'.$zone; //区分 T LT
        $conn=odbc_connect('kinghistory','sa','sa');
        if (!$conn)
        {
            exit("连接失败: " . $conn);
        }
        $sql="SELECT * FROM realtime where TagName like '".$pars."'";
        $rs=odbc_exec($conn,$sql);

        if (!$rs)
        {
            exit("SQL 语句错误");
        }
        $yk_arr = array();
        while (odbc_fetch_row($rs))
        {
            if(odbc_result($rs,"DataValue") < 0){
                $yk_arr[odbc_result($rs,"DataTime")] = 0;
            }else{
                $yk_arr[odbc_result($rs,"DataTime")] =round(odbc_result($rs,"DataValue"),3);
            }

        }
        odbc_close($conn);
        /*dump($yk_arr);
        die;*/
        //dump($realdata);
        /*$return_data['code'] = 200;
        $return_data['msg']  = '查询成功！';
        $return_data['data'] = $real;*/
        echo json_encode($yk_arr,JSON_UNESCAPED_UNICODE);die;

//        $real_times = Db::connect('yk_config')
//            ->table('newtable')
//            ->field('TIME,VALUE')
//            ->order('TIME','desc')
//            ->where('NAME',$name)
//            ->find();
//        $realdata = [];
//        $realdata[$real_times['TIME']] = $real_times['VALUE'];
//        echo json_encode($realdata,JSON_UNESCAPED_UNICODE);die;
    }

    public function status_refresh()
    {
        return  'false';
    }

    public function call_ericsson()
    {

//        $msg = 'hello';
//        $handle = stream_socket_client("udp://127.0.0.1:22335", $errno, $errstr);
//        if( !$handle ){
//            die("ERROR: {$errno} - {$errstr}\n");
//        }
//        fwrite($handle, $msg."\n");
//        $result = fread($handle, 1024);
//        fclose($handle);

        //创建一个socket套接流
        $socket = socket_create(AF_INET,SOCK_DGRAM,SOL_UDP);
        /****************设置socket连接选项，这两个步骤你可以省略*************/
        //接收套接流的最大超时时间1秒，后面是微秒单位超时时间，设置为零，表示不管它
        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 1, "usec" => 0));
        //发送套接流的最大超时时间为6秒
        socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array("sec" => 1, "usec" => 0));
        /****************设置socket连接选项，这两个步骤你可以省略*************/

        //连接服务端的套接流，这一步就是使客户端与服务器端的套接流建立联系
        if(socket_connect($socket,'127.0.0.1',22334) == false){
            echo 'connect fail massege:'.socket_strerror(socket_last_error());
        }else{
            $message = 'call somebody Ericsson Ericsson Ericsson';
            //转为GBK编码，处理乱码问题，这要看你的编码情况而定，每个人的编码都不同
            $message = mb_convert_encoding($message,'GBK','UTF-8');
            //向服务端写入字符串信息

            if(socket_write($socket,$message,strlen($message)) == false){
                echo 'fail to write'.socket_strerror(socket_last_error());

            }else{
                echo 'client write success'.PHP_EOL;
                //读取服务端返回来的套接流信息
//                while($callback = socket_read($socket,1024)){
//                    echo 'server return message is:'.PHP_EOL.$callback;
//                }
            }
        }
        socket_close($socket);//工作完毕，关闭套接流


    }

    public function get_video_set()
    {
        $sets = db('video_set')
            ->select();
        echo json_encode($sets,JSON_UNESCAPED_UNICODE);die;
    }

    public function video_set()
    {
        $realsql = new RealsqlModel();
        $sets = $this->request->param();
        $arr = [];
        $arrs = [];
        foreach ($sets as $key=>$val) {
            $key = substr($key,2);
            $arr['id'] = $key;
            $arr['token'] = $val;
            $arrs[] = $arr;
        }
        $res = $realsql->saveAll($arrs);
        if($res){
            $this->return_msg(200, '更新成功！');
        }
//
    }


    public function get_phone()
    {
        $serverName  =  "192.168.20.50" ;
        $connectionInfo  = array(  "Database" => "RMDDISP" ,  "UID" => "sa" ,  "PWD" => "Qiushi123"  );
        $conn  =  sqlsrv_connect (  $serverName ,  $connectionInfo );
        if(  $conn  ===  false  ) {
            die(  print_r (  sqlsrv_errors (),  true ));
        }
        // print "SQL: $sql\n";
        $sqls='SELECT *  FROM 分机;';//拼接
        $sqls= iconv ( "utf-8", "gb2312//IGNORE", $sqls );//第二次转换，不可省略
        $results = sqlsrv_query($conn , $sqls);//正常运行未报错


        // $result = sqlsrv_query($conn, $sql);
        if($results === false) {
            $this->return_msg(400, '获取失败！');
            die(print_r(sqlsrv_errors(), true));
        }
        #Fetching Data by array
        while($rows = sqlsrv_fetch_array($results, SQLSRV_FETCH_ASSOC)) {
            // $row = iconv('gbk', 'utf-8', $row);
            foreach ($rows as $key => $value) {
                $keys = $this->doEncoding($key);
                $arrs[$keys] = $this->doEncoding($value);
                unset($arrs['唯一序号']);
                unset($arrs['分机号码2']);
                unset($arrs['分机号码3']);
                // unset($arrs['分机状态']);
            }
            $phone_datas[] = $arrs;
        }


        // $str=iconv ( "utf-8", "gb2312//IGNORE", $str ); //第一次转换，不可省略，省略立即报错
        $sql_g='SELECT *  FROM 组分机;';//拼接
        $sql_g= iconv ( "utf-8", "gb2312//IGNORE", $sql_g );//第二次转换，不可省略
        $result_g = sqlsrv_query($conn , $sql_g);//正常运行未报错


        // $result = sqlsrv_query($conn, $sql);
        if($result_g === false) {
            $this->return_msg(400, '获取失败！');
            die(print_r(sqlsrv_errors(), true));
        }
        #Fetching Data by array
        while($row = sqlsrv_fetch_array($result_g, SQLSRV_FETCH_ASSOC)) {
            // $row = iconv('gbk', 'utf-8', $row);
            foreach ($row as $key => $value) {
                $keys = $this->doEncoding($key);
                $arr[$keys] = $this->doEncoding($value);
            }
            $phone_group[] = $arr;
        }



        foreach($phone_group as $k=>$v){
            foreach ($v as $key => $value) {
                # code...
                //去除空格 数据清洗
                $vv[$key] = trim($value," ");
                unset($vv['序号']);

            }
            foreach ($phone_datas as $val) {
                # code...

                if($vv['分机号码'] == $val['分机号码']){
                    $vv['分机名称'] =  $val['分机名称'];
                    $vv['分机状态'] =  $val['分机状态'];
                }
            }
            $phone_groups[$vv['分组名称']][]    =   $vv;

        }
        $res['组分机'] = $phone_groups;
        $res['分机'] = $phone_datas;
        $this->return_msg(200, '获取成功！',$phone_groups);
    }

    public function call_phone()
    {
        $type = $this->request->param('type');
        $from_num = $this->request->param('from_num');
        $call = $this->request->param('call');
//        $this->Socket = new Socketmodel($this->scket_ip,$this->scket_port);
//        $aa = $this->doEncoding($aa);

        if ($from_num == 8001){
            $from_name = '调度台1';
        }else if($from_num == 8002){
            $from_name = '调度台2';
        }

        if ($type == '调度群呼'){
            $num_or_g = '分组';
        }else if($type == '调度单呼'){
            $num_or_g = '号码';
        }
        $msg =  $type.' 调度号码='.$from_num.' 调度名称='.$from_name.' '.$num_or_g.'='.$call.chr(13);
        $msgo =  $type.' 调度号码='.$from_num.' 调度名称='.$from_name.' '.$num_or_g.'='.$call;
//        dump($msg);
//        die;
        $msgs= iconv ( "utf-8", "gb2312//IGNORE", $msg );
        $fp = stream_socket_client("udp://192.168.20.50:2008", $errno, $errstr);
        if (!$fp) {
            echo "ERROR: $errno - $errstr<br />\n";
        } else {
            fwrite($fp, $msgs);
            $this->return_msg(200,'呼叫中',$msgo);
//            echo fread($fp, 1024);
            fclose($fp);
        }
    }

    //获取亚控工业实时库 水位 最大 最小 平均值
    public function get_s_logs()
    {
        $sensor = $this->request->param('sensor');
        $conn=odbc_connect('kinghistory','sa','sa');
        if (!$conn)
        {
            exit("连接失败: " . $conn);
        }
        //一天所有数据
        $count="SELECT * FROM realtime where TagName like '".$sensor."/_%' ESCAPE '/'";
        $num = odbc_num_rows (odbc_exec($conn,$count));
        $nums = $num * 60 * 24;
        $dateo = date("Y-m-d",strtotime("-1 day"));
        $sql="SELECT top $nums * FROM history where TagName like '".$sensor."/_%' ESCAPE '/' and DataTime > '".$dateo."' order by DataTime desc";
        $rs=odbc_exec($conn,$sql);

        if (!$rs)
        {
            exit("SQL 语句错误");
        }
        $water_arr = array();
        while (odbc_fetch_row($rs))
        {
//            dump(odbc_result($rs,"DataValue"));
            $datatime = date("Y-m-d H:i",strtotime(odbc_result($rs,"DataTime")));

            if(odbc_result($rs,"DataValue") < 0 || odbc_result($rs,"DataValue") > 9000){
                $water_arr[$datatime][] =0;
            }else{
                $water_arr[$datatime][] =round(odbc_result($rs,"DataValue"),3);
            }
//                $logs['l_max'][odbc_result($rs,"DataTime")] = max($water_arr[odbc_result($rs,"DataTime")]);
//                $logs['l_min'][odbc_result($rs,"DataTime")] = min($water_arr[odbc_result($rs,"DataTime")]);
//                $logs['l_avg'][odbc_result($rs,"DataTime")] = array_sum($water_arr[odbc_result($rs,"DataTime")])/count($water_arr[odbc_result($rs,"DataTime")]);
        }

//        dump($water_arr);die;
        foreach ($water_arr as $key =>$item) {
            $logs['l_max'][$key] = max($item);
            $logs['l_min'][$key] = min($item);
            $logs['l_avg'][$key] = round(array_sum($item)/count($item),3);
        }
        odbc_close($conn);
        foreach ($logs['l_max'] as $time => $l_max) {
            $l_arr['max'][] = array($time,$l_max);
        }
        foreach ($logs['l_min'] as $time => $l_max) {
            $l_arr['min'][] = array($time,$l_max);
        }
        foreach ($logs['l_avg'] as $time => $l_max) {
            $l_arr['avg'][] = array($time,$l_max);
        }
        $this->return_msg(200,'查询成功！',$l_arr);
    }

    //获取亚控工业实时库 指定传感器 最大 最小 平均值
    public function get_sensor()
    {
        $sensor = $this->request->param('sensor');
        $conn=odbc_connect('kinghistory','sa','sa');
        if (!$conn)
        {
            exit("连接失败: " . $conn);
        }
        $sql="SELECT * FROM realtime where TagName like '".$sensor."/_%' ESCAPE '/'";
        $rs=odbc_exec($conn,$sql);

        if (!$rs)
        {
            exit("SQL 语句错误");
        }
        $water_arr = array();
        while (odbc_fetch_row($rs))
        {

            if(odbc_result($rs,"DataValue") < 0 || odbc_result($rs,"DataValue") > 9000){
                $water_arr[] =0;
            }else{
                $water_arr[] =round(odbc_result($rs,"DataValue"),3);
                $date_time = date("Y-m-d H:i",strtotime(odbc_result($rs,"DataTime")));
            }
        }
        odbc_close($conn);

        $sensor_arr['s_max'] = array($date_time,max($water_arr));
        $sensor_arr['s_min'] = array($date_time,min($water_arr));
        $sensor_arr['s_avg'] = array($date_time,round(array_sum($water_arr)/count($water_arr),3));
        $this->return_msg(200,'查询成功！',$sensor_arr);
    }


    //获取风机/水泵 状态
    public function plc_get()
    {
        $this->soap = new \SoapClient("http://192.168.10.30/Service.asmx?WSDL");
        $zone = $this->request->param('zone');
        $plc = $this->request->param('plc');
        $pump_num = $this->request->param('pump_num');
        if($plc == 'fan'){
            $run = 'Run_Fan_'.$zone;
            $model = 'Manual_MODE_Fan_'.$zone;
            $msg = $zone.' 风机';
            $remote = 'Remote_Fan_'.$zone;
            $manual = 'Manual_MODE_Fan_'.$zone;
            $auto = 'Auto_MODE_Fan_'.$zone;
            $s_auto = 'Auto_CMD_Fan_'.$zone;
            $s_manual = 'Manual_CMD_Fan_'.$zone;
        }else if($plc == 'pump'){
            if(!$pump_num){
                $this->return_msg(400,'请传入正确plc参！');
            }
            $zone = $zone.'_'.$pump_num;
            $run = 'Run_Pump_'.$zone;
            $model = 'Manual_MODE_Pump_'.$zone;
            $msg = $zone.' 水泵';
            $remote = 'Remote_Pump_'.$zone;
            $manual = 'Manual_MODE_Pump_'.$zone;
            $auto = 'Auto_MODE_Pump_'.$zone;
            $s_auto = 'Auto_CMD_Pump_'.$zone;
            $s_manual = 'Manual_CMD_Pump_'.$zone;
        }

//        dump('远程就地状态为： '.$remote.'----------'.$this->soap->GetKsTagValue(array('cpTagName' => $remote))->GetKsTagValueResult);
//        dump('当前手动模式为：  '.$manual.'----------'.$this->soap->GetKsTagValue(array('cpTagName' => $manual))->GetKsTagValueResult);
//        dump('当前自动模式为：  '.$auto.'----------'.$this->soap->GetKsTagValue(array('cpTagName' => $auto))->GetKsTagValueResult);
//        dump('设置手动模式为：  '.$s_manual.'----------'.$this->soap->GetKsTagValue(array('cpTagName' => $s_manual))->GetKsTagValueResult);
//        dump('设置自动模式为：  '.$s_auto.'----------'.$this->soap->GetKsTagValue(array('cpTagName' => $s_auto))->GetKsTagValueResult);

        $run_res =  $this->soap->GetKsTagValue(array('cpTagName' => $run));
        $manual_res =  $this->soap->GetKsTagValue(array('cpTagName' => $manual));
        //运行状态 是否启动
        $data['is_run'] = strtolower($run_res->GetKsTagValueResult);
        //运行模式 是否手动
        $data['is_manual'] = strtolower($manual_res->GetKsTagValueResult);
        if($run_res->GetKsTagValueResult !== 'ERROR' || $manual_res->GetKsTagValueResult !== 'ERROR'){
            $this->return_msg(200,$msg.'状态获取成功！',$data);
        }
        $this->return_msg(400,$msg.'状态获取失败');
    }

    //设置风机/水泵 起停状态
    public function plc_set()
    {
        $this->soap = new \SoapClient("http://192.168.10.30/Service.asmx?WSDL");
        $zone = $this->request->param('zone');
        $status = $this->request->param('status');
        $plc = $this->request->param('plc');
        $pump_num = $this->request->param('pump_num');
        if($plc == 'fan'){
            $run = 'Run_Fan_'.$zone;
            $remote = 'Remote_Fan_'.$zone;
            $msg = $zone.' 风机修改状态成功！';
            $manual = 'Manual_MODE_Fan_'.$zone;
            $start = 'Start_CMD_Fan_'.$zone;
            $stop = 'Stop_CMD_Fan_'.$zone;
        }else if($plc == 'pump'){
            if(!$pump_num){
                $this->return_msg(400,'请传入正确plc参！');
            }
            $zone = $zone.'_'.$pump_num;
            $run = 'Run_Pump_'.$zone;
            $remote = 'Remote_Pump_'.$zone;
            $msg = $zone.' 水泵修改状态成功！';
            $manual = 'Manual_MODE_Pump_'.$zone;
            $start = 'Start_CMD_Pump_'.$zone;
            $stop = 'Stop_CMD_Pump_'.$zone;
        }else{
            $this->return_msg(400,'请传入正确plc参数！');
        }
        $remote_res =  $this->soap->GetKsTagValue(array('cpTagName' => $remote));
        $manual_res =  $this->soap->GetKsTagValue(array('cpTagName' => $manual));
        if($remote_res->GetKsTagValueResult == 'TRUE'){
            if($manual_res->GetKsTagValueResult == 'TRUE'){
//                dump($status);
//                dump($stop);
//                dump($start);
//                die;
                if($status == 'start'){
//                    $stop_res = $this->soap->SetKsTagValue(array('cpTagName' => $stop,'cpTagValue'=>0))->SetKsTagValueResult;
//                    dump($stop_res);
//                    die;
//                    if($stop_res == '0'){
//                    $stop_res = $this->soap->SetKsTagValue(array('cpTagName' => $stop,'cpTagValue'=>0))->SetKsTagValueResult;
//                    if($stop_res == '0'){
                    $start_res = $this->soap->SetKsTagValue(array('cpTagName' => $start,'cpTagValue'=>'1'))->SetKsTagValueResult;
                    sleep(2);
                    $start_res = $this->soap->SetKsTagValue(array('cpTagName' => $start,'cpTagValue'=>'0'))->SetKsTagValueResult;
                    if($start_res == '0'){
                        $this->return_msg(200,$msg);
                    }
                    $this->return_msg(400,'修改模式失败！');
//                    }
//                        $start_res = $this->soap->SetKsTagValue(array('cpTagName' => $start,'cpTagValue'=>1))->SetKsTagValueResult;
//                    }
                }else if($status == 'stop'){
//                    $start_res = $this->soap->SetKsTagValue(array('cpTagName' => $start,'cpTagValue'=>0))->SetKsTagValueResult;
//                    if($start_res == '0'){
//                        $auto_res = $this->soap->SetKsTagValue(array('cpTagName' => $auto,'cpTagValue'=>'1'))->SetKsTagValueResult;
                    $stop_res = $this->soap->SetKsTagValue(array('cpTagName' => $stop,'cpTagValue'=>'1'))->SetKsTagValueResult;
                    sleep(2);
                    $stop_res = $this->soap->SetKsTagValue(array('cpTagName' => $stop,'cpTagValue'=>'0'))->SetKsTagValueResult;
                    if($stop_res == '0'){
                        $this->return_msg(200,$msg);
                    }
                    $this->return_msg(400,'修改模式失败！');
//                    }
                }else{
                    $this->return_msg(400,'请传入正确model参数！');
                }

            }else{
                $this->return_msg(400,'请修改风机模式后再进行操作！');
            }
        }else{
            $this->return_msg(400,'请现场操作！');
        }
    }

    //设置风机/水泵 所处模式
    public function plc_model_set()
    {
        $this->soap = new \SoapClient("http://192.168.10.30/Service.asmx?WSDL");
        $zone = $this->request->param('zone');
        $model = $this->request->param('model');
        $plc = $this->request->param('plc');
        $pump_num = $this->request->param('pump_num');
        if($plc == 'fan'){
            $remote = 'Remote_Fan_'.$zone;
            $msg = $zone.' 风机模式修改成功！';
            $auto = 'Auto_CMD_Fan_'.$zone;
            $manual = 'Manual_CMD_Fan_'.$zone;
        }else if($plc == 'pump'){
            if(!$pump_num){
                $this->return_msg(400,'请传入正确plc参数！');
            }
            $zone = $zone.'_'.$pump_num;
            $remote = 'Remote_Pump_'.$zone;
            $msg = $zone.' 水泵模式修改成功！';
            $auto = 'Auto_CMD_Pump_'.$zone;
            $manual = 'Manual_CMD_Pump_'.$zone;
        }else{
            $this->return_msg(400,'请传入正确plc参数！');
        }
        //判断 远程/就地
        $remote_res =  $this->soap->GetKsTagValue(array('cpTagName' => $remote));
        if($remote_res->GetKsTagValueResult == 'TRUE'){
            if($model == 'manual'){
//                $auto_res = $this->soap->SetKsTagValue(array('cpTagName' => $auto,'cpTagValue'=>'0'))->SetKsTagValueResult;
//                if($auto_res == '0'){
                    $manual_res = $this->soap->SetKsTagValue(array('cpTagName' => $manual,'cpTagValue'=>'1'))->SetKsTagValueResult;
                    sleep(2);
                    $manual_res = $this->soap->SetKsTagValue(array('cpTagName' => $manual,'cpTagValue'=>'0'))->SetKsTagValueResult;
//                }
                if($manual_res == '0'){
                    $this->return_msg(200,$msg);
                }else{
                    $this->return_msg(400,'修改模式失败！');
                }
            }else if($model == 'auto'){
//                $manual_res = $this->soap->SetKsTagValue(array('cpTagName' => $manual,'cpTagValue'=>'0'))->SetKsTagValueResult;
//                if($manual_res == '0'){
                    $auto_res = $this->soap->SetKsTagValue(array('cpTagName' => $auto,'cpTagValue'=>'1'))->SetKsTagValueResult;
                    sleep(2);
                    $auto_res = $this->soap->SetKsTagValue(array('cpTagName' => $auto,'cpTagValue'=>'0'))->SetKsTagValueResult;
//                }
                if($auto_res == '0'){
                    $this->return_msg(200,$msg);
                }else{
                    $this->return_msg(400,'修改模式失败！');
                }
            }else{
                $this->return_msg(400,'请传入正确model参数！');
            }


        }else if($remote_res->GetKsTagValueResult == 'ERROR'){
            $this->return_msg(400,'请检查传感器！');
        }
        else{
            $this->return_msg(400,'请现场操作！');
        }
    }

    //获取电能数据 历史
    public function get_j_log()
    {
        $conn=odbc_connect('kinghistory','sa','sa');
        if (!$conn)
        {
            exit("连接失败: " . $conn);
        }
        //一天所有数据
        $count="SELECT * FROM realtime where TagName like 'j_%_AP' ESCAPE '/'";
        $num = odbc_num_rows (odbc_exec($conn,$count));

        $nums = $num * 60 * 24;
        $dateo = date("Y-m-d",strtotime("-1 day"));
        $sql="SELECT top $nums * FROM history where TagName like 'j_%_AP' ESCAPE '/' and DataTime > '".$dateo."' order by DataTime desc";
        $rs=odbc_exec($conn,$sql);

        if (!$rs)
        {
            exit("SQL 语句错误");
        }
        $item_arr = array();
        while (odbc_fetch_row($rs))
        {
//            dump(odbc_result($rs,"DataValue"));
            if (odbc_result($rs,"DataValue") < 9000){
                $datatime = date("Y-m-d H:i",strtotime(odbc_result($rs,"DataTime")));
                $item_arr[$datatime][odbc_result($rs,"tagname")] = round((float)odbc_result($rs,"DataValue"),3);
//                dump($item_arr);
//                $j_arr[$datatime][] =round(odbc_result($rs,"DataValue"),3);
//                $logs['l_max'][odbc_result($rs,"DataTime")] = max($water_arr[odbc_result($rs,"DataTime")]);
//                $logs['l_min'][odbc_result($rs,"DataTime")] = min($water_arr[odbc_result($rs,"DataTime")]);
//                $logs['l_avg'][odbc_result($rs,"DataTime")] = array_sum($water_arr[odbc_result($rs,"DataTime")])/count($water_arr[odbc_result($rs,"DataTime")]);
            }
        }
        foreach ($item_arr as $key=> $item) {
            $log_arr[] = array($key,array_sum($item));
        }
//        dump($log_arr);die;
        $this->return_msg(200,'查询成功！',$log_arr);
    }





    //获取电能数据 实时
    public function get_j_real()
    {
        $conn=odbc_connect('kinghistory','sa','sa');
        if (!$conn)
        {
            exit("连接失败: " . $conn);
        }
        $sqls = "SELECT CURRENT_TIMESTAMP";
        $sql_date = odbc_exec($conn,$sqls);
        if (!$sql_date)
        {
            exit("SQL 语句错误");
        }
        while(odbc_fetch_row($sql_date)){
            $datatime = date("Y-m-d H:i",strtotime(odbc_result($sql_date,1)));
//            $sql="SELECT * FROM realtime where TagName like 'j_%_AP' ESCAPE '/' and DataTime > DATEADD(MINUTE,-5,'".$datatime."')";
//            $rs=odbc_exec($conn,$sql);
        }
        $sql="SELECT * FROM realtime where TagName like 'j_%_AP' ESCAPE '/'";
        $rs=odbc_exec($conn,$sql);
        if (!$rs)
        {
            exit("SQL 语句错误");
        }
        $j_arr = array();
        $j = 0;
        while (odbc_fetch_row($rs))
        {
//            $water_arr[$datatime][] =round(odbc_result($rs,"DataValue"),3);
//            dump($datatime);
            $j += (float)odbc_result($rs,"DataValue");
        }
        $j_arr[] = [$datatime,round($j,3)];
        odbc_close($conn);
        $this->return_msg(200,'查询成功！',$j_arr);
    }
}