<?php
namespace app\admin\controller;

use think\Request;
use think\Session;
use app\index\model\User as UserModel;

class IndexController extends \think\Controller
{
    private $username;
    private $pwd;
    
    public function index(Request $request)
    {
        //if($request->isPost()){
//           echo "当前为POST请求";
//           dump ($request->param());
//    
//        }

        $username=$request->param('username');
        //数据库内的pwd已使用md5加密保存
        $pwd=md5($request->param('pwd'));
        
        //连接数据库,利用模型对象查询POST来的$username，$pwd是否存在
        $user = UserModel::where('username',$username)
                            ->where('userpwd',$pwd)
                            ->where('gid',1)
                            ->find();       
        
        if(empty($user)){
            $this->error('登录失败，用户名或密码错误。');
        }else{
            $this->assign([
            'home'=>$request->domain(),
            'username'=>$username,
            ]);
            Session::set('login','1');
            Session::set('loginuser',$username);
            return View();
        }
      
        //dump ($user);
//        echo "login user gid:".$user['username']."</br>";

    }
    
    public function login(Request $request)
    {

        $this->assign([
        'home'=>$request->domain(),
        
        ]);
        return View();

    }
    
    
}

