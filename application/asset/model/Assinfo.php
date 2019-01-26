<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\asset\model;

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
    const ASSSTATUS=conAssEntArr['status'];
        
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
        $output = $dBStrEn;
        //引用本模块公共文件（dashboard/common.php）中定义的数组常量conAssStatusArr
        if (array_key_exists($dBStrEn, conAssStatusArr)) {
            $output = conAssStatusArr[$dBStrEn];
        }
        return $output;
    }

    //修改器，修改存入数据表assinfo中status_now字段值，转换为类型编码。
    protected function setStatusNowAttr($strChi)
    {
        $output = $strChi;
        //引用本模块公共文件（dashboard/common.php）中定义的数组常量conAssStatusArr
        foreach(conAssStatusArr as $key => $val){
            if($strChi==$val){
                $output=$key;
            }
        }
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
      $pArr=array_keys(self::ASSPERIOD);
      #保证$period的值是规定的范围内
      $period=in_array($period,$pArr)?$period:$pArr[0];
      #模型查询中的条件
      $where=[];
      foreach(self::ASSPERIOD as $key=>$val){
        if($key == $period){
          $where['status_now']=empty($val['statusQuery'])?$val['status']:[$val['statusQuery'],$val['status']];
          break;
        } 
      }
      self::$obj=new self();
      $query=self::$obj->where($where);
      self::$obj=null;
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
    static public function getFieldGroupByArr($field='',$arr=[],$period='') {
      $resArr=[]; #返回数组
      $tArr=[];   #键值转换数组
      $tempArr=[];#中间数组
      #设定arr的默认结构
      $arr=count($arr)?$arr:['txt'=>'','val'=>''];
      #组装$tArr
      if($field=='status_now') $tArr=self::ASSSTATUS;
      #得到dept的键值转换数组$tArr。abbr为键，name为值的关联数组
      $deptSet=DeptModel::all();
      #转换为数据集
      $deptSet=is_array($deptSet)?collection($deptSet):$deptSet;
      if($field=='dept_now') $tArr=array_combine($deptSet->column('abbr'),$deptSet->column('name'));
      
      #组装$tempArr
      self::$obj=new self();
      $patSet=self::$obj->getPeriodSet($period);
      self::$obj=null;
      #转换为数据集
      $patSet=is_array($patSet)?collection($patSet):$patSet;
      #得到中间数组$tempArr，$field值对应的索引数组（去掉重复值）
      $tempArr=array_unique($patSet->column($field));
      #重新排序让数组下标连续
      sort($tempArr);
      
      #组装$resArr，按照$arr的结构
      foreach($arr as $key => $val){
        foreach($tempArr as $k => $v){
          if($key=='txt'){
            $val=($field=='dept')?$v.'，简称：'.array_search($v,$tArr):$v;
          }
          if($key=='val'){
            $val=($field=='dept')?$v:array_search($v,$tArr);
          }
          $resArr[$k][$key]=$val;
        }
      }
      return $resArr;
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