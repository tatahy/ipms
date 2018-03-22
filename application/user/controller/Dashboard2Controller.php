<?php
namespace app\user\controller;

use think\Request;
use think\Session;
use think\View;
use think\File as FileObj; 

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
    
    private $today=null;
    
    private $now=null;
    // 初始化
    protected function _initialize()
    {
        $this->username=Session::get('username');
        $this->pwd=Session::get('pwd');
        $this->log=Session::get('log');
        $this->roles=Session::get('role');
        $this->dept=Session::get('dept');
        $this->today=date('Y-m-d');
        $this->now=date("Y-m-d H:i:s");
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
    
    //上传附件文件到temp目录
    public function uploadAttTemp(Request $request,AttinfoModel $attMdl)
    {
      $attData=array('uploaddate'=>$this->now,
                        'uploader'=>$request->param('uploader'),
                        'atttype' =>$request->param('attType'),
                        'attmap_id' =>$request->param('attmap_id'),
                        'attmap_type' =>$request->param('attmap_type'),
                        'name' =>$request->param('attName'),
                        'rolename' =>$request->param('attRoleName'),
                        'deldisplay' =>$request->param('deldisplay')
      );
      
      //应用AttinfoModel中定义的fileUpdateTemp()方法上传附件文件到temp目录
      $att_return=$attMdl->fileUploadTemp($attData,$request->file('attFile'));
      
      return $att_return;
      
    }
    
     //上传附件文件到指定目录
     //参数1：$fileSet，类型：对象。值：不为空。说明：拟上传的文件对象
     //参数2：$dirName，类型：字符。值：不为空。说明：上传文件拟放入的目录名称
     //参数3：$attId，类型：字符。值：不为空。说明：拟记录上传文件路径的记录id
    private function _uploadAtt($fileSet,$dirName,$attId)
    {
      
      if(!empty($fileSet)){
            // 移动到框架根目录的uploads/ 目录下,系统重新命名文件名
            $info = $fileSet->validate(['size'=>10485760,'ext'=>'jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,rar'])
                        ->move(ROOT_PATH.DS.'uploads'.DS.$dirName);
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
            
            $path= '..'.DS.'uploads'. DS.$dirName.DS.$info->getSaveName();
            
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
        $iss=array('id'=>'','topic'=>'','abstract'=>'');
        //查询当前用户已上传的所有附件信息
        $att= AttinfoModel::all(['attmap_id'=>0,'uploader'=>$this->username,'rolename'=>'_EDIT','deldisplay'=>1]);
        $pat=array('id'=>'','topic'=>'','patowner'=>'','otherinventor'=>'','inventor'=>'');
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
    public function issPatOprt(Request $request,IssinfoModel $issMdl,IssrecordModel $issRdMdl,
                                PatinfoModel $patMdl,PatrecordModel $patRdMdl,
                                AttinfoModel $attMdl)
    {
      $this->_loginUser();
      
      // $oprt接收前端页面传来的oprt值
      if(!empty($request->param('oprt'))){
        $oprt=$request->param('oprt');
      }else{
        $oprt='_NONE';
      }
      
      // $auth接收前端页面传来的auth值
      if(!empty($request->param('auth'))){
        $auth=$request->param('auth');
      }else{
        $auth='_NONE';
      }
      
      // $patId接收前端页面传来的patId值
      if(!empty($request->param('patId'))){
        $patId=$request->param('patId');
      }else{
        $patId=0;
      }
      
      // $issId接收前端页面传来的issId值
      if(!empty($request->param('issId'))){
        $issId=$request->param('issId');
      }else{
        $issId=0;
      }
      
     //如果要通过$request->param()获取的数据为数组，要加上 /a 修饰符才能正确获取。
      if(!empty($request->param('attId/a'))){
        $arrAttId=$request->param('attId/a');
        $arrAttFileName=$request->param('attFileName/a');
        $arrAttFileObjStr=$request->param('attFileObjStr/a');
      }else{
        $arrAttId=array(0);
        $arrAttFileName=array(0);
        $arrAttFileObjStr=array(0);;
      }
      
      //变量赋初值
      $issData=array('z'=>0);
      $issDataPatch=array('z'=>0);
      $issRdData=array('z'=>0);
      $issRdDataPatch=array('z'=>0);
      $issId_return=0;
      
      $patData=array('z'=>0);
      $patDataPatch=array('z'=>0);
      $patRdData=array('z'=>0);
      $patRdDataPatch=array('z'=>0);
      $patId_return=0;
      
      $attData=array('z'=>0);
      $attDataPatch=array('z'=>0);
      $attId_return=0;
      
     // $issMdlOprt='';
//      $patMdlOprt='';
//      $attMdlOprt='';
      
      $oprtCHNStr='';
      
      $msg="";
      //$tplFile='dashboard2'.DS.'issPatAuthSingle'.DS;
      
      switch($oprt){
        //“_EDIT”权限拥有的操作
        case'_ADDNEW':
          //patId=0,issId=0
          $oprtCHNStr='新增';
          $attDataPatch=array('deldisplay'=>1);
          
        break;
        
        case'_SUBMIT':
          //patId!=0,issId!=0
          $oprtCHNStr='提交';
          
          $patDataPatch=array('status'=>'内审',
                              'submitdate'=>$this->now,
                              );
          $patRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利《'.$request->param('patTopic').'》提交。待内部审查<br>',
                                );
          $issDataPatch=array('status'=>'待审核',
                              'submitdate'=>$this->now,
                              );
          $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》提交审核。',
                                );
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_DELETE':
          //patId!=0,issId!=0
          $oprtCHNStr='删除';
        break;
        
        case'_UPDATE':
          //patId!=0,issId!=0
          $oprtCHNStr='更新';

          $attDataPatch=array('deldisplay'=>1);
          
        break;
        //“_AUDIT”权限拥有的操作
        case'_PASS':
          //patId!=0,issId!=0
          $oprtCHNStr='审核';

          $issDataPatch=array('status'=>'审核通过',
                              'auditrejectdate'=>$this->now,
                              );
          $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审核结果：</br>
                                              <span class="label label-success">审核通过</span></br>'
                                );
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_FAIL':
          //patId!=0,issId!=0
          $oprtCHNStr='审核';
          
          $issDataPatch=array('status'=>'审核未通过',
                              'auditrejectdate'=>$this->now,
                              );
          $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审核结果：</br>
                                              <span class="label label-warning">审核未通过</span></br>
                                              审核意见：<span class="label label-primary">'.$request->param('auditMsg').'</span></br>',
                                );
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_MODIFY':
          //patId!=0,issId!=0
          $oprtCHNStr='审核';
          
          $patDataPatch=array('status'=>'内审修改');
          $patRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利《'.$request->param('patTopic').'》审核结果：</br>
                                              <span class="label label-primary">内审修改</span></br>
                                              审核意见：<span class="label label-primary">'.$request->param('auditMsg').'</span></br>',
                                );
          $issDataPatch=array('status'=>'返回修改',
                              'auditrejectdate'=>$this->now,
                              );
          $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审核结果：</br>
                                              <span class="label label-primary">返回修改</span></br>
                                              审核意见：<span class="label label-primary">'.$request->param('auditMsg').'</span></br>',
                                );
          $attDataPatch=array('deldisplay'=>0);
         
        break;
        //“_APPROVE”权限拥有的操作
        case'_PERMIT':
          //patId!=0,issId!=0
          $oprtCHNStr='审批';
          //根据iss.status的值进行赋值
          $issSet=$issMdl->where('id',$issId)->find();
          
          if($issSet->status=='审核通过' || $issSet->status=='审核未通过' ){
            $patDataPatch=array('status'=>'拟申报(内审批准)',
                                'auditrejectdate'=>$this->now,
                                );
            $patRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利《'.$request->param('patTopic').'》审批结果：</br>
                                              <span class="label label-success">拟申报(内审批准)</span></br>'
                                  );
            $issDataPatch=array('status'=>'批准申报');
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审批结果：</br>
                                              <span class="label label-success">批准申报</span></br>'
                                  );
            
          }else if($issSet->status=='变更申请'){
            $issDataPatch=array('status'=>'准予变更',
                                'auditrejectdate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审批结果：</br>
                                              <span class="label label-success">准予变更</span></br>'
                                  );
          }else{
            //$issSet->status=='拟续费'
            $issDataPatch=array('status'=>'准予续费',
                                'auditrejectdate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审批结果：</br>
                                              <span class="label label-success">准予续费</span></br>'
                                  );
          }
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_VETO':
          //patId!=0,issId!=0
          $oprtCHNStr='审批';
          
           //根据iss.status的值进行赋值
          $issSet=$issMdl->where('id',$issId)->find();
          
          if($issSet->status=='审核通过' || $issSet->status=='审核未通过' ){
            $patDataPatch=array('status'=>'内审否决');
            $patRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利《'.$request->param('patTopic').'》审批结果：</br>
                                              <span class="label label-danger">内审否决</span></br>
                                              审批意见：<span class="label label-primary">'.$request->param('approveMsg').'</span></br>'
                                  );
            $issDataPatch=array('status'=>'否决申报',
                                'auditrejectdate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审批结果：</br>
                                              <span class="label label-danger">否决申报</span></br>
                                              审批意见：<span class="label label-primary">'.$request->param('approveMsg').'</span></br>'
                                  );
            
          }else if($issSet->status=='变更申请'){
            $issDataPatch=array('status'=>'否决变更',
                                'auditrejectdate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审批结果：</br>
                                              <span class="label label-danger">否决变更</span></br>
                                              审批意见：<span class="label label-primary">'.$request->param('approveMsg').'</span></br>'
                                  );
          }else{
            //$issSet->status=='拟续费'
            $patDataPatch=array('status'=>'放弃续费');
            $patRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利《'.$request->param('patTopic').'》续费审批结果：</br>
                                              <span class="label label-default">放弃续费</span></br>
                                              审批意见：<span class="label label-default">'.$request->param('approveMsg').'</span></br>'
                                  );
            $issDataPatch=array('status'=>'放弃续费',
                                'auditrejectdate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审批结果：</br>
                                              <span class="label label-default">放弃续费</span></br>
                                              审批意见：<span class="label label-default">'.$request->param('approveMsg').'</span></br>'
                                  );
          }
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_COMPLETE':
          //patId!=0,issId!=0
         $oprtCHNStr='审批';
          
          //根据iss.status的值进行赋值
          //$issSet=$issMdl->where('id',$issId)->find();
          
          //$issSet->status=='审核通过' || $issSet->status=='审核未通过' ){
          $patDataPatch=array('status'=>'内审修改');
          $patRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利《'.$request->param('patTopic').'》审批结果：</br>
                                              <span class="label label-warning">内审修改</span></br>
                                              审批意见：<span class="label label-primary">'.$request->param('approveMsg').'</span></br>'
                                  );
          $issDataPatch=array('status'=>'修改完善',
                                'auditrejectdate'=>$this->now,
                                );
          $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审批结果：</br>
                                              <span class="label label-warning">修改完善</span></br>
                                              审批意见：<span class="label label-primary">'.$request->param('approveMsg').'</span></br>'
                                  );
            
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        //“_EXECUTE”权限拥有的操作
        case'_ACCEPT':
          //patId!=0,issId!=0
          $oprtCHNStr='领受';
        
          $issDataPatch=array('status'=>'申报执行',
                              'operatestartdate'=>$this->now,
                              );
          $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报开始</br>'
                                );
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_REFUSE':
          //patId!=0,issId!=0
          $oprtCHNStr='变更申述';
         
          $issDataPatch=array('status'=>'变更申请',
                              'executerchangeto'=>$this->username,
                              );
          $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报变更申请</br>
                                变更申请意见：<span class="label label-warning">'.$request->param('executeMsg').'</span></br>'
                                );
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_REPORT':
          //patId!=0,issId!=0
          $oprtCHNStr='申报执行报告';
          
          $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报执行报告</br>
                                报告简述：<span class="label label-primary">'.$request->param('executeMsg').'</span></br>'
                                );
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_FINISH':
          //patId!=0,issId!=0
          $oprtCHNStr='申报提交复核';
          
          $issDataPatch=array('status'=>'申报复核');
          $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报提交复核</br>
                                提交内容简述：<span class="label label-primary">'.$request->param('executeMsg').'</span></br>'
                                );
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        //“_MAINTAIN”权限拥有的操作
        case'_APPLY':
          //patId!=0,issId!=0
          $oprtCHNStr='<span class="label label-primary">申报正式提交</span>';
          
           //根据iss.status的值进行赋值
          $issSet=$issMdl->where('id',$issId)->find();
          
          if($issSet->status=='申报复核'){
            $patDataPatch=array('status'=>'申报',
                                'applydate'=>$this->now,
                                );
            $patRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利《'.$request->param('patTopic').'》申报提交</br>
                                                申报提交简述：<span class="label label-primary">'.$request->param('maintainMsg').'</span></br>'
                                  );
            $issDataPatch=array('status'=>'申报提交',
                                'applydate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报提交</br>
                                                申报提交简述：<span class="label label-primary">'.$request->param('maintainMsg').'</span></br>'
                                  );
            
          }else{
            //$issSet->status=='准予续费'
            $patDataPatch=array('status'=>'续费中',
                                'renewapplydate'=>$this->now,
                                );
            $patRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利《'.$request->param('patTopic').'》续费申报提交</br>
                                              申报提交简述：<span class="label label-primary">'.$request->param('maintainMsg').'</span></br>'
                                  );
            $issDataPatch=array('status'=>'续费提交',
                                'applydate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》续费申报提交</br>
                                              申报提交简述：<span class="label label-primary">'.$request->param('maintainMsg').'</span></br>'
                                  );
          }
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_IMPROVE':
          //patId!=0,issId!=0
      
           //根据iss.status的值进行赋值
          $issSet=$issMdl->where('id',$issId)->find();
          
          if($issSet->status=='申报复核'){
            $oprtCHNStr='申报修改';
            
            $issDataPatch=array('status'=>'申报修改');
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报复核结果：</br>
                                                申报修改</br>
                                                申报修改原因：<span class="label label-primary">'.$request->param('maintainMsg').'</span></br>'
                                  );
            
          }else{
            //$issSet->status=='申报提交'
            $oprtCHNStr='<span class="label label-warning">申报修改</span>';
            
            $patDataPatch=array('status'=>'申报修改',
                                'modifydate'=>$this->now,
                                );
            $patRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利《'.$request->param('patTopic').'》申报提交结果：</br>'.$oprtCHNStr.'</br> 
                                              申报修改原因：<span class="label label-warning">'.$request->param('maintainMsg').'</span></br>'
                                  );
            $issDataPatch=array('status'=>'申报修改',
                                'resultdate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》</br>'.$oprtCHNStr.'</br> 
                                              申报修改原因：<span class="label label-warning">'.$request->param('maintainMsg').'</span></br>'
                                  );
          }
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_AUTHORIZE':
          //patId!=0,issId!=0
          $oprtCHNStr='<span class="label label-success">授权</span>';
          //根据iss.status的值进行赋值
          $issSet=$issMdl->where('id',$issId)->find();
          
          if($issSet->status=='申报提交'){            
            $patDataPatch=array('status'=>'授权',
                                'authrejectdate'=>$this->now,
                                );
            $patRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利《'.$request->param('patTopic').'》申报提交结果：</br>'.$oprtCHNStr.'</br>'
                                  );
            $issDataPatch=array('status'=>'专利授权',
                                'resultdate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报提交结果：</br>'.$oprtCHNStr.'</br>'
                                  );
            
          }else{
            //$issSet->status=='续费提交'
            $patDataPatch=array('status'=>'续费授权',
                                //'authrejectdate'=>$request->param('xxDate'),
                                //'nextrenewdate'=>$request->param('xxDate'),
                                //'renewdeadlindate'=>$request->param('xxDate'),
                                );
            $patRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利《'.$request->param('patTopic').'》续费申报提交结果：</br>'.$oprtCHNStr.'</br>'
                                  );
            $issDataPatch=array('status'=>'专利授权',
                                'resultdate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》续费申报提交结果：</br>'.$oprtCHNStr.'</br>'
                                  );
          }
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_REJECT':
          //patId!=0,issId!=0
          $oprtCHNStr='<span class="label label-danger">驳回</span>';
          //根据iss.status的值进行赋值
          $issSet=$issMdl->where('id',$issId)->find();
          
          if($issSet->status=='申报提交'){            
            $patDataPatch=array('status'=>'驳回',
                                'authrejectdate'=>$this->now,
                                );
            $patRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利《'.$request->param('patTopic').'》申报提交结果：</br>'.$oprtCHNStr.'</br>
                                  驳回原因：<span class="label label-danger">'.$request->param('maintainMsg').'</span></br>'
                                  );
            $issDataPatch=array('status'=>'专利驳回',
                                'resultdate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报提交结果：</br>'.$oprtCHNStr.'</br>
                                  驳回原因：<span class="label label-danger">'.$request->param('maintainMsg').'</span></br>'
                                  );
          }else{
            //$issSet->status=='续费提交'
            $patDataPatch=array('status'=>'驳回续费',
                                'renewrejectdate'=>$request->param('xxDate'),
                                //'nextrenewdate'=>$request->param('xxDate'),
                                //'renewdeadlindate'=>$request->param('xxDate'),
                                );
            $patRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利《'.$request->param('patTopic').'》续费申报提交结果：</br>'.$oprtCHNStr.'</br>
                                  驳回原因：<span class="label label-danger">'.$request->param('maintainMsg').'</span></br>'
                                  );
            $issDataPatch=array('status'=>'专利驳回',
                                'resultdate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》续费申报提交结果：</br>'.$oprtCHNStr.'</br>
                                  驳回原因：<span class="label label-danger">'.$request->param('maintainMsg').'</span></br>'
                                  );
          }
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_CLOSE':
          //patId!=0,issId!=0
          $oprtCHNStr='<span class="label label-default">完结</span>';
          //根据iss.status的值进行赋值
          $issSet=$issMdl->where('id',$issId)->find();
                    
          if($issSet->status=='放弃续费' ){            
            $patDataPatch=array('status'=>'超期无效',
                                'renewabandondate'=>$this->now,
                                );
            $patRdDataPatch=array('act'=>'<span class="label label-default">超期无效</span>',
                                    'actdetail'=>'专利《'.$request->param('patTopic').'》</br><span class="label label-default">超期无效</span></br>'
                                  );
            
          }else{
            //$issSet->status=='放弃续费' || $issSet->status=='续费授权' 
            $patDataPatch=array('z'=>0);
            $patRdDataPatch=array('z'=>0);
           
          }
          $issDataPatch=array('status'=>'完结',
                              'finishdate'=>$this->now,
                              );
          $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》'.$oprtCHNStr.'</br>'
                                );
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_ADDRENEW':
          //patId!=0,issId=0
          if($request->param('returnType')=='_JSON'){
            return json(array_merge($patMdl->where('id',$request->param('patId'))->find()->toArray(),
                              array("today"=>$this->today,"username"=>$this->username,"deptMaintainer"=>$this->dept)));
          }else{
            $oprtCHNStr='续费报告';
            
            $patDataPatch=array('z'=>0);
            $patRdDataPatch=array('z'=>0);
            $issDataPatch=array('z'=>0);
            $issRdDataPatch=array('z'=>0);
            $attDataPatch=array('deldisplay'=>0);
          }
        break;
        
        //
        
      }
      
      //对5个数据表分4类情况进行操作
      if($oprt=='_ADDNEW'){
        //patId=0,issId=0
        
        if($patId==0){
          //1.patinfo表新增
          $patData=array(
                'topic'=>$request->param('patTopic'),
                'pattype'=>$request->param('patType'),
                'patowner'=>$request->param('patOwner'),
                'inventor'=>$request->param('patInventor'),
                'otherinventor'=>$request->param('patOtherInventor'),
                'author'=>$request->param('patAuthor'),
                'dept'=>$request->param('dept'),
                
                'status'=>'填报',
                'addnewdate'=>$this->now,
                
          );
          //新增                  
          $patId_return = $patMdl->patCreate(array_merge($patData,$patDataPatch));
          return json(array('patId'=>$patId_return));
        }else{
          //2.patrecord表新增
          $msg.='专利【新增】成功。<br>';
            $patSet=$patMdl->where('id',$patId)->find();
            
            $patRdData=array(
                'patinfo_id'=>$patId,
                'num'=>$patSet->patnum,
                'act'=>'填报',
                'actdetail'=>'专利《'.$patSet->topic.'》新增填报',
                'acttime'=>$this->now,
                'username'=>$this->username,
                'rolename'=>$auth,
                
            );
            //新增patRd
            $patRdId = $patRdMdl->patRdCreate(array_merge($patRdData,$patRdDataPatch));
        
        //3.issinfo表新增
            $issData=array(
                    'issmap_type'=>$request->param('issType'),
                    'topic'=>$request->param('issPatTopic'),
                    'abstract'=>$request->param('issPatAbstract'),
                    
                    'issmap_id'=>$patId,
                    'num_id'=>$patId,
                    'addnewdate'=>$this->now,
                    'status'=>'填报',
                    'writer'=>$this->username,
                    'dept'=>$this->dept,
            
            );
            //新增issPat
            $issId_return = $issMdl->issCreate(array_merge($issData,$issDataPatch)); 
        
        //4.issrecord表新增
            //取出新增的isspat内容
            $issSet = $issMdl->where('id',$issId_return)->find();
            $msg.='专利事务【新增】成功。<br>';  
            
            $issRdData=array(
                'issinfo_id'=>$issId_return,
                'num'=>$issSet->issnum,
                'act'=>'填报',
                'actdetail'=>'专利事务《'.$issSet->topic.'》新增填报',
                'acttime'=>$this->now,
                'username'=>$this->username,
                'rolename'=>$auth,               
            );
            //新增issRd
            $issRdId = $issRdMdl->issRdCreate(array_merge($issRdData,$issRdDataPatch));
        }
      }else if($oprt=='_ADDRENEW'){
        //patId!=0,issId=0
        
        //1.patinfo表无需更新记录

        //2.patrecord表无需新增记录
        
        //3.issinfo表新增记录
        $issData=array(
                'issmap_type'=>$request->param('issType'),
                'topic'=>$request->param('issPatTopic'),
                'abstract'=>$request->param('issPatAbstract'),
                
                'issmap_id'=>$patId_return,
                'addnewdate'=>$this->now,
                'status'=>'拟续费',
                'writer'=>$this->username,
                'dept'=>$this->dept,
        
        );
        //新增
        $issId_return = $issMdl->issCreate(array_merge($issData,$issDataPatch));  
        
        //4.issrecord表新增
        //取出新增的isspat内容
        $issSet = $issMdl->where('id',$issId_return)->find();
        $msg.='专利事务【新增续费】成功。<br>';  
            
        $issRdData=array(
                'issinfo_id'=>$issId_return,
                'num'=>$issSet->issnum,
                'act'=>'拟续费',
                'actdetail'=>'专利事务《'.$issSet->topic.'》新增填报',
                'acttime'=>$this->now,
                'username'=>$this->username,
                'rolename'=>$auth,               
        );
        //新增
        $issRdId = $issRdMdl->issRdCreate(array_merge($issRdData,$issRdDataPatch));
        
      }else if($oprt=='_DELETE'){
        //$patId!=0,$issId!=0
        //1.删除pat
        $patId_return=$patMdl->patDelete($patId);
        
        //2.删除patRd
        $patRdId_return=$patRdMdl->where('patinfo_id',$patId)->delete();
        
        //3.删除iss
        $issId_return=$issMdl->issDelete($issId);
        
        //4.删除issRd
        $issRdId_return=$issRdMdl->where('issinfo_id',$issId)->delete();
        
        //5.删除att
        $attId_return=$attMdl->where('attmap_id',$issId)->delete();
        
        return json(array('msg'=>$msg,'topic'=>$request->param('issPatTopic'),'patId'=>$patId));
      
      }else{
        //$patId!=0,$issId!=0
        $patSet=$issMdl->where('id',$patId)->find();
        $issSet=$issMdl->where('id',$issId)->find();
        
        //1.删除pat
        $patId_return=$patMdl->patDelete($patId);
        
        //2.删除patRd
        $patRdId_return=$patRdMdl->where('patinfo_id',$patId)->delete();
        
        //3.删除iss
        $issId_return=$issMdl->issDelete($issId);
        
        //4.删除issRd
        $issRdId_return=$issRdMdl->where('issinfo_id',$issId)->delete();
        
      }
      
      //5.attinfo表更新
      //循环更新attMdl,将文件从现有的‘temp’目录移动到指定目录
      for($i=0;$i<count($arrAttId);$i++){
        
        $fileStr=$arrAttFileObjStr[$i];
        $name=$arrAttFileName[$i];
        
        //有‘temp’字符串才移动到指定目录
        if(substr_count($fileStr,'temp')){
          $newDir='..'.DS.'uploads'.DS.$issSet->issnum;
          
          //调用AttinfoModel中定义的fileMove()方法，返回true后才更新Attinfo表
          if($attMdl->fileMove($fileStr,$name,$newDir)){
            
            $attData=array('num_id'=>$issSet->issnum,
                            'attmap_id'=>$issSet->id,
                            'attpath'=>$newDir.DS.$name,
                          //  'deldisplay'=>0
                            );
                
            //更新att
            $attId = $attMdl->attUpdate(array_merge($attData,$attDataPatch),$arrAttId[0]);
                          
            $msg.="附件".$fileStr."移动成功</br>"; 
          }else{
            $msg.="附件".$fileStr."移动失败</br>"; 
          }
        }
      } 
//  <----------------------------------------------------------------------------------------->
       
      //return $msg;
      //return json(array('msg'=>$msg,'btnHtml'=>$btnHtml,'topic'=>$request->param('issPatTopic')));
      return json(array('msg'=>$msg,'topic'=>$request->param('issPatTopic'),'patId'=>$patId));
      //return $this->issPatAuth($request);//参数不够，不会产生分页。
    }
    
    //为前端显示PatRenew模板准备，1.数据库数据；2.向模板变量赋值；3.选择模板文件PatRenew.html返回前端
     public function patRenew(Request $request,PatinfoModel $patMdl)
    {
      $this->_loginUser();
      
      //调用模型文件Patinfo.php中定义的patRenew方法找出合适的pat。
      $pat= $patMdl->patRenew();
      
            
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
      
   
      return ':)<br> issThe 模块开发中……';
     
    }
    
    public function issPro(Request $request)
    {
       
      return ':)<br> issPro 模块开发中……';
     
    }
    
     public function test(Request $request,AttinfoModel $attMdl)
    {
      $msg='</br>';
      if(!empty($request->param('patId'))){
        $patId=$request->param('patId');
      }else{
        $patId=0;
      }
      
      if($request->param('oprt')=='_ADDNEW'){
        $patId=1;
      }     
      
      $fileStr='';
      $name='';
      $newDir='..'.DS.'uploads'.DS.'xx';  
      
      //如果要获取的数据为数组，要加上 /a 修饰符才能正确获取。
      if(!empty($request->param('attId/a'))){
        $arrAttId=$request->param('attId/a');
        $arrAttFileName=$request->param('attFileName/a');
        $arrAttFileObjStr=$request->param('attFileObjStr/a');
        
        $fileStr=$arrAttFileObjStr[0];
        $name=$arrAttFileName[0];

      }else{
        $arrAttId=array(0);
        $arrAttFileName=array(0);
        $name=0;
      }
      
      //有‘temp’字符串才移动到指定目录
        if(substr_count($fileStr,'temp')){
          
          //调用AttinfoModel中定义的fileMove()方法，返回true后才更新Attinfo表
          if($attMdl->fileMove($fileStr,$name,$newDir)){
            
            $attData=array('num_id'=>0,
                            'attmap_id'=>0,
                            'attpath'=>$newDir.DS.$name,
                            'deldisplay'=>0
                            );
                
            //更新att
            $attId = $attMdl->attUpdate($attData,$arrAttId[0]);
                          
            $msg.="附件".$fileStr."移动成功</br>"; 
          }else{
            $msg.="附件".$fileStr."移动失败</br>"; 
          }
        }
      
      $data=array('msg'=>$msg,'topic'=>$request->param('issPatTopic'),'patId'=>$patId,'attId'=>$arrAttId);
      return json($data);
      //return $data;
    } 
}
