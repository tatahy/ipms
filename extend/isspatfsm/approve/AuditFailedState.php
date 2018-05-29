<?php

/**
 * @author tatahy
 * @copyright 2018
 * �̳С�ʵ�ֳ����ࣺApproveState
 */

namespace isspatfsm\approve;

use isspatfsm\approve\ApproveState;
use isspatfsm\approve\ApproveContext;

class AuditFailedState extends ApproveState{
  
  public function approve($data){
    $this->_updateStatus($data);
    //״̬�޸�
    $this->_context->setState(ApproveContext::$applyApprovedState);
    
    return 'approve�����';
  }
  public function veto($data){
    $this->_updateStatus($data);
    //״̬�޸�
    $this->_context->setState(ApproveContext::$applyVetoedState);
    
    return 'veto�����';
  }
  public function complete($data){
    $this->_updateStatus($data);
    //״̬�޸�
    $this->_context->setState(ApproveContext::$completingState);
    
    return 'complete�����';
  }
  
  
}

?>