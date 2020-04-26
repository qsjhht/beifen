<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/19
 * Time: 17:43
 */

namespace app\index\model;

use think\Model;
use think\Db;
class Patrol extends Model
{
    public function getSites()
    {
        /*$user_data[] = '{"id":1,"pid":0,"title":"1-1"},{"id":2,"pid":0,"title":"1-2"},{"id":3,"pid":0,"title":"1-3"},{"id":4,"pid":1,"title":"1-1-1"},{"id":5,"pid":1,"title":"1-1-2"},{"id":6,"pid":2,"title":"1-2-1"},{"id":7,"pid":2,"title":"1-2-3"},{"id":8,"pid":3,"title":"1-3-1"},{"id":9,"pid":3,"title":"1-3-2"},{"id":10,"pid":4,"title":"1-1-1-1"},{"id":11,"pid":4,"title":"1-1-1-2"}';*/
        // $this->where(array('userid' => $data['userid']))->find();
        //$sites1 = db('sites')->where('parentid','in','1,2,3,4,5')->select();
        //$count = db('sites')->where('parentid','in','1,2,3,4,5')->count();
        //$sites2 = db('sites')->where('parentid','in','6,7,8,9,10,11,12,13,14,15')->select();
        $sites = db('sites')->select();
        $arr = array();
        foreach ($sites as $key => $value) {
            $arr[] = array("id"=>$value['id'],"pid"=>$value['parentid'],"title"=>$value['site_name']);
        }
        /*foreach ($sites2 as $key => $value) {
            $arr[] = array("id"=>$value['id'],"pid"=>$value['parentid'],"title"=>$value['site_name']);
        }*/
        return $arr;
    }

    public function getSites_hk()
    {
        /*$user_data[] = '{"id":1,"pid":0,"title":"1-1"},{"id":2,"pid":0,"title":"1-2"},{"id":3,"pid":0,"title":"1-3"},{"id":4,"pid":1,"title":"1-1-1"},{"id":5,"pid":1,"title":"1-1-2"},{"id":6,"pid":2,"title":"1-2-1"},{"id":7,"pid":2,"title":"1-2-3"},{"id":8,"pid":3,"title":"1-3-1"},{"id":9,"pid":3,"title":"1-3-2"},{"id":10,"pid":4,"title":"1-1-1-1"},{"id":11,"pid":4,"title":"1-1-1-2"}';*/
        // $this->where(array('userid' => $data['userid']))->find();
        //$sites1 = db('sites')->where('parentid','in','1,2,3,4,5')->select();
        //$count = db('sites')->where('parentid','in','1,2,3,4,5')->count();
        //$sites2 = db('sites')->where('parentid','in','6,7,8,9,10,11,12,13,14,15')->select();
        $sites = db('sites_hk')->select();
        $arr = array();
        foreach ($sites as $key => $value) {
            $arr[] = array("id"=>$value['id'],"pid"=>$value['parentid'],"title"=>$value['site_name'],"hk_did"=>$value['hk_did']);
        }
        /*foreach ($sites2 as $key => $value) {
            $arr[] = array("id"=>$value['id'],"pid"=>$value['parentid'],"title"=>$value['site_name']);
        }*/
        return $arr;
    }
}