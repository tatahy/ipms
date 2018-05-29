<?php

/**
 * @author tatahy
 * @copyright 2018
 * ����һ��isspat,_AUDITȨ���µ�state�����ࡰAuditState��
 */

namespace isspatfsm\audit;
//����������ݿ��5��ģ��
use isspatfsm\OprtModel as Mdl;

use isspatfsm\audit\AuditContext;

abstract class AuditState{
  
  //����һ���������ԣ��̳е�������У�����ֵ��AuditContext����ʵ����
  protected $_context;
  
  public function __construct(){
    //���ʱ����ж���ľ�̬����
    
  }
  
  //�趨�����Ļ���
  public function setContext(AuditContext $context){
	$this->_context = $context;
 }
  
  //_AUDIT��3�ֲ���
  public abstract function pass($data);
  public abstract function fail($data);
  public abstract function modify($data);
  //ʹ��Mdl�з�װ�õĶ����ݿ�Ĳ���������
  protected function _updateStatus($data){
    //patId!=0,issId!=0
    Mdl::issPatStatusUpdate($data);
  }
  
}

?>