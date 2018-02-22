<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\index\model;

use think\Model;

class Authatt extends Model
{   
    //protected $auto = ['patnum','pronum'];
    //protected $insert = ['dev_id'];  
    //protected $update = ['topic','abstract','addnewdate'];
    
    
    //只读字段，这个字段的值一旦写入，就无法更改。
    //protected $readonly = ['rolety_id'];
    
     
    /**
     * 获取authiss
     */
    public function usergroup()
    {   
        return $this->blongsTo('Usergroup');
    }     
   
   /**
     * 获取用户所属的角色信息，与Rolety通过虚拟中间表‘auth’建立多对多关联
     */
//	public function roles()
//    {
//    	return $this->belongsToMany('Rolety', 'auth');
//    }
    
}
?>

