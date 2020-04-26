<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use app\common\controller\Adminbase;
use app\index\model\AdminUser as AdminUser_model;
require_once STATIC_PATH.'phpqrcode\phpqrcode.php';

class Index extends Adminbase
{
    protected $filename;
    protected $QRcode;
    protected $url;
    protected $Reimg;
    protected $errorCorrectionLevel = 'L';
    protected $matrixPointSize = 3;
    protected function initialize()
    {
        parent::initialize();
        $this->QRcode = new \QRcode();
        $this->filename = ROOT_PATH.'qrcode/qrcodes.png';
        $this->logo = ROOT_PATH.'qrcode\logo4.jpg'; //准备好的logo背景图片
        $this->Reimg = ROOT_PATH.'qrcode\Reimg.jpeg'; //组合好的二维码图片
        $this->apk = ROOT_PATH.'qrcode\innowis.apk';
        $this->url = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/index/index/download';
    }
    public function index()
    {
//dump($this->url);die;
//        $user_phone = db('user')->field('user_phone')->select();
        //dump($user_phone);die;
//        $this->assign('accounts',$user_phone);
        $this->assign('userInfo', $this->_userinfo);
        $navi = Db('auth')->where('auth_pid',0)->order('auth_id', 'esc')->select();
        //dump( $navi);die;
        $this->assign('Navi',$navi);
        if(!@fopen( $this->filename, 'r' )){
            $this->QRcode->png($this->url,$this->filename,$this->errorCorrectionLevel, $this->matrixPointSize);
//            if(@fopen( $this->Reimg, 'r' )){
//                unlink($this->Reimg);
//            }
        }
//        $this->qrcode_logo();
        $this->assign('imgurl',$this->Reimg);
        return $this->fetch();
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }

    //登录判断
    public function login()
    {
        $AdminUser_model = new AdminUser_model;
        if ($AdminUser_model->isLogin()) {
            $this->redirect('index/index/index');
        }
        if ($this->request->isPost()) {
            $data = $this->request->post();
            // 验证数据
            $result = $this->validate($data, 'AdminUser.checklogin');
            if (true !== $result) {
                $this->error($result);
            }
            //验证码
            /*if (!captcha_check($data['captcha'])) {
            $this->error('验证码输入错误！');
            return false;
            }*/
            if ($AdminUser_model->login($data['username'], $data['password'])) {

                $this->success('恭喜您，登陆成功!');
            } else {
                $this->error($AdminUser_model->getError());
            }
        } else {
            return $this->fetch();
        }

    }

    //手动退出登录
    public function logout()
    {
        $AdminUser_model = new AdminUser_model;
        if ($AdminUser_model->logout()) {
            //手动登出时，清空forward
            //cookie("forward", NULL);
            $this->success('注销成功！');
        }
    }

//二维码相关
    public function download()
    {
        //$download =  new \think\response\Download($this->apk);
        //return $download->name('kid-ward.apk');
        // 或者使用助手函数完成相同的功能
        // download是系统封装的一个助手函数
        return download($this->apk, 'innowis.apk');
    }
    public function re_qrcode()
    {
        if(@fopen( $this->filename, 'r' )){
            unlink($this->filename);
            $this->qrcode_logo();
        }
//        $result = array("code" => 1, "data" => array('msg' => 'success!', ));

//        dump('asdfasdfaSD');die;
//        return json($result);

    }

    public function qrcode()
    {
        $errorCorrectionLevel = 'L';
        $matrixPointSize = 10;

        $QR = $this->QRcode->png($this->url,$this->filename,$errorCorrectionLevel, $matrixPointSize);
        //$QR = $this->filename;//已经生成的原始二维码图

    }

    public function qrcode_logo()
    {

        $errorCorrectionLevel = 'H';//容错级别
        $matrixPointSize = 3;//生成图片大小
        //生成二维码图片
        if(!@fopen( $this->filename, 'r' )){
            $this->QRcode->png($this->url,$this->filename,$errorCorrectionLevel, $matrixPointSize);
            if(@fopen( $this->Reimg, 'r' )){
                unlink($this->Reimg);
            }
        }
        //$QR = 'qrcode.png';//已经生成的原始二维码图
        if (FALSE !== $this->logo && !@fopen( $this->Reimg, 'r' )) {
            /*$QR = imagecreatefromstring(file_get_contents($this->filename));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);//二维码图片宽度
            $QR_height = imagesy($QR);//二维码图片高度
            $logo_width = imagesx($logo);//logo图片宽度
            $logo_height = imagesy($logo);//logo图片高度
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width/$logo_qr_width;
            $logo_qr_height = $logo_height/$scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;*/

            $dst_image = imagecreatefromstring(file_get_contents($this->logo));

            $src_image = imagecreatefromstring(file_get_contents($this->filename));

            $BG_width = imagesx($dst_image);//背景图宽度
            $BG_height = imagesy($dst_image);//背景图高度

            $src_width = imagesx($src_image);//抠图的宽度
            $src_height = imagesy($src_image);//抠图的高度

            $dst_w = 180;   //dst 区域宽度
            //$scale = $QR_width/$dst_w;
            $dst_h = 180;   //dst 区域高度
            /*		    $dst_w = 150; 	//dst 区域宽度
                        //$scale = $QR_width/$dst_w;
                        $dst_h = 150; 	//dst 区域高度
            */		    $dst_x = 1225;     //dst 区域x坐标
            $dst_y = 380;   //dat 区域y坐标·
            // $dst_x = 685; 	//dst 区域x坐标
            // $dst_y = 220;	//dat 区域y坐标·
            //dump($dst_x.'+'.$dst_y);die;
            //重新组合图片并调整大小   将一幅图像中的一块正方形区域拷贝到另一个图像中，平滑地插入像素值，
            //$dst_image：目标图象连接资源  $src_image：源图象连接资源。 $dst_x：目标 X 坐标点。 $dst_y：目标 Y 坐标点。 $src_x：源的 X 坐标点 $src_y：源的 Y 坐标点。$dst_w：目标宽度。 $dst_h：目标高度 $src_w：源图象的宽度 $src_h：源图象的高度。 坐标指的是左上角 在dst_image内根据坐标制作一个区域，将src_image内根据坐标抠出来的图放进去，缩放！
            //imagecopyresampled ( resource $dst_image , resource $src_image , int $dst_x , int $dst_y , int $src_x , int $src_y , int $dst_w , int $dst_h , int $src_w , int $src_h ) : bool
            imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $src_width, $src_height);
            Header("Content-type: image/png");
            //echo "<style>img{ width:100%;height:100%;  }</style>";

            ImagePng($dst_image,$this->Reimg);
            exit();
        }
    }
}
