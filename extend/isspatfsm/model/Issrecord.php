<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace isspatfsm\model;

use think\Model;

class Issrecord extends Model
{   
    //protected $auto = ['patnum','pronum'];
    protected $insert = ['issinfo_id'];  
    //protected $update = ['topic','abstract','addnewdate'];  
   
    //只读字段，这个字段的值一旦写入，就无法更改。
    protected $readonly = ['issinfo_id'];
    
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
    
    /**
     * 获取issrecord所属的issue信息
     */
    public function issrecords()
    {   
        return $this->belongsTo('Issinfo');
    }
    
     /**
     * 新增一个issureRecord。
     * @param  array $data 新增issure的各项信息
     * @return integer|bool  新增成功返回主键，新增失败返回false
     * 要求：传入的数组下标名与模型属性名（数据表字段名）一模一样。
     */
    public function issRdCreate($data = [])
    {
        $result = $this->allowField(true)->save($data);
        if ($result) {
            return $this->getData('id');
        } else {
            return false;
        }
    }
    
    /**
     * 更新issureRecord。
     * @param  array $data 更新issure的各项信息
     * @return integer|bool  更新成功返回主键，未更新返回false
     * 要求：传入的数组下标名与模型属性名（数据表字段名）一模一样。
     */
    public function issRdUpdate($data = [],$issId)
    {
       $idmax=$this::all('issinfo_id',$issId)->max('id');
       
       $result = $this::get($idmax)->allowField(true)->save($data);
       // $issRd=$this::get($id);
//        $result = $issRd->allowField(true)->data($data, true)->save();
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }
    
     /**
     * 删除issureRecord。
     * @param  integer $issId 删除issure的id
     * @return integer|bool  删除成功返回主键，未成功返回false
     * 考虑应用TP5的软删除进行改进，？？？2018/3/23
     */
    public function issRdDelete($issId)
    {
        //delete()方法返回的是受影响记录数
        $result = $this->where('issinfo_id',$issId)->delete();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
        
    
}
    



?>