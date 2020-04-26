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
class Phone extends Model
{
    // 设置当前模型的数据库连接
    protected $connection = 'phone_config';
    // 设置当前模型对应的完整数据表名称
    protected $table = '组分机';


    public function get_phone(){
        $res = Db::query("select * from '组分机'");
        return $res;
    }
}