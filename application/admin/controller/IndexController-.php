<?php
namespace app\admin\controller;

use think\Request;
use think\Session;
use app\index\model\User as UserModel;

class IndexController extends \think\Controller
{
    private $username;
    private $pwd;
    private $login;
    
    public function __construct()
    {
        //$this->username=Session::get('loginuser');
//        $this->pwd=Session::get('pwd');
//        $this->login=Session::get('login');
//        echo "username:".$this->username."</br>";
//        echo "pwd:".$this->pwd."</br>";
//        echo "login:".$this->login."</br>";
    }   
    
    public function index(Request $request)
    {
        //if($request->isPost()){
//           echo "当前为POST请求";
//           dump ($request->param());
//    
//        }
        //self::$username=Session::get('loginuser');
//        self::$pwd=Session::get('pwd');
//        self::$login=Session::get('login');
        echo "username:".self::$username."</br>";
        echo "pwd:".self::$pwd."</br>";
        echo "login:".self::$login."</br>";

        if(1==self::$login){
            self::$username=Session::get('loginuser');
            self::$pwd=Session::get('pwd');
        }else{
            self::$username=$request->param('username');
            //数据库内的pwd已使用md5加密保存
            self::$pwd=md5($request->param('pwd'));
        }
        
        //连接数据库,利用模型对象查询POST来的$username，$pwd是否存在
        $user = UserModel::where('username',self::$username)
                            ->where('userpwd',self::$pwd)
                            ->where('gid',1)
                            ->find(); 
        //dump($user);      
        
        if(empty($user)){
            self::$error('登录失败，用户名或密码错误。');
        }else{
            $this->assign([
            'home'=>$request->domain(),
            'username'=>self::$username,
            ]);
            Session::set('login','1');
            Session::set('loginuser',self::$username);
            Session::set('pwd',self::$pwd);
            return View();
        }
      
        //dump ($user);
//        echo "login user gid:".$user['username']."</br>";

    }
    
    public function login(Request $request)
    {
        
       $this->login=1;
       $this->username=Session::get('loginuser');
       echo "home:".$request->domain()."</br>";
       echo "username:".$this->username."</br>";
       echo "login:".$this->login."</br>";
        
        //if(!empty($this->login) && $this->login==1){
        if(!empty($this->login)){
            $this->assign([
            'home'=>$request->domain(),
            'username'=>$this->username,
            ]);
            return $this->fetch('index/index');
        }else{
            $this->assign('home',$request->domain());
        return View();
        }
        

    }
    
    
}

