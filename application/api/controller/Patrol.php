<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/29
 * Time: 11:40
 */
namespace app\api\controller;
use think\Db;
use think\Request;

class Patrol extends Common
{
    private $host;
    private $url;
    protected function initialize()
    {
        parent::initialize();
        $this->host = 'http://192.168.5.112:8088';
        $this->url = $this->host .= "/RMD_PipeGallery/rmdBaseEquipmentController.do?getPositionByUserId";
        $this->scket_ip = '192.168.5.111';
        $this->scket_port = '22333';
    }
    //推送web socket
    public function demo()
    {
        $type = $this->request->param('type');
        $from_num = $this->request->param('from_num');
        $from_name = $this->request->param('from_name');
        $call = $this->request->param('call');
//        $this->Socket = new Socketmodel($this->scket_ip,$this->scket_port);
//        $aa = $this->doEncoding($aa);

        if ($type == '调度群呼'){
            $num_or_g = '分组';
        }else if($type == '调度单呼'){
            $num_or_g = '号码';
        }
        $msg =  $type.' 调度号码='.$from_num.' 调度名称='.$from_name.' '.$num_or_g.'='.$call.'chr(13)';
        dump($msg);
        die;
        $fp = stream_socket_client("udp://192.168.5.111:2008", $errno, $errstr);
        if (!$fp) {
            echo "ERROR: $errno - $errstr<br />\n";
        } else {
            fwrite($fp, $aa);
//            echo fread($fp, 1024);
            fclose($fp);
        }

//        $list =  "{'flag':'0','routeList':[[114.61465703896,38.122961340611],[114.61520860247,38.123817756155],[114.61568633017,38.12446819936],[114.61656090764,38.125681293144],[114.61700370428,38.126299713278],[114.61764550076,38.127086436083],[114.61823535865,38.127803112471],[114.61903252434,38.128773641566],[114.61979984919,38.129715052192],[114.62022925348,38.130382011419],[114.62039191148,38.130636573698],[114.6207409421,38.131473818194]]}";

        //$return_data['code'] = 200;
        //$datas = json_encode();
        //$to_uid = "";
        // 推送的url地址，使用自己的服务器地址
//        $this->send_msg($list);

    }

    //接收 巡检开始 信息 insert or delete
    public function data()
    {
//        $data = input('post.');
//        $data = input('post.');
        $data = $this->params;
//        dump($data);die;
       /*$data['header']['accept'] =  $this->request->header('accept');
       $data['header']['accept-encoding'] =  $this->request->header('accept-encoding');
       $data['header']['user-agent'] =  $this->request->header('user-agent');*/

        //$datas =Db::connect('sqlsrv_config')->table('rmd_base_equipment')->where('equipment_code','01120204007')->find();


        //$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
//        unset($data['/patrol/data']);
        $txt =  json_encode($data,JSON_UNESCAPED_UNICODE);

        $reload['reload'] = true;
        $this->send_msg($data,'222');
        $this->send_msg($reload,'2333');
        /*$url = 'http://192.168.0.243:9001';
        $type['type'] = '1';
        $type = json_encode($type);
        $this->post_send($type,$url);*/
        //$txt = str_replace('"',"'",$txt);
        //$txt = '{"flag":"1","qs_u_ids":"3","equipmentCodes":"05010201002,05010202042,05020201001,05020202042,05030201001,05030202042,05040201001,05050201001,05050202042,05060201001,05060202022,05070201001","routeList":[[114.61465703896,38.122961340611],[114.61520860247,38.123817756155],[114.61568633017,38.12446819936],[114.61656090764,38.125681293144],[114.61700370428,38.126299713278],[114.61764550076,38.127086436083],[114.61823535865,38.127803112471],[114.61903252434,38.128773641566],[114.61979984919,38.129715052192],[114.62022925348,38.130382011419],[114.62039191148,38.130636573698],[114.6207409421,38.131473818194]],"checkRecordInfo":{"checkType":"廊内巡更","bpmStatus":"否","preventFireArea":"防火区1-7","checkStartTime":"2019-08-31 10:18:44","startPoint":"园博园大街东","checkContent":"","cabin":"电仓","checkCycle":"30","equipmentType":"新城B01,新城B02,新城B03,新城B04,新城B05,新城B06,新城B07,新城B08,新城B09,新城B10,新城B11,新城B12","checkCode":"201908271811033","routeName":"新城大道巡检1","limitTime":"5","checkUser":"国富","endpoint":"园博园大街西","checkEndTime":"","street":"园博园","name":"8月新城巡检"}}';
        //巡检开始时调用接口
        //开始句柄 flag=1
        if ($data['flag']){
            $arr['checkCode'] = $data['checkRecordInfo']['checkCode'];
            $arr['json'] = $txt;
            Db('is_patrol')->insert($arr);
            $this->return_msg(200, '巡检开始！');
        }else{
            //结束句柄 flag=0
            $res = Db('is_patrol')->where('checkCode',$data['checkCode'])->delete();
            if($res){
                $this->return_msg(200, '巡检结束！');
            }
            $this->return_msg(200, '无该任务信息！');
        }

        /*$return_data['routeList'] =  $data['routeList'];
        $return_data['checkRecordInfo'] =  $data['checkRecordInfo'];*/




        // 推送的url地址，使用自己的服务器地址


        //file_put_contents("test.txt", $txt, FILE_APPEND);
        //fwrite($myfile, $txt);
        //fclose($myfile);



    }

    //配置 dotnet 地址端口配置 中冶BIM用
    public function get_dotnet()
    {
        //$patrol = Db('is_patrol')->select();
//        $patrol = Db('is_patrol')->limit(1)->order('id','desc')->field('checkCode,json')->find();
//
//        $patrol = str_replace('"',"'",$patrol['json']);
        /* if ($patrol){
             $patrol['flag'] = '1';
             return json_encode($patrol,JSON_UNESCAPED_UNICODE);
         }
         $patrol['flag'] = '0';*/
        $dotnet['ip']= '192.168.5.100:9005';
//        if($patrol){
            return json_encode($dotnet,JSON_UNESCAPED_UNICODE);
//        }
//        $this->return_msg(200, '无巡检数据！');

    }

    //获取已经开始的巡检任务信息
    public function get_patrol()
    {
        //$patrol = Db('is_patrol')->select();
        $patrol = Db('is_patrol')->limit(1)->order('id','desc')->field('checkCode,json')->find();

        $patrol = str_replace('"',"'",$patrol['json']);
       /* if ($patrol){
            $patrol['flag'] = '1';
            return json_encode($patrol,JSON_UNESCAPED_UNICODE);
        }
        $patrol['flag'] = '0';*/
       if($patrol){
           return json_encode($patrol,JSON_UNESCAPED_UNICODE);
       }
       $this->return_msg(200, '无巡检数据！');

    }
    //webgl 获取单个巡检信息
    public function get_patrol_json()
    {
        $patrol = Db('is_patrol')->limit(1)->order('id','desc')->field('json')->find();
        if($patrol){
            $json = str_replace("'",'"',$patrol['json']);
            $json = json_decode($json,true);
            unset($json['routeList']);
            $json = json_encode($json,JSON_UNESCAPED_UNICODE);
            //$json = str_replace("'",'"',$patrol['json']);
            return $json;
        }
        $this->return_msg(200, '无巡检数据！');
        //$this->return_msg(200, '巡检开始！',$json);

    }
    //获取设备信息 for 中冶
    public function eqpt_data()
    {
        $code = $this->request->param('eqpt_id');
        $datas =Db::connect('sqlsrv_config')->table('rmd_base_equipment')->field('equipment_code,equipment_name,catalogue_value,equipment_xh,equipment_jszt,brand,prevent_fire_area,prevent_fire_area_id')->where('equipment_code',$code)->find();

        $site_id =Db::connect('mysql_config')->table('qs_site')->field('parentid')->where('id',$datas['prevent_fire_area_id'])->find();
        $site_name =Db::connect('mysql_config')->table('qs_site')->field('site_name')->where('id',$site_id['parentid'])->find();

        if($datas){
            $datas['location'] = $site_name['site_name'] .'-' .$datas['prevent_fire_area'];
            //$datas['catalogue_type'] = $datas['catalogue_value'];
            foreach ($datas as $key=>$value) {
                if ($key == 'catalogue_value'){
                    $datas['catalogue_type'] = $datas[$key];
                }
            }
        }
        unset($datas['prevent_fire_area_id']);
        unset($datas['catalogue_value']);
        unset($datas['prevent_fire_area']);
        $return_data['code'] = 200;
        $return_data['msg']  = '查询成功！';
        $return_data['data'] = $datas;
        echo json_encode($return_data,JSON_UNESCAPED_UNICODE);die;
    }

    //获取设备信息 for GIS 获取水电舱位置
    public function gis_data()
    {
        $code = $this->request->param('cate_code');
        $zone = $this->request->param('zone');
        $zone_name = explode('/',$zone);

        $datas =Db::connect('sqlsrv_config')
            ->table('rmd_base_equipment')
            ->field('equipment_code,equipment_name,catalogue_value,equipment_xh,equipment_jszt,catalogue_value,brand,prevent_fire_area,prevent_fire_area_id')
            ->where('catalogue',$code)
            ->where('prevent_fire_area',$zone_name[0])
            ->find();
        $site_id =Db::connect('mysql_config')->table('qs_sites')->field('parentid')->where('site_name',$zone)->find();

        $site_name =Db::connect('mysql_config')->table('qs_sites')->field('site_name')->where('id',$site_id['parentid'])->find();
        if($datas){
            $datas['location'] = $site_name['site_name'] .'-' .$datas['prevent_fire_area'];
        }
        //$datas['catalogue_type'] = $datas['catalogue_value'];
//        foreach ($datas as $key=>$value) {
//            if ($key == 'catalogue_value'){
//                $datas['catalogue_type'] = $datas[$key];
//            }
//        }
        unset($datas['prevent_fire_area_id']);
//        unset($datas['catalogue_value']);
        unset($datas['prevent_fire_area']);
        $this->return_msg(200,'查询成功！',$datas);
    }

    public function get_user_position()
    {
        $user_id = $this->request->get('check_user_id');

        $arr['userId'] = $user_id;
        $arr['u_id'] = '1';
        $arr = json_encode($arr);
        $res = $this->post_send($arr,$this->url);
        return $res;
    }

    //获取设备类型
    public function get_type()
    {
        $type_id = $this->request->param('type_id');
        $cate_name = Db('category')->field('cate_name')->where('id',$type_id)->find();
        $return_data['code'] = 200;
        $return_data['msg']  = '查询成功！';
        $return_data['data'] = $cate_name['cate_name'];
        echo json_encode($return_data,JSON_UNESCAPED_UNICODE);die;
    }

    //发送消息
    protected function send_msg($content,$to_uid='2333'){
        $content = json_encode($content,JSON_UNESCAPED_UNICODE);
        $content =  str_replace('"',"'",$content);
        $push_api_url = "http://192.168.5.100:2121/";
        $post_data = array(
            "type" => "publish",
            "content" => $content,
            "to" => $to_uid,
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
/*        var_export($return);*/
    }

    public function post_send($arr,$url){

        $post_data = array(
            "params" => $arr,
        );
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        $output = json_decode($return,true);
        return $return;
    }

    //巡查巡检记录
    public function patrol_logs()
    {
        $limit = $this->request->param('limit/d', 10);
        $page = $this->request->param('page/d', 1);
        $datas = Db::connect('sqlsrv_config')
            ->table('check_record_ending')
            ->order('check_start_time','desc')
            ->page($page, $limit)
            ->select();
        echo json_encode($datas,JSON_UNESCAPED_UNICODE);die;
    }

    //待巡查巡检任务
    public function patrols()
    {
        $limit = $this->request->param('limit/d', 10);
        $page = $this->request->param('page/d', 1);
        $datas = Db::connect('sqlsrv_config')
            ->table('check_record_not_Start')
            ->page($page, $limit)
            ->select();
        echo json_encode($datas,JSON_UNESCAPED_UNICODE);die;
    }

    //正在巡检的任务
    public function patroling()
    {
        $limit = $this->request->param('limit/d', 10);
        $page = $this->request->param('page/d', 1);
        $datas = Db::connect('sqlsrv_config')
            ->table('check_record_starting')
            ->order('check_start_time','desc')
            ->page($page, $limit)
            ->select();
        echo json_encode($datas,JSON_UNESCAPED_UNICODE);die;
    }

    public function patrol_reload()
    {
        $patrol_logs_limit = $this->request->param('patrol_logs_limit/d', 10);
        $patrol_logs_page = $this->request->param('patrol_logs_page/d', 1);
        $patrol_logs = Db::connect('sqlsrv_config')
            ->table('check_record_ending')
            ->order('check_start_time','desc')
            ->page($patrol_logs_page, $patrol_logs_limit)
            ->select();
        $patrols_limit = $this->request->param('patrols_limit/d', 10);
        $patrols_page = $this->request->param('patrols_page/d', 1);
        $patrols = Db::connect('sqlsrv_config')
            ->table('check_record_not_Start')
            ->page($patrols_page, $patrols_limit)
            ->select();
        $patroling_limit = $this->request->param('patroling_limit/d', 10);
        $patroling_page = $this->request->param('patroling_page/d', 1);
        $patroling = Db::connect('sqlsrv_config')
            ->table('check_record_starting')
            ->order('check_start_time','desc')
            ->page($patroling_page, $patroling_limit)
            ->select();

        if($patrol_logs || $patrols || $patroling){
            $data['patrol_logs'] = $patrol_logs;
            $data['patrols'] = $patrols;
            $data['patroling'] = $patroling;
            $this->return_msg(200, '查询成功！',$data);
        }
        $this->return_msg(400, '查询失败！',$data);

    }
}