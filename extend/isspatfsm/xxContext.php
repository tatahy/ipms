<?php

/**
 * @author tatahy
 * @copyright 2018
 * ����һ�������࣬ʵ�ֶ�_xxȨ���¶�issPat״̬�Ŀ��ơ��л�
 */

namespace isspatfsm\xx;
//input state 
use isspatfsm\xx\xxState;

//output state
use isspatfsm\xx\xxState;

class xxContext{
  //��������_xx��issPat״̬
  //static��ʶ����ľ�̬����ֻ�����࣬�����ʵ�����������޹ء�
  //��ľ�̬���������ں�����ȫ�ֱ�����������ⲿʹ������ֱ�ӷ�����ľ�̬���ԡ�xxContext::fillingState��
  static $checkingState = null;
  
  //����һ����ǰ_xx״̬������ֵ�ǵ�ǰ_EDIT״̬����ʵ����
  private $_currentState;
  
  public function __construct(){
    //���ʱ����ж���ľ�̬����
    self::$xxState = new xxState();
    
    
  }
  
  //��ȡ״̬
  public function getState(){
    return $this->_currentState;
  }
  //���õ�ǰ״̬
  public function setState(xxState $xxState){
    $this->_currentState=$xxState;
    //�ѵ�ǰ�Ļ���֪ͨ������ʵ������
    $this->_currentState->setContext($this);
  }
  
  //_AUDIT��״̬�Ĳ���
  public function pass($data){
   return $this->_currentState->pass($data);
  }
  
  
   
}

?>