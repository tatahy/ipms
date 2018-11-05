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

    const ASSTYPE=conAssTypeArr;
    static private $stUserName='';
    static private $stUserDept='';
    static private $stAuth=[];
    static private $stNumArr=[];
       
    static function setAccessUser($userName,$userDept,$auth)
    {
      //使用静态变量的好处就是一次赋初值，所有实例化的对象都可以用到。
      self::$stUserName=$userName;
      self::$stUserDept=$userDept;
      self::$stAuth=$auth;
    }
    
    static function getAccessUser()
    {
      return ['userName'=>self::$stUserName,'dept'=>self::$stUserDept,'auth'=>self::$stAuth,'assType'=>self::ASSTYPE];
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
    //待完善
    protected function checkAssTypeStr($assType='')
    {
      $assType=!empty($assType)?$assType:'_USUAL';
      $res='';
      if(array_key_exists($assType,self::ASSTYPE)){
        $res=$assType;
      }else{
        $res='wrong $assType value! It should be in array'.json_encode(array_keys(self::ASSTYPE));
      }
      return $res;
    }
    
    //获得各类asset数量
    public function getAssTypeNumArr($assType='')
    {
      $numArr=[];
      foreach(self::ASSTYPE as $key=>$val){
        self::$stNumArr[$key]=$this->assTypeNum($key);
      }
      if($assType){
        $numArr=self::$stNumArr[$assType];
      }else{
        $numArr=self::$stNumArr;
      }
      return $numArr;
    }
        
    //asset数量
    public function assTypeNum($assType='')
    {
      $result=$this->checkAssTypeStr();
      $res='';
      if($result){
        $res=$this->assTypeQuery($assType)->count();
      }else{
        $res=$result;
      }
      //return $res;
      return $this->assTypeQuery($assType)->count();
    }
    
    //asset查询对象
    public function assTypeQuery($assType='')
    {
      $auth=self::$stAuth;
      $assType=!empty($assType)?$assType:'_USUAL';
      $authNum=0;
      
      foreach($auth as $val){
        $authNum+=$val;
      }
      
      if($auth['read']==1 && $authNum<=1){
        //登录用户的asset权限有且仅有read，仅能查阅自己名下的asset，
        $query=$this->scope('assType',$assType)->where('keeper_now',self::$stUserName);
      }else if($auth['read']==1 && $auth['edit']==1 && $authNum<=2){
        //登录用户的asset权限有且仅有read，仅能查阅自己部门的asset，
        $query=$this->scope('assType',$assType)->where('dept_now',self::$stUserDept);
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