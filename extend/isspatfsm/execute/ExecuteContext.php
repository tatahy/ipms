<?php

/**
 * @author tatahy
 * @copyright 2018
 * ����һ�������࣬ʵ��_EXECUTEȨ���¶�issPat��8��״̬��ת����4�ֲ�����ִ�С�
 */

namespace isspatfsm\execute;

use isspatfsm\execute\ExecuteState;

//input State
use isspatfsm\execute\ApplyApprovedState;
use isspatfsm\execute\ExeChApprovedState;
use isspatfsm\execute\ExeChVetoedState;
//output state
use isspatfsm\maintain\ApplyReviewingState;
use isspatfsm\approve\ExeChangingState;
//input & output state
use isspatfsm\execute\ApplyModifyingState;
use isspatfsm\execute\ExecutingState;

class ApproveContext{
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
  
  //_EXECUTE��4�ֲ���
  public function accept($data){
   return $this->_currentState->accept($data);
  }
  
  public function refuse($data){
    $this->_currentState->refuse($data);
  }
  
  public function report($data){
    $this->_currentState->report($data);
  }
  
  public function finish($data){
    $this->_currentState->finish($data);
  }
}

?>