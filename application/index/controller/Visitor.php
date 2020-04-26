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
use app\index\model\Patrol as PatrolModel;
class Visitor extends Adminbase
{
    protected function initialize()
    {
        parent::initialize();
        $this->Patrol = new PatrolModel;
    }

    public function index()
    {
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 10);
            $page = $this->request->param('page/d', 1);
            $field = "v.v_id,v.v_name,v.v_id_card,v.v_company,v.v_post,v.v_phone,v.v_num,v.v_start_time,v.v_end_time,v.v_for,v.v_place,u.user_name";
            $join  = [['user u', 'v.v_user_id = u.user_id', 'LEFT']];
            $res   = db('visitor')->alias('v')->field($field)->order('v.v_id', 'desc')->join($join)->page($page, $limit)->select();
            $total = db('visitor')->count();
            foreach ($res as &$re) {
                $re['v_start_time'] = date('Y-m-d H:i:s',$re['v_start_time']);
                $re['v_end_time'] = date('Y-m-d H:i:s',$re['v_end_time']);
            }
            $result = array("code" => 0,"msg" => '', "count" => $total, "data" => $res);
            return json($result);
        }
        /*$depts = $this->UserModel->get_Tree($depts);
        //dump($depts);die;
        $this->assign('depts',$depts);*/
        return $this->fetch();
    }

    //    新增访客信息
    public function visitor_add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();

            $data['v_start_time'] = strtotime($data['v_start']);
            $data['v_end_time'] = strtotime($data['v_end']);
            $res = Db('visitor')
                ->strict(false)
                ->insert($data);
            if ($res){
                $this->success('新增访客信息成功！');
            }$this->error('新增访客信息失败！');
        }else{
            $user = Db('user')->field('user_id,user_name')->select();
            $this->assign('User',$user);
            return $this->fetch();
        }
    }

//    访客信息详情
    public function visitor_details()
    {
        $v_id = $this->request->param('v_id');
        $join  = [['user u', 'v.v_user_id = u.user_id', 'LEFT']];
        $res   = db('visitor')->alias('v')->join($join)->where('v_id',$v_id)->find();

        $res['v_start_time'] = date('Y-m-d H:i:s',$res['v_start_time']);
        $res['v_end_time'] = date('Y-m-d H:i:s',$res['v_end_time']);
        $this->assign('Info',$res);
        return $this->fetch();

    }

//    访客信息编辑
    public function visitor_edit()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();


            $data['v_start_time'] = strtotime($data['v_start']);
            $data['v_end_time'] = strtotime($data['v_end']);

            $res = Db('visitor')
                ->where('v_id',$data['v_id'])
                ->strict(false)
                ->update($data);
            if ($res){
                $this->success('修改访客信息成功！');
            }$this->error('修改访客信息失败！');
        }else{
            $v_id = $this->request->param('v_id');
            $join  = [['user u', 'v.v_user_id = u.user_id', 'LEFT']];
            $res   = db('visitor')->alias('v')->join($join)->where('v_id',$v_id)->find();
            $res['v_start_time'] = date('Y-m-d H:i:s',$res['v_start_time']);
            $res['v_end_time'] = date('Y-m-d H:i:s',$res['v_end_time']);
            $this->assign('Info',$res);
            $user = Db('user')->field('user_id,user_name')->select();
            $this->assign('User',$user);
            return $this->fetch();
        }
    }
}