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
        
        $numTotal=count($assMdl::all());
        
        $sortData=array('listRows'=>10,'sortName'=>'assnum','sortOrder'=>'asc','pageNum'=>1);
        
        $this->assign([
                    
          'home'=>$request->domain(),
          'username'=>$this->username,
          'year'=>date('Y'),
          
          'sortData'=>$sortData,
          
          'numTotal'=>$numTotal,
        ]);
        return view();
        //return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    
    //根据前端的sortData/searchData，选择返回前端的asset list
    public function assList(Request $request,AssinfoModel $assMdl,$sortData=[],$searchData=[])
    {
        $this->priLogin();
        
        //分页参数
        $listRows=!empty($request->param('listRows'))?$request->param('listRows'):10;
        $pageNum=!empty($request->param('pageNum'))?$request->param('pageNum'):1;
        
        $sortDefaults=array('listRows'=>10,'sortName'=>'assnum','sortOrder'=>'asc','pageNum'=>1);
        // 接收前端的排序参数数组
        $sortData=!empty($request->param('sortData/a'))?$request->param('sortData/a'):$sortDefaults;
        $sortData=array_merge($sortDefaults,$sortData);
        
        $searchDefaults=array();
        // 接收前端的搜索参数数组，由前端保证传来的搜索参数值非0，非空。
        $searchData=!empty($request->param('searchData/a'))?$request->param('searchData/a'):$searchDefaults;
        $searchData=array_merge($searchDefaults,$searchData);
        
                
        //利用模型对象得到asset记录总数
        $numTotal=count($assMdl::all());
        
        //分页,每页$listRows条记录
        $assSet=$assMdl::where('id','>',0)
                        //->order('place_now', 'asc')
                        ->order($sortData['sortName'], $sortData['sortOrder'])
                        ->paginate($sortData['listRows'],false,['type'=>'bootstrap','var_page' =>'pageNum','page'=>$sortData['pageNum'],
                        'query'=>['listRows'=>$sortData['listRows']]]);
        // 获取分页显示
        $assList=$assSet->render(); 
        
        $this->assign([
                    
          //'home'=>$request->domain(),
//          'username'=>$this->username,
//          'year'=>date('Y'),
          
          'numTotal'=>$numTotal,
          
          
          'assSet'=>$assSet,
          'assList'=>$assList,
          
          
          //排序字段值
          'sortData'=>$sortData,
          
          //分页参数
          
          //搜索字段值
		  
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
