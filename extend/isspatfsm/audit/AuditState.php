<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个isspat,_AUDIT权限下的state抽象类“AuditState”
 */

namespace isspatfsm\audit;
//引入操作数据库的5个模型
use isspatfsm\IssPatModel;

use isspatfsm\audit\AuditContext;

abstract class AuditState
{

  //定义一个环境属性，继承的子类才有，属性值是EditContext对象实例。
  protected $_context;
  //
  protected $_mdl;
  //操作所需数据
  protected $_oprtData;

  public function __construct()
  {
    //实例化IssPatModel类，便于使用其封装的方法。
    $this->_mdl = new IsspatModel();

  }

  //设定上下文环境
  public function setContext(AuditContext $context)
  {
    $this->_context = $context;
  }
  //得到操作所需的数据
  public function getData($data)
  {
    $this->_oprtData = $data;
  }
  //_AUDIT的3种操作
  abstract function pass();
  abstract function fail();
  abstract function modify();

}

?>