<?php
namespace app\thesis\controller;

use think\Request;
use think\Session;
use think\Model;

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
    
    public function index()
    {
      return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
    }
    
    // 判断是否为登录用户
    private function _loginUser()
    {
      //通过$this->log判断是否是登录用户，非登录用户退回到登录页面
      $this->log=Session::get('log');
      
      if(1!=$this->log){
        return $this->error('无用户名或密码，请先登录系统');
      }    
    }
    
    public function thelist(Request $request)
    {
        $this->_loginUser();
        
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
        //--在index.html页面输出自定义信息的HTML代码块
		  $destr= "请求方法:".$request->method()."</br>";
                
        $this->assign([
              'destr'=>$destr,
              'home'=>$request->domain(),
              'username'=>$this->username,
              'year'=>date('Y')
        ]);
        return view();
    
    }
    
     // 输出total模板
    public function total(Request $request)
    {
      $this->_loginUser();
      
      return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
      
      //通过$log判断是否是登录用户，非登录用户退回到登录页面
      if(1!==$this->log){
        $this->error('未登录用户，请先登录系统');
      //$this->redirect($request->domain());
      }else{
        
      }
      
      
      $this->assign([
              'home'=>$request->domain(),
              
              // 分页显示所需参数
              'patTotal'=>$patTotal,
              'numTotal'=>$numTotal,
              'pageTotal'=>$pageTotal,
              'totalTableRows'=>$totalTableRows,
              'pageTotalNum'=>$pageTotalNum,
              
              // 表格搜索字段
              'searchPatName'=>$searchPatName,
              'searchDept'=>$searchDept,
              'searchPatStatus'=>$searchPatStatus,
              'searchPatType'=>$searchPatType,
              'searchWriter'=>$searchWriter,
              
    
              
              // 表格排序信息
              'sortName'=>$sortName,
              'sort'=>$sort,
              'totalTableRows'=>$totalTableRows,
              
              // 所return的页面显示的pat状态值$patStatus
              'patStatus'=>$patStatus,
              
        ]);
      return view();
      
      
    }
    
    
}
