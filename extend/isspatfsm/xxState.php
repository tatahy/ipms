<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个isspat,_xx权限下的state抽象类“xxState”
 */

namespace isspatfsm\xx;

use isspatfsm\xx\xxContext;

abstract class xxState{
  
  //定义一个环境属性，继承的子类才有，属性值是xxContext对象实例。
  protected $_context;
 
  //设定上下文环境
  public function setContext(xxContext $context){
	$this->_context = $context;
 }
  
  //_xx的各种操作
  public abstract function addNew($data);
  
  
}

?>