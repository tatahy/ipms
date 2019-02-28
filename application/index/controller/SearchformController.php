<?php

namespace app\index\controller;

use think\Request;
use think\Session;
use think\Controller;

use app\index\model\Assinfo;
use app\index\model\Patinfo;

# 继承了think\Controller类，可直接调用think\View，think\Request类的方法
# 类名与类文件名相同,
# 若配置文件conf.php中'controller_suffix' 设为true，则类名需以‘Controller’结尾，
# 且‘Controller’之前的单词必须第一个字母大写，其余小写，否则类无法加载会报‘控制器不存在’。
class SearchformController extends Controller {
  //用户权限
  private $authArr=array();
  //用户登录状态
  private $log = 0;
  //用户登录状态
  private $username = '';
  //用户登录状态
  private $pwd = '';
  //定义前端可搜索的数据库字段
  const SEARCHDBFIELD=[
    'pat'=>['topic'=>'','author'=>'','type'=>0,'dept'=>0,'status'=>0],
    'ass'=>['brand_model'=>'','dept_now'=>0,'keeper_now'=>'','assnum'=>'','code'=>'',
            'bar_code'=>'','status_now'=>0,'place_now'=>0,'status_now_user_name'=>''],
    'pro'=>['topic'=>''],
    'the'=>['topic'=>'']
  ];
  #单个select返回前端的数据结构
  const SELECTSINGLE=['num'=>0,'val'=>[''],'txt'=>['']];
  
  #验证是否为登录用户
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
  
  private function priGetFormTplFile ($arr) {
    $ent=array_key_exists('ent',$arr)?$arr['ent']:'pat'; 
    $searchData= array_key_exists('searchData',$arr)?$arr['searchData']:self::SEARCHDBFIELD[$ent]; 
    
    $fileName=$ent.'searchform';
        
    $this->assign([
      'numTotal'=>1,
      'searchData'=>json_encode($searchData,JSON_UNESCAPED_UNICODE),
    ]);
    return view($fileName);
    //return $this->fetch($fileName);
  }
  
  private function priGetEntDBfieldGroup($ent,$period,$field){
    $res=self::SELECTSINGLE;
    #模型对象
    $mdl='';   
    
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
    $res=$mdl->getFieldGroupByArr($field,$res,$period);
    
    return $res;
  }
  
  public function index () {
    $this->priLogin();
    
    $reqObj=$this->request;  
    
    $rqArr=$reqObj->only(['ent','searchData']);
    
    return $this->priGetFormTplFile($rqArr);
   // return $rqArr;
  }
   
  #
  public function getSelOptData() {
    $this->priLogin();
    
    $selArr=[];
    #前端传来json字符串
    $reqArr=$this->request->param(); 
    #选用部分 
    $ent=$reqArr['ent'];
    $period=$reqArr['period'];
    $fmArr=$reqArr['queryField'];

    #每个select赋初值
    foreach($fmArr as $k=>$v){
      if($v['tagName']=='select'){
        $selArr[$k]=$this->priGetEntDBfieldGroup($ent,$period,$k);
      }
    }
    
   #返回前端json字符串
    return json($selArr);
  }
}