<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\user\model;

use think\Model;

class User extends Model
{   
    //protected $auto = ['patnum','pronum'];
    //protected $insert = ['dev_id'];  
    //protected $update = ['topic','abstract','addnewdate'];   
   
    //只读字段，这个字段的值一旦写入，就无法更改。
    protected $readonly = ['roleid'];
    
    // 关联关系
    public function rolety()
    {
        return $this->hasOne('Rolety','roleid','rolenum');
    }
    
}
?>

