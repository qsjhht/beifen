<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//用户登录
Route::post('user/login', 'api/user/login')->allowCrossDomain();


Route::post('patrol/data', 'api/patrol/data')->allowCrossDomain();

//中冶 bim 通过bim设备code 获取信息
Route::get('patrol/eqpt_data', 'api/patrol/eqpt_data')->allowCrossDomain();
//GIS 通过位置 zone 设备类型 cate 获取设备信息
Route::get('patrol/gis_data', 'api/patrol/gis_data')->allowCrossDomain();

Route::get('patrol/get_type', 'api/patrol/get_type')->allowCrossDomain();

Route::get('patrol/get_patrol', 'api/patrol/get_patrol')->allowCrossDomain();
Route::get('patrol/get_dotnet', 'api/patrol/get_dotnet')->allowCrossDomain();

Route::get('patrol/get_patrol_json', 'api/patrol/get_patrol_json')->allowCrossDomain();

Route::get('patrol/get_user_position', 'api/patrol/get_user_position')->allowCrossDomain();

Route::get('patrol/patrol_logs', 'api/patrol/patrol_logs')->allowCrossDomain();
Route::get('patrol/patrols', 'api/patrol/patrols')->allowCrossDomain();
Route::get('patrol/demo', 'api/patrol/demo')->allowCrossDomain();
Route::get('patrol/patroling', 'api/patrol/patroling')->allowCrossDomain();

Route::post('patrol/patrol_reload', 'api/patrol/patrol_reload')->allowCrossDomain();

// 获取防火分区 传感器实时数据
Route::get('real/real_time', 'api/real/real_time')->allowCrossDomain();
//获取防火分区 指定传感器数据  最近200条
Route::get('real/real_times', 'api/real/real_times')->allowCrossDomain();
//帮前端处理数据格式  获取防火分区 指定传感器数据  最近200条
Route::get('real/real_timest', 'api/real/real_timest')->allowCrossDomain();
//获取指定传感器 指定防火分区最新数据
Route::get('real/get_last_real', 'api/real/get_last_real')->allowCrossDomain();

Route::get('real/status_refresh', 'api/real/status_refresh')->allowCrossDomain();
// 预计拨打爱立信调度
Route::get('real/call_ericsson', 'api/real/call_ericsson')->allowCrossDomain();
//获取 主页右上角四个摄像头 token
Route::get('real/get_video_set', 'api/real/get_video_set')->allowCrossDomain();
//设置 主页右上角四个摄像头 token
Route::get('real/video_set', 'api/real/video_set')->allowCrossDomain();
//获取爱立信调度服务 所有调度电话信息
Route::get('real/get_phone', 'api/real/get_phone')->allowCrossDomain();
//爱立信调度服务 拨号
Route::get('real/call_phone', 'api/real/call_phone')->allowCrossDomain();
//获取全部水位实时数据 最大最小平均值
Route::get('real/get_s_logs', 'api/real/get_s_logs')->allowCrossDomain();
//获取指定传感器实时数据 最大最小平均值
Route::get('real/get_sensor', 'api/real/get_sensor')->allowCrossDomain();



Route::get('bigdata/date', 'api/bigdata/date')->allowCrossDomain();
Route::get('bigdata/get_data', 'api/bigdata/get_data')->allowCrossDomain();
Route::get('bigdata/get_sites', 'api/bigdata/get_sites')->allowCrossDomain();
Route::get('bigdata/get_sites_hk', 'api/bigdata/get_sites_hk')->allowCrossDomain();
Route::get('bigdata/enter_datas', 'api/bigdata/enter_datas')->allowCrossDomain();
Route::get('bigdata/enter_details', 'api/bigdata/enter_details')->allowCrossDomain();
Route::post('bigdata/send_ws', 'api/bigdata/send_ws')->allowCrossDomain();
Route::get('bigdata/alarms_list', 'api/bigdata/alarms_list')->allowCrossDomain();
Route::get('bigdata/alarms_chart', 'api/bigdata/alarms_chart')->allowCrossDomain();
Route::get('bigdata/get_apk_v', 'api/bigdata/get_apk_v')->allowCrossDomain();
Route::get('bigdata/get_apk_by_v', 'api/bigdata/get_apk_by_v')->allowCrossDomain();

Route::any('bigdata/real_alarm', 'api/bigdata/real_alarm')->allowCrossDomain();

Route::get('master/last_alarm', 'api/master/last_alarm')->allowCrossDomain();
Route::get('master/add_alarm', 'api/master/add_alarm')->allowCrossDomain();

Route::get('master/alarm_manage', 'api/master/alarm_manage')->allowCrossDomain();