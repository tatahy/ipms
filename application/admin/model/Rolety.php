<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\admin\model;

use think\Model;

class Rolety extends Model
{   
   // 关联关系
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
     * 获取角色下面的用户信息，与User通过虚拟中间表‘auth’建立多对多关联
     */
	 //public function users()
//    {
//    	return $this->belongsToMany('User', 'auth');
//    }
    
    
}


?>