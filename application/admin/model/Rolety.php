<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\admin\model;

use think\Model;

class Rolety extends Model
{   
   // 关联关系
    public function users()
    {
        return $this->hasMany('User');
    }
    
    /**
     * 获取角色下面的用户信息，与User通过虚拟中间表‘auth’建立多对多关联
     */
	 //public function users()
//    {
//    	return $this->belongsToMany('User', 'auth');
//    }
    
    
}


?>