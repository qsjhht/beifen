<?php
// +----------------------------------------------------------------------
// | 后台控制模块
// +----------------------------------------------------------------------
namespace app\common\controller;

use app\index\model\AdminUser as AdminUser_model;
use think\Controller;

class Adminbase extends Controller
{
    public $_userinfo; //当前登录账号信息
    //初始化
    protected function initialize()
    {
        parent::initialize();
        //验证需要登陆的行为
        $allowUrl = ['index/index/login',
            'index/index/logout',
        ];
        $rule = strtolower($this->request->module() . '/' . $this->request->controller() . '/' . $this->request->action());
        if (in_array($rule, $allowUrl)) {

        } else {
            $this->AdminUser_model = new AdminUser_model;
            //检验是否登录
            $this->competence();
            /*
            if (defined('UID')) {
                return;
            }
            define('UID', (int) $this->AdminUser_model->isLogin());
            if (false == $this->competence()) {
                //跳转到登录界面
                $this->redirect(url('index/index/login'));
                $this->error('请先登陆', url('index/index/login'));
            } else {
                //是否超级管理员
                if (!$this->AdminUser_model->isAdministrator()) {
                    //检测访问权限
                    if (!$this->checkRule($rule, [1, 2])) {
                        $this->error('未授权访问!');
                    }
                }

            }*/
        }
    }

    //验证登录
    private function competence()
    {
        //检查是否登录
        $uid = (int) $this->AdminUser_model->isLogin();
        if (empty($uid)) {
            return false;
        }
        //获取当前登录用户信息
        $userInfo = $this->AdminUser_model->getUserInfo($uid);
        if (empty($userInfo)) {
            $this->AdminUser_model->logout();
            return false;
        }
        $this->_userinfo = $userInfo;
        //是否锁定
        /*if (!$userInfo['status']) {
        User::getInstance()->logout();
        $this->error('您的帐号已经被锁定！', url('admin/index/login'));
        return false;
        }*/
        return $userInfo;

    }
    /**
     * 权限检测
     * @param string  $rule    检测的规则
     * @param string  $mode    check模式
     * @return boolean
     */
    final protected function checkRule($rule, $type = AuthRule::RULE_URL, $mode = 'url')
    {
        static $Auth = null;
        if (!$Auth) {
            $Auth = new \libs\Auth();
        }
        if (!$Auth->check($rule, UID, $type, $mode)) {
            return false;
        }
        return true;
    }

    public function return_msg($code, $msg = '', $data = [])
    {
        // 组合数据
        $return_data['code'] = $code;
        $return_data['msg']  = $msg;
        $return_data['data'] = $data;
        // 返回信息并终止脚本
        echo json_encode($return_data,JSON_UNESCAPED_UNICODE);die;
    }

}
