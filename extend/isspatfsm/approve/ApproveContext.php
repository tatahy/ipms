<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个控制类，实现_APPROVE权限下对issPat的11种状态的转换，3种操作的执行。
 */

namespace isspatfsm\audit;

use isspatfsm\approve\ApproveState;

//input State
use isspatfsm\approve\AuditPassedState;
use isspatfsm\approve\AuditFailedState;
use isspatfsm\approve\ExeChangingState;
use isspatfsm\approve\RenewPlanningState;
//output state
use isspatfsm\execute\ApplyApprovedState;
use isspatfsm\maintain\ApplyVetoedState;
use isspatfsm\edit\CompletingState;
use isspatfsm\execute\ExeChApprovedState;
use isspatfsm\execute\ExeChVetoedState;
use isspatfsm\maintain\RenewApprovedState;
use isspatfsm\maintain\RenewVetoedState;

class ApproveContext{
  //_APPROVE的7种状态
  //static标识的类的静态属性只属于类，与对象实例和其他类无关。
  //类的静态属性类似于函数的全局变量，在类的外部使用类名直接访问类的静态属性“EditContext::$fillingState”
  static $auditPassedState = null;
  static $auditFailedState = null;
  static $exeChangingState = null;
  static $renewPlanningState = null;
  static $applyApprovedState = null;
  static $applyVetoedState = null;
  static $completingState = null;
  static $exeChApprovedState = null;
  static $exeChVetoedState = null;
  static $renewApprovedState = null;
  static $renewVetoedState = null;
  
  //定义一个当前_APPROVE状态，属性值是当前_APPROVE状态对象实例。
  private $_currentState;
  
  public function __construct(){
    //访问本类中定义的静态属性
    self::$auditPassedState = new AuditPassedState();
    self::$auditFailedState = new AuditFailedState();
    self::$exeChangingState = new ExeChangingState();
    self::$renewPlanningState = new RenewPlanningState();
    self::$applyApprovedState = new ApplyApprovedState();
    self::$applyVetoedState = new ApplyVetoedState();
    self::$completingState = new CompletingState();
    self::$exeChApprovedState = new ExeChApprovedState();
    self::$exeChVetoedState = new ExeChVetoedState();
    self::$renewApprovedState = new RenewApprovedState();
    self::$renewVetoedState = new RenewVetoedState();
  }
  
  //获取状态
  public function getState(){
    return $this->_currentState;
  }
  //设置当前状态
  public function setState(ApproveState $approveState){
    $this->_currentState=$approveState;
    //把当前的环境通知到各个实现类中
    $this->_currentState->setContext($this);
  }
  
  //_APPROVE的3种操作
  public function permit($data){
   return $this->_currentState->permit($data);
  }
  
  public function veto($data){
    $this->_currentState->veto($data);
  }
  
  public function complete($data){
    $this->_currentState->complete($data);
  }
  
}

?>