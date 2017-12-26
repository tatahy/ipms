<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\user\model;

use think\Model;

class Patrecord extends Model
{   
    //protected $auto = ['patnum','pronum'];
    protected $insert = ['num'];  
    //protected $update = ['topic','abstract','addnewdate'];  
   
    //只读字段，这个字段的值一旦写入，就无法更改。
    protected $readonly = ['num'];
    
    /**
     * 获取patrecord所属的patent信息
     */
    public function patrecords()
    {   
        return $this->belongsTo('Patinfo');
    }
        
    
}
    



?>