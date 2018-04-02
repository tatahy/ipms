<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\patent\model;

use think\Model;

class Patrecord extends Model
{   
    //protected $auto = ['patnum','pronum'];
    protected $insert = ['num'];  
    //protected $update = ['topic','abstract','addnewdate'];  
   
    //只读字段，这个字段的值一旦写入，就无法更改。
    protected $readonly = ['num'];
    
    /**
     * 获取patrecord所属的patent信息
     */
    public function patrecords()
    {   
        return $this->belongsTo('Patinfo');
    }
    
     //获取器，获取数据表patrecord中rolename字段值，转换为中文输出
    //获取器，获取数据表issrecord中rolename字段值，转换为中文输出
    protected function getRolenameAttr($value)
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
        
         case '_EDIT':
          $outPut='撰写人';
        break;
        
        case '_AUDIT':
          $outPut='审核人';
        break;
        
        case '_APPROVE':
          $outPut='批准人';
        break;
        
        case '_EXECUTE':
          $outPut='执行人';
        break;
        
        case '_MAINTAIN':
          $outPut='维护人';
        break;
                
        default:
          $outPut=$value;
        break;
        
      }
      return $outPut;
    }
        
    
}
    



?>