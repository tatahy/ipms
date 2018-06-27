<?php

/**
 * @author tatahy
 * @copyright 2018
 * ����һ�������࣬ʵ��_MAINTAINȨ���¶�issPat��13��״̬��ת����7�ֲ�����ִ�С�
 */

namespace app\dashboard\model\isspatfsm\maintain;

use app\dashboard\model\isspatfsm\maintain\MaintainState;

//input State
use app\dashboard\model\isspatfsm\maintain\RenewCreatingState;
use app\dashboard\model\isspatfsm\maintain\ApplyReviewingState;
use app\dashboard\model\isspatfsm\maintain\ApplyVetoedState;
use app\dashboard\model\isspatfsm\maintain\RenewApprovedState;
use app\dashboard\model\isspatfsm\maintain\RenewVetoedState;
//output state
use app\dashboard\model\isspatfsm\approve\RenewPlanningState;
use app\dashboard\model\isspatfsm\maintain\ClosedState;
use app\dashboard\model\isspatfsm\execute\ApplyModifyingState;
//input & output state
use app\dashboard\model\isspatfsm\maintain\ApplySubmittingState;
use app\dashboard\model\isspatfsm\maintain\ApplyAuthorizedState;
use app\dashboard\model\isspatfsm\maintain\ApplyRejectedState;
use app\dashboard\model\isspatfsm\maintain\RenewSubmittingState;
use app\dashboard\model\isspatfsm\maintain\RenewAuthorizedState;


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
  //��תstate��Ҫ���������
  public function transferData($data)
  {
    //state����Ҫ���������
    $this->_currentState->getData($data);
  }  
  
  //_MAINTAIN��7�ֲ���
  public function apply(){
    return $this->_currentState->apply();
  }
  
  public function review(){
    return $this->_currentState->review();
  }
  
  public function improve(){
    return $this->_currentState->improve();
  }
  
  public function authorize(){
    return $this->_currentState->authorize();
  }
  
  public function reject(){
    return $this->_currentState->reject();
  }
  
  public function addRenew(){
    return $this->_currentState->addRenew();
  }
  
  public function close(){
    return $this->_currentState->close();
  }
}

?>