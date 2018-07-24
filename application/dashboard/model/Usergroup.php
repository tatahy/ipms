<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\dashboard\model;

use think\Model;

class Usergroup extends Model
{   
    //protected $auto = ['patnum','pronum'];
    //protected $insert = ['dev_id'];  
    //protected $update = ['topic','abstract','addnewdate'];
    
     protected $type = [
        'authority'  =>  'json',
    ];  
   
    //只读字段，这个字段的值一旦写入，就无法更改。
    //protected $readonly = ['rolety_id'];
    
  
   /**
     * 获取用户所属的用户组信息，与User通过虚拟中间表‘group’建立多对多关联,待完成HY,20180723
     */
    public function user()
    {
    	return $this->belongsToMany('User', 'group');
    }
    
  
    
}
?>

