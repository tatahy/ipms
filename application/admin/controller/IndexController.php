<?php
namespace app\admin\controller;

use think\Request;
use think\Session;
use think\Model;

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
       
      $this->assign([
            //--在bg-head.html页面输出自定义信息的HTML代码块
              'destr'=>$destrr= "请求方法:".$request->method()."</br>".
                                "username:".$this->username."</br>".
                                "log:".$this->log."</br>",
              
              'home'=>$request->domain(),
              'username'=>$this->username,
              'year'=>date('Y')
              
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
        
         case '_TIME':
          $res=date('Y-m-d H:i:s', time());
        break;
        
      }
      // 返回前端数组
      //return json_encode($res).json_encode($showVal).json_encode($removeVal);
      return $res;
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
                            ->where('enable',1)
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
       $this->redirect('index');
      }
      
    }
        
    // 用户CDUR，接收客户端通过Ajax，post来的参数，返回json数据
    public function deptOprt(Request $request)
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
    public function sysSummary(Request $request)
    {
      $this->_loginUser();
      
      //遍历输出全局变量$_ENV
      $strENV='';
      if(!empty($_ENV)){
        foreach($_ENV as $key=>$value){
          $strENV+='$_ENV['.$key.']='.$value.'<br>';
        }
        
      }else{
        //$strENV=phpinfo(INFO_ENVIRONMENT);
        
      }
      
      
      $this->assign([
               // 获取服务器域名 
              'serverDomain'=>$_SERVER['SERVER_NAME'],
              'serverIP'=>$_SERVER['SERVER_ADDR'],
              'serverPort'=>$_SERVER['SERVER_PORT'],
              //获取$_SERVER全局变量信息并输出
              'serverList'=>json_encode($_SERVER),
              //输出$_ENV全局变量
             // 'serverENV'=>$strENV,
              //获取服务器操作系统类型及版本号,PHP 5
              'serverOS'=>php_uname('s').php_uname('v'),
              //'serverOS'=>getenv('OS'),
    			   // 获取MySQL版本信息
              'mysqlVersion' => $this->_mysqlVersion(),
    			   //获取服务器时间
              'serverTime' => date('Y-m-d H:i:s', time()),
              // 获取Apache版本信息，PHP 5
              //'apacheVersion'=>apache_get_version(),
              'apacheVersion'=>$_SERVER['SERVER_SOFTWARE'],
              // 获取PHP版本信息，PHP 5
              'phpVersion'=>phpversion(),
              
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
                           // ->paginate($userTableRows,false,['type'=>'bootstrap','var_page'=>'pageUserNum']);
                            ->paginate($userTableRows,false,['query'=>['userTableRows'=>$userTableRows,'searchUserName'=>$searchUserName,'searchUsergroup'=>$searchUsergroup,'searchDept'=>$searchDept]
                                                              ,'type'=>'bootstrap','fragment'=>'sysUser','var_page'=>'pageUserNum']);                                                                           
          
        // 分页变量
        $pageUser = $users->render();
        
        // 查出所有的用户总数
        $usersNum=count(UserModel::where($map)
                            ->group('username')
                            ->select()); 
                            
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
                        'mobile'=>$request->param('mobile'),
                        'dept'=>$request->param('dept'),
                        'enable'=>$request->param('userEn'),
                        'usergroup_id'=>$request->param('usergroup_id'),
                        );
      
      }elseif($oprt=='_UPDATE'){
        $userData=array('username'=>$request->param('userName'),
                        'mobile'=>$request->param('mobile'),
                        'dept'=>$request->param('dept'),
                        'enable'=>$request->param('userEn'),
                        'usergroup_id'=>$request->param('usergroup_id'),
                        );
        
      }
      
      //2.分情况执行业务逻辑，生成返回前端的数据、操作数据库等 
      switch($oprt){
        case '_ADDNEW':
          $user=array('id'=>$id,'username'=>'','dept'=>'','mobile'=>'','usergroup_id'=>0);
          
        break;
        
        case '_EDIT':
          //调用Usergroup模型层定义的initUsergroupAuth()方法，初始化用户组的各个模块权限
          //$usergroupMdl->initUsergroupAuth($id);
          $user=$userMdl::get($id);
        break;
        
        case '_CREATE':
          $user=$userMdl::get(['mobile'=>$request->param('mobile')]);
          $name=$request->param('userName');
          if(count($user)){
            $result='false';
            $msg='创建失败';
            $msgPatch='手机号：'.$request->param('mobile').',已存在。';
          }else{
            $user=$userMdl::create($userData,true);
            $result='success';
            $msg='创建成功';
            $id=$user->id;
          }
        break;
        
        case '_UPDATE': 
          $name=$userMdl::get($id)->username;
          $mobile=$userMdl::get($id)->mobile;
          
          //手机号要唯一
          if($request->param('mobile')==$mobile){
              if($request->param('userName')!=$name){
                  $msgPatch='用户名更新为【'.$request->param('userName').'】。';
                  
              }
              $userMdl::update($userData,['id'=>$id],true);
              $result='success';
              $msg='保存成功';
              //else{
//                  $result='false';
//                  $msg='保存失败';
//                  $msgPatch='手机号：'.$request->param('mobile').',已存在。';
//                
//              }
              
          }else{
              if(count($userMdl::get(['mobile'=>$request->param('mobile')]))){
                  $result='false';
                  $msg='保存失败';
                  $msgPatch='手机号：'.$request->param('mobile').',已存在。';
              }else{
                  $result='success';
                  $msg='保存成功';
                  if($request->param('userName')!=$name){
                      $msgPatch='用户名更新为【'.$request->param('userName').'】。';
                  }
                  $userMdl::update($userData,['id'=>$id],true);
              }
          }
          
        break;
        
        case '_DELETE':
          $name=$userMdl::get($id)->username;  
          $userMdl::destroy($id);
          $result='success';
          $msg='删除成功';
          //返回最小的$id
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
               'user'=>$user,
               'oprt'=>$oprt
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
            $msg='创建失败';
            $msgPatch='用户组【'.$request->param('usergroupName').'】已存在。';
          }else{
            $usergroup=$usergroupMdl::create($usergroupData,true);
            $msg='创建成功';
          }
        break;
        
        case '_UPDATE': 
          $n=count($usergroupMdl->where('name',$request->param('usergroupName'))->select());
          $name=$usergroupMdl::get($id)->name;
          if($request->param('usergroupName')==$name){
            $usergroup = $usergroupMdl::update($usergroupData,['id'=>$id],true);
            $result='success';
            $msg='更新成功';
          }else if($request->param('usergroupName')!=$name && $n){  
            $usergroup=$usergroupMdl::get($id);
            $result='false';
            $msg='修改失败';
            $msgPatch='用户组【'.$request->param('usergroupName').'】已存在。';
            
          }else{
            // 使用静态方法，向Usergroup表更新信息，赋值有变化就会更新和返回对象，无变化则无更新和对象返回。
            $usergroup = $usergroupMdl::update($usergroupData,['id'=>$id],true);
            $result='success';
            $msg='修改成功';
            $msgPatch='修改为【'.$request->param('usergroupName').'】';
          }
          
        break;
        
        case '_DELETE':
          $name=$usergroupMdl::get($id)->name;  
          $usergroupMdl::destroy($id);
          $result='success';
          $msg='删除成功';
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

