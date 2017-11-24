<?php
namespace app\project\controller;

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
    
    public function prolist(Request $request)
    {
        $this->_loginUser();
        
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
        //--在index.html页面输出自定义信息的HTML代码块
		  $destr= "请求方法:".$request->method()."</br>";
                
        $this->assign([
              'destr'=>$destr,
              'home'=>$request->domain(),
              'username'=>$this->username,
        ]);
        return view();
    
    }
    
     // 输出total模板
    public function total(Request $request)
    {
      // 应用私有方法_loginUser()，判断是否是登录用户，非登录用户退回到登录页面
      $this->_loginUser();
      
      return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
      
      //$totalTableRows接收前端页面传来的分页时每页显示的记录数，默认为10
        if(!empty($request->param('totalTableRows'))){
          $totalTableRows=$request->param('totalTableRows');
        }else{
          $totalTableRows=10;
        }
        
         // 接收前端分页页数变量：“pageUserNum”
        if(!empty($request->param('pageTotalNum'))){
          $pageTotalNum=$request->param('pageTotalNum');
        }else{
          $pageTotalNum=1;
        }
        
        // $sortName接收前端页面传来的排序字段名
        if(!empty($request->param('sortName'))){
          $sortName=$request->param('sortName');
        }else{
          $sortName='_PATNAME';
        }
        
        // $sort接收前端页面传来的排序顺序
        if(!empty($request->param('sort'))){
          $sort=$request->param('sort');
        }else{
          $sort='_ASC';
        }
        
        // $patStatus接收前端页面传来的专利状态值
        if(!empty($request->param('patStatus'))){
          $patStatus=$request->param('patStatus');
        }else{
          $patStatus=0;
        }
        
         // 查询词1，'searchPatName'
        if(!empty($request->param('searchPatName'))){
          $searchPatName=$request->param('searchPatName');
        }else{
          $searchPatName='';
        } 
        
        // 查询词2，'searchDept'
        if(!empty($request->param('searchDept'))){
          $searchDept=$request->param('searchDept');
        }else{
          $searchDept=0;
        } 
        
        // 查询词3，'searchPatStatus'
        if(!empty($request->param('searchPatStatus'))){
          $searchPatStatus=$request->param('searchPatStatus');
        }else{
          $searchPatStatus=0;
        }
        
        // 查询词4，'searchPatType'
        if(!empty($request->param('searchPatType'))){
          $searchPatType=$request->param('searchPatType');
        }else{
          $searchPatType=0;
        } 
        
        // 查询词5，'searchWriter'
        if(!empty($request->param('searchWriter'))){
          $searchWriter=$request->param('searchWriter');
        }else{
          $searchWriter='';
        }  
      
      // 选择排序字段
      switch($sortName){
        case '_PATNAME':
          $strOrder='topic';
        break;
            
        case '_PATTYPE':
          $strOrder='pattype';
        break;
        
        case '_AUTHOR':
          $strOrder='author';
        break;
        
        case '_INVENTOR':
          $strOrder='inventor';
        break;
            
        case '_PATOWNER':
          $strOrder='patowner';
        break;
            
        case '_SUBMITDATE':
          $strOrder='submitdate';
        break;
            
        case '_DEPT':
          $strOrder='dept';
        break;
            
        case '_PROJECT':
          $strOrder='pronum';
        break;
        
        case '_PATSTATUS':
          $strOrder='status';
        break;
            
        //默认按字段“topic”
        default:
          $strOrder='topic';  
          $sortName="_PATNAME";
        break;
      } 
      
      //  组合升序or降序查询
      if($sort=="_ASC"){
          $strOrder=$strOrder.' asc';
      }else{
          $strOrder=$strOrder.' desc';
          
      }
      
      // 组合状态查询条件，
      switch($patStatus){
        case '#total':
          $map='';
          
          // 5个查询词
          if($searchDept){
            $map['dept']=$searchDept;
          }
          
          if($searchPatName){
            $map['topic']=['like','%'.$searchPatName.'%'];
          }
          
          if($searchPatStatus){
            $map['status']=$searchPatStatus;
          }
          
          if($searchPatType){
            $map['pattype']=$searchPatType;
          }
          
          if($searchWriter){
            $map['author']=['like','%'.$searchWriter.'%'];
          }
          
        break;
        // '新增'   
        case '#newAdd':
          $map['status'] ='新增';
        break;
        // ''    
        case '#apply':
          $map['status'] =['in',['返回修改','申报']];
        break;
        // ''    
        case '#authorize':
          $map['status'] ='授权';
        break;
        // ''    
        case '#render':
          $map['status'] =['in',['续费授权','续费']];
        break;
        // ''    
        case '#abandon':
          $map['status'] ='放弃';
        break;
        // ''
        case '#reject':
          $map['status'] ='驳回';
        break;
            
        //默认所有状态专利'#total':
        default:
          $map='';
        break;
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
