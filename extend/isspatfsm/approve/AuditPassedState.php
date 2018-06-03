<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：ApproveState
 */

namespace isspatfsm\approve;

use isspatfsm\approve\ApproveState;
use isspatfsm\approve\ApproveContext;

class AuditPassedState extends ApproveState{
  
  public function permit(){
    //写入数据库的信息
    $this->_oprtData['iss']['info']['status'] = '批准申报';
    $this->_oprtData['pat']['info']['status'] = '内审批准';
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    return '<br>permit:'.$this->_mdl->test();
    
    //父类数据库模型操作方法
    $this->_oprtMdl();
    
    //状态修改
    $this->_context->setState(ApproveContext::$applyApprovedState);
    
    return 'approve结果：';
  }
  public function veto(){
    //写入数据库的信息
    $this->_oprtData['iss']['info']['status'] = '否决申报';
    $this->_oprtData['pat']['info']['status'] = '内审否决';
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    return '<br>veto:'.$this->_mdl->test();
    
    //父类数据库模型操作方法
    $this->_oprtMdl();
    
    //状态修改
    $this->_context->setState(ApproveContext::$applyVetoedState);
    
    return 'veto结果：';
  }
  public function complete(){
    //写入数据库的信息
    $this->_oprtData['iss']['info']['status'] = '修改完善';
    $this->_oprtData['pat']['info']['status'] = '内审修改';
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    return '<br>complete:'.$this->_mdl->test();
    
    //父类数据库模型操作方法
    $this->_oprtMdl();
    
    //状态修改
    $this->_context->setState(ApproveContext::$completingState);
    
    return 'complete结果：';
  }
  
  
}

?>