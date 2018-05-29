<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个控制类，实现_AUDIT权限下对issPat的4种状态的转换，3种操作的执行。
 */

namespace isspatfsm\audit;

use isspatfsm\audit\AuditState;

//input State
use isspatfsm\audit\CheckingState;
//output state
use isspatfsm\approve\AuditPassedState;
use isspatfsm\approve\AuditFailedState;
use isspatfsm\edit\ModifyingState;


class AuditContext{
  //_AUDIT的4种状态
  //static标识的类的静态属性只属于类，与对象实例和其他类无关。
  //类的静态属性类似于函数的全局变量，在类的外部使用类名直接访问类的静态属性“EditContext::fillingState”
  static $checkingState = null;
  static $auditPassedState = null;
  static $auditFailedState = null;
  static $modifyingState = null;
  
  //定义一个当前_AUDIT状态，属性值是当前_AUDIT状态对象实例。
  private $_currentState;
  
  public function __construct(){
    //访问本类中定义的静态属性
    self::$checkingState = new CheckingState();
    self::$auditPassedState = new AuditPassedState();
    self::$auditFailedState = new AuditFailedState();
    self::$modifyingState = new ModifyingState();
  }
  
  //获取状态
  public function getState(){
    return $this->_currentState;
  }
  //设置当前状态
  public function setState(AuditState $auditState){
    $this->_currentState=$auditState;
    //把当前的环境通知到各个实现类中
    $this->_currentState->setContext($this);
  }
  
  //_AUDIT的3种操作
  public function pass($data){
   return $this->_currentState->pass($data);
  }
  
  public function fail($data){
    $this->_currentState->fail($data);
  }
  
  public function modify($data){
    $this->_currentState->modify($data);
  }
  
}

?>