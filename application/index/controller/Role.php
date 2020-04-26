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
use app\index\model\Role as RoleModel;
use app\common\controller\Adminbase;

class Role extends Adminbase
{
    protected function initialize()
    {
        parent::initialize();
        $this->RoleModel = new RoleModel;
    }
    public function index()
    {
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 10);
            $page = $this->request->param('page/d', 1);
            $total = $this->RoleModel->count();
            $data = $this->RoleModel->page($page, $limit)->select();
            foreach ($data as &$da) {
                $da['user_num'] = db('admin')->where('roleid',$da['role_id'])->count();
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

    public function role_add()
    {
        if ($this->request->isPost()) {

            Db::transaction(function () {
                $data['role_name'] = $this->request->param('role_name');
                $data['role_detail'] = $this->request->param('role_detail');
                $data['role_auth_ids'] = $this->request->param('role_auth_ids');
                $data['role_auth_names'] = $this->request->param('role_auth_names');

                $data['role_auth_ac'] = $this -> saveAuthAC($data['role_auth_ids']);
                Db('role')->insertGetId($data);
                $this->success('新增角色成功！');
            });
            $this->error('新增角色失败！');
        }else{
            return $this->fetch();
        }
    }
    public function role_details()
    {
        $role_id = $this->request->param('role_id');
        $details = $this->RoleModel->where('role_id',$role_id)->find();
        /*if(!$details['dept_pid']){
            $details['dept_p'] = '该角色为顶级';
        }else{
            $dept_name = $this->RoleModel->where('dept_id',$details['dept_pid'])->field('dept_name')->find();
            $details['dept_p'] = $dept_name['dept_name'];
        }*/
        $this->assign('details',$details);
        return $this->fetch();
    }
    public function role_edit()
    {

        if ($this->request->isPost()) {
            Db::transaction(function () {
                $role_id = $this->request->param('role_id');
                $data['role_name'] = $this->request->param('role_name');
                $data['role_detail'] = $this->request->param('role_detail');
                $data['role_auth_ids'] = $this->request->param('role_auth_ids');
                $data['role_auth_names'] = $this->request->param('role_auth_names');
                $data['role_auth_ac'] = $this -> saveAuthAC($data['role_auth_ids']);
                Db('role')->where('role_id',$role_id)->update($data);
                $this->success('编辑角色成功！');
            });
            $this->error('编辑角色失败！');
        }
        $role_id = $this->request->param('role_id');
        $details = $this->RoleModel->where('role_id',$role_id)->find();
        $this->assign('details',$details);
        return $this->fetch();
    }
    public function role_del(){
        $role_id = (int)$this->request->param('role_id/d');
        if (empty($role_id)) {
            $this->error('ID错误');
        }
        /*$result = Db::name('eqpt_management')->where(["parentid" => $id])->find();
        if ($result) {
            $this->error("含有子菜单，无法删除！");
        }*/
        if ($this->RoleModel->where('role_id', $role_id)->delete()) {
            $this->success("删除角色成功！");
        } else {
            $this->error("删除角色失败！");
        }
    }
    public function get_tree_data()
    {
        $auths = Db('auth')->field('auth_id,auth_pid,auth_name')->select();
        $auths = $this->RoleModel->get_Tree($auths,'auth_id','auth_pid','auth_name');
        return $auths;
    }
    //根据$auth_ids的字符串获得对应权限的"控制器-操作方法"字符串
    private function saveAuthAC($auth_ids){
        //根据$auth_ids获得权限信息
        //select() 全部数据
        //select(数字) 单个记录信息(id=数字)
        //select("数字,数字,数字。。")多个记录信息(id in 数字,数字,数字。。)
        $authinfo = Db('auth')->select($auth_ids);
        $s = "";
        foreach($authinfo as $k => $v){
            if(!empty($v['auth_c']) && !empty($v['auth_a']))
                $s .= $v['auth_c'].'-'.$v['auth_a'].",";
        }
        $s = rtrim($s,',');
        return $s;
    }
}