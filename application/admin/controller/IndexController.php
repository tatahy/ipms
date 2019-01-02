<?php
namespace app\admin\controller;

use think\Request;
use think\Session;
use think\Model;

use app\admin\model\Dept as DeptModel;
use app\admin\model\Usergroup as UsergroupModel;
use app\admin\model\Usergroup1 as UsergroupModel1;
use app\admin\model\User as UserModel;

class IndexController extends \think\Controller
{
     //用户名
    private $userId = null;  
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
    //用户权限
    private $authArr=array();
    //响应前端要求的分页信息
    private $resPgInfo=array();
    //响应前端要求的分页信息
    private $allDept=array();
    //响应前端要求的分页信息
    private $allGroup=array();
    
    // 初始化
    protected function _initialize()
    {
        //$this->username=Session::get('username');
//        $this->pwd=Session::get('pwd');
//        $this->log=Session::get('log');
//        $this->roles=Session::get('role');
//        $this->dept=Session::get('dept');
        $this->userId='';
        $this->username='';
        $this->pwd='';
        $this->log='';
        $this->roles=array();
        $this->dept='';
        $this->authArr=array();
        $this->resPgInfo=['listRows'=>10,'pageNum'=>1,'showId'=>0,'url'=>__FUNCTION__];

    }
    
    public function index(Request $request)
    {
      //用户是否已经登录。
      $this->_loginUser();
      //$pageParam=['listRows'=>10,'pageNum'=>1,'showId'=>0,'url'=>__FUNCTION__];
      $this->assign([
              'home'=>$request->domain(),
              'username'=>$this->username,
              'pageParam'=>$this->resPgInfo,
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
      
      if(1!=$this->log){
        return $this->error('无用户名或密码，请先登录系统');
      }else{
        $this->userId=Session::get('userId');
        $this->username=Session::get('username');
        $this->pwd=Session::get('pwd');
        $this->dept=Session::get('dept');
        $this->authArr=Session::get('authArr');
        
        $this->allDept=DeptModel::getEnDepts();
        $this->allGroup=UsergroupModel1::getEnGroups();
        
        $this->_setResPgInfo($this->request->param('pageParam/a'));
      }    
    }
    
    // 将数据库中存储的auth值，组装成app/common.php中预定义的结构，方便前端使用。
    private function _authDbToFe($arr=[])
    {
      #app/common.php中预定义的结构，整个函数输出严重依赖conAuthEntArr的结构
      $authEntArr=conAuthEntArr;
      $entName='';
      $resultArr=[];
      
      if(empty($arr)){
        foreach($authEntArr as $k=>$v){
          #将ent名称转为全小写，并去掉字符串中的下划线，
          $k=strtolower(strtr($k,['_'=>'']));
          $resultArr[$k]=$v;
        }
        return $resultArr;
      }
      
      #输出数组以$authEntArr为基础，叠加$arr中值为1的部分。
      foreach($authEntArr as $k=>$v){
        #将ent名称转为全小写，并去掉字符串中的下划线，
        $k=strtolower(strtr($k,['_'=>'']));
        //$authEntArr[$k]['entEn']=$v['entEn'];
        if(isset($arr[$k])){
          foreach($arr[$k] as $kC=>$vC){
            $v['auth'][$kC]['val']=$vC;
          }
        }
        $resultArr[$k]=$v;
      }
      return $resultArr;
      
      #定义闭包得到预定义的全部auth值，组装成数据库中存储的结构，暂时没用。
      $authArr=function($arr,$lower=true){
        $nameArr=array_column($arr,'entEn');//索引数组，每个值为一个ent名称（全大写，‘-’分隔的每个字符串的首字符为下划线‘_’）
        $tArr=array_column($arr,'auth');//索引数组，每个值为一个关联数组（索引为权限名称，值为关联数组）
        $authKeys=[];
        $authVals=[];
        $authArr=[];
        $resultArr=[];
        #组装成数据库中存储的结构
        foreach($nameArr as $k=>$v){
          $authKeys[$k]=array_column($tArr[$k],'en');
          $authVals[$k]=array_column($tArr[$k],'val');
          $authArr[$k]=array_combine($authKeys[$k],$authVals[$k]);
          #将ent名称转为全小写，并去掉字符串中的下划线
          $nameArr[$k]=$lower?strtr(strtolower($nameArr[$k]),['_'=>'']):$nameArr[$k];
          $resultArr[$nameArr[$k]]=$authArr[$k];
        }
        return $resultArr;
      };
    }
    //
    private function _setResPgInfo($rqPgInfo=[])
    {
      $this->resPgInfo=empty($rqPgInfo)?$this->resPgInfo:array_merge($this->resPgInfo,$rqPgInfo);
      return $this->resPgInfo;
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
          $res=UsergroupModel::field('id,name')->where('enable','1')->select();
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
    
     public function logout(Request $request)
    {
        Session::clear();
        Session::destroy();
        $this->success('安全退出系统','index/login');
                                
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
       
          //调用User模型层定义的refreshUserAuth()方法，刷新登录用户的各个模块权限
          $userMdl->refreshUserAuth($user->username,$user->pwd);
           // 写入session
          Session::set('userId',$user->id);
          Session::set('pwd',$user->pwd);
          Session::set('username',$user->username);
          Session::set('log',1);
          //Session::set('role','管理员');
          Session::set('dept',$user->dept);
          Session::set('authArr',$user->authority);
          
        // 重定向到index页面
       // $this->redirect('index');
       $this->redirect('index');
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
      
      $tplFile=!empty($request->param('sId'))?$request->param('sId'):'#sysSummary';
           
      //前端发送的是锚点值返回模板文件
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
      //$strENV='';
//      if(!empty($_ENV)){
//        foreach($_ENV as $key=>$value){
//          $strENV+='$_ENV['.$key.']='.$value.'<br>';
//        }
//        
//      }else{
//        //$strENV=phpinfo(INFO_ENVIRONMENT);
//        
//      }
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
    public function sysUser(Request $request,UsergroupModel $usergroupMdl,UserModel $userMdl)
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
        $users = $userMdl::where($map)
                            ->order($strOrder)
                           // ->paginate($userTableRows,false,['type'=>'bootstrap','var_page'=>'pageUserNum']);
                            ->paginate($userTableRows,false,['query'=>['userTableRows'=>$userTableRows,'searchUserName'=>$searchUserName,'searchUsergroup'=>$searchUsergroup,'searchDept'=>$searchDept]
                                                              ,'type'=>'bootstrap','fragment'=>'sysUser','var_page'=>'pageUserNum']);                                                                           
          
        // 分页变量
        $pageUser = $users->render();
        
        // 查出所有的用户总数        
         $usersNum=$userMdl::where($map)
                            ->group('username')
                            ->count(); 
                            
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
    public function sysUsergroup(Request $request,UsergroupModel $usrgroupMdl)
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
        $usergroup = $usrgroupMdl::where('id','>',0)
                              ->order('id asc')
                              ->paginate($usergroupTableRows,false,['type'=>'bootstrap','var_page'=>'usergroupPageNum']);                     
        
        // 分页变量
        $usergroupPage = $usergroup->render();
        
        // 记录总数
        $usergroupNum = $usrgroupMdl::where('id','>',0)
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
    
    public function userList(UserModel $userMdl)
    {
      $this->_loginUser();
         
      $pageNum=$this->resPgInfo['pageNum'];
      $listRows=$this->resPgInfo['listRows'];
      #定义查询关键字及结果数组，关键字为数据表中的字段
      $searchDataDefault=['keys'=>['username'=>'','dept'=>'','usergroup_id'=>''],'resultNum'=>'null','trig'=>false];
      $searchData=empty($this->request->param('searchData/a'))?$searchDataDefault:array_merge($searchDataDefault,$this->request->param('searchData/a'));
      
      #定义查询条件数组
      $whereData=[];
     
      #其他查询条件
      foreach($searchData['keys'] as $k=>$v){
        if(!empty($v)){
          $whereData[$k]=($k=='dept')?$v:['like','%'.$v.'%'];
        }
      }
      $userSet=$userMdl::all(function($query)use($whereData){
                          $query->where($whereData);
                        });
      
      $userSet=is_array($userSet)?collection($userSet):$userSet;
      #因数据表user中'usergroup_id'字段值为由‘,’分隔的数值字符串，$searchData['usergroup_id']为单个数值，还需进行一次比对查找
      if(!empty($searchData['keys']['usergroup_id'])){
        foreach($userSet as $k=>$v){
          #数据表user的'usergroup_id'，已定义获取器，取原始数据
          $arr=explode(',',$userSet[$k]->getData('usergroup_id'));
          if(!in_array($searchData['keys']['usergroup_id'],$arr)){unset($userSet[$k]);}
        }
      }
      
      if($searchData['trig']){
        #返回前端查询结果数
        $searchData['resultNum']=count($userSet);
        #调整分页从第一页开始
        $pageNum=1;
      }
      
      #显示用结果集对象
      $userList=$userSet->slice(($pageNum-1)*$listRows,$listRows);
      #分页对象
      $pageSet=$userMdl->where('id','in',$userSet->column('id'))->paginate($listRows,false,['type'=>'bootstrap','var_page' =>'pageNum','page'=>$pageNum,
                        'query'=>['listRows'=>$listRows]]);    
      
      $test=$this->resPgInfo;
      $this->assign([       
        #用户总数
        'userNum'=>count($userMdl::all()),
        
        'userList'=>$userList,
        
        'pageSet'=>$pageSet,
        #分页参数
        'pageNum'=>$pageNum,
        'listRows'=>$listRows,
        'pageParam'=>json_encode($this->resPgInfo,JSON_UNESCAPED_UNICODE),
        #查询条件及结果
        'searchData'=>json_encode($searchData,JSON_UNESCAPED_UNICODE),
        #限定查询关键词的范围
        'allDept'=>json_encode($this->allDept,JSON_UNESCAPED_UNICODE),
        'allGroup'=>json_encode($this->allGroup,JSON_UNESCAPED_UNICODE),
        
        'test'=>json_encode($searchData,JSON_UNESCAPED_UNICODE)
        
      ]);
      
      return view();
      //return 'UserList';
    }
    
    #前端传来的oprt值:_CREATE、_UPDATE、_EDIT、_DISABLE、_ENABLE、_READ
    #$userMdl中mobile字段为无重复值字段
    public function userOprt1(UserModel $userMdl)
    {
      $this->_loginUser();
      $request=$this->request;
      $oprt=empty($request->param('oprt'))?'':$request->param('oprt');
      $id=empty($request->param('id'))?'':$request->param('id');
      
      $result=0;
      $msg='';
      $name='';
      #需要更新的数据表字段值
      $data['username']=empty($request->param('username'))?'':$request->param('username');
      $data['mobile']=empty($request->param('mobile'))?'':$request->param('mobile');
      $data['dept']=empty($request->param('dept'))?'':$request->param('dept');
      $data['enable']=empty($request->param('userEn'))?'':$request->param('userEn');
      $data['usergroup_id']=empty($request->param('joinedGroup/a'))?'':implode(',',$request->param('joinedGroup/a'));
      
     
      #模型Create，$oprt=='_CREATE'
      if($oprt=='_CREATE') {
        if(count($userMdl::get(['mobile'=>$data['mobile']]))){
          $msg='创建失败。手机号【'.$data['mobile'].'】已存在。';
        }else{
          $userSet= $userMdl::create($data,true);
          if($userSet->id){
            $result=1;
            $msg='成功';
            $data=$userSet;
            $name=$userSet->name;
          }else{
            $result=0;
            $msg='失败';
          }
        }      
      }
      
      #模型Delete，$oprt=='_DELETE'
      if($oprt=='_DELETE'){
        //$ugSet= $ugMdl::update(['enable'=>($oprt=='_ENABLE')?1:0],['id' => $id]);
        $userSet= $userMdl::get($id);
        $name=$userSet->name;
        if($userSet->delete()){
          $result=1;
          $msg='成功';
        }else{
          $result=0;
          $msg='失败';
        }        
      }
      
      #模型Update，$oprt:in['_ENABLE','_DISABLE','_UPDATE'] 
      if($oprt=='_ENABLE' || $oprt=='_DISABLE'){
        //$ugSet= $ugMdl::update(['enable'=>($oprt=='_ENABLE')?1:0],['id' => $id]);
        $userSet= $userMdl::get($id);
        $userSet->enable = ($oprt=='_ENABLE')?1:0;
        $msg=$userSet->save()?'success':'error';
        $result=$userSet->enable;
        $name=$userSet->username;
      }
      if($oprt=='_UPDATE' ){
        $userSet= $userMdl::get($id);
        if($userSet->save($data)){
          $result=1;
          $msg='修改成功';
        }else{
          $result=0;
          $msg='无变化';
        }
        $data=$userSet;
        $name=$userSet->username;
        
      }
      #模型Read，查询提交的$data['name']是否已存在，因为$data['name']的值在数据库中必须唯一
      if($oprt=='_READ'){
        //$ugSet=$ugMdl::all(function($query)use($data){ 
//                        $query->where('name',$data['name']);
//                      });
        $userSet=$userMdl::get(['mobile'=>$data['mobile']]);
        if(count($userSet)){
          //$id==0是‘新增’，$id!=$ugSet->id是‘编辑’
          if($id==0 || $id!=$userSet->id){
            $result=1;
            $msg='手机号【'.$data['mobile'].'】已存在。';
          }else if($id==$userSet->id){
            $msg='';
            $result=0;
          }
        }
      }
      
      return ['result'=>$result,'msg'=>$msg,'name'=>$name,'data'=>$data,'oprt'=>$oprt];    
      
    }
    //单个用户信息表单
    public function fmUserSingle(UserModel $userMdl,UsergroupModel1 $ugMdl)
    {
      $this->_loginUser();
      $request=$this->request;
      #前端传来的数据
      $oprt=empty($request->param('oprt'))?'_READ':$request->param('oprt');
      $id=empty($request->param('id'))?0:$request->param('id');
      
      $userSet['id']=$id;
      $userSet['username']='';
      $userSet['pwd']=md5('111111a');
      $userSet['mobile']='12345678901';
      $userSet['dept']='';
      $userSet['allDept']=$userMdl->getAllDept();
      $userSet['enable']=1;
      $userSet['toJoinGroup']=$userMdl->getAllGroup(); 
      $userSet['joinedGroup']=[];
      //可见字段值   
      $fieldArr=array_keys($userSet);
      if($id){
        #追加数据表中没有而模型中定义了获取器的2个字段值
        $userSet= $userMdl::get($id)->append(['joinedGroup','toJoinGroup','allDept'])->visible($fieldArr);
        $mobile=$userSet->getData('mobile');
        #要修改$userSet对象中mobile的值，必须将$userSet对象转为数组，
        #因为mobile已定义获取器，对象取值操作（$userSet->mobile）会触发获取器，所以$userSet->mobile=$mobile无法正确赋值。
        $userSet=$userSet->toArray();
        $userSet['mobile']=$mobile;
      }
      $userSet['pwd']='';
      #将数组转换为对象
      $userSet=is_array($userSet)?collection($userSet):$userSet;
                 
      $this->assign([
        #返回前端必须为对象类型:$ugSet
        'userSet'=>$userSet,
        'oprt'=>$oprt,
      ]);
      
      return view();
    }
    
    public function usergroupList(UsergroupModel1 $userGpMdl)
    {
      $this->_loginUser();
      
      $pageNum=$this->resPgInfo['pageNum'];
      $listRows=$this->resPgInfo['listRows'];
 
      $userGpSet=$userGpMdl->where('id','>',0)->select();
      $userGpSet=is_array($userGpSet)?collection($userGpSet):$userGpSet;
      $userGpList=$userGpSet->slice(($pageNum-1)*$listRows,$listRows);
      
     // $pageParam['showId']=($pageParam['showId']==0)?$userGpList[0]->id:$pageParam['showId'];
                 
      $pageSet=$userGpMdl->where('id','>',0)->paginate($listRows,false,['type'=>'bootstrap','var_page' =>'pageNum','page'=>$pageNum,
                        'query'=>['listRows'=>$listRows]]);    
                        
      $testArr=$userGpList->column('authority')[0];
      $this->assign([
        //显示结果集
        'userGpList'=>$userGpList,
        
        //分页对象
        'pageSet'=>$pageSet,
        
        'pageNum'=>$pageNum,
        'listRows'=>$listRows,
        
        'searchNum'=>$userGpSet->count(),
        'pageParam'=>json_encode($this->resPgInfo,JSON_UNESCAPED_UNICODE),
        'auth'=>json_encode(conAuthNameArr,JSON_UNESCAPED_UNICODE),
        
        'testDis'=>json_encode($testArr,JSON_UNESCAPED_UNICODE)
        //排序数组？
        
        //查询数组？
      
      ]);
      
      return view();
    }
    #前端传来的oprt值:_CREATE、_UPDATE、_EDIT、_DISABLE、_ENABLE、_READ
    #$ugMdl中的name字段为无重复值字段
    public function usergroupOprt1(Request $request,UsergroupModel1 $ugMdl)
    {
      $this->_loginUser();
      
      $oprt=empty($request->param('oprt'))?'':$request->param('oprt');
      $id=empty($request->param('id'))?'':$request->param('id');
      $reqEnt=empty($request->param('reqEnt'))?'':$request->param('reqEnt');
      
      $result=0;
      $msg='';
      $name='';
      #需要更新的数据表字段值
      $data['name']=empty($request->param('usergroupName'))?'':$request->param('usergroupName');
      $data['description']=empty($request->param('usergroupDescription'))?'':$request->param('usergroupDescription');
      $data['enable']=empty($request->param('usergroupEn'))?'':$request->param('usergroupEn');
      $data['authority']=empty($request->param('auth/a'))?'':$request->param('auth/a');

      #模型Create，$oprt=='_CREATE'
      if($oprt=='_CREATE') {
        if(count($ugMdl::get(['name'=>$data['name']]))){
          $msg='创建失败。用户组【'.$data['name'].'】已存在。';
        }else{
          $ugSet= $ugMdl::create($data,true);
          if($ugSet->id){
            $result=1;
            $msg='成功';
            $data=$ugSet;
            $name=$ugSet->name;
          }else{
            $result=0;
            $msg='失败';
          }
        }      
      }
      
      #模型Delete，$oprt=='_DELETE'
      if($oprt=='_DELETE'){
        //$ugSet= $ugMdl::update(['enable'=>($oprt=='_ENABLE')?1:0],['id' => $id]);
        $ugSet= $ugMdl::get($id);
        $name=$ugSet->name;
        if($ugSet->delete()){
          $result=1;
          $msg='成功';
        }else{
          $result=0;
          $msg='失败';
        }        
      }
      
      #模型Update，$oprt:in['_ENABLE','_DISABLE','_UPDATE'] 
      if($oprt=='_ENABLE' || $oprt=='_DISABLE'){
        //$ugSet= $ugMdl::update(['enable'=>($oprt=='_ENABLE')?1:0],['id' => $id]);
        $ugSet= $ugMdl::get($id);
        $ugSet->enable = ($oprt=='_ENABLE')?1:0;
        $msg=$ugSet->save()?'success':'error';
        $result=$ugSet->enable;
        $name=$ugSet->name;
      }
      if($oprt=='_UPDATE' ){
        $ugSet= $ugMdl::get($id);
        if($ugSet->save($data)){
          $result=1;
          $msg='修改成功';
        }else{
          $result=0;
          $msg='无变化';
        }
        $data=$ugSet;
        $name=$ugSet->name;
        
      }
      #模型Read，查询提交的$data['name']是否已存在，因为$data['name']的值在数据库中必须唯一
      if($oprt=='_READ'){
        //$ugSet=$ugMdl::all(function($query)use($data){ 
//                        $query->where('name',$data['name']);
//                      });
        $ugSet=$ugMdl::get(['name'=>$data['name']]);
        if(count($ugSet)){
          //$id==0是‘新增’，$id!=$ugSet->id是‘编辑’
          if($id==0 || $id!=$ugSet->id){
            $result=1;
            $msg='用户组【'.$data['name'].'】已存在。';
          }else if($id==$ugSet->id){
            $msg='';
            $result=0;
          }
        }
      }
      $this->assign([
        'home'=>$request->home,
        
      ]);
      
      return ['result'=>$result,'msg'=>$msg,'name'=>$name,'data'=>$data,'oprt'=>$oprt];
    }
    //单个用户组信息表单
    public function fmUsergroupSingle(UsergroupModel1 $ugMdl)
    {
      $this->_loginUser();
      $request=$this->request;
      #前端传来的数据
      $oprt=empty($request->param('oprt'))?'_READ':$request->param('oprt');
      $id=empty($request->param('id'))?'':$request->param('id');
      $reqEnt=empty($request->param('reqEnt'))?'':$request->param('reqEnt');
      $ugSet=[];   
      
      if($id){
        $ugSet= $ugMdl::get($id);
        $ugSet->authority=$this->_authDbToFe($ugSet->authority);
      }else{
        $ugSet['id']=0;
        $ugSet['name']='';
        $ugSet['description']='';
        $ugSet['enable']=1;
        $ugSet['authority']=$this->_authDbToFe();
        #将数组转换为对象
        $ugSet=collection($ugSet);
      }
                 
      $this->assign([
        #返回前端必须为对象类型:$ugSet
        'ugSet'=>$ugSet,
        'oprt'=>$oprt
      ]);
      
      return view();
    }
     // 输出系统设置模板
    public function sysSetting(Request $request,DeptModel $deptMdl)
    {
      $this->_loginUser();
      
      $this->assign([
              'home'=>$request->domain(),
              'depts'=>$deptMdl::where('id','>',0)->order('name Asc')->select(),
              'numDept'=>$deptMdl::where('id','>',0)->count()
        ]);
      return view();
    }
    
     // 用户CDUR，接收客户端通过Ajax，post来的参数，返回json数据
    public function userOprt(Request $request,UserModel $userMdl,$oprt='_CREATE',$id='0')
    {
      $this->_loginUser();
      
      $oprt=$request->param('oprt');
      $id=!empty($request->param('id'))?$request->param('id'):0;
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
          //因对‘mobile’字段定义了获取器，调用getData()方法得到数据库的原始数据
          $mobile=$userMdl::get($id)->getData('mobile');
          
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
          if($oprt=='_EDIT'){
            //因对‘mobile’字段定义了获取器，调用getData()方法得到数据库的原始数据
            $mobile=$userMdl::get($id)->getData('mobile');
          
          }else{
            $mobile=0;
          }
          
          $this->assign([
               'home'=>$request->domain(),
               'user'=>$user,
               'oprt'=>$oprt,
               'userMobile'=>$mobile
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
      $issTemp=array();
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
        
        if(count($request->param('authAss/a'))){
          foreach($request->param('authAss/a') as $v){
            $ass[$v]=1;
          }
        }else{
          $ass=array();
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
        $ass=array_merge(_commonModuleAuth('_ASS'),$ass);
           
        //组装数据
        $authority=array("iss"=>$iss,
                            "att"=>$att,
                            "pat"=>$pat,
                            "pro"=>$pro,
                            "the"=>$the,
                            "admin"=>$admin,
                            "ass"=>$ass,
                            );
        $usergroupData=array('name'=>$request->param('usergroupName'),
                                'enable'=>$request->param('usergroupEn'),
                                'authority'=>$authority,
                                'description'=>!empty($request->param('usergroupDescription'))?$request->param('usergroupDescription'):'无');
      }
      //2. 分情况操作数据库
      switch($oprt){
        case '_ADDNEW':
          $usergroup=array('id'=>$id,'name'=>'','authority'=>_commonModuleAuth(),'description'=>'无');
          //将iss权限数组的$key转为中文_commonAuthArrKeyToCHN
          //$usergroup['authority']['iss']=_commonAuthArrKeyToCHN($usergroup['authority']['iss']);
//          $usergroup['authority']['pat']=_commonAuthArrKeyToCHN($usergroup['authority']['pat']);
//          $usergroup['authority']['pro']=_commonAuthArrKeyToCHN($usergroup['authority']['pro']);
//          $usergroup['authority']['the']=_commonAuthArrKeyToCHN($usergroup['authority']['the']);
//          $usergroup['authority']['att']=_commonAuthArrKeyToCHN($usergroup['authority']['att']);     
        break;
        
        case '_EDIT':
          $usergroup=$usergroupMdl::get($id);
        break;
        
        case '_CREATE':
          $usergroup=$usergroupMdl::get(['name'=>$request->param('usergroupName')]);
          $name=$request->param('usergroupName');
          $result='success';
          $id=!empty($usergroup->id)?$usergroup->id:0;
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
             'usergroup'=>$usergroup,
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
    
    // 部门组CDUR，接收客户端通过Ajax，post来的参数，返回json数据
    //前端传来的oprt值:_CREATE、_UPDATE、_ADDNEW、_EDIT、_DISABLE、_ENABLE、_DELETE
    public function deptOprt(Request $request,DeptModel $deptMdl,$oprt='_CREATE',$id='0')
    {
      $this->_loginUser();
    
      $oprt=$request->param('oprt');
      $id=$request->param('id');
      $result='';
      $msg='';
      $msgPatch='';
      //1.分情况变量赋值
      if($oprt=='_CREATE' || $oprt=='_UPDATE') {
        $deptData=array('name'=>$request->param('deptName'),
                        'abbr'=>$request->param('deptAbbr'),
                        'enable'=>$request->param('deptEn'),
                        );
      }
      //2. 分情况操作数据库
      switch($oprt){
        case '_ADDNEW':
          $dept=array('id'=>0,'name'=>'','abbr'=>'','enable'=>1);
        break;
        
        case '_EDIT':
          $dept=$deptMdl::get($id);
        break;
        
        case '_CREATE':
          //$dept=$deptMdl::get(['name'=>$request->param('deptName')]);
          $dept=$deptMdl::where('name',$request->param('deptName'))->select();
          $name=$request->param('deptName');
          if(count($dept)){
            $result='fail';
            $msg='创建失败';
            $msgPatch='部门【'.$request->param('deptName').'】已存在。';
          }else{
            //$request->param('deptAbbr')已存在
            if($deptMdl::where('abbr',$request->param('deptAbbr'))->count()){
              $result='fail';
              $msg='创建失败';
              $msgPatch='部门简称【'.$request->param('deptAbbr').'】已存在。';
            }else{
              $result='success';
              $dept=$deptMdl::create($deptData,true);
              $msg='创建成功';
              $id=$dept->id;
            }           
          }
        break;
        
        case '_UPDATE': 
        //写入数据库的deptName和deptAbbr都要保证在字段中的唯一性。
          $deptDb=$deptMdl::get($id);
          $getName=$deptMdl::get(['name'=>$request->param('deptName')]);//是否empty
          $getAbbr=$deptMdl::get(['abbr'=>$request->param('deptAbbr')]);//是否empty
          $name=$deptDb->name;
          if(count($getName)==0 && count($getAbbr)==0){
            $result='success';
            $msgPatch='部门【'.$name.'】修改为：【'.$request->param('deptName').'】<br>
                        部门简称【'.$deptDb->abbr.'】修改为：【'.$request->param('deptAbbr').'】';
          }elseif(count($getName)==0 && count($getAbbr)==1){
            if($getAbbr->id == $id){
              $result='success';
              $msgPatch='部门【'.$name.'】修改为：【'.$request->param('deptName').'】';
            }else{
              $result='fail';
              $msgPatch='部门简称【'.$request->param('deptAbbr').'】已存在';
            }
          }elseif(count($getName)==1 && $getName->id==$id){
            if(count($getAbbr)){
              if($getAbbr->id!=$id){
                $result='fail';
                $msgPatch='部门简称【'.$getAbbr->abbr.'】已存在';
              }else{
                $result='remain';
                $msgPatch='部门及部门简称没有变化';
              }
            }else{
              $result='success';
              $msgPatch='部门简称【'.$deptDb->abbr.'】修改为：【'.$request->param('deptAbbr').'】';
            }
          }elseif(count($getName)==1 && $getName->id!=$id){
            $result='fail';
            $msgPatch='部门【'.$getName->name.'】已存在';
          }
          switch($result){
            case'success':
              $msg='修改成功';
              $deptMdl::update($deptData,['id' => $id],true);
            break;
            case'fail':
              $msg='修改失败';
            break;
            case'remain':
              $msg='无修改';
            break; 
          }
        break;
        
        case '_DELETE':
          $name=$deptMdl::get($id)->name;  
          $deptMdl::destroy($id);
          $result='success';
          $msg='删除成功';
          //返回默认的$id
          $id=$deptMdl::where('id','>',0)->min('id');
          
        break;
        
        case'_DISABLE':
          $deptMdl::update(array('enable'=> 0), ['id' => $id]);
        break;
        
        case'_ENABLE':
          $deptMdl::update(array('enable'=> 1), ['id' => $id]); 
        break;
      }
     //3.分情况返回前端数据
     if ($oprt=='_ADDNEW' || $oprt=='_EDIT'){
             
        $this->assign([
             'home'=>$request->domain(),
             'dept'=>$dept,
             'oprt'=>$oprt,
        ]);
        // 返回前端模板文件
        return view('editDept');
     }elseif($oprt=='_CREATE' || $oprt=='_UPDATE' || $oprt=='_DELETE'){
        // 返回前端JSON数据 
        return ['result'=>$result,'id'=>$id,'name'=>$name,'msg'=>$msg,'msgPatch'=>$msgPatch];
     }elseif($oprt=='_DISABLE' || $oprt=='_ENABLE'){
        // 返回前端JSON数据 
        return ['enable'=>$deptMdl::get($id)->enable,'id'=>$id];
     }
      
    }
    
    
    
    
    
    
}

