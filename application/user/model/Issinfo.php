<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\user\model;

use think\Model;

class Issinfo extends Model
{   
    //protected $auto = ['patnum','pronum'];
    protected $insert = ['issnum'];  
    //protected $update = ['topic','abstract','addnewdate'];  
   
    //只读字段，这个字段的值一旦写入，就无法更改。
    protected $readonly = ['issnum'];
    
    //设置issnum字段的值为iss+yyyy+0000的形式，即是在当年进行流水编号
    protected function setIssnumAttr()
    {
        
        $idmax = Issinfo::max('id');
        $value = Issinfo::where('id',$idmax)->value('issnum');
        
        $year=substr($value,3,4);
        $num=substr($value,3)+1;
        
        if($year==date('Y')){
            $result ="iss".$num;
        }else{
            $result ="iss".date('Y')."0001";
        }
        
        return ($result);
    }
    
    /**
     * 获取对应patent的内容
     */    
    public function patinfo()
    {
        return $this->hasOne('app\patent\model\Patinfo');
        //return $this->hasOne('Patinfo');
    }    
    
    /**
     * 获取issue的过程记录
     */
    public function issrecords()
    {   
        return $this->hasMany('Issrecord');
    }
    
    //获取issinfo的多态模型,涉及issinfo表中的num_id和num_type两个字段内容
     public function num()
    {
        return $this->morphTo(null, [
            '0' => 'app\issue\model\Issinfo',
            '1' => 'app\project\model\Proinfo',
            '2' => 'app\patent\model\Patinfo',
        ]);
    }

    
}
    



?>