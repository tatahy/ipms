<?php

/**
 * @author tatahy
 * @copyright 2018
 * �̳С�ʵ�ֳ����ࣺExecuteState
 */

namespace isspatfsm\execute;

use isspatfsm\execute\ExecuteState;
use isspatfsm\execute\ExecuteContext;

class ExeChVetoedState extends ExecuteState{
  
  public function accept($data){
    $this->_updateStatus($data);
    //״̬�޸�
    $this->_context->setState(ExecuteContext::$executingState);
    
    return 'accept�����';
  }
  public function refuse($data){
  
    return '��Чrefuse����';
  }
  public function report($data){
    
    return '��Чreport������';
  }
  public function finish($data){
    
    return '��Чfinish����';
  }
  
  
}

?>