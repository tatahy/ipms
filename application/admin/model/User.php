<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\admin\model;

use think\Model;
use app\admin\model\Usergroup as UsergroupModel;
use app\admin\model\Dept as DeptModel;

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
    //获取器，获取数据表usergroup_id字段值，转换为字符串输出
    protected function getUsergroupIdAttr($dbVal)
    {
        $ugMdl=new UsergroupModel;
        $arr=explode(",", $dbVal);//$usergroup_id=array(8,9,10)
        $str='';
        $preStr='';
        $patchStr='';
        foreach($arr as $k=>$v){
          $preStr=($v/10>=1)?'&nbsp;':'&nbsp;&nbsp;&nbsp;';
          $patchStr='&nbsp;/&nbsp;';
         // $str.=$ugMdl::get($v)['name'].$patchStr.$v.'<br>';
          $str.=$preStr.$v.$patchStr.$ugMdl::get($v)['name'].'<br>';
        }
        return $str;
    }
    #method，得到所有的启用用户组
    static public function getAllGroup()
    {
     
      $obj = new User;
      $arr=$obj->getAllGroupAttr(1);
      unset($obj);
      return $arr;
    }
    #获取器 ，得到所有的启用用户组
    protected function getAllGroupAttr($dbVal)
    {
      $ugSet=UsergroupModel::where('enable',1)->order('id asc')->select();
      $keys=[];
      $vals=[];
      foreach($ugSet as $k=>$v){
        $keys[$k]=$v['id'];
        $vals[$k]=$v['name'];
      }
      return array_combine($keys,$vals);
    }
    #获取器，根据getJoinedGroupAttr()，得到可参加的用户组数组
    protected function getToJoinGroupAttr($dbVal,$data)
    {
        $allGroup=$this->getAllGroupAttr(1);
        $joinedGoup=$this->getJoinedGroupAttr(1,$data);
        //foreach($ugSet as $k=>$v){
//          $keys[$k]=$v['id'];
//          $vals[$k]=$v['name'];
//        }
//        $allGroup=array_combine($keys,$vals);
        
        
        return array_diff_assoc($allGroup,$joinedGoup);
    }
    #获取器，根据数据表usergroup_id字段值，得到已参加的用户组数组
    protected function getJoinedGroupAttr($dbVal,$data)
    {
      $ugSet=UsergroupModel::where('id','in',explode(",", $data['usergroup_id']))->order('id asc')->select();
      $keys=[];
      $vals=[];
      foreach($ugSet as $k=>$v){
        $keys[$k]=$v['id'];
        $vals[$k]=$v['name'];
      }
      return array_combine($keys,$vals);
    }
    #method，得到所有有效的部门数组
    static public function getAllDept()
    {
      $obj = new User;
      $arr=$obj->getAllDeptAttr(1,$obj->data);
      unset($obj);
      return $arr;
      //return $this->getAllDeptAttr(1,$this->data);
    }
    #获取器，得到所有有效的部门数组
    Protected function getAllDeptAttr($dbVal,$data)
    {
      $dtSet=DeptModel::where('enable',1)->order('id asc')->select();
      $dtSet=is_array($dtSet)?collection($dtSet):$dtSet;
      
      return array_combine($dtSet->column('id'),$dtSet->column('name'));
    }
    #method，加入用户组$ugId
    static public function addUserGroup($ugId)
    {
      $obj = new User;
      $arr=$obj->getAllDeptAttr(1,$obj->data);
      unset($obj);
      return $arr;
      //return $this->getAllDeptAttr(1,$this->data);
    }
    #method，添加/删除用户组$ugId
    static public function setUserGroup($userId='',$ugId='',$oprt='')
    {
      $obj = new User;
      $userSet=$obj::get($userId);
      $arr=explode(",", $userSet->getData('usergroup_id'));
      
      #默认为$oprt=='_DELETE'
      for($i=0;$i<count($arr);$i++){
        if($arr[$i]==$ugId){
          unset($arr[$i]);
          break;
        }
      }
      
      if($oprt=='_ADD'){
        array_push($arr,$ugId);
      }
      sort($arr);
      $userSet->data('usergroup_id',implode(',',$arr));
      $userSet->save();
      
      unset($obj);
      return true;
     
    }
    /**
     * 获取用户所属的角色信息
     */
  	public function role()
   {
      	return $this->belongsTo('Rolety');
        //return $this->belongsTo('Usergroup');
   }
   
    
    /**
     * 获取用户所属的角色信息
     */
  	public function userGroup()
   {
        return $this->belongsTo('Usergroup');
   }
    
   /**
     * 获取用户所属的角色信息，与Rolety通过虚拟中间表‘auth’建立多对多关联
     */
//	public function roles()
//    {
//    	return $this->belongsToMany('Rolety', 'auth');
//    }
    
    /**
     * 刷新登录用户的权限
     */
    public function refreshUserAuth($username,$pwd)
    {
      $user=$this->where('username',$username)->where('pwd',$pwd)->where('enable',1)->find();
      #本模型中已定义'usergroup_id'字段的获取器，要获得原始数据
      $ugArr= explode(",", $user->getData('usergroup_id'));//$usergroup_id=array(8,9,10)
      $authArr=[];
      #将$ugArr中各个值对应的authoriy逐个merge进$authArr
      foreach($ugArr as $v){
        $ugAuth=UsergroupModel::get($v);
        $authArr=array_merge($authArr,$ugAuth->authority);
        
      }
      
      #使用静态方法，向User表更新信息，赋值有变化就会更新和返回对象，无变化则无更新和对象返回。
      return $this::update([
          'authority'  => $authArr,
        ], ['username' => $username,'pwd'=>$pwd]);
    }
}
?>

