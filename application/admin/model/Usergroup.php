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
    
    // 与模型User（表user）的关联关系
    public function users()
    {
        return $this->hasMany('User');
    }
    
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
    
    /**
     * 初始化用户组的各个模块（issue，project，patent，thesis，attachment）权限
     */
     public function initUsergroupAuth($id)
    {
      $usergroup=$this::get($id);

      //array_filter($arr)去除数组$arr中值为false的键值对后的新数组
      //array_merge再重新合并成新数组，得到用户组新的权限集。
     // $iss=array_merge(_commonModuleAuth('_ISS'),array($usergroup['authority']['iss']));
//      $pat=array_merge(_commonModuleAuth('_PAT'),array($usergroup['authority']['pat']));
//      $pro=array_merge(_commonModuleAuth('_PRO'),array($usergroup['authority']['pro']));
//      $the=array_merge(_commonModuleAuth('_THE'),array($usergroup['authority']['the']));
//      $att=array_merge(_commonModuleAuth('_ATT'),array($usergroup['authority']['att']));
//      $admin=array_merge(_commonModuleAuth('_ADMIN'),array($usergroup['authority']['admin']));
      
      $iss=_commonModuleAuth('_ISS');
      $pat=_commonModuleAuth('_PAT');
      $att=_commonModuleAuth('_ATT');
      $pro=_commonModuleAuth('_PRO');
      $the=_commonModuleAuth('_THE');
      $admin=_commonModuleAuth('_ADMIN');
      
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

