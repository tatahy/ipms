<?php

/**
 * @author tatahy
 * @copyright 2018
 * ����һ�������࣬ʵ��_EXECUTEȨ���¶�issPat��7��״̬��ת����4�ֲ�����ִ�С�
 */

namespace app\dashboard\model\isspatfsm\execute;

use app\dashboard\model\isspatfsm\execute\ExecuteState;

//input State
use app\dashboard\model\isspatfsm\execute\ApplyApprovedState;
use app\dashboard\model\isspatfsm\execute\ExeChApprovedState;
use app\dashboard\model\isspatfsm\execute\ExeChVetoedState;
//output state
use app\dashboard\model\isspatfsm\maintain\ApplyReviewingState;
use app\dashboard\model\isspatfsm\approve\ExeChangingState;
//input & output state
use app\dashboard\model\isspatfsm\execute\ApplyModifyingState;
use app\dashboard\model\isspatfsm\execute\ExecutingState;

class ExecuteContext{
  //_EXECUTE��7��״̬
  //static��ʶ����ľ�̬����ֻ�����࣬�����ʵ�����������޹ء�
  //��ľ�̬���������ں�����ȫ�ֱ�����������ⲿʹ������ֱ�ӷ�����ľ�̬���ԡ�EditContext::$fillingState��
  static $applyApprovedState = null;
  static $exeChApprovedState = null;
  static $exeChVetoedState = null;
  static $applyReviewingState = null;
  static $exeChangingState = null;
  static $applyModifyingState = null;
  static $executingState = null;
  
  //����һ����ǰ_EXECUTE״̬������ֵ�ǵ�ǰ_EXECUTE״̬����ʵ����
  private $_currentState;
  
  public function __construct(){
    //���ʱ����ж���ľ�̬����
    self::$applyApprovedState = new ApplyApprovedState();
    self::$exeChApprovedState = new ExeChApprovedState();
    self::$exeChVetoedState = new ExeChVetoedState();
    self::$applyReviewingState = new ApplyReviewingState();
    self::$exeChangingState = new ExeChangingState();
    self::$applyModifyingState = new ApplyModifyingState();
    self::$executingState = new ExecutingState();
   
  }
  
  //��ȡ״̬
  public function getState(){
    return $this->_currentState;
  }
  //���õ�ǰ״̬
  public function setState(ExecuteState $executeState){
    $this->_currentState=$executeState;
    //�ѵ�ǰ�Ļ���֪ͨ������ʵ������
    $this->_currentState->setContext($this);
  }
  //��תstate��Ҫ���������
  public function transferData($data)
  {
    //state����Ҫ���������
    $this->_currentState->getData($data);
  }  
  
  //_EXECUTE��4�ֲ���
  public function accept(){
   return $this->_currentState->accept();
  }
  public function refuse(){
    return $this->_currentState->refuse();
  }
  public function report(){
    return $this->_currentState->report();
  }
  public function finish(){
    return $this->_currentState->finish();
  }
}

?>