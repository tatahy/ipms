<?php

/**
 * @author tatahy
 * @copyright 2018
 * ����һ��isspat,_AUDITȨ���µ�state�����ࡰAuditState��
 */

namespace isspatfsm\audit;
//����������ݿ��5��ģ��
use isspatfsm\IssPatModel;

use isspatfsm\audit\AuditContext;

abstract class AuditState
{

  //����һ���������ԣ��̳е�������У�����ֵ��EditContext����ʵ����
  protected $_context;
  //
  protected $_mdl;
  //������������
  protected $_oprtData;

  public function __construct(){
    //ʵ����IssPatModel�࣬����ʹ�����װ�ķ�����
    $this->_mdl = new IssPatModel();
  }
  //�趨�����Ļ���
  public function setContext(AuditContext $context){
    $this->_context = $context;
  }
  //�õ��������������
  public function getData($data){
    $this->_oprtData = $data;
  }
  //_AUDIT��3�ֲ���
  abstract function pass();
  abstract function fail();
  abstract function modify();
  

}

?>