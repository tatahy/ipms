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
    //请求的issType
    private $issType = '';
  
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
        $this->issType=$this->request->param('issType');
        $this->auth=UserModel::where(['userName'=>$this->userName,'pwd'=>$this->pwd])->find()->authority['iss'];
        //使用模型前的初始化，为模型内部使用的变量赋初值，后续的各个方法中无需再初始化，但可以进行修改
        IssinfoModel::initModel($this->userName,$this->dept,$this->auth,$this->issType); 
       
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
        
    public function index(Request $request,AssinfoModel $assMdl,$sortData=[],$searchData=[])
    {
      $this->priLogin();
      $issType=!empty($this->request->param('issType'))?$this->request->param('issType'):'';
      
      
      $this->assign([
        'home'=>$this->home,
          
      ]); 
      return view();
      //return json_encode($this->auth,JSON_UNESCAPED_UNICODE); 
     // switch($issType){
//        case '_PAT':
//         // $showObj='issPat';
//          $showObj=$this->issPat();
//          break;
//        case '_THE':
//          $showObj=$this->issThe();
//          //$showObj='issThe';
//          break;
//        case '_PRO':
//          $showObj=$this->issPro();
//          //$showObj='issPro';
//          break;
//      }
//      return $showObj;        
    }
    
    public function issList(IssinfoModel $issMdl,$issType='',$status='')
    {
      $this->priLogin();
      $issType=!empty($this->request->param('issType'))?$this->request->param('issType'):'';
      $status=!empty($this->request->param('status'))?$this->request->param('status'):'';
      $listPath='';
      switch($issType){
        case'_THE':
          $listPath='issue/issThe/issTheList';
          
          break;
        case'_PAT':
          $listPath='issue/issPat/issPatList';
          break;
        case'_PRO':
          $listPath='issue/issPro/issProList';
          break;  
        
      }
      
      $issSet=$issMdl->issStatusQuery($issType,$status);
      $this->assign([
        'home'=>$this->home,
        'issSet'=>$issSet  
      ]);
      
      //return $listPath;
      return view($listPath);
      //return $this->fetch($listPath);
    }
    
    public function searchForm($issType='',$status='')
    {
      $this->priLogin();
      $issType=!empty($this->request->param('issType'))?$this->request->param('issType'):'';
      $status=!empty($this->request->param('status'))?$this->request->param('status'):'';
      $this->assign([
        'home'=>$this->home,
          
      ]);
      
      return [$issType,$status,IssinfoModel::getAccessUser()];
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
