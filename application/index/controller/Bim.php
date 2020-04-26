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

class Bim extends Adminbase
{
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

}