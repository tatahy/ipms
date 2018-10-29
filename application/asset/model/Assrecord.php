<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\asset\model;

use think\Model;

class Assrecord extends Model
{
    protected $autoWriteTimestamp = 'integer';
    //设置软删除所用的字段为‘delete_soft_time’,不设置的话默认为‘delete_time’
    //protected $deleteTime='delete_soft_time';
    // 时间字段输出格式
    protected $dateFormat = 'Y/m/d H:i:s';

    
    //获取器，获取数据表assrecord中oprt字段值，转换为中文输出
    protected function getOprtAttr($dBStrEn)
    {
        $output = $dBStrEn;
        //引用公共文件（common.php）中定义的数组常量conAssOprtArr
        if (array_key_exists($dBStrEn, conAssOprtArr)) {
            $output = conAssOprtArr[$dBStrEn];
        }
        return $output;
        
        
    }

    //修改器，修改存入数据表assrecord中Act字段值，转换为类型编码。
    protected function setOprtAttr($strChi)
    {
        $output = $strChi;
        //引用公共文件（common.php）中定义的数组常量conAssOprtArr
        foreach(conAssOprtArr as $key => $val){
            if($strChi==$val){
                $output=$key;
            }
        }
        return $output;
    }
   
}

?>