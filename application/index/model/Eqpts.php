<?php
namespace app\index\model;

use think\Db;
use think\Model;

class Eqpts extends Model
{
    public $lastId;
    public static function init()
    {
        // 钩子方法 实现插入数据后更新字段
        Eqpts::event('after_insert', function ($data) {
            //获取设备编号
            $res = Db::table('category')->where('id',$data['eqpt_cate_id'])->field('cate_num')->find();
            $date = date('ymd', time());
            $eqptnum = sprintf('%05s', $data['id']);
            $eqpt_num = $date.'-'.$res['cate_num'].'-'.$eqptnum;
            //dump($eqpt_num);
            Eqpts::where('eqpt_id', $data['id'])
                ->update(['eqpt_num' => $eqpt_num]);
        });

    }
    public function scopeScore($query, $score)
    {
        $query->where('score', '>', $score);
    }
}
