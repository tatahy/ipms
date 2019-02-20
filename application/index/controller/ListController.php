<?php

namespace app\index\controller;

use think\Request;
use think\Session;
use think\Controller;

use app\index\model\User; 
use app\index\model\Patinfo;
use app\index\model\Assinfo;

# 继承了think\Controller类，可直接调用think\View，think\Request类的方法
# 类名与类文件名相同
class ListController extends Controller {
  //用户权限
  private $authArr=array();
  //用户登录状态
  private $log = 0;
  //用户登录状态
  private $username = '';
  //用户登录状态
  private $pwd = '';
  
  private $searchField=[
    'pat'=>['topic'=>'','author'=>'','type'=>0,'dept'=>0,'status'=>0],
    'ass'=>['topic'=>''],
    'pro'=>['topic'=>''],
    'the'=>['topic'=>''],
  ];
  
  private $sortData=['listRows'=>10,'sortName'=>'','sortOrder'=>'asc','pageNum'=>1,'showId'=>0];
  
  private function priLogin(){
  
    $this->log=Session::has('log')?Session::get('log'):0;
    //通过$log判断是否是登录用户，非登录用户退回到登录页面
    if(!$this->log){
      return $this->error('未登录用户，请先登录系统','index/login');
    }
    //return $this->success('priLogin()调试，'.json_encode(Session::get('authArr')),'login','',10);
    $this->authArr=Session::get('authArr');
    $this->username=Session::get('username');
    $this->pwd=Session::get('pwd');
    
    return $this->log;
  }
  
  private function priGetList ($arr) {
        
    $ent=array_key_exists('ent',$arr)?$arr['ent']:'pat'; 
    $searchData= array_key_exists('searchData',$arr)?$arr['searchData']:$this->searchField[$ent]; 
    $sortData= array_key_exists('sortData',$arr)?$arr['sortData']:$this->sortData; 
    $period=array_key_exists('period',$arr)?$arr['period']:'';
    //return $arr;
    $queryField=array_key_exists('queryField',$arr)?$arr['queryField']:[];
    
    #查询、排序结果总数
    $searchResultNum=0;
    #模型对象
    $mdl='';    
    #返回前端进行显示的内容
    $list=array();
    #进行搜索的条件数组
    $whereArr=[]; 
    
    $fileName=$ent.'List';
    
    ##组装$whereArr
    if(count($queryField)){
      foreach($queryField as $field=>$v){
        $operator='like';
        $queryStr=!empty($searchData[$field])?'%'.$searchData[$field].'%':'';
        
        if($v['tagName']=='select'){
          $operator='in';
          $queryStr=!empty($searchData[$field])?$searchData[$field]:'';
        }
        
        if($queryStr){
          $whereArr[$field]=[$operator,$queryStr];
        }
        $whereArr[$field]=$queryStr;
      }
      
    }
      //#前端input值搜索,like操作符
//      $whereArr['topic']=!empty($searchData['topic'])?['like','%'.$searchData['topic'].'%']:'';
//      $whereArr['author']=!empty($searchData['author'])?['like','%'.$searchData['author'].'%']:'';
//      
//      #前端select值搜索，in操作符
//      $whereArr['dept']=!empty($searchData['dept'])?['in',$searchData['dept']]:'';
//      $whereArr['status']=!empty($searchData['status'])?['in',$searchData['status']]:'';
//      $whereArr['type']=!empty($searchData['type'])?['in',$searchData['type']]:'';
      
      #将空白元素删除
      foreach($whereArr as $key=>$val){
        if(empty($val)){
          unset($whereArr[$key]);
        }
      }
      
      #选择模型对象
      switch($ent){
        case 'pat':
          $mdl= new Patinfo;
          break;
        case 'ass':
          $mdl= new Assinfo;
          break;
        case 'pro':
        
          break;
        case 'the':
        
          break;
      }
      
      #pat模型对象，查询、排序用
      $queryBase=$mdl->getPeriodSql($period)
                        ->where($whereArr)
                        ->order($sortData['sortName'],$sortData['sortOrder']);
      
      #查询、排序结果数据集：
      $baseSet=$queryBase->select();
      $baseSet=is_array($baseSet)?collection($baseSet):$baseSet;
      
      #查询、排序结果总数
      $searchResultNum=count($baseSet);
      
      if($searchResultNum){
        #本页要显示的记录
        $list=$baseSet->slice(($sortData['pageNum']-1)*$sortData['listRows'],$sortData['listRows']);
      }
      
      #pat模型对象，排序、查询后分页用
      $pageQuery=$mdl->where('id','in',$baseSet->column('id'))
                      ->order($sortData['sortName'],$sortData['sortOrder']);
      #分页对象，符合查询条件的所有iss记录分页,每页$listRows条记录
      $pageSet=$pageQuery->paginate($sortData['listRows'],false,['type'=>'bootstrap','var_page' =>'pageNum','page'=>$sortData['pageNum'],
                        'query'=>['listRows'=>$sortData['listRows']]]);
      
      $this->assign([
        'home'=>$this->request->domain(),
        'searchResultNum'=>$searchResultNum,
        #当前页显示内容
        'list'=>$list,
        #分页对象
        'pageSet'=>$pageSet,
        #排序数组
        'sortData'=>$sortData,
        'sortName'=>$sortData['sortName'],
        #搜索数组。JSON_UNESCAPED_UNICODE，保持编码格式。若前端文件采用utf-8编码，汉字就可直接解析显示。
        'searchData'=>json_encode($searchData,JSON_UNESCAPED_UNICODE),
      ]);
    
    ##
    return view($fileName);
    //return $this->fetch($fileName);
  }
  
  public function index () {
    $this->priLogin();
    
    
    $rqArr=$this->request->request();
    
  
    return $this->priGetList($rqArr);
   
  }
  
}