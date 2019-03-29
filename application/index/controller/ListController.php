<?php

namespace app\index\controller;

use app\common\factory\EntinfoFactory as EntinfoMdl;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory as PhpSpreadsheetIO;

use app\index\controller\CommonController;

# 继承了CommonController类，可直接调用think\View，think\Request类的方法
# 类名与类文件名相同
class ListController extends CommonController {
   
  private $searchField=[
    'pat'=>['topic'=>'','author'=>'','type'=>0,'dept'=>0,'status'=>0],
    'ass'=>['brand_model'=>'','dept_now'=>0,'keeper_now'=>'','assnum'=>'','code'=>'',
            'bar_code'=>'','status_now'=>0,'place_now'=>0,'status_now_user_name'=>''],
    'pro'=>['topic'=>'','author'=>'','type'=>0,'dept'=>0,'status'=>0],
    'the'=>['topic'=>'','author'=>'','type'=>0,'dept'=>0,'status'=>0],
  ];
  
  private $sortData=['listRows'=>10,'sortName'=>'','sortOrder'=>'asc','pageNum'=>1,'showId'=>0];
  
    
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
    $whereArr=count($searchData)?$this->getMdlWhereArr($searchData,$queryField):[];
    #模板文件中进行显示的结果集
    $list=array();
    
    #返回前端的模板文件名
    $fileName=implode('-',['list',$ent]); 
            
    #选择模型对象并初始化
    $mdl=$this->getMdl($ent);
   
    #模型对象，查询、排序用
    $queryBase=$mdl->getPeriodSql($period,$whereArr)
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
    $this->chkLogin();
    #前端传来json字符串
    $rqArr=$this->request->param();
    
    //return json_encode($rqArr);
    return $this->priGetListTplFile($rqArr);
   
  }
  
  public function listFileMake() {
    $this->chkLogin();
    $res=['result'=>'','msg'=>''];
    
    #前端传来json对象
    $arr=$this->request->param();
   
    #写入数据表的数组
    $sheetArr=[];
    
    #取出需要的字段
    $ent= array_key_exists('ent',$arr)?$arr['ent']:'';
    $period=array_key_exists('period',$arr)?$arr['period']:'';
    $sheet= array_key_exists('sheet',$arr)?$arr['sheet']:['mode'=>'','idArr'=>[],'type'=>''];
    #要求$searchData的键名必须是数据库中的字段名
    $searchData= array_key_exists('searchData',$arr)?$arr['searchData']:[];
    #要求$queryField的键名必须是数据库中的字段名
    $queryField=array_key_exists('queryField',$arr)?$arr['queryField']:[];
    #进行模型查询的条件数组
    $whereArr=count($searchData)?$this->getMdlWhereArr($searchData,$queryField):[];
    #查询字段
    //$whereArr['id']=['>',0];
    
    if(!$ent){
      $res['result']=false;
      return json($res);
    }
    
    #选择模型对象并初始化
    $mdl=$this->getMdl($ent);

    if($sheet['mode']=='excluded' && array_key_exists('idArr',$sheet)){
      #查询结果数与要排除的记录数一致
      if($mdl->getPeriodSql($period,$whereArr)->count()==count($sheet['idArr'])){
        $res['result']=false;
        $res['msg']='未选中需导出的记录。';
        $mdl=null;
        return json($res);
      }
      
      #添加查询字段
      $whereArr['id']=['notin',$sheet['idArr']];
    }
    
    $head= array_key_exists('head',$sheet)?$sheet['head']:['fieldEn'=>$mdl->getTableFields(),'fieldChi'=>[]]; 
    
    #结果数据集
    $set=$mdl->getPeriodSql($period,$whereArr)->select();
    $mdl=null;
        
    #数据表标题行
    $sheetArr[0]=(count($head['fieldChi']))?$head['fieldChi']:$head['fieldEn'];
    #数据表内容行
    for($i=1;$i<=count($set);$i++){
      foreach($head['fieldEn'] as $k=>$v){
        $sheetArr[$i][$k]=$set[$i-1][$v];
      }
    }
    #调用priMakeListFile()生成文件，返回生成的文件名
    $res['msg']=$this->priMakeListFile($sheetArr,$sheet['type']);
    
    if(!empty($res['msg'])){
      $res['result']=true;
    }
    
    return json($res);
  }
  
  public function listFileDownload() {
    $this->chkLogin();
    #前端传来文件名
    $fileName=$this->request->param('fileName');
    return fn_file_download($fileName);
    
  }
  public function listFileDelete() {
    $this->chkLogin();
    #前端传来文件名
    $fileName=$this->request->param('fileName');
    return fn_file_delete($fileName);
  }
  
  
   
  
}