<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/25
 * Time: 10:33
 */

namespace app\index\controller;


use think\Controller;
use think\Db;
use app\common\controller\Adminbase;
use app\index\model\Patrol as PatrolModel;


class Enterprise extends Adminbase
{
    protected function initialize()
    {
        parent::initialize();
        $this->Patrol = new PatrolModel;
    }
    //$this->

    //设备管理首页 模版继承 注入左侧导航
    //设备管理首页 模版继承 注入左侧导航
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

//    企业基本信息
    public function info()
    {
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 10);
            $page = $this->request->param('page/d', 1);
            $field = "e.co_id,e.co_name,e.co_linker,e.co_linker_post,e.en_start,e.en_end,e.co_phone,e.en_long,e.en_time,t.type_name,u.nickname";
            $join  = [['co_type t', 'e.co_type = t.co_type_id', 'LEFT'],['admin u', 'e.user_id = u.userid', 'LEFT']];
            $res   = db('enterprise')->alias('e')->field($field)->order('e.co_id', 'desc')->join($join)->page($page, $limit)->select();
            foreach ($res as &$re) {
                $re['en_time'] = date('Y-H-d H:i:s',$re['en_time']);

                $starts = trim($re['en_start'],'-');
                $startarr = explode('-',$starts);
                $startone = db('sites')->where('id',$startarr['1'])->field('site_name')->find();
                $starttwo = db('sites')->where('id',$startarr['2'])->field('site_name')->find();
                $re['en_start'] = $startone['site_name'] . $starttwo['site_name'];

                $ends = trim($re['en_end'],'-');
                $endarr = explode('-',$ends);
                $endone = db('sites')->where('id',$endarr['1'])->field('site_name')->find();
                $endtwo = db('sites')->where('id',$endarr['2'])->field('site_name')->find();
                $re['en_end'] = $endone['site_name'] . $endtwo['site_name'];
            }
            $total = db('enterprise')->count();
            $result = array("code" => 0,"msg" => '', "count" => $total, "data" => $res);
            return json($result);
        }
        /*$depts = $this->UserModel->get_Tree($depts);
        //dump($depts);die;
        $this->assign('depts',$depts);*/
        return $this->fetch();
    }

//    新增企业信息
    public function info_add()
    {
        if ($this->request->isPost()) {

            $data = $this->request->post();
            $data['en_time'] = strtotime($data['en_time']);
            $data['en_start'] = '';
            $data['en_end'] = '';
            foreach ($data['start'] as $v1) {
                $data['en_start'] .= $v1.'-';
            }
            foreach ($data['end'] as $v2) {
                $data['en_end'] .= $v2.'-';
            }
            $data['co_local'] = $data['province'] .'-'. $data['city']  .'-'. $data['county']  .'-'. $data['add'];
            $res = Db('enterprise')
                ->strict(false)
                ->insert($data);
            if ($res){
                $this->success('新增企业信息成功！');
            }$this->error('新增企业信息失败！');
        }else{
            $local = '[{"id":1,"pid":0,"title":"河北省"},{"id":2,"pid":0,"title":"北京"},{"id":3,"pid":0,"title":"天津"},{"id":4,"pid":1,"title":"石家庄市"},{"id":5,"pid":1,"title":"唐山市"},{"id":6,"pid":1,"title":"秦皇岛市"},{"id":7,"pid":1,"title":"邯郸市"},{"id":8,"pid":1,"title":"邢台市"},{"id":9,"pid":1,"title":"保定市"},{"id":10,"pid":1,"title":"张家口市"},{"id":11,"pid":1,"title":"承德市"},{"id":12,"pid":1,"title":"沧州市"},{"id":13,"pid":1,"title":"廊坊市"},{"id":14,"pid":1,"title":"衡水市"},{"id":15,"pid":4,"title":"长安区"},{"id":16,"pid":4,"title":"桥东区"},{"id":17,"pid":4,"title":"桥西区"},{"id":18,"pid":4,"title":"新华区"},{"id":19,"pid":4,"title":"井陉矿区"},{"id":20,"pid":4,"title":"裕华区"},{"id":21,"pid":4,"title":"井陉县"},{"id":22,"pid":4,"title":"正定县"},{"id":21,"pid":4,"title":"栾城区"},{"id":21,"pid":4,"title":"行唐县"}]';
            $this->assign('local_data', $local);

            $sites_data = json_encode($this->Patrol->getSites());
            $this->assign('Sitesdata', $sites_data);

            $co_type = Db('co_type')->select();
            $this->assign('Co_type',$co_type);

            $user = Db('admin')->field('userid,nickname')->select();
            $this->assign('User',$user);
            return $this->fetch();
        }
    }

//    企业信息详情
    public function info_details()
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
        $this->assign('Info',$res);
        return $this->fetch();
    }

//    企业信息编辑
    public function info_edit()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['en_time'] = strtotime($data['en_time']);
            $data['en_start'] = '';
            $data['en_end'] = '';
            foreach ($data['start'] as $v1) {
                $data['en_start'] .= $v1.'-';
            }
            foreach ($data['end'] as $v2) {
                $data['en_end'] .= $v2.'-';
            }
            $data['co_local'] = $data['province'] .'-'. $data['city']  .'-'. $data['county']  .'-'. $data['add'];
//            dump($data);
            $res = Db('enterprise')
                ->where('co_id',$data['co_id'])
                ->strict(false)
                ->update($data);
            if ($res){
                $this->success('编辑企业信息成功！');
            }$this->error('编辑企业信息失败！');
        }else{
            $co_id = $this->request->param('co_id');
            $field = "e.co_id,e.co_name,e.co_linker,e.co_local,e.co_type,e.co_linker_post,e.en_start,e.en_end,e.co_legal,e.co_phone,e.en_remark,e.en_long,e.en_time,t.type_name,u.nickname,u.userid";
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

            $local = '[{"id":1,"pid":0,"title":"河北省"},{"id":2,"pid":0,"title":"北京"},{"id":3,"pid":0,"title":"天津"},{"id":4,"pid":1,"title":"石家庄市"},{"id":5,"pid":1,"title":"唐山市"},{"id":6,"pid":1,"title":"秦皇岛市"},{"id":7,"pid":1,"title":"邯郸市"},{"id":8,"pid":1,"title":"邢台市"},{"id":9,"pid":1,"title":"保定市"},{"id":10,"pid":1,"title":"张家口市"},{"id":11,"pid":1,"title":"承德市"},{"id":12,"pid":1,"title":"沧州市"},{"id":13,"pid":1,"title":"廊坊市"},{"id":14,"pid":1,"title":"衡水市"},{"id":15,"pid":4,"title":"长安区"},{"id":16,"pid":4,"title":"桥东区"},{"id":17,"pid":4,"title":"桥西区"},{"id":18,"pid":4,"title":"新华区"},{"id":19,"pid":4,"title":"井陉矿区"},{"id":20,"pid":4,"title":"裕华区"},{"id":21,"pid":4,"title":"井陉县"},{"id":22,"pid":4,"title":"正定县"},{"id":21,"pid":4,"title":"栾城区"},{"id":21,"pid":4,"title":"行唐县"}]';
            $this->assign('local_data', $local);

            $sites_data = json_encode($this->Patrol->getSites());
            $this->assign('Sitesdata', $sites_data);
            $local = explode('-',$res['co_local']);
            $this->assign('Local', json_encode($local,JSON_UNESCAPED_UNICODE));
            $this->assign('Startstr', json_encode($startarr,JSON_UNESCAPED_UNICODE));
            $this->assign('Endstr', json_encode($endarr,JSON_UNESCAPED_UNICODE));
            $this->assign('Time', $res['en_time']);
            $this->assign('Add', $local['3']);

            $this->assign('Sitesdata', $sites_data);
            $co_type = Db('co_type')->select();
            $this->assign('Co_type',$co_type);
            $user = Db('admin')->field('userid,nickname')->select();
            $this->assign('User',$user);
            $this->assign('Info',$res);
            return $this->fetch();
        }
    }

//    删除企业信息
    public function info_del()
    {
        $co_id = $this->request->param('co_id');
        if (empty($co_id)) {
            $this->error('ID错误');
        }
        if(Db('enterprise')->delete($co_id)){
            $this->success('删除企业信息成功！');
        }$this->error('删除企业信息失败！');
    }

    //    入廊施工信息展示
    public function construction()
    {
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 10);
            $page = $this->request->param('page/d', 1);
            $field = "e.wo_id,e.co_name,e.wo_name,e.wo_linker,e.co_linker,e.en_start,e.en_end,e.co_phone,e.en_long,t.type_name,u.nickname,e.wo_plan,wo_real";
            $join  = [['co_type t', 'e.co_type = t.co_type_id', 'LEFT'],['admin u', 'e.user_id = u.userid', 'LEFT']];
            $res   = db('enterprise_work')->alias('e')->field($field)->order('e.wo_id', 'desc')->join($join)->page($page, $limit)->select();
            foreach ($res as &$re) {
                $starts = trim($re['en_start'],'-');
                $startarr = explode('-',$starts);
               // $startone = db('sites')->where('id',$startarr['1'])->field('site_name')->find();
                $starttwo = db('sites')->where('id',$startarr['2'])->field('site_name')->find();
                $re['en_start'] =/* $startone['site_name'] .*/ $starttwo['site_name'];

                $ends = trim($re['en_end'],'-');
                $endarr = explode('-',$ends);
//                $endone = db('sites')->where('id',$endarr['1'])->field('site_name')->find();
                $endtwo = db('sites')->where('id',$endarr['2'])->field('site_name')->find();
                $re['en_end'] =/* $endone['site_name'] .*/ $endtwo['site_name'];
            }
            $total = db('enterprise_work')->count();
            $result = array("code" => 0,"msg" => '', "count" => $total, "data" => $res);
            return json($result);
        }
        /*$depts = $this->UserModel->get_Tree($depts);
        //dump($depts);die;
        $this->assign('depts',$depts);*/
        return $this->fetch();
    }

    public function work_add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['en_start'] = '';
            $data['en_end'] = '';
            foreach ($data['start'] as $v1) {
                $data['en_start'] .= $v1.'-';
            }
            foreach ($data['end'] as $v2) {
                $data['en_end'] .= $v2.'-';
            }
            $res = Db('enterprise_work')
                ->strict(false)
                ->insert($data);
            if ($res){
                $this->success('新增入廊施工信息成功！');
            }$this->error('新增入廊施工信息失败！');
        }else{
            $local = '[{"id":1,"pid":0,"title":"河北省"},{"id":2,"pid":0,"title":"北京"},{"id":3,"pid":0,"title":"天津"},{"id":4,"pid":1,"title":"石家庄市"},{"id":5,"pid":1,"title":"唐山市"},{"id":6,"pid":1,"title":"秦皇岛市"},{"id":7,"pid":1,"title":"邯郸市"},{"id":8,"pid":1,"title":"邢台市"},{"id":9,"pid":1,"title":"保定市"},{"id":10,"pid":1,"title":"张家口市"},{"id":11,"pid":1,"title":"承德市"},{"id":12,"pid":1,"title":"沧州市"},{"id":13,"pid":1,"title":"廊坊市"},{"id":14,"pid":1,"title":"衡水市"},{"id":15,"pid":4,"title":"长安区"},{"id":16,"pid":4,"title":"桥东区"},{"id":17,"pid":4,"title":"桥西区"},{"id":18,"pid":4,"title":"新华区"},{"id":19,"pid":4,"title":"井陉矿区"},{"id":20,"pid":4,"title":"裕华区"},{"id":21,"pid":4,"title":"井陉县"},{"id":22,"pid":4,"title":"正定县"},{"id":21,"pid":4,"title":"栾城区"},{"id":21,"pid":4,"title":"行唐县"}]';
            $this->assign('local_data', $local);

            $sites_data = json_encode($this->Patrol->getSites());
            $this->assign('Sitesdata', $sites_data);

            $co_type = Db('co_type')->select();
            $this->assign('Co_type',$co_type);

            $user = Db('admin')->field('userid,nickname')->select();
            $this->assign('User',$user);
            return $this->fetch();
        }
    }



//    入廊施工详情
    public function work_details()
    {
        $wo_id = $this->request->param('wo_id');
        $field = "e.wo_id,e.co_name,e.wo_name,e.co_linker,e.co_linker_post,e.wo_linker,e.wo_phone,e.wo_linker_post,e.wo_num,e.wo_plan,e.wo_real,e.en_start,e.en_end,e.co_phone,e.wo_remark,e.en_long,t.type_name,u.nickname";
        $join  = [['co_type t', 'e.co_type = t.co_type_id', 'LEFT'],['admin u', 'e.user_id = u.userid', 'LEFT']];
        $res   = db('enterprise_work')->alias('e')->where('wo_id',$wo_id)->field($field)->join($join)->find();


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
        $this->assign('Info',$res);
        return $this->fetch();
    }

//    入廊施工编辑
    public function work_edit()
    {
        if ($this->request->isPost()) {

            $wo_real = $this->request->param('wo_real');
            $wo_id = $this->request->param('wo_id');


            $res = Db('enterprise_work')
                ->where('wo_id',$wo_id)
                ->strict(false)
                ->update(['wo_real' => $wo_real]);
            if ($res){
                $this->success('修改实际施工时间成功！');
            }$this->error('修改实际施工时间失败！');
        }else{
            $wo_id = $this->request->param('wo_id');
            $field = "e.wo_id,e.co_name,e.wo_name,e.co_linker,e.co_linker_post,e.wo_linker,e.wo_phone,e.wo_linker_post,e.wo_num,e.wo_plan,e.wo_real,e.en_start,e.en_end,e.co_phone,e.wo_remark,e.en_long,t.type_name,u.nickname";
            $join  = [['co_type t', 'e.co_type = t.co_type_id', 'LEFT'],['admin u', 'e.user_id = u.userid', 'LEFT']];
            $res   = db('enterprise_work')->alias('e')->where('wo_id',$wo_id)->field($field)->join($join)->find();


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
            $this->assign('Info',$res);
            return $this->fetch();

        }
    }

//    删除入廊施工信息
    public function work_del()
    {
        $wo_id = $this->request->param('wo_id');
        if (empty($wo_id)) {
            $this->error('ID错误');
        }
        if(Db('enterprise_work')->delete($wo_id)){
            $this->success('删除入廊施工信息成功！');
        }$this->error('删除入廊施工信息失败！');
    }


}