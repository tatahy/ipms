<?php
namespace app\patent\controller;

use think\Request;
use think\Session;

class IndexController extends \think\Controller
{
    public function index(Request $request)
    {
        
        $username=Session::get('username');
        $pwd=Session::get('pwd');
        $log=Session::get('log');
        $roles=Session::get('roles');
        
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$log){
            $this->error('未登录用户，请先登录�?);
            $this->redirect($request->domain());
        }
               
        //根据登录人选择的role，控制模板显示对应的内容
        $roleparam=$request->param('role');
        $active=$request->param('active');
        
        //$active的值不能超�?或小�?(因为系统默认每个角色需处理3种状态的事务，分别为1�?�?)，否则修改为1�?        if($active>3 or $active<1){
            $active=1;
        }
        
        //判断$request->param('role')传来的role值是否为Session中存储的role值，否则报错退回到登录页面
        for($i = 0; $i < count($roles); $i++) {
            if($roleparam==$roles[$i]){
                $n=0;
                $n=$n+1;
                break;   
            }
        }
        if($n>=1){
            switch($roleparam){
                case"writer":
                    $rolename="撰写�?;
                break;
                        
                case"reviewer":
                    $rolename="审查�?;
                break;
                        
                case"formchecker":
                    $rolename="形式审查�?;
                break;
                        
                case"financialchecker":
                    $rolename="财务审查�?;
                break;
                        
                case"approver":
                    $rolename="批准�?;
                break;
                        
                case"maintainer":
                    $rolename="维护�?;
                break;
                        
                case"operator":
                    $rolename="执行�?;
                break;
                        
                default:
                $this->error('用户角色错误，请重新登录�?);   
            }
        }else{
            $this->error('用户角色错误，请重新登录�?);
        }

        //--在index.html页面输出自定义信息的HTML代码�?        $destr= "请求方法:".$request->method()."</br>".
                "username:".$username."</br>".
                //"pwd:".sizeof($pwd);
                "pwd:".$pwd."</br>".
                "log:".$log."</br>".
                "roleparam:".$request->param('role').";active:".$request->param('active')."</br>";
        //--!       
        
        $this->assign([
        //在index.html页面通过'destr'输出自定义的信息
        'destr'=>$destr,
        //在index.html页面通过'array'输出自定义的数组内容
        'array'=>$roles, 
        
        'home'=>$request->domain(),
        'username'=>$username,
        'rolename'=>$rolename,
        'role'=>$roleparam,
        'active'=>$active,
        ]);
        return view();
    }
    
    //增加新patent
    public function addnew(Request $request)
    {
        return view();
    }
    
    //patent列表�?    public function patlist(Request $request)
    {
        $username=Session::get('username');
        $pwd=Session::get('pwd');
        $log=Session::get('log');
        $roles=Session::get('roles');
        
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$log){
            $this->error('未登录用户，请先登录�?);
            $this->redirect($request->domain());
        }
        
        //--在index.html页面输出自定义信息的HTML代码�?        $destr= "请求方法:".$request->method()."</br>".
                "username:".$username."</br>".
                //"pwd:".sizeof($pwd);
                "pwd:".$pwd."</br>".
                "log:".$log."</br>".
                "roleparam:".$request->param('role').";active:".$request->param('active')."</br>";
        //--!       
        
        $this->assign([
        //在index.html页面通过'destr'输出自定义的信息
        'destr'=>$destr,
        //在index.html页面通过'array'输出自定义的数组内容
        'array'=>$roles, 
        
        'home'=>$request->domain(),
        'username'=>$username,
//        'rolename'=>$rolename,
//        'role'=>$roleparam,
//        'active'=>$active,
        ]);
        return view();
    }
        
}
