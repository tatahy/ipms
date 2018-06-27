<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\issue\model;

use think\Model;

class Issrecord extends Model
{   
    //protected $auto = ['patnum','pronum'];
    protected $insert = ['issinfo_id'];  
    //protected $update = ['topic','abstract','addnewdate']; 
    
    
    //指定字段类型
    protected $type = [
        'actdetailhtml' => 'json',
        
    ];
    
     //获取器，获取数据表issrecord中rolename字段值，转换为中文输出
    //获取器，获取数据表issrecord中rolename字段值，转换为中文输出
    protected function getRolenameAttr($key)
    {
      $value=$key;
      $roleName = [
                'writer'=>'撰写','reviewer'=>'审核','approver'=>'批准','operator'=>'执行','maintainer'=>'申报/管理',
                '_EDIT'=>'撰写','_AUDIT'=>'审核','_APPROVE'=>'批准','_EXECUTE'=>'执行','_MAINTAIN'=>'申报/管理',
                ];
      if(array_key_exists($key,$roleName)){
        $value = $roleName[$key];
      }
      return $value;
    }
    
    //获取器，获取数据表issrecord中act字段值，转换为中文输出
    protected function getActAttr($key)
    {
      $act = [
                '_ADDNEW'=>'新增','_UPDATE'=>'更新','_SUBMIT'=>'提交','_DELETE'=>'删除',
                '_PASS'=>'审核通过','_FAIL'=>'审核未通过','_MODIFY'=>'返回修改',
                '_PERMIT'=>'批准','_VETO'=>'否决','_COMPLETE'=>'修改完善',
                '_ACCETP'=>'领受','_REFUSE'=>'申诉','_REPORT'=>'报告','_FINISH'=>'完成',
                '_APPLY'=>'申报提交','_REVIEW'=>'申报复核','_IMPROVE'=>'申报修改','_AUTHORIZE'=>'授权','_REJECT'=>'驳回','_ADDRENEW'=>'续费申请','_CLOSE'=>'完结'
            ];
      if(in_array($key,array_keys($act))){
        return $act[$key];
      }else{
        return $key;
      }
    }
   
    //只读字段，这个字段的值一旦写入，就无法更改。
    protected $readonly = ['issinfo_id'];
    
    /**
     * 获取issrecord所属的issue信息
     */
    public function issrecords()
    {   
        return $this->belongsTo('Issinfo');
    }
        
    
}
    



?>