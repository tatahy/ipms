<?php

/**
 * @author tatahy
 * @copyright 2018
 * ����һ��isspat,_APPROVEȨ���µ�state�����ࡰApproveState��
 */

namespace isspatfsm\approve;
//����������ݿ��5��ģ��
use isspatfsm\OprtModel as Mdl;

use isspatfsm\approve\ApproveContext;

abstract class ApproveState{
  
  //����һ���������ԣ��̳е�������У�����ֵ��ApproveContext����ʵ����
  protected $_context;
  
  public function __construct(){
    //���ʱ����ж���ľ�̬����
    
  }
  
  //�趨�����Ļ���
  public function setContext(ApproveContext $context){
	$this->_context = $context;
 }
  
  //_APPROVE��3�ֲ���
  public abstract function approve($data);
  public abstract function veto($data);
  public abstract function complete($data);
  //ʹ��Mdl�з�װ�õĶ����ݿ�Ĳ���������
  protected function _updateStatus($data){
    //patId!=0,issId!=0
    Mdl::issPatStatusUpdate($data);
    
  }
  
}

?>