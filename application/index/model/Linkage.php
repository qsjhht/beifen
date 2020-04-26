<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/26
 * Time: 18:37
 */

namespace app\index\model;

use think\Model;
use think\Db;

class Linkage extends Model
{
    public function getCates()
    {
        /*$user_data[] = '{"id":1,"pid":0,"title":"1-1"},{"id":2,"pid":0,"title":"1-2"},{"id":3,"pid":0,"title":"1-3"},{"id":4,"pid":1,"title":"1-1-1"},{"id":5,"pid":1,"title":"1-1-2"},{"id":6,"pid":2,"title":"1-2-1"},{"id":7,"pid":2,"title":"1-2-3"},{"id":8,"pid":3,"title":"1-3-1"},{"id":9,"pid":3,"title":"1-3-2"},{"id":10,"pid":4,"title":"1-1-1-1"},{"id":11,"pid":4,"title":"1-1-1-2"}';*/
        // $this->where(array('userid' => $data['userid']))->find();
        //$sites1 = db('sites')->where('parentid','in','1,2,3,4,5')->select();
        //$count = db('sites')->where('parentid','in','1,2,3,4,5')->count();
        //$sites2 = db('sites')->where('parentid','in','6,7,8,9,10,11,12,13,14,15')->select();
        // interact data
        $sites = db('category')->select();
        /* $arr = array();
        foreach ($sites as $key => $value) {
            $arr[] = array("id"=>$value['id'],"pid"=>$value['parentid'],"title"=>$value['cate_name']);
        }*/
        /*foreach ($sites2 as $key => $value) {
            $arr[] = array("id"=>$value['id'],"pid"=>$value['parentid'],"title"=>$value['site_name']);
        }*/
//        JQ 二级联动
        $arr = array();
        foreach ($sites as $site) {
            if($site['parentid'] == '0'){
                $arr2 = db('category')->where('parentid',$site['id'])->field('cate_name')->select();
                foreach ($arr2 as $item) {
                    $arr[$site['cate_name']][] = $item['cate_name'];
                }
            }
        }
        return $arr;
    }
}