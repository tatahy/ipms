<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\index\model;

//use app\index\model\EntinfoFactory;

use app\common\factory\EntinfoFactory;
use app\admin\model\Dept as DeptModel;

//class Patinfo extends Entityinfo {
class Patinfo extends EntinfoFactory {
    #引用app\common中定义的常量
    const ENTTYPE=conPatEntArr['type'];
    const PATPERIOD=conPatEntArr['period'];
    const ENTITY=conPatEntArr['name'];
    const ENTITYABBR=conPatEntArr['abbr'];
    //本类的静态方法中用于访问非静态方法时实例化本类对象
    static private $obj=null;
    //protected $auto = ['patnum','pronum'];
    protected $insert = ['patnum', 'issinfo_id'];
    //protected $update = [];

    //只读字段，这个字段的值一旦写入，就无法更改。
    protected $readonly = ['patnum', 'issinfo_id'];
    
        
    #继承自父类的变量 
    //引用app\common中定义的常量：conAssEntArr
    protected $entType=conPatEntArr['type'];
    protected $entPeriod=conPatEntArr['period'];
    protected $entity=conPatEntArr['name'];
    protected $entityAbbr=conPatEntArr['abbr'];

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

    #修改器，将输入的数组Pronum转换为“,”分隔的字符串，再存入数据表
    protected function setPronumAttr($value) {
        $result = $value;
        if ($result[0] == '0') {
            $result[0] = '无';
            return implode(",", $result);
        } else {
            return implode(",", $result);
        }

    }

    #获取器，将字符串Pronum转换为数组输出
   // protected function getPronumAttr($value) {
//        return explode(",", $value);
//    }

    #获取器，获取数据表patinfo中type字段值，转换为中文输出
    protected function getTypeAttr($dBStrEn) {
        //中英文对照数组
        $tArr=$this->entType;
        $output =array_key_exists($dBStrEn,$tArr)?$tArr[$dBStrEn]:$dBStrEn;
        
        return $output;
    }

    #修改器，修改存入数据表patinfo中type字段值，转换为类型编码
    protected function setTypeAttr($strChi) {    
        //中英文对照数组
        $tArr=array_flip($this->entType);
        $output =array_key_exists($strChi,$tArr)?$tArr[$strChi]:$strChi;
       
        return $output;
    }
    #获取器，获取数据表patinfo中status字段值，转换为中文输出
    protected function getStatusAttr($dBStrEn) {
        //中英文对照数组
        $tArr=$this->statusArr;
        $output =array_key_exists($dBStrEn, $tArr)?$tArr[$dBStrEn]:$dBStrEn;
        
        return $output;
    }
    #修改器，修改存入数据表Patinfo中status字段值，转换为类型编码。
    protected function setStatusAttr($strChi) {
        //中英文对照数组
        $tArr=array_flip($this->statusArr);
        $output =array_key_exists($strChi,$tArr)?$tArr[$strChi]:$strChi;
       
        return $output;
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