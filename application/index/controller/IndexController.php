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
  
  #patent的period与status的对应关系，本应用common.php中定义
  const PATPERIODSTATUS=conPatPeriodVsStatus;
  
  public function index(Request $request,PatinfoModel $patMdl,UserModel $userMdl,AssinfoModel $assMdl) {
    //'username'和'pwd'的来源：session或初次登录时表单POST提交
    $username =!empty($request->post('username'))?$request->post('username'):Session::get('username');
    $pwd = !empty($request->post('pwd'))?md5($request->post('pwd')):Session::get('pwd');

    $log = Session::get('log');

    $data = ['name' => $username, 'pwd' => $pwd, ];

    //使用自定义的validate类“Ipvalidate”进行浏览器端验证，类文件目录：application\common\validate
    //也可直接在页面利用jQuery进行验证；<input type="text" required>
    $result = $this->validate($data, 'Ipvalidate.login');
    if (true !== $result)
    {
      //验证失败输出提示错误信息
      $this->error($result);
      //返回登录界面
      $this->assign(['home' => $request->domain(), ]);
      return view('index/login');
    }

    //通过浏览器端验证后再在数据库中查询是否有相应的用户存在,
    //连接数据库,利用模型对象查询有效的$username，$pwd在数据库中是否存在并已启用
    $user = $userMdl::where('username', $username)->where('pwd', $pwd)->where('enable',
      1)->find();

    //不存在，同验证失败的处理
    if (empty($user))
    {
     return $this->error('登录失败，用户名或密码错误。');
    } 
    
      #调用User模型层定义的refreshUserAuth()方法，刷新登录用户的各个模块权限
      $authority = $userMdl->refreshUserAuth($username, $pwd);
      $num=['pat' => 0,'ass'=>0,'pro'=>0,'the'=>0];

      Session::set('userId', $user->id);
      Session::set('username', $username);
      Session::set('pwd', $pwd);
      Session::set('log', 1);
      Session::set('dept', $user->dept);
      Session::set('authArr', $authority);
      
      
      #根据ass是否有read权限进行赋值，利用模型对象得到各个asset总数
      $num['ass']=$authority['ass']['read']?$assMdl::getPeriodNum():0;
      #根据登录用户的ass权限得到可访问的模型对象各个period数量
      //$num['ass']=$assMdl::setAuth($authority['ass']['read'])->getPeriodNum():0;
      
      #根据pat是否有read权限进行赋值，利用模型对象得到各个patent总数
      $num['pat']=$authority['pat']['read']?$patMdl::getPeriodNum():0;
      
      $num['pro']=[
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
      ];

      $num['the']=[
        'total'=>'x',
        'audit'=>'x',
        'plan'=>'x',
        'apply'=>'x',
        'accept'=>'x',
        'publish'=>'x',
        'reject'=>'x'
      ];
      
      $this->assign([
        'home' => $request->domain(), 
        'username' => $username,
        #各模块统计数据
        'num' => json_encode($num,JSON_UNESCAPED_UNICODE),
        'test'=>json_encode($authority,JSON_UNESCAPED_UNICODE),
        'year' => date('Y'), 
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
    $this->success('安全退出系统', 'index/login');

  }
  
  public function getEntSearchFormVal() {
    
    
  }


}
