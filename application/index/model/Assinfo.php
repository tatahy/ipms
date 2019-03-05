<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\index\model;

use think\Model;
use app\admin\model\Dept as DeptModel;

//启用软删除
use traits\model\SoftDelete;

class Assinfo extends Model
{
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
    const ENTTITY='asset';
        
    //本类的静态方法中用于访问非静态方法时实例化本类对象
    static private $obj=null;
    //本类的5个私有静态变量
    static private $userName='';
    static private $userDept='';
    static private $auth=[];
    static private $periodArr=[];
    static private $numArr=[];
    //本类的私有变量
    private $period='';
    private $errStr='not initiate Model Assinfo';
    
    #初始化模型的访问
    static public function initModel($username, $dept, $auth) {
      
      return self::$obj;
    }
    
   //获取器，获取数据表assinfo中status_now字段值，转换为中文输出
    protected function getStatusNowAttr($dBStrEn)
    {
        //引用公共文件（common.php）中定义的函数_commonStatustEn2ChiArr($ent)
        $sArr=_commonStatustEn2ChiArr(self::ENTTITY);
        
        $output =array_key_exists($dBStrEn, $sArr)?$sArr[$dBStrEn]:$dBStrEn;
        
        return $output;
    }

    //修改器，修改存入数据表assinfo中status_now字段值，转换为类型编码。
    protected function setStatusNowAttr($strChi)
    {
        //中英文对照数组
        $sArr=_commonStatustEn2ChiArr(self::ENTTITY);
        $k=array_search($strChi, $tArr);
        
        $output = $k?$tArr[$k]:$strChi;
       
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
      $userName=self::$userName;
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
       
    #得到在period里的query对象
    static public function getPeriodSql($period='') {
      $pArr=self::ASSPERIOD;
      #保证$period的值是规定的范围内
      $period=in_array($period,array_keys($pArr))?$period:$pArr[0];
      #模型查询中的条件    
      $where['status_now']=[$pArr[$period]['queryExp'],$pArr[$period]['status']];
    
      self::$obj=new self();
      $query=self::$obj->where($where);
      self::$obj=null;
      #返回查询结果集
      return $query;
    }
    
    #得到在period里的所有ass
    static public function getPeriodSet($period='') {
      self::$obj=new self();
      $assSet=self::$obj->getPeriodSql($period)->select();
      self::$obj=null;
      return $assSet;
    }
    #得到在period里的所有pat的num
    static public function getPeriodNum($period='') {
      $num='';
      $numArr=[];
      $pArr=array_keys(self::ASSPERIOD);
      
      if(!empty($period)){
        self::$obj=new self();
        $num=self::$obj->getPeriodSql($period)->count();
        self::$obj=null;
        return $num;
      }
      
      foreach($pArr as $key=>$val){
        self::$obj=new self();
        $numArr[$val]=self::$obj->getPeriodSql($val)->count();
        self::$obj=null;
      } 
      return $numArr;
    }
    
    #得到在period的指定field字段的groupby内容
    static public function getFieldGroupByArr($field,$arr=[],$period='') {
      $valArr=[]; 
      $keyArr=[];      
      $tempArr=[];#中间数组
      #键值转换数组
      $tArr=($field=='status_now')?_commonStatustEn2ChiArr(self::ENTTITY):[];   
    #设定返回数组的默认结构
      $arr=array_merge(['num'=>0,'val'=>[''],'txt'=>['']],$arr);
      
      self::$obj=new self();
      $aSet=self::$obj->getPeriodSet($period);
      self::$obj=null;
      #转换为数据集
      $aSet=is_array($aSet)?collection($aSet):$aSet;
    #得到$field字段值。若定义了$field字段的修改器，此处为经过修改器后的输出值（去掉重复值）
      $valArr=array_unique($aSet->column($field));
      if(!count($valArr)){
        return $arr;
      }
      
    #组装$tempArr
      foreach($valArr as $k => $v){
        $keyArr[$k]=($field=='status_now')?array_search($v,$tArr):$v;
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
    /**
     * 获取assent的过程记录
     */
    public function assrecords()
    {
        return $this->hasMany('Patrecord')->order('acttime desc');
    }


}

?>