<?php

/**
 * @author tatahy
 * @copyright 2018
 * ����һ�������࣬ʵ��_APPROVEȨ���¶�issPat��11��״̬��ת����3�ֲ�����ִ�С�
 */

namespace isspatfsm\audit;

use isspatfsm\approve\ApproveState;

//input State
use isspatfsm\approve\AuditPassedState;
use isspatfsm\approve\AuditFailedState;
use isspatfsm\approve\ExeChangingState;
use isspatfsm\approve\RenewPlanningState;
//output state
use isspatfsm\execute\ApplyApprovedState;
use isspatfsm\maintain\ApplyVetoedState;
use isspatfsm\edit\CompletingState;
use isspatfsm\execute\ExeChApprovedState;
use isspatfsm\execute\ExeChVetoedState;
use isspatfsm\maintain\RenewApprovedState;
use isspatfsm\maintain\RenewVetoedState;

class ApproveContext{
  //_APPROVE��7��״̬
  //static��ʶ����ľ�̬����ֻ�����࣬�����ʵ�����������޹ء�
  //��ľ�̬���������ں�����ȫ�ֱ�����������ⲿʹ������ֱ�ӷ�����ľ�̬���ԡ�EditContext::$fillingState��
  static $auditPassedState = null;
  static $auditFailedState = null;
  static $exeChangingState = null;
  static $renewPlanningState = null;
  static $applyApprovedState = null;
  static $applyVetoedState = null;
  static $completingState = null;
  static $exeChApprovedState = null;
  static $exeChVetoedState = null;
  static $renewApprovedState = null;
  static $renewVetoedState = null;
  
  //����һ����ǰ_APPROVE״̬������ֵ�ǵ�ǰ_APPROVE״̬����ʵ����
  private $_currentState;
  
  public function __construct(){
    //���ʱ����ж���ľ�̬����
    self::$auditPassedState = new AuditPassedState();
    self::$auditFailedState = new AuditFailedState();
    self::$exeChangingState = new ExeChangingState();
    self::$renewPlanningState = new RenewPlanningState();
    self::$applyApprovedState = new ApplyApprovedState();
    self::$applyVetoedState = new ApplyVetoedState();
    self::$completingState = new CompletingState();
    self::$exeChApprovedState = new ExeChApprovedState();
    self::$exeChVetoedState = new ExeChVetoedState();
    self::$renewApprovedState = new RenewApprovedState();
    self::$renewVetoedState = new RenewVetoedState();
  }
  
  //��ȡ״̬
  public function getState(){
    return $this->_currentState;
  }
  //���õ�ǰ״̬
  public function setState(ApproveState $approveState){
    $this->_currentState=$approveState;
    //�ѵ�ǰ�Ļ���֪ͨ������ʵ������
    $this->_currentState->setContext($this);
  }
  
  //_APPROVE��3�ֲ���
  public function permit($data){
   return $this->_currentState->permit($data);
  }
  
  public function veto($data){
    $this->_currentState->veto($data);
  }
  
  public function complete($data){
    $this->_currentState->complete($data);
  }
  
}

?>