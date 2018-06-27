<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个控制类，实现_AUDIT权限下对issPat的4种状态的转换，3种操作的执行。
 */

namespace app\dashboard\model\isspatfsm\audit;

use app\dashboard\model\isspatfsm\audit\AuditState;

//input State
use app\dashboard\model\isspatfsm\audit\CheckingState;
//output state
use app\dashboard\model\isspatfsm\approve\AuditPassedState;
use app\dashboard\model\isspatfsm\approve\AuditFailedState;
use app\dashboard\model\isspatfsm\edit\ModifyingState;


class AuditContext
{
  //_AUDIT的4种状态
  //static标识的类的静态属性只属于类，与对象实例和其他类无关。
  //类的静态属性类似于函数的全局变量，在类的外部使用类名直接访问类的静态属性“EditContext::fillingState”
  static $checkingState = null;
  static $auditPassedState = null;
  static $auditFailedState = null;
  static $modifyingState = null;

  //定义一个当前_AUDIT状态，属性值是当前_AUDIT状态对象实例。
  private $_currentState;

  public function __construct()
  {
    //访问本类中定义的静态属性
    self::$checkingState = new CheckingState();
    self::$auditPassedState = new AuditPassedState();
    self::$auditFailedState = new AuditFailedState();
    self::$modifyingState = new ModifyingState();
  }

  //获取状态
  public function getState()
  {
    return $this->_currentState;
  }
  //设置当前状态
  public function setState(AuditState $auditState)
  {
    $this->_currentState = $auditState;
    //把当前的环境通知到各个实现类中
    $this->_currentState->setContext($this);
  }
  //中转state中要处理的数据
  public function transferData($data)
  {
    //state接收要处理的数据
    $this->_currentState->getData($data);
  }
  //_AUDIT的3种操作
  public function pass()
  {
    return $this->_currentState->pass();
  }

  public function fail()
  {
    return $this->_currentState->fail();
  }

  public function modify()
  {
    return $this->_currentState->modify();
  }

}

?>