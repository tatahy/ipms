<?php
namespace app\dashboard\controller;

use think\Request;
use think\Session;
use think\View;
use think\Controller;

use app\dashboard\model\Issinfo as IssinfoModel;
use app\dashboard\model\Issrecord as IssrecordModel;
use app\dashboard\model\Patinfo as PatinfoModel;
use app\dashboard\model\Assinfo as AssinfoModel;
use app\dashboard\model\Assrecord as AssrecordModel;
use app\dashboard\model\User as UserModel;

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
    
    //关联对象名称及关联方法对应关系数组
    const ENT_NAME_METHOD=['_PAT'=>'pat_list','_THE'=>'the_list','_PRO'=>'pro_list'];
    //关联对象共有的字段名称与前端变量名的对应关系
    const FENAME_TBLFIELD=['issEntName'=>'topic','issEntType'=>'type','issEntStatus'=>'status'];
    //关联对象共有的字段名称
    const TBL_FIELDS=['id','topic','type','status'];
    //issStatus数组
    const ISS_STATUS=['_PAT'=>conIssPatStatusArr,'_THE'=>conIssTheStatusArr,'_PRO'=>conIssProStatusArr];
    //iss关联对象Status数组
    const REL_STATUS=['_PAT'=>conPatStatusArr,'_THE'=>conTheStatusArr,'_PRO'=>conProStatusArr];
    //iss关联对象type数组
    const REL_TYPE=['_PAT'=>conPatTypeArr,'_THE'=>conTheTypeArr,'_PRO'=>conProTypeArr];
    
    //issCONF数组
    const ISS_CONF=['_PAT'=>['status'=>conIssPatStatusArr,
                              'relMethod'=>'pat_list',
                              'relStatus'=>conPatStatusArr,
                              'relType'=>conPatTypeArr],
                    '_THE'=>['status'=>conIssTheStatusArr,
                              'relMethod'=>'the_list',
                              'relStatus'=>conTheStatusArr,
                              'relType'=>conTheTypeArr],
                    '_PRO'=>['status'=>conIssProStatusArr,
                              'relMethod'=>'pro_list',
                              'relStatus'=>conProStatusArr,
                              'relType'=>conProTypeArr],
                    'relCommonFields'=>['id','topic','type','status']];
  
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
      $issEntName=!empty($this->request->param('issEntName'))?$this->request->param('issEntName'):'';
      $issStatus=!empty($this->request->param('issStatus'))?$this->request->param('issStatus'):'_INPROCESS';
      
      
      $sortData=array('listRows'=>10,'sortName'=>'issnum','sortOrder'=>'asc','pageNum'=>1,
                      'showIssId'=>0,'issEntName'=>$issEntName,'issStatus'=>$issStatus);
      
      $numArr=$issMdl::getNumArr();
      
      $this->assign([
        'home'=>$this->home,
        'issEntName'=>$issEntName,
        'sortData'=>$sortData,
        'numArr'=>json_encode($numArr)
      ]);
      
      return view();
    }
    
    public function issList(IssinfoModel $issMdl,PatinfoModel $patMdl)
    {
      $this->priLogin();
      $request=$this->request;
      
      //关联对象名称
      $entName='';
       //进行排序的关联对象字段名
      $entSortName='';
      
      //关联对象共有的字段名称与前端的对应关系
      $tblFieldAsArr=self::FENAME_TBLFIELD;
      //关联对象共有的字段名称
      $tblField=self::TBL_FIELDS;
      
      //关联方法名称
      $mdlMethod='';
      //搜索查询条件数组
      $whereArr=[];
      $searchDefaults=array();
      $sortDefaults=array('listRows'=>10,'sortName'=>'issnum','sortOrder'=>'asc','pageNum'=>1,
                          'showIssId'=>0,'issEntName'=>'_PAT','issStatus'=>'_INPROCESS');
      
      // 接收前端的排序参数数组
      $sortData=!empty($request->param('sortData/a'))?$request->param('sortData/a'):$sortDefaults;
      $sortData=array_merge($sortDefaults,$sortData);
      // 接收前端的搜索参数数组，由前端保证传来的搜索参数值非0，非空。
      $searchData=!empty($request->param('searchData/a'))?$request->param('searchData/a'):$searchDefaults;
      $searchData=array_merge($searchDefaults,$searchData);
      $entName=$sortData['issEntName'];
      //'_INPROCESS'字符串出现在$sortData['issStatus']中的次数
      $issStatus=substr_count($sortData['issStatus'],'_INPROCESS')?'_INPROCESS':$sortData['issStatus'];
       //返回前端进行显示的内容
      $issList=array();
    
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
      //根据关联对象名得到关联查询方法
      foreach(self::ENT_NAME_METHOD as $key => $val){
        if($entName==$key){
          $mdlMethod=$val;
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
      //
      $queryBase=$issMdl->issStatusQuery($issStatus,$entName)->where($whereArr)
                      ->order($sortData['sortName'],$sortData['sortOrder']);
      
       //符合查询条件的所有iss记录分页,每页$listRows条记录
      $pageSet=$issMdl->issStatusQuery($issStatus,$entName)->where($whereArr)
                      ->order($sortData['sortName'],$sortData['sortOrder'])
                      ->paginate($sortData['listRows'],false,['type'=>'bootstrap','var_page' =>'pageNum','page'=>$sortData['pageNum'],
                        'query'=>['listRows'=>$sortData['listRows']]]);
      
      //生成查询结果数据集：
      $baseSet=$queryBase->select();
      if(is_array($baseSet)){
          $baseSet=collection($baseSet);
      } 
      
      //搜索记录总数
      $searchResultNum=$baseSet->count();
      
      if($searchResultNum){
        //延迟载入关联查询方法load()得到含有关联对象数据的数据集，仅拉取关联对象共有的字段名
        $baseSet->load([$mdlMethod=>function($query) use($tblField) {
                                  $query->field($tblField);}]);
        //关联对象数据集
        $relSet=collection($baseSet->column($mdlMethod));
      }
                            
       //涉及关联对象$entName中字段的排序   
      if($entSortName){
        
        //组合id与待排序的字段值
        $testSet=array_combine($baseSet->column('id'),$relSet->column($entSortName));
        
        //对待排序的字段值进行自然排序，保持键值关系
        if($sortData['sortOrder']=='asc'){
          asort($testSet,SORT_FLAG_CASE);
        }else{
          arsort($testSet,SORT_FLAG_CASE);
        }
        
        //从排序后的数组中截取本页所需的部分，保持键值对应关系“true”
        $testSet=array_slice($testSet,($sortData['pageNum']-1)*$sortData['listRows'],$sortData['listRows'],true);
        //得到id值（数组） 
        $idArr=array_keys($testSet);
        
        //根据上一步中得到的$idArr，从数据集$baseSet中抽出与id对应的iss记录，将抽出记录按照$idArr的顺序组装好。
        foreach($idArr as $key=>$val){
          for($i=0;$i<$baseSet->count();$i++){
            if($baseSet[$i]['id']==$val){
              $issList[$key]=$baseSet[$i];
              break;
            }
          }
        }
      }else{
        $issList=$baseSet->slice(($sortData['pageNum']-1)*$sortData['listRows'],$sortData['listRows']);
        $testSet=[':)testing'];                
      }       
      
      $this->assign([
        'home'=>$this->home,
        'issEntName'=>$entName,
        'pageSet'=>$pageSet,
        'issList'=>$issList,
        'issTest'=>json_encode($testSet,JSON_UNESCAPED_UNICODE),
        
        'mdlMethod'=>$mdlMethod,
        
        //排序数组
        'sortData'=>$sortData,
        //记录总数
        'searchResultNum'=>$searchResultNum 
      ]);
      
      return view();
    }
    
    public function searchFormSelData(IssinfoModel $issMdl)
    {
      $this->priLogin();
      $request=$this->request;
      $issConf=self::ISS_CONF;
      $tblField=$issConf['relCommonFields'];
      //iss获取关联对象的方法
      $mdlMethod='';
      //iss对象状态数组
      $issStatusArr=[];    
      //iss关联对象状态数组
      $issRelStatusArr=[];
      //iss关联对象类型数组
      $issRelTypeArr=[];
      
      // 接收前端的参数
      $entName=$request->param('issEntName');
      $issStatus=substr_count($request->param('issStatus'),'_INPROCESS')?'_INPROCESS':$request->param('issStatus');
      $selObjArr=$request->param('selObj/a');//$selObjArr=['status','dept','issEntType','issEntStatus']
      $colArr=[];
      //根据关联对象名(_PAT/_PRO/_THE)得到关联查询方法
      foreach($issConf as $key => $val){
        if($entName==$key){
          $mdlMethod=$val['relMethod'];
          $issStatusArr=$val['status'];
          $issRelStatusArr=$val['relStatus'];
          $issRelTypeArr=$val['relType'];
          //$selResData[$key]=$val;
        }
      }
      
      //生成iss查询结果数据集：
      $baseSet=$issMdl->issStatusQuery($issStatus,$entName)->select();
      if(is_array($baseSet)){
          $baseSet=collection($baseSet);
      }
      
      //延迟载入关联查询方法load()得到含有关联对象数据的数据集，仅拉取关联对象共有的字段名
      $baseSet->load([$mdlMethod=>function($query) use($tblField) {
                                  $query->field($tblField);}]);
      //关联对象数据集
      $relSet=collection($baseSet->column($mdlMethod));
      
      
      //组装返回前端的数组
      foreach($selObjArr as $key=>$val){
        switch($val){
          case 'issEntType':
            $colArr=$relSet->column('type');
            //$entTypeChi=$relSet->column('type');
//            $entTypeChi=array_unique($entTypeChi);
//            natcasesort($entTypeChi);
//            foreach($entTypeChi as $key=>$val){
//              foreach($issRelTypeArr as $k=>$v){
//                if($val==$v){
//                  $entTypeEn[$key]=$k;
//                  break;
//                }
//              }
//            }
            
            //$arr=$issRelTypeArr;
            $arr=$colArr;
            //$arr=find_child_array($issRelTypeArr,$clueArr=['keys'=>[],'values'=>$colArr]);
            break;
          case 'issEntStatus':
            $colArr=$relSet->column('status');
            //$arr=$issRelStatusArr;
            $arr=$colArr;
            //$arr=find_child_array($issRelStatusArr,$clueArr=['keys'=>[],'values'=>$colArr]);
            break;
          default:
            $colArr=$baseSet->column($val);
            //$arr=['key1'=>'value1'];
            break;                                  
        }
        $colArr=array_unique($colArr);
        natcasesort($colArr);
        $selResData[$val] = array_values($colArr);
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
    
    public function fmAssSingle(Request $request,AssinfoModel $assMdl,$id=0,$oprt='')
    {
      $this->priLogin();
      $id=$request->param('id');
      $oprt=$request->param('oprt');
      $statusEn='*';
      $authAss=$this->auth['ass'];
      $assSetArr=array('id'=>$id,
                        'assnum'=>'',
                        'code'=>'',
                        'bar_code'=>'',
                        'brand_model'=>'',
                        'quantity'=>1,
                        'place_now'=>'',
                        'dept_now'=>'',
                        'keeper_now'=>'',
                        'dept_now'=>'',
                        'status_now'=>'*',
                        'status_now_desc'=>'新固定资产填报',
                        'status_now_user_name'=>$this->userName,
                        );
      //$assSet=($id*1)?$assMdl::get($id):$assSetArr;
      if($oprt=='_RESTORE' || $oprt=='_DELETE'){
        $assSet=$id?$assMdl::withTrashed()->where('id',$id)->find():$assSetArr;
      }else{
        $assSet=$id?$assMdl::get($id):$assSetArr;
      }
      
      $this->assign([
          'home'=>$request->domain(),
          'oprt'=>$oprt,
          'assSet'=>$assSet,
          'userName'=>$this->userName,
          'authAss'=>$authAss
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
