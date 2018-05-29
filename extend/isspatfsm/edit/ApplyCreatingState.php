<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：EditState
 */

namespace isspatfsm\edit;

use isspatfsm\edit\EditContex;
use isspatfsm\edit\EditState;

class ApplyCreatingState extends EditState{
  public function addNew($data){
    //1.patinfo新增
    //模型create()方法
    //array_merge($data['pat']['data'],$patData);
    Mdl::$patMdl->create($data['pat']['data'],true);
    
    //2.patrecord新增
    Mdl::$patRdMdl->create($data['pat']['rdData'],true);
    
    //3.issinfo新增
    Mdl::$issMdl->create($data['iss']['data'],true);
    
    //4.issrecord新增
    Mdl::$issRdMdl->create($data['iss']['rdData'],true);
    
    //5.attinfo更新???
    
    //状态修改
    $this->_context->setState(EditContext::$FillingState);
    
    return 'addnew操作结果：';
   // return json_encode($data);
  }
  
  public function submit($data){
    //oprt=="submit"要进行的操作
    return '无此操作';
    
  }
  
  public function delete($data){
    return '无此操作';
  }
  
  public function update($data){
    return '无此操作';
  }
  
}

?>