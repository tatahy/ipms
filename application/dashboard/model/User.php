<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\dashboard\model;

use think\Model;
use app\dashboard\model\Usergroup as UsergroupModel;

class User extends Model
{   
    //protected $auto = ['patnum','pronum'];
    //protected $insert = ['dev_id'];  
    //protected $update = ['topic','abstract','addnewdate'];
    
     protected $type = [
        'authority'  =>  'json',
    ];  
   
    //只读字段，这个字段的值一旦写入，就无法更改。
    //protected $readonly = ['rolety_id'];
    
    //获取器，获取数据表mobile字段值，转换为掩码输出(类似：139xxxx1111)
    protected function getMobileAttr($val)
    {
        //将电话号码中间4位转为x
        if(strlen($val)==11){
            $val=substr_replace($val, 'xxxx', 3,4);
        }
        return $val;
    }
    
    /**
     * 获取用户所属的角色信息
     */
  	public function role()
      {
      	return $this->belongsTo('Rolety');
      }
      
   /**
     * 获取用户所属的用户组信息，与Usergroup通过虚拟中间表‘group’建立多对多关联,待完成HY,20180723
     */
    public function userGroup()
    {
    	return $this->belongsToMany('Usergroup', 'group');
    }
    
       
   /**
     * 获取用户所属的用户组信息
     * @param  int $userId 用户Id值
     * @return Array $result 返回值，用户所属用户组信息 
     */
    public function userGroupArr($userId='')
    {
        $userGroupIdArr= explode(",", $this->get($userId)['usergroup_id']);//$usergroup_id=array(8,9,10)
        
        for($i=0;$i<count($userGroupIdArr);$i++){
            $userGroup[$i]=UsergroupModel::get($userGroupIdArr[$i]);
        }
        
        return $userGroup;
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

