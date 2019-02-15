<?php

namespace app\index\controller;

use think\Request;
use think\Session;
use think\Controller;

use app\common\validate\Ipvalidate;
use app\index\model\User as UserModel;
use app\patent\model\Patinfo as PatinfoModel;
use app\asset\model\Assinfo as AssinfoModel;

class IndexController extends Controller
{
  
  //用户权限
  private $authArr=array();
  //用户登录状态
  private $log = 0;
  
  #patent的period与status的对应关系，本应用common.php中定义
  const PATPERIODSTATUS=conPatPeriodVsStatus;
  
  private function priLogin(){
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
    if(!$this->log){
      return $this->error('未登录用户，请先登录系统','index/login');
      //$this->redirect($request->domain());
    }
  }
  
  private function priGetPageInitData() {
    $this->priLogin();
    $request=Request::instance();
    
    $resData=[
      'urlObj'=>[
        'domain'=>$request->domain(),
        'module'=>$request->module(),
        'ctrl'=>strtolower($request->controller()),
        'action'=>'index',
      ],
      'entNum'=>[
        'pat' =>$this->authArr['pat']['read']?PatinfoModel::getPeriodNum():0,
        'ass'=>$this->authArr['ass']['read']?AssinfoModel::getPeriodNum():0,
        'pro'=>[
          'total'=>'x',
          'audit'=>'x',
          'plan'=>'x',
          'apply'=>'x',
          'approve'=>'x',
          'process'=>'x',
          'inspect'=>'x',
          'done'=>'x',
          'terminate'=>'x',
          'reject'=>'x',
        ],
        'the'=>[
          'total'=>'x',
          'audit'=>'x',
          'plan'=>'x',
          'apply'=>'x',
          'accept'=>'x',
          'publish'=>'x',
          'reject'=>'x'
        ]
      ]
    ];
    
    return $resData;
  }
  
  public function index(Request $request,PatinfoModel $patMdl,UserModel $userMdl,AssinfoModel $assMdl) {
    #'username'和'pwd'的来源：session或初次登录时表单POST提交
    $username =!empty($request->param('username'))?$request->param('username'):Session::get('username');
    #前端需保证pwd的值是经过md5加密后的值，因为数据库中存储的就是md5加密后的值
    $pwd = !empty($request->param('pwd'))?$request->param('pwd'):Session::get('pwd');
    #
    $getPageInitData=!empty($request->param('getPageInitData'))?$request->param('getPageInitData'):false;
    
    //通过浏览器端验证后再在数据库中查询是否有相应的用户存在,
    //连接数据库,利用模型对象查询有效的$username，$pwd在数据库中是否存在并已启用
    $user = $userMdl::where('username', $username)->where('pwd', $pwd)->where('enable',
      1)->find();

    //不存在，同验证失败的处理
    if (empty($user)) {
     return $this->error('登录失败，用户名或密码错误。','index/login');
    } 
    
    #调用User模型层定义的refreshUserAuth()方法，刷新登录用户的各个模块权限
    $authority = $userMdl->refreshUserAuth($username, $pwd);
    Session::set('userId', $user->id);
    Session::set('username', $username);
    Session::set('pwd', $pwd);
    Session::set('log', 1);
    Session::set('dept', $user->dept);
    Session::set('authArr', $authority);
    
    $this->authArr=$authority;
    $this->log=1;
    
    if($getPageInitData){
      return $this->priGetPageInitData();;
    }
    
    $this->assign([
      'home' => $request->domain(), 
      'username' => $username,
        #各模块统计数据
        //'num' => json_encode($num,JSON_UNESCAPED_UNICODE),
//        'test'=>json_encode($authority,JSON_UNESCAPED_UNICODE),
//        'year' => date('Y'), 
    ]);
    return view();
  }

  //修改application/config.php的设置将“默认操作”由“index”改为“login”？？
  public function login(Request $request){

    $this->assign(['home' => $request->domain(), 'year' => date('Y')]);

    return view();

  }
  public function logout(Request $request){
    Session::clear();
    Session::destroy();
    #\think\Controller类内置的跳转方法success()
    $this->success('安全退出系统', 'index/login','',1);
  }
  
  public function getEntSearchFormVal() {
    
    
  }
  
}
