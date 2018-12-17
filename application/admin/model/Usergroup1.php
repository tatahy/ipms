<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\admin\model;

use think\Model;

class Usergroup1 extends Model
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
    
    #获取器，获取数据表Name字段值，转换为中文输出
   // protected function getAuthorityAttr($dbArr)
//    {
//      $tarArr=$dbArr;
//      //$refArr=conAuthValueArr;
////      $testArr=fn_merge_auth($tarArr,$refArr);
//      
//      return $tarArr;
//    }
  
    
         
}
?>

