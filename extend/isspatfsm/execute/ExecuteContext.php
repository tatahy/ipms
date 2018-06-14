<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个控制类，实现_EXECUTE权限下对issPat的7种状态的转换，4种操作的执行。
 */

namespace isspatfsm\execute;

use isspatfsm\execute\ExecuteState;

//input State
use isspatfsm\execute\ApplyApprovedState;
use isspatfsm\execute\ExeChApprovedState;
use isspatfsm\execute\ExeChVetoedState;
//output state
use isspatfsm\maintain\ApplyReviewingState;
use isspatfsm\approve\ExeChangingState;
//input & output state
use isspatfsm\execute\ApplyModifyingState;
use isspatfsm\execute\ExecutingState;

class ExecuteContext{
  //_EXECUTE的7种状态
  //static标识的类的静态属性只属于类，与对象实例和其他类无关。
  //类的静态属性类似于函数的全局变量，在类的外部使用类名直接访问类的静态属性“EditContext::$fillingState”
  static $applyApprovedState = null;
  static $exeChApprovedState = null;
  static $exeChVetoedState = null;
  static $applyReviewingState = null;
  static $exeChangingState = null;
  static $applyModifyingState = null;
  static $executingState = null;
  
  //定义一个当前_EXECUTE状态，属性值是当前_EXECUTE状态对象实例。
  private $_currentState;
  
  public function __construct(){
    //访问本类中定义的静态属性
    self::$applyApprovedState = new ApplyApprovedState();
    self::$exeChApprovedState = new ExeChApprovedState();
    self::$exeChVetoedState = new ExeChVetoedState();
    self::$applyReviewingState = new ApplyReviewingState();
    self::$exeChangingState = new ExeChangingState();
    self::$applyModifyingState = new ApplyModifyingState();
    self::$executingState = new ExecutingState();
   
  }
  
  //获取状态
  public function getState(){
    return $this->_currentState;
  }
  //设置当前状态
  public function setState(ExecuteState $executeState){
    $this->_currentState=$executeState;
    //把当前的环境通知到各个实现类中
    $this->_currentState->setContext($this);
  }
  //中转state中要处理的数据
  public function transferData($data)
  {
    //state接收要处理的数据
    $this->_currentState->getData($data);
  }  
  
  //_EXECUTE的4种操作
  public function accept(){
   return $this->_currentState->accept();
  }
  public function refuse(){
    return $this->_currentState->refuse();
  }
  public function report(){
    return $this->_currentState->report();
  }
  public function finish(){
    return $this->_currentState->finish();
  }
}

?>