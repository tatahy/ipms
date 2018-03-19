<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\user\model;

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
    
     /**
     * 新增一个patentRecord。
     * @param  array $data 新增patent的各项信息
     * @return integer|bool  新增成功返回主键，新增失败返回false
     * 要求：传入的数组下标名与模型属性名（数据表字段名）一模一样。
     */
    public function patRdCreate($data = [])
    {
        $result = $this->allowField(true)->save($data);
        if ($result) {
            return $this->getData('id');
        } else {
            return false;
        }
    }
    
    /**
     * 更新patentRecord。
     * @param  array $data 更新patent的各项信息
     * @return integer|bool  更新成功返回主键，未更新返回false
     * 要求：传入的数组下标名与模型属性名（数据表字段名）一模一样。
     */
    public function patRdUpdate($data = [],$patId)
    {
        $result = $this->where('id',$patId)->allowField(true)->save($data);
        if ($result) {
            return $this->getData('id');
        } else {
            return false;
        }
    }
    
     /**
     * 删除patentRecord。
     * @param  integer $patId 删除patent的id
     * @return integer|bool  删除成功返回主键，未成功返回false
     *
     */
    public function patRdDelete($patId)
    {
        //delete()方法返回的是受影响记录数
        $result = $this->where('id',$patId)->delete();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }  
        
    
}
    



?>