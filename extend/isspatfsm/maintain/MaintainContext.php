<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个控制类，实现_MAINTAIN权限下对issPat的13种状态的转换，7种操作的执行。
 */

namespace isspatfsm\maintain;

use isspatfsm\maintain\MaintainState;

//input State
use isspatfsm\maintain\RenewCreatingState;
use isspatfsm\maintain\ApplyReviewingState;
use isspatfsm\maintain\ApplyVetoedState;
use isspatfsm\maintain\RenewApprovedState;
use isspatfsm\maintain\RenewVetoedState;
//output state
use isspatfsm\approve\RenewPlanningState;
use isspatfsm\maintain\ClosedState;
use isspatfsm\execute\ApplyModifyingState;
//input & output state
use isspatfsm\maintain\ApplySubmittingState;
use isspatfsm\maintain\ApplyAuthorizedState;
use isspatfsm\maintain\ApplyRejectedState;
use isspatfsm\maintain\RenewSubmittingState;
use isspatfsm\maintain\RenewAuthorizedState;


class MaintainContext{
  //_MAINTAIN的13种状态
  //static标识的类的静态属性只属于类，与对象实例和其他类无关。
  //类的静态属性类似于函数的全局变量，在类的外部使用类名直接访问类的静态属性“EditContext::$fillingState”
  static $renewCreatingState = null;
  static $applyReviewingState = null;
  static $applyVetoedState = null;
  static $renewApprovedState = null;
  static $renewVetoedState = null;
  static $renewPlanningState = null;
  static $closedState = null; 
  static $applyModifyingState = null;
  static $applySubmittingState = null;
  static $applyAuthorizedState = null;
  static $applyRejectedState = null;
  static $renewSubmittingState = null;
  static $renewAuthorizedState = null; 
  
  
  //定义一个当前_MAINTAIN状态，属性值是当前_MAINTAIN状态对象实例。
  private $_currentState;
  
  public function __construct(){
    //访问本类中定义的静态属性
    self::$renewCreatingState = new RenewCreatingState();
    self::$applyReviewingState = new ApplyReviewingState();
    self::$applyVetoedState = new ApplyVetoedState();
    self::$renewApprovedState = new RenewApprovedState();
    self::$renewVetoedState = new RenewVetoedState();
    self::$renewPlanningState = new RenewPlanningState();
    self::$closedState = new ClosedState();
    self::$applyModifyingState = new ApplyModifyingState;
    self::$applySubmittingState = new ApplySubmittingState();
    self::$applyAuthorizedState = new ApplyAuthorizedState();
    self::$applyRejectedState = new ApplyRejectedState();
    self::$renewSubmittingState = new RenewSubmittingState();
    self::$renewAuthorizedState = new RenewAuthorizedState();
   
  }
  
  //获取状态
  public function getState(){
    return $this->_currentState;
  }
  //设置当前状态
  public function setState(MaintainState $maintainState){
    $this->_currentState=$maintainState;
    //把当前的环境通知到各个实现类中
    $this->_currentState->setContext($this);
  }
  
  //_MAINTAIN的7种操作
  public function apply($data){
   return $this->_currentState->apply($data);
  }
  
  public function review($data){
    $this->_currentState->review($data);
  }
  
  public function improve($data){
    $this->_currentState->improve($data);
  }
  
  public function authorize($data){
    $this->_currentState->authorize($data);
  }
  
  public function reject($data){
    $this->_currentState->reject($data);
  }
  
  public function addRenew($data){
    $this->_currentState->addRenew($data);
  }
  
  public function close($data){
    $this->_currentState->close($data);
  }
}

?>