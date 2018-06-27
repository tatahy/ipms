<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\dashboard\model;

use think\Model;

class Issrecord extends Model
{   
    //protected $auto = ['patnum','pronum'];
    protected $insert = ['issinfo_id'];  
    //protected $update = ['topic','abstract','addnewdate'];  
   
    //只读字段，这个字段的值一旦写入，就无法更改。
    protected $readonly = ['issinfo_id'];
    
    // 开启时间字段自动写入
	protected $autoWriteTimestamp = true; 
    
    //指定字段类型
    protected $type = [
        'actdetailhtml'    =>  'json',
        
    ];
    
     //获取器，获取数据表issrecord中rolename字段值，转换为中文输出
    protected function getRolenameAttr($key)
    {
      $value=$key;
      $roleName = [
                'writer'=>'撰写','reviewer'=>'审核','approver'=>'批准','operator'=>'执行','maintainer'=>'维护',
                '_EDIT'=>'撰写','_AUDIT'=>'审核','_APPROVE'=>'批准','_EXECUTE'=>'执行','_MAINTAIN'=>'维护',
                ];
      if(array_key_exists($key,$roleName)){
        $value = $roleName[$key];
      }
      return $value;
    }
    
    //获取器，获取数据表issrecord中act字段值，转换为中文输出
    protected function getActAttr($key)
    {
      $value=$key;
      $act = [
                '_ADDNEW'=>'新增','_UPDATE'=>'更新','_SUBMIT'=>'提交','_DELETE'=>'删除',
                '_PASS'=>'审核通过','_FAIL'=>'审核未通过','_MODIFY'=>'返回修改',
                '_PERMIT'=>'批准','_VETO'=>'否决','_COMPLETE'=>'修改完善',
                '_ACCETP'=>'领受','_REFUSE'=>'申诉','_REPORT'=>'报告','_FINISH'=>'完成',
                '_APPLY'=>'申报提交','_REVIEW'=>'申报复核','_IMPROVE'=>'申报修改','_AUTHORIZE'=>'授权','_REJECT'=>'驳回','_ADDRENEW'=>'续费申请','_CLOSE'=>'完结'
            ];
      if(array_key_exists($key,$act)){
        $value = $act[$key];
      }
      return $value;
    }
    
    /**
     * 获取issrecord所属的issue信息，与Issinfo模型关联，是多对一的关联关系？？
     */
    public function issrecords()
    {   
        return $this->belongsTo('Issinfo','issinfo_id');

    }
    
    /**
     * 获取特定issue的最新审核、审批、修改意见
     * @param  intger $issId 待查询issue的id
     * @return char $issChRd
     */
    public function issChRdRecent($issId)
    {   
        $idMax=$this->where('issinfo_id',$issId)->where('act','in',['_COMPLETE','_MODIFY','_IMPROVE'])->max('id');
        if($idMax){
            $issChRd=$this->where('id',$idMax)->find()['actdetailhtml']['pre']['text'];
        }else{
            $issChRd='';
        }
        
        return $issChRd;
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