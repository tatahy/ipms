<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：ExecuteState
 */

namespace isspatfsm\execute;

use isspatfsm\execute\ExecuteState;
use isspatfsm\execute\ExecuteContext;

class ExeChApprovedState extends ExecuteState{
  
  public function accept($data){
    $this->_updateStatus($data);
    //状态修改
    $this->_context->setState(ExecuteContext::$executingState);
    
    return 'accept结果：';
  }
  public function refuse($data){
  
    return '无效refuse操作';
  }
  public function report($data){
    
    return '无效report操作：';
  }
  public function finish($data){
    
    return '无效finish操作';
  }
  
  
}

?>