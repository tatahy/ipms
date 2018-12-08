<?php
namespace app\dashboard\controller;

use think\Request;
use think\Session;
use think\View;
use think\Controller;

use app\dashboard\model\Issinfo as IssinfoModel;
use app\dashboard\model\Issrecord as IssrecordModel;
use app\dashboard\model\Patinfo as PatinfoModel;
use app\dashboard\model\Proinfo as ProinfoModel;
use app\dashboard\model\Theinfo as TheinfoModel;
use app\dashboard\model\Assinfo as AssinfoModel;
use app\dashboard\model\Assrecord as AssrecordModel;
use app\dashboard\model\User as UserModel;
use app\dashboard\model\Dept as DeptModel;

class IssueController extends Controller
{
     //用户名
    private $userName = null;
    //用户密码
    private $pwd = null;
    //用户登录状态
    private $log = null;
    //用户所在部门
    private $dept = null;
    //登录用户的权限。
    private $auth=[];
    //请求对象域名
    private $home = '';
    //请求的issEntName
    private $issEntName = '';    
    //issCONF数组
    const ISS_CONF=conIssConf;
  
    //public function __construct(Request $request)
//    {
//      $this->priLogin();
//    }
    // 初始化
    protected function _initialize()
    {
        $this->userName=Session::get('username');
        $this->pwd=Session::get('pwd');
        $this->log=Session::get('log');
        $this->dept=Session::get('dept');
         //继承了控制器基类Controller后，直接可使用其request属性来使用Request类的实例。
        $this->home=$this->request->domain();
        $this->issEntName=$this->request->param('issEntName');
        $this->auth=UserModel::where(['userName'=>$this->userName,'pwd'=>$this->pwd])->find()->authority['iss'];
        //使用模型前的初始化，为模型内部使用的变量赋初值，后续的各个方法中无需再初始化，但可以进行修改
        IssinfoModel::initModel($this->userName,$this->dept,$this->auth,$this->issEntName); 
        
    }
    //
    private function priLogin()
    {
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$this->log){
            $this->error('未登录用户，请先登录系统');
            //$this->redirect($request->domain());
        }
    }
        
    public function index(Request $request,IssinfoModel $issMdl,AssinfoModel $assMdl,$sortData=[],$searchData=[])
    {
      $this->priLogin();
      $issEntName=!empty($this->request->param('issEntName'))?$this->request->param('issEntName'):'_PAT';
      $issEntNameChi=self::ISS_CONF[$issEntName]['entNameChi'];
      $issStatus=!empty($this->request->param('issStatus'))?$this->request->param('issStatus'):'_INPROCESS';
      
      
      $sortData=array('listRows'=>10,'sortName'=>'issnum','sortOrder'=>'asc','pageNum'=>1,
                      'showIssId'=>0,'issEntName'=>$issEntName,'issStatus'=>$issStatus);
      
      $numArr=$issMdl::getNumArr();
      
      $this->assign([
        'home'=>$this->home,
        'issEntName'=>$issEntName,
        'issEntNameChi'=>$issEntNameChi,
        'sortData'=>$sortData,
        'numArr'=>json_encode($numArr)
      ]);
      
      return view();
    }
    //对单个iss记录进行增删改查操作
    public function issOprt(IssinfoModel $issMdl)
    {
      $this->priLogin();
      $req=$this->request;
      $dataIss=$req->param('dataIss/a');
      $dataEnt=$req->param('dataEnt/a');
      $data=$req->param();
      
      //前端接收时数组被视为json对象
      return ['res'=>1,$data];
    }
    
    //返回前端issue的排序、查询后分页显示所需的记录集
    public function issList(IssinfoModel $issMdl)
    {
      $this->priLogin();
      $request=$this->request;
      //引入self::ISS_CONF 
      $issConf=self::ISS_CONF;
      $searchDefaults=array();
      $sortDefaults=array('listRows'=>10,'sortName'=>'issnum','sortOrder'=>'asc','pageNum'=>1,
                          'showIssId'=>0,'issEntName'=>'_PAT','issStatus'=>'_INPROCESS');
      
      // 接收前端的排序参数数组
      $sortData=!empty($request->param('sortData/a'))?$request->param('sortData/a'):$sortDefaults;
      $sortData=array_merge($sortDefaults,$sortData);
      // 接收前端的搜索参数数组，由前端保证传来的搜索参数值非0，非空。
      $searchData=!empty($request->param('searchData/a'))?$request->param('searchData/a'):$searchDefaults;
      $searchData=array_merge($searchDefaults,$searchData);
      
      //关联对象名称
      $entName=$sortData['issEntName'];
      //关联属性/方法名称
      $mdlMethod=$issConf[$entName]['relMethod'];
      //关联对象的数据模型名
      $relModelName=$issConf[$entName]['relEntModelName'];       
      //关联对象共有的字段名称与前端的对应关系
      $tblFieldAsArr=$issConf['relTblFieldsFEName'];
      //关联对象共有的字段名称
      $tblFields=$issConf['relEntTblCommonFields'];
      //关联对象的数据模型对象实例
      $relMdl='';
      //进行排序的关联对象字段名
      $entSortName='';
            
      //根据'_INPROCESS'字符串出现在$sortData['issStatus']中的次数
      $issStatus=substr_count($sortData['issStatus'],'_INPROCESS')?'_INPROCESS':$sortData['issStatus'];
       //返回前端进行显示的内容
      $issList=array();
      
      //iss中进行搜索的条件数组
      $whereArr=[];
      //关联对象中进行搜索的条件数组
      $whereEntArr=[];
      
      switch($relModelName){
        case 'Patinfo':
          $relMdl = new PatinfoModel;
          break;
        case 'Proinfo':
          $relMdl = new ProinfoModel;
          break;
        case 'Theinfo':
          $relMdl = new TheinfoModel;
          break;
      }
    
      //前端输入的关键字搜索,like关键字，issue，4个
      $whereArr['topic']=!empty($searchData['topic'])?['like','%'.$searchData['topic'].'%']:'';
      $whereArr['writer']=!empty($searchData['writer'])?['like','%'.$searchData['writer'].'%']:'';
      $whereArr['registrar']=!empty($searchData['registrar'])?['like','%'.$searchData['registrar'].'%']:'';
      $whereArr['issnum']=!empty($searchData['issnum'])?['like','%'.$searchData['issnum'].'%']:'';
      //前端select值搜索，=select值，issue，2个
      $whereArr['dept']=!empty($searchData['dept'])?['in',$searchData['dept']]:'';
      $whereArr['status']=!empty($searchData['status'])?['in',$searchData['status']]:'';
      
      //前端输入的关键字搜索,like关键字，关联对象，1个
      $whereEntArr['topic']=!empty($searchData['issEntName'])?['like','%'.$searchData['issEntName'].'%']:'';
      //前端select值搜索，=select值，关联对象，2个
      $whereEntArr['type']=!empty($searchData['issEntType'])?['in',$searchData['issEntType']]:'';
      $whereEntArr['status']=!empty($searchData['issEntStatus'])?['in',$searchData['issEntStatus']]:'';
       
      //将空白元素删除
      foreach($whereArr as $key=>$val){
        if(empty($val)){
          unset($whereArr[$key]);
        }
      }
      //将空白元素删除
      foreach($whereEntArr as $key=>$val){
        if(empty($val)){
          unset($whereEntArr[$key]);
        }
      }
      //是否对关联对象的字段进行排序，是就$sortData['sortName']=''
      foreach($tblFieldAsArr as $key=>$val){
        if($sortData['sortName']==$key){
          //关联对象排序字段名
          $entSortName=$val;
          $sortData['sortName']='';
        }
      }
      //iss模型对象，查询、排序用
      $queryBase=$issMdl->issStatusQuery($issStatus,$entName)->where($whereArr)
                        ->order($sortData['sortName'],$sortData['sortOrder']);
      
      //查询、排序结果数据集：
      $baseSet=$queryBase->select();
      $baseSet=is_array($baseSet)?collection($baseSet):$baseSet;
      
      $issmapIdArr=$baseSet->column('issmap_id');
      //查询、排序结果总数，隐含着count($issmapIdArr)==$baseSet->count()
      $searchResultNum=count($issmapIdArr);
      
      //--block start
      
      if($searchResultNum){
        //本页要显示的记录
        $issList=$baseSet->slice(($sortData['pageNum']-1)*$sortData['listRows'],$sortData['listRows']);
        //涉及关联对象$entName中字段的排序、查询 
        if($entSortName || count($whereEntArr)){
          $relQuery=$relMdl->field($tblFields)
                          ->where('id','in',$issmapIdArr)
                          ->where($whereEntArr)
                          ->order($entSortName,$sortData['sortOrder']);
      
          //$issmapIdArr中的值是否能有重复值？若可以有要怎么处理？ HY:2018/12/07
          $relSet=$relQuery->select();
                          
          //关联对象数据集
          $relSet=is_array($relSet)?collection($relSet):$relSet;
          
          $searchResultNum=$relSet->count();
          //关联对象有查询结果
          if($searchResultNum){
            
            $issmapIdArr=$relSet->column('id');
            //从排序后的数组中截取本页所需的部分，取出id字段值
            $idArr=$relSet->slice(($sortData['pageNum']-1)*$sortData['listRows'],$sortData['listRows'])->column('id');
            $issList=[];
            //组装本页的issList。根据上一步中得到的arr，从数据集$baseSet中抽出对应的iss记录，将抽出记录按照arr的顺序组装好。
            foreach($idArr as $key=>$val){
              for($i=0;$i<$baseSet->count();$i++){
                if($baseSet[$i]['issmap_id']==$val){
                  $issList[$key]=$baseSet[$i];
                  break;
                }
              }
            }
            //延迟载入关联查询方法load()得到含有关联对象数据的数据集，
            //仅拉取关联对象共有的字段名，设置可见字段，减小向前端传输的数据量
            //$issList=collection($issList)->load([$mdlMethod=>function($query) use($tblFields) {
  //                                  $query->field($tblFields);}])->visible(['id']);
          }

        }
      }
      //iss模型对象，排序、查询后分页用
      $pageQuery=$issMdl->issStatusQuery($issStatus,$entName)->where('issmap_id','in',$issmapIdArr)
                      ->order($sortData['sortName'],$sortData['sortOrder']);
      
      //符合查询条件的所有iss记录分页,每页$listRows条记录
      $pageSet=$pageQuery->paginate($sortData['listRows'],false,['type'=>'bootstrap','var_page' =>'pageNum','page'=>$sortData['pageNum'],
                        'query'=>['listRows'=>$sortData['listRows']]]);       
      //--block end
      
      $this->assign([
        'home'=>$this->home,
        'issEntName'=>$entName,
        'pageSet'=>$pageSet,
        'issList'=>$issList,
        'issTest'=>json_encode('no debug info',JSON_UNESCAPED_UNICODE),
        'mdlMethod'=>$mdlMethod,
        //排序数组
        'sortData'=>$sortData,
        //将数组转换为json字符串，编码为Unicode字符（\uxxxx）。
        //前端就可以直接作为json对象使用，若前端文件采用utf-8编码，汉字也可直接解析显示。
        'searchData'=>json_encode($searchData,JSON_UNESCAPED_UNICODE),
        //记录总数
        'searchResultNum'=>$searchResultNum 
      ]);
      
      return view();
    }
    
    public function searchFormSelData(IssinfoModel $issMdl,DeptModel $deptMdl)
    {
      $this->priLogin();
      $request=$this->request;
      $issConf=self::ISS_CONF;
      // 接收前端的参数
      $entName=$request->param('issEntName');
      $issStatus=substr_count($request->param('issStatus'),'_INPROCESS')?'_INPROCESS':$request->param('issStatus');
      $selNameArr=$request->param('nameArr/a');//nameArr=['status','dept','issEntType','issEntStatus']
      
      $tblFields=$issConf['relEntTblCommonFields'];
      //iss获取关联对象的方法
      $mdlMethod=$issConf[$entName]['relMethod'];
      //iss对象状态数组
      $issStatusArr=$issConf[$entName]['status'];    
      //iss关联对象状态数组
      $issRelStatusArr=$issConf[$entName]['relStatus'];
      //iss关联对象类型数组
      $issRelTypeArr=$issConf[$entName]['relType'];
      
      //iss涉及的部门数组
      $issDeptArr=$deptMdl->getNameAbbrVSFull();
      
      $colArr=[];
    
      //生成iss查询结果数据集：
      $baseSet=$issMdl->issStatusQuery($issStatus,$entName)->select();
      if(is_array($baseSet)){
          $baseSet=collection($baseSet);
      }
      
      //延迟载入关联查询方法load()得到含有关联对象数据的数据集，仅拉取关联对象共有的字段名
      $baseSet->load([$mdlMethod=>function($query) use($tblFields) {
                                  $query->field($tblFields);}]);
      //关联对象数据集
      $relSet=collection($baseSet->column($mdlMethod));
      //组装返回前端的数组$selResData
      foreach($selNameArr as $key=>$val){
        switch($val){
          case 'issEntType':
            $colArr=$relSet->column('type');
            $parentArr=$issRelTypeArr;
            break;
          case 'issEntStatus':
            $colArr=$relSet->column('status');
            $parentArr=$issRelStatusArr;
            break;
          case 'status':
            $colArr=$baseSet->column($val);
            $parentArr=$issStatusArr;
            break;
          case 'dept':
            $colArr=$baseSet->column($val);
            $parentArr=$issDeptArr;
            break;  
          default:
            //$colArr=$baseSet->column($val);
            //$arr=['key1'=>'value1'];
            break;                                  
        }
        $colArr=get_child_array($parentArr,$colArr,'VALUE');
        //组装返回前端的数组
        $selResData[$key] = ['name'=>$val,'key'=>array_keys($colArr),'value'=>array_values($colArr)];
        $colArr=[];
      }
      //前端接收时数组被视为json对象
      return $selResData;
    }
    
    //响应前端请求，返回信息
    public function selectRes(Request $request,AssinfoModel $assMdl,$req='',$source='db',$assType='_ASSS_USUAL')
    {
      $this->priLogin();
      
      $req = !empty($request->param('req'))?$request->param('req'):0;
      $source = !empty($request->param('source'))?$request->param('source'):0;
      $oprt=!empty($request->param('oprt'))?$request->param('oprt'):0;
      $assType=!empty($request->param('assType'))?$request->param('assType'):'_ASSS_USUAL';
      $statusNow=!empty($request->param('statusNow'))?$request->param('statusNow'):'';
      //$arr=conAssOprtChangeStatusArr;
      $arr=conAssStatusOprtArr;
      
      if($source=='common' && $req=='status_now'){
        //for($i=0;$i<count($arr);$i++){
//          if($oprt==$arr[$i]['oprt']){
//            $res=$arr[$i]['statusChangeTo'];
//          }
//        }
        for($i=0;$i<count($arr);$i++){
          if($statusNow==$arr[$i]['statusChi']){
            foreach($arr[$i]['nextStatus'] as $key=>$val){
              if($oprt==$key){
                $res=$val;
              }
            }
            break;
          }
        }
        
      }else{
        //从数据库获得数据
        $res=$this->priAssQueryObj($assType)->field($req)->group($req)->select();
        //将得到的数据集降为一维索引数组
        if(is_array($res)){
            $res=collection($res)->column($req);        
        }else{
            $res=$res->column($req);
        }
      }
         
      //返回前端的是索引数组  
      return $res;
    }
    
    public function fmIssSingle(IssinfoModel $issMdl,$id=0,$oprt='')
    {
      $this->priLogin();
      $request=$this->request;
      $id=$request->param('id')?$request->param('id'):0;
      $oprt=$request->param('oprt');
      $statusEn='*';
      $auth=$this->auth;
      $issSingleArr=array('id'=>$id,
                          'topic'=>'',
                          'issnum'=>$issMdl->getNewIssNum(),
                          'status'=>'*',
                          'statusdescription'=>'',
                          'dept'=>$this->dept,
                          'writer'=>$this->userName,
                        );
     
      $issSingle=$id?$issMdl::get($id):$issSingleArr;
      
      $this->assign([
          'home'=>$request->domain(),
          'oprt'=>$oprt,
          'issSingle'=>$issSingle,
          'userName'=>$this->userName,
          'auth'=>$auth
        ]);
      return view();
    }
    
     public function tblAssSingle(Request $request,AssinfoModel $assMdl)
    {
      $this->priLogin();
      $id=$request->param('id');
      //不是usual就是trashed
      $assSet=count($assMdl::get($id))?$assMdl::get($id):$assMdl::onlyTrashed()->where('id',$id)->find();
           
      $this->assign([
          'assSet'=>$assSet,
          
          'conAssStatusArr'=>json_encode(conAssStatusArr,JSON_UNESCAPED_UNICODE), 
          'conAssStatusLabelArr'=>json_encode(conAssStatusLabelArr,JSON_UNESCAPED_UNICODE),   
         
        ]);
      return view();
    }
    
    //应用AssFSM
    //1.需要前端提供
    //1.1启动fsm必须的参数：$param=array('auth'=>'_EDIT','status'=>'填报','oprt'=>'_ADDNEW');
    //1.2fsm要处理的对象/数据：
    //2.程序结构
    //part1：变量赋初值
    //part2：配置状态机参数，组装状态机要处理的数据，
    //part3：启动状态机处理数据，得到处理结果，返回前端处理结果
    public function oprtAssFSM(Request $request, AssinfoModel $assMdl, AssFSM $fsm)
    {
        //part1：变量赋初值
        
        
        //part2：配置状态机参数，组装状态机要处理的数据，
        //组装状态机（IssPatFSM）要处理的数据
        $data = array(
                    'ass' => array(
                    'id' => $assId,
                    'info' => $assInfo,
                    'record' => $assRecord)
                );
        
        //配置启动状态机（AssFSM）的参数
        $param = array(
            'auth' => $assAuth,
            'status' => $assStatus,
            'oprt' => $assOprt);
        
        //part3：启动状态机处理数据，得到处理结果，返回前端处理结果
        //启动AssFSM处理$data，得到处理结果
        $msg = $fsm->setFSM($param)->result($data);

        //返回前端处理结果
        return json(array(
            'msg' => $msg,
            //'topic' => $issMdl::get($issId)->topic,
            'topic' => $assInfo['topic'],
            'assId' => $assId)
            );
        
        
    }
    
}
