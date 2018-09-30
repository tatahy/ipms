<?php
namespace app\dashboard\controller;

use think\Request;
use think\Session;
use think\View;

use app\dashboard\model\Assinfo as AssinfoModel;
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
    //
    private $auth=[];
    
    // 初始化
    protected function _initialize()
    {
        $this->username=Session::get('username');
        $this->pwd=Session::get('pwd');
        $this->log=Session::get('log');
        $this->roles=Session::get('role');
        $this->dept=Session::get('dept');
        
        $this->auth=UserModel::where(['username'=>$this->username,'pwd'=>$this->pwd])->find()->authority;
    }
    
    //
    private function priLogin(){
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$this->log){
            $this->error('未登录用户，请先登录系统');
            //$this->redirect($request->domain());
        }
    }
    
    public function index(Request $request,AssinfoModel $assMdl,$sortData=[],$searchData=[])
    {
        $this->priLogin();
        
        //数量总计
        $quanCount=$assMdl->where('quantity','>=',1)->sum('quantity');
        
        
        $this->assign([
          'home'=>$request->domain(),
          'quanCount'=>$quanCount,
          //引用本模块公共文件（dashboard/common.php）中定义的数组常量conAssStatusArr
          //'conAssStatusArr'=>json_encode(conAssStatusArr,JSON_UNESCAPED_UNICODE)
          //'conAssStatusArr'=>conAssStatusArr['_ASSS4']
          //'auth'=>json_encode($this->auth,JSON_UNESCAPED_UNICODE)
        ]);
        return view();
        
    }
    
    //根据前端的sortData/searchData，选择返回前端的asset list
    public function assList(Request $request,AssinfoModel $assMdl,$sortData=[],$searchData=[])
    {
        $this->priLogin();
        $authAss=$this->auth['ass'];
        
        //搜索查询条件数组
        $whereArr=[];
        $sortDefaults=array('listRows'=>10,'sortName'=>'assnum','sortOrder'=>'asc','pageNum'=>1,'showAssId'=>0);
        // 接收前端的排序参数数组
        $sortData=!empty($request->param('sortData/a'))?$request->param('sortData/a'):$sortDefaults;
        $sortData=array_merge($sortDefaults,$sortData);
        
        $searchDefaults=array();
        // 接收前端的搜索参数数组，由前端保证传来的搜索参数值非0，非空。
        $searchData=!empty($request->param('searchData/a'))?$request->param('searchData/a'):$searchDefaults;
        $searchData=array_merge($searchDefaults,$searchData);
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
        $whereArr['keeper_now']=!empty($searchData['keeper_now'])?$searchData['keeper_now']:'';
        
        //将空白元素删除
        foreach($whereArr as $key=>$val){
            if(empty($val)){
                unset($whereArr[$key]);
            }
        }
        
        if($authAss['audit'] || $authAss['approve']){
            //分页,每页$listRows条记录
            //仅查询软删除的记录
            //$assSet=$assMdl::onlyTrashed()->where($whereArr)
            //查询包含软删除的记录
            $assSet=$assMdl::withTrashed()->where($whereArr)
                            ->order($sortData['sortName'], $sortData['sortOrder'])
                            ->paginate($sortData['listRows'],false,['type'=>'bootstrap','var_page' =>'pageNum','page'=>$sortData['pageNum'],
                            'query'=>['listRows'=>$sortData['listRows']]]);
            $searchResultNum=count($assMdl::withTrashed()->where($whereArr)->select());
        }else{
            //分页,每页$listRows条记录
            //仅查询软删除的记录
            //$assSet=$assMdl::onlyTrashed()->where($whereArr)
            //查询包含软删除的记录
            //$assSet=$assMdl::withTrashed()->where($whereArr)
            //查询排除软删除的记录
            $assSet=$assMdl->where($whereArr)//->order('place_now', 'asc')
                            ->order($sortData['sortName'], $sortData['sortOrder'])
                            ->paginate($sortData['listRows'],false,['type'=>'bootstrap','var_page' =>'pageNum','page'=>$sortData['pageNum'],
                            'query'=>['listRows'=>$sortData['listRows']]]);
            $searchResultNum=count($assMdl::where($whereArr)->select());
        }
       
        $assList=$assSet->render(); 
               
        $this->assign([
          'home'=>$request->domain(),
          'assSet'=>$assSet,
          'assList'=>$assList,
          
          //排序数组
          'sortData'=>$sortData,
          
          //搜索数组
          'searchData'=>$searchData,
          
          'searchResultNum'=>$searchResultNum,
          
          'authAss'=>$this->auth['ass'],
          'authAssObj'=>json_encode($this->auth['ass'],JSON_UNESCAPED_UNICODE),
          //'conAssStatusArr'=>json_encode(array_values(conAssStatusArr),JSON_UNESCAPED_UNICODE),
          'conAssStatusArr'=>json_encode(conAssStatusArr,JSON_UNESCAPED_UNICODE),
          'assSetArr'=>json_encode($assSet,JSON_UNESCAPED_UNICODE)
		  
        ]);
        return view();
        
    }
    
    //响应前端请求，返回信息
    public function selectRes(Request $request,AssinfoModel $assMdl,$req='',$source='db')
    {
      $this->priLogin();
      
      $req = !empty($request->param('req'))?$request->param('req'):0;
      $source = !empty($request->param('source'))?$request->param('source'):0;
      
      if($source=='common' && $req=='status_now'){
        //引用本模块公共文件（dashboard/common.php）中定义的数组常量conAssStatusArr
        $res=conAssStatusArr;
      }else{
        //从数据库获得数据
        $res=$assMdl->field($req)->group($req)->select();
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
    
    public function modalAssSingle(Request $request,AssinfoModel $assMdl,$id=0,$oprt='')
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
                        'status_now'=>'新增(待验收)',
                        'status_now_desc'=>'新固定资产填报',
                        'status_now_user_name'=>$this->username,
                        );
      
      if($oprt=='_RESTORE' || $oprt=='_DELETE_TRUE'){
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
    
    public function assOprt(Request $request,AssinfoModel $assMdl,$data=[])
    {
      $this->priLogin();
      
      $data=$request->param();
      $id=$request->param('id');
      $oprt=$request->param('oprt');
      $res=0;
      
      //$res=
      
      switch($oprt){
        case '_CREATE':
            //数据库create
            $res=$assMdl::create($data,true)->id;
            break;
        case '_UPDATE':
            //数据库update
            //模型的save方法，返回的是受影响的记录数。
            $assSet = $assMdl::get($id);
            $res=$assSet->allowField(true)->save($data);
            break;
        case '_AUDIT':
            //数据库update
            //模型的save方法，返回的是受影响的记录数。
            $assSet = $assMdl::get($id);
            $res=$assSet->allowField(true)->save($data);
            break;
        case '_DELETE':
            //模型的destroy方法，返回的是受影响的记录数。已启用框架的软删除，数据仍然在数控库中。
            $res = $assMdl::destroy($id);
            break;
        
        case'_DELETE_TRUE':
            //模型的destroy方法，返回的是受影响的记录数。已启用框架的软删除，但是执行物理删除。
            $res = $assMdl::destroy($id,true);
            break;
            
        case'_RESTORE':
            //模型的软删除restore()方法，返回的是受影响的记录数。
            $res = $assMdl->restore(['id'=>$id]);
            break;
      }
      
      
      //$res='aa'; 
      //return json_encode($res,JSON_UNESCAPED_UNICODE);
      return $res;
      
      
      //part2：组装状态机要处理的数据，配置状态机参数
      //组装状态机（IssPatFSM）要处理的数据
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
