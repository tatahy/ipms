<?php

/**
 * @author tatahy
 * @copyright 2018
 * ����һ��isspat,_APPROVEȨ���µ�state�����ࡰApproveState��
 */

namespace isspatfsm\approve;
//�������5�����ݿ����
use isspatfsm\IssPatModel;

use isspatfsm\approve\ApproveContext;

abstract class ApproveState{
  
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
  public function setContext(ApproveContext $context){
	$this->_context = $context;
 }
  //�õ��������������
  public function getData($data){
    $this->_oprtData = $data;
  }
  
  //_APPROVE��3�ֲ���
  abstract function permit();
  abstract function veto();
  abstract function complete();
  
  //���ݿ�ģ�Ͳ���
  protected function oprtMdl(){
    //1.patinfo����
    $this->_mdl->patUpdate();

    //2.patrecord����
    $this->_mdl->patRdCreate();

    //3.issinfo����
    $this->_mdl->issUpdate();

    //4.issrecord����
    $this->_mdl->issRdCreate();

    //5.attinfo����
    $this->_mdl->attUpdate();
  }
  
}

?>