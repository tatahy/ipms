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
  
  
  public function index(Request $request,PatinfoModel $patMdl,UserModel $userMdl,AssinfoModel $assMdl)
  {
    //'username'和'pwd'的来源：session或初次登录时表单POST提交
    if (!empty($request->post('username')))
    {
      $username = $request->post('username');
    } else
    {
      $username = Session::get('username');
    }

    if (!empty($request->post('pwd')))
    {
      $pwd = md5($request->post('pwd'));
    } else
    {
      $pwd = Session::get('pwd');
    }

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
      $this->error('登录失败，用户名或密码错误。');
    } else
    {
      //patent数据,使用模型Patinfo
      $pats = new PatinfoModel;

      //利用模型对象得到状态status"="新增"）的patent总数
      $numpatadd = $patMdl->where('status', '拟申报')->count();

      //利用模型对象得到申报的patent总数
      $numpatapp = $patMdl->where('status', ['=', '申报'], ['=', '申报修改'], 'or')->count();

      //利用模型对象得到有效的patent总数
      //$numpataut=$pats->where('status',['=','授权'],['=','续费授权'],['=','续费中'],['=','放弃续费'],'or')->count();
      $numpataut = $patMdl->where('id', '>', 0)->where('status', 'in', ['授权', '续费授权',
        '续费中', '放弃续费'])->count();

      //调用User模型层定义的refreshUserAuth()方法，刷新登录用户的各个模块权限
      $authority = $userMdl->refreshUserAuth($username, $pwd);

      Session::set('userId', $user->id);
      Session::set('username', $username);
      Session::set('pwd', $pwd);
      Session::set('log', 1);
      Session::set('dept', $user->dept);
      Session::set('authArr', $authority);
      
      //根据ass是否有read权限进行赋值
      if($authority['ass']['read']){
        $assMdl::initModel($username,$user->dept,$authority['ass']);
        $assNum=$assMdl->getAssTypeNumArr();
      }else{
        $assNum=0;
      }
      //--在index.html页面输出自定义信息的HTML代码块
      $destr = "请求方法:" . $request->method() . "</br>" . "username:" . $username .
        "</br>" . //"pwd:".sizeof($pwd);
        "pwd:" . $pwd . "</br>" . "log:" . $log . "</br>" .
        "<strong>authority Now [JSON string]:</strong>" . json_encode($authority) .
        "</br>";
      //"session:".dump($request->session());
      //--!
            
      $this->assign([ //在index.html页面通过'destr'输出自定义的信息
        'destr' => $destr . "</br>", 'home' => $request->domain(), 'username' => $username,
        //patent数据
        'numpatadd' => $numpatadd, 'numpatapp' => $numpatapp, 'numpataut' => $numpataut,
        //asset数据
        'assNum'=>$assNum,
        'year' => date('Y'), 
        ]);
      //return view();
      return $this->fetch();
    }

  }

  //修改application/config.php的设置将“默认操作”由“index”改为“login”？？
  public function login(Request $request)
  {

    $this->assign(['home' => $request->domain(), 'year' => date('Y')]);

    return view();

  }


  public function logout(Request $request)
  {
    Session::clear();
    Session::destroy();
    $this->success('安全退出系统', 'index/login');

  }


}
