<?php

/**
 * @author tatahy
 * @copyright 2018
 * �̳С�ʵ�ֳ����ࣺApproveState
 */

namespace isspatfsm\approve;

use isspatfsm\approve\ApproveState;
use isspatfsm\approve\ApproveContext;

class RenewPlanningState extends ApproveState{
  
  public function approve($data){
    $this->_updateStatus($data);
    //״̬�޸�
    $this->_context->setState(ApproveContext::$renewApprovedState);
    
    return 'approve�����';
  }
  public function veto($data){
    $this->_updateStatus($data);
    //״̬�޸�
    $this->_context->setState(ApproveContext::$renewVetoedState);
    
    return 'veto�����';
  }
  public function complete($data){
    
    return '��complete����';
  }
  
  
}

?>