<?php
namespace app\admin\controller;

use think\Request;
use think\Session;
use think\Model;
use app\user\model\User as UserModel;
use app\user\model\Rolety as RoletyModel;
use app\admin\model\Dept as DeptModel;


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
      
      $this->_loginUser();
       
        $this->assign([
          
              'destr'=>$destrr= "请求方法:".$request->method()."</br>".
                                "username:".$this->username."</br>".
                                "log:".$this->log."</br>",
              
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
    
    // 判断是否为登录用户
    private function _loginUser()
    {
      //通过$this->log判断是否是登录用户，非登录用户退回到登录页面
      $this->log=Session::get('log');
      
      if(1!=$this->log){
        return $this->error('无用户名或密码，请先登录系统');
      }    
    }
    
    // 获取所有部门信息,不能写成“_dept”，因为前端的HTML文件中的url里不能含有“_”开头的名称，否则就无法访问到，报错
    public function dept()
    {
      $this->_loginUser();
      
      $dept=DeptModel::all();
      // 将数组转化为json
      return json($dept);
    }
    
    // 获取所有用户组名称
    public function userGroup()
    {
        $this->_loginUser();
        
        $groups=RoletyModel::field('id,name')->select();
        return json($groups);
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
      
      //处理登录页面传来的数据
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
      
      //$tableRows接收页面传来的分页时每页表格显示的记录行数，初始值为10
      if(!empty($request->param('tableRows'))){
        $tableRows=$request->param('tableRows');
      }else{
        $tableRows=10;
      }
      
      $user = UserModel::where('username',$username)
                            ->where('pwd',$pwd)
                            ->where('rolety_id',8)
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
          
          // 查出所有的用户并分页
          $users = UserModel::where('id','>',0)
                            ->order('username asc')
                            ->paginate($tableRows,false,['type'=>'bootstrap','fragment'=>'div1','var_page'=>'page',
                                        'query'=>['username'=>$username,'pwd'=>$pwd,],]); 
          
          // 获取分页显示
          $page = $users->render();       
 
          foreach($users as $v){
            $user1 = UserModel::get($v['id']);
            // 使用User模型中定义的关联关系(role)查出用户对应用户组名称(name)
            $roleName=$user1->role->name;
            // 将用户对应的用户组名称加入数据集$users中。
            $v['rolename']=$roleName;
          }          
          
          // 查出所有用户组
          $groups = RoletyModel::where('id','>',0)
                                    ->order('rolenum asc')
                                    ->select();
                                   
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
              'page'=>$page,
              
              // 所有用户组信息
              'groups'=>$groups,
          
          
          ]);
          
          return view('index');
      }
      
      
    }
        
    // 添加新用户组
    public function addUserGroup(Request $request)
    {
      $this->_loginUser();  
      
      // 检查用户组名称是否已存在
      return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
      
      
    }
    
    
     // 用户CDUR，接收客户端通过Ajax，post来的参数，返回json数据
    public function oprtUser(Request $request)
    {
      $this->_loginUser();  
      
      $oprt=$request->param('oprt');
      $username=$request->param('username');
      $pwd=md5($request->param('pwd'));
      $dept=$request->param('dept');
      $roletyId=$request->param('userGroup');
     
      switch($oprt){
        case "add":
          $user=new UserModel;
          $u=$user->where('username',$username)->select();
          
      // 模型方式返回的数据集包含每个User模型对象实例的数组。将数据集/数组$u转化为“，”分隔的字符串
       //   $str=implode(',',$u);
          
          // 遍历数据集$u中的'rolety_id'值是否存在$roletyId，
          $roleBool=0;
          foreach ($u as $v) {
              if($v->rolety_id==$roletyId){
                $roleBool=1;
                break;
              }
          }
          
          //拟增加的用户组的名称，从RoletyModel中查出
          $role=RoletyModel::get($roletyId);
          $roleName=$role->name;
          
          //$result='error';
//          $msg= '数据集：'.$str.' \ '.$roleBool.' \ ';
//          return ['result'=>$result,'msg'=>$msg];
          
          // 数据集$u为空，说明要添加全新的用户
          if(empty($u)){
            // 用户信息写入数据表User
            $user->save([
              'username'  => $username,
              'pwd' => $pwd,
              'dept' => $dept,
              'rolety_id' => $roletyId,
            ]); 
        
            $result='success';
            $msg='新用户：【'.$username.' 】，用户组：['.$roleName.']添加成功。';
          }// 数据集$u中的'username'值是否存在新增的$username
           else if($username==$u['0']['username'] && $dept!=$u['0']['dept']){ 
            $result='error';
            $msg='用户：【'.$username.'】已属于部门：['.$u['0']->dept.']，请重新选择。';
          }else if($username==$u['0']->username && $dept==$u['0']->dept && $roleBool==1){
            
            $result="error";
            $msg='用户：【'.$username.'】已在['.$roleName.']用户组，请重新选择。';
          }else if($username==$u['0']->username && $dept==$u['0']->dept && $roleBool==0){
            // 未与老用户的用户组重复，添加老用户的新用户组
            // 保持老用户的密码不变
            $pwd=$u['0']['pwd'];
            // 用户信息写入数据表User
            $user->save([
              'username'  => $username,
              'pwd' => $pwd,
              'dept' => $dept,
              'rolety_id' => $roletyId,
            ]); 
        
            $result='success';
            $msg='用户：【'.$username.' 】新用户组：['.$roleName.']添加成功。';
          }else if($username!=$u['0']->username){
            // 新用户添加。
            $user->save([
              'username'  => $username,
              'pwd' => $pwd,
              'dept' => $dept,
              'rolety_id' => $roletyId,
            ]); 
            $id=$user->id;
            $result='success';
            $msg='用户：【'.$username.' 】创建成功。';
          }
         
          // 返回前端JSON数据
            return ['result'=>$result,'msg'=>$msg];
            
        break;
        
        case"delete":
          $id=$request->param('id');
          UserModel::destroy($id);
          
          $result='success';
          // 返回前端JSON数据
          return ['result'=>$result];
        break;
        
        case"disable":
          $id=$request->param('id');
          UserModel::update(['enable'=> 0], ['id' => $id]);
          
          $result='success';
          // 返回前端JSON数据
          return ['result'=>$result,'uid'=>$id];
          
        break;
        
        case"enable":
          $id=$request->param('id');
          UserModel::update(['enable'=> 1], ['id' => $id]);
          
          $result='success';
          // 返回前端JSON数据
          return ['result'=>$result,'uid'=>$id];
          
        break;
        
      }
    }
    
    
    
    
}

