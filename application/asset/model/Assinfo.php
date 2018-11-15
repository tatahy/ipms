<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\asset\model;

use think\Model;
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

    //引用app\common中定义的常量：conAssTypeArr
    const ASSTYPE=conAssTypeArr;
    //本类的静态方法中用于访问非静态方法时实例化本类对象
    static private $obj=null;
    //本类的5个私有静态变量
    static private $userName='';
    static private $userDept='';
    static private $auth=[];
    static private $aTypeArr=[];
    static private $numArr=[];
    
    
    private $aType='';
    private $errStr='not initiate Model Assinfo';
    //本类的5个私有静态变量赋初值  
    static function initModel($userName,$userDept,$auth)
    {
      //使用静态变量的好处就是一次赋初值，本类中和所有实例化的对象都可以用到。
      self::$userName=$userName;
      self::$userDept=$userDept;
      self::$auth=$auth;
      self::$aTypeArr=array_keys(self::ASSTYPE);
      
      self::$obj=new self();
      foreach(self::$aTypeArr as $val){
        self::$numArr[$val]=self::$obj->assTypeQuery($val)->count();
      }     
      self::$obj=null;
    }
    
    static function getAccessUser()
    {
      return ['userName'=>self::$userName,'dept'=>self::$userDept,
              'auth'=>self::$auth,'numArr'=>self::$numArr,'ATypeArr'=>self::$aTypeArr];
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
    protected function scopeAssType($query,$assType)
    {
      if($assType=='_USUAL'){
        $query->where('id','>',0);
      }else{
        $query->where('status_now','like','%'.$assType.'%');
      }
      
    }
    //检查输入的$assType是否合法
    protected function checkAssTypeStr()
    {    
      $assType=!empty($this->aType)?$this->aType:'_USUAL';
      $res='';
      if(in_array($assType,self::$aTypeArr,true)){
        $this->aType=$assType;
        $res=true;
      }else{
        $this->errStr='Wrong assType value! It should be empty or in array:</br>'.json_encode(self::$aTypeArr);
        $res=false;
      }
      return $res;
    }
    
    //获得各类asset数量的关联数组
    public function getAssTypeNumArr()
    {    
      if(count(self::$numArr)){
        $res=self::$numArr;
      }else{
        $res=$this->errStr;
      }
      
      return $res;
    }
        
    //获得各类asset的数量
    public function assTypeNum($assType='')
    {
      $this->aType=$assType;
      if(!$this->checkAssTypeStr()){
        return $this->errStr;
      }      
      
      if(count(self::$numArr)){
        $res=self::$numArr[$this->aType];
      }else{
        $res=$this->assTypeQuery($this->aType)->count();
      }
      
      return $res;
    }
    
    //asset查询对象
    public function assTypeQuery($assType='')
    {
      $auth=self::$auth;
      $this->aType=$assType;
      $authNum=0;
      
      if(!$this->checkAssTypeStr()){
        return $this->errStr;
      }
      
      foreach($auth as $val){
        $authNum+=$val;
      }
      
      if($auth['read']==1 && $authNum<=1){
        //登录用户的asset权限有且仅有read，仅能查阅自己名下的asset，
        $query=$this->scope('assType',$assType)->where('keeper_now',self::$userName);
      }else if($auth['read']==1 && $auth['edit']==1 && $authNum<=2){
        //登录用户的asset权限有且仅有read和edit，仅能查阅自己部门的asset，
        $query=$this->scope('assType',$assType)->where('dept_now',self::$userDept);
      }
      else{
        $query=$this->scope('assType',$assType);
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