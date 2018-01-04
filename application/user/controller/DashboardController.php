<?php
namespace app\user\controller;

use think\Request;
use think\Session;
use think\View;

use app\user\model\User as UserModel;
use app\user\model\Rolety as RoletyModel;
use app\user\model\Issinfo as IssinfoModel;
use app\user\model\Issrecord as IssrecordModel;
use app\user\model\Patinfo as PatinfoModel;
use app\user\model\Patrecord as PatrecordModel;
use app\user\model\Attinfo as AttinfoModel;

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
      
      // $issType接收前端页面传来的issType值
      //if(!empty($request->param('issType'))){
//        $issType=$request->param('issType');
//      }else{
//          $issType='_PATENT';
//      }
      
      $destr= "请求方法:".$request->method()."</br>";
      
      $this->assign([
        //在usercenter.html页面输出自定义的信息
        //在index.html页面通过destr输出自定义的信息
        'destr'=>$destr."</br>",
        
        'home'=>$request->domain(),
        
        'role'=>$role,
        ]);
      //return $this->fetch()与view()，有区别？？view()能正常显示html文件内容，$this->fetch()显示的是添加转义字符后的html文件。？？
      //return $this->fetch('dashboard'.DS.'pat'.DS.$role);
      //return view('dashboard'.DS.'pat'.DS.$role);
      return view($role);
    }
    
     // 输出patiss模板显示issue中与pat相关的数据集。数据集记录，根据role的不同，选择对应iss的“status”来构成。
    public function patIss(Request $request,View $view)
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
      
      // $issStatus接收前端页面传来的issStatus值
      if(!empty($request->param('patIssId'))){
        $patIssId=$request->param('patIssId');
      }else{
        $patIssId=0;
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
          $sortName='_TOPIC';
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
        case '_PATNAME':
          $strOrder='abstract';
        break;
        
        case '_PATSTATUS':
          $strOrder='abstract';
        break;
            
        case '_ABSTRACT':
          $strOrder='abstract';
        break;
        
        case '_WRITER':
          $strOrder='writer';
        break;
        
        case '_EXECUTER':
          $strOrder='executer';
        break;
            
        case '_ADDNEWDATE':
          $strOrder='addnewdate';
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
              $map['status'] =['in',['审核未通过','审核通过']];
            break;
           
            case '_DONE':
              $map['status'] =['in',['准予申报','否决','修改完善','返回修改','批准']];
            break;
            
            case '_OPERATE':
              $map['status'] =['in',['申报执行','申报复核','申报修改','申报提交','授权','驳回']];
            break;
                
            //默认'_TODO':
            default:
              $map['status'] ='待审核';
            break;
          }  
        break;
        
        // approver要处理专利授权，专利续费2类事务    
        case'approver':
          switch($issStatus){
            case '_DONE':
              $map['status'] =['in',['准予申报','否决','修改完善','准予续费','放弃','批准']];
            break;
            
            case '_OPERATE':
              $map['status'] =['in',['申报执行','申报复核','申报修改','申报提交','授权','驳回','续费提交','续费授权']];
            break;
                
            //默认'_TODO':
            default:
              $map['status'] =['in',['审核通过','审核未通过','拟续费']];
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
              $map['status'] =['in',['准予申报','批准']];
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
              $map['status'] =['in',['待审核','审核通过','不予推荐']];
              
            break;
           
            case '_DONE':
              $map['status'] =['in',['准予申报','否决','批准']];
             
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
      
      //使用模型Issinfo
      $issSet = new IssinfoModel;
      
      // 查出所有的用户并分页，根据“strOrder”排序，前端页面显示的锚点（hash值）为$fragment，设定分页页数变量：“pageTotalNum”
      // 带上每页显示记录行数$totalTableRows，实现查询结果分页显示。
      $patIssTotal = $issSet->where('id','>',0)
                        //->issmap
                        ->where($map)
                        ->order($strOrder)
                        ->paginate($patIssTableRows,false,['type'=>'bootstrap','var_page' => 'pageTotalNum',
                        'query'=>['patIssTableRows'=>$patIssTableRows]]);

      //foreach ($patIssTotal as $obj) {
//          $issmap=$obj->issmap;
//          dump($issmap->topic.$issmap->status);
//      }
                        
      // 获取分页显示
      $pageTotal = $patIssTotal->render();
      // 记录总数
      $numTotal = $issSet->where('id','>',0)->where($map)->count();
      
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
              
              // 所return的页面，某个button的data-patIssId的值为patIssId
              'patIssId'=>$patIssId,
              
              // 返回前端role值
              'role'=>$role,
              
        ]);
      
        
        //return $this->fetch('dashboard'.DS.'pat'.DS.'patIss');
        //return $this->display('dashboard'.DS.'pat'.DS.'patIss.html');
        //return $this->fetch();
        //指定模板文件view/dashboard/patiss/patiss.html
        return view('dashboard'.DS.'patiss'.DS.'patIss');
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
      }else if(!empty($request->request('oprt'))){
        $oprt=$request->request('oprt');
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
      
      // $attUpload接收前端页面传来的attUpload值，值为1表示后续有上传文件，为0表示后续无上传文件
      if(!empty($request->param('attUpload'))){
        $attUpload=$request->param('attUpload');
      }else{
        $attUpload=0;
      }
      
      // 获取表单上传附件文件
      if(!empty($request->file('att'))){
        $att=$request->file('att');
      }else{
        $att=0;
      }
      
      //$returnType控制返回前端的数据，便于调试。取值就是0或1
      if($returnType){
        //return json(array('role'=>$role,'oprt'=>$oprt));
        $data=array('result'=>'success','msg'=>'专利事务保存成功');
        return json($data);
      }else{
        // 按照role/oprt值的不同，渲染不同的模板文件并对数据库进行不同的操作
        $today=date('Y-m-d H:i:s');
        $result='';
        $msg='';
        switch($role){
          
          case 'writer':
              switch($oprt){
                // writer 删除专利事务
                case 'delete':
                
                  //多数据表（issinfo/attinfo/patinfo）删除，定义模型关联后，对主表进行删除时系统自动应用事务进行。
                  //需注意MySQL 的 MyISAM 不支持事务处理，需要使用 InnoDB 引擎。？？
                  
                  $issSet=IssinfoModel::get($request->request('issId'));
                  $numId=$issSet->issnum;
                  
                  // 删除step1，如果存在附件则删除由$dirName指定的目录及其文件，否则跳过这一步。
                  if(count(AttinfoModel::where('num_id',$numId)->select())){
                    $dirName=ROOT_PATH.DS.'uploads'.DS.$numId;
                    if(is_dir($dirName)){
                      $result=$this->_deleteDirs($dirName);
                      $msg='附件删除。<br>';
                    }else{
                      $result="error";
                      // "text-danger"为前端已定义好的css标签
                      $msg='事务删除失败。<br><span class="text-danger">附件文件不存在。</span>';
                      // 中断删除操作，向前端反馈信息。
                      $data=array('result'=>$result,'msg'=>$msg,'patIssId'=>$request->request('issId'));
                      return json($data);  
                    }
                  }else{
                    $result="success";
                  }
                  
                  
                  // 删除step2，指定条件删除数据，向attinfo表删除
                  AttinfoModel::destroy([
                    'num_id' => $numId,
                  ]);
                                    
                  // 删除step3，使用静态方法，向patinfo表删除
                  PatinfoModel::destroy($request->request('patId'));
                  
                 
                  // 删除step4，使用静态方法，向issinfo表删除
                  IssinfoModel::destroy($request->request('issId'));
                 
                  if('success'==$result){
                    $msg.='专利事务删除成功。<br>';
                  }else{
                    $msg='专利事务删除出错。<br>';
                  }
                break;
                
                // writer 更新专利事务
                case 'update':
                  
                  // 使用静态方法，向patinfo表更新信息，赋值有变化就会更新和返回对象，无变化则无更新和对象返回。
                  $patSet = PatinfoModel::update([
                      'topic'  => $request->request('patTopic'),
                      'status' => '内审',
                      'pattype'  => $request->request('patType'),
                      'addnewdate'=> $today,
                      'author' => $request->request('username'),
                      'dept' => $request->request('dept'),
                  ], ['id' => $request->request('patId')]);
                  $result='success';
                  $msg='专利信息已更新。<br>';
                  
                  // 使用静态方法，向issinfo表更新信息，赋值有变化就会更新和返回对象，无变化则无更新和对象返回。
                  $issSet = IssinfoModel::update([
                      'topic'  => $request->request('topic'),
                     // 'status' => '填报',
                      'issmap_type'=>$request->request('issType'),
                      'abstract'=>$request->request('abstract'),
                      'addnewdate'=> $today,
                      'writer' => $request->request('username'),
                      'dept' => $request->request('dept'),
                  ], ['id' => $request->request('issId')]);
          
                  $result='success';
                  $msg.='事务信息已更新。<br>';
                  
                  $issId=$request->request('issId');
                  // 设置att的num_id字段值
                  $issSet=IssinfoModel::get($issId);
                  $numId=$issSet->issnum;
                  
                  // $issSet->status=='填报'时，使用静态方法，向issrecord/patrecord表更新acttime。
                  if($issSet->status=='填报'){
                    IssrecordModel::update([
                    'acttime'=>$today,
                    ],['num' => $numId]);
                    
                    PatrecordModel::update([
                    'acttime'=>$today,
                    ],['patinfo_id' => $request->request('patId')]);
                  }
                  // 设置att的deldisplay的字段值。用于前端页面判断是否显示“删除”按钮。0为不显示。
                  $deldisplay=1;
                 
                
                break;
                
                case 'submit':
                // 使用静态方法，向patinfo表更新信息，赋值有变化就会更新和返回对象，无变化则无更新和对象返回。
                  PatinfoModel::update([
                      'topic'  => $request->request('patTopic'),
                      'status' => '内审',
                      'pattype'  => $request->request('patType'),
                      'addnewdate'=> $today,
                      'author' => $request->request('username'),
                      'dept' => $request->request('dept'),
                  ], ['id' => $request->request('patId')]);
                  $result='success';
                  $msg='专利信息已更新。<br>';
                  
                  // 使用静态方法，向issinfo表更新信息，赋值有变化就会更新和返回对象，无变化则无更新和对象返回。
                  IssinfoModel::update([
                      'topic'  => $request->request('topic'),
                      'status' => '待审核',
                      'issmap_type'=>$request->request('issType'),
                      'abstract'=>$request->request('abstract'),
                      'submitdate'=> $today,
                      'writer' => $request->request('username'),
                      'dept' => $request->request('dept'),
                  ], ['id' => $request->request('issId')]);
                  $result='success';
                  $msg.='事务信息已更新。<br>';
                  
                  $issId=$request->request('issId');
                  $issSet = IssinfoModel::get($issId);
                  // 设置att的num_id字段值
                  $numId=$issSet->issnum;
                  
                  // 使用静态方法，向issrecord表新增信息。
                  $issRecordSet=IssrecordModel::create([
                    'num'=>$numId,
                    'act'=>'提交',
                    'actdetail'=>'专利事务"'.$request->request('topic').'"提交审核',
                    'acttime'=>$today,
                    'username'=>$request->param('username'),
                    'rolename'=>$role,
                    'issinfo_id'=>$issId,
                  ]);
                  //静态方法创建新对象后，返回对象id
                  $issRecordId= $issRecordSet->id;
                  
                  // 设置att的deldisplay字段值。用于新附件上传后，前端页面判断是否显示“删除”按钮。0为不显示。
                  $deldisplay=0;
                  
                  //设置所有已上传附件的deldisplay字段值为0
                  AttinfoModel::update([
                    'deldisplay'=>0
                  ],['num_id'=>$numId]);
                  
                  $result='success';
                  $msg.='专利事务<strong>"'.$request->request('topic').'"</strong>已提交。请留意后续审批结果。<br>';
                break;
                
                // 默认为‘addNew’
                default:
                  // 因为专利id与事务id是1对多的关系，所以先写入patinfo表
                  
                  // 使用静态方法，向patinfo表写入新pat信息
                  $patSet = PatinfoModel::create([
                    'topic'  => $request->param('patTopic'),
                    'status' => '内审',
                    'pattype'  => $request->param('patType'),
                    'addnewdate'=> $today,
                    'author' => $request->param('username'),
                    'dept' => $request->param('dept'),
                  ]);
                  //静态方法创建新对象后，返回对象id
                  $patId= $patSet->id;
                  
                  // 使用静态方法，向patrecord表新增信息。
                  $patRecordSet=PatrecordModel::create([
                    'num'=>$patSet->patnum,
                    'act'=>'填报',
                    'actdetail'=>'专利"'.$request->param('patTopic').'"填报',
                    'acttime'=>$today,
                    'username'=>$request->param('username'),
                    'rolename'=>$role,
                    'patinfo_id'=>$patId,
                  ]);
                  //静态方法创建新对象后，返回对象id
                  $patRecordId= $patRecordSet->id;
                  
                  // 使用静态方法，向issinfo表写入新iss信息
                  $issSet = IssinfoModel::create([
                    'topic'  => $request->param('topic'),
                    'status' => '填报',
                    'issmap_type'=>$request->param('issType'),
                    'issmap_id'=>$patId,
                    // 兼容之前的代码
                    'num_id'=>$patId,
                    'abstract'=>$request->param('abstract'),
                    'addnewdate'=> $today,
                    'writer' => $request->param('username'),
                    'dept' => $request->param('dept'),
                  ]);
                  //静态方法创建新对象后，取得返回对象的id
                  $issId= $issSet->id;
                  
                  // 使用静态方法，向issrecord表新增信息。
                  $issRecordSet=IssrecordModel::create([
                    'num'=>$issSet->issnum,
                    'act'=>'填报',
                    'actdetail'=>'专利事务"'.$request->request('topic').'"填报',
                    'acttime'=>$today,
                    'username'=>$request->param('username'),
                    'rolename'=>$role,
                    'issinfo_id'=>$issId,
                  ]);
                  //静态方法创建新对象后，返回对象id
                  $issRecordId= $issRecordSet->id;
                  
                  // 设置att的deldisplay字段值。用于前端页面判断是否显示“删除”按钮。0为不显示。
                  $deldisplay=1;
                  // 设置att的num_id字段值
                  $numId=$issSet->issnum;
                  
                  $result='success';
                  $msg.='专利事务<strong>"'.$request->request('topic').'"</strong>已填报。<br>请尽快提交审核。<br>';
                break;
                
              }
              
              // 存储上传的附件    
              if($attUpload){
                // 使用静态方法，向attinfo表写入新att信息，要用到$issId，因为attId与issId是多对一的关系
                $attSet = AttinfoModel::create([
                'num_id'  =>$numId,
                'name'  => $request->param('attName'),
                'atttype' => $request->param('attType'),
                'attmap_type' => $request->param('attObj'),
                'attmap_id' => $issId,
                'uploaddate'=> $today,
                'uploader'=> $request->param('username'),
                'rolename'=> $role,
                'deldisplay'=>$deldisplay,
                ]);
                //静态方法创建新对象后，返回对象id
                $attId= $attSet->id;
                
                //完成附件上传
                $result=$this->_uploadAtt($att,$attSet->num_id,$attSet->id);
                
                if($result=='success'){
                  $msg.='附件上传成功。<br>';
                }else{
                  $msg.=$result;
                }
                    
              }else{
              $result='success';
              $msg.='无附件操作。<br>';
              }
              
          break;
          
          case 'reviewer':
            switch($oprt){
              case 'fail':
                // 使用静态方法，向issinfo表更新信息，赋值有变化就会更新和返回对象，无变化则无更新和对象返回。
                  IssinfoModel::update([
                      'status' => '审核未通过',
                      'auditrejectdate'=> $today,
                  ], ['id' => $request->request('issId')]);
                  $result='success';
                  $msg.='审核结果：<strong class="text-warning">审核未通过</strong>。<br><span class="text-info">审核意见：'.$request->request('auditMsg').'</span><br>';
                  
                  $issId=$request->request('issId');
                  $issSet = IssinfoModel::get($issId);
                  //
                  $numId=$issSet->issnum;
                  
                  // 使用静态方法，向issrecord表新增信息。
                  $issRecordSet=IssrecordModel::create([
                    'num'=>$numId,
                    'act'=>'审核',
                    'actdetail'=>'专利事务"'.$issSet->topic.'"审核未通过。<br><span class="text-info">审核意见：'.$request->request('auditMsg').'</span><br>',
                    'acttime'=>$today,
                    'username'=>$request->param('username'),
                    'rolename'=>$role,
                    'issinfo_id'=>$issId,
                  ]);
                  //静态方法创建新对象后，返回对象id
                  $issRecordId= $issRecordSet->id;
                  
                  $result='success';
                  $msg.='专利事务<strong>"'.$issSet->topic.'"</strong>审核未通过。需留意后续审批结果。<br>';
              break;
              
              case 'modify':
                // 使用静态方法，向issinfo表更新信息，赋值有变化就会更新和返回对象，无变化则无更新和对象返回。
                  IssinfoModel::update([
                      'status' => '返回修改',
                      'auditrejectdate'=> $today,
                  ], ['id' => $request->request('issId')]);
                  $result='success';
                  $msg.='审核结果：<strong class="text-warning">返回修改</strong>。<br><span class="text-info">审核意见：'.$request->request('auditMsg').'</span><br>';
                  
                  $issId=$request->request('issId');
                  $issSet = IssinfoModel::get($issId);
                  
                  //
                  $numId=$issSet->issnum;
                  
                  // 使用静态方法，向issrecord表新增信息。
                  $issRecordSet=IssrecordModel::create([
                    'num'=>$numId,
                    'act'=>'审核',
                    'actdetail'=>'专利事务"'.$issSet->topic.'"审核后返回撰写人【'.$issSet->writer.'】修改。<br><span class="text-info">审核意见：'.$request->request('auditMsg').'</span><br>',
                    'acttime'=>$today,
                    'username'=>$request->param('username'),
                    'rolename'=>$role,
                    'issinfo_id'=>$issId,
                  ]);
                  //静态方法创建新对象后，返回对象id
                  $issRecordId= $issRecordSet->id;
                  
                  $result='success';
                  $msg.='专利事务<strong>"'.$issSet->topic.'"</strong>审核后返回撰写人【'.$issSet->writer.'】修改。<br>';
                
              break;
              // pass
              default:
                // 使用静态方法，向issinfo表更新信息，赋值有变化就会更新和返回对象，无变化则无更新和对象返回。
                  IssinfoModel::update([
                      'status' => '审核通过',
                      'auditrejectdate'=> $today,
                  ], ['id' => $request->request('issId')]);
                  $result='success';
                  $msg.='审核结果：<strong class="text-success">审核通过</strong>。<br>';
                  
                  $issId=$request->request('issId');
                  $issSet = IssinfoModel::get($issId);
                  
                  //
                  $numId=$issSet->issnum;
                  
                  // 使用静态方法，向issrecord表新增信息。
                  $issRecordSet=IssrecordModel::create([
                    'num'=>$numId,
                    'act'=>'审核',
                    'actdetail'=>'专利事务"'.$issSet->topic.'"审核通过',
                    'acttime'=>$today,
                    'username'=>$request->param('username'),
                    'rolename'=>$role,
                    'issinfo_id'=>$issId,
                  ]);
                  //静态方法创建新对象后，返回对象id
                  $issRecordId= $issRecordSet->id;
                  
                  $result='success';
                  $msg.='专利事务<strong>"'.$issSet->topic.'"</strong>已审核通过。请留意后续审批结果。<br>';
              break;
              
            }
              // 存储上传的附件    
              if($attUpload){
                $issSet = IssinfoModel::get($request->request('issId'));
                    
                // 使用静态方法，向attinfo表新增附件文件
                $attSet = AttinfoModel::create([
                  'num_id'  => $numId,
                  'name'  => $request->request('attName'),
                  'atttype' => $request->request('attType'),
                  'attmap_type' => $request->request('attObj'),
                  'attmap_id' => $request->request('issId'),
                  'uploaddate'=> $today,
                  'uploader'=> $request->request('username'),
                  'rolename'=> $role,
                ]);
                //完成附件上传
                $uploadResult=$this->_uploadAtt($att,$attSet->num_id,$attSet->id);
                        
                if($uploadResult=='success'){
                  $msg.='新附件上传成功。<br>';
                }else{
                  $msg.=$uploadResult;
                }  
              }else{
                $result='success';
                $msg.='无新附件上传。<br>';
              }  
          
          break;
          
          case 'approver':
            switch($oprt){
              case 'veto':
                // 使用静态方法，向issinfo表更新信息，赋值有变化就会更新和返回对象，无变化则无更新和对象返回。
                  IssinfoModel::update([
                      'status' => '否决',
                      'auditrejectdate'=> $today,
                  ], ['id' => $request->request('issId')]);
                  $result='success';
                  $msg.='审批结果：<strong class="text-danger">否决</strong>。<br><span class="text-info">审批意见：'.$request->request('approveMsg').'</span><br>';
                  
                  $issId=$request->request('issId');
                  $issSet = IssinfoModel::get($issId);
                  //
                  $numId=$issSet->issnum;
                  
                  // 使用静态方法，向issrecord表新增信息。
                  $issRecordSet=IssrecordModel::create([
                    'num'=>$numId,
                    'act'=>'审批',
                    'actdetail'=>'专利事务"'.$issSet->topic.'"被否决。<br><span class="text-info">审批意见：'.$request->request('approveMsg').'</span><br>',
                    'acttime'=>$today,
                    'username'=>$request->param('username'),
                    'rolename'=>$role,
                    'issinfo_id'=>$issId,
                  ]);
                  //静态方法创建新对象后，返回对象id
                  $issRecordId= $issRecordSet->id;
                  
                  $result='success';
                  $msg.='专利事务<strong>"'.$issSet->topic.'"</strong>被否决。<br>';
              break;
              
              case 'modify':
                // 使用静态方法，向issinfo表更新信息，赋值有变化就会更新和返回对象，无变化则无更新和对象返回。
                  IssinfoModel::update([
                      'status' => '修改完善',
                      'auditrejectdate'=> $today,
                  ], ['id' => $request->request('issId')]);
                  $result='success';
                  $msg.='审批结果：<strong class="text-warning">修改完善</strong>。<br><span class="text-info">审批意见：'.$request->request('approveMsg').'</span><br>';
                  
                  $issId=$request->request('issId');
                  $issSet = IssinfoModel::get($issId);
                  
                  //
                  $numId=$issSet->issnum;
                  
                  // 使用静态方法，向issrecord表新增信息。
                  $issRecordSet=IssrecordModel::create([
                    'num'=>$numId,
                    'act'=>'审批',
                    'actdetail'=>'专利事务"'.$issSet->topic.'"审批后返回撰写人【'.$issSet->writer.'】修改。<br><span class="text-info">审核意见：'.$request->request('approveMsg').'</span><br>',
                    'acttime'=>$today,
                    'username'=>$request->param('username'),
                    'rolename'=>$role,
                    'issinfo_id'=>$issId,
                  ]);
                  //静态方法创建新对象后，返回对象id
                  $issRecordId= $issRecordSet->id;
                  
                  $result='success';
                  $msg.='专利事务<strong>"'.$issSet->topic.'"</strong>审批后返回撰写人【'.$issSet->writer.'】修改。<br>';
                
              break;
              // approve
              default:
                // 使用静态方法，向issinfo表更新信息，赋值有变化就会更新和返回对象，无变化则无更新和对象返回。
                  IssinfoModel::update([
                      'status' => '批准',
                      'auditrejectdate'=> $today,
                      'executer'=>$request->request('operator')
                  ], ['id' => $request->request('issId')]);
                  $result='success';
                  $msg.='审批结果：<strong class="text-success">批准</strong>。<br>';
                  
                  $issId=$request->request('issId');
                  $issSet = IssinfoModel::get($issId);
                  
                  //
                  $numId=$issSet->issnum;
                  
                  // 使用静态方法，向issrecord表新增信息。
                  $issRecordSet=IssrecordModel::create([
                    'num'=>$numId,
                    'act'=>'审批',
                    'actdetail'=>'专利事务"'.$issSet->topic.'"批准',
                    'acttime'=>$today,
                    'username'=>$request->param('username'),
                    'rolename'=>$role,
                    'issinfo_id'=>$issId,
                  ]);
                  //静态方法创建新对象后，返回对象id
                  $issRecordId= $issRecordSet->id;
                  
                  $result='success';
                  $msg.='专利事务<strong>"'.$issSet->topic.'"</strong>已批准。<br>选定的执行人为【'.$request->request('operator').'】。<br>';
              break;
              
            }
              // 存储上传的附件    
              if($attUpload){
                $issSet = IssinfoModel::get($request->request('issId'));
                    
                // 使用静态方法，向attinfo表新增附件文件
                $attSet = AttinfoModel::create([
                  'num_id'  => $numId,
                  'name'  => $request->request('attName'),
                  'atttype' => $request->request('attType'),
                  'attmap_type' => $request->request('attObj'),
                  'attmap_id' => $request->request('issId'),
                  'uploaddate'=> $today,
                  'uploader'=> $request->request('username'),
                  'rolename'=> $role,
                ]);
                //完成附件上传
                $uploadResult=$this->_uploadAtt($att,$attSet->num_id,$attSet->id);
                        
                if($uploadResult=='success'){
                  $msg.='新附件上传成功。<br>';
                }else{
                  $msg.=$uploadResult;
                }  
              }else{
                $result='success';
                $msg.='无新附件上传。<br>';
              }
          
          break;
          
          case 'operator':  //待完善，hy2018/1/4
            switch($oprt){
              case 'refuse':
                // 使用静态方法，向issinfo表更新信息，赋值有变化就会更新和返回对象，无变化则无更新和对象返回。
                  IssinfoModel::update([
                      'status' => '否决',
                      'auditrejectdate'=> $today,
                  ], ['id' => $request->request('issId')]);
                  $result='success';
                  $msg.='审批结果：<strong class="text-danger">否决</strong>。<br><span class="text-info">审批意见：'.$request->request('approveMsg').'</span><br>';
                  
                  $issId=$request->request('issId');
                  $issSet = IssinfoModel::get($issId);
                  //
                  $numId=$issSet->issnum;
                  
                  // 使用静态方法，向issrecord表新增信息。
                  $issRecordSet=IssrecordModel::create([
                    'num'=>$numId,
                    'act'=>'审批',
                    'actdetail'=>'专利事务"'.$issSet->topic.'"被否决。<br><span class="text-info">审批意见：'.$request->request('approveMsg').'</span><br>',
                    'acttime'=>$today,
                    'username'=>$request->param('username'),
                    'rolename'=>$role,
                    'issinfo_id'=>$issId,
                  ]);
                  //静态方法创建新对象后，返回对象id
                  $issRecordId= $issRecordSet->id;
                  
                  $result='success';
                  $msg.='专利事务<strong>"'.$issSet->topic.'"</strong>被否决。<br>';
              break;
              
              case 'report':
                // 使用静态方法，向issinfo表更新信息，赋值有变化就会更新和返回对象，无变化则无更新和对象返回。
                  IssinfoModel::update([
                      'status' => '修改完善',
                      'auditrejectdate'=> $today,
                  ], ['id' => $request->request('issId')]);
                  $result='success';
                  $msg.='审批结果：<strong class="text-warning">修改完善</strong>。<br><span class="text-info">审批意见：'.$request->request('approveMsg').'</span><br>';
                  
                  $issId=$request->request('issId');
                  $issSet = IssinfoModel::get($issId);
                  
                  //
                  $numId=$issSet->issnum;
                  
                  // 使用静态方法，向issrecord表新增信息。
                  $issRecordSet=IssrecordModel::create([
                    'num'=>$numId,
                    'act'=>'审批',
                    'actdetail'=>'专利事务"'.$issSet->topic.'"审批后返回撰写人【'.$issSet->writer.'】修改。<br><span class="text-info">审核意见：'.$request->request('approveMsg').'</span><br>',
                    'acttime'=>$today,
                    'username'=>$request->param('username'),
                    'rolename'=>$role,
                    'issinfo_id'=>$issId,
                  ]);
                  //静态方法创建新对象后，返回对象id
                  $issRecordId= $issRecordSet->id;
                  
                  $result='success';
                  $msg.='专利事务<strong>"'.$issSet->topic.'"</strong>审批后返回撰写人【'.$issSet->writer.'】修改。<br>';
                
              break;
              
              case 'finish':
                // 使用静态方法，向issinfo表更新信息，赋值有变化就会更新和返回对象，无变化则无更新和对象返回。
                  IssinfoModel::update([
                      'status' => '修改完善',
                      'auditrejectdate'=> $today,
                  ], ['id' => $request->request('issId')]);
                  $result='success';
                  $msg.='审批结果：<strong class="text-warning">修改完善</strong>。<br><span class="text-info">审批意见：'.$request->request('approveMsg').'</span><br>';
                  
                  $issId=$request->request('issId');
                  $issSet = IssinfoModel::get($issId);
                  
                  //
                  $numId=$issSet->issnum;
                  
                  // 使用静态方法，向issrecord表新增信息。
                  $issRecordSet=IssrecordModel::create([
                    'num'=>$numId,
                    'act'=>'审批',
                    'actdetail'=>'专利事务"'.$issSet->topic.'"审批后返回撰写人【'.$issSet->writer.'】修改。<br><span class="text-info">审核意见：'.$request->request('approveMsg').'</span><br>',
                    'acttime'=>$today,
                    'username'=>$request->param('username'),
                    'rolename'=>$role,
                    'issinfo_id'=>$issId,
                  ]);
                  //静态方法创建新对象后，返回对象id
                  $issRecordId= $issRecordSet->id;
                  
                  $result='success';
                  $msg.='专利事务<strong>"'.$issSet->topic.'"</strong>审批后返回撰写人【'.$issSet->writer.'】修改。<br>';
                
              break;
              // accept
              default:
                // 使用静态方法，向issinfo表更新信息，赋值有变化就会更新和返回对象，无变化则无更新和对象返回。
                  IssinfoModel::update([
                      'status' => '批准',
                      'auditrejectdate'=> $today,
                      'executer'=>$request->request('operator')
                  ], ['id' => $request->request('issId')]);
                  $result='success';
                  $msg.='审批结果：<strong class="text-success">批准</strong>。<br>';
                  
                  $issId=$request->request('issId');
                  $issSet = IssinfoModel::get($issId);
                  
                  //
                  $numId=$issSet->issnum;
                  
                  // 使用静态方法，向issrecord表新增信息。
                  $issRecordSet=IssrecordModel::create([
                    'num'=>$numId,
                    'act'=>'审批',
                    'actdetail'=>'专利事务"'.$issSet->topic.'"批准',
                    'acttime'=>$today,
                    'username'=>$request->param('username'),
                    'rolename'=>$role,
                    'issinfo_id'=>$issId,
                  ]);
                  //静态方法创建新对象后，返回对象id
                  $issRecordId= $issRecordSet->id;
                  
                  $result='success';
                  $msg.='专利事务<strong>"'.$issSet->topic.'"</strong>已批准。<br>选定的执行人为【'.$request->request('operator').'】。<br>';
              break;
              
            }
              // 存储上传的附件    
              if($attUpload){
                $issSet = IssinfoModel::get($request->request('issId'));
                    
                // 使用静态方法，向attinfo表新增附件文件
                $attSet = AttinfoModel::create([
                  'num_id'  => $numId,
                  'name'  => $request->request('attName'),
                  'atttype' => $request->request('attType'),
                  'attmap_type' => $request->request('attObj'),
                  'attmap_id' => $request->request('issId'),
                  'uploaddate'=> $today,
                  'uploader'=> $request->request('username'),
                  'rolename'=> $role,
                ]);
                //完成附件上传
                $uploadResult=$this->_uploadAtt($att,$attSet->num_id,$attSet->id);
                        
                if($uploadResult=='success'){
                  $msg.='新附件上传成功。<br>';
                }else{
                  $msg.=$uploadResult;
                }  
              }else{
                $result='success';
                $msg.='无新附件上传。<br>';
              }
          break;
          
          case 'maintainer':
          
          break;
          
          default:
          
          break;
          
        }
        // 向前端传送添加/修改成功后生成的patIssId
        $data=array('result'=>$result,'msg'=>$msg,'patIssId'=>$request->request('issId'));
        //$data=array('result'=>$result,'msg'=>$msg);
        return json($data); 
        
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div> ';
        // 输出对应的模板文件
        //return view($role.'_'.$oprt);
      }
        
    }  
    
    // 各个role的patIss的增删改查模板文件
    public function patIssTpl(Request $request,View $view)
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
                  $pat= PatinfoModel::get($iss->issmap_id);
                  
                  // 查出issue所对应的attachment
                  $att= AttinfoModel::where('attmap_type','_ATTO1')->where('attmap_id',$patIssId)->select();
                  
                  $this->assign([
                    'home'=>$request->domain(),
                    // 
                    'writer'=>$this->username,
                    'dept'=>$this->dept,
                    //'issStatus'=>$iss->status,
                    
                    //
                    'patIssId'=>$patIssId,
                    
                    'iss'=>$iss,
                    'pat'=>$pat,
                    'att'=>$att,
              
                  ]);
                break;
                
              }
          break;
          
          case "reviewer":
            switch($tpl){                
                case "audit":
                  // 查出所审核的issue的数据：
                  $iss= IssinfoModel::get($patIssId);
                  
                  // 查出issue所对应的patent
                  $pat= PatinfoModel::get($iss->issmap_id);
                  
                  // 查出issue所对应的attachment
                  $att= AttinfoModel::where('attmap_type','_ATTO1')->where('attmap_id',$patIssId)->select();
                  
                  $this->assign([
                    'home'=>$request->domain(),
                    // 
                    'reviewer'=>$this->username,
                    'dept'=>$this->dept,
                    //'issStatus'=>$iss->status,
                    
                    //
                    'patIssId'=>$patIssId,
                    
                    'iss'=>$iss,
                    'pat'=>$pat,
                    'att'=>$att,
              
                  ]);
                break;
                
              }
          break;
          
          case "approver":
            switch($tpl){                
                case "approve":
                  // 查出所审核的issue的数据：
                  $iss= IssinfoModel::get($patIssId);
                  
                  // 查出issue所对应的patent
                  $pat= PatinfoModel::get($iss->issmap_id);
                  
                  // 查出issue所对应的attachment
                  $att= AttinfoModel::where('attmap_type','_ATTO1')->where('attmap_id',$patIssId)->select();
                  
                  $this->assign([
                    'home'=>$request->domain(),
                    // 
                    'approver'=>$this->username,
                    'dept'=>$this->dept,
                    //'issStatus'=>$iss->status,
                    
                    //
                    'patIssId'=>$patIssId,
                    
                    'iss'=>$iss,
                    'pat'=>$pat,
                    'att'=>$att,
              
                  ]);
                break;
                
              }
          break;
          
          case "operator":
            switch($tpl){                
                case "todo":
                  // 查出所审核的issue的数据：
                  $iss= IssinfoModel::get($patIssId);
                  
                  // 查出issue所对应的patent
                  $pat= PatinfoModel::get($iss->issmap_id);
                  
                  // 查出issue所对应的attachment
                  $att= AttinfoModel::where('attmap_type','_ATTO1')->where('attmap_id',$patIssId)->select();
                  
                  $this->assign([
                    'home'=>$request->domain(),
                    // 
                    'operator'=>$this->username,
                    'dept'=>$this->dept,
                    //'issStatus'=>$iss->status,
                    
                    //
                    'patIssId'=>$patIssId,
                    
                    'iss'=>$iss,
                    'pat'=>$pat,
                    'att'=>$att,
              
                  ]);
                break;
                
                case "inprocess":
                  // 查出所审核的issue的数据：
                  $iss= IssinfoModel::get($patIssId);
                  
                  // 查出issue所对应的patent
                  $pat= PatinfoModel::get($iss->issmap_id);
                  
                  // 查出issue所对应的attachment
                  $att= AttinfoModel::where('attmap_type','_ATTO1')->where('attmap_id',$patIssId)->select();
                  
                  $this->assign([
                    'home'=>$request->domain(),
                    // 
                    'operator'=>$this->username,
                    'dept'=>$this->dept,
                    //'issStatus'=>$iss->status,
                    
                    //
                    'patIssId'=>$patIssId,
                    
                    'iss'=>$iss,
                    'pat'=>$pat,
                    'att'=>$att,
              
                  ]);
                break;
                
              }
          break;
          
          case "maintainer":
          
          break;
          
          default:
          
          break;
          
        }
        
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div> ';
        //输出对应的模板文件$role.'_'.$tpl.html
        //return $this->fetch('dashboard'.DS.'pat'.DS.$role.'_'.$tpl);
        return view('dashboard'.DS.'patiss'.DS.$role.'_'.$tpl);
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
    
     //上传附件文件
     //参数1：$fileSet，类型：对象。值：不为空。说明：拟上传的文件对象
     //参数2：$num_id，类型：字符。值：不为空。说明：上传文件拟放入的目录名称
     //参数3：$attId，类型：字符。值：不为空。说明：拟记录上传文件路径的记录id
    private function _uploadAtt($fileSet,$num_id,$attId)
    {
      
      if(!empty($fileSet)){
            // 移动到框架根目录的uploads/ 目录下,系统重新命名文件名
            $info = $fileSet->validate(['size'=>10485760,'ext'=>'jpg,jpeg,pdf,doc,docx,xls,xlsx,ppt,pptx'])
                        ->move(ROOT_PATH.DS.'uploads'.DS.$num_id);
        }else{
            $this->error('未选择文件，请选择需上传的文件。');
        }
        
        if($info){
            // 成功上传后 获取上传信息
            // 文件的后缀名
            $info->getExtension()."<br/>";
            // 文件存放的文件夹路径：类似20160820/42a79759f284b767dfcb2a0197904287.jpg
            $info->getSaveName()."<br/>";
            // 完整的文件名
            $info->getFilename(); 
            
            $path= '..'.DS.'uploads'. DS.$num_id.DS.$info->getSaveName();
            
            $attSet = AttinfoModel::where('id',$attId)->find(); 
            $attSet->save([
              'attpath'=>$path,
            ]);
             
            // 静态调用更新
            //$attSet=AttinfoModel::update([
//              'name'  => 'topthink',
//              'email' => 'topthink@qq.com',
//            ], ['num_id'=>$num_id]);
            return "success";
            
        }else{
            // 上传失败获取错误信息
            //echo $file->getError();
            return $fileSet->getError();
        }
      
    }
    
    //删除$dirName目录及其文件
    // 应用php5里的dir,is_dir,unlink,rmdir
    private function _deleteDirs($dirName)
    {
        //循环删除目录和文件，成功后返回 "success"
        $d=dir($dirName);
        $result=0;
        while(false!==($child=$d->read())){
          // 清除目录里所有的文件
          if($child!="."&&$child!=".."){
            if(is_dir($dirName.DS.$child)){
              // 递归调用自己
              $this->_deleteDirs($dirName.DS.$child);
            }else{
              unlink($dirName.DS.$child);
              
            }
          }
        }
        $d->close();
        
        //清除目录
        rmdir($dirName);
        
        if(is_dir($dirName)){
          $result=$dirName;
        }else{
          $result="success";
        }
        return $result;
    }
    
    // 向前端返回查询的operator信息
     public function selectOperator()
    {
      $this->_loginUser();
      
      $operator=UserModel::where('rolety_id','7')->where('enable','1')->order('dept', 'asc')->select();
      // 将数组转化为json
      return json($operator);
      
    }



}
