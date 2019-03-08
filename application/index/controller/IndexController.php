<?php

namespace app\index\controller;

use think\Request;
use think\Session;
use think\Controller;

use app\common\validate\Ipvalidate;
use app\index\model\User as UserModel;
use app\index\model\Patinfo as PatinfoModel;
use app\index\model\Assinfo as AssinfoModel;
use app\index\model\Theinfo as TheinfoModel;
use app\index\model\Proinfo as ProinfoModel;

class IndexController extends Controller
{
  
  //用户权限
  private $authArr=array();
  //用户登录状态
  private $log = 0;
  //用户名
  private $userName = '';
  //用户密码
  private $pwd = '';
  //用户所属部门
  private $dept = '';
  
  #patent的period与status的对应关系，本应用common.php中定义
  const PATPERIODSTATUS=conPatPeriodVsStatus;
  
  private function priLogin(){
  
    $this->log=Session::has('log')?Session::get('log'):0;
    //通过$log判断是否是登录用户，非登录用户退回到登录页面
    if(!$this->log){
      return $this->error('未登录用户，请先登录系统','index/login');
    }
    //return $this->success('priLogin()调试，'.json_encode(Session::get('authArr')),'login','',10);
    $this->authArr=Session::get('authArr');
    $this->userName=Session::get('username');
    $this->dept=Session::get('dept');
    $this->pwd=Session::get('pwd');
    
    return $this->log;
  }
  
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
        #用户的数据库保存密码值
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
    $this->priLogin();
    $mdl=null;
    $request=Request::instance();
    
    $entNum=['pat'=>0,'ass'=>0,'pro'=>0,'the'=>0];
    
    foreach($entNum as $key=>$val){
      //选择mdl
      if($this->authArr[$key]['read']){
        switch($key){
          case 'pat':
            $mdl=new PatinfoModel();
            break;
          case 'ass':
            $mdl=new AssinfoModel();
            break;
          case 'pro':
            $mdl=new ProinfoModel();
            break;
          case 'the':
            $mdl=new TheinfoModel();
            break;
        }
      
        $entNum[$key]=$mdl->initModel($this->userName,$this->dept,$this->authArr[$key])->getPeriodNum();
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
      'server'=>$request->server(),
      'cookie'=>$request->cookie(),
      //'session'=>$request->session(),
    ];
    
    return json($resData);
  }
  
  public function index(Request $request) {
        
    $this->priLogin();
    
    //$getPageInitData=!empty($request->param('getPageInitData'))?$request->param('getPageInitData'):false;
//  
//    if($getPageInitData){
//      return $this->priGetPageInitData();
//    }

    $this->assign([
      'home' => $request->domain(), 
      'username' => $this->userName,
      'authArr'=>json_encode($this->authArr),
    ]);
    return view();
    //return json_encode(['tpl'=>view(),'home'=> $request->domain()]);
  }
  
  public function getInitData() {
    $this->priLogin();
    
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
