<?php

/**
 * @author tatahy
 * @copyright 2018
 * ����һ��isspat,_EXECUTEȨ���µ�state�����ࡰExecuteState��
 */

namespace isspatfsm\execute;
//����������ݿ��5��ģ��
use isspatfsm\OprtModel as Mdl;

use isspatfsm\execute\ExecuteContext;

abstract class ExecuteState{
  
  //����һ���������ԣ��̳е�������У�����ֵ��ExecuteContext����ʵ����
  protected $_context;
  
  public function __construct(){
    //���ʱ����ж���ľ�̬����
    
  }
  
  //�趨�����Ļ���
  public function setContext(ExecuteContext $context){
	$this->_context = $context;
 }
  
  //_EXECUTE��4�ֲ���
  public abstract function accept($data);
  public abstract function refuse($data);
  public abstract function report($data);
  public abstract function finish($data);
  //ʹ��Mdl�з�װ�õĶ����ݿ�Ĳ���������
  protected function _updateStatus($data){
    //patId!=0,issId!=0
    Mdl::issPatStatusUpdate($data);
    
  }
  
}

?>