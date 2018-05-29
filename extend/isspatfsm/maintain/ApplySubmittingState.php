<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：MaintainState
 */

namespace isspatfsm\maintain;

use isspatfsm\maintain\MaintainState;
use isspatfsm\maintain\MaintainContext;

class ApplySubmittingState extends MaintainState{
  
  public function apply($data){  
    return '<br>无效apply操作';
  }
  public function review($data){
  
    return '<br>无效review操作';
  }
  public function improve($data){
    $this->_updateStatus($data);
    //状态修改
    $this->_context->setState(MaintainContext::$applyModifyingState);
    return '<br>improve结果';
  }
  public function authorize($data){
    $this->_updateStatus($data);
    //状态修改
    $this->_context->setState(MaintainContext::$applyAuthorizedState);
    return '<br>authorize结果';
  }
  
  public function reject($data){  
    $this->_updateStatus($data);
    //状态修改
    $this->_context->setState(MaintainContext::$applyRejectedState);
    return '<br>reject结果';
  }
  
  public function addRenew($data){  
    return '<br>无效addRenew操作';
  }
  
  public function close($data){  
    return '<br>无效close操作';
  }
  
}

?>