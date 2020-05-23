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


class Soapcli extends Adminbase
{
    protected function initialize()
    {
        parent::initialize();
    }
    //设备管理首页 模版继承 注入左侧导航
    public function index()
    {
        $tags = $this->request->param('tags');
        $val = $this->request->param('val');
//        dump($tags);
//        dump($val);
        $soap = new \SoapClient("http://192.168.10.30/Service.asmx?WSDL");

        print_r($soap->__getFunctions());

//        $tags = $soap->GetTagsList();
//        dump($tags);
//        $param = array("cpTagName"=>$tags,"cpTagValue"=>$val);
//        $out = $soap->SetKsTagValue($param);
//        $data = $out->SetKsTagValueResult;
//        dump($out);
//        dump($data);

    }

}