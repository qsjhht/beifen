<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/24
 * Time: 18:54
 */

namespace app\index\controller;

use think\Controller;
use think\Db;
use app\common\controller\Adminbase;
use app\index\model\Role as RoleModel;


class Alarm extends Adminbase
{
    protected function initialize()
    {
        parent::initialize();
        $this->RoleModel = new RoleModel;
    }
    //设备管理首页 模版继承 注入左侧导航
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
        return $this->fetch('index',[],['__PUBLIC__'=>'/public/']);
    }

//    报警日志
    public function log()
    {
        /*$datas = Db('alarmlog')->order(array( 'id' => 'ESC'))->select();
            //dump($resultcate);
        return $this->fetch();*/
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 10);
            $page = $this->request->param('page/d', 1);
            $datas = Db('alarmlog')->order(array( 'id' => 'ESC'))->page($page, $limit)->select();

            $total = Db('alarmlog')->order(array( 'id' => 'ESC'))->count();
            $result = array("code" => 0,"msg" => '', "count" => $total, "data" => $datas);
            return json($result);
        }
        return $this->fetch();
    }

    //    报警日志 新
    public function logs()
    {
        /*$datas = Db('alarmlog')->order(array( 'id' => 'ESC'))->select();
            //dump($resultcate);
        return $this->fetch();*/
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 10);
            $page = $this->request->param('page/d', 1);
            $details = Db('alarmrecs')
                ->alias('r')
                ->join('alarmcode c','r.alarm_code = c.code','LEFT')
                ->join('alarmtype t','r.alarm_type = t.type','LEFT')
                ->join('alarmlvl l','r.alarmlevel = l.level','LEFT')
                ->join('alarmmanage m','r.alarm_manage = m.manage_id','LEFT')
                ->join('categorys s','r.cate_code = s.id','LEFT')
//                ->join('eqpts e','r.equipment_code = e.eqpt_id'),e.eqpt_type,e.eqpt_site_detail r.eqpt_sys,
                ->field('r.id,r.alarm_time,t.typecontent,r.alarm_code,r.eqpt_site,r.remark,s.parentid,s.cate_name,c.codecontent,l.lvlcontent,m.manage_type,r.is_manage,r.rec_details')
                ->order('r.alarm_time', 'desc')
                ->page($page, $limit)
                ->select();
            //['alarmtime'] = date('Y-m-d H:i:s',$details['alarmtime']);
            foreach ($details as &$detail) {
                $cate_names =  db('categorys')->field('cate_name')->where('id',$detail['parentid'])->find();
                $detail['parent'] = $cate_names['cate_name'];
                $detail['alarm_time'] = date('Y-m-d H:i:s',$detail['alarm_time']);
               /* if ($detail['is_manage']){
                    $detail['is_manage'] = '已处置';
                }else{
                    $detail['is_manage'] = '未处置';
                }*/
            }

            $total = Db('alarmrecs')->order(array( 'id' => 'ESC'))->count();
            $result = array("code" => 0,"msg" => '', "count" => $total, "data" => $details);
            return json($result);
        }
        return $this->fetch();
    }

//    报警设置
    public function alarm_set()
    {
         if ($this->request->isAjax()) {
        $tree = new \util\Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $result = Db('categorys')
            ->field('a.id,a.parentid,a.cate_name,a.alarm_lvl,l.lvlcontent')
            ->alias('a')
            ->join('alarmlvl l','a.alarm_lvl = l.level')
            ->where('is_alarm = 1')
            ->select();

//        $result = Db::name('categorys')->order(array('listorder', 'id' => 'DESC'))->select();


        $tree->init($result);

        $_list = $tree->getTreeList($tree->getTreeArray(0), 'cate_name');

        $total = count($_list);
        $result = array("code" => 0, "count" => $total, "data" => $_list);
        return json($result);
         }
        return $this->fetch();
    }

    //    修改级别
    public function alarm_edit()
    {
        $haschild = $this->request->param('haschild');
        $cate_id = $this->request->param('cate_id');
        $cate_name =  db('categorys')->field('cate_name,alarm_lvl,id')->where('id',$cate_id)->find();
        $alarm_lvl =  db('alarmlvl')->field('level,lvlcontent')->select();
        $this->assign('haschild',$haschild);
        $this->assign('cate_name',$cate_name);
        $this->assign('alarm_lvl',$alarm_lvl);
        return $this->fetch();
    }

    //    修改级别
    public function alarm_edit_ajax()
    {
        $cateid = $this->request->param('cateid');
        $lvl = $this->request->param('lvl');
        $haschild = $this->request->param('haschild');
        if(!$haschild){
            $res = Db('categorys')
                ->where('id',$cateid)
                ->strict(false)
                ->update(['alarm_lvl'=>$lvl]);
        }else{
            $res = Db('categorys')
                ->where('id',$cateid)
                ->strict(false)
                ->update(['alarm_lvl'=>$lvl]);
            $ress = Db('categorys')
                ->where('parentid',$cateid)
                ->strict(false)
                ->update(['alarm_lvl'=>$lvl]);
        }

        if($res){
            $result = array("code" => 1,"msg" => '处理成功！');
        }else{
            $result = array("code" => 1,"msg" => '处理失败！');
        }
        return json($result);

    }


    public function get_tree_data()
    {
        $auths = Db('categorys')->field('id,parentid,cate_name')->where('is_alarm = 1')->select();
        $auths = $this->RoleModel->get_Tree($auths,'id','parentid','cate_name');
        return $auths;
    }

//    报警处置
    public function manage()
    {
        $rec_id = $this->request->param('rec_id');
        $details = Db('alarmrecs')
            ->alias('r')
            ->join('alarmcode c','r.alarm_code = c.code','LEFT')
            ->join('alarmtype t','r.alarm_type = t.type','LEFT')
            ->join('alarmlvl l','r.alarmlevel = l.level','LEFT')
            ->join('alarmmanage m','r.alarm_manage = m.manage_id','LEFT')
            ->join('categorys s','r.cate_code = s.id','LEFT')
//                ->join('eqpts e','r.equipment_code = e.eqpt_id'),e.eqpt_type,e.eqpt_site_detail r.eqpt_sys,
            ->field('r.id,r.alarm_time,t.typecontent,r.alarm_code,c.codecontent,r.eqpt_site,s.parentid,s.cate_name,l.lvlcontent,m.manage_type,r.is_manage,r.rec_details')
            ->find($rec_id);


        $details['alarm_time'] = date('Y-m-d H:i:s',$details['alarm_time']);
        if ($details['is_manage']){
            $details['is_manage'] = '已处置';
        }else{
            $details['is_manage'] = '未处置';
        }
        $cate_names =  db('categorys')->field('cate_name')->where('id',$details['parentid'])->find();
        $details['parent'] = $cate_names['cate_name'];
        $this->assign('details',$details);
        dump($details);
        return $this->fetch();
    }

//    日志详情
    public function details()
    {
        $rec_id = $this->request->param('rec_id');
        $details = Db('alarmrecs')
            ->alias('r')
            ->join('alarmcode c','r.alarm_code = c.code','LEFT')
            ->join('alarmtype t','r.alarm_type = t.type','LEFT')
            ->join('alarmlvl l','r.alarmlevel = l.level','LEFT')
            ->join('alarmmanage m','r.alarm_manage = m.manage_id','LEFT')
            ->join('categorys s','r.cate_code = s.id','LEFT')
//                ->join('eqpts e','r.equipment_code = e.eqpt_id'),e.eqpt_type,e.eqpt_site_detail r.eqpt_sys,
            ->field('r.id,r.alarm_time,t.typecontent,r.alarm_code,c.codecontent,r.remark,r.eqpt_site,s.parentid,s.cate_name,l.lvlcontent,m.manage_type,r.is_manage,r.rec_details')
            ->find($rec_id);


        $details['alarm_time'] = date('Y-m-d H:i:s',$details['alarm_time']);
        if ($details['is_manage']){
            $details['is_manage'] = '已处置';
        }else{
            $details['is_manage'] = '未处置';
        }
        $cate_names =  db('categorys')->field('cate_name')->where('id',$details['parentid'])->find();
        $details['parent'] = $cate_names['cate_name'];
        $this->assign('details',$details);
        dump($details);
        return $this->fetch();
    }

//    处理报警类型
    public function alarm_manage(){
        $rec_id = $this->request->param('rec_id');
        $type = $this->request->param('type');
        $details = $this->request->param('details');
        if(!$details){
            $details = '无';
        }

        if($type == '1000'){
            $res = Db('alarmrecs')
                ->where('id', $rec_id)
                ->update([
                    'rec_details' =>  $details,
                    'is_manage'	=> '1'
                ]);
        }else{
            $res = Db('alarmrecs')
                ->where('id', $rec_id)
                ->update([
                    'alarm_type' =>  $type,
                    'rec_details' =>  $details,
                    'is_manage'	=> '1'
                ]);
        }

        if($res){
            $result = array("code" => 1,"msg" => '处理成功！');
        }else{
            $result = array("code" => 0,"msg" => '处理失败！');
        }
        return json($result);
    }
}