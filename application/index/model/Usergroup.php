<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\index\model;

use think\Model;

class Usergroup extends Model
{   
    //protected $auto = ['patnum','pronum'];
    //protected $insert = ['dev_id'];  
    //protected $update = ['topic','abstract','addnewdate'];
    
    
    //只读字段，这个字段的值一旦写入，就无法更改。
    //protected $readonly = ['rolety_id'];
    
     
    /**
     * 定义一对一关系，获取authiss
     */
    public function authissHasOne($value)
    {   
        //在定义关联的时候添加额外条件:查询数据集不包含'id','usergroup_id','usergroup_name'3个字段的值。
        return $this->hasOne('Authiss')->field(['id','usergroup_id','usergroup_name'],true)->where('id',$value)->find();
    } 
    
    /**
     * 定义一对一关系，获取authatt
     */
    public function authattHasOne($value)
    {   
        //在定义关联的时候添加额外条件:查询数据集不包含'id','usergroup_id','usergroup_name'3个字段的值。
        return $this->hasOne('Authatt')->field(['id','usergroup_id','usergroup_name'],true)->where('id',$value)->find();
    }
    
    /**
     * 定义一对一关系，获取authpat
     */
    public function authpatHasOne($value)
    {   
        //在定义关联的时候添加额外条件:查询数据集不包含'id','usergroup_id','usergroup_name'3个字段的值。
        return $this->hasOne('Authpat')->field(['id','usergroup_id','usergroup_name'],true)->where('id',$value)->find();
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

