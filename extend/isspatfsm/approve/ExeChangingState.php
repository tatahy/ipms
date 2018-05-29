<?php

/**
 * @author tatahy
 * @copyright 2018
 * �̳С�ʵ�ֳ����ࣺApproveState
 */

namespace isspatfsm\approve;

use isspatfsm\approve\ApproveState;
use isspatfsm\approve\ApproveContext;

class ExeChangingState extends ApproveState{
  
  public function approve($data){
    $this->_updateStatus($data);
    //״̬�޸�
    $this->_context->setState(ApproveContext::$exeChApprovedState);
    
    return 'approve�����';
  }
  public function veto($data){
    $this->_updateStatus($data);
    //״̬�޸�
    $this->_context->setState(ApproveContext::$exeChVetoedState);
    
    return 'veto�����';
  }
  public function complete($data){
    
    return '��complete����';
  }
  
  
}

?>