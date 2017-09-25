<?php
namespace app\admin\controller;

use think\Request;
use think\Session;
use think\Model;
use app\user\model\User as UserModel;

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
        //$this->username=Session::get('username');
//        $this->pwd=Session::get('pwd');
//        $this->log=Session::get('log');
//        $this->roles=Session::get('role');
//        $this->dept=Session::get('dept');
        $this->username='';
        $this->pwd='';
        $this->log='';
        $this->roles=array();
        $this->dept='';

    }
    
    public function index(Request $request)
    {
      //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
      
      //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!=$this->log){
            $this->error('无用户名或密码，请先登录系统');
            //$this->redirect($request->domain());
        }else{
            
        }
        
       
        
        $this->assign([
          
              'home'=>$request->domain(),
              'username'=>$this->username,
              //获取服务器信息（操作系统、Apache版本、PHP版本）
              //'server_version' => $_SERVER['SERVER_SOFTWARE'],$_SERVER['SERVER_SOFTWARE']与apache_get_version()结果相同
              
              // 获取服务器操作系统
              //'serverOS'=>PHP_OS,
              
              // 获取服务器域名 
              'serverDomain'=>$_SERVER['SERVER_NAME'],
              //获取服务器操作系统类型及版本号,PHP 5
              'serverOS'=>php_uname('s').php_uname('v'),
    			   // 获取MySQL版本信息
              'mysqlVersion' => $this->_mysqlVersion(),
    			   //获取服务器时间
              'serverTime' => date('Y-m-d H:i:s', time()),
              // 获取PHP版本信息，PHP 5
              'phpVersion'=>phpversion(),
              // 获取Apache版本信息，PHP 5
              'apacheVersion'=>apache_get_version(),
              
              
          
            ]);
            return view();
        
        
    }
    
    // 获取MySQL版本信息,通过查询语句的方式。
    private function _mysqlVersion()
    {
        $model = new UserModel;
        $version = $model->query("select version() as ver");
        return $version[0]['ver'];
    }
    
    // 
    public function login(Request $request)
    {
      //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
      
      $this->assign([
        
        'home'=>$request->domain(),
        
        ]);
        return view();
    }
    
    // 验证用户名、密码是否为数据库中有效管理员，是就显示后台主页，否就退回登录页面
    public function check(Request $request)
    {
      //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
      
      //处理登录页面post来的数据
      if(!empty($request->param('username'))){
            $username=$request->param('username');
      }else{
          $this->error('无用户名或密码，请先登录系统');
      }
        
      if(!empty($request->param('pwd')) ){
            $pwd=md5($request->param('pwd'));
      }else{
          $this->error('无用户名或密码，请先登录系统');
      }
      
      $user = UserModel::where('username',$username)
                            ->where('pwd',$pwd)
                            ->where('roleid',0)
                            ->select();
      
       // 查出所有的用户
        $users=UserModel::where('id','>',0)
                            ->order('username asc')
                            ->select();
      
      if(empty($user)){
          $this->error('用户名或密码错误，请重新登录');
          //return view("login"); 
      }else{
          // 写入session
          Session::set('pwd',$user[0]['pwd']);
          Session::set('username',$user[0]['username']);
          Session::set('log',1);
          Session::set('role','管理员');
          Session::set('dept',$user[0]['dept']);
          
          $this->username=Session::get('username');
          $this->pwd=Session::get('pwd');
          $this->log=Session::get('log');
          $this->roles=Session::get('role');
          $this->dept=Session::get('dept');
          
        //--在bg-head.html页面输出自定义信息的HTML代码块
          
          
          $this->assign([
            //--在bg-head.html页面输出自定义信息的HTML代码块
              'destr'=>$destrr= "请求方法:".$request->method()."</br>".
                                "username:".$this->username."</br>".
                                "log:".$this->log."</br>",
              
              'home'=>$request->domain(),
              'username'=>$this->username,
              
               // 获取服务器域名 
              'serverDomain'=>$_SERVER['SERVER_NAME'],
              //获取服务器操作系统类型及版本号,PHP 5
              'serverOS'=>php_uname('s').php_uname('v'),
    			   // 获取MySQL版本信息
              'mysqlVersion' => $this->_mysqlVersion(),
    			   //获取服务器时间
              'serverTime' => date('Y-m-d H:i:s', time()),
              // 获取PHP版本信息，PHP 5
              'phpVersion'=>phpversion(),
              // 获取Apache版本信息，PHP 5
              'apacheVersion'=>apache_get_version(),
              
              // 所有用户信息
              'users'=>$users,
          
          
          ]);
          
          return view('index');
      }
      
      
    }
    
    
    
    
    
    
}

