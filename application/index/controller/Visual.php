<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/29
 * Time: 9:43
 */
namespace app\index\controller;

use think\Controller;
use think\Db;
use app\index\model\Visuals as Visual_Model;
use app\common\controller\Adminbase;

class Visual extends Adminbase
{
    public function index()
    {
//        $res   = db('patrol_log')->order('log_date', 'desc')->field('log_date,p_time,user_ids,str_name')->limit('10')->select();
//        if($res){
//            foreach ($res as &$re) {
//                $re['user_names'] = '';
//                $userstr = trim($re['user_ids'],',');
//                $userarr = explode(',',$userstr);
//                $users = db('admin')->field('nickname')->where('userid','in',$userarr)->select();
//                foreach ($users as $vo) {
//                    $re['user_names'] .= $vo['nickname'].',';
//                }
//                $re['user_names'] = trim($re['user_names'],',');
//                $re['date_time'] = $re['log_date'] .' '. $re['p_time'];
//            }
//            $this->assign('Patrols', $res);
//        }else{
//            $this->assign('Patrols', '');
//        }
        $this->assign('Onpatrol', '');

        $alarms = db('alarmrecs')
            ->alias('a')
            ->join('alarmlvl l','a.alarmlevel = l.level','LEFT')
            ->join('categorys c','a.cate_code = c.id','LEFT')
            ->field('a.id,a.alarm_time,c.cate_name,a.zone,l.lvlcontent,a.eqpt_site')
            ->limit(10)
            ->order('alarm_time desc,id desc')
            ->select();
        $this->assign('Alarms', $alarms);

        $videos = db('video_set')
            ->select();
        foreach ($videos as $video) {
            $sets[$video['html_id']] = $video['token'];
        }
        $videos = json_encode($sets,JSON_UNESCAPED_UNICODE);
        $this->assign('Videos', $videos);

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

        $alarmnum = json_encode($alarmnum,256);
        $this->assign('Alarmnum', $alarmnum);

//        $patrol_logs = Db::connect('sqlsrv_config')
//            ->table('check_record_ending')
//            ->order('check_start_time','desc')
//            ->select();
////        foreach ($patrol_logs as &$patrol_log) {
////            $patrol_log['check_end_time'] = substr($patrol_log['check_end_time'],11);
////        }
//        $this->assign('Patrol_logs', $patrol_logs);
//
//        $patrols = Db::connect('sqlsrv_config')
//            ->table('check_record_not_Start')
//            ->select();
//        $this->assign('Patrols', $patrols);
//
//        $patroling = Db::connect('sqlsrv_config')
//            ->table('check_record_starting')
//            ->order('check_start_time','desc')
//            ->select();
//        $this->assign('Patroling', $patroling);
        return $this->fetch();
    }

    public function map()
    {
        return $this->fetch();
    }
    public function bim()
    {
        return $this->fetch();
    }
    public function gis()
    {
        return $this->fetch();
    }
    public function demo()
    {
        return $this->fetch();
    }
    public function demoOne()
    {
        $alarms = db('alarmrecs')
            ->alias('a')
            ->join('alarmlvl l','a.alarmlevel = l.level','LEFT')
            ->join('categorys c','a.cate_code = c.id','LEFT')
            ->field('a.id,a.alarm_time,c.cate_name,a.eqpt_site,l.lvlcontent')
            ->limit(10)
            ->order('alarm_time desc,id desc')
            ->select();
        $this->assign('Alarms', $alarms);

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

        $alarmnum = json_encode($alarmnum,256);
        $this->assign('Alarmnum', $alarmnum);

        return $this->fetch();
    }
    public function demoTwo()
    {
        return $this->fetch();
    }
    public function demoThree()
    {
//        $res   = db('patrol_log')->order('log_date', 'desc')->field('log_date,p_time,user_ids,str_name')->limit('10')->select();
//        if($res){
//            foreach ($res as &$re) {
//                $re['user_names'] = '';
//                $userstr = trim($re['user_ids'],',');
//                $userarr = explode(',',$userstr);
//                $users = db('admin')->field('nickname')->where('userid','in',$userarr)->select();
//                foreach ($users as $vo) {
//                    $re['user_names'] .= $vo['nickname'].',';
//                }
//                $re['user_names'] = trim($re['user_names'],',');
//                $re['date_time'] = $re['log_date'] .' '. $re['p_time'];
//            }
//            $this->assign('Patrols', $res);
//        }else{
//            $this->assign('Patrols', '');
//        }
//        $this->assign('Onpatrol', '');
//        $patrol_logs = Db::connect('sqlsrv_config')
//            ->table('check_record_ending')
//            ->order('check_start_time','desc')
//            ->select();
////        foreach ($patrol_logs as &$patrol_log) {
////            $patrol_log['check_end_time'] = substr($patrol_log['check_end_time'],11);
////        }
//        $this->assign('Patrol_logs', $patrol_logs);
//
//        $patrols = Db::connect('sqlsrv_config')
//            ->table('check_record_not_Start')
//            ->select();
//        $this->assign('Patrols', $patrols);
//
//        $patroling = Db::connect('sqlsrv_config')
//            ->table('check_record_starting')
//            ->order('check_start_time','desc')
//            ->select();
//        $this->assign('Patroling', $patroling);
        $videos = db('video_set')
            ->select();
        foreach ($videos as $video) {
            $sets[$video['html_id']] = $video['token'];
        }
        $videos = json_encode($sets,JSON_UNESCAPED_UNICODE);
        $this->assign('Videos', $videos);

//        dump($patrol_logs);
//        dump($patrols);
//        dump($patroling);
//        die;
        return $this->fetch();
    }
}