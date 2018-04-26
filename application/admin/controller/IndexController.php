<?php
namespace app\admin\controller;

use think\Request;
use think\Session;
use think\Model;


use app\admin\model\Rolety as RoletyModel;
use app\admin\model\Dept as DeptModel;
use app\admin\model\Usergroup as UsergroupModel;
use app\admin\model\User as UserModel;

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
      //用户是否已经登录。
      $this->_loginUser();
       
      $username=Session::get('username');
      $pwd=Session::get('pwd');
      
      // 限制登录的用户必须rolety_id=8（admin）或9（superadmin）
      $user = UserModel::where('username',$username)
                            ->where('pwd',$pwd)
                            ->where('rolety_id','in','8,9')
                            ->select();
                            
      if(empty($user)){
          $this->error('用户名或密码错误，请重新登录');
          //return view("login"); 
      }else{                     
        $this->assign([
            //--在bg-head.html页面输出自定义信息的HTML代码块
              'destr'=>$destrr= "请求方法:".$request->method()."</br>".
                                "username:".$this->username."</br>".
                                "log:".$this->log."</br>",
              
              'home'=>$request->domain(),
              'username'=>$username,
              
        ]);
        return view('index');
      }
    }
    
    public function index2(Request $request)
    {
      //用户是否已经登录。
      $this->_loginUser();
       
      $this->assign([
            //--在bg-head.html页面输出自定义信息的HTML代码块
              'destr'=>$destrr= "请求方法:".$request->method()."</br>".
                                "username:".$this->username."</br>".
                                "log:".$this->log."</br>",
              
              'home'=>$request->domain(),
              'username'=>$this->username,
              
        ]);
      return view();
    }
    
     // 输出系统摘要模板
    public function sys_sumary()
    {
      $this->_loginUser();
      //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
      
      $this->assign([
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
    
    // 输出系统用户模板
    public function sys_user(Request $request)
    {
      $this->_loginUser();
      //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
      
      // 分页页数变量：“pageUserNum”
      if(!empty($request->param('pageUserNum'))){
          $pageUserNum=$request->param('pageUserNum');
      }else{
          $pageUserNum=1;
      }
      
      //$userTableRows接收页面传来的分页时每页表格显示的记录行数，初始值为10
        if(!empty($request->param('userTableRows'))){
          $userTableRows=$request->param('userTableRows');
        }else{
          $userTableRows=10;
        }
        
        // 查询词1，'searchUserName'
        if(!empty($request->param('searchUserName'))){
          $searchUserName=$request->param('searchUserName');
        }else{
          $searchUserName=0;
        } 
        
        // 查询词2，'searchDept'
        if(!empty($request->param('searchDept'))){
          $searchDept=$request->param('searchDept');
        }else{
          $searchDept=0;
        } 
        
        // 查询词3，'$searchUsergroup'
        if(!empty($request->param('$searchUsergroup'))){
          $searchUsergroup=$request->param('$searchUsergroup');
        }else{
          $searchUsergroup=0;
        } 
        
        //$sortName、$sort接收页面传来的排序信息
        $sortName=$request->param('sortName');
        $sort=$request->param('sort');
        //  升序asc查询
        if($sort=="_ASC"){
          switch($sortName){
            case '_USERNAME':
              $strOrder='username asc';
              
            break;
            
            case '_DEPT':
              $strOrder='dept asc';
              
            break;
            
            case '_USERGROUP':
              $strOrder='rolety_id asc';
             
            break;
            
            case '_ENABLE':
              $strOrder='enable asc';
             
            break;
            
            //默认按字段“username”的升序
            default:
              $strOrder='username asc';  
              $sortName="_USERNAME";
              $sort="_ASC";
             
            break;
          } 
        }else{
          // 降序desc查询
          switch($sortName){
            case '_USERNAME':
              $strOrder='username desc';
              
            break;
            
            case '_DEPT':
              $strOrder='dept desc';
              
            break;
            
            case '_USERGROUP':
              $strOrder='rolety_id desc';
             
            break;
            
            case '_ENABLE':
              $strOrder='enable desc';
             
            break;
            
            //默认按字段“username”的升序
            default:
              $strOrder='username asc';  
              $sortName="_USERNAME";
              $sort="_ASC";
              
            break;
          } 
        }
        
        // 组合查询条件，
        if($searchDept!='0'){
          $map['dept'] = $searchDept;
                
          if(!empty($searchUserName) && $searchUsergroup!=0 ){
            
            $map['username'] = ['like','%'.$searchUserName.'%'];
            $map['rolety_id'] = $searchUsergroup;
            
          }elseif(!empty($searchUserName) && $searchUsergroup==0  ){
            
            $map['username'] = ['like','%'.$searchUserName.'%'];
            
          }elseif($searchUsergroup!=0 && empty($searchUserName)){
    
            $map['rolety_id']  = $searchUsergroup;
            
          }else{
            
          }
          
        }else{
          
          if(!empty($searchUserName) && $searchUsergroup!=0 ){
            $map['username'] = ['like','%'.$searchUserName.'%'];
            $map['rolety_id'] = $searchUsergroup;
            
          }elseif(!empty($searchUserName) && $searchUsergroup==0  ){
            $map['username'] = ['like','%'.$searchUserName.'%'];
            
          }elseif($searchUsergroup!=0 && empty($searchUserName)){
            $map['rolety_id']  = $searchUsergroup;
            
          }else{
            $map='';
          }
          
        }
        
        // 查出所有的用户并分页，根据“strOrder”排序，设定前端页面显示的锚点（hash值）为“div1”，设定分页页数变量：“pageUserNum”
        // 带上每页显示记录行数$userTableRows和3个查询词，实现查询结果分页显示。
        $users = UserModel::where('id','>',0)
                            //->where('dept',$searchDept)
                            ->where($map)
                            ->order($strOrder)
                            ->paginate($userTableRows,false,['type'=>'bootstrap','fragment'=>'div1','var_page'=>'pageUserNum',
                            'query'=>['userTableRows'=>$userTableRows,'searchDept'=>$searchDept,'searchUserName'=>$searchUserName,'$searchUsergroup'=>$searchUsergroup]]);                     
          
        // 分页变量
        $pageUser = $users->render();
        
        // 记录总数
        $userRecords= UserModel::where('id','>',0)
                            ->where($map)
                            //->where('dept',$searchDept)
                            ->count();
        
        // 查出所有的用户总数
        $users1= UserModel::where('id','>',0)
                            ->where($map)
                            //->where('dept',$searchDept)
                            ->group('username')
                            ->select();
        //$usersNum=count($users1);     
        $usersNum=count(UserModel::where('id','>',0)
                            ->where($map)
                            //->where('dept',$searchDept)
                            ->group('username')
                            ->select());    
 
        foreach($users as $v){
            $user1 = UserModel::get($v['id']);
            // 使用User模型中定义的关联关系(role)查出用户对应用户组名称(name)
            $roleName=$user1->role->name;
            // 将用户对应的用户组名称加入数据集$users中。
            $v['rolename']=$roleName;
        }
      
      $this->assign([
             'home'=>$request->domain(),
             
             // 所有用户信息
              'users'=>$users,
              'usersNum'=>$usersNum,
              'userRecords'=>$userRecords,
              
              'pageUser'=>$pageUser,
              'pageUserNum'=>$pageUserNum,
              
              // 表格搜索字段
              'searchUserName'=>$searchUserName,
              'searchDept'=>$searchDept,
              '$searchUsergroup'=>$searchUsergroup,
              
              // 表格排序信息
              'sortName'=>$sortName,
              'sort'=>$sort,
              'userTableRows'=>$userTableRows,
             
        ]);
      return view();
    }
    
    // 输出系统用户组模板
    public function sys_usergroup(Request $request)
    {
      $this->_loginUser();
      //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
     
      // 查出所有用户组
      $groups = RoletyModel::where('id','>',0)
                              ->order('name asc')
                              ->select(); 
      $this->assign([
              'home'=>$request->domain(),
              // 所有用户组信息
              'groups'=>$groups,
        ]);
      return view();
    }
    
    // 输出系统设置模板
    public function sys_param(Request $request)
    {
      $this->_loginUser();
      //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
      
      // 查出所有部门信息
      $depts = DeptModel::all();
      
      $this->assign([
              'home'=>$request->domain(),
      
              'depts'=>$depts,
              
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
      $this->username=Session::get('username');
      $this->pwd=Session::get('pwd');
      $this->dept=Session::get('dept');
      
      if(1!=$this->log){
        return $this->error('无用户名或密码，请先登录系统');
      }    
    }
    
    // 获取所有部门信息,不能写成“_dept”，因为前端的HTML文件中的url里不能含有“_”开头的名称，否则就无法访问到，会报错
    public function dept()
    {
      $this->_loginUser();
      
      $dept=DeptModel::where('enable','1')->select();
      // 将数组转化为json
      return json($dept);
    }
    
    // 获取所有用户组名称
    public function userGroup()
    {
        $this->_loginUser();
        
        //$groups=RoletyModel::field('id,name')->select();
        $groups=UsergroupModel::field('id,name')->select();
        // 将数组转化为json
        return json($groups);
    }
    
    //响应前端请求，返回信息
    public function selectResponse(Request $request)
    {
      $this->_loginUser();
      $req=$request->param('req');
      switch($req){
        case '_DEPT':
          $res=DeptModel::where('enable','1')->select();
        break;
        
        case '_USERGROUP':
          $res=UsergroupModel::field('id,name')->select();
        break;
        
      }
      // 将数组转化为json
      return json($res);
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
    public function check(Request $request,UserModel $userMdl)
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
      
      // 查询数据库中是否存在页面传来的数据
      // 限制登录的用户必须rolety_id=6（admin）或7（superadmin）
      $user = UserModel::where('username',$username)
                            ->where('pwd',$pwd)
                            ->where('rolety_id','in','8,9')
                            ->whereOr('usergroup_id','in','6,7')
                            ->find();
                            
      if(empty($user)){
        $this->error('用户名或密码错误，请重新登录');
          //return view("login"); 
      }else{
        // 写入session
          Session::set('pwd',$user->pwd);
          Session::set('username',$user->username);
          Session::set('log',1);
          Session::set('role','管理员');
          Session::set('dept',$user->dept);
          //调用User模型层定义的refreshUserAuth()方法，刷新登录用户的各个模块权限
          $userMdl->refreshUserAuth($user->username,$user->pwd);
          
        // 重定向到index页面
       // $this->redirect('index');
       $this->redirect('index2');
      }
      
    }
        
    // 用户组CDUR，接收客户端通过Ajax，post来的参数，返回json数据
    public function oprtUserGroup(Request $request)
    {
      $this->_loginUser();  
      
      //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
      
      $oprt=$request->param('oprt');
      $id=$request->param('id');      
      
      // 表单提交数据
      if(!empty($request->param('userGroupName'))){
        $userGroupName=$request->param('userGroupName');
      }else{
        $userGroupName='';
      }
      // 前台addnew权限
      if($request->param('addnew')=="true"){
        $addNew=1;
      }else{
        $addNew=0;
      }
      // 前台submit权限
      if($request->param('submit')=="true"){
        $submit=1;
      }else{
        $submit=0;
      }
      // 前台modify权限
      if($request->param('modify')=="true"){
        $modify=1;
      }else{
        $modify=0;
      }
      // 前台upfile权限
      if($request->param('upfile')=="true"){
        $upFile=1;
      }else{
        $upFile=0;
      }
      // 前台downfile权限
      if($request->param('downfile')=="true"){
        $downFile=1;
      }else{
        $downFile=0;
      }
      // 前台audit权限
      if($request->param('audit')=="true"){
        $audit=1;
      }else{
        $audit=0;
      }
      // 前台refuse权限
      if($request->param('refuse')=="true"){
        $refuse=1;
      }else{
        $refuse=0;
      }
      //前台reject权限
      if($request->param('reject')=="true"){
        $reject=1;
      }else{
        $reject=0;
      }
      // 前台approve权限
      if($request->param('approve')=="true"){
        $approve=1;
      }else{
        $approve=0;
      }
      // 前台inspect权限
      if($request->param('inspect')=="true"){
        $inspect=1;
      }else{
        $inspect=0;
      }
      // 后台权限
      if($request->param('bgoEn')=="true"){
        $bgoEn=1;
      }else{
        $bgoEn=0;
      }
      //用户组启用
      if($request->param('userGroupEn')=="true"){
        $userGroupEn=1;
      }else{
        $userGroupEn=0;
      }
      
       // $oprtId=0进行添加，否则是更新
      if($request->param('oprtId')){
        $oprtId=$request->param('oprtId');
      }else{
        $oprtId=0;
      }   
      
      switch($oprt){
          case "add":
          //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
          if($oprtId){
            // save为更新操作，save操作后返回的是受影响的行数
            $userGroup = RoletyModel::get($oprtId);
            $n=$userGroup->save([
              'name'  => $userGroupName,
              'addnew' => $addNew,
              'submit' => $submit,
              'modify' => $modify,
              'upfile' => $upFile,
              'downfile' => $downFile,
              'audit' => $audit,
              'refuse' => $refuse,
              'reject' => $reject,
              'approve' => $approve,
              'inspect' => $inspect,
              'bgo' => $bgoEn,
              'enable' => $userGroupEn,
            ]);

            if($n){
              $result='success';
              $msg='用户组：【'.$userGroup->name.'】更新成功。继续更新？';
            }else{
              $result='error';
              $msg='用户组：【'.$userGroupName.'】没有更新。继续更新？';
            }   
          }else{
            // save为添加操作
            $userGroups =new RoletyModel;    
            $userGroup = $userGroups->where('name',$userGroupName)->find();
            if(!empty($userGroup)){
              $result='error';
              $msg='用户组：【'.$userGroupName.'】已存在。重新添加？';
            }else{
              $n=$userGroups->save([
                'name'  => $userGroupName,
                'addnew' => $addNew,
                'submit' => $submit,
                'modify' => $modify,
                'upfile' => $upFile,
                'downfile' => $downFile,
                'audit' => $audit,
                'refuse' => $refuse,
                'reject' => $reject,
                'approve' => $approve,
                'inspect' => $inspect,
                'bgo' => $bgoEn,
                'enable' => $userGroupEn,
              ]);
              if($n){
                $result='success';
                $msg='新用户组：【'.$userGroups->name.'】添加成功。继续添加？';
              }else{
                $result='error';
                $msg='新用户组：【'.$userGroupName.'】添加失败。重新添加？';
              } 
                
            }
          }    
          // 返回前端JSON数据
          return ['result'=>$result,'msg'=>$msg];
            
        break;
        
        case "delete":
          $n=RoletyModel::destroy($id);
          if($n){
            $result='success';
            $msg='成功删除用户组【'.$userGroupName.'】。';
          }else{
            $result='error';
            $msg='用户组【'.$userGroupName.'】删除失败。';
          }
          // 返回前端JSON数据
          return ['result'=>$result,'msg'=>$msg];
        break;
        
        case "disable":
          RoletyModel::update(['enable'=> 0], ['id' => $id]);
          
          $result='success';
          // 返回前端JSON数据
          return ['result'=>$result,'userGroupId'=>$id];
          
        break;
        
        case"enable":
          RoletyModel::update(['enable'=> 1], ['id' => $id]);
          
          $result='success';
          // 返回前端JSON数据
          return ['result'=>$result,'userGroupId'=>$id];
          
        break;
        
        case"edit":
          $result=RoletyModel::get($id);
          
          // $result='success';
          // 返回前端JSON数据
          return ['result'=>$result,'userGroupId'=>$id];
          
        break;
        
      }
      // 检查用户组名称是否已存在
      //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
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
      $id=$request->param('id');
     
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
          UserModel::destroy($id);
          
          $result='success';
          // 返回前端JSON数据
          return ['result'=>$result];
        break;
        
        case"disable":
          UserModel::update(['enable'=> 0], ['id' => $id]);
          
          $result='success';
          // 返回前端JSON数据
          return ['result'=>$result,'uid'=>$id];
          
        break;
        
        case"enable":
          UserModel::update(['enable'=> 1], ['id' => $id]);
          
          $result='success';
          // 返回前端JSON数据
          return ['result'=>$result,'uid'=>$id];
          
        break;
        
      }
    }
    
     // 用户CDUR，接收客户端通过Ajax，post来的参数，返回json数据
    public function oprtDept(Request $request)
    {
      $this->_loginUser();  
      
      $oprt=$request->param('oprt');
      $username=$request->param('username');
      $pwd=md5($request->param('pwd'));
      $id=$request->param('id');
      
      // 表单提交数据
      // 部门全称
      if(!empty($request->param('deptName'))){
        $deptName=$request->param('deptName');
      }else{
        $deptName='';
      }
      // 部门简称
      if(!empty($request->param('deptAbbr'))){
        $deptAbbr=$request->param('deptAbbr');
      }else{
        $deptAbbr='';
      }
      //部门启用
      if($request->param('deptEn')=="true"){
        $deptEn=1;
      }else{
        $deptEn=0;
      }
      // $oprtId=0进行添加，否则是更新
      if($request->param('oprtId')){
        $oprtId=$request->param('oprtId');
      }else{
        $oprtId=0;
      }   
     
      switch($oprt){
        case "add":
          //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
          if($oprtId){
            // save为更新操作，save操作后返回的是受影响的行数
            $dept = DeptModel::get($oprtId);
            $n=$dept->save([
              'name'  => $deptName,
              'abbr' => $deptAbbr,
              'enable' => $deptEn,
            ]);

            if($n){
              $result='success';
              $msg='部门：【'.$dept->name.'】更新成功。继续更新？';
            }else{
              $result='error';
              $msg='部门：【'.$deptName.'】没有更新。继续更新？';
            }   
          }else{
            // save为添加操作
            $depts =new DeptModel;    
            $dept = $depts->where('name',$deptName)->find();
            if(!empty($dept)){
              $result='error';
              $msg='部门：【'.$deptName.'】已存在，重新添加？';
            }else{
              $n=$depts->save([
                'name'  => $deptName,
                'abbr' => $deptAbbr,
                'enable' => $deptEn,
              ]);
              if($n){
                $result='success';
                $msg='新部门：【'.$depts->name.'】添加成功。继续添加？';
              }else{
                $result='error';
                $msg='新部门：【'.$deptName.'】添加失败。重新添加？';
              } 
                
            }
          }
          // 返回前端JSON数据
          return ['result'=>$result,'msg'=>$msg];
            
        break;
        
        case"delete":
          DeptModel::destroy($id);
          
          $result='success';
          // 返回前端JSON数据
          return ['result'=>$result];
        break;
        
        case"disable":
          DeptModel::update(['enable'=> 0], ['id' => $id]);
          
          $result='success';
          // 返回前端JSON数据
          return ['result'=>$result,'deptId'=>$id];
          
        break;
        
        case"enable":
          DeptModel::update(['enable'=> 1], ['id' => $id]);
          
          $result='success';
          // 返回前端JSON数据
          return ['result'=>$result,'deptId'=>$id];
          
        break;
        
        case"edit":
          $result=DeptModel::get($id);
          // 返回前端JSON数据
          return ['result'=>$result,'deptId'=>$id];
          
        break;
        
      }
    }
    
    // 搜索用户，将index里面有关搜索的代码抽取后完善
    public function search(Request $request)
    {
      $this->_loginUser();  
      
      Route::rule('blog/:id','index/blog/read');

      //就可以使用下面的方式来生成URL地址：
      Url::build('index/blog/read','id=5&name=thinkphp');
      Url::build('index/blog/read',['id'=>5,'name'=>'thinkphp']);
      
      // 重定向到index页面
      $this->redirect('index', ['username' => $searUserName,'pwd' => $pwd]);
      // 检查用户组名称是否已存在
      return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
      
      
    }
    
    // 根据前端发送的模板文件名参数，选择对应的页面文件返回
    public function tplFile(Request $request,UsergroupModel $usergroupMdl)
    {
      $this->_loginUser();
      //前端发送的是锚点值
      if(!empty($request->param('sId'))){
        $tplFile=$request->param('sId');
      }else{
        $tplFile='#sysSummary';
      }
      
      //返回模板文件
      if(substr($tplFile,0,1)=='#'){
        $tplFile=substr($tplFile,1);
        $this->redirect($tplFile);
      }else{
        return '模板文件不存在。';
      }
    
    }
    
     // 输出系统摘要模板
    public function sysSummary()
    {
      $this->_loginUser();
      //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
      
      $this->assign([
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
    
    // 输出系统用户模板
    public function sysUser(Request $request,UsergroupModel $usergroupMdl)
    {
      $this->_loginUser();
      //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
      
      // 分页页数变量：“pageUserNum”
      if(!empty($request->param('pageUserNum'))){
          $pageUserNum=$request->param('pageUserNum');
      }else{
          $pageUserNum=1;
      }
      
       //$userTableRows接收页面传来的分页时每页表格显示的记录行数，初始值为10
        if(!empty($request->param('userTableRows'))){
          $userTableRows=$request->param('userTableRows');
        }else{
          $userTableRows=10;
        }
        // 基本查询条件， 
        $map['id']=['>',0];  
        // 查询词1，'searchUserName'
        if(!empty($request->param('searchUserName'))){
          $searchUserName=$request->param('searchUserName');
          // 组合查询条件1，
          $map['username']=['like','%'.$searchUserName.'%'];
        }else{
          $searchUserName=0;
        } 
        
        // 查询词2，'searchDept'
        if(!empty($request->param('searchDept'))){
          $searchDept=$request->param('searchDept');
          // 组合查询条件2，
          $map['dept']=$searchDept;
        }else{
          $searchDept=0;
        } 
        
        // 查询词3，'$searchUsergroup'
        if(!empty($request->param('searchUsergroup'))){
          $searchUsergroup=$request->param('searchUsergroup');
          // 组合查询条件3，
          $map['usergroup_id']=['like','%'.$searchUsergroup.'%'];
        }else{
          $searchUsergroup=0;
        }

        //$sortName、$sort接收页面传来的排序信息
        $sortName=$request->param('sortName');
        $sort=$request->param('sort');
        //  升序asc查询
        if($sort=="_ASC"){
          switch($sortName){
            case '_USERNAME':
              $strOrder='username asc';
              
            break;
            
            case '_DEPT':
              $strOrder='dept asc';
              
            break;
            
           // case '_USERGROUP':
//              $strOrder='rolety_id asc';
//             
//            break;
            
            case '_ENABLE':
              $strOrder='enable asc';
             
            break;
            
            //默认按字段“username”的升序
            default:
              $strOrder='username asc';  
              $sortName="_USERNAME";
              $sort="_ASC";
             
            break;
          } 
        }else{
          // 降序desc查询
          switch($sortName){
            case '_USERNAME':
              $strOrder='username desc';
              
            break;
            
            case '_DEPT':
              $strOrder='dept desc';
              
            break;
            
           // case '_USERGROUP':
//              $strOrder='rolety_id desc';
//             
//            break;
            
            case '_ENABLE':
              $strOrder='enable desc';
             
            break;
            
            //默认按字段“username”的升序
            default:
              $strOrder='username asc';  
              $sortName="_USERNAME";
              $sort="_ASC";
              
            break;
          } 
        }
        
        // 查出所有的用户并分页，根据“strOrder”排序，设定前端页面显示的锚点（hash值）为“div1”，设定分页页数变量：“pageUserNum”
        // 带上每页显示记录行数$userTableRows和3个查询词，实现查询结果分页显示。
        $users = UserModel::where($map)
                            ->order($strOrder)
                            ->paginate($userTableRows,false,['type'=>'bootstrap','fragment'=>'sysUser','var_page'=>'pageUserNum']);
                           // ->paginate($userTableRows,false,['query'=>['userTableRows'=>$userTableRows,'searchUsergroup'=>$searchUsergroup,'searchDept'=>$searchDept,'searchUserName'=>$searchUserName]
//                                                              ,'type'=>'bootstrap','fragment'=>'sysUser','var_page'=>'pageUserNum']);                                                                           
          
        // 分页变量
        $pageUser = $users->render();
        
        // 记录总数
        $userRecords= UserModel::where('id','>',0)
                            ->where($map)
                            //->where('dept',$searchDept)
                            ->count();
        
        // 查出所有的用户总数
        $users1= UserModel::where('id','>',0)
                            ->where($map)
                            //->where('dept',$searchDept)
                            ->group('username')
                            ->select();
        //$usersNum=count($users1);     
        $usersNum=count(UserModel::where('id','>',0)
                            ->where($map)
                            //->where('dept',$searchDept)
                            ->group('username')
                            ->select());    
 
       // foreach($users as $v){
//            $user1 = UserModel::get($v['id']);
//            // 使用User模型中定义的关联关系(role)查出用户对应用户组名称(name)
//            $roleName=$user1->role->name;
//            // 将用户对应的用户组名称加入数据集$users中。
//            $v['rolename']=$roleName;
//        }
        
        //添加groupNameNum字段到数据集$users中
        //字段内容：根据user的usergroup_id字段添加UserGroup.name的内容后生成$groupNameNum字符串
        foreach($users as $v){
          $groupNameNum='';
          //$groupNameNum=array();
          //根据user的usergroup_id字段生成数组
          $usergroup_id= explode(",", $v['usergroup_id']);//$usergroup_id=array(8,9,10)
          if(count($usergroup_id)){
            //由数组每个元素查找对应的name值
            for($i=0;$i<count($usergroup_id);$i++){
              //$groupNameNum[]=$usergroupMdl::get($usergroup_id[$i])['name'].'('.$usergroup_id[$i].')&nbsp;';
              //由数组每个元素查及对应的name值拼接成字符串
              $groupNameNum.=$usergroupMdl::get($usergroup_id[$i])['name'].'('.$usergroup_id[$i].')&nbsp;<br>';
            }
          }
          //$v['groupNameNum']=json_encode($groupNameNum);
          //添加groupNameNum字段到数据集$users中
          $v['groupNameNum']=$groupNameNum;
        }
      
      $this->assign([
             'home'=>$request->domain(),
             
             // 所有用户信息
              'users'=>$users,
              'usersNum'=>$usersNum,
              'userRecords'=>$userRecords,
              
              'pageUser'=>$pageUser,
              'pageUserNum'=>$pageUserNum,
              
              // 表格搜索字段
              'searchUserName'=>$searchUserName,
              'searchDept'=>$searchDept,
              'searchUsergroup'=>$searchUsergroup,
              
              // 表格排序信息
              'sortName'=>$sortName,
              'sort'=>$sort,
              'userTableRows'=>$userTableRows,
             
        ]);
      return view();
    }
    
    // 输出系统用户组模板
    public function sysUsergroup(Request $request)
    {
      $this->_loginUser();
      //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
     
     // 查出所有的用户组并分页，根据“strOrder”排序，设定前端页面显示的锚点（hash值）为“div1”，设定分页页数变量：“pageUserNum”
        // 带上每页显示记录行数$userTableRows和3个查询词，实现查询结果分页显示。
//        $users = UserModel::where('id','>',0)
//                            ->where('dept',$searchDept)
//                            ->where($map)
//                            ->order($strOrder)
//                            ->paginate($userTableRows,false,['type'=>'bootstrap','fragment'=>'div1','var_page'=>'pageUserNum',
//                            'query'=>['userTableRows'=>$userTableRows,'searchDept'=>$searchDept,'searchUserName'=>$searchUserName,'searchUsergroup'=>$searchUsergroup]]);                     
//          
        // 分页页数变量：“usergroupPageNum”
      if(!empty($request->param('usergroupPageNum'))){
          $usergroupPageNum=$request->param('usergroupPageNum');
      }else{
          $usergroupPageNum=1;
      }
      
      //$usergroupTableRows接收页面传来的分页时每页表格显示的记录行数，初始值为10
        if(!empty($request->param('usergroupTableRows'))){
          $usergroupTableRows=$request->param('usergroupTableRows');
        }else{
          $usergroupTableRows=10;
        }
       
        // 查出所有用户组
        $usergroup = UsergroupModel::where('id','>',0)
                              ->order('id asc')
                              ->paginate($usergroupTableRows,false,['type'=>'bootstrap','var_page'=>'usergroupPageNum']);                     
        
        // 分页变量
        $usergroupPage = $usergroup->render();
        
        // 记录总数
        $usergroupNum = UsergroupModel::where('id','>',0)
                            ->count();
     
      
      $this->assign([
              'home'=>$request->domain(),
              // 所有用户组信息
              'usergroup'=>$usergroup,
              'usergroupNum'=>$usergroupNum,
              'usergroupPage'=>$usergroupPage,
              'usergroupPageNum'=>$usergroupPageNum,
              'usergroupTableRows'=>$usergroupTableRows
        ]);
      return view();
    }
    
     // 输出系统设置模板
    public function sysSetting(Request $request)
    {
      $this->_loginUser();
      //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
      
      // 查出所有部门信息
      $depts = DeptModel::all();
      
      $this->assign([
              'home'=>$request->domain(),
      
              'depts'=>$depts,
              
        ]);
      return view();
    }
    
     // 用户CDUR，接收客户端通过Ajax，post来的参数，返回json数据
    public function userOprt(Request $request,UserModel $userMdl,$oprt='_CREATE',$id='0')
    {
      $this->_loginUser();
      
      $oprt=$request->param('oprt');
      $id=$request->param('id');
      $result='';
      $msg='';
      $msgPatch='';
      //1.分情况变量赋值
      if($oprt=='_CREATE') {
        $userData=array('username'=>$request->param('userName'),
                        'pwd'=>md5($request->param('pwd')),
                        'dept'=>$request->param('dept'),
                        'enable'=>$request->param('userEn'),
                        'usergroup_id'=>$request->param('usergroup_id'),
                        );
      
      }elseif($oprt=='_UPDATE'){
        $userData=array('username'=>$request->param('userName'),
                        'dept'=>$request->param('dept'),
                        'enable'=>$request->param('userEn'),
                        'usergroup_id'=>$request->param('usergroup_id'),
                        );
        
      }
      
      //2.分情况执行业务逻辑，生成返回前端的数据、操作数据库等 
      switch($oprt){
        case '_ADDNEW':
          $user=array('id'=>$id,'username'=>'','dept'=>'','mobile'=>'');
          
        break;
        
        case '_EDIT':
          //调用Usergroup模型层定义的initUsergroupAuth()方法，初始化用户组的各个模块权限
          //$usergroupMdl->initUsergroupAuth($id);
          $user=$userMdl::get($id);
        break;
        
        case '_CREATE':
          $user=$userMdl::get(['username'=>$request->param('userName')]);
          $name=$request->param('userName');
          $result='success';
          $id=$user->id;
          if(count($user)){
            $msg='创建失败。<br>';
            $msgPatch='用户【'.$request->param('userName').'】已存在。';
          }else{
            $user=$userMdl::create($userData,true);
            $msg='创建成功。';
          }
        break;
        
        case '_UPDATE': 
          //$n=count($usergroupMdl->where('name',$request->param('usergroupName'))->select());
//          $name=$usergroupMdl::get($id)->name;
//          if($request->param('usergroupName')==$name){
//            $usergroup = $usergroupMdl::update($usergroupData,['id'=>$id],true);
//            $result='success';
//            $msg='更新成功。<br>';
//          }else if($request->param('usergroupName')!=$name && $n){  
//            $usergroup=$usergroupMdl::get($id);
//            $result='false';
//            $msg='修改失败。<br>';
//            $msgPatch='用户组【'.$request->param('usergroupName').'】已存在。';
//            
//          }else{
//            // 使用静态方法，向Usergroup表更新信息，赋值有变化就会更新和返回对象，无变化则无更新和对象返回。
//            $usergroup = $usergroupMdl::update($usergroupData,['id'=>$id],true);
//            $result='success';
//            $msg='修改成功。<br>';
//            $msgPatch='修改为【'.$request->param('usergroupName').'】';
//          }
          
        break;
        
        case '_DELETE':
          $name=$userMdl::get($id)->name;  
          $userMdl::destroy($id);
          $result='success';
          $msg='删除成功。<br>';
          //返回默认的$id
          $id=$userMdl::where('id','>',0)->min('id');
        break;
        
        case'_DISABLE':
          $userMdl::update(['enable'=> 0], ['id' => $id]);
          
          $result='success';
        break;
        
        case'_ENABLE':
          $userMdl::update(['enable'=> 1], ['id' => $id]);
          
          $result='success';
          
        break;
        
        
      }
      
      //3.分情况组装数据，返回前端
       if ($oprt=='_ADDNEW' || $oprt=='_EDIT'){
          $this->assign([
               'home'=>$request->domain(),
               'user'=>$user
          ]);
          // 返回前端模板文件
          return view('editUser');
       }elseif($oprt=='_CREATE' || $oprt=='_UPDATE' || $oprt=='_DELETE'){
          // 返回前端JSON数据 
          return ['result'=>$result,'id'=>$id,'name'=>$name,'msg'=>$msg,'msgPatch'=>$msgPatch];
       }elseif($oprt=='_DISABLE' || $oprt=='_ENABLE'){
          // 返回前端JSON数据 
          return ['enable'=>$userMdl::get($id)->enable,'id'=>$id];
       }
      
    }
    
    // 用户组CDUR，接收客户端通过Ajax，post来的参数，返回json数据
    //前端传来的oprt值:_CREATE、_UPDATE、_ADDNEW、_EDIT、_DISABLE、_ENABLE
    public function usergroupOprt(Request $request,UsergroupModel $usergroupMdl,$oprt='_CREATE',$id='0')
    {
      $this->_loginUser();
    
      $oprt=$request->param('oprt');
      $id=$request->param('id');
      $result='';
      $msg='';
      $msgPatch='';
      //1.分情况变量赋值
      if($oprt=='_CREATE' || $oprt=='_UPDATE') {
        if(count($request->param('authIss/a'))){
          foreach($request->param('authIss/a') as $v){
            $iss[$v]=1;
          }
        }else{
          $iss=array();
        }
        
        if(count($request->param('authPat/a'))){
          foreach($request->param('authPat/a') as $v){
            $pat[$v]=1;
          }
        }else{
          $pat=array();
        }

        if(count($request->param('authPro/a'))){
          foreach($request->param('authPro/a') as $v){
            $pro[$v]=1;
          }
        }else{
          $pro=array();
        }
        
        if(count($request->param('authThe/a'))){
          foreach($request->param('authThe/a') as $v){
            $the[$v]=1;
          }
        }else{
          $the=array();
        }
        
        if(count($request->param('authAtt/a'))){
          foreach($request->param('authAtt/a') as $v){
            $att[$v]=1;
          }
        }else{
          $att=array();
        }
        
        if(!empty($request->param('adminEn')) && $request->param('adminEn')){
          $admin=['enable'=>1];
        }else{
          $admin=['enable'=>0];
        }
        //array_merge再重新合并成新数组，得到用户组新的权限集。
        $iss=array_merge(_commonModuleAuth('_ISS'),$iss);
        $pat=array_merge(_commonModuleAuth('_PAT'),$pat);
        $pro=array_merge(_commonModuleAuth('_PRO'),$pro);
        $the=array_merge(_commonModuleAuth('_THE'),$the);
        $att=array_merge(_commonModuleAuth('_ATT'),$att);
        $admin=array_merge(_commonModuleAuth('_ADMIN'),$admin);    
        //组装数据
        $authority=array("iss"=>$iss,
                            "att"=>$att,
                            "pat"=>$pat,
                            "pro"=>$pro,
                            "the"=>$the,
                            "admin"=>$admin
                            );
        $usergroupData=array('name'=>$request->param('usergroupName'),
                                'enable'=>$request->param('usergroupEn'),
                                'authority'=>$authority);
      }
      //2. 分情况操作数据库
      switch($oprt){
        case '_ADDNEW':
          $usergroup=array('id'=>$id,'name'=>'','authority'=>_commonModuleAuth());
          
        break;
        
        case '_EDIT':
          //调用Usergroup模型层定义的initUsergroupAuth()方法，初始化用户组的各个模块权限
          //$usergroupMdl->initUsergroupAuth($id);
          $usergroup=$usergroupMdl::get($id);
        break;
        
        case '_CREATE':
          $usergroup=$usergroupMdl::get(['name'=>$request->param('usergroupName')]);
          $name=$request->param('usergroupName');
          $result='success';
          $id=$usergroup->id;
          if(count($usergroup)){
            $msg='创建失败。<br>';
            $msgPatch='用户组【'.$request->param('usergroupName').'】已存在。';
          }else{
            $usergroup=$usergroupMdl::create($usergroupData,true);
            $msg='创建成功。';
          }
        break;
        
        case '_UPDATE': 
          $n=count($usergroupMdl->where('name',$request->param('usergroupName'))->select());
          $name=$usergroupMdl::get($id)->name;
          if($request->param('usergroupName')==$name){
            $usergroup = $usergroupMdl::update($usergroupData,['id'=>$id],true);
            $result='success';
            $msg='更新成功。<br>';
          }else if($request->param('usergroupName')!=$name && $n){  
            $usergroup=$usergroupMdl::get($id);
            $result='false';
            $msg='修改失败。<br>';
            $msgPatch='用户组【'.$request->param('usergroupName').'】已存在。';
            
          }else{
            // 使用静态方法，向Usergroup表更新信息，赋值有变化就会更新和返回对象，无变化则无更新和对象返回。
            $usergroup = $usergroupMdl::update($usergroupData,['id'=>$id],true);
            $result='success';
            $msg='修改成功。<br>';
            $msgPatch='修改为【'.$request->param('usergroupName').'】';
          }
          
        break;
        
        case '_DELETE':
          $name=$usergroupMdl::get($id)->name;  
          $usergroupMdl::destroy($id);
          $result='success';
          $msg='删除成功。<br>';
          //返回默认的$id
          $id=$usergroupMdl::where('id','>',0)->min('id');
          
        break;
        
        case'_DISABLE':
          $usergroupMdl::update(array('enable'=> 0), ['id' => $id]);         
        break;
        
        case'_ENABLE':
          $usergroupMdl::update(array('enable'=> 1), ['id' => $id]);          
        break;
      }
     //3.分情况返回前端数据
     if ($oprt=='_ADDNEW' || $oprt=='_EDIT'){
        $this->assign([
             'home'=>$request->domain(),
             'usergroup'=>$usergroup
        ]);
        // 返回前端模板文件
        return view('editUsergroup');
     }elseif($oprt=='_CREATE' || $oprt=='_UPDATE' || $oprt=='_DELETE'){
        // 返回前端JSON数据 
        return ['result'=>$result,'id'=>$id,'name'=>$name,'msg'=>$msg,'msgPatch'=>$msgPatch];
     }elseif($oprt=='_DISABLE' || $oprt=='_ENABLE'){
        // 返回前端JSON数据 
        return ['enable'=>$usergroupMdl::get($id)->enable,'id'=>$id];
     }
      
    }
    
    
    
}

