<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\index\model;

use app\index\model\Entityinfo;
use app\admin\model\Dept as DeptModel;

//启用软删除
use traits\model\SoftDelete;

class Assinfo extends Entityinfo {
    //启用软删除
    use SoftDelete;
    //protected $auto = ['assnum','pronum'];
    //protected $insert = ['assinfo_id'];
    //protected $update = [];

    //只读字段，这个字段的值一旦写入，就无法更改。
    //protected $readonly = ['assnum', 'assinfo_id'];
    
    // 开启时间字段自动写入 并设置字段类型为datetime
	//protected $autoWriteTimestamp = 'datetime';
    //protected $autoWriteTimestamp = 'timestamp';
    protected $autoWriteTimestamp = true;
    // 时间字段输出格式
    protected $dateFormat = 'Y/m/d H:i:s';

    //引用app\common中定义的常量：conAssEntArr
    const ASSPERIOD=conAssEntArr['period'];
   // const ASSSTATUS=conAssEntArr['status'];
    const ENTITY='asset';
        
    //本类的静态方法中用于访问非静态方法时实例化本类对象
    static private $obj=null;
    //本类的5个私有静态变量
    static private $userDept='';
    static private $auth=[];
    static private $periodArr=[];
    static private $numArr=[];
    //本类的私有变量
    private $period='';
    private $errStr='not initiate Model Assinfo';
    
    //继承自父类的变量
    //引用app\common中定义的常量：conAssEntArr
    protected $entPeriod=conAssEntArr['period'];
    protected $entType=[];
    protected $entity='asset';
   
     public function getEntity() {
      return $this->entity;
    }
    
   //获取器，获取数据表assinfo中status_now字段值，转换为中文输出
    protected function getStatusNowAttr($dBStrEn)
    {
        //中英文对照数组
        $sArr=$this->statusArr;
        $output =array_key_exists($dBStrEn, $sArr)?$sArr[$dBStrEn]:$dBStrEn;
        
        return $output;
    }

    //修改器，修改存入数据表assinfo中status_now字段值，转换为类型编码。
    protected function setStatusNowAttr($strChi)
    {
        //中英文对照数组
        $sArr=array_flip($this->statusArr);
        $output =array_key_exists($strChi, $sArr)?$sArr[$strChi]:$strChi;
       
        return $output;
    }
    //全局查询范围，框架在查询时会自动调用
    protected static function base($query)
    {
        //
        $query->whereNull('delete_time')->where('id','>',0);
        //$query->where('delete_time',0)->where('id','>',0);
    }
    
    //查询asset的类别
    protected function scopePeriod($query,$period)
    {
      if($period=='usual'){
        $query->where('id','>',0);
      }else{
        $query->where('status_now','like','%'.$period.'%');
      }
    }
    //asset查询对象
    public function assPeriodQuery($period='',$whereArr=[]) {
      $this->period=$period;
      $auth=self::$auth;
      $dept=self::$userDept;
      $userName=$this->userName;
      $authNum=0;
      if(!$this->checkAssTypeStr()){
        return $this->errStr;
      }
      
      if(count($auth)){
        foreach($auth as $val){
          $authNum+=$val;
        }
      }else{
        return '无授权，请重新登录。';
      }
      
      //前置查询范围
      $scopeQ=$this->scope('period',$period);
      
      if($auth['read']==1 && $authNum<=1){
        //登录用户的asset权限有且仅有read，仅能查阅自己名下的asset，
        //$query=$scopeQ->where('keeper_now',$userName)->where($whereArr);
        $query=$this->getPeriodSql($period)->where('keeper_now',$userName)->where($whereArr);
      }else if($auth['read']==1 && 1==$auth['edit'] && $authNum<=2){
        //登录用户的asset权限有且仅有read和edit，仅能查阅自己部门和自己名下的asset，
        $scopeArr=($period=='usual')?array('id'=>['>',0]):array('status_now'=>['like','%'.$period.'%']);
        //不能使用已定义的前置查询范围，因为查询的条件是需要在一个前置查询范围内分为2个不同的查询。使用闭包实现
        $query=$this->where(function($query) use($dept,$scopeArr,$whereArr){
                      $query->where($scopeArr)->where('dept_now',$dept)->where($whereArr);
                    })
                    ->whereOr(function($query) use($dept,$userName,$scopeArr,$whereArr){
                      $query->where($scopeArr)->where('keeper_now',$userName)->where('dept_now','<>',$dept)->where($whereArr);
                    });
        
       // $query=$scopeQ->where('dept_now',$dept);
        //$query=$this->where(function($query) use(&$scopeQu,$dept,$scopeArr){
//                      $query->$scopeQu->where('dept_now',$dept)->where($whereArr);
//                    })
//                    ->whereOr(function($query) use(&$scopeQu,$dept,$userName,$scopeArr){
//                      $query->$scopeQu->where('keeper_now',$userName)->where('dept_now','<>',$dept)->where($whereArr);
//                    });
      }
      else{
        //$query=$scopeQ->where($whereArr);
        $query=$this->getPeriodSql($period)->where($whereArr);
      }
      return $query;
    }
       
    /**
     * 获取assent的过程记录
     */
    public function assrecords()
    {
        return $this->hasMany('Patrecord')->order('acttime desc');
    }

}

?>