<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：AuditState
 */

namespace isspatfsm\audit;

use isspatfsm\audit\AuditContext;
use isspatfsm\audit\AuditState;

class CheckingState extends AuditState{
  
  public function pass($data){
    $this->_updateStatus($data);
    //状态修改
    $this->_context->setState(AuditContext::$auditPassedState);
    
    return 'pass结果：';
  }
  
  public function fail($data){
    $this->_updateStatus($data);
    //状态修改
    $this->_context->setState(AuditContext::$auditFailedState);
    
    return 'fail结果：';
    
  }
  
  public function modify($data){
    $this->_updateStatus($data);
    //状态修改
    $this->_context->setState(AuditContext::$modifyingState);
    
    return 'modify结果：';
  }
  
}

?>