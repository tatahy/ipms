<?php

/**
 * @author tatahy
 * @copyright 2018
 * ����һ�������࣬ʵ��_AUDITȨ���¶�issPat��4��״̬��ת����3�ֲ�����ִ�С�
 */

namespace isspatfsm\audit;

use isspatfsm\audit\AuditState;

//input State
use isspatfsm\audit\CheckingState;
//output state
use isspatfsm\approve\AuditPassedState;
use isspatfsm\approve\AuditFailedState;
use isspatfsm\edit\ModifyingState;


class AuditContext
{
  //_AUDIT��4��״̬
  //static��ʶ����ľ�̬����ֻ�����࣬�����ʵ�����������޹ء�
  //��ľ�̬���������ں�����ȫ�ֱ�����������ⲿʹ������ֱ�ӷ�����ľ�̬���ԡ�EditContext::fillingState��
  static $checkingState = null;
  static $auditPassedState = null;
  static $auditFailedState = null;
  static $modifyingState = null;

  //����һ����ǰ_AUDIT״̬������ֵ�ǵ�ǰ_AUDIT״̬����ʵ����
  private $_currentState;

  public function __construct()
  {
    //���ʱ����ж���ľ�̬����
    self::$checkingState = new CheckingState();
    self::$auditPassedState = new AuditPassedState();
    self::$auditFailedState = new AuditFailedState();
    self::$modifyingState = new ModifyingState();
  }

  //��ȡ״̬
  public function getState()
  {
    return $this->_currentState;
  }
  //���õ�ǰ״̬
  public function setState(AuditState $auditState)
  {
    $this->_currentState = $auditState;
    //�ѵ�ǰ�Ļ���֪ͨ������ʵ������
    $this->_currentState->setContext($this);
  }
  //��תstate��Ҫ���������
  public function transferData($data)
  {
    //state����Ҫ���������
    $this->_currentState->getData($data);
  }
  //_AUDIT��3�ֲ���
  public function pass()
  {
    return $this->_currentState->pass();
  }

  public function fail()
  {
    return $this->_currentState->fail();
  }

  public function modify()
  {
    return $this->_currentState->modify();
  }

}

?>