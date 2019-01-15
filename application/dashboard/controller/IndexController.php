<?php

namespace app\dashboard\controller;

use think\Request;
use think\Session;
use think\View;
use think\File as FileObj;
use think\paginator\driver\Bootstrap;
use think\Collection;

use app\dashboard\model\User as UserModel;
use app\dashboard\model\Rolety as RoletyModel;
use app\dashboard\model\Issinfo as IssinfoModel;
use app\dashboard\model\Issrecord as IssrecordModel;
use app\dashboard\model\Patinfo as PatinfoModel;
use app\dashboard\model\Patrecord as PatrecordModel;
use app\dashboard\model\Attinfo as AttinfoModel;
use app\dashboard\model\Assinfo as AssinfoModel;

//引入issPatFSM
//use isspatfsm\IssPatFSM;

use app\dashboard\model\isspatfsm\IssPatFSM;

class IndexController extends \think\Controller
{
    //用户名
    private $userId = null;
    //用户名
    private $userName = null;
    //用户密码
    private $pwd = null;
    //用户登录状态
    private $log = null;
    //用户角色
    private $roles = array();
    //用户所在部门
    private $dept = null;
    //用户权限
    private $authArr = array();
    //登录用户主要信息
    private $logUser = array();

    private $today = null;

    private $now = null;
    #权限实体定义
    private $authEnt=array();
    // 初始化
    protected function _initialize()
    {
        $this->userId = Session::get('userId');
        $this->userName = Session::get('username');
        $this->pwd = Session::get('pwd');
        $this->log = Session::get('log');
        $this->roles = Session::get('role');
        $this->dept = Session::get('dept');
        $this->authArr = Session::get('authArr');
        $this->today = date('Y-m-d');
        $this->now = date("Y-m-d H:i:s");
        
        //取出user的authority字段值
        //$this->authArr = UserModel::get($this->userId)->authority;
        $this->logUser=array('auth'=>$this->authArr,'username'=>$this->userName,'dept'=>$this->dept);
        //使用模型前的初始化，为模型内部使用的变量赋初值，后续的各个方法中无需再初始化，但可以进行修改
        IssinfoModel::initModel($this->userName,$this->dept,$this->authArr); 
    }

    // 判断是否为登录用户，私有方法
    // 判断是否为登录用户
    private function _loginUser()
    {
      //通过$this->log判断是否是登录用户，非登录用户退回到登录页面
      $this->log=Session::get('log');
      #app/common.php中预定义的权限实体
      $authEntArr=conAuthEntArr;
      if(1!=$this->log){
        return $this->error('无用户名或密码，请先登录系统');
      }
      
      $this->userId=Session::get('userId');
      $this->username=Session::get('username');
      $this->pwd=Session::get('pwd');
      $this->dept=Session::get('dept');
      $this->authArr=Session::get('authArr');
      
      foreach($authEntArr as $k=>$v){
          #将ent名称转为全小写，并去掉字符串中的下划线，
          $k=strtolower(strtr($k,['_'=>'']));
          $this->authEnt[$k]=$v;
          $n=0;
          #根据$this->authArr对$this->authEnt中auth进行逐项赋值
          foreach($this->authArr[$k] as $k1=>$v1){
            if($v1){
              $this->authEnt[$k]['auth'][$k1]['val']=$v1;
              $n++;
            }
          }
          #该项模块是否可见
          $this->authEnt[$k]['visible']=($n>0)?true:false;
      }
      return true;    
    }

    public function index(Request $request, IssinfoModel $issMdl,UserModel $userMdl, $auth = '',$item='')
    {
        $this->_loginUser();

        // $auth接收前端页面传来的auth值,表示rolename（映射“用户组名”）
        $auth = !empty($request->param('auth'))?$request->param('auth'):'done';
        $item=!empty($request->param('item'))?$request->param('item'):'userInfo';
        $authEnt=$this->authEnt;
                        
        //调用issinfo模型定义查询方法getNumArr()得到各类iss数据
        $numIssArr=$issMdl::getNumArr();
        //if($authEnt['iss-pat']['visible'] || $authEnt['iss-the']['visible'] || $authEnt['iss-pro']['visible']){
//          $numIssArr=$issMdl::getNumArr();
//        }else{
//          $numIssArr=0;
//        }
        
        $user=$userMdl::get($this->userId);
        $userGroup=$userMdl->userGroupArr($this->userId);
               
        // 模板变量赋值
        $this->assign([
            'home' => $request->domain(), 
            'username' => $this->userName,             
            'numIssArr'=>json_encode($numIssArr),
            #权限实体
            'authEnt'=>json_encode($this->authEnt,JSON_UNESCAPED_UNICODE),
            'authArr'=>json_encode($this->authArr,JSON_UNESCAPED_UNICODE),
            'user' => $user,
            'userGroup' => $userGroup,
            'userMobile'=>$user->getData('mobile'),
            'year' => date('Y')
            ]);

        // 模板输出
        //return $this->fetch();
        return view();
    }

    //上传附件文件到temp目录
    public function attUploadTemp(Request $request, AttinfoModel $attMdl)
    {
        $attData = array(
            'name' => $request->param('attName'),
            'atttype' => $request->param('attType'),
            'attmap_id' => $request->param('attmap_id'),
            'attmap_type' => $request->param('attmap_type'),
            'uploaddate' => $this->now,
            'uploader' => $request->param('uploader'),
            'rolename' => $request->param('attRoleName'),
            'deldisplay' => $request->param('deldisplay'));
        //应用AttinfoModel中定义的fileUpdateTemp()方法上传附件文件到temp目录
        $att_return = $attMdl->fileUploadTemp($attData, $request->file('attFile'));

        return $att_return;

    }

    //上传附件文件到指定目录
    //参数1：$fileSet，类型：对象。值：不为空。说明：拟上传的文件对象
    //参数2：$dirName，类型：字符。值：不为空。说明：上传文件拟放入的目录名称
    //参数3：$attId，类型：字符。值：不为空。说明：拟记录上传文件路径的记录id
    private function _uploadAtt($fileSet, $dirName, $attId)
    {

        if (!empty($fileSet)) {
            // 移动到框架根目录的uploads/ 目录下,系统重新命名文件名
            $info = $fileSet->validate(['size' => 10485760, 'ext' => 'jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,rar'])->move(ROOT_PATH.
                DS.'uploads'.DS.$dirName);
        } else {
            $this->error('未选择文件，请选择需上传的文件。');
        }

        if ($info) {
            // 成功上传后 获取上传信息
            // 文件的后缀名
            $info->getExtension()."<br/>";
            // 文件存放的文件夹路径：类似20160820/42a79759f284b767dfcb2a0197904287.jpg
            $info->getSaveName()."<br/>";
            // 完整的文件名
            $info->getFilename();

            $path = '..'.DS.'uploads'.DS.$dirName.DS.$info->getSaveName();

            $attSet = AttinfoModel::where('id', $attId)->find();
            $attSet->save(['attpath' => $path, ]);

            // 静态调用更新
            //$attSet=AttinfoModel::update([
            //              'name'  => 'topthink',
            //              'email' => 'topthink@qq.com',
            //            ], ['num_id'=>$num_id]);
            return "success";

        } else {
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
        $d = dir($dirName);
        $result = 0;
        while (false !== ($child = $d->read())) {
            // 清除目录里所有的文件
            if ($child != "." && $child != "..") {
                if (is_dir($dirName.DS.$child)) {
                    // 递归调用自己
                    $this->_deleteDirs($dirName.DS.$child);
                } else {
                    unlink($dirName.DS.$child);

                }
            }
        }
        $d->close();

        //清除目录
        rmdir($dirName);

        if (is_dir($dirName)) {
            $result = $dirName;
        } else {
            $result = "success";
        }
        return $result;
    }

    // 向前端返回查询的Executer信息
    public function selectExecuter()
    {
        $this->_loginUser();

        //查出所有未禁用的用户的指定字段信息
        $user = UserModel::where('enable', '1')->field('username,dept,usergroup_id')->order('dept', 'asc')->select();
        $executer = array();
        foreach ($user as $v) {
            //用户在operator用户组(usergroupid=4)中：即是usergroup_id字段含有'4'
            if (strstr($v->usergroup_id, '4')) {
                unset($v->usergroup_id);
                array_push($executer, $v);
            }
        }
        // 将数组转化为json
        //return json($executer);
        return $executer;

    }

    // 根据前端发送的模板文件名参数，选择对应的页面文件返回
    public function tplFile(Request $request, $auth = '', $id = '')
    {
        $this->_loginUser();
        //前端发送的是锚点值
        if (!empty($request->param('sId'))) {
            $tplFile = $request->param('sId');
        } else {
            $tplFile = '#userInfo';
        }

        if (!empty($request->param('auth'))) {
            $auth = $request->param('auth');
        }

        if (!empty($request->param('id'))) {
            $id = $request->param('id');
        }

        //返回模板文件
        if (substr($tplFile, 0, 1) == '#') {
            $tplFile = substr($tplFile, 1);
            $this->redirect($tplFile, ['auth' => $auth, 'id' => $id]);
        } else {
            return '模板文件不存在。';
        }

    }

    public function issPat(Request $request, IssinfoModel $issMdl, $auth = 'done', $issId = '')
    {
        $this->_loginUser();

        // $auth接收前端页面传来的auth值
        if (!empty($request->param('auth'))) {
            $auth = $request->param('auth');
        } else {
            foreach ($this->authArr['iss'] as $key => $value) {
                if ($value) {
                    $auth = $key;
                    break;
                }
            }
        }
        // $issId接收前端页面传来issId值
        if (!empty($request->param('id'))) {
            $issId = $request->param('id');
        }
        
         //调用issinfo模型定义查询方法issPatProcess()得到查询结果数据集
        $num=$issMdl->issPatProcess($this->logUser,'_NUM');

        // 模板变量赋值
        $this->assign([
                    'home' => $request->domain(), 
                    'username' => $this->userName, //向前端权限变量赋值
                    //所有权限
                    'authArray' => $this->authArr, 
                    'auth' => $auth, //当前权限
                    'numIssPatEdit' => $num['edit'], 
                    'numIssPatAudit' => $num['audit'], 
                    'numIssPatApprove' => $num['approve'], 
                    'numIssPatExecute' => $num['execute'], 
                    'numIssPatMaintain' => $num['maintain'],
                    'numIssPatDone' => $num['done'], 
                    'numTotal' => $num['todo'],
                    'numPatRenewTotal' => $num['patrenew'], 
                    'issId' => $issId, 
                    ]);
        return view();
        //return ':)<br> issthe 模块开发中……';

    }
    
    public function issPat2(Request $request, IssinfoModel $issMdl, $issId = '')
    {
        $this->_loginUser();

        // $issId接收前端页面传来issId值
        $issId=!empty($request->param('id'))?$request->param('id'):0;
        
        //调用issinfo模型定义查询方法issPatProcess()得到查询结果数据集
        $num=$issMdl->issPatProcess($this->logUser,'_NUM');

        // 模板变量赋值
        $this->assign([
                    'home' => $request->domain(), 
                    'username' => $this->userName, //向前端权限变量赋值
                    //iss权限
                    'authIss' => $this->authArr['iss'], 
                    'numToDo'=>$num['todo'],
                    'numPatRenew' => $num['patrenew'],
                    'numInProcess' => $num['inprocess'],
                    'numDone' => $num['done'], 
                    'issId' => $issId, 
                    ]);
        return view();
        //return ':)<br> issthe 模块开发中……';

    }
    
    //根据登录用户Id，选择返回前端issPat的list
    public function issPatList(Request $request,IssinfoModel $issMdl,$sortData=[],$searchData=[])
    {
        $this->_loginUser();
        $sortDefaults=array('listRows'=>10,'sortName'=>'_TOPIC','sortOrder'=>'_ASC','pageNum'=>1,'process'=>'_TODO');
        // 接收前端的排序参数数组
        $sortData=!empty($request->param('sortData/a'))?$request->param('sortData/a'):$sortDefaults;
        $sortData=array_merge($sortDefaults,$sortData);
        
        $searchDefaults=array();
        // 接收前端的搜索参数数组，由前端保证传来的搜索参数值非0，非空。
        $searchData=!empty($request->param('searchData/a'))?$request->param('searchData/a'):$searchDefaults;
        $searchData=array_merge($searchDefaults,$searchData);
        
        $searchParamArr=array();
        $searchResultArr=array();
        
        // 接收前端页面传来的process值:_TODO/_INPROCESS/_DONE
        $process = $sortData['process'];  
        // 接收前端当前页数
        $pageNum = $sortData['pageNum'];     
        // 接收前端列表行数
        $listRows= $sortData['listRows']; 
        // 接收前端排序字段名称
        $sortName= $sortData['sortName'];
        // 接收前端排序方式"_ASC"/"_DESC"
        $sortOrder= $sortData['sortOrder'];
        
        //调用issinfo模型的查询方法issPatProcess()得到查询结果数据集(数组)
        $issArr=$issMdl->issPatProcess($this->logUser,$process);
    
        //前端排序字段名与数据表字段名对应关系
        $sortField = array(
            '_TOPIC' => 'topic',
            '_STATUS' => 'status',
            '_CREATETIME' => 'create_time',
            '_UPDATETIME' => 'update_time',
            '_PATNAME' => ['issmap','topic'],//'issmap.topic'
            '_PATTYPE' => ['issmap','pattype'],//'issmap.pattype'
            '_PATSTATUS' => ['issmap','status'],//'issmap.status'
            '_DEPT' => 'dept',
            '_WRITER' => 'writer',
            '_EXECUTER' => 'executer',
            '_OPRT' => 'oprt',
            );
        
        //将前端传来的排序字段名转换为数据表字段名
        if (array_key_exists($sortName, $sortField)) {
            $sortName = $sortField[$sortName];
        }else{
            $sortName ='topic';
        }
        
        //转换前端搜索字段名
        $searchField = array(
            //前端搜索字段名与数据表字段名对应关系
            'searchTopic' => 'topic',
            'searchStatus' => 'status',
            'searchDept' => 'dept',
            'searchWriter' => 'writer',
            'searchExecuter' => 'executer',
            //前端时间搜索字段名转换
            'searchTime' => 'timeName',
            //'_CREATETIME' => 'create_time',
//            '_UPDATETIME' => 'update_time',
            'searchTimeRange' => 'timeRange',
            'searchStartTime' => 'startTime',
            'searchEndTime' => 'endTime',
            );
        
        //将前端传来的搜索关联数组转换为模型查询用的关联数组
        foreach($searchData as $key=>$val){
            //非0，非空值才进行操作
            if($val){
                if (array_key_exists($key,$searchField)){
                    $searchParamArr[$searchField[$key]]=$val;
                }
                //针对$searchData['searchTime']='_CREATETIME'或'_UPDATETIME'
                if ($val=='_CREATETIME'){
                    $searchParamArr[$searchField[$key]]='create_time';
                }
                
                if ($val=='_UPDATETIME'){
                    $searchParamArr[$searchField[$key]]='update_time';
                }
            }
        }
        
        //要进行搜索
        if(count($searchData)){
            //调用issinfo模型的方法issPatSearch()得到仅含‘id’字段的搜索结果数据集(数组)
            $searchResultArr=$issMdl->issPatSearch($searchParamArr,['id']);
            //有搜索结果
            if(count($searchResultArr)){
                //利用框架的数据集类的column方法简化为索引数组
                if(is_array($searchResultArr)){
                    $searchResultArr=collection($searchResultArr)->column('id');
                }else{
                    $searchResultArr=$searchResultArr->column('id');
                }
                //调用issinfo模型的用户搜索结果数据集(数组)排序方法issPatSort()得到排序后的数组
                $issArr=$issMdl->issPatSort($issArr,$sortName,$sortOrder,$searchResultArr);
            }else{
                //搜索结果为空
                $issArr=array();  
            }
        }else{
            //无需进行搜索
            $issArr=$issMdl->issPatSort($issArr,$sortName,$sortOrder);      
        }
        
        //将查询结果集数组装入Bootstrap对象，使用其render()方法获取前端分页显示的html代码。Bootstrap($iss,$listRows,$curretPage,$total);
        $objPg = new Bootstrap($issArr,$listRows,$pageNum,count($issArr));
        $pageRender = $objPg->render();
        
        $showList=array_slice($issArr,($pageNum-1)*$listRows,$listRows);
        
        //将数据库issinfo表对应字段转换为前端的排序字段，回传给前端。
        foreach($sortField as $key=>$value){
           if($value==$sortName){
                $sortName=$key;
           }
        }
        //组装权限/状态数组
        $statusArr=array();
        foreach($this->authArr['iss'] as $key=>$val){
            $statusArr[$key] =_commonIssAuthStatus('_PAT',$key);
        }
        
        $this->assign(['home' => $request->domain(), 
                // 分页显示所需参数
                'iss' => $showList,
                'pageRender' => $pageRender, 
                'listRows' => $listRows,
                'process' => $process,
                'pageNum'=>$pageNum,
                
                'authEdit'=>  $this->authArr['iss']['edit'],
                'sortName' => $sortName, 
                'sortOrder' => $sortOrder,
        
                //前端判断权限用数组（权限/状态数组），保持中文
                'statusArr' => json_encode($statusArr,JSON_UNESCAPED_UNICODE),
                'searchData'=>json_encode($searchData,JSON_UNESCAPED_UNICODE),
                'searchResultNum'=>count($issArr),
                'issId'=>0,
                
                'check'=>$sortName.'|'.$sortOrder
                ]);
            
        //return view();  
        return view('index'.DS.'issPatList'.DS.'issPatList');   
    }
    
    //根据前端传来的权限，选择返回前端的模板文件及内容
    public function issPatSingle(Request $request, $oprt = '_NONE', $auth = '_NONE', $issId = 0, $patId = 0)
    {
        $this->_loginUser();

        // 接收前端页面传来的值
        $oprt = !empty($request->param('oprt'))?$request->param('oprt'):'_NONE';
        $auth = !empty($request->param('auth'))?$request->param('auth'):'_NONE';
        $issId = !empty($request->param('issId'))?$request->param('issId'):0;
        $patId = !empty($request->param('patId'))?$request->param('patId'):0;

        //选择模板文件名
        switch ($auth) {
                //edit
            case '_EDIT':
                $tplFile = 'editSingle';
                break;
                //audit
            case '_AUDIT':
                $tplFile = 'auditSingle';
                break;
                //approve
            case '_APPROVE':
                $tplFile = 'approveSingle';
                break;
                //execute
            case '_EXECUTE':
                $tplFile = 'executeSingle';
                break;
                //maintain
            case '_MAINTAIN':
                $tplFile = 'maintainSingle';
                break;
                //done
            default:
                $tplFile = 'doneSingle';
                break;

        }

        if ($oprt == '_ADDNEW') {
            $iss = array(
                'id' => 0,
                'topic' => '',
                'abstract' => '',
                'status' => '申报新增',
                'statusdescription' => 0);
            //查询当前用户已上传的所有附件信息
            $att = AttinfoModel::all(['attmap_id' => 0, 'uploader' => $this->userName, 'rolename' => 'edit', 'deldisplay' => 1]);
            $pat = array(
                'id' => 0,
                'topic' => '',
                'patowner' => '',
                'otherinventor' => '',
                'inventor' => '',
                'summary' => '',
                'keyword' => '',
                'pattype' => '',
                );
            $patType = 0;
            $issChRd = 0;
        } else
            if ($oprt == '_ADDRENEW') {
                $tplFile = 'renewSingle';
                //查询当前用户已上传的所有附件信息
                $att = AttinfoModel::all(['attmap_id' => 0, 'uploader' => $this->userName, 'rolename' => 'maintain', 'deldisplay' => 1]);
                $pat = PatinfoModel::get($patId);
                $iss = array(
                    'id' => 0,
                    'topic' => '关于“'.$pat->topic.'”的授权续费申报',
                    'abstract' => '',
                    'status' => '续费新增',
                    'statusdescription' => 0);
                $patType = $pat->getData('pattype');
                $issChRd = 0;
            } else {
                //得到模板文件中需显示的iss信息
                $iss = IssinfoModel::get($issId);
                // 利用模型issinfo.php中定义的一对多方法“attachments”得到iss对应的attachments信息
                $att = $iss->attachments;
                // 利用模型issinfo.php中定义的多态方法“issmap”得到iss对应的pat信息
                $pat = $iss->issmap;
                // 得到iss对应的pat的'pattype'数据库字段值
                $patType = $iss->issmap->getData('pattype');

                //利用模型issinfo.php中定义的一对多方法“issrecords”得到iss对应的issrecord信息,找出‘审核、审批、修改’意见
                //$issRd=collection($iss->issrecords)->visible(['id','rolename','act']);

                //利用模型issrecord.php中定义的issChRdRecent()方法，得到issId对应issue的最新‘审核、审批、修改’意见
                $issRdMdl = new IssrecordModel;
                $issChRd = $issRdMdl->issChRdRecent($issId);
            }

            //向前端模板中的变量赋值
            $this->assign([
                        'home' => $request->domain(), 
                        'username' => $this->userName, 
                        'dept' => $this->dept, 
                        'auth' => $auth, 
                        'oprt' => $oprt, 
                        'iss' => $iss, 
                        'att' => $att, 
                        'numAtt' => count($att), 
                        'pat' => $pat, 
                        'patType' => $patType,
                        'issChRd' => $issChRd
                        ]);
        //return $this->fetch($tplFile);
        return view('index'.DS.'issPatSingle'.DS.$tplFile);
    }
    
    //为前端显示PatRenew模板准备，1.数据库数据；2.向模板变量赋值；3.选择模板文件PatRenew.html返回前端
    public function patRenewList(Request $request, PatinfoModel $patMdl, $patId = '', $auth = 'maintain')
    {
        $this->_loginUser();

        //调用模型文件Patinfo.php中定义的patRenew方法找出合适的pat。
        $pat = $patMdl->patRenewList();

        // 获取分页显示
        $pageTotal = $pat->render();

        if ($patId == '' && !empty($pat[0])) {
            $patId = $pat[0]->id;
        }

        $this->assign(['home' => $request->domain(), //
            'maintainer' => $this->userName, 'dept' => $this->dept, 'pat' => $pat, 'patRenewTotal' => count($pat), 'pageTotal' => $pageTotal,
            'auth' => $auth, 'patId' => $patId]);

        return view('index'.DS.'issPatList'.DS.'patRenewList');
    }
    

    //根据前端传来的权限，选择返回前端的模板文件及内容
    public function issPatAuth(Request $request, $auth = 'done', $issId = '')
    {
        $this->_loginUser();

        // $role接收前端页面传来的auth值
        $auth = !empty($request->param('auth'))?$request->param('auth'):'done';

        // $issId接收前端页面传来issId值
        $issId = !empty($request->param('id'))?$request->param('id'):'';

        // $returnType接收前端页面传来的returnType值，‘0’为模板文件，‘1’为数据
        $returnType = !empty($request->param('returnType'))?$request->param('returnType'):0;

        //$totalTableRows接收前端页面传来的分页时每页显示的记录数，默认为10
        $issPatTableRows = !empty($request->param('issPatTableRows'))?$request->param('issPatTableRows'):10;

        // 接收前端分页页数变量：“pageTotalNum”
        $pageTotalNum = !empty($request->param('pageTotalNum'))?$request->param('pageTotalNum'):1;

        // $sortName接收前端页面传来的排序字段名
        $sortName = !empty($request->param('sortName'))?$request->param('sortName'):'_TOPIC';

        // $sort接收前端页面传来的排序顺序
        $sort = !empty($request->param('sort'))?$request->param('sort'):'_ASC';

        // 查询词1，'searchPatName'
        $searchPatName = !empty($request->param('searchPatName'))?$request->param('searchPatName'):'';

        // 查询词2，'searchDept'
        $searchDept = !empty($request->param('searchDept'))?$request->param('searchDept'):0;

        // 查询词3，'searchPatStatus'
        $searchPatStatus = !empty($request->param('searchPatStatus'))?$request->param('searchPatStatus'):0;

        // 查询词4，'searchPatType'
        $searchPatType = !empty($request->param('searchPatType'))?$request->param('searchPatType'):0;

        // 查询词5，'searchWriter'
        $searchWriter = !empty($request->param('searchWriter'))?$request->param('searchWriter'):'';

        // 查询词6，'searchPatStatus'
        $searchPatStatus = !empty($request->param('searchPatStatus'))?$request->param('searchPatStatus'):'';
        
        // 选择排序字段
        switch ($sortName) {

            case '_PATNAME':
                $strOrder = 'abstract';
                break;

            case '_PATSTATUS':
                $strOrder = 'abstract';
                break;

            case '_ABSTRACT':
                $strOrder = 'abstract';
                break;

            case '_WRITER':
                $strOrder = 'writer';
                break;

            case '_EXECUTER':
                $strOrder = 'executer';
                break;

            case '_ADDNEWDATE':
                $strOrder = 'addnewdate';
                break;

            case '_STATUS':
                $strOrder = 'status';
                break;

            case '_DEPT':
                $strOrder = 'dept';
                break;

            case '_TOPIC':
                $strOrder = 'topic';
                $sortName = "_TOPIC";
                break;

                //默认按字段“status”
            default:
                $strOrder = 'status';
                $sortName = "_OPERATION";
                break;

        }

        //  组合升序or降序查询
        if ($sort == "_ASC") {
            $strOrder = $strOrder.' asc';
        } else {
            $strOrder = $strOrder.' desc';

        }
        //确保查询的是isspat
        $map['issmap_type'] = ['like', '%'.'_ISST_PAT'.'%'];
        //选择模板文件名,组合查询条件
        switch ($auth) {
                //edit
            case 'edit':
                $map['status'] = ['in', ['填报', '返回修改', '修改完善']];
                $map['dept'] = $this->dept;
                $map['writer'] = $this->userName;
                //$tplFile='issPatEdit';
                $tplFile = 'edit';
                break;
                //audit
            case 'audit':
                $map['status'] = '待审核';
                $map['dept'] = $this->dept;
                //$tplFile='issPatAudit';
                $tplFile = 'audit';
                break;
                //approve
            case 'approve':
                $map['status'] = ['in', ['审核未通过', '审核通过', '变更申请', '拟续费']];
                //$tplFile='issPatApprove';
                $tplFile = 'approve';
                break;
                //execute
            case 'execute':
                $map['executer'] = $this->userName;
                $map['status'] = ['in', ['批准申报', '申报执行', '申报修改', '准予变更', '否决变更']];
                $map['dept'] = $this->dept;

                //$tplFile='issPatExecute';
                $tplFile = 'execute';
                break;
                //maintain
            case 'maintain':
                $map['status'] = ['in', ['申报复核', '申报提交', '续费提交', '准予续费', '否决申报', '专利授权', '专利驳回', '放弃续费', '续费授权']];
                //$tplFile='issPatMaintain';
                $tplFile = 'maintain';
                break;
                ////_MAINTAIN_RENEW
                //          case '_MAINTAIN_RENEW':
                //            $mapPat['status'] =['in',['授权','续费授权']];
                //            //$tplFile='issPatMaintain';
                //            $tplFile='patRenew';
                //          break;
                //done
            default:
                $map['status'] = '完结';
                //$tplFile='issPatDone';
                $tplFile = 'done';
                break;

        }

        //得到模板文件中需显示的内容
        //使用模型Issinfo
        $issSet = new IssinfoModel;
        //$issSet->patinfo;

        // 记录总数
        $numTotal = $issSet->where($map)->count();

        // 查出所有的用户并分页，根据“strOrder”排序，前端页面显示的锚点（hash值）为$fragment，设定分页页数变量：“pageTotalNum”
        // 带上每页显示记录行数$totalTableRows，实现查询结果分页显示。
        $issPatTotal = $issSet->where($map) //->patinfo
            ->order($strOrder)->paginate($issPatTableRows, false, ['type' => 'bootstrap', 'var_page' => 'pageTotalNum', 'query' => ['issPatTableRows' =>
            $issPatTableRows]]);

        // 获取分页显示
        $pageTotal = $issPatTotal->render();

        if ($issId == '' && !empty($issPatTotal[0])) {
            $issId = $issPatTotal[0]->id;
        }

        //返回数据还是模板文件,‘0’为模板文件，‘1’为数据
        if ($returnType) {
            //响应前端的请求，返回前端要求条件的issPat数量
            return ($numTotal);
        } else {
            $this->assign(['home' => $request->domain(), //"destr".$this->authArr['isspro']['edit'],

            // 分页显示所需参数
            'issPatTotal' => $issPatTotal, 'numTotal' => $numTotal, 'pageTotal' => $pageTotal, 'issPatTableRows' => $issPatTableRows,
                'pageTotalNum' => $pageTotalNum, // 表格搜索字段
                'searchPatName' => $searchPatName, 'searchDept' => $searchDept, 'searchPatStatus' => $searchPatStatus, 'searchPatType' =>
                $searchPatType, 'searchWriter' => $searchWriter, 'searchPatStatus' => $searchPatStatus, // 表格排序信息
                'sortName' => $sortName, 'sort' => $sort, 'auth' => $auth, 'issId' => $issId, ]);

            // return view($tplFile);
            return view('index'.DS.'issPatAuth'.DS.$tplFile);
        }
    }

    //根据前端传来的权限，选择返回前端的模板文件及内容
    public function issPatAuthSingle(Request $request, $oprt = '_NONE', $auth = 'done', $issId = 0, $patId = 0)
    {
        $this->_loginUser();

        // 接收前端页面传来的值
        $oprt = !empty($request->param('oprt'))?$request->param('oprt'):'_NONE';
        $auth = !empty($request->param('auth'))?$request->param('auth'):'done';
        $issId = !empty($request->param('issId'))?$request->param('issId'):0;
        $patId = !empty($request->param('patId'))?$request->param('patId'):0;

        //选择模板文件名
        switch ($auth) {
                //edit
            case 'edit':
                $tplFile = 'editSingle';
                break;
                //audit
            case 'audit':
                $tplFile = 'auditSingle';
                break;
                //approve
            case 'approve':
                $tplFile = 'approveSingle';
                break;
                //execute
            case 'execute':
                $tplFile = 'executeSingle';
                break;
                //maintain
            case 'maintain':
                $tplFile = 'maintainSingle';
                break;
                //done
            default:
                $tplFile = 'doneSingle';
                break;

        }


        if ($oprt == '_ADDNEW') {
            $iss = array(
                'id' => 0,
                'topic' => '',
                'abstract' => '',
                'status' => '申报新增',
                'statusdescription' => 0);
            //查询当前用户已上传的所有附件信息
            $att = AttinfoModel::all(['attmap_id' => 0, 'uploader' => $this->userName, 'rolename' => 'edit', 'deldisplay' => 1]);
            $pat = array(
                'id' => 0,
                'topic' => '',
                'patowner' => '',
                'otherinventor' => '',
                'inventor' => '',
                'summary' => '',
                'keyword' => '',
                'pattype' => '',
                );
            $patType = 0;
            $issChRd = 0;
        } else
            if ($oprt == '_ADDRENEW') {
                $tplFile = 'renewSingle';
                //查询当前用户已上传的所有附件信息
                $att = AttinfoModel::all(['attmap_id' => 0, 'uploader' => $this->userName, 'rolename' => 'maintain', 'deldisplay' => 1]);
                $pat = PatinfoModel::get($patId);
                $iss = array(
                    'id' => 0,
                    'topic' => '关于“'.$pat->topic.'”的授权续费申报',
                    'abstract' => '',
                    'status' => '续费新增',
                    'statusdescription' => 0);
                $patType = $pat->getData('pattype');
                $issChRd = 0;
            } else {
                //得到模板文件中需显示的iss信息
                $iss = IssinfoModel::get($issId);
                // 利用模型issinfo.php中定义的一对多方法“attachments”得到iss对应的attachments信息
                $att = $iss->attachments;
                // 利用模型issinfo.php中定义的多态方法“issmap”得到iss对应的pat信息
                $pat = $iss->issmap;
                // 得到iss对应的pat的'pattype'数据库字段值
                $patType = $iss->issmap->getData('pattype');

                //利用模型issinfo.php中定义的一对多方法“issrecords”得到iss对应的issrecord信息,找出‘审核、审批、修改’意见
                //$issRd=collection($iss->issrecords)->visible(['id','rolename','act']);

                //利用模型issrecord.php中定义的issChRdRecent()方法，得到issId对应issue的最新‘审核、审批、修改’意见
                $issRdMdl = new IssrecordModel;
                $issChRd = $issRdMdl->issChRdRecent($request->param('issId'));
            }

            //向前端模板中的变量赋值
            $this->assign(['home' => $request->domain(), 'username' => $this->userName, 'dept' => $this->dept, 'auth' => $request->
                param('auth'), 'oprt' => $oprt, 'iss' => $iss, 'att' => $att, 'numAtt' => count($att), 'pat' => $pat, 'patType' => $patType,
                'issChRd' => $issChRd]);
        //return $this->fetch($tplFile);
        return view('index'.DS.'issPatAuthSingle'.DS.$tplFile);
    }

    //应用IssPatFSM
    //1.需要前端提供
    //1.1启动fsm必须的参数：$param=array('auth'=>'_EDIT','status'=>'填报','oprt'=>'_ADDNEW');
    //1.2fsm要处理的对象/数据：
    //2.程序结构
    //part1：变量赋初值
    //part2：配置状态机参数，组装状态机要处理的数据，
    //part3：启动状态机处理数据，得到处理结果，返回前端处理结果
    public function issPatOprt(Request $request, IssinfoModel $issMdl, PatinfoModel $patMdl, IssPatFSM $fsm)
    {
        $this->_loginUser();

        //part：1.变量赋初值
        // $auth接收前端页面传来的auth值,表示rolename（映射“用户组名”），确定状态机（IssPatFSM）的工作模式
        $issAuth = !empty($request->param('auth'))?$request->param('auth'):'done';
        // $status接收前端页面传来的status值,确定状态机当前的状态，issPat的初始状态为“申报新增”
        $issStatus = !empty($request->param('issStatus'))?$request->param('issStatus'):'申报新增';
        // $oprt接收前端页面传来的oprt值，确定状态机所要进行的操作
        $issOprt = !empty($request->param('oprt'))?$request->param('oprt'):'_NONE';
        // $patId接收前端页面传来的patId值
        $patId = !empty($request->param('patId'))?$request->param('patId'):0;
        // $issId接收前端页面传来的issId值
        $issId = !empty($request->param('issId'))?$request->param('issId'):0;
        //本次进行操作的用户信息
        $oprt_user = array(
            'id' => $this->userId,
            'name' => $this->userName,
            'auth' => $issAuth,
            'oprt' => $issOprt,
            'time' => time());
        
        $authTime= array();
        if (array_key_exists($issAuth, $authTime)) {
            $authTime[$issAuth] = time();
        } else {
            $authTime = array_merge($authTime, array($issAuth => time()));
        }

        //涉及数据库5个数据表的数组赋值
        if ($issId) {
            $iss = $issMdl::get($issId);
            if(!is_array($iss['auth_time'])){
                //转换成数组
                $iss['auth_time']=json_decode($iss['auth_time'],true);
            }
            $iss['auth_time']=array_merge($iss['auth_time'],$authTime);
        } else {
            $iss = array(
                'issnum' => 0,
                'topic' => 0,
                'status' => $issStatus,
                'statusdescription' => 0,
                'issmap_id' => $patId,
                'issmap_type' => 0,
                'num_id' => $patId, //待取消
                'addnewdate' => $this->now, //待取消
                'abstract' => 0,
                'writer' => $this->userName,
                'executer' => $this->userName,
                'executerchangeto' => '',
                'executerchangemsg' => '',
                'dept' => $this->dept,
                'auth_time' => $authTime,
                'oprt_user' => 0);
        }
        //数据表1：issinfo。各个字段赋值
        $issInfo = array(
            'topic' => !empty($request->param('issPatTopic'))?$request->param('issPatTopic'):$iss['topic'],
            'status' => $iss['status'],
            'statusdescription' => !empty($request->param('statusDescription'))?$request->param('statusDescription'):$iss['statusdescription'],
            'issmap_id' => $iss['issmap_id'],
            'issmap_type' => !empty($request->param('issType'))?$request->param('issType'):$iss['issmap_type'],
            'num_id' => $iss['num_id'], //待取消
            'addnewdate' => $iss['addnewdate'], //待取消
            'abstract' => !empty($request->param('issPatAbstract'))?$request->param('issPatAbstract'):$iss['abstract'],
            'writer' => $iss['writer'],
            'executer' => $iss['executer'],
            'executerchangeto' => !empty($request->param('executer'))?$request->param('executer'):$iss['executerchangeto'],
            'executerchangemsg' => !empty($request->param('executeMsg'))?$request->param('executeMsg'):$iss['executerchangemsg'],
            'dept' => $iss['dept'],
            'auth_time' => $iss['auth_time'],
            'oprt_user' => $oprt_user);
        //数据表2：issrecord。各个字段赋值
        $issRecord = array(
            'num' => $iss['issnum'],
            'username' => $this->userName,
            'rolename' => $issAuth,
            'act' => $issOprt,
            'actdetail' => '', //FSM_add
            'actdetailhtml' => '', //FSM_add
            'acttime' => $this->now,
            'issinfo_id' => $issId,
            );
        
        if ($patId) {
            $pat = $patMdl::get($patId);
            //$patMdl中已定义修改器，此处需要数据表中的原始值。
            $pat['pattype'] = $pat->getData('pattype');            
        } else {
            $pat = array(
                'patnum' => 0,
                'topic' => 0,
                'status' => 0,
                'pattype' => 0,
                'patapplynum' => 0,
                'patauthnum' => 0,
                'applyplace' => 0,
                'patadmin' => 0,
                'patagency' => 0,
                'patrenewagency' => 0,
                'patrenewapplynum' => 0,
                'patrenewauthnum' => 0,
                'patowner' => 0,
                'inventor' => 0,
                'otherinventor' => 0,
                'author' => 0,
                'dept' => 0,
                'keyword' => 0,
                'summary' => 0,
                'resource' => 0, //FSM_add??
                'issinfo_id' => $issId,
                'issinfo_topic' => $iss['topic'],
                'statustime' => json_encode(array('_PATS1' => time(), '_PATS2' => time())), //FSM_add
                'milestonetime' => json_encode(array('authorizetime' => time(), 'rejecttime' => time())), //FSM_add
                'addnewdate' => time(),
                //'status_time' => $patStatusTime,
                //'oprt_user' => 0
                );
        }
        //数据表3：patinfo。各个字段赋值
        $patInfo = array(
            //'patnum'=>0,
            'topic' => !empty($request->param('patTopic'))?$request->param('patTopic'):$pat['topic'],
            'status' => $pat['status'],
            'pattype' => !empty($request->param('patType'))?$request->param('patType'):$pat['pattype'],
            'patapplynum' => !empty($request->param('patApplyNum'))?$request->param('patApplyNum'):$pat['patapplynum'],
            'patauthnum' => !empty($request->param('patAuthNum'))?$request->param('patAuthNum'):$pat['patauthnum'],
            'applyplace' => !empty($request->param('patApplyPlace'))?$request->param('patApplyPlace'):$pat['applyplace'],
            'patadmin' => !empty($request->param('patAdmin'))?$request->param('patAdmin'):$pat['patadmin'],
            'patagency' => !empty($request->param('patAgency'))?$request->param('patAgency'):$pat['patagency'],
            'patrenewagency' => !empty($request->param('patRenewAgency'))?$request->param('patRenewAgency'):$pat['patrenewagency'],
            'patrenewapplynum' => !empty($request->param('patRenewApplyNum'))?$request->param('patRenewApplyNum'):$pat['patrenewapplynum'],
            'patrenewauthnum' => !empty($request->param('patRenewAuthNnum'))?$request->param('patRenewAuthNum'):$pat['patrenewauthnum'],
            'patowner' => !empty($request->param('patOwner'))?$request->param('patOwner'):$pat['patowner'],
            'inventor' => !empty($request->param('patInventor'))?$request->param('patInventor'):$pat['inventor'],
            'otherinventor' => !empty($request->param('patOtherInventor'))?$request->param('patOtherInventor'):$pat['otherinventor'],
            'author' => !empty($request->param('patAuthor'))?$request->param('patAuthor'):$pat['author'],
            'dept' => !empty($request->param('dept'))?$request->param('dept'):$pat['dept'],
            'keyword' => !empty($request->param('patKeyword'))?$request->param('patKeyword'):$pat['keyword'],
            'summary' => !empty($request->param('patSummary'))?$request->param('patSummary'):$pat['summary'],
            'resource' => !empty($request->param('patResource/a'))?$request->param('patResource/a'):$pat['resource'], //FSM_add??
            'issinfo_id' => $pat['issinfo_id'],
            'issinfo_topic' => $pat['issinfo_topic'],
            'statustime' => $pat['statustime'], //FSM_add
            'milestonetime' => $pat['milestonetime'], //FSM_add
            'addnewdate' => $pat['addnewdate'],
            //'status_time' => $patStatusTime,
            //'oprt_user' => $oprt_user
            );
        //数据表4：patrecord。各个字段赋值
        $patRecord = array(
            'num' => $pat['patnum'],
            'status' => $pat['status'],
            'milestonetime' => array(), //FSM_add
            'username' => $iss['oprt_user']['name'],
            'rolename' => $issAuth,
            'note' => '', //FSM_add
            'patinfo_id' => $patId,
            );
        //数据表5：attinfo。各个字段赋值
        $attInfo = array(
            'num_id' => $iss['issnum'],
            'name' => !empty($request->param('attName'))?$request->param('attName'):'',
            'atttype' => !empty($request->param('attType'))?$request->param('attType'):'',
            'attmap_id' => $issId,
            'attmap_type' => !empty($request->param('attMapType'))?$request->param('attMapType'):'',
            'uploadtime' => time(),
            'uploader' => $iss['oprt_user']['name'],
            'rolename' => $issAuth,
            'deldisplay' => 0,
            'attpath' => '',
            'attflename' => '',
            );
        //接收前端页面传来的附件文件信息
        //如果要通过$request->param()获取的数据为数组，要加上 /a 修饰符才能正确获取。
        if (!empty($request->param('attId/a'))) {
            $arrAttId = $request->param('attId/a');
            $arrAttFileName = $request->param('attFileName/a');
            $arrAttFileObjStr = $request->param('attFileObjStr/a');
        } else {
            $arrAttId = array();
            $arrAttFileName = array();
            $arrAttFileObjStr = array();
            ;
        }
        //part2：组装状态机要处理的数据，配置状态机参数
        //组装状态机（IssPatFSM）要处理的数据
        $data = array(
            'pat' => array(
                'id' => $patId,
                'info' => $patInfo,
                'record' => $patRecord),
            'iss' => array(
                'id' => $issId,
                'info' => $issInfo,
                'record' => $issRecord),
            'att' => array(
                'info' => $attInfo,
                'arrId' => $arrAttId,
                'arrFileName' => $arrAttFileName,
                'arrFileObjStr' => $arrAttFileObjStr));

        //配置启动状态机（IssPatFSM）的参数
        $param = array(
            'auth' => $issAuth,
            'status' => $issStatus,
            'oprt' => $issOprt);

        //part3：启动状态机处理数据，得到处理结果，返回前端处理结果
        //启动IssPatFSM处理$data，得到处理结果
        $msg = $fsm->setFSM($param)->result($data);

        //返回前端处理结果
        return json(array(
            'msg' => $msg,
            //'topic' => $issMdl::get($issId)->topic,
            'topic' => $issInfo['topic'],
            'patId' => $patId,
            'issId' => $issId));

    }

    //为前端显示PatRenew模板准备，1.数据库数据；2.向模板变量赋值；3.选择模板文件PatRenew.html返回前端
    public function patRenew(Request $request, PatinfoModel $patMdl, $patId = '', $auth = 'maintain')
    {
        $this->_loginUser();

        //调用模型文件Patinfo.php中定义的patRenew方法找出合适的pat。
        $pat = $patMdl->patRenew();


        // 查出所有的用户并分页，根据“strOrder”排序，前端页面显示的锚点（hash值）为$fragment，设定分页页数变量：“pageTotalNum”
        // 带上每页显示记录行数$totalTableRows，实现查询结果分页显示。
        //$issPatTotal = $issSet->where($map)
        //                            ->order($strOrder)
        //                            ->paginate($issPatTableRows,false,['type'=>'bootstrap','var_page' => 'pageTotalNum',
        //                            'query'=>['issPatTableRows'=>$issPatTableRows]]);
        // 获取分页显示
        $pageTotal = $pat->render();

        if ($patId == '' && !empty($pat[0])) {
            $patId = $pat[0]->id;
        }

        $this->assign(['home' => $request->domain(), //
            'maintainer' => $this->userName, 'dept' => $this->dept, 'pat' => $pat, 'patRenewTotal' => count($pat), 'pageTotal' => $pageTotal,
            'auth' => $auth, 'patId' => $patId]);

        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>_RENEW 模块开发中……<br/></p></div>';
        return view('index'.DS.'issPatAuth'.DS.'patRenew');
    }

    public function userInfo(Request $request,UserModel $userMdl)
    {
        $this->_loginUser();
        
        $user=$userMdl::get($this->userId);
        
        $userGroup=$userMdl->userGroupArr($this->userId);
        
        $this->assign([
                    'home' => $request->domain(),
                    
                    'user' => $user,
                    'userGroup' => $userGroup,
                    'userMobile'=>$user->getData('mobile')
                    ]);
        
        return view();


    }
    
    public function updateUserInfo(Request $request,UserModel $userMdl)
    {
        $this->_loginUser();
        
        $userId=(!empty($request->param('userId')))?$request->param('userId'):0;
        $userName=(!empty($request->param('userName')))?$request->param('userName'):0;
        $userMobile=(!empty($request->param('userMobile')))?$request->param('userMobile'):0;
        
        $n=2;
        $msgBody='';
        
        $user=$userMdl::get($this->userId);
        $mobile=$userMdl::get($this->userId)->getData('mobile');
        
        if($this->userId!=$userId){
            $msgBody.='表单数据错误。'; 
        }
        
        if(!$userName){
            $msgBody.='用户名不能为空。';
            $n=0;
        }else{
            //手机号唯一
            if($mobile==$userMobile){
                $msgBody.='手机号没变。';
                //修改pwd,模型的save方法，返回的是受影响的记录数。
                $n=$user->save(['username'=>$userName,'mobile'=>$userMobile]);
            }else if(count($userMdl->where('mobile',$userMobile)->select())){
                $msgBody.='手机号已存在。';
                $n=0;
            }else{
                //修改pwd,模型的save方法，返回的是受影响的记录数。
                $n=$user->save(['username'=>$userName,'mobile'=>$userMobile]);
            }
        }
        
        if($n==1){
            $msgBody='成功';
            $this->userName=$userName;
            session::set('username',$this->userName);
        }else if($n==0){
            $msgBody.='失败';
        }
        
        return ['code'=>$n,'msgHead'=>'【'.$this->userName.'】修改个人信息结果：','msgBody'=>$msgBody];

    }
    
    public function updateUserPwd(Request $request,UserModel $userMdl)
    {
        $this->_loginUser();
        
        $userId=(!empty($request->param('userId')))?$request->param('userId'):0;
        $originPwd=(!empty($request->param('originPwd')))?md5($request->param('originPwd')):0;
        $newPwd=(!empty($request->param('newPwd')))?md5($request->param('newPwd')):0;
        $n=2;
    
        $msgBody='';
        
        $user=$userMdl::get($this->userId);
        
        if($this->userId!=$userId){
            $msgBody.='表单数据错误。'; 
        }
        
        if($this->pwd!=$originPwd){
            $msgBody.='输入的原密码错误。';
        }else if($this->pwd==$newPwd){
            $msgBody.='输入的新密码与原密码相同。';
        }else{
            //修改pwd,模型的save方法，返回的是受影响的记录数。
            $n=$user->save(['pwd'=>$newPwd]);
        }
        
        if($n==1){
            $msgBody='成功';
            $this->pwd=$newPwd;
            session::set('pwd',$this->pwd);
        }else if($n==0){
            $msgBody.='失败';
        }
        
        return ['code'=>$n,'msgHead'=>'【'.$this->userName.'】修改密码结果：','msgBody'=>$msgBody];
    }

    public function attManage(Request $request)
    {

        //return ':)<br> attManage 模块开发中……';
        return view();

    }
    //检查前端(_EDIT、_EXECUTE的权限都需要)送来的topic是否已存在，返回前端检查结果（json格式）。
    public function checkPatTopic(Request $request, PatinfoModel $patMdl, $exist = '')
    {
        // $auth接收前端页面传来的auth值
        $auth = !empty($request->param('auth'))?$request->param('auth'):'done';
        // $patId接收前端页面传来的patId值
        $patId = !empty($request->param('patId'))?$request->param('patId'):0;

        $pat = $patMdl::get(['topic' => $request->param('topic')]);

        if ($patId) {
            //非新增专利
            if (count($pat)) {
                $exist = ($patId == $pat['id'])?0:1;
            } else {
                $exist = 0;
            }
        } else {
            //oprt="_ADDNEW",新增专利
            $exist = count($pat);
        }

        return array('exist' => $exist);
    }

    public function test(Request $request, AttinfoModel $attMdl)
    {
        $msg = '</br>';
        if (!empty($request->param('patId'))) {
            $patId = $request->param('patId');
        } else {
            $patId = 0;
        }

        if ($request->param('oprt') == '_ADDNEW') {
            $patId = 1;
        }

        $fileStr = '';
        $name = '';
        $newDir = '..'.DS.'uploads'.DS.'xx';

        //如果要获取的数据为数组，要加上 /a 修饰符才能正确获取。
        if (!empty($request->param('attId/a'))) {
            $arrAttId = $request->param('attId/a');
            $arrAttFileName = $request->param('attFileName/a');
            $arrAttFileObjStr = $request->param('attFileObjStr/a');

            $fileStr = $arrAttFileObjStr[0];
            $name = $arrAttFileName[0];

        } else {
            $arrAttId = array(0);
            $arrAttFileName = array(0);
            $name = 0;
        }

        //有‘temp’字符串才移动到指定目录
        if (substr_count($fileStr, 'temp')) {

            //调用AttinfoModel中定义的fileMove()方法，返回true后才更新Attinfo表
            if ($attMdl->fileMove($fileStr, $name, $newDir)) {

                $attData = array(
                    'num_id' => 0,
                    'attmap_id' => 0,
                    'attpath' => $newDir.DS.$name,
                    );

                //更新att
                $attId = $attMdl->attUpdate(array_merge($attData, $attDataPatch), $arrAttId[i]);

                $msg .= "附件".$fileStr."移动成功</br>";
            } else {
                $msg .= "附件".$fileStr."移动失败</br>";
            }
        }

        $data = array(
            'msg' => $msg,
            'topic' => $request->param('issPatTopic'),
            'patId' => $patId,
            'attId' => $arrAttId);
        return json($data);
        //return $data;
    }

    public function issThe(Request $request, $auth = 'done', $issId = '')
    {
        $this->_loginUser();

        // $auth接收前端页面传来的auth值
        if (!empty($request->param('auth'))) {
            $auth = $request->param('auth');
        } else {
            foreach ($this->authArr['iss'] as $key => $value) {
                if ($value) {
                    $auth = $key;
                    break;
                }
            }
        }
        // $issId接收前端页面传来issId值
        if (!empty($request->param('id'))) {
            $issId = $request->param('id');
        }

        //使用模型Issinfo
        $issSet = new IssinfoModel;
        //edit
        $mapEdit['status'] = ['in', ['填报', '返回修改', '修改完善']];
        $mapEdit['dept'] = $this->dept;
        $mapEdit['writer'] = $this->userName;
        $mapEdit['issmap_type'] = ['like', '%_ISST_THE%'];

        //audit
        $mapAudit['status'] = '待审核';
        $mapAudit['dept'] = $this->dept;
        $mapAudit['issmap_type'] = ['like', '%_ISST_THE%'];
        //approve
        $mapApprove['status'] = ['in', ['审核未通过', '审核通过', '变更申请', '拟续费']];
        $mapApprove['issmap_type'] = ['like', '%_ISST_THE%'];
        //execute
        $mapExecute['status'] = ['in', ['批准申报', '申报执行', '申报修改', '准予变更', '否决变更']];
        //$mapExecute['dept'] =$this->dept;
        $mapExecute['executer'] = $this->userName;
        $mapExecute['issmap_type'] = ['like', '%_ISST_THE%'];
        //maintain
        $mapMaintain['status'] = ['in', ['申报复核', '申报提交', '续费提交', '准予续费', '否决申报', '专利授权', '专利驳回', '放弃续费', '续费授权']];
        $mapMaintain['issmap_type'] = ['like', '%_ISST_THE%'];
        //done
        $map['status'] = '完结';
        $map['issmap_type'] = ['like', '%_ISST_THE%'];

        if ($this->authArr['iss']['edit']) {
            $numIssTheEdit = $issSet->where($mapEdit)->count();
            //$auth='_EDIT';
        } else {
            $numIssTheEdit = 0;
        }

        if ($this->authArr['iss']['audit']) {
            $numIssTheAudit = $issSet->where($mapAudit)->count();
            //$auth='_AUDIT';
        } else {
            $numIssTheAudit = 0;
        }

        if ($this->authArr['iss']['approve']) {
            $numIssTheApprove = $issSet->where($mapApprove)->count();
            //$auth='_APPROVE';
        } else {
            $numIssTheApprove = 0;
        }

        if ($this->authArr['iss']['execute']) {
            $numIssTheExecute = $issSet->where($mapExecute)->count();
            //$auth='_EXECUTE';
        } else {
            $numIssTheExecute = 0;
        }

        if ($this->authArr['iss']['maintain']) {
            $numIssTheMaintain = $issSet->where($mapMaintain)->count();

        } else {
            $numIssTheMaintain = 0;

        }

        $numIssTheDone = $issSet->where($map)->count();

        $numTotal = $numIssTheEdit + $numIssTheAudit + $numIssTheApprove + $numIssTheExecute + $numIssTheMaintain;

        $destr = "请求方法:".$request->method()."<br/>"."username:".$this->userName."<br/>"."pwd:".$this->pwd."<br/>"."log:".$this->
            log."<br/>"."auth:".json_encode($this->authArr);

        // 模板变量赋值
        $this->assign([ //在usercenter.html页面输出自定义的信息
            //在index.html页面通过destr输出自定义的信息
        'destr' => $destr."</br>", 'home' => $request->domain(), 'username' => $this->userName, //向前端权限变量赋值
            //所有权限
        'authArray' => $this->authArr, //当前权限
            'auth' => $auth, 'numIssTheEdit' => $numIssTheEdit, 'numIssTheAudit' => $numIssTheAudit, 'numIssTheApprove' => $numIssTheApprove,
            'numIssTheExecute' => $numIssTheExecute, 'numIssTheMaintain' => $numIssTheMaintain, 'numIssTheDone' => $numIssTheDone,
            'numTotal' => $numTotal, 'issId' => $issId, ]);
        return view();
        //return ':)<br> issthe 模块开发中……';

    }

    public function issPro(Request $request, $auth = 'done', $issId = '')
    {
        $this->_loginUser();

        // $auth接收前端页面传来的auth值
        if (!empty($request->param('auth'))) {
            $auth = $request->param('auth');
        } else {
            foreach ($this->authArr['iss'] as $key => $value) {
                if ($value) {
                    $auth = $key;
                    break;
                }
            }
        }
        // $issId接收前端页面传来issId值
        if (!empty($request->param('id'))) {
            $issId = $request->param('id');
        }

        //使用模型Issinfo
        $issSet = new IssinfoModel;
        //edit
        $mapEdit['status'] = ['in', ['填报', '返回修改', '修改完善']];
        $mapEdit['dept'] = $this->dept;
        $mapEdit['writer'] = $this->userName;
        $mapEdit['issmap_type'] = ['like', '%_ISST_PRO%'];

        //audit
        $mapAudit['status'] = '待审核';
        $mapAudit['dept'] = $this->dept;
        $mapAudit['issmap_type'] = ['like', '%_ISST_PRO%'];
        //approve
        $mapApprove['status'] = ['in', ['审核未通过', '审核通过', '变更申请', '拟续费']];
        $mapApprove['issmap_type'] = ['like', '%_ISST_PRO%'];
        //execute
        $mapExecute['status'] = ['in', ['批准申报', '申报执行', '申报修改', '准予变更', '否决变更']];
        //$mapExecute['dept'] =$this->dept;
        $mapExecute['executer'] = $this->userName;
        $mapExecute['issmap_type'] = ['like', '%_ISST_PRO%'];
        //maintain
        $mapMaintain['status'] = ['in', ['申报复核', '申报提交', '续费提交', '准予续费', '否决申报', '专利授权', '专利驳回', '放弃续费', '续费授权']];
        $mapMaintain['issmap_type'] = ['like', '%_ISST_PRO%'];
        //done
        $map['status'] = '完结';
        $map['issmap_type'] = ['like', '%_ISST_PRO%'];

        if ($this->authArr['iss']['edit']) {
            $numIssProEdit = $issSet->where($mapEdit)->count();
            //$auth='_EDIT';
        } else {
            $numIssProEdit = 0;
        }

        if ($this->authArr['iss']['audit']) {
            $numIssProAudit = $issSet->where($mapAudit)->count();
            //$auth='_AUDIT';
        } else {
            $numIssProAudit = 0;
        }

        if ($this->authArr['iss']['approve']) {
            $numIssProApprove = $issSet->where($mapApprove)->count();
            //$auth='_APPROVE';
        } else {
            $numIssProApprove = 0;
        }

        if ($this->authArr['iss']['execute']) {
            $numIssProExecute = $issSet->where($mapExecute)->count();
            //$auth='_EXECUTE';
        } else {
            $numIssProExecute = 0;
        }

        if ($this->authArr['iss']['maintain']) {
            $numIssProMaintain = $issSet->where($mapMaintain)->count();

        } else {
            $numIssProMaintain = 0;

        }

        $numIssProDone = $issSet->where($map)->count();

        $numTotal = $numIssProEdit + $numIssProAudit + $numIssProApprove + $numIssProExecute + $numIssProMaintain;

        $destr = "请求方法:".$request->method()."<br/>"."username:".$this->userName."<br/>"."pwd:".$this->pwd."<br/>"."log:".$this->
            log."<br/>"."auth:".json_encode($this->authArr);

        // 模板变量赋值
        $this->assign([ //在usercenter.html页面输出自定义的信息
            //在index.html页面通过destr输出自定义的信息
        'destr' => $destr."</br>", 'home' => $request->domain(), 'username' => $this->userName, //向前端权限变量赋值
            //所有权限
        'authArray' => $this->authArr, //当前权限
            'auth' => $auth, 'numIssProEdit' => $numIssProEdit, 'numIssProAudit' => $numIssProAudit, 'numIssProApprove' => $numIssProApprove,
            'numIssProExecute' => $numIssProExecute, 'numIssProMaintain' => $numIssProMaintain, 'numIssProDone' => $numIssProDone,
            'numTotal' => $numTotal, 'issId' => $issId, ]);
        return view();
        //return ':)<br> issthe 模块开发中……';

    }

    public function attOprt(Request $request, AttinfoModel $attMdl, $attOprt = '', $attId = '')
    {
        $this->_loginUser();

        $attOprt = empty($request->param('attOprt'))?0:$request->param('attOprt');

        $attId = empty($request->param('attId'))?0:$request->param('attId');

        switch ($attOprt) {
            case '_DOWNLOAD':
                $returnArr = $attMdl->singleDownload($attId);
                break;
            case '_DELETE':
                $returnArr = $attMdl->singleDelete($attId);
                break;


        }


        return $returnArr; //$returnArr=array('result'=>true/false,'msg'=>'string')
    }

    public function attFileList(Request $request, IssinfoModel $issMdl, AttinfoModel $attMdl, $issId = '', $resultSet = '')
    {
        $this->_loginUser();

        $issId = empty($request->param('issId'))?0:$request->param('issId');
        $resultSet = ($request->param('resultSet/a') == null)?array(
            'result' => 0,
            'msg' => 0,
            'obj' => 0):$request->param('resultSet/a'); //$resultSet=['result'=>true/false,'msg'=>string,'obj'=>Object]
        //$resultSet=array_merge(array('result'=>0,'msg'=>0,'obj'=>22),$request->param('resultSet/a'));
        if ($issId) {
            //得到模板文件中需显示的内容
            $iss = $issMdl::get($request->param('issId'));
            // 利用模型issinfo.php中定义的一对多方法“attachments”得到iss对应的attachments信息
            $att = $iss->attachments;

        } else {

            //查询当前用户已上传的所有附件信息
            $att = $attMdl::all(['attmap_id' => 0, 'uploader' => $this->userName, 'rolename' => 'edit', 'deldisplay' => 1]);

        }

        $this->assign(['home' => $request->domain(), 'username' => $this->userName,
            // 'resultSet'=>json_encode($request->param('resultSet/a'),JSON_UNESCAPED_UNICODE),
            //'resultSet'=>$request->param('resultSet/a')['msg'],
        'resultSet' => $resultSet, 'att' => $att]);


        return view('index'.DS.'issPatAuthSingle'.DS.'attFileList'); //$returnArr=array('result'=>true/false,'msg'=>'string')
    }
    //响应前端请求，返回信息
    public function selectResponse(Request $request,IssinfoModel $issMdl,$req='',$process='_TODO',$issType='_PAT')
    {
      $this->_loginUser();
      
      $req = empty($request->param('req'))?0:$request->param('req');
      $process = empty($request->param('process'))?'_TODO':$request->param('process');
      $issType = empty($request->param('issType'))?'_PAT':$request->param('issType');
      
      $field=[];
      
      switch($req){
        case '_DEPT':
          $field='dept';
        break;
        
        case '_STATUS':
          $field='status';
        break;
      }
      
      switch($issType){
        case '_PAT':
          //调用issinfo模型的查询方法issPatProcess()得到仅含$field字段的查询结果数据集(数组)
          $res=$issMdl->issPatProcess($this->logUser,$process,[$field]);
          
        break;
        
        case '_THE':
         
        break;
      }
      
      //将得到的数据集降为一维数组
      if(is_array($res)){
        $res=collection($res)->column($field);        
      }else{
        $res=$res->column($field);
      }
      //数组去重
      $res=array_unique($res);
      //对得到的数组进行排序
      sort($res);
      //natsort($res); 
      
      //返回前端的是索引数组  
      return $res;
    }
    
   
}
