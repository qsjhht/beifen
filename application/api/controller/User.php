<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/29
 * Time: 9:50
 */

namespace app\api\controller;


class User extends Common
{
    protected function initialize()
    {
        parent::initialize();
    }

    public function login()
    {
        $data = $this->params;

        $field = "u.userid,u.password,u.encrypt,u.username,u.last_login_time,u.phone,u.nickname,u.sex,r.role_name,d.dept_name";
        $join  = [['role r', 'u.roleid = r.role_id', 'LEFT'], ['dept d', 'u.deptid = d.dept_id', 'LEFT']];
        $db_res   = db('admin')->alias('u')->field($field)->order('u.userid', 'desc')->join($join)->where('username', $data['user_name'])->find();
            $data_pwd = md5(trim($data['user_pwd']) . $db_res['encrypt']);
        if (!$db_res) {
            $this->return_msg(400, '用户名或者密码不正确！');
        }else {
            if ($db_res['password'] !== $data_pwd){
                $this->return_msg(400, '用户名或者密码不正确！!');
            }
            db('admin')->where('userid',$db_res['userid'])->setField('last_login_time', time());
            if($db_res['sex']){
                $db_res['sex'] = '男';
            }else{
                $db_res['sex'] = '女';
            }
            unset($db_res['password']); //密码永不返回
            unset($db_res['encrypt']); //密码永不返回
            $this->return_msg(200, '登陆成功！', $db_res);
        }
    }
}