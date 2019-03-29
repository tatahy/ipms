<?php

namespace app\index\controller;

use think\Request;
use think\Session;

use app\index\controller\CommonController;
use app\index\model\User as UserModel;

use app\common\factory\EntinfoFactory as EntinfoMdl;

class IndexController extends CommonController {
  
  #patent的period与status的对应关系，本应用common.php中定义
  const PATPERIODSTATUS=conPatPeriodVsStatus;
  
  #根据传入的参数，检查登录用户信息是否数据库中唯一存在（是就设置Session，并返回'success'）
  private function priSetSession($data=[]) {
    #计数器
    $n=0;
    #参数初始化
    $data=array_merge(['salt'=>'','username'=>'','pwd'=>''],$data);
    
    //return $this->error('priSetSession()调试：','login',$data,10);
    #参数数量不正确
    if(count($data)!==3){
      return '用户名或密码错误';
    }
    
    $salt=$data['salt'];
    $pwd=$data['pwd'];
    $userName=$data['username'];
    
    #利用模型对象查询已启用的$username，
    $uSet = UserModel::where('username', $userName)->where('enable',1)->select();
    
    if(!count($uSet)){
      //return $this->error('用户【'.$userName.'】不存在','index/login','',1);
      return '用户【'.$userName.'】不存在或已被禁用。';
    }
    
    $uSet=is_array($uSet)?collection($uSet):$uSet;
    
    #$this->pwd现在的值为数据库的md5值加盐后的md5值，在数据集中的pwd加盐后是否仅存在一个与$this->pwd现在的值相同
    foreach($uSet->column('pwd') as $k=>$v){
      if($pwd==md5($v.$salt)){
        $n++;
        #用户密码(数据库保存值)
        $pwd=$v;
      }
    }
    
    if($n!=1){
      //return $this->error('priSetSession()调试：用户名或密码错误','login',$data,1);
      return '用户名或密码错误';
    }
    
    #调用User模型层定义的refreshUserAuth()方法，刷新登录用户的各个模块权限后，返回结果集
    $uSet=UserModel::refreshUserAuth($userName,$pwd);
    #设置登录用户的Session变量      
    Session::set('userId', $uSet->id);
    Session::set('username', $uSet->username);
    Session::set('pwd', $uSet->pwd);
    Session::set('log', 1);
    Session::set('dept', $uSet->dept);
    Session::set('authArr', $uSet->authority);        

    return 'success';
     
  }
  
  private function priGetPageInitData() {
    $this->chkLogin();
    $mdl=null;
    $request=Request::instance();
    
    $entNum=['pat'=>0,'ass'=>0,'pro'=>0,'the'=>0];
    
    foreach($entNum as $ent=>$val){
      //选择mdl
      if($this->authArr[$ent]['read']){
        #选择模型对象并初始化
        $mdl= $this->getMdl($ent);
        $entNum[$ent]=$mdl->getPeriodNum();
        $mdl=null;
      }
    }
    
    $resData=[
      'userName'=>$this->userName,
      'urlObj'=>[
        'domain'=>$request->domain(),
        'module'=>$request->module(),
        'ctrl'=>strtolower($request->controller()),
        'action'=>'index'
      ],
      'entNum'=>$entNum,
      'authArr'=>$this->authArr,
      #服务器端信息,TP5中获取全局变量$_SERVER的方法
      //'server'=>$request->server(),
//      'cookie'=>$request->cookie(),
      //'session'=>$request->session(),
    ];
    
    return json($resData);
  }
  
  public function index(Request $request) {
        
    $this->chkLogin();
    
    //$getPageInitData=!empty($request->param('getPageInitData'))?$request->param('getPageInitData'):false;
//  
//    if($getPageInitData){
//      return $this->priGetPageInitData();
//    }
    
    $ent=!empty($request->param('ent'))?$request->param('ent'):'';
    $period=!empty($request->param('period'))?$request->param('period'):'';
    
    $this->assign([
      'home' => $request->domain(), 
      'userName' => $this->userName,
      //'test'=>json_encode($ent.$period),
      
    ]);
    return view();
    //return json_encode(['tpl'=>view(),'home'=> $request->domain()]);
  }
  
  public function getInitData() {
    $this->chkLogin();
    
    return $this->priGetPageInitData();
  }

  //修改application/config.php的设置将“默认操作”由“index”改为“login”？？
  public function login(Request $request,UserModel $userMdl){  
    #没有request
    if(!count($request->request())){
      $this->assign(['home' => $request->domain(), 'year' => date('Y')]);
      return view();
    }
    #前端只发送了'username'值（有值的参数个数为1）
    if(count($request->request())==1 && !empty($request->param('username'))){
      $userName=$request->param('username');
      $uSet=$userMdl->where('username',$userName)->where('enable',1)->select();
      return count($uSet)?true:'用户【'.$userName.'】不存在或已被禁用。';
    }
     
    #检查登录用户信息并设置用户Session，参数为指定的前端传送数据，返回前端设置结果
    return $this->priSetSession($request->only(['salt','username','pwd']));

  }
  public function logout(Request $request){
    Session::clear();
    Session::destroy();
    #\think\Controller类内置的跳转方法success()
    $this->success('安全退出系统', 'index/login','',1);
  }
  
  #直接调用think\View，think\Request类的方法
  public function example() {
    
    //
    $this->assign('domain',$this->request->url(true));
    return $this->fetch('example');
  }
 
}
