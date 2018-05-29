<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：ExecuteState
 */

namespace isspatfsm\execute;

use isspatfsm\execute\ExecuteState;
use isspatfsm\execute\ExecuteContext;

class ApplyModifyingState extends ExecuteState{
  
  public function accept($data){  
    return '无效accept操作';
  }
  public function refuse($data){
  
    return '无效refuse操作';
  }
  public function report($data){
    $this->_updateStatus($data);
    
    return 'report结果：';
  }
  public function finish($data){
    $this->_updateStatus($data);
    //状态修改
    $this->_context->setState(ExecuteContext::$applyReviewingState);
    return 'finish结果';
  }
  
  
}

?>