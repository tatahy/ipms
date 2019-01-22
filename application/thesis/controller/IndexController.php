<?php
namespace app\thesis\controller;

use think\Request;
use think\Session;
use think\Model;

class IndexController extends \think\Controller
{
    //用户名
    private $username = null;
    //用户密码
    private $pwd = null;
    //用户登录状态
    private $log = null;
    //用户角色
    private $roles=array();
    //用户所在部门
    private $dept = null;
    
    // 初始化
    protected function _initialize()
    {
        $this->username=Session::get('username');
        $this->pwd=Session::get('pwd');
        $this->log=Session::get('log');
        $this->roles=Session::get('role');
        $this->dept=Session::get('dept');
    }
    
    public function index()
    {
       $this->_loginUser();
      
      return view();
    }
    
    // 判断是否为登录用户
    private function _loginUser()
    {
      //通过$this->log判断是否是登录用户，非登录用户退回到登录页面
      $this->log=Session::get('log');
      
      if(1!=$this->log){
        return $this->error('无用户名或密码，请先登录系统');
      }    
    }
    
    public function theList(Request $request)
    {
      $period=$request->param('period')?$request->param('period'):'total';
      $this->_loginUser();
      
      $this->assign([
        'period'=>$period
      ]);
      return view();
    
    }
    
    
}
