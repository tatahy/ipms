<?php

/**
 * @author TATA
 * @copyright 2019
 */
#用于动态获取$ent对应的modal
namespace app\common\factory;

use think\Model;
use think\Db;

use app\admin\model\Dept as DeptModel;

class EntinfoFactory extends Model {
  
  #仅用于子类的属性
  protected $entPeriod=[];
  protected $entType=[];
  protected $entity=[];
  protected $entityAbbr=[];
    
  protected $statusArr=[];
    
  protected $entAuth;
  protected $userName;
  protected $dept;
   
  #覆盖父类构造方法
  public function __construct($data = []){
    parent::__construct($data);
    $this->statusArr=fn_status_array_en2_chi($this->entity);
  }
  
  public static function factory($ent,$nameSpace){
    #$nameSpace:继承类所在命名空间。
    #通过命名空间直接定位要使用的类，
    $className= $nameSpace.DS.ucfirst($ent).'info';
    #返回实例化的对象。
    return new $className();
  }
  
  #初始化模型
    public function initModel($username='', $dept='', $authArr=[]) {
      
      $this->entAuth=$authArr;
      $this->userName=$username;
      $this->dept=$dept;
     
      return $this;
    }
    
    public function getUserAuthSql($whereArr=[]) {
      $auth=$this->entAuth;
      $username=$this->userName;
      $dept=$this->dept;
      $authNum=0;
      
      foreach($auth as $v){
        if($v){
          $authNum++;
        }
      }
      
      #无权限的全局查询结果
      if(!$authNum){
        return $this->where('id','<',0);
      }
      
      #其他权限时的全局查询结果
      $query=$this->where('id','>',0)->where($whereArr);
      
      return $query;
    }
    
    #得到在period里的query对象
    public function getPeriodSql($period='',$whereArr=[]) {
      
      $psArr=$this->entPeriod;
      $pArr=array_keys($psArr);
      #模型查询中的字段
      $field='id';
      #保证$period的值是规定的范围内
      $period=in_array($period,$pArr)?$period:'total';
      
      #模型查询中的字段
      if($period!=$pArr[0]){
        $field=($this->entity=='asset')?'status_now':'status';
      }
      
      $arr[$field]=[$psArr[$period]['queryExp'],$psArr[$period]['status']];
      
      $whereArr=array_merge($whereArr,$arr);
      
      $query=$this->getUserAuthSql($whereArr);
      
      return $query;
    }
    
    #得到在period里的所有pat
    public function getPeriodSet($period='',$whereArr=[]) {
      return $this->getPeriodSql($period,$whereArr=[])->select();
    }
    #得到在period里的所有pat的num
    public function getPeriodNum($period='',$whereArr=[]) {
      $num='';
      $numArr=[];
      $pArr=array_keys($this->entPeriod);
      
      if(!empty($period)){
        return $this->getPeriodSql($period,$whereArr=[])->count();
      }
      
      foreach($pArr as $key=>$val){
        $numArr[$val]=$this->getPeriodSql($val,$whereArr=[])->count();
      } 
      return $numArr;
    }
    #得到在period的指定field字段的groupby内容
    public function getFieldGroupByArr($field,$arr=[],$period='',$whereArr=[]) {
      
      $valArr=[]; 
      $keyArr=[];     
      $tempArr=[];#中间数组
      $tArr=[];   #键值转换数组
    #设定返回数组的默认结构
      $arr=array_merge(['num'=>0,'val'=>[''],'txt'=>['']],$arr);
    
    #组装$tArr
      if($field=='type') $tArr=$this->entType;
      if($field=='status_now' || $field=='status') $tArr=fn_status_array_en2_chi($this->entity);      
      if($field=='dept_now' || $field=='dept') {
        #得到dept的键值转换数组$tArr。abbr为键，name为值的关联数组
        $deptSet=DeptModel::all();
        #转换为数据集
        $deptSet=is_array($deptSet)?collection($deptSet):$deptSet;
        $tArr=array_combine($deptSet->column('abbr'),$deptSet->column('name'));
      }
   
      #组装$tempArr
      if(count($whereArr)){
        $entSet=$this->getPeriodSql($period,$whereArr)->select();
      }else{
        $entSet=$this->getPeriodSet($period);
      }
      #转换为数据集
      $entSet=is_array($entSet)?collection($entSet):$entSet;
    #得到$field字段值。若定义了$field字段的修改器，此处为经过修改器后的输出值（去掉重复值）
      $valArr=array_unique($entSet->column($field));
      if(!count($valArr)){
        return $arr;
      }
      #其他字段的$tArr
      if(!count($tArr)){
        $tArr=array_combine($valArr,$valArr);
      }
      
      #组装$tempArr
      foreach($valArr as $k => $v){
        $keyArr[$k]=array_search($v,$tArr);
        if($field=='dept_now' || $field=='dept'){
         // $valArr[$k]=$v.', 简称: '.array_search($v,$tArr);
          $abbr=array_search($v,$tArr)?array_search($v,$tArr):'无';
          $valArr[$k]=$v.', 简称: '.$abbr;
          $keyArr[$k]=$v;
        }
      }
      $tempArr=array_combine($keyArr,$valArr);
      #对中间数组以键名升序排序
      ksort($tempArr);
    #$arr赋值
      $arr['num']=count($tempArr);
      $arr['val']=array_keys($tempArr);
      $arr['txt']=array_values($tempArr);
      
      return $arr;
    }
    
}

?>