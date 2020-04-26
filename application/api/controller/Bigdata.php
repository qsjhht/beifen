<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/21
 * Time: 15:03
 */

namespace app\api\controller;
use think\Controller;
use think\Db;
use app\index\model\Patrol as PatrolModel;

class Bigdata extends Common
{
    protected function initialize()
    {
        parent::initialize();
        $this->Patrol = new PatrolModel;

        $this->secret_arr = array(
            'HKCJ' => 'zhengdingguanlang'   //浩坤沉降
            ,'RYRQ' => 'zhengdingguanlang'  //人员入侵
            ,'MJ' => 'zhengdingguanlang'    //门禁
            ,'ZHZM' => 'zhengdingguanlang'  //智慧照明
            ,'YK' => 'zhengdingguanlang'    //亚控
        );
    }

    //获取日期等
    public function date()
    {


     $this->start_date = Db('st_d_time')->limit(1)->field('start_time')->find();
    //         $start_time = strtotime();
     //$this->date_start = new \DateTime($this->start_date['start_time']);
     //$days = $this->start_date['start_time']->diff($this->new_date)->days;
     $days = floor(abs((time() - $this->start_date['start_time']) / 86400));
     $num=str_pad($days,4,"0",STR_PAD_LEFT);

    $range = db('work_plan')->field('time_range')->select();
    foreach ($range as $ra) {
        $j = date("H:i:s");

        $h = strtotime($j);

        $time = explode(' - ', $ra['time_range']);


        $z = strtotime($time['0']);//获得指定分钟时间戳，00:00
        if($time['1'] == '00:00:00'){
            $time['1'] = '24:00:00';
        }
        $x = strtotime($time['1']);//获得指定分钟时间戳，00:29
        $mtimes = '';
        if ($h > $z && $h < $x) {
            $mtimes = $z;
            $arr['per'] = round((time() - $z)/($x - $z)*100,2)."%";
            $arr['stime'] = date('H:i',$z);
            $arr['etime'] = date('H:i',$x);
        }
    }
        // 查询 打卡的运维组长
        $ids = Db('admin')->field('userid,username')->where('roleid','3')->select();
        $idss = '';
        foreach ($ids as $id) {
            $idss .= $id['userid'].',';
        }
        $idss = rtrim($idss,',');

        $check = db('attendance')
            ->alias('a')
            ->join('admin u','a.u_id = u.userid')
            ->field('u.nickname')
            ->where('u_id','in',$idss)
            ->where('m_time', 'between time', [$mtimes - 3600, $mtimes + 3600])
            ->find();

        if($check){
            $arr['nickname'] = $check['nickname'];
        }else{
            $arr['nickname'] = '';
        }


     $arr['units'] = substr($num,0,1);
     $arr['tens'] = substr($num,1,1);
     $arr['hundreds'] = substr($num,2,1);
     $arr['thousands'] = substr($num,3,1);
     $txt =  json_encode($arr,JSON_UNESCAPED_UNICODE);
     echo $txt;
    }

    //获取首页相关信息  百分比 开始 结束 运行时间 值班人数
    public function get_data()
    {
        $this->start_date = Db('st_d_time')->limit(1)->field('start_time')->find();
        //         $start_time = strtotime();
        //$this->date_start = new \DateTime($this->start_date['start_time']);
        //$days = $this->start_date['start_time']->diff($this->new_date)->days;
        $days = floor(abs((time() - $this->start_date['start_time']) / 86400));
        $num=str_pad($days,4,"0",STR_PAD_LEFT);

        $range = db('work_plan')->field('time_range')->select();
        $mtimes = '';
        foreach ($range as $ra) {
            $j = date("H:i:s");

            $h = strtotime($j);

            $time = explode(' - ', $ra['time_range']);


            $z = strtotime($time['0']);//获得指定分钟时间戳，00:00

            $x = strtotime($time['1']);//获得指定分钟时间戳，00:29

            if ($h > $z && $h < $x) {
                $arr['per'] = round((time() - $z)/($x - $z)*100,2)."%";
                $mtimes = $z;
                $arr['stime'] = date('H:i',$z);
                $arr['etime'] = date('H:i',$x);
                if($arr['etime'] == '00:00'){
                    $arr['etime'] = '24:00';
                }
            }

        }
        // 查询 打卡的运维组长
        $ids = Db('admin')->field('userid,username')->where('roleid','3')->select();
        $idss = '';
        foreach ($ids as $id) {
            $idss .= $id['userid'].',';
        }
        $idss = rtrim($idss,',');

        $check = db('attendance')
            ->alias('a')
            ->join('admin u','a.u_id = u.userid')
            ->field('u.nickname')
            ->where('u_id','in',$idss)
            ->where('m_time', 'between time', [$mtimes - 3600, $mtimes + 3600])
            ->find();

        if($check){
            $arr['nickname'] = $check['nickname'];
        }else{
            $checks = db('attendance')
                ->alias('a')
                ->join('admin u','a.u_id = u.userid')
                ->field('u.nickname')
//                ->where('u_id','in',$idss)
                ->where('m_time', 'between time', [$mtimes - 3600, $mtimes + 3600])
                ->find();
            if ($checks){
                $arr['nickname'] = '*'.$checks['nickname'];
            }else{
                $arr['nickname'] = '';
            }
        }
//        $person_num = db('attendance')->where('m_time', 'between time', [$mtimes - 3600, $mtimes + 3600])->count();
        $arr['date_num'] = $num;
        $arr['person_num'] = '15';
        if(!$range){
            $this->return_msg(400, '获取失败!','');
        }
        $this->return_msg(200, '获取成功!',$arr);
    }

    //获取防火分区数据
    public function get_sites()
    {
        $sites_data = $this->Patrol->getSites();
        if(!$sites_data){
            $this->return_msg(400, '获取失败!','');
        }
        $this->return_msg(200, '获取成功!',$sites_data);
    }

    //获取防火分区数据
    public function get_sites_hk()
    {
        $sites_data = $this->Patrol->getSites_hk();
        if(!$sites_data){
            $this->return_msg(400, '获取失败!','');
        }
        $this->return_msg(200, '获取成功!',$sites_data);
    }

    //获取企业信息
    public function enter_datas()
    {
        $res   = db('enterprise')->field('co_id,co_name,co_linker,co_phone')->order('co_id', 'desc')->select();
        if(!$res){
            $this->return_msg(400, '获取失败!','');
        }
        $this->return_msg(200, '获取成功!',$res);
    }

    //获取企业详细信息
    public function enter_details()
    {
        $co_id = $this->request->param('co_id');

        $field = "e.co_id,e.co_name,e.co_linker,e.co_local,e.co_linker_post,e.en_start,e.en_end,e.co_legal,e.co_phone,e.en_remark,e.en_long,e.en_time,t.type_name,u.nickname";
        $join  = [['co_type t', 'e.co_type = t.co_type_id', 'LEFT'],['admin u', 'e.user_id = u.userid', 'LEFT']];
        $res   = db('enterprise')->alias('e')->where('co_id',$co_id)->field($field)->join($join)->find();

        $res['en_time'] = date('Y-H-d H:i:s',$res['en_time']);

        $starts = trim($res['en_start'],'-');
        $startarr = explode('-',$starts);
        $startone = db('sites')->where('id',$startarr['1'])->field('site_name')->find();
        $starttwo = db('sites')->where('id',$startarr['2'])->field('site_name')->find();
        $res['en_start'] = $startone['site_name'] . $starttwo['site_name'];

        $ends = trim($res['en_end'],'-');
        $endarr = explode('-',$ends);
        $endone = db('sites')->where('id',$endarr['1'])->field('site_name')->find();
        $endtwo = db('sites')->where('id',$endarr['2'])->field('site_name')->find();
        $res['en_end'] = $endone['site_name'] . $endtwo['site_name'];
        if(!$res){
            $this->return_msg(400, '获取失败!','');
        }
        $this->return_msg(200, '获取成功!',$res);
    }

    //实时报警上传接口  供子系统调用
    public function Real_alarm()
    {
//        $this->params['app_key'] = $this->request->param('app_key');
//        $this->params['format'] = $this->request->param('format');
//        $this->params['sign'] = $this->request->param('sign');
//        $this->params['cate_code'] = $this->request->param('cate_code');
//        $this->params['time'] = $this->request->param('time');
//        $this->params['alarm_type'] = $this->request->param('alarm_type');
//        $this->params['alarm_code'] = $this->request->param('alarm_code');
//        $this->params['alarm_manage'] = $this->request->param('alarm_manage');
//        $this->params['alarm_site'] = $this->request->param('alarm_site');

        $signs = $this->params['sign']; //提前转储 sign
//        dump($this->params);
        unset($this->params['sign']);   //生成sign前剔除 sign参数
        unset($this->params['bigdata']);   //生成sign前剔除 sign参数
        $callback = $this->asc_sort($this->params); //生成sign


        $sign = strtoupper(md5($callback)); //sign对比
//        $this->params['sign'];
//        dump('----------------------');
//        dump($callback);
//        dump('----------------------');
//        dump($sign);
//        dump('----------------------');
//        dump($signs);
//        dump($this->params);
        if($this->params['cate_code'] == '117'){
            $this->return_msg(200, '实时报警上传成功!', '');
        }else{
        if ($sign !== $signs){
            $this->return_msg(400, '参数错误!','');
        }

        if($this->params['alarm_manage'] == '3001' || $this->params['alarm_manage'] == '3005'){
            $this->params['is_manage'] = '0';
        }else{
            $this->params['is_manage'] = '1';
        }
        if(is_string($this->params['alarm_time'])){
            $this->params['alarm_time'] = strtotime($this->params['alarm_time']);
        }

        $lvl = db('categorys')->field('alarm_lvl')->where('id',$this->params['cate_code'])->find();
        $this->params['alarmlevel'] = $lvl['alarm_lvl'];

        $res_id = Db('alarmrecs')->strict(false)->insertGetId($this->params);
        if($res_id){

            $real_alarm = db('alarmrecs')
                ->alias('a')
                ->join('alarmlvl l','a.alarmlevel = l.level','LEFT')
                ->join('categorys c','a.cate_code = c.id','LEFT')
                ->join('alarmtype t','a.alarm_type = t.type','LEFT')
                ->field('a.id,a.alarm_time,t.typecontent,a.cate_code,c.cate_name,a.eqpt_site,a.is_manage,l.lvlcontent,a.rec_details,a.remark,a.zone')
                ->where('a.id',$res_id)
                ->find();
//dump($real_alarm);
//dump($res_id);

            $alarms = db('alarmrecs')
                ->alias('a')
                ->join('alarmlvl l','a.alarmlevel = l.level','LEFT')
                ->join('categorys c','a.cate_code = c.id','LEFT')
                ->field('a.id,a.alarm_time,c.cate_name,a.zone,l.lvlcontent,a.eqpt_site')
                ->limit(10)
                ->order('alarm_time desc,id desc')
                ->select();

            $alarmdata['alarms'] = $alarms;

            $nums = db('alarmrecs')
                ->alias('a')
                ->join('alarmlvl l','a.alarmlevel = l.level')
                ->field('l.lvlcontent,count(a.id) count')
                ->group('alarmlevel')
                ->select();
            $cates = db('alarmlvl')->field('lvlcontent')->select();
            foreach ($nums as $num) {
                $numarr[] = $num['lvlcontent'];
                $numdata[$num['lvlcontent']] = $num['count'];
            }
            // 判断赋值  生成二维数组
            foreach ($cates as $cate) {
                $arr = [];
                if(in_array($cate['lvlcontent'],$numarr)){
                    $arr['name'] = $cate['lvlcontent'];
                    $arr['value'] = $numdata[$cate['lvlcontent']];

                }else{
                    $arr['name'] = $cate['lvlcontent'];
                    $arr['value'] = 0;
                }
                $alarmnum[] = $arr;
            }
            $alarmdata['alarmnum'] = $alarmnum;
            $alarmdata['real_alarm'] = $real_alarm;
//            $alarms = db('alarmrecs')->field('id,alarm_time,cate_code,alarmlevel')->limit(10)->order('alarm_time desc,id desc')->select();
            $this->send_ws($alarmdata);
            $this->return_msg(200, '实时报警上传成功!', $alarmdata);
        }
        $this->return_msg(400, '实时报警上传失败!', '');
        }
//        dump($callback);
//
    }

    public function alarms_list()
    {
        $limit = $this->request->param('limit/d', 10);
        $page = $this->request->param('page/d', 1);
        $alarms = db('alarmrecs')
            ->alias('a')
            ->join('alarmlvl l','a.alarmlevel = l.level','LEFT')
            ->join('categorys c','a.cate_code = c.id','LEFT')
            ->field('a.id,a.alarm_time,a.cate_code,c.cate_name,a.eqpt_site,a.zone,l.lvlcontent')
            ->page($page, $limit)
            ->order('alarm_time desc')
            ->select();
        $this->return_msg(200,'报警列表获取成功！',$alarms);
    }

    public function alarms_chart()
    {
        $nums = db('alarmrecs')
            ->alias('a')
            ->join('alarmlvl l','a.alarmlevel = l.level')
            ->field('l.lvlcontent,count(a.id) count')
            ->group('alarmlevel')
            ->select();
        $cates = db('alarmlvl')->field('lvlcontent')->select();
        foreach ($nums as $num) {
            $numarr[] = $num['lvlcontent'];
            $numdata[$num['lvlcontent']] = $num['count'];
        }
        // 判断赋值  生成二维数组
        foreach ($cates as $cate) {
            $arr = [];
            if(in_array($cate['lvlcontent'],$numarr)){
                $arr['name'] = $cate['lvlcontent'];
                $arr['value'] = $numdata[$cate['lvlcontent']];

            }else{
                $arr['name'] = $cate['lvlcontent'];
                $arr['value'] = 0;
            }
            $alarmnum[] = $arr;
        }
        $this->return_msg(200,'报警图表数据获取成功！',$alarmnum);
    }

    //直接提供apk版本号
      /*
      版本格式：主版本号.次版本号.修订号，版本号递增规则如下：
        主版本号：当你做了不兼容的 API 修改，
        次版本号：当你做了向下兼容的功能性新增，
        修订号：当你做了向下兼容的问题修正。*/
    public function get_apk_v()
    {
        $version = ['version'=>'1.0.0'];
        $this->return_msg(200,'apk版本号获取成功！',$version);
    }

    //获取指定版本号apk
    public function get_apk_by_v()
    {
        $v = $this->request->param('version');
        //$download =  new \think\response\Download($this->apk);
        //return $download->name('kid-ward.apk');
        // 或者使用助手函数完成相同的功能
        // download是系统封装的一个助手函数
        $apk_name = 'innowis-'.$v.'.apk';
        $this->apk = ROOT_PATH.'qrcode/'.$apk_name;

        return download($this->apk, $apk_name);
    }

    public function send_ws($content){

        // 返回信息并终止脚本
        $content = json_encode($content,JSON_UNESCAPED_UNICODE);
        $content =  str_replace('"',"'",$content);
        // 推送的url地址，使用自己的服务器地址
        $push_api_url = "http://192.168.5.100:2121/";
        $post_data = array(
            "type" => "publish",
            "content" => $content,
            "to" => '2333',
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


    public function asc_sort($params = array())
    {
        if (!empty($params)){
            $p = ksort($params);
            if($p){
                $str = $this->secret_arr[$this->params['app_key']];
                foreach ($params as $k => $val) {
                    $str .= $k . $val;
                }
                $strs = $str.$this->secret_arr[$this->params['app_key']];
                return $strs;
            }
        }
        return false;
    }


}