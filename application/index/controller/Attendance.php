<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/25
 * Time: 11:00
 */

namespace app\index\controller;

use think\Db;
use app\common\controller\Adminbase;

class Attendance extends Adminbase
{
    public function index()
    {
        //根据当前控制器名 ->auth_id  ->子类
        $auth_id = Db('auth')->where('auth_c',$this->request->controller())->where('auth_pid','0')->field('auth_id')->find();
        $sub = Db('auth')->where('auth_pid',$auth_id['auth_id'])->select();
        $defaultUrl = ($sub['0']['auth_isLink']) ? $sub['0']['auth_url'] : $sub['0']['auth_c'].'/'.$sub['0']['auth_a'];
        $is_http = ($sub['0']['auth_isLink']) ? 1 : 0;
        $this->assign('Sub',$sub);
        $this->assign('defaultUrl',$defaultUrl);
        $this->assign('is_http',$is_http);
        return $this->fetch('index',[],['__PUBLIC__'=>'/public/']);
    }

//    排班管理
    public function set()
    {
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 10);
            $page = $this->request->param('page/d', 1);
            $field = "p.p_id,p.p_name,p.date_range,p.time_range,d.d_name,p.producer";
            $join  = [['duty_room d', 'p.duty_id = d.d_id', 'LEFT']];
            $res   = db('work_plan')->alias('p')->field($field)->order('p.p_id', 'desc')->join($join)->page($page, $limit)->select();
            $total = db('work_plan')->count();
            $result = array("code" => 0,"msg" => '', "count" => $total, "data" => $res);
            return json($result);
        }
        /*$depts = $this->UserModel->get_Tree($depts);
        //dump($depts);die;
        $this->assign('depts',$depts);*/
        return $this->fetch();
    }

//    新增排班
    public function set_add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();

            $data['user_ids'] = '';
            $data['producer'] = $this->_userinfo['nickname'];
            foreach ($data['user'] as $id=>$d) {
                $data['user_ids'] .= $id .',';
            }
            $ranges = explode(' - ',$data['time_range']);
            if($ranges['1'] == '00:00:00'){
                $ranges['1'] = '24:00:00';
                $data['time_range'] = implode(' - ',$ranges);
            }
            $res = Db('work_plan')
                ->strict(false)
                ->insert($data);
            if ($res){
                $this->success('新增排班计划成功！');
            }$this->error('新增排班计划失败！');
        }else{
            $user = Db('admin')->field('userid,nickname')->select();
            $this->assign('User',$user);
            $duty = Db('duty_room')->field('d_id,d_name')->select();
            $this->assign('Duty',$duty);
            return $this->fetch();
        }
    }

//    排版详情
    public function set_details()
    {
        $p_id = $this->request->param('p_id');
        $join  = [['duty_room d', 'p.duty_id = d.d_id', 'LEFT']];
        $res   = db('work_plan')->alias('p')->join($join)->where('p_id',$p_id)->find();
        $users = trim($res['user_ids'],',');
        $userarr = explode(',',$users);

        foreach ($userarr as $item) {
            $user =  db('admin')->where('userid',$item)->field('nickname')->find();
            $res['user'][] = $user['nickname'];
        }

        $this->assign('Info',$res);

        $duty = Db('duty_room')->field('d_id,d_name')->select();
        $this->assign('Duty',$duty);
        return $this->fetch();
    }

    public function set_edit()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();

            $data['user_ids'] = '';
            $data['producer'] = $this->_userinfo['nickname'];
            foreach ($data['user'] as $id=>$d) {
                $data['user_ids'] .= $id .',';
            }
            $ranges = explode(' - ',$data['time_range']);
            if($ranges['1'] == '00:00:00'){
                $ranges['1'] = '24:00:00';
                $data['time_range'] = implode(' - ',$ranges);
            }
            $res = Db('work_plan')
                ->where('p_id',$data['p_id'])
                ->strict(false)
                ->update($data);
            if ($res){
                $this->success('修改排班信息成功！');
            }$this->error('修改排班信息失败！');
        }else{
            $p_id = $this->request->param('p_id');
            $join  = [['duty_room d', 'p.duty_id = d.d_id', 'LEFT']];
            $res   = db('work_plan')->alias('p')->join($join)->where('p_id',$p_id)->find();
            $users = trim($res['user_ids'],',');
            $userarr = explode(',',$users);

            /*foreach ($userarr as $item) {
                $user =  db('admin')->where('userid',$item)->field('username')->find();
                $res['user'][] = $user['username'];
            }*/
            $this->assign('Info',$res);
            $this->assign('Userarr',$userarr);
            $user = Db('admin')->field('userid,nickname')->select();

            $this->assign('User',$user);
            $duty = Db('duty_room')->field('d_id,d_name')->select();

            $this->assign('Duty',$duty);
            return $this->fetch();
        }
    }

//排班计划删除
    public function set_del()
    {
        $p_id = $this->request->param('p_id');
        if (empty($p_id)) {
            $this->error('ID错误');
        }
        if(Db('work_plan')->delete($p_id)){
            $this->success('删除排班信息成功！');
        }$this->error('删除排班信息失败！');
    }


//    值班考勤
    public function check()
    {
        //$this->_userinfo['username'];$this->_userinfo['userid']
//        $d = strtotime(date('Y-m-d',time()));
//        $d2 = date('Y-m-d',strtotime('+1 day'));

        $this->assign('Uid',$this->_userinfo['userid']);
        $this->assign('Uname',$this->_userinfo['nickname']);


        $range = db('work_plan')->field('time_range')->select();
        foreach ($range as $ra) {
            $j = date("H:i:s");

            $h = strtotime($j);

            $time = explode(' - ', $ra['time_range']);


            $z = strtotime($time['0']);//获得指定分钟时间戳，00:00

            $x = strtotime($time['1']);//获得指定分钟时间戳，00:29

            if ($h > $z && $h < $x) {
                $m_d_s = $z - 3600;
                $m_d_e = $z + 3600;
                $n_s_s = $x - 3600;
                $n_s_e = $x + 3600;


//                if(time() > $m_d_e){
//                    $this->assign('Start', $x);
//                }else{
//                    $this->assign('Start', $z);
//                }
            }
        }
/*        $m_d_l = strtotime($d. '09:00:00');

        $n_s_l = strtotime($d. '17:00:00');*/
//            $this->assign('S_range', date("H:i:s",$m_d_s) .'---'. date("H:i:s",$m_d_e));
//            $this->assign('E_range', date("H:i:s",$n_s_s) .'---'. date("H:i:s",$n_s_e));
//            dump(date("H:i:s",$m_d_s));
//            dump(date("H:i:s",$m_d_e));
//            dump(date("H:i:s",$n_s_s));
//            dump(date("H:i:s",$n_s_e));
//        $map1 = [
//            ['u_id','=',$this->_userinfo['userid']],
//            ['m_time', '> time', $d - 3600]
//        ];
//
//        $map2 = [
//            ['u_id','=',$this->_userinfo['userid']],
//            ['n_time', '> time',  'today']
//        ];
//        $check = db('attendance')
//            ->whereOr([ $map1, $map2 ])
//            ->find();
//        if($check){
////            dump(date('Y-m-d H:i:s',$check['m_time']));
////            dump(date('Y-m-d H:i:s',$check['n_time']));
//
//            if($check['m_time']){
//                $first =  $check['m_time'];
//                $check['m_time'] = date('Y-m-d H:i:s',$check['m_time']);
//                $check['m_c'] = false;
//            }else{
//                $check['m_time'] = '未登记';
//                if(time() > $m_d_s && time() < $m_d_e){
//                    $check['m_c'] = true;
//                }else{
//                    $check['m_c'] = false;
//                }
//            }
//            if($check['n_time']){
//                $check['n_time'] = date('Y-m-d H:i:s',$check['n_time']);
//                $check['n_c'] = false;
//            }else{
//                $check['n_time'] = '未登记';
//                if(time() >  $check['start'] + 21600){
//                    $check['n_c'] = true;
//                }else{
//                    $check['n_c'] = false;
//                }
//            }
//        }else{

//            $check['m_time'] = '未登记';
//            $check['n_time'] = '未登记';
//            if(!$this->_userinfo['userid']){
//                $check['m_c'] = false;
//                $check['n_c'] = false;
//            }else{
//                if((time() > $m_d_s && time() < $m_d_e) || (time() > $n_s_s && time() < $n_s_e)){
//                    $check['m_c'] = true;
//                }else{
//                    $check['m_c'] = false;
//                }

//            if(time() > $n_s_s && time() < $n_s_e){
//                $check['n_c'] = true;
//            }else{
//                $check['n_c'] = false;
//            }
//            }
//
//        }
//        $this->assign('Info',$check);
        $work_plan = Db('work_plan')->field('user_ids,time_range')->select();

        $rang = '';
        foreach ($work_plan as $wp) {
           $wps =  explode(',',rtrim($wp['user_ids'],','));
            foreach ($wps as $item) {
                if ($item == $this->_userinfo['userid']){
                    $rang = $wp['time_range'];
                }
           }
        }

        if($rang){



        $rangs = explode(' - ',$rang);

        $mtimer = explode(':',$rangs[0]);
        $ntimer = explode(':',$rangs[1]);
        $mtimer = mktime($mtimer[0],$mtimer[1],$mtimer[2]);
        $ntimer = mktime($ntimer[0],$ntimer[1],$ntimer[2]);

        // 查询
        $map1 = [
            ['u_id','=',$this->_userinfo['userid']],
            ['m_time', 'between time', [$mtimer - 3600, $mtimer + 3600]]
        ];

        $map2 = [
            ['u_id','=',$this->_userinfo['userid']],
            ['n_time', 'between time', [$ntimer - 3600, $ntimer + 3600]]
        ];
        $check = db('attendance')
            ->whereOr([ $map1, $map2 ])
            ->find();

        if($check){
            if($check['m_time']){
                $check['m_c'] = false;
                $check['m_time'] = date('Y-m-d H:i:s',$check['m_time']);
            }else{
                $check['m_time'] = '未登记！';
                if(time() > $mtimer - 3600 && time() < $mtimer + 3600){
                    $check['m_c'] = true;
                }else{
                    $check['m_c'] = false;
                }
            }

            if($check['n_time']){
                $check['n_c'] = false;
                $check['n_time'] = date('Y-m-d H:i:s',$check['n_time']);
            }else{
                $check['n_time'] = '未登记！';
                if(time() > $ntimer - 3600 && time() < $ntimer + 3600){
                    $check['n_c'] = true;
                }else{
                    $check['n_c'] = false;
                }
            }
        }else{
            $check['m_time'] = '未登记！';
            $check['n_time'] = '未登记！';
            if(time() > $mtimer - 3600 && time() < $mtimer + 3600){
                $check['m_c'] = true;
            }else{
                $check['m_c'] = false;
            }
            if(time() > $ntimer - 3600 && time() < $ntimer + 3600){
                $check['n_c'] = true;
            }else{
                $check['n_c'] = false;
            }
        }
        $this->assign('Info',$check);

            $this->assign('Time_range', $rang);
            $this->assign('S_range', date("H:i:s",$mtimer - 3600) .'---'. date("H:i:s",$mtimer + 3600));
            $this->assign('E_range', date("H:i:s",$ntimer - 3600) .'---'. date("H:i:s",$ntimer + 3600));

        }else{
            $check['m_time'] = '无';
            $check['n_time'] = '无';
            $check['m_c'] = false;
            $check['n_c'] = false;
            $this->assign('Info',$check);
            $rang = '无排班计划';
            $this->assign('Time_range', $rang);
            $this->assign('S_range', '无排班计划');
            $this->assign('E_range', '无排班计划');

        }


        return $this->fetch();
    }

    //签到
    public function sign()
    {
        $d = strtotime(date('Y-m-d',time()));
        $data['u_id'] = $this->_userinfo['userid'];
        $map1 = [
            ['u_id','=',$this->_userinfo['userid']],
            ['m_time', '> time', $d - 3600]
        ];

        $map2 = [
            ['u_id','=',$this->_userinfo['userid']],
            ['n_time', '> time',  'today']
        ];
        $att = db('attendance')
            ->whereOr([ $map1, $map2 ])
            ->find();

        $checks = $this->request->param('check');
//        $start = $this->request->param('start');
        if (empty($checks)) {
            $this->error('数据错误！');
        }

        if($checks == 'm_check'){
            $data['m_time'] = time();
//            $data['start'] = $start;
            $res = db('attendance')
                ->strict(false)
                ->insert($data);
            if ($res){
                $this->success('上班登记成功！');
            }$this->error('上班登记失败！');
        }elseif ($checks == 'n_check'){
            $data['n_time'] = time();
            if($att){
                $res = Db('attendance')
                    ->where('a_id',$att['a_id'])
                    ->strict(false)
                    ->update($data);
            }else{
                $res = db('attendance')
                    ->strict(false)
                    ->insert($data);
            }
            if ($res){
                $this->success('下班签退成功！');
            }$this->error('下班签退失败！');
        }
    }

    //    值班异常记录
    public function abnorma()
    {
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 10);
            $page = $this->request->param('page/d', 1);
            $field = "a.ab_time,ab_remark,u.user_name,a.registrant";
            $join  = [['user u', 'a.user_id = u.user_id', 'LEFT']];
            $res   = db('abnorma')->alias('a')->field($field)->order('a.ab_id', 'desc')->join($join)->page($page, $limit)->select();
            foreach ($res as &$re) {
                $re['ab_time'] = date('Y-m-d H:i:s',$re['ab_time']);
            }
            $total = db('abnorma')->count();
            $result = array("code" => 0,"msg" => '', "count" => $total, "data" => $res);
            return json($result);
        }
        /*$depts = $this->UserModel->get_Tree($depts);
        //dump($depts);die;
        $this->assign('depts',$depts);*/
        return $this->fetch();
    }

    public function abnorma_add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['registrant'] = $this->_userinfo['nickname'];
            $data['ab_time'] = strtotime($data['ab_time']);
            $res = Db('abnorma')
                ->strict(false)
                ->insert($data);
            if ($res){
                $this->success('新增异常记录成功！');
            }$this->error('新增异常记录失败！');
        }else{
            $user = Db('user')->field('user_id,user_name')->select();
            $this->assign('User',$user);
            return $this->fetch();
        }
    }

//    值班日志
    public function log()
    {
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 10);
            $page = $this->request->param('page/d', 1);

            $res   = db('duty_log')->order('d_date', 'desc')->page($page, $limit)->select();
            foreach ($res as &$re) {
                $re['currents_name'] = '';
                $re['nexts_name'] = '';
                $curs = trim($re['currents'],',');
                $nexts = trim($re['nexts'],',');
                $curarr = explode(',',$curs);
                $nextarr = explode(',',$nexts);
                foreach ($curarr as $item) {
                    $user =  db('admin')->where('userid',$item)->field('nickname')->find();
                    $re['currents_name'] .= $user['nickname'].',';
                }
                foreach ($nextarr as $item) {
                    $user =  db('admin')->where('userid',$item)->field('nickname')->find();
                    $re['nexts_name'] .= $user['nickname'].',';
                }
                $re['currents_name'] = trim($re['currents_name'],',');
                $re['nexts_name'] = trim($re['nexts_name'],',');
            }

            $total = db('duty_log')->count();
            $result = array("code" => 0,"msg" => '', "count" => $total, "data" => $res);
            return json($result);
        }
        /*$depts = $this->UserModel->get_Tree($depts);
        //dump($depts);die;
        $this->assign('depts',$depts);*/
        return $this->fetch();
    }

//    新增日志
    public function log_add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            //$data['registrant'] = $this->_userinfo['nickname'];
            //$data['ab_time'] = strtotime($data['ab_time']);
            $data['currents'] = '';
            $data['nexts'] = '';
            foreach ($data['current'] as $id=>$d) {
                $data['currents'] .= $id .',';
            }
            foreach ($data['next'] as $id=>$d) {
                $data['nexts'] .= $id .',';
            }
            $res = Db('duty_log')
                ->strict(false)
                ->insert($data);
            if ($res){
                $this->success('新增值班记录成功！');
            }$this->error('新增值班记录失败！');
        }else{
            $user = Db('admin')->field('userid,nickname')->select();
            $this->assign('User',$user);
            $duty = Db('duty_room')->field('d_id,d_name')->select();
            $this->assign('Duty',$duty);
            return $this->fetch();
        }
    }

//    日志详情
    public function log_details()
    {
        $d_id = $this->request->param('d_id');
        $res = db('duty_log')->where('d_id',$d_id)->find();

        $curs = trim($res['currents'],',');
        $curarr = explode(',',$curs);

        $nexts = trim($res['nexts'],',');
        $nextarr = explode(',',$nexts);

        foreach ($curarr as $item) {
            $user =  db('admin')->where('userid',$item)->field('nickname')->find();
            $res['cur_names'][] = $user['nickname'];
        }
        foreach ($nextarr as $item) {
            $user =  db('admin')->where('userid',$item)->field('nickname')->find();
            $res['next_names'][] = $user['nickname'];
        }

        $this->assign('Info',$res);
/*
        $duty = Db('duty_room')->field('d_id,d_name')->select();
        $this->assign('Duty',$duty);*/
        return $this->fetch();
    }

}