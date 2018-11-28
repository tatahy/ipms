<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\patent\model;

use think\Model;

class Patinfo extends Model
{
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

    //将输入的数组Pronum转换为“,”分隔的字符串
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

    //将字符串Pronum转换为数组输出
    protected function getPronumAttr($value)
    {
        return explode(",", $value);
    }

    //获取器，获取数据表patinfo中type字段值，转换为中文输出
    protected function getTypeAttr($dBStrEn)
    {
        $output = $dBStrEn;
         //引用应用公共文件（app/common.php）中定义的数组常量conPatTypeArr     
        if (array_key_exists($dBStrEn,conPatTypeArr)) {
            $output = conPatTypeArr[$dBStrEn];
        }
        return $output;
    }

    //修改器，修改存入数据表patinfo中type字段值，转换为类型编码
    protected function setTypeAttr($strChi)
    {
        $output = $strChi;
         //引用应用公共文件（app/common.php）中定义的数组常量conPatTypeArr
        foreach(conPatTypeArr as $key => $val){
            if($strChi==$val){
                $output=$key;
            }
        }
        return $output;
    }

    //获取器，获取数据表patinfo中status字段值，转换为中文输出，待考虑是否采用？？
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

    //修改器，修改存入数据表patinfo中status字段值，转换为类型编码。
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