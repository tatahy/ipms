<?php

/**
 * @author tatahy
 * @copyright 2018
 * ����һ��isspat,_xxȨ���µ�state�����ࡰxxState��
 */

namespace isspatfsm\xx;

use isspatfsm\xx\xxContext;

abstract class xxState{
  
  //����һ���������ԣ��̳е�������У�����ֵ��xxContext����ʵ����
  protected $_context;
 
  //�趨�����Ļ���
  public function setContext(xxContext $context){
	$this->_context = $context;
 }
  
  //_xx�ĸ��ֲ���
  public abstract function addNew($data);
  
  
}

?>