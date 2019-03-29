<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\index\model;

//use app\index\model\EntinfoFactory;

use app\common\factory\EntinfoFactory;
use app\admin\model\Dept as DeptModel;

//启用软删除
use traits\model\SoftDelete;

//class Theinfo extends Entityinfo {
class Theinfo extends EntinfoFactory {
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
   
    //继承自父类的变量
    //引用app\common中定义的常量：conTheEntArr
    protected $entType=conTheEntArr['type'];
    protected $entPeriod=conTheEntArr['period'];
    protected $entity=conTheEntArr['name'];
    protected $entityAbbr=conTheEntArr['abbr'];
    
    //获取器，获取数据表theinfo中type字段值，转换为中文输出
    protected function getTypeAttr($dBStrEn)
    {
        //中英文对照数组
        $tArr=$this->entType;
        $output =array_key_exists($dBStrEn, $tArr)?$tArr[$dBStrEn]:$dBStrEn;
        
        return $output;
    }

    //修改器，修改存入数据表theinfo中type字段值，转换为类型编码。
    protected function setTypeAttr($strChi)
    {
        //中英文对照数组
        $tArr=array_flip($this->entType);
        $output =array_key_exists($strChi, $tArr)?$tArr[$strChi]:$strChi;
       
        return $output;
    }
    
   //获取器，获取数据表theinfo中status字段值，转换为中文输出
    protected function getStatusAttr($dBStrEn)
    {
        //中英文对照数组
        //$tArr=_commonStatustEn2ChiArr('thesis');
        $tArr= $this->statusArr;
        $output =array_key_exists($dBStrEn, $tArr)?$tArr[$dBStrEn]:$dBStrEn;
        
        return $output;
    }

    //修改器，修改存入数据表theinfo中status字段值，转换为类型编码。
    protected function setStatusAttr($strChi)
    {
        //中英文对照数组
        $tArr= array_flip($this->statusArr);
        $output =array_key_exists($strChi, $tArr)?$tArr[$strChi]:$strChi;
       
        return $output;
    }
    //全局查询范围，框架在查询时会自动调用
    protected static function base($query)
    {
        
        $query->where('id','>',0);
        //启用软删除时用到
        //$query->whereNull('delete_time')->where('id','>',0);
        //$query->where('delete_time',0)->where('id','>',0);
    }

}

?>