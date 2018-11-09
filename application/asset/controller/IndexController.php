<?php
namespace app\asset\controller;

use think\Request;
use think\Session;
use think\View;

use app\asset\model\Assinfo as AssinfoModel;
use app\asset\model\Assrecord as AssrecordModel;

class IndexController extends \think\Controller
{
     //用户名
    private $userName = null;
    //用户密码
    private $pwd = null;
    //用户登录状态
    private $log = null;
    //用户角色
    private $roles=array();
    //用户所在部门
    private $dept = null;
    //用户的asset权限
    private $auth = [];
    
    // 初始化
    protected function _initialize()
    {
        $this->userName=Session::get('username');
        $this->pwd=Session::get('pwd');
        $this->log=Session::get('log');
        $this->roles=Session::get('role');
        $this->dept=Session::get('dept');
        $this->auth=Session::get('authArr')['ass'];
        //使用模型前的初始化，为模型内部使用的变量赋初值，后续的各个方法中无需再初始化，但可以进行修改
        AssinfoModel::initModel($this->userName,$this->dept,$this->auth); 
    }
    
    //
    private function priLogin(){
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$this->log){
            $this->error('未登录用户，请先登录系统');
            //$this->redirect($request->domain());
        }
    }
    //控制器注入2个实例化后的对象$request，$assMdl   
    public function index(Request $request,AssinfoModel $assMdl)
    {
        $this->priLogin();
    
        $sortData=array('listRows'=>10,'sortName'=>'assnum','sortOrder'=>'asc','pageNum'=>1);
        
        $searchData=array('brand_model'=>'','assnum'=>'','code'=>'','bar_code'=>'','dept_now'=>'','place_now'=>'','keeper_now'=>'');
        
        $this->assign([
          'read'=>$this->auth['read'],
                   
          'assNum'=>$assMdl->getAssTypeNumArr(),
          'assType'=>'_USUAL',
                    
          'home'=>$request->domain(),
          'username'=>$this->userName,
          'year'=>date('Y'),
          
          'sortData'=>$sortData,
          'searchData'=>json_encode($searchData,JSON_UNESCAPED_UNICODE),
          'auth'=>json_encode($this->auth,JSON_UNESCAPED_UNICODE),
          // 获取当前数据表字段信息
          //'fields'=>json_encode($assMdl->getTableFields(),JSON_UNESCAPED_UNICODE),
          // 获取当前数据表字段类型
          //'fieldsType'=>json_encode($assMdl->getFieldsType(),JSON_UNESCAPED_UNICODE),
          'mdlSt'=>json_encode($assMdl->getAccessUser(),JSON_UNESCAPED_UNICODE)
        ]);
        return view();
        //return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    
    //根据前端的sortData/searchData，选择返回前端的asset list
    public function assList(Request $request,AssinfoModel $assMdl,$sortData=[],$searchData=[])
    {
        $this->priLogin();
                          
        $sortDefaults=array('listRows'=>10,'sortName'=>'assnum','sortOrder'=>'asc','pageNum'=>1,'assType'=>'_USUAL');
        // 接收前端的排序参数数组
        $sortData=!empty($request->param('sortData/a'))?$request->param('sortData/a'):$sortDefaults;
        $sortData=array_merge($sortDefaults,$sortData);
        $assType=$sortData['assType'];
        
        $searchDefaults=array();
        // 接收前端的搜索参数（json字符串），由前端保证传来的搜索参数值非0，非空。
        $searchData=!empty($request->param('searchData/a'))?$request->param('searchData/a'):$searchDefaults;
        $searchData=array_merge($searchDefaults,$searchData);
        //搜索查询条件数组
        $whereArr=[];
        $statusArr=[];
                
        $whereArr['brand_model']=!empty($searchData['brand_model'])?['like','%'.$searchData['brand_model'].'%']:'';
        $whereArr['assnum']=!empty($searchData['assnum'])?['like','%'.$searchData['assnum'].'%']:'';
        $whereArr['code']=!empty($searchData['code'])?['like','%'.$searchData['code'].'%']:'';
        $whereArr['bar_code']=!empty($searchData['bar_code'])?['like','%'.$searchData['bar_code'].'%']:'';
        
        $whereArr['dept_now']=!empty($searchData['dept_now'])?['in',$searchData['dept_now']]:'';
        $whereArr['place_now']=!empty($searchData['place_now'])?['in',$searchData['place_now']]:'';
        //将$searchData['status_now']的值（‘,’分隔的中文字符串）转为数据库中存储的类型编码数组
        if(!empty($searchData['status_now'])){
          $arr=explode(',',$searchData['status_now']);
          foreach(conAssStatusArr as $key=>$val){
            for($i=0;$i<count($arr);$i++){
              if($arr[$i]==$val){
                array_push($statusArr,$key);
              }
            }
          }
          $whereArr['status_now']=['in',$statusArr];
        }else{
          $whereArr['status_now']='';
        }
                
        $whereArr['keeper_now']=!empty($searchData['keeper_now'])?['like','%'.$searchData['keeper_now'].'%']:'';
        $whereArr['status_now_user_name']=!empty($searchData['status_now_user_name'])?['like','%'.$searchData['status_now_user_name'].'%']:'';
        
        //将空值的whereArr元素删除        
        foreach($whereArr as $key=>$val){
            if(empty($val)){
                unset($whereArr[$key]);
            }
        }
        
        //分页,每页$listRows条记录
        $assSet=$assMdl->assTypeQuery($assType)->where($whereArr)
                      ->order($sortData['sortName'], $sortData['sortOrder'])
                      ->paginate($sortData['listRows'],false,['type'=>'bootstrap','var_page' =>'pageNum','page'=>$sortData['pageNum'],
                        'query'=>['listRows'=>$sortData['listRows']]]);
        // 获取分页显示
        $assList=$assSet->render(); 
        
        //记录总数
        //$searchResultNum=count($this->priAssQueryObj($assType)->where($whereArr)->select()); 
        $searchResultNum=count($assMdl->assTypeQuery($assType)->where($whereArr)->select()); 
               
        //数量总计
        $quanCount=$assMdl->assTypeQuery($assType)->where($whereArr)->sum('quantity');
        
        $this->assign([
          'home'=>$request->domain(),
          'assSet'=>$assSet,
          'assList'=>$assList,
          
          'searchResultNum'=>$searchResultNum,
          'quanCount'=>$quanCount,
          
          //排序数组
          'sortData'=>$sortData,
          //搜索数组
          'searchData'=>$searchData,
          //状态有关的设置
          'conAssStatusArr'=>json_encode(conAssStatusArr,JSON_UNESCAPED_UNICODE), 
          'conAssStatusLabelArr'=>json_encode(conAssStatusLabelArr,JSON_UNESCAPED_UNICODE), 
          //调试用
          'whereArr'=>json_encode($whereArr,JSON_UNESCAPED_UNICODE),
          'display'=>$this->userName, 
		  
        ]);
        return view();
        
    }
    
    //响应前端请求，返回信息
    public function selectResponse(Request $request,AssinfoModel $assMdl,$req='',$assType='')
    {
      $this->priLogin();
      
      $req = empty($request->param('req'))?0:$request->param('req');
      $assType = empty($request->param('assType'))?0:$request->param('assType');
      
      if($assType){
        $res=$assMdl->assTypeQuery($assType)->field($req)->group($req)->select();
      }else{
        $res=$assMdl->field($req)->group($req)->select();
      }
               
      //将得到的数据集降为一维数组
      if(is_array($res)){
        $res=collection($res)->column($req);        
      }else{
        $res=$res->column($req);
      }
      
      //返回前端的是索引数组  
      return $res;
    }
    
     public function tblAssSingle(Request $request,AssinfoModel $assMdl)
    {
      $this->priLogin();
      $assSet=$assMdl::get($request->param('id'));
           
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
      $assSet=$assMdl::get($id);
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
    
     public function decodeBarcode(Request $request,AssinfoModel $assMdl)
    {
      $this->priLogin();
      $assSet=$assMdl::get($request->param('id'));
           
      $this->assign([
          'assSet'=>$assSet,
          'conAssStatusArr'=>json_encode(conAssStatusArr,JSON_UNESCAPED_UNICODE), 
          'conAssStatusLabelArr'=>json_encode(conAssStatusLabelArr,JSON_UNESCAPED_UNICODE),     
        ]);
      return view();
    }
}
