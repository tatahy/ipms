<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\dashboard\model;

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
    
    // 开启时间字段自动写入 并设置字段类型
	//protected $autoWriteTimestamp = 'datetime';
    //protected $autoWriteTimestamp = 'timestamp';
    protected $autoWriteTimestamp = 'integer';
    //设置软删除所用的字段为‘delete_soft_time’,不设置的话默认为‘delete_time’
    //protected $deleteTime='delete_soft_time';
    // 时间字段输出格式
    protected $dateFormat = 'Y/m/d H:i:s';

    
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
    
    protected function scopeWithSoftDelete($query)
    {
        $query->where('delete_time','>',0);
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