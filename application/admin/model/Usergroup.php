<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\admin\model;

use think\Model;

use app\admin\model\User as UserModel;

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
    
    //获取器，获取数据表Name字段值，转换为中文输出
    protected function getNameAttr($value)
    {
      $outPut='……';
      switch($value){
        case 'writer':
          $outPut='撰写人';
        break;
        
        case 'reviewer':
          $outPut='审核人';
        break;
        
        case 'approver':
          $outPut='批准人';
        break;
        
        case 'operator':
          $outPut='执行人';
        break;
        
        case 'maintainer':
          $outPut='维护人';
        break;
                       
        default:
          $outPut=$value;
        break;
        
      }
      return $outPut;
    }
    
     // 与模型User（表user）的关联关系
    public function users()
    {
        return $this->hasMany('User');
    }
       
    #获取器，获取数据表Name字段值，转换为中文输出
   // protected function getAuthorityAttr($dbArr)
//    {
//      $tarArr=$dbArr;
//      //$refArr=conAuthValueArr;
////      $testArr=fn_merge_auth($tarArr,$refArr);
//      
//      return $tarArr;
//    }
  #可静态使用的方法，得到所有有效的用户组数组（以id为下标，name为值的关联数组）
  Static Public function getEnGroups()
  {
    $obj=new Usergroup;
    $set=$obj->where('enable',1)->order('id asc')->select();
    $set=is_array($set)?collection($set):$set;
    unset($obj);
    return array_combine($set->column('id'),$set->column('name'));
  }
  
  #可静态使用的方法，得到用户组成员对象（以id为下标，username为值的关联数组，已按'dept asc'排列）
  Static Public function getGroupMembers($ugId=0)
  {
    //$arr=[['id'=>0,'username'=>'无','dept'=>'无','enable'=>0]];
    $arr=[];
    $obj=new UserModel;
    $set=$obj::all(function($query)use($ugId){
                          $query->field('id,username,dept,enable,mobile')
                                ->whereLike('usergroup_id',$ugId)
                                ->whereLike('usergroup_id',$ugId.',%','or')
                                ->whereLike('usergroup_id','%,'.$ugId.',%','or')
                                ->whereLike('usergroup_id','%,'.$ugId,'or')
                                ->order('dept asc');
                        });
    if(count($set)){
      $arr=$set;
      //$arr=array_combine($set->column('id'),$set->column('username'));
    }
    unset($obj);
    $arr=is_array($arr)?collection($arr):$arr;
    return $arr;
  }
  
  #可静态使用的方法，得到用户组可添加成员对象（以id为下标，username为值的关联数组，已按'dept asc'排列）
  Static Public function getAddOnGroupMembers($ugId=0)
  {
    $arr=[['id'=>0,'username'=>'无','dept'=>'无','enable'=>0]];
    $ugMem=self::getGroupMembers($ugId);
    $uIdArr=is_array($ugMem)?collection($ugMem):$ugMem;
    #已有成员id数组
    $uIdArr=$uIdArr->column('id');
    $obj=new UserModel;
    #用户组可添加的成员，所有用户按部门升序排列减掉已有的
    $set=$obj::all(function($query)use($uIdArr){
                  $query->field('id,username,dept,enable,mobile')
                        ->where('id','notin',$uIdArr)
                        ->order('dept asc');
                  });
    
    if(count($set)){
      $arr=$set;
      //$arr=array_combine($set->column('id'),$set->column('username'));
    }
    unset($obj);
    $arr=is_array($arr)?collection($arr):$arr;
    return $arr;
  }
    
         
}
?>

