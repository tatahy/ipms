<?php

/**
 * @author tatahy
 * @copyright 2018
 * ����һ�������࣬ʵ��_MAINTAINȨ���¶�issPat��13��״̬��ת����7�ֲ�����ִ�С�
 */

namespace isspatfsm\maintain;

use isspatfsm\maintain\MaintainState;

//input State
use isspatfsm\maintain\RenewCreatingState;
use isspatfsm\maintain\ApplyReviewingState;
use isspatfsm\maintain\ApplyVetoedState;
use isspatfsm\maintain\RenewApprovedState;
use isspatfsm\maintain\RenewVetoedState;
//output state
use isspatfsm\approve\RenewPlanningState;
use isspatfsm\maintain\ClosedState;
use isspatfsm\execute\ApplyModifyingState;
//input & output state
use isspatfsm\maintain\ApplySubmittingState;
use isspatfsm\maintain\ApplyAuthorizedState;
use isspatfsm\maintain\ApplyRejectedState;
use isspatfsm\maintain\RenewSubmittingState;
use isspatfsm\maintain\RenewAuthorizedState;


class MaintainContext{
  //_MAINTAIN��13��״̬
  //static��ʶ����ľ�̬����ֻ�����࣬�����ʵ�����������޹ء�
  //��ľ�̬���������ں�����ȫ�ֱ�����������ⲿʹ������ֱ�ӷ�����ľ�̬���ԡ�EditContext::$fillingState��
  static $renewCreatingState = null;
  static $applyReviewingState = null;
  static $applyVetoedState = null;
  static $renewApprovedState = null;
  static $renewVetoedState = null;
  static $renewPlanningState = null;
  static $closedState = null; 
  static $applyModifyingState = null;
  static $applySubmittingState = null;
  static $applyAuthorizedState = null;
  static $applyRejectedState = null;
  static $renewSubmittingState = null;
  static $renewAuthorizedState = null; 
  
  
  //����һ����ǰ_MAINTAIN״̬������ֵ�ǵ�ǰ_MAINTAIN״̬����ʵ����
  private $_currentState;
  
  public function __construct(){
    //���ʱ����ж���ľ�̬����
    self::$renewCreatingState = new RenewCreatingState();
    self::$applyReviewingState = new ApplyReviewingState();
    self::$applyVetoedState = new ApplyVetoedState();
    self::$renewApprovedState = new RenewApprovedState();
    self::$renewVetoedState = new RenewVetoedState();
    self::$renewPlanningState = new RenewPlanningState();
    self::$closedState = new ClosedState();
    self::$applyModifyingState = new ApplyModifyingState;
    self::$applySubmittingState = new ApplySubmittingState();
    self::$applyAuthorizedState = new ApplyAuthorizedState();
    self::$applyRejectedState = new ApplyRejectedState();
    self::$renewSubmittingState = new RenewSubmittingState();
    self::$renewAuthorizedState = new RenewAuthorizedState();
   
  }
  
  //��ȡ״̬
  public function getState(){
    return $this->_currentState;
  }
  //���õ�ǰ״̬
  public function setState(MaintainState $maintainState){
    $this->_currentState=$maintainState;
    //�ѵ�ǰ�Ļ���֪ͨ������ʵ������
    $this->_currentState->setContext($this);
  }
  
  //_MAINTAIN��7�ֲ���
  public function apply($data){
   return $this->_currentState->apply($data);
  }
  
  public function review($data){
    $this->_currentState->review($data);
  }
  
  public function improve($data){
    $this->_currentState->improve($data);
  }
  
  public function authorize($data){
    $this->_currentState->authorize($data);
  }
  
  public function reject($data){
    $this->_currentState->reject($data);
  }
  
  public function addRenew($data){
    $this->_currentState->addRenew($data);
  }
  
  public function close($data){
    $this->_currentState->close($data);
  }
}

?>