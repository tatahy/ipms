<?php

/**
 * @author tatahy
 * @copyright 2018
 * ����һ��isspat,_AUDITȨ���µ�state�����ࡰAuditState��
 */

namespace app\dashboard\model\isspatfsm\audit;
//����������ݿ��5��ģ��
use app\dashboard\model\isspatfsm\IssPatModel;

use app\dashboard\model\isspatfsm\audit\AuditContext;

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
  
  //���ݿ�ģ�Ͳ���
  protected function _oprtMdl(){
    //1.patinfo����
    $this->_mdl->patUpdate();

    //2.issinfo����
    $this->_mdl->issUpdate();

    //3.issrecord����
    $this->_mdl->issRdCreate();

    //4.attinfo����
    $this->_mdl->attUpdate();
  }
  

}

?>