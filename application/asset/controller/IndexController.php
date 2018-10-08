<?php
namespace app\asset\controller;

use think\Request;
use think\Session;
use think\View;

use app\asset\model\Assinfo as AssinfoModel;

class IndexController extends \think\Controller
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
    
    public function index(Request $request,AssinfoModel $assMdl)
    {
        $this->priLogin();
        //数量总计
        $quanCount=$assMdl->where('quantity','>=',1)->sum('quantity');
        
        $sortData=array('listRows'=>10,'sortName'=>'assnum','sortOrder'=>'asc','pageNum'=>1);
        
        $searchData=array('brand_model'=>'','assnum'=>'','code'=>'','bar_code'=>'','dept_now'=>'','place_now'=>'','keeper_now'=>'');
        
        $this->assign([
                    
          'home'=>$request->domain(),
          'username'=>$this->username,
          'year'=>date('Y'),
          
          'sortData'=>$sortData,
          'searchData'=>json_encode($searchData,JSON_UNESCAPED_UNICODE),
          'quanCount'=>$quanCount,
        ]);
        return view();
        //return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
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
        $whereArr['status_now']=!empty($searchData['status_now'])?$searchData['status_now']:'';
        //将$whereArr['status_now']的值（中文）转为类型编码
        foreach(conAssStatusArr as $key=>$val){
          if($whereArr['status_now']==$val){
            $whereArr['status_now']=$key;
          }
        }
        
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
          
          'searchResultNum'=>$searchResultNum,
          
          //排序数组
          'sortData'=>$sortData,
          
          //搜索数组
          'searchData'=>$searchData,
          
          'whereArr'=>json_encode($whereArr,JSON_UNESCAPED_UNICODE)
		  
        ]);
        return view();
        
    }
    
    //响应前端请求，返回信息
    public function selectResponse(Request $request,AssinfoModel $assMdl,$req='')
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
}
