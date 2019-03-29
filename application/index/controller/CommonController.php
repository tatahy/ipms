<?php

namespace app\index\controller;

use think\Session;
use think\Controller;

use app\common\factory\EntinfoFactory as EntinfoMdl;

# 继承了think\Controller类，可直接调用think\View，think\Request类的方法
# 类名与类文件名相同,
# 若配置文件conf.php中'controller_suffix' 设为true，则类名需以‘Controller’结尾，
# 且‘Controller’之前的单词必须第一个字母大写，其余小写，否则类无法加载会报‘控制器不存在’。
class CommonController extends Controller {
   //用户权限
  protected $authArr=array();
  //用户登录状态
  protected $log = 0;
  //用户登录状态
  protected $userName = '';
  //用户登录状态
  protected $pwd = '';
  //用户所属部门
  protected $dept = '';
  
  #验证是否为登录用户
  protected function chkLogin(){
  
    $this->log=Session::has('log')?Session::get('log'):0;
    //通过$log判断是否是登录用户，非登录用户退回到登录页面
    if(!$this->log){
      return $this->error('未登录用户，请先登录系统','index/login');
    }
    //return $this->success('priLogin()调试，'.json_encode(Session::get('authArr')),'login','',10);
    $this->authArr=Session::get('authArr');
    $this->userName=Session::get('username');
    $this->dept=Session::get('dept');
    $this->pwd=Session::get('pwd');
    
    return $this->log;
  }
  protected function getMdl($ent){
    $nameSpace='app\\index\\model';
    $mdl= EntinfoMdl::factory($ent,$nameSpace)->initModel($this->userName,$this->dept,$this->authArr[$ent]);
    return $mdl;
    
  }
  //根据参数组装模型查询用$whereArr
  protected function getMdlWhereArr ($searchArr,$searchTypeArr) {
    $whereArr=[];
    
    if(count($searchArr)==0 || count($searchTypeArr)==0){
      return $whereArr;
    }
    #组装$whereArr，要求$searchArr的键名必须是数据库中的字段名
    foreach($searchArr as $field=>$v){
      if(!empty($v)){
        switch($searchTypeArr[$field]['tagName']){
          case 'input':
            $operator='like';
            $queryVal='%'.$v.'%';
            break;
          case 'select':
            $operator='in';
            $queryVal=$v;
            break;
        }
        $whereArr[$field]=[$operator,$queryVal];  
      }
    }     
    
    return $whereArr;
  }

}