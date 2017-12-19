<?php
namespace app\user\controller;

use think\Request;
use think\Session;

use app\user\model\User as UserModel;
use app\user\model\Rolety as RoletyModel;
use app\issue\model\Issinfo as IssinfoModel;
use app\patent\model\Patinfo as PatinfoModel;
use app\attachment\model\Attinfo as AttinfoModel;

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
    
     // 输出patiss模板显示issue中与pat相关的数据集。数据集记录，根据role的不同，选择对应iss的“status”来构成。
    public function patIss(Request $request)
    {
       $this->_loginUser();
       
      // $returnType接收前端页面传来的returnType值，‘0’为模板文件，‘1’为数据
      if(!empty($request->param('returnType'))){
        $returnType=$request->param('returnType');
      }else{
        $returnType=0;
      }
      
      // $role接收前端页面传来的role值
      if(!empty($request->param('role'))){
        $role=$request->param('role');
      }else{
        $role=$this->roles[0];
      }
      
      // $issStatus接收前端页面传来的issStatus值
      if(!empty($request->param('issStatus'))){
        $issStatus=$request->param('issStatus');
      }else{
        $issStatus='_TODO';
      }
      
      // 忽略前端页面传来的issType值，直接赋值为'_PATENT'
      $issType='_PATENT';
      
      //$totalTableRows接收前端页面传来的分页时每页显示的记录数，默认为10
        if(!empty($request->param('patIssTableRows'))){
          $patIssTableRows=$request->param('patIssTableRows');
        }else{
          $patIssTableRows=10;
        }
        
         // 接收前端分页页数变量：“pageUserNum”
        if(!empty($request->param('pageTotalNum'))){
          $pageTotalNum=$request->param('pageTotalNum');
        }else{
          $pageTotalNum=1;
        }
        
        // $sortName接收前端页面传来的排序字段名
        if(!empty($request->param('sortName'))){
          $sortName=$request->param('sortName');
        }else{
          $sortName='_PATNAME';
        }
        
        // $sort接收前端页面传来的排序顺序
        if(!empty($request->param('sort'))){
          $sort=$request->param('sort');
        }else{
          $sort='_ASC';
        }
        
         // 查询词1，'searchPatName'
        if(!empty($request->param('searchPatName'))){
          $searchPatName=$request->param('searchPatName');
        }else{
          $searchPatName='';
        } 
        
        // 查询词2，'searchDept'
        if(!empty($request->param('searchDept'))){
          $searchDept=$request->param('searchDept');
        }else{
          $searchDept=0;
        } 
        
        // 查询词3，'searchPatStatus'
        if(!empty($request->param('searchPatStatus'))){
          $searchPatStatus=$request->param('searchPatStatus');
        }else{
          $searchPatStatus=0;
        }
        
        // 查询词4，'searchPatType'
        if(!empty($request->param('searchPatType'))){
          $searchPatType=$request->param('searchPatType');
        }else{
          $searchPatType=0;
        } 
        
        // 查询词5，'searchWriter'
        if(!empty($request->param('searchWriter'))){
          $searchWriter=$request->param('searchWriter');
        }else{
          $searchWriter='';
        }  
      
      // 选择排序字段
      switch($sortName){
        //case '_TOPIC':
//          $strOrder='topic';
//        break;
            
        case '_ABSTRACT':
          $strOrder='abstract';
        break;
        
        case '_WRITER':
          $strOrder='writer';
        break;
        
        case '_EXECUTER':
          $strOrder='executer';
        break;
            
        case '_SUBMITDATE':
          $strOrder='submitdate';
        break;
        
        case '_STATUS':
          $strOrder='status';
        break;
            
        case '_DEPT':
          $strOrder='dept';
        break;
            
        //默认按字段“topic”
        default:
          $strOrder='topic';  
          $sortName="_TOPIC";
        break;
      } 
      
      //  组合升序or降序查询
      if($sort=="_ASC"){
          $strOrder=$strOrder.' asc';
      }else{
          $strOrder=$strOrder.' desc';
          
      }
      
      // 组合状态查询条件，根据role值和issStatus不同，查询的patIss的“status”值不同
      switch($role){            
        case'reviewer':
           // reviewer只能审查本部门的iss
          $map['dept'] =$this->dept;
          switch($issStatus){
            case '_INPROCESS':
              $map['status'] =['in',['不予推荐','审核通过','返回修改']];
            break;
           
            case '_DONE':
              $map['status'] =['in',['准予申报','否决','修改完善']];
            break;
            
            case '_OPERATE':
              $map['status'] =['in',['申报执行','申报复核','申报修改','申报提交','授权','驳回']];
            break;
                
            //默认'_TODO':
            default:
              $map['status'] ='拟申报';
            break;
          }  
        break;
        
        // approver要处理专利授权，专利续费2类事务    
        case'approver':
          switch($issStatus){
            case '_DONE':
              $map['status'] =['in',['准予申报','否决','修改完善','准予续费','放弃']];
            break;
            
            case '_OPERATE':
              $map['status'] =['in',['申报执行','申报复核','申报修改','申报提交','授权','驳回','续费提交','续费授权']];
            break;
                
            //默认'_TODO':
            default:
              $map['status'] =['in',['审核通过','不予推荐','拟续费']];
            break;
          }  
        break;
            
        case'operator':
          $map['executer'] =$this->username;
          switch($issStatus){            
            case '_OPERATE_INPROCESS':
              $map['status'] =['in',['申报执行','申报修改']];
            break;
            
            case '_OPERATE_DONE':
              $map['status'] ='申报复核';
            break;
                
            //默认'_OPERATE_TODO':
            default:
              $map['status'] ='准予申报';
            break;
          }  
        break;
        
        // maintainer要处理专利授权，专利续费2类事务    
        case'maintainer':
          switch($issStatus){
            case '_OPERATE_INPROCESS':
              $map['status'] =['in',['申报提交','续费提交','拟续费']];
            break;
           
            case '_OPERATE_DONE':
              $map['status'] =['in',['申报修改','授权','驳回','续费授权','放弃']];
            break;
                
            //默认'_TODO'，对到期时间在半年内的“授权”或“续费授权”的专利，系统自动放入_TODO里！！考虑代码实现？？
            default:
              $map['status'] =['in',['申报执行','申报复核','准予续费']];
            break;
          }     
        break;
        
        // 默认为writer
        default:
          // writer只能查看自己撰写的iss
            $map['writer'] =$this->username;
          switch($issStatus){
            
            case '_INPROCESS':
              $map['status'] =['in',['拟申报','审核通过','不予推荐']];
              
            break;
           
            case '_DONE':
              $map['status'] =['in',['准予申报','否决']];
             
            break;
            
            case '_OPERATE':
              $map['status'] =['in',['申报执行','申报复核','申报修改','申报提交','授权','驳回']];
              
            break;
                
            //默认'_TODO':
            default:
              $map['status'] =['in',['返回修改','填报','修改完善']];
              
            break;
          }  
        break;
            
      }
      
      //使用模型Patinfo
      $pats = new IssinfoModel;
      
      // 查出所有的用户并分页，根据“strOrder”排序，前端页面显示的锚点（hash值）为$fragment，设定分页页数变量：“pageTotalNum”
      // 带上每页显示记录行数$totalTableRows，实现查询结果分页显示。
      $patIssTotal = $pats->where('id','>',0)
                        ->where($map)
                        ->order($strOrder)
                        ->paginate($patIssTableRows,false,['type'=>'bootstrap','var_page' => 'pageTotalNum',
                        'query'=>['patIssTableRows'=>$patIssTableRows]]);
                        
      // 获取分页显示
      $pageTotal = $patIssTotal->render();
      // 记录总数
      $numTotal = $pats->where('id','>',0)->where($map)->count();
      
      //返回数据还是模板文件,‘0’为模板文件，‘1’为数据
      if($returnType){
        return ($numTotal);
      }else{
        $this->assign([
              'home'=>$request->domain(),
              
              // 分页显示所需参数
              'patIssTotal'=>$patIssTotal,
              'numTotal'=>$numTotal,
              'pageTotal'=>$pageTotal,
              'patIssTableRows'=>$patIssTableRows,
              'pageTotalNum'=>$pageTotalNum,
              
              // 表格搜索字段
              'searchPatName'=>$searchPatName,
              'searchDept'=>$searchDept,
              'searchPatStatus'=>$searchPatStatus,
              'searchPatType'=>$searchPatType,
              'searchWriter'=>$searchWriter,
              
    
              
              // 表格排序信息
              'sortName'=>$sortName,
              'sort'=>$sort,
              //'patIssTableRows'=>$patIssTableRows,
              
              // 所return的页面显示的iss状态值$issStatus
              'issStatus'=>$issStatus,
              
              // 返回前端role值
              'role'=>$role,
              
        ]);
      
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p>'.$role.$issType.$issStatus.'</div> ';
        // return '<div style="padding: 24px 48px;">模块开发中……'.$role.$issType.$issStatus.'</div>';
        return view();
        
      }
    
    }
    
    // 各个role的patIss的增删改查操作
    public function patIssOprt(Request $request)
    {
      $this->_loginUser();
      
      // $role接收前端页面传来的role值
      if(!empty($request->param('role'))){
        $role=$request->param('role');
      }else{
        $role=0;
      }

      // $oprt接收前端页面传来的oprt值
      if(!empty($request->param('oprt'))){
        $oprt=$request->param('oprt');
      }else{
        $oprt=0;
      }
      
      // $returnType接收前端页面传来的returnType值，‘0’为模板文件，‘1’为数据
      if(!empty($request->param('returnType'))){
        $returnType=$request->param('returnType');
      }else{
        $returnType=0;
      }
      
      // $patIssId接收前端页面传来的patIssId值
      if(!empty($request->param('patIssId'))){
        $patIssId=$request->param('patIssId');
      }else{
        $patIssId=0;
      }
      
      // 获取表单上传附件文件
     // if(!empty($request->file('att'))){
//        $att=$request->file('att');
//      }else{
//        $att=0;
//      }
      
      //返回数据还是模板文件,‘0’为模板文件，‘1’为数据
      if($returnType){
        //return json(array('role'=>$role,'oprt'=>$oprt));
        $data=array('result'=>'success','msg'=>'专利事务保存成功！');
        return json($data);
        //return ($returnType);
      }else{
        // 按照role/oprt值的不同，渲染不同的模板文件并对数据库进行不同的操作
        switch($role){
          
          case "writer":
              switch($oprt){
                // writer 删除专利事务
                case "delete":
                  
                break;
                
                case "save":
                
                //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div> ';
                
                return ('success');  
                break;
                
                case "submit":
                  
                break;
                
                // 默认为‘addNew’
                default:
                  // 向issinfo表写入信息
                  
                  
                  // 向patinfo表写入信息
                  
                  
                  // 向attinfo表写入信息
                  
                  
                  // 存储上传的附件
                  
                  
                break;
                
              }
              
              
          break;
          
          case "reviewer":
          
          break;
          
          case "approver":
          
          break;
          
          case "operator":
          
          break;
          
          case "maintainer":
          
          break;
          
          default:
          
          break;
          
        }
        
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div> ';
        // 输出对应的模板文件
        //return view($role.'_'.$oprt);
      }
        
    }  
    
    // 各个role的patIss的增删改查模板文件
    public function patIssTpl(Request $request)
    {
      $this->_loginUser();
      
      // $role接收前端页面传来的role值
      if(!empty($request->param('role'))){
        $role=$request->param('role');
      }else{
        $role=0;
      }

      // $tpl接收前端页面传来的tpl值
      if(!empty($request->param('tpl'))){
        $tpl=$request->param('tpl');
      }else{
        $tpl=0;
      }
      
      // $returnType接收前端页面传来的returnType值，‘0’为模板文件，‘1’为数据
      if(!empty($request->param('returnType'))){
        $returnType=$request->param('returnType');
      }else{
        $returnType=0;
      }
      
      // $patIssId接收前端页面传来的patIssId值
      if(!empty($request->param('patIssId'))){
        $patIssId=$request->param('patIssId');
      }else{
        $patIssId=0;
      }
      
      //返回数据还是模板文件,‘0’为模板文件，‘1’为数据
      if($returnType){
        //return json(array('role'=>$role,'tpl'=>$tpl));
        return $tpl;
      }else{
        // 按照role/tpl值的不同，渲染不同的模板文件并对数据库进行不同的操作
        switch($role){
          
          case "writer":
              switch($tpl){
                // writer 新增专利申报事务
                case "addNew":
                
                  $this->assign([
                    'home'=>$request->domain(),

                    // 
                    'writer'=>$this->username,
                    'dept'=>$this->dept,
              
                  ]);
                break;
                
                case "edit":
                  // 查出所编辑的issue的数据：
                  $iss= IssinfoModel::get($patIssId);
                  
                  // 查出issue所对应的patent
                  $pat= PatinfoModel::get($iss->num_id);
                  
                  // 查出issue所对应的attachment
                  $attSet= AttinfoModel::where('num_type','_ATTT1')->where('num_id',$patIssId)->select();
                  
                  $this->assign([
                    'home'=>$request->domain(),

                    // 
                    'writer'=>$this->username,
                    'dept'=>$this->dept,
                    //'issStatus'=>$iss->status,
                    'iss'=>$iss,
                    'pat'=>$pat,
                    'attSet'=>$attSet,
              
                  ]);
                break;
                
                default:
                
                break;
                
              }
          break;
          
          case "reviewer":
          
          break;
          
          case "approver":
          
          break;
          
          case "operator":
          
          break;
          
          case "maintainer":
          
          break;
          
          default:
          
          break;
          
        }
        
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div> ';
        // 输出对应的模板文件
        return view($role.'_'.$tpl);
      }
        
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
      
      // $issStatus接收前端页面传来的issStatus值
      if(!empty($request->param('issStatus'))){
        $issStatus=$request->param('issStatus');
      }else{
          $issStatus='_TODO';
      }
      
      // 忽略前端页面传来的issType值，直接赋值为'_THESIS'
      $issType='_THESIS';
      
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
      
      // $issStatus接收前端页面传来的issStatus值
      if(!empty($request->param('issStatus'))){
        $issStatus=$request->param('issStatus');
      }else{
          $issStatus='_TODO';
      }
      
      // 忽略前端页面传来的issType值，直接赋值为'_PROJECT'
      $issType='_PROJECT';
      
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
