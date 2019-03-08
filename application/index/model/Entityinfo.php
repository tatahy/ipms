<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\index\model;

use think\Model;
use think\Db;

use app\admin\model\Dept as DeptModel;

//定义抽象类
abstract class Entityinfo extends Model implements EntityinfoInterface {
   
   //仅用于子类的属性
    protected static $entPeriod=[];
    protected static $entType=[];
    protected static $entity=[];
    //要操作的数据表名
    protected static $tblName='';
   // protected static $obj=null;
    
    protected $userName;
    protected $dept;
    protected $auth;
    
    //需要实现静态方法，用于初始化module
    abstract function initModel($userName, $dept, $auth);
    
    //static function getSelfMdl(){
//      if(is_null(self::$obj)){
//        self::$obj=new self();
//      }
//      return self::$obj;
//    }
    #得到在period里的query对象
    static function getPeriodSql($period='') {
      
      $psArr=self::$entPeriod;
      $pArr=array_keys($psArr);
      #模型查询中的字段
      $field='id';
      #保证$period的值是规定的范围内
      $period=in_array($period,$pArr)?$period:'total';
      
      #模型查询中的字段
      if($period!=$pArr[0]){
        $field=(self::$entity=='asset')?'status_now':'status';
      }
      
      $whereArr[$field]=[$psArr[$period]['queryExp'],$psArr[$period]['status']];
      
      
      $query=Db::table(self::$tblName)->where($whereArr);
      //$query=Model::where($whereArr);#
      
      return $query;
    }
    
    #得到在period里的所有pat
    static function getPeriodSet($period='') {
      return self::getPeriodSql($period)->select();
    }
    #得到在period里的所有pat的num
    static function getPeriodNum($period='') {
      $num='';
      $numArr=[];
      $pArr=array_keys(self::$entPeriod);
      
      if(!empty($period)){
        return self::getPeriodSql($period)->count();
      }
      
      foreach($pArr as $key=>$val){
        $numArr[$val]=self::getPeriodSql($val)->count();
      } 
      return $numArr;
    }
    #得到在period的指定field字段的groupby内容
    static public function getFieldGroupByArr($field,$arr=[],$period='',$whereArr=[]) {
      
      $valArr=[]; 
      $keyArr=[];     
      $tempArr=[];#中间数组
      $tArr=[];   #键值转换数组
    #设定返回数组的默认结构
      $arr=array_merge(['num'=>0,'val'=>[''],'txt'=>['']],$arr);
    
    #组装$tArr
      if($field=='type') $tArr=self::$entType;
      if($field=='status_now' || $field=='status') $tArr=_commonStatustEn2ChiArr(self::$entity);      
      if($field=='dept_now' || $field=='dept') {
        #得到dept的键值转换数组$tArr。abbr为键，name为值的关联数组
        $deptSet=DeptModel::all();
        #转换为数据集
        $deptSet=is_array($deptSet)?collection($deptSet):$deptSet;
        $tArr=array_combine($deptSet->column('abbr'),$deptSet->column('name'));
      }
   
      #组装$tempArr
      if(count($whereArr)){
        $entSet=self::getPeriodSql($period)->where($whereArr)->select();
      }else{
        $entSet=self::getPeriodSet($period);
      }
      #转换为数据集
      $entSet=is_array($entSet)?collection($entSet):$entSet;
    #得到$field字段值。若定义了$field字段的修改器，此处为经过修改器后的输出值（去掉重复值）
      $valArr=array_unique($entSet->column($field));
      if(!count($valArr)){
        return $arr;
      }
      //其他字段的$tArr
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