<?php

/**
 * @author tatahy
 * @copyright 2018
 * �̳С�ʵ�ֳ����ࣺExecuteState
 */

namespace isspatfsm\execute;

use isspatfsm\execute\ExecuteState;
use isspatfsm\execute\ExecuteContext;

class ExecutingState extends ExecuteState{
  
  public function accept($data){  
    return '��Чaccept����';
  }
  public function refuse($data){
  
    return '��Чrefuse����';
  }
  public function report($data){
    $this->_updateStatus($data);
    
    return 'report�����';
  }
  public function finish($data){
    $this->_updateStatus($data);
    //״̬�޸�
    $this->_context->setState(ExecuteContext::$applyReviewingState);
    return 'finish���';
  }
  
  
}

?>