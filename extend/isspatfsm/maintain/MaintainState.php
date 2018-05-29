<?php

/**
 * @author tatahy
 * @copyright 2018
 * ����һ��isspat,_MAINTAINȨ���µ�state�����ࡰMaintainState��
 */

namespace isspatfsm\execute;
//����������ݿ��5��ģ��
use isspatfsm\IssPatModel as Mdl;

use isspatfsm\maintain\MaintainContext;

abstract class MaintainState{
  
  //����һ���������ԣ��̳е�������У�����ֵ��ExecuteContext����ʵ����
  protected $_context;
  
  public function __construct(){
    //���ʱ����ж���ľ�̬����
    
  }
  
  //�趨�����Ļ���
  public function setContext(MaintainContext $context){
	$this->_context = $context;
 }
  
  //_MAINTAIN��7�ֲ���
  abstract function apply($data);
  abstract function review($data);
  abstract function improve($data);
  abstract function authorize($data);
  abstract function reject($data);
  abstract function addRenew($data);
  public function close($data){
    $this->_updateStatus($data);
    //״̬�޸�
    $this->_context->setState(MaintainContext::$closedState);
    return '<br>close�����';
  }
  //ʹ��Mdl�з�װ�õĶ����ݿ�Ĳ���������
  protected function _updateStatus($data){
    //patId!=0,issId!=0
    Mdl::issUpdate($data);
    Mdl::issRdCreate($data);
    Mdl::patUpdate($data);
    Mdl::patRdCreate($data);
    Mdl::attUpdate($data);
  }
  
}

?>