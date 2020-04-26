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
use Config;
use app\index\model\Patrol as PatrolModel;
use app\common\controller\Adminbase;

class Patrol extends Adminbase
{

    protected function initialize()
    {
        parent::initialize();
        $this->Patrol = new PatrolModel;
    }
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
    public function live()
    {
        return $this->fetch();
    }
    public function bim()
    {
        return $this->fetch();
    }
    public function realtime()
    {
        $sites_data = json_encode($this->Patrol->getSites());
        //dump($sites_data);

        $config['realtime_IP'] = Config::get('config.realtime_IP');
        $sites_hk = Db('sites_hk')->where('hk_did','not null')->select();
        $site_hk = array();
        foreach ($sites_hk as $item) {
//            $item['site_name'] =  $item[["hk_did"]];
            $site_hk[$item['site_name']] = $item['hk_did'];
        }

        $this->assign('Sitehk', json_encode($site_hk));
        $this->assign('Sitesdata', $sites_data);
        $this->assign('Config', $config);
        return $this->fetch();
    }
    public function sitesdata()
    {
        header('Content-Type:application/json');
        $data =  json_encode($this->Patrol->getSites());


        $user_data[] = '{"id":1,"pid":0,"title":"1-1"},{"id":2,"pid":0,"title":"1-2"},{"id":3,"pid":0,"title":"1-3"},{"id":4,"pid":1,"title":"1-1-1"},{"id":5,"pid":1,"title":"1-1-2"},{"id":6,"pid":2,"title":"1-2-1"},{"id":7,"pid":2,"title":"1-2-3"},{"id":8,"pid":3,"title":"1-3-1"},{"id":9,"pid":3,"title":"1-3-2"},{"id":10,"pid":4,"title":"1-1-1-1"},{"id":11,"pid":4,"title":"1-1-1-2"}';
        $user_data = json_encode($user_data);
        return $user_data;
    }
    public function json()
    {
        $json = array('name'=>array("iso","english","china","ufo","seo"),'data'=>array(400,200,300,100,11));
        $json = json_encode($json);
        return $json;
    }

    public function log()
    {
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 10);
            $page = $this->request->param('page/d', 1);
            $res   = db('patrol_log')->order('log_date', 'desc')->page($page, $limit)->select();

            foreach ($res as &$re) {
                $re['user_names'] = '';
                $userstr = trim($re['user_ids'],',');
                $userarr = explode(',',$userstr);
                $users = db('admin')->field('nickname')->where('userid','in',$userarr)->select();
                foreach ($users as $vo) {
                    $re['user_names'] .= $vo['nickname'].',';
                }
                $re['user_names'] = trim($re['user_names'],',');

                /*$starts = trim($re['en_start'],'-');
                $startarr = explode('-',$starts);
                $startone = db('sites')->where('id',$startarr['1'])->field('site_name')->find();
                $starttwo = db('sites')->where('id',$startarr['2'])->field('site_name')->find();
                $re['en_start'] = $startone['site_name'] . $starttwo['site_name'];

                $ends = trim($re['en_end'],'-');
                $endarr = explode('-',$ends);
                $endone = db('sites')->where('id',$endarr['1'])->field('site_name')->find();
                $endtwo = db('sites')->where('id',$endarr['2'])->field('site_name')->find();
                $re['en_end'] = $endone['site_name'] . $endtwo['site_name'];*/
            }
            $total = db('patrol_log')->count();
            $result = array("code" => 0,"msg" => '', "count" => $total, "data" => $res);
            return json($result);
        }
        /*$depts = $this->UserModel->get_Tree($depts);
        //dump($depts);die;
        $this->assign('depts',$depts);*/
        return $this->fetch();
    }

    public function log_add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['patrol_ids'] = '';
            $data['user_ids'] = '';

           /* $data['en_start'] = '';
            $data['en_end'] = '';
            foreach ($data['start'] as $v1) {
                $data['en_start'] .= $v1.'-';
            }
            foreach ($data['end'] as $v2) {
                $data['en_end'] .= $v2.'-';
            }
*/
            foreach ($data['patrol'] as $id=>$d) {
                $data['patrol_ids'] .= $id .',';
            }

            foreach ($data['user'] as $id=>$d) {
                $data['user_ids'] .= $id .',';
            }

            $data['registrant'] = $this->_userinfo['nickname'];

            $res = Db('patrol_log')
                ->strict(false)
                ->insert($data);
            if ($res){
                $this->success('新增巡检记录成功！');
            }$this->error('新增巡检记录失败！');
        }else{
            $sites_data = json_encode($this->Patrol->getSites());
            //dump($sites_data);
            $config['realtime_IP'] = Config::get('config.realtime_IP');
            $this->assign('Sitesdata', $sites_data);
            $user = Db('admin')->field('userid,nickname')->select();
            $this->assign('User',$user);
            return $this->fetch();
        }
    }

    public function log_details()
    {
        $log_id = $this->request->param('log_id');
        $res   = db('patrol_log')->where('log_id',$log_id)->find();
        $userstr = trim($res['user_ids'],',');
        $userarr = explode(',',$userstr);
        $patrolstr = trim($res['patrol_ids'],',');
        $patrolarr = explode(',',$patrolstr);
        foreach ($userarr as $item) {
            $user =  db('admin')->where('userid',$item)->field('nickname')->find();
            $res['users'][] = $user['nickname'];
        }

        foreach ($patrolarr as $item) {
            $res['patrols'][] = $item;
        }

        /*$starts = trim($res['en_start'],'-');
        $startarr = explode('-',$starts);
        $startone = db('sites')->where('id',$startarr['1'])->field('site_name')->find();
        $starttwo = db('sites')->where('id',$startarr['2'])->field('site_name')->find();
        $res['en_start'] = $startone['site_name'] . $starttwo['site_name'];

        $ends = trim($res['en_end'],'-');
        $endarr = explode('-',$ends);
        $endone = db('sites')->where('id',$endarr['1'])->field('site_name')->find();
        $endtwo = db('sites')->where('id',$endarr['2'])->field('site_name')->find();
        $res['en_end'] = $endone['site_name'] . $endtwo['site_name'];*/

        $this->assign('Info',$res);
        $user = Db('admin')->field('userid,nickname')->select();
        $this->assign('User',$user);
        return $this->fetch();
    }
}