<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\asset\model;

use think\Model;

class Assinfo extends Model
{
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

    
    //获取器，获取数据表assinfo中status字段值，转换为中文输出
    protected function getStatusNowAttr($dBStrEn)
    {
        $output = $dBStrEn;
        //引用本模块公共文件（asset/common.php）中定义的数组常量assStatusArr
        if (array_key_exists($dBStrEn, assStatusArr)) {
            $output = assStatusArr[$dBStrEn];
        }
        return $output;
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