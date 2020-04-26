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
use app\common\controller\Adminbase;

class Contingency extends Adminbase
{
    protected function initialize()
    {
        parent::initialize();
    }
    //主页 模版
    public function index()
    {
        //根据当前控制器名 ->auth_id  ->子类
        $auth_id = Db('auth')->where('auth_c',$this->request->controller())->where('auth_pid','0')->field('auth_id')->find();
        $sub = Db('auth')->where('auth_pid',$auth_id['auth_id'])->order('auth_id','esc')->select();
        $defaultUrl = ($sub['0']['auth_isLink']) ? $sub['0']['auth_url'] : $sub['0']['auth_c'].'/'.$sub['0']['auth_a'];
        $is_http = ($sub['0']['auth_isLink']) ? 1 : 0;
        $this->assign('Sub',$sub);
        $this->assign('defaultUrl',$defaultUrl);
        $this->assign('is_http',$is_http);
        return $this->fetch();
    }

    //应急预案
    public function plan()
    {
        return $this->fetch();
    }

    //应急物资
    public function supplies()
    {
        $cates = Db('sup_cate')->select();
        $this->assign('Cates',$cates);

        if ($this->request->isAjax()) {
        $limit = $this->request->param('limit/d', 10);
        $page = $this->request->param('page/d', 1);
        $cate = $this->request->param('cate');
        $keyword = $this->request->param('keyword');

        if($keyword == null||$keyword==""){
            $keyword = '%';
        }
//        dump($keyword);
//        dump($cate);
            if(!empty($cate)){
                $result = Db('supplies')
                    ->page($page, $limit)
                    ->field('s.sup_id,s.s_name,c.s_c_name,s.s_num,s.s_c_remark,s.s_number,s.s_position,a.nickname,s.s_car_num,s.s_car_model,s_model,a.phone')
                    ->alias('s')
                    ->order(array('sup_id' => 'DESC'))
                    ->where('s.s_c_id', $cate)
                    ->where('s.s_name|s.s_model|s.s_car_model|c.s_c_name|s.s_position|s.s_car_num|a.nickname', 'like', '%' . $keyword . '%')
                    ->join('sup_cate c','s.s_c_id = c.s_c_id')
                    ->join('admin a','s.s_user_id = a.userid')
//                ->where('is_alarm = 1')
                    ->select();
                $total = Db('supplies')
                    ->alias('s')
                    ->where('s.s_c_id', $cate)
                    ->where('s.s_name|s.s_model|s.s_car_model|c.s_c_name|s.s_position|s.s_car_num|a.nickname', 'like', '%' . $keyword . '%')
                    ->join('sup_cate c','s.s_c_id = c.s_c_id')
                    ->join('admin a','s.s_user_id = a.userid')
                    ->count();
            }else{
                $result = Db('supplies')
                    ->page($page, $limit)
                    ->field('s.sup_id,s.s_name,c.s_c_name,s.s_num,s.s_c_remark,s.s_number,s.s_position,a.nickname,s.s_car_num,s.s_car_model,s_model,a.phone')
                    ->alias('s')
                    ->order(array('sup_id' => 'DESC'))
                    ->where('s.s_name|s.s_model|s.s_car_model|c.s_c_name|s.s_position|s.s_car_num|a.nickname', 'like', '%' . $keyword . '%')
                    ->join('sup_cate c','s.s_c_id = c.s_c_id')
                    ->join('admin a','s.s_user_id = a.userid')
//                ->where('is_alarm = 1')
                    ->select();
                $total = Db('supplies')
                    ->alias('s')
                    ->where('s.s_name|s.s_model|s.s_car_model|c.s_c_name|s.s_position|s.s_car_num|a.nickname', 'like', '%' . $keyword . '%')
                    ->join('sup_cate c','s.s_c_id = c.s_c_id')
                    ->join('admin a','s.s_user_id = a.userid')
                    ->count();
            }

//        $result = Db::name('categorys')->order(array('listorder', 'id' => 'DESC'))->select();

        $result = array("code" => 0,"msg" => '', "count" => $total, "data" => $result);
        return json($result);
        }
        return $this->fetch();
    }

    //应急物资 新增
    public function supplies_add()
    {
        if ($this->request->isPost()) {
            Db::transaction(function () {
                $data = $this->request->post();
                $res = Db('supplies')
                    ->strict(false)
                    ->insertGetId($data);
                $s_number = 'YJWZ-'.sprintf('%03d', $res);
                $ress = Db('supplies')
                    ->where('sup_id', $res)
                    ->update(['s_number' => $s_number]);
                $this->success('新增成功！');
            });
//            dump($data);die;
            $this->error('新增失败！');
        }else{
            $cates = Db('sup_cate')->select();
            $users = db('admin')->field('userid,nickname')->order('userid', 'desc')->select();
            $this->assign('Cates',$cates);
            $this->assign('Users',$users);
            return $this->fetch();
        }
    }

    //应急物资 详情
    public function sup_details(){
        $sup_id = $this->request->param('sup_id');
        //获取设备信息
        $sups = Db('supplies')
            ->alias('s')
            ->field('s.sup_id,s.s_name,s.s_number,c.is_car,c.s_c_name,s.s_num,s.s_c_remark,s.s_number,s.s_position,a.nickname,s.s_car_num,s.s_car_model,s_model,a.phone')
            ->where('s.sup_id',$sup_id)
            ->join('sup_cate c','s.s_c_id = c.s_c_id')
            ->join('admin a','s.s_user_id = a.userid')
            ->find();
        //注入
        $this->assign("Sups", $sups);
        return $this->fetch();
    }

    //应急物资 详情编辑
    public function sup_edit(){
        if ($this->request->isPost()) {
            $sup_id = $this->request->param('sup_id');
            $data = $this->request->post();
            $res = Db('supplies')
                ->where('sup_id',$sup_id)
                ->strict(false)
                ->update($data);
//            $s_number = 'YJWZ-'.sprintf('%03d', $res);
//            $ress = Db('supplies')
//                ->where('sup_id', $res)
//                ->update(['s_number' => $s_number]);
            if ($res){
                $this->success('编辑成功！');
            }$this->error('编辑失败！');
        }else{
            $sup_id = $this->request->param('sup_id');
            //获取设备信息
            $sups = Db('supplies')
                ->alias('s')
                ->field('s.sup_id,s.s_name,s.s_c_id,a.userid,s.s_number,c.is_car,c.s_c_name,s.s_num,s.s_c_remark,s.s_number,s.s_position,a.nickname,s.s_car_num,s.s_car_model,s_model,a.phone')
                ->where('s.sup_id',$sup_id)
                ->join('sup_cate c','s.s_c_id = c.s_c_id')
                ->join('admin a','s.s_user_id = a.userid')
                ->find();
            //注入
            $this->assign("Sups", $sups);
            $cates = Db('sup_cate')->select();
            $users = db('admin')->field('userid,nickname')->order('userid', 'desc')->select();
            $this->assign('Cates',$cates);
            $this->assign('Users',$users);
            return $this->fetch();
        }

    }

    //应急物资 删除
    public function sup_del()
    {
        $sup_id = $this->request->param('sup_id');
        if (empty($sup_id)) {
            $this->error('ID错误');
        }
        if(Db('supplies')->delete($sup_id)){
            $this->success('删除成功！');
        }$this->error('删除失败！');
    }


    //应急队伍
    public function team()
    {
        $t_cate = Db('team_cate')->select();
        $this->assign('Cates',$t_cate);
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 10);
            $page = $this->request->param('page/d', 1);
            $cate = $this->request->param('cate');
            if(!empty($cate) && $cate !== ''){
                $result = Db('teams')
                    ->page($page, $limit)
                    ->alias('t')
                    ->order('team_id','desc')
                    ->join('team_cate c','t.t_c_id = c.t_c_id')
                    ->where('t.t_c_id', $cate)
                    ->select();
            }else{
                $result = Db('teams')
                    ->page($page, $limit)
                    ->alias('t')
                    ->order('team_id','desc')
                    ->join('team_cate c','t.t_c_id = c.t_c_id')
//                ->where('is_alarm = 1')
                    ->select();
            }
            //数据处理
            foreach ($result as &$res) {
//                dump($res['t_c_id']);
                if($res['t_c_id'] == 1){
                    $leader = Db('admin')
                        ->field('nickname,phone')
                        ->where('userid',$res['leader'])
                        ->find();

//                    $res['leaders'][] = json_encode([$leader['nickname'] => $leader['phone']],JSON_UNESCAPED_UNICODE);
                    $res['leaders'][] =  '<button type="button" class="layui-btn leaderbtn" style="margin: 3px">'.'队长：'.$leader['nickname'].'&nbsp;&nbsp;'.'手机：'.$leader['phone'].'</button>' ;
                    $members = Db('admin')
                        ->field('nickname,phone')
                        ->whereIn('userid',$res['member'])
                        ->select();
                    $memstr  = '';
//                    foreach ($members as $member) {
                    if($members){
//
                        foreach ($members as $member) {
                            $memstr .= '<button type="button" class="layui-btn layui-btn-normal memberbtn" style="margin: 3px">'.$member['nickname'].': '.$member['phone'].'</button>' ;
                        }
                        $res['members'][] = $memstr;
                    }
//                    $res['ex_p'] = '组长：'.$leader['nickname'].':'.$leader['phone'] .'  '.$memberstr;
//                    dump($group);
                }else{
                    $memstr  = '';
                    $members = explode(' ',$res['ex_p']);
                    foreach ($members as $member) {
                        $item = explode('-',$member);
//                        dump($item);
                        $memstr .= '<button type="button" class="layui-btn layui-btn-normal memberbtn" style="margin: 3px">'.$item[0].': '.$item[1].'</button>' ;
                    }
                    $res['members'][] = $memstr;
                }
            }
            $total = Db('teams')
                ->alias('t')
                ->join('team_cate c','t.t_c_id = c.t_c_id')
                ->count();
            $result = array("code" => 0,"msg" => '', "count" => $total, "data" => $result);
            return json($result);
        }
        return $this->fetch();
    }

    //应急队伍 新增
    public function team_add()
    {
        if ($this->request->isPost()) {
            Db::transaction(function () {
                $data = $this->request->post();
                if(!empty($data['ex_p'])){
                    $data['ex_p']=preg_replace ( "/\s(?=\s)/","\\1", $data['ex_p']);
                    $data['ex_p'] = trim($data['ex_p']);
                }
                $res = Db('teams')
                    ->strict(false)
                    ->insertGetId($data);
                $t_number = 'YJDW-'.sprintf('%03d', $res);
                $ress = Db('teams')
                    ->where('team_id', $res)
                    ->update(['t_number' => $t_number]);
                $this->success('新增成功！');
            });
            $this->error('新增失败！');
        }else{
//            $dept = db('dept')->field('dept_id,dept_pid,dept_name')->order('dept_id', 'desc')->select();
//            $users = db('admin')->field('deptid,userid,nickname')->order('userid', 'desc')->select();
//            $arr = [];
//            foreach ($dept as $de) {
//                $children = '';
//                foreach ($users as $item) {
//                    if($de['dept_id'] == $item['deptid']){
//                        $children[] =  ['name'=>$item['nickname'],'value'=>$item['userid'],'children'=>''];
//                    }
//                }
//                if($children){
//                    $arr[] = ['name'=>$de['dept_name'],'value'=>$de['dept_id'],'children'=>$children];
//                }
//            }

            $users = db('admin')
                ->alias('a')
                ->join('dept d','a.deptid = d.dept_id')
                ->field('dept_name,userid,nickname')
                ->order('userid', 'desc')
                ->select();
            foreach ($users as $user) {
                $arr[] = ['name'=>$user['nickname'],'value'=>$user['userid'],'showname'=>$user['dept_name'].'-'.$user['nickname']];
            }
            $this->assign('Users',json_encode($arr,JSON_UNESCAPED_UNICODE));
            $t_cate = Db('team_cate')->select();
            $this->assign('Cates',$t_cate);



    //        dump($t_cate);die;
            return $this->fetch();
        }
    }

    //应急队伍 详情
    public function team_details()
    {
        $team_id = $this->request->param('team_id');

        $result = Db('teams')
            ->alias('t')
            ->join('team_cate c','t.t_c_id = c.t_c_id')
            ->where('team_id',$team_id)
            ->find();

        if($result['t_c_id'] == 1){
            $leader = Db('admin')
                ->field('nickname,phone')
                ->where('userid',$result['leader'])
                ->find();
            $result['leaders'] = '';
//                    $res['leaders'][] = json_encode([$leader['nickname'] => $leader['phone']],JSON_UNESCAPED_UNICODE);
            $result['leaders'] .=  $leader['nickname'].':'.$leader['phone'].'  ';

            $members = Db('admin')
                ->field('nickname,phone')
                ->whereIn('userid',$result['member'])
                ->select();
            $memstr  = '';
//                    foreach ($members as $member) {
            if($members){
                foreach ($members as $member) {
                    $memstr .= $member['nickname'].':'.$member['phone'].'     ';
                }
                $result['members'] = $memstr;
            }
//                    $res['ex_p'] = '组长：'.$leader['nickname'].':'.$leader['phone'] .'  '.$memberstr;
//                    dump($group);
        }else{
            $memstr  = '';
            $members = explode(' ',$result['ex_p']);
            foreach ($members as $member) {
                $item = explode('-',$member);
//                        dump($item);
                $memstr .= $item[0].':'.$item[1].'     ';
//                  $memstr .= '<button type="button" class="layui-btn layui-btn-normal memberbtn" style="margin: 3px">'.$item[0].': '.$item[1].'</button>' ;
            }
            $result['leaders'] = '';
            $result['members'] = $memstr;
//            dump($result);die;
        }
        //获取设备信息
        //注入
        $this->assign("Teams", $result);
        return $this->fetch();
    }

    //应急队伍 删除
    public function team_del()
    {
        $team_id = $this->request->param('team_id');
        if (empty($team_id)) {
            $this->error('ID错误');
        }
        if(Db('teams')->delete($team_id)){
            $this->success('删除成功！');
        }$this->error('删除失败！');
    }

    //应急队伍 详情编辑
    public function team_edit(){
        if ($this->request->isPost()) {
            $team_id = $this->request->param('team_id');
            $data = $this->request->post();
//            dump($data);die;
            if(!empty($data['ex_p'])){
                $data['ex_p']=preg_replace ( "/\s(?=\s)/","\\1", $data['ex_p']);
                $data['ex_p'] = trim($data['ex_p']);
            }
            $res = Db('teams')
                ->where('team_id',$team_id)
                ->strict(false)
                ->update($data);
//            $s_number = 'YJWZ-'.sprintf('%03d', $res);
//            $ress = Db('supplies')
//                ->where('sup_id', $res)
//                ->update(['s_number' => $s_number]);
            if ($res){
                $this->success('编辑成功！');
            }$this->error('编辑失败！');
        }else{
            $team_id = $this->request->param('team_id');

            $result = Db('teams')
                ->alias('t')
                ->join('team_cate c','t.t_c_id = c.t_c_id')
                ->where('team_id',$team_id)
                ->find();

            $users = db('admin')
                ->alias('a')
                ->join('dept d','a.deptid = d.dept_id')
                ->field('dept_name,userid,nickname')
                ->order('userid', 'desc')
                ->select();
            $member_arr = explode(',',$result['leader']);
            foreach ($users as $user) {
                if(!in_array($user['userid'],$member_arr)){
                    $member_s[] = ['name'=>$user['nickname'],'value'=>$user['userid'],'showname'=>$user['dept_name'].'-'.$user['nickname']];
                }
                $leader_s[] = ['name'=>$user['nickname'],'value'=>$user['userid'],'showname'=>$user['dept_name'].'-'.$user['nickname']];
            }

            $result['member'] =  json_encode(explode(',',$result['member']),JSON_UNESCAPED_UNICODE);
            $result['leader'] =  json_encode(explode(',',$result['leader']),JSON_UNESCAPED_UNICODE);
            $this->assign('Leader_s',json_encode($leader_s,JSON_UNESCAPED_UNICODE));
            $this->assign('Member_s',json_encode($member_s,JSON_UNESCAPED_UNICODE));
            $t_cate = Db('team_cate')->select();
            $this->assign('Cates',$t_cate);
            //获取设备信息
            //注入
            $this->assign("Teams", $result);
//            dump()
            return $this->fetch();
        }

    }


    //应急演练
    public function drill()
    {
        return $this->fetch();
    }

    //指挥调度
    public function dispatch()
    {
        return $this->fetch();
    }

    //应急指挥
    public function command()
    {
        return $this->fetch();
    }

}