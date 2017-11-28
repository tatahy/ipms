<?php
namespace app\user\controller;

use think\Request;
use think\Session;
use app\user\model\User as UserModel;
use app\user\model\Rolety as RoletyModel;
use app\issue\model\Issinfo as IssinfoModel;
use app\patent\model\Patinfo as PatinfoModel;

class DashboardController extends \think\Controller
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
    
     // 判断是否为登录用户
    private function _loginUser()
    {
      //通过$this->log判断是否是登录用户，非登录用户退回到登录页面
      $this->log=Session::get('log');
      
      if(1!=$this->log){
        return $this->error('无用户名或密码，请先登录系统');
      }    
    }
    
    public function index(Request $request)
    {
       $this->_loginUser();
       
//从session中取出登录用户的关键信息
        $username=Session::get('username');
        $pwd=Session::get('pwd');
        $log=Session::get('log');
        $roles=Session::get('role');
        $dept=Session::get('dept');
        $role=$request->param('role');
        
       
        
         //传来的role值是否在$roles中
        if(!empty($role)){
            
            $i=0;
            foreach ($roles as $value) {
                if($value==$role)
                $i=1;
            }
            if(1!=$i){
               $this->error('未授权用户，请先登录系统');
            }
        }else{
            $role=0;
        }
        //实例化IssinfoModel模型类
        $issues = new IssinfoModel; 
        
        //实例化PatinfoModel模型类
        $pats = new PatinfoModel; 
        
        
        //登录用户各角色涉及专利类事务的总数赋初值
        $num_writer1type2=0;
        $num_writer2type2=0;
        $num_writer3type2=0;
        $num_writer4type2=0;
                    
        $num_reviewer1type2=0;
        $num_reviewer2type2=0;
        $num_reviewer3type2=0;
        $num_reviewer4type2=0;
        
        $num_approver1type2=0;
        $num_approver2type2=0;
        $num_approver3type2=0;
        $num_approver4type2=0;
        
        $num_operator1type2=0;
        $num_operator2type2=0;
        $num_operator3type2=0;
        
        $num_maintainer1type2=0;
        $num_maintainer2type2=0;
        $num_maintainer3type2=0;
        
        //汇总统计登录用户各角色涉及事务的总数
        for($i = 0; $i < count($roles); $i++) {
            
            switch($roles[$i]){
                case"writer":
                    
                    //type2：专利类事务
                    $num_writer1type2=$issues->where('writer',$username)
                                            ->where('num_type','like','2%')
                                            ->where('status',['=','填报'],['=','退回修改'],'or')
                                            ->count();
                    $num_writer2type2=$issues->where('writer',$username)
                                            ->where('num_type','like','2%')
                                            ->where('status',['=','提交'],['=','审核通过'],'or')
                                            ->count();
                    $num_writer3type2=$issues->where('writer',$username)
                                            ->where('num_type','like','2%')
                                            ->where('status',['=','批准'],['=','否决'],'or')
                                            ->count();
                    $num_writer4type2=$issues->where('writer',$username)
                                            ->where('num_type','like','2%')
                                            ->where('status',['=','执行中'],['=','完成'],'or')
                                            ->count();
                    
                break;
                        
                case"reviewer":
                    
                    //type2：专利类事务
                    $num_reviewer1type2=$issues->where('num_type','like','2%')
                                            ->where('dept',$dept)
                                            ->where('status','提交')
                                            ->count();
                    $num_reviewer2type2=$issues->where('num_type','like','2%')
                                            ->where('dept',$dept)
                                            ->where('status',['=','退回修改'],['=','审核通过'],'or')
                                            ->count();
                    $num_reviewer3type2=$issues->where('num_type','like','2%')
                                            ->where('dept',$dept)
                                            ->where('status',['=','批准'],['=','否决'],'or')
                                            ->count();
                    $num_reviewer4type2=$issues->where('num_type','like','2%')
                                            ->where('dept',$dept)
                                            ->where('status',['=','执行中'],['=','完成'],'or')
                                            ->count();
                break;
                        
                case"formchecker":
                    $rolename="形式审查人";
                break;
                        
                case"financialchecker":
                    $rolename="财务审查人";
                break;
                        
                case"approver":
                    
                    //type2：专利类事务
                    $num_approver1type2=$issues->where('num_type','like','2%')
                                            ->where('status','审核通过')
                                            ->count();
                    $num_approver2type2=$issues->where('num_type','like','2%')
                                            ->where('status',['=','退回修改'],['=','批准'],['=','否决'],'or')
                                            ->count();
                    $num_approver3type2=$issues->where('num_type','like','2%')
                                            ->where('status','执行中')
                                            ->count();
                    $num_approver4type2=$issues->where('num_type','like','2%')
                                            ->where('status','完成')
                                            ->count();
                break;
                        
                case"maintainer":
                    //$rolename="维护人";
                    
                    
                    //type2：专利类事务
                    //"待处理"
                    $num_maintainer1type2=$pats->where('status','新增')
                                            ->count();
                    //"审批中"
                    $num_maintainer2type2=$pats->where('status',['=','申报'],['=','续费中'],'or')
                                            ->count();
                    //"审批结果"
                    $num_maintainer3type2=$pats->where('status',['=','授权'],['=','驳回'],['=','放弃'],['=','续费授权'],['=','返回修改'],'or')
                                            ->count();
                   
                break;
                        
                case"operator":
                    
                    //type2：专利类事务
                    $num_operator1type2=$issues->where('num_type','like','2%')
                                            ->where('executer',$username)
                                            ->where('status','批准')
                                            ->count();
                    $num_operator2type2=$issues->where('num_type','like','2%')
                                            ->where('executer',$username)
                                            ->where('status','执行中')
                                            ->count();
                    $num_operator3type2=$issues->where('num_type','like','2%')
                                            ->where('executer',$username)
                                            ->where('status','完成')
                                            ->count();
                   
                break;
                        
                default:
                    $num_writer1type2=0;
                    $num_writer2type2=0;
                    $num_writer3type2=0;
                    $num_writer4type2=0;
                    
                    $num_reviewer1type2=0;
                    $num_reviewer2type2=0;
                    $num_reviewer3type2=0;
                    $num_reviewer4type2=0;
                    
                    $num_approver1type2=0;
                    $num_approver2type2=0;
                    $num_approver3type2=0;
                    $num_approver4type2=0;
                    
                    $num_operator1type2=0;
                    $num_operator2type2=0;
                    $num_operator3type2=0;
                    
                    $num_maintainer1type2=0;
                    $num_maintainer2type2=0;
                    $num_maintainer3type2=0;
                   
            }
        }
        
        
        
        
        $destr= "请求方法:".$request->method()."<br/>".
                "username:". $username."<br/>".
                "pwd:".$pwd."<br/>".
                "roles:".$roles[0]."<br/>".
                "log:".$log; 
                
        $this->assign([
        //在usercenter.html页面输出自定义的信息
        //在index.html页面通过destr输出自定义的信息
        'destr'=>$destr."</br>",
        //在index.html页面通过array输出自定义的数组内容
        'array'=>$roles, 
        
        'home'=>$request->domain(),
        'username'=>$username,
        'roles'=>$roles,
        //'roles'=>json($roles),
        'role'=>$role,
        'role1st'=>$roles[0],
//        'pwd'=>$pwd,
//        'log'=>$log,

        'num_writer1type2'=>$num_writer1type2,
        'num_writer2type2'=>$num_writer2type2,
        'num_writer3type2'=>$num_writer3type2,
        'num_writer4type2'=>$num_writer4type2,
        
        'num_reviewer1type2'=>$num_reviewer1type2,
        'num_reviewer2type2'=>$num_reviewer2type2,
        'num_reviewer3type2'=>$num_reviewer3type2,
        'num_reviewer4type2'=>$num_reviewer4type2,
        
        'num_approver1type2'=>$num_approver1type2,
        'num_approver2type2'=>$num_approver2type2,
        'num_approver3type2'=>$num_approver3type2,
        'num_approver4type2'=>$num_approver4type2,
        
        'num_operator1type2'=>$num_operator1type2,
        'num_operator2type2'=>$num_operator2type2,
        'num_operator3type2'=>$num_operator3type2,
        
        'num_maintainer1type2'=>$num_maintainer1type2,
        'num_maintainer2type2'=>$num_maintainer2type2,
        'num_maintainer3type2'=>$num_maintainer3type2,

        ]);
        return view();
    }
    
     // 根据前端post的role返回对应的模板文件，role的可能值：“#writer”，“#reviewer”,“#approver”,“#operator”……
     // 对应的模板文件名：writer.html reviewer.html approver.html operator.html
    public function role(Request $request)
    {
      $this->_loginUser();
      //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
      
      // $role接收前端页面传来的role值，第一位是“#”，需去除
      if(!empty($request->param('role'))){
        $role=substr($request->param('role'),1);
      }else{
          $role=$this->roles[0];
      }
      
      $destr= "请求方法:".$request->method()."</br>";
      
      $this->assign([
        //在usercenter.html页面输出自定义的信息
        //在index.html页面通过destr输出自定义的信息
        'destr'=>$destr."</br>",
        
        'home'=>$request->domain(),
        
        'role'=>$role,
        ]);
      return view($role);
    }
    
     // 输出patiss模板
    public function patIss(Request $request)
    {
       $this->_loginUser();
       
       // $role接收前端页面传来的role值
      if(!empty($request->param('role'))){
        $role=$request->param('role');
      }else{
          $role=$this->roles[0];
      }
      
      // $issType接收前端页面传来的issType值
      if(!empty($request->param('issType'))){
        $issType=$request->param('issType');
      }else{
          $issType='_PATENT';
      }
      
      // $issStatus接收前端页面传来的issStatus值
      if(!empty($request->param('issStatus'))){
        $issStatus=$request->param('issStatus');
      }else{
          $issStatus='_TODO';
      }
      
      return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div> '.$role.$issType.$issStatus;
      return view();
        
    } 
    
    // patent的issue的增删改查
    public function patOprt(Request $request)
    {
      $this->_loginUser();
       
      
      return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div> ';
      return view();
        
    } 
    
     // 输出theiss模板
    public function theIss(Request $request)
    {
       $this->_loginUser();
        
        // $role接收前端页面传来的role值
      if(!empty($request->param('role'))){
        $role=$request->param('role');
      }else{
          $role=$this->roles[0];
      }
      
      // $issType接收前端页面传来的issType值
      if(!empty($request->param('issType'))){
        $issType=$request->param('issType');
      }else{
          $issType='_THESIS';
      }
      
      // $issStatus接收前端页面传来的issStatus值
      if(!empty($request->param('issStatus'))){
        $issStatus=$request->param('issStatus');
      }else{
          $issStatus='_TODO';
      }
      
      return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div> '.$role.$issType.$issStatus;
      return view();
        
    }
    
    // thesis的issue的增删改查
    public function theOprt(Request $request)
    {
      $this->_loginUser();
       
      
      return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div> ';
      return view();
        
    }   
    
     // 输出proiss模板
    public function proIss(Request $request)
    {
       $this->_loginUser();
        
        // $role接收前端页面传来的role值
      if(!empty($request->param('role'))){
        $role=$request->param('role');
      }else{
          $role=$this->roles[0];
      }
      
      // $issType接收前端页面传来的issType值
      if(!empty($request->param('issType'))){
        $issType=$request->param('issType');
      }else{
          $issType='_PATENT';
      }
      
      // $issStatus接收前端页面传来的issStatus值
      if(!empty($request->param('issStatus'))){
        $issStatus=$request->param('issStatus');
      }else{
          $issStatus='_TODO';
      }
      
      return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div> '.$role.$issType.$issStatus;
      return view();
        
    }
    
    // project的issue的增删改查
    public function proOprt(Request $request)
    {
      $this->_loginUser();
       
      
      return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div> ';
      return view();
        
    }     
    
    
    
    
}
