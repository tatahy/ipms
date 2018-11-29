<?php
namespace app\dashboard\controller;

use think\Request;
use think\Session;
use think\View;
use think\Controller;

use app\dashboard\model\Issinfo as IssinfoModel;
use app\dashboard\model\Issrecord as IssrecordModel;
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
    
    public function issList(IssinfoModel $issMdl)
    {
      $this->priLogin();
      $request=$this->request;
      
      //关联对象名称及关联方法对应关系数组
      $entNameMethodArr=['_PAT'=>'patinfo','_THE'=>'theinfo','_PRO'=>'proinfo'];
      //关联对象共有的字段名称与前端的对应关系
      $entFieldAsArr=['issEntName'=>'topic','issEntType'=>'type','issEntStatus'=>'status'];
      //关联对象共有的字段名称
      $entField=['id','topic','type','status'];
      //关联对象名称
      $entName='';
      //关联方法名称
      $entMethod='';
      //进行排序的关联对象字段名
      $entSortName='';
      
      //搜索查询条件数组
      $whereArr=[];
      $searchDefaults=array();
      $sortDefaults=array('listRows'=>10,'sortName'=>'issnum','sortOrder'=>'asc','pageNum'=>1,
                          'showIssId'=>0,'issEntName'=>'_PAT','issStatus'=>'_INPROCESS');
      
      // 接收前端的排序参数数组
      $sortData=!empty($request->param('sortData/a'))?$request->param('sortData/a'):$sortDefaults;
      $sortData=array_merge($sortDefaults,$sortData);
      $entName=$sortData['issEntName'];
      //'_INPROCESS'字符串出现在$sortData['issStatus']中的次数
      $issStatus=substr_count($sortData['issStatus'],'_INPROCESS')?'_INPROCESS':$sortData['issStatus'];
      //前端显示的内容
      $issList=[];
      // 接收前端的搜索参数数组，由前端保证传来的搜索参数值非0，非空。
      $searchData=!empty($request->param('searchData/a'))?$request->param('searchData/a'):$searchDefaults;
      $searchData=array_merge($searchDefaults,$searchData);
      
      //前端输入的关键字搜索,like关键字
      
      //前端select值搜索，=select值
       
      //将空白元素删除
      foreach($whereArr as $key=>$val){
        if(empty($val)){
          unset($whereArr[$key]);
        }
      }
      //根据关联对象名得到关联方法
      foreach($entNameMethodArr as $key => $val){
        if($entName==$key){
          $entMethod=$val;
        }
      }
      
      foreach($entFieldAsArr as $key=>$val){
        if($sortData['sortName']==$key){
          $entSortName=$val;
          $sortData['sortName']='';
        }
      }
      
       //符合查询条件的记录分页,每页$listRows条记录
      $issSet=$issMdl->issStatusQuery($issStatus,$entName)
                      ->where($whereArr)
                      ->order($sortData['sortName'],$sortData['sortOrder'])
                      ->paginate($sortData['listRows'],false,['type'=>'bootstrap','var_page' =>'pageNum','page'=>$sortData['pageNum'],
                        'query'=>['listRows'=>$sortData['listRows']]]);
       
      
      //涉及关联对象$entName中字段的排序   
      if($entSortName){
        //符合查询条件的所有iss记录，待按$entName字段值排序后显示
        $issTemp=$issMdl->issStatusQuery($issStatus,$entName)->where($whereArr)->select();
        
        //保证$issTemp为数据集对象才能使用延迟载入关联查询方法load()
        if(is_array($issTemp)){
          $issTemp=collection($issTemp);
        }                
        //延迟载入关联查询方法load()得到关联对象的数据集，仅关联对象共有的字段名称可见
        $entList=$issTemp->load($entMethod)->visible(['id',$entMethod=>$entField]);
        
        //能否在load()方法中进行order和limit？？
        //$entList=$issTemp->load([$entMethod=> function ($query) use ($entField,$entSortName,$sortData) {
//                                  $query->field($entField)
//                                        ->order($entSortName,$sortData['sortOrder'])
//                                        ->limit(($sortData['pageNum']-1)*$sortData['listRows'],$sortData['listRows']);
//                              }])
//                          ->visible(['id',$entMethod]);
        
        //将$entList转为仅有id与$entSortName字段的数组后，按$entSortName字段值排序
        $arrId=$entList->column('id');//每个元素为id字段值(数字)的索引数组
        $arrSub=collection($entList->column($entMethod))->column($entSortName);//每个元素为$entSortName字段值(字符串)的索引数组
        $arr=array_combine($arrId,$arrSub);
        
        if($sortData['sortOrder']=='asc'){
          //按值升序，保持键值关系
          asort($arr);
        }else{
          //按值降序，保持键值关系
          arsort($arr);
        }
        
        $index=0;   
        foreach($arr as $key=>$val){
          $arr1[$index]=['id'=>$key,'value'=>$val];
          $index++;
        }
        
        //$entList=$arr;
        //$entList=$arr1;
        
        $testSet =$issMdl->issStatusQuery($issStatus,$entName)->where($whereArr)->select();
        
        $issmapId=collection($testSet)->column('issmap_id');
        $issId=collection($testSet)->column('id');                    
        
        $testSet = $issMdl->patinfoOrder($entSortName,$sortData['sortOrder'])
                            ->limit(($sortData['pageNum']-1)*$sortData['listRows'],$sortData['listRows'])
                            ->select($issmapId);
 
        $testList=$testSet;
        
        //重组本页的issList内容
        $mStart=($sortData['pageNum']-1)*$sortData['listRows'];
        $mEnd=$sortData['pageNum']*$sortData['listRows'];
        for($n=$mStart;$n<$mEnd;$n++){
          if($n<count($arr1)){
            $id=$arr1[$n]['id'];
            for($i=0;$i<count($issTemp);$i++){
              if($issTemp[$i]->id==$id){
                $issList[$n]=$issTemp[$i];
                break;
              }
            }
          }
        }
      }else{
        $issTemp=$issMdl->issStatusQuery($issStatus,$entName)->where($whereArr)->order($sortData['sortName'],$sortData['sortOrder'])
                ->limit(($sortData['pageNum']-1)*$sortData['listRows'],$sortData['listRows'])->select();
        $issList=$issTemp;
        $entList=['haha'];
        $testList=['haha'];
      }
      
      
      //搜索记录总数
      $searchResultNum=$issMdl->issStatusQuery($issStatus,$entName)->where($whereArr)->count();
      
      $this->assign([
        'home'=>$this->home,
        'issEntName'=>$entName,
        'issSet'=>$issSet,
        'issList'=>$issList,
        'issTest'=>json_encode($testList,JSON_UNESCAPED_UNICODE),
        
        'entMethod'=>$entMethod,
        
        //排序数组
        'sortData'=>$sortData,
        //记录总数
        'searchResultNum'=>$searchResultNum 
      ]);
      
      return view();
    }
    
    public function searchForm(IssinfoModel $issMdl,$issEntName='',$issStatus='')
    {
      $this->priLogin();
      //$issEntName=!empty($this->request->param('issEntName'))?$this->request->param('issEntName'):'';
//      $issStatus=!empty($this->request->param('issStatus'))?$this->request->param('issStatus'):'';

      // 接收前端的排序参数数组
      $sortData=$this->request->param('sortData/a');
      
      $issEntName=$sortData['issEntName'];
      //'_INPROCESS'字符串出现在$sortData['issStatus']中的次数
      $issStatus=substr_count($sortData['issStatus'],'_INPROCESS')?'_INPROCESS':$sortData['issStatus'];

      //关联查询的对象
      switch($issEntName){
        case '_PAT':
          $entName='patinfo';
          break;
        case '_THE':
          $entName='theinfo';
          break;
        case '_PRO':
          $entName='proinfo';
          break;
        default:
          
          break;
        
      }
      
      $issSet=$issMdl->issStatusQuery($issStatus,$issEntName)->order($sortData['sortName'],$sortData['sortOrder'])->select(); 
      $issSet=collection($issSet)->visible(['topic','issnum']);
      //$issSet=$issMdl->issStatusQuery($issStatus,$issEntName)->limit(5)->select();
      $this->assign([
        'home'=>$this->home,
        'issSet'=>$issSet
      ]);
      
      //return [IssinfoModel::getAccessUser(),$issSet];
      return json_encode($issSet,JSON_UNESCAPED_UNICODE);
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
