<?php
namespace app\common\controller;
header('Access-Control-Allow-Origin:*');
use think\Controller;
use think\Db;
use think\Request;
use think\Validate;

class Common extends Controller
{
    protected $reuqest; //用来处理参数
    protected $validater; //用来严恒数据/参数
    protected $params; //过滤后符合要求的参数
    protected $rules = array(
        'User'    => array(
            'login'           => array(
                'user_name' => 'require',
                'user_pwd'  => 'require|length:32',
            ),
            'register'        => array(
                'user_name' => 'require',
                'user_pwd'  => 'require|length:32',
                'code'      => 'require|number|length:6',
            ),
            'upload_head_img' => array(
                'user_id'   => 'require|number',
                //'user_icon' => 'require|image|fileSize:10000000|fileExt:jpg,png,bmp,jpeg',
            ),
            'change_pwd'      => array(
                'user_name'    => 'require',
                'user_ini_pwd' => 'require|length:32',
                'user_pwd'     => 'require|length:32',
            ),
            'find_pwd'        => array(
                'user_name' => 'require',
                'user_pwd'  => 'require|length:32',
                'code'      => 'require|number|length:6',
            ),
            'bind_phone'      => array(
                'user_id' => 'require|number',
                'phone'   => ['require', 'regex' => '/^1[34578]\d{9}$/'],
                'code'    => 'require|number|length:6',
            ),
            'bind_email'      => array(
                'user_id' => 'require|number',
                'email'   => 'require|email',
                'code'    => 'require|number|length:6',
            ),
            'bind_username'   => array(
                'user_id'   => 'require|number',
                'user_name' => 'require',
                'code'      => 'require|number|length:6',
            ),
            'set_nickname'    => array(
                'user_id'       => 'require|number',
                'user_nickname' => 'require|chsDash',
            ),
        ),
        'Code'    => array(
            'get_code' => array(
                'username' => 'require',
                'is_exist' => 'require|number|length:1',
            ),
        ),
        'Article' => array(
            'add_article'    => array(
                'article_uid'   => 'require|number',
                //'article_title' => 'require|chsDash',
            ),
            'article_list'   => array(
                'user_id' => 'require|number',
                'num'     => 'number',
                'page'    => 'number',
            ),
            'article_list_day'   => array(
                'user_id' => 'require|number',
                'start' => 'require|dateFormat:Y-m-d',
                'end' => 'require|dateFormat:Y-m-d',
                'num'     => 'number',
                'page'    => 'number',
            ),
            'article_detail' => array(
                'user_id' => 'require|number',
            ),
            'update_article' => array(
                'article_id'    => 'require|number',
                'article_title' => 'chsDash',
            ),
            'del_article'    => array(
                'article_id' => 'require|number',
            ),
        ),

    );
    // 图片验证规则
    protected $rule_img = array('imgs' => 'require'); 
    protected function _initialize()
    {
        parent::_initialize();
        $this->request = Request::instance();
        //$this->check_time($this->request->only(['time']));
        //$this->check_token($this->request->param());
        //$this->params = $this->check_params($this->request->except(['time','token']));  // 以上无法获取$_FILES so  如下
        $this->params = $this->check_params($this->request->param(true));
    }
    /**
     * 验证请求是否存在  是否超时
     * @param  [type] $arr [包含时间戳的参数数组]
     * @return [type]      [检测结果]
     */
    public function check_time($arr)
    {
        if (!isset($arr['time']) || intval($arr['time']) <= 1) {
            $this->return_msg(400, '时间戳不存在！');
        }
        if (time() - intval($arr['time']) > 60) {
            $this->return_msg(400, '请求超时！');
        }
    }
    /**
     * api 数据返回
     * @param  [type] $code [结果码 200：正常 /4** 数据问题 /5** 服务器问题]
     * @param  string $msg  [接口要返回的提示信息]
     * @param  [array] $data [接口要返回的数据]
     * @return [string]       [最终的json数据]
     */
    public function return_msg($code, $msg = '', $data = [])
    {
        // 组合数据
        $return_data['code'] = $code;
        $return_data['msg']  = $msg;
        $return_data['data'] = $data;
        // 返回信息并终止脚本
        echo json_encode($return_data);die;
    }
    /**
     * 验证token(防止篡改数据)
     * @param  [array] $arr [全部请求参数]
     * @return [json]      [token验证结果]
     */
    public function check_token($arr)
    {
        // api传过来的token
        if (!isset($arr['token']) || empty($arr['token'])) {
            $this->return_msg(400, 'token 不能为空！');
        }
        $app_token = $arr['token']; // api传过来的token
        //服务器端生成token
        unset($arr['token']);
        $service_token = '';
        foreach ($arr as $key => $value) {
            $service_token .= md5($value);
        }
        $service_token = md5('api_' . $service_token . '_api');
        //对比token 返回结果
        if ($app_token !== $service_token) {
            $this->return_msg(400, 'token不正确！');
        }
    }
    /**
     * [验证]
     * @param  [type] $arr [description]
     * @return [type]      [description]
     */
    public function check_params($arr)
    {
        // 获取参数的验证规则
        $rule            = $this->rules[$this->request->controller()][$this->request->action()];
        $this->validater = new Validate($rule); //实例化验证类
        // 判断验证失败流程
        if (!$this->validater->check($arr)) {
            $this->return_msg(400, $this->validater->getError());
        }
        //验证成功流程
        return $arr;
    }

    public function check_imgs($arr)
    {
        // 获取参数的验证规则
        $rule            = $this->rule_img;
		$this->validater = new Validate($rule);
        foreach ($arr as $key => $value) {
        	if (!$this->validater->check($value)) {
            $this->return_msg(400, $this->validater->getError());
        }
        }
        die;
         //实例化验证类
        dump($this->validater);die;
        // 判断验证失败流程
        
        //验证成功流程
        return $arr;
    }

    /**
     * 检测用户名并返回用户名类别
     * @param  [string] $username [用户名，可能是邮箱，也可能是手机号]
     * @return [string]           [检测结果]
     */
    public function check_username($username)
    {
        /*********** 判断是否为邮箱  ***********/
        $is_email = Validate::is($username, 'email') ? 1 : 0;
        /*********** 判断是否为手机  ***********/
        $is_phone = preg_match('/^1[34578]\d{9}$/', $username) ? 4 : 2;
        /*********** 最终结果  ***********/
        $flag = $is_email + $is_phone;
        switch ($flag) {
            /*********** not phone not email  ***********/
            case 2:
                $this->return_msg(400, '邮箱或手机号不正确!');
                break;
            /*********** is email not phone  ***********/
            case 3:
                return 'email';
                break;
            /*********** is phone not email  ***********/
            case 4:
                return 'phone';
                break;
        }
    }
    public function check_exist($value, $type, $exist)
    {

        $type_num = $type == "phone" ? 2 : 4;

        $flag      = $type_num + $exist;
        $phone_res = db('user')->where('user_phone', $value)->find();
        $email_res = db('user')->where('user_email', $value)->find();
        switch ($flag) {
            // 2+0 phone need no exist
            case 2:
                if ($phone_res) {
                    $this->return_msg(400, '此手机号已被占用！');
                }
                break;
            // 2+1 phone need  exist
            case 3:
                if (!$phone_res) {
                    $this->return_msg(400, '此手机号不存在！');
                }
                break;
            // 4+0 email need no exist
            case 4:
                if ($email_res) {
                    $this->return_msg(400, '此邮箱已被占用！');
                }
                break;
            // 4+1 email need  exist
            case 5:
                if (!$email_res) {
                    $this->return_msg(400, '此邮箱不存在！');
                }
                break;
        }
    }

    /**
     * 检测验证码
     * @param  [string] $user_name [用户名]
     * @param  [int] $code      [验证码]
     * @return [json]            [api返回的json数据]
     */
    public function check_code($user_name, $code)
    {
        // 检测是否超时
        $last_time = session($user_name . '_last_send_time');
        if (time() - $last_time > 600) {
            $this->return_msg(400, '验证超时，请在一分钟内验证！');
        }
        // 检测验证码是否正确

        $md5_code = md5($user_name . '_' . md5($code));
        if (session($user_name . '_code') !== $md5_code) {
            $this->return_msg(400, '验证码不正确！');
        }
        // 无论正确与否，每个验证码都只验证一次
        session($user_name . '_code', null);
    }

    public function upload_file($file, $type = '')
    {
    	// $this->param(true);无法获取img so 使用数组获取
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if ($info) {
            $path = '/uploads/' . $info->getSaveName();
            if (!empty($type)) {
                $this->image_edit($path, $type);
            }

            return str_replace('\\', '/', $path);
        } else {
            $this->return_msg(400, $file->getError());
        }
    }

    public function upload_files($files)
    {
    	$files_path = '';
    	$files_time = '';
        foreach ($files as $file) {
            // 移动到框架应用根目录/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if ($info) {
                $path = '/uploads/' . $info->getSaveName();
                /*if (!empty($type)) {
                    $this->image_edit($path, $type);
                }*/
                $files_time .= time().',';
                $files_path .= str_replace('\\', '/', $path).',';
            } else {
                // 上传失败获取错误信息
                $this->return_msg(400, $file->getError());
            }
        }
        $arr = array('paths' => $files_path,'times' => $files_time);
        return $arr;
    }

    public function image_edit($path, $type)
    {
        //dump(ROOT_PATH.'public'.$path);die;
        $image = \think\Image::open(ROOT_PATH . 'public' . $path);
        //dump($image);die;
        switch ($type) {
            case 'head_img':
                $image->thumb(200, 200, \think\Image::THUMB_CENTER)->save(ROOT_PATH . 'public' . $path);
                break;

                /*default:
        # code...
        break;*/
        }
    }
}
