<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\admin\model;

use think\Model;

class Dept extends Model
{   
   // 关联关系
    //public function users()
//    {
//        return $this->hasMany('User');
//    }
  #可静态使用的方法，得到所有有效的部门数组（以id为下标，name为值的关联数组）
  Static Public function getEnDepts()
  {
    $obj=new Dept;
    $set=$obj->where('enable',1)->order('id asc')->select();
    $set=is_array($set)?collection($set):$set;
    unset($obj);
    return array_combine($set->column('id'),$set->column('name'));
  }
    
}


?>