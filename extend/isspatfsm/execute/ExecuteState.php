<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个isspat,_EXECUTE权限下的state抽象类“ExecuteState”
 */

namespace isspatfsm\execute;
//引入操作5个数据库的类
use isspatfsm\IssPatModel;

use isspatfsm\execute\ExecuteContext;

abstract class ExecuteState{
  
  //定义一个环境属性，继承的子类才有，属性值是EditContext对象实例。
  protected $_context;
  //
  protected $_mdl;
  //操作所需数据
  protected $_oprtData;
  
  public function __construct(){
    //实例化IssPatModel类，便于使用其封装的方法。
    $this->_mdl = new IssPatModel();
  }
  //设定上下文环境
  public function setContext(ExecuteContext $context){
	$this->_context = $context;
 }
 //得到操作所需的数据
  public function getData($data){
    $this->_oprtData = $data;
  }
  
  //_EXECUTE的4种操作
  abstract function accept();
  abstract function refuse();
  abstract function report();
  abstract function finish();
  
}

?>