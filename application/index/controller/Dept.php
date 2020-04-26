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
use app\index\model\Dept as DeptModel;
use app\common\controller\Adminbase;

class Dept extends Adminbase
{
    protected function initialize()
    {
        parent::initialize();
        $this->DeptModel = new DeptModel;
    }
    public function index()
    {
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 10);
            $page = $this->request->param('page/d', 1);
            $total = $this->DeptModel->count();
            $data = $this->DeptModel->page($page, $limit)->select();
            foreach ($data as &$da) {
                $da['user_num'] = db('admin')->where('deptid',$da['dept_id'])->count();
                }
            $data=json_decode($data,false);
            $result = array("code" => 0,"msg" => '', "count" => $total, "data" => $data);
            return json($result);
        }
        /*$depts = $this->DeptModel->get_Tree($depts);
        //dump($depts);die;
        $this->assign('depts',$depts);*/
        return $this->fetch();

    }
    public function map()
    {
        return $this->fetch();
    }
    public function dept_add()
    {
        if ($this->request->isPost()) {

            Db::transaction(function () {
                $data['dept_name'] = $this->request->param('dept_name');
                $data['dept_pid'] = $this->request->param('dept_pid');
                $data['location'] = $this->request->param('location');
                $data['detail'] = $this->request->param('detail');
                $data['remarks'] = $this->request->param('remarks');
                $dept_path = Db('dept')->insertGetId($data);
                if(!$data['dept_pid']){
                    $update['dept_level'] = Db('dept')->field('dept_level')->where('dept_id',$data['dept_pid'])->find()['dept_level'] + 1;
                }
                $update['dept_level'] = '0';
                $update['dept_path'] = $dept_path;
                Db('dept')->where('dept_id',$dept_path)->update($update);
                $this->success('新增部门成功！');
            });
            $this->error('新增部门失败！');
        }else{
            return $this->fetch();
        }
    }
    public function dept_details()
    {
        $dept_id = $this->request->param('dept_id');
        $details = $this->DeptModel->where('dept_id',$dept_id)->find();
        if(!$details['dept_pid']){
            $details['dept_p'] = '该部门为顶级';
        }else{
            $dept_name = $this->DeptModel->where('dept_id',$details['dept_pid'])->field('dept_name')->find();
            $details['dept_p'] = $dept_name['dept_name'];
        }
        $this->assign('details',$details);
        return $this->fetch();
    }
    public function dept_edit()
    {

        if ($this->request->isPost()) {
            Db::transaction(function () {
                $dept_id = $this->request->param('dept_id');
                $data['dept_name'] = $this->request->param('dept_name');
                $data['dept_pid'] = $this->request->param('dept_pid');
                $data['location'] = $this->request->param('location');
                $data['detail'] = $this->request->param('detail');
                $data['remarks'] = $this->request->param('remarks');
                Db('dept')->where('dept_id',$dept_id)->update($data);
                if($data['dept_pid']){
                    $update['dept_level'] = Db('dept')->field('dept_level')->where('dept_id',$data['dept_pid'])->find()['dept_level'] + 1;
                }else{
                    $update['dept_level'] = 0;
                }
                Db('dept')->where('dept_id',$dept_id)->update($update);
                $this->success('编辑部门成功！');
            });
            $this->error('编辑部门失败！');
        }
        $dept_id = $this->request->param('dept_id');
        $details = $this->DeptModel->where('dept_id',$dept_id)->find();
        if(!$details['dept_pid']){
            $details['dept_p'] = '该部门为顶级';
        }else{
            $dept_name = $this->DeptModel->where('dept_id',$details['dept_pid'])->field('dept_name')->find();
            $details['dept_p'] = $dept_name['dept_name'];
        }
        $this->assign('details',$details);
        return $this->fetch();
    }
    public function dept_del(){
        $dept_id = (int)$this->request->param('dept_id/d');
        if (empty($dept_id)) {
            $this->error('ID错误');
        }
        /*$result = Db::name('eqpt_management')->where(["parentid" => $id])->find();
        if ($result) {
            $this->error("含有子菜单，无法删除！");
        }*/
        if ($this->DeptModel->where('dept_id', $dept_id)->delete()) {
            $this->success("删除部门成功！");
        } else {
            $this->error("删除部门失败！");
        }
    }
    public function get_tree_data()
    {

        $depts = Db('dept')->field('dept_id,dept_pid,dept_name')->select();
        $depts = $this->DeptModel->get_Tree($depts,'dept_id','dept_pid','dept_name');
        return $depts;
    }
}