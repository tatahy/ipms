<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\dashboard\model;

use think\Model;
use app\dashboard\model\Issinfo;

class Theinfo extends Model
{
    //protected $auto = ['patnum','pronum'];
    protected $insert = ['thenum', 'issinfo_id'];
    //protected $update = [];

    //只读字段，这个字段的值一旦写入，就无法更改。
    protected $readonly = ['thenum', 'issinfo_id'];

    // 开启时间字段自动写入
    protected $autoWriteTimestamp = true;

    //修改器，设置patnum字段的值为pat+yyyy+0000的形式，即是在当年进行流水编号
    protected function setThenumAttr()
    {

        $idmax = $this::max('id');
        $value = $this::where('id', $idmax)->value('patnum');

        $year = substr($value, 3, 4);
        $num = substr($value, 3) + 1;

        if ($year == date('Y')) {
            $result = "pat".$num;
        } else {
            $result = "pat".date('Y')."0001";
        }

        return ($result);
    }

    //修改器，将输入的数组Pronum转换为“,”分隔的字符串
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

    //获取器，将字符串Pronum转换为数组输出
    protected function getPronumAttr($value)
    {
        return explode(",", $value);
    }

    //获取器，获取数据表theinfo中type字段值，转换为中文输出
    protected function getTypeAttr($dBStrEn)
    {
        $output = $dBStrEn;
        //引用应用公共文件（app/common.php）中定义的数组常量conTheTypeArr
        if (array_key_exists($dBStrEn, conTheTypeArr)) {
            $output = conTheTypeArr[$dBStrEn];
        }
        return $output;
    }

    //修改器，修改存入数据表theinfo中type字段值，转换为类型编码。
    protected function setTypeAttr($strChi)
    {
        $output = $strChi;
        //引用应用公共文件（app/common.php）中定义的数组常量conTheTypeArr
        foreach(conTheTypeArr as $key => $val){
            if($strChi==$val){
                $output=$key;
            }
        }
        return $output;
    }

    //获取器，获取数据表theinfo中status字段值，转换为中文输出，待考虑是否采用？？
    protected function getStatusAttr($dBStrEn)
    {
        $output = $dBStrEn;
        //引用应用公共文件（app/common.php）中定义的数组常量conTheStatusArr
        if (array_key_exists($dBStrEn, conTheStatusArr)) {
            $output = conTheStatusArr[$dBStrEn];
        }
        return $output;
    }
    
    //修改器，修改存入数据表theinfo中status字段值，转换为类型编码。
    protected function setStatusAttr($strChi)
    {
        $output = $strChi;
        //引用应用公共文件（app/common.php）中定义的数组常量conTheStatusArr
        foreach(conTheStatusArr as $key => $val){
            if($strChi==$val){
                $output=$key;
            }
        }
        return $output;
    }

    /**
     * 获取即将到期需授权续费的patent信息
     */
    public function patRenew()
    {
        $today = date('Y-m-d');
        $deadline = date('Y-m-d', strtotime("+6 month"));
        $map['status'] = ['in', ['授权', '续费授权']];
        return $this->where($map)->where('renewdeadlinedate', 'between time', [$today, $deadline])->order('renewdeadlinedate asc')->
            paginate();
        // ->select();
    }
    
    /**
     * 获取即将到期需授权续费的patent结构集
     */
    public function patRenewList()
    {
        $today = date('Y-m-d');
        $deadline = date('Y-m-d', strtotime("+6 month"));
        $map['status'] = ['in', ['授权', '续费授权']];
        return $this->where($map)->where('renewdeadlinedate', 'between time', [$today, $deadline])->order('renewdeadlinedate asc')->
            paginate();
        // ->select();
    }

    /**
     * 获取内容所属的issue信息
     */
    public function issinfo()
    {
        //return $this->belongsTo('app\issue\model\Issinfo');
        return $this->belongsTo('Issinfo');
    }

    /**
     * 获取专利的所有issues
     */
    public function issues()
    {
        //return $this->morphMany('Issinfo', 'issmap', ['_ISST_PAT1', '_ISST_PAT2']);
        return $this->morphMany('Issinfo', 'issmap',['_ISST_PAT1', '_ISST_PAT2']);
        
    }

    ///**
    //     * 获取专利的所有attachment
    //     */
    //    public function attachments()
    //    {
    //        return $this->morphMany('app\attachment\model\Attinfo', 'num', 2);
    //    }

    /**
     * 获取patent的附件
     */
    public function attachments()
    {
        return $this->morphMany('Attinfo', 'attmap', '_ATTO2');
    }

    /**
     * 获取patent的过程记录
     */
    public function patrecords()
    {
        return $this->hasMany('Patrecord');
    }

    /**
     * 新增一个patent。
     * @param  array $data 新增patent的各项信息
     * @return integer|bool  新增成功返回主键，新增失败返回false
     * 要求：传入的数组下标名与模型属性名（数据表字段名）一模一样。
     */
    public function patCreate($data = [])
    {
        $result = $this->allowField(true)->save($data);
        if ($result) {
            return $this->getData('id');
        } else {
            return false;
        }
    }

    /**
     * 更新patent。
     * @param  array $data 更新patent的各项信息
     * @return integer|bool  更新成功返回主键，未更新返回false
     * 要求：传入的数组下标名与模型属性名（数据表字段名）一模一样。
     */
    public function patUpdate($data = [], $id)
    {
        $result = $this::get($id)->allowField(true)->save($data);
        //$pat=$this::get($id);
        //        $result = $pat->allowField(true)->data($data, true)->save();
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 删除patent。
     * @param  integer $patId 删除patent的id
     * @return integer|bool  删除成功返回主键，未成功返回false
     * 考虑应用TP5的软删除进行改进，？？？2018/3/23
     */
    public function patDelete($patId)
    {
        //delete()方法返回的是受影响记录数
        $result = $this->where('id', $patId)->delete();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }


}

?>