<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\dashboard\model;

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
    protected $autoWriteTimestamp = 'true';
    // 时间字段输出格式
    protected $dateFormat = 'Y/m/d H:i:s';

    
    //获取器，获取数据表assinfo中status字段值，转换为中文输出，待考虑是否采用？？
    protected function getStatusAttr($key)
    {
        $value = $key;
        $status = array(
            '_PATS1' => '填报',
            '_PATS2' => '内审',
            '_PATS3' => '内审审核',
            '_PATS4' => '内审修改',
            '_PATS5' => '内审否决',
            '_PATS6' => '拟申报(内审批准)',
            '_PATS7' => '申报中',
            '_PATS8' => '申报修改',
            '_PATS9' => '授权',
            '_PATS10' => '驳回',
            '_PATS11' => '续费中',
            '_PATS12' => '放弃续费',
            '_PATS13' => '续费授权',
            '_PATS14' => '续费驳回',
            '_PATS15' => '超期无效',
            );
        if (array_key_exists($key, $status)) {
            $value = $status[$key];
        }
        return $value;
    }

    //修改器，修改存入数据表assinfo中status字段值，转换为类型编码。
    protected function setStatusAttr($key)
    {
        $value = $key;
        $status = array(
            '填报' => '_PATS1',
            '内审' => '_PATS2',
            '内审审核' => '_PATS3',
            '内审修改' => '_PATS4',
            '内审否决' => '_PATS5',
            '拟申报(内审批准)' => '_PATS6',
            '申报中' => '_PATS7',
            '申报修改' => '_PATS8',
            '授权' => '_PATS9',
            '驳回' => '_PATS10',
            '续费中' => '_PATS11',
            '放弃续费' => '_PATS12',
            '续费授权' => '_PATS13',
            '续费驳回' => '_PATS14',
            '超期无效' => '_PATS15',
            );
        if (array_key_exists($key, $status)) {
            $value = $status[$key];
        }
        return $value;
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