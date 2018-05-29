<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：ApproveState
 */

namespace isspatfsm\approve;

use isspatfsm\approve\ApproveState;
use isspatfsm\approve\ApproveContext;

class RenewPlanningState extends ApproveState{
  
  public function approve($data){
    $this->_updateStatus($data);
    //状态修改
    $this->_context->setState(ApproveContext::$renewApprovedState);
    
    return 'approve结果：';
  }
  public function veto($data){
    $this->_updateStatus($data);
    //状态修改
    $this->_context->setState(ApproveContext::$renewVetoedState);
    
    return 'veto结果：';
  }
  public function complete($data){
    
    return '无complete操作';
  }
  
  
}

?>