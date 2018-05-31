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
  public function addNew(){
    $data=$this->_oprtData;
    //确保写入数据库的关键信息无误（前端无法准确给出??）
    //$data['iss']['info']['status']='新增';
    //$data['pat']['info']['status']='内审';
    $data=array_merge($data,array($data['iss']['info']['status']=>'新增',$data['pat']['info']['status']=>'内审'));
    
    //1.patinfo新增
    $this->_mdl->patCreate($data['pat']['info']);
    
    //2.patrecord新增
    $this->_mdl->patRdCreate($data['pat']['record']);
    
    //3.issinfo新增
    $this->_mdl->issCreate($data['iss']['info']);
    
    //4.issrecord新增
    $this->_mdl->issRdCreate($data['iss']['record']);
    
    //5.attinfo更新???
    
    //状态修改
    $this->_context->setState(EditContext::$FillingState);
    
    return 'addnew操作结果：';
   // return json_encode($data);
  }
  
  public function submit(){
    //oprt=="submit"要进行的操作
    return '<br>无submit操作';
    
  }
  
  public function delete(){
    return '<br>无delete操作';
  }
  
  public function update(){
    return '<br>无update操作';
  }
  
}

?>