<?php
namespace app\dashboard\controller;

use think\Request;
use think\Session;
use think\View;

use app\dashboard\model\Assinfo as AssinfoModel;

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
    
    // 初始化
    protected function _initialize()
    {
        $this->username=Session::get('username');
        $this->pwd=Session::get('pwd');
        $this->log=Session::get('log');
        $this->roles=Session::get('role');
        $this->dept=Session::get('dept');
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
        $quanCount=$assMdl->where('quantity','>=',1)->count();
        
        
        $this->assign([
          'home'=>$request->domain(),
          'quanCount'=>$quanCount,
          
        ]);
        return view();
        
    }
    
    //根据前端的sortData/searchData，选择返回前端的asset list
    public function assList(Request $request,AssinfoModel $assMdl,$sortData=[],$searchData=[])
    {
        $this->priLogin();
        
        //搜索查询条件数组
        $whereArr=[];
        $sortDefaults=array('listRows'=>10,'sortName'=>'assnum','sortOrder'=>'asc','pageNum'=>1);
        // 接收前端的排序参数数组
        $sortData=!empty($request->param('sortData/a'))?$request->param('sortData/a'):$sortDefaults;
        $sortData=array_merge($sortDefaults,$sortData);
        
        $searchDefaults=array();
        // 接收前端的搜索参数数组，由前端保证传来的搜索参数值非0，非空。
        $searchData=!empty($request->param('searchData/a'))?$request->param('searchData/a'):$searchDefaults;
        $searchData=array_merge($searchDefaults,$searchData);
        
        $whereArr['id']=['>',0];
        
        $whereArr['brand_model']=!empty($searchData['brand_model'])?['like','%'.$searchData['brand_model'].'%']:'';
        $whereArr['assnum']=!empty($searchData['assnum'])?['like','%'.$searchData['assnum'].'%']:'';
        $whereArr['code']=!empty($searchData['code'])?['like','%'.$searchData['code'].'%']:'';
        $whereArr['bar_code']=!empty($searchData['bar_code'])?['like','%'.$searchData['bar_code'].'%']:'';
        
        $whereArr['dept_now']=!empty($searchData['dept_now'])?$searchData['dept_now']:'';
        $whereArr['place_now']=!empty($searchData['place_now'])?$searchData['place_now']:'';
        $whereArr['keeper_now']=!empty($searchData['keeper_now'])?$searchData['keeper_now']:'';
        
        //将空白元素删除
        foreach($whereArr as $key=>$val){
            if(empty($val)){
                unset($whereArr[$key]);
            }
        }
        
        //分页,每页$listRows条记录
        $assSet=$assMdl::where($whereArr)
                        //->order('place_now', 'asc')
                        ->order($sortData['sortName'], $sortData['sortOrder'])
                        ->paginate($sortData['listRows'],false,['type'=>'bootstrap','var_page' =>'pageNum','page'=>$sortData['pageNum'],
                        'query'=>['listRows'=>$sortData['listRows']]]);
        $searchResultNum=count($assMdl::where($whereArr)->select());
        // 获取分页显示
        $assList=$assSet->render(); 
               
        $this->assign([
           'assSet'=>$assSet,
          'assList'=>$assList,
          
          //排序数组
          'sortData'=>$sortData,
          
          //搜索数组
          'searchData'=>$searchData,
          
          'searchResultNum'=>$searchResultNum,
          'whereArr'=>json_encode($whereArr,JSON_UNESCAPED_UNICODE)
		  
        ]);
        return view();
        
    }
    
    //响应前端请求，返回信息
    public function selectRes(Request $request,AssinfoModel $assMdl,$req='')
    {
      $this->priLogin();
      
      $req = empty($request->param('req'))?0:$request->param('req');
      
      $res=$assMdl->field($req)->group($req)->select();
         
      //将得到的数据集降为一维数组
      if(is_array($res)){
        $res=collection($res)->column($req);        
      }else{
        $res=$res->column($req);
      }
      
      //返回前端的是索引数组  
      return $res;
    }
    
    public function editAss(Request $request,AssinfoModel $assMdl)
    {
      $this->priLogin();
      $assSet=array('id'=>0);
      $this->assign([
          'home'=>$request->domain(),
          'userName'=>$this->username,
          'assSet'=>$assSet
        ]);
        return view();
    }
    
    
    public function assOprt(Request $request,AssinfoModel $assMdl)
    {
      $this->priLogin();
      
      $data=$request->param();
      unset($data['id']);
      unset($data['oprt']);
      //写入数据库
      $res=$assMdl::create($data,true)->id;
      
      //$res='aa'; 
      //return json_encode($res,JSON_UNESCAPED_UNICODE);
      return $res;
    }
}
