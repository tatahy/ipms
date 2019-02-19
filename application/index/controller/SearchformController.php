<?php

namespace app\index\controller;

use think\Request;
use think\Session;
use think\Controller;

# 继承了think\Controller类，可直接调用think\View，think\Request类的方法
# 类名与类文件名相同
class SearchformController extends Controller {
  //用户权限
  private $authArr=array();
  //用户登录状态
  private $log = 0;
  //用户登录状态
  private $username = '';
  //用户登录状态
  private $pwd = '';
  
  private $searchField=[
    'pat'=>['topic'=>'','author'=>'','type'=>0,'dept'=>0,'status'=>0]
  ];
  
  #patent的period与status的对应关系，本应用common.php中定义
  const SEARCHFIELD=[
    'pat'=>['topic'=>'','author'=>'','type'=>0,'dept'=>0,'status'=>0]
  ];
  
  
  
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
  
  private function priGetSearchForm ($arr) {
    $ent=array_key_exists('ent',$arr)?$arr['ent']:'pat'; 
    $searchData= array_key_exists('searchData',$arr)?$arr['searchData']:$this->searchField[$ent]; 
    
    $fileName=$ent.'searchform';
        
    $this->assign([
      'numTotal'=>1,
      'searchData'=>json_encode($searchData,JSON_UNESCAPED_UNICODE),
    ]);
    return view($fileName);
    //return $this->fetch($fileName);
  }
  
  public function index () {
    $this->priLogin();
    
    $reqObj=$this->request;  
    
    $rqArr=$reqObj->only(['ent','searchData']);
    
    return $this->priGetSearchForm($rqArr);
   // return $rqArr;
  }
  
  
  
  #直接调用think\View，think\Request类的方法
  public function example() {
    
    //
    $this->assign('domain',$this->request->url(true));
    return $this->fetch('example');
  }
}