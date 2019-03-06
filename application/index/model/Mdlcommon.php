<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\index\model;

use think\Model;
use app\admin\model\Dept as DeptModel;

class Mdlcommon extends Model
{
   //仅用于子类的属性
    //本类的静态方法中用于访问非静态方法时实例化本类对象
    static protected $obj=null;
    protected $entPeriod=null;
    protected $entType=null;
    protected $entity=null;
        
    //const ENTPERIOD=conTheEntArr['period'];
//    const ENTTYPE=conTheEntArr['type'];
//    const ENTITY='thesis';
    
    #得到在period里的query对象
    static public function getPeriodSql($period='') {
      self::$obj=new self();
      $psArr=self::$obj->entPeriod;
      $pArr=array_keys($psArr);
      
      #保证$period的值是规定的范围内
      $period=in_array($period,$pArr)?$period:'total';
      
      #模型查询中的条件
      $field=($period==$pArr[0])?'id':'status';
      $where[$field]=[$psArr[$period]['queryExp'],$psArr[$period]['status']];
      
      #模型查询中的条件
     // if($period=='total'){
//        $where['id']=['>',0];
//      }else{
//        $where['status']=['in',$psArr[$period]['status']];
//      }
      
      $query=self::$obj->where($where);
      self::$obj=null;
      return $query;
    }
    
    #得到在period里的所有pat
    static public function getPeriodSet($period='') {
      self::$obj=new self();
      $patSet=self::$obj->getPeriodSql($period)->select();
      self::$obj=null;
      return $patSet;
    }
    #得到在period里的所有pat的num
    static public function getPeriodNum($period='') {
      self::$obj=new self();
      $num='';
      $numArr=[];
      $pArr=array_keys(self::$obj->entPeriod);
      //$pArr=self::ENTPERIOD;
      
      if(!empty($period)){
        $num=self::$obj->getPeriodSql($period)->count();
        self::$obj=null;
        return $num;
      }
      
      foreach($pArr as $key=>$val){
        $numArr[$val]=self::$obj->getPeriodSql($val)->count();
      } 
      self::$obj=null;
      return $numArr;
    }
    #得到在period的指定field字段的groupby内容
    static public function getFieldGroupByArr($field,$arr=[],$period='',$whereArr=[]) {
      self::$obj=new self();
      $valArr=[]; 
      $keyArr=[];     
      $tempArr=[];#中间数组
      $tArr=[];   #键值转换数组
    #设定返回数组的默认结构
      $arr=array_merge(['num'=>0,'val'=>[''],'txt'=>['']],$arr);
    
    #组装$tArr
      if($field=='type') $tArr=self::$entType;
      if($field=='status_now' || $field=='status') $tArr=_commonStatustEn2ChiArr(self::$obj->entity);      
      if($field=='dept_now' || $field=='dept') {
        #得到dept的键值转换数组$tArr。abbr为键，name为值的关联数组
        $deptSet=DeptModel::all();
        #转换为数据集
        $deptSet=is_array($deptSet)?collection($deptSet):$deptSet;
        $tArr=array_combine($deptSet->column('abbr'),$deptSet->column('name'));
      }
   
      #组装$tempArr
      
      if(count($whereArr)){
        $aSet=self::$obj->getPeriodSql($period)->where($whereArr)->select();
      }else{
        $aSet=self::$obj->getPeriodSet($period);
      }
      self::$obj=null;
      #转换为数据集
      $aSet=is_array($aSet)?collection($aSet):$aSet;
    #得到$field字段值。若定义了$field字段的修改器，此处为经过修改器后的输出值（去掉重复值）
      $valArr=array_unique($aSet->column($field));
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