<?php

namespace app\index\controller;

use app\index\controller\CommonController;

use app\common\factory\EntinfoFactory as EntinfoMdl;

# 继承了think\Controller类，可直接调用think\View，think\Request类的方法
# 类名与类文件名相同,
# 若配置文件conf.php中'controller_suffix' 设为true，则类名需以‘Controller’结尾，
# 且‘Controller’之前的单词必须第一个字母大写，其余小写，否则类无法加载会报‘控制器不存在’。
class SearchformController extends CommonController {
 
  #单个select返回前端的数据结构
  const SELECTSINGLE=['num'=>0,'val'=>[''],'txt'=>['']];
  
  private function priGetFormTplFile ($arr) {
    $ent=array_key_exists('ent',$arr)?$arr['ent']:'pat'; 
    
    $fileName=implode('-',['search','form',$ent]);
   
    return view($fileName);
  }
  
  private function priGetEntDBfieldGroup($ent,$period,$field,$whereArr=[]){
    //选择模型对象并初始化
    $mdl= $this->getMdl($ent);
    return $mdl->getFieldGroupByArr($field,self::SELECTSINGLE,$period,$whereArr);
  }
  
 
  public function index () {
    $this->chkLogin();
    
    $reqObj=$this->request;  
    
    $rqArr=$reqObj->only(['ent','searchData']);
    
    return $this->priGetFormTplFile($rqArr);
   // return $rqArr;
  }
   
  #
  public function getSelOptData() {
    $this->chkLogin();
    
    $selArr=[];
    #前端传来json字符串
    $reqArr=$this->request->param(); 
    #选用部分 
    $ent=$reqArr['ent'];
    $period=$reqArr['period'];
    $searchData=array_key_exists('searchData',$reqArr)?$reqArr['searchData']:[];
    $fmArr=$reqArr['queryField'];
        
    $whereArr=$this->getMdlWhereArr($searchData,$fmArr);

    #每个select赋初值
    foreach($fmArr as $k=>$v){
      if($v['tagName']=='select'){
        $selArr[$k]=$this->priGetEntDBfieldGroup($ent,$period,$k,$whereArr);
      }
    }
    
   #返回前端json字符串
    return json($selArr);
  }
}