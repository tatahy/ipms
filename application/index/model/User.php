<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\index\model;

use think\Model;
use app\index\model\Usergroup as UsergroupModel;

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
    
    /**
     * 获取用户所属的角色信息
     */
  	public function role()
      {
      	return $this->belongsTo('Rolety');
      }
    
   /**
     * 获取用户所属的角色信息，与Rolety通过虚拟中间表‘auth’建立多对多关联
     */
//	public function roles()
//    {
//    	return $this->belongsToMany('Rolety', 'auth');
//    }

    /**
     * 刷新登录用户的各个模块（issue，project，patent，thesis，attachment）权限
     */
     public function userAuth($username,$pwd)
    {
        $user=$this->where('username',$username)->where('pwd',$pwd)->select();
        $usergroup_id= explode(",", $user[0]['usergroup_id']);//$usergroup_id=array(1,2,4)
        $i=0;
        foreach($usergroup_id as $key=>$value) {
           $ugSet=UserGroupModel::get($value);
           //$authissSet=AuthissModel::where('usergroup_id',$value)->field(['id','usergroup_id','usergroup_name'],true)->find();
           if($ugSet->enable){
              //得到authiss
              if($ugSet->authiss){
                //$authiss[]=$ugSet->authissHasOne($value); 
                foreach($ugSet->authissHasOne($value)->toArray() as $k=>$v){
                  if($v){
                    $authiss[$k]=$v;
                  }
                }
              }else{
                $authiss[$key]=0;
              }
              
              //得到authatt
              if($ugSet->authatt){
                //$authatt[]=$ugSet->authattHasOne($value); 
                foreach($ugSet->authattHasOne($value)->toArray() as $k=>$v){
                  if($v){
                    $authatt[$k]=$v;
                  }
                }
              }else{
                $authatt[$key]=0; 
              }
              
              //得到authpat表中字段值为1的字段
              //$authpat[]=0;
              if($ugSet->authpat){
                foreach($ugSet->authpatHasOne($value)->toArray() as $k=>$v){
                  if($v){
                    $authpat[$k]=$v;
                  }
                }
                //$authpat[]=$ugSet->authpatHasOne();  
              }else{
                $authpat[$key]=0;
              }
              
              //得到authpro
              if($ugSet->authpro){
                //$authpro[]=$ugSet->authproHasOne($value); 
                foreach($ugSet->authproHasOne($value)->toArray() as $k=>$v){
                  if($v){
                    $authpro[$k]=$v;
                  }
                }
              }else{
                $authpro[$key]=0; 
              }
              
              //得到auththe
              if($ugSet->auththe){
                //$auththe[]=$ugSet->auththeHasOne($value); 
                foreach($ugSet->auththeHasOne($value)->toArray() as $k=>$v){
                  if($v){
                    $auththe[$k]=$v;
                  }
                }
              }else{
                $auththe[$key]=0; 
              }
              
              $userG[$key]=$ugSet;
           }else{
              $userG[$key]=0;
           }
           
           $i=$i+$value;
        }
        $authissEmpty=array("edit"=>0,"audit"=>0,"approve"=>0,"execute"=>0,"maintain"=>0);
        $authpatEmpty=array("edit"=>0,"audit"=>0,"approve"=>0,"execute"=>0,"maintain"=>0);
        $authattEmpty=array("upload"=>0,"download"=>0,"erase"=>0,"move"=>0,"copy"=>0);
        $authproEmpty=array("edit"=>0,"audit"=>0,"approve"=>0,"execute"=>0,"maintain"=>0);
        $auththeEmpty=array("edit"=>0,"audit"=>0,"approve"=>0,"execute"=>0,"maintain"=>0);
        
        //array_filter($arr)去除数组$arr中值为false的键值对后的新数组
        $authority=array("authiss"=>array_merge($authissEmpty,array_filter($authiss)),
                          "authatt"=>array_merge($authattEmpty,array_filter($authatt)),
                          "authpat"=>array_merge($authpatEmpty,array_filter($authpat)),
                          "authpro"=>array_merge($authproEmpty,array_filter($authpro)),
                          "auththe"=>array_merge($auththeEmpty,array_filter($auththe))
                          );
        
        // 使用静态方法，向User表更新信息，赋值有变化就会更新和返回对象，无变化则无更新和对象返回。
        $this::update([
          'authority'  => $authority,
        ], ['username' => $username,'pwd'=>$pwd]);
      return $authority;
    }
    
}
?>
