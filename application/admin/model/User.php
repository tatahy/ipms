<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\admin\model;

use think\Model;
use app\admin\model\Usergroup1 as UsergroupModel;
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
        
        foreach($arr as $k=>$v){
          $str.=$ugMdl::get($v)['name'].'('.$v.')&nbsp;<br>';
        }
        return $str;
    }
    //method，得到所有的启用用户组
    public function getAllGroup()
    {
      //$ugSet=UsergroupModel::where('enable',1)->order('id asc')->select();
//      $keys=[];
//      $vals=[];
//      foreach($ugSet as $k=>$v){
//        $keys[$k]=$v['id'];
//        $vals[$k]=$v['name'];
//      }
      return $this->getAllGroupAttr(1);
    }
    //获取器 ，得到所有的启用用户组
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
    //获取器，根据getJoinedGroupAttr()，得到可参加的用户组数组
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
    //获取器，根据数据表usergroup_id字段值，得到已参加的用户组数组
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
    //method，得到所有有效的部门数组
    public function getAllDept()
    {
      return $this->getAllDeptAttr(1,$this->data);
    }
    //获取器，得到所有有效的部门数组
    Protected function getAllDeptAttr($dbVal,$data)
    {
      $dtSet=DeptModel::where('enable',1)->order('id asc')->select();
      $dtSet=is_array($dtSet)?collection($dtSet):$dtSet;
      
      return array_combine($dtSet->column('id'),$dtSet->column('name'));
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
     * 刷新登录用户的各个模块（issue，project，patent，thesis，attachment，asset）权限
     */
     public function refreshUserAuth($username,$pwd)
    {
      $user=$this->where('username',$username)->where('pwd',$pwd)->where('enable',1)->find();
      $usergroup_id= explode(",", $user->usergroup_id);//$usergroup_id=array(8,9,10)
      
      //$iss=array("edit"=>0,"audit"=>0,"approve"=>0,"execute"=>0,"maintain"=>0);
//      $pat=array("edit"=>0,"audit"=>0,"approve"=>0,"execute"=>0,"maintain"=>0);
//      $att=array("upload"=>0,"download"=>0,"erase"=>0,"move"=>0,"copy"=>0);
//      $pro=array("edit"=>0,"audit"=>0,"approve"=>0,"execute"=>0,"maintain"=>0);
//      $the=array("edit"=>0,"audit"=>0,"approve"=>0,"execute"=>0,"maintain"=>0); 
     // $admin=array("enable"=>0);
      
      $iss=_commonModuleAuth('_ISS');
      $pat=_commonModuleAuth('_PAT');
      $att=_commonModuleAuth('_ATT');
      $pro=_commonModuleAuth('_PRO');
      $the=_commonModuleAuth('_THE');
      $admin=_commonModuleAuth('_ADMIN');
      $ass=_commonModuleAuth('_ASS');
      
      for($i=0;$i<count($usergroup_id);$i++){
        //根据usergroup_id，应用模型UsergroupModel分别取出对应usergroup的iss/pat/pro/the/att权限的项，
        $usergroup=UsergroupModel::get($usergroup_id[$i]);
        //array_filter($arr)去除数组$arr中值为false的键值对后的新数组
        //array_merge再重新合并成新数组，得到用户所在所有用户组权限的交集。
        //$iss=array_merge($iss,array_filter($usergroup['authority']['iss']));
//        $pat=array_merge($pat,array_filter($usergroup['authority']['pat']));
//        $pro=array_merge($pro,array_filter($usergroup['authority']['pro']));
//        $the=array_merge($the,array_filter($usergroup['authority']['the']));
//        $att=array_merge($att,array_filter($usergroup['authority']['att']));
//        $admin=array_merge($admin,array_filter($usergroup['authority']['admin']));

        //$iss=array_merge($iss,array($usergroup['authority']['iss']));
//        $pat=array_merge($pat,array($usergroup['authority']['pat']));
//        $pro=array_merge($pro,array($usergroup['authority']['pro']));
//        $the=array_merge($the,array($usergroup['authority']['the']));
//        $att=array_merge($att,array($usergroup['authority']['att']));
//        $admin=array_merge($admin,array($usergroup['authority']['admin']));

        if(array_filter($usergroup['authority']['iss'])){
          $iss=array_merge($iss,array_filter($usergroup['authority']['iss']));
        }
        if(array_filter($usergroup['authority']['pat'])){
          $pat=array_merge($pat,array_filter($usergroup['authority']['pat']));
        }
        if(array_filter($usergroup['authority']['pro'])){
          $pro=array_merge($pro,array_filter($usergroup['authority']['pro']));
        }
        if(array_filter($usergroup['authority']['the'])){
          $the=array_merge($the,array_filter($usergroup['authority']['the']));
        }
        if(array_filter($usergroup['authority']['att'])){
          $att=array_merge($att,array_filter($usergroup['authority']['att']));
        }
        if(array_filter($usergroup['authority']['ass'])){
          $ass=array_merge($ass,array_filter($usergroup['authority']['ass']));
        }
        
        
      }
      //组装数据
      $authority=array("iss"=>$iss,
                        "att"=>$att,
                        "pat"=>$pat,
                        "pro"=>$pro,
                        "the"=>$the,
                        "admin"=>$admin,
                        "ass"=>$ass,
                        );
        
      // 使用静态方法，向User表更新信息，赋值有变化就会更新和返回对象，无变化则无更新和对象返回。
      $this::update([
          'authority'  => $authority,
        ], ['username' => $username,'pwd'=>$pwd]);
      return $authority;
    }
}
?>

