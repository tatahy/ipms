<?php
namespace app\dashboard\controller;

use think\Request;
use think\Session;
use think\View;

use app\dashboard\model\Assinfo as AssinfoModel;
use app\dashboard\model\Assrecord as AssrecordModel;
use app\dashboard\model\User as UserModel;

class AssetController extends \think\Controller
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
    //登录用户的权限。
    private $auth=[];
    //asset分类汇总记录数据
    private $assNum=[];
    
    // 初始化
    protected function _initialize()
    {
        $this->username=Session::get('username');
        $this->pwd=Session::get('pwd');
        $this->log=Session::get('log');
        $this->roles=Session::get('role');
        $this->dept=Session::get('dept');
        
        $this->auth=UserModel::where(['username'=>$this->username,'pwd'=>$this->pwd])->find()->authority;
        $this->assNum=$this->priGetAssNum();
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
    //获得各类asset数量
    private function priGetAssNum()
    {
      $this->assNum=['_TOTAL'=>$this->priAssNum('_ASSS_USUAL')+$this->priAssNum('_ASSS6'),
                        '_ASSS_USUAL'=>$this->priAssNum('_ASSS_USUAL'),
                        '_ASSS1'=>$this->priAssNum('_ASSS1'),
                        '_ASSS2'=>$this->priAssNum('_ASSS2'),
                        '_ASSS3'=>$this->priAssNum('_ASSS3'),
                        '_ASSS4'=>$this->priAssNum('_ASSS4'),
                        '_ASSS5'=>$this->priAssNum('_ASSS5'),
                        '_ASSS6'=>$this->priAssNum('_ASSS6')
                        ];
      return $this->assNum;
    }
    //计算各类asset数量
    private function priAssNum($assType='')
    {
      $assType=!empty($assType)?$assType:'_ASSS_USUAL';
      return $this->priAssQueryObj($assType)->count();
    }
    //各类asset查询语句对象
    private function priAssQueryObj($assType)
    {
      switch($assType){
        case '_ASSS_USUAL':
          $query=AssinfoModel::where('id','>',0);
          break;
        case '_ASSS6':
          $query=AssinfoModel::onlyTrashed();
          break;
        default :
          $query=AssinfoModel::scope('assType',$assType);
          break;
      }
      return $query;
    }
    
    public function index(Request $request,AssinfoModel $assMdl,$sortData=[],$searchData=[])
    {
        $this->priLogin();
        
        $assType=!empty($request->param('assType'))?$request->param('assType'):'_ASSS_USUAL';
        $sortData=array('listRows'=>10,'sortName'=>'assnum','sortOrder'=>'asc','pageNum'=>1,'showAssId'=>0,'assType'=>$assType);
        
        $quantityNormal=$assMdl::sum('quantity');
        $quantityTrash=$assMdl::onlyTrashed()->sum('quantity');
               
        $this->assign([
          'home'=>$request->domain(),
          
          'numTotal'=>$this->assNum['_TOTAL'],
          'numObj'=>json_encode($this->assNum,JSON_UNESCAPED_UNICODE),
     
          'quanTotal'=>$quantityNormal+$quantityTrash,
          
          'assType'=>$assType,
          'sortData'=>$sortData,
          
          //将应用公共文件（common.php）中定义的数组常量conAssStatusLabelArr转为json对象
          'conAssStatusLabelArr'=>json_encode(conAssStatusLabelArr,JSON_UNESCAPED_UNICODE),
          
        ]);
        return view();
        
    }
    
    //根据前端的sortData/searchData，选择返回前端的asset list
    public function assList(Request $request,AssinfoModel $assMdl,$sortData=[],$searchData=[])
    {
        $this->priLogin();
        $authAss=$this->auth['ass'];
        
        $quantityNormal=0;
        $quantityTrash=0;
        
        //搜索查询条件数组
        $whereArr=[];
        $sortDefaults=array('listRows'=>10,'sortName'=>'assnum','sortOrder'=>'asc','pageNum'=>1,'showAssId'=>0,'assType'=>'_ASSS_USUAL');
        // 接收前端的排序参数数组
        $sortData=!empty($request->param('sortData/a'))?$request->param('sortData/a'):$sortDefaults;
        $sortData=array_merge($sortDefaults,$sortData);
        $assType=$sortData['assType'];
        
        $searchDefaults=array();
        // 接收前端的搜索参数数组，由前端保证传来的搜索参数值非0，非空。
        $searchData=!empty($request->param('searchData/a'))?$request->param('searchData/a'):$searchDefaults;
        $searchData=array_merge($searchDefaults,$searchData);
        $reqObj=!empty($searchData['reqObj'])?$searchData['reqObj']:'';
        //基础搜索条件
        //$whereArr['id']=['>',0];
        
        //前端输入的关键字搜索
        $whereArr['brand_model']=!empty($searchData['brand_model'])?['like','%'.$searchData['brand_model'].'%']:'';
        $whereArr['assnum']=!empty($searchData['assnum'])?['like','%'.$searchData['assnum'].'%']:'';
        $whereArr['code']=!empty($searchData['code'])?['like','%'.$searchData['code'].'%']:'';
        $whereArr['bar_code']=!empty($searchData['bar_code'])?['like','%'.$searchData['bar_code'].'%']:'';
        //前端select值搜索
        $whereArr['dept_now']=!empty($searchData['dept_now'])?$searchData['dept_now']:'';
        $whereArr['place_now']=!empty($searchData['place_now'])?$searchData['place_now']:'';
        $whereArr['status_now']=!empty($searchData['status_now'])?$searchData['status_now']:'';
        
        $whereArr['keeper_now']=!empty($searchData['keeper_now'])?['like','%'.$searchData['keeper_now'].'%']:'';
        $whereArr['status_now_user_name']=!empty($searchData['status_now_user_name'])?['like','%'.$searchData['status_now_user_name'].'%']:'';
        //将$whereArr['status_now']的值（中文）转为类型编码
        foreach(conAssStatusArr as $key=>$val){
          if($whereArr['status_now']==$val){
            $whereArr['status_now']=$key;
          }
        }
        //将空白元素删除
        foreach($whereArr as $key=>$val){
            if(empty($val)){
                unset($whereArr[$key]);
            }
        }
        
        //分页,每页$listRows条记录
        $assSet=$this->priAssQueryObj($assType)->where($whereArr)
                      ->order($sortData['sortName'], $sortData['sortOrder'])
                      ->paginate($sortData['listRows'],false,['type'=>'bootstrap','var_page' =>'pageNum','page'=>$sortData['pageNum'],
                        'query'=>['listRows'=>$sortData['listRows']]]);
        // 获取分页显示
        $assList=$assSet->render(); 
        
        //记录总数
        $searchResultNum=count($this->priAssQueryObj($assType)->where($whereArr)->select());        
        //数量总计
        $quanCount=$this->priAssQueryObj($assType)->where($whereArr)->sum('quantity');
                       
        $this->assign([
          'home'=>$request->domain(),
          'assSet'=>$assSet,
          'assList'=>$assList,
          
          'quantityNormal'=>$quantityNormal,
          'quantityTrash'=>$quantityTrash,
          'quanCount'=>$quanCount,
          
          //asset类型
          'assType'=>$assType,
          
          //排序数组
          'sortData'=>$sortData,
          
          //搜索数组
          'searchData'=>$searchData,
          
          'searchResultNum'=>$searchResultNum,
          'reqObj'=>$reqObj,
          
          'authAss'=>$this->auth['ass'],
          'authAssObj'=>json_encode($this->auth['ass'],JSON_UNESCAPED_UNICODE),
          //'conAssStatusArr'=>json_encode(array_values(conAssStatusArr),JSON_UNESCAPED_UNICODE),
          'conAssStatusArr'=>json_encode(conAssStatusArr,JSON_UNESCAPED_UNICODE),
          'conAssStatusOprtArr'=>json_encode(conAssStatusOprtArr,JSON_UNESCAPED_UNICODE),
          'conAssAuthOprtArr'=>json_encode(conAssAuthOprtArr,JSON_UNESCAPED_UNICODE),
          'conAssStatusLabelArr'=>json_encode(conAssStatusLabelArr,JSON_UNESCAPED_UNICODE),   
          
          'assSetArr'=>json_encode($assSet,JSON_UNESCAPED_UNICODE)
		  
        ]);
        return view();
        
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
                        'status_now_user_name'=>$this->username,
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
          'userName'=>$this->username,
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
     public function assRecords(Request $request,AssinfoModel $assMdl,AssrecordModel $assRdMdl)
    {
      $this->priLogin();
      $id=$request->param('id');
      //不是usual就是trashed
      $assSet=count($assMdl::get($id))?$assMdl::get($id):$assMdl::onlyTrashed()->where('id',$id)->find();
      $assRdSet=$assRdMdl::where('assinfo_id',$id)->order('create_time','desc')->select();
      $this->assign([
          'assSet'=>$assSet,
          'assRdSet'=>$assRdSet,
          'totalNum'=>count($assRdSet),
          'conAssStatusArr'=>json_encode(conAssStatusArr,JSON_UNESCAPED_UNICODE), 
          'conAssStatusLabelArr'=>json_encode(conAssStatusLabelArr,JSON_UNESCAPED_UNICODE),     
        ]);
      return view();
    }
    
    public function assOprt(Request $request,AssinfoModel $assMdl,AssrecordModel $assRdMdl,$data=[])
    {
      $this->priLogin();
      
      $data=$request->param();

      $id=$data['id'];
      $oprt=$data['oprt'];
      $statusNow=!empty($data['status_now'])?$data['status_now']:'';
      $statusNowUserName=!empty($data['status_now_user_name'])?$data['status_now_user_name']:'';
      $res=0;
      
      $rdDataArr=['status_now'=>$statusNow,
                  'status_now_user_name'=>$statusNowUserName,
                  'oprt'=>$data['oprt'],
                  'oprt_detail'=>$data['status_now_desc'],
                  'oprt_detail_json'=>'{}',
                  'assinfo_id'=>$data['id'],
                  'keeper_now'=>$data['keeper_now'],
                  'dept_now'=>$data['dept_now'],
                  'place_now'=>$data['place_now'],
                  ];
      
      //将空白元素删除
      foreach($rdDataArr as $key=>$val){
        if(empty($val)){
          unset($rdDataArr[$key]);
        }
      }
      
      switch($oprt){
        case '_CREATE':
            //数据库create
            unset($data['id']);
            $res=$assMdl::create($data,true)->id;
            //新状态写入assRecord表
            $rdDataArr['assinfo_id']=$res;
            $assRdMdl::create($rdDataArr,true);
            break;
        case '_UPDATE':
            //数据库update
            //模型的save方法，返回的是受影响的记录数。
            unset($data['status_now']);
            $assSet = $assMdl::get($id);
            $res=$assSet->allowField(true)->save($data);
            break;
       
        case '_TRASH':
            //模型的destroy方法，返回的是受影响的记录数。已启用框架的软删除，数据仍然在数控库中。
            $assMdl::update($data, ['id' =>$id], true);
            $res = $assMdl::destroy($id);
            //新状态写入assRecord表
            $assRdMdl::create($rdDataArr,true);
            break;
        
        case'_DELETE':
            //模型的destroy方法，返回的是受影响的记录数。已启用框架的软删除，但是执行物理删除。
            $res = $assMdl::destroy($id,true);
            //assRecord表删除相应的记录
            $assRdMdl::destroy(function($query) use($id){
                  $query->where('assinfo_id',$id);
                });
            break;
            
        case'_RESTORE':
            //模型的软删除restore()方法，返回的是受影响的记录数。
            $assMdl::update($data, ['id' =>$id], true);
            $res = $assMdl->restore(['id'=>$id]);
            //新状态写入assRecord表
            $assRdMdl::create($rdDataArr,true);
            break;
        //'_SUBMIT','_AUDIT','_APPROVE','_MAINTAIN'
        default:
          //数据库update
          //模型的save方法，返回的是受影响的记录数。
          $assSet = $assMdl::get($id);
          $res=$assSet->allowField(true)->save($data);
          //新状态写入assRecord表
          $assRdMdl::create($rdDataArr,true);    
          break;
      }
      //返回操作结果'res'、各类asset的记录数'num'、asset数量总计'quanTotal'
      return ['res'=>$res,'num'=>$this->priGetAssNum(),'quanTotal'=>$assMdl::sum('quantity')+$assMdl::onlyTrashed()->sum('quantity')];
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
