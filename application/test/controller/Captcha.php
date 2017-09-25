<?php
namespace app\test\controller;

class Captcha extends \think\Controller{
    //验证码表单
    public function index()
    {
        return $this->fetch();
    }
    
    public function check($code='')
    {
        //实例化Captcha类，命名空间\think\captcha\
        $captcha = new \think\captcha\Captcha();
        if (!$captcha->check($code)){
            $this->error('验证码错误');
        }else {
            $this->success('验证码正确');
        }
    }
    
}

?>