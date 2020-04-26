<?php
namespace app\index\model;

use think\Db;
use think\Model;

class User extends Model
{
    public static function init()
    {
        self::event('after_insert', function ($user) {
            if (1 != $user->status) {
                return false;
            }
        });
    }
    public function get_dept_tree(){
        return json_encode($this->field('dept_id')->select());
        
    }

    public function get_Tree($arr,$pk = 'id', $pid = 'pid',$label='label'){
        $array = [];
        foreach ($arr as $key => $value) {
            $array[$key]['id'] = $value[$pk];
            $array[$key]['pid'] = $value[$pid];
            $array[$key]['label'] = $value[$label];
        }

        return $this->quote_make_tree($array);
    }
   
    public static function quote_make_tree($list, $pk = 'id', $pid = 'pid',$child = 'children', $root = 0)
    {
        $tree = $packData = [];
        foreach ($list as $data) {
            $packData[$data[$pk]] = $data;
        }
        foreach ($packData as $key =>$val) {
            if ($val[$pid] == $root) {//代表跟节点
              $tree[] = & $packData[$key];
              unset($packData[$key]['pid']);
            } else {
              //找到其父类
              $packData[$val[$pid]][$child][] = & $packData[$key];
              unset($packData[$key]['pid']);
            }
        }
        return $tree;
    }

}
