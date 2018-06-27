<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个控制类，实现_APPROVE权限下对issPat的11种状态的转换，3种操作的执行。
 */

namespace app\dashboard\model\isspatfsm\approve;

use app\dashboard\model\isspatfsm\approve\ApproveState;

//input State
use app\dashboard\model\isspatfsm\approve\AuditPassedState;
use app\dashboard\model\isspatfsm\approve\AuditFailedState;
use app\dashboard\model\isspatfsm\approve\ExeChangingState;
use app\dashboard\model\isspatfsm\approve\RenewPlanningState;
//output state
use app\dashboard\model\isspatfsm\execute\ApplyApprovedState;
use app\dashboard\model\isspatfsm\maintain\ApplyVetoedState;
use app\dashboard\model\isspatfsm\edit\CompletingState;
use app\dashboard\model\isspatfsm\execute\ExeChApprovedState;
use app\dashboard\model\isspatfsm\execute\ExeChVetoedState;
use app\dashboard\model\isspatfsm\maintain\RenewApprovedState;
use app\dashboard\model\isspatfsm\maintain\RenewVetoedState;

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
  //中转state中要处理的数据
  public function transferData($data)
  {
    //state接收要处理的数据
    $this->_currentState->getData($data);
  }  
  //_APPROVE的3种操作
  public function permit(){
    return $this->_currentState->permit();
  }
  public function veto(){
    return $this->_currentState->veto();
  }
  public function complete(){
    return $this->_currentState->complete();
  }
  
}

?>