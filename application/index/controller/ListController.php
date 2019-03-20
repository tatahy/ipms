<?php

namespace app\index\controller;

use think\Request;
use think\Session;
use think\Controller;

use app\index\model\User; 
use app\index\model\Patinfo as PatinfoModel;
use app\index\model\Assinfo as AssinfoModel;
use app\index\model\Theinfo as TheinfoModel;
use app\index\model\Proinfo as ProinfoModel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory as PhpSpreadsheetIO;

# 继承了think\Controller类，可直接调用think\View，think\Request类的方法
# 类名与类文件名相同
class ListController extends Controller {
  //用户权限
  private $authArr=array();
  //用户登录状态
  private $log = 0;
  //用户登录状态
  private $userName = '';
  //用户登录状态
  private $pwd = '';
  //用户所属部门
  private $dept = '';
  
  private $searchField=[
    'pat'=>['topic'=>'','author'=>'','type'=>0,'dept'=>0,'status'=>0],
    'ass'=>['brand_model'=>'','dept_now'=>0,'keeper_now'=>'','assnum'=>'','code'=>'',
            'bar_code'=>'','status_now'=>0,'place_now'=>0,'status_now_user_name'=>''],
    'pro'=>['topic'=>'','author'=>'','type'=>0,'dept'=>0,'status'=>0],
    'the'=>['topic'=>'','author'=>'','type'=>0,'dept'=>0,'status'=>0],
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
    $this->userName=Session::get('username');
    $this->dept=Session::get('dept');
    $this->pwd=Session::get('pwd');
    
    return $this->log;
  }
  //根据参数组装模型查询用$whereArr
  private function priGetMdlWhereArr ($searchArr,$searchTypeArr) {
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
  
  private function priGetListTplFile ($arr) {
        
    $ent=array_key_exists('ent',$arr)?$arr['ent']:'pat'; 
    #要求$searchData的键名必须是数据库中的字段名
    $searchData= array_key_exists('searchData',$arr)?$arr['searchData']:[];
    $sortData= array_key_exists('sortData',$arr)?$arr['sortData']:$this->sortData; 
    $period=array_key_exists('period',$arr)?$arr['period']:'';
    #要求$queryField的键名必须是数据库中的字段名
    $queryField=array_key_exists('queryField',$arr)?$arr['queryField']:[];
    
    #查询、排序结果总数
    $searchResultNum=0;
    #模型对象
    $mdl='';    
    #进行模型查询的条件数组
    $whereArr=count($searchData)?$this->priGetMdlWhereArr($searchData,$queryField):[];
    #模板文件中进行显示的结果集
    $list=array();
    
    #返回前端的模板文件名
    $fileName=implode('-',['list',$ent]); 
            
    #选择模型对象
    $mdl=$this->priGetMdl($ent);
    
    #模型对象，查询、排序用
    $queryBase=$mdl->getPeriodSql($period)
                    ->where($whereArr)
                    ->order($sortData['sortName'],$sortData['sortOrder']);
      
    #查询、排序结果数据集：
    $baseSet=$queryBase->select();
    $baseSet=is_array($baseSet)?collection($baseSet):$baseSet;
      
    #查询、排序结果总数
    $searchResultNum=count($baseSet);
      
    if($searchResultNum){
      #要显示的结果集
      $list=$baseSet->slice(($sortData['pageNum']-1)*$sortData['listRows'],$sortData['listRows']);
    }
      
    #模型对象，排序、查询后分页用
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
      #分页对象（html字符串）
      'pageNode'=>$pageSet->render(),
      #排序数组
      'sortData'=>$sortData,
//      'sortName'=>$sortData['sortName'],
//      #搜索数组。JSON_UNESCAPED_UNICODE，保持编码格式。若前端文件采用utf-8编码，汉字就可直接解析显示。
//      'searchData'=>json_encode($searchData,JSON_UNESCAPED_UNICODE),
    ]);
    
    #渲染页面文件并输出
    return view($fileName);
    //return $this->fetch($fileName);
  }
  //选择模型对象并初始化
  private function priGetMdl($ent) {
    $mdl=null;
    switch($ent){
      case 'pat':
        $mdl= new PatinfoModel();
        break;
      case 'ass':
        $mdl= new AssinfoModel();
        break;
      case 'pro':
        $mdl= new ProinfoModel();
        break;
      case 'the':
        $mdl= new TheinfoModel();
        break;
    }
    
    return $mdl->initModel($this->userName,$this->dept,$this->authArr[$ent]);  
  }
  //得到特定的list数据集转为指定的文件
  private function priMakeListFile($arr=[],$type='xlsx') {
    
    $spreadSheet= new Spreadsheet();
    $sheet= $spreadSheet->getActiveSheet();

    #2维数组写入数据表
    $sheet->fromArray($arr,null);
    $type=strtolower($type);
    $fileName=Md5(strtotime("now")).'.'.$type;
    
    $path='./downloads/'.$fileName;
    
    $writer=PhpSpreadsheetIO::createWriter($spreadSheet,ucfirst($type));
    $writer->save($path);
    
    //header('Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//    header('Content-Disposition:attachment;filename="'.$fileName.'"');
//    header('Cache-Control:max-age=0');
//    $writer=new Xlsx($spreadSheet);
//    $writer->save('php://output');
       
    $spreadSheet->disconnectWorksheets();
    unset($spreadSheet);
    
    if(!file_exists($path)){
      $fileName='';  
    }
    return $fileName;
  }
  
  public function index () {
    $this->priLogin();
    #前端传来json字符串
    $rqArr=$this->request->param();
    
    //return json_encode($rqArr);
    return $this->priGetListTplFile($rqArr);
   
  }
  
  public function makeListFile() {
    $this->priLogin();
    $res=['result'=>'','fileName'=>''];
    
    #前端传来json对象
    $arr=$this->request->param();
    
    #查询字段
    $whereArr['id']=['>',0];
    #写入数据表的数组
    $sheetArr=[];
    
    #取出需要的字段
    $ent= array_key_exists('ent',$arr)?$arr['ent']:'';
    $period=array_key_exists('period',$arr)?$arr['period']:'';
    $sheet= array_key_exists('sheet',$arr)?$arr['sheet']:['mode'=>'','idArr'=>[],'type'=>''];
    
    if(!$ent){
      $res['result']=false;
      return $res;
    }
    
    #模型对象
    $mdl=$this->priGetMdl($ent);

    if($sheet['mode']=='excluded'){
      #查询结果数与要排除的记录数一致
      if(isset($sheet['idArr']) && $mdl->getPeriodNum($period)==count($sheet['idArr'])){
        $res['result']=false;
        return $res;
      }
      
      if(isset($sheet['idArr'])){
        #查询字段
        $whereArr['id']=['notin',$sheet['idArr']];
      }
    }
    
    $head= array_key_exists('head',$sheet)?$sheet['head']:['fieldEn'=>$mdl->getTableFields(),'fieldChi'=>[]]; 
    
    #结果数据集
    $set=$mdl->getPeriodSql($period)->where($whereArr)->select();    
    #数据表标题行
    $sheetArr[0]=(count($head['fieldChi']))?$head['fieldChi']:$head['fieldEn'];
    #数据表内容行
    for($i=1;$i<=count($set);$i++){
      foreach($head['fieldEn'] as $k=>$v){
        $sheetArr[$i][$k]=$set[$i-1][$v];
      }
    }
    
    $res['fileName']=$this->priMakeListFile($sheetArr,$sheet['type']);
    
    if(!empty($res['fileName'])){
      $res['result']=true;
    }
    $mdl=null;
    return $res;
  
  }
  
  public function downloadListFile() {
    $this->priLogin();
    #前端传来文件名
    $fileName=$this->request->param('fileName');
    return _commonDownloadFile($fileName);
    
  }
  
  
   
  
}