<?php

/**
 * @author tatahy
 * @copyright 2018
 * ����һ��isspat,_EXECUTEȨ���µ�state�����ࡰExecuteState��
 */

namespace isspatfsm\execute;
//�������5�����ݿ����
use isspatfsm\IssPatModel;

use isspatfsm\execute\ExecuteContext;

abstract class ExecuteState{
  
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
  public function setContext(ExecuteContext $context){
	$this->_context = $context;
 }
 //�õ��������������
  public function getData($data){
    $this->_oprtData = $data;
  }
  
  //_EXECUTE��4�ֲ���
  abstract function accept();
  abstract function refuse();
  abstract function report();
  abstract function finish();
  
}

?>