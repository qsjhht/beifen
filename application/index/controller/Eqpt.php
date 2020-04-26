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
use app\index\model\Eqpts as Eqpt_Model;
use app\common\controller\Adminbase;


class Eqpt extends Adminbase
{
    protected function initialize()
    {
        parent::initialize();
        $this->Eqpt_Model = new Eqpt_Model;
    }
    //$this->

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

    //设备台账
    public function ledger()
    {
        //实例化工具树类
        $tree = new \util\Tree();
        //获取分类菜单
        $resultcate = Db('categorys')->order(array( 'id' => 'ESC'))->where('isShow','1')->select();
        $arraycate = array();
        foreach ($resultcate as $r) {
            $r['selected'] = $r['id'] == 0 ? 'selected' : '';
            $arraycate[] = $r;
        }
        $strcate = "<option value='\$id' \$selected>\$spacer \$cate_name</option>";
        $tree->init($arraycate);
        $select_categorys = $tree->get_tree(0, $strcate);
        /*$cate = $this->request->param('cate');
        if($tree->get_childs($cate)){
            $cates = $cate .=  ','. implode(',',$tree->get_childs($cate));
        }else{
            $cates = $cate;
        }

        dump($cates);*/
        //<option value='1' > 园博园大街</option><option value='6' >&nbsp;├ YBY-001</option><option value='38' >&nbsp;│&nbsp;├ YBY-001-001</option><option value='39' >&nbsp;│&nbsp;├ YBY-001-002</option><option value='40' >&nbsp;│&nbsp;└ YBY-001-003</option><option value='7' >&nbsp;├ YBY-002</option><option value='41' >&nbsp;│&nbsp;├ YBY-002-001</option><option value='42' >&nbsp;│&nbsp;└ YBY-002-002</option><option value='8' >&nbsp;├ YBY-003</option><option value='43' >&nbsp;│&nbsp;└ YBY-003-001</option><option value='9' >&nbsp;├ YBY-004</option><option value='10' >&nbsp;├ YBY-005</option><option value='11' >&nbsp;└ YBY-006</option><option value='2' > 新城大道</option><option value='12' >&nbsp;├ XC-001</option><option value='13' >&nbsp;├ XC-002</option><option value='14' >&nbsp;├ XC-003</option><option value='15' >&nbsp;├ XC-004</option><option value='16' >&nbsp;├ XC-005</option><option value='17' >&nbsp;└ XC-006</option><option value='3' > 太行大街</option><option value='18' >&nbsp;├ TH-001</option><option value='19' >&nbsp;├ TH-002</option><option value='20' >&nbsp;├ TH-003</option><option value='21' >&nbsp;├ TH-004</option><option value='22' >&nbsp;├ TH-005</option><option value='23' >&nbsp;├ TH-006</option><option value='24' >&nbsp;├ TH-007</option><option value='25' >&nbsp;└ TH-008</option><option value='4' > 迎旭大道</option><option value='26' >&nbsp;├ YX-001</option><option value='27' >&nbsp;├ YX-002</option><option value='28' >&nbsp;├ YX-003</option><option value='29' >&nbsp;├ YX-004</option><option value='30' >&nbsp;└ YX-005</option><option value='5' > 隆兴路</option><option value='31' >&nbsp;├ LX-001</option><option value='32' >&nbsp;├ LX-002</option><option value='33' >&nbsp;├ LX-003</option><option value='34' >&nbsp;├ LX-004</option><option value='35' >&nbsp;├ LX-005</option><option value='36' >&nbsp;├ LX-006</option><option value='37' >&nbsp;└ LX-007</option><option value='999' > 未安装定位</option>
        //获取位置菜单
        $resultsite = Db('site')->order(array( 'id' => 'ESC'))->select();
        $arraysite = array();

        foreach ($resultsite as $r) {
            $r['selected'] = $r['id'] == 0 ? 'selected' : '';
            $arraysite[] = $r;
        }
        $strsite = "<option value='\$id' \$selected>\$spacer \$site_name</option>";
        $tree->init($arraysite);
        $select_sites = $tree->get_tree(0, $strsite);
        //dump($select_sites);die;
        //$where  =strpos($select_sites,"<option value='999'");
        //将下拉选项 设备未定位选项删除
        //$select_sites  =substr($select_sites,0,strpos($select_sites,"<option value='999'"));
        //dump($select_sites);
        //获取设备信息
        /*$eqpts = Db::table('eqpts')
            ->field('e.id,e.eqpt_name,e.eqpt_type,e.eqpt_brand,e.eqpt_num,e.eqpt_site,e.eqpt_status,c.cate_name')
            ->alias('e')
            ->join('category c','e.eqpt_cate_id = c.id')
            ->select();*/
        //注入
        $this->assign("select_sites", $select_sites);
        $this->assign("select_categorys", $select_categorys);
        /*        $this->assign('Eqpts',$eqpts);*/
        /*$data = $this->Eqpt_Model
            ->order(array('id' => 'DESC'))
            ->field('e.id,e.eqpt_name,e.eqpt_type,e.eqpt_brand,e.eqpt_num,e.eqpt_site,e.eqpt_status,c.cate_name')
            ->alias('e')
            ->join('category c','e.eqpt_cate_id = c.id')
            ->select();
        $total = $this->Eqpt_Model->order('id')->count();
        $result = array("code" => 0, "count" => $total, "data" => $data);
        dump(json_encode($result));*/
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 10);
            $page = $this->request->param('page/d', 1);
            $keyword = $this->request->param('keyword');
            $cate = $this->request->param('cate');
            $site = $this->request->param('site');
            /*$Installed = $this->request->param('Installed');
            $unerected = $this->request->param('unerected');
            $normal = $this->request->param('normal');
            $fault = $this->request->param('fault');
            $scrap = $this->request->param('scrap');*/
            //$statusNum = $normal + $fault + $scrap;
            //$installNum = $Installed + $unerected;
            /*switch ($statusNum) {
                case 0:
                    $whereStatus ='e.eqpt_id <> 0';
                    break;
                case 1:
                    $whereStatus = 'e.eqpt_status = "正常"';
                    break;
                case 2:
                    $whereStatus = 'e.eqpt_status = "故障"';
                    break;
                case 3:
                    $whereStatus = 'e.eqpt_status = "正常" OR e.eqpt_status = "故障"';
                    break;
                case 4:
                    $whereStatus = 'e.eqpt_status = "报废"';
                    break;
                case 6:
                    $whereStatus = 'e.eqpt_status = "报废" OR e.eqpt_status = "故障"';
                    break;
                case 7:
                    $whereStatus ='e.eqpt_id <> 0';
                    break;
            }*/

            //数据库设置未安装设备 设备位置id eqpt_site_id 为999
            /*switch ($installNum){
                case 0:
                    $whereInstall ='e.eqpt_id <> 0';
                    break;
                case 1:
                    $whereInstall = 'e.eqpt_site_id <> 999';
                    break;
                case 2:
                    $whereInstall = 'e.eqpt_site_id = 999';
                    break;
                case 3:
                    $whereInstall = 'e.eqpt_id <> 0';;
                    break;
            }*/

            //实例化工具树类
            $tree = new \util\Tree();
            //获取分类菜单
            $resultcate = Db('categorys')->order(array( 'id' => 'ESC'))->where('isShow','1')->select();
            $arraycate = array();
            foreach ($resultcate as $r) {
                $r['selected'] = $r['id'] == 0 ? 'selected' : '';
                $arraycate[] = $r;
            }
            $tree->init($arraycate);
            if($tree->get_childs($cate)){
                $cates = $cate .=  ','. implode(',',$tree->get_childs($cate));
            }else{
                $cates = $cate;
            }

            //获取位置菜单
            $resultsite = Db('site')->order(array( 'id' => 'ESC'))->select();
            $arraysite = array();
            foreach ($resultsite as $r) {
                $r['selected'] = $r['id'] == 0 ? 'selected' : '';
                $arraysite[] = $r;
            }
            $tree->init($arraysite);
            if($tree->get_childs($site)){
                $sites = $site .=  ','. implode(',',$tree->get_childs($site));
            }else{
                $sites = $site;
            }
            $datas = Db::connect('sqlsrv_config')
                ->page($page, $limit)
                ->table('rmd_base_equipment')
                ->where('equipment_code|equipment_name', 'like', '%' . $keyword . '%')
                ->where('prevent_fire_area_id', 'in', $sites)
                ->where('catalogue', 'in', $cates)
                ->select();
            //dump($cate);die;
            //$data=json_decode($datas,false);

            $total = Db::connect('sqlsrv_config')
                ->table('rmd_base_equipment')
                ->where('equipment_code|equipment_name', 'like', '%' . $keyword . '%')
                ->where('prevent_fire_area_id', 'in', $sites)
                ->where('catalogue', 'in', $cates)
                ->count();
            $result = array("code" => 0,"msg" => '', "count" => $total, "data" => $datas);
            return json($result);
            /*$data = $this->Eqpt_Model->page($page, $limit)
                    ->order(array('eqpt_id' => 'DESC'))
                    ->field('e.eqpt_id,e.eqpt_model,e.eqpt_type,e.eqpt_brand,e.eqpt_num,e.eqpt_site_detail,s.site_name,e.eqpt_status,c.cate_name,e.eqpt_had_model')
                    ->alias('e')
                    ->join('category c','e.eqpt_cate_id = c.id')
                    ->join('site s','e.eqpt_site_id = s.id')
                    ->where('e.eqpt_id|c.cate_name|e.eqpt_num|e.eqpt_brand|e.eqpt_model|e.eqpt_type|s.site_name','like','%' . $keyword . '%')
                    ->where('c.id','in',$cates)
                    ->where('s.id','in',$sites)
                    ->where($whereStatus)
                    ->where($whereInstall)

                    ->fault($fault)
                    ->scrap($scrap)
                    ->select();
                $data=json_decode($data,false);
                $total = $this->Eqpt_Model
                    ->field('e.eqpt_id,e.eqpt_model,e.eqpt_type,e.eqpt_brand,e.eqpt_num,e.eqpt_site_detail,site_name,e.eqpt_status,c.cate_name,e.eqpt_had_model')
                    ->alias('e')
                    ->join('category c','e.eqpt_cate_id = c.id')
                    ->join('site s','e.eqpt_site_id = s.id')
                    ->where('e.eqpt_id|c.cate_name|e.eqpt_num|e.eqpt_brand|e.eqpt_model|e.eqpt_type|s.site_name','like','%' . $keyword . '%')
                    ->where('c.id','in',$cates)
                    ->where('s.id','in',$sites)
                    ->where($whereStatus)
                    ->where($whereStatus)


                    ->fault($fault)
                    ->scrap($scrap)
                    ->count();

                $result = array("code" => 0,"msg" => '', "count" => $total, "data" => $data);

            return json($result);*/
        }
        return $this->fetch();
    }

    //设备拆解
    public function decompose()
    {
        //实例化工具树类
        $tree = new \util\Tree();
        //获取分类菜单
        $resultcate = Db('categorys')->order(array( 'id' => 'ESC'))->where('isShow','1')->select();
        $arraycate = array();
        foreach ($resultcate as $r) {
            $r['selected'] = $r['id'] == 0 ? 'selected' : '';
            $arraycate[] = $r;
        }
        $strcate = "<option value='\$id' \$selected>\$spacer \$cate_name</option>";
        $tree->init($arraycate);
        $select_categorys = $tree->get_tree(0, $strcate);
        /*$cate = $this->request->param('cate');
        if($tree->get_childs($cate)){
            $cates = $cate .=  ','. implode(',',$tree->get_childs($cate));
        }else{
            $cates = $cate;
        }

        dump($cates);*/
        //<option value='1' > 园博园大街</option><option value='6' >&nbsp;├ YBY-001</option><option value='38' >&nbsp;│&nbsp;├ YBY-001-001</option><option value='39' >&nbsp;│&nbsp;├ YBY-001-002</option><option value='40' >&nbsp;│&nbsp;└ YBY-001-003</option><option value='7' >&nbsp;├ YBY-002</option><option value='41' >&nbsp;│&nbsp;├ YBY-002-001</option><option value='42' >&nbsp;│&nbsp;└ YBY-002-002</option><option value='8' >&nbsp;├ YBY-003</option><option value='43' >&nbsp;│&nbsp;└ YBY-003-001</option><option value='9' >&nbsp;├ YBY-004</option><option value='10' >&nbsp;├ YBY-005</option><option value='11' >&nbsp;└ YBY-006</option><option value='2' > 新城大道</option><option value='12' >&nbsp;├ XC-001</option><option value='13' >&nbsp;├ XC-002</option><option value='14' >&nbsp;├ XC-003</option><option value='15' >&nbsp;├ XC-004</option><option value='16' >&nbsp;├ XC-005</option><option value='17' >&nbsp;└ XC-006</option><option value='3' > 太行大街</option><option value='18' >&nbsp;├ TH-001</option><option value='19' >&nbsp;├ TH-002</option><option value='20' >&nbsp;├ TH-003</option><option value='21' >&nbsp;├ TH-004</option><option value='22' >&nbsp;├ TH-005</option><option value='23' >&nbsp;├ TH-006</option><option value='24' >&nbsp;├ TH-007</option><option value='25' >&nbsp;└ TH-008</option><option value='4' > 迎旭大道</option><option value='26' >&nbsp;├ YX-001</option><option value='27' >&nbsp;├ YX-002</option><option value='28' >&nbsp;├ YX-003</option><option value='29' >&nbsp;├ YX-004</option><option value='30' >&nbsp;└ YX-005</option><option value='5' > 隆兴路</option><option value='31' >&nbsp;├ LX-001</option><option value='32' >&nbsp;├ LX-002</option><option value='33' >&nbsp;├ LX-003</option><option value='34' >&nbsp;├ LX-004</option><option value='35' >&nbsp;├ LX-005</option><option value='36' >&nbsp;├ LX-006</option><option value='37' >&nbsp;└ LX-007</option><option value='999' > 未安装定位</option>
        //获取位置菜单
        $resultsite = Db('site')->order(array( 'id' => 'ESC'))->select();
        $arraysite = array();

        foreach ($resultsite as $r) {
            $r['selected'] = $r['id'] == 0 ? 'selected' : '';
            $arraysite[] = $r;
        }
        $strsite = "<option value='\$id' \$selected>\$spacer \$site_name</option>";
        $tree->init($arraysite);
        $select_sites = $tree->get_tree(0, $strsite);
        //dump($select_sites);die;
        //$where  =strpos($select_sites,"<option value='999'");
        //将下拉选项 设备未定位选项删除
        //$select_sites  =substr($select_sites,0,strpos($select_sites,"<option value='999'"));
        //dump($select_sites);
        //获取设备信息
        /*$eqpts = Db::table('eqpts')
            ->field('e.id,e.eqpt_name,e.eqpt_type,e.eqpt_brand,e.eqpt_num,e.eqpt_site,e.eqpt_status,c.cate_name')
            ->alias('e')
            ->join('category c','e.eqpt_cate_id = c.id')
            ->select();*/
        //注入
        $this->assign("select_sites", $select_sites);
        $this->assign("select_categorys", $select_categorys);
        /*        $this->assign('Eqpts',$eqpts);*/
        /*$data = $this->Eqpt_Model
            ->order(array('id' => 'DESC'))
            ->field('e.id,e.eqpt_name,e.eqpt_type,e.eqpt_brand,e.eqpt_num,e.eqpt_site,e.eqpt_status,c.cate_name')
            ->alias('e')
            ->join('category c','e.eqpt_cate_id = c.id')
            ->select();
        $total = $this->Eqpt_Model->order('id')->count();
        $result = array("code" => 0, "count" => $total, "data" => $data);
        dump(json_encode($result));*/
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 10);
            $page = $this->request->param('page/d', 1);
            $keyword = $this->request->param('keyword');
            $cate = $this->request->param('cate');
            $site = $this->request->param('site');
            /*$Installed = $this->request->param('Installed');
            $unerected = $this->request->param('unerected');
            $normal = $this->request->param('normal');
            $fault = $this->request->param('fault');
            $scrap = $this->request->param('scrap');*/
            //$statusNum = $normal + $fault + $scrap;
            //$installNum = $Installed + $unerected;
            /*switch ($statusNum) {
                case 0:
                    $whereStatus ='e.eqpt_id <> 0';
                    break;
                case 1:
                    $whereStatus = 'e.eqpt_status = "正常"';
                    break;
                case 2:
                    $whereStatus = 'e.eqpt_status = "故障"';
                    break;
                case 3:
                    $whereStatus = 'e.eqpt_status = "正常" OR e.eqpt_status = "故障"';
                    break;
                case 4:
                    $whereStatus = 'e.eqpt_status = "报废"';
                    break;
                case 6:
                    $whereStatus = 'e.eqpt_status = "报废" OR e.eqpt_status = "故障"';
                    break;
                case 7:
                    $whereStatus ='e.eqpt_id <> 0';
                    break;
            }*/

            //数据库设置未安装设备 设备位置id eqpt_site_id 为999
            /*switch ($installNum){
                case 0:
                    $whereInstall ='e.eqpt_id <> 0';
                    break;
                case 1:
                    $whereInstall = 'e.eqpt_site_id <> 999';
                    break;
                case 2:
                    $whereInstall = 'e.eqpt_site_id = 999';
                    break;
                case 3:
                    $whereInstall = 'e.eqpt_id <> 0';;
                    break;
            }*/

            //实例化工具树类
            $tree = new \util\Tree();
            //获取分类菜单
            $resultcate = Db('categorys')->order(array('id' => 'ESC'))->where('isShow','1')->select();
            $arraycate = array();
            foreach ($resultcate as $r) {
                $r['selected'] = $r['id'] == 0 ? 'selected' : '';
                $arraycate[] = $r;
            }
            $tree->init($arraycate);
            if ($tree->get_childs($cate)) {
                $cates = $cate .= ',' . implode(',', $tree->get_childs($cate));
            } else {
                $cates = $cate;
            }

            //获取位置菜单
            $resultsite = Db('site')->order(array('id' => 'ESC'))->select();
            $arraysite = array();
            foreach ($resultsite as $r) {
                $r['selected'] = $r['id'] == 0 ? 'selected' : '';
                $arraysite[] = $r;
            }
            $tree->init($arraysite);
            if ($tree->get_childs($site)) {
                $sites = $site .= ',' . implode(',', $tree->get_childs($site));
            } else {
                $sites = $site;
            }

            $datas = Db::connect('sqlsrv_config')
                ->page($page, $limit)
                ->table('rmd_base_equipment')
                ->where('equipment_code|equipment_name', 'like', '%' . $keyword . '%')
                ->where('catalogue', 'in', '35,34')
                ->where('prevent_fire_area_id', 'in', $sites)
                ->where('catalogue', 'in', $cates)
                ->select();
            //dump($cate);die;
            //$data=json_decode($datas,false);
            $total = Db::connect('sqlsrv_config')
                ->table('rmd_base_equipment')
                ->where('equipment_code|equipment_name', 'like', '%' . $keyword . '%')
                ->where('catalogue', 'in', '35,34')
                ->where('prevent_fire_area_id', 'in', $sites)
                ->where('catalogue', 'in', $cates)
                ->count();
            $result = array("code" => 0, "msg" => '', "count" => $total, "data" => $datas);
            return json($result);
            /*$data = $this->Eqpt_Model->page($page, $limit)
                    ->order(array('eqpt_id' => 'DESC'))
                    ->field('e.eqpt_id,e.eqpt_model,e.eqpt_type,e.eqpt_brand,e.eqpt_num,e.eqpt_site_detail,s.site_name,e.eqpt_status,c.cate_name,e.eqpt_had_model')
                    ->alias('e')
                    ->join('category c','e.eqpt_cate_id = c.id')
                    ->join('site s','e.eqpt_site_id = s.id')
                    ->where('e.eqpt_id|c.cate_name|e.eqpt_num|e.eqpt_brand|e.eqpt_model|e.eqpt_type|s.site_name','like','%' . $keyword . '%')
                    ->where('c.id','in',$cates)
                    ->where('s.id','in',$sites)
                    ->where($whereStatus)
                    ->where($whereInstall)

                    ->fault($fault)
                    ->scrap($scrap)
                    ->select();
                $data=json_decode($data,false);
                $total = $this->Eqpt_Model
                    ->field('e.eqpt_id,e.eqpt_model,e.eqpt_type,e.eqpt_brand,e.eqpt_num,e.eqpt_site_detail,site_name,e.eqpt_status,c.cate_name,e.eqpt_had_model')
                    ->alias('e')
                    ->join('category c','e.eqpt_cate_id = c.id')
                    ->join('site s','e.eqpt_site_id = s.id')
                    ->where('e.eqpt_id|c.cate_name|e.eqpt_num|e.eqpt_brand|e.eqpt_model|e.eqpt_type|s.site_name','like','%' . $keyword . '%')
                    ->where('c.id','in',$cates)
                    ->where('s.id','in',$sites)
                    ->where($whereStatus)
                    ->where($whereStatus)


                    ->fault($fault)
                    ->scrap($scrap)
                    ->count();

                $result = array("code" => 0,"msg" => '', "count" => $total, "data" => $data);

            return json($result);*/
        }
        return $this->fetch();
    }
    //设备台账添加
    public function eqpt_add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $data['eqpt_ins_time'] =  strtotime($data['eqpt_ins_time']);
            //dump($data);
            $fileds = array('eqpt_site_id'=>999,'eqpt_type'=>$data['eqpt_type'],'eqpt_brand'=>$data['eqpt_brand'],'eqpt_cate_id'=>$data['eqpt_cate'],'eqpt_model'=>$data['eqpt_model'],'eqpt_status'=>$data['eqpt_status'],'eqpt_ins_time'=>$data['eqpt_ins_time']);
            //$this->Eqpt_Model->save($fileds);
            //开启事务
            $db = $this->Eqpt_Model->db(false);
            $db->startTrans();
            try {
                $this->Eqpt_Model->save($fileds);
                //获取新增设备表设备id
                $id = $this->Eqpt_Model->id;
                //字段
                $details_data = ['eqpt_id' => $id, 'eqpt_rated' => $data['eqpt_rated'], 'eqpt_durable' => $data['eqpt_durable'], 'eqpt_supplier' => $data['eqpt_supplier'], 'eqpt_buyer' => $data['eqpt_buyer'], 'eqpt_service_MP' => $data['eqpt_service_MP'], 'eqpt_quality' => $data['eqpt_quality'], 'eqpt_upkeep_info' => $data['eqpt_upkeep_info'], 'eqpt_remarks' => $data['eqpt_remarks']];
                //新增设备详情表
                Db('eqpt_details')->insert($details_data);
                $db->commit();
                $this->success('添加设备成功','index/eqpt/eqpt_add','',2);
            } catch (Exception $e) {
                //失败回滚
                $db->rollback();
                $this->error('新增失败');
            }
            /*if ($this->Eqpt_Model->insert($fileds)) {
                //cach e('system_config', null);
                $this->success('添加设备分组信息成功','admin/eqpt_management/index');
            } else {
                $this->error('添加设备分组信息失败');
            }*/
        }else{
            //实例化工具树类
            $tree = new \util\Tree();
            //获取分类菜单
            $resultcate = Db('categorys')->order(array( 'id' => 'ESC'))->where('isShow','1')->select();
            $arraycate = array();
            foreach ($resultcate as $r) {
                $r['selected'] = $r['id'] == 0 ? 'selected' : '';
                $arraycate[] = $r;
            }
            $strcate = "<option value='\$id' \$selected>\$spacer \$cate_name</option>";
            $tree->init($arraycate);
            $select_categorys = $tree->get_tree(0, $strcate);
            $cate = $this->request->param('cate');
            if($tree->get_childs($cate)){
                $cates = $cate .=  ','. implode(',',$tree->get_childs($cate));
            }else{
                $cates = $cate;
            }

            //获取位置菜单
            $resultsite = Db('site')->order(array( 'id' => 'ESC'))->select();
            $arraysite = array();
            foreach ($resultsite as $r) {
                $r['selected'] = $r['id'] == 0 ? 'selected' : '';
                $arraysite[] = $r;
            }
            $strsite = "<option value='\$id' \$selected>\$spacer \$site_name</option>";
            $tree->init($arraysite);
            $select_sites = $tree->get_tree(0, $strsite);

            //获取设备信息
            /*$eqpts = Db::table('eqpts')
                ->field('e.id,e.eqpt_name,e.eqpt_type,e.eqpt_brand,e.eqpt_num,e.eqpt_site,e.eqpt_status,c.cate_name')
                ->alias('e')
                ->join('category c','e.eqpt_cate_id = c.id')
                ->select();*/
            //注入
            $this->assign("select_sites", $select_sites);
            $this->assign("select_categorys", $select_categorys);
            return $this->fetch();
        }
    }
    public function eqpt_details(){
        $data = $this->request->param();
        //获取设备信息
        $eqpts = Db::table('eqpts')
            ->alias('e')
            ->field('e.eqpt_id,e.eqpt_type,e.eqpt_num,e.eqpt_brand,s.site_name,e.eqpt_status,e.eqpt_model,e.eqpt_ins_time,c.cate_name,d.eqpt_rated,d.eqpt_durable,d.eqpt_supplier,d.eqpt_buyer,d.eqpt_service_MP,d.eqpt_quality,d.eqpt_upkeep_info,d.eqpt_remarks')
            ->join('categorys c','e.eqpt_cate_id = c.id')
            ->join('site s','e.eqpt_site_id = s.id')
            ->join('eqpt_details d','e.eqpt_id = d.eqpt_id')
            ->where('e.eqpt_id',$data['eqpt_id'])
            ->find();
        //注入
        //dump($eqpts);
        $eqpts['eqpt_ins_time'] = date("Y-m-d H:i:s",$eqpts['eqpt_ins_time']) ;
        $this->assign("eqpts", $eqpts);
        //dump($eqpts);
        return $this->fetch();
    }
    public function eqpt_edit(){
        $eqpt_id = $this->request->param();
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $eqpts = Db::table('eqpts')
                ->alias('e')
                ->field('e.eqpt_id,e.eqpt_cate_id,e.eqpt_site_id,e.eqpt_type,e.eqpt_brand,e.eqpt_model,e.eqpt_status,e.eqpt_ins_time,d.eqpt_rated,d.eqpt_durable,d.eqpt_supplier,d.eqpt_buyer,d.eqpt_service_MP,d.eqpt_quality,d.eqpt_upkeep_info,d.eqpt_remarks')
                ->join('categorys c', 'e.eqpt_cate_id = c.id')
                ->join('site s', 'e.eqpt_site_id = s.id')
                ->join('eqpt_details d', 'e.eqpt_id = d.eqpt_id')
                ->where('e.eqpt_id', $eqpt_id['eqpt_id'])
                ->find();
            $eqpts['eqpt_ins_time'] = date("Y-m-d H:i:s",$eqpts['eqpt_ins_time']) ;
            if ($eqpts == $data){
                $this->error('数据无变更！','','',1);
            }
            /*dump($data);
            die();*/
            $eqpt_fileds = array('eqpt_site_id'=>$data['eqpt_site_id'],'eqpt_type'=>$data['eqpt_type'],'eqpt_brand'=>$data['eqpt_brand'],'eqpt_cate_id'=>$data['eqpt_cate_id'],'eqpt_model'=>$data['eqpt_model'],'eqpt_status'=>$data['eqpt_status'],'eqpt_ins_time'=>$data['eqpt_ins_time']);
            $details_data = [ 'eqpt_rated' => $data['eqpt_rated'], 'eqpt_durable' => $data['eqpt_durable'], 'eqpt_supplier' => $data['eqpt_supplier'], 'eqpt_buyer' => $data['eqpt_buyer'], 'eqpt_service_MP' => $data['eqpt_service_MP'], 'eqpt_quality' => $data['eqpt_quality'], 'eqpt_upkeep_info' => $data['eqpt_upkeep_info'], 'eqpt_remarks' => $data['eqpt_remarks']];
            //$this->Eqpt_Model->save($fileds);
            //开启事务
            $db = $this->Eqpt_Model->db(false);
            $db->startTrans();
            try {
                //$this->Eqpt_Model->update($fileds);
                //获取新增设备表设备id
                //$id = $this->Eqpt_Model->id;
                //字段
                //新增设备详情表
                Db('eqpts')
                    ->where('eqpt_id', $eqpt_id['eqpt_id'])
                    ->update($eqpt_fileds);
                Db('eqpt_details')
                    ->where('eqpt_id', $eqpt_id['eqpt_id'])
                    ->update($details_data);
                $db->commit();
                $this->success('设备信息变更成功！',url('index/eqpt/eqpt_edit',['eqpt_id'=>$data['eqpt_id']]),"",2);
            } catch (Exception $e) {
                //失败回滚
                $db->rollback();
                $this->error('设备信息变更失败！');
            }
            /*if ($this->Eqpt_Model->insert($fileds)) {
                //cach e('system_config', null);
                $this->success('添加设备分组信息成功','admin/eqpt_management/index');
            } else {
                $this->error('添加设备分组信息失败');
            }*/
        }else {
            //获取设备信息
            $eqpts = Db::table('eqpts')
                ->alias('e')
                ->field('e.eqpt_id,e.eqpt_type,e.eqpt_cate_id,e.eqpt_site_id,e.eqpt_num,e.eqpt_brand,e.eqpt_site_id,s.site_name,e.eqpt_status,e.eqpt_model,e.eqpt_ins_time,c.cate_name,d.eqpt_rated,d.eqpt_durable,d.eqpt_supplier,d.eqpt_buyer,d.eqpt_service_MP,d.eqpt_quality,d.eqpt_upkeep_info,d.eqpt_remarks')
                ->join('categorys c', 'e.eqpt_cate_id = c.id')
                ->join('site s', 'e.eqpt_site_id = s.id')
                ->join('eqpt_details d', 'e.eqpt_id = d.eqpt_id')
                ->where('e.eqpt_id', $eqpt_id['eqpt_id'])
                ->find();
            //注入
            //dump($eqpts);
            $eqpts['eqpt_ins_time'] = date("Y-m-d H:i:s", $eqpts['eqpt_ins_time']);
            //实例化工具树类
            $tree = new \util\Tree();
            //获取分类菜单
            $resultcate = Db('categorys')->order(array('id' => 'ESC'))->select();
            $arraycate = array();
            foreach ($resultcate as $r) {
                $r['selected'] = $r['id'] == $eqpts['eqpt_cate_id'] ? 'selected' : '';
                $arraycate[] = $r;
            }

            $strcate = "<option value='\$id' \$selected>\$spacer \$cate_name</option>";
            $tree->init($arraycate);
            $select_categorys = $tree->get_tree(0, $strcate);
            $cate = $this->request->param('cate');
            if ($tree->get_childs($cate)) {
                $cates = $cate .= ',' . implode(',', $tree->get_childs($cate));
            } else {
                $cates = $cate;
            }

            //获取位置菜单
            $resultsite = Db('site')->order(array('id' => 'ESC'))->select();
            $arraysite = array();
            foreach ($resultsite as $r) {
                $r['selected'] = $r['id'] == 0 ? 'selected' : '';
                $arraysite[] = $r;
            }
            $strsite = "<option value='\$id' \$selected>\$spacer \$site_name</option>";
            $tree->init($arraysite);
            $select_sites = $tree->get_tree(0, $strsite);

            //获取设备信息
            /*$eqpts = Db::table('eqpts')
                ->field('e.id,e.eqpt_name,e.eqpt_type,e.eqpt_brand,e.eqpt_num,e.eqpt_site,e.eqpt_status,c.cate_name')
                ->alias('e')
                ->join('category c','e.eqpt_cate_id = c.id')
                ->select();*/
            //注入
            $this->assign("select_sites", $select_sites);
            $this->assign("select_categorys", $select_categorys);
            $this->assign("eqpts", $eqpts);
            //dump($eqpts);
        }
        return $this->fetch();
    }
    public function upload_file(){
        $file = request()->file('file'); // 获取上传的文件
        if($file==null){
            return json(['code' => 0, 'msg' =>$file->getError(), 'url' => '']);
        }
        $info = $file->validate(['size'=>10*1024*1000,'ext'=>'jpg,png,gif'])->move( '../uploads');
        $img = $info->getFilename();
        if($info){
            // 成功上传后 获取上传信息
            return json(['code' => 0, 'msg' => '上传成功!', 'url' => $info->getSaveName()]);
        }else{
            // 上传失败获取错误信息
            return json(['code' => 1, 'msg' => $info->getError(), 'url' => '']);
        }
    }

    public function cate_list()
    {
        if ($this->request->isAjax()) {
            $tree = new \util\Tree();
            $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
            $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
            $result = Db('categorys')
                ->field('id,parentid,cate_name,cate_remark,isShow,is_alarm')
//                ->alias('a')
//                ->join('alarmlvl l','a.alarm_lvl = l.level')
//                ->where('is_alarm = 1')
                ->select();

//        $result = Db::name('categorys')->order(array('listorder', 'id' => 'DESC'))->select();
        $result1 = $result;
        foreach ($result as &$res) {
            $res['has_sub'] = 0;
            foreach ($result1 as $item) {
                    if($res['id'] == $item['parentid']){
                        $res['has_sub'] += 1;
                    }
                }
            }
            $tree->init($result);

            $_list = $tree->getTreeList($tree->getTreeArray(0), 'cate_name');

            $total = count($_list);
            $result = array("code" => 0, "count" => $total, "data" => $_list);
            return json($result);
        }
        return $this->fetch();
    }

    public function cate_add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $res = Db('categorys')
                ->strict(false)
                ->insert($data);
            if ($res){
                $this->success('新增成功！');
            }$this->error('新增失败！');
        }else{
            $cate = Db('categorys')
                ->where('parentid','0')
                ->select();
            $this->assign('Cate',$cate);
            return $this->fetch();
        }
    }


    public function cate_del()
    {
        $id = $this->request->param('id');
        if (empty($id)) {
            $this->error('ID错误');
        }
        if(Db('categorys')->delete($id)){
            $this->success('删除成功！');
        }$this->error('删除失败！');
    }

    public function cate_details()
    {
        $id = $this->request->param('id');
        $res   = db('categorys')->where('id',$id)->find();
        $parents   = db('categorys')->field('cate_name')->where('id',$res['parentid'])->find();
        $res['parent_name'] = '无';
        if($parents['cate_name'] !== null){
            $res['parent_name'] = $parents['cate_name'];
        }else{
            $res['parent_name'] = '无';
        }
        $this->assign('Info',$res);
        return $this->fetch();
    }

    public function cate_edit()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $res = Db('categorys')
                ->where('id',$data['id'])
                ->strict(false)
                ->update($data);
            if ($res){
                $this->success('修改成功！');
            }$this->error('修改失败！');
        }else{
            $id = $this->request->param('id');
            $cate = Db('categorys')
                ->where('parentid','0')
                ->select();
            $this->assign('Cate',$cate);
            $res   = db('categorys')->where('id',$id)->find();
            $this->assign('Info',$res);
            return $this->fetch();
        }
    }


    public function temp(){
        $Socket = new SocketModel();
        $temp = $Socket->temp();
        echo $temp;
    }
    public function eqptLeft(){
        return $this->fetch();
    }
    public function eqptRightSearch(){
        return $this->fetch();
    }
    public function rightMes(){
        /*$cdt['smid'] = I('get.smid');
        $cdt['layer_name'] = I('get.layer_name');
        $info = D('eqpts')
            ->where($cdt)
            ->select();
        $this -> assign('info',$info);*/
        return $this->fetch();
    }
    public function botMesDiv(){
        return $this->fetch();
    }
    public function socket(){
        $Socket = new SocketModel();
        $Socket->socket();
    }
    public function bimBtn()
    {
        return $this->fetch();
    }
}