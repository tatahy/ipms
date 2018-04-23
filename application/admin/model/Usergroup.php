<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\admin\model;

use think\Model;

class Usergroup extends Model
{   
    //protected $auto = ['patnum','pronum'];
    //protected $insert = ['dev_id'];  
    //protected $update = ['topic','abstract','addnewdate'];
    
    
    //只读字段，这个字段的值一旦写入，就无法更改。
    //protected $readonly = ['rolety_id'];
    
    protected $type = [
        'authority'  =>  'json',
    ];
    
    /**
     * 初始化用户组的各个模块（issue，project，patent，thesis，attachment）权限
     */
     public function initUsergroupAuth($id)
    {
      $usergroup=$this::get($id);

      //array_filter($arr)去除数组$arr中值为false的键值对后的新数组
      //array_merge再重新合并成新数组，得到用户组新的权限集。
      $iss=array_merge(_commonModuleAuth('_ISS'),array_filter($usergroup['authority']['iss']));
      $pat=array_merge(_commonModuleAuth('_PAT'),array_filter($usergroup['authority']['pat']));
      $pro=array_merge(_commonModuleAuth('_PRO'),array_filter($usergroup['authority']['pro']));
      $the=array_merge(_commonModuleAuth('_THE'),array_filter($usergroup['authority']['the']));
      $att=array_merge(_commonModuleAuth('_ATT'),array_filter($usergroup['authority']['att']));
      $admin=array_merge(_commonModuleAuth('_ADMIN'),array_filter($usergroup['authority']['admin']));
        
      //组装数据
     $authority=array("iss"=>$iss,
                        "att"=>$att,
                        "pat"=>$pat,
                        "pro"=>$pro,
                        "the"=>$the,
                        "admin"=>$admin
                        );
        
      // 使用静态方法，向Usergroup表更新信息，赋值有变化就会更新和返回对象，无变化则无更新和对象返回。
      $this::update([
          'authority'  => $authority,
        ], ['id'=>$id]);
      return $authority;
    }
    
         
}
?>

