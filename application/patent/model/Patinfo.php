<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\patent\model;

use think\Model;
use app\admin\model\Dept as DeptModel;

class Patinfo extends Model
{
    #引用app\common中定义的常量
    const PATSTATUS=conPatStatusArr;
    const PATTYPE=conPatTypeArr;
    #patent的period与status的对应关系，本模块common.php中定义
    const PATPERIODSTATUS=conPatPeriodVsStatus;
    //本类的静态方法中用于访问非静态方法时实例化本类对象
    static private $obj=null;
    //protected $auto = ['patnum','pronum'];
    protected $insert = ['patnum', 'issinfo_id'];
    //protected $update = [];

    //只读字段，这个字段的值一旦写入，就无法更改。
    protected $readonly = ['patnum', 'issinfo_id'];

    //设置patnum字段的值为pat+yyyy+0000的形式，即是在当年进行流水编号
    protected function setPatnumAttr()
    {

        $idmax = Patinfo::max('id');
        $value = Patinfo::where('id', $idmax)->value('patnum');

        $year = substr($value, 3, 4);
        $num = substr($value, 3) + 1;

        if ($year == date('Y')) {
            $result = "pat".$num;
        } else {
            $result = "pat".date('Y')."0001";
        }

        return ($result);
    }

    #将输入的数组Pronum转换为“,”分隔的字符串
    protected function setPronumAttr($value)
    {
        $result = $value;
        if ($result[0] == '0') {
            $result[0] = '无';
            return implode(",", $result);
        } else {
            return implode(",", $result);
        }

    }

    #将字符串Pronum转换为数组输出
    protected function getPronumAttr($value)
    {
        return explode(",", $value);
    }

    #获取器，获取数据表patinfo中type字段值，转换为中文输出
    protected function getTypeAttr($dBStrEn)
    {
        $output = $dBStrEn;    
        if (array_key_exists($dBStrEn,self::PATTYPE)) {
            $output = self::PATTYPE[$dBStrEn];
        }
        return $output;
    }

    #修改器，修改存入数据表patinfo中type字段值，转换为类型编码
    protected function setTypeAttr($strChi)
    {
        $output = $strChi;
        foreach(self::PATTYPE as $key => $val){
            if($strChi==$val){
                $output=$key;
            }
        }
        return $output;
    }

    #获取器，获取数据表patinfo中status字段值，转换为中文输出
    protected function getStatusAttr($dBStrEn)
    {
        $output = $dBStrEn;
        if (array_key_exists($dBStrEn, self::PATSTATUS)) {
            $output = self::PATSTATUS[$dBStrEn];
        }
        return $output;
    }
    
    #修改器，修改存入数据表Patinfo中status字段值，转换为类型编码。
    protected function setStatusAttr($strChi)
    {
        $output = $strChi;
        foreach(self::PATSTATUS as $key => $val){
            if($strChi==$val){
                $output=$key;
            }
        }
        return $output;
    }
    #得到在period里的所有pat
    static public function getPeriodSql($period='')
    {
      $pArr=array_keys(self::PATPERIODSTATUS);
      $psArr=self::PATPERIODSTATUS;
      $period=in_array($period,$pArr)?$period:'total';
      #模型查询中的条件
      if($period=='total'){
        $where['id']=['>',0];
      }else{
        $where['status']=['in',$psArr[$period]['status']];
      }
      self::$obj=new self();
      $query=self::$obj->where($where);
      self::$obj=null;
      return $query;
    }
    
    #得到在period里的所有pat
    static public function getPeriodSet($period='')
    {
      self::$obj=new self();
      $patSet=self::$obj->getPeriodSql($period)->select();
      self::$obj=null;
      return $patSet;
    }
    #得到在period里的所有pat的num
    static public function getPeriodNum($period='')
    {
      $num='';
      $numArr=[];
      $pArr=array_keys(self::PATPERIODSTATUS);
      
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
    #得到在period的select组件内容
    static public function getPeriodSelData($name='',$arr=[],$period='')
    {
      $resArr=[]; #返回数组
      $tArr=[];   #键值转换数组
      $tempArr=[];#中间数组
      
      #组装$tArr
      if($name=='status') $tArr=self::PATSTATUS;
      if($name=='type') $tArr=self::PATTYPE;
      #得到dept的键值转换数组$tArr。abbr为键，name为值的关联数组
      $deptSet=DeptModel::all();
      #转换为数据集
      $deptSet=is_array($deptSet)?collection($deptSet):$deptSet;
      if($name=='dept') $tArr=array_combine($deptSet->column('abbr'),$deptSet->column('name'));
      
      #组装$tempArr
      self::$obj=new self();
      $patSet=self::$obj->getPeriodSet($period);
      self::$obj=null;
      #转换为数据集
      $patSet=is_array($patSet)?collection($patSet):$patSet;
      #得到中间数组$tempArr，$name值对应的索引数组（去掉重复值）
      $tempArr=array_unique($patSet->column($name));
      #重新排序让数组下标连续
      sort($tempArr);
      
      #组装$resArr，按照$arr的结构
      foreach($arr as $key => $val){
        foreach($tempArr as $k => $v){
          if($key=='txt'){
            $val=($name=='dept')?$v.'，简称：'.array_search($v,$tArr):$v;
          }
          if($key=='val'){
            $val=($name=='dept')?$v:array_search($v,$tArr);
          }
          $resArr[$k][$key]=$val;
        }
      }
      return $resArr;
    }

    /**
     * 获取内容所属的issue信息
     */
    public function issinfo()
    {
        return $this->belongsTo('app\issue\model\Issinfo');
    }

    /**
     * 获取专利的所有issues
     */
    public function issues()
    {
        return $this->morphMany('app\issue\model\Issinfo', 'num', 2);
    }

    /**
     * 获取专利的所有attachment
     */
    public function attachments()
    {
        return $this->morphMany('app\attachment\model\Attinfo', 'num', 2);
    }

    /**
     * 获取patent的过程记录
     */
    public function patrecords()
    {
        return $this->hasMany('Patrecord')->order('acttime desc');
    }


}

?>