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

class Dashboard2Controller extends \think\Controller
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
    //用户权限
    private $auth=array();
    
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
      }else{
        //取出user的authority字段值
        $user=new UserModel;           
        $userlg=$user->where('username',$this->username)->where('pwd',$this->pwd)->find();
        $this->auth=$userlg->authority;
      }    
    }
    
    public function index2(Request $request)
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
        
        //使用模型Issinfo
        $issSet = new IssinfoModel; 
        //edit
        $mapEdit['status'] =['in',['填报','返回修改','修改完善']];
        $mapEdit['dept'] =$this->dept;
        $mapEdit['writer']=$this->username;
        
        //audit
        $mapAudit['status'] ='待审核';
        $mapAudit['dept'] =$this->dept;
        //approve
        $mapApprove['status'] =['in',['审核未通过','审核通过','变更申请','拟续费']];
        //execute
        $mapExecute['status'] =['in',['批准申报','申报执行','申报修改','准予变更','否决变更']];
        $mapExecute['dept'] =$this->dept;
        $mapExecute['executer'] =$this->username;
        //maintain
        $mapMaintain['status'] =['in',['申报复核','申报提交','续费提交','准予续费',
                                      '否决申报','专利授权','专利驳回','放弃续费','续费授权']];
        //done
        $map['status'] ='完结';
        
        if($this->auth['authiss']['edit']){
          $numIssPatEdit=$issSet->where($mapEdit)->count(); 
        }else{
          $numIssPatEdit=0;
        }
        
        if($this->auth['authiss']['audit']){
          $numIssPatAudit=$issSet->where($mapAudit)->count(); 
        }else{
          $numIssPatAudit=0;
        }
        
        if($this->auth['authiss']['approve']){
          $numIssPatApprove=$issSet->where($mapApprove)->count(); 
        }else{
          $numIssPatApprove=0;
        }
        
        if($this->auth['authiss']['execute']){
          $numIssPatExecute=$issSet->where($mapExecute)->count(); 
        }else{
          $numIssPatExecute=0;
        }
        
        if($this->auth['authiss']['maintain']){
          $numIssPatMaintain=$issSet->where($mapMaintain)->count(); 
          //得到满足续费条件的专利数
          $today=date('Y-m-d');
          $deadline=date('Y-m-d',strtotime("+6 month"));
          $mapRenew['status'] =['in',['授权','续费授权']];          
          // 查出满足条件的patent
          $numPatRenewTotal= PatinfoModel::where($mapRenew)->where('renewdeadlinedate','between time',[$today,$deadline])->count();
        }else{
          $numIssPatMaintain=0;
          $numPatRenewTotal=0;
        }
        
        $numIssPatDone=$issSet->where($map)->count(); 
        
        $numTotal=$numIssPatEdit+$numIssPatAudit+$numIssPatApprove+$numIssPatExecute+$numIssPatMaintain+$numPatRenewTotal;
        
        //取出user的authority字段值
      //  $user=new UserModel;           
//        $userlg=$user->where('username',$username)->where('pwd',$pwd)->find();
//        $this->auth=$userlg->authority;
        
       // UserModel::update([
//          'dept'=>'dept2',
//          //'authority$.att$.delete'=>0,
//          'authority'=>json_encode('{"isspat":{"create":0,"edit":0,"audit":1,"approve":1,"execute":1,"maintain":1},"isspro":{"create":0,"edit":0,"audit":0,"approve":0,"execute":0,"maintain":0},"issthe":{"create":0,"edit":0,"audit":0,"approve":0,"execute":0,"maintain":0},"att":{"upload":1,"download":1,"delete":1}}'),
//        ], ['id' => $userlg->id]);
        
        //$user1=$userlg->data(array('dept'=>'dept2'),true)->save();

        $destr= "请求方法:".$request->method()."<br/>".
                "username:". $username."<br/>".
                "pwd:".$pwd."<br/>".
                "roles:".$roles[0]."<br/>".
                "log:".$log."<br/>".
                "auth:".json_encode($this->auth); 
        
         // 模板变量赋值        
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
  
          //向前端权限变量赋值
          'auth'=>$this->auth, 
          
          'numIssPatEdit'=>$numIssPatEdit,
          'numIssPatAudit'=>$numIssPatAudit,
          'numIssPatApprove'=>$numIssPatApprove,
          'numIssPatExecute'=>$numIssPatExecute,
          'numIssPatMaintain'=>$numIssPatMaintain,
          'numIssPatDone'=>$numIssPatDone,
          'numTotal'=>$numTotal,
          'numPatRenewTotal'=>$numPatRenewTotal,
      	
        ]);
        //return view();
        // 模板输出
        return $this->fetch();
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
    
     //上传附件文件
     //参数1：$fileSet，类型：对象。值：不为空。说明：拟上传的文件对象
     //参数2：$num_id，类型：字符。值：不为空。说明：上传文件拟放入的目录名称
     //参数3：$attId，类型：字符。值：不为空。说明：拟记录上传文件路径的记录id
    private function _uploadAtt($fileSet,$num_id,$attId)
    {
      
      if(!empty($fileSet)){
            // 移动到框架根目录的uploads/ 目录下,系统重新命名文件名
            $info = $fileSet->validate(['size'=>10485760,'ext'=>'jpg,jpeg,pdf,doc,docx,xls,xlsx,ppt,pptx,rar'])
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
    
     public function issPat(Request $request)
    {
       $this->_loginUser();
      
      //从session中取出登录用户的关键信息
        $username=Session::get('username');
        $pwd=Session::get('pwd');
        $log=Session::get('log');
        $roles=Session::get('role');
        $dept=Session::get('dept');
        $role=$request->param('role');
      
      //使用模型Issinfo
        $issSet = new IssinfoModel; 
        //edit
        $mapEdit['status'] =['in',['填报','返回修改','修改完善']];
        $mapEdit['dept'] =$this->dept;
        $mapEdit['writer']=$this->username;
        
        //audit
        $mapAudit['status'] ='待审核';
        $mapAudit['dept'] =$this->dept;
        //approve
        $mapApprove['status'] =['in',['审核未通过','审核通过','变更申请','拟续费']];
        //execute
        $mapExecute['status'] =['in',['批准申报','申报执行','申报修改','准予变更','否决变更']];
        $mapExecute['dept'] =$this->dept;
        $mapExecute['executer'] =$this->username;
        //maintain
        $mapMaintain['status'] =['in',['申报复核','申报提交','续费提交','准予续费',
                                      '否决申报','专利授权','专利驳回','放弃续费','续费授权']];
        //done
        $map['status'] ='完结';
        
        if($this->auth['authiss']['edit']){
          $numIssPatEdit=$issSet->where($mapEdit)->count(); 
        }else{
          $numIssPatEdit=0;
        }
        
        if($this->auth['authiss']['audit']){
          $numIssPatAudit=$issSet->where($mapAudit)->count(); 
        }else{
          $numIssPatAudit=0;
        }
        
        if($this->auth['authiss']['approve']){
          $numIssPatApprove=$issSet->where($mapApprove)->count(); 
        }else{
          $numIssPatApprove=0;
        }
        
        if($this->auth['authiss']['execute']){
          $numIssPatExecute=$issSet->where($mapExecute)->count(); 
        }else{
          $numIssPatExecute=0;
        }
        
        if($this->auth['authiss']['maintain']){
          $numIssPatMaintain=$issSet->where($mapMaintain)->count(); 
          //得到满足续费条件的专利数
          $today=date('Y-m-d');
          $deadline=date('Y-m-d',strtotime("+6 month"));
          $mapRenew['status'] =['in',['授权','续费授权']];          
          // 查出满足条件的patent
          $numPatRenewTotal= PatinfoModel::where($mapRenew)->where('renewdeadlinedate','between time',[$today,$deadline])->count();
        }else{
          $numIssPatMaintain=0;
          $numPatRenewTotal=0;
        }
        
        $numIssPatDone=$issSet->where($map)->count(); 
        
        $numTotal=$numIssPatEdit+$numIssPatAudit+$numIssPatApprove+$numIssPatExecute+$numIssPatMaintain+$numPatRenewTotal;
        
        $destr= "请求方法:".$request->method()."<br/>".
                "username:". $username."<br/>".
                "pwd:".$pwd."<br/>".
                "roles:".$roles[0]."<br/>".
                "log:".$log."<br/>".
                "auth:".json_encode($this->auth); 
        
         // 模板变量赋值        
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
  
          //向前端权限变量赋值
          'authArray'=>$this->auth, 
          
          'numIssPatEdit'=>$numIssPatEdit,
          'numIssPatAudit'=>$numIssPatAudit,
          'numIssPatApprove'=>$numIssPatApprove,
          'numIssPatExecute'=>$numIssPatExecute,
          'numIssPatMaintain'=>$numIssPatMaintain,
          'numIssPatDone'=>$numIssPatDone,
          'numTotal'=>$numTotal,
          'numPatRenewTotal'=>$numPatRenewTotal,
      	
        ]);
      return view();
      //return ':)<br> issthe 模块开发中……';
     
    } 
    
    //根据前端传来的权限，选择返回前端的模板文件及内容
    public function issPatAuth(Request $request)
    {
      $this->_loginUser();
      
      // $role接收前端页面传来的auth值
      if(!empty($request->param('auth'))){
        $auth=$request->param('auth');
      }else{
        $auth='_DONE';
      }
      
      // $returnType接收前端页面传来的returnType值，‘0’为模板文件，‘1’为数据
      if(!empty($request->param('returnType'))){
        $returnType=$request->param('returnType');
      }else{
        $returnType=0;
      }

      //$totalTableRows接收前端页面传来的分页时每页显示的记录数，默认为10
      if(!empty($request->param('issPatTableRows'))){
          $issPatTableRows=$request->param('issPatTableRows');
      }else{
          $issPatTableRows=10;
      }
      
       // 接收前端分页页数变量：“pageTotalNum”
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
            
        case '_TOPIC':
          $strOrder='topic';  
          $sortName="_TOPIC";
        break;
        
         //默认按字段“status”
        default:
          $strOrder='status';  
          $sortName="_OPERATION";
        break;
        
      } 
      
      //  组合升序or降序查询
      if($sort=="_ASC"){
          $strOrder=$strOrder.' asc';
      }else{
          $strOrder=$strOrder.' desc';
          
      }
      //选择模板文件名,组合查询条件
      switch($auth){
          //_EDIT
          case '_EDIT':
            $map['status'] =['in',['填报','返回修改','修改完善']];
            $map['dept'] =$this->dept;
            $map['writer']=$this->username;
            //$tplFile='issPatEdit';
            $tplFile='edit';
          break;
          //_AUDIT
          case '_AUDIT':
            $map['status'] ='待审核';
            $map['dept'] =$this->dept;
            //$tplFile='issPatAudit';
            $tplFile='audit';
          break;
          //_APPROVE
          case '_APPROVE':
            $map['status'] =['in',['审核未通过','审核通过','变更申请','拟续费']];
            //$tplFile='issPatApprove';
            $tplFile='approve';
          break;
          //_EXECUTE
          case '_EXECUTE':
            $map['executer'] =$this->username;
            $map['status'] =['in',['批准申报','申报执行','申报修改','准予变更','否决变更']];
            $map['dept'] =$this->dept;
            
            //$tplFile='issPatExecute';
            $tplFile='execute';
          break;
          //_MAINTAIN
          case '_MAINTAIN':
            $map['status'] =['in',['申报复核','申报提交','续费提交','准予续费',
                                      '否决申报','专利授权','专利驳回','放弃续费','续费授权']];
            //$tplFile='issPatMaintain';
            $tplFile='maintain';
          break;
          ////_MAINTAIN_RENEW
//          case '_MAINTAIN_RENEW':
//            $mapPat['status'] =['in',['授权','续费授权']];
//            //$tplFile='issPatMaintain';
//            $tplFile='patRenew';
//          break;
          //_DONE
          default:
            $map['status'] ='完结';
            //$tplFile='issPatDone';
            $tplFile='done';
          break;
          
      }
      
      //得到模板文件中需显示的内容
      //使用模型Issinfo
     $issSet = new IssinfoModel; 
          
     // 记录总数
     $numTotal = $issSet->where($map)->count();
          
     // 查出所有的用户并分页，根据“strOrder”排序，前端页面显示的锚点（hash值）为$fragment，设定分页页数变量：“pageTotalNum”
     // 带上每页显示记录行数$totalTableRows，实现查询结果分页显示。
     $issPatTotal = $issSet->where($map)
                            ->order($strOrder)
                            ->paginate($issPatTableRows,false,['type'=>'bootstrap','var_page' => 'pageTotalNum',
                            'query'=>['issPatTableRows'=>$issPatTableRows]]);
     // 获取分页显示
     $pageTotal = $issPatTotal->render();
      //返回数据还是模板文件,‘0’为模板文件，‘1’为数据
      if($returnType){
        //响应前端的请求，返回前端要求条件的issPat数量
        return ($numTotal);
      }else{
        $this->assign([
              'home'=>$request->domain(),
              //"destr".$this->auth['isspro']['edit'],
              
              // 分页显示所需参数
              'issPatTotal'=>$issPatTotal,
              'numTotal'=>$numTotal,
              'pageTotal'=>$pageTotal,
              'issPatTableRows'=>$issPatTableRows,
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
              'auth'=>$auth          
              
        ]);
        
       // return view($tplFile);
        return view('dashboard2'.DS.'issPatAuth'.DS.$tplFile);
      }
    }
    
    //根据前端传来的权限，选择返回前端的模板文件及内容
    public function issPatAuthSingle(Request $request)
    {
      $this->_loginUser();
      
      // $oprt接收前端页面传来的oprt值
      if(!empty($request->param('oprt'))){
        $oprt=$request->param('oprt');
      }else{
        $oprt='_NONE';
      }
      
      if(!empty($request->param('auth'))){
        $auth=$request->param('auth');
      }else{
        $auth='';
      }
      
      //选择模板文件名
      switch($auth){
        //_EDIT
        case '_EDIT':
          $tplFile='editSingle';
        break;
        //_AUDIT
        case '_AUDIT':
          $tplFile='auditSingle';
        break;
        //_APPROVE
        case '_APPROVE':
          $tplFile='approveSingle';
        break;
        //_EXECUTE
        case '_EXECUTE':
          $tplFile='executeSingle';
        break;
        //_MAINTAIN
        case '_MAINTAIN':
          $tplFile='maintainSingle';
        break;
        //_DONE
        default:
          $tplFile='doneSingle';
        break;
        
      }
      
      if($oprt=='_ADDNEW'){
        $iss=0;
        $att=0;
        $pat=array('topic'=>'','patowner'=>'','otherinventor'=>'','inventor'=>'');
        $patType=0;
      }else{
        //得到模板文件中需显示的内容
        $iss=IssinfoModel::get($request->param('issId'));
        // 利用模型issinfo.php中定义的一对多方法“attachments”得到iss对应的attachments信息
        $att=$iss->attachments;
        // 利用模型issinfo.php中定义的多态方法“issmap”得到iss对应的pat信息
        $pat=$iss->issmap;
        // 得到iss对应的pat的'pattype'数据库字段值
        $patType=$iss->issmap->getData('pattype');
      }
      
      //向前端模板中的变量赋值
      $this->assign([
        'home'=>$request->domain(),
        'username'=>$this->username,
        'dept'=>$this->dept,
        'auth'=>$request->param('auth'),
        'oprt'=>$oprt,
        'iss'=>$iss,
        'att'=>$att,
        'pat'=>$pat,
        'patType'=>$patType,
        
      ]);
      //return $this->fetch($tplFile);
      return view('dashboard2'.DS.'issPatAuthSingle'.DS.$tplFile);
    }
    
    //根据前端传来的操作类型，对数据库进行操作
    public function issPatOprt(Request $request,IssinfoModel $issObj,IssrecordModel $issRdObj,
                                PatinfoModel $patObj,PatrecordModel $patRdObj,
                                AttinfoModel $attObj)
    {
      $this->_loginUser();
      
      // $oprt接收前端页面传来的oprt值
      if(!empty($request->param('oprt'))){
        $oprt=$request->param('oprt');
      }else{
        $oprt='_NONE';
      }
      
      $msg="";
      $tplFile='dashboard2'.DS.'issPatAuthSingle'.DS;
            
      switch($oprt){
        //“_EDIT”权限
        case'_ADDNEW':
        
          $msg='<div style="padding: 24px 48px;"><h1>:)</h1><p>'.$oprt.'模块开发中……<br/></p></div>';
          $tplFile.='editSingle';
          
        break;
        
        case'_SUBMIT':
          
          $msg='<div style="padding: 24px 48px;"><h1>:)</h1><p>'.$oprt.'模块开发中……<br/></p></div>';
          $tplFile.='editSingle';
          
        break;
        
        case'_DELETE':
          
          $msg='<div style="padding: 24px 48px;"><h1>:)</h1><p>'.$oprt.'模块开发中……<br/></p></div>';
          $tplFile.='editSingle';
          
        break;
        
        case'_UPDATE':
          
          $msg='<div style="padding: 24px 48px;"><h1>:)</h1><p>'.$oprt.'模块开发中……<br/></p></div>';
          $tplFile.='editSingle';
          
        break;
        //“_AUDIT”权限
        case'_PASS':
          
          $msg='<div style="padding: 24px 48px;"><h1>:)</h1><p>'.$oprt.'模块开发中……<br/></p></div>';
          $tplFile.='auditSingle';
          
        break;
        
        case'_FAIL':
          
          $msg='<div style="padding: 24px 48px;"><h1>:)</h1><p>'.$oprt.'模块开发中……<br/></p></div>';
          $tplFile.='auditSingle';
         
        break;
        
        case'_MODIFY':
          
          $msg='<div style="padding: 24px 48px;"><h1>:)</h1><p>'.$oprt.'模块开发中……<br/></p></div>';
          $tplFile.='auditSingle';
         
        break;
        //“_APPROVE”权限
        case'_PERMIT':
          
          $msg='<div style="padding: 24px 48px;"><h1>:)</h1><p>'.$oprt.'模块开发中……<br/></p></div>';
        break;
        
        case'_VETO':
          
          $msg='<div style="padding: 24px 48px;"><h1>:)</h1><p>'.$oprt.'模块开发中……<br/></p></div>';
        break;
        
        case'_COMPLETE':
          
          $msg='<div style="padding: 24px 48px;"><h1>:)</h1><p>'.$oprt.'模块开发中……<br/></p></div>';
        break;
        //“_EXECUTE”权限
        case'_ACCEPT':
          
          $msg='<div style="padding: 24px 48px;"><h1>:)</h1><p>'.$oprt.'模块开发中……<br/></p></div>';
        break;
        
        case'_REFUSE':
          
          $msg='<div style="padding: 24px 48px;"><h1>:)</h1><p>'.$oprt.'模块开发中……<br/></p></div>';
        break;
        
        case'_REPORT':
          
          $msg='<div style="padding: 24px 48px;"><h1>:)</h1><p>'.$oprt.'模块开发中……<br/></p></div>';
        break;
        
        case'_FINISH':
          
          $msg='<div style="padding: 24px 48px;"><h1>:)</h1><p>'.$oprt.'模块开发中……<br/></p></div>';
        break;
        //“_MAINTAIN”权限
        case'_APPLY':
          
          $msg='<div style="padding: 24px 48px;"><h1>:)</h1><p>'.$oprt.'模块开发中……<br/></p></div>';
        break;
        
        case'_IMPROVE':
          
          $msg='<div style="padding: 24px 48px;"><h1>:)</h1><p>'.$oprt.'模块开发中……<br/></p></div>';
        break;
        
        case'_AUTHORIZE':
          
          $msg='<div style="padding: 24px 48px;"><h1>:)</h1><p>'.$oprt.'模块开发中……<br/></p></div>';
        break;
        
        case'_REJECT':
          
          $msg='<div style="padding: 24px 48px;"><h1>:)</h1><p>'.$oprt.'模块开发中……<br/></p></div>';
        break;
        
        case'_CLOSE':
          
          $msg='<div style="padding: 24px 48px;"><h1>:)</h1><p>'.$oprt.'模块开发中……<br/></p></div>';
        break;
        
        case'_ADDRENEW':
          
          if($request->param('returnType')=='_JSON'){
            return json(array_merge($patObj->where('id',$request->param('patId'))->find()->toArray(),
                              array("today"=>date('Y-m-d'),"username"=>$this->username,"deptMaintainer"=>$this->dept)));
          }else{
            $msg='<div style="padding: 24px 48px;"><h1>:)</h1><p>'.$oprt.'模块开发中……<br/></p></div>';
          }
          
        break;
        
      }
      
      //return $msg;
      return $this->issPatAuth($request);
      
    }
    
    //为前端显示PatRenew模板准备，1.数据库数据；2.向模板变量赋值；3.选择模板文件PatRenew.html返回前端
     public function patRenew(Request $request,PatinfoModel $patObj)
    {
      $this->_loginUser();
      
      //调用模型文件Patinfo.php中定义的patRenew方法找出合适的pat。
      $pat= $patObj->patRenew();
      
            
     // 查出所有的用户并分页，根据“strOrder”排序，前端页面显示的锚点（hash值）为$fragment，设定分页页数变量：“pageTotalNum”
     // 带上每页显示记录行数$totalTableRows，实现查询结果分页显示。
     //$issPatTotal = $issSet->where($map)
//                            ->order($strOrder)
//                            ->paginate($issPatTableRows,false,['type'=>'bootstrap','var_page' => 'pageTotalNum',
//                            'query'=>['issPatTableRows'=>$issPatTableRows]]);
     // 获取分页显示
     $pageTotal = $pat->render();
       
      $this->assign([
          'home'=>$request->domain(),
          // 
          'maintainer'=>$this->username,
          'dept'=>$this->dept,
          'pat'=>$pat,
          'patRenewTotal'=>count($pat),
          'pageTotal'=>$pageTotal,
      ]);
       
       //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>_RENEW 模块开发中……<br/></p></div>';
       return view('dashboard2'.DS.'issPatAuth'.DS.'patRenew');
    }
    
    public function issThe(Request $request)
    {
       $this->_loginUser();
      
      ////从session中取出登录用户的关键信息
//        $username=Session::get('username');
//        $pwd=Session::get('pwd');
//        $log=Session::get('log');
//        $roles=Session::get('role');
//        $dept=Session::get('dept');
//        $role=$request->param('role');
//      
//      //使用模型Issinfo
//        $issSet = new IssinfoModel; 
//        //edit
//        $mapEdit['status'] =['in',['填报','返回修改','修改完善']];
//        $mapEdit['dept'] =$this->dept;
//        $mapEdit['writer']=$this->username;
//        
//        //audit
//        $mapAudit['status'] ='待审核';
//        $mapAudit['dept'] =$this->dept;
//        //approve
//        $mapApprove['status'] =['in',['审核未通过','审核通过','变更申请','拟续费']];
//        //execute
//        $mapExecute['status'] =['in',['批准申报','申报执行','申报修改','准予变更','否决变更']];
//        $mapExecute['dept'] =$this->dept;
//        $mapExecute['executer'] =$this->username;
//        //maintain
//        $mapMaintain['status'] =['in',['申报复核','申报提交','续费提交','准予续费',
//                                      '否决申报','专利授权','专利驳回','放弃续费','续费授权']];
//        //done
//        $map['status'] ='完结';
//        
//        if($this->auth['authiss']['edit']){
//          $numIssPatEdit=$issSet->where($mapEdit)->count(); 
//        }else{
//          $numIssPatEdit=0;
//        }
//        
//        if($this->auth['authiss']['audit']){
//          $numIssPatAudit=$issSet->where($mapAudit)->count(); 
//        }else{
//          $numIssPatAudit=0;
//        }
//        
//        if($this->auth['authiss']['approve']){
//          $numIssPatApprove=$issSet->where($mapApprove)->count(); 
//        }else{
//          $numIssPatApprove=0;
//        }
//        
//        if($this->auth['authiss']['execute']){
//          $numIssPatExecute=$issSet->where($mapExecute)->count(); 
//        }else{
//          $numIssPatExecute=0;
//        }
//        
//        if($this->auth['authiss']['maintain']){
//          $numIssPatMaintain=$issSet->where($mapMaintain)->count(); 
//          //得到满足续费条件的专利数
//          $today=date('Y-m-d');
//          $deadline=date('Y-m-d',strtotime("+6 month"));
//          $mapRenew['status'] =['in',['授权','续费授权']];          
//          // 查出满足条件的patent
//          $numPatRenewTotal= PatinfoModel::where($mapRenew)->where('renewdeadlinedate','between time',[$today,$deadline])->count();
//        }else{
//          $numIssPatMaintain=0;
//          $numPatRenewTotal=0;
//        }
//        
//        $numIssPatDone=$issSet->where($map)->count(); 
//        
//        $numTotal=$numIssPatEdit+$numIssPatAudit+$numIssPatApprove+$numIssPatExecute+$numIssPatMaintain+$numPatRenewTotal;
//        
//        $destr= "请求方法:".$request->method()."<br/>".
//                "username:". $username."<br/>".
//                "pwd:".$pwd."<br/>".
//                "roles:".$roles[0]."<br/>".
//                "log:".$log."<br/>".
//                "auth:".json_encode($this->auth); 
//        
//         // 模板变量赋值        
//        $this->assign([
//          //在usercenter.html页面输出自定义的信息
//          //在index.html页面通过destr输出自定义的信息
//          'destr'=>$destr."</br>",
//          //在index.html页面通过array输出自定义的数组内容
//          'array'=>$roles, 
//          
//          'home'=>$request->domain(),
//          'username'=>$username,
//          'roles'=>$roles,
//          //'roles'=>json($roles),
//          'role'=>$role,
//          'role1st'=>$roles[0],
//  
//          //向前端权限变量赋值
//          'authArray'=>$this->auth, 
//          
//          'numIssPatEdit'=>$numIssPatEdit,
//          'numIssPatAudit'=>$numIssPatAudit,
//          'numIssPatApprove'=>$numIssPatApprove,
//          'numIssPatExecute'=>$numIssPatExecute,
//          'numIssPatMaintain'=>$numIssPatMaintain,
//          'numIssPatDone'=>$numIssPatDone,
//          'numTotal'=>$numTotal,
//          'numPatRenewTotal'=>$numPatRenewTotal,
//      	
//        ]);
//      return view('dashboard2'.DS.'issThe'.DS.'theNavPills');
      return ':)<br> issthe 模块开发中……';
     
    }
    
    public function issPro(Request $request)
    {
       
      return ':)<br> issPro 模块开发中……';
     
    }
}
