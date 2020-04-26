<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/25
 * Time: 10:50
 */

namespace app\index\controller;

use think\Controller;
use think\Db;
use app\common\controller\Adminbase;
use app\index\model\Linkage as LinkageModel;

class Linkage extends Adminbase
{
    protected function initialize()
    {
        parent::initialize();
        $this->Linkage = new LinkageModel;
    }
    public function index()
    {
        /*$depts = $this->UserModel->get_Tree($depts);
        //dump($depts);die;
        $this->assign('depts',$depts);*/
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 10);
            $page = $this->request->param('page/d', 1);
            $res = db('linkage')->order('l_id', 'desc')->page($page, $limit)->select();
            $total = db('linkage')->count();
            $remarks = array();
            $datas = array();
            $set = ['SHB','SHBsub','paramset','paramsetnum','lingtypeset'];
            foreach ($res as $k=>$re) {
                $remarks[] = $re['remark'];
                unset($re['remark']);
                foreach ($re as $kk=>$vv) {
                    $vv = trim($vv,',');
                    $vv = explode(',',$vv);

                    if(in_array($kk,$set)){
                        foreach ($vv as $kkk=>$vvv) {
                            $datas[$k]['set'][$kkk][$kk] =$vvv;
                            $datas[$k]['remark'] = $remarks[$k];
                    }

                    }else{
                        foreach ($vv as $kkk=>$vvv) {
                            $datas[$k]['get'][$kkk][$kk] =$vvv;
                            $datas[$k]['remark'] = $remarks[$k];
                        }
                    }
                }
            }
           // $this->assign('Datas', $datas);
            $result = array("code" => 0,"msg" => '', "count" => $total, "data" => $datas);
            return json($result);
    //        dump($datas);
        }
        $res = db('linkage')->order('l_id', 'desc')->select();
        $total = db('linkage')->count();
        $remarks = array();
        $datas = array();
        $set = ['SHB','SHBsub','paramset','paramsetnum','lingtypeset'];
        foreach ($res as $k=>$re) {
            $remarks[] = $re['remark'];
            unset($re['remark']);
            foreach ($re as $kk=>$vv) {
                $vv = trim($vv,',');
                $vv = explode(',',$vv);

                if(in_array($kk,$set)){
                    foreach ($vv as $kkk=>$vvv) {
                        $datas[$k]['set'][$kkk][$kk] =$vvv;
                        $datas[$k]['remark'] = $remarks[$k];
                    }

                }else{
                    foreach ($vv as $kkk=>$vvv) {
                        $datas[$k]['get'][$kkk][$kk] =$vvv;
                        $datas[$k]['remark'] = $remarks[$k];
                    }
                }
            }
        }
        $this->assign('Datas', $datas);
        return $this->fetch();

    }

//    新增联动
    public function linkage_add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $datas = array();
            foreach ($data as $key=>$val) {
                $datas[$key] = '';
                if($key !== 'remark'){
                    if(is_array($val)){
                        foreach ($val as $vo) {

                            $datas[$key] .= $vo.',';
                        }
                    }else{
                        $datas[$key] .= $val.',';
                    }
                }
            }
            $datas['remark'] = $data['remark'];

            $res = Db('linkage')
                ->strict(false)
                ->insert($datas);

            if ($res) {
                $this->success('新增系统联动成功！');
            }
            $this->error('新增系统联动失败！');
        } else {
            $cates_data = json_encode($this->Linkage->getCates(), JSON_UNESCAPED_UNICODE);
            $this->assign('Catesdata', $cates_data);
            return $this->fetch();
        }

    }
}