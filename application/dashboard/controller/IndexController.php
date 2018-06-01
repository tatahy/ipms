<?php
namespace app\dashboard\controller;

use think\Request;
use think\Session;
use think\View;
use think\File as FileObj; 

use app\dashboard\model\User as UserModel;
use app\dashboard\model\Rolety as RoletyModel;
use app\dashboard\model\Issinfo as IssinfoModel;
use app\dashboard\model\Issrecord as IssrecordModel;
use app\dashboard\model\Patinfo as PatinfoModel;
use app\dashboard\model\Patrecord as PatrecordModel;
use app\dashboard\model\Attinfo as AttinfoModel;

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
    
    private $today=null;
    
    private $now=null;
    // 初始化
    protected function _initialize()
    {
        $this->userId=Session::get('userId');
        $this->username=Session::get('username');
        $this->pwd=Session::get('pwd');
        $this->log=Session::get('log');
        $this->roles=Session::get('role');
        $this->dept=Session::get('dept');
        $this->authArr=Session::get('authArr');
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
        $this->authArr=$userlg->authority;
      }    
    }
    
    public function index(Request $request,IssinfoModel $issMdl,$auth='done')
    {
        $this->_loginUser();
       
        if(!empty($request->param('auth'))){
           $auth=$request->param('auth');
        }
        
        //调用模型Issinfo中定义的issPatNum($useId[,$auth])方法得到对应的issPat数量
        $numIssPat=$issMdl->issPatNum($this->userId);
        
        $numIssPro=$issMdl::where('issmap_type','like','%_ISST_PRO%')->count();
        $numIssThe=$issMdl::where('issmap_type','like','%_ISST_THE%')->count();
    
         // 模板变量赋值        
        $this->assign([
          'home'=>$request->domain(),
          'username'=>$this->username,
         
          'numIssPat'=>$numIssPat,
          'numIssPro'=>$numIssPro,
          'numIssThe'=>$numIssThe,
          
          'year'=>date('Y')
      	
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
    
    // 向前端返回查询的Executer信息
     public function selectExecuter()
    {
      $this->_loginUser();
      //查出所有未禁用的用户
      $user=UserModel::where('enable','1')->order('dept', 'asc')->select();
      $executer=array();
      foreach($user as $v){
        //用户在operator用户组(usergroupid=4)中：即是usergroup_id字段含有'4'
        if(strstr($v->usergroup_id,'4')){
            array_push($executer,$v);
        }
      }
      // 将数组转化为json
      return json($executer);
      
    }
    
    // 根据前端发送的模板文件名参数，选择对应的页面文件返回
    public function tplFile(Request $request,$auth='',$id='')
    {
      $this->_loginUser();
      //前端发送的是锚点值
      if(!empty($request->param('sId'))){
        $tplFile=$request->param('sId');
      }else{
        $tplFile='#issPat';
      }
      
      if(!empty($request->param('auth'))){
        $auth=$request->param('auth');
      }
      
      if(!empty($request->param('id'))){
        $id=$request->param('id');
      }
      
      //返回模板文件
      if(substr($tplFile,0,1)=='#'){
        $tplFile=substr($tplFile,1);
        $this->redirect($tplFile,['auth' =>$auth,'id'=>$id]);
      }else{
        return '模板文件不存在。';
      }
    
    }
    
     public function issPat(Request $request,IssinfoModel $issMdl,$auth='done',$issId='')
    {
       $this->_loginUser();
       
       // $auth接收前端页面传来的auth值
        if(!empty($request->param('auth'))){
          $auth=$request->param('auth');
        }else{
          foreach($this->authArr['iss'] as $key=>$value){
            if($value){
              $auth=$key;
              break;
            }
          }
        }
        // $issId接收前端页面传来issId值
       if(!empty($request->param('id'))){
        $issId=$request->param('id');
       }

         // 模板变量赋值        
        $this->assign([
          'home'=>$request->domain(),
          'username'=>$this->username,
  
          //向前端权限变量赋值
          //所有权限
          'authArray'=>$this->authArr,
          //当前权限
          'auth'=>$auth,  

          'numIssPatEdit'=>$issMdl->issPatNum($this->userId,'edit'),
          'numIssPatAudit'=>$issMdl->issPatNum($this->userId,'audit'),
          'numIssPatApprove'=>$issMdl->issPatNum($this->userId,'approve'),
          'numIssPatExecute'=>$issMdl->issPatNum($this->userId,'execute'),
          'numIssPatMaintain'=>$issMdl->issPatNum($this->userId,'maintain'),
          'numIssPatDone'=>$issMdl->issPatNum($this->userId,'done'),
          'numTotal'=>$issMdl->issPatNum($this->userId,'total'),
          'numPatRenewTotal'=>$issMdl->issPatNum($this->userId,'patrenew'),
          
          'issId'=>$issId,
      	
        ]);
      return view();
      //return ':)<br> issthe 模块开发中……';
     
    } 
    
    //根据前端传来的权限，选择返回前端的模板文件及内容
    public function issPatAuth(Request $request,$auth='done',$issId='')
    {
      $this->_loginUser();
      
      // $role接收前端页面传来的auth值
      if(!empty($request->param('auth'))){
        $auth=$request->param('auth');
      }
      
       // $issId接收前端页面传来issId值
       if(!empty($request->param('id'))){
        $issId=$request->param('id');
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
          //edit
          case 'edit':
            $map['status'] =['in',['填报','返回修改','修改完善']];
            $map['dept'] =$this->dept;
            $map['writer']=$this->username;
            //$tplFile='issPatEdit';
            $tplFile='edit';
          break;
          //audit
          case 'audit':
            $map['status'] ='待审核';
            $map['dept'] =$this->dept;
            //$tplFile='issPatAudit';
            $tplFile='audit';
          break;
          //approve
          case 'approve':
            $map['status'] =['in',['审核未通过','审核通过','变更申请','拟续费']];
            //$tplFile='issPatApprove';
            $tplFile='approve';
          break;
          //execute
          case 'execute':
            $map['executer'] =$this->username;
            $map['status'] =['in',['批准申报','申报执行','申报修改','准予变更','否决变更']];
            $map['dept'] =$this->dept;
            
            //$tplFile='issPatExecute';
            $tplFile='execute';
          break;
          //maintain
          case 'maintain':
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
          //done
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
     
     if($issId=='' && !empty($issPatTotal[0])){
        $issId=$issPatTotal[0]->id;
     }
      
      //返回数据还是模板文件,‘0’为模板文件，‘1’为数据
      if($returnType){
        //响应前端的请求，返回前端要求条件的issPat数量
        return ($numTotal);
      }else{
        $this->assign([
              'home'=>$request->domain(),
              //"destr".$this->authArr['isspro']['edit'],
              
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
              'auth'=>$auth,
              'issId'=>$issId,          
              
        ]);
        
       // return view($tplFile);
        return view('index'.DS.'issPatAuth'.DS.$tplFile);
      }
    }
    
    //根据前端传来的权限，选择返回前端的模板文件及内容
    public function issPatAuthSingle(Request $request,$auth='done')
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
      }
      
      //选择模板文件名
      switch($auth){
        //edit
        case 'edit':
          $tplFile='editSingle';
        break;
        //audit
        case 'audit':
          $tplFile='auditSingle';
        break;
        //approve
        case 'approve':
          $tplFile='approveSingle';
        break;
        //execute
        case 'execute':
          $tplFile='executeSingle';
        break;
        //maintain
        case 'maintain':
          $tplFile='maintainSingle';
        break;
        //done
        default:
          $tplFile='doneSingle';
        break;
        
      }
      
      if($oprt=='_ADDNEW'){
        $iss=array('id'=>0,'topic'=>'','abstract'=>'');
        //查询当前用户已上传的所有附件信息
        $att= AttinfoModel::all(['attmap_id'=>0,'uploader'=>$this->username,'rolename'=>'edit','deldisplay'=>1]);
        $pat=array('id'=>0,'topic'=>'','patowner'=>'','otherinventor'=>'','inventor'=>'');
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
      return view('index'.DS.'issPatAuthSingle'.DS.$tplFile);
    }
    
    //根据前端传来的操作类型，对数据库进行操作
    //结构：1.变量赋初值  //结构2.21个oprt接收前端页面传来的数据，分别对变量赋值再进行数据库表的操作
    public function issPatOprt(Request $request,IssinfoModel $issMdl,IssrecordModel $issRdMdl,
                                PatinfoModel $patMdl,PatrecordModel $patRdMdl,AttinfoModel $attMdl)
    {
      $this->_loginUser();
      
      //结构：1.变量赋初值 
      // $oprt接收前端页面传来的oprt值
      $oprt=empty($request->param('oprt'))?'_NONE':$request->param('oprt');
      
      // $auth接收前端页面传来的auth值,表示rolename（映射“用户组名”）
      $auth=empty($request->param('auth'))?'done':$request->param('auth');
      
      // $patId接收前端页面传来的patId值
      $patId=empty($request->param('patId'))?0:$request->param('patId');
      
      // $issId接收前端页面传来的issId值
      $issId=empty($request->param('issId'))?0:$request->param('issId');
      
     //接收前端页面传来的附件文件信息
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
      
      //涉及数据库5个数据表的变量赋初值
      $issData=array('z'=>0);
      $issRdData=array('z'=>0);
      if($issId){
        //issrecord新增时需patch的数据
        $issRdDataPatch=array('acttime'=>$this->now,
                              'username'=>$this->username,
                              'rolename'=>$auth,
                              'issinfo_id'=>$issId,
                              'num'=>$issMdl::get($issId)->issnum
                              );
        $issStatus=$issMdl::get($issId)->status;
      }else{
        $issRdDataPatch=array('z'=>0);
        $issStatus=0;
        
      }
      $issReturn=0;
     
      $patData=array('z'=>0);
      $patRdData=array('z'=>0);
      if($patId){
        //patrecord新增时需patch的数据
        $patRdDataPatch=array('acttime'=>$this->now,
                              'username'=>$this->username,
                              'rolename'=>$auth,
                              'patinfo_id'=>$patId,
                              'num'=>$patMdl::get($patId)->patnum
                              );
      }else{
        $patRdDataPatch=array('z'=>0);
      }
      $patReturn=0;

      $attData=array('z'=>0);
      $attDataPatch=array('z'=>0);
      
      $oprtCHNStr='';

      $msg='';
      
//<结构2.----------------------------------------------------------------------------------------->
//5个权限总计21个oprt接收前端页面传来的数据，分别对变量赋值再进行数据库表的操作
      switch($oprt){
        //“_EDIT”权限拥有的操作
        case'_ADDNEW':
          //patId=0,issId=0
          $oprtCHNStr='新增';
                    
          if($patId==0){
            //专利名称是否已存在
            if($patMdl::where('topic','like',$request->param('patTopic'))->find()){
              $msg='专利名称：《'.$request->param('patTopic').'》已存在，请重新填写';
            }else{
              //1.patinfo表新增   
              //patData
              $patData=array('topic'=>$request->param('patTopic'),
                              'pattype'=>$request->param('patType'),
                              'patowner'=>$request->param('patOwner'),
                              'inventor'=>$request->param('patInventor'),
                              'otherinventor'=>$request->param('patOtherInventor'),
                              'author'=>$request->param('patAuthor'),
                              'dept'=>$request->param('dept'),
                              
                              'status'=>'填报',
                              'addnewdate'=>$this->now,
                            );
              //新增，自定义patCreate()方法    
             // $patReturn = $patMdl->patCreate($patData);
              //新增，模型create()方法，返回的是新建的对象
              $patReturn = $patMdl::create($patData,true);
              $patId=$patReturn->id;
              $msg.='专利【新增】成功。<br>';
            }
            //配合前端的ajax请求，返回前端patId
            return json(array('patId'=>$patId,'msg'=>$msg));
          }else{
            //2.patrecord表新增
            //patRdData
            $patSet=$patMdl::get($patId);
            $patRdData=array('patinfo_id'=>$patId,
                              'num'=>$patSet->patnum,
                              'actdetail'=>'专利《'.$patSet->topic.'》新增填报',
                              
                              'act'=>'填报',
                              'acttime'=>$this->now,
                              'username'=>$this->username,
                              'rolename'=>$auth,
                            );
            //新增，自定义patRdCreate()方法  
           // $patRdId = $patRdMdl->patRdCreate($patRdData);
            //新增，模型create()方法
            $patRdId = $patRdMdl::create($patRdData,true);
            
            //3.issinfo表新增
            //issData
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
            //新增，自定义issCreate()方法
            //$issReturn = $issMdl->issCreate($issData); 
            //新增，模型create()方法，返回的是新建的对象
            $issReturn = $issMdl::create($issData,true);
            $issId=$issReturn->id;
            
            //4.issrecord表新增
            //issRdData
            $msg.='成功。<br>';  
            
            $issRdData=array('act'=>'填报',
                              'actdetail'=>'专利事务《'.$issReturn->topic.'》新增填报',
                              'acttime'=>$this->now,
                              'username'=>$this->username,
                              'rolename'=>$auth,
                              'issinfo_id'=>$issReturn->id,
                              'num'=>$issReturn->issnum,                              
                            );
            //新增，自定义issRdCreate()方法 
            //$issRdId = $issRdMdl->issRdCreate($issRdData);
            //新增，模型create()方法
            $issRdId = $issRdMdl::create($issRdData,true);
        }
          //5.attinfo更新
          //attData
          $attData=array('deldisplay'=>1);
        break;
        
        case'_SUBMIT':
          //patId!=0,issId!=0
          $oprtCHNStr='提交';
          //1.patinfo更新
          //patData
          $patData=array('topic'=>$request->param('patTopic'),
                          'pattype'=>$request->param('patType'),
                          'patowner'=>$request->param('patOwner'),
                          'inventor'=>$request->param('patInventor'),
                          'otherinventor'=>$request->param('patOtherInventor'),
                          'author'=>$request->param('patAuthor'),
                          'dept'=>$request->param('dept'),
                          
                          'submitdate'=>$this->now,
                          'status'=>'内审',
                          
                          );
          //更新，自定义patUpdate()方法
         // $patMdl->patUpdate($patData,$patId);
         //更新，模型update()方法
          $patMdl::update($patData,['id' => $patId],true);
          
          //2.patrecord新增
          //patRdData
          $patRdData=array('act'=>$oprtCHNStr,
                            'actdetail'=>'专利《'.$request->param('patTopic').'》提交。待内部审查<br>',
                            );
          //新增，自定义patRdCreate()方法
          //$patRdMdl->patRdCreate($patData);
          //新增，模型create()方法
          $patRdMdl::create(array_merge($patRdData,$patRdDataPatch),true);
          
          //3.issinfo更新
          //issData
          $issData=array('topic'=>$request->param('issPatTopic'),
                          'abstract'=>$request->param('issPatAbstract'),
                          'submitdate'=>$this->now,
                          'status'=>'待审核'
                          );
          //更新，自定义issUpdate()方法
          //$issMdl->issUpdate($issData,$issId);
          //更新，模型update()方法
          $issMdl::update($issData,['id'=>$issId],true);
                          
          //4.issrecord新增
          //issRdData
          $issRdData=array('act'=>$oprtCHNStr,
                            'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》提交审核。',
                            );
          //新增，自定义issRdCreate()方法
          //$issRdMdl->issRdCreate($issRdData);
          //新增，模型create()方法
          $issRdMdl::create(array_merge($issRdData,$issRdDataPatch),true);
          
          //5.attinfo更新
          //attData                      
          $attData=array('deldisplay'=>0);
          
        break;
        
        case'_DELETE':
          //patId!=0,issId!=0
          $oprtCHNStr='删除';
           //借助各自模型定义的Delete()方法进行删除
          //考虑应用TP5的软删除进行改进，？？？2018/3/23
          //1.删除pat，自定义patDelete()方法
         // $patReturn=$patMdl->patDelete($patId);
          //1.删除pat，模型destroy()方法
          $patMdl::destroy($patId);
          
          //2.删除patRd，自定义patRdDelete()方法
         // $patRdId_return=$patRdMdl->patRdDelete($patId);
          //2.删除patRd，模型destroy()方法
          $patRdMdl::destroy(['patinfo_id'=>$patId]);
          
          //3.删除iss，自定义issDelete()方法
          //$issReturn=$issMdl->issDelete($issId);
          //3.删除iss，模型destroy()方法
          $issMdl::destroy($issId);
          
          //4.删除issRd，自定义issRdDelete()方法
          //$issRdId_return=$issRdMdl->issRdDelete($issId);
          //4.删除issRd，模型destroy()方法
          $issRdMdl::destroy(['issinfo_id'=>$issId]);
          
          //5.删除att，自定义attDelete()方法
          //$attId_return=$attMdl->attDelete($issId);
          //5.删除att，模型destroy()方法
          $attMdl::destroy(['attmap_id'=>$issId]);
          
          $msg.='成功。<br>';  
          return json(array('msg'=>$msg,'topic'=>$request->param('issPatTopic'),'patId'=>$patId));
        break;
        
        case'_UPDATE':
          //patId!=0,issId!=0
          $oprtCHNStr='更新';
          //issStatus=='新增','返回修改' ,'修改完善'
        
          //1.patinfo更新
          //patData
          $patData=array('topic'=>$request->param('patTopic'),
                          'pattype'=>$request->param('patType'),
                          'patowner'=>$request->param('patOwner'),
                          'inventor'=>$request->param('patInventor'),
                          'otherinventor'=>$request->param('patOtherInventor'),
                          'author'=>$request->param('patAuthor'),
                          'dept'=>$request->param('dept'),
                          
                          'status'=>'内审',
                          );
          //更新，自定义patUpdate()方法
          //$patMdl->patUpdate($patData,$patId);
          //更新，模型update()方法
          $patMdl::update($patData,['id'=>$patId],true);
          
          //2.patrecord更新
          //patRdData
          $patRdData=array('actdetail'=>'专利《'.$patMdl::get($patId)->topic.'》新增填报');
          //更新，自定义patRdUpdate()方法
          //$patRdMdl->patRdUpdate($patRdData,$patId);
          //更新，模型update()方法
          $patRdMdl::update($patRdData,['act'=>'填报','patinfo_id'=>$patId],true);
          
          //3.issinfo更新
          //issData
          $issData=array('topic'=>$request->param('issPatTopic'),
                          'abstract'=>$request->param('issPatAbstract'),
                          );
          //更新，自定义issUpdate()方法
          //$issMdl->issUpdate($issData,$issId);
          //更新，模型模型update()方法
          $issMdl::update($issData,['id'=>$issId],true);
          
          //4.issrecord更新
          //issRdData
          $issRdData=array('actdetail'=>'专利事务《'.$request->param('issPatTopic').'》新增填报。');
          //更新，自定义issRdUpdate()方法
          //$issRdMdl->issRdUpdate($issData,$issId);
          //更新，模型模型update()方法
          $issRdMdl::update($issRdData,['act'=>'填报','issinfo_id'=>$issId],true);
          
          //5.attinfo更新
          //attData                      
          $attData=array('deldisplay'=>1);
          
          $msg.='成功。<br>';  
          
        break;
        //“_AUDIT”权限拥有的操作
        case'_PASS':
          //patId!=0,issId!=0
          $oprtCHNStr='审核';
          //1.patData无
          
          //2.patRdData无
          
          //3.issinfo更新
          //issData
          $issData=array('status'=>'审核通过',
                          'auditrejectdate'=>$this->now,
                          );
          //更新，模型update()方法
          $issMdl::update($issData,['id'=>$issId],true);
          
                          
          //4.issrecord新增
          //issRdData
          $issRdData=array('act'=>$oprtCHNStr,
                            'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审核结果：<span class="label label-success">审核通过</span></br>',
                            );
          //新增，自定义issRdCreate()方法
          //$issRdMdl->issRdCreate($issRdData);
          //新增，模型create()方法
          $issRdMdl::create(array_merge($issRdData,$issRdDataPatch),true);
          
          //5.attinfo更新
          //attData
          $attData=array('deldisplay'=>0);
          
        break;
        
        case'_FAIL':
          //patId!=0,issId!=0
          $oprtCHNStr='审核';
          //1.patData无
          
          //2.patRdData无
          
          //3.issinfo更新
          //issData
          $issData=array('status'=>'审核未通过',
                          'auditrejectdate'=>$this->now,
                              );
          //更新，自定义issCreate()方法
          //issMdl->issUpdate($issData,$issId);
          //更新，模型模型update()方法
          $issMdl::update($issData,['id'=>$issId],true);
          
          //4.issrecord新增
          //issRdData
          $issRdData=array('act'=>$oprtCHNStr,
                            'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审核结果：<span class="label label-warning">审核未通过</span></br>
                                          审核意见：<span class="text-primary">'.$request->param('auditMsg').'</span></br>',
                                );
          //新增，自定义issRdCreate()方法
         // $issRdMdl->issRdCreate($issRdData);
         //新增，模型create()方法
          $issRdMdl::create(array_merge($issRdData,$issRdDataPatch),true);
          
          //5.attinfo更新
          //attData
          $attData=array('deldisplay'=>0);
          
        break;
        
        case'_MODIFY':
          //patId!=0,issId!=0
          $oprtCHNStr='审核';
          //1.patinfo更新
          //patData
          $patData=array('status'=>'内审修改');
          //更新，自定义patUpdate()方法
          //$patMdl->patUpdate($patData,$patId);
          //更新，模型update()方法
          $patMdl::update($patData,['id'=>$patId],true);
          
          //2.patrecord新增
          //patRdData
          $patRdData=array('act'=>$oprtCHNStr,
                            'actdetail'=>'专利《'.$request->param('patTopic').'》审核结果：<span class="label label-primary">内审修改</span></br>
                                          审核意见：<span class="text-primary">'.$request->param('auditMsg').'</span></br>',
                                );
          //新增，自定义patRdCreate()方法
          //$patRdMdl->patRdCreate($patRdData);
          //新增，模型create()方法
          $patRdMdl::create(array_merge($patRdData,$patRdDataPatch),true);
          
          //3.issinfo更新
          //issData
          $issData=array('status'=>'返回修改',
                          'auditrejectdate'=>$this->now,
                          );
                              //$issMdl->where('id',$issId)->find()->toArray['issnum']
          //更新，自定义issUpdate()方法
          //issMdl->issUpdate($issData,$issId);
          //更新，模型模型update()方法
          $issMdl::update($issData,['id'=>$issId],true);
          
          //4.issrecord新增
          //issRdData
          $issRdData=array('act'=>$oprtCHNStr,
                            'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审核结果：<span class="label label-primary">返回修改</span></br>
                                          审核意见：<span class="text-primary">'.$request->param('auditMsg').'</span></br>',
                                );
          //新增，自定义issRdCreate()方法
          //$issRdMdl->issRdCreate($issRdData);
          //新增，模型create()方法
          $issRdMdl::create(array_merge($issRdData,$issRdDataPatch),true);
          
          //5.attinfo更新
          //attData
          $attData=array('deldisplay'=>0);
         
        break;
        //“_APPROVE”权限拥有的操作
        case'_PERMIT':
          //patId!=0,issId!=0
          $oprtCHNStr='审批';
          //根据$issStatus的值进行赋值
          if($issStatus=='审核通过' || $issStatus=='审核未通过' ){
            $patData=array('status'=>'拟申报',
                            'auditrejectdate'=>$this->now,
                            );
            $patRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利《'.$request->param('patTopic').'》审批结果：<span class="label label-success">拟申报</span>(内审批准)</br>'
                              );
            $issData=array('status'=>'批准申报',
                            'executer'=>$request->param('executer')
                            );
            $issRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审批结果：<span class="label label-success">批准申报</span></br>
                                            专利事务执行人：<span class="text-primary">【<strong>'.$request->param('executer').'</strong>】</span></br>'
                              );
            
          }else if($issStatus=='变更申请'){
            
            $issData=array('status'=>'准予变更',
                            'executer'=>$issMdl::get($issId)->executerchangeto,
                            'auditrejectdate'=>$this->now,
                            );
            $issRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》"变更申请"审批结果：<span class="label label-success">准予变更</span></br>
                                            专利事务执行人：<span class="text-primary">【<strong>'.$issMdl::get($issId)->executer.'</strong>】</span></br>'
                              );
          }else{
            //$issStatus=='拟续费'
            $issData=array('status'=>'准予续费',
                            'auditrejectdate'=>$this->now,
                            );
            $issRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审批结果：<span class="label label-success">准予续费</span></br>'
                              );
          }
          //1.patinfo更新
          //更新，模型update()方法
          $patMdl::update($patData,['id'=>$patId],true);
          
          //2.patrecord新增
          //新增，模型create()方法
          $patRdMdl::create(array_merge($patRdData,$patRdDataPatch),true);
          
          //3.issinfo更新
          //更新，模型模型update()方法
          $issMdl::update($issData,['id'=>$issId],true);
          
          //4.issrecord新增
          //新增，模型create()方法
          $issRdMdl::create(array_merge($issRdData,$issRdDataPatch),true);

          //5.attinfo更新
          //attData
          $attData=array('deldisplay'=>0);
          
        break;
        
        case'_VETO':
          //patId!=0,issId!=0
          $oprtCHNStr='审批';
          //根据$issStatus的值进行赋值
          if($issStatus=='审核通过' || $issStatus=='审核未通过' ){
            $patData=array('status'=>'内审否决',
                            'abandonrejectdate'=>$this->now,
                            );
            $patRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利《'.$request->param('patTopic').'》审批结果：<span class="label label-danger">内审否决</span></br>
                                            审批意见：<span class="text-primary">'.$request->param('approveMsg').'</span></br>'
                              );
            $issData=array('status'=>'否决申报',
                            'auditrejectdate'=>$this->now,
                            );
            $issRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审批结果：<span class="label label-danger">否决申报</span></br>
                                            审批意见：<span class="text-primary">'.$request->param('approveMsg').'</span></br>'
                              );
            
          }else if($issStatus=='变更申请'){
            $issData=array('status'=>'否决变更',
                            'auditrejectdate'=>$this->now,
                            );
            $issRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》"变更申请"审批结果：<span class="label label-danger">否决变更</span></br>
                                            审批意见：<span class="text-primary">'.$request->param('approveMsg').'</span></br>
                                            专利事务执行人：<span class="text-primary">【<strong>'.$issMdl::get($issId)->executer.'</strong>】</span></br>'
                              );
          }else{
            //$issStatus=='拟续费'
            $patData=array('status'=>'放弃续费');
            $patRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利《'.$request->param('patTopic').'》续费审批结果：<span class="label label-default">放弃续费</span></br>
                                            审批意见：<span class="text-default">'.$request->param('approveMsg').'</span></br>'
                              );
            $issData=array('status'=>'放弃续费',
                            'auditrejectdate'=>$this->now,
                            );
            $issRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审批结果：<span class="label label-default">放弃续费</span></br>
                                            审批意见：<span class="text-default">'.$request->param('approveMsg').'</span></br>
                                            专利事务执行人：<span class="text-primary">【<strong>'.$issMdl::get($issId)->executer.'</strong>】</span></br>'
                              );
          }
          //1.patinfo更新
          //更新，模型update()方法
          $patMdl::update($patData,['id'=>$patId],true);
          
          //2.patrecord新增
          //新增，模型create()方法
          $patRdMdl::create(array_merge($patRdData,$patRdDataPatch),true);
          
          //3.issinfo更新
          //更新，模型模型update()方法
          $issMdl::update($issData,['id'=>$issId],true);
          
          //4.issrecord新增
          //新增，模型create()方法
          $issRdMdl::create(array_merge($issRdData,$issRdDataPatch),true);

          //5.attinfo更新
          //attData
          $attData=array('deldisplay'=>0);
          
          
        break;
        
        case'_COMPLETE':
          //patId!=0,issId!=0
         $oprtCHNStr='审批';
          
          //根据iss.status的值进行赋值
          //$issStatus=$issMdl::get($issId)->status;
          
          //$issStatus=='审核通过' || $issStatus=='审核未通过' ){
          //1.patinfo更新
          //patData
          $patData=array('status'=>'内审修改');
          //更新，模型update()方法
          $patMdl::update($patData,['id'=>$patId],true);
          
          //2.patrecord新增
          //patRdData
          $patRdData=array('act'=>$oprtCHNStr,
                            'actdetail'=>'专利《'.$request->param('patTopic').'》审批结果：<span class="label label-warning">内审修改</span></br>
                                              审批意见：<span class="text-primary">'.$request->param('approveMsg').'</span></br>'
                            );
          //新增，模型create()方法
          $patRdMdl::create(array_merge($patRdData,$patRdDataPatch),true);
          
          //3.issinfo更新
          //issData
          $issData=array('status'=>'修改完善',
                          'auditrejectdate'=>$this->now,
                          );
          //更新，模型模型update()方法
          $issMdl::update($issData,['id'=>$issId],true);
          
          //4.issrecord新增
          //issRdData
          $issRdData=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审批结果：<span class="label label-warning">修改完善</span></br>
                                              审批意见：<span class="text-primary">'.$request->param('approveMsg').'</span></br>'
                                  );
          //新增，模型create()方法
          $issRdMdl::create(array_merge($issRdData,$issRdDataPatch),true);

          //5.attinfo更新
          //attData
          $attData=array('deldisplay'=>0);
          
        break;
        //“_EXECUTE”权限拥有的操作
        case'_ACCEPT':
          //patId!=0,issId!=0
          $oprtCHNStr='领受';
          //1.patinfo无
          //2.patrecord无
          
          //3.issinfo更新
          //issData
          $issData=array('status'=>'申报执行',
                          'operatestartdate'=>$this->now,
                          );
          //更新，模型模型update()方法
          $issMdl::update($issData,['id'=>$issId],true); 
          
          //4.issrecord新增
          //issRdData         
          $issRdData=array('act'=>$oprtCHNStr,
                            'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报开始</br>'
                            );
          //新增，模型create()方法
          $issRdMdl::create(array_merge($issRdData,$issRdDataPatch),true);
          
          //5.attinfo更新
          //attData
          $attData=array('deldisplay'=>0);
          $msg.='成功</br>';
        break;
        
        case'_REFUSE':
          //patId!=0,issId!=0
          $oprtCHNStr='变更申述';
          //1.patinfo无
          //2.patrecord无
          
          //3.issinfo更新
          //issData
          //执行人变更
            if($request->param('changeExecuter')){
              $issData=array('status'=>'变更申请',
                              'executerchangeto'=>$request->param('executer'),
                              'executerchangemsg'=>$request->param('executeMsg'),
                              );
              $strAppend='申请执行人由【<strong>'.$this->username.'</strong>】变更为【<strong>'.$request->param('executer').'</strong>】</br>';
            }else{
              //非执行人变更
              $issData=array('status'=>'变更申请',
                              'executerchangeto'=>$this->username,
                              'executerchangemsg'=>$request->param('executeMsg'),
                              );
              $strAppend='';
            }
          //更新，模型模型update()方法
          $issMdl::update($issData,['id'=>$issId],true);
          
          //4.issrecord新增
          //issRdData
          $issRdData=array('act'=>$oprtCHNStr,
                            'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报变更申请</br>'.$strAppend.'
                                变更申请原因：<span class="text-warning">'.$request->param('executeMsg').'</span></br>'
                            );
          //新增，模型create()方法
          $issRdMdl::create(array_merge($issRdData,$issRdDataPatch),true);
          
          //5.attinfo更新
          //attData
          $attData=array('deldisplay'=>0);
          
        break;
        
        case'_REPORT':
          //patId!=0,issId!=0
          $oprtCHNStr='申报执行报告';
          //1.patinfo更新
          $patData=array('topic'=>$request->param('patTopic'),
                          'pattype'=>$request->param('patType'),
                          'patowner'=>$request->param('patOwner'),
                          'inventor'=>$request->param('patInventor'),
                          'otherinventor'=>$request->param('patOtherInventor'),
                          );
          //更新，模型update()方法
          $patMdl::update($patData,['id'=>$patId],true);
          
          //2.patrecord无
          
          //3.issinfo更新
          $issData=array('topic'=>$request->param('issPatTopic'),
                          'abstract'=>$request->param('issPatAbstract'),
                          'status'=>'申报执行'
                          );
          //更新，模型模型update()方法
          $issMdl::update($issData,['id'=>$issId],true);
          
          //4.issrecord新增
          $issRdData=array('act'=>$oprtCHNStr,
                            'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报执行报告</br>
                                          报告简述：<span class="text-primary">'.$request->param('executeMsg').'</span></br>'
                            );
          //新增，模型create()方法
          $issRdMdl::create(array_merge($issRdData,$issRdDataPatch),true);
          
          //5.attinfo更新
          //attData
          $attData=array('deldisplay'=>0);
          
        break;
        
        case'_FINISH':
          //patId!=0,issId!=0
          $oprtCHNStr='申报提交复核';
          //1.patinfo更新
          $patData=array('topic'=>$request->param('patTopic'),
                          'pattype'=>$request->param('patType'),
                          'patowner'=>$request->param('patOwner'),
                          'inventor'=>$request->param('patInventor'),
                          'otherinventor'=>$request->param('patOtherInventor'),
                          );
          //更新，模型update()方法
          $patMdl::update($patData,['id'=>$patId],true);
          
          //2.patrecord无
          
          //3.issinfo更新
          $issData=array('topic'=>$request->param('issPatTopic'),
                          'abstract'=>$request->param('issPatAbstract'),
                          'status'=>'申报复核'
                          );
          //更新，模型模型update()方法
          $issMdl::update($issData,['id'=>$issId],true);
          
          //4.issrecord新增
          //issRdData
          $issRdData=array('act'=>$oprtCHNStr,
                            'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报提交复核</br>提交内容简述：<span class="text-primary">'.$request->param('executeMsg').'</span></br>'
                            );
          //新增，模型create()方法
          $issRdMdl::create(array_merge($issRdData,$issRdDataPatch),true);
                            
          //5.attinfo更新
          //attData
          $attData=array('deldisplay'=>0);
          
        break;
        //“_MAINTAIN”权限拥有的操作
        case'_APPLY':
          //patId!=0,issId!=0
          
          //根据$issStatus的值进行赋值
          if($issStatus=='申报复核'){
            $oprtCHNStr='<span class="label label-primary">授权申报正式提交</span>';
            $patData=array('status'=>'申报',
                            'applydate'=>$this->now,
                            
                            'patapplynum'=>$request->param('patApplyNum'),
                            'patadmin'=>$request->param('patAdmin'),
                            'patagency'=>$request->param('patAgency'),
                            'patowner'=>$request->param('patOwner'),
                            'inventer'=>$request->param('patInventor'),
                            'otherinventor'=>$request->param('patOtherInventor'),
                            'keyword'=>$request->param('patKeyword'),
                            'summary'=>$request->param('patSummary'),
                            );
            $patRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利《'.$request->param('patTopic').'》申报提交</br>申报提交简述：<span class="text-primary">'.$request->param('maintainMsg').'</span></br>'
                              );
            $issData=array('status'=>'申报提交',
                            'applydate'=>$this->now,
                            );
            $issRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报提交</br>申报提交简述：<span class="text-primary">'.$request->param('maintainMsg').'</span></br>'
                              );
            
          }else{
            //$issStatus=='准予续费'
            $oprtCHNStr='<span class="label label-primary">续费申报正式提交</span>';
            $patData=array('status'=>'续费中',
                            'patrenewapplynum'=>$request->param('patRenewApplyNum'),
                            'patadmin'=>$request->param('patAdmin'),
                            'patrenewagency'=>$request->param('patRenewAgency'),
                            'renewapplydate'=>$this->now,
                            );
            $patRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利《'.$request->param('patTopic').'》续费申报提交</br>申报提交简述：<span class="text-primary">'.$request->param('maintainMsg').'</span></br>'
                              );
            $issData=array('status'=>'续费提交',
                            'applydate'=>$this->now,
                            );
            $issRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》续费申报提交</br>申报提交简述：<span class="text-primary">'.$request->param('maintainMsg').'</span></br>'
                              );
          }
          //1.patinfo更新
          //更新，模型update()方法
          $patMdl::update($patData,['id'=>$patId],true);
          
          //2.patrecord新增
          //新增，模型create()方法
          $patRdMdl::create(array_merge($patRdData,$patRdDataPatch),true);
          
          //3.issinfo更新
          //更新，模型模型update()方法
          $issMdl::update($issData,['id'=>$issId],true);
          
          //4.issrecord新增
          //新增，模型create()方法
          $issRdMdl::create(array_merge($issRdData,$issRdDataPatch),true);
          
          //5.attinfo更新
          //attData
          $attData=array('deldisplay'=>0);
        break;
        
        case'_REVIEW':
          //patId!=0,issId!=0
          $oprtCHNStr='复核修改';
          //根据$issStatus的值进行赋值
          if($issStatus=='申报复核'){
          //1.patinfo更新
          $patData=array('patapplynum'=>$request->param('patApplyNum'),
                          'patadmin'=>$request->param('patAdmin'),
                          'patagency'=>$request->param('patAgency'),
                          'patowner'=>$request->param('patOwner'),
                          'inventer'=>$request->param('patInventor'),
                          'otherinventor'=>$request->param('patOtherInventor'),
                          'keyword'=>$request->param('patKeyword'),
                          'summary'=>$request->param('patSummary'),
                          );
          
          }else{
            //$issStatus=='准予续费'
            $patData=array('status'=>'续费中',
                            'patrenewapplynum'=>$request->param('patRenewApplyNum'),
                            'patadmin'=>$request->param('patAdmin'),
                            'patrenewagency'=>$request->param('patRenewAgency'),
                            'renewapplydate'=>$this->now,
                            );
          }
          //更新，模型update()方法
          $pat=$patMdl::update($patData,['id'=>$patId],true);
          
          //2.patrecord无
          
          //3.issinfo无
          
          //4.issrecord无
          
          //5.attinfo更新
          //attData
          $attData=array('deldisplay'=>0);
        break;
        
        case'_IMPROVE':
          //patId!=0,issId!=0
          //根据$issStatus的值进行赋值
           if($issStatus=='申报复核'){
            $oprtCHNStr='申报修改';
            
            $issData=array('status'=>'申报修改',
                            'statussummary'=>$request->param('maintainMsg')
                            );
            $issRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报复核结果：申报修改</br>申报修改原因：<span class="text-primary">'.$request->param('maintainMsg').'</span></br>'
                              );
            
          }else{
            //$issStatus=='申报提交'
            $oprtCHNStr='<span class="label label-warning">申报修改</span>';
            
            $patData=array('status'=>'申报修改',
                            'modifydate'=>$this->now,
                            );
            $patRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利《'.$request->param('patTopic').'》申报提交结果：'.$oprtCHNStr.'</br>申报修改原因：<span class="text-warning">'.$request->param('maintainMsg').'</span></br>'
                              );
            $issData=array('status'=>'申报修改',
                            'statussummary'=>$request->param('maintainMsg'),
                            'resultdate'=>$this->now,
                            );
            $issRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》'.$oprtCHNStr.'</br>申报修改原因：<span class="text-warning">'.$request->param('maintainMsg').'</span></br>'
                              );
          }
          //1.patinfo更新
          //更新，模型update()方法
          $patMdl::update($patData,['id'=>$patId],true);
          
          //2.patrecord新增
          //新增，模型create()方法
          $patRdMdl::create(array_merge($patRdData,$patRdDataPatch),true);
          
          //3.issinfo更新
          //更新，模型模型update()方法
          $issMdl::update($issData,['id'=>$issId],true);
          
          //4.issrecord新增
          //新增，模型create()方法
          $issRdMdl::create(array_merge($issRdData,$issRdDataPatch),true);
          
          //5.attinfo更新
          //attData
          $attData=array('deldisplay'=>0);
        break;
        
        case'_AUTHORIZE':
          //patId!=0,issId!=0
          $oprtCHNStr='<span class="label label-success">授权</span>';
          //根据$issStatus的值进行赋值
          if($issStatus=='申报提交'){            
            $patData=array('status'=>'授权',
                            'patauthnum'=>$request->param('patAuthNum'),
                            'authrejectdate'=>$request->param('patAuthDate'),
                            'renewdeadlinedate'=>$request->param('renewDeadlineDate'),
                            'patadmin'=>$request->param('patAdmin'),
                            );
            $patRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利《'.$request->param('patTopic').'》申报提交结果：'.$oprtCHNStr.'</br>'
                              );
            $issData=array('status'=>'专利授权',
                            'statussummary'=>$oprtCHNStr,
                            'resultdate'=>$this->now,
                            );
            $issRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报提交结果：'.$oprtCHNStr.'</br>'
                              );
            
          }else{
            //$issStatus=='续费提交'
            $patData=array('status'=>'续费授权',
                            'patrenewauthnum'=>$request->param('patRenewAuthNum'),
                            'renewauthrejectdate'=>$request->param('patRenewAuthDate'),
                            'patadmin'=>$request->param('patAdmin'),
                            );
            $patRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利《'.$request->param('patTopic').'》续费申报提交结果：'.$oprtCHNStr.'</br>'
                              );
            $issData=array('status'=>'专利授权',
                            'resultdate'=>$this->now,
                            );
            $issRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》续费申报提交结果：'.$oprtCHNStr.'</br>'
                              );
          }
          //1.patinfo更新
          //更新，模型update()方法
          $patMdl::update($patData,['id'=>$patId],true);
          
          //2.patrecord新增
          //新增，模型create()方法
          $patRdMdl::create(array_merge($patRdData,$patRdDataPatch),true);
          
          //3.issinfo更新
          //更新，模型模型update()方法
          $issMdl::update($issData,['id'=>$issId],true);
          
          //4.issrecord新增
          //新增，模型create()方法
          $issRdMdl::create(array_merge($issRdData,$issRdDataPatch),true);
          
          //5.attinfo更新
          //attData
          $attData=array('deldisplay'=>0);
          
        break;
        
        case'_REJECT':
          //patId!=0,issId!=0
          $oprtCHNStr='<span class="label label-danger">驳回</span>';
          //根据$issStatus的值进行赋值
          if($issStatus=='申报提交'){            
            $patData=array('status'=>'驳回',
                            'authrejectdate'=>$request->param('patRejectDate'),
                            'patadmin'=>$request->param('patAdmin'),
                            'abandonrejectreason'=>$request->param('maintainMsg'),
                            );
            $patRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利《'.$request->param('patTopic').'》申报提交结果：'.$oprtCHNStr.'</br>
                                            驳回原因：<span class="text-danger">'.$request->param('maintainMsg').'</span></br>'
                              );
            $issData=array('status'=>'专利驳回',
                            'statussummary'=>$request->param('maintainMsg'),
                            'resultdate'=>$request->param('authRejectDate'),
                            );
            $issRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报提交结果：'.$oprtCHNStr.'</br>
                                            驳回原因：<span class="text-danger">'.$request->param('maintainMsg').'</span></br>'
                              );
          }else{
            //$issStatus=='续费提交'
            $patData=array('status'=>'驳回续费',
                            'renewrejectdate'=>$request->param('patRenewRejectDate'),
                            'patadmin'=>$request->param('patAdmin'),
                            'abandonrejectreason'=>$request->param('maintainMsg'),
                            );
            $patRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利《'.$request->param('patTopic').'》续费申报提交结果：'.$oprtCHNStr.'</br>驳回原因：<span class="text-danger">'.$request->param('maintainMsg').'</span></br>'
                              );
            $issData=array('status'=>'专利驳回',
                            'statussummary'=>$request->param('maintainMsg'),
                            'resultdate'=>$this->now,
                            );
            $issRdData=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》续费申报提交结果：'.$oprtCHNStr.'</br>驳回原因：<span class="text-danger">'.$request->param('maintainMsg').'</span></br>'
                              );
          }
          //1.patinfo更新
          //更新，模型update()方法
          $patMdl::update($patData,['id'=>$patId],true);
          
          //2.patrecord新增
          //新增，模型create()方法
          $patRdMdl::create(array_merge($patRdData,$patRdDataPatch),true);
          
          //3.issinfo更新
          //更新，模型模型update()方法
          $issMdl::update($issData,['id'=>$issId],true);
          
          //4.issrecord新增
          //新增，模型create()方法
          $issRdMdl::create(array_merge($issRdData,$issRdDataPatch),true);
          
          //5.attinfo更新
          //attData
          $attData=array('deldisplay'=>0);
          
        break;
        
        case'_CLOSE':
          //patId!=0,issId!=0
          $oprtCHNStr='<span class="label label-default">完结</span>';
          //根据$issStatus的值进行赋值          
          if($issStatus=='放弃续费' ){            
            $patData=array('status'=>'超期无效',
                            'renewabandondate'=>$this->now,
                            );
            $patRdData=array('act'=>'<span class="label label-default">超期无效</span>',
                              'actdetail'=>'专利《'.$request->param('patTopic').'》<span class="label label-default">超期无效</span></br>'
                              );
            //1.patinfo更新
            //更新，模型update()方法
            $patMdl::update($patData,['id'=>$patId],true);
          
            //2.patrecord新增
            //新增，模型create()方法
            $patRdMdl::create(array_merge($patRdData,$patRdDataPatch),true);
            
          }else{
           // $issStatuss=='专利授权' || '续费授权' || '否决申报' || '专利驳回'
            $patData=array('z'=>0);
            $patRdData=array('z'=>0);
          }
          $issData=array('status'=>'完结',
                          'finishdate'=>$this->now,
                          );
          $issRdData=array('act'=>$oprtCHNStr,
                            'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》'.$oprtCHNStr.'</br>'
                            );
          
          
          //3.issinfo更新
          //更新，模型模型update()方法
          $issMdl::update($issData,['id'=>$issId],true);
          
          //4.issrecord新增
          //新增，模型create()方法
          $issRdMdl::create(array_merge($issRdData,$issRdDataPatch),true);
          
          //5.attinfo更新
          //attData
          $attData=array('deldisplay'=>0);
          
        break;
        
        case'_ADDRENEW':
          //patId!=0,issId=0
          if($request->param('returnType')=='_JSON'){
            //配合前端请求，返回前端json数据
            return json(array_merge($patMdl::where('id',$request->param('patId'))->find()->toArray(),
                              array("today"=>$this->today,"username"=>$this->username,"deptMaintainer"=>$this->dept)));
          }else{
            $oprtCHNStr='续费报告';
            //1.patinfo表更新记录
            $patData=array('status'=>'续费中',
                            'renew_createdate'=>$this->now
                            );
            //更新
            $patMdl::update($patData,['id'=>$patId],true);

            //2.patrecord表新增记录
            $patRdData=array('act'=>$oprtCHNStr,
                              'actdetail'=>'专利《'.$patMdl::get($patId)->topic.'》'.$oprtCHNStr.'</br>'
                              );
            //新建
            $patRdMdl::create(array_merge($patRdData,$patRdDataPatch),true);
         
            //3.issinfo新增
            //issData
            $issData=array('issmap_type'=>$request->param('issMapType'),
                            'topic'=>$request->param('issPatTopic'),
                            'abstract'=>$request->param('issPatAbstract'),
                            
                            'issmap_id'=>$patId,
                            'num_id'=>$patId,
                            'status'=>'拟续费',
                            'addnewdate'=>$this->now,
                            'writer'=>$this->username,
                            'executer'=>$this->username,
                            'dept'=>$this->dept,
                            );
            //新增，模型create()方法，返回的是新增的对象
            $issReturn=$issMdl::create($issData,true);
            $issId=$issReturn->id;
            
            //4.issrecord新增
            //issRdData
            $issRdData=array('issinfo_id'=>$issReturn->id,
                              'num'=>$issReturn->issnum,
                              'act'=>$oprtCHNStr,
                              'actdetail'=>'专利事务《'.$issReturn->topic.'》新增填报',
                              'acttime'=>$this->now,
                              'username'=>$this->username,
                              'rolename'=>$auth,
                              );
            //新增，模型create()方法
            $issRdMdl::create($issRdData,true);

            //5.attinfo更新
            //attData
            $attData=array('deldisplay'=>0);
            $msg.='【新增续费】专利事务成功。<br>';  
          }
        break;

      }
      
      //5.attinfo更新
      if($issId){
        $issSet=$issMdl::get($issId);
        //循环更新attMdl,将文件从现有的‘temp’目录移动到指定目录
        for($i=0;$i<count($arrAttId);$i++){
          
          $fileStr=$arrAttFileObjStr[$i];
          $name=$arrAttFileName[$i];
          
          //有‘temp’字符串才移动到指定目录
          if(substr_count($fileStr,'temp')){
            $newDir='..'.DS.'uploads'.DS.$issSet->issnum;
            
            //调用AttinfoModel中定义的fileMove()方法，返回true后才更新Attinfo表
            if($attMdl->fileMove($fileStr,$name,$newDir)){
              $attDataPatch=array('num_id'=>$issSet->issnum,
                              'attmap_id'=>$issSet->id,
                              'attpath'=>$newDir.DS.$name,
                              );
              //更新att
              $attMdl::update(array_merge($attData,$attDataPatch),['id'=>$arrAttId[$i]],true);
                            
              $msg.="附件".$name."已上传。</br>"; 
            }else{
              $msg.="附件".$name."上传失败。</br>"; 
            }
          }
        } 
        
      }else{
        $msg.="无附件</br>"; 
      }
       
//  </结构2.----------------------------------------------------------------------------------------->
       
      //return $msg;
      //return json(array('msg'=>$msg,'btnHtml'=>$btnHtml,'topic'=>$request->param('issPatTopic')));
      return json(array('msg'=>$msg,'topic'=>$issMdl::get($issId)->topic,'patId'=>$patId,'issId'=>$issId));
      //return $this->issPatAuth($request);//参数不够，不会产生分页。
    }
    
    //为前端显示PatRenew模板准备，1.数据库数据；2.向模板变量赋值；3.选择模板文件PatRenew.html返回前端
     public function patRenew(Request $request,PatinfoModel $patMdl,$patId='',$auth='maintain')
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
     
     if($patId=='' && !empty($pat[0])){
        $patId=$pat[0]->id;
     }
       
      $this->assign([
          'home'=>$request->domain(),
          // 
          'maintainer'=>$this->username,
          'dept'=>$this->dept,
          'pat'=>$pat,
          'patRenewTotal'=>count($pat),
          'pageTotal'=>$pageTotal,
          'auth'=>$auth,
          'patId'=>$patId
      ]);
       
       //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>_RENEW 模块开发中……<br/></p></div>';
       return view('index'.DS.'issPatAuth'.DS.'patRenew');
    }
    
    public function userInfo(Request $request)
    {
       
      //return ':)<br> userInfo 模块开发中……';
       return view();
     
     
    }
    
    public function attManage(Request $request)
    {
       
      //return ':)<br> attManage 模块开发中……';
      return view();
     
    }
    //检查前端送来的topic是否已存在，返回前端检查结果（json格式）。
    public function checkPatTopic(Request $request,PatinfoModel $patMdl)
    {
        $exist=$patMdl::where('topic','like',$request->param('topic'))->count();
      
        return array('exist'=>$exist);  
        
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
                            );
                
            //更新att
            $attId = $attMdl->attUpdate(array_merge($attData,$attDataPatch),$arrAttId[i]);
                          
            $msg.="附件".$fileStr."移动成功</br>"; 
          }else{
            $msg.="附件".$fileStr."移动失败</br>"; 
          }
        }
      
      $data=array('msg'=>$msg,'topic'=>$request->param('issPatTopic'),'patId'=>$patId,'attId'=>$arrAttId);
      return json($data);
      //return $data;
    }
    
     public function issThe(Request $request,$auth='done',$issId='')
    {
       $this->_loginUser();
       
       // $auth接收前端页面传来的auth值
        if(!empty($request->param('auth'))){
          $auth=$request->param('auth');
        }else{
          foreach($this->authArr['iss'] as $key=>$value){
            if($value){
              $auth=$key;
              break;
            }
          }
        }
        // $issId接收前端页面传来issId值
       if(!empty($request->param('id'))){
        $issId=$request->param('id');
       }
      
      //使用模型Issinfo
        $issSet = new IssinfoModel; 
        //edit
        $mapEdit['status'] =['in',['填报','返回修改','修改完善']];
        $mapEdit['dept'] =$this->dept;
        $mapEdit['writer']=$this->username;
        $mapEdit['issmap_type']=['like','%_ISST_THE%'];
        
        //audit
        $mapAudit['status'] ='待审核';
        $mapAudit['dept'] =$this->dept;
        $mapAudit['issmap_type']=['like','%_ISST_THE%'];
        //approve
        $mapApprove['status'] =['in',['审核未通过','审核通过','变更申请','拟续费']];
        $mapApprove['issmap_type']=['like','%_ISST_THE%'];
        //execute
        $mapExecute['status'] =['in',['批准申报','申报执行','申报修改','准予变更','否决变更']];
        //$mapExecute['dept'] =$this->dept;
        $mapExecute['executer'] =$this->username;
        $mapExecute['issmap_type']=['like','%_ISST_THE%'];
        //maintain
        $mapMaintain['status'] =['in',['申报复核','申报提交','续费提交','准予续费',
                                      '否决申报','专利授权','专利驳回','放弃续费','续费授权']];
        $mapMaintain['issmap_type']=['like','%_ISST_THE%'];
        //done
        $map['status'] ='完结';
        $map['issmap_type']=['like','%_ISST_THE%'];
        
        if($this->authArr['iss']['edit']){
          $numIssTheEdit=$issSet->where($mapEdit)->count(); 
          //$auth='_EDIT';
        }else{
          $numIssTheEdit=0;
        }
        
        if($this->authArr['iss']['audit']){
          $numIssTheAudit=$issSet->where($mapAudit)->count();
          //$auth='_AUDIT'; 
        }else{
          $numIssTheAudit=0;
        }
        
        if($this->authArr['iss']['approve']){
          $numIssTheApprove=$issSet->where($mapApprove)->count();
          //$auth='_APPROVE'; 
        }else{
          $numIssTheApprove=0;
        }
        
        if($this->authArr['iss']['execute']){
          $numIssTheExecute=$issSet->where($mapExecute)->count();
          //$auth='_EXECUTE'; 
        }else{
          $numIssTheExecute=0;
        }
        
        if($this->authArr['iss']['maintain']){
          $numIssTheMaintain=$issSet->where($mapMaintain)->count(); 
         
        }else{
          $numIssTheMaintain=0;
      
        }
        
        $numIssTheDone=$issSet->where($map)->count(); 
        
        $numTotal=$numIssTheEdit+$numIssTheAudit+$numIssTheApprove+$numIssTheExecute+$numIssTheMaintain;
        
        $destr= "请求方法:".$request->method()."<br/>".
                "username:". $this->username."<br/>".
                "pwd:".$this->pwd."<br/>".
                "log:".$this->log."<br/>".
                "auth:".json_encode($this->authArr); 
        
         // 模板变量赋值        
        $this->assign([
          //在usercenter.html页面输出自定义的信息
          //在index.html页面通过destr输出自定义的信息
          'destr'=>$destr."</br>",
          
          'home'=>$request->domain(),
          'username'=>$this->username,
  
          //向前端权限变量赋值
          //所有权限
          'authArray'=>$this->authArr,
          //当前权限
          'auth'=>$auth,  
          
          'numIssTheEdit'=>$numIssTheEdit,
          'numIssTheAudit'=>$numIssTheAudit,
          'numIssTheApprove'=>$numIssTheApprove,
          'numIssTheExecute'=>$numIssTheExecute,
          'numIssTheMaintain'=>$numIssTheMaintain,
          'numIssTheDone'=>$numIssTheDone,
          'numTotal'=>$numTotal,
          
          'issId'=>$issId,
      	
        ]);
      return view();
      //return ':)<br> issthe 模块开发中……';
     
    }
    
     public function issPro(Request $request,$auth='done',$issId='')
    {
       $this->_loginUser();
       
       // $auth接收前端页面传来的auth值
        if(!empty($request->param('auth'))){
          $auth=$request->param('auth');
        }else{
          foreach($this->authArr['iss'] as $key=>$value){
            if($value){
              $auth=$key;
              break;
            }
          }
        }
        // $issId接收前端页面传来issId值
       if(!empty($request->param('id'))){
        $issId=$request->param('id');
       }
      
      //使用模型Issinfo
        $issSet = new IssinfoModel; 
        //edit
        $mapEdit['status'] =['in',['填报','返回修改','修改完善']];
        $mapEdit['dept'] =$this->dept;
        $mapEdit['writer']=$this->username;
        $mapEdit['issmap_type']=['like','%_ISST_PRO%'];
        
        //audit
        $mapAudit['status'] ='待审核';
        $mapAudit['dept'] =$this->dept;
        $mapAudit['issmap_type']=['like','%_ISST_PRO%'];
        //approve
        $mapApprove['status'] =['in',['审核未通过','审核通过','变更申请','拟续费']];
        $mapApprove['issmap_type']=['like','%_ISST_PRO%'];
        //execute
        $mapExecute['status'] =['in',['批准申报','申报执行','申报修改','准予变更','否决变更']];
        //$mapExecute['dept'] =$this->dept;
        $mapExecute['executer'] =$this->username;
        $mapExecute['issmap_type']=['like','%_ISST_PRO%'];
        //maintain
        $mapMaintain['status'] =['in',['申报复核','申报提交','续费提交','准予续费',
                                      '否决申报','专利授权','专利驳回','放弃续费','续费授权']];
        $mapMaintain['issmap_type']=['like','%_ISST_PRO%'];
        //done
        $map['status'] ='完结';
        $map['issmap_type']=['like','%_ISST_PRO%'];
        
        if($this->authArr['iss']['edit']){
          $numIssProEdit=$issSet->where($mapEdit)->count(); 
          //$auth='_EDIT';
        }else{
          $numIssProEdit=0;
        }
        
        if($this->authArr['iss']['audit']){
          $numIssProAudit=$issSet->where($mapAudit)->count();
          //$auth='_AUDIT'; 
        }else{
          $numIssProAudit=0;
        }
        
        if($this->authArr['iss']['approve']){
          $numIssProApprove=$issSet->where($mapApprove)->count();
          //$auth='_APPROVE'; 
        }else{
          $numIssProApprove=0;
        }
        
        if($this->authArr['iss']['execute']){
          $numIssProExecute=$issSet->where($mapExecute)->count();
          //$auth='_EXECUTE'; 
        }else{
          $numIssProExecute=0;
        }
        
        if($this->authArr['iss']['maintain']){
          $numIssProMaintain=$issSet->where($mapMaintain)->count(); 
          
        }else{
          $numIssProMaintain=0;
        
        }
        
        $numIssProDone=$issSet->where($map)->count(); 
        
        $numTotal=$numIssProEdit+$numIssProAudit+$numIssProApprove+$numIssProExecute+$numIssProMaintain;
        
        $destr= "请求方法:".$request->method()."<br/>".
                "username:". $this->username."<br/>".
                "pwd:".$this->pwd."<br/>".
                "log:".$this->log."<br/>".
                "auth:".json_encode($this->authArr); 
        
         // 模板变量赋值        
        $this->assign([
          //在usercenter.html页面输出自定义的信息
          //在index.html页面通过destr输出自定义的信息
          'destr'=>$destr."</br>",
          
          'home'=>$request->domain(),
          'username'=>$this->username,
  
          //向前端权限变量赋值
          //所有权限
          'authArray'=>$this->authArr,
          //当前权限
          'auth'=>$auth,  
          
          'numIssProEdit'=>$numIssProEdit,
          'numIssProAudit'=>$numIssProAudit,
          'numIssProApprove'=>$numIssProApprove,
          'numIssProExecute'=>$numIssProExecute,
          'numIssProMaintain'=>$numIssProMaintain,
          'numIssProDone'=>$numIssProDone,
          'numTotal'=>$numTotal,
          
          'issId'=>$issId,
      	
        ]);
      return view();
      //return ':)<br> issthe 模块开发中……';
     
    }   
}
