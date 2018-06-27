<?php

/**
 * @author tatahy
 * @copyright 2018
 * ����һ�������࣬ʵ��_APPROVEȨ���¶�issPat��11��״̬��ת����3�ֲ�����ִ�С�
 */

namespace app\dashboard\model\isspatfsm\approve;

use app\dashboard\model\isspatfsm\approve\ApproveState;

//input State
use app\dashboard\model\isspatfsm\approve\AuditPassedState;
use app\dashboard\model\isspatfsm\approve\AuditFailedState;
use app\dashboard\model\isspatfsm\approve\ExeChangingState;
use app\dashboard\model\isspatfsm\approve\RenewPlanningState;
//output state
use app\dashboard\model\isspatfsm\execute\ApplyApprovedState;
use app\dashboard\model\isspatfsm\maintain\ApplyVetoedState;
use app\dashboard\model\isspatfsm\edit\CompletingState;
use app\dashboard\model\isspatfsm\execute\ExeChApprovedState;
use app\dashboard\model\isspatfsm\execute\ExeChVetoedState;
use app\dashboard\model\isspatfsm\maintain\RenewApprovedState;
use app\dashboard\model\isspatfsm\maintain\RenewVetoedState;

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
  //��תstate��Ҫ���������
  public function transferData($data)
  {
    //state����Ҫ���������
    $this->_currentState->getData($data);
  }  
  //_APPROVE��3�ֲ���
  public function permit(){
    return $this->_currentState->permit();
  }
  public function veto(){
    return $this->_currentState->veto();
  }
  public function complete(){
    return $this->_currentState->complete();
  }
  
}

?>