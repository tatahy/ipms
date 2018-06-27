<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个控制类，实现_MAINTAIN权限下对issPat的13种状态的转换，7种操作的执行。
 */

namespace app\dashboard\model\isspatfsm\maintain;

use app\dashboard\model\isspatfsm\maintain\MaintainState;

//input State
use app\dashboard\model\isspatfsm\maintain\RenewCreatingState;
use app\dashboard\model\isspatfsm\maintain\ApplyReviewingState;
use app\dashboard\model\isspatfsm\maintain\ApplyVetoedState;
use app\dashboard\model\isspatfsm\maintain\RenewApprovedState;
use app\dashboard\model\isspatfsm\maintain\RenewVetoedState;
//output state
use app\dashboard\model\isspatfsm\approve\RenewPlanningState;
use app\dashboard\model\isspatfsm\maintain\ClosedState;
use app\dashboard\model\isspatfsm\execute\ApplyModifyingState;
//input & output state
use app\dashboard\model\isspatfsm\maintain\ApplySubmittingState;
use app\dashboard\model\isspatfsm\maintain\ApplyAuthorizedState;
use app\dashboard\model\isspatfsm\maintain\ApplyRejectedState;
use app\dashboard\model\isspatfsm\maintain\RenewSubmittingState;
use app\dashboard\model\isspatfsm\maintain\RenewAuthorizedState;


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
  //中转state中要处理的数据
  public function transferData($data)
  {
    //state接收要处理的数据
    $this->_currentState->getData($data);
  }  
  
  //_MAINTAIN的7种操作
  public function apply(){
    return $this->_currentState->apply();
  }
  
  public function review(){
    return $this->_currentState->review();
  }
  
  public function improve(){
    return $this->_currentState->improve();
  }
  
  public function authorize(){
    return $this->_currentState->authorize();
  }
  
  public function reject(){
    return $this->_currentState->reject();
  }
  
  public function addRenew(){
    return $this->_currentState->addRenew();
  }
  
  public function close(){
    return $this->_currentState->close();
  }
}

?>